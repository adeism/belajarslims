Berikut adalah kumpulan data yang dapat digunakan untuk fine-tuning atau RAG chatbot:

1. Preambul | A page for SLiMS User, and Developer

Pertanyaan: Apa saja yang dibutuhkan untuk memulai pengembangan di SLiMS?

Jawaban: Untuk memulai, Anda memerlukan: XAMPP/LAMPP/MAMPP, PHP 8.1+, MySQL 5.7+ atau MariaDB 10.3+, Code Editor (VSCode, VSCodium, Sublime, dll), dan SLiMS terbaru atau SLiMS Develop.

Pertanyaan: Bagaimana cara mengubah environment SLiMS dari production ke development?

Jawaban: Buka modul system, pilih System Environment Setting, ubah nilai production ke development, lalu klik Save Settings.

Pertanyaan: Apa kegunaan mengubah environment ke development?

Jawaban: Untuk menampilkan pesan error yang terjadi saat development, karena secara default SLiMS menyembunyikan error pada mode production.

Pertanyaan: Apa itu SLiMS?

Jawaban: SLiMS (Senayan Library Management System) adalah sistem manajemen perpustakaan sumber terbuka berbasis web.

2. Struktur Direktori | A page for SLiMS User, and Developer

Pertanyaan: Di mana lokasi file konfigurasi SLiMS?

Jawaban: File konfigurasi SLiMS berada di dalam folder config.

Pertanyaan: Di mana lokasi modul admin SLiMS?

Jawaban: Modul admin SLiMS berada di dalam folder admin/modules.

Pertanyaan: Di mana lokasi template admin SLiMS?

Jawaban: Template admin SLiMS berada di dalam folder admin/admin_template.

Pertanyaan: Di mana saya dapat menemukan file-file yang berhubungan dengan automation di SLiMS?

Jawaban: File-file automation dapat ditemukan di dalam folder admin/modules.

3. OPAC | A page for SLiMS User, and Developer

Pertanyaan: Apa itu OPAC?

Jawaban: OPAC (Online Public Access Catalog) adalah katalog perpustakaan yang dapat diakses secara online oleh publik.

Pertanyaan: Bagaimana alur kerja OPAC SLiMS?

Jawaban: Alur kerjanya dimulai dari akses OPAC, cek request, jika ke halaman utama tampilkan halaman utama, jika request plugin tampilkan halaman plugin, jika file statis tampilkan file dari /lib/contents/, jika konten berita tampilkan dari database, jika tidak ditemukan tampilkan "Content Not Found".

Pertanyaan: Bagaimana alur kerja plugin di SLiMS?

Jawaban: Alur kerjanya dimulai dari SLiMS diakses, sistem plugin aktif, cek apakah ada plugin aktif, jika ya load plugin, jika dipanggil di OPAC/Admin tampilkan hasil, jika tidak proses selesai.

Pertanyaan: Apa yang terjadi jika konten yang diminta di OPAC tidak ditemukan?

Jawaban: Jika konten tidak ditemukan, maka akan ditampilkan pesan "Content Not Found".

4. Pengantar | A page for SLiMS User, and Developer

Pertanyaan: Apa tujuan dari sistem plugin di SLiMS?

Jawaban: Mempermudah pengembang dalam melakukan upgrade SLiMS dan memodifikasi SLiMS tanpa mengubah core system.

Pertanyaan: Apa saja yang dibutuhkan untuk membuat plugin di SLiMS?

Jawaban: Anda membutuhkan pengetahuan tentang HTML, CSS, PHP, JS, SQL, dan composer (opsional).

Pertanyaan: Di mana lokasi plugin di SLiMS?

Jawaban: Plugin SLiMS secara default diletakkan di folder plugins/.

Pertanyaan: Apa ekstensi file plugin SLiMS?

Jawaban: Ekstensi file plugin SLiMS adalah .plugin.php.

5. Membuat Plugin Sederhana | A page for SLiMS User, and Developer

Pertanyaan: Bagaimana cara membuat plugin "Hello World" di SLiMS?

Jawaban: Buat file hello_world.plugin.php di folder plugins/, isi dengan informasi plugin dan kode $plugins->register(Plugins::CONTENT_BEFORE_LOAD, function(){ exit('<h1>Hello World</h1>'); });, aktifkan plugin di modul System > Plugin, lalu buka halaman OPAC.

