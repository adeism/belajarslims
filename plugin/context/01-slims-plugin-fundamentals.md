# SLiMS Plugin Development - Fundamentals

> **Context File untuk AI Chatbot**  
> **Purpose**: Core knowledge tentang sistem plugin SLiMS  
> **Reference**: raw-copas-slims-dev-guide-plugin, penting.instructions.SIMPLIFIED.md

---

## 🎯 **Core Concepts**

### **1. Apa itu Plugin SLiMS?**

Plugin SLiMS adalah sistem modular yang diperkenalkan sejak **SLiMS Bulian 9.3.0** untuk:
- Mempermudah upgrade SLiMS tanpa merusak kustomisasi
- Memisahkan kode custom dari core system
- Memungkinkan developer extend functionality tanpa edit core

### **2. Lokasi Plugin**

```
/path/to/slims/
└── plugins/                    # Root folder untuk semua plugin
    ├── my-plugin/             # Folder plugin individual
    │   ├── my_plugin.plugin.php    # Registration file (Pattern 1)
    │   │   OR
    │   ├── registrat.plugin.php    # Registration file (Pattern 2)
    │   ├── migration/              # Database migration folder
    │   │   └── 1_CreateTable.php
    │   ├── index.php               # Main interface file
    │   └── assets/                 # CSS, JS, images
    └── another-plugin/
```

**Kedalaman Scanning**: SLiMS memindai plugin hingga **2 tingkat subfolder** (`$deep = 2` di `lib/Plugins.php`):
```
plugins/                        # Root folder plugin
├── plugin.plugin.php          # Tingkat 1 ✅ DETECTED
    ├── my-plugin.plugin.php   # Tingkat 2 ✅ DETECTED
    └── nested-folder/         # Tingkat 2
        └── deeper.plugin.php  # Tingkat 3 ❌ NOT DETECTED
```

---

**File**: `my_plugin.plugin.php`

```php
<?php
/**
 * Plugin Name: My Plugin Name
 * Plugin URI: https://github.com/username/my-plugin
 * Description: Plugin description here
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 */

use SLiMS\Plugins;

// Get plugin instance - NOTE: $plugins (PLURAL)
$plugins = Plugins::getInstance();

// Register menu
$plugins->registerMenu('reporting', 'My Plugin', __DIR__ . '/index.php');
```

**Characteristics**:
- Variable: `$plugins` (plural)
- File naming: `{name}.plugin.php` (any name)
- Most common in documentation

### **Pattern 2: Production Legacy**

**File**: `registrat.plugin.php` (fixed name)

```php
<?php
use SLiMS\Plugins;

// Get plugin instance - NOTE: $plugin (SINGULAR)
$plugin = Plugins::getInstance();

// Register menu
$plugin->registerMenu('reporting', 'My Plugin', __DIR__ . '/index.php');
```

**Characteristics**:
- Variable: `$plugin` (singular)
- File naming: MUST be `registrat.plugin.php`
- Found in production plugins

### **Pattern 3: Modern Static Method** ⭐ NEW!

**File**: `registrat.plugin.php`

```php
<?php
/**
 * Plugin Name: Bebas Pustaka
 * Plugin URI: https://github.com/...
 * Description: Modern plugin with static registration
 * Version: 1.0.0
 */

// No getInstance() call needed!
\SLiMS\Plugins::menu('opac', 'Bebas Pustaka', __DIR__ . '/opac.routes.php');
\SLiMS\Plugins::menu('membership', 'Admin Panel', __DIR__ . '/admin.routes.php');
```

**Characteristics**:
- ✅ No `getInstance()` call (cleaner)
- ✅ Direct static method
- ✅ Multiple menu registration
- ✅ Modern and concise
- ✅ Used in production plugins (bebas-pustaka)

**When to use**:
- New modern plugins (recommended)
- Multiple menu registrations
- Route-based plugins
- Clean architecture preferred

