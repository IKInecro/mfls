# Fitur: Kontrol Pembukaan Pendaftaran Beasiswa (admin)

Status: SELESAI (2026-10-31) — 14/14 test hijau, smoke test manual lolos.
Design: approved (chat, 2026-10-31). Bounded path.

## Scope (semua done)
1. [x] `.env`: TURNSTILE_SITE_KEY + TURNSTILE_SECRET_KEY (keys asli dari user)
2. [x] Test `tests/Feature/PengaturanPendaftaranTest.php` (12 test: default tertutup, jendela waktu, override, gerbang pendaftar, halaman admin, validasi)
3. [x] Migration `2026_10_31_000001_create_pengaturan_pendaftaran_table` (mulai, selesai, mode auto/buka/tutup)
4. [x] Model `PengaturanPendaftaran` + `instance()` + `aktif(): bool` (satu-satunya sumber kebenaran)
5. [x] `PengaturanController` (GET/POST /admin/pengaturan/pendaftaran) + view + menu sidebar (`layouts/admin.blade.php`)
6. [x] Gerbang 1: `AuthController::showRegister()` + `register()` → cek aktif()
7. [x] Gerbang 2: route pendaftar → closure `$gate(method)` (route asli dipulihkan dari git history d368c54)
8. [x] Gerbang 3: homepage — status pill + 3 tombol dinamis + countdown ikut `pendaftaran_selesai`
9. [x] Swap reCAPTCHA → Cloudflare Turnstile (register.blade.php + AuthController siteverify)
10. [x] Verifikasi: `php artisan test` 14 passed + smoke curl (tutup/buka/simpan)

## Perubahan tambahan yang tidak ada di design awal
- `phpunit.xml`: sqlite :memory: diaktifkan (dulu test nembak MySQL dev)
- 4 migration di-guard `DB::getDriverName()==='mysql'` (raw ALTER MODIFY gak valid di SQLite): 003128, 003843, 005443, 230616 — perilaku MySQL TIDAK berubah
- `tests/Feature/ExampleTest.php`: + RefreshDatabase (butuh tabel di sqlite memory)
- Countdown hardcoded `2026-05-04` (udah lewat) → ikut `pendaftaran_selesai`

## Deploy note (server baru beasiswa.mncu.ac.id)
- `.env` production: APP_URL, FRONTEND_URL, SANCTUM_STATEFUL_DOMAINS → domain baru + TURNSTILE key asli (domain `localhost`/`127.0.0.1`/domain asli ke-daftar di dashboard Turnstile)
- Baris default DB baru = tertutup; admin set jadwal via /admin/pengaturan/pendaftaran
