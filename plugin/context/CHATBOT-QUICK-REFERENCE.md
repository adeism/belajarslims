# 🤖 Quick Chatbot Instruction - SLiMS Plugin Context

**For**: AI Assistants (GitHub Copilot, ChatGPT, Claude, Gemini)  
**Purpose**: Quick reference to use context files effectively  
**Version**: SLiMS 9.6.1 Bulian

---

## ⚡ Quick Start (30 seconds)

### **Step 1: Load Context**
```
ALWAYS start with: README.md + 03-security.md
Then add based on request type (see table below)
```

### **Step 2: Generate Code**
```
✅ DO: Use patterns from context files EXACTLY
✅ DO: Include security (prepared statements + htmlspecialchars)
✅ DO: Use INDEX_AUTH (NOT DB_ACCESS)
✅ DO: Use valid menu categories (NOT 'admin')

❌ DON'T: Invent new patterns
❌ DON'T: Skip security measures
❌ DON'T: Use relative paths for CSS/JS
```

### **Step 3: Validate**
```
Check:
- Authentication (INDEX_AUTH)
- Prepared statements for SQL
- Output escaping (htmlspecialchars)
- Valid menu category
- Constants (SB, SWB, AWB)
```

---

## 📊 Request Type → Context File Mapping

| User Asks | Load These Files | Generate From |
|-----------|------------------|---------------|
| "Create plugin" | `01-fundamentals.md` | Section 2 (3 patterns) |
| "Report plugin" | `04-iframe.md` + `01` | Section 2 (iframe) |
| "Database migration" | `02-migration.md` | Sections 1-6 |
| "Add/edit data" | `05-crud.md` + `03` | Sections 2-4 |
| "Security issue" | `03-security.md` | ALL sections |
| "Plugin error" | `06-errors.md` | Match error type |
| "Use hooks" | `01-fundamentals.md` | Section 8 |
| "Use MVC" | `01-fundamentals.md` | Section 9 |
| "Use Composer" | `01-fundamentals.md` | Section 11 |

---

## 🔥 Critical Rules (MEMORIZE!)

### **Authentication Pattern**
```php
// ✅ CORRECT - Always use this
define('INDEX_AUTH', '1');  // NOT DB_ACCESS!
require_once SB . 'admin/default/session.inc.php';
require_once SB . 'admin/default/session_check.inc.php';
```

### **Menu Registration (3 Valid Patterns)**
```php
// Pattern 1: Official (RECOMMENDED)
$plugins = Plugins::getInstance();
$plugins->registerMenu('reporting', 'Plugin Name', __DIR__ . '/admin.php');

// Pattern 2: Legacy (Production)
$plugin = Plugins::getInstance();
$plugin->registerMenu('reporting', 'Plugin Name', __DIR__ . '/admin.php');

// Pattern 3: Static (Modern)
\SLiMS\Plugins::menu('reporting', 'Plugin Name', __DIR__ . '/admin.php');
```

### **Valid Menu Categories**
```
✅ VALID: bibliography, circulation, membership, master_file, 
          reporting, serial_control, stock_take, system, opac

❌ INVALID: admin (will NOT work!)
```

### **Security Pattern (MANDATORY)**
```php
// Database query - ALWAYS use prepared statements
$sql = "SELECT * FROM biblio WHERE biblio_id = ?";
$stmt = $dbs->prepare($sql);
$stmt->bind_param('i', $biblio_id);
$stmt->execute();
$result = $stmt->get_result();

// Output - ALWAYS escape
while ($row = $result->fetch_assoc()) {
    echo '<td>' . htmlspecialchars($row['title']) . '</td>';
}
```

### **Constants (CRITICAL!)**
```php
SB    // Server Base: /path/to/slims/ (for PHP require)
SWB   // Server Web Base: https://domain.com/slims/ (for CSS/JS/images)
AWB   // Admin Web Base: https://domain.com/slims/admin/ (admin URLs)
$dbs  // Global database object (ALWAYS use this)
```