**Production Example**: `working-plugin/psb-feb-ui-plugins/bebas-pustaka/registrat.plugin.php`

**IMPORTANT**: ✅ **ALL THREE PATTERNS ARE VALID AND WORK IN SLIMS!**

---

## 🔧 **Essential Constants**

```php
// Server paths (for PHP require/include)
SB    // Server Base: /path/to/slims/
      // Usage: require_once SB . 'admin/default/session.inc.php';

// Web paths (for HTML <link>, <script>, <img>)
SWB   // Server Web Base: https://domain.com/slims/
      // Usage: <link href="' . SWB . 'plugins/my-plugin/assets/style.css">

// Admin paths
AWB   // Admin Web Base: https://domain.com/slims/admin/
      // Usage: header('Location: ' . AWB . 'index.php?mod=...');

// Database
$dbs  // Global MySQLi object
      // Usage: $result = $dbs->query($sql);
```

**Critical Rules**:
- ✅ Use `SB` for PHP `require`/`include`
- ✅ Use `AWB` for admin redirects
- ❌ NEVER mix server paths with web paths

---

## 📚 **Valid Menu Categories**

Plugin can be registered in these categories:

```php
'bibliography'      // Katalog/bibliografi
'circulation'       // Sirkulasi/peminjaman
'membership'        // Keanggotaan
'master_file'       // Master file
'reporting'         // Laporan (RECOMMENDED untuk custom reports)
'serial_control'    // Kontrol terbitan berkala
'stock_take'        // Stock opname
'system'            // System management
'opac'              // OPAC (public catalog)
```

❌ **INVALID Categories**:
- `'admin'` - This is NOT a valid category!

**Example**:
```php
// ✅ CORRECT
$plugins->registerMenu('reporting', 'My Report', __DIR__ . '/report.php');

// ❌ WRONG - will not appear in menu!
$plugins->registerMenu('admin', 'My Report', __DIR__ . '/report.php');
```

---

## 🎣 **Hook System (Event-Driven Plugins)**

### **What are Hooks?**

Hooks allow plugins to execute code at specific system events **WITHOUT registering menu items**. Perfect for:
- Modifying existing pages
- Adding global CSS/JS
- Event-driven logic
- Background tasks

### **Basic Hook Pattern**

```php
<?php
use SLiMS\Plugins;

// Register hook - no getInstance() needed!
Plugins::hook(Plugins::ADMIN_SESSION_AFTER_START, function () {
    // Your code executes after admin session starts
    echo "Hook executed!";
});
```

### **Available Hook Events**

| Hook Constant | When It Fires | Use Case |
|--------------|---------------|----------|
| `Plugins::ADMIN_SESSION_AFTER_START` | After admin login session starts | Modify admin pages, inject code / assets |
| `Plugins::ADMIN_AFTER_MODULE_LOADED` | After admin module is loaded | Intercept admin module execution |
| `Plugins::CONTENT_BEFORE_LOAD` | Before OPAC content is loaded | Inject custom logic / assets to OPAC |
| `Plugins::CONTENT_AFTER_LOAD` | After OPAC content is rendered | Filter / modify OPAC rendered output |
| `Plugins::CIRCULATION_AFTER_SUCCESSFUL_TRANSACTION` | After loan/return completed | Notifications, logging, receipt generation |
| `Plugins::BIBLIOGRAPHY_BEFORE_SAVE` | Before saving catalog item | Validate / alter bibliographic record |
| `Plugins::MEMBERSHIP_BEFORE_SAVE` | Before saving member data | Validate / enrich member attributes |

### **Real Production Example**

From `working-plugin/psb-feb-ui-plugins/due-date-updater/`:

