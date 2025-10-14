# 🤖 Chatbot Instructions - SLiMS Plugin Context Engineering

**Purpose**: Guide AI chatbots to effectively use context engineering files for SLiMS 9.6.1 Bulian plugin development

**Target Users**: GitHub Copilot, ChatGPT, Claude, Gemini, or any AI assistant helping developers create SLiMS plugins

---

## 📚 How to Use These Context Files

### **For AI Chatbots**

When a user asks about SLiMS plugin development, follow this workflow:

#### **Step 1: Identify the Request Type**

Analyze the user's question and categorize:

| User Request | Primary File(s) | Secondary References |
|--------------|----------------|---------------------|
| "How to create a plugin?" | `README.md` → `01-fundamentals.md` | `QUICK-START.md` |
| "How to register plugin menu?" | `01-fundamentals.md` (Section 2) | Working plugins |
| "Database migration issues" | `02-migration.md` | `06-errors.md` |
| "Security best practices" | `03-security.md` | `01-fundamentals.md` |
| "Report plugin with forms" | `04-iframe.md` | `01-fundamentals.md` |
| "Add/edit/delete data" | `05-crud.md` | `03-security.md` |
| "Plugin not working" | `06-errors.md` | All context files |
| "Hook system / MVC" | `01-fundamentals.md` (Sections 8-10) | `05-crud.md` |
| "Use Composer packages" | `01-fundamentals.md` (Section 11) | Working plugins |

#### **Step 2: Load Relevant Context**

**Always start with these 2 files**:
1. `README.md` - Get overview and file index
2. The primary file from table above

**Then load additional context** based on:
- Security concerns → Always include `03-security.md`
- Database operations → Include `02-migration.md`
- Troubleshooting → Include `06-errors.md`

#### **Step 3: Generate Accurate Response**

**CRITICAL RULES**:

✅ **DO**:
- Follow patterns from context files EXACTLY
- Use all 3 plugin registration patterns when explaining (Official, Legacy, Static)
- Always include security measures (prepared statements, htmlspecialchars)
- Reference specific section numbers from context files
- Provide complete, runnable code examples
- Use generic paths like `/path/to/slims/` (NOT server-specific paths)
- Mention working plugin examples when available

❌ **DON'T**:
- Use `'admin'` as menu category (INVALID!)
- Use `DB_ACCESS` constant (use `INDEX_AUTH`)
- Show code without security measures
- Use raw SQL without prepared statements
- Output data without escaping
- Use relative paths for CSS/JS
- Make assumptions - stick to documented patterns

#### **Step 4: Provide Working Examples**

**Code Structure for Plugin Development**:

```php
// 1. Registration File: my-plugin.plugin.php
<?php
/**
 * Plugin Name: My Plugin
 * Description: Plugin description
 * Version: 1.0.0
 */
use SLiMS\Plugins;

// Pattern 1: Official (RECOMMENDED for new plugins)
$plugins = Plugins::getInstance();
$plugins->registerMenu('reporting', 'My Plugin', __DIR__ . '/admin_interface.php');

// Pattern 2: Legacy (Found in production)
$plugin = Plugins::getInstance();
$plugin->registerMenu('reporting', 'My Plugin', __DIR__ . '/admin_interface.php');

// Pattern 3: Modern Static (SLiMS 9.6+)
\SLiMS\Plugins::menu('reporting', 'My Plugin', __DIR__ . '/admin_interface.php');
```

```php
// 2. Admin Interface: admin_interface.php
<?php
// ALWAYS start with authentication
define('INDEX_AUTH', '1');
require_once SB . 'admin/default/session.inc.php';
require_once SB . 'admin/default/session_check.inc.php';

// Check privileges
$can_read = utility::havePrivilege('reporting', 'r');
if (!$can_read) {
    die('<div class="errorBox">' . __('Access denied!') . '</div>');
}

// Your plugin code here with SECURITY
global $dbs;

// Use prepared statements
$sql = "SELECT * FROM biblio WHERE biblio_id = ?";
$stmt = $dbs->prepare($sql);
$stmt->bind_param('i', $biblio_id);
$stmt->execute();
$result = $stmt->get_result();

// Escape output
while ($row = $result->fetch_assoc()) {
    echo '<td>' . htmlspecialchars($row['title']) . '</td>';
}
?>
```

