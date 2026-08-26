# 📚 Ringkasan Simbio untuk Pemula

Framework Simbio adalah "kotak peralatan" yang berisi kode-kode siap pakai bisa juga digunakan untuk mempermudah pembuatan plugin SLiMS Senayan. Bayangkan ini seperti kumpulan alat dasar yang sudah disiapkan, jadi Anda tidak perlu membuat semuanya dari awal.  Di bawah ini adalah daftar file-file penting dalam Simbio, diibaratkan seperti bagian-bagian dari kotak peralatan tersebut:

| Ikon  | Nama File                                     | Penjelasan untuk Developer & Pemula                                                                                                                               |
| :----: | --------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------- |
| 🏠    | **simbio.inc.php**                            | **Rumah Utama Simbio!** 🏡 File inti dari Simbio yang memuat aturan dasar dan fungsi global. |
| 🧰    | **simbio_DB/simbio_dbop.inc.php**            | **Tukang Operasi Database!** 🧰 Membantu abstraksi query CRUD database sederhana. |
| 📊    | **simbio_DB/datagrid/simbio_dbgrid.inc.php**  | **Pembuat Tabel Data Otomatis!** 📊 Mengubah data database menjadi tabel admin interaktif lengkap dengan paging, sorting, dan filter pencarian. |
| 🏛️    | **simbio_DB/isis/simbio_isis.inc.php**       | **Penghubung Database ISIS!** 🏛️ Jembatan integrasi khusus untuk database ISIS. |
| 🗄️    | **simbio_DB/mysql/simbio_mysql.inc.php**     | **Konektor Database MySQL!** 🗄️ Driver koneksi dan eksekusi query MySQL/MariaDB. |
| 🐘    | **simbio_DB/pgsql/simbio_pgsql.inc.php**     | **Konektor Database PostgreSQL!** 🐘 Driver koneksi untuk database PostgreSQL. |
| 🧳    | **simbio_DB/sqlite/simbio_sqlite.inc.php**   | **Konektor Database SQLite!** 🧳 Driver database SQLite berbasis file. |
| 📂    | **simbio_FILE/simbio_directory.inc.php**     | **Pengelola Folder!** 📂 Helper inspeksi direktori dan berkas server. |
| 📤    | **simbio_FILE/simbio_file_upload.inc.php**   | **Juru Upload File!** 📤 Helper penanganan unggah berkas, validasi MIME type, dan ukuran. |
| ⌨️    | **simbio_GUI/form_maker/simbio_form_element.inc.php** | **Koleksi Elemen Form!** ⌨️ Komponen form HTML (input teks, select, checkbox, radio, datepicker). |
| 🗂️    | **simbio_GUI/paging/simbio_paging.inc.php**    | **Paging Otomatis!** 🗂️ Generator navigasi pagination halaman tabel data. |
| 🏓    | **simbio_GUI/table/simbio_table.inc.php**     | **Generator Tabel HTML!** 🏓 Kelas dasar pembuat markup tabel HTML terstruktur. |
| 🎭    | **simbio_GUI/template_parser/simbio_template_parser.inc.php** | **Parser Template!** 🎭 Penggabung template HTML dengan variabel data dinamis. |
| 🖋️    | **simbio_GUI/form_maker/simbio_form_table.inc.php** | **Pembuat Form Berbasis Tabel!** 🖋️ Form builder yang tersusun dalam layout tabel admin. |
| 🔍    | **simbio_UTILS/simbio_qparser.inc.php**        | **Parser Query Pencarian!** 🔍 Penerjemah keyword pencarian pengguna menjadi sintaks SQL. |
| 🗓️    | **simbio_UTILS/simbio_date.inc.php**         | **Helper Tanggal & Waktu!** 🗓️ Fungsi manipulasi tanggal, penghitungan hari libur, dan selisih hari. |
| 🛡️    | **simbio_UTILS/simbio_security.inc.php**      | **Helper Keamanan!** 🛡️ Fungsi sanitasi input, token, dan validasi keamanan sistem. |

---

### 💡 Tips Penting: Lazy Loading Guard
Ketika menggunakan komponen Simbio GUI di dalam plugin Anda, **selalu gunakan guard `class_exists()`** untuk mencegah error `Cannot declare class simbio_table_field, because the name is already in use`:

```php
if (!class_exists('simbio_table')) {
    require_once SIMBIO . 'simbio_GUI/table/simbio_table.inc.php';
}
if (!class_exists('simbio_datagrid')) {
    require_once SIMBIO . 'simbio_DB/datagrid/simbio_dbgrid.inc.php';
}
```
