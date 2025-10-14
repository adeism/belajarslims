# SLiMS Plugin Common Errors & Solutions

> **Context File untuk AI Chatbot**  
> **Purpose**: Troubleshooting guide untuk error-error umum  
> **Reference**: PLUGIN-ERROR-TROUBLESHOOTING-GUIDE.md

---

## 🚨 **Error Categories**

1. **Plugin Registration Errors** - Plugin tidak muncul di menu
2. **Authentication Errors** - Access denied issues
3. **Database Errors** - SQL query failures
4. **UI Errors** - Admin menu hilang, CSS tidak load
5. **Migration Errors** - Table creation failures

---

## 1️⃣ **Plugin Registration Errors**

### **Error: Plugin Not Showing in Menu**

**Symptoms**:
- Plugin installed di folder
- Tidak muncul di admin menu

**Possible Causes**:

#### **Cause 1: Wrong File Naming**

```php
// ❌ WRONG - No .plugin.php extension
my_plugin.php

// ✅ CORRECT - Pattern 1
my_plugin.plugin.php

// ✅ CORRECT - Pattern 2
registrat.plugin.php
```

#### **Cause 2: Wrong Variable Name**

```php
// ❌ WRONG - Undefined variable
$plg = Plugins::getInstance();

// ✅ CORRECT - Pattern 1
$plugins = Plugins::getInstance();  // Plural

// ✅ CORRECT - Pattern 2
$plugin = Plugins::getInstance();   // Singular
```

#### **Cause 3: Invalid Menu Category**

```php
// ❌ WRONG - 'admin' is NOT valid!
$plugins->registerMenu('admin', 'My Plugin', __DIR__ . '/index.php');

// ✅ CORRECT - Use valid category
$plugins->registerMenu('reporting', 'My Plugin', __DIR__ . '/index.php');
```

**Valid categories**: `bibliography`, `circulation`, `membership`, `master_file`, `reporting`, `serial_control`, `stock_take`, `system`, `opac`

#### **Cause 4: Wrong Folder Depth**

```
plugins/
└── level1/
    └── level2/
        └── level3/
            └── my_plugin.plugin.php  ✅ OK (depth 3)
            └── level4/
                └── plugin.php        ❌ TOO DEEP (depth 4)
```

**SLiMS scans up to 3 levels deep!**

#### **Cause 5: File Permissions**

```bash
# Check permissions
ls -la /path/to/slims/plugins/my-plugin/

# Fix permissions
chmod -R 755 /path/to/slims/plugins/my-plugin/
```

### **Solution Checklist**:

- [ ] File named `*.plugin.php` or `registrat.plugin.php`
- [ ] Uses `$plugins` (Pattern 1) or `$plugin` (Pattern 2)
- [ ] Valid menu category (NOT `'admin'`)
- [ ] Folder depth ≤ 3 levels
- [ ] File permissions 755
- [ ] PHP syntax valid: `php -l filename.php`

---

## 2️⃣ **Authentication Errors**

### **Error: "Access denied!" or "You do not have permission"**

**Symptoms**:
- Can access login page
- After login, see "Access denied"

**Possible Causes**:

#### **Cause 1: Wrong Authentication Constant**

```php
// ❌ WRONG - Deprecated SLiMS 8 style
define('DB_ACCESS', '1');

// ✅ CORRECT - Modern SLiMS 9
define('INDEX_AUTH', '1');
```

#### **Cause 2: Missing Session Files**

```php
// ❌ WRONG - Old pattern (no longer used in production)
define('INDEX_AUTH', '1');
require_once '../../sysconfig.inc.php';
// Missing session files!

// ✅ CORRECT - Production pattern from working plugins
if (!defined('INDEX_AUTH')) {
    define('INDEX_AUTH', '1');
}
global $dbs, $sysconf;
require SB . 'admin/default/session.inc.php';
require SB . 'admin/default/session_check.inc.php';
```

#### **Cause 3: Wrong Privilege Module**

```php
// Check what privilege module you're checking
$can_read = utility::havePrivilege('reporting', 'r');

// Make sure user has that privilege!
// Check in: Admin → User Management → Edit User → Privileges
```

**Solution**: Login as superadmin to test, or grant correct privileges to user

#### **Cause 4: Case Sensitivity**

```php
// ❌ WRONG - PHP is case-sensitive
Define('INDEX_AUTH', '1');  // Capital D

// ✅ CORRECT
define('INDEX_AUTH', '1');  // Lowercase d
```

### **Solution Checklist**:

- [ ] Uses `define('INDEX_AUTH', '1');`
- [ ] Loads `session.inc.php` and `session_check.inc.php`
- [ ] Privilege check matches user's actual privileges
- [ ] No typos in constant names (case-sensitive!)
- [ ] User is logged in to admin panel

