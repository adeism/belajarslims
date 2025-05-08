# SLiMS 9 Bulian — Tampilan 2 Buku per Baris di Mobile

> **Tujuan** : Membuat bagian **“Popular among our collections”** pada halaman OPAC menampilkan **dua kartu buku dalam satu baris** ketika lebar user akses via HP / layar ≤ 576 px.
> 
> **Lingkup** : Hanya mengubah _stylesheet_ tema (CSS). 

---

## 1  Permasalahan
Secara bawaan, komponen `<slims-collection>` merender daftar buku dalam grid _flex_ dengan setiap kartu (`div`) memiliki lebar tetap `w-48` (≈ 12 rem). Di layar ponsel, kartu‑kartu ini jatuh ke satu kolom sehingga pengguna harus banyak menggulir.

## 2  Tampilan Sebelum & Sesudah
| | **Sebelum** | **Sesudah** |
|---|---|---|
| **Mobile ≤ 576 px** | ![image](https://github.com/user-attachments/assets/809e9c8f-7e10-4d77-be63-05bfc956f1be)| ![image](https://github.com/user-attachments/assets/a1b34ba6-a9c2-4dac-a63a-4061d7953160) |



---

## 3  Solusi — Override CSS Tema
1. **Buka file CSS tema**  
   Contoh jalur tema bawaan _classic_:
   ```
   template/default/assets/css/style.css
   ```
   > Jika menggunakan tema lain, sesuaikan folder `template/<nama‑tema>/assets/css/`.

2. **Tambahkan aturan media‑query di akhir file**:
   ```css
   /* === OPAC: tampilkan 2 buku per baris pada layar ≤576 px === */
   @media (max-width: 576px) {
     /* elemen kartu berada langsung di dalam .collection */
     .collection > div {
       flex: 0 0 50%;   /* basis 50%  → dua kolom */
       max-width: 50%;  /* batasi lebar maksimal */
       padding-left: .5rem; /* selaras dengan kelas pr-4 */
       padding-right: .5rem;
       margin-bottom: 1rem;  /* jarak vertikal antarkartu */
     }
   }
   ```

3. **Simpan file** dan **hapus cache**:
   - Lakukan *hard‑refresh* (Ctrl + F5) di browser **ATAU** tingkatkan `static_file_version` melalui menu *System → General Setting* agar CSS baru langsung dimuat.

---

## 4  Langkah Verifikasi
1. Buka OPAC di peramban ponsel atau gunakan _DevTools_ → **Toggle Device Toolbar**.
2. Setel lebar viewport ≤ 576 px.
3. Pastikan di bagian **Popular among our collections** terlihat **2 kartu** per baris.

---

## 5  Rollback
Jika perlu mengembalikan tampilan satu kolom:
1. Hapus atau komentar blok CSS yang baru ditambahkan.
2. Simpan perubahan dan bersihkan cache.

---

## 6  Referensi Tambahan
- Dokumentasi Tailwind CSS (v3.x) – [Mengelola grid _flex_ responsif](https://tailwindcss.com/docs/flex-basis)
- SLiMS 9 Bulian – [User Guide](https://slims.web.id/docs)

> **Selesai!** Kini OPAC Anda lebih nyaman diakses melalui perangkat seluler 📱.
