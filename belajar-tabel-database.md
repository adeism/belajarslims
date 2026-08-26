# 📚 Belajar SLiMS: Tentang Tabel Database

**SLiMS (Senayan Library Management System)** adalah sistem manajemen perpustakaan berbasis web yang fleksibel dan kaya fitur. Repositori ini menjelaskan tabel-tabel utama dalam database SLiMS, termasuk fungsinya, sehingga mempermudah pemahaman relasi antar tabel dan penggunaannya.

---

## 🏗️ Daftar Tabel Database SLiMS

Berikut adalah daftar tabel utama yang terdapat dalam database SLiMS, dikelompokkan berdasarkan fungsinya:

### 📋 Tabel Sistem dan Konfigurasi
- **`backup_log`**: Mencatat riwayat backup database.
- **`group_access`**: Mendefinisikan hak akses grup user terhadap modul dan sub-menu.
- **`mst_module`**: Tabel definisi modul aplikasi.
- **`plugins`**: Mencatat registrasi dan status aktivasi plugin SLiMS 9.
- **`setting`**: Tabel untuk konfigurasi dan pengaturan sistem global.
- **`system_log`**: Mencatat log sistem secara global (login, perubahan data, dsb.).
- **`cache`**: Penyimpanan cache sistem berbasis database.

---

### 📚 Tabel Koleksi Perpustakaan
- **`biblio`**: Tabel utama untuk data bibliografi katalog perpustakaan.
- **`biblio_attachment`**: Menyimpan metadata lampiran berkas pada data bibliografi.
- **`biblio_author`**: Hubungan many-to-many antara bibliografi dengan data pengarang (`mst_author`).
- **`biblio_custom`**: Data atribut tambahan kustom bibliografi.
- **`biblio_log`**: Mencatat riwayat modifikasi entri katalog bibliografi.
- **`biblio_mark`**: Bookmark / penanda koleksi favorit yang disimpan anggota.
- **`biblio_relation`**: Hubungan/relasi antar rekaman bibliografi (koleksi terkait).
- **`biblio_topic`**: Hubungan data bibliografi dengan subjek/topik otoritas (`mst_topic`).
- **`files`**: Menyimpan data metadata berkas lampiran.
- **`files_read`**: Statistik jumlah akses/unduhan berkas lampiran.
- **`index_documents`**: Indeks dokumen untuk fitur pencarian katalog.
- **`index_words`**: Indeks kosakata untuk optimasi pencarian katalog.
- **`item`**: Data item fisik / eksemplar koleksi perpustakaan (barcode, lokasi, status).
- **`item_custom`**: Data atribut tambahan kustom eksemplar.
- **`serial`**: Data langganan terbitan berkala (majalah/jurnal).
- **`kardex`**: Kartu kendali tracking penerimaan terbitan berkala (serial kardex).
- **`stock_take`**: Sesi pelaksanaan inventarisasi / stok opname koleksi.
- **`stock_take_item`**: Detail item eksemplar yang diperiksa pada sesi stok opname.

---

### 👥 Tabel Anggota dan Pengguna
- **`member`**: Tabel utama untuk data anggota perpustakaan.
- **`member_custom`**: Data atribut tambahan kustom anggota perpustakaan.
- **`user`**: Tabel akun pengguna backend (staf perpustakaan / administrator).
- **`user_group`**: Referensi kelompok hak akses pengguna (*User Groups*).
- **`user_tokens`**: Token autentikasi sesi dan *remember me* pengguna.

---

### 🔄 Tabel Transaksi Sirkulasi
- **`loan`**: Mencatat transaksi peminjaman aktif yang sedang berjalan.
- **`loan_history`**: Riwayat arsip seluruh transaksi peminjaman yang telah selesai.
- **`reserve`**: Mencatat pemesanan / reservasi koleksi oleh anggota.
- **`fines`**: Mencatat data denda keterlambatan sirkulasi anggota perpustakaan.
- **`holiday`**: Daftar hari libur perpustakaan untuk perhitungan jatuh tempo dan denda.

---

### 🔧 Tabel Master Referensi dan Metadata
- **`mst_author`**: Referensi data otoritas pengarang / kreator.
- **`mst_carrier_type`**: Referensi RDA tipe pembawa konten (carrier type).
- **`mst_coll_type`**: Referensi jenis koleksi (Reference, Fiction, Text, dsb.).
- **`mst_content_type`**: Referensi RDA tipe konten.
- **`mst_custom_field`**: Definisi kolom kustom (custom fields) pada modul.
- **`mst_frequency`**: Referensi frekuensi terbitan berseri.
- **`mst_gmd`**: Referensi General Material Designation (GMD).
- **`mst_item_status`**: Referensi status item/eksemplar (Available, Repair, Missing, dsb.).
- **`mst_label`**: Referensi label/tanda klasifikasi warna koleksi.
- **`mst_language`**: Referensi bahasa dokumen.
- **`mst_loan_rules`**: Aturan peminjaman (loan rules) berdasarkan kombinasi tipe anggota, tipe koleksi, dan GMD.
- **`mst_location`**: Referensi kode lokasi rak / gedung koleksi.
- **`mst_media_type`**: Referensi RDA jenis media.
- **`mst_member_type`**: Referensi tipe/kategori keanggotaan (Dosen, Mahasiswa, Umum).
- **`mst_place`**: Referensi otoritas tempat terbit.
- **`mst_publisher`**: Referensi otoritas penerbit.
- **`mst_relation_term`**: Referensi istilah relasi bibliografi/subjek.
- **`mst_servers`**: Konfigurasi server P2P, Z39.50, dan SRU.
- **`mst_supplier`**: Referensi vendor / pemasok pengadaan buku.
- **`mst_topic`**: Referensi otoritas subjek / tajuk subjek.
- **`mst_visitor_room`**: Referensi ruangan buku tamu pengunjung perpustakaan.
- **`mst_voc_ctrl`**: Referensi sistem pengendalian vokabuler subjek.

---

### 📑 Tabel Interaksi & Pencarian
- **`comment`**: Menyimpan ulasan dan komentar buku dari anggota via OPAC.
- **`content`**: Konten artikel / halaman statis perpustakaan (profil, berita).
- **`search_biblio`**: Tabel flat indeks terdenormalisasi untuk optimasi pencarian katalog.
- **`visitor_count`**: Data presensi statistik buku tamu pengunjung perpustakaan.

---

## 💡 Tips Memahami Tabel Database SLiMS
1. **Pahami Relasi Antar Tabel**: Banyak tabel SLiMS saling berelasi secara logis. Contohnya, relasi many-to-many `biblio_author` menghubungkan tabel `biblio` dengan `mst_author`.
2. **Prioritas Loan Rules**: Aturan di `mst_loan_rules` akan menimpa (*override*) pengaturan baseline yang ada di `mst_member_type`.
3. **Backup Sebelum Modifikasi**: Selalu buat backup database (`files/backup/` atau mysqldump) sebelum melakukan migrasi atau penambahan tabel kustom.


