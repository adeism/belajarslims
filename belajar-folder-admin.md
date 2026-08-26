# 🔧 Struktur Folder `/admin` SLiMS 9 Bulian

Folder `/admin` adalah inti dari area backend/administrasi SLiMS. Di sini, staf perpustakaan (pustakawan) atau administrator mengelola berbagai modul operasional perpustakaan.

---

## 📁 Subfolder dan File Penting dalam `/admin`

### 1. `/admin/modules/` 🧩
Menyimpan seluruh modul inti SLiMS. Setiap folder merepresentasikan modul spesifik:
- **`bibliography/`** 📚: Modul pengelolaan katalog, bibliografi, eksemplar, dan item batch.
- **`circulation/`** 🔄: Modul transaksi peminjaman, pengembalian, perpanjangan, dan denda.
- **`membership/`** 👥: Modul pengelolaan data anggota, tipe keanggotaan, dan cetak kartu.
- **`master_file/`** 🗃️: Modul pengelolaan data otoritas (Pengarang, Penerbit, Tempat Terbit, Subjek/Topik, GMD, Bahasa, Lokasi Rak, Tipe Koleksi).
- **`reporting/`** 📊: Modul laporan statistik koleksi, sirkulasi, pengunjung, dan rekapitulasi.
- **`system/`** ⚙️: Modul konfigurasi sistem global, manajemen user/grup, backup, dan manajemen plugin.
- **`stock_take/`** 📦: Modul inventarisasi dan stock opname koleksi.
- **`serial_control/`** 📰: Modul pengelolaan terbitan berkala (majalah/jurnal).

---

### 2. `/admin/default/` 🔐
Menyimpan file inisialisasi sesi dan keamanan backend:
- `session.inc.php`: Inisialisasi sesi login pustakawan.
- `session_check.inc.php`: Pengecekan otentikasi sesi aktif.

---

### 3. `/admin/admin_template/` 🎨
Berisi template tampilan area admin:
- `default/index_template.inc.php`: Kerangka utama layout dashboard admin.
- `printed_page_tpl.php`: Template standar untuk cetak laporan / printed view (terletak langsung di bawah `admin/admin_template/`).
---

### 4. File Entry Point & Utility Backend
- **`index.php`** 🏁: Entry point utama area admin SLiMS.
- **`logout.php`** 🚪: Handler logout sesi pengguna backend.
- **`AJAX_lookup_handler.php`** 🔍: Handler permintaan AJAX autocomplete/lookup (pengarang, penerbit, subjek, member).
- **`view.php`** 👁️: Viewer berkas/gambar dari database atau repository.

## 📌 Fungsi Utama Area `/admin`
1. **Pengelolaan Koleksi & Otoritas**: Manajemen bibliografi, eksemplar, dan master file.
2. **Pengelolaan Anggota**: Pendaftaran anggota, import data member, dan cetak kartu.
3. **Transaksi Sirkulasi**: Layanan peminjaman, pengembalian kilat (*quick return*), dan reservasi.
4. **Pelaporan & Audit**: Rekapitulasi statistik dan log sistem.
5. **Konfigurasi Sistem**: Pengaturan plugin, tema, hak akses user group, dan backup database.
