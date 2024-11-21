# Struktur Folder `/files` 📂

Folder `/files` digunakan untuk menyimpan semua file yang diunggah atau dihasilkan oleh SLiMS. File-file ini mencakup lampiran, gambar sampul buku, repositori digital, dan berkas lainnya yang terkait dengan data perpustakaan.

## Struktur Folder dan File

```plaintext
/files
├── att/                    📎 Menyimpan file lampiran (attachments) untuk katalog
├── cover/                  🖼️ Menyimpan gambar sampul koleksi
├── repository/             📂 Menyimpan file digital untuk repositori
├── cache/                  ⚡ Folder cache untuk file sementara
├── barcode/                🖨️ Menyimpan hasil cetakan barcode
├── membership_card/        🆔 Menyimpan hasil cetakan kartu anggota
├── document_templates/     📄 Template dokumen untuk laporan atau output lainnya
```

Penjelasan Per Folder

## /files/att/ 📎

Folder ini digunakan untuk menyimpan file lampiran (attachments) yang diunggah ke katalog.

- File yang diunggah di sini biasanya berupa PDF, gambar, atau dokumen lain yang terkait dengan entri bibliografi.
- Setiap lampiran terhubung ke koleksi tertentu dalam database SLiMS.

Contoh:
- file1.pdf
- gambar1.jpg


## /files/cover/ 🖼️

Folder ini digunakan untuk menyimpan gambar sampul buku atau koleksi.

- Setiap file di folder ini mewakili sampul sebuah koleksi di database SLiMS.
- Gambar sampul ini ditampilkan di OPAC (Online Public Access Catalog).

Contoh:
- 12345.jpg (kode koleksi di database)

  

## /files/repository/ 📂

Folder ini digunakan untuk menyimpan file digital yang diunggah sebagai bagian dari repositori perpustakaan.

- Berkas di sini bisa berupa file e-book, dokumen, atau materi lainnya yang dapat diunduh oleh pengguna.
- Biasanya terkait dengan fitur digital repository di SLiMS.

Contoh:
- ebook1.pdf
- laporan_tugas_akhir.docx



## /files/cache/ ⚡

Folder ini digunakan untuk menyimpan file sementara (cache) yang dihasilkan oleh sistem.

- File cache ini dapat membantu mempercepat proses pemrosesan data di SLiMS.
- Bisa dihapus secara manual jika diperlukan (misalnya, untuk membersihkan ruang penyimpanan).


## /files/barcode/ 🖨️

Folder ini menyimpan file hasil cetakan barcode koleksi perpustakaan.

- File di sini biasanya berupa gambar yang dihasilkan dari modul pencetakan barcode SLiMS.
- Setiap file mewakili barcode untuk koleksi tertentu.

Contoh:
- barcode1.png
- barcode2.png


## /files/membership_card/ 🆔

Folder ini digunakan untuk menyimpan hasil cetakan kartu anggota.

- File kartu anggota di sini biasanya berupa file gambar atau dokumen yang dirancang untuk dicetak.
- Kartu anggota berisi informasi penting seperti nama anggota, ID anggota, dan barcode.

Contoh:
- card1.png
- card2.png
  

## /files/document_templates/ 📄

Folder ini menyimpan template dokumen yang digunakan untuk laporan, surat, atau output lainnya.

- File di sini biasanya digunakan oleh modul pelaporan atau modul lain yang memerlukan output dalam format tertentu.
- Format file biasanya berupa `.docx` atau `.odt`.

Contoh:
- laporan_stok.docx
- surat_pernyataan.odt



Fungsi Folder /files

1. Menyimpan semua file yang diunggah ke sistem SLiMS, termasuk lampiran, gambar, dan file digital lainnya.
2. Memberikan lokasi penyimpanan terpisah untuk file sementara (cache) dan file hasil cetakan.
3. Mempermudah pengelolaan file terkait koleksi dan anggota perpustakaan.

> *Tutorial ini dibuat dengan CustomGPT SLiMS Plugin Maker (ChatGPT) dengan fitur pengetahuan yang ada dalam folder rag (belum di review developer)*
