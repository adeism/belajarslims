# Struktur Folder SLiMS 📂

SLiMS (Senayan Library Management System) memiliki struktur folder yang terorganisasi dengan baik untuk memisahkan berbagai komponen sistem. Berikut ini adalah penjelasan setiap folder dalam SLiMS dengan ikon untuk mempermudah pemahaman.

---

## 📁 Daftar Folder Utama

### 1. `/admin` 🔧
Folder ini berisi file dan modul yang digunakan di area administrasi SLiMS. Semua fitur yang diakses oleh pustakawan atau administrator, seperti pengelolaan anggota, koleksi, dan peminjaman, berada di dalam folder ini.

- **Subfolder Penting**:
  - `/admin/modules`: Berisi modul utama untuk fitur administrasi.

---

### 2. `/api` 🔗
Berisi file untuk keperluan web service atau integrasi API. Digunakan jika ingin mengakses data SLiMS dari aplikasi atau sistem lain melalui endpoint API.

---

### 3. `/config` ⚙️
Folder ini menyimpan file konfigurasi database sistem SLiMS hasil proses instalasi.

- **File Penting**:
  - `config/database.php`: Berisi kredensial koneksi database (Host, Port, User, Pass, DB Name) yang dibuat saat instalasi selesai.
  - `sysconfig.inc.php` *(di direktori root)*: Konfigurasi sistem utama, inisialisasi konstanta path (`SB`, `SWB`, `AWB`), dan bootstrapping autoloader.

---

### 4. `/files` 📂
Folder untuk menyimpan file yang dihasilkan sistem atau file utilitas SLiMS:
- **`backup/`**: File dump pencadangan database (`.sql`, `.sql.tar.gz`).
- **`cache/`**: Cache data JSON dan temporary data sistem.
- **`membercard/`**: Berkas template cetak kartu anggota (`classic/`, `old/`, `individual-membercard.php`).
- **`reports/`**: File output cetak laporan HTML.
- **`tntsearch/`**: File database indeks pencarian cepat TNT Search.

---

### 5. `/images` 🖼️
Folder untuk menyimpan seluruh gambar media dan aset visual:
- **`docs/`**: File gambar sampul koleksi buku / bibliografi (`IMGBS`).
- **`persons/`**: File foto profil anggota / member (`PICS`).
- **`barcodes/`**: Cache cetakan barcode koleksi.
- **`cache/`**: Thumbnail otomatis hasil render `createthumb.php`.
- **`default/`**: Gambar default placeholder (misal foto member default, sampul default).

---

### 6. `/lib` 📚
Folder pustaka inti (core libraries) yang digunakan oleh SLiMS untuk memproses backend, routing, dan fitur sistem:
- **`contents/`**: File handler konten statis OPAC (`help.inc.php`, `librarian.inc.php`, dll.).
- **Subfolder Layanan Modern PSR-4**: `Auth/`, `Cache/`, `Cli/`, `Filesystems/`, `Form/`, `Http/`, `Log/`, `Migration/`, `Table/`, dll.
- **File Library Inti**: `Plugins.php`, `DB.php`, `Opac.php`, `utility.inc.php`, `helper.inc.php`.

---

### 7. `/simbio2` 🧬
Folder framework Simbio (Simfoni Bio) bawaan SLiMS yang menyediakan abstraksi database (`simbio_DB`), pembuat form AJAX (`simbio_GUI`), datagrid (`simbio_dbgrid`), manipulasi file (`simbio_FILE`), dan utilitas (`simbio_UTILS`).

---

### 8. `/locale` 🌐
Folder untuk file lokalisasi bahasa (i18n). Berisi file terjemahan seperti `en_US/`, `id_ID/`, dan lainnya.

---

### 9. `/plugins` 🧩
Folder tempat semua plugin pihak ketiga disimpan untuk memperluas fungsi SLiMS:
- Setiap plugin memiliki foldernya masing-masing di bawah `plugins/`.
- File utama pendaftaran plugin menggunakan format nama `*.plugin.php`.

---

### 10. `/repository` 🗂️
Folder penyimpanan berkas digital dokumen penuh (fulltext PDF, dokumen Word, dsb.) yang dilampirkan pada rekaman katalog bibliografi (`REPOBS`).

---

### 11. `/template` 🎨
Folder tema dan template tampilan antarmuka katalog publik (OPAC):
- **`template/default/`**: Tema standar bawaan SLiMS 9 Bulian.
- Tema kustom dapat ditambahkan sebagai subfolder baru di dalam direktori ini.

---

### 12. `/index.php` 🏁
File utama entry point untuk semua permintaan pengunjung OPAC ke sistem SLiMS.

---

## 📌 Panduan Praktis Penyesuaian SLiMS

- **Menambahkan Plugin**: Letakkan folder plugin baru di direktori `/plugins/`, lalu aktifkan via menu **Sistem > Plugin**.
- **Mengubah Template OPAC**: Letakkan folder tema baru di `/template/`, lalu pilih melalui menu **Sistem > Tema**.
- **Kredensial Database**: Diatur melalui file `config/database.php`.
- **Pengaturan Global Sistem**: Dikelola melalui menu **Sistem > Pengaturan Sistem** atau dikonfigurasi di `sysconfig.inc.php`.

---
Pemahaman terhadap tata letak direktori ini memudahkan pemeliharaan, pencadangan (*backup*), serta penyesuaian (*customization*) sistem perpustakaan Anda.