#### **Step 5: Troubleshooting Reference**

When user encounters errors, check `06-common-errors.md` for:

- **Plugin Not Found** → Section 1 (file naming, permissions)
- **Form Opens New Tab** → Section 2 (iframe pattern)
- **CSS 404 Error** → Section 3 (SWB constant)
- **SQL Errors** → Section 4 (prepared statements)
- **Access Denied** → Section 5 (INDEX_AUTH)
- **Column Mismatch** → Section 6 (SQL aliases)

---

## 🎯 Response Templates for Common Requests

### **Request: "How to create a report plugin?"**

**Your Response Structure**:
```
1. Read context: README.md + 01-fundamentals.md + 04-iframe.md
2. Explain iframe pattern (prevents admin menu loss)
3. Show complete code with:
   - Registration file (3 patterns)
   - Admin interface with authentication
   - Iframe implementation (dual-mode)
   - Security measures (prepared statements + escaping)
4. Reference: "Pattern dari plugin rekap-plus-lokasi (Section 4, 04-iframe.md)"
5. Warn about common errors: form target, hidden fields
```

### **Request: "How to use Composer packages?"**

**Your Response Structure**:
```
1. Read context: 01-fundamentals.md (Section 11)
2. Show composer.json setup
3. Explain autoloading with Composer
4. Show namespace registration
5. Example: Using Carbon or Eloquent ORM
6. Security note: Validate composer packages
```

### **Request: "Migration not working"**

**Your Response Structure**:
```
1. Read context: 02-migration.md + 06-errors.md
2. Check migration file structure
3. Verify up() and down() methods
4. Show error handling pattern (try-catch)
5. Explain common error codes
6. Reference: Section 6 of 02-migration.md
```

### **Request: "Plugin security issues"**

**Your Response Structure**:
```
1. Read context: 03-security.md (CRITICAL!)
2. Audit code for:
   - Authentication (INDEX_AUTH)
   - SQL injection (prepared statements)
   - XSS (htmlspecialchars)
   - Privilege checking
   - CSRF protection
3. Show secure patterns
4. Reference: Sections 1-5 of 03-security.md
```

---

## 📖 Context File Mapping

### **Quick Reference Table**

| File | Primary Topic | Key Sections | When to Use |
|------|---------------|--------------|-------------|
| `README.md` | Index & Overview | All sections | START HERE |
| `01-fundamentals.md` | Core Concepts | 1-11 (all patterns) | Plugin registration, hooks, MVC |
| `02-migration.md` | Database Changes | 1-6 (migration system) | Database schema changes |
| `03-security.md` | Security Rules | 1-7 (all security) | ALWAYS for code generation |
| `04-iframe.md` | UI Pattern | 1-4 (iframe pattern) | Report plugins with forms |
| `05-crud.md` | Data Operations | 1-7 (CRUD) | Add/edit/delete features |
| `06-errors.md` | Troubleshooting | 1-10 (all errors) | Debugging issues |

### **Section Cross-References**

When explaining a concept, reference multiple files:

**Example: "Plugin Registration"**
- Primary: `01-fundamentals.md` Section 2 (3 patterns)
- Security: `03-security.md` Section 1 (authentication)
- Errors: `06-errors.md` Section 1 (plugin not found)

**Example: "Database Queries"**
- Primary: `05-crud.md` Section 2 (read operations)
- Security: `03-security.md` Section 2 (SQL injection)
- Migration: `02-migration.md` Section 3 (schema changes)

**Example: "Report Plugin"**
- Primary: `04-iframe.md` Section 2 (dual-mode pattern)
- Fundamentals: `01-fundamentals.md` Section 2 (registration)
- Security: `03-security.md` Section 1 (authentication)

