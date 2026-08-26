# 🚀 Quick Start: Membuat Plugin SLiMS 9 Bulian dalam 5 Menit

Panduan praktis langkah demi langkah untuk membuat plugin SLiMS 9 yang aman, modular, dan siap produksi.

---

## 📁 Langkah 1: Buat Struktur Folder Plugin

Masuk ke folder `plugins/` instalasi SLiMS Anda dan buat folder plugin baru:

```bash
mkdir -p plugins/halo_dunia/{migration,assets,inc}
```

Struktur folder minimal:
```
plugins/halo_dunia/
├── halo_dunia.plugin.php    # Entry point registrasi plugin
├── admin_menu.php           # Tampilan & controller admin
├── helper.php               # Fungsi helper & database (opsional)
├── migration/               # Skrip migrasi tabel
│   └── 1_CreateHaloTable.php
└── assets/                  # CSS & JavaScript
```

---

## 🔌 Langkah 2: Registrasi Plugin (`halo_dunia.plugin.php`)

Buat file `plugins/halo_dunia/halo_dunia.plugin.php`:

```php
<?php
/**
 * Plugin Name: Halo Dunia SLiMS
 * Plugin URI: https://github.com/adeism/belajarslims
 * Description: Plugin contoh cepat untuk belajar SLiMS 9 Bulian
 * Version: 1.0.0
 * Author: Adeism
 */

use SLiMS\Plugins;

// ⚠️ JANGAN tulis define('INDEX_AUTH', 1) di file ini!

$plugins = Plugins::getInstance();

// Daftarkan menu ke modul 'system', 'reporting', 'membership', dsb.
$plugins->registerMenu('system', 'Halo Dunia', __DIR__ . '/admin_menu.php');

// Daftarkan route OPAC publik (opsional)
$plugins->registerMenu('opac', 'halo_publik', __DIR__ . '/opac_menu.php');
```

---

## 🛡️ Langkah 3: Buat Antarmuka Admin (`admin_menu.php`)

Buat file `plugins/halo_dunia/admin_menu.php`:

```php
<?php
defined('INDEX_AUTH') || die('Direct access not allowed');

// 1. Session & Privilege Check
require_once SB . 'admin/default/session.inc.php';
require_once SB . 'admin/default/session_check.inc.php';

$can_read  = utility::havePrivilege('system', 'r');
$can_write = utility::havePrivilege('system', 'w');

if (!$can_read) {
    die('<div class="errorBox">' . __('Akses ditolak!') . '</div>');
}

// 2. Simbio Lazy Load Guard (Mencegah Class Redeclaration Error)
if (!class_exists('simbio_table')) {
    require_once SIMBIO . 'simbio_GUI/table/simbio_table.inc.php';
}
if (!class_exists('simbio_datagrid')) {
    require_once SIMBIO . 'simbio_DB/datagrid/simbio_dbgrid.inc.php';
}

// 3. Handler Form POST dengan CSRF Protection
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan']) && $can_write) {
    $pesan = trim($_POST['pesan'] ?? '');
    if (!empty($pesan)) {
        $stmt = $dbs->prepare("INSERT INTO halo_dunia_data (pesan, created_at) VALUES (?, NOW())");
        $stmt->bind_param("s", $pesan);
        $stmt->execute();
        $stmt->close();
        $msg = 'Pesan berhasil disimpan!';
    }
}
?>

<div class="menuBox">
    <div class="menuBoxInner">
        <h3 style="margin-top:0;">👋 Selamat Datang di Plugin Halo Dunia</h3>
        
        <?php if (!empty($msg)): ?>
            <div class="infoBox"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <!-- Form Input -->
        <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>?mod=system&id=halo_dunia" style="margin-bottom:20px;">
            <div style="margin-bottom:10px;">
                <label><strong>Tulis Pesan:</strong></label><br />
                <input type="text" name="pesan" class="form-control" placeholder="Ketik pesan..." required style="width:300px; padding:6px;" />
            </div>
            <button type="submit" name="simpan" value="1" class="btn btn-primary">Simpan Pesan</button>
        </form>

        <!-- Datagrid Tabel Data -->
        <h4>📋 Daftar Pesan Tersimpan</h4>
        <?php
        $datagrid = new simbio_datagrid();
        $datagrid->setSQLColumn("id", "pesan AS 'Isi Pesan'", "created_at AS 'Waktu'");
        $datagrid->setSQLorder("id DESC");
        echo $datagrid->createDataGrid($dbs, 'halo_dunia_data', 20, $can_read);
        ?>
    </div>
</div>
```

---

## 🗄️ Langkah 4: Buat Database Migration (`migration/1_CreateHaloTable.php`)

Buat file `plugins/halo_dunia/migration/1_CreateHaloTable.php`:

```php
<?php
defined('INDEX_AUTH') || defined('PINJAM_RUANG_CONTEXT') || die('Direct access not allowed');

use SLiMS\Migration\Migration;

class CreateHaloTable extends Migration
{
    public function up()
    {
        global $dbs;
        $db = $dbs ?? \SLiMS\DB::getInstance();

        $sql = "CREATE TABLE IF NOT EXISTS halo_dunia_data (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            pesan VARCHAR(255) NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $db->query($sql);
    }

    public function down()
    {
        global $dbs;
        $db = $dbs ?? \SLiMS\DB::getInstance();
        $db->query("DROP TABLE IF EXISTS halo_dunia_data");
    }
}
```

---

## ✅ Langkah 5: Aktifkan & Uji Plugin
1. Buka Admin Panel SLiMS: **System > Plugin**.
2. Cari **Halo Dunia SLiMS** dan klik tombol **Aktifkan**.
3. Buka menu **System > Halo Dunia** untuk mencoba menyimpan pesan dan melihat hasilnya di tabel Datagrid!

---

## 📚 Dokumen Lanjutan
- [Instruksi Lengkap & Guardrails](penting.instructions.SIMPLIFIED.md)
- [Pola Iframe Laporan](context/04-slims-iframe-pattern.md)
- [Troubleshooting & Solusi Error](PLUGIN-ERROR-TROUBLESHOOTING-GUIDE.md)
