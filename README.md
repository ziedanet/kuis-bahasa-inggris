# Find the Word — Platform Kuis Multi-Kuis

Update besar: sekarang bukan cuma 1 kuis "Find The Right Word", tapi platform yang bisa punya banyak kuis. Admin bisa bikin kuis baru, isi soal per kuis, lihat hasil per kelompok — semua dari panel admin baru (login username/password).

## 1. Timpa file ke project Laravel kamu

Timpa/gabungkan semua folder di paket ini ke project Laravel kamu (struktur sudah sama persis):

- `app/Models/*`
- `app/Http/Controllers/*`
- `database/migrations/*`
- `database/seeders/*`
- `resources/views/*`
- `routes/web.php` (timpa file bawaan)
- `config/auth.php` (timpa file bawaan — **lihat catatan penting di poin 3**)

**Hapus file lama yang sudah tidak dipakai** (kalau masih ada di project kamu):
- `app/Http/Middleware/AdminAuth.php` — sudah diganti login asli Laravel (`Auth::attempt`), file ini jadi dead code.
- Kalau di `bootstrap/app.php` atau `app/Http/Kernel.php` ada alias `'admin.auth' => ...AdminAuth::class`, hapus baris itu juga.
- `resources/views/admin/quizzes/create.blade.php` — halaman "Input Kuis Baru" sudah diganti modal langsung di halaman Daftar Kuis, file ini jadi tidak terpakai.
- `app/Http/Controllers/Admin/ResultController.php` dan `resources/views/admin/results.blade.php` — menu **Hasil Kelompok** sudah dihapus (digantikan menu **Leaderboard** yang lebih lengkap), jadi 2 file ini jadi tidak terpakai.

## 2. Migrasi database

Mulai update ini, cukup jalankan migrasi biasa (bukan `fresh`) — **data yang sudah kamu input aman, tidak akan hilang**:

```bash
php artisan migrate
```

Perintah ini cuma menjalankan file migrasi yang belum pernah dijalankan sebelumnya. Di update kali ini ada beberapa migrasi baru: kolom `kelompok_id` di `zd_quiz_groups` (buat menghubungkan sesi main kelompok ke akun login kelompok), dan 2 tabel baru `zd_individu_attempts` & `zd_individu_attempt_answers` (buat menyimpan progres & jawaban kuis individu per siswa). Tabel & data yang sudah ada tidak disentuh sama sekali.

Catatan soal `migrate:fresh --seed` yang dulu saya suruh jalankan tiap update: itu cuma perlu di awal (waktu tabel-tabel baru macam `zd_siswa`/`zd_kelompok`/dst pertama kali dibuat). Sekarang strukturnya sudah stabil, jadi ke depannya perubahan kecil seperti ini cukup `php artisan migrate` saja. Saya cuma akan minta `migrate:fresh --seed` lagi kalau memang ada perubahan besar di struktur tabel yang datanya perlu direset total — dan saya akan bilang eksplisit kalau itu terjadi.

## 3. WAJIB — ganti `config/auth.php`

Kalau kemarin sempat muncul error **"Auth guard [siswa] is not defined"** pas buka halaman utama, itu tandanya langkah ini belum jalan — bukan bug kode, cuma file config yang belum diganti.

Sebelumnya saya cuma kasih instruksi "tambah baris ini manual", ternyata bikin ribet. Sekarang paket ini sudah menyertakan file lengkap `config/auth.php` siap pakai (sudah termasuk guard `siswa` & `kelompok`) — kamu tinggal **timpa langsung** file `config/auth.php` di project kamu dengan yang ada di paket ini, tidak perlu edit manual lagi.

Catatan: file `config/auth.php` bawaan Laravel hampir selalu masih default (jarang ada yang mengubahnya di awal project), jadi aman ditimpa. Tapi kalau kamu pernah mengubah isi file itu sebelumnya (misalnya nambah guard lain di luar yang saya buatkan), kasih tahu saya dulu sebelum menimpa supaya perubahan kamu tidak hilang.

Setelah ditimpa, restart `php artisan serve` (Ctrl+C lalu jalankan lagi) supaya config-nya kebaca ulang, baru refresh halaman utama.

## 4. Login admin

```
http://127.0.0.1:8000/admin/login
```
atau langsung dari halaman utama, klik menu **Login Admin** di navbar (sekarang link langsung ke halaman ini, bukan scroll lagi — form login yang di homepage sekarang khusus buat siswa/kelompok, lihat bagian di bawah).

