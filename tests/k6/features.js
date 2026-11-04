/**
 * k6 — uji beban per fitur wajib MFLS.
 *
 * Satu fitur per run supaya metrik tidak tercampur:
 *   FEATURE=register|upload|cbt|dashboard|biodata|ujian
 *
 * Contoh:
 *   k6 run -e BASE_URL=http://127.0.0.1:8002 -e FEATURE=dashboard -e VUS=10 \
 *          --summary-export=/tmp/opencode/k6-dashboard.json tests/k6/features.js
 *
 * Target uji: staging lokal (bukan production).
 */
import http from 'k6/http';
import { check, sleep } from 'k6';
import { Trend } from 'k6/metrics';
import {
  accountFor, draftJawaban, getPage, getToken, login, register,
  submitUjian, uploadBerkas, updateBiodata, UJIAN_ID,
} from './lib/common.js';

const EXEC = {
  register: 'registerFlow',
  upload: 'uploadFlow',
  cbt: 'cbtFlow',
  dashboard: 'dashboardFlow',
  biodata: 'biodataFlow',
  ujian: 'ujianFlow',
};

const FEATURE = __ENV.FEATURE || 'dashboard';
const EXEC_FN = EXEC[FEATURE] || 'dashboardFlow';
const VUS = Number(__ENV.VUS || 10);
const HOLD = __ENV.HOLD || '2m';

export const options = {
  scenarios: {
    [FEATURE]: {
      executor: 'ramping-vus',
      startVUs: 0,
      stages: [
        { duration: '20s', target: VUS },
        { duration: HOLD, target: VUS },
        { duration: '15s', target: 0 },
      ],
      gracefulStop: '30s',
      exec: EXEC_FN,
      tags: { feature: FEATURE },
    },
  },
  thresholds: {
    [`http_req_duration{feature:${FEATURE}}`]: ['p(95)<5000'],
    [`http_req_failed{feature:${FEATURE}}`]: ['rate<0.01'],
    checks: ['rate>0.95'],
  },
};

/** Login sekali per VU; sesi (cookie) dipakai ulang di iterasi berikutnya. */
let ready = false;
function ensureLogin() {
  if (ready) return true;
  const res = login(accountFor());
  ready = res.status === 302;
  return ready;
}

/* ---------- 1. Pendaftaran ---------- */
export function registerFlow() {
  const uniq = `${__VU}${__ITER}${Date.now()}`;
  register(uniq);
  sleep(1);
}

/* ---------- 2. Upload Berkas ---------- */
export function uploadFlow() {
  if (!ensureLogin()) return;
  const page = getToken('/pendaftar/berkas');
  check(page.res, { 'halaman berkas 200': (r) => r.status === 200 });
  uploadBerkas(page.token);
  sleep(1);
}

/* ---------- 3. Ujian CBT (mulai ujian + autosave) ---------- */
export function cbtFlow() {
  if (!ensureLogin()) return;
  const list = getPage('/pendaftar/ujian');
  check(list, { 'daftar ujian 200': (r) => r.status === 200 });

  const start = getPage(`/pendaftar/ujian/${UJIAN_ID}`, { name: 'GET /pendaftar/ujian/:id' });
  check(start, { 'halaman ujian 200/302': (r) => r.status === 200 || r.status === 302 });

  const token = (start.body.match(/name="csrf-token"\s+content="([^"]+)"/) || [])[1] || '';
  draftJawaban(token);
  sleep(1);
}

/* ---------- 4. Dashboard ---------- */
export function dashboardFlow() {
  if (!ensureLogin()) return;
  const dash = getPage('/pendaftar/dashboard');
  check(dash, { 'dashboard 200': (r) => r.status === 200 });
  const nilai = getPage('/pendaftar/nilai');
  check(nilai, { 'halaman nilai 200': (r) => r.status === 200 });
  sleep(1);
}

/* ---------- 5. Biodata Diri ---------- */
export function biodataFlow() {
  if (!ensureLogin()) return;
  const page = getToken('/pendaftar/biodata');
  check(page.res, { 'halaman biodata 200': (r) => r.status === 200 });
  updateBiodata(page.token);
  sleep(1);
}

/* ---------- 6. Fitur Ujian (submit jawaban + lihat hasil) ---------- */
export function ujianFlow() {
  if (!ensureLogin()) return;
  const page = getToken(`/pendaftar/ujian/${UJIAN_ID}`);
  draftJawaban(page.token);
  // Submit berulang: 200 saat pertama, 400 saat sudah pernah submit — keduanya sah.
  submitUjian(page.token);
  const nilai = getPage('/pendaftar/nilai');
  check(nilai, { 'halaman nilai 200': (r) => r.status === 200 });
  sleep(1);
}
