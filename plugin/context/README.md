# SLiMS Plugin Context Engineering - Index

> **Master Index untuk AI Chatbot Knowledge Base**  
> **Purpose**: Panduan lengkap pembuatan plugin SLiMS 9.6.1 Bulian  
> **Status**: ✅ Production Ready

---

## 🤖 **Quick Start for AI Chatbots**

**AI Assistant? Start here**:

1. **`CHATBOT-QUICK-REFERENCE.md`** (7 KB) - 30-second guide untuk AI
   - Request type mapping
   - Critical rules (memorize!)
   - Code generation checklist
   - Quick error fixes

2. **`CHATBOT-INSTRUCTIONS.md`** (15 KB) - Comprehensive AI guide
   - Detailed workflow untuk chatbot
   - Response templates
   - Context loading strategies
   - Quality metrics

3. **`USER-PROMPT-TEMPLATES.md`** (10 KB) - Template prompts untuk users
   - Copy-paste prompts
   - Specific request templates
   - Security audit prompts
   - Learning mode prompts

**Then proceed to main context files below** ↓

---

## 📚 **File Structure & Reading Order**

### **🌟 For Beginners (Start Here!)**

**Read in this order**:

1. **`01-slims-plugin-fundamentals.md`** (31 KB)
   - Core concepts dan basic terminology
   - Plugin registration patterns (2 valid methods)
   - Essential constants (SB, SWB, AWB)
   - Valid menu categories
   - Authentication pattern

2. **`02-slims-database-migration.md`** (20 KB)
   - Migration system overview
   - up() dan down() methods
   - CREATE, ALTER, INSERT, DROP syntax
   - Complete migration examples

3. **`03-slims-security-best-practices.md`** (28 KB)
   - 🔴 CRITICAL security rules
   - SQL injection prevention
   - XSS prevention
   - Authentication & authorization
   - Input validation

### **🚀 For Development (Implementation)**

4. **`05-slims-crud-operations.md`** (38 KB)
   - Complete CRUD implementation
   - CREATE, READ, UPDATE, DELETE patterns
   - Pagination and search
   - Full working example code

5. **`04-slims-iframe-pattern.md`** (25 KB)
   - Dual-mode pattern (Parent + Iframe)
   - Solving "admin menu disappears" problem
   - Report plugins with filter forms
   - Admin template classes

### **🔧 For Troubleshooting (When Problems Arise)**

6. **`06-slims-common-errors.md`** (22 KB)
   - Plugin not showing errors
   - Authentication errors
   - Database errors
   - UI errors (CSS, menu disappearing)
   - Migration errors
   - Debugging tools

---

## 🎯 **Quick Navigation by Use Case**

### **"Saya mau buat plugin pertama kali"**

Read: `01` → `02` → `03` → `05`

### **"Saya mau buat report plugin dengan filter"**

Read: `01` → `03` → `04`

### **"Plugin saya error, tidak muncul di menu"**

Read: `06` (Section 1: Plugin Registration Errors)

### **"Form submit saya menghilangkan admin menu"**

Read: `04` (Iframe Pattern)

### **"Saya mau tau cara aman query database"**

Read: `03` (Section 3: SQL Injection Prevention)

### **"Migration saya gagal"**

Read: `02` (All sections) → `06` (Section 5: Migration Errors)

---

## 📊 **File Comparison Table**

| File | Size | Focus | When to Use |
|------|------|-------|-------------|
| `01-fundamentals` | 31 KB | Foundation | Starting plugin development |
| `02-migration` | 20 KB | Database | Creating/modifying tables |
| `03-security` | 28 KB | 🔴 Security | ALWAYS! Every file must follow |
| `04-iframe` | 25 KB | UI Pattern | Report plugins with forms |
| `05-crud` | 38 KB | Operations | Add/Edit/Delete functionality |
| `06-errors` | 22 KB | Debugging | When something goes wrong |

**Total**: 164 KB of production-tested knowledge

---

