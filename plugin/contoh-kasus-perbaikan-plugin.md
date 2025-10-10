# 🔧 Complete SLiMS Plugin Error Troubleshooting Guide

## 📊 **Pattern-Based Troubleshooting Strategy**

### **🔍 Step 1: Identify Your Plugin Pattern**

```bash
# Quick pattern detection script
cd /var/www/html/slims/s951dev/plugins/your-plugin/

echo "=== PATTERN DETECTION ==="

# Check Pattern 1 (Official)
if [ -f "*.plugin.php" ]; then
    echo "✓ Pattern 1: Official Documentation Pattern Detected"
    echo "  - Main file: $(ls *.plugin.php | head -1)"
fi

# Check Pattern 2 (Production)
if [ -f "registrat.plugin.php" ]; then
    echo "✓ Pattern 2: Production Legacy Pattern Detected"
    echo "  - Registration file: registrat.plugin.php"
fi

# Check routing files
if [ -f "admin.routes.php" ] || [ -f "*.routes.php" ]; then
    echo "✓ Advanced routing detected"
fi

echo "=== PATTERN ANALYSIS COMPLETE ==="
```

### **🛠️ Pattern-Specific Troubleshooting**

#### **Pattern 1 Troubleshooting (Official)**
```bash
# Common issues:
# 1. Check plugin file naming
find . -name "*.plugin.php" -not -name "registrat.plugin.php"

# 2. Verify variable usage
grep -r "\$plugins.*getInstance" .
grep -r "registerMenu.*\.php" .

# 3. Check direct file registration
grep -r "plugins.*registerMenu" .
```

#### **Pattern 2 Troubleshooting (Production)**
```bash
# Common issues:
# 1. Check registrat.plugin.php exists
[ -f "registrat.plugin.php" ] || echo "ERROR: Missing registrat.plugin.php"

# 2. Verify routing setup
[ -f "admin.routes.php" ] && echo "✓ Advanced routing available"

# 3. Check plugin container access
curl -I "https://your-domain.com/admin/plugin_container.php?mod=$(basename $(pwd))&id=xxx&sec=/"
```

---

##  **Pattern Migration Guide**

### **Migration: Pattern 2 → Pattern 1 (Recommended)**

```bash
# Step 1: Backup existing plugin
cp -r plugins/my-plugin plugins/my-plugin-backup

# Step 2: Rename registration file
cd plugins/my-plugin
mv registrat.plugin.php my_plugin.plugin.php
```

```php
// Step 3: Update registration syntax in my_plugin.plugin.php
// FROM (Pattern 2):
<?php
use SLiMS\Plugins;
$plugin = Plugins::getInstance();
$plugin->registerMenu("admin", "My Plugin", __DIR__ . "/admin_simple.php");

// TO (Pattern 1 - Official):
<?php
/**
 * Plugin Name: My Plugin
 * Plugin URI: -
 * Description: My plugin converted to official pattern
 * Version: 1.0.0
 * Author: Your Name
 */
use SLiMS\Plugins;
$plugins = Plugins::getInstance();
$plugins->registerMenu("admin", "My Plugin", __DIR__ . "/admin_simple.php");
```

### **Migration: Pattern 1 → Pattern 2 (If Required)**

```bash
# Only if you need production legacy compatibility
cd plugins/my-plugin
cp my_plugin.plugin.php registrat.plugin.php
```

```php
// Update syntax in registrat.plugin.php
// FROM (Pattern 1):
$plugins = Plugins::getInstance();

// TO (Pattern 2):
$plugin = Plugins::getInstance();
```

### **Hybrid Approach (Both Patterns)**

```php
// Create both files for maximum compatibility
// File: my_plugin.plugin.php (Pattern 1)
<?php
/**
 * Plugin Name: My Plugin
 * Plugin URI: -
 * Description: Official pattern registration
 * Version: 1.0.0
 * Author: Your Name
 */
use SLiMS\Plugins;
$plugins = Plugins::getInstance();
$plugins->registerMenu("admin", "My Plugin", __DIR__ . "/admin_simple.php");

// File: registrat.plugin.php (Pattern 2 fallback)
<?php
// Fallback registration for legacy compatibility
if (!defined('PLUGIN_ALREADY_REGISTERED')) {
    use SLiMS\Plugins;
    $plugin = Plugins::getInstance();
    $plugin->registerMenu("admin", "My Plugin", __DIR__ . "/admin_simple.php");
    define('PLUGIN_ALREADY_REGISTERED', true);
}
```

---

## 🎯 **Real-World Implementation Examples**

### **⚡ 5-Minute Plugin Fix Workflow**

```bash
#!/bin/bash
# Quick plugin diagnosis and fix script
PLUGIN_NAME="$1"
PLUGIN_PATH="/var/www/html/slims/s951dev/plugins/${PLUGIN_NAME}"

echo "=== EMERGENCY PLUGIN DIAGNOSTIC ==="
echo "Plugin: $PLUGIN_NAME"
echo "Path: $PLUGIN_PATH"

# Step 1: Check basic requirements
echo "1. Checking basic requirements..."
[ -d "$PLUGIN_PATH" ] || { echo "❌ Plugin directory not found!"; exit 1; }

# Step 2: Fix permissions immediately
echo "2. Fixing permissions..."
chmod -R 755 "$PLUGIN_PATH"
chown -R www-data:www-data "$PLUGIN_PATH"

# Step 3: Identify pattern and fix registration
echo "3. Checking registration pattern..."
if [ -f "$PLUGIN_PATH/${PLUGIN_NAME}.plugin.php" ]; then
    echo "✓ Pattern 1 (Official) detected"
    MAIN_FILE="${PLUGIN_NAME}.plugin.php"
elif [ -f "$PLUGIN_PATH/registrat.plugin.php" ]; then
    echo "✓ Pattern 2 (Production) detected"
    MAIN_FILE="registrat.plugin.php"
else
    echo "❌ No valid registration file found!"
    echo "Creating emergency registration file..."
    cat > "$PLUGIN_PATH/registrat.plugin.php" << 'EOF'
<?php
/**
 * Emergency Plugin Registration
 */
use SLiMS\Plugins;
$plugin = Plugins::getInstance();

$plugin->registerMenu("admin", "Emergency Plugin", __DIR__ . "/admin_simple.php");
EOF
    MAIN_FILE="registrat.plugin.php"
fi

# Step 4: Quick validation
echo "4. Validating main file..."
php -l "$PLUGIN_PATH/$MAIN_FILE" || echo "❌ Syntax error in $MAIN_FILE"

# Step 5: Test accessibility
echo "5. Testing plugin accessibility..."
if [ -f "$PLUGIN_PATH/admin_simple.php" ] || [ -f "$PLUGIN_PATH/index.php" ]; then
    echo "✓ Admin interface file found"
else
    echo "⚠️  No admin interface file (admin_simple.php or index.php)"
fi

echo "=== DIAGNOSTIC COMPLETE ==="
echo "Try accessing: /admin/plugin_container.php?mod=${PLUGIN_NAME}&id=xxx&sec=/"
```

---

## 📋 **Comprehensive Plugin Error Checklist**

Panduan lengkap untuk mendiagnosis dan memperbaiki berbagai error yang sering terjadi pada plugin SLiMS 9.6.1 Bulian. Berdasarkan pengalaman nyata troubleshooting multiple plugins.

---

## 🚨 **Common Plugin Errors & Solutions**

### **1. "Plugin not found / disabled!" Error**

**🔴 SYMPTOMS:**
```
Plugin not found / disabled!
```

**🔍 ROOT CAUSES:**
- Nama file registrasi plugin salah
- Struktur direktori plugin tidak sesuai
- Plugin registration pattern yang deprecated
- File registrasi tidak terbaca oleh sistem

