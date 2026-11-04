import http from 'k6/http';
import { check } from 'k6';
import { randomString } from 'https://jslib.k6.io/k6-utils/1.4.0/index.js';

export const BASE = __ENV.BASE_URL || 'http://127.0.0.1:8002';
export const PASSWORD = __ENV.QA_PASSWORD || 'TestPass123!';
export const UJIAN_ID = Number(__ENV.UJIAN_ID || 1);
export const MAX_ACCOUNT = Number(__ENV.QA_ACCOUNTS || 60);

const FIXTURES = {
  png: open('../fixtures/sample.png', 'b'),
};

function tokenFrom(html) {
  if (!html) return '';
  const hidden = html.match(/name="_token"\s+value="([^"]+)"/);
  if (hidden) return hidden[1];
  const meta = html.match(/name="csrf-token"\s+content="([^"]+)"/);
  return meta ? meta[1] : '';
}

/** Akun QA stabil per VU: qa1..qa60 (dibuat oleh reset.sh). */
export function accountFor(vu = __VU) {
  return `qa${(vu % MAX_ACCOUNT) + 1}@example.test`;
}

export function getPage(path, tags) {
  return http.get(`${BASE}${path}`, { tags: tags || { name: `GET ${path}` } });
}

export function getToken(path) {
  const res = getPage(path);
  return { res, token: tokenFrom(res.body) };
}

export function login(email) {
  const { token } = getToken('/login');
  const res = http.post(
    `${BASE}/login`,
    { _token: token, email, password: PASSWORD },
    { redirects: 0, tags: { name: 'POST /login' } }
  );

  check(res, {
    'login 302 ke dashboard': (r) =>
      r.status === 302 && String(r.headers.Location || '').includes('/pendaftar/dashboard'),
  });

  return res;
}

/** Registrasi lengkap (berhenti di halaman OTP). Turnstile pakai test secret always-pass. */
export function register(uniq) {
  const { token } = getToken('/register');
  const suffix = String(uniq).replace(/\D/g, '').slice(-9).padStart(9, '0');

  const payload = {
    _token: token,
    nama: `Load Test ${uniq}`,
    nisn: `8${suffix}`.slice(0, 10),
    email: `lt.${uniq}@example.test`,
    password: PASSWORD,
    password_confirmation: PASSWORD,
    no_whatsapp: `0899${suffix}`.slice(0, 14),
    tgl_lahir: '2008-01-01',
    jenis_kelamin: 'Laki-laki',
    tahun_lulus: '2026',
    provinsi: 'DKI Jakarta',
    kabupaten: 'Jakarta Selatan',
    nama_sekolah: 'SMA Load Test',
    no_guru_bk: '081200000000',
    sumber_informasi: 'Load Test',
    kode_referral: '',
    'cf-turnstile-response': 'XXXX.DUMMY.TOKEN.XXXX',
  };

  const res = http.post(`${BASE}/register`, payload, {
    redirects: 0,
    headers: { 'User-Agent': `k6-lt-${uniq}-${randomString(8)}` },
    tags: { name: 'POST /register' },
  });

  check(res, {
    'register diterima (302 ke OTP)': (r) =>
      r.status === 302 && String(r.headers.Location || '').includes('/verify-otp'),
  });

  return res;
}

/** Upload satu berkas (foto). Butuh sesi pendaftar yang sudah login. */
export function uploadBerkas(token) {
  const res = http.post(
    `${BASE}/pendaftar/berkas`,
    {
      _token: token,
      upload_field: 'foto',
      foto: http.file(FIXTURES.png, `foto-${randomString(6)}.png`, 'image/png'),
    },
    { redirects: 0, tags: { name: 'POST /pendaftar/berkas' } }
  );

  check(res, { 'upload diterima': (r) => r.status === 302 || r.status === 200 });
  return res;
}

export function updateBiodata(token, vu = __VU) {
  const res = http.post(
    `${BASE}/pendaftar/biodata`,
    {
      _token: token,
      nama: `QA User ${(vu % MAX_ACCOUNT) + 1}`,
      nisn: `99900${String((vu % MAX_ACCOUNT) + 1).padStart(5, '0')}`,
      no_whatsapp: `0813000${String((vu % MAX_ACCOUNT) + 1).padStart(5, '0')}`,
      jenis_kelamin: 'Laki-laki',
      tgl_lahir: '2008-01-01',
      provinsi: 'DKI Jakarta',
      kabupaten: 'Jakarta Selatan',
      nama_sekolah: 'SMA QA',
      tahun_lulus: '2026',
      no_guru_bk: '081300099999',
      kode_referral: '',
    },
    { redirects: 0, tags: { name: 'POST /pendaftar/biodata' } }
  );

  check(res, { 'biodata tersimpan': (r) => r.status === 302 });
  return res;
}

export function startUjian(token) {
  const res = getPage(`/pendaftar/ujian/${UJIAN_ID}`, { name: 'GET /pendaftar/ujian/:id' });
  check(res, { 'halaman ujian 200/302': (r) => r.status === 200 || r.status === 302 });
  return { res, token: token || tokenFrom(res.body) };
}

export function draftJawaban(token) {
  const res = http.post(
    `${BASE}/pendaftar/ujian/${UJIAN_ID}/draft`,
    JSON.stringify({ jawaban: { 1: 'a', 2: 'b', 3: 'c' } }),
    {
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
      tags: { name: 'POST /pendaftar/ujian/:id/draft' },
    }
  );

  check(res, { 'draft tersimpan (200)': (r) => r.status === 200 });
  return res;
}

export function submitUjian(token) {
  const res = http.post(
    `${BASE}/pendaftar/ujian/${UJIAN_ID}/submit`,
    JSON.stringify({ ujian_id: UJIAN_ID, answers: { 1: 'a', 2: 'b', 3: 'c' } }),
    {
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
      tags: { name: 'POST /pendaftar/ujian/:id/submit' },
    }
  );

  check(res, { 'submit 200/400': (r) => r.status === 200 || r.status === 400 });
  return res;
}
