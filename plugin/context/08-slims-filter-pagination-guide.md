# 🔍 Panduan Filter, Advanced Filter, Quick Button & Paginasi di Plugin SLiMS 9

Panduan lengkap implementasi pencarian, form filter canggih, tombol filter cepat (quick buttons / tabs), dan paginasi data pada antarmuka admin maupun OPAC SLiMS 9 Bulian.

---

## 🚨 4 Aturan Kritis Filter & Paginasi di SLiMS

1. **Wajib Input Hidden `mod` dan `id` pada Form GET Admin**:
   Form HTML dengan `method="GET"` secara default akan **menghapus semua query string URL** saat disubmit. Jika tidak menyertakan input hidden `name="mod"` dan `name="id"`, halaman admin akan dialihkan ke `admin/index.php` dan memicu error *"Plugin not found / disabled!"*.
2. **Sanitasi Parameter Paginasi**:
   Parameter halaman `$_GET['page']` wajib di-cast integer `(int)($_GET['page'] ?? 1)` dan dibatasi minimal bernilai `1` untuk mencegah kalkulasi offset negatif pada SQL `LIMIT`.
3. **Pertahankan State URL saat Klik Tombol Quick Filter**:
   Gunakan `http_build_query(array_merge($_GET, ['status' => '...']))` agar parameter modul, pencarian kata kunci, dan filter lain tidak hilang saat berpindah tab status.
4. **Proteksi Lazy Loading `simbio_paging`**:
   Bungkus pemanggilan `simbio_paging.inc.php` dengan `if (!class_exists('simbio_paging'))`.

---

## 🔘 1. Quick Button Filter (Status Tabs / Pill Buttons)

Pola tombol filter cepat untuk memfilter data berdasarkan status transaksi (contoh: *Semua*, *Menunggu*, *Disetujui*, *Selesai*, *Ditolak*):

```php
<?php
defined('INDEX_AUTH') || die('Direct access not allowed');

// Ambil filter aktif saat ini
$current_status = $_GET['status'] ?? 'all';

// Definisi daftar filter status
$status_tabs = [
    'all'       => ['label' => 'Semua', 'class' => 'btn-default'],
    'pending'   => ['label' => 'Menunggu', 'class' => 'btn-warning'],
    'approved'  => ['label' => 'Disetujui', 'class' => 'btn-success'],
    'rejected'  => ['label' => 'Ditolak', 'class' => 'btn-danger'],
];
?>

<div class="btn-group mb-3" role="group" style="margin-bottom: 15px;">
    <?php foreach ($status_tabs as $key => $tab): 
        // Pertahankan seluruh parameter GET yang ada (mod, id, search, dll)
        $url_params = array_merge($_GET, ['status' => $key, 'page' => 1]);
        $btn_url = $_SERVER['PHP_SELF'] . '?' . http_build_query($url_params);
        $active_class = ($current_status === $key) ? 'active font-weight-bold' : '';
    ?>
        <a href="<?= htmlspecialchars($btn_url) ?>" 
           class="btn <?= $tab['class'] ?> <?= $active_class ?>">
            <?= htmlspecialchars($tab['label']) ?>
        </a>
    <?php endforeach; ?>
</div>
```

---

## 🔎 2. Advanced Filter Form (Pencarian Tanggal, Dropdown, Kata Kunci)

Pola form filter multi-kriteria yang aman dan tidak memutus wrapper admin SLiMS:

```php
<?php
defined('INDEX_AUTH') || die('Direct access not allowed');

// Ambil parameter filter
$keyword    = trim($_GET['keywords'] ?? '');
$coll_type  = trim($_GET['coll_type'] ?? '');
$start_date = trim($_GET['start_date'] ?? '');
$end_date   = trim($_GET['end_date'] ?? '');
?>

<!-- FORM FILTER (METHOD GET) -->
<form method="GET" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="form-inline mb-4" style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
    
    <!-- ⚠️ KRITIS: Pertahankan mod dan id agar plugin tidak disabled saat submit -->
    <input type="hidden" name="mod" value="<?= htmlspecialchars($_GET['mod'] ?? '') ?>" />
    <input type="hidden" name="id" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>" />
    <?php if (isset($_GET['status'])): ?>
        <input type="hidden" name="status" value="<?= htmlspecialchars($_GET['status']) ?>" />
    <?php endif; ?>

    <div class="form-group mr-2">
        <label class="mr-1">Kata Kunci:</label>
        <input type="text" name="keywords" value="<?= htmlspecialchars($keyword) ?>" 
               placeholder="Judul / Pengarang / Barcode" class="form-control form-control-sm" />
    </div>

    <div class="form-group mr-2">
        <label class="mr-1">Tipe Koleksi:</label>
        <select name="coll_type" class="form-control form-control-sm">
            <option value="">-- Semua Tipe --</option>
            <?php
            global $dbs;
            $ct_query = $dbs->query("SELECT coll_type_id, coll_type_name FROM mst_coll_type ORDER BY coll_type_name ASC");
            while ($ct = $ct_query->fetch_assoc()) {
                $sel = ($coll_type === (string)$ct['coll_type_id']) ? 'selected' : '';
                echo '<option value="' . (int)$ct['coll_type_id'] . '" ' . $sel . '>' . htmlspecialchars($ct['coll_type_name']) . '</option>';
            }
            ?>
        </select>
    </div>

    <div class="form-group mr-2">
        <label class="mr-1">Dari Tgl:</label>
        <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" class="form-control form-control-sm" />
    </div>

    <div class="form-group mr-2">
        <label class="mr-1">Sampai:</label>
        <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" class="form-control form-control-sm" />
    </div>

    <button type="submit" class="btn btn-primary btn-sm">
        <i class="fa fa-search"></i> Filter Data
    </button>
    <a href="<?= htmlspecialchars($_SERVER['PHP_SELF'] . '?mod=' . urlencode($_GET['mod'] ?? '') . '&id=' . urlencode($_GET['id'] ?? '')) ?>" 
       class="btn btn-secondary btn-sm ml-1">
        Reset
    </a>
</form>
```