---

## 3️⃣ **Database Errors**

### **Error: "Column not found" or "Unknown column"**

**Symptoms**:
- Error: Column 'xyz' in field list is unknown

**Possible Causes**:

#### **Cause 1: Typo in Column Name**

```php
// ❌ WRONG - Column name typo
SELECT equip_name FROM library_equipment

// ✅ CORRECT - Check exact column name
SELECT equipment_name FROM library_equipment
```

**Solution**: Verify column names in database

```sql
DESCRIBE library_equipment;
SHOW COLUMNS FROM library_equipment;
```

#### **Cause 2: Reserved Word Without Backticks**

```php
// ❌ WRONG - 'condition' is MySQL reserved word
SELECT condition FROM library_equipment

// ✅ CORRECT - Use backticks
SELECT `condition` FROM library_equipment
```

#### **Cause 3: Table Doesn't Exist**

```php
// ❌ Table not created yet
SELECT * FROM library_equipment

// ✅ Check if migration ran
SHOW TABLES LIKE 'library_equipment';
```

**Solution**: Deactivate and reactivate plugin to run migration

### **Error: SQL Syntax Error**

**Symptoms**:
- Error: You have an error in your SQL syntax

**Possible Causes**:

#### **Cause 1: Wrong SQL Syntax**

```php
// ❌ WRONG - Missing comma
SELECT id, name location FROM table

// ✅ CORRECT
SELECT id, name, location FROM table
```

#### **Cause 2: Unescaped Quotes**

```php
// ❌ WRONG - Quote inside string
$sql = "SELECT * FROM table WHERE name = 'O'Reilly'";

// ✅ CORRECT - Use prepared statements
$sql = "SELECT * FROM table WHERE name = ?";
$stmt->bind_param('s', $name);
```

#### **Cause 3: Wrong CREATE TABLE Syntax**

```php
// ❌ WRONG - Braces instead of parentheses (official docs error!)
CREATE TABLE `my_table` {
    `id` INT PRIMARY KEY
}

// ✅ CORRECT - Use parentheses
CREATE TABLE `my_table` (
    `id` INT PRIMARY KEY
)
```

### **Error: "Duplicate entry" or "Primary key constraint"**

**Symptoms**:
- Error: Duplicate entry '123' for key 'PRIMARY'

**Solution**:

```php
// Check if record exists before INSERT
$check_sql = "SELECT id FROM library_equipment WHERE id = ?";
$stmt = $dbs->prepare($check_sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Record exists - use UPDATE
    $sql = "UPDATE library_equipment SET ... WHERE id = ?";
} else {
    // Record doesn't exist - use INSERT
    $sql = "INSERT INTO library_equipment (...) VALUES (...)";
}
```

### **Solution Checklist**:

- [ ] Column names match database exactly
- [ ] Reserved words use backticks
- [ ] Table exists (check migration)
- [ ] SQL syntax is valid
- [ ] Prepared statements used for user input
- [ ] Check for duplicate keys before INSERT

---

## 4️⃣ **UI Errors**

### **Error: Admin Menu Disappears After Form Submit**

**Symptoms**:
- Form submit causes page reload
- Admin sidebar menu disappears

**Cause**: Missing plugin context parameters (`mod`, `id`, `sec`)

**Solution**: Use iframe pattern (see `04-slims-iframe-pattern.md`)

```php
// ✅ CORRECT - Maintain context with hidden fields
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" target="reportView">
    <input type="hidden" name="mod" value="<?= $_GET['mod'] ?? '' ?>" />
    <input type="hidden" name="id" value="<?= $_GET['id'] ?? '' ?>" />
    <input type="hidden" name="sec" value="/" />
    <input type="hidden" name="reportView" value="true" />
    
    <!-- Your form fields -->
</form>

<iframe name="reportView" ...></iframe>
```

### **Error: CSS Not Loading (404)**

**Symptoms**:
- Browser console: 404 error for CSS file
- Styles not applied

**Possible Causes**:

#### **Cause 1: Wrong Path Constant**

```php
// ❌ WRONG - Server path for browser
<link href="<?= SB ?>plugins/my-plugin/assets/style.css">

// ❌ WRONG - Relative path
<link href="assets/style.css">

// ✅ CORRECT - Web path
<link href="<?= SWB ?>plugins/my-plugin/assets/style.css">
```

#### **Cause 2: File Doesn't Exist**

```bash
# Verify file exists
ls -la /path/to/slims/plugins/my-plugin/assets/

# Check file permissions
chmod 644 /path/to/slims/plugins/my-plugin/assets/style.css
```

