# README: SLiMS 9 Bulian - Rangkuman untuk Agen AI

SLiMS 9 Bulian adalah Sistem Manajemen Perpustakaan (Library Management System) berbasis web yang komprehensif, dikembangkan menggunakan PHP dan database MySQL/MariaDB. Sistem ini dirancang untuk mengelola semua aspek operasional perpustakaan, mulai dari katalogisasi bibliografi, manajemen sirkulasi, keanggotaan, hingga pelaporan. Arsitekturnya yang modular, terutama melalui sistem plugin yang kuat, memungkinkan pengembang untuk memperluas dan menyesuaikan fungsionalitas tanpa mengubah kode inti. Hal ini menjadikannya platform yang fleksibel dan dapat diadaptasi untuk berbagai kebutuhan perpustakaan. SLiMS juga menyediakan serangkaian pustaka internal bernama **Simbio** untuk mempercepat pengembangan antarmuka pengguna seperti formulir dan *data grid*.

---

### I. Struktur Direktori & Konfigurasi Utama

Memahami struktur direktori SLiMS adalah kunci untuk pengembangan.

-   `/admin/`: Berisi semua file dan logika untuk halaman administrasi.
    -   `/admin/modules/`: Direktori inti tempat semua modul utama (seperti `bibliography`, `circulation`, `system`) berada. Setiap modul memiliki file `submenu.php` untuk mendefinisikan navigasi.
-   `/plugins/`: Direktori utama untuk semua plugin kustom. SLiMS akan secara otomatis memindai direktori ini untuk file `*.plugin.php`.
-   `/lib/`: Berisi pustaka inti SLiMS dan pustaka pihak ketiga.
    -   `/lib/simbio2/`: Pustaka Simbio untuk antarmuka pengguna dan operasi database.
-   `/config/`: Direktori untuk file konfigurasi.
    -   `/config/database.php`: Konfigurasi koneksi database.
-   `/template/`: Berisi tema-tema untuk antarmuka publik (OPAC).
-   `sysconfig.inc.php`: File konfigurasi utama yang memuat semua pengaturan sistem, pustaka, dan memulai sesi.

---

### II. Sistem Plugin SLiMS

Sistem plugin memungkinkan penambahan atau modifikasi fungsionalitas tanpa mengubah kode inti.

#### A. Konsep Dasar

1.  **Lokasi**: Semua plugin ditempatkan di dalam direktori `/plugins/`.
2.  **File Utama**: Setiap plugin harus memiliki file utama dengan akhiran `.plugin.php` (contoh: `my_plugin.plugin.php`).
3.  **Header Plugin**: File utama harus diawali dengan blok komentar yang berisi metadata plugin.

    ```php
    <?php
    /**
     * Plugin Name: Nama Plugin Anda
     * Plugin URI: http://url-plugin-anda.com
     * Description: Deskripsi singkat tentang apa yang dilakukan plugin ini.
     * Version: 1.0.0
     * Author: Nama Anda
     * Author URI: http://website-anda.com
     */
    ```

#### B. Tipe Operasi Plugin

Plugin dapat berinteraksi dengan SLiMS melalui beberapa cara:

1.  **Hook**: Menjalankan fungsi pada titik-titik eksekusi tertentu dalam alur kerja SLiMS. Ini adalah cara paling umum untuk memodifikasi perilaku SLiMS.
    -   **Contoh Registrasi**:
        ```php
        use SLiMS\Plugins;
        $plugin = Plugins::getInstance();
        
        // Menjalankan fungsi sebelum data bibliografi disimpan
        $plugin->register('bibliography_before_save', function($data) {
            // Lakukan sesuatu dengan data bibliografi
            utility::jsToastr('Plugin Hook', 'Data akan disimpan!', 'info');
        });
        ```
    -   **Hook yang Tersedia**: `admin_session_after_start`, `content_before_load`, `bibliography_init`, `membership_after_save`, `circulation_after_successful_transaction`, dan banyak lagi.

2.  **Menu**: Menambahkan item menu baru ke submenu modul di area admin.
    -   **Contoh Registrasi**:
        ```php
        $plugin->registerMenu('bibliography', 'Label & Barcode Kustom', __DIR__ . '/label_generator.php');
        ```
    -   **Penjelasan**: Kode ini akan menambahkan menu "Label & Barcode Kustom" di dalam modul **Bibliografi**. Ketika diklik, file `label_generator.php` akan dimuat.

3.  **Path (OPAC)**: Mengganti halaman OPAC yang sudah ada atau membuat halaman baru.
    -   **Contoh Registrasi**:
        ```php
        // Mengganti halaman area anggota default
        $plugin->registerMenu('opac', 'member', __DIR__ . '/my_custom_member_page.php');
        ```
    -   **Penjelasan**: Ketika pengguna mengakses `index.php?p=member`, SLiMS akan memuat `my_custom_member_page.php` dari direktori plugin, bukan file aslinya.

#### C. Migrasi Database

Plugin dapat memiliki skema database sendiri. Fitur migrasi memfasilitasi pembuatan dan pembaruan tabel tanpa intervensi manual.

