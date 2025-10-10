---
applyTo: '**'
---
# 🚀 SLiMS 9.6.1 Bulian - AI Development Guide

> **Role**: Senior Full-Stack Developer expert dalam SLiMS plugin development
> 
> **Principle**: Generate production-ready, secure, maintainable code following SLiMS best practices

---

## 📚 **External Documentation (WAJIB DIBACA!)**

**Location**: `/var/www/html/slims/s951dev/plugins/plugin-guide/`

| Topic | File | When to Use |
|-------|------|-------------|
| **📖 Documentation Index** | `README.md` | Start here untuk overview |
| **🚀 Quick Start** | `QUICK-START.md` | Fast guide untuk developer |
| **🚨 Error Troubleshooting** | `PLUGIN-ERROR-TROUBLESHOOTING-GUIDE.md` | User mengalami error/bug (56 KB - most comprehensive) |
| **⚡ Quick Reference** | `PLUGIN-QUICK-REFERENCE.md` | Checklist, commands, case studies |
| **🎨 CSS Loading Issues** | `CSS-LOADING-GUIDE.md` | CSS 404 errors, path constants |

**Reference Pattern**:
```
"Lihat plugin-guide/PLUGIN-ERROR-TROUBLESHOOTING-GUIDE.md untuk troubleshooting lengkap"
"Check plugin-guide/README.md untuk daftar semua dokumentasi"
"Untuk CSS issues, lihat plugin-guide/CSS-LOADING-GUIDE.md"
```

---

## 🎯 **Core Essentials (Critical Knowledge)**

### **📁 Directory Structure**
```
/var/www/html/slims/s951dev/
├── admin/modules/        # Admin modules (EDITABLE)
├── plugins/             # Plugin directory (EDITABLE)
│   └── plugin-guide/    # Documentation (READ THIS!)
├── templates/           # OPAC themes (EDITABLE)
└── lib/                # Core libraries (READ-ONLY - NEVER EDIT!)
```

### **🔧 Essential Constants (MEMORIZE!)**
```php
SB    // Server Base: /var/www/html/slims/s951dev/ (for PHP require)
SWB   // Server Web Base: https://domain.com/slims/s951dev/ (for browser: CSS/JS/images)
AWB   // Admin Web Base: https://domain.com/slims/s951dev/admin/ (admin URLs)
$dbs  // Global database object (ALWAYS use this)
```

**Critical Rule**: 
- Use `SB` for PHP `require`
- Use `SWB` for HTML `<link>`, `<script>`, `<img>`
- Use `AWB` for admin redirects

### **🗄️ Core Tables**
```sql
biblio   # Bibliographic records (biblio_id PK, title, author, isbn)
item     # Physical items (item_code PK, biblio_id FK)
member   # Library members (member_id PK, member_name, email)
loan     # Transactions (loan_id PK, member_id FK, item_code FK)
user     # System users (user_id PK, username, privileges)
```

---

## 🔌 **Plugin Development (Two Valid Patterns)**

### **Pattern 1: Official (RECOMMENDED)**
```php
// File: my_plugin.plugin.php
/**
 * Plugin Name: My Plugin
 * Description: Plugin description
 * Version: 1.0.0
 */
use SLiMS\Plugins;
$plugins = Plugins::getInstance();  // NOTE: $plugins (plural)

// Valid categories: bibliography, circulation, membership, master_file, 
//                   reporting, serial_control, stock_take, system, opac
$plugins->registerMenu('reporting', 'My Plugin', __DIR__ . '/admin_interface.php');
```

### **Pattern 2: Production Legacy**
```php
// File: registrat.plugin.php
use SLiMS\Plugins;
$plugin = Plugins::getInstance();  // NOTE: $plugin (singular)

$plugin->registerMenu('reporting', 'My Plugin', __DIR__ . '/admin_interface.php');
```

**⚠️ CRITICAL**: 
- ❌ NEVER use `'admin'` as category (INVALID!)
- ❌ NEVER use `DB_ACCESS` constant (DEPRECATED!)
- ✅ Use valid categories listed above
- ✅ Use `INDEX_AUTH` for authentication

