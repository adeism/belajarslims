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

### 📋 **Manajemen Bibliografi**
- `biblio_list.inc.php`: Menampilkan daftar bibliografi, mendukung pencarian dengan CQL melalui berbagai mesin pencari (MySQL, Elasticsearch, Sphinx).
- `biblio_list_index.inc.php`: Menampilkan daftar bibliografi dari tabel indeks.
- `biblio_list_elasticsearch.inc.php`: Menampilkan daftar bibliografi dari Elasticsearch.
- `biblio_list_sphinx.inc.php`: Menampilkan daftar bibliografi dari Sphinx.
- `biblio_list_sqlite.inc.php`: Menampilkan daftar bibliografi dari SQLite.
- `biblio_list_mongodb.inc.php`: Menampilkan daftar bibliografi dari MongoDB.
- `biblio_list_model.inc.php`: Model abstrak untuk daftar bibliografi.
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
- `Plugins.php`: Manajemen plugin SLiMS 9 (`SLiMS\Plugins`, registrasi hook dan menu).
- `module.inc.php`: Kelas untuk manajemen modul aplikasi, termasuk pembuatan menu berdasarkan modul.

### 📄 **Konten dan Artikel**
- `content.inc.php`: Menampilkan konten dari database.
- `content_custom.inc.php`: Mengambil konten kustom dari database.
- `content_list.inc.php`: Menampilkan daftar item konten.

### 📊 **Format Data dan Utility**
- `Json.php`: Kelas untuk encoding dan decoding JSON.
- `Number.php`: Kelas untuk manipulasi angka.
- `Currency.php`: Kelas untuk format mata uang menggunakan NumberFormatter.
- `helper.inc.php`: Kumpulan fungsi utilitas global (`pluginUrl`, `writeLog`, `dd`, dll.).
- `utility.inc.php`: Kumpulan fungsi utilitas statis (`utility::havePrivilege`, `utility::writeLogs`).
- `Sanitizer.php`: Kelas untuk membersihkan input data.

### 🔑 **Keamanan**
- `ip_based_access.inc.php`: Membatasi akses modul berdasarkan alamat IP.
- `csrf/csrf-magic.php`: Perlindungan terhadap Cross-Site Request Forgery (CSRF).

### 📀 **Manajemen Data**
- `DB.php`: Kelas untuk manajemen database SLiMS 9 (`SLiMS\DB`, PDO & MySQLi wrapper).
- `marcxmlsenayan.inc.php`: Fungsi untuk memparsing file MARCXML.
- `modsxmlsenayan.inc.php`: Fungsi untuk memparsing file MODS XML.
- `modsxmlslims.inc.php`: Fungsi untuk memparsing file MODS XML lainnya.

### 🔍 **API dan Mesin Pencari**
- `api.inc.php`: Utilitas API untuk fungsi seperti mengambil data bibliografi, logging, dan pembaruan indeks pencarian (Solr/Elasticsearch).

### 📹 **Streaming dan Multimedia**
- `VideoStream.php`: Kelas untuk streaming video.

---

## 🗂️ Subfolder Layanan Modern di Direktori `lib` (PSR-4 Namespace)

SLiMS 9 Bulian mengadopsi struktur class modular berbasis namespace `SLiMS\*`:

1. **`Auth/`** (`SLiMS\Auth\*`): Driver autentikasi admin & member.
2. **`Cli/`** (`SLiMS\Cli\*`): Antarmuka baris perintah CLI SLiMS (perintah migrasi, aktivasi plugin, cache clear).
3. **`Csv/`** (`SLiMS\Csv\*`): Generator dan parser berkas CSV.
4. **`Debug/`** (`SLiMS\Debug\*`): Penanganan error dan debugging log.
5. **`Filesystems/`** (`SLiMS\Filesystems\*`): Abstraksi manajemen penyimpanan berkas.
6. **`Form/`** (`SLiMS\Form\*`): Komponen pembangun form modern.
7. **`Http/`** (`SLiMS\Http\*`): Abstraksi HTTP client dan response.
8. **`Log/`** (`SLiMS\Log\*`): Factory logging sistem terintegrasi.
9. **`Migration/`** (`SLiMS\Migration\*`): Engine migrasi database untuk core dan plugin (`Migration`, `Runner`).
10. **`Parcel/`** (`SLiMS\Parcel\*`): Engine instalasi paket plugin dan template (`Package`, `Installer`).
11. **`Polyglot/`** (`SLiMS\Polyglot\*`): Penanganan multi-bahasa dan lokalisasi.
12. **`SearchEngine/`** (`SLiMS\SearchEngine\*`): Abstraksi mesin pencari (MySQL, Solr, Sphinx, Elasticsearch, TNT Search).
13. **`Table/`** (`SLiMS\Table\*`): Skema manipulasi tabel database (`Schema`, `Blueprint`).
14. **`contents/`**: Berkas view konten publik OPAC (`help.inc.php`, `librarian.inc.php`, `login.inc.php`, dll.).
15. **`csrf/`**: Library proteksi CSRF token (`csrf-magic.php`).

---

## 💡 Tips Menggunakan File `lib`
1. **Gunakan SLiMS 9 Classes**: Manfaatkan class resmi seperti `\SLiMS\DB::getInstance()`, `\SLiMS\Plugins::getInstance()`, dan `\SLiMS\Migration\Migration`.
2. **Gunakan Helper Bawaan**: Gunakan fungsi dari `lib/helper.inc.php` dan `lib/utility.inc.php` daripada membuat fungsi utilitas kustom dari awal.
3. **Pemuatan Otomatis (Autoloading)**: Class di bawah namespace `SLiMS\` otomatis dimuat via `lib/autoload.php` tanpa perlu `require_once` manual.
