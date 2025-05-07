### Alur yang terjadi ketika pengguna membuka **OPAC SLiMS**

![image](https://github.com/user-attachments/assets/82dfd7a7-c267-4528-a178-44d1c59563c1)




1. **Akses OPAC**
   Pengguna mem‑anggil `index.php` di root. File ini mem‑**boot‑strap** semua komponen inti (mem‐require `sysconfig.inc.php`, men‑setup autoloader, koneksi DB, dll.). Setelah itu dibuat objek `Opac` dan pemrosesan dialihkan ke fungsi `$Opac->handle('p')->orWelcome();`&#x20;

2. **Akses hal utama?**
   *Jika* parameter URL `p` **tidak ada**, metode `orWelcome()` langsung merender halaman sambutan (template default) – inilah **Halaman Utama**. Proses selesai.

3. **Cek permintaan** (`p` terisi)
   Nilai `p` dibaca untuk dipetakan ke salah satu dari tiga jenis sumber: **plugin, file statis, atau konten berita**. Logika ini berada di kelas `Opac` (dipanggil dari langkah 1) dan memanfaatkan sistem Plugin, file di `/lib/contents/`, dan tabel `content`.

4. **Berbasis plugin?**

   * Saat fase *bootstrapping*, `sysconfig.inc.php` sudah men‑load semua plugin aktif melalui `\SLiMS\Plugins::getInstance()->loadPlugins();`&#x20;
   * Tiap plugin OPAC mendaftarkan menu lewat `Plugins::registerMenu('opac', …)` di file utamanya, contoh:

     * `member_self_registration.plugin.php`&#x20;
     * `registrat.plugin.php` (UI‑ana)&#x20;
   * Bila nilai `p` cocok dengan salah‑satu menu plugin, `Opac` men‑***include*** berkas endpoint yang didaftarkan (mis. `digital.routes.php`, `opac.routes.php`, dll.). Tampilan yang dihasilkan menjadi **Hal pada plugin**.

5. **File statis di `/lib/contents/`?**
   Jika bukan plugin, `Opac` mencari file PHP/HTML bernama persis nilai `p` di folder `lib/contents/`. Contoh: akses `index.php?p=help` akan mem‑include `lib/contents/help.php`. Konten yang dijalankan inilah “**Tampilkan file di /lib/contents/**”.

6. **Konten berita di DB?**
   Apabila file statis tidak ditemukan, `Opac` melakukan query ke tabel `content` (lihat skema pada dump database) untuk baris `content_path = p`. Jika ada, data (title, desc, dll.) dirender sebagai halaman berita/halaman statis yang tersimpan di database . Ini menjadi “**Tampilkan berita yg ada di DB**”.

7. **Hal tidak ditemukan**
   Bila ketiga pencarian di atas gagal, `Opac` mengirim respons 404 sederhana – **“Hal tidak ditemukan”** – lalu berhenti.

8. **Selesai**
   Setelah salah‑satu cabang di atas menghasilkan konten, eksekusi dilanjutkan ke `parseToTemplate()` milik `Opac` untuk digabungkan dengan theme, kemudian HTML dikirim ke browser dan proses berakhir.

---

### Tabel ringkas keterkaitan langkah & berkas

| Langkah (flowchart) | Penjelasan singkat                                          | Berkas/kode utama yang bekerja                                                                                     |
| ------------------- | ----------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------ |
| Akses OPAC          | Browser memanggil root `index.php` → bootstrapping aplikasi | `index.php`, `sysconfig.inc.php`                                                                                   |
| Halaman Utama       | `p` kosong → `$Opac->orWelcome()` merender welcome page     | `index.php`, kelas `Opac`                                                                                          |
| Cek permintaan      | `handle('p')` menentukan jenis permintaan                   | `index.php` (baris `$Opac->handle(...)`)                                                                           |
| Hal pada plugin     | `p` cocok dengan menu yang diregistrasi plugin              | File `.plugin.php` (mis. `member_self_registration.plugin.php`, `registrat.plugin.php`) & endpoint yg didaftarkan  |
| File statis         | `p` cocok dengan file di `lib/contents/` → di‑include       | Berkas dalam `lib/contents/<p>.php`                                                                                |
| Berita DB           | `p` cocok dengan `content_path` pada tabel `content`        | Tabel `content` & query di kelas `Opac`                                                                            |
| Hal tidak ditemukan | Tidak ada kecocokan sama sekali → 404                       | Kelas `Opac` (pesan default “Hal tidak ditemukan”)                                                                 |
| Selesai             | Konten + template dirender, respons dikirim                 | `Opac->parseToTemplate()`, file template di `template/<theme>/`                                                    |

Diagram pada gambar merepresentasikan precisely rantai keputusan di atas bersama titik‑titik file yang terlibat. Dengan memahami pemetaan ini, developer dapat men‑debug atau menambahkan fungsi baru (mis. plugin) tanpa menyentuh core SLiMS.

-Generate by ChatGPT - O3-
