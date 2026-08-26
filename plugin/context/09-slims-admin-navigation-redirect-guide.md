# 🧭 Panduan Menjaga Navigasi Plugin Tetap di Halaman Admin SLiMS (Anti-Breakout)

Panduan ini membahas penyebab umum mengapa tombol, link, form, atau redirect pada plugin SLiMS tiba-tiba **keluar dari antarmuka plugin / melempar user ke halaman dashboard utama admin (`admin/index.php`)** beserta solusi standar pencegahannya.

---

## 🚨 Penyebab Utama Plugin Terlempar ke Halaman Awal Admin

Di SLiMS 9, antarmuka admin plugin dijalankan melalui container:
`admin/plugin_container.php?mod={nama_modul}&id={hash_plugin}&sec={sub_halaman}`

Jika link, tombol, atau redirect kehilangan parameter `mod` dan `id`, sistem SLiMS tidak mengenali plugin tujuan dan otomatis mengalihkan browser ke **Dashboard Utama Admin (`admin/index.php`)** atau memunculkan pesan error *"Plugin not found or disabled"*.

---

## 🛠️ 4 Pola Standar Menjaga Navigasi Plugin

### 1. Pola Link Tombol Aksi (Anchor `<a>` Tags)

❌ **SALAH (Akan Melempar ke Dashboard Admin / Error)**:
```html
<!-- ❌ Relatif query memotong parameter 'mod' dan 'id' -->
<a href="?action=edit&id=10">Sunting Data</a>

<!-- ❌ Mengarah ke index.php admin -->
<a href="index.php?action=edit&id=10">Sunting Data</a>
```

✅ **BENAR (Menjaga Parameter Modul & ID Hash)**:
```php
<?php
// Buat URL dengan menggabungkan parameter $_GET yang sedang aktif
$editParams = array_merge($_GET, ['action' => 'edit', 'item_id' => $row['id']]);
$editUrl = $_SERVER['PHP_SELF'] . '?' . http_build_query($editParams);
?>

<a href="<?= htmlspecialchars($editUrl) ?>" class="btn btn-primary btn-sm">
    Sunting Data
</a>
```

---

### 2. Pola Redirect Backend (`header('Location: ...')`)

Setelah memproses form (misal: simpan/hapus data), script PHP sering melakukan redirect.

❌ **SALAH (Akan Keluar dari Plugin ke Dashboard Admin)**:
```php
<?php
// ❌ Melempar ke halaman depan admin
header('Location: index.php');
exit;

// ❌ Menghapus parameter mod dan id
header('Location: ?msg=success');
exit;
```

✅ **BENAR (Tetap Berada di Halaman Plugin dengan Pesan Sukses)**:
```php
<?php
// 1. Siapkan parameter tujuan dengan mempertahankan parameter GET aktif
$redirectParams = array_merge($_GET, [
    'action'  => 'view',
    'status'  => 'success',
    'message' => 'Data berhasil disimpan'
]);

// 2. Hapus parameter yang tidak dibutuhkan lagi saat redirect
unset($redirectParams['submit_btn']);

$targetUrl = $_SERVER['PHP_SELF'] . '?' . http_build_query($redirectParams);

// 3. Eksekusi redirect dan WAJIB panggil exit;
header('Location: ' . $targetUrl);
exit;
```

---

### 3. Pola Submit Form (POST & GET)

❌ **SALAH**:
```html
<!-- ❌ Action kosong atau statis tanpa query string -->
<form action="index.php" method="POST">
<form method="GET" action="index.php">
```

✅ **BENAR untuk Form POST**:
```html
<!-- Form action mempertahankan seluruh query string URL (mod, id, dll) -->
<form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF'] . '?' . http_build_query($_GET)) ?>">
    <?= csrfField() ?>
    <!-- Input Form -->
    <button type="submit" name="save_data" class="btn btn-success">Simpan</button>
</form>
```

✅ **BENAR untuk Form GET (Pencarian / Filter)**:
```html
<form method="GET" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
    <!-- ⚠️ KRITIS: Hidden input mod dan id WAJIB ada karena method GET akan mereset query string URL -->
    <input type="hidden" name="mod" value="<?= htmlspecialchars($_GET['mod'] ?? '') ?>" />
    <input type="hidden" name="id" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>" />
    
    <input type="text" name="keywords" value="<?= htmlspecialchars($_GET['keywords'] ?? '') ?>" />
    <button type="submit" class="btn btn-primary">Cari</button>
</form>
```

---

### 4. Pola Redirect JavaScript & AJAX

❌ **SALAH**:
```javascript
// ❌ Akan keluar dari plugin
window.location.href = 'index.php';
location.href = '?status=deleted';
```

✅ **BENAR**:
```php
<?php
$returnUrl = $_SERVER['PHP_SELF'] . '?' . http_build_query(array_merge($_GET, ['action' => 'list']));
?>
<script>
    if (confirm('Yakin ingin memproses data ini?')) {
        window.location.href = <?= json_encode($returnUrl) ?>;
    }
</script>
```

---

## 📋 Checklist Anti-Breakout Plugin SLiMS

- [ ] Seluruh tag `<a href="...">` tidak menggunakan link statis `?param=...` atau `index.php`.
- [ ] Semua `header('Location: ...')` menggunakan `$_SERVER['PHP_SELF'] . '?' . http_build_query(...)` dan diakhiri `exit;`.
- [ ] Form `method="GET"` memiliki hidden field `mod` dan `id`.
- [ ] Link internal tidak menggunakan `target="_top"` atau `target="_parent"` tanpa alasan khusus.
- [ ] Hasil audit `php bin/check-plugin.php /path/to/plugin` menunjukkan **[PASS] Navigasi & Redirect**.
