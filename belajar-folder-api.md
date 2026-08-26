# 🔗 Struktur & Dokumentasi API SLiMS 9 Bulian (`/api`)

SLiMS 9 Bulian menyediakan layanan **RESTful Web Service API** resmi yang berlokasi di direktori `/api/v1/`. API ini dirancang dengan pola MVC Router (`AltoRouter`) untuk mempermudah integrasi sistem pihak ketiga seperti aplikasi mobile perpustakaan, dashboard monitor, Sistem Informasi Akademik (SIAKAD), maupun sistem RFID/otomasi perpustakaan.

---

## 📁 Struktur Asli Direktori `/api` di SLiMS 9

```plaintext
/api
└── v1/                          # Versi 1 RESTful API SLiMS
    ├── routes.php               # Konfigurasi rute URL & pemetaan HTTP method
    ├── controllers/             # Controller penangan logika data
    │   ├── Controller.php       # Base controller API
    │   ├── HomeController.php   # Endpoint informasi index API
    │   ├── BiblioController.php # Endpoint bibliografi & katalog
    │   ├── ItemController.php   # Endpoint eksemplar & ketersediaan buku
    │   ├── MemberController.php # Endpoint anggota & top member
    │   ├── LoanController.php   # Endpoint transaksi sirkulasi & statistik
    │   └── SubjectController.php# Endpoint topik/subjek koleksi
    └── helpers/                 # Helper pendukung
        ├── Cache.php            # Cache response API
        └── Image.php            # Helper pemrosesan cover gambar
```

---

## 🛣️ Daftar Endpoint REST API Resmi SLiMS 9

### 1. 📚 Endpoint Bibliografi & Koleksi
| HTTP Method | Endpoint URI | Deskripsi | Controller Action |
|:---:|---|---|---|
| `GET` | `/api/biblio/popular` | Mendapatkan daftar buku terpopuler/paling banyak dipinjam | `BiblioController@getPopular` |
| `GET` | `/api/biblio/latest` | Mendapatkan daftar koleksi terbaru | `BiblioController@getLatest` |
| `GET` | `/api/biblio/gmd/[gmd_name]` | Filter bibliografi berdasarkan format GMD (Text, Art, CD, dll.) | `BiblioController@getByGmd` |
| `GET` | `/api/biblio/coll_type/[type]` | Filter bibliografi berdasarkan tipe koleksi (Referensi, Tandon, dll.) | `BiblioController@getByCollType` |
| `GET` | `/api/biblio/total/all` | Statistik total seluruh judul bibliografi | `BiblioController@getTotalAll` |

---

### 2. 📦 Endpoint Eksemplar & Ketersediaan Item
| HTTP Method | Endpoint URI | Deskripsi | Controller Action |
|:---:|---|---|---|
| `GET` | `/api/item/total/all` | Mendapatkan total seluruh jumlah eksemplar buku | `ItemController@getTotalAll` |
| `GET` | `/api/item/total/lent` | Mendapatkan jumlah total buku yang sedang dipinjam | `ItemController@getTotalLent` |
| `GET` | `/api/item/total/available` | Mendapatkan jumlah buku yang tersedia di rak | `ItemController@getTotalAvailable` |

---

### 3. 👥 Endpoint Anggota & Subjek
| HTTP Method | Endpoint URI | Deskripsi | Controller Action |
|:---:|---|---|---|
| `GET` | `/api/member/top` | Mendapatkan daftar anggota paling aktif meminjam | `MemberController@getTopMember` |
| `GET` | `/api/subject/popular` | Mendapatkan daftar subjek/topik terpopuler | `SubjectController@getPopular` |
| `GET` | `/api/subject/latest` | Mendapatkan daftar subjek/topik terbaru | `SubjectController@getLatest` |

---

### 4. 🔄 Endpoint Sirkulasi & Statistik Peminjaman
| HTTP Method | Endpoint URI | Deskripsi | Controller Action |
|:---:|---|---|---|
| `GET` | `/api/loan/summary` | Ringkasan statistik transaksi peminjaman | `LoanController@getSummary` |
| `GET` | `/api/loan/getdate/[start_date]` | Data peminjaman berdasarkan tanggal mulai | `LoanController@getDate` |
| `GET` | `/api/loan/summary/[date]` | Ringkasan transaksi peminjaman pada tanggal tertentu | `LoanController@getSummaryDate` |

---

## 🔌 Ekstensibilitas API via Plugin Hook (`custom_api_route`)

Salah satu fitur terbaik pada router API SLiMS 9 adalah dukungan penambahan rute API kustom melalui plugin tanpa mengubah file inti `/api/v1/routes.php`.

Contoh implementasi hook pada file `my_plugin.plugin.php`:
```php
use SLiMS\Plugins;

$plugins = Plugins::getInstance();

$plugins->register('custom_api_route', function($args) {
    $router = $args['router'];
    
    // Daftarkan endpoint custom API
    $router->map('GET', '/custom/statistik-ruangan', function() {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data' => [
                'ruangan_tersedia' => 5,
                'ruangan_terpakai' => 2
            ]
        ]);
        exit;
    });
});
```

---

## 💡 Contoh Permintaan & Respons API

### Request:
```bash
curl -X GET "https://perpustakaan.kampus.ac.id/api/biblio/popular"
```

### Response (JSON):
```json
{
  "status": 200,
  "message": "success",
  "data": [
    {
      "biblio_id": 104,
      "title": "Dasar-Dasar Ilmu Perpustakaan dan Informasi",
      "author": "Sulistyo-Basuki",
      "image": "https://perpustakaan.kampus.ac.id/images/docs/cov_104.jpg",
      "total_loan": 45
    }
  ]
}
```
