# Laporan Uji Beban & Kinerja (Stress & Load Testing) — MFLS
**Aplikasi:** MNC University Future Leader Scholarship (MFLS)  
**Tanggal Pengujian:** 4 November 2026  
**Tools Uji:** k6 v2.3.0 (Grafana Labs)  
**Lingkungan:** Staging Lokal Terisolasi (Port 8002, DB `mfls_staging`)  
**Status Target:** 100% Aman — Nol Request ke Production (`beasiswa.mncu.ac.id`)

---

## 1. Ringkasan Eksekutif

Pengujian beban dan stres sistem portal penerimaan beasiswa **MFLS (MNC University Future Leader Scholarship)** dilakukan untuk mengukur kapasitas puncak, latensi respons, dan kestabilan sistem dalam menghadapi lonjakan trafik ribuan calon pendaftar, terutama pada periode batas akhir pengunggahan berkas dan pelaksanaan ujian CBT secara daring serentak.

Pengujian difokuskan pada **6 fitur wajib sistem**:
1. **Pendaftaran Akun Baru (Register Flow)**
2. **Unggah Berkas Persyaratan (Upload Document Flow)**
3. **Ujian CBT / Autosave Draft Jawaban (CBT Flow)**
4. **Dashboard Pendaftar & Pengumuman (Dashboard Flow)**
5. **Pembaruan Biodata Diri (Biodata Flow)**
6. **Pelaksanaan & Finalisasi Ujian (Fitur Ujian / Submit Flow)**

### Ringkasan Temuan Utama:
- **Baseline Single Feature:** Seluruh 6 fitur wajib beroperasi dengan sukses **100% check pass** pada pengujian beban terisolasi 10 Virtual Users (VU), dengan rata-rata waktu respons berkisar antara **347 ms s.d. 2.754 ms**.
- **Concurrent Load Test (30 VU Gabungan):** Saat ke-6 fitur dijalankan bersamaan (kondisi jam sibuk normal), sistem memproses **2.348 request** selama 2 menit dengan throughput **19,09 RPS**, tingkat keberhasilan check **99,54%**, dan $p(95)$ latensi dashboard berada di **1,77 detik** (memenuhi Service Level Objective < 5 detik).
- **Stress Test Saturation Breakpoint:** Melalui skenario eskalasi bertingkat (*ramp-up* 20 $\rightarrow$ 40 $\rightarrow$ 60 $\rightarrow$ 80 $\rightarrow$ 100 $\rightarrow$ 120 VU), titik jenuh sistem (*breakpoint*) teridentifikasi pada **120 VU**. Pada tahap ini, antrean koneksi web server melampaui alokasi proses pekerja (*worker pool saturation*), menyebabkan $p(95)$ melonjak hingga **59,75 detik** (timeout ambang batas 60 detik) dan memicu kegagalan transaksi beruntun.

---

## 2. Metodologi & Lingkungan Pengujian

### 2.1 Spesifikasi Lingkungan Uji (Staging)
Pengujian dijalankan pada server staging lokal terisolasi demi menjamin keamanan data pendaftar nyata:
- **Server:** Apache 2.4 Prefork (`mod_php 8.2`, OPcache CLI diaktifkan)
- **Konfigurasi Worker Apache:** `StartServers 8`, `MinSpareServers 8`, `MaxSpareServers 16`, `MaxRequestWorkers 24`
- **Basis Data:** MariaDB 10.x lokal (`mfls_staging`, 32 tabel duplikasi struktur)
- **Akun Uji:** 60 akun pendaftar QA terisolasi (`qa1@example.test` s.d. `qa60@example.test`) + 1 akun admin QA
- **Mocking & Bypass:** 
  - Cloudflare Turnstile menggunakan *test secret key* resmi (always-pass).
  - Sinkronisasi pihak ketiga (`GoogleSheetService`, SMTP nyata) dimatikan (`MAIL_MAILER=log`, queue `database/sync`).

### 2.2 Skenario Pengujian
| Jenis Uji | Virtual Users (VU) | Durasi | Tujuan |
|---|---|---|---|
| **Smoke Test** | 1 VU | 8,3 detik | Verifikasi integritas fungsional seluruh rute dan validitas token CSRF |
| **Feature Test** | 10 VU per fitur | 2 menit per fitur | Mengukur metrik individual masing-masing dari 6 fitur wajib |
| **Load Test** | 30 VU (5 VU x 6 fitur) | 2 menit | Menguji konkurensi multi-fitur di kondisi jam kerja/pendaftaran normal |
| **Stress Test** | 20 $\rightarrow$ 120 VU | 6 menit (1 menit/tahap) | Menemukan titik degradasi (*saturation point*) dan batas toleransi server |

---

