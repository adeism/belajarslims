```nginx
# Ganti domain-anda.com dengan nama domain Anda
# Ganti /path/to/your/slims dengan path direktori root instalasi SLiMS Anda

server {
    server_name domain-anda.com;
    index index.php index.html index.htm default.php default.htm default.html;
    root /path/to/your/slims;

    # ===== BLOK KEAMANAN - PERBAIKAN KRITIS =====
    
    # Izinkan createthumb.php untuk membuat thumbnail sampul buku
    location ~ ^/lib/minigalnano/createthumb.php$ {
        try_files $uri =404;
        fastcgi_pass unix:/var/run/php/php-fpm.sock; # Sesuaikan dengan versi PHP Anda
        fastcgi_index index.php;
        include fastcgi.conf;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        
        # Terapkan batasan keamanan
        fastcgi_param PHP_VALUE "open_basedir=$document_root:/tmp:/var/lib/php/sessions";
        fastcgi_param PHP_VALUE "allow_url_fopen=Off";
        fastcgi_param PHP_VALUE "allow_url_include=Off";
    }
    
    # Blokir eksekusi PHP di direktori sensitif (SANGAT PENTING)
    location ~ ^/(lib|vendor|files|images)/.+\.php$ {
        return 403;
    }

    # Blokir ekstensi file berbahaya
    location ~* \.(alfa|bak|sql|log|conf|ini|env|sample|backup|old|tmp|swp|orig)$ {
        return 403;
    }

    # Blokir direktori dan file sensitif
    location ~ ^/(config|simbio2|upgrade|\.git|\.svn|\.htaccess|\.user\.ini)/ {
        return 403;
    }

    # Blokir akses langsung ke direktori backdoor umum
    location ~ /ALFA_DATA/ {
        return 403;
    }

    # Blokir akses ke file yang namanya mengandung kata-kata berbahaya
    location ~* (backdoor|shell|hack|exploit|webshell) {
        return 403;
    }

    # Header keamanan tambahan
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header X-Robots-Tag "noindex, nofollow" always;

    # ===== AKHIR BLOK KEAMANAN =====

    # Blok untuk RSS Feed
    location = /feed/rss.xml {
        try_files $uri /rss.php?$is_args$args;
    }

    # Blok utama untuk memproses file PHP dengan peningkatan keamanan
    location ~ [^/]\.php(/|$) {
        try_files $uri =404;
        fastcgi_pass unix:/var/run/php/php-fpm.sock; # Sesuaikan dengan versi PHP Anda
        fastcgi_index index.php;
        fastcgi_hide_header X-Powered-By;
        include fastcgi.conf;

        # Parameter keamanan untuk PHP
        fastcgi_param PHP_VALUE "open_basedir=$document_root:/tmp:/var/lib/php/sessions";
        fastcgi_param PHP_VALUE "allow_url_fopen=Off";
        fastcgi_param PHP_VALUE "allow_url_include=Off";
        fastcgi_param PHP_VALUE "expose_php=Off";

        set $real_script_name $fastcgi_script_name;
        if ($fastcgi_script_name ~ "^(.+?\.php)(/.+)$") {
            set $real_script_name $1;
            set $path_info $2;
        }

        fastcgi_param SCRIPT_FILENAME $document_root$real_script_name;
        fastcgi_param SCRIPT_NAME $real_script_name;
    }

    # Blok untuk aset statis (CSS, JS, gambar, dll) untuk performa
    location ~* \.(css|js|jpg|jpeg|gif|png|ico|svg|ttf|woff|eot|pdf)$ {
        try_files $uri =404;
        expires max;
        access_log off;
    }
    
    # Aturan URL Cantik (Pretty URLs) untuk SLiMS
    location / {
        try_files $uri $uri/ @slims_rewrite;
    }
    
    location @slims_rewrite {
        rewrite ^/sd=([0-9]+)/?$ /index.php?p=show_detail&id=$1 last;
        rewrite ^/([^/]+)/?$ /index.php?p=$1 last;
        rewrite ^/([^/]+)/([^/]+)/?$ /index.php?p=$1&action=$2 last;
        rewrite ^/([^/]+)/([^/]+)/([^/]+)/?$ /index.php?p=$1&action=$2&id=$3 last;
        rewrite ^.*$ /index.php?$query_string last;
    }

    # Blokir akses ke file konfigurasi tersembunyi
    location ~ /\. {
        deny all;
    }

    # Konfigurasi SSL (dikelola oleh Certbot)
    listen 443 ssl;
    ssl_certificate /etc/letsencrypt/live/domain-anda.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/domain-anda.com/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;
}

# Blok pengalihan HTTP ke HTTPS
server {
    if ($host = domain-anda.com) {
        return 301 https://$host$request_uri;
    }

    listen 80;
    server_name domain-anda.com;
    return 404;
}
```

