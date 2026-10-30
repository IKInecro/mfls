# Spec: Pengelolaan Template Email dari Admin (Body + Subject + Preview)

Tanggal: 2026-10-31
Status: DRAFT — menunggu review
Path: architectural (brainstorming → spec → writing-plans → implementasi)
Pendekatan yang disetujui: **A — tabel `email_templates` + override fragment isi, fallback ke Blade**

---

## 1. Tujuan

Admin bisa mengubah **isi body (bagian dalam) dan subject** ketujuh email transaksional aplikasi dari halaman admin, tanpa menyentuh kode, lengkap dengan **preview email utuh** sebelum disimpan. Struktur luar email (logo, CSS inline, border, footer) tetap terkunci di template Blade asli.

## 2. Cakupan

### 2.1 Email yang masuk (7 key)

| key | Label admin | Subject default (di kode) | View body | Variabel tersedia |
|---|---|---|---|---|
| `otp` | Kode OTP Pendaftaran | `Kode OTP Verifikasi Pendaftaran MNCU Future Leader Scholarship` (`app/Mail/OtpMail.php`) | `emails.otp` | `{nama}`, `{otp}` |
| `reset_password` | Reset Password | `Reset Password MNCU Future Leader Scholarship` (`AuthController`) | `emails.reset_password` | `{nama}`, `{linkReset}` |
| `email_change_otp` | OTP Ubah Email | `Kode Verifikasi Ubah Email MNCU Future Leader Scholarship` (`PendaftarController`) | `emails.email_change_otp` | `{nama}`, `{otp}` |
| `admin_changed_email` | Notif Perubahan Email oleh Admin | `Pembaruan Alamat Email Akun - …` dan `Pemberitahuan Perubahan Email Akun - …` (`AdminController`, 2 call site) | `emails.admin_changed_email` | `{nama}`, `{namaBaru}` |
| `lolos_seleksi_ujian` | Pengumuman Lolos Seleksi Ujian | `🎉 Selamat! Anda Lolos Seleksi Ujian MNCU Future Leader Scholarship 2026` (`AdminController` + `SendLolosUjianEmailJob`) | `emails.lolos_seleksi_ujian` | `{nama}`, `{asalSekolah}` |
| `certificate` | Sertifikat Partisipasi | `Sertifikat Partisipasi MNCU Future Leader Scholarship - {nama}` (`app/Mail/CertificateMail.php`) | `emails.certificate` | `{nama}` |
| `ujian_link` | Undangan Ujian Online | `Undangan Ujian Online MNCU Future Leader Scholarship 2026` (`app/Mail/UjianLinkMail.php`) | `emails.ujian_link` | `{nama}`, `{linkUjian}` |

Catatan `admin_changed_email`: satu view, dua call site dengan subject berbeda. Subject hasil edit admin **dipakai di keduanya** bila diset; bila kosong, masing-masing tetap memakai subject default-nya sendiri.

Variabel final per key diverifikasi dari kode saat implementasi; whitelist per key mendefinisikan placeholder yang boleh dipakai.

### 2.2 Di luar cakupan (Non-goal)

- Mengirim email uji/test dari panel (preview cukup).
- Mengedit cangkang email (logo, CSS, footer) — tetap di Blade.
- Template email baru di luar 7 key di atas — dev yang menambah key + label + placeholder.
- Editor visual (WYSIWYG). Textarea HTML + preview.

---

## 3. Model Data

Tabel baru `email_templates`:

| kolom | tipe | ket |
|---|---|---|
| `id` | bigint PK | |
| `key` | string(50), unique | salah satu dari 7 key |
| `subject` | string nullable | `NULL` = pakai subject default di kode |
| `body` | longText nullable | `NULL` = pakai isi Blade asli |
| `created_at` / `updated_at` | timestamp | |

- **Baris tidak ada / kolom `NULL` → fallback penuh ke perilaku lama.** Tabel kosong = aplikasi persis seperti sebelum fitur.
- Satu baris per key (upsert).

Model `EmailTemplate` (`app/Models/EmailTemplate.php`):

```php
public static function get(string $key): ?self;              // null kalau belum diedit
public static function subject(string $key, string $default): string; // override atau default
public static function body(string $key): ?string;           // body ter-override atau null
public static function renderBody(string $key, array $data): ?string;
    // ganti placeholder {nama} → nilai dari $data sesuai whitelist per key
public static function reset(string $key): void;             // hapus baris
```

Aturan placeholder:
- Sintaks `{nama}`, `{otp}`, dst (kurung kurawal tanpa `$`).
- Substitusi via regex `/\{(\w+)\}/` dengan **whitelist per key** — placeholder tak dikenal dibiarkan apa adanya (terlihat di preview, bukan error).
- **DB body TIDAK dikompile jadi Blade** — hanya substitusi string. Alasan: mencegah eksekusi kode dari konten yang ditulis lewat web.

---

## 4. Pipeline Pengiriman (perubahan di 7 jalur kirim)

Pola untuk setiap jalur (`Mail::send(view, data, closure)` maupun Mailable):

