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
        if (!$realPath || !is_dir($realPath)) {
            echo COLOR_RED . "✖ Error: Direktori plugin tidak ditemukan: {$pluginPath}\n" . COLOR_RESET;
            exit(1);
        }
        $this->pluginPath = rtrim($realPath, DIRECTORY_SEPARATOR);
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

        $this->printSummary();

        return count($this->errors) > 0 ? 1 : 0;
    }

    private function scanFiles(): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->pluginPath, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $filePath = $file->getPathname();
                $this->phpFiles[] = $filePath;

                if (str_contains($file->getFilename(), '.plugin.php')) {
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
            $output = [];
            $code = 0;
            exec("php -l " . escapeshellarg($file) . " 2>&1", $output, $code);
            if ($code !== 0) {
                $this->error("Sintaks PHP error pada [{$rel}]: " . implode(' ', $output));
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
            if (!preg_match('/public\s+function\s+up\s*\(/i', $content)) {
                $this->error("Metode 'public function up()' tidak ditemukan pada migrasi [{$rel}].");
            }
            if (!preg_match('/public\s+function\s+down\s*\(/i', $content)) {
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
        return ltrim(str_replace($this->pluginPath, '', $absolute), DIRECTORY_SEPARATOR);
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