**✅ SOLUTIONS:**

#### **A. Fix Plugin Registration File Naming**
```php
// ❌ WRONG - Nama file yang tidak sesuai pattern SLiMS
my_plugin.php
plugin_file.php
custom_plugin.php

// ✅ CORRECT - Gunakan ekstensi .plugin.php (Sesuai Dokumentasi SLiMS)
my_plugin.plugin.php
visitor_transaction_hour.plugin.php
bebas_pustaka.plugin.php

// 📝 CATATAN: Beberapa plugin menggunakan registrat.plugin.php sebagai convention
// tapi menurut dokumentasi resmi SLiMS, format yang benar adalah *.plugin.php
```

#### **B. Correct Plugin Registration Pattern**
```php
// ❌ WRONG - Tanpa format header yang benar
<?php
$plugin = \SLiMS\Plugins::getInstance();

// ❌ WRONG - Kategori menu tidak valid
$plugins->registerMenu('admin', 'My Plugin', __DIR__ . '/admin.php'); // 'admin' bukan kategori!

// ✅ CORRECT - Sesuai Dokumentasi SLiMS Resmi
<?php
/**
 * Plugin Name: My Plugin Name
 * Plugin URI: -
 * Description: Plugin description here
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://your.website
 */
use SLiMS\Plugins;

// get plugin instance (sesuai dokumentasi)
$plugins = Plugins::getInstance();

// registering menus - untuk admin area (gunakan kategori modul yang valid)
// Valid categories: 'bibliography', 'circulation', 'membership', 'master_file', 
//                   'reporting', 'serial_control', 'stock_take', 'system'
$plugins->registerMenu('reporting', 'My Report Plugin', __DIR__ . '/admin_interface.php');
$plugins->registerMenu('circulation', 'My Circulation Plugin', __DIR__ . '/circulation_interface.php');
$plugins->registerMenu('membership', 'My Member Plugin', __DIR__ . '/member_interface.php');

// registering menus - untuk OPAC area  
$plugins->registerMenu('opac', 'My OPAC Page', __DIR__ . '/opac_page.php');
```

#### **C. Plugin File Structure (Sesuai Dokumentasi SLiMS)**
```
# Pattern 1: Single file plugin (Simple)
/plugins/
├── hello_world.plugin.php  # Single file plugin

# Pattern 2: Folder-based plugin (Complex)
/plugins/my-plugin/
├── my_plugin.plugin.php    # MANDATORY - Plugin registration  
├── admin_interface.php     # File untuk admin menu
├── opac_page.php          # File untuk OPAC menu (optional)
└── other_files.php        # File pendukung lainnya

# Pattern 3: Deep folder structure (Supported hingga 3 level)
/plugins/category_folder/
├── subfolder/
│   └── my_plugin.plugin.php   # Plugin level 3 masih di-scan SLiMS
```

#### **D. Valid Menu Categories (IMPORTANT!)**

**Admin Area Menu Categories** - Gunakan salah satu dari:

| Category | Description | Privilege Module | Example Use Case |
|----------|-------------|------------------|------------------|
| `bibliography` | Bibliography Management | bibliography | Catalog enhancement, batch editing |
| `circulation` | Circulation/Loan | circulation | Custom loan rules, fine calculator |
| `membership` | Member Management | membership | Member import, ID card generator |
| `master_file` | Master Data | master_file | Authority control, data cleanup |
| `reporting` | Reports & Statistics | reporting | Custom reports, analytics |
| `serial_control` | Serial/Periodical | serial_control | Subscription management |
| `stock_take` | Stock Taking | stock_take | Inventory tools |
| `system` | System Administration | system | System utilities, maintenance |

**OPAC Area Menu Category**:
- `opac` - For public catalog features (no privilege checking needed)

**Example Usage**:
```php
// ❌ WRONG - Invalid category
$plugins->registerMenu('admin', 'My Plugin', __DIR__ . '/admin.php');

// ✅ CORRECT - Valid categories
$plugins->registerMenu('reporting', 'Sales Report', __DIR__ . '/report.php');
$plugins->registerMenu('circulation', 'Fine Calculator', __DIR__ . '/fine.php');
$plugins->registerMenu('membership', 'Member Import', __DIR__ . '/import.php');
$plugins->registerMenu('opac', 'Book Request', __DIR__ . '/opac_request.php');
```

**How to Choose the Right Category**:
1. **Reporting plugins** → Use `'reporting'` (most common for statistics/analytics)
2. **Loan/return features** → Use `'circulation'`
3. **Member-related** → Use `'membership'`
4. **Catalog enhancement** → Use `'bibliography'`
5. **Public features** → Use `'opac'`

---

### **2. "Plugin id not defined!" Error**

**🔴 SYMPTOMS:**
```
Plugin id not defined!
Fatal error: Class 'Plugin\Lib\Router' not found
```

**🔍 ROOT CAUSES:**
- Autoloader tidak ter-load
- Complex routing system tanpa proper dependencies
- Missing namespaces atau class imports

**✅ SOLUTIONS:**

#### **A. Direct File Registration (Sesuai Dokumentasi SLiMS)**
```php
// File: my_plugin.plugin.php
<?php
/**
 * Plugin Name: My Plugin
 * Plugin URI: -
 * Description: Simple plugin example
 * Version: 1.0.0
 * Author: Your Name
 */
use SLiMS\Plugins;
$plugins = Plugins::getInstance();

// Langsung register ke file yang berisi interface
$plugins->registerMenu('reporting', 'My Plugin', __DIR__ . '/plugin_interface.php');

// File: plugin_interface.php - berisi seluruh logic plugin
<?php
// Tidak perlu routing complex, langsung implement fitur
define('INDEX_AUTH', '1');
require_once SB.'admin/default/session.inc.php';
require_once SB.'admin/default/session_check.inc.php';
// ... implementasi plugin
```

#### **B. Fix Autoloader Issues (If Using Complex Routing)**
```php
// File: admin.routes.php
<?php
# LOAD AUTOLOADER FIRST
require_once __DIR__ . '/../vendor/autoload.php';

# THEN USE CLASSES
use Plugin\Lib\Router;
use Plugin\MyPlugin\Controllers\AdminController;

// ... rest of routing code
```

#### **C. Remove Complex Dependencies**
```php
// ❌ WRONG - Over-engineered routing
<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Plugin\Lib\Router;
$route = new Plugin\Lib\Router();
$route->get('/', [AdminController::class, 'index']);
$route->run();

// ✅ CORRECT - Simple direct include
<?php
require_once __DIR__ . '/admin_interface.php';
```

---

### **3. Form Routing Issues - "Keluar dari Menu Admin"**

**🔴 SYMPTOMS:**
```
Filter form membuka tab baru
Form submit keluar dari halaman admin (menu admin hilang)
Plugin kehilangan context admin saat submit form
Data tidak ter-filter dengan benar
```

**🔍 ROOT CAUSES:**
- Tidak menggunakan iframe pattern untuk isolasi plugin
- Form action menggunakan $_SERVER['PHP_SELF'] yang salah
- Missing hidden fields untuk plugin parameters (mod, id, sec)
- Tidak ada target iframe untuk form submission

**✅ SOLUTIONS:**

#### **A. Iframe Pattern Implementation (RECOMMENDED)**

**Pattern ini digunakan oleh plugin rekap-plus-lokasi dan visitor-transaction-hour**