```php
<?php
Plugins::hook(Plugins::ADMIN_SESSION_AFTER_START, function () {
    // Get call stack to detect current page
    $e = new Exception();
    $trace = $e->getTrace();
    $arr = explode('/admin', $trace[count($trace)-1]['file']);
    
    // Only inject code on loan_list.php page
    if (count($arr) === 2 && $arr[1] === '/modules/circulation/loan_list.php') {
        include_once __DIR__ . '/loan_list.php';
    }
});
```

**How it works**:
1. Hook fires when admin session starts
2. Check current page using call stack
3. If on loan_list.php, inject custom code
4. No menu item created!

### **Multiple Hooks Example**

```php
<?php
use SLiMS\Plugins;

// Hook 1: Run code after admin session starts
Plugins::hook(Plugins::ADMIN_SESSION_AFTER_START, function () {
    // Inject scripts or modify global environment
});

// Hook 2: Intercept OPAC before content load
Plugins::hook(Plugins::CONTENT_BEFORE_LOAD, function () {
    // Custom OPAC initialization
});

// Hook 3: Run action after circulation transaction succeeds
Plugins::hook(Plugins::CIRCULATION_AFTER_SUCCESSFUL_TRANSACTION, function ($data) {
    global $dbs;
    // Log transaction or send notification
});
```

- No user interface needed
- Modifying existing SLiMS pages
- Adding global styles/scripts
- Event-driven logic
- Background processing
- Standalone feature
- User needs to navigate to plugin
- Custom reports/tools

```php
// Pattern A: Hook (no menu)
Plugins::hook(Plugins::ADMIN_HEADER, function () {
});

// Pattern B: Menu Registration (with menu)
$plugins = Plugins::getInstance();
$plugins->registerMenu('reporting', 'My Report', __DIR__ . '/index.php');
// User must click menu
```

### **Production References**

| Plugin | Hook Usage | Purpose |
|--------|------------|---------|
| `due-date-updater` | `ADMIN_SESSION_AFTER_START` | Inject code to loan page |
| `member-area` | `CONTENT_BEFORE_LOAD` | Custom OPAC logic |
| `bebas-pustaka` | Multiple hooks | Various enhancements |

**Full example**: See `working-plugin/psb-feb-ui-plugins/due-date-updater/registrat.plugin.php`

---

## 🏗️ **MVC Pattern for Complex Plugins**

### **What is MVC Pattern in SLiMS?**

For complex plugins with multiple features, SLiMS supports MVC (Model-View-Controller) architecture:

- **Model**: Data layer (database queries, business logic)
- **View**: Presentation layer (HTML templates)
- **Controller**: Request handler (routing, validation)

### **MVC Directory Structure**

From `working-plugin/psb-feb-ui-plugins/bebas-pustaka/`:

```
bebas-pustaka/
├── registrat.plugin.php          # Plugin registration
├── admin.routes.php               # Admin route handler
├── verify.routes.php              # Custom route handler
├── Controllers/                   # Controllers folder
│   ├── BebasPustakaAdminController.php
│   └── BebasPustakaController.php
├── Models/                        # Models folder
│   └── BebasPustaka.php
├── Views/                         # Views folder
│   ├── admin/
│   │   ├── index.php
│   │   └── form.php
│   └── opac/
│       └── list.php
└── migration/                     # Database migrations
    ├── 1_ProdiTable.php
    └── 2_CreateBebasPustakaTable.php
```

### **Step 1: Route-Based Registration**

**File**: `registrat.plugin.php`

```php
<?php
/**
 * Plugin Name: Bebas Pustaka
 * Description: Complex plugin with MVC pattern
 */

// Register multiple menus with route files
\SLiMS\Plugins::menu('opac', 'Bebas Pustaka', __DIR__ . '/opac.routes.php');
\SLiMS\Plugins::menu('membership', 'Admin Panel', __DIR__ . '/admin.routes.php');
```

**Key Points**:
- Uses static method (Pattern 3)
- Points to route files (not direct interface)
- Multiple menu registrations

### **Step 2: Route Handler**

**File**: `admin.routes.php`

