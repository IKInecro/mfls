<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Soal;
use App\Models\JawabanUjian;
use App\Models\Ujian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\GeminiService;

class SoalController extends Controller
{
    /**
     * Get list of available exams.
     */
    public function getUjians(Request $request)
    {
        $user = $request->user();
        $pesertaId = null;
        
        // Get peserta_id if user is logged in as pendaftar
        if ($user && $user->role === 'pendaftar' && $user->peserta) {
            $pesertaId = $user->peserta->id;
        }
        
        // Pilih kolom jadwal/is_active juga — dibutuhkan isOpen() (return JSON
        // tetap dibatasi map() di bawah).
        $ujians = Ujian::select('id', 'nama', 'durasi', 'is_active', 'override_status', 'jadwal_mulai', 'jadwal_selesai')
            ->where('is_active', true)
            ->withCount('soals as jumlah_soal') // Count questions
            ->get()
            ->filter(fn ($ujian) => $ujian->isOpen()) // jadwal/override panel admin
            ->map(function ($ujian) use ($pesertaId) {
                $data = [
                    'id' => $ujian->id,
                    'nama' => $ujian->nama,
                    'durasi' => $ujian->durasi,
                    'jumlah_soal' => $ujian->jumlah_soal,
                    'is_submitted' => false,
                ];
                
                // Check if user has already submitted this exam
                if ($pesertaId) {
                    $hasSubmitted = JawabanUjian::where('ujian_id', $ujian->id)
                        ->where('peserta_id', $pesertaId)
                        ->exists();

                    // Check if there is an active dispensation
                    $hasDispensation = \App\Models\DispensasiUjian::where('ujian_id', $ujian->id)
                        ->where('peserta_id', $pesertaId)
                        ->where('tambahan_menit', '>', 0)
                        ->exists();

                    // If dispensation exists, allow them to re-enter (is_submitted = false)
                    $data['is_submitted'] = $hasSubmitted && !$hasDispensation;
                }
                
                return $data;
            });
        
        return response()->json([
            'status' => 'success',
            'data' => $ujians
        ]);
    }

    /**
     * Get questions for an exam.
     */
    public function index(Request $request)
    {
        $ujianId = $request->query('ujian_id');

        if (!$ujianId) {
            return response()->json([
                'status' => 'error',
                'message' => 'ujian_id is required'
            ], 400);
        }

        $ujian = Ujian::findOrFail($ujianId);

        // Gate jadwal panel admin — peserta yang sesi web-nya sudah jalan tetap
        // boleh ambil soal (resume), selain itu hanya saat ujian terbuka.
        $user = $request->user('sanctum') ?? $request->user();
        $pesertaIdAwal = ($user && $user->role === 'pendaftar' && $user->peserta) ? $user->peserta->id : null;
        $sesiJalan = $pesertaIdAwal
            ? \App\Models\SesiUjian::where('ujian_id', $ujianId)->where('peserta_id', $pesertaIdAwal)->exists()
            : false;
        if (!$ujian->isOpen() && !$sesiJalan) {
            return response()->json(['status' => 'error', 'message' => 'Ujian belum dibuka oleh panitia.'], 403);
        }

        $soals = Soal::where('ujian_id', $ujianId)
            ->orderBy('id', 'asc') // Urutan tetap berdasarkan ID
            ->get(['id', 'ujian_id', 'pertanyaan', 'gambar', 'opsi_a', 'opsi_a_image', 'opsi_b', 'opsi_b_image', 'opsi_c', 'opsi_c_image', 'opsi_d', 'opsi_d_image', 'opsi_e', 'opsi_e_image', 'kategori', 'bobot'])
            ->map(function ($soal) {
                $data = $soal->toArray();
                $baseUrl = request()->getSchemeAndHttpHost() . '/storage/';
                
                $data['gambar'] = $soal->gambar ? $baseUrl . $soal->gambar : null;
                $data['opsi_a_image'] = $soal->opsi_a_image ? $baseUrl . $soal->opsi_a_image : null;
                $data['opsi_b_image'] = $soal->opsi_b_image ? $baseUrl . $soal->opsi_b_image : null;
                $data['opsi_c_image'] = $soal->opsi_c_image ? $baseUrl . $soal->opsi_c_image : null;
                $data['opsi_d_image'] = $soal->opsi_d_image ? $baseUrl . $soal->opsi_d_image : null;
                $data['opsi_e_image'] = $soal->opsi_e_image ? $baseUrl . $soal->opsi_e_image : null;
                return $data;
            });

        $dispensasiMenit = 0;
        $hasSubmittedBefore = false;
        $savedAnswers = null;

        $user = $request->user('sanctum') ?? $request->user();
        if ($user && $user->role === 'pendaftar' && $user->peserta) {
            $pesertaId = $user->peserta->id;
            
            $dispensasi = \App\Models\DispensasiUjian::where('ujian_id', $ujianId)
                ->where('peserta_id', $pesertaId)
                ->first();
            if ($dispensasi) {
                $dispensasiMenit = $dispensasi->tambahan_menit;
            }

            $existingJawaban = \App\Models\JawabanUjian::where('ujian_id', $ujianId)
                ->where('peserta_id', $pesertaId)
                ->first();
            if ($existingJawaban) {
                $hasSubmittedBefore = true;
                $savedAnswers = json_decode($existingJawaban->jawaban, true) ?: new \stdClass();
            }
        }

        return response()->json([
            'status' => 'success',
            'ujian' => [
                'id' => $ujian->id,
                'title' => $ujian->nama,
                'duration' => $ujian->durasi ?? 60,
                'dispensasi_menit' => $dispensasiMenit,
                'has_submitted_before' => $hasSubmittedBefore,
                'saved_answers' => $savedAnswers
            ],
            'data' => $soals
        ]);
    }