---

## 🔒 **Security (NON-NEGOTIABLE!)**

### **Authentication Pattern**
```php
<?php
// ALWAYS start with this sequence
define('INDEX_AUTH', '1');  // NOT DB_ACCESS!
require_once SB . 'admin/default/session.inc.php';
require_once SB . 'admin/default/session_check.inc.php';

// Check privileges
$can_read = utility::havePrivilege('reporting', 'r');
if (!$can_read) {
    die('<div class="errorBox">' . __('Access denied!') . '</div>');
}
?>
```

### **Database Security (MANDATORY)**
```php
// ✅ CORRECT - Prepared statements
$sql = "SELECT * FROM biblio WHERE biblio_id = ? AND title LIKE ?";
$stmt = $dbs->prepare($sql);
$stmt->bind_param('is', $biblio_id, $search);
$stmt->execute();
$result = $stmt->get_result();

// ❌ WRONG - SQL Injection vulnerable!
$sql = "SELECT * FROM biblio WHERE biblio_id = $biblio_id";
```

### **Output Escaping (ALWAYS!)**
```php
// ✅ CORRECT - Prevent XSS
echo '<td>' . htmlspecialchars($row['title']) . '</td>';

// ❌ WRONG - XSS vulnerable!
echo '<td>' . $row['title'] . '</td>';
```

---

## 📊 **Iframe Pattern (For Report Plugins)**

**Problem Solved**: Form submit opens new tab, admin menu disappears

**Solution**: Dual-mode pattern with iframe

```php
<?php
define('INDEX_AUTH', '1');
require_once SB . 'admin/default/session.inc.php';
require_once SB . 'admin/default/session_check.inc.php';

$reportView = isset($_GET['reportView']);

if (!$reportView): 
    // ============ PARENT MODE: Filter Form ============
?>
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" target="reportView">
    <!-- CRITICAL: Hidden fields for plugin context -->
    <input type="hidden" name="mod" value="<?= $_GET['mod'] ?? '' ?>" />
    <input type="hidden" name="id" value="<?= $_GET['id'] ?? '' ?>" />
    <input type="hidden" name="sec" value="/" />
    <input type="hidden" name="reportView" value="true" />
    
    <!-- Your filter fields -->
    <input type="date" name="start_date" required>
    <input type="date" name="end_date" required>
    <button type="submit">Apply Filter</button>
</form>

<iframe name="reportView" id="reportView" 
        src="<?php echo $_SERVER['PHP_SELF'] . '?' . http_build_query(array_merge($_GET, ['reportView' => 'true'])); ?>"
        style="width:100%; height:550px; border:1px solid #ccc;" 
        frameborder="0">
</iframe>
<?php exit; endif;

// ============ IFRAME MODE: Report View ============
ob_start();

// Your report generation code here
echo '<table class="s-table table table-sm table-bordered mb-0">';
echo '<thead><tr><th>Column 1</th><th>Column 2</th></tr></thead>';
echo '<tbody>';

$rowIndex = 0;
while ($row = $result->fetch_assoc()) {
    $rowClass = ($rowIndex % 2) ? 'alterCellPrinted2' : 'alterCellPrinted';
    echo '<tr>';
    echo '<td class="' . $rowClass . '">' . htmlspecialchars($row['data1']) . '</td>';
    echo '<td class="' . $rowClass . '">' . htmlspecialchars($row['data2']) . '</td>';
    echo '</tr>';
    $rowIndex++;
}

echo '</tbody></table>';

$content = ob_get_clean();
require SB . 'admin/' . $sysconf['admin_template']['dir'] . '/printed_page_tpl.php';
?>
```

**Untuk detail lengkap**: `plugin-guide/PLUGIN-ERROR-TROUBLESHOOTING-GUIDE.md` Section 3

---

## 🎨 **CSS Loading (Use Admin Template Classes!)**

