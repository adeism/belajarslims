# Memperbaiki Error SLiMS Setelah Pemasangan Plugin Baru

Jika sistem SLiMS Anda mengalami *error* atau gagal dimuat setelah menginstal plugin baru, ikuti panduan ini untuk mengidentifikasi dan menonaktifkan plugin yang bermasalah.

## 🛠️ Panduan Perbaikan Plugin Error

### Langkah 1: Identifikasi Lokasi Plugin

Tentukan folder plugin mana yang baru saja dipasang dan dicurigai sebagai sumber *error*.

* **Lokasi**: Akses direktori `plugins` pada instalasi SLiMS Anda.

### Langkah 2: Nonaktifkan Plugin Sementara (Wajib) ⚠️

Cara tercepat untuk menonaktifkan plugin tanpa menghapusnya adalah dengan mengubah nama foldernya.

* **Tindakan**: Ganti nama folder plugin yang bermasalah.
* **Contoh**: Ubah nama `nama-plugin-error` menjadi `nama-plugin-error-disabled`.
    * *Sistem SLiMS tidak akan mengenali folder dengan nama baru ini, sehingga plugin tidak akan dimuat.*

### Langkah 3: Hapus Entri di Database (Opsional)

Langkah ini dilakukan jika *error* berlanjut setelah Langkah 2, atau jika Anda ingin benar-benar membersihkan jejak plugin di sistem. **Selalu lakukan *backup* database sebelum proses ini!**

1.  **Akses Database**: Masuk ke *database* SLiMS menggunakan **phpMyAdmin** atau *tool* manajemen MySQL lainnya.
2.  **Hapus Data**: Cari dan hapus entri/data plugin yang *error* dari tabel yang menyimpan daftar plugin aktif (lokasi tabel bisa bervariasi tergantung versi SLiMS).
3.  **Peringatan**: **Backup database Anda terlebih dahulu!**

### Langkah 4: Uji Coba

Setelah menonaktifkan plugin (Langkah 2) dan/atau membersihkan entri *database* (Langkah 3), verifikasi sistem SLiMS.

* **Tindakan**: Muat ulang (*Reload*) halaman SLiMS Anda di *browser*.
* **Verifikasi**: Pastikan *error* telah hilang dan *interface* SLiMS dapat diakses dengan normal.

## 💡 Jika Masalah Berlanjut

Apabila *error* tetap ada, ini menunjukkan bahwa plugin tersebut kemungkinan telah melakukan perubahan yang lebih dalam pada:

* **Struktur Database** SLiMS.
* **File Sistem Inti** SLiMS.

Dalam situasi ini, Anda mungkin perlu mencari bantuan di komunitas SLiMS atau melakukan pemulihan (*restore*) sistem ke kondisi sebelum plugin tersebut dipasang (menggunakan *backup* file dan *database*).
