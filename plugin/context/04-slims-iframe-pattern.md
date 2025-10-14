# SLiMS Plugin UI Patterns - Iframe & Admin Template

> **Context File untuk AI Chatbot**  
> **Purpose**: UI patterns untuk report plugins dan styling  
> **Common Use Case**: Report plugins dengan filter form

---

## 🎯 **Problem Statement**

### **The Admin Menu Disappearing Problem**

Saat membuat plugin report dengan form filter:

```php
// ❌ PROBLEM: Form submit tanpa target
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="date" name="start_date">
    <button type="submit">Filter</button>
</form>
```

**Hasil**: Setelah submit, halaman reload dan **admin menu hilang!**

**Penyebab**: Form submit membuka halaman tanpa plugin context (`mod`, `id`, `sec`)

---

## ✅ **Solution: Iframe Pattern**

### **Konsep Dual-Mode**

```
┌──────────────────────────────────────┐
│  PARENT MODE                         │
│  ┌────────────────────────────────┐  │
│  │ Filter Form                    │  │
│  │ [Start Date] [End Date] [Go]   │  │
│  └────────────────────────────────┘  │
│  ┌────────────────────────────────┐  │
│  │ <iframe name="reportView">     │  │
│  │   ┌──────────────────────────┐ │  │
│  │   │ IFRAME MODE              │ │  │
│  │   │ Report Table Here        │ │  │
│  │   │ [Data rows...]           │ │  │
│  │   └──────────────────────────┘ │  │
│  └────────────────────────────────┘  │
└──────────────────────────────────────┘
```

- **Parent Mode**: Menampilkan form filter + iframe container
- **Iframe Mode**: Menampilkan hasil report di dalam iframe

---

## 📝 **Complete Iframe Pattern Template**

### **File Structure**

```
plugins/my-report-plugin/
├── my_plugin.plugin.php    # Plugin registration
└── report.php              # Main interface (dual-mode)
```

### **Full Implementation**

