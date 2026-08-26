#!/usr/bin/env php
<?php
/**
 * SLiMS 9 Plugin Migration Test Runner
 * 
 * Script CLI untuk menguji eksekusi migrasi database plugin SLiMS (up() dan down())
 * secara terisolasi tanpa memerlukan klik manual di antarmuka web admin.
 * 
 * Penggunaan:
 *   php bin/test-migration.php /path/to/plugin_folder [/path/to/slims_root]
 * 
 * @author Belajar SLiMS Team
 * @license GPL-3.0
 */

declare(strict_types=1);

const COLOR_RESET  = "\033[0m";
const COLOR_RED    = "\033[31m";
const COLOR_GREEN  = "\033[32m";
const COLOR_YELLOW = "\033[33m";
const COLOR_BLUE   = "\033[34m";
const COLOR_BOLD   = "\033[1m";

if (php_sapi_name() !== 'cli') {
    die("Script ini hanya dapat dijalankan melalui CLI.\n");
}

$pluginPath = $argv[1] ?? '';
$slimsPath  = $argv[2] ?? '/var/www/html/slims/s951dev/98';

if (empty($pluginPath) || !is_dir($pluginPath)) {
    echo COLOR_RED . "✖ Penggunaan: php bin/test-migration.php /path/to/plugin_folder [/path/to/slims_root]\n" . COLOR_RESET;
    exit(1);
}

$pluginReal = realpath($pluginPath);
$slimsReal  = realpath($slimsPath);

if (!$slimsReal || !file_exists($slimsReal . '/config/database.php')) {
    echo COLOR_RED . "✖ Error: Direktori instalasi SLiMS tidak valid (tidak ditemukan config/database.php di {$slimsPath})\n" . COLOR_RESET;
    exit(1);
}

// Bootstrap SLiMS environment before sending any output
define('INDEX_AUTH', 1);
ob_start();
require_once $slimsReal . DIRECTORY_SEPARATOR . 'sysconfig.inc.php';
ob_end_clean();

echo COLOR_BOLD . COLOR_BLUE . "\n======================================================\n";
echo " 🗄️  SLiMS 9 Plugin Migration Test Runner\n";
echo " Plugin : {$pluginReal}\n";
echo " SLiMS  : {$slimsReal}\n";
echo "======================================================\n" . COLOR_RESET . "\n";

// Setup database connection
try {
    $db = \SLiMS\DB::getInstance();
    echo COLOR_GREEN . "  ✔ Berhasil terhubung ke database SLiMS\n" . COLOR_RESET;
} catch (\Throwable $e) {
    echo COLOR_RED . "  ✖ Gagal menghubungkan ke database SLiMS: " . $e->getMessage() . "\n" . COLOR_RESET;
    exit(1);
}

$migrationDir = $pluginReal . DIRECTORY_SEPARATOR . 'migration';
if (!is_dir($migrationDir)) {
    echo COLOR_YELLOW . "  ℹ Plugin ini tidak memiliki direktori migration/.\n\n" . COLOR_RESET;
    exit(0);
}

$files = glob($migrationDir . DIRECTORY_SEPARATOR . '*.php') ?: [];
natsort($files);

if (empty($files)) {
    echo COLOR_YELLOW . "  ℹ Direktori migration/ kosong.\n\n" . COLOR_RESET;
    exit(0);
}

echo "\n" . COLOR_BOLD . "▶ Menjalankan Pengujian Migrasi UP()...\n" . COLOR_RESET;
$hasErrors = false;

foreach ($files as $file) {
    $filename = basename($file);
    require_once $file;

    if (!preg_match('/^\d+_(.+)\.php$/', $filename, $m)) {
        echo COLOR_RED . "  ✖ Format nama file salah: {$filename}\n" . COLOR_RESET;
        $hasErrors = true;
        continue;
    }

    $className = $m[1];
    if (!class_exists($className)) {
        echo COLOR_RED . "  ✖ Class {$className} tidak ditemukan di dalam {$filename}\n" . COLOR_RESET;
        $hasErrors = true;
        continue;
    }

    try {
        $migration = new $className();
        $migration->up();
        echo COLOR_GREEN . "  ✔ [UP SUCCESS]   {$filename} -> {$className}::up()\n" . COLOR_RESET;
    } catch (\Throwable $e) {
        echo COLOR_RED . "  ✖ [UP FAILED]    {$filename}: " . $e->getMessage() . "\n" . COLOR_RESET;
        $hasErrors = true;
    }
}

echo "\n" . COLOR_BOLD . "----------------- HASIL PENGUJIAN MIGRASI -----------------" . COLOR_RESET . "\n";
if (!$hasErrors) {
    echo COLOR_BOLD . COLOR_GREEN . " 🎉 Seluruh skrip migrasi plugin berhasil dieksekusi tanpa error!\n\n" . COLOR_RESET;
    exit(0);
} else {
    echo COLOR_BOLD . COLOR_RED . " ❌ Terdapat error saat eksekusi migrasi. Harap periksa detail di atas.\n\n" . COLOR_RESET;
    exit(1);
}
