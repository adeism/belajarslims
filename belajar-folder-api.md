# Struktur Folder `/api` SLiMS 9 Bulian 🔗

Folder `/api` pada SLiMS 9 Bulian menyediakan layanan **RESTful API v1** berbasis routing yang di-handle oleh AltoRouter (`lib/router.inc.php`). API ini dapat dimanfaatkan untuk integrasi dengan aplikasi mobile, SIAKAD, display informasi, dashboard perpustakaan, maupun otomasi lainnya.

Entry point API dipanggil melalui OPAC (`index.php?p=api/...` atau melalui rewrite URL `/api/...`).

---

## 📁 Struktur Direktori Asli `/api`

```plaintext
/api
└── v1/
    ├── controllers/
    │   ├── Controller.php        🏛️ Base controller abstract class
    │   ├── HomeController.php    🏠 Endpoint root API (status & info)
    │   ├── BiblioController.php  📚 Endpoint data bibliografi & statistik
    │   ├── ItemController.php    📙 Endpoint data eksemplar koleksi
    │   ├── LoanController.php    🔄 Endpoint data sirkulasi & ringkasan peminjaman
    │   ├── MemberController.php  👤 Endpoint data anggota teraktif
    │   └── SubjectController.php 🏷️ Endpoint topik/subjek populer & terbaru
    ├── helpers/
    │   ├── Cache.php             ⚡ Helper manajemen HTTP cache response
    │   └── Image.php             🖼️ Helper pemrosesan gambar/cover
    └── routes.php                🚦 Registrasi rute URL API & plugin hook
```

> [!NOTE]
> SLiMS 9 core **tidak menggunakan** folder modular seperti `/api/modules/biblio/` atau `/api/auth/`. Seluruh routing v1 dipusatkan di `api/v1/routes.php` dengan pola MVC Controller.

---

## 🚦 Daftar Endpoint REST API v1 Bawaan SLiMS

Semua endpoint bawaan SLiMS 9 v1 menggunakan metode HTTP **`GET`**:

### 1. Umum & Beranda
| Method | Endpoint | Handler | Deskripsi |
|---|---|---|---|
| `GET` | `/api/` | `HomeController@index` | Menampilkan status dan informasi dasar API |

### 2. Bibliografi (Koleksi)
| Method | Endpoint | Handler | Deskripsi |
|---|---|---|---|
| `GET` | `/api/biblio/popular` | `BiblioController@getPopular` | Daftar koleksi paling sering dipinjam/populer |
| `GET` | `/api/biblio/latest` | `BiblioController@getLatest` | Daftar koleksi terbaru yang ditambahkan |
| `GET` | `/api/biblio/gmd/[gmd]` | `BiblioController@getByGmd` | Daftar koleksi berdasarkan jenis GMD tertentu |
| `GET` | `/api/biblio/coll_type/[coll_type]` | `BiblioController@getByCollType` | Daftar koleksi berdasarkan tipe koleksi |
| `GET` | `/api/biblio/total/all` | `BiblioController@getTotalAll` | Total seluruh judul bibliografi dalam katalog |

### 3. Eksemplar (Item Koleksi)
| Method | Endpoint | Handler | Deskripsi |
|---|---|---|---|
| `GET` | `/api/item/total/all` | `ItemController@getTotalAll` | Total seluruh eksemplar fisik |
| `GET` | `/api/item/total/lent` | `ItemController@getTotalLent` | Total eksemplar yang sedang dipinjam |
| `GET` | `/api/item/total/available` | `ItemController@getTotalAvailable` | Total eksemplar yang tersedia di rak |

### 4. Subjek / Topik
| Method | Endpoint | Handler | Deskripsi |
|---|---|---|---|
| `GET` | `/api/subject/popular` | `SubjectController@getPopular` | Daftar subjek paling populer |
| `GET` | `/api/subject/latest` | `SubjectController@getLatest` | Daftar subjek terbaru |

### 5. Anggota
| Method | Endpoint | Handler | Deskripsi |
|---|---|---|---|
| `GET` | `/api/member/top` | `MemberController@getTopMember` | Daftar anggota teraktif / paling sering meminjam |

### 6. Peminjaman & Statistik Sirkulasi
| Method | Endpoint | Handler | Deskripsi |
|---|---|---|---|
| `GET` | `/api/loan/summary` | `LoanController@getSummary` | Ringkasan transaksi peminjaman |
| `GET` | `/api/loan/getdate/[start_date]` | `LoanController@getDate` | Data peminjaman mulai tanggal tertentu |
| `GET` | `/api/loan/summary/[date]` | `LoanController@getSummaryDate` | Ringkasan peminjaman pada tanggal spesifik |

---

## ⚡ HTTP Caching

SLiMS API mendukung HTTP caching. Jika request mengirimkan header `SLiMS-Http-Cache` atau `slims-http-cache`, response akan menyertakan header `Cache-Control: max-age=...` sesuai konfigurasi `$sysconf['http']['cache']['lifetime']`.

---

## 🔌 Menambah Route API Baru via Plugin Hook

SLiMS menyediakan hook `custom_api_route` di `api/v1/routes.php:L51` sehingga pengembang plugin dapat mendaftarkan endpoint API kustom tanpa mengubah file inti SLiMS:

```php
<?php
/**
 * Contoh penambahan route API kustom di file plugin: nama_plugin.plugin.php
 */
use SLiMS\Plugins;

Plugins::getInstance()->register('custom_api_route', function($params) {
    /** @var Router $router */
    $router = $params['router'];

    // Menambahkan endpoint baru: GET /api/custom-data
    $router->map('GET', '/custom-data', function() {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data' => [
                'app' => 'My SLiMS Plugin API',
                'timestamp' => time()
            ]
        ]);
        exit;
    });
});
```

---
Modul API SLiMS ini dapat diakses untuk integrasi mobile apps, sistem informasi akademik (SIAKAD), maupun otomasi perpustakaan lainnya.
