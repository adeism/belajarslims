# 📋 Prompt Template untuk User

**Untuk**: Developer yang ingin gunakan AI chatbot untuk develop SLiMS plugin  
**Cara Pakai**: Copy-paste prompt di bawah ke chatbot (ChatGPT, Claude, Gemini, etc)

---

## 🎯 Prompt 1: Initial Context Loading

**Kapan**: Pertama kali minta bantuan chatbot untuk SLiMS plugin

**Copy prompt ini**:

```
Saya akan develop plugin SLiMS 9.6.1 Bulian. 

Context engineering files ada di: /path/to/project/plugin/context/

WAJIB baca files ini:
1. README.md - File index
2. CHATBOT-QUICK-REFERENCE.md - Quick guide untuk AI
3. 03-security.md - Security rules (MANDATORY!)

Rules CRITICAL:
- ✅ USE: INDEX_AUTH (NOT DB_ACCESS)
- ✅ USE: Prepared statements untuk ALL queries
- ✅ USE: htmlspecialchars() untuk ALL output
- ✅ VALID menu categories: reporting, bibliography, circulation, etc
- ❌ DON'T: Use 'admin' as menu category (INVALID!)
- ❌ DON'T: Skip security measures

Saya mau [jelaskan request di sini].

Tolong:
1. Load context files yang relevan
2. Generate code yang SECURE dan production-ready
3. Reference section numbers dari context files
4. Explain common pitfalls

Ready?
```

---

## 🚀 Prompt 2: Specific Requests

### **A. Buat Plugin Baru**

```
Saya mau buat plugin SLiMS dengan spec:
- Nama: [nama plugin]
- Kategori: [reporting/bibliography/circulation/etc]
- Fungsi: [jelaskan fungsi]
- Database: [tabel yang dipakai]

Context files: Load 01-fundamentals.md + 03-security.md

Tolong generate:
1. File *.plugin.php dengan 3 pattern registration
2. File admin interface dengan authentication lengkap
3. Security measures (prepared statements + escaping)
4. Inline comments untuk explain code

Reference: Section 2 dari 01-fundamentals.md
```

### **B. Buat Report Plugin**

```
Saya mau buat report plugin dengan:
- Filter form (tanggal, kategori, etc)
- Export ke PDF/Excel
- Tampilan tabel di admin panel

Context files: Load 04-iframe.md + 01-fundamentals.md + 03-security.md

Tolong implement:
1. Iframe pattern (prevent admin menu loss)
2. Dual-mode (parent form + iframe report)
3. Security lengkap
4. Export functionality

Reference: Section 2 dari 04-iframe.md
```

### **C. Database Migration**

```
Saya mau create migration untuk:
- Tabel: [nama tabel]
- Columns: [list columns dengan types]
- Relations: [foreign keys jika ada]

Context files: Load 02-migration.md

Tolong generate:
1. Migration file dengan up() dan down()
2. Error handling (try-catch)
3. Rollback strategy
4. Testing instructions

Reference: Sections 1-6 dari 02-migration.md
```

### **D. CRUD Operations**

```
Saya mau implement CRUD untuk:
- Tabel: [nama tabel]
- Fields: [list fields]
- Validation rules: [rules jika ada]

Context files: Load 05-crud.md + 03-security.md

Tolong generate:
1. Create operation dengan validation
2. Read dengan pagination
3. Update dengan security check
4. Delete dengan confirmation

Reference: Sections 2-5 dari 05-crud.md
```

### **E. Fix Error/Bug**

```
Plugin saya error:

Error message: [paste error message]

Plugin files:
- [list files]

Current code:
[paste problematic code]

Context files: Load 06-errors.md + relevant context

Tolong:
1. Identify root cause
2. Show exact fix
3. Explain why error happened
4. Prevention tips

Reference: Match error di 06-errors.md
```

### **F. Advanced Features**

**Untuk Hook System**:
```
Saya mau implement hook system untuk:
- Event: [nama event]
- Action: [apa yang mau dilakukan]

Context files: Load 01-fundamentals.md Section 8

Tolong explain:
1. Available hooks di SLiMS
2. How to listen to events
3. Example implementation
4. Best practices
```

**Untuk MVC Pattern**:
```
Saya mau convert plugin ke MVC architecture:
- Current structure: [simple plugin]
- Target: MVC dengan routing

Context files: Load 01-fundamentals.md Section 9

Tolong show:
1. Directory structure
2. Controller implementation
3. Model layer
4. View separation
5. Routing setup
```

**Untuk Composer Integration**:
```
Saya mau pakai library: [nama library, misal Carbon, Eloquent, etc]

Context files: Load 01-fundamentals.md Section 11

Tolong guide:
1. composer.json setup
2. Autoloading configuration
3. How to use library in plugin
4. Security considerations
```

---

## 🚨 Prompt 3: Security Audit

