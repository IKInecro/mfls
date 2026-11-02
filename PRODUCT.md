# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

Calon mahasiswa baru berprestasi (siswa/i kelas 12 SMA/SMK/MA sederajat tahun ajaran 2026/2027, atau lulusan tahun ajaran 2022/2023/2024/2025/2026/2027) di seluruh Indonesia yang mencari beasiswa kuliah unggulan jenjang sarjana (S1), berkeinginan mengembangkan jiwa kepemimpinan (leadership), serta membutuhkan dukungan biaya kuliah hingga 100% sampai lulus di Media Nusantara Citra University (MNC University).

## Product Purpose

MNCU Future Leader Scholarship (MFLS) Ajaran 2027 adalah portal pendaftaran dan seleksi beasiswa resmi MNC University yang diselenggarakan bersama ekosistem MNC Group. Platform ini memfasilitasi seluruh alur seleksi mulai dari pengenalan program, pendaftaran akun, kelengkapan berkas & esai kepemimpinan, ujian seleksi berbasis komputer (CBT online), hingga pengumuman penerima beasiswa (awardee) dengan cakupan beasiswa parsial (75%) hingga beasiswa penuh (100% selama 8 semester).

## Positioning

Program beasiswa sarjana yang terintegrasi langsung dengan ekosistem konglomerasi media dan industri terbesar di Indonesia (MNC Group), menyediakan tidak hanya keringanan biaya kuliah hingga 100%, melainkan kurikulum kepemimpinan langsung dari praktisi industri, company visit/sit-in class di unit bisnis MNC, peluang magang, serta prioritas rekrutmen karier bagi lulusan terbaik.

## Operating Context

- Digunakan oleh calon mahasiswa dari berbagai latar belakang perangkat (mayoritas smartphone mobile 360px-430px dan laptop desktop).
- Memerlukan informasi yang sangat kredibel, transparan, prestisius, dan mudah dipahami dalam waktu singkat (timeline seleksi, syarat berkas, program studi, benefit, dan testimoni awardee).
- Memiliki siklus periode pendaftaran aktif (countdown dinamis ke tanggal batas pendaftaran) dan periode seleksi/pengumuman.

## Capabilities and Constraints

- Menggunakan arsitektur Laravel Blade + Tailwind CSS (v4) + Vite.
- Memiliki kontrol dinamis dari database untuk status pendaftaran (`PengaturanPendaftaran::aktif()`, `pendaftaran_selesai`), berita terbitan admin, serta tautan pendaftaran/login.
- Aset visual yang ada: foto awardee autentik (Irfan, Satria, Cellin, Sandy, Kirania, dll.), dokumentasi kegiatan kampus/seminar, dan logo mitra OSIS/komunitas kepemudaan seluruh Indonesia.

## Brand Commitments

- Nama Resmi: MNCU Future Leader Scholarship (MFLS) - Media Nusantara Citra University.
- Warna Identitas Utama: Deep Royal Navy (`#023681` / `#002257`), Bright Gold Accent (`#F5C518` / `#FFD700`), didukung oleh neutral palette yang bersih, berwibawa, dan kontras tinggi.
- Tipografi: Plus Jakarta Sans / modern sans-serif berkarakter kuat dan profesional untuk institusi pendidikan tinggi bertaraf industri, meninggalkan font santai/cursive yang tidak sesuai dengan wibawa beasiswa kepemimpinan nasional.

## Evidence on Hand

- Database riil awardee dan kuotasi testimoni nyata dari angkatan sebelumnya.
- Daftar 8 program studi aktif di 2 fakultas (Fakultas Bisnis dan Keuangan, Fakultas Industri dan Kreatif).
- Timeline resmi tahapan seleksi (Pendaftaran, Mini Project, Ujian CBT/TPA/TBI, Interview BoD, Sit in Class/Company Visit, Pengumuman Awardee).
- Format template dokumen (Personal Statement, Study Plan, Surat Rekomendasi).

## Product Principles

1. **Prestige & Authority**: Tampilan merefleksikan standar universitas modern di bawah naungan grup media terkemuka; berwibawa, inspiratif, dan bebas dari ornamen murahan.
2. **Action-Driven Clarity**: Pengunjung langsung memahami apa itu MFLS, apa benefit nyatanya (beasiswa up to 100%), apa saja persyaratannya, dan bagaimana cara mendaftar dalam hitungan detik.
3. **Flawless Readability & Accessibility**: Kontras tinggi, penataan tipografi yang proporsional tanpa eyebrow/kicker klise, dan adaptasi sempurna di layar ponsel maupun monitor desktop.
4. **Authentic Social Proof**: Mengedepankan wajah dan testimoni nyata awardee serta jaringan kolaborasi nasional untuk membangun kepercayaan penuh calon pendaftar.