```php
<?php
defined('INDEX_AUTH') || die('Direct access not allowed');

global $dbs, $sysconf;
require SB . 'admin/default/session.inc.php';
require SB . 'admin/default/session_check.inc.php';

# REQUIRE COMPOSER AUTOLOAD (if using Composer)
require_once __DIR__ . '/../vendor/autoload.php';

# LOAD CONTROLLER
use Controllers\BebasPustakaAdminController;

# INSTANTIATE CONTROLLER
$controller = new BebasPustakaAdminController($dbs, $sysconf);

# ROUTING
$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'create':
        $controller->create();
        break;
    case 'edit':
        $controller->edit($_GET['id'] ?? null);
        break;
    case 'delete':
        $controller->delete($_GET['id'] ?? null);
        break;
        $controller->index();
        break;
}
```

### **Step 3: Controller**

**File**: `Controllers/BebasPustakaAdminController.php`

```php
<?php

use Models\BebasPustaka;

{
    private $dbs;
    private $sysconf;
    
    public function __construct($dbs, $sysconf)
    {
        $this->dbs = $dbs;
        $this->sysconf = $sysconf;
    
    // Display list
    public function index()
        $model = new BebasPustaka($this->dbs);
        $data = $model->getAll();
        
    }
    
    // Create new record
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new BebasPustaka($this->dbs);
            $model->insert($_POST);
            
            header('Location: ' . $_SERVER['PHP_SELF'] . '?' . http_build_query($_GET));
            exit;
        }
        
        include __DIR__ . '/../Views/admin/form.php';
    }
    
    // Edit record
    public function edit($id)
    {
        $model = new BebasPustaka($this->dbs);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model->update($id, $_POST);
            header('Location: ' . $_SERVER['PHP_SELF'] . '?' . http_build_query(array_diff_key($_GET, ['action' => '', 'id' => ''])));
            exit;
        
        $record = $model->getById($id);
        include __DIR__ . '/../Views/admin/form.php';
    }
}
```

### **Step 4: Model**

**File**: `Models/BebasPustaka.php`

```php
<?php
namespace Models;

class BebasPustaka
{
    
    public function __construct($dbs)
    {
        $this->dbs = $dbs;
    }
    
    // Get all records
    public function getAll()
    {
        $sql = "SELECT * FROM bebas_pustaka ORDER BY created_at DESC";
        $result = $this->dbs->query($sql);
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
        }
        return $data;
    }
    
    // Get by ID
    public function getById($id)
    {
        $sql = "SELECT * FROM bebas_pustaka WHERE id = ?";
        $stmt = $this->dbs->prepare($sql);
        $stmt->bind_param('s', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // Insert new record
    public function insert($data)
    {
        $sql = "INSERT INTO bebas_pustaka (id, member_name, member_email, note) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->dbs->prepare($sql);
        
        $id = uniqid();
        $stmt->bind_param('ssss', 
            $id, 
            $data['member_name'], 
            $data['member_email'], 
            $data['note']
        );
        
        return $stmt->execute();
    }
    
    // Update record
    public function update($id, $data)
    {
        $sql = "UPDATE bebas_pustaka 
                SET member_name = ?, member_email = ?, note = ?
                WHERE id = ?";
        $stmt = $this->dbs->prepare($sql);
        $stmt->bind_param('ssss', 
            $data['member_name'], 
            $data['member_email'], 
            $data['note'],
            $id
        );
        
        return $stmt->execute();
    }
}
```

### **Step 5: View**

**File**: `Views/admin/index.php`

