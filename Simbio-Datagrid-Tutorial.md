# 📘 Simbio Datagrid Tutorial 

Simbio **Datagrid** adalah komponen yang disediakan oleh Simbio untuk menampilkan data dalam format tabel dengan berbagai fitur seperti pencarian, pengurutan, dan paginasi. Tutorial ini akan memandu Anda melalui langkah-langkah dasar untuk membuat dan mengelola **Datagrid**.

_*tutorial ini dibuat dengan CustomGPT SLiMS Plugin Maker (ChatGPT) dengan fitur pengetahuan yang ada dalam folder rag (belum di review developer)_ 

---

## ✨ Fitur Utama Simbio Datagrid

- **Pencarian Data**: Mempermudah pengguna untuk mencari data spesifik.
- **Pengurutan Kolom**: Data dapat diurutkan berdasarkan kolom tertentu.
- **Paginasi**: Membagi data menjadi beberapa halaman untuk meningkatkan keterbacaan.
- **Kustomisasi Kolom**: Mendukung manipulasi data dalam kolom.
- **Integrasi SQL**: Mudah digunakan dengan query database.

---

## 📂 Struktur Dasar Datagrid

Simbio Datagrid menggunakan pendekatan berbasis PHP. Berikut adalah contoh struktur dasar kode untuk menggunakan Datagrid:

```php
<?php
require_once 'simbio/Simbio_Datagrid.php';

// Buat instance dari Datagrid
$datagrid = new Simbio_Datagrid();

// Konfigurasi data
$data = [
    ['ID' => 1, 'Nama' => 'John Doe', 'Email' => 'johndoe@example.com'],
    ['ID' => 2, 'Nama' => 'Jane Smith', 'Email' => 'janesmith@example.com'],
];

// Tentukan kolom yang ingin ditampilkan
$datagrid->setColumn(['ID', 'Nama', 'Email']);

// Masukkan data ke Datagrid
$datagrid->setData($data);

// Tampilkan tabel Datagrid
echo $datagrid->render();
?>
```

**Output**: Kode di atas akan menghasilkan tabel sederhana dengan tiga kolom: ID, Nama, dan Email.

---

## 🛠️ Langkah-Langkah Penggunaan Simbio Datagrid

### 1. **Menghubungkan ke Database**

Agar lebih dinamis, Datagrid sering digunakan untuk menampilkan data dari database. Berikut adalah contoh penggunaan dengan MySQL:

```php
<?php
require_once 'simbio/Simbio_Datagrid.php';
require_once 'config/database.php'; // Pastikan file ini berisi konfigurasi database Anda

// Buat koneksi ke database
global $dbs; // Variabel global untuk koneksi database di SLiMS

// Query untuk mendapatkan data
$query = "SELECT id, name AS Nama, email AS Email FROM users";

// Buat instance dari Datagrid
$datagrid = new Simbio_Datagrid();

// Jalankan query dan set hasilnya ke Datagrid
$datagrid->setSQLQuery($query, $dbs);

// Tentukan kolom yang ingin ditampilkan
$datagrid->setColumn(['ID', 'Nama', 'Email']);

// Tampilkan tabel Datagrid
echo $datagrid->render();
?>
```

### 2. **Menambahkan Paginasi**

Paginasi sangat penting untuk mengelola dataset besar. Simbio Datagrid mendukung paginasi bawaan dengan sangat mudah:

```php
$datagrid->enablePagination(true);
$datagrid->setPaginationLimit(10); // Batas data per halaman
```

Tambahkan kode ini sebelum `echo $datagrid->render();`.

### 3. **Mengubah Tampilan Data Kolom**

Anda dapat memformat atau mengubah tampilan data dalam kolom tertentu menggunakan callback:

```php
$datagrid->modifyColumnContent('Email', function ($data) {
    return "<a href='mailto:{$data}'>Kirim Email</a>";
});
```

Kode ini akan mengubah kolom "Email" menjadi tautan untuk mengirim email.

### 4. **Menambahkan Aksi pada Baris**

Tambahkan kolom aksi (seperti tombol edit atau hapus) ke dalam tabel:

```php
$datagrid->addActionColumn('Aksi', function ($row) {
    $id = $row['ID'];
    return "
        <a href='edit.php?id={$id}'>Edit</a> |
        <a href='delete.php?id={$id}' onclick='return confirm(\"Yakin ingin menghapus?\")'>Hapus</a>
    ";
});
```

---

## 💡 Tips dan Trik

1. **Optimalkan Query SQL**: Jika dataset Anda besar, gunakan query yang teroptimasi (contoh: `LIMIT` dan `OFFSET`) untuk menghindari masalah performa.
2. **Gunakan CSS Kustom**: Anda dapat menambahkan CSS untuk mempercantik tampilan tabel.
3. **Validasi Data**: Selalu validasi data sebelum menampilkan atau memprosesnya untuk mencegah serangan injeksi SQL.

---

## 📚 Dokumentasi Resmi

Untuk informasi lebih lengkap, silakan merujuk ke dokumentasi resmi **Simbio Datagrid** atau dokumentasi SLiMS.

---

Dengan menggunakan tutorial ini, Anda dapat dengan mudah mengimplementasikan **Simbio Datagrid** untuk menampilkan data dengan cepat dan interaktif. Selamat mencoba! 🎉