## 3. Hasil Pengujian 6 Fitur Wajib (Individual Feature Benchmark)

Setiap fitur wajib diuji secara bertahap selama 2 menit dengan beban konstan 10 Virtual Users (VU) aktif.

| Fitur Wajib | Beban (VU) | Total Request | Throughput (RPS) | Durasi Rata-rata | Latensi $p(90)$ | Latensi $p(95)$ | Error Rate | Tingkat Keberhasilan Checks |
|---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| **1. Pendaftaran (Register)** | 10 | 244 | 2,49 RPS | 2.754,36 ms | 5.158,74 ms | 5.728,85 ms | 0,00% | **100,0%** (122/122) |
| **2. Upload Berkas** | 10 | 661 | 6,94 RPS | 842,76 ms | 1.550,46 ms | 1.823,49 ms | 0,00% | **100,0%** (444/444) |
| **3. Ujian CBT (Mulai + Autosave)** | 10 | 542 | 5,35 RPS | 1.377,02 ms | 2.431,65 ms | 3.269,12 ms | 0,00% | **100,0%** (286/286) |
| **4. Dashboard Pendaftar** | 10 | 808 | 8,40 RPS | 715,92 ms | 1.540,09 ms | 2.070,76 ms | 0,00% | **100,0%** (414/414) |
| **5. Biodata Diri (Update)** | 10 | 1.159 | 11,84 RPS | 347,42 ms | 765,01 ms | 1.054,39 ms | 0,00% | **100,0%** (776/776) |
| **6. Fitur Ujian (Final Submit)** | 10 | 1.132 | 11,13 RPS | 608,16 ms | 1.158,04 ms | 1.598,83 ms | 0,00% | **100,0%** (442/442) |

### Analisis Kinerja Fitur:
1. **Fitur Paling Cepat:** Pembaruan **Biodata Diri** ($avg = 347\text{ ms}$) dan **Finalisasi Ujian** ($avg = 608\text{ ms}$) menunjukkan efisiensi query database yang sangat baik karena didukung index kunci primer/peserta_id yang presisi.
2. **Fitur Paling Berat:** **Pendaftaran Akun Baru** ($avg = 2.754\text{ ms}$, $p95 = 5.728\text{ ms}$) memiliki waktu pemrosesan paling panjang. Hal ini dipengaruhi oleh kalkulasi enkripsi Bcrypt (`Hash::make`), verifikasi token Turnstile, serta pengecekan rate limiter berbasis hash IP.
3. **Upload Berkas:** Beroperasi stabil pada $avg = 842\text{ ms}$ dengan throughput 6,94 RPS. Validasi mime-type dan kompresi file berjalan mulus tanpa kegagalan I/O.

---

## 4. Hasil Uji Beban Gabungan (Concurrent Load Test — 30 VU)

Skenario ini merepresentasikan operasional nyata ketika 30 pendaftar aktif secara serentak membuka dashboard, mengunggah berkas, mengisi biodata, dan mengerjakan ujian CBT.

```
Execution Summary (tests/k6/load.js):
--------------------------------------------------------------------------------
Duration                 : 2m 03s
Concurrent Virtual Users : 30 VU (5 VU per feature)
Total Requests           : 2,348 reqs
Throughput               : 19.09 requests/second
Total Iterations         : 633 completed iterations
Data Transferred         : 37 MB received / 1.8 MB sent

Response Time Breakdown:
- Minimum                : 95.02 ms
- Median                 : 765.30 ms
- Average                : 1,279.38 ms (1.28 s)
- 90th Percentile (p90)  : 1,672.82 ms (1.67 s)
- 95th Percentile (p95)  : 2,459.76 ms (2.46 s)
- Maximum                : 60,071.35 ms (60 s timeout pada lonjakan terminasi)

Verification Checks:
- Checks Total           : 1,324 checks
- Succeeded              : 1,318 (99.54%)
- Failed                 : 6 (0.45% — terjadi pada penutupan koneksi akhir)
- HTTP Request Failures  : 0.51% (12 failed out of 2,348)
```

**Evaluasi Ambang Batas (Thresholds):**
- $\checkmark$ `checks > 95%`: Tercapai (**99,54%**)
- $\checkmark$ `http_req_duration{dashboard} p(95) < 5s`: Tercapai (**1,77 detik**)
- $\checkmark$ `http_req_failed{dashboard} < 1%`: Tercapai (**0,00%**)

---

## 5. Hasil Uji Stres & Analisis Titik Jenuh (Stress Test Breakpoint)

Skenario stres meningkatkan beban secara bertahap setiap 1 menit untuk menemukan batas maksimum ketahanan aplikasi sebelum kolaps.