```
username: admin
password: admin
```

Ganti nama/username/email/password admin lewat menu **Profil Admin** — sekarang aksesnya klik ikon avatar di **pojok kanan atas**, muncul dropdown **Profil** & **Keluar** (sudah dipindah dari sidebar bawah).

## 5. Alur admin sekarang

Sidebar admin (dirapikan lagi):
- **Dashboard** — halaman awal setelah login, isinya cuma statistik (Total Kuis/Soal/Peserta Selesai) + 2 grafik: jumlah kelompok per kuis, dan sebaran predikat kelompok yang sudah selesai.
- **Daftar Kuis Kelompok** — tabel semua kuis kelompok (jumlah soal, status aktif/draft, tombol edit/hapus). Klik **"Buat Kuis Baru"** buka modal langsung di halaman ini. Tombol **Soal** di tiap baris buka Kelola Soal khusus kuis itu — sekarang juga menampilkan thumbnail gambar tiap soal, dengan placeholder abu-abu kalau belum ada gambar.
- **Daftar Kuis Individu** — menu baru, sama polanya kayak Daftar Kuis Kelompok tapi buat kuis mode individu (per siswa, bukan per kelompok). Soalnya beda struktur: narasi + gambar opsional + 5 opsi jawaban (1 benar), disimpan di tabel terpisah (`zd_soal_individu` & `zd_opsi_individu`). **Alur main publiknya sekarang sudah ada** (lihat poin 6) — begitu status kuis aktif dan sudah ada minimal 1 soal, tombol "Mulai Kuis" langsung muncul di homepage.
- **Leaderboard** — menu ini sekarang gantiin **Hasil Kelompok** yang sudah dihapus. Papan peringkat berdasarkan skor tertinggi (skala 0-100, kalau seri yang waktu pengerjaannya paling singkat menang). Ada 2 tab: Kuis Kelompok & Kuis Individu, keduanya sudah terisi data asli.
- **Master Kelas** — kelola daftar kelas, dipakai sebagai referensi kelas di data Daftar User & Daftar Kelompok.
- **Daftar User** — kelola data siswa (nama, NISN, jenis kelamin, kelas, foto), bisa difilter per kelas & jenis kelamin. Password login **otomatis dibuat sama dengan NISN** (tidak ada input password manual di form). Kalau siswa lupa password, klik tombol kunci di kolom Aksi buat reset balik ke NISN.
- **Daftar Kelompok** — kelola akun kelompok (nama kelompok, kelas, username, password). Username + password di sini yang dipakai kelompok buat login & main kuis kelompok di homepage.
- **Profil Admin** — klik avatar di pojok kanan atas → dropdown **Profil** (update nama/username/email/password akun admin sendiri) & **Keluar**.

Grafik di Dashboard pakai Chart.js dari CDN (`cdnjs.cloudflare.com`) — sama seperti FontAwesome/Google Fonts, ini masih dependency online, belum di-self-host.

## 6. Alur kuis (peserta) — sekarang wajib login

Login siswa/kelompok sekarang **benar-benar terhubung** ke alur main kuis (sebelumnya cuma mekanisme akun terpisah, sekarang sudah nyambung):

- **Kuis kelompok**: peserta harus login sebagai **kelompok** dulu (menu Masuk di navbar → tab Login Kelompok). Setelah login, klik "Mulai Kuis" pada kuis kelompok → halaman konfirmasi (nama & kelas otomatis dari akun kelompok, tidak input manual lagi) → jawab tiap gambar, pilih tepat 5 dari 10 kata → hasil: skor **0-100** + predikat otomatis. Progres tersimpan di akun kelompok itu (bukan session browser lagi), jadi tetap nyambung walau pindah perangkat.
- **Kuis individu**: peserta harus login sebagai **siswa** dulu (tab Login Siswa, pakai NISN + password). Setelah login, klik "Mulai Kuis" pada kuis individu → langsung masuk ke soal (narasi + gambar opsional + 5 opsi jawaban, pilih 1) → soal berikutnya otomatis, sampai semua terjawab → hasil: skor **0-100** + predikat otomatis.
- Siswa **tidak bisa** ikut kuis kelompok, dan akun kelompok **tidak bisa** ikut kuis individu — kalau salah, sistem tolak & arahkan balik ke bagian login dengan pesan.
- Klik "Mulai Kuis" tanpa login akan diarahkan ke homepage bagian login dengan pesan "Login dulu".

