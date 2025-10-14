# SLiMS Security & Best Practices

> **Context File untuk AI Chatbot**  
> **Purpose**: Security rules dan best practices untuk plugin development  
> **Priority**: 🔴 CRITICAL - MUST FOLLOW!

---

## 🔒 **Security Fundamentals**

### **The Three Pillars of Security**

1. **Authentication** - Verify user identity
2. **Authorization** - Check user permissions
3. **Input Validation** - Sanitize and validate all input

**RULE**: ❌ **NEVER skip any of these three!**

---

## 🛡️ **1. Authentication Pattern (MANDATORY)**

### **Every Admin Interface MUST Start With:**

```php
<?php
/**
 * SECURITY LAYER 1: Authentication
 * Ensures only logged-in users can access
 */

// Step 1: Define authentication constant (if not defined)
if (!defined('INDEX_AUTH')) {
    define('INDEX_AUTH', '1');
}

// Step 2: Access global objects
global $dbs, $sysconf;

// Step 3: Start session and check authentication
require SB . 'admin/default/session.inc.php';
require SB . 'admin/default/session_check.inc.php';

// Step 4: Now you have access to:
// - $dbs (database connection)
// - $sysconf (system configuration)
// - $_SESSION['uid'] (user ID)
// - $_SESSION['username']
```

### **⚠️ Common Mistakes**

```php
// ❌ WRONG - Using deprecated constant
define('DB_ACCESS', '1');  // Old SLiMS 8 style

// ✅ CORRECT - Modern SLiMS 9
if (!defined('INDEX_AUTH')) {
    define('INDEX_AUTH', '1');
}
global $dbs, $sysconf;

// ❌ WRONG - No authentication check
// File starts directly with HTML/PHP code

// ✅ CORRECT - Authentication first
if (!defined('INDEX_AUTH')) {
    define('INDEX_AUTH', '1');
}
global $dbs, $sysconf;
require SB . 'admin/default/session.inc.php';
// ... then your code
```

---

## 🔐 **2. Authorization (Privilege Checking)**

### **Check User Permissions**

```php
/**
 * SECURITY LAYER 2: Authorization
 * Ensures user has required privileges
 */

// Check privileges using utility class
$can_read = utility::havePrivilege('reporting', 'r');
$can_write = utility::havePrivilege('reporting', 'w');

// Deny access if no read permission
if (!$can_read) {
    die('<div class="errorBox">' . __('You do not have permission to access this module!') . '</div>');
}

// Show form only if user can write
if ($can_write) {
    // Display add/edit/delete buttons
    echo '<button>Add New</button>';
    echo '<button>Delete</button>';
} else {
    // Read-only view
    echo '<p>You have read-only access</p>';
}
```

### **Privilege Levels**

| Level | Description | Can Do |
|-------|-------------|--------|
| `'r'` | Read only | View data, generate reports |
| `'w'` | Write | Read + Add/Edit/Delete |
| `'rw'` | Read-Write | Same as `'w'` |

### **Module Categories**

```php
'bibliography'      // Katalog/bibliografi
'circulation'       // Sirkulasi/peminjaman
'membership'        // Keanggotaan
'master_file'       // Master file
'reporting'         // Laporan (most common for plugins)
'serial_control'    // Kontrol terbitan berkala
'stock_take'        // Stock opname
'system'            // System management
```

**Example**: Check multiple privileges

```php
$can_read_biblio = utility::havePrivilege('bibliography', 'r');
$can_write_circulation = utility::havePrivilege('circulation', 'w');
$can_access_system = utility::havePrivilege('system', 'r');

if (!$can_read_biblio && !$can_write_circulation) {
    die('Access denied! You need bibliography or circulation privileges.');
}
```

---

## 🛡️ **3. SQL Injection Prevention (CRITICAL!)**

### **ALWAYS Use Prepared Statements**

```php
/**
 * SECURITY LAYER 3: SQL Injection Prevention
 * NEVER trust user input in SQL queries!
 */

// ✅ CORRECT - Prepared statement (MySQLi)
$search = $_GET['search'];
$sql = "SELECT * FROM biblio WHERE title LIKE ?";
$stmt = $dbs->prepare($sql);
$search_param = '%' . $search . '%';
$stmt->bind_param('s', $search_param);
$stmt->execute();
$result = $stmt->get_result();

// ✅ CORRECT - Prepared statement (PDO)
use SLiMS\DB;
$search = $_GET['search'];
$sql = "SELECT * FROM biblio WHERE title LIKE ?";
$stmt = DB::getInstance()->prepare($sql);
$stmt->execute(['%' . $search . '%']);
$result = $stmt->fetchAll();

// ❌ WRONG - SQL Injection vulnerable!
$search = $_GET['search'];
$sql = "SELECT * FROM biblio WHERE title LIKE '%$search%'";
$result = $dbs->query($sql);
// Attacker can inject: ' OR '1'='1
```

