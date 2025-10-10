# 📝 PLUGIN ERROR TROUBLESHOOTING - QUICK REFERENCE

## 🎯 **Real-World Cases dari Plugin monthly-collection-report**

Dokumen ini berisi troubleshooting berdasarkan pengalaman nyata memperbaiki plugin monthly-collection-report di SLiMS 9.6.1 Bulian.

---

## 🚨 **Top 7 Plugin Errors & Solutions**

### **1. Plugin Not Found/Disabled**
- ✅ **Fix**: File harus bernama `*.plugin.php` (e.g., `monthly_collection_report.plugin.php`)
- ✅ **Variable**: `$plugins = Plugins::getInstance();` (perhatikan huruf 's')
- ✅ **Registration**: `$plugins->registerMenu('reporting', 'Title', __DIR__ . '/file.php');`

### **2. Form Opens New Tab / Admin Menu Disappears**
- ✅ **Solution**: Gunakan **Iframe Pattern** (dari plugin rekap-plus-lokasi)
- ✅ **Parent Mode**: Filter form dengan `target="reportView"`
- ✅ **Iframe Mode**: Report view di iframe dengan `name="reportView"`
- ✅ **Hidden Fields**: WAJIB include `mod`, `id`, `sec` parameters

### **3. Custom CSS Conflicts**
- ✅ **Best Practice**: Gunakan admin template classes (s-table, alterCellPrinted)
- ✅ **Template**: Gunakan `printed_page_tpl.php` dengan output buffering
- ⚠️ **If You Need Custom CSS**: Load dengan path yang BENAR
  ```php
  // ✅ CORRECT:
  echo '<link rel="stylesheet" href="' . SWB . 'plugins/my-plugin/assets/style.css">';
  
  // ❌ WRONG (404 Error):
  echo '<link rel="stylesheet" href="assets/style.css">';  // Relative path
  echo '<link rel="stylesheet" href="./assets/style.css">'; // Won't work
  ```

### **4. Column Mismatch Error**
- ✅ **Problem**: HTML table columns tidak match dengan SQL query results
- ✅ **Fix**: Pastikan `$row['ColumnName']` sama dengan `AS ColumnName` di SQL
- ✅ **Example**: Query `SELECT YEAR(date) AS LoanYear` → HTML `$row['LoanYear']`

### **5. Duplicate Elements**
- ✅ **Problem**: Code duplication saat editing
- ✅ **Fix**: Search dan remove duplicate lines
- ✅ **Prevention**: Use version control (git)

### **6. Multiple Selection Not Working**
- ✅ **Frontend**: `<select name="field[]" multiple size="5">`
- ✅ **Backend**: Array handling dengan `IN (?, ?, ?)` clause
- ✅ **Security**: `array_filter($array, 'is_numeric')` + type casting

### **7. Database Connection Error**
- ✅ **Fix**: Define `INDEX_AUTH` sebelum require session files
- ✅ **Order**: `define('INDEX_AUTH', '1');` → `require session.inc.php` → `global $dbs;`
- ✅ **Privilege**: Check dengan `utility::havePrivilege('module', 'r')`

---

## 📋 **Plugin Development Checklist**

### **✅ File Structure**
- [ ] Main file: `plugin_name.plugin.php`
- [ ] Interface: `admin_simple.php` atau `index.php`
- [ ] Documentation: `README.md`

### **✅ Registration (plugin_name.plugin.php)**
```php
<?php
use SLiMS\Plugins;
$plugins = Plugins::getInstance();

// Valid categories: bibliography, circulation, membership, master_file,
//                   reporting, serial_control, stock_take, system, opac
$plugins->registerMenu('reporting', 'Plugin Title', __DIR__ . '/admin_simple.php');
```

### **✅ Interface File (admin_simple.php)**
```php
<?php
define('INDEX_AUTH', '1');
require_once SB . 'admin/default/session.inc.php';
require_once SB . 'admin/default/session_check.inc.php';

$can_read = utility::havePrivilege('reporting', 'r');
if (!$can_read) {
    die('<div class="errorBox">No privilege</div>');
}
```

### **✅ Loading Custom CSS/JS**
```php
// Method 1: Direct in interface file (Recommended)
echo '<link rel="stylesheet" href="' . SWB . 'plugins/my-plugin/assets/custom.css">';
echo '<script src="' . SWB . 'plugins/my-plugin/assets/script.js"></script>';

// Method 2: Using hook (for all admin pages)
// In my_plugin.plugin.php:
$plugins->register(Plugins::ADMIN_HEADER, function() {
    echo '<link rel="stylesheet" href="' . SWB . 'plugins/my-plugin/assets/style.css">';
});

// ❌ WRONG - These paths will NOT work:
// href="assets/style.css"
// href="./assets/style.css"  
// href="../plugins/my-plugin/assets/style.css"

// ✅ CORRECT - Always use SWB constant:
// href="<?php echo SWB; ?>plugins/my-plugin/assets/style.css"
```