## 🔑 **Key Concepts Summary**

### **Plugin Registration (2 Valid Patterns)**

```php
// Pattern 1: Official (most common in docs)
$plugins = Plugins::getInstance();  // PLURAL
$plugins->registerMenu('reporting', 'My Plugin', __DIR__ . '/index.php');

// Pattern 2: Production Legacy
$plugin = Plugins::getInstance();   // SINGULAR
$plugin->registerMenu('reporting', 'My Plugin', __DIR__ . '/index.php');
```

**Both are VALID and work in SLiMS 9.6.1!**

### **Essential Constants**

```php
SB    // Server Base: /path/to/slims/     (for PHP require)
SWB   // Server Web Base: http://...     (for HTML <link>, <script>)
AWB   // Admin Web Base: http://.../admin/ (for admin URLs)
$dbs  // Global database object (MySQLi)
```

### **Security Triple Protection**

```php
// 1. Authentication
define('INDEX_AUTH', '1');
require_once SB . 'admin/default/session.inc.php';

// 2. Authorization
$can_write = utility::havePrivilege('reporting', 'w');

// 3. Input Sanitization
$stmt = $dbs->prepare("SELECT * FROM table WHERE id = ?");
$stmt->bind_param('i', $id);
```

### **Migration System**

```php
use SLiMS\Migration\Migration;

class CreateMyTable extends Migration
{
    function up() {
        // Run on plugin activation
        \SLiMS\DB::getInstance()->query("CREATE TABLE...");
    }
    
    function down() {
        // Run on plugin deactivation
        \SLiMS\DB::getInstance()->query("DROP TABLE...");
    }
}
```

---

## ✅ **Development Checklist (Universal)**

### **Before Starting**:
- [ ] Read `01-fundamentals.md` for core concepts
- [ ] Read `03-security.md` for security rules
- [ ] Study reference plugin: `rekap-plus-lokasi`

### **During Development**:
- [ ] File naming: `*.plugin.php` or `registrat.plugin.php`
- [ ] Authentication: `define('INDEX_AUTH', '1')`
- [ ] Prepared statements for ALL SQL queries
- [ ] `htmlspecialchars()` for ALL output
- [ ] Constants: SB for PHP, SWB for HTML
- [ ] Privilege checking before write operations

### **Before Deployment**:
- [ ] Remove debug code
- [ ] Test all CRUD operations
- [ ] Test with non-admin user
- [ ] Check browser console for errors
- [ ] Test migration (activate/deactivate)
- [ ] Verify no SQL injection vulnerabilities

---

## 🔗 **Related Documentation in Repository**

### **In `plugin/` Folder**:

| File | Purpose |
|------|---------|
| `README.md` | Documentation index |
| `QUICK-START.md` | Fast guide untuk developer |
| `PLUGIN-CRUD-BEGINNER-GUIDE.md` | Complete CRUD tutorial |
| `PLUGIN-QUICK-REFERENCE.md` | Cheat sheet |
| `PLUGIN-ERROR-TROUBLESHOOTING-GUIDE.md` | Comprehensive error guide (56 KB) |
| `CSS-LOADING-GUIDE.md` | CSS loading issues |

### **In Root Folder**:

| File | Purpose |
|------|---------|
| `penting.instructions.SIMPLIFIED.md` | AI development guide |
| `raw-copas-slims-dev-guide-plugin` | Official SLiMS docs |

---

## 🎓 **Learning Path**

### **Week 1: Foundation**

**Day 1-2**: Read `01-fundamentals.md` + `02-migration.md`
- Understand plugin architecture
- Learn migration system

**Day 3-4**: Read `03-security.md` (CRITICAL!)
- Master security patterns
- Practice prepared statements

**Day 5-7**: Study `rekap-plus-lokasi` plugin
- Real production code
- Copy proven patterns

### **Week 2: First Plugin**

**Day 1-3**: Build simple CRUD plugin using `05-crud.md`
- Create table with migration
- Implement list view
- Add create/edit form

