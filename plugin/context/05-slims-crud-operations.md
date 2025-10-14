# SLiMS Plugin CRUD Operations - Complete Guide

> **Context File untuk AI Chatbot**  
> **Purpose**: Step-by-step guide untuk Create, Read, Update, Delete operations  
> **Reference**: PLUGIN-CRUD-BEGINNER-GUIDE.md

---

## 🎯 **CRUD Overview**

**CRUD** = Create, Read, Update, Delete (operasi dasar database)

```
┌─────────────────────────────────────────┐
│ PLUGIN CRUD WORKFLOW                    │
├─────────────────────────────────────────┤
│ 1. CREATE  → Insert new record          │
│ 2. READ    → Display records in table   │
│ 3. UPDATE  → Edit existing record       │
│ 4. DELETE  → Remove record              │
└─────────────────────────────────────────┘
```

---

## 📁 **File Structure**

```
plugins/equipment-manager/
├── equipment.plugin.php        # Plugin registration
├── migration/
│   ├── 1_CreateEquipmentTable.php   # Create table
│   └── 2_InsertSampleData.php       # Sample data
├── index.php                   # Main CRUD interface
└── assets/
    └── style.css               # Custom styling (optional)
```

---

## 🗄️ **Step 1: Create Database Table (Migration)**

### **File: migration/1_CreateEquipmentTable.php**

```php
<?php
/**
 * Migration: CreateEquipmentTable
 * Creates library_equipment table for managing library equipment
 */

use SLiMS\Migration\Migration;

class CreateEquipmentTable extends Migration
{
    /**
     * Run when plugin is activated
     */
    function up()
    {
        \SLiMS\DB::getInstance()->query(<<<SQL
        CREATE TABLE `library_equipment` (
            `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
            `equipment_name` VARCHAR(100) NOT NULL,
            `category` VARCHAR(50) DEFAULT NULL,
            `quantity` INT(11) DEFAULT 0,
            `condition` ENUM('baik', 'rusak', 'hilang') DEFAULT 'baik',
            `location` VARCHAR(100) DEFAULT NULL,
            `purchase_date` DATE DEFAULT NULL,
            `price` DECIMAL(10,2) DEFAULT 0.00,
            `notes` TEXT DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            INDEX `idx_category` (`category`),
            INDEX `idx_condition` (`condition`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);
    }
    
    /**
     * Rollback when plugin is deactivated
     */
    function down()
    {
        \SLiMS\DB::getInstance()->query(<<<SQL
        DROP TABLE IF EXISTS `library_equipment`
        SQL);
    }
}
```

---

## 📋 **Step 2: Main CRUD Interface**

### **File: index.php** (Complete Implementation)

```php
<?php
/**
 * Equipment Manager - CRUD Interface
 * File: index.php
 */

// ===== SECURITY LAYER =====
if (!defined('INDEX_AUTH')) {
    define('INDEX_AUTH', '1');
}
global $dbs, $sysconf;
require SB . 'admin/default/session.inc.php';
require SB . 'admin/default/session_check.inc.php';

// Check privileges
$can_read = utility::havePrivilege('master_file', 'r');
$can_write = utility::havePrivilege('master_file', 'w');

if (!$can_read) {
    die('<div class="errorBox">' . __('You do not have permission!') . '</div>');
}

// ===== HANDLE FORM SUBMISSIONS =====

// DELETE operation
if (isset($_POST['delete']) && $can_write) {
    $id = (int)$_POST['id'];
    
    $sql = "DELETE FROM library_equipment WHERE id = ?";
    $stmt = $dbs->prepare($sql);
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        $info = '<div class="infoBox">' . __('Equipment deleted successfully!') . '</div>';
    } else {
        $error = '<div class="errorBox">' . __('Failed to delete equipment!') . '</div>';
    }
}