#### **Cause 3: Wrong Hook for Global Loading**

```php
// ✅ CORRECT - Load CSS globally
// File: my_plugin.plugin.php

$plugins->register(Plugins::ADMIN_HEADER, function() {
    echo '<link rel="stylesheet" href="' . SWB . 'plugins/my-plugin/assets/style.css">';
});
```

### **Solution Checklist**:

- [ ] Use `SWB` constant for CSS/JS URLs
- [ ] File exists and has correct permissions (644)
- [ ] Path is absolute, not relative
- [ ] For global CSS, use `Plugins::ADMIN_HEADER` hook
- [ ] Check browser console for actual error

---

## 5️⃣ **Migration Errors**

### **Error: Table Already Exists**

**Symptoms**:
- Error: Table 'library_equipment' already exists

**Solution**:

```php
// ✅ Use IF NOT EXISTS
function up()
{
    \SLiMS\DB::getInstance()->query(<<<SQL
    CREATE TABLE IF NOT EXISTS `library_equipment` (
        `id` INT PRIMARY KEY
    )
    SQL);
}
```

### **Error: Can't DROP table**

**Symptoms**:
- Error: Unknown table 'library_equipment'

**Solution**:

```php
// ✅ Use IF EXISTS
function down()
{
    \SLiMS\DB::getInstance()->query(<<<SQL
    DROP TABLE IF EXISTS `library_equipment`
    SQL);
}
```

### **Error: Foreign Key Constraint Fails**

**Symptoms**:
- Error: Cannot add or update a child row: foreign key constraint fails

**Solution**: Create parent table first

```php
// ✅ CORRECT - Numbered migration files ensure order
migration/
├── 1_CreateParentTable.php      # Create parent first
└── 2_CreateChildTable.php       # Then child with FK
```

### **Solution Checklist**:

- [ ] Migration files numbered correctly (1_, 2_, 3_)
- [ ] `up()` uses `IF NOT EXISTS`
- [ ] `down()` uses `IF EXISTS`
- [ ] Parent tables created before child tables
- [ ] Foreign key column types match referenced columns
- [ ] Test both activate and deactivate

---

## 🔍 **Debugging Tools**

### **1. Check PHP Syntax**

```bash
# Windows (cmd)
cd /path/to/project
php -l plugin\my-plugin\index.php

# Linux
php -l /path/to/slims/plugins/my-plugin/index.php
```

### **2. Enable Error Display**

```php
// Add to top of file for debugging (REMOVE in production!)
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### **3. Check Database**

```sql
-- Show all tables
SHOW TABLES;

-- Check table structure
DESCRIBE library_equipment;

-- Check if data exists
SELECT COUNT(*) FROM library_equipment;

-- Check last error
SHOW ERRORS;
```

### **4. Check Browser Console**

```
F12 → Console tab
- Look for JavaScript errors
- Look for 404 errors (missing CSS/JS)
- Look for AJAX errors
```

### **5. Check Error Logs**

```bash
# Apache error log (Linux)
tail -f /var/log/apache2/error.log

# PHP error log
tail -f /var/log/php/error.log

# SLiMS log
tail -f /path/to/slims/files/error_log/error.log
```

---

## 🛠️ **Quick Fixes**

### **Reset Plugin**

```bash
# 1. Deactivate plugin in admin panel
# 2. Delete plugin folder
rm -rf /path/to/slims/plugins/my-plugin/

# 3. Extract fresh copy
unzip my-plugin.zip -d /path/to/slims/plugins/

# 4. Fix permissions
chmod -R 755 /path/to/slims/plugins/my-plugin/

# 5. Reactivate in admin panel
```

### **Fix Permissions (Linux)**

```bash
# Plugin folder
chmod -R 755 /path/to/slims/plugins/my-plugin/

# Plugin files
find /path/to/slims/plugins/my-plugin/ -type f -exec chmod 644 {} \;

# Plugin directories
find /path/to/slims/plugins/my-plugin/ -type d -exec chmod 755 {} \;
```

### **Clear Browser Cache**

```
Chrome: Ctrl + Shift + Delete
Firefox: Ctrl + Shift + Delete
Or: Hard reload with Ctrl + F5
```

---

## 📖 **Additional Resources**

- **Complete Guide**: `PLUGIN-ERROR-TROUBLESHOOTING-GUIDE.md` (56 KB)
- **Quick Reference**: `PLUGIN-QUICK-REFERENCE.md`
- **Security Guide**: `03-slims-security-best-practices.md`

---

**Last Updated**: 2025-10-14  
**SLiMS Version**: 9.6.1 Bulian  
**Status**: ✅ Production Ready
