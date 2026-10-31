@extends('pendaftar.layout')

@section('content')
<div class="max-w-6xl mx-auto space-y-10 pb-32">
    {{-- 1. PREMIUM HEADER --}}
    <div class="relative overflow-hidden bg-slate-900 rounded-[3.5rem] p-10 md:p-16 text-white shadow-2xl border border-white/5">
        <div class="absolute top-0 right-0 w-[400px] h-[400px] bg-primary-gold/5 rounded-full blur-[120px] -mr-40 -mt-40"></div>
        
        <div class="relative z-10">
            <div class="inline-flex items-center gap-3 bg-white/5 border border-white/10 px-4 py-2 rounded-2xl mb-8 backdrop-blur-md">
                <span class="iconify text-primary-gold text-lg" data-icon="solar:camera-add-bold-duotone"></span>
                <span class="text-[9px] font-black uppercase tracking-[0.3em] text-primary-gold">Official Identity Kit</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight mb-6 leading-tight">Twibbon <span class="text-primary-gold italic">Studio</span></h1>
            <p class="text-slate-400 text-sm md:text-base font-medium max-w-2xl leading-relaxed mb-8">
                Tunjukkan kebanggaanmu sebagai calon penerima beasiswa MNCU Future Leader Scholarship. Kreasikan twibbon resmi dan bagikan semangatmu di media sosial.
            </p>
        </div>
    </div>

    {{-- 2. MAIN CREATIVE STUDIO --}}
    <div class="bg-white rounded-[3.5rem] p-8 md:p-12 border border-slate-100 shadow-sm overflow-hidden relative group">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
            <!-- Left: Upload & Controls -->
            <div class="space-y-10">
                <div class="space-y-6">
                    <div>
                        <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4">Studio Adjustments</h3>
                        <label class="block text-2xl font-black text-slate-800 tracking-tight leading-tight">Personalize Your Identity</label>
                    </div>

                    <div class="relative group/upload">
                        <input type="file" id="photoUpload" accept="image/*" class="hidden">
                        <label for="photoUpload" class="flex flex-col items-center justify-center w-full h-56 border-2 border-dashed border-slate-100 rounded-[2.5rem] cursor-pointer bg-slate-50/50 hover:bg-primary-gold/5 hover:border-primary-gold transition-all duration-500">
                            <div class="flex flex-col items-center justify-center p-8">
                                <div class="w-16 h-16 bg-white rounded-2xl shadow-xl text-slate-300 mb-4 flex items-center justify-center group-hover/upload:text-primary-gold group-hover/upload:scale-110 transition-all">
                                    <span class="iconify text-3xl" data-icon="solar:camera-bold-duotone"></span>
                                </div>
                                <p class="mb-2 text-[10px] font-black uppercase tracking-widest text-slate-400 group-hover/upload:text-primary-gold text-center">Pilih Foto Terbaikmu</p>
                                <p class="text-[9px] font-bold text-slate-300 uppercase tracking-widest text-center">Square Image Recommended</p>
                            </div>
                        </label>
                    </div>

                    <div class="p-8 bg-slate-50 rounded-[2.5rem] border border-slate-100 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <div class="flex items-center justify-between">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Ukuran / Scale</label>
                                    <span id="scaleValue" class="text-[10px] font-black text-primary-gold">100%</span>
                                </div>
                                <input type="range" id="scaleSlider" min="10" max="300" value="100" class="w-full h-1 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-primary-gold">
                            </div>

                            <div class="space-y-6">
                                <div class="flex items-center justify-between">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Rotasi / Degree</label>
                                    <span id="rotateValue" class="text-[10px] font-black text-slate-800">0°</span>
                                </div>
                                <input type="range" id="rotateSlider" min="0" max="360" value="0" class="w-full h-1 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-slate-800">
                            </div>

                            <div class="space-y-6">
                                <div class="flex items-center justify-between">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Posisi X</label>
                                    <span id="xValue" class="text-[10px] font-black text-slate-800">540</span>
                                </div>
                                <input type="range" id="xSlider" min="0" max="1080" value="540" class="w-full h-1 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-slate-800">
                            </div>

                            <div class="space-y-6">
                                <div class="flex items-center justify-between">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Posisi Y</label>
                                    <span id="yValue" class="text-[10px] font-black text-slate-800">540</span>
                                </div>
                                <input type="range" id="ySlider" min="0" max="1080" value="540" class="w-full h-1 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-slate-800">
                            </div>
                        </div>
                    </div>

                    <button id="downloadBtn" class="w-full px-10 py-5 bg-slate-900 text-white font-black rounded-2xl hover:bg-primary-gold hover:text-slate-900 transition-all flex items-center justify-center gap-4 transform active:scale-95 shadow-xl group">
                        <span class="text-[10px] uppercase tracking-[0.3em]">Download Result</span>
                        <span class="iconify text-xl group-hover:translate-y-1 transition-transform" data-icon="solar:download-square-bold"></span>
                    </button>
                </div>
            </div>

            <!-- Right: Preview Canvas -->
            <div class="space-y-8">
                <div class="flex items-center justify-between">
                     <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em]">Live Preview</h3>
                     <div class="flex items-center gap-2">
                         <span class="w-2 h-2 rounded-full bg-primary-gold animate-pulse"></span>
                         <span class="text-[8px] font-black text-primary-gold uppercase tracking-widest">Studio Ready</span>
                     </div>
                </div>
                
                <div class="relative group/canvas">
                    <div class="absolute -inset-4 bg-primary-gold/5 rounded-[3.5rem] blur-2xl opacity-50"></div>
                    <div class="relative bg-white p-3 rounded-[2.5rem] border border-slate-100 shadow-2xl mx-auto overflow-hidden" style="max-width: 500px;">
                        <canvas id="twibonCanvas" width="1080" height="1080" class="w-full h-auto rounded-2xl cursor-grab active:cursor-grabbing"></canvas>
                        <div id="dragHint" class="absolute inset-3 bg-slate-900/80 backdrop-blur-sm rounded-2xl flex flex-col items-center justify-center text-white opacity-0 transition-opacity pointer-events-none z-20">
                            <span class="iconify text-5xl text-primary-gold mb-4 animate-bounce" data-icon="solar:camera-rotate-bold-duotone"></span>
                            <span class="text-[9px] font-black uppercase tracking-widest">Upload foto untuk kustomisasi</span>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-900 p-6 rounded-[2.5rem] border border-white/5 flex items-center gap-5">
                    <div class="w-12 h-12 bg-white/5 text-primary-gold rounded-xl flex items-center justify-center shrink-0 border border-white/10">
                        <span class="iconify text-2xl" data-icon="solar:stars-bold-duotone"></span>
                    </div>
                    <p class="text-[10px] text-slate-400 font-medium leading-relaxed">
                        <strong class="text-white block mb-1 uppercase tracking-widest">Tips Studio:</strong>
                        Gunakan foto square (1:1) untuk hasil terbaik. Anda dapat menggeser (drag) foto di dalam kanvas untuk posisi yang pas.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. SOCIAL CHANNEL SUBMISSION --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Caption Helper -->
        <div class="lg:col-span-5 bg-white rounded-[3.5rem] p-10 border border-slate-100 shadow-sm space-y-8">
            <div>
                <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4">Marketing Kit</h3>
                <label class="block text-2xl font-black text-slate-800 tracking-tight leading-tight">Official Caption</label>
            </div>

            <div class="bg-slate-900 rounded-[2.5rem] p-8 relative overflow-hidden group/caption">
                <div class="absolute top-0 right-0 p-6 opacity-5">
                    <span class="iconify text-5xl text-white" data-icon="solar:chat-round-check-bold"></span>
                </div>
                
                <div class="relative z-10">
                    <div id="captionText" class="text-[10px] text-slate-400 font-medium leading-relaxed space-y-4 max-h-[250px] overflow-y-auto pr-4 custom-scrollbar">
                        <p class="text-primary-gold font-black uppercase tracking-wider">I'M READY FOR MNCU FUTURE LEADER SCHOLARSHIP 📢‼️</p>
                        <p>Behind this twibbon, there's a dream, a hope, and a step forward towards the future. ✨</p>
                        <p>Halo, future leader friends 👋🏻 Perkenalkan saya {{ Auth::user()->nama }} berasal dari {{ $peserta->nama_sekolah ?? '[Asal sekolah]' }} dengan ini siap memulai perjalanan kepemimpinan dalam program MNCU Future Leader Scholarship. 🚀</p>
                        <p>🗣 The future needs leaders. Starting the journey with MNCU Future Leader Scholarship. #ShapingFutureLeader #MNCUFutureLeaderScholarship</p>
                    </div>

                    <button onclick="copyCaption(this)" class="w-full mt-8 py-4 bg-white text-slate-900 rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-primary-gold transition-all flex items-center justify-center gap-3 active:scale-95">
                        <span class="iconify text-lg" data-icon="solar:copy-bold"></span>
                        <span>Copy Caption</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Submission Form -->
        <div class="lg:col-span-7 bg-white rounded-[3.5rem] p-10 border border-slate-100 shadow-sm">
            <form action="{{ route('pendaftar.twibbon.store') }}" method="POST" class="space-y-8">
                @csrf
                <div>
                    <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4">Verification</h3>
                    <label class="block text-2xl font-black text-slate-800 tracking-tight leading-tight">Post Verification</label>
                </div>

                <div class="space-y-6">
                    <div class="space-y-4">
                        <div class="group/field">
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Instagram Post Link</label>
                            <div class="relative">
                                <div class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within/field:text-primary-gold transition-colors">
                                    <span class="iconify" data-icon="solar:link-bold-duotone"></span>
                                </div>
                                <input type="url" name="link_twibbon" value="{{ old('link_twibbon', $peserta->link_twibbon) }}" placeholder="https://instagram.com/p/..." class="w-full bg-slate-50 px-12 py-4 rounded-xl border border-slate-100 focus:border-primary-gold focus:bg-white outline-none font-bold text-slate-700 transition-all" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="group/field">
                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Profile Instagram</label>
                                <input type="url" name="link_ig" value="{{ old('link_ig', $peserta->link_ig) }}" placeholder="Link Profil" class="w-full bg-slate-50 px-5 py-4 rounded-xl border border-slate-100 focus:border-slate-300 focus:bg-white outline-none font-bold text-slate-700 transition-all">
                            </div>
                            <div class="group/field">
                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Profile TikTok</label>
                                <input type="url" name="link_tiktok" value="{{ old('link_tiktok', $peserta->link_tiktok) }}" placeholder="Link Profil" class="w-full bg-slate-50 px-5 py-4 rounded-xl border border-slate-100 focus:border-slate-300 focus:bg-white outline-none font-bold text-slate-700 transition-all">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-5 bg-slate-900 text-white font-black rounded-xl hover:bg-primary-gold hover:text-slate-900 transition-all flex items-center justify-center gap-4 transform active:scale-95 shadow-xl">
                        <span class="text-[10px] uppercase tracking-[0.3em]">Save Media Connections</span>
                        <span class="iconify text-xl" data-icon="solar:check-circle-bold"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('twibonCanvas');
    const ctx = canvas.getContext('2d');
    const photoUpload = document.getElementById('photoUpload');
    const scaleSlider = document.getElementById('scaleSlider');
    const scaleValue = document.getElementById('scaleValue');
    const rotateSlider = document.getElementById('rotateSlider');
    const rotateValue = document.getElementById('rotateValue');
    const xSlider = document.getElementById('xSlider');
    const xValue = document.getElementById('xValue');
    const ySlider = document.getElementById('ySlider');
    const yValue = document.getElementById('yValue');
    const downloadBtn = document.getElementById('downloadBtn');
    const dragHint = document.getElementById('dragHint');

    let userPhoto = null;
    let twibonFrame = new Image();
    let photoScale = 1;
    let photoRotation = 0;
    let photoX = 540; 
    let photoY = 540; 
    let isDragging = false;
    let dragStartX = 0;
    let dragStartY = 0;

    twibonFrame.src = '{{ asset("icon/twiibon.webp") }}';
    twibonFrame.onload = function() {
        drawCanvas();
    };

    photoUpload.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                userPhoto = new Image();
                userPhoto.onload = function() {
                    photoX = canvas.width / 2;
                    photoY = canvas.height / 2;
                    dragHint.classList.add('opacity-0');
                    drawCanvas();
                    Swal.fire({
                        icon: 'success',
                        title: 'Ready to Edit!',
                        text: 'Silakan atur posisi dan ukuran foto Anda.',
                        timer: 2000,
                        showConfirmButton: false,
                        confirmButtonColor: '#0f172a',
                        customClass: { popup: 'rounded-[2rem]' }
                    });
                };
                userPhoto.src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    scaleSlider.addEventListener('input', function() {
        photoScale = this.value / 100;
        scaleValue.textContent = this.value + '%';
        drawCanvas();
    });

    rotateSlider.addEventListener('input', function() {
        photoRotation = (this.value * Math.PI) / 180;
        rotateValue.textContent = this.value + '°';
        drawCanvas();
    });

    xSlider.addEventListener('input', function() {
        photoX = parseInt(this.value);
        xValue.textContent = this.value;
        drawCanvas();
    });

    ySlider.addEventListener('input', function() {
        photoY = parseInt(this.value);
        yValue.textContent = this.value;
        drawCanvas();
    });

    canvas.addEventListener('mousedown', function(e) {
        if (!userPhoto) return;
        isDragging = true;
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        dragStartX = (e.clientX - rect.left) * scaleX - photoX;
        dragStartY = (e.clientY - rect.top) * scaleY - photoY;
    });

    canvas.addEventListener('mousemove', function(e) {
        if (!isDragging || !userPhoto) return;
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        photoX = (e.clientX - rect.left) * scaleX - dragStartX;
        photoY = (e.clientY - rect.top) * scaleY - dragStartY;
        
        // Update sliders to match drag position
        xSlider.value = photoX;
        xValue.textContent = Math.round(photoX);
        ySlider.value = photoY;
        yValue.textContent = Math.round(photoY);

        drawCanvas();
    });

    window.addEventListener('mouseup', () => isDragging = false);

    function drawCanvas() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        if (userPhoto) {
            ctx.save();
            ctx.translate(photoX, photoY);
            ctx.rotate(photoRotation);
            const scaledWidth = userPhoto.width * photoScale;
            const scaledHeight = userPhoto.height * photoScale;
            ctx.drawImage(userPhoto, -scaledWidth / 2, -scaledHeight / 2, scaledWidth, scaledHeight);
            ctx.restore();
        } else {
            dragHint.classList.remove('opacity-0');
        }
        if (twibonFrame.complete) {
            ctx.drawImage(twibonFrame, 0, 0, canvas.width, canvas.height);
        }
    }

    downloadBtn.addEventListener('click', function() {
        if (!userPhoto) {
            Swal.fire({ icon: 'warning', title: 'Belum Ada Foto', text: 'Silakan upload foto terlebih dahulu!', customClass: { popup: 'rounded-[1.5rem]' } });
            return;
        }
        const link = document.createElement('a');
        link.download = 'twibbon-mfls-' + Date.now() + '.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    });

    drawCanvas();
});

function copyCaption(button) {
    const captionEl = document.getElementById('captionText');
    const paragraphs = Array.from(captionEl.querySelectorAll('p'));
    const textToCopy = paragraphs.map(p => p.innerText.trim()).join('\n\n');

    navigator.clipboard.writeText(textToCopy).then(() => {
        const originalContent = button.innerHTML;
        button.innerHTML = '<span class="iconify text-lg text-primary-gold" data-icon="solar:check-read-bold"></span><span class="text-primary-gold">Caption Copied!</span>';
        button.classList.add('bg-slate-900', 'border-primary-gold/20');
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.classList.remove('bg-slate-900', 'border-primary-gold/20');
        }, 2000);
    });
}
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
</style>
@endsection