// CREATE or UPDATE operation
if (isset($_POST['save']) && $can_write) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $equipment_name = trim($_POST['equipment_name']);
    $category = trim($_POST['category']);
    $quantity = (int)$_POST['quantity'];
    $condition = $_POST['condition'];
    $location = trim($_POST['location']);
    $purchase_date = $_POST['purchase_date'] ?: null;
    $price = (float)$_POST['price'];
    $notes = trim($_POST['notes']);
    
    // Validation
    if (empty($equipment_name)) {
        $error = '<div class="errorBox">' . __('Equipment name is required!') . '</div>';
    } else {
        if ($id > 0) {
            // UPDATE
            $sql = <<<SQL
            UPDATE library_equipment SET
                equipment_name = ?,
                category = ?,
                quantity = ?,
                `condition` = ?,
                location = ?,
                purchase_date = ?,
                price = ?,
                notes = ?
            WHERE id = ?
            SQL;
            
            $stmt = $dbs->prepare($sql);
            $stmt->bind_param('ssissdssi', 
                $equipment_name, $category, $quantity, $condition, 
                $location, $purchase_date, $price, $notes, $id
            );
        } else {
            // CREATE
            $sql = <<<SQL
            INSERT INTO library_equipment 
            (equipment_name, category, quantity, `condition`, location, purchase_date, price, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            SQL;
            
            $stmt = $dbs->prepare($sql);
            $stmt->bind_param('ssissdss', 
                $equipment_name, $category, $quantity, $condition, 
                $location, $purchase_date, $price, $notes
            );
        }
        
        if ($stmt->execute()) {
            $info = '<div class="infoBox">' . __('Equipment saved successfully!') . '</div>';
        } else {
            $error = '<div class="errorBox">' . __('Failed to save equipment!') . '</div>';
        }
    }
}

// ===== GET EDIT DATA =====
$edit_data = null;
if (isset($_GET['edit']) && $can_write) {
    $edit_id = (int)$_GET['edit'];
    $sql = "SELECT * FROM library_equipment WHERE id = ?";
    $stmt = $dbs->prepare($sql);
    $stmt->bind_param('i', $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_data = $result->fetch_assoc();
}

// ===== READ DATA WITH PAGINATION =====
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Search filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where_clause = '';
$search_param = '';

if (!empty($search)) {
    $where_clause = "WHERE equipment_name LIKE ? OR category LIKE ? OR location LIKE ?";
    $search_param = '%' . $search . '%';
}

// Count total records
$count_sql = "SELECT COUNT(*) as total FROM library_equipment $where_clause";
if ($search_param) {
    $stmt = $dbs->prepare($count_sql);
    $stmt->bind_param('sss', $search_param, $search_param, $search_param);
    $stmt->execute();
    $count_result = $stmt->get_result();
} else {
    $count_result = $dbs->query($count_sql);
}
$total = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total / $per_page);

// Get records
$sql = <<<SQL
SELECT * FROM library_equipment 
$where_clause
ORDER BY equipment_name ASC
LIMIT ? OFFSET ?
SQL;