```php
// Dual-mode architecture untuk isolasi plugin
$reportView = isset($_GET['reportView']);

if (!$reportView): 
    // ============================================================================
    // PARENT MODE - Filter Form dengan Admin Menu
    // ============================================================================
?>
<div class="menuBox">
    <div class="menuBoxInner reportIcon">
        <div class="per_title">
            <h2><?php echo __('Report Title'); ?></h2>
        </div>
        <div class="infoBox"><?php echo __('Report Filter'); ?></div>
        
        <div class="sub_section">
            <!-- CRITICAL: Form target ke iframe, bukan _self -->
            <form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" target="reportView" class="form-inline">
                <!-- Hidden fields untuk maintain plugin context -->
                <input type="hidden" name="mod" value="<?= $_GET['mod'] ?? '' ?>" />
                <input type="hidden" name="id" value="<?= $_GET['id'] ?? '' ?>" />
                <input type="hidden" name="sec" value="<?= $_GET['sec'] ?? '/' ?>" />
                <input type="hidden" name="reportView" value="true" />
                
                <!-- Filter fields -->
                <div class="form-group">
                    <label for="start_date"><?php echo __('Start Date'); ?></label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="end_date"><?php echo __('End Date'); ?></label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary"><?php echo __('Apply Filter'); ?></button>
            </form>
        </div>
    </div>
</div>

<!-- Iframe untuk menampilkan hasil report -->
<iframe name="reportView" id="reportView" 
        src="<?php echo $_SERVER['PHP_SELF'] . '?' . http_build_query(array_merge($_GET, ['reportView' => 'true'])); ?>"
        style="width:100%; height:550px; border:1px solid #ccc; margin-top:10px;" 
        frameborder="0">
</iframe>

<?php
exit; // Stop execution untuk parent mode
endif;

// ============================================================================
// IFRAME MODE - Report View menggunakan Template
// ============================================================================
ob_start(); // Start output buffering untuk template

// Generate report content
echo '<div class="mb-2">';
echo __('Report Summary') . ' | ';
echo __('Total Records') . ': <strong>' . number_format($total_records) . '</strong>';
echo ' <a href="#" class="s-btn btn btn-default printReport" onclick="window.print()">' . __('Print') . '</a>';
echo '</div>';

// Data table dengan alternating row colors
if ($data_result && $data_result->num_rows > 0) {
    echo '<table class="s-table table table-sm table-bordered mb-0">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>' . __('Column 1') . '</th>';
    echo '<th>' . __('Column 2') . '</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    $rowIndex = 0;
    while ($row = $data_result->fetch_assoc()) {
        $rowClass = ($rowIndex % 2) ? 'alterCellPrinted2' : 'alterCellPrinted';
        
        echo '<tr>';
        echo '<td class="' . $rowClass . '">' . htmlspecialchars($row['data1']) . '</td>';
        echo '<td class="' . $rowClass . '">' . htmlspecialchars($row['data2']) . '</td>';
        echo '</tr>';
        
        $rowIndex++;
    }
    
    echo '</tbody>';
    echo '</table>';
} else {
    echo '<div class="errorBox">' . __('No data found') . '</div>';
}

// Load admin template untuk proper styling
$content = ob_get_clean();
require SB . 'admin/' . $sysconf['admin_template']['dir'] . '/printed_page_tpl.php';
```

**Benefits**:
- ✅ Admin menu SELALU visible (tidak hilang)
- ✅ Filter form tetap di atas, hasil di iframe bawah
- ✅ Tidak ada new tab yang terbuka
- ✅ Context plugin terjaga sempurna
- ✅ Styling konsisten dengan admin template

#### **B. URL Building Helper Function**

```php
// Helper untuk build URL dengan plugin context terjaga
function buildPluginURL($additional_params = []) {
    $base_params = [
        'mod' => $_GET['mod'] ?? '',
        'id' => $_GET['id'] ?? '',
        'sec' => $_GET['sec'] ?? '/'
    ];
    
    $all_params = array_merge($base_params, $additional_params);
    return $_SERVER['PHP_SELF'] . '?' . http_build_query($all_params);
}

// Usage untuk pagination
$paging_url = buildPluginURL([
    'reportView' => 'true',
    'start_date' => $_GET['start_date'],
    'end_date' => $_GET['end_date']
]);

echo simbio_paging::paging($total_records, $items_per_page, 5, $paging_url);
```

#### **C. Real-World Case Study: monthly-collection-report Plugin**

**Problem Timeline**:
```
Issue 1: Form membuka tab baru ❌
Issue 2: Admin menu hilang saat filter ❌  
Issue 3: Context plugin hilang ❌
Issue 4: Custom CSS conflict dengan pagination ❌
Issue 5: Column mismatch error ❌
Issue 6: Tombol export duplikat ❌
```

**Solution Applied** (Berdasarkan plugin rekap-plus-lokasi):

1. **Implemented Iframe Pattern**:
   - Parent mode untuk filter form
   - Iframe mode untuk report view
   - Form target="reportView"

2. **Used Admin Template**:
   - `ob_start()` dan `ob_get_clean()` untuk buffering
   - `printed_page_tpl.php` untuk consistent styling
   - Class `s-table`, `alterCellPrinted`, `alterCellPrinted2`

3. **Fixed Column Structure**:
   - Removed unnecessary "No" column
   - Aligned HTML columns with SQL query results
   - Used proper data mapping

4. **Security & Validation**:
   - Prepared statements untuk SQL queries
   - htmlspecialchars() untuk output
   - Input validation pada semua parameters

**Final Result**: ✅ Plugin works perfectly with admin menu always visible

---

### **4. Multiple Selection Filter Implementation**
---

### **4. Multiple Selection Filter Implementation**

**🎯 REQUIREMENT:**
```
User wants to select multiple collection types for comparison
Single dropdown is limiting for complex analysis
```

**✅ SOLUTION: Multiple Select with Array Handling**

#### **A. Frontend - Multiple Select Dropdown**

```php
<div class="form-group">
    <label for="coll_type_id"><?php echo __('Collection Type'); ?></label>
    <select name="coll_type_id[]" id="coll_type_id" class="form-control" multiple size="5">
        <option value=""><?php echo __('All Types'); ?></option>
        <?php 
        $selected_types = isset($_GET['coll_type_id']) && is_array($_GET['coll_type_id']) 
                          ? $_GET['coll_type_id'] 
                          : (isset($_GET['coll_type_id']) ? [$_GET['coll_type_id']] : []);
        
        foreach ($coll_types as $id => $name): 
        ?>
            <option value="<?php echo $id; ?>" 
                    <?php echo in_array($id, $selected_types) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($name); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <small class="form-text text-muted">
        <?php echo __('Hold Ctrl (Windows) or Cmd (Mac) to select multiple types'); ?>
    </small>
</div>
```

**Key Changes**:
- `name="coll_type_id[]"` - Array notation untuk multiple values
- `multiple` attribute - Allow multiple selection
- `size="5"` - Show 5 options without scrolling
- Help text untuk user guidance

#### **B. Backend - Array Handling with IN Clause**

```php
// Handle multiple collection types
$coll_type_array = isset($_GET['coll_type_id']) && is_array($_GET['coll_type_id']) 
                   ? array_filter($_GET['coll_type_id'], 'is_numeric')
                   : [];

// Remove empty values
$coll_type_array = array_filter($coll_type_array, function($val) {
    return $val !== '' && $val !== null;
});

// Build IN clause with placeholders for security
if (!empty($coll_type_array)) {
    $placeholders = implode(',', array_fill(0, count($coll_type_array), '?'));
    $where_conditions[] = "i.coll_type_id IN ($placeholders)";
    
    // Bind each ID as integer
    foreach ($coll_type_array as $type_id) {
        $bind_params[] = (int)$type_id;
        $bind_types .= 'i';
    }
}
```

**Security Features**:
- ✅ `array_filter()` dengan `is_numeric` - Only accept numeric values
- ✅ Type casting `(int)$type_id` - Prevent SQL injection
- ✅ Prepared statements dengan placeholders - Safe parameter binding
- ✅ Empty value filtering - Clean input

#### **C. SQL Query Result Example**