---

## 🔍 Context Priority System

### **Priority 1: ALWAYS Load** (Critical for Accuracy)
1. `README.md` - File index
2. `03-security.md` - Security rules (NON-NEGOTIABLE!)

### **Priority 2: Load Based on Request**
- Plugin structure → `01-fundamentals.md`
- Database → `02-migration.md`
- Forms/Reports → `04-iframe.md`
- Data operations → `05-crud.md`
- Errors → `06-errors.md`

### **Priority 3: Reference When Needed**
- Advanced features → `01-fundamentals.md` Sections 8-11
- Specific error patterns → `06-errors.md` by section
- Working examples → Report files (PHASE-1, PHASE-2)

---

## 🚨 Critical Validation Checklist

Before providing code to user, validate:

### **Authentication (MANDATORY)**
```php
✅ define('INDEX_AUTH', '1');  // NOT DB_ACCESS!
✅ require_once SB . 'admin/default/session.inc.php';
✅ require_once SB . 'admin/default/session_check.inc.php';
✅ Privilege checking with utility::havePrivilege()
```

### **Security (MANDATORY)**
```php
✅ Prepared statements for ALL queries
✅ htmlspecialchars() for ALL output
✅ Input validation and sanitization
✅ No direct $_GET/$_POST use without validation
```

### **Constants (MANDATORY)**
```php
✅ SB for PHP require/include
✅ SWB for CSS/JS/images in browser
✅ AWB for admin URLs
✅ Global $dbs for database
```

### **Menu Registration (MANDATORY)**
```php
✅ Valid category (NOT 'admin'!)
✅ Valid categories: bibliography, circulation, membership, 
                     master_file, reporting, serial_control, 
                     stock_take, system, opac
```

---

## 💡 Smart Response Strategies

### **Strategy 1: Progressive Disclosure**

**Level 1 - Quick Answer** (for simple questions):
```
1. Direct answer with code snippet
2. One security note
3. Reference to context file section
```

**Level 2 - Detailed Explanation** (for complex questions):
```
1. Explain concept with context
2. Show complete working code
3. Multiple security considerations
4. Reference multiple context sections
5. Common pitfalls from 06-errors.md
```

**Level 3 - Comprehensive Guide** (for "teach me"):
```
1. Full tutorial structure
2. Step-by-step implementation
3. All security measures
4. Testing procedures
5. Troubleshooting guide
6. References to all relevant context files
```

### **Strategy 2: Pattern Recognition**

Recognize these user patterns:

| User Says | They Want | Your Response |
|-----------|-----------|---------------|
| "not working" | Debugging help | Load `06-errors.md` first |
| "how to..." | Implementation | Show complete code with security |
| "best practice" | Security/quality | Reference `03-security.md` |
| "example" | Working code | Copy from context patterns |
| "error: ..." | Specific fix | Match error in `06-errors.md` |

### **Strategy 3: Context Awareness**

**Build context from conversation**:
```
1. First question → Load basic context
2. Follow-up → Keep previous context + add specific file
3. Error message → Switch to troubleshooting mode
4. "show me code" → Switch to implementation mode
```

**Remember across messages**:
- User's plugin purpose
- Security level needed
- Database tables involved
- Previous errors encountered

---

## 🎓 Learning Mode vs. Implementation Mode

### **Learning Mode** (User wants to understand)

Focus on:
- Explain WHY (concepts from fundamentals)
- Show all 3 patterns (let user choose)
- Explain security reasoning
- Reference context file sections for further reading

Example:
```
"Ada 3 cara register plugin menu (01-fundamentals.md Section 2):

1. Pattern Official - Recommended untuk plugin baru
2. Pattern Legacy - Digunakan di production plugins
3. Pattern Static - Modern way di SLiMS 9.6+

Untuk security, HARUS pakai INDEX_AUTH bukan DB_ACCESS 
(03-security.md Section 1) karena..."
```

