---
applyTo: '**'
---
# 🚀 SLiMS 9 Bulian - AI Plugin Development Guide & Best Practices

> **Role**: Senior Full-Stack Developer expert dalam SLiMS plugin development
> 
> **Principle**: Generate production-ready, secure, maintainable, and native SLiMS plugins

---

## 📚 **External Documentation Index**

**Location**: `plugin/` & `plugin/context/`

| Topic | File | When to Use |
|-------|------|-------------|
| **📖 Documentation Index** | `plugin/README.md` | Start here untuk overview |
| **🚀 Quick Start** | `plugin/QUICK-START.md` | Panduan cepat 5 menit untuk developer |
| **🔍 Plugin Fundamentals** | `plugin/context/01-slims-plugin-fundamentals.md` | Arsitektur registrasi plugin & menu |
| **🗄️ Database Migration** | `plugin/context/02-slims-database-migration.md` | Skrip migrasi native SLiMS |
| **🔒 Security & CSRF** | `plugin/context/03-slims-security-best-practices.md` | Prepared statements, CSRF, & XSS |
| **📊 Iframe Pattern** | `plugin/context/04-slims-iframe-pattern.md` | Filter laporan tanpa kehilangan menu admin |
| **🛠️ CRUD & Simbio Guard** | `plugin/context/05-slims-crud-operations.md` | Simbio Datagrid aman tanpa konflik class |
| **🚨 Error Troubleshooting** | `plugin/PLUGIN-ERROR-TROUBLESHOOTING-GUIDE.md` | Panduan lengkap 50+ error & solusinya |
| **🎨 CSS & Mobile UI** | `plugin/CSS-LOADING-GUIDE.md` | Path constants (`SWB`, `SB`) & responsive UI |

---

## 🎯 **Core Essentials (Critical Knowledge)**

### **📁 Directory Structure**
```
/path/to/slims/
├── admin/modules/        # Core admin modules (DO NOT EDIT)
├── plugins/             # Plugin directory (EDITABLE)
│   └── my-plugin/       # Folder plugin Anda
│       ├── my_plugin.plugin.php  # Registration file (NO INDEX_AUTH here!)
│       ├── admin_menu.php        # Admin Controller & Router
│       ├── helper.php            # Helper & Database Functions
│       ├── migration/            # Native SLiMS Migrations
│       │   └── 1_CreateMyTable.php
│       └── assets/               # CSS, JS, Images
├── templates/           # OPAC themes
└── lib/                # Core libraries (READ-ONLY - NEVER EDIT!)
```

### **🔧 Essential Constants (MEMORIZE!)**
```php
SB    // Server Base: /path/to/slims/ (for PHP require/include)
SWB   // Server Web Base: https://domain.com/slims/ (for HTML: CSS, JS, images)
AWB   // Admin Web Base: https://domain.com/slims/admin/ (for admin URLs)
$dbs  // Global database object mysqli (ALWAYS use this)
```

**Critical Rule**: 
- Gunakan `SB` untuk PHP `require_once SB . 'admin/default/session.inc.php';`
- Gunakan `SWB` untuk HTML `<link href="<?= SWB ?>plugins/my-plugin/assets/style.css">`
- Gunakan `AWB` atau URL query untuk redirect admin

---

## 🔌 **Plugin Development Lifecycle**

### **1. Entry Point Registration (`*.plugin.php`)**
```php
<?php
/**
 * Plugin Name: My Cool Plugin
 * Plugin URI: https://github.com/username/my-plugin
 * Description: Deskripsi plugin SLiMS 9 Bulian
 * Version: 1.0.0
 * Author: Developer Name
 */
use SLiMS\Plugins;

// ⚠️ ATURAN EMAS: JANGAN menulis define('INDEX_AUTH', 1) di file entry point!
// File ini dimuat oleh autoloader SLiMS saat booting sistem.

$plugins = Plugins::getInstance();

// Kategori Menu Admin Valid:
// bibliography, circulation, membership, master_file, reporting, serial_control, stock_take, system
$plugins->registerMenu('reporting', 'My Cool Plugin', __DIR__ . '/admin_menu.php');

// Registrasi Route OPAC Publik (Opsional):
$plugins->registerMenu('opac', 'my_public_view', __DIR__ . '/opac_menu.php');
```

---

## 🔒 **Security & Guardrails (NON-NEGOTIABLE!)**

### **1. Autentikasi & Privilege Check di Admin Interface (`admin_menu.php`)**
```php
<?php
defined('INDEX_AUTH') || die('Direct access not allowed');

// Pastikan session aktif
require_once SB . 'admin/default/session.inc.php';
require_once SB . 'admin/default/session_check.inc.php';

// Cek hak akses modul (r = read, w = write)
$can_read  = utility::havePrivilege('reporting', 'r');
$can_write = utility::havePrivilege('reporting', 'w');

if (!$can_read) {
    die('<div class="errorBox">' . __('Anda tidak memiliki hak akses ke modul ini!') . '</div>');
}
```