if ($search_param) {
    $stmt = $dbs->prepare($sql);
    $stmt->bind_param('sssii', $search_param, $search_param, $search_param, $per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $stmt = $dbs->prepare($sql);
    $stmt->bind_param('ii', $per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
}

?>

<!-- ===== HTML INTERFACE ===== -->
<div class="menuBox">
    <div class="menuBoxInner masterFileIcon">
        <div class="per_title">
            <h2><?php echo __('Equipment Manager'); ?></h2>
        </div>
        
        <!-- Display messages -->
        <?php
        if (isset($info)) echo $info;
        if (isset($error)) echo $error;
        ?>
        
        <!-- SEARCH FORM -->
        <div class="infoBox">
            <form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="mod" value="<?= $_GET['mod'] ?? '' ?>">
                <input type="hidden" name="id" value="<?= $_GET['id'] ?? '' ?>">
                
                <div class="form-group" style="display: inline-block;">
                    <input type="text" name="search" class="form-control" 
                           placeholder="<?php echo __('Search equipment...'); ?>"
                           value="<?= htmlspecialchars($search) ?>">
                </div>
                <button type="submit" class="s-btn btn btn-primary">
                    <?php echo __('Search'); ?>
                </button>
                <?php if ($search): ?>
                <a href="<?php echo $_SERVER['PHP_SELF'] . '?mod=' . ($_GET['mod'] ?? '') . '&id=' . ($_GET['id'] ?? ''); ?>" 
                   class="s-btn btn btn-default">
                    <?php echo __('Clear'); ?>
                </a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- ADD/EDIT FORM -->
        <?php if ($can_write): ?>
        <div style="margin-bottom: 20px;">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?mod=' . ($_GET['mod'] ?? '') . '&id=' . ($_GET['id'] ?? ''); ?>">
                <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                <?php endif; ?>
                
                <fieldset>
                    <legend><?php echo $edit_data ? __('Edit Equipment') : __('Add New Equipment'); ?></legend>
                    
                    <div class="form-group">
                        <label><?php echo __('Equipment Name'); ?> *</label>
                        <input type="text" name="equipment_name" class="form-control" 
                               value="<?= htmlspecialchars($edit_data['equipment_name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label><?php echo __('Category'); ?></label>
                        <input type="text" name="category" class="form-control" 
                               value="<?= htmlspecialchars($edit_data['category'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><?php echo __('Quantity'); ?></label>
                        <input type="number" name="quantity" class="form-control" 
                               value="<?= $edit_data['quantity'] ?? 1 ?>" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label><?php echo __('Condition'); ?></label>
                        <select name="condition" class="form-control">
                            <option value="baik" <?= ($edit_data['condition'] ?? '') === 'baik' ? 'selected' : '' ?>>
                                <?php echo __('Good'); ?>
                            </option>
                            <option value="rusak" <?= ($edit_data['condition'] ?? '') === 'rusak' ? 'selected' : '' ?>>
                                <?php echo __('Damaged'); ?>
                            </option>
                            <option value="hilang" <?= ($edit_data['condition'] ?? '') === 'hilang' ? 'selected' : '' ?>>
                                <?php echo __('Lost'); ?>
                            </option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><?php echo __('Location'); ?></label>
                        <input type="text" name="location" class="form-control" 
                               value="<?= htmlspecialchars($edit_data['location'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><?php echo __('Purchase Date'); ?></label>
                        <input type="date" name="purchase_date" class="form-control" 
                               value="<?= $edit_data['purchase_date'] ?? '' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><?php echo __('Price'); ?></label>
                        <input type="number" step="0.01" name="price" class="form-control" 
                               value="<?= $edit_data['price'] ?? 0 ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><?php echo __('Notes'); ?></label>
                        <textarea name="notes" class="form-control" rows="3"><?= htmlspecialchars($edit_data['notes'] ?? '') ?></textarea>
                    </div>
                    
                    <button type="submit" name="save" class="s-btn btn btn-primary">
                        <?php echo __('Save'); ?>
                    </button>
                    <?php if ($edit_data): ?>
                    <a href="<?php echo $_SERVER['PHP_SELF'] . '?mod=' . ($_GET['mod'] ?? '') . '&id=' . ($_GET['id'] ?? ''); ?>" 
                       class="s-btn btn btn-default">
                        <?php echo __('Cancel'); ?>
                    </a>
                    <?php endif; ?>
                </fieldset>
            </form>
        </div>
        <?php endif; ?>
        
        <!-- DATA TABLE -->
        <table class="s-table table table-sm table-bordered">
            <thead>
                <tr>
                    <th><?php echo __('ID'); ?></th>
                    <th><?php echo __('Equipment Name'); ?></th>
                    <th><?php echo __('Category'); ?></th>
                    <th><?php echo __('Quantity'); ?></th>
                    <th><?php echo __('Condition'); ?></th>
                    <th><?php echo __('Location'); ?></th>
                    <?php if ($can_write): ?>
                    <th><?php echo __('Actions'); ?></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows === 0) {
                    echo '<tr><td colspan="' . ($can_write ? 7 : 6) . '">' . __('No equipment found') . '</td></tr>';
                } else {
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $row['id'] . '</td>';
                        echo '<td>' . htmlspecialchars($row['equipment_name']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['category']) . '</td>';
                        echo '<td>' . $row['quantity'] . '</td>';
                        echo '<td>' . htmlspecialchars($row['condition']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['location']) . '</td>';
                        
                        if ($can_write) {
                            echo '<td>';
                            // Edit button
                            $edit_url = $_SERVER['PHP_SELF'] . '?mod=' . ($_GET['mod'] ?? '') . '&id=' . ($_GET['id'] ?? '') . '&edit=' . $row['id'];
                            echo '<a href="' . $edit_url . '" class="s-btn btn btn-sm btn-default">' . __('Edit') . '</a> ';
                            
                            // Delete button
                            echo '<form method="post" style="display:inline;" onsubmit="return confirm(\'' . __('Are you sure?') . '\')">';
                            echo '<input type="hidden" name="id" value="' . $row['id'] . '">';
                            echo '<button type="submit" name="delete" class="s-btn btn btn-sm btn-danger">' . __('Delete') . '</button>';
                            echo '</form>';
                            echo '</td>';
                        }
                        
                        echo '</tr>';
                    }
                }
                ?>
            </tbody>
        </table>
        
        <!-- PAGINATION -->
        <?php if ($total_pages > 1): ?>
        <div style="margin-top: 10px;">
            <?php
            for ($i = 1; $i <= $total_pages; $i++) {
                $active = ($i === $page) ? 'btn-primary' : 'btn-default';
                $url = $_SERVER['PHP_SELF'] . '?mod=' . ($_GET['mod'] ?? '') . '&id=' . ($_GET['id'] ?? '') . '&page=' . $i;
                if ($search) $url .= '&search=' . urlencode($search);
                
                echo '<a href="' . $url . '" class="s-btn btn btn-sm ' . $active . '">' . $i . '</a> ';
            }
            ?>
        </div>
        <?php endif; ?>
        
        <div style="margin-top: 10px;">
            <strong><?php echo __('Total Records'); ?>:</strong> <?= $total ?>
        </div>
    </div>
