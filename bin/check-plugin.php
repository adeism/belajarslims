#!/usr/bin/env php
<?php
/**
 * SLiMS 9 Plugin Linter & Validation Harness
 * 
 * Script CLI untuk memvalidasi kepatuhan plugin SLiMS 9 Bulian terhadap
 * standar keamanan, arsitektur, guardrails Simbio, dan integritas migrasi.
 * 
 * Penggunaan:
 *   php bin/check-plugin.php /path/to/plugin_folder
 * 
 * @author Belajar SLiMS Team
 * @license GPL-3.0
 */

declare(strict_types=1);

// PHP 7.4 Compatibility Polyfills
if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool {
        return $needle === '' || mb_strpos($haystack, $needle) !== false;
    }
}
if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool {
        return $needle === '' || strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool {
        return $needle === '' || $needle === substr($haystack, -strlen($needle));
    }
}

// ANSI Colors
const COLOR_RESET  = "\033[0m";
const COLOR_RED    = "\033[31m";
const COLOR_GREEN  = "\033[32m";
const COLOR_YELLOW = "\033[33m";
const COLOR_BLUE   = "\033[34m";
const COLOR_BOLD   = "\033[1m";

class PluginValidator
{
    private string $pluginPath;
    private array $errors = [];
    private array $warnings = [];
    private array $passes = [];
    private array $phpFiles = [];
    private array $pluginEntryFiles = [];

    private const VALID_MENU_CATEGORIES = [
        'bibliography',
        'circulation',
        'membership',
        'master_file',
        'reporting',
        'serial_control',
        'stock_take',
        'system',
        'opac'
    ];

    private const INVALID_HOOKS = [
        'Plugins::ADMIN_HEADER' => 'ADMIN_SESSION_AFTER_START atau sisipkan di template',
        'Plugins::OPAC_HEADER' => 'CONTENT_BEFORE_LOAD atau sisipkan di template OPAC',
        'Plugins::BEFORE_CIRCULATION' => 'Gunakan hook sirkulasi yang valid',
        'Plugins::AFTER_CIRCULATION' => 'CIRCULATION_AFTER_SUCCESSFUL_TRANSACTION'
    ];

    public function __construct(string $pluginPath)
    {
        $realPath = realpath($pluginPath);
        if (!$realPath || (!is_dir($realPath) && !is_file($realPath))) {
            echo COLOR_RED . "✖ Error: Berkas/Direktori plugin tidak ditemukan: {$pluginPath}\n" . COLOR_RESET;
            exit(1);
        }
        $this->pluginPath = is_dir($realPath) ? rtrim($realPath, DIRECTORY_SEPARATOR) : $realPath;
    }

    public function run(): int
    {
        echo COLOR_BOLD . COLOR_BLUE . "\n======================================================\n";
        echo " 🔍 SLiMS 9 Bulian Plugin Validator & Test Harness\n";
        echo " Target: " . $this->pluginPath . "\n";
        echo "======================================================\n" . COLOR_RESET . "\n";

        $this->scanFiles();
        $this->checkSyntax();
        $this->checkPluginEntryFile();
        $this->checkSecurityGuardrails();
        $this->checkSimbioUsage();
        $this->checkMigrationIntegrity();
        $this->checkSqlSafety();
        $this->checkFormAndUrls();
        $this->checkExportHandlers();
        $this->checkFilterAndPagination();
        $this->checkNavigationAndRedirects();

        $this->printSummary();

        return count($this->errors) > 0 ? 1 : 0;
    }

