# 🎨 CSS & Asset Loading Guide for SLiMS Plugins

## 📝 Overview

Panduan lengkap untuk loading CSS dan asset lainnya (JS, images) di plugin SLiMS 9.6.1 Bulian. Berdasarkan pengalaman nyata troubleshooting kesalahan path loading.

---

## ⚠️ **Common Mistake: Path Loading Error**

### **❌ WRONG - Kesalahan yang Sering Terjadi**

```php
// File: admin_interface.php
<?php
define('INDEX_AUTH', '1');
require_once SB . 'admin/default/session.inc.php';
require_once SB . 'admin/default/session_check.inc.php';

// ❌ WRONG - Relative path (404 Error!)
echo '<link rel="stylesheet" href="assets/custom.css">';
echo '<link rel="stylesheet" href="./assets/custom.css">';
echo '<link rel="stylesheet" href="../plugins/my-plugin/assets/custom.css">';

// ❌ WRONG - Using server path (won't work in browser!)
echo '<link rel="stylesheet" href="' . SB . 'plugins/my-plugin/assets/custom.css">';
// Result: file:///var/www/html/slims/... (browser can't access server path!)

// ❌ WRONG - Hardcoded domain (breaks on different installations)
echo '<link rel="stylesheet" href="https://mydomain.com/slims/plugins/my-plugin/assets/custom.css">';
```

