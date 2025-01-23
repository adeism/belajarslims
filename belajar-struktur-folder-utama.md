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
Folder ini menyimpan file konfigurasi sistem SLiMS, termasuk pengaturan database dan pengaturan dasar aplikasi.

- **File Penting**:
  - `database.php`: Berisi pengaturan koneksi database. < dibuat ketika selesai installasi awal 
  - `sysconfig.inc.php`: Konfigurasi sistem utama.

---

### 4. `/docs` 📖
Berisi dokumentasi bawaan SLiMS. File di sini dapat digunakan untuk referensi pengembang atau administrator.

---

### 5. `/files` 📂
Folder untuk menyimpan file yang diunggah atau dihasilkan oleh SLiMS, seperti gambar sampul buku, file digital, dan dokumen lain.

- **Subfolder Penting**:
  - `/files/att`: Menyimpan file lampiran (attachments).
  - `/files/cover`: Menyimpan gambar sampul koleksi.
  - `/files/repository`: Menyimpan file dokumen digital (repository digital).

---

### 6. `/images` 🖼️
Folder ini digunakan untuk menyimpan gambar atau aset yang digunakan dalam tampilan antarmuka SLiMS.

---

### 7. `/lib` 📚
Folder inti yang berisi file sumber daya (libraries) yang digunakan oleh SLiMS untuk memproses berbagai fungsionalitas.

- **Subfolder Penting**:
  - `/lib/contents`: Berisi file PHP untuk konten halaman.
  - `/lib/ui`: Berisi file untuk komponen antarmuka pengguna.
  - `/lib/utils`: Berisi utilitas umum seperti fungsi dan helper.

---

### 8. `/locale` 🌐
Folder untuk file terjemahan. SLiMS mendukung banyak bahasa, dan file terjemahan disimpan di folder ini.

---

### 9. `/plugins` 🧩
Folder tempat semua plugin disimpan. Plugin adalah cara untuk menambahkan atau memodifikasi fungsi tanpa mengubah file inti SLiMS.

- **Struktur Plugin**:
  - Setiap plugin memiliki folder sendiri.
  - File utama plugin harus menggunakan ekstensi `.plugin.php`.

---

### 10. `/repository` 🗂️
Folder untuk menyimpan file digital yang diunggah pengguna melalui modul repository. Berbeda dengan `/files/repository`, folder ini biasanya digunakan untuk repositori publik.

---

### 11. `/template` 🎨
Folder ini berisi template atau tema yang digunakan untuk tampilan antarmuka OPAC (Online Public Access Catalog).

- **Subfolder Penting**:
  - `/template/default`: Template bawaan SLiMS.
  - `/template/your_custom_template`: Tambahkan template kustom Anda di sini.

---

### 12. `/uploads` 📤
Folder ini digunakan untuk menyimpan file yang diunggah secara umum, seperti file gambar anggota.

---

### 13. `/index.php` 🏁
File utama untuk mengarahkan semua permintaan HTTP ke SLiMS. Ini adalah entry point aplikasi.

---

## 📌 Cara Menggunakan Struktur Folder

- **Menambahkan Plugin**: Letakkan file atau folder plugin di dalam folder `/plugins/`.
- **Mengubah Template**: Tambahkan template baru ke folder `/template/`.
- **Modifikasi Konfigurasi**: Lakukan perubahan di file dalam folder `/config/`.

Dengan memahami struktur folder ini, Anda dapat lebih mudah mengelola dan memodifikasi SLiMS sesuai kebutuhan Anda.

---

> *tutorial ini dibuat dengan CustomGPT SLiMS Plugin Maker (ChatGPT) dengan fitur pengetahuan yang ada dalam folder rag (belum di review developer)
