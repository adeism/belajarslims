# Belajar Plugin

![image](https://github.com/user-attachments/assets/9867109a-b304-4920-8a33-99946507172c)


Diagram ini mengilustrasikan bagaimana SLiMS mengintegrasikan dan menjalankan fungsionalitas tambahan melalui sistem plugin saat pengguna mengakses aplikasi.

1.  **SLiMS Diakses (Entry Point & Bootstrapping Awal)**
    *   **Penjelasan:** Pengguna memulai interaksi dengan mengakses URL SLiMS. Ini memicu file entri utama (`index.php` untuk OPAC atau `admin/index.php` untuk area admin) untuk memulai proses pemuatan aplikasi.
    *   **Konsep Dev Guide:** Front Controller Pattern (sebagian besar permintaan diarahkan melalui file index utama), Bootstrapping (proses memuat konfigurasi inti, library, dan dependensi).
    *   **File Terlibat:**
        *   `index.php` (Root): Titik masuk utama OPAC.
        *   `admin/index.php`: Titik masuk utama area admin.
        *   `sysconfig.inc.php`: **File Kunci.** Dimuat sangat awal oleh file index. Bertugas mendefinisikan konstanta path (`SB`, `LIB`, `MDLBS`, dll.), memuat konfigurasi dasar, menginisialisasi koneksi database awal (`$dbs`), memuat autoloader, dan *memulai pemuatan sistem plugin* (`\SLiMS\Plugins::getInstance()->loadPlugins();`).

2.  **Sistem Plugin Bekerja (Inisialisasi & Pemuatan Kelas Inti)**
    *   **Penjelasan:** Setelah `sysconfig.inc.php` berjalan, class inti plugin (`Plugins.php`) diinisialisasi (biasanya sebagai Singleton). Autoloader memastikan class ini dan dependensinya (seperti `DB.php`) dapat ditemukan dan dimuat.
    *   **Konsep Dev Guide:** Autoloading (PSR-4), Dependency Injection (secara implisit, class `Plugins` membutuhkan akses ke `$dbs`), Singleton Pattern.
    *   **File Terlibat:**
        *   `sysconfig.inc.php`: (Seperti disebut di atas, memicu pemuatan).
        *   `lib/autoload.php`: Mekanisme pemuatan otomatis untuk class SLiMS dan dependensi (Composer/manual).
        *   `lib/Plugins.php`: Class utama yang mengelola logika plugin.
        *   `lib/DB.php`: Class untuk abstraksi database (PDO/mysqli).

3.  **Decision: Apakah ada plugin yg aktif? (Pemeriksaan Status Plugin)**
    *   **Penjelasan:** Class `Plugins` akan berinteraksi dengan database untuk mengambil daftar plugin yang statusnya "aktif". Status ini disimpan dalam tabel `plugins`.
    *   **Konsep Dev Guide:** Interaksi Database, Skema Database (memahami struktur tabel `plugins`).
    *   **File Terlibat:**
        *   `lib/Plugins.php`: Metode `getActive()` (atau metode internal serupa) melakukan query ke DB.
        *   `lib/DB.php` (atau `mysqli`): Menyediakan koneksi dan fungsi query.
        *   *Database:* Tabel `plugins` (menyimpan ID unik, path, status aktif/nonaktif, opsi).

4.  **Proses: Muat plugin aktif (Inklusi Kode & Registrasi)**
    *   **Penjelasan:** Sistem plugin melakukan iterasi pada daftar plugin aktif. Untuk setiap plugin aktif, file utamanya (yang berakhiran `.plugin.php`) akan dimuat menggunakan `require_once`. Saat file ini dimuat, kode di dalamnya dieksekusi. Kode ini *harus* berisi pemanggilan metode `registerMenu()` dan/atau `registerHook()` (atau alias statisnya `Plugins::menu()` dan `Plugins::hook()`) untuk mendaftarkan fungsionalitas plugin ke sistem SLiMS. Informasi *header* plugin (nama, deskripsi, dll.) juga biasanya dibaca pada tahap ini atau saat penemuan plugin awal.
    *   **Konsep Dev Guide:** Struktur Plugin (File utama, header komentar), API Plugin (`registerMenu`, `registerHook`), Konvensi Penamaan File (`.plugin.php`), Manajemen Dependensi (jika plugin menggunakan Composer, `vendor/autoload.php`-nya mungkin dimuat di sini atau melalui autoloader utama).
    *   **File Terlibat:**
        *   `lib/Plugins.php`: Metode `loadPlugins()`.
        *   Setiap file `<nama_plugin>.plugin.php` yang aktif di dalam direktori `plugins/`.

5.  **Decision: plugin dipanggil di OPAC/Admin? (Pemicuan Hook & Resolusi Rute/Menu)**
    *   **Penjelasan:** Setelah semua plugin aktif dimuat dan mendaftarkan diri, SLiMS melanjutkan pemuatan halaman atau modul yang diminta. Pada titik-titik tertentu dalam kode inti SLiMS (misalnya, sebelum/sesudah memuat konten OPAC, sebelum/sesudah menyimpan data bibliografi, saat membangun menu admin), panggilan ke `Plugins::run()` atau `$plugin->execute()` dilakukan. Sistem plugin kemudian memeriksa *registry*-nya (daftar hook/menu yang sudah didaftarkan) untuk melihat apakah ada *callback* atau *endpoint* yang cocok dengan hook yang dipicu atau menu yang diminta (berdasarkan URL atau parameter).
    *   **Konsep Dev Guide:** Hooks (titik ekstensi berbasis event), Menu (titik ekstensi berbasis navigasi), Routing (bagaimana URL diterjemahkan ke aksi/konten, termasuk endpoint plugin), Konteks Eksekusi (memahami variabel apa yang tersedia saat hook/menu dijalankan).
    *   **File Terlibat:**
        *   **Hooks:** `lib/Plugins.php` (metode `execute`/`run`), File inti SLiMS yang memicu hook (contoh: `lib/Opac.php` untuk hook `content_after_load`, `admin/modules/bibliography/index.php` untuk hook terkait bibliografi).
        *   **Menus (Admin):** `lib/module.inc.php` (memuat daftar menu modul, termasuk dari plugin), `admin/index.php` (memproses request modul), `admin/plugin_container.php` (sering digunakan sebagai *wrapper* untuk memuat file endpoint menu plugin admin, dipanggil berdasarkan parameter `mod` dan `id` plugin).
        *   **Menus (OPAC):** `index.php`, `lib/Opac.php`. Penanganan menu OPAC plugin lebih fleksibel, seringkali melalui hook yang memeriksa parameter URL (`?p=nama_plugin_menu`) atau logika internal plugin.

6.  **Proses: Tampilkan hasil prosesnya (Eksekusi Logika Plugin)**
    *   **Penjelasan:** Jika ada kecocokan pada langkah sebelumnya, kode spesifik plugin akan dieksekusi. Untuk *hook*, ini adalah fungsi *callback* yang didaftarkan. Untuk *menu*, ini adalah file PHP yang ditunjuk sebagai *endpoint* saat menu didaftarkan (seringkali `index.php` di dalam folder plugin itu sendiri, atau file lain). Kode ini melakukan tugas utama plugin, seperti menampilkan HTML, memodifikasi data, berinteraksi dengan API eksternal, dll.
    *   **Konsep Dev Guide:** Logika Bisnis Plugin, Akses ke API SLiMS (fungsi utilitas, objek `$dbs`, `$sysconf`), Output HTML/JavaScript, Interaksi dengan Template.
    *   **File Terlibat:**
        *   File PHP endpoint/callback plugin (misal: `plugins/rekap-plus-lokasi/index.php`, atau class/fungsi yang didefinisikan dalam plugin).

7.  **Selesai (Rendering & Finalisasi)**
    *   **Penjelasan:** Setelah semua logika inti SLiMS dan logika plugin yang relevan dieksekusi, SLiMS merakit output akhir. Jika plugin menghasilkan output HTML (misalnya, untuk menu admin atau hook OPAC), output tersebut akan diintegrasikan ke dalam template halaman utama. SLiMS kemudian mengirimkan respons HTML lengkap ke browser pengguna. Jika tidak ada plugin aktif atau tidak ada yang dipicu, SLiMS langsung menuju ke tahap rendering akhir ini.
    *   **Konsep Dev Guide:** Sistem Templating SLiMS (bagaimana template dimuat dan variabel di-assign), Urutan Eksekusi.
    *   **File Terlibat:**
        *   `index.php` / `admin/index.php` (titik akhir sebelum mengirim output).
        *   File-file template (`template/default/index_template.inc.php`, `admin/admin_template/default/index_template.inc.php`, dan file-file bagian `parts/`).
        *   `lib/Opac.php` (untuk OPAC, metode `parseToTemplate()` merakit semuanya).

**Konsep Tambahan dari Panduan Pengembangan:**

*   **Best Practices:** Menulis kode yang bersih, terdokumentasi, menghindari modifikasi file inti SLiMS, menggunakan API yang tersedia, menangani keamanan (XSS, SQL Injection).
*   **Database Migration:** (Seperti yang Anda lihat di plugin `read_counter`) Mengelola perubahan skema database yang diperlukan oleh plugin menggunakan sistem migrasi agar mudah di-upgrade atau dihapus (`plugins/nama_plugin/migration/`). File seperti `1_CreateReadCounterTable.php` adalah contohnya. Class `SLiMS\Migration\Runner` dan `SLiMS\Table\Schema` terlibat.
*   **Configuration:** Plugin seringkali membutuhkan konfigurasi. Ini bisa disimpan dalam file `config.php` di dalam folder plugin, atau (untuk yang lebih kompleks) disimpan dalam tabel `setting` SLiMS.
*   **Internationalization (i18n):** Menggunakan fungsi `__()` untuk string yang dapat diterjemahkan agar plugin mendukung berbagai bahasa.
*   **Packaging:** Memaketkan plugin dalam format ZIP (seperti yang ditangani `admin/modules/system/plugins.php` dan class `SLiMS\Parcel\Package`) untuk distribusi dan instalasi yang mudah.

**Tabel Ringkasan (Diperbarui)**

| Langkah Flowchart                 | File Utama Terlibat                                                                                                                                      | Fungsi Utama & Konsep Dev Guide Terkait                                                                                                                                                                                             |
| :-------------------------------- | :------------------------------------------------------------------------------------------------------------------------------------------------------- | :--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| SLiMS Diakses                     | `index.php`, `admin/index.php`                                                                                                                             | Titik masuk aplikasi (Front Controller).                                                                                                                                                                                          |
| Sistem Plugin Bekerja             | `sysconfig.inc.php`, `lib/autoload.php`, `lib/Plugins.php`, `lib/DB.php`                                                                                   | Bootstrapping, Inisialisasi Sistem Plugin, Autoloading Class Inti, Koneksi DB awal.                                                                                                                                             |
| Apakah ada plugin yg aktif?       | `lib/Plugins.php`, `lib/DB.php`                                                                                                                            | Memeriksa status plugin di tabel `plugins` database.                                                                                                                                                                             |
| Muat plugin aktif                 | `lib/Plugins.php`, Setiap file `<nama_plugin>.plugin.php` yang aktif                                                                                     | Inklusi (`require_once`) file utama plugin, Eksekusi kode registrasi (`registerMenu`, `registerHook`), Parsing header plugin.                                                                                                           |
| plugin dipanggil di OPAC/Admin? | `lib/Plugins.php`, `lib/Opac.php`, `lib/module.inc.php`, `admin/plugin_container.php`, file inti SLiMS lainnya, file `.plugin.php` (saat registrasi). | Pemicuan Hook: Sistem plugin memeriksa *registry* saat hook dipanggil oleh kode inti. Resolusi Menu: Sistem menu admin (`module.inc.php`) atau logika OPAC (`Opac.php`/plugin) memeriksa URL/parameter untuk mencocokkan menu plugin. |
| Tampilkan hasil prosesnya         | File PHP endpoint/callback plugin (misal: `plugins/nama_plugin/index.php`)                                                                                  | Eksekusi logika bisnis spesifik plugin, interaksi dengan SLiMS API, menghasilkan output atau memodifikasi data.                                                                                                                    |
| Selesai                           | `index.php`, `admin/index.php`, File template (`*.inc.php`), `lib/Opac.php` (`parseToTemplate`)                                                            | Merakit output akhir dari SLiMS dan plugin (jika ada), mengirimkan respons HTML ke browser menggunakan sistem templating.                                                                                                         |

Semoga penjelasan yang lebih rinci ini memberikan gambaran yang lebih lengkap tentang bagaimana sistem plugin SLiMS bekerja dan file-file mana saja yang berperan di setiap tahapannya!
