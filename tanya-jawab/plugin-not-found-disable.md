# Plugin SLiMS: Error 'Plugin not found / disable' dan Parameter Wajib

Ketika menghadapi pesan kesalahan seperti **"Plugin error plugin not found / disable?"** di SLiMS, pemahaman tentang cara kerja parameter plugin sangatlah penting.

Setiap plugin SLiMS memerlukan dua ($\text{2}$) parameter utama yang harus selalu ada agar sistem dapat mengenalinya dan menjalankannya dengan benar.

## Dua ($\text{2}$) Parameter Wajib Plugin SLiMS

Plugin SLiMS harus membawa dua ($\text{2}$) parameter wajib:

1.  **`mod`**: Ini adalah **ID Modul** tempat plugin tersebut berada atau diakses.
2.  **`id`**: Ini adalah **ID Plugin** itu sendiri.

## Dari Mana Mendapatkan Parameter `mod` dan `id`?

Kedua ($\text{2}$) parameter ini didapatkan dari:

1.  **Variabel Global `$_GET`**: Parameter ini akan terekam dalam *global variable* `$_GET` pada saat plugin pertama kali diakses. Ini berarti mereka adalah bagian dari *query string* (`?key=value&...`) di URL.
2.  **Tautan Sub Menu**: Anda dapat memeriksa tautan (*link*) yang ter-generasi di sub-menu navigasi SLiMS, di mana plugin tersebut didaftarkan. Tautan tersebut akan menampilkan `.../index.php?mod=ID_MODUL&id=ID_PLUGIN`.

> **Contoh di URL:**
> `http://nama_domain/index.php?mod=pencatatan_buku&id=nama_plugin_saya`