```php
<div class="menuBox">
    <div class="menuBoxInner printIcon">
        <div class="per_title">
            <h2>Bebas Pustaka - Data List</h2>
        </div>
        
        <div class="infoBox">
            <a href="?mod=bebas-pustaka&id=admin&action=create" class="btn btn-primary">
                Add New
            </a>
        </div>
        
        <table class="s-table table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Member Name</th>
                    <th>Email</th>
                    <th>Note</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['member_name']) ?></td>
                    <td><?= htmlspecialchars($row['member_email']) ?></td>
                    <td><?= htmlspecialchars($row['note']) ?></td>
                    <td>
                        <a href="?mod=bebas-pustaka&id=admin&action=edit&id=<?= $row['id'] ?>">Edit</a>
                        |
                        <a href="?mod=bebas-pustaka&id=admin&action=delete&id=<?= $row['id'] ?>" 
                           onclick="return confirm('Delete?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
```

### **When to Use MVC Pattern**

✅ **Use MVC for**:
- Large complex plugins (10+ files)
- Multiple related features
- Team development
- Code reusability
- Maintainability important

❌ **Don't use MVC for**:
- Simple one-page plugins
- Quick reports
- POC/prototypes
- Single feature plugins

### **Benefits of MVC**

1. **Separation of Concerns**: Logic, data, and presentation separated
2. **Reusability**: Controllers and models can be reused
3. **Testability**: Easier to write unit tests
4. **Maintainability**: Changes isolated to specific layers
5. **Team Development**: Multiple developers can work simultaneously

### **Production Examples**

| Plugin | MVC Usage | Complexity |
|--------|-----------|------------|
| `bebas-pustaka` | Full MVC + Routes | High (complex) |
| `member-area` | Partial MVC | Medium |
| `metadata-switcher` | None (simple) | Low (simple CRUD) |

**Full example**: See `working-plugin/psb-feb-ui-plugins/bebas-pustaka/`

---

## � **Using Composer in Plugins**

### **What is Composer?**

Composer is PHP's dependency manager, allowing plugins to use modern PHP packages and libraries.

### **Why Use Composer in SLiMS Plugins?**

✅ **Benefits**:
- Use modern PHP packages (logging, PDF, Excel, etc.)
- Laravel Eloquent ORM for elegant database queries
- Shared libraries across multiple plugins
- PSR-4 autoloading for organized code
- Better dependency management

### **Basic Composer Setup**

#### **Step 1: Create `composer.json`**

**Location**: Parent folder (shared across plugins)

```json
{
    "name": "myorganization/slims-plugins",
    "description": "SLiMS Plugins Collection",
    "type": "library",
    "require": {
        "php": ">=7.4",
        "illuminate/database": "^8.0",
        "monolog/monolog": "^2.0"
    },
    "autoload": {
        "psr-4": {
            "Controllers\\": "lib/Controllers/",
            "Models\\": "lib/Models/",
            "Helpers\\": "lib/Helpers/"
        }
    }
}
```

#### **Step 2: Install Dependencies**

```bash
cd /path/to/slims/plugins/my-plugins-folder
composer install
```

This creates:
```
my-plugins-folder/
├── composer.json
├── composer.lock
├── vendor/                    # Composer packages
│   ├── autoload.php          # Autoloader
│   ├── illuminate/           # Laravel packages
│   └── monolog/              # Logging package
└── lib/                      # Shared code
    ├── Controllers/
    ├── Models/
    └── Helpers/
```

#### **Step 3: Load Autoloader in Plugin**

**File**: `registrat.plugin.php` or route file

```php
<?php
# REQUIRE COMPOSER AUTOLOAD
require_once __DIR__ . '/../vendor/autoload.php';

use SLiMS\Plugins;

// Now you can use Composer packages!
$plugins = Plugins::getInstance();
$plugins->registerMenu('reporting', 'My Plugin', __DIR__ . '/index.php');
```

### **Using Eloquent ORM**

#### **Setup Eloquent**

```php
<?php
# REQUIRE COMPOSER AUTOLOAD
require_once __DIR__ . '/../vendor/autoload.php';

# REQUIRE ELOQUENT MODELS BOOTSTRAP
require_once __DIR__ . '/../vendor/idoalit/slims-eloquent-models/bootstrap.php';

use SLiMS\Eloquent\Member;
use SLiMS\Eloquent\Biblio;
```