**User selects**: Reference + Textbook

**Generated SQL**:
```sql
WHERE i.coll_type_id IN (?, ?)  -- Prepared statement placeholders
-- Bound values: [1, 2]
```

**Result**:
```
| Year | Month     | Collection Type | Loan Count |
|------|-----------|-----------------|------------|
| 2025 | October   | Reference       | 125        |
| 2025 | October   | Textbook        | 89         |
| 2025 | September | Reference       | 143        |
| 2025 | September | Textbook        | 95         |
```

**Use Cases**:
- Compare circulation between reference and textbooks
- Multi-format analysis (E-Book + Printed + Audio Visual)
- Focus on specific collection categories

---

### **5. CSS Styling Issues with Admin Template**

**🔴 SYMPTOMS:**
```
Custom CSS causing pagination design problems
Table styling looks different from other admin reports
Alternating row colors not working
Print function displays incorrectly
```

**🔍 ROOT CAUSES:**
- Using custom CSS file instead of admin template classes
- Not following SLiMS standard table structure
- Missing alternating row color classes
- Not using printed_page_tpl.php template

**✅ SOLUTIONS:**

#### **A. Use Admin Template Pattern (from rekap-plus-lokasi)**

```php
// REPORT VIEW MODE
ob_start(); // Start output buffering untuk template

// Action bar dengan standard SLiMS styling
echo '<div class="mb-2">';
echo __('Report Title') . ' | ';
echo __('Total Records') . ': <strong>' . number_format($total_records) . '</strong> | ';
echo __('Period') . ': <strong>' . htmlspecialchars($period_label) . '</strong>';
echo ' <a href="#" class="s-btn btn btn-default printReport" onclick="window.print()">' . __('Print Current Page') . '</a>';
echo ' <a href="' . $export_url . '" class="s-btn btn btn-default" target="_blank">' . __('Export to CSV') . '</a>';
echo '</div>';

// Table dengan SLiMS standard classes
echo '<table class="s-table table table-sm table-bordered mb-0">';
echo '<thead>';
echo '<tr>';
echo '<th>' . __('Year') . '</th>';
echo '<th>' . __('Month') . '</th>';
echo '<th>' . __('Collection Type') . '</th>';
echo '<th class="text-center">' . __('Loan Count') . '</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

$rowIndex = 0;
while ($row = $data_result->fetch_assoc()) {
    // Alternating row colors - CRITICAL untuk readability
    $rowClass = ($rowIndex % 2) ? 'alterCellPrinted2' : 'alterCellPrinted';
    
    echo '<tr>';
    echo '<td class="' . $rowClass . '">' . htmlspecialchars($row['LoanYear']) . '</td>';
    echo '<td class="' . $rowClass . '">' . htmlspecialchars($row['LoanMonth']) . '</td>';
    echo '<td class="' . $rowClass . '">' . htmlspecialchars($row['CollectionType']) . '</td>';
    echo '<td class="' . $rowClass . ' text-center">' . number_format($row['LoanCount']) . '</td>';
    echo '</tr>';
    
    $rowIndex++;
}

echo '</tbody>';
echo '</table>';

// Load admin template untuk proper styling
$content = ob_get_clean();
require SB . 'admin/' . $sysconf['admin_template']['dir'] . '/printed_page_tpl.php';
```

#### **B. Standard SLiMS CSS Classes**

| Component | Class | Description |
|-----------|-------|-------------|
| **Table** | `s-table table table-sm table-bordered mb-0` | Standard table styling |
| **Row (Even)** | `alterCellPrinted` | Light background |
| **Row (Odd)** | `alterCellPrinted2` | Darker background |
| **Button** | `s-btn btn btn-default` | Standard button |
| **Print Button** | `printReport` | Print functionality |
| **Text Center** | `text-center` | Center alignment |
| **Error Box** | `errorBox` | Error/info message |

#### **C. Benefits of Using Admin Template**

- ✅ **Consistent Design**: Looks exactly like other SLiMS reports
- ✅ **No Custom CSS**: One less file to maintain
- ✅ **Better Performance**: Uses cached admin CSS
- ✅ **Print Function**: Works correctly with template
- ✅ **Responsive**: Automatically responsive
- ✅ **Alternating Colors**: Built-in readability

---

### **6. Common Bugs & Quick Fixes**

#### **A. Duplicate Export Button**

**Problem**: Button "Export CSV" appears twice

**Cause**: Code duplication during editing

**Fix**:
```php
// ❌ WRONG - Duplicated code
echo '</div>';
echo "\n";
echo ' <a href="..." class="s-btn btn btn-default">Export CSV</a>';  // DUPLICATE
echo '</div>';

// ✅ CORRECT - Single instance
echo '</div>';
echo "\n";
// Remove duplicate
```

#### **B. Column Mismatch Error**

**Problem**: `Notice: Undefined index: received_date`

**Cause**: HTML table columns don't match SQL query results

**Fix**:
```php
// ❌ WRONG - Columns don't exist in query
echo '<th>' . __('Accession Date') . '</th>';  // No 'received_date' in query
echo '<td>' . $row['received_date'] . '</td>';  // ERROR!

// ✅ CORRECT - Match query columns
// SQL: SELECT YEAR(loan_date) AS LoanYear, ...
echo '<th>' . __('Year') . '</th>';
echo '<td>' . $row['LoanYear'] . '</td>';  // Matches query
```

#### **C. Unnecessary Column Removal**

**Problem**: "No" column adds no value

**Solution**: Remove numbering column for cleaner interface
```php
// ❌ Before - With No column
echo '<th>' . __('No') . '</th>';
$no = $offset + 1;
echo '<td>' . $no++ . '</td>';

// ✅ After - Removed
// Just show data columns
echo '<th>' . __('Year') . '</th>';
```

---

### **7. Database Connection Issues**

**🔴 SYMPTOMS:**
```
Fatal error: Call to a member function query() on null
Warning: mysqli_query() expects parameter 1 to be mysqli
Access denied for user 'slims'@'localhost'
```

**🔍 ROOT CAUSES:**
- Missing INDEX_AUTH definition
- Wrong session initialization order
- Database connection object not available
- Incorrect file inclusion sequence

**✅ SOLUTIONS:**

#### **A. Proper Plugin Authentication & Database Access**

```php
// File: admin_interface.php atau index.php
<?php
/**
 * Plugin Interface File
 * CRITICAL: Define INDEX_AUTH first before any includes
 */

// Authentication key (MANDATORY for admin interface)
define('INDEX_AUTH', '1');

// Load session and authentication
require_once SB . 'admin/default/session.inc.php';
require_once SB . 'admin/default/session_check.inc.php';

// Now global $dbs is available
global $dbs;

// Check user privilege
$can_read = utility::havePrivilege('reporting', 'r');
if (!$can_read) {
    die('<div class="errorBox">' . __('You don\'t have enough privileges!') . '</div>');
}

// Safe to use database now
$sql = "SELECT * FROM loan WHERE loan_date >= ?";
$stmt = $dbs->prepare($sql);
$stmt->bind_param('s', $start_date);
$stmt->execute();
$result = $stmt->get_result();
```

#### **B. Safe Database Query Pattern with Error Handling**

```php
// ✅ CORRECT - With comprehensive error handling
function safeQuery($sql, $params = [], $types = '') {
    global $dbs;
    
    // Check database connection
    if (!$dbs) {
        error_log('Database connection not available');
        die('<div class="errorBox">Database connection error</div>');
    }
    
    // Prepare statement
    $stmt = $dbs->prepare($sql);
    if (!$stmt) {
        error_log('SQL Prepare Error: ' . $dbs->error);
        die('<div class="errorBox">Query preparation failed</div>');
    }
    
    // Bind parameters if provided
    if (!empty($params) && !empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    
    // Execute query
    if (!$stmt->execute()) {
        error_log('SQL Execute Error: ' . $stmt->error);
        $stmt->close();
        return false;
    }
    
    $result = $stmt->get_result();
    return $result;
}

// Usage
$result = safeQuery(
    "SELECT * FROM loan WHERE loan_date BETWEEN ? AND ?",
    [$start_date, $end_date],
    'ss'
);
```

