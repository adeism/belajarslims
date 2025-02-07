Berikut adalah pemicu (trigger) yang akan membuat entri log di tabel `system_log`:

1.  **Login/Logout Pustakawan:**
    *   Ketika pustakawan berhasil login (`admin_logon.inc.php`).
    *   Ketika pustakawan logout (`admin/logout.php`).

2.  **Aktivitas Modul Bibliografi (`bibliography`):**
    *   Menyimpan data bibliografi baru (`bibliography/index.php`).
    *   Memperbarui data bibliografi (`bibliography/index.php`).
    *   Menghapus data bibliografi (`bibliography/index.php`).
    *   Mengunggah gambar sampul (`bibliography/index.php`).
    *   Menghapus gambar sampul (`bibliography/index.php`).
    *   Menambah, menghapus, atau mengubah relasi bibliografi.
    *   Menambah, menghapus, atau mengubah data eksemplar (item).
    *   Menambah, menghapus, atau mengubah data penulis.
    *   Menambah, menghapus, atau mengubah data subjek.
    *   Menambah, menghapus, atau mengubah lampiran (file attachment).
    *  Aktivitas copy-cataloging.

3.  **Aktivitas Modul Sirkulasi (`circulation`):**
    *   Memulai transaksi sirkulasi (`circulation/circulation_action.php`).
    *   Menyelesaikan transaksi sirkulasi (`circulation/circulation_action.php`).
    *   Mengembalikan item (termasuk *quick return*) (`circulation/circulation_action.php`).
    *   Memperpanjang peminjaman (`circulation/circulation_action.php`).
    *   Menambahkan reservasi (`circulation/circulation_action.php`).
    *   Menghapus reservasi (`circulation/circulation_action.php`).

4.  **Aktivitas Modul Keanggotaan (`membership`):**
   * Menambah anggota baru (`membership/index.php`)
   * Mengubah data anggota (`membership/index.php`)

5.  **Aktivitas Modul Sistem (`system`):**
    *   Mengubah konfigurasi sistem global (`system/index.php`).
    *   Menambah, mengubah, atau menghapus pengguna/pustakawan (`system/app_user.php`).
    *   Mengubah grup pengguna (`system/user_group.php`).
    *   Mengubah/membuat pola kode item (`system/biblio_indexes.php`).
    *   Mengaktifkan/menonaktifkan plugin (`system/plugin_action.php`).
    *   Memulai backup database (`system/backup_proc.php`).
    *   Mengubah pengaturan hari libur (`system/holiday.php`).
    *   Menghapus atau Mengubah Masterfile (author, topic, publisher, gmd, dll)

6.  **Aktivitas Stock Opname (`stock_take`):**
    *   Memulai proses stock opname.
    *   Menyelesaikan proses stock opname.
    *   Menyinkronkan ulang data item (`stock_take/resync.php`).
    *   Mengunggah daftar item (*item list*) (`stock_take/st_upload.php`).
    *   Kesalahan/error yang terjadi selama proses *stock take* (`stock_take/stock_take_action.php`).

7.   **Aktivitas File**
    *   Upload file pada saat proses penambahan data bibliografi.

8.   **Kesalahan/Error:**
    *   Kesalahan pada saat kueri database (`simbio_dbop.inc.php`).
    *   Kesalahan saat mengakses halaman yang membutuhkan otorisasi.

9. **Aktivitas lain**
    * Akses file, download dan upload.

Pemicu-pemicu ini tersebar di berbagai file PHP, terutama di dalam modul-modul SLiMS. Pencatatan log umumnya dilakukan dengan memanggil fungsi `utility::writeLogs()`, yang akan menyimpan informasi seperti tipe log, ID pengguna, lokasi log (modul), pesan log, dan waktu kejadian.  Ada juga penggunaan kelas `AdvancedLogging` (`AlLibrarian`) yang tampaknya merupakan sistem logging yang lebih canggih, tetapi ini belum sepenuhnya terimplementasi ("STILL EXPERIMENTAL").  Pemicu ini memberikan gambaran komprehensif tentang aktivitas yang dicatat oleh sistem log SLiMS.