#### **Query with Eloquent (Elegant!)**

```php
// Get all active members
$members = Member::where('is_active', 1)
                 ->orderBy('member_name', 'asc')
                 ->get();

foreach ($members as $member) {
    echo $member->member_name . ' - ' . $member->email . '<br>';
}

// Get member by ID
$member = Member::find('M001');
echo $member->member_name;

// Update member
$member = Member::find('M001');
$member->email = 'newemail@example.com';
$member->save();

// Create new member
$newMember = new Member();
$newMember->member_id = 'M999';
$newMember->member_name = 'John Doe';
$newMember->email = 'john@example.com';
$newMember->save();
```

#### **Compare: MySQLi vs Eloquent**

**MySQLi (Traditional)**:
```php
// Complex and verbose
$sql = "SELECT * FROM member WHERE is_active = ? ORDER BY member_name ASC";
$stmt = $dbs->prepare($sql);
$stmt->bind_param('i', $is_active);
$stmt->execute();
$result = $stmt->get_result();
$members = [];
while ($row = $result->fetch_assoc()) {
    $members[] = $row;
}
```

**Eloquent (Modern)**:
```php
// Clean and elegant
$members = Member::where('is_active', 1)
                 ->orderBy('member_name', 'asc')
                 ->get();
```

### **Shared Libraries Across Plugins**

#### **Create Shared Helper**

**File**: `lib/Helpers/DateHelper.php`

```php
<?php
namespace Helpers;

class DateHelper
{
    public static function formatIndonesian($date)
    {
        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        
        $date = date_create($date);
        $day = date_format($date, 'd');
        $month = $months[date_format($date, 'm')];
        $year = date_format($date, 'Y');
        
        return "$day $month $year";
    }
}
```

#### **Use in Multiple Plugins**

**Plugin A**:
```php
require_once __DIR__ . '/../vendor/autoload.php';

use Helpers\DateHelper;

echo DateHelper::formatIndonesian('2025-01-10');
// Output: 10 Januari 2025
```

**Plugin B** (same helper!):
```php
require_once __DIR__ . '/../vendor/autoload.php';

use Helpers\DateHelper;

$formatted = DateHelper::formatIndonesian($loan_date);
```

### **Popular Packages for SLiMS Plugins**

| Package | Purpose | Install |
|---------|---------|---------|
| `illuminate/database` | Laravel Eloquent ORM | `composer require illuminate/database` |
| `monolog/monolog` | Logging | `composer require monolog/monolog` |
| `phpoffice/phpspreadsheet` | Excel export | `composer require phpoffice/phpspreadsheet` |
| `mpdf/mpdf` | PDF generation | `composer require mpdf/mpdf` |
| `guzzlehttp/guzzle` | HTTP client | `composer require guzzlehttp/guzzle` |

### **Production Example**

From `working-plugin/psb-feb-ui-plugins/member-area/registrat.plugin.php`:

```php
<?php
/**
 * Plugin Name: Member Area
 * Description: Complex plugin using Composer
 */

# REQUIRE COMPOSER AUTOLOAD
require_once __DIR__ . '/../vendor/autoload.php';

# REQUIRE GLOBAL FUNCTION
require_once __DIR__ . '/../lib/function.php';

# REQUIRE ELOQUENT MODELS
require_once __DIR__ . '/../vendor/idoalit/slims-eloquent-models/bootstrap.php';

use SLiMS\Plugins;

$plugin = Plugins::getInstance();
$plugin->registerMenu('opac', 'Member Area', __DIR__ . '/index.php');
```

### **Directory Structure with Composer**

