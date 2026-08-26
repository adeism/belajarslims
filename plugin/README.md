# 📚 SLiMS Plugin Development Guide

Dokumentasi lengkap untuk pengembangan plugin SLiMS 9.6.1 Bulian.

---

## 📋 **Daftar Dokumentasi**

### **🚨 Troubleshooting & Error Fixing**

| File | Purpose | When to Use |
|------|---------|-------------|
| **PLUGIN-ERROR-TROUBLESHOOTING-GUIDE.md** | Panduan lengkap troubleshooting error plugin | User mengalami error atau bug dalam plugin |
| **PLUGIN-QUICK-REFERENCE.md** | Quick reference untuk development | Butuh checklist cepat atau command |
| **CSS-LOADING-GUIDE.md** | Guide lengkap untuk loading CSS/JS | CSS tidak load atau 404 error |

### **📖 Panduan Inti & Modul Konteks**
| File | Purpose | Description |
|------|---------|-------------|
| **QUICK-START.md** | Panduan Cepat 5 Menit | Langkah awal membuat plugin baru |
| **penting.instructions.SIMPLIFIED.md** | Master Rule & AI Instruction | Standar guardrail, arsitektur, dan coding |
| **context/01-slims-plugin-fundamentals.md** | Dasar Arsitektur Plugin | Registrasi plugin, menu scope, dan hook |
| **context/02-slims-database-migration.md** | Migrasi Database Native | Standar migrasi SLiMS `SLiMS\Migration\Migration` |
| **context/03-slims-security-best-practices.md** | Keamanan & CSRF | Prepared statements, token CSRF, dan validasi upload |
### **Baru Mulai Development?**
1. Baca: `PLUGIN-QUICK-REFERENCE.md` - Get started dengan checklist
2. Study: Plugin reference (`rekap-plus-lokasi`) di direktori plugins
3. Follow: Development workflow checklist

### **Mengalami Error?**
1. Cek: `PLUGIN-ERROR-TROUBLESHOOTING-GUIDE.md` - Complete error patterns
2. Search: Error message atau pattern yang mirip
3. Apply: Solution yang provided

### **CSS Tidak Load?**
1. Baca: `CSS-LOADING-GUIDE.md` - Complete CSS loading guide
2. Verify: Path constants (SB vs SWB)
3. Test: Browser console untuk check 404 errors

### **Ingin Belajar dari Case Study?**
1. Baca: `PLUGIN-QUICK-REFERENCE.md` Section: Real-World Cases
2. Study: monthly-collection-report case (6 problems solved)
3. Apply: Lessons learned ke project Anda

---

## 📊 **Document Structure**

### **PLUGIN-ERROR-TROUBLESHOOTING-GUIDE.md** (56 KB)
**Sections:**
1. Pattern-Based Troubleshooting Strategy
2. Common Plugin Errors & Solutions (9 sections)
3. Plugin Development Best Practices
4. Real-World Implementation Examples
5. Debugging & Troubleshooting Tools
6. Automated Plugin Validator Script

**Content:**
- ✅ 9 common error patterns dengan solutions
- ✅ Dual plugin pattern documentation (Pattern 1 & 2)
- ✅ Valid menu categories table
- ✅ Iframe pattern implementation
- ✅ Admin template integration
- ✅ CSS loading best practices
- ✅ Security patterns (INDEX_AUTH, prepared statements)
- ✅ Complete code examples

### **PLUGIN-QUICK-REFERENCE.md** (7.8 KB)
**Sections:**
1. Top 7 Plugin Errors & Solutions
2. Plugin Development Checklist
3. Reference Plugins (Best Examples)
4. Quick Commands
5. Key Takeaways

**Content:**
- ✅ Quick fixes untuk 7 error paling umum
- ✅ Copy-paste ready code examples
- ✅ Bash commands untuk validation
- ✅ Debug mode implementation
- ✅ Real-world case studies

### **CSS-LOADING-GUIDE.md** (16 KB)
**Sections:**
1. Common Mistakes (404 Errors)
2. Correct Solutions (3 Methods)
3. Path Constants Reference (SB, SWB, AWB)
4. CSS Loading Methods Comparison
5. Recommended File Structure
6. Best Practices
7. Debugging Tools
8. Real-World Examples
9. Quick Reference Card

**Content:**
- ✅ Path constants explanation (SB vs SWB vs AWB)
- ✅ 3 loading methods dengan pros/cons
- ✅ Common mistakes yang cause 404
- ✅ Debugging helper functions
- ✅ Browser console checks
- ✅ Complete working examples

---

## 🎓 **Learning Path**

### **Beginner Developer:**
```
Day 1: Read PLUGIN-QUICK-REFERENCE.md
       Study rekap-plus-lokasi plugin structure
       
Day 2: Follow development workflow checklist
       Create first simple plugin
       
Day 3: Test with multiple scenarios
       Debug using provided commands
```

### **Intermediate Developer:**
```
Week 1: Study PLUGIN-ERROR-TROUBLESHOOTING-GUIDE.md
        Implement iframe pattern
        Master admin template classes
        
Week 2: Study CSS-LOADING-GUIDE.md
        Implement multiple selection
        Add export functionality
        
Week 3: Security hardening
        Performance optimization
        Production deployment
```

### **Advanced Developer:**
```
Month 1: Create complex plugins with hooks
         Implement custom authentication
         Build API integrations
         
Month 2: Contribute to documentation
         Share best practices
         Mentor other developers
```

---