Pertanyaan: Apa yang dilakukan oleh kode $plugins->register(Plugins::CONTENT_BEFORE_LOAD, function(){ exit('<h1>Hello World</h1>'); });?

Jawaban: Kode tersebut akan menampilkan teks "Hello World" di halaman OPAC sebelum konten lain dimuat.

Pertanyaan: Bagaimana cara mengaktifkan plugin di SLiMS?

Jawaban: Anda dapat mengaktifkan plugin di modul System > Plugin.

6. Membuat plugin modifikasi halaman tertentu di OPAC | A page for SLiMS User, and Developer

Pertanyaan: Bagaimana cara memodifikasi halaman member di OPAC menggunakan plugin?

Jawaban: Buat folder kustom_member di plugins/, buat file kustom_member.plugin.inc.php, isi dengan kode untuk meregistrasi menu OPAC, salin lib/contents/member.inc.php ke plugins/kustom_member/, modifikasi member.inc.php, aktifkan plugin.

Pertanyaan: Bagaimana cara membuat halaman baru di OPAC menggunakan plugin?

Jawaban: Buat folder bebas_pustaka di plugins/, buat file bebas_pustaka.plugin.php, isi dengan kode untuk meregistrasi menu OPAC, buat file bebas_pustaka.php, isi dengan konten, aktifkan plugin.

Pertanyaan: Apa fungsi dari $plugins->registerMenu('opac', 'member', __DIR__ . '/member.inc.php');?

Jawaban: Mendaftarkan halaman member di OPAC untuk dimodifikasi oleh file member.inc.php yang berada di dalam folder plugin.

7. Cara membuat plugin diarea admin | A page for SLiMS User, and Developer

Pertanyaan: Bagaimana cara menginterupsi proses penyimpanan data bibliografi sebelum data disimpan menggunakan hook?

Jawaban: Buat folder hooking di plugins/, buat file hooking.plugin.php, isi dengan kode $plugins->register('bibliography_before_save', function($data){ toastr('Test hooking')->native(); });, aktifkan plugin, buat data bibliografi baru.

Pertanyaan: Bagaimana cara menambahkan menu kustom di modul bibliography menggunakan plugin?

Jawaban: Buat folder custom_menu di plugins/, buat file custom_menu.plugin.php, isi dengan kode $plugins->registerMenu('bibliography', 'Kustom Menu', __DIR__ . '/custom_menu.php');, buat file custom_menu.php, isi dengan konten, aktifkan plugin.

Pertanyaan: Bagaimana cara memodifikasi halaman index modul bibliography menggunakan plugin?

Jawaban: Pada folder plugins/custom_menu buat file custom_index.php, pada custom_menu.plugin.php tambahkan $plugins->registerMenu('bibliography', __('Bibliographic List'), __DIR__ . '/custom_index.php');, isi custom_index.php dengan konten, refresh halaman modul bibliography.

8. Migration | A page for SLiMS User, and Developer

Pertanyaan: Apa itu Database Migration di SLiMS?

Jawaban: Fitur untuk memindahkan struktur tabel plugin dengan mudah tanpa perlu export file .sql.

Pertanyaan: Bagaimana cara membuat file migration di SLiMS?

Jawaban: Buat folder migration di dalam folder plugin, buat file dengan nama diawali angka (urutan eksekusi) dan dipisahkan _, contoh: 1_CreateBase.php.

Pertanyaan: Apa fungsi method up() pada file migration?

Jawaban: Untuk menjalankan proses pembuatan tabel, kolom, dll saat plugin diaktifkan.

Pertanyaan: Apa fungsi method down() pada file migration?

Jawaban: Untuk menjalankan proses penghapusan tabel, kolom, dll saat plugin dinonaktifkan.

9. Pengantar | A page for SLiMS User, and Developer

Pertanyaan: Apa RDBMS default yang digunakan SLiMS?

Jawaban: MySQL atau MariaDB.

Pertanyaan: Di mana lokasi file konfigurasi database SLiMS?

Jawaban: config/database.php.

Pertanyaan: Apa yang dimaksud dengan opsi proxy pada konfigurasi database?

Jawaban: Opsi untuk mengaktifkan switching database berdasarkan rule tertentu.

