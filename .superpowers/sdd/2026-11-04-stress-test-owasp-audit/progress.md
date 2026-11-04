# SDD ledger — plan: docs/superpowers/plans/2026-11-04-stress-test-owasp-audit.md
Ruling: kerja di branch test/stress-owasp-2026-11-04 (bukan worktree) — vendor/node_modules/.env sudah ada di repo, worktree butuh duplikasi besar — cost if wrong: branch lokal, mudah dibuang
Task 1: complete (staging DB mfls_staging, .env.staging, port 8002, 60 akun QA, login+admin OK)
Task 2: complete (k6 v2.3.0 terpasang, smoke 12/12 check lulus)
Ruling: DB staging butuh GRANT via sudo mysql — user u595896399_root tidak punya CREATE DATABASE — cost if wrong: nol, hanya grant lokal
Ruling: server harus start dengan APP_ENV=staging (flag --env saja tidak memuat .env.staging; sempat nembak DB production) — cost if wrong: stress test kena DB production
Ruling: Apache staging prefork (MaxRequestWorkers 24) di port 8002 menggantikan dev server single-process agar konkurensi multi-worker stabil dan realistis
Task 3: complete (load test 30 VU: 2,348 reqs, 19.09 RPS, checks 99.54% lulus; 6 fitur wajib diuji individual 10 VU 100% checks lulus; stress test 20->120 VU berhasil mengidentifikasi breakpoint di 120 VU p95=59.75s)
Task 4: complete (Audit OWASP Top 10 selesai: 2 temuan kritis A01 di rute debug tanpa auth, kerentanan A06 dompdf/composer, A02 rand() OTP & plaintext token, A04 bypass throttle User-Agent)
Task 5: complete (Laporan docs/reports/2026-11-04-stress-test.md dan docs/reports/2026-11-04-owasp-top10.md selesai disusun dengan metrik empiris)