## 🔍 **Common Use Cases**

### **Use Case 1: Plugin Not Found Error**
```bash
# Quick fix:
1. Check file naming: *.plugin.php or registrat.plugin.php
2. Verify variable: $plugins or $plugin
3. Fix permissions: chmod -R 755 plugins/my-plugin/
4. See: PLUGIN-ERROR-TROUBLESHOOTING-GUIDE.md Section 1
```

### **Use Case 2: Form Opens New Tab**
```bash
# Quick fix:
1. Implement iframe pattern (dual-mode)
2. Add target="reportView" to form
3. Include hidden fields (mod, id, sec)
4. See: PLUGIN-ERROR-TROUBLESHOOTING-GUIDE.md Section 3
5. Reference: rekap-plus-lokasi plugin
```

### **Use Case 3: CSS 404 Error**
```bash
# Quick fix:
1. Use SWB constant (NOT relative paths!)
2. Example: echo '<link href="' . SWB . 'plugins/my-plugin/assets/style.css">';
3. Verify in browser console (Network tab)
4. See: CSS-LOADING-GUIDE.md
```

### **Use Case 4: Column Mismatch Error**
```bash
# Quick fix:
1. Match HTML column names with SQL aliases
2. SQL: SELECT YEAR(date) AS LoanYear
3. HTML: $row['LoanYear'] (must match!)
4. See: PLUGIN-QUICK-REFERENCE.md Section 4
```

---

## 📖 **Reference Plugins**

Study these production-tested plugins sebagai template:

| Plugin | Location | What to Learn |
|--------|----------|---------------|
| **rekap-plus-lokasi** | `/plugins/rekap-plus-lokasi/` | PRIMARY REFERENCE - Iframe, template, styling |
| **visitor-transaction-hour** | `/plugins/visitor-transaction-hour/` | Iframe isolation pattern |
| **monthly-collection-report** | `/plugins/monthly-collection-report/` | Complete best practices |

**How to Study:**
```bash
cd /var/www/html/slims/s951dev/plugins/

# Study structure
ls -la rekap-plus-lokasi/

# Read registration file
cat rekap-plus-lokasi/registrat.plugin.php

# Read interface file
cat rekap-plus-lokasi/admin_simple.php

# Copy patterns:
# 1. Iframe structure
# 2. Output buffering (ob_start/ob_get_clean)
# 3. Admin template classes (s-table, alterCellPrinted)
# 4. Hidden form fields
# 5. Prepared statements
```

---

## 🛠️ **Quick Commands**

### **Validation Commands:**
```bash
# Syntax check
php -l /var/www/html/slims/s951dev/plugins/my-plugin/admin_simple.php

# Permission fix
chmod -R 755 /var/www/html/slims/s951dev/plugins/my-plugin/

# Find plugin files
find plugins/ -name "*.plugin.php"

# Check for errors
grep -r "DB_ACCESS" plugins/my-plugin/           # Should be empty
grep -r "registerMenu.*'admin'" plugins/         # Should be empty
```

### **Debug Mode:**
```php
// Add to admin_simple.php
if (isset($_GET['debug']) && $_GET['debug'] == '1') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    echo '<pre>';
    print_r($_GET);
    print_r($_POST);
    echo '</pre>';
}

// Access: plugin_container.php?mod=myplugin&id=xxx&sec=/&debug=1
```

---

## 🔒 **Security Checklist**

- [ ] **INDEX_AUTH** defined (NOT DB_ACCESS)
- [ ] Session files loaded in correct order
- [ ] Prepared statements for ALL SQL queries
- [ ] htmlspecialchars() for ALL output
- [ ] Type casting for numeric inputs
- [ ] Array filtering before SQL
- [ ] Privilege checking implemented
- [ ] Input validation on all forms
- [ ] No sensitive data in error messages
- [ ] CSRF token validation (if applicable)

---

## ✅ **Development Workflow**

### **Before Starting:**
- [ ] Study reference plugin (rekap-plus-lokasi)
- [ ] Plan database queries and tables
- [ ] Identify privilege module
- [ ] Decide on interface pattern

### **During Development:**
- [ ] Use proper file naming
- [ ] Define INDEX_AUTH constant
- [ ] Include session files correctly
- [ ] Use admin template classes
- [ ] Implement prepared statements
- [ ] Add output escaping
- [ ] Test with multiple users

### **Before Deployment:**
- [ ] Remove debug code
- [ ] Test all forms thoroughly
- [ ] Verify CSS loads (browser console)
- [ ] Test with invalid inputs
- [ ] Check SQL injection vulnerabilities
- [ ] Validate privilege checking
- [ ] Test pagination and filtering

---

## 📞 **Support & Resources**

- **Official Docs**: `/SLiMS-9-Documentation/userguide/development-guide/`
- **Plugin Examples**: Working plugins in `/plugins/` directory
- **Community**: SLiMS Community Forum
- **Testing Tools**: Validation scripts in troubleshooting guide

---

## 🎯 **Key Principles**

1. **Iframe Pattern is MANDATORY** for report plugins
2. **Admin Template Classes** are comprehensive - use them!
3. **Security is NON-NEGOTIABLE** - prepared statements always
4. **Reference Plugins** are your best friend - copy proven patterns
5. **Path Constants** must be correct - SWB for browser, SB for PHP

---

**Last Updated**: 2025-10-10  
**SLiMS Version**: 9.6.1 Bulian  
**Document Version**: 2.0 (Complete Edition)
