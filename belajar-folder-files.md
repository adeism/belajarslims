# Struktur Direktori `/files` SLiMS 9 Bulian 📂

Folder `/files` digunakan oleh SLiMS 9 Bulian untuk menyimpan berkas-berkas pencadangan sistem (*backup*), cache data, template kartu anggota, laporan, serta indeks pencarian.

---

## 📁 Struktur Direktori Asli `/files`

```plaintext
/files
├── backup/         💾 Menyimpan berkas dump pencadangan database (.sql, .sql.tar.gz)
├── cache/          ⚡ Berkas cache data JSON dan data sementara sistem
├── membercard/     🆔 Berkas template kartu anggota (classic, old, individual-membercard.php)
├── reports/        📊 Berkas hasil generate laporan kustom / cetak HTML
├── tntsearch/      🔍 Berkas database index mesin pencari cepat TNT Search
├── chat/           💬 Sesi dan histori data fitur pesan/obrolan
└── swfs/           🎞️ Berkas asset multimedia Flash/kompatibilitas warisan
```

> [!IMPORTANT]
> **Lokasi Berkas Media Lainnya di SLiMS:**
> - 📚 **Sampul Buku / Cover Koleksi**: Disimpan di direktori **`/images/docs/`** (`IMGBS`).
> - 👤 **Foto Profil Anggota**: Disimpan di direktori **`/images/persons/`** (`PICS`).
> - 🗂️ **Lampiran Digital / Fulltext PDF**: Disimpan di direktori **`/repository/`** (`REPOBS`).
> - 🖨️ **Barcode Generator**: Disimpan secara dinamis di direktori **`/images/barcodes/`**.

---

## 📝 Penjelasan Per Subdirektori `/files`

### 1. `/files/backup/` 💾
Menyimpan file arsip cadangan (*database backup*) yang dihasilkan dari modul **Sistem > Salin Cadangan Database**.
- Format file: `.sql` atau kompresi `.sql.tar.gz`.
- **Keamanan**: Sangat disarankan memblokir akses langsung publik ke folder ini melalui konfigurasi web server (Nginx/Apache).

### 2. `/files/cache/` ⚡
Menyimpan cache transient untuk mempercepat pemrosesan data, misalnya cache query atau data JSON. File di direktori ini dapat dibersihkan secara aman jika diperlukan.

### 3. `/files/membercard/` 🆔
Menyimpan template desain kartu anggota perpustakaan yang dapat dimodifikasi oleh pustakawan:
- `classic/`: Template klasik kartu anggota.
- `old/`: Template kartu anggota model lama.
- `individual-membercard.php`: Script pencetakan kartu anggota satuan.

### 4. `/files/reports/` 📊
Menyimpan berkas sementara hasil pembuatan laporan dari modul **Pelaporan**.

### 5. `/files/tntsearch/` 🔍
Menyimpan berkas indeks pencarian saat SLiMS dikonfigurasi menggunakan engine **TNT Search** untuk pencarian katalog berkecepatan tinggi.

---

## 🔒 Izin Akses (Permissions)

Pastikan izin folder `/files/` dan seluruh sub-direktorinya dapat ditulis oleh web server (misalnya `chmod 755` atau kepemilikan user `www-data` / `nginx`):

```bash
chown -R www-data:www-data /var/www/html/slims/files/
chmod -R 755 /var/www/html/slims/files/
```