1.  Buat folder `migration/` di dalam direktori plugin Anda.
2.  Buat file PHP dengan format `<versi>_<NamaClass>.php`, contoh: `1_CreateReadCounterTable.php`.
3.  Class di dalam file harus meng-extend `\SLiMS\Migration\Migration` dan memiliki method `up()` dan `down()`.

    ```php
    <?php
    use SLiMS\Table\Schema;
    use SLiMS\Table\Blueprint;
    
    class CreateReadCounterTable extends \SLiMS\Migration\Migration
    {
        function up() {
            // Logika untuk membuat atau mengubah tabel
            Schema::create('read_counter', function(Blueprint $table) {
                $table->autoIncrement('id');
                $table->string('item_code', 20)->notNull();
                $table->string('title', 255)->notNull();
            });
        }
    
        function down() {
            // Logika untuk mengembalikan perubahan (menghapus tabel)
            Schema::drop('read_counter');
        }
    }
    ```

---

### III. Pustaka Simbio (GUI & Database)

Simbio adalah kumpulan kelas internal SLiMS untuk mempercepat pembuatan antarmuka dan interaksi database.

#### A. Komponen Antarmuka (GUI)

-   **`simbio_form_table_AJAX`**: Digunakan untuk membuat formulir HTML dengan struktur tabel yang datanya dikirim melalui AJAX. Sangat umum digunakan di seluruh modul admin.
    ```php
    // Contoh di admin/modules/system/app_user.php
    $form = new simbio_form_table_AJAX('mainForm', $_SERVER['PHP_SELF'], 'post');
    $form->addTextField('text', 'userName', __('Login Username').'*', $data['username']??'', 'class="form-control"');
    $form->addSelectList('userType', __('User Type').'*', $options, $data['user_type']??'');
    echo $form->printOut();
    ```

-   **`simbio_dbgrid`**: Kelas utama untuk membuat tabel data (datagrid) yang sudah dilengkapi dengan fitur *paging*, *sorting*, dan *checkbox* untuk aksi massal.
    ```php
    // Contoh di admin/modules/master_file/publisher.php
    $datagrid = new simbio_datagrid();
    $datagrid->setSQLColumn('p.publisher_id', 'p.publisher_name', 'p.last_update');
    $datagrid->setSQLorder('publisher_name ASC');
    $datagrid->setSQLCriteria("p.publisher_name LIKE '%$keywords%'");
    echo $datagrid->createDataGrid($dbs, 'mst_publisher AS p', 20, true);
    ```

-   **`report_dbgrid`**: Varian dari `simbio_dbgrid` yang dirancang khusus untuk laporan yang bisa dicetak dan diekspor ke format *spreadsheet*.

#### B. Operasi Database (`simbio_dbop`)

Kelas ini menyederhanakan operasi CRUD (Create, Read, Update, Delete) ke database.

```php
$sql_op = new simbio_dbop($dbs);

// Insert Data
$data['publisher_name'] = 'My Publisher';
$sql_op->insert('mst_publisher', $data);

// Update Data
$data['publisher_name'] = 'My New Publisher';
$sql_op->update('mst_publisher', $data, 'publisher_id = 1');

// Delete Data
$sql_op->delete('mst_publisher', 'publisher_id = 1');
```

---

### IV. Panduan Desain & Antarmuka

Untuk menjaga konsistensi tampilan saat membuat plugin, gunakan kelas CSS bawaan SLiMS.

#### A. Struktur Dasar Halaman Plugin

Gunakan struktur HTML berikut untuk halaman admin plugin Anda.

```php
<div class="menuBox">
  <div class="menuBoxInner someIcon">
    <div class="per_title">
      <h2><?php echo __('Judul Halaman Plugin Anda'); ?></h2>
    </div>
    <div class="sub_section">
      <!-- Letakkan tombol atau form pencarian di sini -->
      <div class="btn-group">
        <a href="#" class="btn btn-default"><?= __('Tombol Aksi 1') ?></a>
      </div>
      <form id="search" class="form-inline">
          <input type="text" name="keywords" class="form-control col-md-3" />
          <button type="submit" class="btn btn-default"><?= __('Cari') ?></button>
      </form>
    </div>
  </div>
</div>

<!-- Tampilkan datagrid atau form di sini -->
<?php
// contoh: echo $datagrid_result;
?>
```

#### B. Kelas CSS Umum

-   **`.menuBox`**: Kontainer utama untuk header halaman modul.
-   **`.per_title > h2`**: Judul utama halaman.
-   **`.sub_section`**: Area untuk tombol aksi dan form pencarian.
-   **`.infoBox`**: Kotak untuk menampilkan pesan informasi (latar biru/abu-abu).
-   **`.errorBox`**: Kotak untuk menampilkan pesan kesalahan (latar merah).
-   **`.s-table .table`**: Kelas yang harus digunakan untuk semua tabel data agar konsisten.
-   **`.s-btn .btn .btn-default`**: Kelas standar untuk tombol. Gunakan `.btn-primary`, `.btn-danger`, dll. untuk variasi warna.

Dengan mengikuti struktur dan kelas CSS ini, plugin Anda akan terlihat menyatu dengan antarmuka SLiMS lainnya.