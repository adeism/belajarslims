# 📊 Panduan Ekspor Data (CSV, Excel, PDF/Cetak) di Plugin SLiMS 9

Panduan standar untuk mengimplementasikan fitur ekspor data laporan (CSV, Excel, PDF / Print View) pada plugin SLiMS 9 Bulian dengan aman dan benar.

---

## 🚨 3 Aturan Kritis Ekspor Data di SLiMS

1. **Wajib Menggunakan `exit;` atau `die();`**:
   Setelah mengirim header file dan mencetak stream data, eksekusi script **WAJIB dihentikan** dengan `exit;`. Jika tidak, template HTML SLiMS akan ikut tercetak di akhir file dan merusak format CSV/Excel.
2. **Wajib Bersihkan Output Buffer (`ob_clean();`)**:
   Gunakan `ob_clean();` sebelum mengirim fungsi `header()` untuk membersihkan spasi/whitespace yang tidak sengaja tercetak sebelum header download.
3. **Wajib Pengecekan Hak Akses**:
   Pastikan pengguna memiliki hak akses baca (`utility::havePrivilege('reporting', 'r')`) sebelum memproses ekspor data sensitif.

---

## 📄 1. Ekspor CSV (Format Standar & Ringan)

### Pola Stream CSV dengan `fputcsv()`

```php
<?php
defined('INDEX_AUTH') || die('Direct access not allowed');

// 1. Cek Hak Akses
$can_read = utility::havePrivilege('reporting', 'r');
if (!$can_read) {
    die('<div class="errorBox">Akses ditolak!</div>');
}

// 2. Handler Ekspor CSV
if (isset($_GET['action']) && $_GET['action'] === 'export_csv') {
    // Bersihkan buffer agar tidak ada whitespace/HTML yang mendahului
    if (ob_get_length()) {
        ob_clean();
    }

    $filename = 'laporan_koleksi_' . date('Y-m-d_His') . '.csv';

    // Header Download CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Buka output stream PHP
    $output = fopen('php://output', 'w');

    // Tulis UTF-8 BOM agar terbaca dengan benar di Microsoft Excel Indonesia
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // Tulis Header Kolom
    fputcsv($output, ['ID', 'Judul Buku', 'Pengarang', 'Tipe Koleksi', 'Tahun']);

    // Query Data dari Database
    global $dbs;
    $query = $dbs->query("SELECT b.biblio_id, b.title, b.publish_year, ct.coll_type_name 
                          FROM biblio AS b 
                          LEFT JOIN mst_coll_type AS ct ON b.coll_type_id = ct.coll_type_id 
                          LIMIT 1000");

    while ($row = $query->fetch_assoc()) {
        fputcsv($output, [
            $row['biblio_id'],
            $row['title'],
            $row['publish_year'] ?? '-',
            $row['coll_type_name'] ?? '-'
        ]);
    }

    fclose($output);
    
    // ⚠️ KRITIS: Hentikan script agar tidak memuat HTML template SLiMS
    exit;
}
```

---

## 📑 2. Ekspor Excel (.xls / Spreadsheet)

### Pola HTML Table Spreadsheet (Kompatibel Semua Versi Excel)

```php
<?php
defined('INDEX_AUTH') || die('Direct access not allowed');

if (isset($_GET['action']) && $_GET['action'] === 'export_excel') {
    if (ob_get_length()) {
        ob_clean();
    }

    $filename = 'rekap_peminjaman_' . date('Y-m-d') . '.xls';

    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>';
    echo '<body>';
    echo '<table border="1">';
    echo '<thead>';
    echo '<tr style="background-color: #2b579a; color: #ffffff; font-weight: bold;">';
    echo '<th>No</th><th>Kode Anggota</th><th>Nama</th><th>Total Pinjam</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    global $dbs;
    $q = $dbs->query("SELECT m.member_id, m.member_name, COUNT(l.loan_id) AS total_loan 
                      FROM member AS m 
                      LEFT JOIN loan AS l ON m.member_id = l.member_id 
                      GROUP BY m.member_id");
    
    $no = 1;
    while ($r = $q->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $no++ . '</td>';
        echo '<td style="mso-number-format:\'\@\';">' . htmlspecialchars($r['member_id']) . '</td>'; // Format string agar 0 di awal tidak hilang
        echo '<td>' . htmlspecialchars($r['member_name']) . '</td>';
        echo '<td>' . (int)$r['total_loan'] . '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '</body></html>';

    // ⚠️ KRITIS: Hentikan script
    exit;
}
```

---

## 🖨️ 3. Ekspor PDF / Tampilan Cetak (Print View)

### Menggunakan Template Cetak Bawaan SLiMS (`printed_page_tpl.php`)

SLiMS menyediakan template khusus cetak di `admin/admin_template/printed_page_tpl.php` dengan style tabel cetak standar perpustakaan (`s-table`, `alterCellPrinted`, font monospaced/clean).

```php
<?php
defined('INDEX_AUTH') || die('Direct access not allowed');

// Jika parameter print aktif
if (isset($_GET['action']) && $_GET['action'] === 'print') {
    global $sysconf, $dbs;

    // 1. Buffer konten laporan
    ob_start();
    ?>
    <div class="printHeader">
        <h2><?= htmlspecialchars($sysconf['library_name']) ?></h2>
        <h3>Laporan Transaksi Sirkulasi</h3>
        <p>Dicetak pada: <?= date('d-m-Y H:i') ?></p>
    </div>

    <table class="s-table" style="width: 100%;">
        <thead>
            <tr>
                <th>No</th>
                <th>Barcode</th>
                <th>Judul Buku</th>
                <th>Peminjam</th>
                <th>Tgl Pinjam</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = $dbs->query("SELECT l.*, b.title, m.member_name FROM loan l 
                              JOIN item i ON l.item_code = i.item_code 
                              JOIN biblio b ON i.biblio_id = b.biblio_id 
                              JOIN member m ON l.member_id = m.member_id LIMIT 100");
            $no = 1;
            while ($row = $q->fetch_assoc()):
                $rowClass = ($no % 2 === 0) ? 'alterCellPrinted' : 'alterCellPrinted2';
            ?>
            <tr class="<?= $rowClass ?>">
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['item_code']) ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['member_name']) ?></td>
                <td><?= htmlspecialchars($row['loan_date']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <script>
        window.print();
    </script>
    <?php
    $content = ob_get_clean();

    // 2. Sertakan template cetak SLiMS
    $page_title = 'Cetak Laporan Sirkulasi';
    require SB . 'admin/' . $sysconf['admin_template']['dir'] . '/printed_page_tpl.php';

    // ⚠️ KRITIS: Hentikan script
    exit;
}
```

---

## 📋 Checklist Validasi Ekspor pada Plugin

- [ ] Memiliki tombol aksi ekspor di antarmuka admin (contoh: `<a href="...?action=export_csv" class="btn btn-default">Ekspor CSV</a>`).
- [ ] Memastikan `ob_clean();` dijalankan sebelum `header()`.
- [ ] Memastikan `exit;` dipanggil tepat setelah stream data selesai.
- [ ] Menggunakan format teks `mso-number-format:'\@'` pada Excel untuk kolom kode/barcode agar digit angka depan `0` tidak terhapus otomatis oleh Excel.
- [ ] Validasi lolos pengujian `php bin/check-plugin.php /path/to/plugin`.
