# Panel Ujian + Halaman CBT Peserta (Fitur Ujian)

Tanggal: 2026-10-31
Status: Disetujui user ("gas")

## Konteks

Mesin ujian (tabel `ujian`/`soal`/`jawaban_ujian`/`nilai_ujian`/`dispensasi_ujian`,
API CBT, Bank Soal, Hasil Ujian, Seleksi Ujian) sudah ada. Yang belum ada:

1. **Gak bisa bikin test baru** — ujian cuma lahir dari seeder; Bank Soal cuma edit durasi + is_active.
2. **Gak ada panel kontrol sesi** (jadwal mulai/selesai + buka/tutup manual).
3. **Gak ada halaman web buat peserta ngerjain** — API `/api/soal` cuma dipakai app external.
4. Email undangan `ujian_link` dead code (belum pernah dikirim).

## Keputusan user

- Ujian dikerjakan lewat **halaman web Laravel ini** (API lama tetap jalan).
- Kontrol sesi: **jadwal otomatis (jam mulai–selesai) + override manual** ala fitur pendaftaran.
- UI pengerjaan: **CBT satu soal per layar** (panel nomor soal, ragu-ragu, timer, autosave).
- Anti-copy level: **blokir select/copy/klik kanan/print** — TANPA deteksi pindah tab, TANPA watermark.
- Peserta masuk lewat **kartu di dashboard + link email undangan** (login tetap wajib).
- Responsif: mobile & desktop sama-sama nyaman.
- Test baru (mis. Test Akademik, Bahasa Inggris, Wawasan Kebangsaan, ...) dibuat dari panel.

## Desain

### 1. Skema (migration baru)

`ujian` tambah kolom:
- `jadwal_mulai` datetime nullable
- `jadwal_selesai` datetime nullable
- `override_status` string default `'auto'` (nilai: `auto` | `buka` | `tutup`)

Tabel baru `sesi_ujian` (status pengerjaan per peserta per ujian):
- `ujian_id` FK cascade, `peserta_id` FK cascade, unique pair
- `mulai_pada` datetime (kapan peserta mulai — dasar timer)
- `jawaban` text nullable (draft autosave, JSON `{soal_id: "a"}`)
- timestamps

`jawaban_ujian`/`nilai_ujian` TIDAK diubah — submit final tetap lewat logika API
yang ada (skor, AI narrative pemetaan diri, dsb). `sesi_ujian` dihapus saat final
submit sukses.

### 2. Model `Ujian`

- `isOpen(): bool` — aturan akses (dipakai halaman web DAN API):
  - `override_status === 'tutup'` → tertutup; `buka` → terbuka (lewati jadwal);
  - `auto`: `is_active` && (tanpa jadwal → terbuka bila is_active; ada `jadwal_mulai`/`jadwal_selesai` → sekarang di dalam rentang).
- `sisaDetik(peserta): int` — dari `mulai_pada + durasi*60 + dispensasi`, dibatasi
  `jadwal_selesai + dispensasi`. Belum mulai → `durasi*60`.
- `statusSesi(): 'tertutup'|'berjalan'|'selesai'` untuk label di panel & kartu peserta.

### 3. Panel Ujian (admin)

Route di grup `role:admin,akademik` (ngikutin aturan Bank Soal), menu sidebar
"Panel Ujian" dekat Bank Soal. Halaman responsif, kartu per test:

- Info: nama, durasi, jumlah soal, jadwal, status (Belum dibuka / Berjalan / Selesai),
  jumlah peserta selesai / belum.