**Semua kuis sekarang pakai skala skor 0-100**, berapa pun jumlah soalnya — dihitung dari persentase jawaban benar (skor mentah dibagi skor maksimum kuis itu, dikali 100), jadi kuis 4 soal dan kuis 15 soal tetap setara & bisa dibandingkan langsung di leaderboard. Predikat juga dari persentase yang sama:
- ≥ 90 → Sangat Baik
- ≥ 60 → Baik
- ≥ 30 → Cukup
- < 30 → Perlu Bimbingan

## 7. Isi/ganti gambar

Ada 2 cara pasang gambar soal:

**A. Gambar bawaan (4 soal "Find The Right Word") — taruh file langsung, tidak perlu upload:**

Taruh 4 file ini di `public/images/soal/` (bikin folder kalau belum ada):
- `taman-tengku-mahratu.jpg`
- `pantai-rupat.jpg`
- `asia-heritage.jpg`
- `mall-ska-pekanbaru.jpg`

Nama file harus persis sama (huruf kecil semua, pakai tanda hubung). Setelah file ditaruh, langsung muncul otomatis di kuis — tidak perlu proses apa pun lagi.

**B. Soal baru yang dibuat lewat admin panel:** upload seperti biasa lewat **Kelola Soal → Tambah/Edit Soal** (form upload). Ini butuh `php artisan storage:link` sudah dijalankan (lihat catatan di bawah).

## 8. Logo

Taruh file `logo-kuis.png` kamu di `public/images/logo-kuis.png`. Otomatis dipakai di navbar, footer, sidebar admin, dan halaman login admin.

## 9. Master Kelas

Menu sidebar admin **Master Kelas** dipakai buat kelola daftar kelas (misal: X-1, XI IPA 2, dst). Kelas yang didaftarkan di sini otomatis muncul sebagai pilihan dropdown di data Daftar User & Daftar Kelompok. Tambahkan kelas-kelas kamu dulu di sini sebelum data siswa/kelompok didaftarkan.

## 10. Login Siswa & Kelompok di homepage

Section login di homepage (yang dulu isinya login admin) sekarang jadi login untuk siswa/kelompok, ada 2 tab:
- **Login Siswa** — pakai NISN + password (didaftarkan admin di menu Daftar User). Setelah login, cuma bisa ikut **kuis individu**.
- **Login Kelompok** — pakai username + password (didaftarkan admin di menu Daftar Kelompok). Setelah login, cuma bisa ikut **kuis kelompok**.

Kalau berhasil login, navbar & section itu berubah tampilkan nama yang login. Sekarang login ini **benar-benar terhubung** ke alur main kuis (lihat poin 6) — bukan cuma mekanisme akun terpisah lagi.

## 11. Profil siswa/kelompok (ganti password)

Klik nama yang login (di navbar kanan atas, atau di section login homepage) untuk buka halaman **Profil Saya** — di situ siswa/kelompok bisa ganti password sendiri (perlu masukkan password lama dulu). Beda dari **Profil Admin** yang khusus buat akun admin.

## 12. Filter katalog kuis di homepage

Section "Kuis Yang Tersedia" nampilin **semua kuis** (kelompok + individu), dengan tombol filter Semua/Kelompok/Individu di atasnya. Kartu kuis tampil **"Mulai Kuis"** kalau statusnya aktif dan sudah ada minimal 1 soal (berlaku sama buat kelompok maupun individu) — kalau nonaktif atau soalnya masih kosong, otomatis tampil **"Segera Hadir"**.

## 13. ⚠️ Kalau repo Git kamu Public — soal seeder & kredensial default

Karena `database/seeders/*` ikut ke-commit ke Git, dua hal ini perlu diperhatikan kalau repo kamu **Public**:

- **`FindTheWordSeeder.php`** sekarang isinya **data contoh/demo** (kata & kunci jawaban bukan yang asli lagi), supaya kunci jawaban asesmen asli tidak kelihatan di GitHub. Kuis "Find The Right Word" bawaan ini jadi murni buat demo/portofolio. **Untuk kuis yang beneran dipakai asesmen ke siswa**, buat & isi soalnya lewat **Admin Panel** (menu Daftar Kuis Kelompok/Individu → Soal) — data yang diinput lewat situ tersimpan di database server, bukan di kode, jadi aman tidak ikut ke GitHub sama sekali biarpun repo Public.
- **Login admin default** (`admin`/`admin`) sekarang bisa diatur lewat `.env`, supaya tidak hardcoded kelihatan di kode. Sebelum `php artisan migrate --seed` di server, tambahkan baris ini ke `.env` (opsional tapi disarankan kalau repo Public):
  ```
  ADMIN_DEFAULT_USERNAME=admin
  ADMIN_DEFAULT_EMAIL=admin@sekolah.id
  ADMIN_DEFAULT_PASSWORD=ganti-dengan-password-kamu-sendiri
  ```
  Kalau tidak diisi, tetap jatuh ke default lama (`admin`/`admin`) — jadi **wajib** login & ganti password lewat menu Profil Admin begitu server sudah jalan, apalagi kalau repo-nya Public.

## 14. Halaman publik sekarang berbahasa Inggris

Semua yang tampil ke pengunjung/siswa/kelompok (navbar, footer, homepage, form login, halaman mulai kuis, halaman main kuis, halaman hasil, halaman Profil Saya, pesan error login) sudah diterjemahkan ke **Bahasa Inggris**. **Panel admin tetap Bahasa Indonesia** — tidak disentuh sama sekali, jadi kamu sebagai admin/guru tetap pakai antarmuka Indonesia seperti biasa.

Catatan teknis: nilai string `predikat` di dalam kode (`Sangat Baik`/`Baik`/`Cukup`/`Perlu Bimbingan`) **sengaja tidak diubah** karena dipakai bareng sama Leaderboard & Dashboard admin. Yang diterjemahkan cuma tampilannya di halaman hasil publik (lewat pemetaan `Excellent`/`Good`/`Fair`/`Needs Improvement` di file blade-nya) — jadi Leaderboard admin kamu tetap normal.

**Kalau kamu re-seed data katalog/demo** (`QuizCatalogSeeder`, `FindTheWordSeeder`): judul-judul kuis & soal contoh di file ini sudah ikut diterjemahkan (misal "Tebak Aktivitas Wisata" → "Guess the Tourism Activity"). Karena judul dipakai sebagai kunci pencocokan (`updateOrCreate` berdasarkan `title`), kalau kamu jalankan ulang seeder ini di server yang **sudah pernah** di-seed dengan judul Indonesia sebelumnya, hasilnya akan jadi **baris baru** (bukan update baris lama) — baris lama yang judulnya masih Bahasa Indonesia akan tetap ada di database sebagai duplikat. Ini aman (tidak merusak data), tapi kalau mau rapi, hapus manual baris lama itu lewat Admin Panel (menu Daftar Kuis Kelompok/Individu → Hapus) setelah re-seed.

## Catatan

- Kalau menu admin **Kelola Soal → Tambah/Edit Soal** dipakai untuk upload gambar dan setelah simpan gambarnya kosong/rusak, jalankan `php artisan storage:link` lalu refresh (panel admin sekarang otomatis kasih peringatan kuning kalau ini belum dijalankan).
- Skor bersifat pemetaan awal, **tidak** masuk nilai rapor.
- Kuis baru yang dibuat admin otomatis muncul di homepage; kalau belum ada soalnya atau statusnya draft, tombolnya otomatis "Segera Hadir" (tidak perlu diatur manual).
- Data lama di `zd_quiz_groups` (dari sebelum login kelompok wajib) tetap ada di database tapi tidak lagi punya `kelompok_id` (kolom baru) — data itu jadi "yatim", tidak muncul di Leaderboard karena Leaderboard sekarang hanya ambil dari kelompok yang login resmi. Tidak masalah dibiarkan; kalau mau beres-beres, hapus langsung lewat database (menu Hasil Kelompok yang dulu punya tombol hapus sudah tidak ada, digantikan Leaderboard yang read-only).
- Aset CSS/JS eksternal (Google Fonts, FontAwesome) saat ini masih dimuat dari CDN — belum sempat di-self-host lokal karena sandbox tempat saya kerja diblokir akses ke CDN tersebut. Kalau mau, saya bisa siapkan langkah-langkah untuk kamu jalankan sendiri di komputer kamu (download font/ikon, taruh di `public/`, ganti link di layout).