```php
<?php
/**
 * File: report.php
 * Dual-mode pattern: Parent (form) + Iframe (report)
 */

// ===== SECURITY LAYER =====
if (!defined('INDEX_AUTH')) {
    define('INDEX_AUTH', '1');
}
global $dbs, $sysconf;
require SB . 'admin/default/session.inc.php';
require SB . 'admin/default/session_check.inc.php';

// Check privileges
$can_read = utility::havePrivilege('reporting', 'r');
if (!$can_read) {
    die('<div class="errorBox">' . __('You do not have permission!') . '</div>');
}

// ===== DETECT MODE =====
$reportView = isset($_GET['reportView']);

if (!$reportView):
    // ╔════════════════════════════════════════════════╗
    // ║           PARENT MODE: Filter Form             ║
    // ╚════════════════════════════════════════════════╝
?>
<div class="menuBox">
    <div class="menuBoxInner reportIcon">
        <div class="per_title">
            <h2><?php echo __('My Custom Report'); ?></h2>
        </div>
        
        <div class="infoBox">
            <?php echo __('Select date range to filter report'); ?>
        </div>
        
        <!-- FILTER FORM -->
        <form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" target="reportView">
            <!-- CRITICAL: Hidden fields to maintain plugin context -->
            <input type="hidden" name="mod" value="<?= $_GET['mod'] ?? '' ?>" />
            <input type="hidden" name="id" value="<?= $_GET['id'] ?? '' ?>" />
            <input type="hidden" name="sec" value="/" />
            <input type="hidden" name="reportView" value="true" />
            
            <!-- Filter fields -->
            <div class="form-group">
                <label><?php echo __('Start Date'); ?></label>
                <input type="date" name="start_date" class="form-control" 
                       value="<?= $_GET['start_date'] ?? date('Y-m-01') ?>" required>
            </div>
            
            <div class="form-group">
                <label><?php echo __('End Date'); ?></label>
                <input type="date" name="end_date" class="form-control" 
                       value="<?= $_GET['end_date'] ?? date('Y-m-d') ?>" required>
            </div>
            
            <button type="submit" class="s-btn btn btn-primary">
                <?php echo __('Apply Filter'); ?>
            </button>
        </form>
        
        <br>
        
        <!-- IFRAME CONTAINER -->
        <iframe name="reportView" id="reportView" 
                src="<?php 
                    // Build initial iframe URL with default dates
                    $params = [
                        'mod' => $_GET['mod'] ?? '',
                        'id' => $_GET['id'] ?? '',
                        'sec' => '/',
                        'reportView' => 'true',
                        'start_date' => $_GET['start_date'] ?? date('Y-m-01'),
                        'end_date' => $_GET['end_date'] ?? date('Y-m-d')
                    ];
                    echo $_SERVER['PHP_SELF'] . '?' . http_build_query($params);
                ?>"
                style="width:100%; height:550px; border:1px solid #ccc;" 
                frameborder="0">
        </iframe>
    </div>
</div>
<?php
    exit; // IMPORTANT: Exit parent mode
endif;

// ╔════════════════════════════════════════════════╗
// ║         IFRAME MODE: Report Display            ║
// ╚════════════════════════════════════════════════╝

// Start output buffering
ob_start();

// Get filter parameters
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// ===== QUERY DATA =====
$sql = <<<SQL
SELECT 
    b.biblio_id,
    b.title,
    b.author,
    b.publisher,
    b.publish_year
FROM biblio b
WHERE b.input_date BETWEEN ? AND ?
ORDER BY b.title
SQL;

$stmt = $dbs->prepare($sql);
$stmt->bind_param('ss', $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

// ===== RENDER TABLE =====
echo '<h3>Report: ' . htmlspecialchars($start_date) . ' to ' . htmlspecialchars($end_date) . '</h3>';

if ($result->num_rows === 0) {
    echo '<div class="errorBox">' . __('No data found for selected date range') . '</div>';
} else {
    echo '<table class="s-table table table-sm table-bordered mb-0">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>No</th>';
    echo '<th>' . __('Biblio ID') . '</th>';
    echo '<th>' . __('Title') . '</th>';
    echo '<th>' . __('Author') . '</th>';
    echo '<th>' . __('Publisher') . '</th>';
    echo '<th>' . __('Year') . '</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    $no = 1;
    $rowIndex = 0;
    while ($row = $result->fetch_assoc()) {
        // Alternating row colors
        $rowClass = ($rowIndex % 2) ? 'alterCellPrinted2' : 'alterCellPrinted';
        
        echo '<tr>';
        echo '<td class="' . $rowClass . '">' . $no++ . '</td>';
        echo '<td class="' . $rowClass . '">' . htmlspecialchars($row['biblio_id']) . '</td>';
        echo '<td class="' . $rowClass . '">' . htmlspecialchars($row['title']) . '</td>';
        echo '<td class="' . $rowClass . '">' . htmlspecialchars($row['author']) . '</td>';
        echo '<td class="' . $rowClass . '">' . htmlspecialchars($row['publisher']) . '</td>';
        echo '<td class="' . $rowClass . '">' . htmlspecialchars($row['publish_year']) . '</td>';
        echo '</tr>';
        
        $rowIndex++;
    }
    
    echo '</tbody>';
    echo '</table>';
    
    echo '<p><strong>Total Records: ' . $result->num_rows . '</strong></p>';
}

// ===== RENDER TO TEMPLATE =====
$content = ob_get_clean();
require SB . 'admin/' . $sysconf['admin_template']['dir'] . '/printed_page_tpl.php';
?>
```

---

## 🔑 **Key Components Explained**

### **1. Mode Detection**

```php
$reportView = isset($_GET['reportView']);

if (!$reportView) {
    // Parent mode: show form
} else {
    // Iframe mode: show report
}
```

### **2. Plugin Context Preservation**