-----

### Penjelasan Konfigurasi Keamanan

Konfigurasi ini menerapkan beberapa lapisan keamanan untuk melindungi SLiMS dari serangan umum dan malware.

#### 1\. Mencegah Eksekusi Kode Berbahaya

Ini adalah bagian terpenting. Malware sering kali bekerja dengan cara mengunggah file PHP (shell) ke direktori yang seharusnya hanya berisi gambar atau dokumen, lalu menjalankannya dari URL.

  * **`location ~ ^/(lib|vendor|files|images)/.+\.php$`**: Aturan ini **memblokir** semua upaya untuk mengakses file yang berakhiran `.php` di dalam direktori `/lib`, `/vendor`, `/files`, dan `/images`. Jika penyerang berhasil mengunggah `shell.php` ke direktori `/images`, mereka tidak akan bisa menjalankannya.
  * **`location ~ ^/lib/minigalnano/createthumb.php$`**: Ini adalah **pengecualian**. Ada satu file PHP di dalam direktori `/lib` yang memang harus bisa dijalankan (`createthumb.php` untuk thumbnail). Aturan ini secara spesifik mengizinkan *hanya file itu saja* untuk dieksekusi, sementara file PHP lain di `/lib` tetap diblokir.

#### 2\. Membatasi Kemampuan PHP

Bahkan untuk skrip PHP yang sah, kemampuannya dibatasi untuk mengurangi dampak jika ada celah keamanan.

  * **`open_basedir`**: "Memenjarakan" PHP agar hanya bisa mengakses file dan direktori yang ditentukan. PHP tidak akan bisa membaca atau menulis file di luar direktori SLiMS.
  * **`allow_url_fopen=Off`** dan **`allow_url_include=Off`**: Menonaktifkan fitur PHP yang bisa digunakan untuk serangan *Remote File Inclusion* (RFI), di mana penyerang menyisipkan dan menjalankan kode dari server lain.

#### 3\. Menyembunyikan Informasi Sensitif

Penyerang sering mencari informasi tentang server untuk melancarkan serangan yang lebih tertarget.

  * **`expose_php=Off`** dan **`fastcgi_hide_header X-Powered-By`**: Menyembunyikan versi PHP yang Anda gunakan dari *HTTP headers*.
  * **`location ~* \.(bak|sql|log|env)`**: Memblokir akses ke file backup, dump database, log, atau file konfigurasi lingkungan (`.env`) yang mungkin secara tidak sengaja terekspos di web root.
  * **`location ~ ^/(\.git|\.svn)`**: Memblokir akses ke direktori sistem kontrol versi yang bisa membocorkan seluruh kode sumber aplikasi Anda.

#### 4\. Menambahkan HTTP Security Headers

Ini adalah lapisan pertahanan di sisi browser pengguna.

  * **`X-Frame-Options "SAMEORIGIN"`**: Mencegah situs lain memuat situs Anda dalam `<iframe>` (serangan *clickjacking*).
  * **`X-Content-Type-Options "nosniff"`**: Mencegah browser menebak tipe konten, yang bisa dieksploitasi dalam serangan XSS.
  * **`X-XSS-Protection "1; mode=block"`**: Mengaktifkan filter *Cross-Site Scripting* (XSS) bawaan browser.

#### 5\. URL Cantik (Pretty URLs) yang Aman

Konfigurasi ini juga menangani URL SLiMS agar lebih ramah pengguna (`domain.com/member` daripada `domain.com/index.php?p=member`).

  * **`location / { try_files $uri $uri/ @slims_rewrite; }`**: Jika URL yang diakses bukan file atau direktori yang ada, Nginx akan meneruskannya ke blok `@slims_rewrite`.
  * **`location @slims_rewrite`**: Blok ini berisi aturan `rewrite` yang mengubah URL cantik menjadi format parameter `?p=` yang dimengerti oleh `index.php` SLiMS.

Singkatnya, konfigurasi ini secara proaktif **menutup celah keamanan paling umum** dengan cara memblokir eksekusi kode di tempat yang tidak seharusnya, membatasi akses file, dan menyembunyikan informasi penting.
