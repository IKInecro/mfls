@extends('layouts.admin')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h2 class="text-xl sm:text-2xl font-black text-gray-800">
            {{ auth()->user()->role === 'dosen' ? 'Evaluasi Wawancara (Dosen)' : ($type === 'mentor' ? 'Evaluasi Peserta (Mentor)' : 'Evaluasi Peserta (Akademik)') }}
        </h2>
        <p class="text-gray-500 text-sm">
            {{ auth()->user()->role === 'dosen' ? 'Masukkan rubrik penilaian wawancara dan rekomendasi kelulusan.' : 'Update penilaian kualitatif untuk setiap peserta.' }}
        </p>
    </div>
    <a href="{{ $type === 'mentor' ? route('admin.penilaian.index') : route('admin.penilaian.akademik.index') }}" class="inline-flex self-start sm:self-auto items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-2xl text-xs font-black shadow-sm hover:bg-slate-50 transition-all uppercase tracking-widest">
        <span class="iconify" data-icon="solar:arrow-left-bold"></span> Kembali
    </a>
</div>

<div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- Main Form Column -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Profile Card -->
            <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-950 p-8 rounded-[2.5rem] text-white shadow-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 opacity-5">
                    <span class="iconify text-9xl" data-icon="solar:user-bold"></span>
                </div>
                
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 relative z-10">
                    <div class="w-20 h-20 bg-white/10 backdrop-blur-md rounded-3xl flex items-center justify-center text-4xl font-black text-white shadow-inner">
                        {{ substr($user->nama, 0, 1) }}
                    </div>
                    <div class="min-w-0 flex-grow">
                        <span class="px-3 py-1 bg-orange-500/20 text-orange-400 rounded-full text-[10px] font-black uppercase tracking-widest border border-orange-500/30">Peserta Wawancara</span>
                        <h3 class="text-2xl font-black mt-2 leading-tight">{{ $user->nama }}</h3>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mt-1">{{ $user->peserta->nama_sekolah ?? '-' }} | NISN: {{ $user->peserta->nisn ?? '-' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-8 pt-6 border-t border-white/10 text-xs font-semibold text-slate-300">
                    <div>
                        <span class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Kabupaten/Kota</span>
                        <span class="text-white font-bold text-sm">{{ $user->peserta->kabupaten ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Ruang Wawancara</span>
                        <span class="text-white font-bold text-sm flex items-center gap-1.5">
                            <span class="w-2 h-2 bg-emerald-400 rounded-full"></span>
                            {{ $user->peserta->ruangan ?? 'Belum Ditentukan' }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Dosen Pewawancara</span>
                        <span class="text-white font-bold text-sm">
                            {{ $user->peserta->interviewer->nama ?? 'Belum Ditentukan' }}
                        </span>
                    </div>
                </div>
            </div>
            <!-- Form Input (Mentor) -->
            @if($type === 'mentor' && (auth()->user()->role === 'mentor' || auth()->user()->role === 'admin'))
            <div class="bg-white p-5 sm:p-10 rounded-[2.5rem] border {{ auth()->user()->role === 'mentor' ? 'border-blue-100 shadow-xl shadow-blue-500/5' : 'border-gray-100' }} shadow-sm">
                <h3 class="text-xl font-black text-slate-800 mb-8 flex items-center gap-3">
                    <span class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-lg">
                        <span class="iconify" data-icon="solar:pen-new-square-bold"></span>
                    </span>
                    Form Penilaian Mentor
                </h3>
                
                <form action="{{ route('admin.pendaftar.mentor_nilai', $user->id) }}" method="POST" class="space-y-8">
                    @csrf
                    @php
                        $myMentorEval = $user->peserta->penilaianMentors->where('mentor_id', auth()->id())->first();
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kepemimpinan (35%)</label>
                            <input type="number" name="nilai_kepemimpinan" min="0" max="100" required 
                                value="{{ $myMentorEval->nilai_kepemimpinan ?? '' }}"
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:border-blue-600 focus:ring-4 focus:ring-blue-100 outline-none font-black text-xl text-blue-600 transition-all" placeholder="0">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kepribadian (35%)</label>
                            <input type="number" name="nilai_kepribadian" min="0" max="100" required 
                                value="{{ $myMentorEval->nilai_kepribadian ?? '' }}"
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:border-blue-600 focus:ring-4 focus:ring-blue-100 outline-none font-black text-xl text-blue-600 transition-all" placeholder="0">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Keaktifan (30%)</label>
                            <input type="number" name="nilai_keaktifan" min="0" max="100" required 
                                value="{{ $myMentorEval->nilai_keaktifan ?? '' }}"
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:border-blue-600 focus:ring-4 focus:ring-blue-100 outline-none font-black text-xl text-blue-600 transition-all" placeholder="0">
                        </div>
                    </div>
                    
                    @if($myMentorEval)
                    <div class="p-6 bg-blue-50 border border-blue-100 rounded-[1.5rem] flex justify-between items-center shadow-inner">
                        <span class="text-xs font-black text-blue-700 uppercase tracking-widest">Total Nilai Berbobot</span>
                        <span class="text-4xl font-black text-blue-700">{{ number_format($myMentorEval->nilai, 2) }}</span>
                    </div>
                    @endif

                    <div class="space-y-4">
                        <label class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">
                            <span class="iconify" data-icon="solar:notes-bold"></span> Catatan Kualitatif
                        </label>
                        <textarea name="catatan" rows="5" placeholder="Berikan alasan penilaian atau catatan perkembangan..."
                            class="w-full px-8 py-6 bg-slate-50 border border-slate-200 rounded-[2rem] focus:border-blue-600 focus:ring-4 focus:ring-blue-100 outline-none font-semibold text-slate-700 shadow-inner transition-all">{{ $myMentorEval->catatan ?? '' }}</textarea>
                    </div>
                    
                    <button type="submit" class="group w-full py-6 bg-blue-600 text-white font-black rounded-[2rem] shadow-2xl shadow-blue-200 hover:bg-blue-700 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                        <span class="text-sm uppercase tracking-[0.2em]">Simpan Penilaian Mentor</span>
                        <span class="iconify text-xl group-hover:translate-x-1 transition-transform" data-icon="solar:arrow-right-bold"></span>
                    </button>
                </form>
            </div>
            @endif

            <!-- Form Input (Dosen Wawancara) -->
            @if(auth()->user()->role === 'dosen')
            <div class="bg-white p-5 sm:p-10 rounded-[2.5rem] border border-orange-100 shadow-xl shadow-orange-500/5 animate-fade-in">
                <div class="flex items-center gap-4 mb-10 pb-6 border-b border-gray-100">
                    <div class="w-14 h-14 bg-gradient-to-br from-orange-400 to-orange-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-orange-200">
                        <span class="iconify text-2xl" data-icon="solar:medal-ribbon-bold"></span>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-800">Form Penilaian Wawancara</h3>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Rubrik Penilaian Wawancara Dosen</p>
                    </div>
                </div>
                
                <form action="{{ route('admin.penilaian.akademik.store', $user->id) }}" method="POST" class="space-y-10">
                    @csrf
                    @php
                        $myDosenEval = $user->peserta->penilaianAkademiks->where('penilai_id', auth()->id())->first();
                    @endphp

                    @if($errors->any())
                    <div class="p-5 bg-red-50 border border-red-200 rounded-2xl">
                        <h4 class="text-xs font-black text-red-600 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <span class="iconify" data-icon="solar:danger-triangle-bold"></span>
                            Terdapat {{ $errors->count() }} kesalahan yang harus diperbaiki:
                        </h4>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li class="text-[11px] font-bold text-red-700">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <!-- 1. Motivasi & Komitmen -->
                    <div class="p-6 sm:p-8 bg-slate-50/50 rounded-[2rem] border border-slate-100 relative overflow-hidden transition-all hover:border-orange-200 shadow-sm">
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider flex items-center justify-between mb-4">
                            <span>1. Motivasi & Komitmen (Bobot 25%)</span>
                            <span class="text-xs font-black text-orange-600 bg-orange-50 px-3 py-1 rounded-full border border-orange-100">25%</span>
                        </h4>
                        <p class="text-xs font-semibold text-slate-400 mb-4">Mengukur dorongan diri calon mahasiswa, kesungguhan, komitmen, dan ketahanan dalam menyelesaikan studi.</p>
                        
                        <!-- Hal yang Digali & Panduan Pertanyaan Banner -->
                        <div class="mb-6 bg-orange-50/40 border border-orange-100/50 rounded-2xl px-5 py-4 space-y-3">
                            <div class="flex items-start gap-3">
                                <span class="iconify text-orange-600 mt-0.5 text-base shrink-0" data-icon="solar:info-square-bold-duotone"></span>
                                <div>
                                    <span class="block text-[8px] font-black text-orange-800 uppercase tracking-widest mb-0.5">Hal yang Digali:</span>
                                    <p class="text-[10px] font-bold text-slate-600 leading-relaxed">Ketulusan motivasi, kesadaran diri, relevansi dengan program.</p>
                                </div>
                            </div>
                            <div class="pt-3 border-t border-orange-100/30">
                                <span class="block text-[8px] font-black text-orange-800 uppercase tracking-widest mb-2">Panduan Pertanyaan Wawancara (Deskripsi):</span>
                                <ul class="list-decimal list-inside space-y-1.5 ml-1">
                                    <li class="text-[10px] font-bold text-slate-600 leading-relaxed">Apa yang mendorong Anda melamar MNCU Future Leader Scholarship, dan mengapa Anda merasa layak mendapatkannya?</li>
                                    <li class="text-[10px] font-bold text-slate-600 leading-relaxed">Bagaimana beasiswa ini akan berkontribusi pada tujuan jangka panjang Anda?</li>
                                    <li class="text-[10px] font-bold text-slate-600 leading-relaxed">Tantangan terbesar apa yang pernah Anda hadapi dalam perjalanan akademik Anda, dan bagaimana Anda mengatasinya?</li>
                                    <li class="text-[10px] font-bold text-slate-600 leading-relaxed">Apa komitmen dan kontribusi apa yang akan Anda berikan untuk MNC University / MNCU Future Leader Scholarship?</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Single Rating Field -->
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-3">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Skor Penilaian Motivasi & Komitmen</label>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                @foreach([1 => 'SK', 2 => 'K', 3 => 'C', 4 => 'B', 5 => 'SB'] as $score => $label)
                                    <label class="cursor-pointer text-center flex-grow">
                                        <input type="radio" name="wawancara_motivasi" value="{{ $score }}" class="sr-only peer calc-input" required
                                            {{ ($myDosenEval->wawancara_motivasi ?? '') == $score ? 'checked' : '' }}>
                                        <div class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-[10px] font-black text-slate-600 transition-all peer-checked:bg-orange-600 peer-checked:text-white peer-checked:border-orange-600 hover:bg-slate-50 shadow-sm">
                                            <span>{{ $score }} <span class="text-[8px] opacity-75 font-semibold">({{ $label }})</span></span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- 2. Prestasi Akademik -->
                    <div class="p-6 sm:p-8 bg-slate-50/50 rounded-[2rem] border border-slate-100 relative overflow-hidden transition-all hover:border-orange-200 shadow-sm">
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider flex items-center justify-between mb-4">
                            <span>2. Prestasi Akademik (Bobot 20%)</span>
                            <span class="text-xs font-black text-orange-600 bg-orange-50 px-3 py-1 rounded-full border border-orange-100">20%</span>
                        </h4>
                        <p class="text-xs font-semibold text-slate-400 mb-4">Mengukur rekam jejak prestasi, pemahaman akademik dasar, serta potensi pengembangan keilmuan calon mahasiswa.</p>
                        
                        <!-- Hal yang Digali & Panduan Pertanyaan Banner -->
                        <div class="mb-6 bg-orange-50/40 border border-orange-100/50 rounded-2xl px-5 py-4 space-y-3">
                            <div class="flex items-start gap-3">
                                <span class="iconify text-orange-600 mt-0.5 text-base shrink-0" data-icon="solar:info-square-bold-duotone"></span>
                                <div>
                                    <span class="block text-[8px] font-black text-orange-800 uppercase tracking-widest mb-0.5">Hal yang Digali:</span>
                                    <p class="text-[10px] font-bold text-slate-600 leading-relaxed">Kualitas pencapaian, kerja keras, konsistensi belajar, pemahaman mendalam, minat keilmuan, sikap terhadap kegagalan, strategi perbaikan, pengalaman riset, kemampuan analitis.</p>
                                </div>
                            </div>
                            <div class="pt-3 border-t border-orange-100/30">
                                <span class="block text-[8px] font-black text-orange-800 uppercase tracking-widest mb-2">Panduan Pertanyaan Wawancara (Deskripsi):</span>
                                <ul class="list-decimal list-inside space-y-1.5 ml-1">
                                    <li class="text-[10px] font-bold text-slate-600 leading-relaxed">Ceritakan pencapaian akademik yang paling membanggakan Anda dan proses di baliknya.</li>
                                    <li class="text-[10px] font-bold text-slate-600 leading-relaxed">Mata Pelajaran atau bidang studi mana yang paling Anda kuasai dan mengapa?</li>
                                    <li class="text-[10px] font-bold text-slate-600 leading-relaxed">Bagaimana Anda menyikapi mata pelajaran yang nilainya kurang memuaskan?</li>
                                    <li class="text-[10px] font-bold text-slate-600 leading-relaxed">Apakah Anda pernah terlibat dalam penelitian, karya tulis, atau proyek akademik? Jelaskan kontribusi Anda.</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Single Rating Field -->
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-3">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Skor Penilaian Prestasi Akademik</label>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                @foreach([1 => 'SK', 2 => 'K', 3 => 'C', 4 => 'B', 5 => 'SB'] as $score => $label)
                                    <label class="cursor-pointer text-center flex-grow">
                                        <input type="radio" name="wawancara_prestasi" value="{{ $score }}" class="sr-only peer calc-input" required
                                            {{ ($myDosenEval->wawancara_prestasi ?? '') == $score ? 'checked' : '' }}>
                                        <div class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-[10px] font-black text-slate-600 transition-all peer-checked:bg-orange-600 peer-checked:text-white peer-checked:border-orange-600 hover:bg-slate-50 shadow-sm">
                                            <span>{{ $score }} <span class="text-[8px] opacity-75 font-semibold">({{ $label }})</span></span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- 3. Karakter & Integritas -->
                    <div class="p-6 sm:p-8 bg-slate-50/50 rounded-[2rem] border border-slate-100 relative overflow-hidden transition-all hover:border-orange-200 shadow-sm">
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider flex items-center justify-between mb-4">
                            <span>3. Karakter & Integritas (Bobot 20%)</span>
                            <span class="text-xs font-black text-orange-600 bg-orange-50 px-3 py-1 rounded-full border border-orange-100">20%</span>
                        </h4>
                        <p class="text-xs font-semibold text-slate-400 mb-4">Mengukur kejujuran, nilai-nilai moral, sopan santun, etika komunikasi, dan kepribadian calon mahasiswa.</p>
                        
                        <!-- Hal yang Digali & Panduan Pertanyaan Banner -->
                        <div class="mb-6 bg-orange-50/40 border border-orange-100/50 rounded-2xl px-5 py-4 space-y-3">
                            <div class="flex items-start gap-3">
                                <span class="iconify text-orange-600 mt-0.5 text-base shrink-0" data-icon="solar:info-square-bold-duotone"></span>
                                <div>
                                    <span class="block text-[8px] font-black text-orange-800 uppercase tracking-widest mb-0.5">Hal yang Digali:</span>
                                    <p class="text-[10px] font-bold text-slate-600 leading-relaxed">Kejujuran, konsistensi nilai, pengambilan keputusan etis, keteguhan karakter, integritas di bawah tekanan, empati, tanggung jawab sosial, sistem nilai, konsistensi antara prinsip dan perilaku.</p>
                                </div>
                            </div>
                            <div class="pt-3 border-t border-orange-100/30">
                                <span class="block text-[8px] font-black text-orange-800 uppercase tracking-widest mb-2">Panduan Pertanyaan Wawancara (Deskripsi):</span>
                                <ul class="list-decimal list-inside space-y-1.5 ml-1">
                                    <li class="text-[10px] font-bold text-slate-600 leading-relaxed">Ceritakan situasi di mana Anda harus membuat keputusan sulit yang melibatkan nilai-nilai moral atau etika.</li>
                                    <li class="text-[10px] font-bold text-slate-600 leading-relaxed">Pernahkah Anda menghadapi tekanan untuk melakukan sesuatu yang bertentangan dengan prinsip Anda? Bagaimana respons Anda?</li>
                                    <li class="text-[10px] font-bold text-slate-600 leading-relaxed">Bagaimana Anda membangun kepercayaan dalam hubungan dengan teman, guru, atau anggota organisasi?</li>
                                    <li class="text-[10px] font-bold text-slate-600 leading-relaxed">Apa nilai hidup yang paling penting bagi Anda dan bagaimana nilai itu tercermin dalam keseharian Anda?</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Single Rating Field -->
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-3">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Skor Penilaian Karakter & Integritas</label>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                @foreach([1 => 'SK', 2 => 'K', 3 => 'C', 4 => 'B', 5 => 'SB'] as $score => $label)
                                    <label class="cursor-pointer text-center flex-grow">
                                        <input type="radio" name="wawancara_karakter" value="{{ $score }}" class="sr-only peer calc-input" required
                                            {{ ($myDosenEval->wawancara_karakter ?? '') == $score ? 'checked' : '' }}>
                                        <div class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-[10px] font-black text-slate-600 transition-all peer-checked:bg-orange-600 peer-checked:text-white peer-checked:border-orange-600 hover:bg-slate-50 shadow-sm">
                                            <span>{{ $score }} <span class="text-[8px] opacity-75 font-semibold">({{ $label }})</span></span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- 4. Kontribusi & Kepemimpinan -->
                    <div class="p-6 sm:p-8 bg-slate-50/50 rounded-[2rem] border border-slate-100 relative overflow-hidden transition-all hover:border-orange-200 shadow-sm">
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider flex items-center justify-between mb-4">
                            <span>4. Kontribusi & Kepemimpinan (Bobot 20%)</span>
                            <span class="text-xs font-black text-orange-600 bg-orange-50 px-3 py-1 rounded-full border border-orange-100">20%</span>
                        </h4>
                        <p class="text-xs font-semibold text-slate-400 mb-4">Mengukur keaktifan berorganisasi, jiwa kepemimpinan, kerja tim, serta rencana kontribusi bagi masyarakat.</p>
                        
                        <!-- Hal yang Digali & Panduan Pertanyaan Banner -->
                        <div class="mb-6 bg-orange-50/40 border border-orange-100/50 rounded-2xl px-5 py-4 space-y-3">
                            <div class="flex items-start gap-3">
                                <span class="iconify text-orange-600 mt-0.5 text-base shrink-0" data-icon="solar:info-square-bold-duotone"></span>
                                <div>
                                    <span class="block text-[8px] font-black text-orange-800 uppercase tracking-widest mb-0.5">Hal yang Digali:</span>
                                    <p class="text-[10px] font-bold text-slate-600 leading-relaxed">Gaya kepemimpinan, kemampuan manajemen konflik, dampak sosial, inisiatif, kepedulian, kecerdasan emosional, keterampilan interpersonal, visi kepemimpinan ke depan, rasa tanggung jawab.</p>
                                </div>
                            </div>
                            <div class="pt-3 border-t border-orange-100/30">
                                <span class="block text-[8px] font-black text-orange-800 uppercase tracking-widest mb-2">Panduan Pertanyaan Wawancara (Deskripsi):</span>
                                <ul class="list-decimal list-inside space-y-1.5 ml-1">
                                    <li class="text-[10px] font-bold text-slate-600 leading-relaxed">Ceritakan pengalaman Anda memimpin suatu tim atau organisasi. Apa tantangan terbesar dan bagaimana Anda mengatasinya?</li>
                                    <li class="text-[10px] font-bold text-slate-600 leading-relaxed">Kontribusi nyata apa yang pernah Anda berikan kepada organisasi/komunitas atau lingkungan sekitar Anda?</li>
                                    <li class="text-[10px] font-bold text-slate-600 leading-relaxed">Bagaimana Anda memotivasi anggota tim yang kurang bersemangat atau berkonflik satu sama lain?</li>
                                    <li class="text-[10px] font-bold text-slate-600 leading-relaxed">Jika mendapat beasiswa ini, kontribusi apa yang ingin Anda berikan bagi almamater atau masyarakat?</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Single Rating Field -->
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-3">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Skor Penilaian Kontribusi & Kepemimpinan</label>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                @foreach([1 => 'SK', 2 => 'K', 3 => 'C', 4 => 'B', 5 => 'SB'] as $score => $label)
                                    <label class="cursor-pointer text-center flex-grow">
                                        <input type="radio" name="wawancara_kontribusi" value="{{ $score }}" class="sr-only peer calc-input" required
                                            {{ ($myDosenEval->wawancara_kontribusi ?? '') == $score ? 'checked' : '' }}>
                                        <div class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-[10px] font-black text-slate-600 transition-all peer-checked:bg-orange-600 peer-checked:text-white peer-checked:border-orange-600 hover:bg-slate-50 shadow-sm">
                                            <span>{{ $score }} <span class="text-[8px] opacity-75 font-semibold">({{ $label }})</span></span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- 5. Kemampuan Komunikasi -->
                    <div class="p-6 sm:p-8 bg-slate-50/50 rounded-[2rem] border border-slate-100 relative overflow-hidden transition-all hover:border-orange-200 shadow-sm">
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider flex items-center justify-between mb-4">
                            <span>5. Kemampuan Komunikasi (Bobot 15%)</span>
                            <span class="text-xs font-black text-orange-600 bg-orange-50 px-3 py-1 rounded-full border border-orange-100">15%</span>
                        </h4>
                        <p class="text-xs font-semibold text-slate-400 mb-4">Mengukur kejelasan penyampaian pendapat, pengucapan kata, kepercayaan diri, dan kelancaran bertutur kata.</p>
                        
                        <!-- Hal yang Digali & Panduan Pertanyaan Banner -->
                        <div class="mb-6 bg-orange-50/40 border border-orange-100/50 rounded-2xl px-5 py-4 space-y-3">
                            <div class="flex items-start gap-3">
                                <span class="iconify text-orange-600 mt-0.5 text-base shrink-0" data-icon="solar:info-square-bold-duotone"></span>
                                <div>
                                    <span class="block text-[8px] font-black text-orange-800 uppercase tracking-widest mb-0.5">Hal yang Digali:</span>
                                    <p class="text-[10px] font-bold text-slate-600 leading-relaxed">Kejelasan, struktur komunikasi, kepercayaan diri, asertivitas, diplomasi, kemampuan persuasi, keterampilan public speaking, ketenangan di bawah sorotan, active listening, kemampuan adaptasi komunikasi.</p>
                                </div>
                            </div>
                            <div class="pt-3 border-t border-orange-100/30">
                                <span class="block text-[8px] font-black text-orange-800 uppercase tracking-widest mb-2">Panduan Pertanyaan Wawancara (Deskripsi):</span>
                                <ul class="list-decimal list-inside space-y-1.5 ml-1">
                                    <li class="text-[10px] font-bold text-slate-600 leading-relaxed">Jelaskan secara singkat tentang diri Anda dalam 60 detik kepada orang yang baru Anda kenal.</li>
                                    <li class="text-[10px] font-bold text-slate-600 leading-relaxed">Bagaimana cara Anda menyampaikan ide atau pendapat yang berbeda dari mayoritas kelompok?</li>
                                    <li class="text-[10px] font-bold text-slate-600 leading-relaxed">Ceritakan pengalaman Anda melakukan presentasi atau berbicara di depan umum.</li>
                                    <li class="text-[10px] font-bold text-slate-600 leading-relaxed">Bagaimana Anda memastikan pesan yang Anda sampaikan dipahami dengan baik oleh lawan bicara?</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Single Rating Field -->
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-3">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Skor Penilaian Kemampuan Komunikasi</label>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                @foreach([1 => 'SK', 2 => 'K', 3 => 'C', 4 => 'B', 5 => 'SB'] as $score => $label)
                                    <label class="cursor-pointer text-center flex-grow">
                                        <input type="radio" name="wawancara_komunikasi" value="{{ $score }}" class="sr-only peer calc-input" required
                                            {{ ($myDosenEval->wawancara_komunikasi ?? '') == $score ? 'checked' : '' }}>
                                        <div class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-[10px] font-black text-slate-600 transition-all peer-checked:bg-orange-600 peer-checked:text-white peer-checked:border-orange-600 hover:bg-slate-50 shadow-sm">
                                            <span>{{ $score }} <span class="text-[8px] opacity-75 font-semibold">({{ $label }})</span></span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Live Calculation & Recommendations -->
                    <div class="p-6 sm:p-8 bg-slate-900 text-white rounded-[2rem] border border-slate-800 shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
                        <div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">TOTAL SKOR AKHIR (Weighted)</span>
                            <span class="text-5xl font-black text-yellow-400" id="live-total">0.00</span>
                            <span class="text-slate-400 text-xs font-bold block mt-1">Formula: ((Motivasi x 25%) + (Prestasi x 20%) + (Karakter x 20%) + (Kontribusi x 20%) + (Komunikasi x 15%)) x 20 (Skala 100)</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="iconify text-6xl text-yellow-400/20" data-icon="solar:star-ring-bold-duotone"></span>
                        </div>
                    </div>

                    <!-- Recommendations Section -->
                    <div class="p-6 sm:p-8 bg-orange-50/30 rounded-[2rem] border border-orange-100/50 space-y-6">
                        <h4 class="text-sm font-black text-orange-600 uppercase tracking-[0.2em] flex items-center gap-3 mb-6">
                            <span class="w-3 h-3 bg-orange-500 rounded-full shadow-sm"></span>
                            Rekomendasi Hasil Akhir
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Rekomendasi Kelulusan</label>
                                <select name="rekomendasi_akhir" required class="w-full px-5 py-4 bg-white border border-orange-100 rounded-2xl focus:border-orange-500 focus:ring-4 focus:ring-orange-100 outline-none font-bold text-slate-700 transition-all shadow-sm">
                                    <option value="" disabled selected>Pilih Rekomendasi</option>
                                    @foreach(['Sangat Direkomendasikan', 'Direkomendasikan', 'Direkomendasikan dengan Catatan', 'Tidak Direkomendasikan'] as $option)
                                        <option value="{{ $option }}" {{ ($myDosenEval->rekomendasi_akhir ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Rekomendasi Skema Beasiswa</label>
                                <select name="rekomendasi_beasiswa" required class="w-full px-5 py-4 bg-white border border-orange-100 rounded-2xl focus:border-orange-500 focus:ring-4 focus:ring-orange-100 outline-none font-bold text-slate-700 transition-all shadow-sm">
                                    <option value="" disabled selected>Pilih Skema</option>
                                    @foreach(['Beasiswa 100%', 'Beasiswa 75%', 'Mandiri'] as $option)
                                        <option value="{{ $option }}" {{ ($myDosenEval->rekomendasi_beasiswa ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Rekomendasi Kelas</label>
                                <select name="rekomendasi_kelas" required class="w-full px-5 py-4 bg-white border border-orange-100 rounded-2xl focus:border-orange-500 focus:ring-4 focus:ring-orange-100 outline-none font-bold text-slate-700 transition-all shadow-sm">
                                    <option value="" disabled selected>Pilih Kelas</option>
                                    @foreach(['Kelas Reguler', 'Kelas Excellent'] as $option)
                                        <option value="{{ $option }}" {{ ($myDosenEval->rekomendasi_kelas ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Rekomendasi Prodi 1</label>
                                <select name="rekomendasi_prodi_1" required class="w-full px-5 py-4 bg-white border border-orange-100 rounded-2xl focus:border-orange-500 focus:ring-4 focus:ring-orange-100 outline-none font-bold text-slate-700 transition-all shadow-sm">
                                    <option value="" disabled selected>Pilih Prodi 1</option>
                                    @foreach(['Sains Komunikasi', 'Desain Komunikasi Visual (DKV)', 'Manajemen', 'Akuntansi', 'Sistem Informasi', 'Pendidikan Bahasa Inggris', 'Pendidikan Matematika', 'Ilmu Komputer'] as $option)
                                        <option value="{{ $option }}" {{ ($myDosenEval->rekomendasi_prodi_1 ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Rekomendasi Prodi 2</label>
                                <select name="rekomendasi_prodi_2" required class="w-full px-5 py-4 bg-white border border-orange-100 rounded-2xl focus:border-orange-500 focus:ring-4 focus:ring-orange-100 outline-none font-bold text-slate-700 transition-all shadow-sm">
                                    <option value="" disabled selected>Pilih Prodi 2</option>
                                    @foreach(['Sains Komunikasi', 'Desain Komunikasi Visual (DKV)', 'Manajemen', 'Akuntansi', 'Sistem Informasi', 'Pendidikan Bahasa Inggris', 'Pendidikan Matematika', 'Ilmu Komputer'] as $option)
                                        <option value="{{ $option }}" {{ ($myDosenEval->rekomendasi_prodi_2 ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="space-y-2 mt-4">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Catatan / Ulasan Hasil Wawancara</label>
                            <textarea name="catatan" rows="4" placeholder="Tuliskan ulasan, catatan kualitatif, atau alasan rekomendasi kelulusan & skema beasiswa secara keseluruhan..."
                                class="w-full px-5 py-4 bg-white border border-orange-100 rounded-2xl focus:border-orange-500 focus:ring-4 focus:ring-orange-100 outline-none font-semibold text-slate-700 text-xs transition-all shadow-sm">{{ $myDosenEval->catatan ?? '' }}</textarea>
                        </div>
                    </div>

                    <button type="submit" class="group w-full py-6 bg-slate-900 text-white font-black rounded-[2rem] shadow-2xl shadow-slate-200 hover:bg-black hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                        <span class="text-sm uppercase tracking-[0.2em]">Simpan Evaluasi Wawancara</span>
                        <span class="iconify text-xl group-hover:translate-x-1 transition-transform" data-icon="solar:arrow-right-bold"></span>
                    </button>
                </form>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const inputs = document.querySelectorAll('.calc-input');
                    const totalEl = document.getElementById('live-total');

                    function calculateTotal() {
                        const getVal = (name) => {
                            const checkedInput = document.querySelector(`input[name="${name}"]:checked`);
                            return checkedInput ? parseFloat(checkedInput.value) : 0;
                        };

                        const m = getVal('wawancara_motivasi');
                        const p = getVal('wawancara_prestasi');
                        const k = getVal('wawancara_karakter');
                        const c = getVal('wawancara_kontribusi');
                        const cm = getVal('wawancara_komunikasi');

                        const total = ((m * 0.25) + (p * 0.20) + (k * 0.20) + (c * 0.20) + (cm * 0.15)) * 20;
                        totalEl.innerText = total.toFixed(2);
                    }

                    inputs.forEach(input => input.addEventListener('change', calculateTotal));
                    calculateTotal(); // Initial calculation
                });
            </script>
            @endif

            <!-- Form Input (Akademik) -->
                        <!-- Cek Detail (Akademik / Admin / Palugada) -->
            @if($type === 'akademik' && auth()->user()->role !== 'dosen')
            @php
                $interviewerId = $user->peserta->interviewer_id;
                $dosenEval = null;
                if ($interviewerId) {
                    $dosenEval = $user->peserta->penilaianAkademiks->where('penilai_id', $interviewerId)->first();
                } else {
                    $dosenEval = $user->peserta->penilaianAkademiks->first();
                }
            @endphp

            @if($dosenEval)
            <div class="bg-white p-5 sm:p-10 rounded-[2.5rem] border border-orange-100 shadow-xl shadow-orange-500/5 space-y-10">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-slate-100">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-400 to-orange-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-orange-200">
                            <span class="iconify text-2xl" data-icon="solar:clipboard-text-bold"></span>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-800">Detail Hasil Wawancara</h3>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Dosen Penilai: {{ $dosenEval->penilai->nama ?? 'Nama Dosen' }}</p>
                        </div>
                    </div>
                    
                    <div class="px-5 py-2.5 bg-orange-50 border border-orange-100 rounded-2xl text-center">
                        <span class="block text-[8px] font-black text-orange-400 uppercase tracking-widest">TOTAL SKOR AKHIR</span>
                        <span class="text-2xl font-black text-orange-600">{{ number_format($dosenEval->total_akhir, 2) }} / 100</span>
                    </div>
                </div>

                <!-- Rekomendasi Akhir & Beasiswa & Kelas/Prodi -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 bg-slate-50 p-6 sm:p-8 rounded-[2rem] border border-slate-100 shadow-inner">
                    <div class="space-y-1">
                        <span class="block text-[9px] font-black text-slate-400 uppercase tracking-widest">Rekomendasi Kelulusan</span>
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider {{ in_array($dosenEval->rekomendasi_akhir, ['Sangat Direkomendasikan', 'Direkomendasikan', 'Direkomendasikan dengan Catatan']) ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                            <span class="iconify text-base" data-icon="{{ in_array($dosenEval->rekomendasi_akhir, ['Sangat Direkomendasikan', 'Direkomendasikan', 'Direkomendasikan dengan Catatan']) ? 'solar:shield-check-bold' : 'solar:shield-cross-bold' }}"></span>
                            {{ $dosenEval->rekomendasi_akhir }}
                        </div>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[9px] font-black text-slate-400 uppercase tracking-widest">Rekomendasi Skema Beasiswa</span>
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider bg-indigo-100 text-indigo-800 border border-indigo-200">
                            <span class="iconify text-base" data-icon="solar:diploma-bold"></span>
                            {{ $dosenEval->rekomendasi_beasiswa }}
                        </div>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[9px] font-black text-slate-400 uppercase tracking-widest">Rekomendasi Kelas</span>
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200">
                            <span class="iconify text-base" data-icon="solar:users-group-two-rounded-bold"></span>
                            {{ $dosenEval->rekomendasi_kelas ?? '-' }}
                        </div>
                    </div>
                    <div class="space-y-1 md:col-span-2 lg:col-span-3 pt-3 border-t border-slate-200/50">
                        <span class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Rekomendasi Program Studi</span>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="flex-1 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black bg-blue-50 text-blue-800 border border-blue-200">
                                <span class="iconify text-base" data-icon="solar:square-academic-cap-bold"></span>
                                <span class="text-[9px] opacity-75 font-semibold">Prodi 1:</span> {{ $dosenEval->rekomendasi_prodi_1 ?? '-' }}
                            </div>
                            <div class="flex-1 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black bg-teal-50 text-teal-800 border border-teal-200">
                                <span class="iconify text-base" data-icon="solar:square-academic-cap-bold"></span>
                                <span class="text-[9px] opacity-75 font-semibold">Prodi 2:</span> {{ $dosenEval->rekomendasi_prodi_2 ?? '-' }}
                            </div>
                        </div>
                    </div>
                    @if($dosenEval->catatan_rekomendasi_beasiswa)
                    <div class="col-span-1 md:col-span-2 lg:col-span-3 pt-4 border-t border-slate-200/50 space-y-1">
                        <span class="block text-[9px] font-black text-slate-400 uppercase tracking-widest">Catatan & Rationale Rekomendasi</span>
                        <p class="text-xs font-bold text-slate-600 leading-relaxed italic">"{{ $dosenEval->catatan_rekomendasi_beasiswa }}"</p>
                    </div>
                    @endif
                </div>

                <!-- Detail Rubrik (Read-Only) -->
                <div class="space-y-8">
                    @php
                        $rubrikData = [
                            [
                                'title' => '1. Motivasi & Komitmen (Bobot 25%)',
                                'color' => 'orange',
                                'questions' => [
                                    1 => 'Apa yang mendorong Anda melamar MNCU Future Leader Scholarship, dan mengapa Anda merasa layak mendapatkannya?',
                                    2 => 'Bagaimana beasiswa ini akan berkontribusi pada tujuan jangka panjang Anda?',
                                    3 => 'Tantangan terbesar apa yang pernah Anda hadapi dalam perjalanan akademik Anda, dan bagaimana Anda mengatasinya?',
                                    4 => 'Apa komitmen dan kontribusi apa yang akan Anda berikan untuk MNC University / MNCU Future Leader Scholarship?'
                                ],
                                'score' => $dosenEval->wawancara_motivasi
                            ],
                            [
                                'title' => '2. Prestasi Akademik (Bobot 20%)',
                                'color' => 'indigo',
                                'questions' => [
                                    1 => 'Ceritakan pencapaian akademik yang paling membanggakan Anda dan proses di baliknya.',
                                    2 => 'Mata Pelajaran atau bidang studi mana yang paling Anda kuasai dan mengapa?',
                                    3 => 'Bagaimana Anda menyikapi mata pelajaran yang nilainya kurang memuaskan?',
                                    4 => 'Apakah Anda pernah terlibat dalam penelitian, karya tulis, atau proyek akademik? Jelaskan kontribusi Anda.'
                                ],
                                'score' => $dosenEval->wawancara_prestasi
                            ],
                            [
                                'title' => '3. Karakter & Integritas (Bobot 20%)',
                                'color' => 'rose',
                                'questions' => [
                                    1 => 'Ceritakan situasi di mana Anda harus membuat keputusan sulit yang melibatkan nilai-nilai moral atau etika.',
                                    2 => 'Pernahkah Anda menghadapi tekanan untuk melakukan sesuatu yang bertentangan dengan prinsip Anda? Bagaimana respons Anda?',
                                    3 => 'Bagaimana Anda membangun kepercayaan dalam hubungan dengan teman, guru, atau anggota organisasi?',
                                    4 => 'Apa nilai hidup yang paling penting bagi Anda dan bagaimana nilai itu tercermin dalam keseharian Anda?'
                                ],
                                'score' => $dosenEval->wawancara_karakter
                            ],
                            [
                                'title' => '4. Kontribusi & Kepemimpinan (Bobot 20%)',
                                'color' => 'emerald',
                                'questions' => [
                                    1 => 'Ceritakan pengalaman Anda memimpin suatu tim atau organisasi. Apa tantangan terbesar dan bagaimana Anda mengatasinya?',
                                    2 => 'Kontribusi nyata apa yang pernah Anda berikan kepada organisasi/komunitas atau lingkungan sekitar Anda?',
                                    3 => 'Bagaimana Anda memotivasi anggota tim yang kurang bersemangat atau berkonflik satu sama lain?',
                                    4 => 'Jika mendapat beasiswa ini, kontribusi apa yang ingin Anda berikan bagi almamater atau masyarakat?'
                                ],
                                'score' => $dosenEval->wawancara_kontribusi
                            ],
                            [
                                'title' => '5. Kemampuan Komunikasi (Bobot 15%)',
                                'color' => 'amber',
                                'questions' => [
                                    1 => 'Jelaskan secara singkat tentang diri Anda dalam 60 detik kepada orang yang baru Anda kenal.',
                                    2 => 'Bagaimana cara Anda menyampaikan ide atau pendapat yang berbeda dari mayoritas kelompok?',
                                    3 => 'Ceritakan pengalaman Anda melakukan presentasi atau berbicara di depan umum.',
                                    4 => 'Bagaimana Anda memastikan pesan yang Anda sampaikan dipahami dengan baik oleh lawan bicara?'
                                ],
                                'score' => $dosenEval->wawancara_komunikasi
                            ],
                        ];
                    @endphp

                    @foreach($rubrikData as $rubrik)
                    <div class="p-6 sm:p-8 bg-slate-50/50 rounded-[2rem] border border-slate-100 shadow-sm space-y-4">
                        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 pb-3 border-b border-slate-100">
                            <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider">
                                {{ $rubrik['title'] }}
                            </h4>
                            <div class="flex items-center gap-1.5 bg-white p-1 rounded-xl border border-slate-100 shadow-sm">
                                @foreach([1 => 'SK', 2 => 'K', 3 => 'C', 4 => 'B', 5 => 'SB'] as $score => $label)
                                    <div class="px-3 py-1.5 rounded-lg text-[9px] font-black transition-all border {{ ($rubrik['score'] ?? 0) == $score ? 'bg-orange-600 text-white border-orange-600 shadow-sm' : 'bg-slate-50 text-slate-400 border-slate-200/50' }}">
                                        {{ $score }} <span class="text-[8px] opacity-75 font-semibold">({{ $label }})</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="bg-white p-5 rounded-2xl border border-slate-100/50 space-y-2">
                            <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Panduan Pertanyaan Wawancara (Deskripsi):</span>
                            <ul class="list-decimal list-inside space-y-1 ml-1">
                                @foreach($rubrik['questions'] as $qText)
                                    <li class="text-[10px] font-bold text-slate-500 leading-relaxed">{{ $qText }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <!-- Belum Ada Penilaian -->
            <div class="bg-white p-10 rounded-[2.5rem] border border-orange-100 shadow-xl shadow-orange-500/5 text-center space-y-4">
                <span class="iconify text-6xl text-orange-200 mx-auto" data-icon="solar:notes-minimalistic-bold-duotone"></span>
                <div>
                    <h3 class="text-lg font-black text-slate-800">Evaluasi Belum Selesai</h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Dosen penilai/pewawancara belum mengisi penilaian wawancara untuk peserta ini.</p>
                </div>
            </div>
            @endif
            @endif
        </div>

        <!-- Right Side: History & Summary Sidebar -->
        <div class="lg:col-span-1 space-y-8">
            <!-- Summary Mentor -->
            @if($type === 'mentor' || $type === 'admin')
            <div class="bg-slate-50 p-6 sm:p-8 rounded-[2.5rem] border border-slate-100">
                <h4 class="font-black text-slate-800 mb-6 uppercase tracking-[0.2em] text-[10px] flex items-center gap-2">
                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                    Ringkasan Evaluasi Mentor
                </h4>
                @php $allMentorPenilaian = $user->peserta->penilaianMentors; @endphp
                @if($allMentorPenilaian->count() > 0)
                    <div class="space-y-4">
                        @foreach($allMentorPenilaian as $penilaian)
                            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm transition-all hover:shadow-md">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">{{ $penilaian->mentor->nama ?? 'Unknown Mentor' }}</span>
                                    <span class="text-xl font-black text-slate-900">{{ number_format($penilaian->nilai, 2) }}</span>
                                </div>
                                <p class="text-[11px] text-slate-500 font-semibold italic border-l-2 border-slate-100 pl-3">"{{ $penilaian->catatan ?? 'Tidak ada catatan.' }}"</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <span class="iconify text-3xl text-slate-200 mb-2 mx-auto" data-icon="solar:notes-minimalistic-bold-duotone"></span>
                        <p class="text-[10px] text-slate-400 font-bold italic uppercase tracking-widest">Belum ada penilaian mentor.</p>
                    </div>
                @endif
            </div>
            @endif

            <!-- Summary Akademik -->
            @if($type === 'akademik' || $type === 'admin')
            <div class="bg-slate-50 p-6 sm:p-8 rounded-[2.5rem] border border-slate-100">
                <h4 class="font-black text-slate-800 mb-6 uppercase tracking-[0.2em] text-[10px] flex items-center gap-2">
                    <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                    Ringkasan Evaluasi Akademik
                </h4>
                @php $allAkademikPenilaian = $user->peserta->penilaianAkademiks; @endphp
                @if($allAkademikPenilaian->count() > 0)
                    <div class="space-y-4">
                        @foreach($allAkademikPenilaian as $penilaian)
                            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm transition-all hover:shadow-md">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-[10px] font-black text-orange-600 uppercase tracking-widest">{{ $penilaian->penilai->nama ?? 'Unknown Evaluator' }}</span>
                                    <span class="text-xl font-black text-slate-900">{{ number_format($penilaian->total_akhir, 2) }}</span>
                                </div>
                                @if($penilaian->wawancara_motivasi !== null)
                                <div class="space-y-2 mt-3 pt-3 border-t border-slate-100">
                                    <div class="flex justify-between items-center text-[10px] font-black text-slate-700">
                                        <span>Rekomendasi Kelulusan:</span>
                                        <span class="text-orange-600 uppercase tracking-wider text-[9px]">{{ $penilaian->rekomendasi_akhir }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[10px] font-black text-slate-700">
                                        <span>Skema Beasiswa:</span>
                                        <span class="text-indigo-600 uppercase tracking-wider text-[9px]">{{ $penilaian->rekomendasi_beasiswa }}</span>
                                    </div>
                                    @if($penilaian->rekomendasi_kelas)
                                    <div class="flex justify-between items-center text-[10px] font-black text-slate-700">
                                        <span>Rekomendasi Kelas:</span>
                                        <span class="text-amber-600 uppercase tracking-wider text-[9px]">{{ $penilaian->rekomendasi_kelas }}</span>
                                    </div>
                                    @endif
                                    @if($penilaian->rekomendasi_prodi_1)
                                    <div class="text-[10px] font-black text-slate-700 space-y-0.5">
                                        <div class="flex justify-between items-center">
                                            <span>Rekomendasi Prodi 1:</span>
                                            <span class="text-blue-600 uppercase tracking-wider text-[9px]">{{ $penilaian->rekomendasi_prodi_1 }}</span>
                                        </div>
                                        @if($penilaian->rekomendasi_prodi_2)
                                        <div class="flex justify-between items-center">
                                            <span>Rekomendasi Prodi 2:</span>
                                            <span class="text-teal-600 uppercase tracking-wider text-[9px]">{{ $penilaian->rekomendasi_prodi_2 }}</span>
                                        </div>
                                        @endif
                                    </div>
                                    @endif
                                    <div class="grid grid-cols-5 gap-1 pt-1">
                                        <span class="text-center py-1 bg-slate-50 border border-slate-100 rounded text-[9px] font-black text-slate-600" title="Motivasi">M: {{ number_format($penilaian->wawancara_motivasi * 20, 1) }}</span>
                                        <span class="text-center py-1 bg-slate-50 border border-slate-100 rounded text-[9px] font-black text-slate-600" title="Prestasi">P: {{ number_format($penilaian->wawancara_prestasi * 20, 1) }}</span>
                                        <span class="text-center py-1 bg-slate-50 border border-slate-100 rounded text-[9px] font-black text-slate-600" title="Karakter">K: {{ number_format($penilaian->wawancara_karakter * 20, 1) }}</span>
                                        <span class="text-center py-1 bg-slate-50 border border-slate-100 rounded text-[9px] font-black text-slate-600" title="Kontribusi">KT: {{ number_format($penilaian->wawancara_kontribusi * 20, 1) }}</span>
                                        <span class="text-center py-1 bg-slate-50 border border-slate-100 rounded text-[9px] font-black text-slate-600" title="Komunikasi">KM: {{ number_format($penilaian->wawancara_komunikasi * 20, 1) }}</span>
                                    </div>
                                    @if($penilaian->catatan_rekomendasi_beasiswa)
                                    <p class="text-[10px] text-slate-400 font-bold italic mt-2">"{{ $penilaian->catatan_rekomendasi_beasiswa }}"</p>
                                    @endif
                                </div>
                                @else
                                <div class="flex gap-2 mb-3">
                                    <span class="px-2 py-0.5 bg-orange-50 text-orange-600 rounded text-[8px] font-black border border-orange-100/50">DOSEN: {{ $penilaian->total_dosen }}</span>
                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-600 rounded text-[8px] font-black border border-blue-100/50">MHS: {{ $penilaian->total_mhs }}</span>
                                </div>
                                <p class="text-[11px] text-slate-500 font-semibold italic border-l-2 border-slate-100 pl-3">"{{ $penilaian->catatan ?? 'Tidak ada catatan.' }}"</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <span class="iconify text-3xl text-slate-200 mb-2 mx-auto" data-icon="solar:library-bold-duotone"></span>
                        <p class="text-[10px] text-slate-400 font-bold italic uppercase tracking-widest">Belum ada penilaian akademik.</p>
                    </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
