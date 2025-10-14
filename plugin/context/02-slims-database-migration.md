# SLiMS Database Migration System

> **Context File untuk AI Chatbot**  
> **Purpose**: Complete guide untuk database migration di SLiMS  
> **Feature Since**: SLiMS 9.x (Bulian)

---

## 🎯 **Apa itu Migration System?**

Migration system di SLiMS adalah fitur untuk:

- **Mengelola database schema secara terstruktur**
- **Otomatis dijalankan saat plugin aktif/nonaktif**
- **Rollback schema jika plugin dinonaktifkan**
- **Version control untuk database changes**

---

## 📁 **Struktur Migration Folder**

```
plugins/my-plugin/
├── my_plugin.plugin.php           # Plugin registration
└── migration/                     # Migration folder
    ├── 1_CreateEquipmentTable.php # Migration file #1
    ├── 2_AddColumnStatus.php      # Migration file #2
    └── 3_InsertSampleData.php     # Migration file #3
```

**Aturan Penamaan**:

- **Format**: `{angka}_{NamaClass}.php`
- **Angka**: Menentukan urutan eksekusi (1, 2, 3, dst)
- **NamaClass**: PascalCase, descriptive
- **Eksekusi**: Berurutan dari angka terkecil

---

## 📝 **Migration Class Template**

### **Basic Structure**

```php
<?php
/**
 * Migration: CreateEquipmentTable
 * Description: Creates library_equipment table
 */

use SLiMS\Migration\Migration;

class CreateEquipmentTable extends Migration
{
    /**
     * Run migration (saat plugin diaktifkan)
     */
    function up()
    {
        // Create table, add columns, insert data
        \SLiMS\DB::getInstance()->query(<<<SQL
        CREATE TABLE `library_equipment` (
            `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);
    }
    
    /**
     * Rollback migration (saat plugin dinonaktifkan)
     */
    function down()
    {
        // Drop table, remove columns, delete data
        \SLiMS\DB::getInstance()->query(<<<SQL
        DROP TABLE IF EXISTS `library_equipment`
        SQL);
    }
}
```

---

## 🔧 **Database Access Methods**

### **Method 1: \SLiMS\DB::getInstance() (RECOMMENDED)**

```php
use SLiMS\Migration\Migration;