```
plugins/
└── my-plugins-collection/
    ├── composer.json              # Composer config
    ├── composer.lock              # Dependency lock
    ├── vendor/                    # Composer packages
    │   ├── autoload.php
    │   ├── illuminate/
    │   └── monolog/
    ├── lib/                       # Shared code
    │   ├── Controllers/
    │   ├── Models/
    │   └── Helpers/
    ├── plugin-1/                  # Plugin 1
    │   ├── registrat.plugin.php
    │   └── index.php
    └── plugin-2/                  # Plugin 2
        ├── registrat.plugin.php
        └── index.php
```

### **When to Use Composer**

✅ **Use Composer when**:
- Need modern PHP packages
- Want elegant Eloquent ORM
- Multiple plugins sharing code
- Complex business logic
- Team development

❌ **Don't use Composer when**:
- Simple one-file plugin
- No external dependencies
- Quick POC/prototype
- Server has no Composer access

### **Best Practices**

1. **One composer.json per plugin collection** (not per plugin)
2. **Commit `composer.lock`** to version control
3. **Don't commit `vendor/`** folder (too large)
4. **Use PSR-4 autoloading** for organized code
5. **Document dependencies** in README

### **Production References**

| Plugin | Composer Usage | Packages Used |
|--------|----------------|---------------|
| `member-area` | ✅ Yes | Eloquent, custom helpers |
| `bebas-pustaka` | ✅ Yes | Eloquent, shared libs |
| `metadata-switcher` | ❌ No | Simple plugin |

**Full example**: See `working-plugin/psb-feb-ui-plugins/member-area/`

---

## �🗄️ **Core Database Tables**

```sql
-- Bibliographic records
biblio
  - biblio_id (PK)
  - title
  - author
  - isbn
  - publisher

-- Physical items
item
  - item_code (PK)
  - biblio_id (FK → biblio)
  - location
  - call_number

-- Library members
member
  - member_id (PK)
  - member_name
  - email
  - member_type_id (FK)

-- Transactions
loan
  - loan_id (PK)
  - member_id (FK → member)
  - item_code (FK → item)
  - loan_date
  - due_date
  - return_date

-- System users
user
  - user_id (PK)
  - username
  - realname
  - privileges
```

---

## 🔐 **Authentication Pattern (MANDATORY)**

**Every admin interface file MUST start with**:

```php
<?php
// Step 1: Protect direct access
defined('INDEX_AUTH') || die('Direct access not allowed');

// Step 2: Access global objects
global $dbs, $sysconf;

// Step 3: Load session
require SB . 'admin/default/session.inc.php';
require SB . 'admin/default/session_check.inc.php';

// Step 4: Check privileges
$can_read = utility::havePrivilege('reporting', 'r');
$can_write = utility::havePrivilege('reporting', 'w');

if (!$can_read) {
    die('<div class="errorBox">' . __('Access denied!') . '</div>');
}

// Now safe to use:
// - $dbs (database object)
// - $sysconf (system configuration)
// - Session variables
```

**Privilege Levels (`utility::havePrivilege`)**:
- `'r'` - Read only (melihat data)
- `'w'` - Write / Read-Write (menambah, mengubah, menghapus data)

---

## 📊 **Database Access - Two Methods**

### **Method 1: MySQLi (Global $dbs)**

```php
// Simple query
$query = $dbs->query("SELECT * FROM biblio WHERE biblio_id = 1");
$data = $query->fetch_assoc();

$stmt = $dbs->prepare($sql);
$search = '%' . $_GET['search'] . '%';
$stmt->bind_param('s', $search);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    // Process data
}
```

**Note**: Must use `global $dbs;` inside functions

### **Method 2: PDO (SLiMS\DB)**

```php
use SLiMS\DB;

// Simple query
$query = DB::getInstance()->query("SELECT * FROM biblio WHERE biblio_id = 1");
$data = $query->fetch(PDO::FETCH_ASSOC);

// Prepared statement
$query = DB::getInstance()->prepare("SELECT * FROM biblio WHERE title LIKE ?");
$query->execute(['%' . $_GET['search'] . '%']);

```