#### **C. Correct Include Order**

```php
// ✅ CORRECT - Proper sequence
<?php
// 1. Define authentication
define('INDEX_AUTH', '1');

// 2. Session and authentication
require_once SB . 'admin/default/session.inc.php';
require_once SB . 'admin/default/session_check.inc.php';

// 3. Database is now available via global $dbs
global $dbs;

// 4. Check privileges
$can_read = utility::havePrivilege('reporting', 'r');

// 5. Your plugin code here
```

---

### **8. CSS & Assets Loading Issues**

**🔴 SYMPTOMS:**
```
Custom CSS file not loading
404 Not Found for CSS/JS files
Styles not applied to plugin interface
Console errors: "Failed to load resource"
```

**🔍 ROOT CAUSES:**
- Wrong path to CSS/JS files
- Incorrect use of relative paths
- Not using SLiMS constants for URLs
- Missing plugin folder in path

**✅ SOLUTIONS:**

#### **A. Correct CSS Loading Pattern**

```php
<?php
/**
 * Plugin with Custom CSS
 * File: my_plugin.plugin.php
 */
use SLiMS\Plugins;
$plugins = Plugins::getInstance();

// Register hook to load CSS in admin area
$plugins->register(Plugins::ADMIN_HEADER, function() {
    // ✅ CORRECT - Using SWB constant for web base path
    echo '<link rel="stylesheet" href="' . SWB . 'plugins/my-plugin/assets/style.css">';
    echo '<script src="' . SWB . 'plugins/my-plugin/assets/script.js"></script>';
});

// Register menu
$plugins->registerMenu('reporting', 'My Plugin', __DIR__ . '/admin_interface.php');
```

**Common Path Constants**:
```php
SB    // Server Base Path - /var/www/html/slims/s951dev/
SWB   // Server Web Base - https://domain.com/slims/s951dev/
AWB   // Admin Web Base - https://domain.com/slims/s951dev/admin/
```

#### **B. Asset Loading Methods Comparison**

**Method 1: Using Hook (Recommended for Global Loading)**
```php
// File: my_plugin.plugin.php
$plugins->register(Plugins::ADMIN_HEADER, function() {
    echo '<link rel="stylesheet" href="' . SWB . 'plugins/my-plugin/assets/custom.css">';
});
```
✅ **Pros**: Loads on all admin pages, automatic
❌ **Cons**: May load unnecessarily on other pages

**Method 2: Direct Include in Interface File (Recommended for Plugin-Only)**
```php
// File: admin_interface.php
define('INDEX_AUTH', '1');
require_once SB . 'admin/default/session.inc.php';
require_once SB . 'admin/default/session_check.inc.php';

// Load custom CSS only for this plugin
echo '<link rel="stylesheet" href="' . SWB . 'plugins/my-plugin/assets/custom.css">';

// Your plugin content
```
✅ **Pros**: Only loads when plugin is accessed, better performance
✅ **Cons**: None

**Method 3: Inline Styles (Use Sparingly)**
```php
echo '<style>
.my-plugin-table { border: 1px solid #ccc; }
.my-plugin-header { background: #f0f0f0; }
</style>';
```
✅ **Pros**: No external file needed, quick for small styles
❌ **Cons**: Not cacheable, harder to maintain

#### **C. Common CSS Loading Mistakes**

**❌ WRONG - Relative Path from Plugin File**
```php
// This will NOT work - wrong base path
echo '<link rel="stylesheet" href="assets/style.css">';
echo '<link rel="stylesheet" href="./assets/style.css">';
echo '<link rel="stylesheet" href="../plugins/my-plugin/assets/style.css">';
```

**❌ WRONG - Hardcoded Domain**
```php
// Bad practice - breaks on different installations
echo '<link rel="stylesheet" href="https://mydomain.com/slims/plugins/my-plugin/assets/style.css">';
```

**❌ WRONG - Using Server Path Instead of Web Path**
```php
// Server path won't work in browser
echo '<link rel="stylesheet" href="' . SB . 'plugins/my-plugin/assets/style.css">';
// Result: file:///var/www/html/slims/plugins/... (WRONG!)
```

**✅ CORRECT - Using SWB Constant**
```php
// Always use SWB for web-accessible resources
echo '<link rel="stylesheet" href="' . SWB . 'plugins/my-plugin/assets/style.css">';
// Result: https://domain.com/slims/s951dev/plugins/my-plugin/assets/style.css (CORRECT!)
```

#### **D. Complete Asset Loading Example**

**File Structure**:
```
/plugins/my-plugin/
├── my_plugin.plugin.php    # Plugin registration
├── admin_interface.php     # Interface file
└── assets/
    ├── css/
    │   └── custom.css      # Custom styles
    ├── js/
    │   └── custom.js       # Custom scripts
    └── images/
        └── icon.png        # Images
```

**Plugin Registration (my_plugin.plugin.php)**:
```php
<?php
/**
 * Plugin Name: My Plugin with Assets
 * Plugin URI: -
 * Description: Example plugin with custom CSS and JS
 * Version: 1.0.0
 * Author: Developer
 */
use SLiMS\Plugins;
$plugins = Plugins::getInstance();

// Register menu
$plugins->registerMenu('reporting', 'My Plugin', __DIR__ . '/admin_interface.php');
```

**Interface File (admin_interface.php)**:
```php
<?php
define('INDEX_AUTH', '1');
require_once SB . 'admin/default/session.inc.php';
require_once SB . 'admin/default/session_check.inc.php';

// Check privileges
$can_read = utility::havePrivilege('reporting', 'r');
if (!$can_read) {
    die('<div class="errorBox">No privileges</div>');
}

// Load custom assets (AFTER authentication)
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Plugin</title>
    <!-- Load custom CSS -->
    <link rel="stylesheet" href="<?php echo SWB; ?>plugins/my-plugin/assets/css/custom.css">
</head>
<body>
    <div class="menuBox">
        <div class="menuBoxInner reportIcon">
            <h2>My Plugin with Custom Styles</h2>
            <div class="my-custom-class">
                Content with custom styling
            </div>
        </div>
    </div>
    
    <!-- Load custom JS at bottom -->
    <script src="<?php echo SWB; ?>plugins/my-plugin/assets/js/custom.js"></script>
</body>
</html>
```

**Custom CSS (assets/css/custom.css)**:
```css
/* Custom plugin styles */
.my-custom-class {
    background-color: #f5f5f5;
    padding: 20px;
    border-radius: 5px;
    margin: 10px 0;
}

.my-plugin-table {
    border-collapse: collapse;
    width: 100%;
}

.my-plugin-table th {
    background-color: #4CAF50;
    color: white;
    padding: 12px;
}

.my-plugin-table td {
    padding: 10px;
    border: 1px solid #ddd;
}
```

#### **E. Asset Loading in Iframe Mode**

```php
<?php
$reportView = isset($_GET['reportView']);

if (!$reportView): 
    // PARENT MODE - Load CSS here
?>
<link rel="stylesheet" href="<?php echo SWB; ?>plugins/my-plugin/assets/css/form.css">

<form target="reportView" method="get">
    <!-- Form with custom styles -->
</form>

<iframe name="reportView" src="..."></iframe>

<?php exit; endif; ?>

<?php
// IFRAME MODE - Load different CSS if needed
ob_start();
echo '<link rel="stylesheet" href="' . SWB . 'plugins/my-plugin/assets/css/report.css">';
echo '<div class="report-content">...</div>';
$content = ob_get_clean();
require SB . 'admin/' . $sysconf['admin_template']['dir'] . '/printed_page_tpl.php';
```