class MyMigration extends Migration
{
    function up()
    {
        // PDO-based
        \SLiMS\DB::getInstance()->query(<<<SQL
        CREATE TABLE `my_table` (
            `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL
        ) ENGINE=InnoDB
        SQL);
    }
    
    function down()
    {
        \SLiMS\DB::getInstance()->query("DROP TABLE IF EXISTS `my_table`");
    }
}
```

### **Method 2: Global $dbs (MySQLi)**

```php
use SLiMS\Migration\Migration;

class MyMigration extends Migration
{
    function up()
    {
        global $dbs;
        
        $sql = <<<SQL
        CREATE TABLE `my_table` (
            `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL
        ) ENGINE=InnoDB
        SQL;
        
        $dbs->query($sql);
    }
    
    function down()
    {
        global $dbs;
        $dbs->query("DROP TABLE IF EXISTS `my_table`");
    }
}
```

**Note**: Kedua method ini VALID dan berfungsi!

---

## 📊 **SQL Syntax Rules**

### **1. CREATE TABLE**

```php
// ✅ CORRECT - Backticks for identifiers, parentheses for definition
\SLiMS\DB::getInstance()->query(<<<SQL
CREATE TABLE `library_equipment` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `equipment_name` VARCHAR(100) NOT NULL,
    `category` VARCHAR(50) DEFAULT NULL,
    `quantity` INT(11) DEFAULT 0,
    `condition` ENUM('baik', 'rusak', 'hilang') DEFAULT 'baik',
    `location` VARCHAR(100) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_category` (`category`),
    INDEX `idx_condition` (`condition`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);

// ❌ WRONG - Official docs have typo with braces {}
CREATE TABLE `library_equipment` {  // Wrong! Should be (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY
} ENGINE=InnoDB;  // Wrong! Should be )
```

### **2. ALTER TABLE**

```php
// Add column
\SLiMS\DB::getInstance()->query(<<<SQL
ALTER TABLE `library_equipment` 
ADD COLUMN `status` VARCHAR(20) DEFAULT 'active'
SQL);

// Modify column
\SLiMS\DB::getInstance()->query(<<<SQL
ALTER TABLE `library_equipment` 
MODIFY COLUMN `quantity` INT(11) NOT NULL DEFAULT 0
SQL);

// Drop column
\SLiMS\DB::getInstance()->query(<<<SQL
ALTER TABLE `library_equipment` 
DROP COLUMN `status`
SQL);
```

### **3. INSERT DATA**

```php
// Single row
\SLiMS\DB::getInstance()->query(<<<SQL
INSERT INTO `library_equipment` 
(`equipment_name`, `category`, `quantity`) 
VALUES ('Laptop', 'Elektronik', 5)
SQL);

// Multiple rows
\SLiMS\DB::getInstance()->query(<<<SQL
INSERT INTO `library_equipment` 
(`equipment_name`, `category`, `quantity`) 
VALUES 
    ('Laptop', 'Elektronik', 5),
    ('Proyektor', 'Elektronik', 3),
    ('Meja Baca', 'Furniture', 20)
SQL);
```

### **4. DROP TABLE**

```php
// Safe drop (recommended)
\SLiMS\DB::getInstance()->query(<<<SQL
DROP TABLE IF EXISTS `library_equipment`
SQL);

// Force drop
\SLiMS\DB::getInstance()->query(<<<SQL
DROP TABLE `library_equipment`
SQL);
```

---

## 📋 **Complete Migration Examples**

### **Example 1: Create Table with Foreign Key**

```php
<?php
/**
 * File: 1_CreateBorrowTable.php
 * Creates equipment_borrow table with FK to member
 */

use SLiMS\Migration\Migration;

class CreateBorrowTable extends Migration
{
    function up()
    {
        \SLiMS\DB::getInstance()->query(<<<SQL
        CREATE TABLE `equipment_borrow` (
            `borrow_id` INT(11) AUTO_INCREMENT PRIMARY KEY,
            `member_id` VARCHAR(20) NOT NULL,
            `equipment_id` INT(11) NOT NULL,
            `borrow_date` DATE NOT NULL,
            `return_date` DATE DEFAULT NULL,
            `status` ENUM('borrowed', 'returned') DEFAULT 'borrowed',
            `notes` TEXT DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            
            INDEX `idx_member` (`member_id`),
            INDEX `idx_equipment` (`equipment_id`),
            INDEX `idx_status` (`status`),
            
            CONSTRAINT `fk_borrow_member` 
                FOREIGN KEY (`member_id`) 
                REFERENCES `member` (`member_id`) 
                ON DELETE CASCADE 
                ON UPDATE CASCADE,
                
            CONSTRAINT `fk_borrow_equipment` 
                FOREIGN KEY (`equipment_id`) 
                REFERENCES `library_equipment` (`id`) 
                ON DELETE CASCADE 
                ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);
    }
    
    function down()
    {
        \SLiMS\DB::getInstance()->query(<<<SQL
        DROP TABLE IF EXISTS `equipment_borrow`
        SQL);
    }
}
```

### **Example 2: Add Columns to Existing Table**

```php
<?php
/**
 * File: 2_AddStatusColumns.php
 * Adds status and notes columns
 */

use SLiMS\Migration\Migration;

class AddStatusColumns extends Migration
{
    function up()
    {
        \SLiMS\DB::getInstance()->query(<<<SQL
        ALTER TABLE `library_equipment` 
        ADD COLUMN `status` ENUM('available', 'borrowed', 'maintenance') DEFAULT 'available' AFTER `condition`,
        ADD COLUMN `notes` TEXT DEFAULT NULL AFTER `status`
        SQL);
    }
    
    function down()
    {
        \SLiMS\DB::getInstance()->query(<<<SQL
        ALTER TABLE `library_equipment` 
        DROP COLUMN `status`,
        DROP COLUMN `notes`
        SQL);
    }
}
```

### **Example 3: Insert Sample Data**

```php
<?php
/**
 * File: 3_InsertSampleData.php
 * Inserts initial equipment data
 */

use SLiMS\Migration\Migration;

class InsertSampleData extends Migration
{
    function up()
    {
        \SLiMS\DB::getInstance()->query(<<<SQL
        INSERT INTO `library_equipment` 
        (`equipment_name`, `category`, `quantity`, `condition`, `location`) 
        VALUES 
            ('Laptop Dell Latitude', 'Elektronik', 5, 'baik', 'Ruang Sirkulasi'),
            ('Proyektor Epson', 'Elektronik', 2, 'baik', 'Ruang Meeting'),
            ('Scanner Canon', 'Elektronik', 3, 'baik', 'Ruang Katalog'),
            ('Meja Baca Kayu', 'Furniture', 20, 'baik', 'Ruang Baca'),
            ('Kursi Plastik', 'Furniture', 50, 'baik', 'Ruang Baca'),
            ('Rak Buku Besi', 'Furniture', 15, 'baik', 'Gudang Buku')
        SQL);
    }
    
    function down()
    {
        // Delete sample data on rollback
        \SLiMS\DB::getInstance()->query(<<<SQL
        DELETE FROM `library_equipment` 
        WHERE `equipment_name` IN (
            'Laptop Dell Latitude',
            'Proyektor Epson',
            'Scanner Canon',
            'Meja Baca Kayu',
            'Kursi Plastik',
            'Rak Buku Besi'
        )
        SQL);
    }
}
```

---

## 🔄 **Migration Execution Flow**

### **Saat Plugin Diaktifkan**

```
1. User klik "Activate" di plugin manager
2. SLiMS scan folder migration/
3. Urutkan file berdasarkan angka (1_, 2_, 3_)
4. Eksekusi up() untuk setiap migration file
5. Plugin status: ACTIVE
```

### **Saat Plugin Dinonaktifkan**

```
1. User klik "Deactivate" di plugin manager
2. SLiMS scan folder migration/
3. Urutkan file TERBALIK (3_, 2_, 1_)
4. Eksekusi down() untuk setiap migration file
5. Plugin status: INACTIVE
```

---

## ⚠️ **Important Notes**

### **1. Urutan Eksekusi Sangat Penting!**

```php
// ❌ SALAH - File tidak berurutan
migration/
├── 1_CreateTable.php
├── 3_AddColumn.php      // Skip 2!
└── 5_InsertData.php     // Skip 4!

// ✅ BENAR - File berurutan
migration/
├── 1_CreateTable.php
├── 2_AddColumn.php
└── 3_InsertData.php
```

### **2. down() Harus Membalikkan up()**

```php
class MyMigration extends Migration
{
    function up()
    {
        // Buat tabel
        \SLiMS\DB::getInstance()->query("CREATE TABLE `my_table` (...)");
    }
    
    function down()
    {
        // Hapus tabel - MUST reverse up()!
        \SLiMS\DB::getInstance()->query("DROP TABLE IF EXISTS `my_table`");
    }
}
```

### **3. Foreign Key Harus Drop di down()**

```php
function up()
{
    // Create table with FK
    \SLiMS\DB::getInstance()->query(<<<SQL
    CREATE TABLE `child` (
        `id` INT PRIMARY KEY,
        `parent_id` INT,
        FOREIGN KEY (`parent_id`) REFERENCES `parent` (`id`)
    )
    SQL);
}

function down()
{
    // Drop table (FK dropped automatically)
    \SLiMS\DB::getInstance()->query("DROP TABLE IF EXISTS `child`");
}
```

### **4. Data Migration Harus Reversible**

```php
function up()
{
    // Insert configuration
    \SLiMS\DB::getInstance()->query(<<<SQL
    INSERT INTO `setting` (`name`, `value`) 
    VALUES ('my_plugin_setting', 'default_value')
    SQL);
}

function down()
{
    // Delete configuration on rollback
    \SLiMS\DB::getInstance()->query(<<<SQL
    DELETE FROM `setting` WHERE `name` = 'my_plugin_setting'
    SQL);
}
```

---

## 🔍 **Debugging Migration**

### **Check Migration Execution**

```php
// Enable error reporting in migration file
function up()
{
    try {
        \SLiMS\DB::getInstance()->query(<<<SQL
        CREATE TABLE `my_table` (...)
        SQL);
        
        error_log("Migration up() success");
    } catch (Exception $e) {
        error_log("Migration error: " . $e->getMessage());
        throw $e;
    }
}
```

### **Verify Table Creation**

```sql
-- Run in MySQL console
SHOW TABLES LIKE 'library_equipment';
DESCRIBE library_equipment;
SHOW CREATE TABLE library_equipment;
```

---

## 🛡️ **Error Handling in Migrations (Production Pattern)**

### **Why Error Handling?**

Migrations may run multiple times due to:
- Plugin reinstall
- Plugin updates
- Manual database changes
- Testing/development

**Without error handling**: Fatal errors stop SLiMS execution!

---

### **Production Pattern with Try-Catch** ⭐

From working plugins (`bebas-pustaka`, etc.):

```php
use SLiMS\Migration\Migration;

class CreateMyTable extends Migration
{
    function up()
    {
        try {
            \SLiMS\DB::getInstance()->query(
                "CREATE TABLE `my_table` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(255) NOT NULL,
                    PRIMARY KEY (`id`)
                )
                COLLATE='utf8mb4_unicode_ci'
                ENGINE=MyISAM
                ;"
            );
        } catch (\PDOException $e) {
            // Error 1050: Table already exists - safe to ignore
            if ($e->errorInfo[1] != 1050) {
                throw $e;  // Re-throw other errors
            }
        }
    }
    
    function down()
    {
        // Production: Comment out to prevent data loss
        // \SLiMS\DB::getInstance()->query("DROP TABLE IF EXISTS `my_table`;");
    }
}
```

---

### **Common MySQL Error Codes**

| Code | Meaning | Action |
|------|---------|--------|
| `1050` | Table already exists | Ignore (safe) |
| `1060` | Duplicate column name | Ignore (safe) |
| `1061` | Duplicate key name | Ignore (safe) |
| `1091` | Can't DROP (doesn't exist) | Ignore (safe) |
| `1062` | Duplicate entry for key | Check data |
| Others | Real errors | Re-throw |

**Pattern**:
```php
catch (\PDOException $e) {
    if ($e->errorInfo[1] != 1050) {  // Not "table exists" error
        throw $e;  // Re-throw real errors
    }
    // Silently ignore "table exists"
}
```

---

### **down() Method in Production**

**Two Approaches**:

#### **Approach 1: Active DROP (for testing)**
```php
function down()
{
    \SLiMS\DB::getInstance()->query("DROP TABLE IF EXISTS `my_table`;");
}
```

✅ **Use when**: Testing, development, temporary data

#### **Approach 2: Commented (production safe)** ⭐
```php
function down()
{
    // Don't drop table in production - data loss risk!
    // \SLiMS\DB::getInstance()->query("DROP TABLE IF EXISTS `my_table`;");
}
```

✅ **Use when**: Production data, valuable records

**Production Reality**: Many working plugins comment out `down()` to prevent accidental data loss!

---

### **Engine Choice**

**Both are valid in production**:

```php
ENGINE=InnoDB     // Supports foreign keys, transactions
ENGINE=MyISAM     // Faster for read-heavy operations
```

**Choose based on needs**:
- Use `InnoDB` if you need:
  - Foreign key constraints
  - Transaction support (COMMIT/ROLLBACK)
  - Data integrity critical
  
- Use `MyISAM` if you need:
  - Faster reads
  - Full-text search (older MySQL)
  - No foreign keys needed

**Production Examples**:
- `bebas-pustaka`: Uses `MyISAM`
- Most plugins: Use `InnoDB` (default)

---

### **Complete Production Example**

```php
<?php
/**
 * Migration: CreateBebasPustakaTable
 * Based on: working-plugin/bebas-pustaka/migration/
 */

use SLiMS\Migration\Migration;

class CreateBebasPustakaTable extends Migration
{
    function up()
    {
        try {
            \SLiMS\DB::getInstance()->query(
                "CREATE TABLE `bebas_pustaka` (
                    `id` VARCHAR(36) NOT NULL COLLATE 'utf8mb4_unicode_ci',
                    `id_member` VARCHAR(36) NOT NULL COLLATE 'utf8mb4_unicode_ci',
                    `member_name` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
                    `member_id` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
                    `member_email` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
                    `note` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
                    `file` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
                    `created_at` TIMESTAMP NULL DEFAULT current_timestamp(),
                    `updated_at` TIMESTAMP NULL DEFAULT NULL,
                    PRIMARY KEY (`id`) USING BTREE,
                    INDEX `FK_member` (`id_member`) USING BTREE
                )
                COLLATE='utf8mb4_unicode_ci'
                ENGINE=MyISAM
                ;"
            );
        } catch (\PDOException $e) {
            if ($e->errorInfo[1] != 1050) {
                // ignore if already exist
                throw $e;
            }
        }
    }

    function down()
    {
        // \SLiMS\DB::getInstance()->query("DROP TABLE IF EXISTS `bebas_pustaka`;");
    }
}
```

**Key Points**:
- ✅ Try-catch wraps CREATE TABLE
- ✅ Error 1050 (table exists) silently ignored
- ✅ down() commented (production safe)
- ✅ MyISAM engine (valid choice)
- ✅ Production-tested pattern

---

## ✅ **Migration Best Practices**

1. **One Purpose Per Migration**: Satu file = satu tujuan
   ```php
   // ✅ GOOD
   1_CreateEquipmentTable.php
   2_AddStatusColumn.php
   3_InsertSampleData.php
   
   // ❌ BAD
   1_CreateTableAndInsertDataAndAddColumn.php
   ```

2. **Always Use IF EXISTS/IF NOT EXISTS**
   ```php
   DROP TABLE IF EXISTS `my_table`;
   CREATE TABLE IF NOT EXISTS `my_table` (...);
   ```

3. **Use Backticks for Identifiers**
   ```php
   // ✅ CORRECT
   CREATE TABLE `library_equipment` (`id` INT)
   
   // ❌ WRONG (may fail with reserved words)
   CREATE TABLE library_equipment (id INT)
   ```

4. **Specify Engine and Charset**
   ```php
   CREATE TABLE `my_table` (...)
   ENGINE=InnoDB 
   DEFAULT CHARSET=utf8mb4 
   COLLATE=utf8mb4_unicode_ci
   ```

5. **Test Both up() and down()**
   - Activate plugin → Check table created
   - Deactivate plugin → Check table dropped
   - Reactivate plugin → Check table recreated

---

## 🚨 **Common Migration Errors**

### **Error 1: Table Already Exists**

```
Error: Table 'library_equipment' already exists
```

**Solution**:
```php
// Always use IF NOT EXISTS
CREATE TABLE IF NOT EXISTS `library_equipment` (...)
```

### **Error 2: Column Mismatch in down()**

```
Error: Column 'status' doesn't exist
```

**Solution**: Make sure down() exactly reverses up()

```php
function up()
{
    \SLiMS\DB::getInstance()->query("ALTER TABLE `eq` ADD COLUMN `status` VARCHAR(20)");
}

function down()
{
    // Must drop the same column name!
    \SLiMS\DB::getInstance()->query("ALTER TABLE `eq` DROP COLUMN `status`");
}
```

### **Error 3: Foreign Key Constraint Fails**

```
Error: Cannot add foreign key constraint
```

**Solution**: Ensure parent table exists first

```php
// ✅ CORRECT - Create parent first
1_CreateParentTable.php
2_CreateChildTableWithFK.php

// ❌ WRONG - Child before parent
1_CreateChildTableWithFK.php  // FK fails!
2_CreateParentTable.php
```

---

## 📖 **Reference**

- **Official Documentation**: `raw-copas-slims-dev-guide-plugin`
- **Complete Example**: `PLUGIN-CRUD-BEGINNER-GUIDE.md`
- **Quick Reference**: `PLUGIN-QUICK-REFERENCE.md`

---

**Last Updated**: 2025-10-14  
**SLiMS Version**: 9.6.1 Bulian  
**Status**: ✅ Production Ready