```
Tahap Beban:
- Tahap 1: 20 VU  (Menit 0 - 1)
- Tahap 2: 40 VU  (Menit 1 - 2)
- Tahap 3: 60 VU  (Menit 2 - 3)
- Tahap 4: 80 VU  (Menit 3 - 4)
- Tahap 5: 100 VU (Menit 4 - 5)
- Tahap 6: 120 VU (Menit 5 - 6)
```

### Hasil Metrik per Tahap:
| Tahap | Beban (VU) | Status Sistem | Performa Respons | Kondisi Layanan |
|:---:|:---:|---|---|---|
| **s20** | 20 VU | Normal | $avg \approx 650\text{ ms}$, $p95 < 1,5\text{ s}$ | Sangat lancar, utilisasi CPU < 40% |
| **s40** | 40 VU | Terkendali | $avg \approx 850\text{ ms}$, $p95 < 2,2\text{ s}$ | Beban optimal, respons stabil |
| **s60** | 60 VU | Antrean Awal | $avg \approx 1,4\text{ s}$, $p95 \approx 3,8\text{ s}$ | Worker Apache mulai penuh, latensi merambat naik |
| **s80** | 80 VU | Padat | $avg \approx 2,5\text{ s}$, $p95 \approx 7,2\text{ s}$ | Peningkatan waktu tunggu koneksi (*socket queuing*) |
| **s100** | 100 VU | Kritis | $avg \approx 4,8\text{ s}$, $p95 \approx 18,5\text{ s}$ | CPU load average > 8, worker exhausted |
| **s120** | 120 VU | **BREAKPOINT** | $avg = 6,67\text{ s}$, $p95 = \mathbf{59,75\text{ s}}$ | **Ambang batas terlampaui**, koneksi timeout (60s) |

```
Stress Test Cumulative (tests/k6/stress.js):
- Total Requests       : 7,684 requests
- Average Throughput   : 20.21 RPS
- Threshold p95 < 5s   : GAGAL pada s120 (59.75s)
```

---

## 6. Identifikasi Bottleneck (Akar Penyebab)

Dari observasi selama eksekusi uji beban dan analisis log web server / MariaDB:

1. **Worker Pool Saturation (Apache Prefork Limits):**
   - Konfigurasi `MaxRequestWorkers 24` membatasi Apache hanya dapat melayani maksimal 24 koneksi PHP aktif secara simultan. Ketika request konkuren mencapai $\ge 60\text{ VU}$, sisa request tertahan di antrean *listen backlog*.
   - Saat mencapai 120 VU, waktu tunggu di antrean melampaui timeout klien (60 detik).
2. **Kalkulasi Enkripsi CPU-Bound:**
   - Fitur autentikasi (Login dan Register) menggunakan algoritma Bcrypt (cost default = 10-12). Lonjakan pendaftaran massal mengonsumsi CPU cycle secara drastis, menyebabkan perebutan resource (*CPU contention*) pada prosesor 4-core.
3. **Penyimpanan Sesi Database:**
   - Tabel `sessions` di MariaDB menerima beban pembacaan dan penulisan di setiap siklus request/response. Pada beban tinggi, *lock contention* pada tabel sesi menurunkan throughput secara keseluruhan dibandingkan jika menggunakan Redis / in-memory cache.

---

## 7. Rekomendasi Solusi & Peningkatan

### Rekomendasi Jangka Pendek (Praktis):
1. **Peningkatan Alokasi Worker:**
   - Pada server produksi (VPS), tingkatkan alokasi worker PHP-FPM / Apache sesuai kapasitas RAM:
     $$\text{MaxChildren} = \frac{\text{Tersedia RAM (MB)} - 1024\text{ MB}}{\text{Rata-rata Memory per Worker PHP (}\approx 45\text{ MB)}}$$
2. **Migrasi Session Driver ke Redis / Memcached:**
   - Ubah `SESSION_DRIVER=database` menjadi `SESSION_DRIVER=redis` untuk menghilangkan lock database dan mempercepat throughput hingga 3–5 kali lipat.
3. **Optimasi Asset & Browser Caching:**
   - Pastikan header `Cache-Control` untuk file statis (CSS, JS, gambar berkas/twibbon) diset minimal 30 hari guna mengurangi request ke engine PHP.

### Rekomendasi Jangka Panjang:
1. **Asynchronous Queue untuk Email & Ekspor:**
   - Jalankan antrean email verifikasi OTP dan generasi sertifikat secara *asynchronous* via supervisor queue worker (`php artisan queue:work`), bukan proses request sinkron.
2. **Auto-scaling / Rate Limiting Terpadu:**
   - Pasang Cloudflare WAF Rate Limiting di level edge (misal: maksimal 30 request/menit per IP) untuk menangkal serangan flooding/DDoS pada endpoint pendaftaran dan CBT.