    private function scanFiles(): void
    {
        if (is_file($this->pluginPath)) {
            $this->phpFiles[] = $this->pluginPath;
            if (strpos(basename($this->pluginPath), '.plugin.php') !== false) {
                $this->pluginEntryFiles[] = $this->pluginPath;
            }
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->pluginPath, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $filePath = $file->getPathname();
                // Skip external packages (vendor, node_modules, tests)
                if (strpos($filePath, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR) !== false ||
                    strpos($filePath, DIRECTORY_SEPARATOR . 'node_modules' . DIRECTORY_SEPARATOR) !== false ||
                    strpos($filePath, DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR) !== false) {
                    continue;
                }
                $this->phpFiles[] = $filePath;

                if (strpos($file->getFilename(), '.plugin.php') !== false) {
                    $this->pluginEntryFiles[] = $filePath;
                }
            }
        }
    }

    private function checkSyntax(): void
    {
        $hasSyntaxError = false;
        foreach ($this->phpFiles as $file) {
            $rel = $this->relative($file);
            try {
                if (function_exists('token_get_all')) {
                    @token_get_all(file_get_contents($file), TOKEN_PARSE);
                }
            } catch (\ParseError $pe) {
                $this->error("Sintaks PHP error pada [{$rel}]: " . $pe->getMessage());
                $hasSyntaxError = true;
            }
        }
        if (!$hasSyntaxError) {
            $this->pass("Sintaks PHP: Seluruh (" . count($this->phpFiles) . ") berkas PHP valid.");
        }
    }

    private function checkPluginEntryFile(): void
    {
        if (empty($this->pluginEntryFiles)) {
            $this->error("File utama plugin (*.plugin.php) tidak ditemukan di dalam direktori plugin.");
            return;
        }

        foreach ($this->pluginEntryFiles as $entryFile) {
            $rel = $this->relative($entryFile);
            $content = file_get_contents($entryFile);

            // Check metadata
            if (preg_match('/\*\s*Plugin Name\s*:/i', $content)) {
                $this->pass("Metadata Header terdeteksi pada [{$rel}].");
            } else {
                $this->warn("Header komentar 'Plugin Name:' sebaiknya disertakan pada [{$rel}].");
            }

            // CRITICAL: No define('INDEX_AUTH', 1) in *.plugin.php
            if (preg_match('/define\s*\(\s*[\'"]INDEX_AUTH[\'"]\s*,\s*[1\'"]\s*\)/i', $content)) {
                $this->error("KRITIS: 'define(\\'INDEX_AUTH\\', 1)' DILARANG berada di dalam file [{$rel}]. Plugin file di-load saat bootstrapping autoloader!");
            } else {
                $this->pass("Entry point guard: [{$rel}] bersih dari pendefinisian paksa INDEX_AUTH.");
            }

            // Check menu registration categories
            if (preg_match_all('/(?:registerMenu|menu)\s*\(\s*[\'"]([^\'"]+)[\'"]/i', $content, $matches)) {
                foreach ($matches[1] as $cat) {
                    if ($cat === 'admin') {
                        $this->error("Kategori menu 'admin' pada [{$rel}] TIDAK VALID di SLiMS. Gunakan salah satu dari: " . implode(', ', self::VALID_MENU_CATEGORIES));
                    } elseif (!in_array($cat, self::VALID_MENU_CATEGORIES, true)) {
                        $this->warn("Kategori menu '{$cat}' pada [{$rel}] bukan kategori standar SLiMS.");
                    } else {
                        $this->pass("Kategori menu '{$cat}' pada [{$rel}] valid.");
                    }
                }
            }

            // Check non-existent hooks
            foreach (self::INVALID_HOOKS as $badHook => $suggestion) {
                if (str_contains($content, $badHook)) {
                    $this->error("Hook [{$badHook}] pada [{$rel}] TIDAK ADA di core SLiMS. Saran: {$suggestion}");
                }
            }
        }
    }

    private function checkSecurityGuardrails(): void
    {
        foreach ($this->phpFiles as $file) {
            if (in_array($file, $this->pluginEntryFiles, true)) {
                continue;
            }

            $rel = $this->relative($file);
            $content = file_get_contents($file);

            // Skip migration files for index_auth
            if (str_contains($file, DIRECTORY_SEPARATOR . 'migration' . DIRECTORY_SEPARATOR)) {
                continue;
            }

            // Check direct access guard
            if (!str_contains($content, 'INDEX_AUTH')) {
                $this->warn("File antarmuka [{$rel}] belum memiliki proteksi akses 'defined(\\'INDEX_AUTH\\') || die(...)'.");
            }

            // Check deprecated DB_ACCESS
            if (str_contains($content, 'DB_ACCESS')) {
                $this->error("Konstanta usang 'DB_ACCESS' terdeteksi pada [{$rel}]. Gunakan 'INDEX_AUTH'.");
            }

            // Check invalid privilege level 'rw'
            if (preg_match('/havePrivilege\s*\([^,]+,\s*[\'"]rw[\'"]\s*\)/i', $content)) {
                $this->error("Nilai privilege 'rw' pada [{$rel}] tidak valid untuk utility::havePrivilege(). Hanya mendukung 'r' atau 'w'.");
            }
        }
    }

    private function checkSimbioUsage(): void
    {
        foreach ($this->phpFiles as $file) {
            $rel = $this->relative($file);
            $content = file_get_contents($file);

            // Check simbio_table include without class_exists guard
            if (preg_match('/(?:require|include)(?:_once)?\s+.*simbio_table\.inc\.php/i', $content)) {
                if (!str_contains($content, "class_exists('simbio_table')") && !str_contains($content, 'class_exists("simbio_table")')) {
                    $this->error("Pemuatan 'simbio_table.inc.php' pada [{$rel}] harus dibungkus guard 'if (!class_exists(\\'simbio_table\\'))' untuk mencegah redeclaration fatal error.");
                } else {
                    $this->pass("Simbio lazy loading guard terpasang dengan benar pada [{$rel}].");
                }
            }

            // Check datagrid invisibleFields (camelCase) vs invisible_fields (snake_case array)
            if (preg_match('/->invisibleFields\s*=/i', $content)) {
                $this->error("Properti '->invisibleFields' pada [{$rel}] keliru (camelCase). Seharusnya '->invisible_fields = [0, ...];' (array snake_case).");
            }
        }
    }

    private function checkMigrationIntegrity(): void
    {
        $migrationDir = $this->pluginPath . DIRECTORY_SEPARATOR . 'migration';
        if (!is_dir($migrationDir)) {
            return;
        }

        $migrationFiles = glob($migrationDir . DIRECTORY_SEPARATOR . '*.php') ?: [];
        if (empty($migrationFiles)) {
            $this->warn("Folder migration/ ada tetapi kosong.");
            return;
        }

        foreach ($migrationFiles as $mFile) {
            $filename = basename($mFile);
            $rel = $this->relative($mFile);
            $content = file_get_contents($mFile);

            // Check naming pattern: {number}_{ClassName}.php
            if (!preg_match('/^(\d+)_(.+)\.php$/', $filename, $m)) {
                $this->error("Format nama berkas migrasi [{$rel}] tidak sesuai konvensi SLiMS: '{number}_{ClassName}.php' (contoh: '1_CreateMyTable.php').");
                continue;
            }

            $expectedClass = $m[2];

            // Check class definition and inheritance
            if (preg_match('/class\s+([a-zA-Z0-9_]+)\s+extends\s+([a-zA-Z0-9_\\\\]+)/i', $content, $classMatches)) {
                $actualClass = $classMatches[1];
                $baseClass = $classMatches[2];

                if ($actualClass !== $expectedClass) {
                    $this->error("Nama class [{$actualClass}] pada [{$rel}] tidak cocok dengan nama file. Seharusnya class [{$expectedClass}].");
                } else {
                    $this->pass("Class migrasi [{$actualClass}] pada [{$rel}] sesuai konvensi nama berkas.");
                }

                if (!str_contains($baseClass, 'Migration')) {
                    $this->error("Class migrasi [{$actualClass}] pada [{$rel}] harus mewarisi 'SLiMS\\Migration\\Migration'.");
                }
            } else {
                $this->error("Tidak ditemukan deklarasi class migrasi yang valid pada [{$rel}].");
            }

            // Check up() and down() methods
            if (!preg_match('/(?:public\s+)?function\s+up\s*\(/i', $content)) {
                $this->error("Metode 'public function up()' tidak ditemukan pada migrasi [{$rel}].");
            }
            if (!preg_match('/(?:public\s+)?function\s+down\s*\(/i', $content)) {
                $this->error("Metode 'public function down()' tidak ditemukan pada migrasi [{$rel}].");
            }
        }
    }

    private function checkSqlSafety(): void
    {
        foreach ($this->phpFiles as $file) {
            $rel = $this->relative($file);
            $content = file_get_contents($file);

            // Check for is_active column on plugins table
            if (preg_match('/UPDATE\s+plugins\s+SET\s+is_active\s*=/i', $content)) {
                $this->error("Query 'UPDATE plugins SET is_active = ...' pada [{$rel}] tidak valid. Kolom 'is_active' tidak ada di SLiMS 9 (gunakan 'deleted_at').");
            }
        }
    }

    private function checkFormAndUrls(): void
    {
        foreach ($this->phpFiles as $file) {
            $rel = $this->relative($file);
            $content = file_get_contents($file);

            // Check hardcoded id in form action
            if (preg_match('/<form[^>]+action=[\'"][^\'"]*\?mod=[^&\'"]+&id=[a-zA-Z0-9_]+[\'"]/i', $content, $matches)) {
                $this->warn("Form action pada [{$rel}] tampaknya meng-hardcode nilai 'id='. Seharusnya gunakan \$_SERVER['PHP_SELF'] . '?' . http_build_query(\$_GET) karena 'id' adalah string hash MD5.");
            }
        }
    }

    private function checkExportHandlers(): void
    {
        $exportDetected = false;

        foreach ($this->phpFiles as $file) {
            $rel = $this->relative($file);
            $content = file_get_contents($file);

            $isExportFile = false;
            $exportTypes = [];

            // Check CSV export
            if (preg_match('/Content-Type:\s*text\/csv/i', $content) || preg_match('/fputcsv\s*\(/i', $content) || str_contains($content, 'SLiMS\\Csv\\Builder')) {
                $isExportFile = true;
                $exportTypes[] = 'CSV';
            }

            // Check Excel export
            if (preg_match('/Content-Type:\s*application\/vnd\.(?:ms-excel|openxmlformats)/i', $content) || (preg_match('/Spreadsheet|xlsx|xls/i', $content) && str_contains($content, 'Content-Disposition'))) {
                $isExportFile = true;
                $exportTypes[] = 'Excel';
            }

            // Check PDF export
            if (preg_match('/Content-Type:\s*application\/pdf/i', $content) || preg_match('/\b(?:Dompdf|TCPDF|FPDF|Mpdf)\b/', $content) || str_contains($content, 'printed_page_tpl.php')) {
                $isExportFile = true;
                $exportTypes[] = 'PDF/Print';
            }

            // Check general Content-Disposition attachment
            if (preg_match('/Content-Disposition:\s*attachment/i', $content)) {
                $isExportFile = true;
            }

            if (!$isExportFile) {
                continue;
            }

            $exportDetected = true;
            $typeLabel = !empty($exportTypes) ? implode('/', array_unique($exportTypes)) : 'File Download';

            // Check 1: Mandatory exit/die after file streaming
            if (preg_match('/Content-Disposition:\s*attachment/i', $content) || preg_match('/Content-Type:\s*(?:text\/csv|application\/pdf|application\/vnd)/i', $content)) {
                if (!preg_match('/(?:exit|die)\s*(?:\(.*\))?\s*;/i', $content)) {
                    $this->error("Ekspor [{$typeLabel}] pada [{$rel}] tidak memiliki perintah 'exit;' atau 'die();'. Hal ini menyebabkan template HTML SLiMS bocor dan merusak berkas hasil unduhan.");
                } else {
                    $this->pass("Ekspor [{$typeLabel}]: Handler pada [{$rel}] memiliki terminasi 'exit;' yang tepat.");
                }
            }

            // Check 2: Privilege check in export file
            if (!str_contains($content, 'havePrivilege') && !str_contains($content, 'session_check')) {
                $this->warn("Fitur ekspor [{$typeLabel}] pada [{$rel}] sebaiknya dilindungi dengan pengecekan hak akses 'utility::havePrivilege()'.");
            } else {
                $this->pass("Fitur ekspor [{$typeLabel}] pada [{$rel}] memiliki proteksi otorisasi sesi.");
            }

            // Check 3: Output buffer cleaning (ob_clean / ob_end_clean) before headers
            if (preg_match('/Content-Disposition:\s*attachment/i', $content)) {
                if (!preg_match('/ob_(?:end_)?clean\s*\(\s*\)/i', $content)) {
                    $this->warn("Ekspor [{$typeLabel}] pada [{$rel}]: Disarankan menggunakan 'ob_clean();' sebelum mengirim header download untuk mencegah kontaminasi whitespace/BOM.");
                } else {
                    $this->pass("Ekspor [{$typeLabel}]: Output buffer dibersihkan dengan 'ob_clean()' sebelum pengiriman berkas pada [{$rel}].");
                }
            }

            // Check 4: Printed template path verification
            if (str_contains($content, 'printed_page_tpl.php')) {
                if (preg_match('/admin_template\/default\/printed_page_tpl\.php/i', $content)) {
                    $this->error("Path template cetak pada [{$rel}] salah ('admin_template/default/printed_page_tpl.php'). Berkas 'printed_page_tpl.php' terletak langsung di bawah 'admin/admin_template/printed_page_tpl.php'.");
                } else {
                    $this->pass("Template cetak laporan SLiMS dipanggil dengan path yang benar pada [{$rel}].");
                }
            }
        }

        if (!$exportDetected) {
            $this->pass("Fitur Ekspor: Berkas plugin bersih atau tidak menggunakan handler ekspor kustom.");
        }
    }

    private function checkFilterAndPagination(): void
    {
        $filterDetected = false;

        foreach ($this->phpFiles as $file) {
            $rel = $this->relative($file);
            $content = file_get_contents($file);

            // 1. Check simbio_paging include without class_exists guard
            if (preg_match('/(?:require|include)(?:_once)?\s+.*simbio_paging\.inc\.php/i', $content)) {
                if (!str_contains($content, "class_exists('simbio_paging')") && !str_contains($content, 'class_exists("simbio_paging")')) {
                    $this->error("Pemuatan 'simbio_paging.inc.php' pada [{$rel}] harus dibungkus guard 'if (!class_exists(\\'simbio_paging\\'))' untuk mencegah redeclaration fatal error.");
                } else {
                    $this->pass("Paginasi Simbio: Guard 'class_exists(\\'simbio_paging\\')' terpasang pada [{$rel}].");
                }
            }

            // 2. Check GET Form missing hidden mod and id inputs in Admin interface
            if (preg_match('/<form[^>]+method=[\'"]GET[\'"]/i', $content)) {
                $filterDetected = true;
                if (!str_contains($file, 'opac')) {
                    $hasMod = preg_match('/<input[^>]+name=[\'"]mod[\'"]/i', $content) || str_contains($content, "name=\"mod\"") || str_contains($content, "name='mod'");
                    $hasId  = preg_match('/<input[^>]+name=[\'"]id[\'"]/i', $content) || str_contains($content, "name=\"id\"") || str_contains($content, "name='id'");
                    
                    if (!$hasMod || !$hasId) {
                        $this->warn("Filter Form (GET) pada [{$rel}]: Tidak ditemukan input hidden 'mod' dan/atau 'id'. Form submission GET di admin SLiMS akan menghapus query string URL dan menyebabkan error 'Plugin not found / disabled!'. Tambahkan: <input type=\"hidden\" name=\"mod\" value=\"<?= htmlspecialchars(\$_GET['mod'] ?? '') ?>\" /> dan input hidden 'id'.");
                    } else {
                        $this->pass("Filter Form (GET): Hidden input 'mod' dan 'id' terpasang dengan benar pada [{$rel}].");
                    }
                }
            }

            // 3. Check SQL injection in LIKE query filter
            if (preg_match('/LIKE\s*[\'"]%?\s*[\'"]\s*\.\s*\$_GET/i', $content) || 
                preg_match('/LIKE\s*[\'"]%?\{\$_GET\[/i', $content) ||
                preg_match('/WHERE\s+[a-zA-Z0-9_.]+\s*=\s*[\'"]\s*\.\s*\$_GET/i', $content)) {
                $this->error("Potensi SQL Injection pada filter query di [{$rel}]: Penggabungan langsung variabel \$_GET ke query SQL. Gunakan \$dbs->escape_string() atau Prepared Statements.");
            }

            // 4. Check custom pagination page/offset safety
            if (preg_match('/\$_GET\[[\'"]page[\'"]\]/i', $content) || preg_match('/\$_GET\[[\'"]paged[\'"]\]/i', $content)) {
                $filterDetected = true;
                if (!preg_match('/\(int\)\s*\$_GET\[[\'"]page/i', $content) && !preg_match('/intval\s*\(\s*\$_GET\[[\'"]page/i', $content) && !preg_match('/filter_var\s*\(\s*\$_GET\[[\'"]page/i', $content)) {
                    $this->warn("Paginasi kustom pada [{$rel}]: Parameter \$_GET['page'] sebaiknya di-cast integer secara eksplisit: (int)(\$_GET['page'] ?? 1) untuk mencegah nilai negatif/injeksi.");
                } else {
                    $this->pass("Paginasi kustom pada [{$rel}]: Parameter halaman telah di-sanitize integer.");
                }
            }

            // 5. Check Quick Filter button links missing query preservation
            if (preg_match('/<a[^>]+href=[\'"]\?(?:status|filter|tab)=[^\'"]+[\'"]/i', $content, $m)) {
                $this->warn("Tombol Quick Filter pada [{$rel}] menggunakan link statis '{$m[0]}'. Ini dapat memutus parameter 'mod' dan 'id' plugin. Seharusnya gunakan http_build_query(array_merge(\$_GET, ['status' => '...'])).");
            }
        }

        if (!$filterDetected) {
            $this->pass("Filter & Paginasi: Pemeriksaan filter dan paginasi selesai.");
        }
    }

    private function checkNavigationAndRedirects(): void
    {
        foreach ($this->phpFiles as $file) {
            $rel = $this->relative($file);
            $content = file_get_contents($file);
            $isOpacFile = str_contains($file, 'opac') || str_contains($content, "do_checkIP('opac')") || str_contains($content, "do_checkIP(\"opac\")");

            // 1. PHP header('Location: ...') redirects breaking out of plugin
            if (preg_match_all('/header\s*\(\s*[\'"]Location:\s*([^\'"]+)[\'"]\s*\)/i', $content, $matches)) {
                foreach ($matches[1] as $redirectTarget) {
                    $target = trim($redirectTarget);

                    // Skip OPAC p= routing for OPAC files
                    if ($isOpacFile && (str_starts_with($target, '?p=') || str_starts_with($target, 'index.php?p='))) {
                        continue;
                    }

                    // Case A: Redirect to index.php directly in admin
                    if ($target === 'index.php' || str_starts_with($target, 'index.php?')) {
                        if (!str_contains($target, 'mod=') && !str_contains($target, 'id=') && !str_contains($target, 'p=')) {
                            $this->error("Redirect bahaya pada [{$rel}]: 'header(\"Location: {$target}\")' akan mengeluarkan user dari plugin dan melempar ke dashboard utama admin. Gunakan '\$_SERVER[\'PHP_SELF\'] . \\'?\\' . http_build_query(array_merge(\$_GET, [...]))'.");
                        }
                    }

                    // Case B: Redirect to relative query string without mod and id in admin
                    if (str_starts_with($target, '?')) {
                        if (!str_contains($target, 'mod=') && !str_contains($target, 'id=') && !str_contains($target, 'p=')) {
                            $this->error("Redirect bahaya pada [{$rel}]: 'header(\"Location: {$target}\")' menghapus parameter mod/id plugin. Gunakan http_build_query(\$_GET).");
                        }
                    }
                }
            }

            // 2. Anchor tags (<a href="...">) with broken relative links
            if (preg_match_all('/<a[^>]+href=[\'"]([^\'"]+)[\'"]/i', $content, $linkMatches)) {
                foreach ($linkMatches[1] as $href) {
                    $h = trim($href);
                    
                    if (str_starts_with($h, '#') || str_starts_with($h, 'javascript:') || str_starts_with($h, 'mailto:') || str_starts_with($h, 'http') || str_contains($h, '<?=')) {
                        continue;
                    }

                    // In OPAC context, ?p= or index.php?p= is native SLiMS OPAC routing
                    if ($isOpacFile && (str_starts_with($h, '?p=') || str_starts_with($h, 'index.php?p='))) {
                        continue;
                    }

                    // Link starting with ? but missing mod or id
                    if (str_starts_with($h, '?') && !str_contains($h, 'mod=') && !str_contains($h, 'id=') && !str_contains($h, 'p=')) {
                        $this->error("Link keluar halaman pada [{$rel}]: Tag '<a href=\"{$h}\">' menggunakan query string statis tanpa 'mod' & 'id'. Ini akan menyebabkan halaman kembali ke menu awal/error. Gunakan \$_SERVER['PHP_SELF'] . '?' . http_build_query(array_merge(\$_GET, [...])).");
                    }

                    // Link to index.php without mod in admin
                    if ((str_starts_with($h, 'index.php?') || $h === 'index.php') && !str_contains($h, 'mod=') && !str_contains($h, 'p=')) {
                        $this->warn("Link pada [{$rel}]: '<a href=\"{$h}\">' mengarah langsung ke 'index.php' tanpa menyertakan modul plugin.");
                    }
                }
            }

            // 3. Breaking out of iframe using target="_top" or target="_parent"
            if (preg_match('/<a[^>]+target=[\'"](?:_top|_parent)[\'"][^>]*>/i', $content, $targetMatches)) {
                if (!str_contains($targetMatches[0], 'http') && !str_contains($targetMatches[0], 'logout')) {
                    $this->warn("Link iframe breakout pada [{$rel}]: Penggunaan 'target=\"_top\"' atau 'target=\"_parent\"' pada '{$targetMatches[0]}' dapat memaksa halaman plugin keluar dari frame navigasi SLiMS.");
                }
            }

            // 4. JavaScript navigation breaking out of container
            if (preg_match('/(?:location\.href|window\.location)\s*=\s*[\'"]([^\'"]+)[\'"]/i', $content, $jsMatches)) {
                $jsTarget = trim($jsMatches[1]);
                if (str_starts_with($jsTarget, '?') && !str_contains($jsTarget, 'mod=')) {
                    $this->error("JavaScript redirect pada [{$rel}]: 'location.href = \"{$jsTarget}\"' akan menghilangkan parameter modul plugin. Gunakan URL lengkap dengan query parameter yang di-preserve.");
                }
            }
        }
        $this->pass("Navigasi & Redirect: Pemeriksaan integritas tautan halaman dan redirect selesai.");
    }

    private function pass(string $msg): void
    {
        $this->passes[] = $msg;
        echo COLOR_GREEN . "  ✔ [PASS] " . COLOR_RESET . $msg . "\n";
    }

    private function warn(string $msg): void
    {
        $this->warnings[] = $msg;
        echo COLOR_YELLOW . "  ⚠ [WARN] " . COLOR_RESET . $msg . "\n";
    }

    private function error(string $msg): void
    {
        $this->errors[] = $msg;
        echo COLOR_RED . "  ✖ [FAIL] " . COLOR_RESET . $msg . "\n";
    }

    private function relative(string $absolute): string
    {
        if (is_file($this->pluginPath) && $absolute === $this->pluginPath) {
            return basename($absolute);
        }
        $rel = ltrim(str_replace($this->pluginPath, '', $absolute), DIRECTORY_SEPARATOR);
        return $rel !== '' ? $rel : basename($absolute);
    }

    private function printSummary(): void
    {
        echo "\n" . COLOR_BOLD . "----------------- HASIL AUDIT PLUGIN -----------------" . COLOR_RESET . "\n";
        echo COLOR_GREEN  . "  ✔ Lolos (Pass)     : " . count($this->passes) . "\n" . COLOR_RESET;
        echo COLOR_YELLOW . "  ⚠ Peringatan (Warn): " . count($this->warnings) . "\n" . COLOR_RESET;
        echo COLOR_RED    . "  ✖ Gagal (Error)    : " . count($this->errors) . "\n" . COLOR_RESET;
        echo "------------------------------------------------------\n";

        if (count($this->errors) === 0) {
            echo COLOR_BOLD . COLOR_GREEN . " 🎉 SELAMAT! Plugin lolos seluruh validasi standar SLiMS 9 Bulian.\n\n" . COLOR_RESET;
        } else {
            echo COLOR_BOLD . COLOR_RED . " ❌ GAGAL: Harap perbaiki seluruh error di atas sebelum mendistribusikan plugin.\n\n" . COLOR_RESET;
        }
    }
}

// CLI Entrypoint
if (php_sapi_name() !== 'cli') {
    die("Script ini hanya dapat dijalankan melalui CLI.\n");
}

$target = $argv[1] ?? '.';
$validator = new PluginValidator($target);
exit($validator->run());
