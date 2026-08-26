# Plugin SLiMS: Error 'Plugin not found / disable' dan Parameter Wajib

Ketika menghadapi pesan kesalahan seperti **"Plugin error plugin not found / disable?"** di SLiMS, pemahaman tentang cara kerja parameter plugin sangatlah penting.

Setiap plugin SLiMS memerlukan dua ($\text{2}$) parameter utama yang harus selalu ada agar sistem dapat mengenalinya dan menjalankannya dengan benar.

## Dua ($\text{2}$) Parameter Wajib Plugin SLiMS

Plugin SLiMS harus membawa dua ($\text{2}$) parameter wajib:

1.  **`mod`**: Ini adalah **ID Modul** (kategori menu SLiMS) tempat plugin tersebut didaftarkan (misalnya `bibliography`, `circulation`, `membership`, `system`, dll.).
2.  **`id`**: Ini adalah **ID Plugin** yang merupakan string hash MD5 dari `realpath` file menu plugin yang dihasilkan secara otomatis oleh sistem registrasi SLiMS.

## Dari Mana Mendapatkan Parameter `mod` dan `id`?

Kedua ($\text{2}$) parameter ini didapatkan dari:

1.  **Variabel Global `$_GET`**: Parameter ini dibaca oleh `admin/plugin_container.php` dari URL query string (`$_GET['mod']` dan `$_GET['id']`).
2.  **Tautan Sub Menu**: Ketika plugin didaftarkan via `$plugins->registerMenu(...)`, SLiMS membuat tautan otomatis menuju wrapper `admin/plugin_container.php?mod=...&id=...`.

> **Contoh di URL Backend:**
> `http://nama_domain/admin/plugin_container.php?mod=system&id=e4d909c290d0fb1ca068ffaddf22cbd0`

> [!WARNING]
> Jika Anda membuat form submission (`POST`/`GET`) di dalam plugin tanpa menyertakan `mod` dan `id` asli pada `action` URL, request akan ditolak oleh `plugin_container.php` dengan pesan **"Plugin not found / disabled!"**. Selalu gunakan `$_SERVER['PHP_SELF'] . '?' . http_build_query($_GET)` atau `pluginUrl(...)` sebagai action form.