Pertanyaan: Apa fungsi dari storage_engine pada konfigurasi database?

Jawaban: Untuk menentukan storage engine yang digunakan oleh tabel di MySQL/MariaDB (opsional).

10. Penggunaan Database | A page for SLiMS User, and Developer

Pertanyaan: Apa perbedaan antara MySQLi dan PDO di SLiMS?

Jawaban: MySQLi menggunakan $dbs dan membutuhkan global scope di dalam function, PDO menggunakan DB::class dan tidak membutuhkan global scope.

Pertanyaan: Bagaimana cara menggunakan MySQLi tanpa variabel $dbs?

Jawaban: $query = \SLiMS\DB::getInstance('mysqli')->query('select * from biblio');

Pertanyaan: Bagaimana cara mengganti koneksi database di SLiMS?

Jawaban: $db2 = DB::connection('non-default');

Pertanyaan: Bagaimana cara menghindari SQL Injection saat menggunakan MySQLi?

Jawaban: Selalu gunakan escape_string saat memasukkan input user ke dalam query.

11. Ekstensi Database | A page for SLiMS User, and Developer

Pertanyaan: Apa fungsi dari ekstensi SLiMS\Query?

Jawaban: Membantu mengelola data dari database menggunakan PDO.

Pertanyaan: Bagaimana cara menggunakan ekstensi SLiMS\Query?

Jawaban: $biblio = DB::query('select * from biblio');

Pertanyaan: Apa fungsi dari setConnection pada SLiMS\Query?

Jawaban: Mengatur profil koneksi yang akan digunakan.

Pertanyaan: Apa fungsi dari setDefaultOutput pada SLiMS\Query?

Jawaban: Mengatur tipe data yang diolah, seperti PDO::FETCH_OBJ.

12. Membuat Ekstensi Database Anda Sendiri | A page for SLiMS User, and Developer

Pertanyaan: Bagaimana cara membuat ekstensi database sendiri di SLiMS?

Jawaban: Buat plugin, install illuminate/database via composer, buat file Builder.php, isi dengan kode extends Manager, daftarkan ekstensi di .plugin.php, aktifkan plugin.

Pertanyaan: Bagaimana cara menggunakan ekstensi illuminate/database di SLiMS?

Jawaban: $builder = DB::builder(); $biblio = $builder->table('biblio')->limit(10)->get();

13. Schema | A page for SLiMS User, and Developer

Pertanyaan: Apa fungsi dari Schema di SLiMS?

Jawaban: Untuk mengelola struktur database seperti menambah, menghapus, dan mengubah tabel/kolom.

Pertanyaan: Bagaimana cara membuat tabel menggunakan Schema?

Jawaban: Schema::create('nama_tabel', function(Blueprint $table){ /* definisi kolom */ });

Pertanyaan: Bagaimana cara menghapus tabel menggunakan Schema?

Jawaban: Schema::drop('nama_tabel');

Pertanyaan: Bagaimana cara mengubah kolom menggunakan Schema?

Jawaban: Schema::table('nama_tabel', function(Blueprint $table){ /* modifikasi kolom */ });

14. Penjelasan | A page for SLiMS User, and Developer

Pertanyaan: Apa itu Console di SLiMS?

Jawaban: Fitur untuk kebutuhan belakang layar seperti backup terjadwal, pengiriman email, dll.

Pertanyaan: Sejak versi berapa Console tersedia di SLiMS?

Jawaban: Sejak versi 9.6.0.

15. Cara penggunaan | A page for SLiMS User, and Developer

Pertanyaan: Apa yang dibutuhkan untuk menggunakan fitur Console di SLiMS?

Jawaban: Antarmuka berbasis perintah (CLI) seperti Command Prompt, Windows Terminal, PowerShell, atau Terminal di Linux.

Pertanyaan: Bagaimana cara menampilkan daftar plugin yang aktif menggunakan Console?

Jawaban: php index.php plugin:list

Pertanyaan: Bagaimana format penulisan perintah pada SLiMS Console?

Jawaban: php index.php [perintah] [opsi]

16. Tarsius | A page for SLiMS User, and Developer

Pertanyaan: Apa itu Tarsius di SLiMS?

Jawaban: Nama hewan primata maskot SLiMS dan nama program baris perintah (CLI) untuk proses di balik layar.