    /**
     * Submit answers and get score.
     */
    public function submit(Request $request)
    {
        $request->validate([
            'ujian_id' => 'required|exists:ujian,id',
            'answers' => 'nullable|array', // Structure: { "soal_id": "a", "soal_id_2": "b" }
        ]);

        $user = $request->user();
        if ($user->role !== 'pendaftar') {
            return response()->json(['message' => 'Hanya pendaftar yang bisa mengambil ujian.'], 403);
        }

        $peserta = $user->peserta;
        if (!$peserta) {
            return response()->json(['message' => 'Profil peserta tidak ditemukan.'], 404);
        }

        // Check if already submitted
        $existing = JawabanUjian::where('ujian_id', $request->ujian_id)
            ->where('peserta_id', $peserta->id)
            ->first();

        // Check if there is an active dispensation
        $dispensasi = \App\Models\DispensasiUjian::where('ujian_id', $request->ujian_id)
            ->where('peserta_id', $peserta->id)
            ->first();
        
        if ($existing && (!$dispensasi || $dispensasi->tambahan_menit <= 0)) {
             return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah mengerjakan ujian ini sebelumnya.',
                'score' => $existing->nilai
            ], 400);
        }

        $ujianId = $request->ujian_id;
        $ujian = Ujian::findOrFail($ujianId);
        $submittedAnswers = $request->input('answers', []);

        // Gate jadwal panel admin: yang sesi web-nya sudah jalan boleh finalkan
        // draft kapan pun; yang belum mulai hanya saat ujian terbuka.
        $sesiAda = \App\Models\SesiUjian::where('ujian_id', $ujianId)
            ->where('peserta_id', $peserta->id)
            ->exists();
        if (!$ujian->isOpen() && !$sesiAda) {
            return response()->json(['status' => 'error', 'message' => 'Ujian belum dibuka oleh panitia.'], 403);
        }
        
        // Get all questions for this exam to calculate score
        $soals = Soal::where('ujian_id', $ujianId)->get();
        
        $totalScore = 0;
        $maxScore = (float) $soals->sum('bobot');
        $correctCount = 0;
        $totalCount = $soals->count();

        // Effective max score (fallback ke total jumlah butir jika bobot belum diset/0)
        $effectiveMaxScore = $maxScore > 0 ? $maxScore : ($totalCount > 0 ? (float) $totalCount : 0);

        foreach ($soals as $soal) {
            $userAnswer = $submittedAnswers[$soal->id] ?? null;
            $cleanUserAns = trim(strtolower((string)$userAnswer));
            $cleanKunci = trim(strtolower((string)$soal->kunci_jawaban));

            if ($cleanKunci !== '' && $cleanUserAns !== '' && $cleanUserAns === $cleanKunci) {
                $totalScore += ($maxScore > 0 ? (float)$soal->bobot : 1.0);
                $correctCount++;
            }
        }

        // Calculate final score out of 100 based on weight
        $finalScore = $effectiveMaxScore > 0 ? round(($totalScore / $effectiveMaxScore) * 100, 2) : 0;

        // Handle AI Analysis for Pemetaan Diri
        $kesimpulanAi = null;
        if (str_contains(strtolower($ujian->nama), 'pemetaan diri')) {
            $dimensionScores = [
                'SA' => 0,
                'SC' => 0,
                'SS' => 0,
                'SM' => 0,
                'GM' => 0,
                'LP' => 0,
            ];

            // Hitung jumlah butir per dimensi secara dinamis
            $dimensionCounts = [
                'SA' => 0,
                'SC' => 0,
                'SS' => 0,
                'SM' => 0,
                'GM' => 0,
                'LP' => 0,
            ];
            
            // Map values: A=1, B=2, C=3, D=4, E=5
            $valMap = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5];
            
            foreach ($soals as $soal) {
                $cat = strtolower($soal->kategori ?? '');
                if (str_contains($cat, 'awareness') || str_contains($cat, 'sa')) {
                    $dimensionCounts['SA']++;
                } elseif (str_contains($cat, 'confidence') || str_contains($cat, 'sc')) {
                    $dimensionCounts['SC']++;
                } elseif (str_contains($cat, 'social') || str_contains($cat, 'ss')) {
                    $dimensionCounts['SS']++;
                } elseif (str_contains($cat, 'management') || str_contains($cat, 'sm')) {
                    $dimensionCounts['SM']++;
                } elseif (str_contains($cat, 'growth') || str_contains($cat, 'gm')) {
                    $dimensionCounts['GM']++;
                } elseif (str_contains($cat, 'leadership') || str_contains($cat, 'lp')) {
                    $dimensionCounts['LP']++;
                }

                $userAns = trim(strtolower((string)($submittedAnswers[$soal->id] ?? '')));
                $val = $valMap[$userAns] ?? 0;
                
                if ($val > 0) {
                    if (str_contains($cat, 'awareness') || str_contains($cat, 'sa')) {
                        $dimensionScores['SA'] += $val;
                    } elseif (str_contains($cat, 'confidence') || str_contains($cat, 'sc')) {
                        $dimensionScores['SC'] += $val;
                    } elseif (str_contains($cat, 'social') || str_contains($cat, 'ss')) {
                        $dimensionScores['SS'] += $val;
                    } elseif (str_contains($cat, 'management') || str_contains($cat, 'sm')) {
                        $dimensionScores['SM'] += $val;
                    } elseif (str_contains($cat, 'growth') || str_contains($cat, 'gm')) {
                        $dimensionScores['GM'] += $val;
                    } elseif (str_contains($cat, 'leadership') || str_contains($cat, 'lp')) {
                        $dimensionScores['LP'] += $val;
                    }
                }
            }

            // Hitung persentase tiap dimensi & kategori
            $pct = [];
            $kategoriDimensi = [];
            
            $getCategory = function($percentage) {
                if ($percentage >= 85) {
                    return ['Sangat Tinggi', 'Menunjukkan potensi yang sangat matang dan siap menjadi mentor/pemimpin.'];
                } elseif ($percentage >= 70) {
                    return ['Tinggi', 'Memiliki kompetensi yang kuat di atas rata-rata.'];
                } elseif ($percentage >= 55) {
                    return ['Sedang', 'Kompetensi berkembang namun memerlukan bimbingan berkala.'];
                } elseif ($percentage >= 40) {
                    return ['Rendah', 'Membutuhkan pengembangan intensif pada aspek terkait.'];
                } else {
                    return ['Sangat Rendah', 'Memerlukan pemetaan ulang dan pendampingan khusus.'];
                }
            };

            foreach ($dimensionScores as $key => $score) {
                $maxDim = ($dimensionCounts[$key] > 0 ? $dimensionCounts[$key] : 6) * 5;
                $pct[$key] = round(($score / $maxDim) * 100, 2);
                $kategoriDimensi[$key] = $getCategory($pct[$key])[0];
            }

            // Hitung total skor akhir & persentase
            $totalSkor = array_sum($dimensionScores);
            $totalDimItems = array_sum($dimensionCounts);
            $maxPossibleTotal = $totalDimItems > 0 ? ($totalDimItems * 5) : 180;
            $totalPercentage = round(($totalSkor / $maxPossibleTotal) * 100, 2);

            $finalCategoryInfo = $getCategory($totalPercentage);
            $finalKategori = $finalCategoryInfo[0];
            $finalDeskripsi = $finalCategoryInfo[1];

            // Tentukan profil kepribadian utama secara matematis
            $profilUtama = 'The Balanced Professional';
            $karakteristik = 'Memiliki keseimbangan yang baik di seluruh aspek kompetensi diri, adaptif, dan siap berkembang di berbagai bidang.';
            $prodiRekomendasi = 'Sains Komunikasi, Sistem Informasi, Manajemen';
            $karirRekomendasi = 'General Consultant, Project Coordinator, Entrepreneur';

            if ($pct['LP'] >= 80 && $pct['SS'] >= 70) {
                $profilUtama = 'The Strategic Leader / Visionary Pioneer';
                $karakteristik = 'Sangat kuat dalam memimpin tim, mengambil keputusan taktis di bawah tekanan, memiliki pengaruh positif yang besar, dan komunikatif.';
                $prodiRekomendasi = 'Manajemen, Sains Komunikasi';
                $karirRekomendasi = 'Corporate Executive, Business Development Manager, Public Policy Specialist, Politician';
            } elseif ($pct['GM'] >= 80 && $pct['SM'] >= 70) {
                $profilUtama = 'The Analytical Innovator / High Achiever';
                $karakteristik = 'Sangat adaptif terhadap pembelajaran baru, disiplin tinggi dalam eksekusi tugas, terencana, dan selalu mencari peningkatan kualitas.';
                $prodiRekomendasi = 'Sistem Informasi, Sains Komunikasi (Digital)';
                $karirRekomendasi = 'IT Analyst, Data Scientist, Researcher, Product Manager';
            } elseif ($pct['SS'] >= 80 && $pct['SC'] >= 70) {
                $profilUtama = 'The Collaborative Diplomat / PR Specialist';
                $karakteristik = 'Sangat terampil dalam membangun relasi interpersonal, percaya diri tinggi dalam bersosialisasi dan berbicara di depan umum, serta ulung menyelesaikan konflik.';
                $prodiRekomendasi = 'Sains Komunikasi';
                $karirRekomendasi = 'Public Relations Manager, Corporate Communications, HR Specialist, Diplomat';
            } elseif ($pct['SM'] >= 80 && $pct['SA'] >= 70) {
                $profilUtama = 'The Precision Strategist / Operations Director';
                $karakteristik = 'Sangat teratur, memiliki ketahanan emosi yang prima saat tertekan, bertanggung jawab penuh, dan sadar betul akan kelebihan serta kelemahan diri.';
                $prodiRekomendasi = 'Manajemen, Sistem Informasi';
                $karirRekomendasi = 'Operations Manager, Chief Financial Officer, Strategic Planner, Risk Analyst';
            } else {
                // Cari skor tertinggi
                arsort($pct);
                $highestKey = key($pct);
                
                switch ($highestKey) {
                    case 'SA':
                        $profilUtama = 'The Self-Reflective Explorer';
                        $karakteristik = 'Memiliki kesadaran diri yang sangat tinggi, sangat memahami kelebihan, kelemahan, serta motivasi internal yang mendorong kesuksesan.';
                        $prodiRekomendasi = 'Sains Komunikasi, Manajemen (SDM)';
                        $karirRekomendasi = 'Human Resource Analyst, Counselor, Creative Writer, Researcher';
                        break;
                    case 'SC':
                        $profilUtama = 'The Bold Entrepreneur';
                        $karakteristik = 'Memiliki kepercayaan diri yang luar biasa, berani mengambil risiko, tidak takut gagal, dan sangat mandiri dalam mengambil keputusan penting.';
                        $prodiRekomendasi = 'Manajemen (Bisnis/Pemasaran)';
                        $karirRekomendasi = 'Startup Founder, Venture Builder, Marketing Director, Investment Analyst';
                        break;
                    case 'SS':
                        $profilUtama = 'The Community Connector';
                        $karakteristik = 'Memiliki kecerdasan sosial yang tinggi, pandai berkolaborasi, pendengar yang baik, serta sangat menghargai keberagaman pendapat.';
                        $prodiRekomendasi = 'Sains Komunikasi';
                        $karirRekomendasi = 'Social Media Manager, PR Consultant, Community Director, Customer Success Specialist';
                        break;
                    case 'SM':
                        $profilUtama = 'The Operations Master';
                        $karakteristik = 'Sangat disiplin, andal dalam manajemen waktu, memiliki ketahanan mental yang tinggi, dan memiliki perencanaan hidup yang sangat matang.';
                        $prodiRekomendasi = 'Sistem Informasi, Manajemen';
                        $karirRekomendasi = 'Project Manager, Operations Specialist, Database Administrator, Auditor';
                        break;
                    case 'GM':
                        $profilUtama = 'The Lifelong Learner';
                        $karakteristik = 'Memiliki growth mindset yang luar biasa, terbuka terhadap kritik, menikmati proses belajar, dan menganggap kesalahan sebagai batu loncatan.';
                        $prodiRekomendasi = 'Sistem Informasi, Sains Komunikasi';
                        $karirRekomendasi = 'Software Developer, UX Researcher, Business Consultant, Educator';
                        break;
                    case 'LP':
                        $profilUtama = 'The Dynamic Executive';
                        $karakteristik = 'Memiliki bakat kepemimpinan alami yang sangat baik, berani berinisiatif, dan siap menjadi role model bagi rekan sebayanya.';
                        $prodiRekomendasi = 'Manajemen';
                        $karirRekomendasi = 'Management Trainee, CEO Office Associate, Operations Lead, Community Leader';
                        break;
                }
            }

            // Hitung narasi secara lokal di PHP tanpa menggunakan API Gemini
            $narasiAi = JawabanUjian::generateLocalNarrative($pct, $profilUtama, $prodiRekomendasi);

            // Susun report final berformat Markdown cantik
            $kesimpulanAi = "📊 **HASIL ANALISIS INSTRUMEN PEMETAAN DIRI**\n";
            $kesimpulanAi .= "--------------------------------------------------\n";
            $kesimpulanAi .= "👤 **Nama Peserta:** " . ($user->nama ?? 'Peserta') . "\n";
            $kesimpulanAi .= "📅 **Tanggal Ujian:** " . now()->translatedFormat('d F Y') . "\n\n";
            
            $kesimpulanAi .= "📈 **PENCAPAIAN SKOR DIMENSI:**\n";
            $kesimpulanAi .= "1. **Self Awareness (SA):** " . $dimensionScores['SA'] . "/" . (($dimensionCounts['SA'] > 0 ? $dimensionCounts['SA'] : 6) * 5) . " (" . $pct['SA'] . "%) - " . $kategoriDimensi['SA'] . "\n";
            $kesimpulanAi .= "2. **Self Confidence (SC):** " . $dimensionScores['SC'] . "/" . (($dimensionCounts['SC'] > 0 ? $dimensionCounts['SC'] : 6) * 5) . " (" . $pct['SC'] . "%) - " . $kategoriDimensi['SC'] . "\n";
            $kesimpulanAi .= "3. **Social Skill (SS):** " . $dimensionScores['SS'] . "/" . (($dimensionCounts['SS'] > 0 ? $dimensionCounts['SS'] : 6) * 5) . " (" . $pct['SS'] . "%) - " . $kategoriDimensi['SS'] . "\n";
            $kesimpulanAi .= "4. **Self Management (SM):** " . $dimensionScores['SM'] . "/" . (($dimensionCounts['SM'] > 0 ? $dimensionCounts['SM'] : 6) * 5) . " (" . $pct['SM'] . "%) - " . $kategoriDimensi['SM'] . "\n";
            $kesimpulanAi .= "5. **Growth Mindset (GM):** " . $dimensionScores['GM'] . "/" . (($dimensionCounts['GM'] > 0 ? $dimensionCounts['GM'] : 6) * 5) . " (" . $pct['GM'] . "%) - " . $kategoriDimensi['GM'] . "\n";
            $kesimpulanAi .= "6. **Leadership Potential (LP):** " . $dimensionScores['LP'] . "/" . (($dimensionCounts['LP'] > 0 ? $dimensionCounts['LP'] : 6) * 5) . " (" . $pct['LP'] . "%) - " . $kategoriDimensi['LP'] . "\n\n";
            
            $kesimpulanAi .= "🎯 **SKOR KESELURUHAN & KESIAPAN:**\n";
            $kesimpulanAi .= "* **Total Skor:** " . $totalSkor . "/{$maxPossibleTotal}\n";
            $kesimpulanAi .= "* **Persentase Akhir:** " . $totalPercentage . "%\n";
            $kesimpulanAi .= "* **Kategori Kesiapan:** **" . $finalKategori . "**\n";
            $kesimpulanAi .= "* *Deskripsi:* " . $finalDeskripsi . "\n\n";
            
            $kesimpulanAi .= "🧠 **PROFIL KEPRIBADIAN (DOMINAN):**\n";
            $kesimpulanAi .= "* **Profil Utama:** **" . $profilUtama . "**\n";
            $kesimpulanAi .= "* **Karakteristik Kunci:** " . $karakteristik . "\n\n";
            
            $kesimpulanAi .= "🎓 **REKOMENDASI PROGRAM STUDI & KARIR:**\n";
            $kesimpulanAi .= "* **Program Studi Sangat Cocok (MNC University):** **" . $prodiRekomendasi . "**\n";
            $kesimpulanAi .= "* **Rekomendasi Karir Masa Depan:** **" . $karirRekomendasi . "**\n\n";
            
            $kesimpulanAi .= "--------------------------------------------------\n";
            $kesimpulanAi .= "🤖 **INTERPRETASI PSIKOLOGIS (ARION AI):**\n";
            $kesimpulanAi .= $narasiAi;

            // Untuk Pemetaan Diri, skor akhir adalah persentase kesiapan keseluruhan
            $finalScore = $totalPercentage;
            $totalScore = $totalSkor;
        }

        // Save or update jawaban_ujian (Detailed JSON)
        if ($existing) {
            $existing->update([
                'jawaban' => json_encode($submittedAnswers),
                'nilai' => $finalScore,
                'kesimpulan_ai' => $kesimpulanAi
            ]);
            $result = $existing;

            // Update nilai_ujian (Official Score Summary)
            \App\Models\NilaiUjian::updateOrCreate(
                ['ujian_id' => $ujianId, 'peserta_id' => $peserta->id],
                [
                    'skor_asli' => $totalScore,
                    'skor_rata' => $finalScore
                ]
            );
        } else {
            $result = JawabanUjian::create([
                'ujian_id' => $ujianId,
                'peserta_id' => $peserta->id,
                'jawaban' => json_encode($submittedAnswers),
                'nilai' => $finalScore, // Use final scale of 0-100
                'kesimpulan_ai' => $kesimpulanAi
            ]);

            // Save to nilai_ujian (Official Score Summary)
            \App\Models\NilaiUjian::updateOrCreate(
                ['ujian_id' => $ujianId, 'peserta_id' => $peserta->id],
                [
                    'skor_asli' => $totalScore,
                    'skor_rata' => $finalScore
                ]
            );
        }

        // Clean up dispensation since it is now used
        if ($dispensasi) {
            $dispensasi->delete();
        }

        // Sesi pengerjaan web selesai → hapus (jawaban final sudah tersimpan)
        \App\Models\SesiUjian::where('ujian_id', $ujianId)
            ->where('peserta_id', $peserta->id)
            ->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Jawaban berhasil disimpan.',
            'results' => [
                'earned_raw_score' => $totalScore,
                'max_raw_score' => $maxScore,
                'score' => $finalScore,
                'correct_answers' => $correctCount,
                'total_questions' => $totalCount,
            ]
        ]);
    }
}