```php
<!-- CRITICAL: These maintain plugin context -->
<input type="hidden" name="mod" value="<?= $_GET['mod'] ?? '' ?>" />
<input type="hidden" name="id" value="<?= $_GET['id'] ?? '' ?>" />
<input type="hidden" name="sec" value="/" />
<input type="hidden" name="reportView" value="true" />
```

**Without these**: Admin menu will disappear after form submit!

### **3. Iframe Target**

```php
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" 
      target="reportView">  <!-- Opens in iframe -->
```

### **4. Initial Iframe Source**

```php
<iframe name="reportView" src="<?php 
    $params = [
        'mod' => $_GET['mod'] ?? '',
        'id' => $_GET['id'] ?? '',
        'reportView' => 'true',
        'start_date' => date('Y-m-01'),
        'end_date' => date('Y-m-d')
    ];
    echo $_SERVER['PHP_SELF'] . '?' . http_build_query($params);
?>">
</iframe>
```

**Purpose**: Load default report saat halaman pertama kali dibuka

### **5. Printed Template**

```php
$content = ob_get_clean();
require SB . 'admin/' . $sysconf['admin_template']['dir'] . '/printed_page_tpl.php';
```

**Purpose**: Render content dalam print-friendly template

---

## 🎨 **Admin Template Classes**

### **Standard Table Styling**

```php
// Base table class
echo '<table class="s-table table table-sm table-bordered mb-0">';

// Table header
echo '<thead>';
echo '<tr>';
echo '<th>' . __('Column Name') . '</th>';
echo '</tr>';
echo '</thead>';

// Table body with alternating rows
echo '<tbody>';
$rowIndex = 0;
while ($row = $result->fetch_assoc()) {
    // Alternate colors
    $rowClass = ($rowIndex % 2) ? 'alterCellPrinted2' : 'alterCellPrinted';
    
    echo '<tr>';
    echo '<td class="' . $rowClass . '">' . htmlspecialchars($row['data']) . '</td>';
    echo '</tr>';
    
    $rowIndex++;
}
echo '</tbody>';
echo '</table>';
```

### **Button Styles**

```php
// Primary action
echo '<button class="s-btn btn btn-primary">Submit</button>';

// Secondary action
echo '<button class="s-btn btn btn-default">Cancel</button>';

// Danger action
echo '<button class="s-btn btn btn-danger">Delete</button>';

// Success action
echo '<button class="s-btn btn btn-success">Approve</button>';
```

### **Form Styles**

```php
echo '<div class="form-group">';
echo '<label>' . __('Field Name') . '</label>';
echo '<input type="text" class="form-control" name="field_name">';
echo '</div>';

echo '<div class="form-group">';
echo '<label>' . __('Select Option') . '</label>';
echo '<select class="form-control" name="option">';
echo '<option value="">-- ' . __('Select') . ' --</option>';
echo '<option value="1">Option 1</option>';
echo '</select>';
echo '</div>';
```

### **Message Boxes**

```php
// Info message
echo '<div class="infoBox">' . __('Information message here') . '</div>';

// Error message
echo '<div class="errorBox">' . __('Error message here') . '</div>';

// Warning message
echo '<div class="warningBox">' . __('Warning message here') . '</div>';

// Success message
echo '<div class="successBox">' . __('Success message here') . '</div>';
```

---

## 🔧 **Common Variations**

### **Export to Excel Button**

```php
// In parent mode
<button type="button" class="s-btn btn btn-success" 
        onclick="exportToExcel()">
    <?php echo __('Export to Excel'); ?>
</button>

<script>
function exportToExcel() {
    var iframe = document.getElementById('reportView');
    var params = new URLSearchParams(iframe.contentWindow.location.search);
    params.set('export', 'excel');
    window.location.href = '<?= $_SERVER['PHP_SELF'] ?>?' + params.toString();
}
</script>

<?php
// In iframe mode, after data generation
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="report_' . date('Y-m-d') . '.xls"');
    // Output table HTML (Excel will parse it)
    exit;
}
?>
```