</div>
```

---

## 🔑 **Key CRUD Patterns**

### **1. CREATE (Insert)**

```php
// Prepare statement
$sql = "INSERT INTO library_equipment (equipment_name, category, quantity) VALUES (?, ?, ?)";
$stmt = $dbs->prepare($sql);

// Bind parameters
$stmt->bind_param('ssi', $equipment_name, $category, $quantity);
// s = string, i = integer

// Execute
if ($stmt->execute()) {
    $new_id = $dbs->insert_id; // Get auto-increment ID
    echo "Success! New ID: $new_id";
}
```

### **2. READ (Select)**

```php
// Single record
$sql = "SELECT * FROM library_equipment WHERE id = ?";
$stmt = $dbs->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Multiple records
$sql = "SELECT * FROM library_equipment ORDER BY equipment_name";
$result = $dbs->query($sql);
while ($row = $result->fetch_assoc()) {
    echo $row['equipment_name'] . '<br>';
}

// With search
$sql = "SELECT * FROM library_equipment WHERE equipment_name LIKE ?";
$stmt = $dbs->prepare($sql);
$search_param = '%' . $search . '%';
$stmt->bind_param('s', $search_param);
$stmt->execute();
$result = $stmt->get_result();
```

### **3. UPDATE (Modify)**

```php
$sql = <<<SQL
UPDATE library_equipment SET
    equipment_name = ?,
    category = ?,
    quantity = ?
WHERE id = ?
SQL;

$stmt = $dbs->prepare($sql);
$stmt->bind_param('ssii', $equipment_name, $category, $quantity, $id);

