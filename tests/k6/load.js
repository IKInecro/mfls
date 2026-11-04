/**
 * k6 — load test gabungan: 6 fitur wajib berjalan bersamaan.
 * Tiap fitur 5 VU (total 30 VU) selama 2 menit — kondisi "jam sibuk" normal.
 *
 *   k6 run -e BASE_URL=http://127.0.0.1:8002 \
 *          --summary-export=/tmp/opencode/k6-load.json tests/k6/load.js
 */
import { check, sleep } from 'k6';
import {
  accountFor, draftJawaban, getPage, getToken, login, register,
  submitUjian, uploadBerkas, updateBiodata, UJIAN_ID,
} from './lib/common.js';

const VUS = Number(__ENV.VUS || 5);

function scenario(exec) {
  return {
    executor: 'constant-vus',
    vus: VUS,
    duration: __ENV.HOLD || '2m',
    exec,
    tags: { feature: exec },
    gracefulStop: '30s',
  };
}

export const options = {
  scenarios: {
    register: scenario('registerFlow'),
    upload: scenario('uploadFlow'),
    cbt: scenario('cbtFlow'),
    dashboard: scenario('dashboardFlow'),
    biodata: scenario('biodataFlow'),
    ujian: scenario('ujianFlow'),
  },
  thresholds: {
    checks: ['rate>0.95'],
    'http_req_failed{feature:dashboardFlow}': ['rate<0.01'],
    'http_req_duration{feature:dashboardFlow}': ['p(95)<5000'],
  },
};

// Satu instance modul per VU → satu flag cukup.
let ready = false;

/** Login sekali per VU; `off` memisahkan akun antar scenario (60 akun QA). */
function ensureLogin(off) {
  if (ready) return true;
  ready = login(accountFor(__VU + off)).status === 302;
  return ready;
}

export function registerFlow() {
  register(`${__VU}${__ITER}${Date.now()}`);
  sleep(1);
}

export function uploadFlow() {
  if (!ensureLogin(10)) return;
  const page = getToken('/pendaftar/berkas');
  check(page.res, { 'halaman berkas 200': (r) => r.status === 200 });
  uploadBerkas(page.token);
  sleep(1);
}

export function cbtFlow() {
  if (!ensureLogin(20)) return;
  const list = getPage('/pendaftar/ujian');
  check(list, { 'daftar ujian 200': (r) => r.status === 200 });
  const start = getPage(`/pendaftar/ujian/${UJIAN_ID}`, { name: 'GET /pendaftar/ujian/:id' });
  check(start, { 'halaman ujian 200/302': (r) => r.status === 200 || r.status === 302 });
  const token = (start.body.match(/name="csrf-token"\s+content="([^"]+)"/) || [])[1] || '';
  draftJawaban(token);
  sleep(1);
}

export function dashboardFlow() {
  if (!ensureLogin(30)) return;
  check(getPage('/pendaftar/dashboard'), { 'dashboard 200': (r) => r.status === 200 });
  check(getPage('/pendaftar/nilai'), { 'halaman nilai 200': (r) => r.status === 200 });
  sleep(1);
}

export function biodataFlow() {
  if (!ensureLogin(40)) return;
  const page = getToken('/pendaftar/biodata');
  check(page.res, { 'halaman biodata 200': (r) => r.status === 200 });
  updateBiodata(page.token);
  sleep(1);
}

export function ujianFlow() {
  if (!ensureLogin(50)) return;
  const page = getToken(`/pendaftar/ujian/${UJIAN_ID}`);
  draftJawaban(page.token);
  submitUjian(page.token);
  check(getPage('/pendaftar/nilai'), { 'halaman nilai 200': (r) => r.status === 200 });
  sleep(1);
}