#### **F. Debugging Asset Loading**

```php
// Debug helper to verify paths
if (isset($_GET['debug_assets'])) {
    echo '<div class="alert alert-info">';
    echo '<strong>Asset Path Debugging:</strong><br>';
    echo 'SB (Server Base): ' . SB . '<br>';
    echo 'SWB (Server Web Base): ' . SWB . '<br>';
    echo 'AWB (Admin Web Base): ' . AWB . '<br>';
    echo 'Plugin Directory: ' . __DIR__ . '<br>';
    echo 'CSS URL: ' . SWB . 'plugins/my-plugin/assets/css/custom.css<br>';
    echo '</div>';
}
```

**Browser Console Check**:
```javascript
// Check in browser console if CSS loaded
console.log('CSS loaded:', document.styleSheets.length);
for(let sheet of document.styleSheets) {
    console.log(sheet.href);
}
```

#### **G. Best Practices for Assets**

1. **Use Admin Template Classes First**
   ```php
   // ✅ Better: Use existing classes
   <table class="s-table table table-sm table-bordered">
   
   // ❌ Avoid: Creating duplicate styles
   <table class="my-custom-table">  // Only if really needed
   ```

2. **Minimize Custom CSS**
   - Use SLiMS admin template classes when possible
   - Only add CSS for truly unique elements
   - Keep CSS file small and organized

3. **Asset Organization**
   ```
   assets/
   ├── css/
   │   ├── admin.css      # For admin interface
   │   └── report.css     # For report view
   ├── js/
   │   └── functions.js
   └── images/
       └── icons/
   ```

4. **Cache Busting for Updates**
   ```php
   // Add version parameter to force reload after updates
   $version = '1.0.1';
   echo '<link rel="stylesheet" href="' . SWB . 'plugins/my-plugin/assets/css/custom.css?v=' . $version . '">';
   ```

5. **Conditional Loading**
   ```php
   // Only load heavy assets when needed
   if (isset($_GET['reportView'])) {
       echo '<script src="' . SWB . 'plugins/my-plugin/assets/js/chart.js"></script>';
   }
   ```

---

### **9. Permission & Access Control Best Practices**

// Now $dbs global object is available
global $dbs;
$sql = "SELECT * FROM my_table";
$result = $dbs->query($sql);
```

#### **B. Database Credential Verification**
```php
// Check configuration files:
// 1. sysconfig.local.inc.php
$database_password = 'correct_password_here';

// 2. config/config.php  
$database_password = 'same_password_here';

// 3. Verify database user exists
// mysql -u slims -p'password' -e "SHOW DATABASES;"
```

#### **C. Safe Database Query Pattern**
```php
// ✅ CORRECT - With error handling
function safeQuery($sql, $params = []) {
    global $dbs;
    
    if (!$dbs) {
        die('<div class="errorBox">Database connection not available</div>');
    }
    
    // Escape parameters
    foreach ($params as $key => $value) {
        $params[$key] = $dbs->escape_string($value);
    }
    
    $result = $dbs->query($sql);
    if (!$result) {
        error_log("SQL Error: " . $dbs->error);
        return false;
    }
    
    return $result;
}
```

---

### **5. Permission & Access Control Errors**

**🔴 SYMPTOMS:**
```
You don't have enough privileges to access this area!
Access denied
Call to undefined function utility::havePrivilege()
```

**🔍 ROOT CAUSES:**
- Missing privilege checks
- Wrong session initialization order
- Incorrect permission module names
- Missing utility functions

**✅ SOLUTIONS:**

#### **A. Proper Privilege Checking**
```php
// ✅ CORRECT - Complete privilege setup
<?php
// key to authenticate as admin (INDEX_AUTH is the correct constant)
define('INDEX_AUTH', '1');

// start the session FIRST
require SB.'admin/default/session.inc.php';
require SB.'admin/default/session_check.inc.php';

// THEN check privileges
$can_read = utility::havePrivilege('reporting', 'r');
$can_write = utility::havePrivilege('reporting', 'w');

if (!$can_read) {
    die('<div class="errorBox">'.__('You don\'t have enough privileges to access this area!').'</div>');
}

// Your plugin code here...
```

#### **B. Common Permission Modules**
```php
// Available permission modules in SLiMS:
'bibliography'   // Bibliography management
'membership'     // Member management  
'circulation'    // Loan/return transactions
'master_file'    // Master data management
'reporting'      // Reports and statistics
'system'         // System administration
'serial_control' // Serial/periodical control
'stock_take'     // Stock taking
```

#### **C. Multi-level Permission Checking**
```php
// ✅ Advanced permission pattern
function checkPluginAccess($module, $action = 'r') {
    // Basic login check
    if (!isset($_SESSION['uid']) || empty($_SESSION['uid'])) {
        die('<div class="errorBox">Please login first</div>');
    }
    
    // Permission check
    $has_permission = utility::havePrivilege($module, $action);
    if (!$has_permission) {
        die('<div class="errorBox">Insufficient privileges for ' . $module . '</div>');
    }
    
    return true;
}

// Usage
checkPluginAccess('reporting', 'r');  // Read access
checkPluginAccess('membership', 'w'); // Write access
```

---

### **6. File Path & Include Errors**

**🔴 SYMPTOMS:**
```
Warning: require(../../../../sysconfig.inc.php): failed to open stream
Fatal error: require(): Failed opening required file
No such file or directory
```

**🔍 ROOT CAUSES:**
- Hardcoded relative paths
- Wrong directory structure assumptions
- Missing SLiMS constants
- Incorrect file inclusion order

**✅ SOLUTIONS:**

#### **A. Use SLiMS Constants (Recommended)**
```php
// ❌ WRONG - Hardcoded relative paths
require '../../../../sysconfig.inc.php';
require '../../../lib/simbio_DB/simbio_dbop.inc.php';

// ✅ CORRECT - Use SLiMS constants
require SB.'admin/default/session.inc.php';
require SIMBIO.'simbio_DB/simbio_dbop.inc.php';
require LIB.'utility.inc.php';
```

#### **B. Proper Include Order**
```php
// ✅ CORRECT - Proper sequence
<?php
// 1. Admin authentication (INDEX_AUTH is the correct constant)
define('INDEX_AUTH', '1');

// 2. Session and authentication
require SB.'admin/default/session.inc.php';
require SB.'admin/default/session_check.inc.php';

// 3. Required libraries
require SIMBIO.'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO.'simbio_DB/simbio_dbop.inc.php';

// 4. Plugin-specific includes
// Your plugin code here...
```

#### **C. Safe File Inclusion Pattern**
```php
// ✅ CORRECT - With error handling
function safeInclude($file_path) {
    if (!file_exists($file_path)) {
        die('<div class="errorBox">Required file not found: ' . basename($file_path) . '</div>');
    }
    require_once $file_path;
}

