> ⚠️ **Disclaimer**  
> JANGAN langsung pasang Plugin di SLiMS yang Operasional. Selalu Tes Plugin di PC/SLiMS lain. Gunakan dengan risiko Anda sendiri.


# 🧩 Panduan Pemasangan Plugin SLiMS 🚀

Berikut adalah panduan dasar dan langkah-langkah umum untuk memasang plugin pada SLiMS (Senayan Library Management System).

---

## 💡 Konsep Dasar Pemasangan Plugin SLiMS:

Sebagian besar plugin SLiMS melibatkan penyalinan file dan folder plugin ke direktori tertentu di dalam instalasi SLiMS Anda. Beberapa plugin mungkin memerlukan langkah konfigurasi tambahan di antarmuka admin SLiMS.

---

## 🛠️ Langkah-Langkah Umum Pemasangan Plugin (Versi Sederhana):

**Prasyarat: SLiMS minimal versi 9.4.0 **

1.  **📥 Unduh Plugin:**
    *   Dapatkan file plugin SLiMS. Biasanya plugin datang dalam format `.zip`.
    *   Sumber plugin bisa dari repositori resmi SLiMS, forum komunitas, atau pengembang pihak ketiga.
    *   Contoh unduh dari GitHub: [https://github.com/adeism/slims-rekap-plus-lokasi](https://github.com/adeism/slims-rekap-plus-lokasi)
    *   ![Contoh Halaman Unduh Plugin](https://github.com/user-attachments/assets/9930665e-d72f-4b52-8509-a601d3e7516c)

2.  **🗜️ Ekstrak File Plugin (Jika dalam format `.zip`):**
    *   Klik kanan pada file `.zip` plugin yang telah diunduh.
    *   Pilih "Extract All..." atau "Ekstrak Semua..." (atau gunakan aplikasi arsip seperti 7-Zip atau WinRAR untuk mengekstraknya).
    *   Anda akan mendapatkan sebuah folder yang berisi file-file plugin.

3.  **📍 Identifikasi Lokasi Instalasi SLiMS Portabel Anda:**
    *   **Jika menggunakan XAMPP:**
        *   Biasanya di ``C:\xampp\htdocs\nama_folder_slims_anda\``
        *   Contoh: ``C:\xampp\htdocs\slims9_bulian\``
    *   **Jika menggunakan Paket SLiMS Portabel (misal SPOTS):**
        *   Cari folder utama paket portabel Anda. Di dalamnya, cari folder SLiMS (bisa bernama `slims_version`, `htdocs`, atau `www`).
        *   Contoh: ``D:\SPOTS_SLiMS9\slims9_bulian\`` atau ``D:\SLiMS_Portable\htdocs\``

4.  **➡️ Salin Folder/File Plugin ke Direktori SLiMS yang Tepat:**
    *   **Lokasi Umum Penyalinan File Plugin:**
        *   **📂 Folder `plugins`:** Banyak plugin akan meminta Anda menyalin folder utama plugin ke dalam direktori `plugins` di root SLiMS Anda.
            *   Contoh: Jika folder hasil ekstrak plugin bernama `nama_plugin_saya`, maka Anda menyalinnya ke ``C:\xampp\htdocs\slims9_bulian\plugins\nama_plugin_saya\``

    *   **Contoh Sederhana:** Jika dokumentasi plugin mengatakan "Copy the `my_awesome_plugin` folder to your SLiMS `plugins` directory."
        1.  Buka folder hasil ekstrak plugin Anda. Anda akan melihat folder `my_awesome_plugin`.
        2.  Salin (Copy) folder `my_awesome_plugin` ini.
        3.  Buka File Explorer dan navigasi ke direktori `plugins` di dalam folder instalasi SLiMS portabel Anda (misal, ``C:\xampp\htdocs\slims9_bulian\plugins\``).
        4.  Tempel (Paste) folder `my_awesome_plugin` ke sana.

5.  **🚀 Jalankan Server SLiMS Portabel Anda:**
    *   **XAMPP:** Pastikan Apache dan MySQL berjalan dari XAMPP Control Panel.
    *   **Paket Portabel:** Jalankan file starter server (`apache_start.bat`, `webserver_start.exe`, dll.).

6.  **⚙️ Aktivasi atau Konfigurasi Plugin (Jika Perlu):**
    *   Buka SLiMS di browser Anda (misal `http://localhost/slims9_bulian/` atau `http://localhost:8089/`).
    *   Login ke area **Admin**.
    *   **Cari menu plugin atau modul:**
        *   Beberapa plugin akan muncul di menu "Sistem" ➡️ "Plugin" (atau "Modul"). Anda mungkin perlu mengaktifkannya di sana.
        *   Plugin lain mungkin menambahkan menu baru di sidebar admin atau di dalam modul yang sudah ada (misalnya, plugin untuk "Sirkulasi" akan muncul di menu "Sirkulasi").
    *   Ikuti instruksi konfigurasi spesifik dari plugin tersebut (jika ada).

---

## ✨ Tips Penting:

*   **💾 BACKUP!** Sebelum memasang plugin baru, terutama jika Anda tidak yakin, selalu buat cadangan (backup) folder instalasi SLiMS Anda dan database-nya. Jika terjadi masalah, Anda bisa mengembalikannya.
*   **📖 Baca Dokumentasi:** Ini tidak bisa cukup ditekankan. Dokumentasi plugin adalah sumber informasi terbaik tentang cara instalasi dan konfigurasi.
*   **✅ Kompatibilitas:** Pastikan plugin kompatibel dengan versi SLiMS yang Anda gunakan.
*   **🛡️ Sumber Terpercaya:** Unduh plugin dari sumber yang terpercaya untuk menghindari malware atau plugin yang rusak.
*   **1️⃣ Satu per Satu:** Jika memasang banyak plugin, lakukan satu per satu. Ini memudahkan pelacakan jika terjadi masalah.

---

Ini adalah panduan yang disederhanakan. Prosesnya bisa sedikit berbeda tergantung kompleksitas plugin. Selalu utamakan membaca instruksi yang disertakan bersama plugin.