- Aksi per kartu:
  - **Tambah Test Baru** — form: nama, durasi (menit), jadwal mulai, jadwal selesai (boleh kosong).
  - **Atur Jadwal** — update jadwal mulai–selesai.
  - **Buka sekarang / Tutup sekarang** — set `override_status`; tombol "Kembalikan ke otomatis".
  - **Kelola Soal** → `/admin/soal?ujian_id={id}` (filter sudah ada).
  - **Edit** nama/durasi, **Hapus Test** (konfirmasi; soal, sesi, jawaban, nilai ikut cascade).
  - **Kirim Email Undangan** — kirim `UjianLinkMail` (link `/ujian`) ke semua peserta
    `status_seleksi_ujian = 'lulus'`; loop sinkron dengan try/catch, flash jumlah sukses/gagal.

`updateUjian` lama di Bank Soal tetap (durasi + is_active).

### 4. Halaman peserta (role:pendaftar)

- `GET /ujian` — daftar test: kartu grid (1 kolom mobile, 3 kolom desktop). Per kartu
  status: "Belum dibuka" (disabled), "Mulai Ujian", "Lanjutkan" (sesi ada draft),
  "Sudah dikerjakan" (lihat skor), "Ujian selesai" (waktu habis).
- `GET /ujian/{id}` — halaman pengerjaan:
  - Server cek: `isOpen()`, belum final-submit, sesi masih dalam sisa waktu → kalau
    belum ada sesi, `POST /ujian/{id}/mulai` buat `mulai_pada`.
  - Layout: desktop → sidebar grid nomor soal (1–N, warna: terjawab / belum / ragu) +
    soal tengah; mobile → grid nomor atas, navigasi sticky bawah. Timer besar di header.
  - Autosave draft → `POST /ujian/{id}/draft` (JSON), 1 detik debounce + saat
    navigasi soal; server tolak kalau sesi lewat batas waktu.
  - Submit → `POST /api/soal/submit` (endpoint lama, jangan diduplikasi). Sukses →
    hapus sesi, tampilkan hasil.
  - Timer habis (client) → auto-submit pakai jawaban tersimpan.
- Dashboard: kartu "Mulai Ujian" → `/ujian` (selalu tampil, status di dalamnya).

### 5. Pengerasan API lama (gate jadwal)

`SoalController::getUjians/index/submit` tambah cek `Ujian::isOpen()` (403 kalau
tutup; peserta bisa GET sesi yang sudah ia mulai walau jadwal lewat — dibatasi
`jadwal_selesai + dispensasi` di submit). App external ikut tergate — memang
diinginkan: admin yang pegang kontrol buka/tutup.

### 6. Anti-copy (di halaman pengerjaan saja)

CSS `user-select:none` + `touch-action` pada soal/jawaban; JS blokir `contextmenu`,
`copy`, `cut`, `dragstart`, `selectstart`, dan shortcut Ctrl/Cmd+C/X/P/S. Teks tetap
bisa diakali via DevTools — penghalang, bukan tembok (disampaikan ke user).

### 7. Yang TIDAK berubah

- API lama (rute, format JSON, skor, AI narrative).
- Bank Soal, Hasil Ujian, Seleksi Ujian, dispensasi, penilaian.
- Ujian buatan baru otomatis muncul di Bank Soal (query `Ujian::all()`).

## Tahapan eksekusi

1. Migration (kolom ujian + tabel sesi), model `Ujian` helpers + `SesiUjian`, Panel Ujian admin (CRUD test, jadwal, override, kirim undangan).
2. Halaman daftar test + kartu dashboard.
3. Halaman pengerjaan CBT (render, mulai sesi, autosave, submit via API, auto-submit habis waktu) + gate `isOpen()` di API.
4. Anti-copy layer.
5. Hidupkan email `ujian_link` dari Panel Ujian.
6. Test suite (gate jadwal, override, sesi/timer, autosave, anti-copy markup, CRUD test, akses role) + smoke manual.

## Risiko / catatan

- `Mail::send` closure butuh `use(...)` (pelajaran bug d1a43b4).
- Timezone pakai `now()` app konsisten untuk jadwal & sisa waktu.
- Rollback aman: migration `down()` drop kolom/tabel; rute & view baru tidak menyentuh kode lama selain penambahan gate `isOpen()` di API.