### **Implementation Mode** (User wants working code)

Focus on:
- Provide complete, runnable code
- One pattern (recommend Official/Static)
- All security included by default
- Brief comments only

Example:
```php
// Quick implementation - Pattern Static (Modern)
<?php
/**
 * Plugin Name: Quick Report
 * Version: 1.0.0
 */
\SLiMS\Plugins::menu('reporting', 'Quick Report', __DIR__ . '/admin.php');

// admin.php - Complete with security
<?php
define('INDEX_AUTH', '1');
require_once SB . 'admin/default/session.inc.php';
require_once SB . 'admin/default/session_check.inc.php';

// [Complete implementation here]
?>
```

---

## 🔧 Advanced Features Guidance

### **When to Mention Hooks** (Section 8, 01-fundamentals.md)

User needs hooks when:
- "Add custom behavior to SLiMS events"
- "Modify existing functionality without core changes"
- "Listen to form submissions"
- "Add custom validation"

### **When to Mention MVC** (Section 9, 01-fundamentals.md)

User needs MVC when:
- "Complex plugin with multiple pages"
- "RESTful API endpoints"
- "Separate business logic"
- "Modern development practices"

### **When to Mention Composer** (Section 11, 01-fundamentals.md)

User needs Composer when:
- "Use external libraries"
- "Use Carbon for dates"
- "Use Eloquent ORM"
- "Modern PHP packages"

---

## 📊 Response Quality Metrics

### **High-Quality Response Includes**:
✅ Code that follows ALL security rules  
✅ References to specific context file sections  
✅ Complete working examples (not snippets)  
✅ Security explanations included  
✅ Common pitfalls mentioned  
✅ Testing instructions  

### **Low-Quality Response** (AVOID):
❌ Code without security measures  
❌ No context file references  
❌ Incomplete snippets  
❌ Wrong constants (DB_ACCESS, 'admin' category)  
❌ Relative paths for assets  
❌ No error handling  

---

## 🎯 Final Instructions for AI Chatbots

### **Your Mission**:
Help developers create **secure, production-ready** SLiMS plugins by:
1. Providing accurate code from context files
2. NEVER compromising on security
3. Explaining WHY, not just WHAT
4. Referencing documentation for learning
5. Debugging issues systematically

### **Your Constraints**:
- ONLY use patterns from context files
- NEVER invent new patterns
- ALWAYS include security by default
- NEVER use deprecated constants
- ALWAYS validate code against checklist

### **Your Success Metrics**:
- User gets working code on first try
- Code passes security audit
- User understands the pattern
- User can extend the code independently
- Zero SQL injection vulnerabilities
- Zero XSS vulnerabilities

---

## 📞 Quick Reference Commands

### **For AI Chatbots**:

**Initial Load**:
```
1. Load README.md
2. Identify request type
3. Load primary context file
4. Load 03-security.md (always)
```

**Generate Code**:
```
1. Copy pattern from context
2. Add security measures
3. Validate against checklist
4. Add inline comments
5. Provide context references
```

**Debug Issues**:
```
1. Load 06-errors.md
2. Match error pattern
3. Load related context files
4. Provide step-by-step fix
5. Explain root cause
```

**Complex Features**:
```
1. Load multiple context files
2. Check advanced sections (8-11 in 01-fundamentals.md)
3. Show progressive implementation
4. Reference working plugins
```

---

## 🚀 Ready to Help Users!

**Remember**:
- These context files are **production-tested** patterns
- **Security is non-negotiable**
- **All patterns are documented** - don't invent new ones
- **Working plugins exist** - reference them
- **User success = Your success**

**File Location**: `/path/to/project/plugin/context/`  
**Total Documentation**: ~2,600 lines, 165 KB  
**Coverage**: 100% of plugin development needs  

---

**Start with**: `README.md` → Identify request → Load relevant context → Generate secure code → Reference sections

**Good luck helping developers build amazing SLiMS plugins! 🎉**
