# 📚 Belajar SLiMS: Panduan Komprehensif & Pengembangan Plugin SLiMS 9

Selamat datang di repositori **Belajar SLiMS (Senayan Library Management System)**! 🎉  
Repositori ini adalah pusat referensi, tutorial arsitektur, panduan keamanan server, studi kasus nyata, dan panduan lengkap pembuatan plugin untuk **SLiMS 9 Bulian**.

---

## 🧭 Daftar Isi Utama

### 🏛️ 1. Struktur & Arsitektur SLiMS Core
- [Struktur Folder Utama SLiMS](belajar-struktur-folder-utama.md)
- [Folder Admin & Modul Sirkulasi/Bibliografi](belajar-folder-admin.md)
- [Folder Lib & Core Helpers](belajar-lib-folder.md)
- [Folder API & RESTful Endpoints](belajar-folder-api.md)
- [Folder Files & Upload Directory](belajar-folder-files.md)
- [Tabel & Skema Database SLiMS](belajar-tabel-database.md)
- [Perbedaan Database SLiMS 8 (Akasia) vs SLiMS 9 (Bulian)](perbedaan-db-slims8.3.1-vs-slims9.6.1.md)
- [Aturan & Logika Peminjaman (Loan Rules)](belajar-aturan-peminjaman.md)
- [Sistem Log Aktivitas (System Log)](belajar-system_log.md)
- [Memahami Simbio GUI & Simbio Datagrid](belajar-simbio.md)
- [Implementasi Simbio Datagrid Praktis](belajar-simbio-datagrid.md)
- [Alur Proses OPAC & Tema Publik](belajar-proses-opac.md)

---

### 🧩 2. Panduan Pembuatan Plugin SLiMS (Modern Plugin Factory)
Panduan lengkap pengembangan plugin mandiri, aman, dan mematuhi standar SLiMS 9 Bulian:
- 🚀 **[Quick Start Plugin Development](plugin/QUICK-START.md)**: Panduan cepat membuat plugin pertama dalam 5 menit.
- 📖 **[AI Development & Instruction Guide](plugin/penting.instructions.SIMPLIFIED.md)**: Panduan standar coding, guardrail, dan best practices.
- 🔍 **[Plugin Fundamentals](plugin/context/01-slims-plugin-fundamentals.md)**: Arsitektur registrasi plugin, scope menu, dan hook SLiMS.
- 🗄️ **[Native Database Migration Guide](plugin/context/02-slims-database-migration.md)**: Panduan migrasi database native (`SLiMS\Migration\Migration`).
- 🔒 **[Security Best Practices & CSRF](plugin/context/03-slims-security-best-practices.md)**: Prepared statement, CSRF token, XSS escaping, dan file upload safety.
- 📊 **[Iframe Pattern untuk Laporan](plugin/context/04-slims-iframe-pattern.md)**: Mencegah menu admin hilang saat submit form filter laporan.
- 🛠️ **[Operasi CRUD & Simbio Lazy Load](plugin/context/05-slims-crud-operations.md)**: Pola CRUD aman tanpa konflik redeclaration class.
- 🚨 **[Troubleshooting & Error Guide](plugin/PLUGIN-ERROR-TROUBLESHOOTING-GUIDE.md)**: Solusi 50+ error umum plugin SLiMS.
- 🎨 **[CSS Loading & Mobile Responsive Guide](plugin/CSS-LOADING-GUIDE.md)**: Path constants (`SWB`, `SB`) dan responsive UI.
- 📝 **[Template Prompt untuk AI Chatbot](plugin/context/USER-PROMPT-TEMPLATES.md)**: Template prompt untuk membuat plugin via AI Agent.

---

### 🛡️ 3. Keamanan Server & Penanganan Malware SLiMS
Panduan proteksi server dan pembersihan insiden keamanan:
- 🛡️ **[Konfigurasi Nginx Security Hardening](keamanan/nginx-configuration-security-hardening.md)**: Blokir eksekusi PHP liar, proteksi folder `/files/` dan `/images/`, serta proteksi path sensitif.
- 🦠 **[Ciri & Cara Kerja Malware pada SLiMS](keamanan/ciri-dan-cara-kerja-malware-pada-slims-bulian-studi-kasus.md)**: Analisis backdoor, webshell, dan teknik injeksi skrip.
- 🧹 **[Penanganan & Pembersihan Malware (Studi Kasus)](keamanan/penanganan-malware-pada-slims-bulian-studi-kasus.md)**: Langkah investigasi dan recovery sistem.
- 📊 **[Laporan Implementasi Security Hardening](keamanan/SECURITY-HARDENING-IMPLEMENTATION-REPORT.md)**: Standar audit keamanan aplikasi perpustakaan.

---

### 💡 4. Koleksi Modifikasi Praktis & Tips Trik
- 📦 **[Perbaikan Import Data (importfix)](modifikasi/importfix/)**: Modifikasi import bibliografi & item.
- 📱 **[Tampilan 2 Buku per Baris di Mobile OPAC](modifikasi/Tampilan-2-Buku-per-Baris-di-Mobile.md)**: Optimasi visual katalog di smartphone.
- 📇 **[Pemisahan Baris Telepon Kartu Anggota](modifikasi/kartu-old-pisah-baris-telepon.md)**: Kustomisasi cetak kartu anggota perpustakaan.
- ❓ **[Tanya Jawab & FAQ SLiMS](tanya-jawab/)**: Solusi kasus plugin disabled, template error, dsb.

---

## 📖 Dokumentasi Resmi & Kontributor SLiMS

### Dokumentasi Resmi:
- 📄 **[Panduan Pengguna SLiMS](https://slims.web.id/docs/user-guide/about/)**
- 🛠️ **[Panduan Pengembang SLiMS](https://slims.web.id/docs/development-guide/about/)**
- 🗂️ **[Repositori Resmi SLiMS GitHub](https://github.com/slims?tab=repositories)**

### Pengembang & Kontributor SLiMS:
- [Waris Agung Widodo (idoalit)](https://github.com/idoalit)
- [Erwan Setyo Budi (erwansetyobudi)](https://github.com/erwansetyobudi)
- [Drajat Hasan (drajathasan)](https://github.com/drajathasan)
- [Ari Nugraha (dicarve)](https://github.com/dicarve)
- [Hendro Wicaksono (hendrowicaksono)](https://github.com/hendrowicaksono)
- [Purwoko (purwoko)](https://github.com/purwoko)
- [Arif Syamsudin (buitenzorg812)](https://github.com/buitenzorg812)
- [Heru Subekti (heroesoebekti)](https://github.com/heroesoebekti)

---

## 🌟 Komunitas SLiMS
- 🌐 **[Website Resmi SLiMS](https://slims.web.id/web/)**
- 👥 **[Grup Facebook SLiMS Community](https://www.facebook.com/groups/senayan.slims/)**
- 💬 **[Grup WhatsApp SLiMS](https://chat.whatsapp.com/JNyiQPmJjFT7cjjzveB7HH)**
