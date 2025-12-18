
# 📚 Logika Sirkulasi SLiMS 9 Bulian

Panduan ringkas mengenai hierarki aturan peminjaman pada SLiMS 9 Bulian untuk menghindari kebingungan antara **Tipe Keanggotaan** dan **Aturan Peminjaman**.

---

## ⚖️ Aturan Emas (Hierarchy)

> **"Aturan yang LEBIH SPESIFIK selalu mengalahkan Aturan Umum."**

Dalam SLiMS, sistem akan selalu memprioritaskan menu **Aturan Peminjaman** terlebih dahulu sebelum melihat ke setelan di **Tipe Keanggotaan**.

---

## 📊 Simulasi Bentrok Aturan

Berikut adalah contoh kasus jika terjadi perbedaan angka antara kedua menu tersebut:

### 1. Setelan Tipe Keanggotaan (Umum)

| Tipe Anggota | Kuota Pinjam | Durasi |
| --- | --- | --- |
| Mahasiswa | **5 Buku** | 7 Hari |

### 2. Setelan Aturan Peminjaman (Spesifik)

| Tipe Anggota | Tipe Koleksi | Kuota Pinjam | Durasi |
| --- | --- | --- | --- |
| Mahasiswa | **Buku Umum** | **2 Buku** | 3 Hari |

### 🏆 Hasil Akhir di Meja Sirkulasi:

Jika Mahasiswa meminjam **Buku Umum**, maka yang berlaku adalah:

* ✅ **Jumlah Pinjam:** 2 Buku (Angka 5 diabaikan)
* ✅ **Lama Pinjam:** 3 Hari (Angka 7 diabaikan)

---

## 💡 Ringkasan Perbedaan

| Fitur | Tipe Keanggotaan | Aturan Peminjaman |
| --- | --- | --- |
| **Level** | Global / Default | Spesifik / Custom |
| **Fungsi** | Standar dasar untuk anggota | Pengecualian berdasarkan jenis buku |
| **Kapan Berjalan?** | Jika tidak ada aturan khusus | Kapanpun ada kecocokan Anggota + Koleksi |

---

## 🛠 Rekomendasi Pengaturan

1. **Tipe Keanggotaan:** Isi dengan kebijakan paling umum di perpustakaan.
2. **Aturan Peminjaman:** Isi HANYA jika ada koleksi tertentu (seperti Referensi, Kamus, atau Buku Langka) yang durasi pinjamnya berbeda dari buku biasa.