### **2. Proteksi CSRF (Cross-Site Request Forgery)**
```php
// Helper Pembuatan & Validasi Token CSRF
function prGetCsrfToken(): string {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) { @session_start(); }
    if (empty($_SESSION['pr_csrf_token'])) {
        $_SESSION['pr_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['pr_csrf_token'];
}

function prCsrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(prGetCsrfToken(), ENT_QUOTES, 'UTF-8') . '" />';
}

function prValidateCsrf(): bool {
    $token = $_POST['csrf_token'] ?? '';
    return !empty($token) && hash_equals($_SESSION['pr_csrf_token'] ?? '', $token);
}

// Di handler POST:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!prValidateCsrf()) {
        die('<div class="errorBox">Token keamanan tidak valid (CSRF). Silakan refresh halaman.</div>');
    }
    // Lanjutkan proses form...
}
```

### **3. Database Security (Prepared Statements Wajib)**
```php
// ✅ BENAR - Prepared statements
$stmt = $dbs->prepare("SELECT * FROM biblio WHERE biblio_id = ? AND title LIKE ?");
$stmt->bind_param('is', $biblio_id, $searchParam);
$stmt->execute();
$result = $stmt->get_result();

// ❌ SALAH - Rentan SQL Injection!
$result = $dbs->query("SELECT * FROM biblio WHERE biblio_id = $biblio_id");
```

### **4. Output Escaping (XSS Prevention)**
```php
// ✅ BENAR
echo '<td>' . htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') . '</td>';

// ❌ SALAH
echo '<td>' . $row['title'] . '</td>';
```

---

## 🛠️ **Simbio GUI Idempotent Lazy Loading Guard**

### **Penyebab Error**:
`Cannot declare class simbio_table_field, because the name is already in use`  
Terjadi karena modul SLiMS core sering memanggil `require SIMBIO . 'simbio_GUI/table/simbio_table.inc.php'` (bukan `require_once`).

### **Pola Solusi**:
Gunakan guard `class_exists()` sebelum memuat komponen Simbio:
```php
if (!class_exists('simbio_table')) {
    require_once SIMBIO . 'simbio_GUI/table/simbio_table.inc.php';
}
if (!class_exists('simbio_datagrid')) {
    require_once SIMBIO . 'simbio_DB/datagrid/simbio_dbgrid.inc.php';
}
```

---

## 🗄️ **Native SLiMS Migration System**

SLiMS memiliki sistem migrasi native berbasis class turunan `SLiMS\Migration\Migration`.

### **Format Penamaan File & Class:**
File: `migration/1_CreateMyPluginTable.php`
```php
<?php
defined('INDEX_AUTH') || die('Direct access not allowed');

use SLiMS\Migration\Migration;

class CreateMyPluginTable extends Migration
{
    public function up()
    {
        global $dbs;
        $db = $dbs ?? \SLiMS\DB::getInstance();
        
        $sql = "CREATE TABLE IF NOT EXISTS my_plugin_table (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(150) NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->query($sql);
    }

    public function down()
    {
        global $dbs;
        $db = $dbs ?? \SLiMS\DB::getInstance();
        $db->query("DROP TABLE IF EXISTS my_plugin_table");
    }
}
```

---

## 🌐 **Pola OPAC Standalone & Member Session**

### **1. Standalone Fullscreen View (Bypass Tema OPAC)**
Jika membuat display monitor TV, kiosk, atau antrean mandiri yang tidak ingin terbungkus navbar/footer tema OPAC:
```php
<?php
// opac_menu.php
defined('INDEX_AUTH') || die('Direct access not allowed');

// Sesi member
$isLoggedIn = !empty($_SESSION['mid']);
$memberId   = $_SESSION['mid'] ?? null;
$memberName = $_SESSION['m_name'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Layar TV / Kiosk</title>
    <!-- Custom CSS -->
</head>
<body>
    <!-- Konten Mandiri -->
</body>
</html>
<?php
// Matikan eksekusi script agar navbar & footer tema SLiMS tidak membalut halaman
exit;
```

---

## 🚨 **Ringkasan Larangan Kritis (CRITICAL DON'Ts)**

1. ❌ **JANGAN** taruh `define('INDEX_AUTH', 1)` di file `*.plugin.php`.
2. ❌ **JANGAN** gunakan `'admin'` sebagai kategori menu di `registerMenu()`.
3. ❌ **JANGAN** gunakan `DB_ACCESS` (konstanta usang SLiMS lama).
4. ❌ **JANGAN** memodifikasi folder core `/lib/` atau `/admin/modules/`.
5. ❌ **JANGAN** gunakan query mentah tanpa *Prepared Statements*.
6. ❌ **JANGAN** output data tanpa `htmlspecialchars()`.
7. ❌ **JANGAN** gunakan path relatif untuk CSS/JS (gunakan `SWB`).
8. ❌ **JANGAN** memuat `simbio_table.inc.php` tanpa guard `class_exists()`.

---

**Version**: 2.1.0  
**SLiMS Target**: SLiMS 9 Bulian (9.3.0+)  
**Status**: Production Ready ✅