1. Ambil `EmailTemplate::get($key)`.
2. Kalau `subject` terisi → pakai; kalau tidak → subject default yang sekarang ada di kode.
3. Kalau `body` terisi → `renderBody($key, $data)` → kirim sebagai data view `$templateBody`; kalau tidak → kirim `null`.
4. View Blade dibuka slot isi:

```blade
{{-- bagian dalam .content, contoh emails/otp.blade.php --}}
@if(!empty($templateBody))
    {!! $templateBody !!}
@else
    {{-- isi asli, tidak diubah --}}
    <h1>Verifikasi Akun ...</h1>
    ...
@endif
```

Struktur luar (DOCTYPE, `<style>`, `.header` logo, `.footer`) **tidak** masuk slot — selalu dari Blade.

Call site yang disentuh (7 email, ±9 lokasi — diverifikasi ulang saat implementasi):
- `app/Mail/OtpMail.php` (Envelope subject), dipakai `AuthController` (2×)
- `AuthController` (reset password inline)
- `PendaftarController` (email change OTP inline)
- `AdminController` (2× admin_changed, 1× lolos ujian) dan `app/Jobs/SendLolosUjianEmailJob.php`
- `app/Mail/CertificateMail.php` + `app/Services/CertificateService.php`
- `app/Mail/UjianLinkMail.php`

---

## 5. UI Admin

Route baru (di grup role, **khusus `admin`** — email bersifat sensitif):

| Method | URL | Fungsi |
|---|---|---|
| GET | `/admin/email-templates` | Daftar 7 template: label, status (**Default** / **Diubah** + waktu edit), tombol Edit |
| GET | `/admin/email-templates/{key}/edit` | Form: input Subject (hint: kosongkan = pakai judul asli), textarea body (monospace, font kecil), chip tombol placeholder (klik untuk sisipkan ke kursor), tombol **Simpan**, **Reset ke Default**, **Preview** |
| POST | `/admin/email-templates/{key}/preview` | Terima draft (subject + body dari form, bisa belum disimpan) + mode data contoh → balik **HTML utuh** (cangkang asli + isi draft + placeholder sudah dirender) untuk ditampilkan di `<iframe>` |
| POST | `/admin/email-templates/{key}` | Simpan (upsert subject & body) → back + `success` |
| DELETE | `/admin/email-templates/{key}` | Reset ke default (hapus baris) → back + `success` |

- Preview pakai **data contoh** per key (mis. `{nama}` → `Budi Santoso`, `{otp}` → `123456`, `{linkUjian}` → `https://…`, dst) — didefinisikan map per key di controller.
- **Peringatan lembut** (bukan blokir): bila body tersimpan untuk key `otp`/`email_change_otp` tetapi `{otp}` tidak ada, atau `certificate` tanpa `{nama}` — tampilkan warning kuning di halaman edit & preview: "Placeholder {otp} tidak ditemukan — kode tidak akan tampil di email."
- Menu sidebar **"Kelola Email"** di bawah Pengaturan Pendaftaran, **ditampilkan hanya untuk role `admin`** (konsisten dengan aturan akses route — hindari menu yang bisa diklik tapi 403).
- Flash message memakai pola SweetAlert layout admin yang ada (`session('success')`/`session('error')`).

---

## 6. Keamanan & Keandalan

- Akses: middleware `auth` + `role:admin` (hanya `admin`).
- Body dirender dengan `{!! !!}` **sengaja** (HTML email) — aman karena: input hanya bisa ditulis role admin, dan isi DB tidak pernah dikompile jadi kode (lihat §3).
- Fallback fail-safe: row tidak ada / kolom NULL → jalur lama. Tidak ada kasus email gagal kirim gara-gara template kosong.
- Validasi simpan: `key` wajib salah satu dari whitelist (404 bila asing), `subject` max 200 char nullable, `body` max 64KB nullable.

---

## 7. Pengujian

`tests/Feature/EmailTemplateTest.php` (SQLite in-memory, RefreshDatabase):

1. Tanpa baris → kirim OTP memakai subject & isi Blade default (assert view + subject).
2. Dengan baris (subject + body terisi) → subject ter-override, body ter-override, placeholder `{nama}`/`{otp}` terganti nilai asli.
3. Baris ada, `body` NULL → isi Blade default, subject override tetap berlaku (dan sebaliknya).
4. Substitusi placeholder tidak mengikuti tak dikenal (dibiarkan literal, tidak error).
5. Endpoint daftar/edit/preview: admin 200, tamu redirect login, role non-admin (mis. `akademik`) 403.
6. Simpan + Reset ke default: `assertDatabaseHas` / baris hilang, status kembali Default.
7. Preview menerima draft yang belum disimpan dan merendernya.

Verifikasi manual (server dev 8001): buka daftar → edit `otp` → ubah teks → preview cocok → simpan → kirim OTP nyata (via fitur lupa password / register dengan jendela buka) → cek `storage/logs/laravel.log` (MAIL_MAILER=log) memakai isi baru → reset.

---

## 8. Catatan Deploy

- Tabel baru dibuat oleh migration (`php artisan migrate`), aman untuk DB production yang ada (tabel kosong = tidak ada perubahan perilaku).
- Tidak ada dependensi service/key baru (berbeda dengan Turnstile).
- `.env` tidak berubah.