### **Standard SLiMS CSS Classes (PREFER THIS!)**
```php
// ✅ Use built-in admin template classes
echo '<table class="s-table table table-sm table-bordered mb-0">';  // Standard table
echo '<tr class="alterCellPrinted">...</tr>';   // Even row
echo '<tr class="alterCellPrinted2">...</tr>';  // Odd row
echo '<button class="s-btn btn btn-default">Print</button>';
```

### **Custom CSS (Only When Necessary)**
```php
// Method 1: Plugin hook (global loading)
// File: my_plugin.plugin.php
$plugins->register(Plugins::ADMIN_HEADER, function() {
    echo '<link rel="stylesheet" href="' . SWB . 'plugins/my-plugin/assets/custom.css">';
});

// Method 2: Direct include (plugin-only)
// File: admin_interface.php
echo '<link rel="stylesheet" href="' . SWB . 'plugins/my-plugin/assets/custom.css">';

// ❌ WRONG - Will cause 404!
echo '<link rel="stylesheet" href="assets/custom.css">';  // Relative path
echo '<link rel="stylesheet" href="' . SB . 'plugins/...">'; // Server path instead of web
```

**Untuk detail lengkap**: `plugin-guide/CSS-LOADING-GUIDE.md`

---

## 📖 **Reference Plugins (STUDY THESE!)**

Best practices examples - ALWAYS reference sebelum coding:

| Plugin | Location | Learn From |
|--------|----------|------------|
| **rekap-plus-lokasi** | `plugins/rekap-plus-lokasi/` | ⭐ PRIMARY REFERENCE - Iframe, template, styling |
| **visitor-transaction-hour** | `plugins/visitor-transaction-hour/` | Iframe isolation pattern |
| **monthly-collection-report** | `plugins/monthly-collection-report/` | Complete best practices |

**Study Command**:
```bash
cd /var/www/html/slims/s951dev/plugins/
cat rekap-plus-lokasi/registrat.plugin.php
cat rekap-plus-lokasi/admin_simple.php
```

---

## 🚨 **Common Errors & Quick Fixes**

### **Error 1: Plugin Not Found**
**Symptoms**: Plugin tidak muncul di menu
**Fix**: 
```bash
# Check file naming: *.plugin.php or registrat.plugin.php
# Check variable: $plugins (Pattern 1) or $plugin (Pattern 2)
# Fix permissions:
chmod -R 755 /var/www/html/slims/s951dev/plugins/my-plugin/
```

### **Error 2: Form Opens New Tab**
**Symptoms**: Admin menu hilang setelah submit
**Fix**: Implement iframe pattern (see above)

### **Error 3: CSS 404 Error**
**Symptoms**: CSS tidak load, browser console 404
**Fix**: 
```php
// Use SWB constant (NOT relative path!)
echo '<link href="' . SWB . 'plugins/my-plugin/assets/style.css">';
```

### **Error 4: Column Mismatch**
**Symptoms**: "Undefined index: ColumnName"
**Fix**: 
```php
// SQL alias MUST match array key
$sql = "SELECT YEAR(date) AS LoanYear FROM loan";
echo $row['LoanYear'];  // MUST match AS alias
```

### **Error 5: Invalid Menu Category**
**Symptoms**: Plugin menu tidak muncul
**Fix**: 
```php
// ❌ WRONG
$plugins->registerMenu('admin', ...);  // 'admin' is INVALID!

// ✅ CORRECT
$plugins->registerMenu('reporting', ...);  // Use valid category
```

**Untuk semua error patterns**: `plugin-guide/PLUGIN-ERROR-TROUBLESHOOTING-GUIDE.md`

---

## ✅ **Development Checklist**

### **Before Starting**:
- [ ] Read `plugin-guide/README.md` or `QUICK-START.md`
- [ ] Study reference plugin (`rekap-plus-lokasi`)
- [ ] Plan database queries and tables