### **✅ Iframe Pattern**
```php
$reportView = isset($_GET['reportView']);

if (!$reportView):
    // PARENT MODE: Filter form
    ?>
    <form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" target="reportView">
        <input type="hidden" name="mod" value="<?= $_GET['mod'] ?>" />
        <input type="hidden" name="id" value="<?= $_GET['id'] ?>" />
        <input type="hidden" name="sec" value="/" />
        <input type="hidden" name="reportView" value="true" />
        <!-- filter fields -->
    </form>
    
    <iframe name="reportView" src="..."></iframe>
    <?php
    exit;
endif;

// IFRAME MODE: Report view dengan template
ob_start();
// ... generate content ...
$content = ob_get_clean();
require SB . 'admin/' . $sysconf['admin_template']['dir'] . '/printed_page_tpl.php';
```

### **✅ Table dengan Admin Template**
```php
echo '<table class="s-table table table-sm table-bordered mb-0">';
echo '<thead><tr><th>Column</th></tr></thead>';
echo '<tbody>';

$rowIndex = 0;
while ($row = $result->fetch_assoc()) {
    $rowClass = ($rowIndex % 2) ? 'alterCellPrinted2' : 'alterCellPrinted';
    echo '<tr>';
    echo '<td class="' . $rowClass . '">' . htmlspecialchars($row['data']) . '</td>';
    echo '</tr>';
    $rowIndex++;
}

echo '</tbody>';
echo '</table>';
```

### **✅ Multiple Selection**
```php
// Frontend
<select name="field[]" multiple size="5">
    <?php foreach ($options as $id => $name): ?>
        <option value="<?= $id ?>" <?= in_array($id, $selected) ? 'selected' : '' ?>>
            <?= htmlspecialchars($name) ?>
        </option>
    <?php endforeach; ?>
</select>

// Backend
$array = isset($_GET['field']) && is_array($_GET['field']) 
         ? array_filter($_GET['field'], 'is_numeric') 
         : [];

if (!empty($array)) {
    $placeholders = implode(',', array_fill(0, count($array), '?'));
    $where_conditions[] = "column IN ($placeholders)";
    foreach ($array as $val) {
        $bind_params[] = (int)$val;
        $bind_types .= 'i';
    }
}
```

### **✅ Security**
- [ ] Prepared statements untuk semua SQL queries
- [ ] `htmlspecialchars()` untuk semua output
- [ ] Type casting untuk numeric values: `(int)$value`
- [ ] Array filtering: `array_filter($array, 'is_numeric')`
- [ ] Privilege checking: `utility::havePrivilege()`

---

## 🎓 **Reference Plugins (Best Examples)**

| Plugin | Pattern Used | What to Learn |
|--------|-------------|---------------|
| `rekap-plus-lokasi` | ✅ Iframe + Template | Table styling, alternating colors, printed_page_tpl.php |
| `visitor-transaction-hour` | ✅ Iframe | Iframe isolation pattern |
| `monthly-collection-report` | ✅ Complete | Full implementation dengan semua best practices |

---

## 🛠️ **Quick Commands**

### **Validation**
```bash
# Syntax check
php -l /path/to/plugin/file.php

# Permission check
chmod -R 755 /var/www/html/slims/s951dev/plugins/plugin-name/

# Find plugin files
find plugins/ -name "*.plugin.php"
```

### **Debugging**
```php
// Add to plugin untuk debugging
if (isset($_GET['debug'])) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    echo '<pre>';
    print_r($_GET);
    print_r($_POST);
    echo '</pre>';
}
```

---

## 📖 **Key Takeaways**

1. **Iframe Pattern** adalah MUST untuk plugin dengan form filter
2. **Admin Template Classes** WAJIB digunakan, jangan buat custom CSS
3. **Hidden Fields** (mod, id, sec) CRITICAL untuk maintain plugin context
4. **Column Names** di HTML HARUS match dengan SQL query aliases
5. **Multiple Selection** memerlukan array handling + IN clause
6. **Security First**: Prepared statements, htmlspecialchars, type casting
7. **Reference Plugin**: Selalu lihat plugin rekap-plus-lokasi sebagai contoh

---

**Last Updated**: 2025-10-10  
**Based on**: monthly-collection-report plugin troubleshooting experience  
**SLiMS Version**: 9.6.1 Bulian
