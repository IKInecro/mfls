/**
 * k6 — stress test MFLS: cari titik jenuh server.
 *
 * Tiap tahap adalah scenario terpisah (constant-vus 1 menit) supaya
 * ringkasan JSON menyimpan metrik per tag `stage`, bukan hanya total.
 * Yang dicari: tahap pertama saat error > 1% atau p95 > 5 detik.
 *
 *   k6 run -e BASE_URL=http://127.0.0.1:8002 \
 *          --summary-export=/tmp/opencode/k6-stress.json tests/k6/stress.js
 */
import { sleep } from 'k6';
import { accountFor, draftJawaban, getPage, getToken, login, UJIAN_ID } from './lib/common.js';

const STAGES = [20, 40, 60, 80, 100, 120];
const STEP = '1m';

const scenarios = {};
STAGES.forEach((vus, i) => {
  scenarios[`s${vus}`] = {
    executor: 'constant-vus',
    vus,
    duration: STEP,
    startTime: `${i}m`,
    exec: 'mixed',
    tags: { stage: String(vus) },
    gracefulStop: '20s',
  };
});

export const options = {
  scenarios,
  thresholds: {
    // Tidak dipakai untuk lulus/gagal — hanya penanda titik jenuh di ringkasan.
    'http_req_duration{stage:120}': ['p(95)<5000'],
  },
};

let ready = false;
let token = '';

export function mixed() {
  if (!ready) {
    ready = login(accountFor()).status === 302;
    if (!ready) return;
    token = getToken('/pendaftar/dashboard').token;
  }

  getPage('/pendaftar/dashboard');
  getPage('/pendaftar/ujian');
  draftJawaban(token);
  sleep(1);
}