if ($stmt->execute()) {
    echo "Updated successfully!";
}
```

### **4. DELETE (Remove)**

```php
$sql = "DELETE FROM library_equipment WHERE id = ?";
$stmt = $dbs->prepare($sql);
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    echo "Deleted successfully!";
}
```

---

## 🔍 **Advanced Queries**

### **Join with Another Table**

```php
// Join equipment with borrow table
$sql = <<<SQL
SELECT 
    e.id,
    e.equipment_name,
    m.member_name,
    b.borrow_date,
    b.return_date
FROM library_equipment e
LEFT JOIN equipment_borrow b ON e.id = b.equipment_id
LEFT JOIN member m ON b.member_id = m.member_id
WHERE e.id = ?
SQL;

$stmt = $dbs->prepare($sql);
$stmt->bind_param('i', $equipment_id);
$stmt->execute();
$result = $stmt->get_result();
```

### **Aggregate Functions**

```php
// Count by category
$sql = <<<SQL
SELECT 
    category,
    COUNT(*) as total,
    SUM(quantity) as total_quantity
FROM library_equipment
GROUP BY category
ORDER BY total DESC
SQL;

$result = $dbs->query($sql);
while ($row = $result->fetch_assoc()) {
    echo $row['category'] . ': ' . $row['total'] . ' items<br>';
}
```

---

## ✅ **CRUD Best Practices**

### **1. Input Validation**

```php
// Always validate before saving
if (empty($equipment_name)) {
    $error = 'Equipment name is required!';
} elseif (strlen($equipment_name) > 100) {
    $error = 'Equipment name too long!';
} elseif ($quantity < 0) {
    $error = 'Quantity cannot be negative!';
}
```

### **2. Error Handling**

```php
try {
    $stmt = $dbs->prepare($sql);
    $stmt->bind_param('s', $name);
    $stmt->execute();
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    $error = 'An error occurred. Please try again.';
}
```

### **3. Confirm Before Delete**

```php
// JavaScript confirmation
<form method="post" onsubmit="return confirm('Are you sure you want to delete this equipment?')">
    <button type="submit" name="delete">Delete</button>
</form>
```

### **4. Pagination for Large Datasets**

```php
// Always use LIMIT for large tables
$sql = "SELECT * FROM library_equipment LIMIT ? OFFSET ?";
$stmt = $dbs->prepare($sql);
$stmt->bind_param('ii', $per_page, $offset);
```

---

## 🚨 **Common CRUD Errors**

### **Error 1: Column Mismatch**

```php
// ❌ WRONG - Column name in query doesn't match table
INSERT INTO library_equipment (name, cat) VALUES (?, ?)

// ✅ CORRECT - Match exact column names
INSERT INTO library_equipment (equipment_name, category) VALUES (?, ?)
```

### **Error 2: Parameter Binding Mismatch**

```php
// ❌ WRONG - 3 placeholders, 2 parameters
$stmt->bind_param('ss', $name, $category);
$sql = "INSERT INTO table (col1, col2, col3) VALUES (?, ?, ?)";

// ✅ CORRECT - Match parameter count
$stmt->bind_param('ssi', $name, $category, $quantity);
```

### **Error 3: Unescaped Output**

```php
// ❌ WRONG - XSS vulnerable
echo '<td>' . $row['equipment_name'] . '</td>';

// ✅ CORRECT - Always escape
echo '<td>' . htmlspecialchars($row['equipment_name']) . '</td>';
```

---

## 📖 **Reference**

- **Complete Tutorial**: `PLUGIN-CRUD-BEGINNER-GUIDE.md`
- **Security Guide**: `03-slims-security-best-practices.md`
- **Migration Guide**: `02-slims-database-migration.md`

---

**Last Updated**: 2025-10-14  
**SLiMS Version**: 9.6.1 Bulian  
**Status**: ✅ Production Ready
