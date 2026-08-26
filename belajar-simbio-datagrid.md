# 📊 Panduan Lengkap Simbio Datagrid di SLiMS 9 Bulian

`simbio_datagrid` adalah komponen bawaan SLiMS yang berfungsi mengubah query database menjadi tabel data interaktif di area admin dengan fitur bawaan:
- ✅ **Paginasi Otomatis (Pagination)**
- ✅ **Pencarian Cepat (Live Search Filter)**
- ✅ **Pengurutan Kolom (Sorting ASC/DESC)**
- ✅ **Kustomisasi Kolom & Tombol Aksi (Callback Function)**
- ✅ **Format Visual Selaras dengan Tema Admin SLiMS**

---

## 🛠️ 1. Pemuatan Aman (Idempotent Lazy Loading Guard)

Sebelum membuat objek `simbio_datagrid`, **wajib** lakukan pengecekan `class_exists()` untuk mencegah fatal error saat class sudah dimuat oleh SLiMS core:

```php
if (!class_exists('simbio_table')) {
    require_once SIMBIO . 'simbio_GUI/table/simbio_table.inc.php';
}
if (!class_exists('simbio_datagrid')) {
    require_once SIMBIO . 'simbio_DB/datagrid/simbio_dbgrid.inc.php';
}
```

---

## ⚙️ 2. Metode Utama `simbio_datagrid`

| Method | Fungsi | Contoh Penggunaan |
|---|---|---|
| `setSQLColumn(...)` | Menentukan kolom yang dipilih dan alias judul header tabel | `$grid->setSQLColumn("id", "title AS 'Judul'", "author AS 'Pengarang'");` |
| `setSQLorder(...)` | Mengatur default pengurutan data | `$grid->setSQLorder("id DESC");` |
| `setSQLCriteria(...)` | Menambahkan filter kondisi query `WHERE` | `$grid->setSQLCriteria("is_active = 1");` |
| `invisibleFields` | Menyembunyikan kolom tertentu dari tampilan namun datanya tetap bisa diakses di callback | `$grid->invisibleFields = '0,4';` |
| `modifyColumnContent($colIndex, $callback)` | Memodifikasi output sel tabel menggunakan fungsi PHP callback | `$grid->modifyColumnContent(3, 'callback{formatStatus}');` |
| `createDataGrid($dbs, $table, $perPage, $canRead)` | Merender dan menghasilkan output markup HTML tabel lengkap | `echo $grid->createDataGrid($dbs, 'biblio', 20, $can_read);` |

---

## 📝 3. Contoh Implementasi Lengkap pada Plugin

Berikut adalah contoh skrip antarmuka admin yang menggunakan `simbio_datagrid`:

```php
<?php
defined('INDEX_AUTH') || die('Direct access not allowed');

// 1. Session & Hak Akses
require_once SB . 'admin/default/session.inc.php';
require_once SB . 'admin/default/session_check.inc.php';

$can_read  = utility::havePrivilege('reporting', 'r');
$can_write = utility::havePrivilege('reporting', 'w');

if (!$can_read) {
    die('<div class="errorBox">' . __('Akses ditolak!') . '</div>');
}

// 2. Lazy Loading Guard
if (!class_exists('simbio_table')) {
    require_once SIMBIO . 'simbio_GUI/table/simbio_table.inc.php';
}
if (!class_exists('simbio_datagrid')) {
    require_once SIMBIO . 'simbio_DB/datagrid/simbio_dbgrid.inc.php';
}

// 3. Callback Function untuk Format Kolom Status & Tombol Aksi
function formatStatusBadge($obj_db, $data, $field_num) {
    $status = $data[$field_num];
    if ($status === 'aktif' || $status == '1') {
        return '<span class="badge badge-success" style="background:#28a745; color:#fff; padding:4px 8px; border-radius:4px;">Aktif</span>';
    }
    return '<span class="badge badge-secondary" style="background:#6c757d; color:#fff; padding:4px 8px; border-radius:4px;">Nonaktif</span>';
}

function renderActionButtons($obj_db, $data, $field_num) {
    $id = (int)$data[$field_num];
    $editUrl   = prAdminRedirect('my_plugin', ['action' => 'edit', 'id' => $id]);
    $deleteUrl = prAdminRedirect('my_plugin', ['action' => 'delete', 'id' => $id]);
    
    return '<a href="' . $editUrl . '" class="btn btn-sm btn-primary">Sunting</a> ' .
           '<a href="' . $deleteUrl . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Yakin ingin menghapus data ini?\')">Hapus</a>';
}

// 4. Inisialisasi dan Konfigurasi Datagrid
$grid = new simbio_datagrid();

// Kolom: index 0 (id), 1 (nama), 2 (kategori), 3 (status), 4 (aksi)
$grid->setSQLColumn(
    "id",
    "item_name AS 'Nama Item'",
    "category AS 'Kategori'",
    "status AS 'Status'",
    "id AS 'Aksi'"
);

// Default sorting
$grid->setSQLorder("id DESC");

// Pasang callback modifikasi visual
$grid->modifyColumnContent(3, 'callback{formatStatusBadge}');
$grid->modifyColumnContent(4, 'callback{renderActionButtons}');

// Sembunyikan kolom ID murni (kolom ke-0) jika tidak ingin ditampilkan
$grid->invisibleFields = '0';

// 5. Cetak Tabel Datagrid
echo '<div class="menuBox">';
echo '<div class="menuBoxInner">';
echo '<h3 style="margin-top:0;">📋 Daftar Data Master</h3>';
echo $grid->createDataGrid($dbs, 'my_custom_table', 20, $can_read);
echo '</div>';
echo '</div>';
```

---

## 🎯 4. Keuntungan Menggunakan Simbio Datagrid Dibanding HTML Manual
1. **Tidak Perlu Menulis Query Pagination Manual**: LIMIT dan OFFSET ditangani otomatis oleh Simbio.
2. **Built-in Search Form**: Kotak pencarian otomatis muncul di atas tabel dan melakukan filter SQL otomatis.
3. **Standar Antarmuka SLiMS**: Tampilan selaras dengan tabel-tabel bawaan modul Bibliografi, Sirkulasi, dan Keanggotaan SLiMS.
