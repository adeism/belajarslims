# Belajar Plugin

![image](https://github.com/user-attachments/assets/9867109a-b304-4920-8a33-99946507172c)


**Penjelasan Diagram Alir Sistem Plugin SLiMS**

Diagram ini menggambarkan bagaimana SLiMS memproses dan menjalankan plugin ketika sebuah halaman diakses, baik di OPAC maupun di area admin.

1.  **SLiMS Diakses (Entry Point)**
    *   **Penjelasan:** Ini adalah titik awal ketika pengguna membuka halaman SLiMS, baik halaman utama OPAC maupun halaman admin.
    *   **File Terlibat:**
        *   `index.php` (di root SLiMS): File utama untuk menangani semua permintaan ke OPAC.
        *   `admin/index.php`: File utama untuk menangani semua permintaan ke area admin.

2.  **Sistem Plugin Bekerja (Initialization & Loading)**
    *   **Penjelasan:** Setelah SLiMS mulai dimuat, sistem plugin diinisialisasi. Ini terjadi sangat awal dalam proses loading SLiMS. File konfigurasi utama akan memanggil mekanisme untuk memuat plugin.
    *   **File Terlibat:**
        *   `sysconfig.inc.php`: File konfigurasi inti SLiMS. File ini sangat penting karena biasanya di sinilah proses inisialisasi utama terjadi, termasuk memuat library inti dan memanggil fungsi untuk memuat plugin aktif (`\SLiMS\Plugins::getInstance()->loadPlugins();`).
        *   `lib/Plugins.php`: Ini adalah *class* inti yang mengelola semua aspek sistem plugin: menemukan plugin, memeriksa status aktif, mendaftarkan hook dan menu, serta mengeksekusi hook. Fungsi `loadPlugins()` di dalam class ini yang bertanggung jawab memuat file utama dari plugin yang aktif.
        *   `lib/DB.php` (dan driver database spesifik): Digunakan oleh `Plugins.php` untuk berinteraksi dengan database.

3.  **Decision: Apakah ada plugin yg aktif?**
    *   **Penjelasan:** Sistem plugin memeriksa database (tabel `plugins`) untuk mengetahui plugin mana saja yang statusnya aktif (belum dihapus secara logis/fisik).
    *   **File Terlibat:**
        *   `lib/Plugins.php`: Metode `getActive()` mengambil data plugin aktif dari database.
        *   `lib/DB.php`: Menyediakan koneksi dan metode untuk query ke tabel `plugins`.

4.  **Proses: Muat plugin aktif**
    *   **Penjelasan:** Jika ada plugin yang aktif, file utama (`.plugin.php`) dari setiap plugin aktif tersebut akan dimuat menggunakan `require_once`. Saat dimuat, kode di dalam file plugin tersebut (seperti registrasi hook dan menu) akan dieksekusi.
    *   **File Terlibat:**
        *   `lib/Plugins.php`: Metode `loadPlugins()` melakukan `require_once` pada path file plugin yang aktif.
        *   File `.plugin.php` dari masing-masing plugin aktif (misal: `plugins/nama_plugin/nama_plugin.plugin.php`).

5.  **Decision: plugin dipanggil di OPAC/Admin?**
    *   **Penjelasan:** Setelah plugin aktif dimuat dan hook/menu didaftarkan, SLiMS melanjutkan proses normalnya. Sistem plugin kemudian memeriksa apakah *request* saat ini (URL yang diakses atau titik eksekusi dalam kode SLiMS) memicu salah satu *hook* atau *menu* yang telah didaftarkan oleh plugin yang aktif.
    *   **File Terlibat:**
        *   **Hooks:**
            *   `lib/Plugins.php`: Metode `execute()` atau `run()` dipanggil dari berbagai titik di kode inti SLiMS (misalnya, di dalam `lib/Opac.php` untuk hook OPAC, atau di akhir proses simpan data bibliografi). Metode ini memeriksa apakah ada fungsi callback yang terdaftar untuk hook tersebut.
            *   File inti SLiMS lainnya yang memanggil `Plugins::run()` atau `$plugin->execute()`, seperti `lib/Opac.php`, `admin/modules/bibliography/index.php` (tergantung implementasi hook).
        *   **Menus (Admin):**
            *   `lib/module.inc.php`: Class ini mengambil daftar menu, termasuk yang ditambahkan oleh plugin melalui `Plugins::getInstance()->getMenus()`.
            *   `admin/index.php`: Memproses modul yang diminta dan memuat submenu yang relevan.
            *   `admin/plugin_container.php`: Seringkali bertindak sebagai *wrapper* untuk memuat file PHP endpoint yang didaftarkan oleh plugin menu admin. URL menu plugin biasanya akan mengarah ke file ini dengan parameter `mod` dan `id` plugin.
        *   **Menus (OPAC):**
            *   `index.php` & `lib/Opac.php`: Penanganan menu OPAC plugin biasanya lebih bergantung pada bagaimana plugin tersebut diregistrasikan. Bisa jadi melalui parameter `p=` di URL yang kemudian diperiksa oleh hook plugin, atau cara lain yang ditentukan plugin.

6.  **Proses: Tampilkan hasil prosesnya**
    *   **Penjelasan:** Jika request saat ini memicu hook atau menu plugin, maka kode yang terkait dengan plugin tersebut (fungsi *callback* untuk hook, atau file PHP *endpoint* untuk menu) akan dieksekusi. Hasil dari eksekusi ini (bisa berupa output HTML, modifikasi data, atau aksi lainnya) akan ditampilkan atau diintegrasikan ke dalam alur SLiMS.
    *   **File Terlibat:**
        *   File PHP spesifik milik plugin yang berisi logika *callback* hook atau yang berfungsi sebagai *endpoint* menu (misal: `plugins/nama_plugin/index.php` atau file lain yang ditentukan saat `registerMenu`).

7.  **Selesai**
    *   **Penjelasan:** Ini adalah titik akhir dari alur kerja plugin untuk request saat ini. SLiMS melanjutkan proses rendering halaman atau menyelesaikan tugasnya. Jika tidak ada plugin aktif atau tidak ada plugin yang dipanggil, SLiMS langsung menuju ke tahap ini dari decision point sebelumnya.
    *   **File Terlibat:**
        *   `index.php` / `admin/index.php`: Melanjutkan proses rendering.
        *   File-file template (misal: `template/default/index_template.inc.php`, `admin/admin_template/default/index_template.inc.php`).

**Ringkasan File Utama dan Perannya dalam Sistem Plugin:**

| Langkah Flowchart                 | File Utama Terlibat                                                                                                                                      | Fungsi Utama dalam Konteks Ini                                                                                                                               |
| :-------------------------------- | :------------------------------------------------------------------------------------------------------------------------------------------------------- | :----------------------------------------------------------------------------------------------------------------------------------------------------------- |
| SLiMS Diakses                     | `index.php`, `admin/index.php`                                                                                                                             | Titik masuk aplikasi.                                                                                                                                        |
| Sistem Plugin Bekerja             | `sysconfig.inc.php`, `lib/Plugins.php`, `lib/DB.php`                                                                                                       | Inisialisasi SLiMS, memuat class `Plugins`, memanggil `loadPlugins()`.                                                                                     |
| Apakah ada plugin yg aktif?       | `lib/Plugins.php`, `lib/DB.php`                                                                                                                            | Metode `getActive()` memeriksa tabel `plugins` di database.                                                                                                    |
| Muat plugin aktif                 | `lib/Plugins.php`, File `.plugin.php` masing-masing plugin                                                                                               | Metode `loadPlugins()` melakukan `require_once` file utama plugin, menjalankan kode registrasi hook/menu di dalamnya.                                          |
| plugin dipanggil di OPAC/Admin? | `lib/Plugins.php`, `lib/Opac.php`, `lib/module.inc.php`, `admin/plugin_container.php`, file inti SLiMS lainnya, file `.plugin.php` (untuk registrasi). | Memeriksa apakah URL/titik eksekusi saat ini cocok dengan hook/menu yang didaftarkan. Mengambil daftar menu plugin. Memuat endpoint plugin admin. |
| Tampilkan hasil prosesnya         | File PHP spesifik milik plugin (misal: `index.php` di folder plugin)                                                                                        | Berisi logika inti plugin yang dieksekusi (callback hook atau isi halaman menu).                                                                              |
| Selesai                           | `index.php`, `admin/index.php`, File template (`*.inc.php`)                                                                                                | Melanjutkan proses normal SLiMS, merender output akhir ke browser.                                                                                             |

Singkatnya, `sysconfig.inc.php` memulai proses, `lib/Plugins.php` adalah otak pengelola plugin, `lib/DB.php` menyediakan akses data status plugin, `lib/Opac.php` dan `lib/module.inc.php` (serta file inti lain) memanggil eksekusi hook/menu, dan `admin/plugin_container.php` membantu memuat endpoint plugin di admin. File `.plugin.php` dan file endpoint lainnya di dalam folder plugin berisi kode spesifik plugin tersebut.