---

## ⚡ 3. Konstruksi Query SQL Aman untuk Filter Dinamis

Pola menyusun klausa `WHERE` secara dinamis dengan prepared statements atau escaping:

```php
<?php
global $dbs;

$conditions = [];
$params = [];
$types = '';

// Filter Status
if (!empty($current_status) && $current_status !== 'all') {
    $conditions[] = "l.status = ?";
    $params[] = $current_status;
    $types .= 's';
}

// Filter Kata Kunci
if (!empty($keyword)) {
    $conditions[] = "(b.title LIKE ? OR m.member_name LIKE ? OR l.item_code = ?)";
    $likeKw = '%' . $keyword . '%';
    $params[] = $likeKw;
    $params[] = $likeKw;
    $params[] = $keyword;
    $types .= 'sss';
}

// Filter Tanggal
if (!empty($start_date) && !empty($end_date)) {
    $conditions[] = "l.loan_date BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;
    $types .= 'ss';
}

$whereSQL = count($conditions) > 0 ? "WHERE " . implode(" AND ", $conditions) : "";
```

---

## 📄 4. Paginasi Kustom Menggunakan `simbio_paging`

Jika tidak menggunakan Simbio Datagrid, gunakan class `simbio_paging` bawaan SLiMS:

```php
<?php
defined('INDEX_AUTH') || die('Direct access not allowed');

// 1. Guard Lazy Load Simbio Paging
if (!class_exists('simbio_paging')) {
    require SIMBIO . 'simbio_GUI/paging/simbio_paging.inc.php';
}

// 2. Tentukan Limit & Halaman
$limit = 20;
$page  = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

// 3. Hitung Total Data (COUNT)
$countSQL = "SELECT COUNT(*) AS total FROM loan AS l 
             JOIN biblio AS b ON l.biblio_id = b.biblio_id 
             JOIN member AS m ON l.member_id = m.member_id {$whereSQL}";

$stmtCount = $dbs->prepare($countSQL);
if (!empty($params)) {
    $stmtCount->bind_param($types, ...$params);
}
$stmtCount->execute();
$total_rows = (int)$stmtCount->get_result()->fetch_assoc()['total'];
$stmtCount->close();

// 4. Query Data Halaman Ini
$dataSQL = "SELECT l.*, b.title, m.member_name FROM loan AS l 
            JOIN biblio AS b ON l.biblio_id = b.biblio_id 
            JOIN member AS m ON l.member_id = m.member_id 
            {$whereSQL} 
            ORDER BY l.loan_date DESC 
            LIMIT {$offset}, {$limit}";

$stmtData = $dbs->prepare($dataSQL);
if (!empty($params)) {
    $stmtData->bind_param($types, ...$params);
}
$stmtData->execute();
$result = $stmtData->get_result();

// 5. Render Paginasi Simbio
$paging = new simbio_paging();
$paging->setLimit($limit);
$paging->setOffset($offset);
$paging->setTotalResult($total_rows);

// URL dasar untuk navigasi link paginasi (mempertahankan filter aktif)
$paging_params = $_GET;
unset($paging_params['page']); // Hapus page agar digenerate dinamis oleh paging
$baseUrl = $_SERVER['PHP_SELF'] . '?' . http_build_query($paging_params);

$paging->setLink($baseUrl);
$paging_html = $paging->createPaging();
?>

<!-- RENDER TABEL & HASIL PAGINASI -->
<table class="s-table" style="width: 100%;">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Eksemplar</th>
            <th>Judul</th>
            <th>Peminjam</th>
            <th>Tgl Pinjam</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = $offset + 1;
        while ($row = $result->fetch_assoc()): 
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['item_code']) ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['member_name']) ?></td>
            <td><?= htmlspecialchars($row['loan_date']) ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- NAVIGASI HALAMAN -->
<div class="paging-container" style="margin-top: 15px;">
    <?= $paging_html ?>
</div>
```

---

## 📋 Checklist Validasi Filter & Paginasi

- [ ] Form filter (GET) memiliki `<input type="hidden" name="mod" ... />` dan `<input type="hidden" name="id" ... />`.
- [ ] Tombol Quick Filter (tabs) mempertahankan parameter GET dengan `http_build_query()`.
- [ ] Variabel pencarian `LIKE` menggunakan prepared statement / `$dbs->escape_string()`.
- [ ] Paginasi kustom meng-cast `(int)$_GET['page']` dengan proteksi `max(1, ...)`.
- [ ] Class `simbio_paging` dimuat dengan guard `if (!class_exists('simbio_paging'))`.
- [ ] Lolos verifikasi `php bin/check-plugin.php /path/to/plugin`.