**Problem**: Browser akan mencari CSS di path yang salah:
- `https://domain.com/admin/assets/custom.css` (404!)
- `https://domain.com/assets/custom.css` (404!)
- `file:///var/www/html/slims/...` (browser can't access!)

---

## ✅ **CORRECT - Cara yang Benar**

### **Solution 1: Direct Loading in Interface File (Recommended)**

```php
// File: admin_interface.php
<?php
define('INDEX_AUTH', '1');
require_once SB . 'admin/default/session.inc.php';
require_once SB . 'admin/default/session_check.inc.php';

// ✅ CORRECT - Using SWB constant
echo '<link rel="stylesheet" href="' . SWB . 'plugins/my-plugin/assets/custom.css">';
echo '<script src="' . SWB . 'plugins/my-plugin/assets/script.js"></script>';

// Result: https://domain.com/slims/s951dev/plugins/my-plugin/assets/custom.css ✅
```

**Why This Works:**
- `SWB` = Server Web Base = Full web path to SLiMS installation
- Generates complete URL that browser can access
- Works on any installation (different domains/paths)

---

## 📚 **SLiMS Path Constants Reference**

### **Available Constants**

| Constant | Value Example | Description | Use For |
|----------|---------------|-------------|---------|
| **SB** | `/var/www/html/slims/s951dev/` | Server Base Path | File system operations (require, file_exists) |
| **SWB** | `https://domain.com/slims/s951dev/` | Server Web Base | Web assets (CSS, JS, images, links) |
| **AWB** | `https://domain.com/slims/s951dev/admin/` | Admin Web Base | Admin area links |
| **SIMBIO** | `/var/www/html/slims/s951dev/simbio2/` | Simbio Library Path | Requiring simbio classes |
| **LIB** | `/var/www/html/slims/s951dev/lib/` | Library Path | Requiring library files |

### **When to Use Each**

```php
// ✅ For requiring PHP files (server-side)
require SB . 'admin/default/session.inc.php';
require SIMBIO . 'simbio_DB/simbio_dbop.inc.php';
require LIB . 'utility.inc.php';

// ✅ For web assets (client-side)
echo '<link href="' . SWB . 'plugins/my-plugin/assets/style.css">';
echo '<script src="' . SWB . 'plugins/my-plugin/assets/script.js"></script>';
echo '<img src="' . SWB . 'plugins/my-plugin/assets/logo.png">';

// ✅ For links
echo '<a href="' . AWB . 'modules/reporting/">Reports</a>';
```

---

## 🔧 **CSS Loading Methods**

### **Method 1: Direct in Interface File (Best for Plugin-Specific Styles)**

**Pros**: 
- ✅ Only loads when plugin accessed
- ✅ Better performance
- ✅ Easy to debug

**File Structure**:
```
/plugins/my-plugin/
├── my_plugin.plugin.php
├── admin_interface.php
└── assets/
    └── css/
        └── custom.css
```

**Implementation**:
```php
// File: admin_interface.php
<?php
define('INDEX_AUTH', '1');
require_once SB . 'admin/default/session.inc.php';
require_once SB . 'admin/default/session_check.inc.php';

// Check privileges
$can_read = utility::havePrivilege('reporting', 'r');
if (!$can_read) {
    die('<div class="errorBox">No privileges</div>');
}

// Load custom CSS
?>
<link rel="stylesheet" href="<?php echo SWB; ?>plugins/my-plugin/assets/css/custom.css">

<div class="menuBox">
    <div class="menuBoxInner reportIcon">
        <h2>My Plugin with Custom Styles</h2>
        <!-- Your content -->
    </div>
</div>
```

---

### **Method 2: Using Hook (For Global Loading)**

**Pros**: 
- ✅ Loads automatically on all admin pages
- ✅ Good for plugins that modify existing pages

**Cons**: 
- ⚠️ May load unnecessarily
- ⚠️ Can affect performance

**Implementation**:
```php
// File: my_plugin.plugin.php
<?php
/**
 * Plugin Name: My Plugin
 * Plugin URI: -
 * Description: Plugin with custom CSS
 * Version: 1.0.0
 */
use SLiMS\Plugins;
$plugins = Plugins::getInstance();

// Load CSS on all admin pages
$plugins->register(Plugins::ADMIN_HEADER, function() {
    echo '<link rel="stylesheet" href="' . SWB . 'plugins/my-plugin/assets/css/global.css">';
    echo '<script src="' . SWB . 'plugins/my-plugin/assets/js/global.js"></script>';
});

// Register menu
$plugins->registerMenu('reporting', 'My Plugin', __DIR__ . '/admin_interface.php');
```

**Available Hooks for Asset Loading**:
```php
// Admin area hooks
Plugins::ADMIN_HEADER        // <head> section in admin
Plugins::ADMIN_FOOTER        // Before </body> in admin

// OPAC hooks
Plugins::OPAC_HEADER         // <head> section in OPAC
Plugins::OPAC_FOOTER         // Before </body> in OPAC
```

---

### **Method 3: Conditional Loading**

**Best for**: Heavy libraries that are only needed sometimes

```php
// File: admin_interface.php
<?php
define('INDEX_AUTH', '1');
require_once SB . 'admin/default/session.inc.php';
require_once SB . 'admin/default/session_check.inc.php';

// Load chart library only when showing chart
if (isset($_GET['view']) && $_GET['view'] == 'chart') {
    echo '<script src="' . SWB . 'plugins/my-plugin/assets/js/chart.min.js"></script>';
    echo '<link rel="stylesheet" href="' . SWB . 'plugins/my-plugin/assets/css/chart.css">';
}

// Always load main CSS
echo '<link rel="stylesheet" href="' . SWB . 'plugins/my-plugin/assets/css/main.css">';
?>
```

---

## 📁 **Recommended File Structure**

```
/plugins/my-plugin/
├── my_plugin.plugin.php           # Plugin registration
├── admin_interface.php            # Main interface
├── README.md                      # Documentation
└── assets/                        # All assets here
    ├── css/
    │   ├── main.css              # Main styles
    │   ├── form.css              # Form styles
    │   └── report.css            # Report styles
    ├── js/
    │   ├── main.js               # Main scripts
    │   └── validation.js         # Form validation
    └── images/
        ├── icons/
        │   ├── info.png
        │   └── warning.png
        └── logo.png
```

---

## 🎨 **Best Practices**

### **1. Prefer Admin Template Classes First**

```php
// ✅ BEST - Use existing SLiMS classes
<table class="s-table table table-sm table-bordered">
    <tr class="alterCellPrinted">...</tr>
    <tr class="alterCellPrinted2">...</tr>
</table>

// ⚠️ ONLY if really needed
<style>
.my-unique-element {
    /* Custom styles that don't exist in admin template */
}
</style>
```

**Common SLiMS CSS Classes**:
- `s-table` - Standard table
- `alterCellPrinted` / `alterCellPrinted2` - Alternating row colors
- `s-btn btn btn-default` - Button
- `printReport` - Print button
- `errorBox` - Error message box
- `infoBox` - Info message box
- `menuBox` - Menu container
- `form-control` - Form input (Bootstrap)

---

### **2. Minimize Custom CSS**

```php
// ❌ BAD - Duplicating existing styles
.my-table {
    border: 1px solid #ccc;
    width: 100%;
}

// ✅ GOOD - Only add what's truly unique
.my-plugin-highlight {
    background-color: #fffacd;
    font-weight: bold;
}
```

---

### **3. Organize CSS Files**

```css
/* assets/css/main.css */

/* =================================
   Plugin: My Plugin
   Version: 1.0.0
   ================================= */

/* Layout */
.my-plugin-container { }
.my-plugin-sidebar { }

/* Components */
.my-plugin-card { }
.my-plugin-badge { }

/* Utilities */
.my-plugin-hidden { display: none; }
.my-plugin-spacer { margin: 20px 0; }
```

---

### **4. Cache Busting for Updates**

```php
// Add version parameter
$plugin_version = '1.0.2';
echo '<link rel="stylesheet" href="' . SWB . 'plugins/my-plugin/assets/css/main.css?v=' . $plugin_version . '">';

// Or use file modification time
$css_file = SB . 'plugins/my-plugin/assets/css/main.css';
$version = file_exists($css_file) ? filemtime($css_file) : time();
echo '<link rel="stylesheet" href="' . SWB . 'plugins/my-plugin/assets/css/main.css?v=' . $version . '">';
```

---

### **5. Loading Order**

```php
// ✅ CORRECT ORDER
<!-- 1. CSS files first -->
<link rel="stylesheet" href="<?php echo SWB; ?>plugins/my-plugin/assets/css/main.css">

<!-- 2. HTML content -->
<div class="my-plugin-container">
    Content here
</div>

<!-- 3. JS files at bottom (before </body>) -->
<script src="<?php echo SWB; ?>plugins/my-plugin/assets/js/main.js"></script>
```

---

## 🔍 **Debugging CSS Loading Issues**

### **Debug Helper Function**

```php
// Add to your plugin interface file
function debugAssetPaths() {
    if (!isset($_GET['debug_assets'])) return;
    
    echo '<div style="background: #f0f0f0; padding: 20px; margin: 20px 0; border: 2px solid #333;">';
    echo '<h3>Asset Path Debugging</h3>';
    echo '<strong>Server Paths (for PHP require):</strong><br>';
    echo 'SB: ' . SB . '<br>';
    echo 'SIMBIO: ' . SIMBIO . '<br>';
    echo 'LIB: ' . LIB . '<br>';
    echo '<br>';
    echo '<strong>Web Paths (for browser/CSS/JS):</strong><br>';
    echo 'SWB: ' . SWB . '<br>';
    echo 'AWB: ' . AWB . '<br>';
    echo '<br>';
    echo '<strong>Plugin Paths:</strong><br>';
    echo '__DIR__: ' . __DIR__ . '<br>';
    echo '__FILE__: ' . __FILE__ . '<br>';
    echo '<br>';
    echo '<strong>Expected CSS URL:</strong><br>';
    echo SWB . 'plugins/my-plugin/assets/css/custom.css<br>';
    echo '</div>';
}

// Call it
debugAssetPaths();
```

**Usage**: Add `?debug_assets=1` to your plugin URL

---

### **Browser Console Check**

```javascript
// Paste in browser console to check loaded stylesheets
console.log('=== Loaded Stylesheets ===');
for(let i = 0; i < document.styleSheets.length; i++) {
    let sheet = document.styleSheets[i];
    console.log((i+1) + '. ' + (sheet.href || 'inline styles'));
}

// Check for specific plugin CSS
let pluginCSS = Array.from(document.styleSheets).filter(sheet => 
    sheet.href && sheet.href.includes('my-plugin')
);
console.log('Plugin CSS loaded:', pluginCSS.length > 0);
```

---

### **Network Tab Check**

1. Open browser DevTools (F12)
2. Go to **Network** tab
3. Reload page
4. Filter by **CSS**
5. Look for your plugin CSS file:
   - ✅ **Status 200**: CSS loaded successfully
   - ❌ **Status 404**: CSS not found (wrong path!)
   - ❌ **Status 403**: Permission denied

**Common 404 Causes**:
```
Request URL: https://domain.com/admin/assets/custom.css
              ↑ Wrong! Should be: .../plugins/my-plugin/assets/...

Request URL: file:///var/www/html/slims/plugins/...
              ↑ Wrong! Used SB instead of SWB

Request URL: https://domain.com/slims/s951dev/plugins/my-plugin/assets/cstom.css
                                                                         ↑ Typo!
```

---

## 💡 **Real-World Examples**

### **Example 1: Simple Plugin with Custom CSS**

```php
// File: my_plugin.plugin.php
<?php
/**
 * Plugin Name: Simple Report
 * Version: 1.0.0
 */
use SLiMS\Plugins;
$plugins = Plugins::getInstance();
$plugins->registerMenu('reporting', 'Simple Report', __DIR__ . '/admin.php');
```

```php
// File: admin.php
<?php
define('INDEX_AUTH', '1');
require_once SB . 'admin/default/session.inc.php';
require_once SB . 'admin/default/session_check.inc.php';

$can_read = utility::havePrivilege('reporting', 'r');
if (!$can_read) die('<div class="errorBox">No privilege</div>');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple Report</title>
    <!-- Load custom CSS -->
    <link rel="stylesheet" href="<?php echo SWB; ?>plugins/simple-report/assets/report.css">
</head>
<body>
    <div class="my-report-container">
        <h2>Report Content</h2>
        <table class="s-table table">
            <!-- Use SLiMS standard table class -->
        </table>
    </div>
    
    <script src="<?php echo SWB; ?>plugins/simple-report/assets/report.js"></script>
</body>
</html>
```

```css
/* File: assets/report.css */
.my-report-container {
    padding: 20px;
    background: #f9f9f9;
}

.my-report-highlight {
    background-color: #ffeb3b;
    padding: 2px 5px;
}
```

---

### **Example 2: Iframe Plugin with Different CSS**

```php
// File: admin.php
<?php
define('INDEX_AUTH', '1');
require_once SB . 'admin/default/session.inc.php';
require_once SB . 'admin/default/session_check.inc.php';

$reportView = isset($_GET['reportView']);

if (!$reportView): 
?>
<!-- PARENT MODE: Load form CSS -->
<link rel="stylesheet" href="<?php echo SWB; ?>plugins/my-plugin/assets/css/form.css">

<form method="get" target="reportView">
    <!-- Form fields -->
</form>

<iframe name="reportView" src="..."></iframe>

<?php exit; endif; ?>

<?php
// IFRAME MODE: Load report CSS
ob_start();
echo '<link rel="stylesheet" href="' . SWB . 'plugins/my-plugin/assets/css/report.css">';
echo '<div class="report-content">...</div>';

$content = ob_get_clean();
require SB . 'admin/' . $sysconf['admin_template']['dir'] . '/printed_page_tpl.php';
?>
```

---

## 📝 **Quick Reference Card**

```php
// ═══════════════════════════════════════════════
// CSS LOADING QUICK REFERENCE
// ═══════════════════════════════════════════════

// ✅ CORRECT WAYS:
// ────────────────────────────────────────────────

// 1. Direct in interface file (RECOMMENDED)
echo '<link rel="stylesheet" href="' . SWB . 'plugins/PLUGIN-NAME/assets/style.css">';

// 2. Using hook (for global loading)
$plugins->register(Plugins::ADMIN_HEADER, function() {
    echo '<link rel="stylesheet" href="' . SWB . 'plugins/PLUGIN-NAME/assets/style.css">';
});

// 3. With cache busting
$version = '1.0.0';
echo '<link rel="stylesheet" href="' . SWB . 'plugins/PLUGIN-NAME/assets/style.css?v=' . $version . '">';


// ❌ WRONG WAYS (WILL CAUSE 404):
// ────────────────────────────────────────────────

echo '<link href="assets/style.css">';                    // ❌ Relative path
echo '<link href="./assets/style.css">';                  // ❌ Relative path
echo '<link href="../plugins/my-plugin/assets/style.css">'; // ❌ Relative path
echo '<link href="' . SB . 'plugins/my-plugin/assets/style.css">'; // ❌ Server path
echo '<link href="https://mydomain.com/slims/plugins/...">'; // ❌ Hardcoded


// CONSTANTS REFERENCE:
// ────────────────────────────────────────────────

SB    = /var/www/html/slims/s951dev/           → PHP require
SWB   = https://domain.com/slims/s951dev/      → CSS/JS/IMG
AWB   = https://domain.com/slims/s951dev/admin/ → Admin links
```

---

## ✅ **Summary**

1. **Always use `SWB` constant** for web assets (CSS, JS, images)
2. **Never use relative paths** (`assets/...`, `./assets/...`)
3. **Never use server paths** (`SB`) for browser resources
4. **Load directly in interface file** for plugin-specific styles
5. **Use hooks** only for global styles needed everywhere
6. **Prefer admin template classes** over custom CSS
7. **Add version parameter** for cache busting
8. **Debug with browser DevTools** Network tab (check for 404s)

---

## 🎉 **No More 404 Errors!**

Dengan mengikuti panduan ini, CSS dan asset lainnya akan ter-load dengan sempurna! 🚀