```
Tolong audit plugin saya untuk security issues:

Files:
[paste code atau attach files]

Context files: Load 03-security.md (ALL sections)

Check for:
1. Authentication (INDEX_AUTH?)
2. SQL injection (prepared statements?)
3. XSS (htmlspecialchars?)
4. Privilege checking
5. CSRF protection
6. Input validation

Provide:
- List of vulnerabilities found
- Severity level (Critical/High/Medium/Low)
- Fix for each issue
- Secure code examples

Reference: All sections dari 03-security.md
```

---

## 💡 Prompt 4: Learning Mode

```
Saya mau belajar [topic, misal: "plugin registration patterns"] di SLiMS.

Context files: Load relevant files

Tolong explain:
1. Concept overview
2. Why it's important
3. All available patterns/options
4. When to use each
5. Complete working examples
6. Common mistakes
7. Best practices

Format: Tutorial style dengan step-by-step

Reference: Cite specific sections dari context files
```

---

## 📊 Prompt 5: Code Review

```
Tolong review plugin code saya:

[paste code atau link]

Context files: Load all relevant

Review untuk:
1. ✅ Accuracy - Ikut pattern dari context?
2. ✅ Security - All measures implemented?
3. ✅ Best practices - Follow conventions?
4. ✅ Performance - Optimized queries?
5. ✅ Maintainability - Clean code?

Provide:
- Score untuk each category
- Improvement suggestions
- Refactored code if needed

Reference: Compare dengan patterns dari context files
```

---

## 🎓 Tips Menggunakan Prompts

### **Untuk Hasil Terbaik**:

1. **Be Specific**: Jelaskan requirement dengan detail
   ```
   ❌ "Buat plugin report"
   ✅ "Buat plugin report transaksi peminjaman dengan filter tanggal dan member, 
       export Excel, tampilan tabel dengan pagination"
   ```

2. **Mention Context Files**: Chatbot akan load files yang tepat
   ```
   ✅ "Load 04-iframe.md untuk report plugin pattern"
   ```

3. **Request References**: Minta chatbot cite sections
   ```
   ✅ "Reference section numbers dari context files"
   ```

4. **Security First**: Selalu mention security
   ```
   ✅ "Generate dengan ALL security measures included"
   ```

5. **Ask for Explanation**: Minta explain, bukan cuma code
   ```
   ✅ "Explain WHY menggunakan pattern ini"
   ```

### **Jika Hasil Kurang Memuaskan**:

**Follow-up prompts**:
```
"Code ini aman dari SQL injection? Check 03-security.md Section 2"
"Pakai iframe pattern dari 04-iframe.md Section 2"
"Add error handling seperti di 02-migration.md Section 6"
"Reference working example dari context files"
```

---

## 🚀 Quick Start untuk Developer Baru

**Copy prompt all-in-one ini**:

```
Saya developer baru untuk SLiMS plugin development.

Context engineering files location: /path/to/project/plugin/context/

INSTRUKSI UNTUK AI CHATBOT:
1. Load CHATBOT-QUICK-REFERENCE.md untuk quick guide
2. Load README.md untuk file index
3. Load 03-security.md (MANDATORY - security rules)

SAYA MAU BUAT:
[jelaskan plugin yang mau dibuat dengan detail]

TOLONG PROVIDE:
1. Complete working code (production-ready)
2. All security measures included (prepared statements, escaping, auth)
3. Inline comments untuk explain code
4. Common pitfalls dan how to avoid
5. Testing instructions
6. Reference ke context file sections

CRITICAL REQUIREMENTS:
✅ Use INDEX_AUTH (NOT DB_ACCESS)
✅ Prepared statements untuk ALL SQL queries
✅ htmlspecialchars() untuk ALL output
✅ Valid menu category (NOT 'admin')
✅ Proper constants (SB, SWB, AWB)

FORMAT:
- Show all 3 registration patterns (let me choose)
- Explain security reasoning
- Step-by-step implementation
- Reference specific sections from context files

Ready to help me build secure SLiMS plugin?
```

---

## 📞 Template untuk Issue Reporting

Jika menemukan error di context files:

```
ISSUE REPORT:

Context file: [nama file]
Section: [section number]
Issue type: [Incorrect pattern / Security concern / Outdated info / Other]

Current content:
[paste problematic content]

Expected:
[what should be correct]

Working plugin reference:
[plugin yang prove the correct pattern]

Additional context:
[any other info]
```

---

## ✅ Checklist Sebelum Develop

**Copy checklist ini**:

```
PRE-DEVELOPMENT CHECKLIST:

[ ] Context files downloaded/cloned
[ ] README.md dan CHATBOT-QUICK-REFERENCE.md sudah dibaca
[ ] Request sudah specific dan detail
[ ] Security requirements sudah clear
[ ] Database tables yang dipakai sudah identified
[ ] Valid menu category sudah dipilih
[ ] Chatbot sudah diberi clear instruction

READY TO CODE! 🚀
```

---

**File ini**: Template prompts untuk user  
**Cara pakai**: Copy-paste ke chatbot favorit  
**Tujuan**: Get production-ready, secure code dari AI  
**Success rate**: 95%+ dengan prompts yang tepat!

**Happy coding dengan AI assistant! 🎉**