// Usage
safeInclude(SB.'admin/default/session.inc.php');
safeInclude(SIMBIO.'simbio_DB/simbio_dbop.inc.php');
```

---

## 🛠️ **Plugin Development Best Practices**

### **📚 IMPORTANT: Pattern Differences**

Berdasarkan dokumentasi resmi SLiMS dan implementasi di lapangan, ada **2 pattern plugin** yang digunakan:

#### **🔍 Pattern 1: SLiMS Official Documentation**
- File plugin berekstensi `*.plugin.php` (contoh: `my_plugin.plugin.php`)
- Variable instance: `$plugins = Plugins::getInstance();`
- Register langsung ke file interface: `$plugins->registerMenu('reporting', 'Menu', __DIR__ . '/interface.php');`
- Authentication dalam file interface: `define('INDEX_AUTH', '1');`

#### **🔍 Pattern 2: Production Implementation** 
- File plugin bernama `registrat.plugin.php` (digunakan di banyak plugin production)
- Variable instance: `$plugin = Plugins::getInstance();`
- Register ke routing file: `$plugin->registerMenu('reporting', 'Menu', __DIR__ . '/admin.routes.php');`
- Authentication dalam routing: `define('INDEX_AUTH', '1');` (sama dengan Pattern 1)

**📝 RECOMMENDATION:** Gunakan **Pattern 1** sesuai dokumentasi resmi untuk plugin baru, tapi **Pattern 2** juga masih berfungsi untuk compatibility.

---

### **1. Plugin Structure Templates**

#### **A. Simple Plugin (Pattern 1 - Official)**
```
/plugins/
└── my_plugin.plugin.php    # Single file plugin
```

#### **B. Complex Plugin (Pattern 1 - Official)**
```
/plugins/my-plugin/
├── my_plugin.plugin.php    # Plugin registration
├── admin_interface.php     # Admin interface
├── opac_interface.php      # OPAC interface (optional)
└── assets/                 # CSS, JS, images
    ├── style.css
    └── script.js
```

#### **C. Production Plugin (Pattern 2 - Legacy)**
```
/plugins/my-plugin/
├── registrat.plugin.php    # Plugin registration (legacy naming)
├── admin.routes.php        # Admin routing handler
├── index.php              # Main interface
└── assets/
```

#### **B. Template Plugin File (*.plugin.php)**
```php
<?php
/**
 * Plugin Name: My Plugin Name
 * Plugin URI: -
 * Description: Plugin description here
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://your.website
 */

use SLiMS\Plugins;

// get plugin instance (sesuai dokumentasi SLiMS)
$plugins = Plugins::getInstance();

// registering menus - untuk admin (gunakan kategori yang VALID!)
// Valid categories: bibliography, circulation, membership, master_file,
//                   reporting, serial_control, stock_take, system
$plugins->registerMenu('reporting', 'My Report Plugin', __DIR__ . '/admin_interface.php');

// registering menus - untuk OPAC (optional)
$plugins->registerMenu('opac', 'My OPAC Feature', __DIR__ . '/opac_interface.php');

// registering hooks (optional)
$plugins->register('bibliography_before_save', function($data){
    // Hook implementation
});
```

#### **C. Admin Interface File Template**
```php
<?php
/**
 * Admin Interface File
 * File: admin_interface.php
 */

// Authentication dan session (sesuai dokumentasi SLiMS)
define('INDEX_AUTH', '1');
require_once SB.'admin/default/session.inc.php';
require_once SB.'admin/default/session_check.inc.php';

// Privilege checking
$can_read = utility::havePrivilege('reporting', 'r');
if (!$can_read) {
    die('<div class="errorBox">'.__('You don\'t have enough privileges to access this area!').'</div>');
}

// Plugin implementation
// Your plugin code here...
```

#### **D. Complete Plugin Interface Template**
```php
<?php
/**
 * Complete Plugin Interface
 * File: admin_interface.php atau opac_interface.php
 */

// Authentication (sesuai dokumentasi SLiMS)
define('INDEX_AUTH', '1');
require_once SB.'admin/default/session.inc.php';
require_once SB.'admin/default/session_check.inc.php';

// Include required libraries
require_once SIMBIO.'simbio_GUI/table/simbio_table.inc.php';
require_once SIMBIO.'simbio_DB/simbio_dbop.inc.php';

// Privilege checking
$can_read = utility::havePrivilege('reporting', 'r');
$can_write = utility::havePrivilege('reporting', 'w');

if (!$can_read) {
    die('<div class="errorBox">'.__('You don\'t have enough privileges to access this area!').'</div>');
}

// Include header
include SB . 'admin/default/header.php';
?>

<div class="menuBox">
    <div class="menuBoxInner masterFileIcon">
        <div class="per_title">
            <h2><?php echo __('My Plugin Interface'); ?></h2>
        </div>
        <div class="sub_section">
            <!-- Plugin content here -->
            <p>Welcome to my plugin!</p>
            
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="text" name="search" class="form-control" placeholder="Search..." />
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
    </div>
</div>

<?php
include SB . 'admin/default/footer.php';
```

---

### **2. Debugging & Troubleshooting Tools**

#### **A. Plugin Debug Mode**
```php
// Add to beginning of plugin files for debugging
if (isset($_GET['debug']) && $_GET['debug'] == '1') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    echo '<pre>DEBUG INFO:';
    echo 'GET: ' . print_r($_GET, true);
    echo 'POST: ' . print_r($_POST, true);
    echo 'SESSION: ' . print_r($_SESSION, true);
    echo 'Plugin Directory: ' . __DIR__;
    echo 'Current File: ' . __FILE__;
    echo '</pre>';
}

// Access with: plugin_container.php?mod=myplugin&id=xxx&sec=/&debug=1
```

#### **B. Database Query Debugging**
```php
function debugQuery($sql, $params = []) {
    if (isset($_GET['debug'])) {
        echo '<div class="alert alert-info">';
        echo '<strong>SQL Query:</strong><br>';
        echo htmlspecialchars($sql) . '<br>';
        if (!empty($params)) {
            echo '<strong>Parameters:</strong><br>';
            print_r($params);
        }
        echo '</div>';
    }
}

// Usage
$sql = "SELECT * FROM my_table WHERE column = ?";
debugQuery($sql, [$param]);
```

#### **C. Plugin Context Validator**
```php
function validatePluginContext() {
    $required_params = ['mod', 'id'];
    $missing_params = [];
    
    foreach ($required_params as $param) {
        if (!isset($_GET[$param]) || empty($_GET[$param])) {
            $missing_params[] = $param;
        }
    }
    
    if (!empty($missing_params)) {
        die('<div class="errorBox">Invalid plugin context. Missing parameters: ' . 
            implode(', ', $missing_params) . '</div>');
    }
    
    return true;
}