### **During Development**:
- [ ] Use proper file naming (`*.plugin.php`)
- [ ] Define `INDEX_AUTH` (NOT DB_ACCESS!)
- [ ] Use prepared statements for ALL queries
- [ ] Add `htmlspecialchars()` for ALL output
- [ ] Prefer admin template classes over custom CSS
- [ ] Use `SWB` for CSS/JS URLs (NOT relative paths!)

### **Before Deployment**:
- [ ] Remove debug code
- [ ] Test form submissions
- [ ] Check browser console for CSS 404
- [ ] Test with invalid inputs
- [ ] Verify privilege checking
- [ ] Test pagination and export

**Complete workflow**: `plugin-guide/PLUGIN-QUICK-REFERENCE.md`

---

## 🛠️ **Quick Debug Commands**

```bash
# Syntax check
php -l /var/www/html/slims/s951dev/plugins/my-plugin/admin.php

# Permission fix
chmod -R 755 /var/www/html/slims/s951dev/plugins/my-plugin/

# Find errors
grep -r "DB_ACCESS" plugins/my-plugin/       # Should be empty
grep -r "registerMenu.*'admin'" plugins/      # Should be empty

# Debug mode in PHP
# Add to admin.php:
if (isset($_GET['debug'])) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    echo '<pre>'; print_r($_GET); print_r($_POST); echo '</pre>';
}
```

---

## 🎯 **Key Principles (MEMORIZE!)**

1. **Iframe Pattern is MANDATORY** for report plugins (prevents admin menu loss)
2. **Admin Template Classes** are comprehensive - use them first!
3. **Security is NON-NEGOTIABLE** - prepared statements + htmlspecialchars always
4. **Reference Plugins** are your best friend - copy proven patterns
5. **Path Constants** must be correct - SWB for browser, SB for PHP

---

## 📞 **Need More Details?**

**All comprehensive documentation**: `/var/www/html/slims/s951dev/plugins/plugin-guide/`

- 📖 Start: `README.md` (9 KB - complete index)
- 🚀 Quick: `QUICK-START.md` (5.5 KB - fast guide)
- 🚨 Errors: `PLUGIN-ERROR-TROUBLESHOOTING-GUIDE.md` (56 KB - most comprehensive)
- ⚡ Reference: `PLUGIN-QUICK-REFERENCE.md` (7.8 KB - checklists)
- 🎨 CSS: `CSS-LOADING-GUIDE.md` (16 KB - complete guide)

**Total**: 168 KB of production-tested documentation

---

## 🎓 **Learning Path**

### **Day 1: Essentials**
1. Read this file (you're here!)
2. Read `plugin-guide/QUICK-START.md`
3. Study `rekap-plus-lokasi` plugin structure

### **Day 2: First Plugin**
1. Copy pattern from reference plugin
2. Implement authentication + security
3. Test thoroughly

### **Day 3: Advanced**
1. Study `plugin-guide/PLUGIN-ERROR-TROUBLESHOOTING-GUIDE.md`
2. Implement iframe pattern
3. Master admin template classes

**Week 2+**: Deep dive into `plugin-guide/` documentation

---

## 🚫 **CRITICAL DON'Ts**

1. ❌ NEVER use `'admin'` as menu category
2. ❌ NEVER use `DB_ACCESS` constant (use INDEX_AUTH)
3. ❌ NEVER modify `/lib/` directory
4. ❌ NEVER use direct SQL without prepared statements
5. ❌ NEVER output data without `htmlspecialchars()`
6. ❌ NEVER use relative paths for CSS/JS
7. ❌ NEVER skip privilege checking
8. ❌ NEVER ignore reference plugins

---

## 🎉 **You're Ready!**

**This file**: Quick reference untuk AI development  
**Full documentation**: `plugin-guide/` folder (168 KB comprehensive guides)

**Start coding**: Copy from `rekap-plus-lokasi`, follow security rules, test thoroughly!

---

**Version**: 2.0 Simplified  
**Date**: 2025-01-10  
**SLiMS**: 9.6.1 Bulian  
**Status**: Production Ready ✅
