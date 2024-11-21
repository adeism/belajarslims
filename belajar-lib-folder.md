# 📚 SLiMS Library (lib) Directory Documentation

Direktori **lib** adalah inti dari **SLiMS (Senayan Library Management System)** yang mencakup kelas dan fungsi utama yang mendukung seluruh sistem. File dan subfolder di dalam direktori ini dirancang dengan arsitektur modular untuk menyediakan fleksibilitas dan kemudahan pengembangan.

---

## 📄 Ringkasan File Utama (Tanpa Subfolder)

Berikut adalah daftar file di folder `lib` (tanpa subfolder) beserta deskripsinya:

### 🔐 **Autentikasi dan Log**
- `admin_logon.inc.php`: Kelas untuk autentikasi administrator, mendukung native dan LDAP. Menggunakan hashing BCRYPT untuk keamanan.
- `member_logon.inc.php`: Kelas untuk autentikasi anggota perpustakaan, juga mendukung LDAP dan BCRYPT.
- `AdvancedLogging.php`: Logging tingkat lanjut menggunakan Monolog, mendukung logging ke file atau Elasticsearch.
- `AlLibrarian.php`: Logging tindakan pustakawan (login, logout, akses modul), mendukung Elasticsearch.
- `system_log.inc.php`: Logging global untuk sistem SLiMS.

### 📋 **Manajemen Bibliografi**
- `biblio_list.inc.php`: Menampilkan daftar bibliografi, mendukung pencarian dengan CQL melalui berbagai mesin pencari (MySQL, Elasticsearch, Sphinx).
- `biblio_list_index.inc.php`: Menampilkan daftar bibliografi dari tabel indeks.
- `biblio_list_elasticsearch.inc.php`: Menampilkan daftar bibliografi dari Elasticsearch.
- `biblio_list_sphinx.inc.php`: Menampilkan daftar bibliografi dari Sphinx.
- `biblio_list_sqlite.inc.php`: Menampilkan daftar bibliografi dari SQLite.
- `biblio_list_mongodb.inc.php`: Menampilkan daftar bibliografi dari MongoDB.
- `biblio_list_model.inc.php`: Model abstrak untuk daftar bibliografi.
- `biblio_log.inc.php`: Logging modifikasi data bibliografi.
- `detail.inc.php`: Menampilkan detail bibliografi dalam berbagai format (HTML, XML, JSON-LD, MARC).

### 🔄 **Transaksi dan Aktivitas**
- `circulation_api.inc.php`: API untuk fungsi sirkulasi seperti peminjaman.
- `member_session.inc.php`: Script untuk mengelola sesi anggota perpustakaan.
- `comment.inc.php`: Fungsi untuk menampilkan dan menambahkan komentar pada bibliografi.
- `Visitor.php`: Merekam statistik pengunjung perpustakaan.

### 🌐 **Manajemen URL dan HTTP**
- `AltoRouter.php`: Kelas routing URL menggunakan AltoRouter.
- `router.inc.php`: Routing URL yang disesuaikan untuk SLiMS.
- `http_request.inc.php`: Kelas untuk menangani HTTP request.
- `Url.php`: Kelas untuk manipulasi URL.

### 📦 **Cache dan Kinerja**
- `Cache.php`: Kelas untuk manajemen cache, mendukung berbagai provider (File, Redis, dll.).

### ✉️ **Manajemen Email**
- `Mail.php`: Kelas untuk mengirim email menggunakan PHPMailer, mendukung antrian email.

### ⚙️ **Manajemen Modul dan Plugin**
- `Plugins.php`: Manajemen plugin (registrasi hook dan menu).
- `module.inc.php`: Kelas untuk manajemen modul aplikasi, termasuk pembuatan menu berdasarkan modul.

### 📄 **Konten dan Artikel**
- `content.inc.php`: Menampilkan konten dari database.
- `content_custom.inc.php`: Mengambil konten kustom dari database.
- `content_list.inc.php`: Menampilkan daftar item konten.