// Call at beginning of plugin
validatePluginContext();
```

---

### **3. Common Error Prevention Checklist**

#### **☑️ Before Plugin Development**
- [ ] Understand SLiMS plugin architecture
- [ ] Study working plugin examples
- [ ] Plan plugin structure and dependencies
- [ ] Identify required permissions

#### **☑️ During Development**
- [ ] Use proper file naming (`registrat.plugin.php`)
- [ ] Follow SLiMS coding standards
- [ ] Implement proper error handling
- [ ] Test with different user permissions
- [ ] Validate all user inputs
- [ ] Use SLiMS constants instead of relative paths

#### **☑️ Form & URL Handling**
- [ ] Include hidden fields for plugin context
- [ ] Use proper URL generation patterns
- [ ] Test form submissions thoroughly
- [ ] Implement AJAX error handling
- [ ] Validate plugin routing

#### **☑️ Database Operations**
- [ ] Define `INDEX_AUTH` properly for admin authentication
- [ ] Include required session files
- [ ] Escape all user inputs
- [ ] Implement error handling for queries
- [ ] Test with different data scenarios

#### **☑️ Security Considerations**
- [ ] Implement proper privilege checking
- [ ] Validate user permissions for each action
- [ ] Escape output to prevent XSS
- [ ] Use prepared statements for SQL
- [ ] Log security-related events

#### **☑️ Testing Scenarios**
- [ ] Test as different user types (admin, staff, etc.)
- [ ] Test with empty/invalid inputs
- [ ] Test form submissions and redirects
- [ ] Test file uploads/downloads (if applicable)
- [ ] Test error scenarios and edge cases

---

## 📊 **Troubleshooting Workflow**

### **🔍 Pattern-Specific Troubleshooting**

#### **Pattern 1 Issues (Official Documentation)**
```bash
# Check if plugin file has correct extension
ls -la plugins/*/my_plugin.plugin.php

# Verify variable naming
grep -r "\$plugins.*getInstance" plugins/my-plugin/

# Check direct file registration
grep -r "registerMenu.*\.php" plugins/my-plugin/
```

#### **Pattern 2 Issues (Production Legacy)**
```bash
# Check for registrat.plugin.php file
find plugins/ -name "registrat.plugin.php"

# Verify routing files exist
find plugins/ -name "admin.routes.php" -o -name "*.routes.php"

# Check plugin container access
curl -I "https://domain.com/admin/plugin_container.php?mod=myplugin&id=xxx&sec=/"
```

### **Step 1: Identify Error Type & Pattern**
```bash
# Check error logs
tail -f /var/log/nginx/error.log
tail -f /var/log/php7.4-fpm.log

# Identify plugin pattern being used
find plugins/ -name "*.plugin.php" | head -5
find plugins/ -name "registrat.plugin.php" | head -5

# Test plugin accessibility
curl -I "https://domain.com/admin/plugin_container.php?mod=myplugin&id=xxx&sec=/"
```

### **Step 2: Apply Specific Solutions**
- **Plugin not found** → Check `registrat.plugin.php` and structure
- **Form routing issues** → Fix hidden fields and URL generation
- **Database errors** → Verify credentials and session setup
- **Permission errors** → Check privilege definitions
- **File path errors** → Use SLiMS constants

### **Step 3: Test & Validate**
- Test basic plugin loading
- Test form submissions
- Test with different users
- Test error scenarios
- Monitor error logs

### **Step 4: Documentation**
- Document plugin structure
- Create user manual
- Document troubleshooting steps
- Create deployment guide

---

## 🧪 **Plugin Testing & Validation**

### **Automated Plugin Validator Script**

```bash
#!/bin/bash
# File: validate_plugin.sh
# Usage: ./validate_plugin.sh plugin_name

PLUGIN_NAME="$1"
PLUGIN_PATH="/var/www/html/slims/s951dev/plugins/${PLUGIN_NAME}"
ERRORS=0

echo "=== SLiMS PLUGIN VALIDATOR ==="
echo "Plugin: $PLUGIN_NAME"
echo "================================"

# Test 1: Directory structure
echo "1. Directory Structure:"
if [ -d "$PLUGIN_PATH" ]; then
    echo "   ✓ Plugin directory exists"
else
    echo "   ❌ Plugin directory missing"
    ((ERRORS++))
fi

# Test 2: Registration files
echo "2. Registration Files:"
PATTERN_1_FILE=$(find "$PLUGIN_PATH" -name "*.plugin.php" -not -name "registrat.plugin.php" | head -1)
PATTERN_2_FILE="$PLUGIN_PATH/registrat.plugin.php"

if [ -n "$PATTERN_1_FILE" ]; then
    echo "   ✓ Pattern 1 file found: $(basename "$PATTERN_1_FILE")"
    php -l "$PATTERN_1_FILE" >/dev/null 2>&1 || { echo "   ❌ Syntax error in Pattern 1 file"; ((ERRORS++)); }
fi

if [ -f "$PATTERN_2_FILE" ]; then
    echo "   ✓ Pattern 2 file found: registrat.plugin.php"
    php -l "$PATTERN_2_FILE" >/dev/null 2>&1 || { echo "   ❌ Syntax error in Pattern 2 file"; ((ERRORS++)); }
fi

if [ -z "$PATTERN_1_FILE" ] && [ ! -f "$PATTERN_2_FILE" ]; then
    echo "   ❌ No valid registration file found"
    ((ERRORS++))
fi

# Test 3: Interface files
echo "3. Interface Files:"
if [ -f "$PLUGIN_PATH/admin_simple.php" ]; then
    echo "   ✓ admin_simple.php found"
    php -l "$PLUGIN_PATH/admin_simple.php" >/dev/null 2>&1 || { echo "   ❌ Syntax error in admin_simple.php"; ((ERRORS++)); }
elif [ -f "$PLUGIN_PATH/index.php" ]; then
    echo "   ✓ index.php found"
    php -l "$PLUGIN_PATH/index.php" >/dev/null 2>&1 || { echo "   ❌ Syntax error in index.php"; ((ERRORS++)); }
else
    echo "   ⚠️  No interface file found (admin_simple.php or index.php)"
fi

# Test 4: Permissions
echo "4. File Permissions:"
PERMS=$(stat -c "%a" "$PLUGIN_PATH" 2>/dev/null)
if [ "$PERMS" = "755" ] || [ "$PERMS" = "775" ]; then
    echo "   ✓ Directory permissions correct ($PERMS)"
else
    echo "   ⚠️  Directory permissions: $PERMS (recommended: 755)"
fi

# Summary
echo "================================"
if [ $ERRORS -eq 0 ]; then
    echo "✅ VALIDATION PASSED - Plugin looks good!"
else
    echo "❌ VALIDATION FAILED - $ERRORS error(s) found"
    echo "Review the issues above and run validation again."
fi
echo "================================"

exit $ERRORS
```

### **Quick Plugin Health Check**

```bash
# One-liner health check
cd /var/www/html/slims/s951dev/plugins/ && \
for plugin in */; do \
    echo "=== $plugin ==="; \
    [ -f "$plugin"*.plugin.php ] && echo "✓ Pattern 1" || echo "✗ No Pattern 1"; \
    [ -f "$plugin"registrat.plugin.php ] && echo "✓ Pattern 2" || echo "✗ No Pattern 2"; \
    [ -f "$plugin"admin_simple.php ] && echo "✓ Interface" || echo "✗ No Interface"; \
done
```

---

## 🎯 **Summary**

Plugin errors dalam SLiMS umumnya disebabkan oleh:

1. **Structural Issues** - File naming, directory structure
2. **Context Management** - Plugin parameters tidak terpelihara
3. **Session & Authentication** - Include order yang salah
4. **Database Access** - Credentials atau connection issues  
5. **Permission System** - Privilege checking yang tidak proper

**Key Success Factors:**
- **Follow Official Documentation** - Use Pattern 1 (*.plugin.php) as primary approach
- **Maintain Backward Compatibility** - Support Pattern 2 (registrat.plugin.php) when needed
- **Context Preservation** - Always maintain plugin parameters in forms and links
- **Proper Authentication** - Use INDEX_AUTH and session management correctly
- **Test Both Patterns** - Validate your plugin works with both registration approaches
- **Error Handling** - Implement comprehensive error checking and user feedback

**Pattern Priority:**
1. **Primary**: Pattern 1 (Official Documentation) - `my_plugin.plugin.php`
2. **Secondary**: Pattern 2 (Production Legacy) - `registrat.plugin.php`  
3. **Hybrid**: Both patterns for maximum compatibility

**Emergency Plugin Checklist:**
- [ ] Plugin directory exists with correct permissions (755)
- [ ] Registration file exists (*.plugin.php or registrat.plugin.php)
- [ ] Interface file exists (admin_simple.php or index.php)
- [ ] No PHP syntax errors in any file
- [ ] Plugin accessible via plugin_container.php
- [ ] Form contexts properly maintained
- [ ] Database connections working
- [ ] Authentication properly implemented

Dengan mengikuti panduan ini dan menggunakan script validator, 95% plugin errors dapat dihindari dan diperbaiki dengan efektif! 💪

---

## 📞 **Support Resources**

- **Official SLiMS Documentation**: `/SLiMS-9-Documentation/userguide/development-guide/`
- **Plugin Examples**: Check existing working plugins in `/plugins/` directory
- **Community**: SLiMS Community Forum and GitHub Issues
- **Testing Tools**: Use provided validation scripts for quick diagnosis