### **Parameter Binding Types**

```php
// Integer
$stmt->bind_param('i', $biblio_id);

// String
$stmt->bind_param('s', $title);

// Multiple parameters
$stmt->bind_param('iss', $biblio_id, $title, $author);
// i = integer, s = string, s = string
```

### **Type Casting for Extra Safety**

```php
// Force integer type
$id = (int)$_GET['id'];
$sql = "SELECT * FROM biblio WHERE biblio_id = ?";
$stmt->bind_param('i', $id);

// Force float type
$price = (float)$_POST['price'];

// Force string and trim
$title = trim((string)$_POST['title']);
```

---

## 🛡️ **4. XSS Prevention (CRITICAL!)**

### **ALWAYS Escape Output**

```php
/**
 * SECURITY LAYER 4: XSS Prevention
 * NEVER output user data without escaping!
 */

// ✅ CORRECT - Escape HTML special characters
echo '<td>' . htmlspecialchars($row['title']) . '</td>';

// ✅ CORRECT - Escape in attributes
echo '<input type="text" value="' . htmlspecialchars($row['title']) . '">';

// ✅ CORRECT - Escape in URL
echo '<a href="detail.php?id=' . urlencode($row['biblio_id']) . '">View</a>';

// ❌ WRONG - XSS vulnerable!
echo '<td>' . $row['title'] . '</td>';
// Attacker can inject: <script>alert('XSS')</script>

// ❌ WRONG - No escaping in form
echo '<input type="text" value="' . $row['title'] . '">';
// Attacker can inject: " onclick="alert('XSS')
```

### **htmlspecialchars() Parameters**

```php
// Basic usage (good for most cases)
htmlspecialchars($text);

// Full specification
htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
// ENT_QUOTES: Escapes both " and '
// UTF-8: Character encoding
```

### **When to Use Each Function**

```php
// HTML content
htmlspecialchars($text)
// Converts: < > & " '

// URL parameters
urlencode($text)
// Converts: spaces, special chars to %XX

// JavaScript strings
json_encode($text)
// Escapes: quotes, newlines, backslashes
```

---

## 🛡️ **5. CSRF Protection**

### **Use POST for State-Changing Operations**

```php
// ❌ WRONG - GET request for delete
<a href="delete.php?id=5">Delete</a>

// ✅ CORRECT - POST request with form
<form method="post" action="delete.php">
    <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
    <button type="submit" name="action" value="delete">Delete</button>
</form>
```

### **Verify Request Method**

```php
// Only accept POST for write operations
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request method');
}

// Only accept expected actions
$allowed_actions = ['add', 'edit', 'delete'];
if (!in_array($_POST['action'], $allowed_actions)) {
    die('Invalid action');
}
```

---

## 🛡️ **6. File Upload Security**

### **Validate File Uploads**

```php
// Check if file was uploaded
if (!isset($_FILES['attachment']) || $_FILES['attachment']['error'] !== UPLOAD_ERR_OK) {
    die('File upload failed');
}

$file = $_FILES['attachment'];

// Validate file type
$allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime_type, $allowed_types)) {
    die('Invalid file type. Only JPEG, PNG, and PDF allowed.');
}

// Validate file size (max 2MB)
$max_size = 2 * 1024 * 1024;
if ($file['size'] > $max_size) {
    die('File too large. Maximum 2MB allowed.');
}

// Generate safe filename
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$safe_name = uniqid() . '.' . $ext;
$upload_path = SB . 'files/plugin_uploads/' . $safe_name;

// Move file
move_uploaded_file($file['tmp_name'], $upload_path);
```

---

## ✅ **Complete Security Checklist**

### **For Every Admin Interface File**

```php
<?php
// ✅ 1. Authentication
if (!defined('INDEX_AUTH')) {
    define('INDEX_AUTH', '1');
}
global $dbs, $sysconf;
require SB . 'admin/default/session.inc.php';
require SB . 'admin/default/session_check.inc.php';

// ✅ 2. Authorization
$can_read = utility::havePrivilege('reporting', 'r');
if (!$can_read) {
    die('<div class="errorBox">Access denied!</div>');
}

// ✅ 3. SQL Injection Prevention
$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM biblio WHERE title LIKE ?";
$stmt = $dbs->prepare($sql);
$stmt->bind_param('s', $search);
$stmt->execute();
$result = $stmt->get_result();

// ✅ 4. XSS Prevention
while ($row = $result->fetch_assoc()) {
    echo '<td>' . htmlspecialchars($row['title']) . '</td>';
}

// ✅ 5. CSRF Protection (for forms)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form
}
?>
```

### **Before Deployment Checklist**

