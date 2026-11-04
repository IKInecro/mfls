import { check, sleep } from 'k6';
import {
  accountFor, draftJawaban, getToken, login, register, startUjian,
  submitUjian, uploadBerkas, updateBiodata, getPage,
} from './lib/common.js';

export const options = {
  vus: 1,
  iterations: 1,
  thresholds: {
    checks: ['rate==1'],
  },
};

export default function () {
  const uniq = `${Date.now()}`;
  register(uniq);

  const email = accountFor();
  login(email);

  const dashboard = getPage('/pendaftar/dashboard');
  check(dashboard, { 'dashboard 200': (r) => r.status === 200 });

  const berkas = getToken('/pendaftar/berkas');
  check(berkas.res, { 'halaman berkas 200': (r) => r.status === 200 });
  uploadBerkas(berkas.token);

  const biodata = getToken('/pendaftar/biodata');
  updateBiodata(biodata.token);

  const ujian = startUjian('');
  draftJawaban(ujian.token);
  submitUjian(ujian.token);

  sleep(1);
}