Pertanyaan: Bagaimana cara menjalankan Tarsius?

Jawaban: php index.php tarsius lalu php tarsius

17. Membuat Command Anda Sendiri | A page for SLiMS User, and Developer

Pertanyaan: Bagaimana cara membuat Command sendiri di SLiMS?

Jawaban: php tarsius make:command NamaCommand, lalu edit file Command di plugins/Commands/.

Pertanyaan: Apa fungsi dari signature pada Command?

Jawaban: Ciri khas dari command, contoh: mail:overdue.

Pertanyaan: Apa fungsi dari handle pada Command?

Jawaban: Tempat menuliskan proses yang akan dilakukan oleh Command.

18. Permulaan | A page for SLiMS User, and Developer

Pertanyaan: Apa itu HttpClient di SLiMS?

Jawaban: Pustaka untuk berkomunikasi dengan layanan lain melalui protokol HTTP.

Pertanyaan: HttpClient di SLiMS dibangun di atas pustaka apa?

Jawaban: guzzlehttp/guzzle.

19. Cara Penggunaan | A page for SLiMS User, and Developer

Pertanyaan: Bagaimana cara melakukan GET request menggunakan HttpClient di SLiMS?

Jawaban: $request = Client::get('https://slims.web.id/kutipan/');

Pertanyaan: Bagaimana cara menambahkan headers menggunakan HttpClient di SLiMS?

Jawaban: $request = Client::withHeaders(['Content-Type' => 'application/json'])->get('https://slims.web.id/kutipan/');

Pertanyaan: Bagaimana cara mengirim body menggunakan HttpClient di SLiMS?

Jawaban: $request = Client::withBody(json_encode(['age' => 20]))->post('https://slims.web.id/kutipan/');

20. Download File | A page for SLiMS User, and Developer

Pertanyaan: Bagaimana cara mendownload file menggunakan HttpClient di SLiMS?

Jawaban: Client::download($url)->to(SB . 'images/dummy.png');

21. Stream File | A page for SLiMS User, and Developer

Pertanyaan: Bagaimana cara melakukan stream file menggunakan HttpClient di SLiMS?

Jawaban: Client::stream($url);

22. Penggunaan | A page for SLiMS User, and Developer

Pertanyaan: Apa fungsi dari pustaka Cache di SLiMS?

Jawaban: Untuk menyimpan data sementara (cache) agar loading lebih cepat.

Pertanyaan: Apa saja provider yang tersedia di pustaka Cache SLiMS?

Jawaban: Files dan Database.

Pertanyaan: Bagaimana cara membuat cache baru di SLiMS?

Jawaban: Cache::set(cacheName: 'namacache', 'content');

Pertanyaan: Bagaimana cara menghapus semua cache di SLiMS?

Jawaban: Cache::purge();

23. Penggunaan | A page for SLiMS User, and Developer

Pertanyaan: Bagaimana cara membuat provider Cache sendiri di SLiMS?

Jawaban: Buat class yang extends \SLiMS\Cache\Contract, implementasikan method yang dibutuhkan, dan daftarkan di config/cache.php.

24. Helpers | A page for SLiMS User, and Developer

Pertanyaan: Apa itu Helpers di SLiMS?

Jawaban: Kumpulan fungsi PHP untuk mempermudah pengembangan di SLiMS.

Pertanyaan: Apa fungsi dari __() di SLiMS?

Jawaban: Untuk menerjemahkan teks.

Pertanyaan: Apa fungsi dari config() di SLiMS?

Jawaban: Untuk mengambil nilai dari file konfigurasi atau tabel setting.

Pertanyaan: Apa fungsi dari debug() di SLiMS?

Jawaban: Untuk menampilkan debug information.

Pertanyaan: Apa fungsi dari currency() di SLiMS?

Jawaban: Untuk memformat angka menjadi format mata uang.

Pertanyaan: Apa fungsi dari toastr() di SLiMS?

Jawaban: Untuk menampilkan notifikasi.

Pertanyaan: Apa fungsi dari redirect() di SLiMS?

Jawaban: Untuk mengalihkan halaman.

Pertanyaan: Apa fungsi dari flash() di SLiMS?

Jawaban: Untuk menampilkan pesan sementara.