- [ ] All admin files define `INDEX_AUTH`
- [ ] All admin files check privileges
- [ ] All SQL queries use prepared statements
- [ ] All output uses `htmlspecialchars()`
- [ ] All forms use POST for state changes
- [ ] All user input is validated and sanitized
- [ ] No sensitive data in error messages
- [ ] No debug code in production
- [ ] File uploads are validated (if any)
- [ ] Paths use constants (SB, SWB, AWB)

---

## 🚨 **Common Security Vulnerabilities**

### **Vulnerability 1: SQL Injection**

```php
// ❌ VULNERABLE CODE
$id = $_GET['id'];
$sql = "SELECT * FROM biblio WHERE biblio_id = $id";
$result = $dbs->query($sql);

// Attack: ?id=1 OR 1=1
// Result: Returns ALL records!

// ✅ FIX
$id = (int)$_GET['id'];
$sql = "SELECT * FROM biblio WHERE biblio_id = ?";
$stmt = $dbs->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
```

### **Vulnerability 2: XSS**

```php
// ❌ VULNERABLE CODE
$username = $_GET['username'];
echo "Welcome, $username!";

// Attack: ?username=<script>alert('XSS')</script>
// Result: Script executes!

// ✅ FIX
$username = $_GET['username'];
echo "Welcome, " . htmlspecialchars($username) . "!";
```

### **Vulnerability 3: Directory Traversal**

```php
// ❌ VULNERABLE CODE
$file = $_GET['file'];
include("templates/$file.php");

// Attack: ?file=../../../etc/passwd
// Result: Can read system files!

// ✅ FIX
$file = basename($_GET['file']); // Remove path components
$allowed_files = ['template1', 'template2', 'template3'];
if (in_array($file, $allowed_files)) {
    include("templates/$file.php");
}
```

### **Vulnerability 4: Unvalidated Redirects**

```php
// ❌ VULNERABLE CODE
$url = $_GET['redirect'];
header("Location: $url");

// Attack: ?redirect=http://malicious-site.com
// Result: User redirected to malicious site!

// ✅ FIX
$allowed_urls = [
    AWB . 'modules.php',
    AWB . 'index.php'
];
$url = $_GET['redirect'];
if (in_array($url, $allowed_urls)) {
    header("Location: $url");
} else {
    header("Location: " . AWB . 'index.php');
}
```

---

## 💡 **Best Practices Summary**

### **DO's** ✅

1. **ALWAYS** use prepared statements for SQL
2. **ALWAYS** escape output with `htmlspecialchars()`
3. **ALWAYS** validate and sanitize user input
4. **ALWAYS** check user privileges
5. **ALWAYS** use POST for state-changing operations
6. **ALWAYS** use constants (SB, SWB, AWB) for paths
7. **ALWAYS** handle errors gracefully
8. **ALWAYS** log security-relevant events

### **DON'Ts** ❌

1. **NEVER** trust user input
2. **NEVER** concatenate user input in SQL
3. **NEVER** output user data without escaping
4. **NEVER** expose sensitive information in errors
5. **NEVER** use GET for delete/update operations
6. **NEVER** use relative paths for includes
7. **NEVER** store passwords in plain text
8. **NEVER** disable error reporting in production

---

## 📋 **Security Testing Checklist**

### **Manual Testing**

```bash
# Test SQL injection
?id=1' OR '1'='1
?search=%' UNION SELECT password FROM user --

# Test XSS
?name=<script>alert('XSS')</script>
?comment=<img src=x onerror="alert('XSS')">

# Test directory traversal
?file=../../../etc/passwd
?template=../../../../sysconfig.inc.php

# Test authentication bypass
# Access admin pages without logging in
```

### **Code Review Checklist**

```bash
# Search for vulnerable patterns
grep -r "query.*\$_GET" .
grep -r "query.*\$_POST" .
grep -r "echo.*\$_" .
grep -r "include.*\$_" .
grep -r "header.*Location.*\$_" .
```

---

## 📖 **Additional Resources**

- **OWASP Top 10**: https://owasp.org/www-project-top-ten/
- **PHP Security Cheatsheet**: https://cheatsheetseries.owasp.org/cheatsheets/PHP_Configuration_Cheat_Sheet.html
- **SQL Injection Prevention**: https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html

---

## 🎓 **Learning Path**

### **Day 1: Understand Threats**
- Read about SQL injection
- Read about XSS
- Understand authentication vs authorization

### **Day 2: Learn Defenses**
- Practice with prepared statements
- Practice with htmlspecialchars()
- Implement privilege checking

### **Day 3: Apply to Code**
- Review existing plugins
- Apply security patterns
- Test for vulnerabilities

---

**Last Updated**: 2025-10-14  
**SLiMS Version**: 9.6.1 Bulian  
**Priority**: 🔴 CRITICAL  
**Status**: ✅ Production Ready