### 📊 **Format Data dan Utility**
- `Json.php`: Kelas untuk encoding dan decoding JSON.
- `Number.php`: Kelas untuk manipulasi angka.
- `Currency.php`: Kelas untuk format mata uang menggunakan NumberFormatter.
- `helper.inc.php`: Kumpulan fungsi utilitas.
- `Sanitizer.php`: Kelas untuk membersihkan input data.

### 🔑 **Keamanan**
- `ip_based_access.inc.php`: Membatasi akses modul berdasarkan alamat IP.
- `csrf.inc.php`: Perlindungan terhadap Cross-Site Request Forgery (CSRF).

### 📀 **Manajemen Data**
- `DB.php`: Kelas untuk manajemen database, mendukung PDO dan MySQLi. Termasuk fungsi backup database.
- `marcxmlsenayan.inc.php`: Fungsi untuk memparsing file MARCXML.
- `modsxmlsenayan.inc.php`: Fungsi untuk memparsing file MODS XML.
- `modsxmlslims.inc.php`: Fungsi untuk memparsing file MODS XML lainnya.

### 🔍 **API dan Mesin Pencari**
- `api.inc.php`: Utilitas API untuk fungsi seperti mengambil data bibliografi, logging, dan pembaruan indeks pencarian (Solr/Elasticsearch).
- `search_biblio.inc.php`: Indeks untuk optimasi pencarian bibliografi.

### 📹 **Streaming dan Multimedia**
- `VideoStream.php`: Kelas untuk streaming video.

---

## 🗂️ Subfolder di Direktori `lib`

### 1. **Zend**  
Berisi pustaka Zend Framework yang mendukung berbagai fungsi di SLiMS.

### 2. **Cache**  
Berisi kelas untuk implementasi mekanisme caching (File, Redis, Memcached, dll.).

### 3. **Captcha**  
Mengimplementasikan CAPTCHA untuk meningkatkan keamanan.

### 4. **Cli**  
Berisi skrip dan kelas untuk Command Line Interface (CLI) SLiMS.

### 5. **collection**  
Berisi kelas untuk manajemen koleksi bibliografi.

### 6. **contents**  
Menghasilkan HTML untuk berbagai halaman OPAC.

### 7. **lang**  
File bahasa untuk mendukung internasionalisasi.

### 8. **Mail**  
Berisi kelas untuk manajemen pengiriman email, menggunakan PHPMailer.

### 9. **oaipmh**  
Berisi kelas untuk protokol OAI-PMH (Open Archives Initiative Protocol for Metadata Harvesting).

### 10. **SearchEngine**  
Berisi kelas untuk manajemen mesin pencari seperti Elasticsearch, Sphinx, atau Solr.

### 11. **Session**  
Kelas untuk manajemen sesi pengguna, mendukung beberapa driver.

---

## 💡 Tips Menggunakan File `lib`
1. **Eksplorasi Modularitas**: Banyak file dan subfolder di `lib` yang dirancang modular, sehingga Anda dapat mengembangkan fungsionalitas tanpa memodifikasi inti sistem.
2. **Pahami API**: File seperti `api.inc.php` menyediakan API yang kaya untuk berbagai operasi SLiMS.
3. **Gunakan Framework**: Manfaatkan pustaka pihak ketiga seperti Zend, Guzzle, dan Symfony untuk pengembangan lanjutan.

---

## 📚 Dokumentasi Tambahan

Untuk informasi lebih lengkap, kunjungi:
- **[Dokumentasi Resmi SLiMS](https://slims.web.id)**
- **[Repositori SLiMS di GitHub](https://github.com/slims)**

---

🚀 **Mulai eksplorasi direktori `lib` SLiMS dan kembangkan fitur sesuai kebutuhan perpustakaan Anda!** 🎉
