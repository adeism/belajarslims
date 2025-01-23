# 📚 Belajar SLiMS: Tentang Tabel Database

**SLiMS (Senayan Library Management System)** adalah sistem manajemen perpustakaan berbasis web yang fleksibel dan kaya fitur. Repositori ini menjelaskan tabel-tabel utama dalam database SLiMS, termasuk fungsinya, sehingga mempermudah pemahaman relasi antar tabel dan penggunaannya.

---

## 🏗️ Daftar Tabel Database SLiMS

Berikut adalah daftar tabel utama yang terdapat dalam database SLiMS, dikelompokkan berdasarkan fungsinya:

### 📋 Tabel Sistem dan Konfigurasi
- **`backup_log`**: Mencatat riwayat backup database.
- **`group_access`**: Mendefinisikan hak akses grup user terhadap modul.
- **`mst_module`**: Tabel definisi modul aplikasi.
- **`setting`**: Tabel untuk konfigurasi sistem.
- **`system_log`**: Mencatat log sistem secara global.

---

### 📚 Tabel Koleksi Perpustakaan
- **`biblio`**: Tabel utama untuk data bibliografi koleksi perpustakaan.
- **`biblio_attachment`**: Menyimpan lampiran file pada data bibliografi.
- **`biblio_attachment_access`**: Mencatat akses lampiran file oleh anggota.
- **`biblio_author`**: Hubungan antara data bibliografi dengan data pengarang.
- **`biblio_custom`**: Data tambahan bibliografi yang spesifik.
- **`biblio_log`**: Mencatat riwayat modifikasi data bibliografi.
- **`biblio_relation`**: Hubungan/relasi antar data bibliografi.
- **`biblio_topic`**: Hubungan data bibliografi dengan subjek/topik.
- **`files`**: Menyimpan data file/berkas digital.
- **`files_read`**: Statistik akses file.
- **`files_read_guest`**: Identitas guest/tamu yang membaca file.
- **`index_documents`**: Indeks pencarian lengkap/fulltext.
- **`index_words`**: Indeks kata untuk pencarian lengkap/fulltext.
- **`item`**: Data item/eksemplar koleksi perpustakaan.
- **`read_counter`**: Statistik pembacaan koleksi.
- **`reservations`**: Mencatat reservasi koleksi oleh anggota.
- **`reserve`**: Mencatat pemesanan koleksi oleh anggota.
- **`serial`**: Data majalah/jurnal yang berlangganan.
- **`stock_take`**: Mencatat stok opname koleksi.
- **`stock_take_item`**: Detail item pada stok opname.

---

### 👥 Tabel Anggota dan Pengguna
- **`member`**: Tabel utama untuk data anggota perpustakaan.
- **`member_custom`**: Data tambahan anggota perpustakaan.
- **`user`**: Tabel utama untuk data pengguna/user aplikasi.
- **`user_group`**: Referensi grup pengguna.

---

### 🔄 Tabel Transaksi dan Log Aktivitas
- **`loan`**: Mencatat transaksi peminjaman.
- **`loan_history`**: Riwayat peminjaman.
- **`fines`**: Mencatat data denda anggota perpustakaan.
- **`bebas_pustaka`**: Mencatat pengajuan bebas pustaka mahasiswa.
- **`bebas_pustaka_logs`**: Log perubahan status pengajuan bebas pustaka.

---

### 🔧 Tabel Referensi dan Metadata
- **`mst_author`**: Referensi data pengarang.
- **`mst_carrier_type`**: Referensi jenis pembawa konten.
- **`mst_coll_type`**: Referensi jenis koleksi.
- **`mst_content_type`**: Referensi jenis konten.
- **`mst_custom_field`**: Mendefinisikan custom field pada tabel tertentu.
- **`mst_frequency`**: Referensi frekuensi terbitan berseri.
- **`mst_gmd`**: Referensi jenis bahan pustaka.
- **`mst_item_status`**: Referensi status item/eksemplar.
- **`mst_label`**: Referensi label/tanda untuk klasifikasi koleksi.
- **`mst_language`**: Referensi bahasa.
- **`mst_loan_rules`**: Aturan peminjaman berdasarkan kombinasi jenis anggota dan koleksi.
- **`mst_location`**: Referensi lokasi item/eksemplar.
- **`mst_media_type`**: Referensi jenis media konten.
- **`mst_member_type`**: Referensi jenis keanggotaan.
- **`mst_place`**: Referensi tempat terbit.
- **`mst_publisher`**: Referensi penerbit.
- **`mst_relation_term`**: Referensi istilah hubungan/relasi subjek.
- **`mst_servers`**: Konfigurasi server P2P, Z39.50, dll.
- **`mst_supplier`**: Referensi supplier/vendor.
- **`mst_topic`**: Referensi subjek/topik.
- **`mst_voc_ctrl`**: Referensi pengendalian vokabuler subjek.

---

### 📑 Tabel Lainnya
- **`comment`**: Menyimpan komentar pada data bibliografi oleh anggota.
- **`content`**: Konten/artikel statis.
- **`group_access`**: Hak akses grup terhadap modul.
- **`search_biblio`**: Indeks untuk optimasi pencarian data bibliografi.
- **`visitor_count`**: Statistik pengunjung perpustakaan.

---

## 💡 Tips Memahami Tabel Database
1. **Pahami Relasi Antar Tabel**: Banyak tabel SLiMS saling berhubungan. Contohnya, `biblio_author` menghubungkan tabel `biblio` dengan `mst_author`.
2. **Gunakan Query SQL**: Untuk eksplorasi lebih lanjut, gunakan query SQL seperti `DESCRIBE` untuk memeriksa struktur tabel.
3. **Gunakan Backup Data**: Sebelum melakukan perubahan besar pada database, selalu lakukan backup data untuk mencegah kehilangan informasi penting.

---

## 📚 Dokumentasi Tambahan

Untuk informasi lebih lanjut, silakan kunjungi:
- **[Dokumentasi Resmi SLiMS](https://slims.web.id)**
- **[Repositori SLiMS di GitHub](https://github.com/slims)**

---

🚀 **Semoga panduan ini membantu Anda memahami tabel database SLiMS dengan lebih baik!** 🎉

>*tutorial ini dibuat dengan CustomGPT SLiMS Plugin Maker (ChatGPT) dengan fitur pengetahuan yang ada dalam folder rag (belum di review developer)