**Note**: No need for `global` keyword

---

## 🎨 **Admin Template Classes (USE THESE!)**

```php
// Tables
echo '<table class="s-table table table-sm table-bordered mb-0">';

// Alternating row colors
echo '<tr class="alterCellPrinted">...</tr>';   // Even rows
echo '<tr class="alterCellPrinted2">...</tr>';  // Odd rows

// Buttons
echo '<button class="s-btn btn btn-primary">Save</button>';
echo '<button class="s-btn btn btn-default">Cancel</button>';

// Forms
echo '<div class="form-group">';
echo '<label>Field Name</label>';
echo '<input type="text" class="form-control">';
echo '</div>';

// Messages
echo '<div class="infoBox">Success message</div>';
echo '<div class="errorBox">Error message</div>';
echo '<div class="warningBox">Warning message</div>';
```

---

## 🔒 **Security Rules (NON-NEGOTIABLE!)**

### **1. SQL Injection Prevention**

```php
// ✅ CORRECT - Prepared statements
$sql = "SELECT * FROM biblio WHERE biblio_id = ?";
$stmt = $dbs->prepare($sql);
$stmt->bind_param('i', $biblio_id);
$stmt->execute();

// ❌ WRONG - SQL injection vulnerable!
$sql = "SELECT * FROM biblio WHERE biblio_id = $biblio_id";
$result = $dbs->query($sql);
```

### **2. XSS Prevention**

```php
// ✅ CORRECT - Escape output
echo '<td>' . htmlspecialchars($row['title']) . '</td>';

// ❌ WRONG - XSS vulnerable!
echo '<td>' . $row['title'] . '</td>';
```

### **3. Input Validation**

```php
// Type casting
$id = (int)$_GET['id'];
$price = (float)$_POST['price'];

// Array filtering
$ids = array_filter($_POST['ids'], 'is_numeric');

// String trimming
$name = trim($_POST['name']);
```

---

## 🚫 **Common Mistakes to AVOID**

1. ❌ Using `'admin'` as menu category
2. ❌ Using `DB_ACCESS` constant (deprecated - use `INDEX_AUTH`)
3. ❌ Direct SQL without prepared statements
4. ❌ Output without `htmlspecialchars()`
5. ❌ Relative paths for CSS/JS (use `SWB` constant!)
6. ❌ Editing `/lib/` directory (READ-ONLY!)
7. ❌ Missing privilege checking
8. ❌ Forgetting `mod`, `id`, `sec` parameters in URLs

---

## ✅ **Quick Checklist for Plugin Files**

### **Registration File (*.plugin.php)**
- [ ] Has proper plugin header comment
- [ ] Uses `use SLiMS\Plugins;`
- [ ] Correct variable name (`$plugins` or `$plugin`)
- [ ] Valid menu category
- [ ] Correct file path with `__DIR__`

### **Interface File (index.php, etc.)**
- [ ] Defines `INDEX_AUTH`
- [ ] Loads session files
- [ ] Checks privileges
- [ ] Uses prepared statements
- [ ] Escapes all output with `htmlspecialchars()`
- [ ] Maintains plugin context (`mod`, `id`, `sec`)

---

## 📖 **Reference Files in Repository**

1. **raw-copas-slims-dev-guide-plugin** - Official documentation
2. **penting.instructions.SIMPLIFIED.md** - Quick reference
3. **PLUGIN-CRUD-BEGINNER-GUIDE.md** - Complete CRUD tutorial
4. **PLUGIN-QUICK-REFERENCE.md** - Cheat sheet
5. **PLUGIN-ERROR-TROUBLESHOOTING-GUIDE.md** - Error solutions

---

**Last Updated**: 2025-10-14  
**SLiMS Version**: 9.6.1 Bulian  
**Status**: ✅ Production Ready
