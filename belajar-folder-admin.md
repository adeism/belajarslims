# Struktur Folder `/admin` 🔧

Folder `/admin` adalah inti dari area administrasi SLiMS. Di sini, pustakawan atau administrator dapat mengelola berbagai fungsi sistem, seperti katalogisasi, anggota, peminjaman, dan pengaturan sistem.

---

## 📁 Subfolder dan File Penting dalam `/admin`

### 1. `/admin/modules` 🧩
Folder ini menyimpan semua modul yang digunakan di area administrasi. Setiap modul adalah bagian dari fitur spesifik, seperti pengelolaan anggota, katalog, dan laporan.

- **Subfolder Penting**:
  - `/modules/bibliography` 📚: Modul untuk mengelola katalog koleksi.
  - `/modules/circulation` 🔄: Modul untuk proses peminjaman dan pengembalian buku.
  - `/modules/member` 🧍: Modul untuk mengelola data anggota perpustakaan.
  - `/modules/reporting` 📊: Modul untuk menghasilkan laporan, seperti statistik koleksi dan data peminjaman.
  - `/modules/system` ⚙️: Modul untuk mengatur konfigurasi sistem SLiMS.
  - `/modules/stock_take` 📦: Modul untuk melakukan stock opname koleksi.

---

### 2. `/admin/template` 🎨
Berisi template dan file CSS untuk area administrasi. Tampilan antarmuka admin SLiMS dikelola melalui file yang berada di folder ini.

- **File Penting**:
  - `admin_template.css`: File CSS utama untuk gaya tampilan di area admin.
  - `header.php` dan `footer.php`: Komponen header dan footer untuk area admin.

---

### 3. `/admin/media` 🖼️
Folder ini menyimpan aset media yang digunakan di area admin, seperti ikon dan gambar.

- **File Penting**:
  - `icon.png`: Ikon yang digunakan di area admin.
  - `loading.gif`: Gambar animasi yang digunakan saat proses loading.

---

### 4. `/admin/locale` 🌐
Folder ini berisi file terjemahan untuk area administrasi SLiMS. Jika sistem mendukung banyak bahasa, file terjemahan untuk area admin disimpan di sini.

---

### 5. `/admin/php` 📂
Berisi file-file PHP tambahan yang digunakan oleh modul-modul di area admin. Misalnya, file helper atau library tambahan yang tidak dimasukkan ke dalam folder `/lib`.

---

### 6. `/admin/index.php` 🏁
File utama yang menjadi entry point untuk seluruh area admin. Saat administrator login, semua permintaan diarahkan melalui file ini.

---

## 📌 Fungsi Utama Folder `/admin`

- **Pengelolaan Koleksi**: Mengelola bibliografi dan eksemplar koleksi perpustakaan.
- **Pengelolaan Anggota**: Menambah, mengedit, atau menghapus data anggota perpustakaan.
- **Sirkulasi**: Mencatat peminjaman dan pengembalian koleksi.
- **Pelaporan**: Menghasilkan laporan statistik penggunaan perpustakaan.
- **Pengaturan Sistem**: Mengelola konfigurasi SLiMS, termasuk pengaturan plugin, tema, dan lainnya.

---

> *Tutorial ini dibuat dengan CustomGPT SLiMS Plugin Maker (ChatGPT) dengan fitur pengetahuan yang ada dalam folder rag (belum di review developer)*