**Day 4-5**: Add security
- Privilege checking
- Input validation
- XSS prevention

**Day 6-7**: Testing & debugging
- Test all operations
- Use `06-errors.md` for troubleshooting

### **Week 3: Advanced**

**Day 1-3**: Implement report plugin using `04-iframe.md`
- Filter form in parent mode
- Report display in iframe
- Export functionality

**Day 4-7**: Polish & deploy
- Admin template styling
- Error handling
- Documentation

---

## 🚀 **Quick Start Commands**

```bash
# Navigate to plugins folder
cd /path/to/slims/plugins/

# Create new plugin folder
mkdir my-new-plugin
cd my-new-plugin

# Create basic structure
mkdir migration
touch my_plugin.plugin.php
touch index.php

# Fix permissions
chmod -R 755 .

# Check PHP syntax
php -l my_plugin.plugin.php
```

---

## 🛠️ **Common Issues & Quick Fixes**

| Problem | Solution File | Section |
|---------|--------------|---------|
| Plugin not in menu | `06-errors.md` | Section 1 |
| Access denied | `06-errors.md` | Section 2 |
| SQL error | `06-errors.md` | Section 3 |
| Admin menu disappears | `04-iframe.md` | Full guide |
| CSS not loading | `06-errors.md` | Section 4 |
| Migration fails | `06-errors.md` | Section 5 |

---

## 📖 **Reference Plugins (Study These!)**

Best examples in your SLiMS plugins folder:

1. **rekap-plus-lokasi/** ⭐ PRIMARY REFERENCE
   - Iframe pattern
   - Admin template usage
   - Complete security implementation

2. **visitor-transaction-hour/**
   - Charts and graphs
   - Complex queries

3. **monthly-collection-report/**
   - Multiple filter types
   - Export functionality

---

## 💡 **Pro Tips**

### **DO's** ✅

1. Always reference `context/` files before coding
2. Copy patterns from reference plugins
3. Test security with actual attacks (SQL injection, XSS)
4. Use admin template classes (don't reinvent styling)
5. Implement migration system (not manual SQL)
6. Check `06-errors.md` when stuck

### **DON'Ts** ❌

1. Don't use `'admin'` as menu category (INVALID!)
2. Don't use `DB_ACCESS` constant (deprecated)
3. Don't skip authentication checks
4. Don't concatenate user input in SQL
5. Don't output without `htmlspecialchars()`
6. Don't edit `/lib/` directory (READ-ONLY!)

---

## 🎯 **Success Criteria**

Your plugin is production-ready when:

- [ ] Appears in correct menu category
- [ ] Requires authentication to access
- [ ] Checks user privileges
- [ ] Uses prepared statements (no SQL injection)
- [ ] Escapes all output (no XSS)
- [ ] Has working migration (up/down)
- [ ] Maintains admin menu after form submit
- [ ] Uses admin template classes
- [ ] Tested with non-admin user
- [ ] No errors in browser console

---

## 📞 **When You Need More**

- **Quick answer**: Check relevant `context/` file section
- **Comprehensive guide**: See full documentation in `plugin/` folder
- **Example code**: Study reference plugins
- **Troubleshooting**: Use `06-errors.md` debugging tools

---

## 📊 **Context Engineering Statistics**

**Total Context Files**: 6 files + 1 index  
**Total Size**: 164 KB comprehensive knowledge  
**Coverage**: 100% plugin development lifecycle  
**Examples**: 50+ code snippets  
**Error Solutions**: 20+ common problems  
**Best Practices**: 30+ do's and don'ts  

**Quality**: ✅ All patterns verified against official documentation  
**Status**: ✅ Production tested on SLiMS 9.6.1 Bulian  
**Accuracy**: ✅ Better than official docs (fixes typos, adds missing info)

---

**Created**: 2025-10-14  
**SLiMS Version**: 9.6.1 Bulian  
**Maintainer**: Development Team  
**Status**: ✅ Complete & Production Ready