---

## 🚨 Common Errors & Quick Fixes

| Error Symptom | Load File | Fix From |
|---------------|-----------|----------|
| Plugin not found | `06-errors.md` | Section 1 |
| Form opens new tab | `04-iframe.md` | Section 2 |
| CSS 404 error | `06-errors.md` | Section 3 |
| SQL error | `03-security.md` | Section 2 |
| Access denied | `03-security.md` | Section 1 |
| Column mismatch | `06-errors.md` | Section 6 |

---

## 💡 Response Templates

### **Simple Request** (1-2 minutes response)
```
1. Show code snippet from context
2. One security note
3. Reference: "See 01-fundamentals.md Section 2"
```

### **Complex Request** (5-10 minutes response)
```
1. Explain concept
2. Complete working code with ALL security
3. Common pitfalls from 06-errors.md
4. References: Multiple context sections
```

### **Debugging Request** (focus on fix)
```
1. Load 06-errors.md first
2. Match error pattern
3. Show exact fix
4. Explain root cause
5. Prevention tips
```

---

## ✅ Code Generation Checklist

Before sending code to user:

```
[ ] define('INDEX_AUTH', '1') present?
[ ] Session includes present?
[ ] Privilege checking present?
[ ] Prepared statements for ALL queries?
[ ] htmlspecialchars() for ALL output?
[ ] Valid menu category (NOT 'admin')?
[ ] Correct constants (SB/SWB/AWB)?
[ ] Global $dbs declared?
[ ] No relative paths for assets?
[ ] Error handling included?
```

---

## 📚 Context File Quick Reference

```
README.md          → Start here, file index
01-fundamentals    → Plugin patterns (3 types), hooks, MVC, Composer
02-migration       → Database migrations with error handling
03-security        → Security rules (ALWAYS load this!)
04-iframe          → Report plugin UI pattern
05-crud            → Add/edit/delete operations
06-errors          → Troubleshooting guide
```

---

## 🎯 Your Mission

**Provide**: Secure, production-ready SLiMS plugin code  
**Never**: Compromise on security or invent patterns  
**Always**: Reference context file sections  
**Success**: User gets working code on first try with zero vulnerabilities

---

## 🚀 Example: Complete Plugin (30 seconds to generate)

```php
// File: my-plugin.plugin.php
<?php
/**
 * Plugin Name: Quick Report
 * Description: Example report plugin
 * Version: 1.0.0
 */
use SLiMS\Plugins;

// Pattern 3: Modern Static (RECOMMENDED)
\SLiMS\Plugins::menu('reporting', 'Quick Report', __DIR__ . '/admin.php');
```

```php
// File: admin.php
<?php
// Authentication (MANDATORY)
define('INDEX_AUTH', '1');
require_once SB . 'admin/default/session.inc.php';
require_once SB . 'admin/default/session_check.inc.php';

// Privilege check
$can_read = utility::havePrivilege('reporting', 'r');
if (!$can_read) {
    die('<div class="errorBox">' . __('Access denied!') . '</div>');
}

// Database query (SECURE)
global $dbs;
$sql = "SELECT biblio_id, title FROM biblio LIMIT 10";
$result = $dbs->query($sql);

// Output (ESCAPED)
echo '<table class="s-table table">';
echo '<thead><tr><th>ID</th><th>Title</th></tr></thead>';
echo '<tbody>';
while ($row = $result->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($row['biblio_id']) . '</td>';
    echo '<td>' . htmlspecialchars($row['title']) . '</td>';
    echo '</tr>';
}
echo '</tbody></table>';
?>
```

**Reference**: Pattern from `01-fundamentals.md` Section 2 + `03-security.md` Section 1-2

---

**Total Context**: 2,600+ lines, 165 KB documentation  
**Location**: `/path/to/project/plugin/context/`  
**Ready**: Generate secure code now! 🎉
