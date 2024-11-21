# Struktur Folder `/api` 🔗

Folder `/api` digunakan untuk menyediakan layanan **web service** atau **RESTful API**. API ini mempermudah integrasi SLiMS dengan aplikasi pihak ketiga, seperti aplikasi mobile atau sistem eksternal.

```plaintext
/api
├── index.php                  🏁 Entry point utama untuk semua permintaan API
├── modules/                   🧩 Berisi modul API untuk fitur SLiMS
│   ├── biblio/                📚 Modul untuk data bibliografi dan koleksi
│   │   ├── biblio.php         📘 Endpoint untuk mendapatkan data bibliografi
│   │   ├── biblio_item.php    📙 Endpoint untuk mendapatkan data eksemplar koleksi
│   ├── member/                🧍 Modul untuk data anggota perpustakaan
│   │   ├── member.php         👤 Endpoint untuk mendapatkan data anggota
│   │   ├── member_update.php  ✏️  Endpoint untuk memperbarui data anggota
│   ├── circulation/           🔄 Modul untuk data peminjaman/pengembalian
│   │   ├── loan.php           📥 Endpoint untuk mendapatkan data peminjaman
│   │   ├── return.php         📤 Endpoint untuk mencatat pengembalian buku
│   ├── report/                📊 Modul untuk data laporan
│   │   ├── report.php         📄 Endpoint untuk mengambil laporan tertentu
├── locale/                    🌐 Berisi file terjemahan untuk layanan API
│   ├── en_US.php              🇺🇸 File terjemahan bahasa Inggris
│   ├── id_ID.php              🇮🇩 File terjemahan bahasa Indonesia
├── auth/                      🔒 Folder untuk autentikasi API
│   ├── auth.php               🔑 File utama untuk autentikasi pengguna
│   ├── token.php              🔐 File untuk menghasilkan token autentikasi
```
# Penjelasan Per File

## index.php 🏁

File entry point utama untuk layanan API. Semua permintaan diarahkan ke file ini.

- Memproses parameter dari URL (misalnya, ?module=biblio).
- Menangani format respons (JSON atau XML).
- Mengelola keamanan API (jika autentikasi diaktifkan).

## /modules/ 🧩

Folder ini berisi modul-modul API yang bertugas menangani fitur-fitur spesifik SLiMS.

## /modules/biblio/ 📚

Modul ini menangani data bibliografi dan koleksi.

- biblio.php: Endpoint untuk mendapatkan data bibliografi berdasarkan parameter tertentu.
- biblio_item.php: Endpoint untuk mendapatkan data eksemplar koleksi.

## /modules/member/ 🧍

Modul ini menangani data anggota perpustakaan.

- member.php: Endpoint untuk mendapatkan data anggota berdasarkan parameter (misalnya, ID anggota).
- member_update.php: Endpoint untuk memperbarui data anggota. Menggunakan metode HTTP POST.

## /modules/circulation/ 🔄

Modul ini menangani data peminjaman dan pengembalian buku.

- loan.php: Endpoint untuk mendapatkan data peminjaman berdasarkan anggota tertentu.
- return.php: Endpoint untuk mencatat pengembalian buku. Menggunakan metode HTTP POST.

## /modules/report/ 📊

Modul ini menyediakan laporan perpustakaan.

- report.php: Endpoint untuk mengambil data laporan berdasarkan parameter yang diminta (misalnya, statistik peminjaman).

## /locale/ 🌐

Folder ini berisi file terjemahan untuk API.

- en_US.php: File terjemahan bahasa Inggris.
- id_ID.php: File terjemahan bahasa Indonesia.

## /auth/ 🔒

Folder ini menangani autentikasi API, baik melalui username/password maupun token.

- auth.php: File utama untuk autentikasi pengguna dan validasi kredensial.
- token.php: File untuk menghasilkan token autentikasi.

## Contoh Penggunaan API
1. Mengambil Data Bibliografi

GET /api/index.php?module=biblio&title=example

Respons:

{
  "biblio_id": "1",
  "title": "Example Title",
  "author": "John Doe",
  "publisher": "Example Publisher"
}

2. Mendapatkan Data Anggota

GET /api/index.php?module=member&member_id=123

Respons:

{
  "member_id": "123",
  "name": "Jane Smith",
  "email": "jane.smith@example.com"
}

3. Mencatat Pengembalian Buku

POST /api/index.php?module=return
Body:
{
  "item_id": "456",
  "member_id": "123",
  "return_date": "2024-11-21"
}

> *Tutorial ini dibuat dengan CustomGPT SLiMS Plugin Maker (ChatGPT) dengan fitur pengetahuan yang ada dalam folder rag (belum di review developer)*