### **Print Button**

```php
// In parent mode
<button type="button" class="s-btn btn btn-default" 
        onclick="printReport()">
    <?php echo __('Print'); ?>
</button>

<script>
function printReport() {
    var iframe = document.getElementById('reportView');
    iframe.contentWindow.print();
}
</script>
```

### **Multiple Filter Types**

```php
// Dropdown filter
<div class="form-group">
    <label><?php echo __('Category'); ?></label>
    <select name="category" class="form-control">
        <option value="">-- <?php echo __('All Categories'); ?> --</option>
        <?php
        $cat_query = $dbs->query("SELECT DISTINCT category FROM items");
        while ($cat = $cat_query->fetch_assoc()) {
            $selected = ($_GET['category'] ?? '') === $cat['category'] ? 'selected' : '';
            echo '<option value="' . htmlspecialchars($cat['category']) . '" ' . $selected . '>';
            echo htmlspecialchars($cat['category']);
            echo '</option>';
        }
        ?>
    </select>
</div>

// Checkbox filter
<div class="form-group">
    <label>
        <input type="checkbox" name="include_deleted" value="1" 
               <?= isset($_GET['include_deleted']) ? 'checked' : '' ?>>
        <?php echo __('Include deleted records'); ?>
    </label>
</div>
```

---

## ⚡ **Performance Tips**

### **1. Limit Results with Pagination**

```php
// Get page number
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 50;
$offset = ($page - 1) * $per_page;

// Query with LIMIT
$sql = "SELECT * FROM biblio WHERE ... LIMIT ? OFFSET ?";
$stmt = $dbs->prepare($sql);
$stmt->bind_param('ii', $per_page, $offset);

// Count total for pagination
$count_sql = "SELECT COUNT(*) as total FROM biblio WHERE ...";
$count_result = $dbs->query($count_sql);
$total = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total / $per_page);

// Render pagination
echo '<div class="pagination">';
for ($i = 1; $i <= $total_pages; $i++) {
    $active = ($i === $page) ? 'active' : '';
    echo '<a href="?page=' . $i . '&..." class="' . $active . '">' . $i . '</a> ';
}
echo '</div>';
```

### **2. Cache Results**

```php
// Simple file-based cache
$cache_key = md5($start_date . $end_date . $category);
$cache_file = SB . 'files/cache/report_' . $cache_key . '.json';
$cache_time = 3600; // 1 hour

if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_time)) {
    // Load from cache
    $data = json_decode(file_get_contents($cache_file), true);
} else {
    // Query database
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    // Save to cache
    file_put_contents($cache_file, json_encode($data));
}
```

---

## ✅ **Iframe Pattern Checklist**

- [ ] Define `$reportView = isset($_GET['reportView']);`
- [ ] Parent mode exits with `exit;` after form/iframe
- [ ] Form has `target="reportView"` attribute
- [ ] Hidden fields preserve `mod`, `id`, `sec`, `reportView`
- [ ] Iframe has `name="reportView"` attribute
- [ ] Iframe `src` includes initial parameters
- [ ] Iframe mode uses output buffering
- [ ] Content rendered with `printed_page_tpl.php`
- [ ] All output is escaped with `htmlspecialchars()`
- [ ] Table uses admin template classes

---

## 📖 **Reference Plugins**

Study these production plugins:

```bash
/path/to/slims/plugins/
├── rekap-plus-lokasi/           # ⭐ PRIMARY REFERENCE
│   ├── registrat.plugin.php
│   └── admin_simple.php         # Iframe pattern example
├── visitor-transaction-hour/    # Iframe with charts
└── monthly-collection-report/   # Complex filters
```

---

**Last Updated**: 2025-10-14  
**SLiMS Version**: 9.6.1 Bulian  
**Pattern**: Iframe Dual-Mode  
**Status**: ✅ Production Ready
