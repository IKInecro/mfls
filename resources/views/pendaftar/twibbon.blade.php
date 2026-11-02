@extends('pendaftar.layout')

@section('content')
<div class="max-w-5xl mx-auto space-y-4 sm:space-y-6 pb-20">
    
    <!-- Hero Header (Grand Royal Navy & Slate Theme - Mobile Friendly) -->
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-br from-[#0B1120] via-[#0F172A] to-[#1E293B] text-white p-5 sm:p-7 md:p-8 border border-slate-800 shadow-xl">
        <div class="absolute -top-32 -left-32 w-80 h-80 rounded-full bg-blue-600/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-80 h-80 rounded-full bg-amber-400/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 sm:gap-6">
            <div class="space-y-1.5 sm:space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/10 border border-white/15 text-[10px] sm:text-[11px] font-bold text-slate-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                    <span>KAMPANYE SOSIAL RESMI MFLS AJARAN 2027</span>
                </div>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-black text-white tracking-tight">
                    Studio Twibbon Resmi
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 font-medium leading-relaxed">
                    Kreasikan foto profil Anda dengan bingkai resmi MNC University Future Leader Scholarship.
                </p>
            </div>

            <div class="shrink-0 w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-blue-400 shadow-sm hidden xs:flex">
                <span class="iconify text-2xl sm:text-3xl" data-icon="solar:camera-add-bold-duotone"></span>
            </div>
        </div>
    </div>

    <!-- Main Creative Studio (Grid 2 Kolom) -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs p-4 sm:p-6 md:p-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
            
            <!-- Right / Top: Live Canvas Preview (Mobile: Order 1, Desktop: Order 2) -->
            <div class="lg:col-span-7 order-1 lg:order-2 flex flex-col items-center space-y-3">
                <div class="flex items-center justify-between w-full max-w-sm sm:max-w-md">
                    <span class="text-xs font-bold text-slate-700">Pratinjau Twibbon</span>
                    <span class="text-[9px] sm:text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">HD 1080x1080</span>
                </div>

                <!-- Canvas Wrapper -->
                <div class="relative w-full max-w-sm sm:max-w-md bg-slate-100 rounded-2xl sm:rounded-3xl p-2 sm:p-3 border border-slate-200/80 shadow-sm overflow-hidden">
                    <canvas id="twibonCanvas" width="1080" height="1080" class="w-full h-auto rounded-xl sm:rounded-2xl cursor-grab active:cursor-grabbing block bg-white touch-none"></canvas>
                    <div id="dragHint" class="absolute inset-2 sm:inset-3 bg-slate-900/75 backdrop-blur-xs rounded-xl sm:rounded-2xl flex flex-col items-center justify-center text-white p-4 text-center transition-opacity pointer-events-none z-20">
                        <svg class="w-8 h-8 text-blue-400 mb-1.5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-xs font-bold">Pilih foto diri untuk memulai</span>
                    </div>
                </div>

                <p class="text-[10px] sm:text-[11px] text-slate-400 font-medium text-center">
                    Geser foto langsung di dalam kotak pratinjau (drag / sentuh).
                </p>
            </div>

            <!-- Left / Bottom: Upload & Slider Controls (Mobile: Order 2, Desktop: Order 1) -->
            <div class="lg:col-span-5 order-2 lg:order-1 space-y-4 sm:space-y-5">
                <div>
                    <h2 class="text-xs sm:text-sm font-extrabold text-slate-900">Kustomisasi Foto</h2>
                    <p class="text-[11px] text-slate-500 font-medium mt-0.5">Unggah foto dan atur posisi presisi Anda pada bingkai twibbon.</p>
                </div>

                <!-- Upload Input Dropzone -->
                <div class="relative">
                    <input type="file" id="photoUpload" accept="image/*" class="hidden">
                    <label for="photoUpload" class="flex flex-col items-center justify-center w-full h-32 sm:h-36 border border-dashed border-slate-200 hover:border-blue-400 rounded-xl sm:rounded-2xl cursor-pointer bg-slate-50/60 hover:bg-blue-50/20 transition-all text-center p-3 group">
                        <svg class="w-6 h-6 text-slate-400 group-hover:text-blue-600 transition-colors mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-xs font-bold text-slate-700 group-hover:text-blue-600">Pilih Foto Diri</span>
                        <span class="text-[10px] text-slate-400 mt-0.5">Disarankan rasio 1:1 (Square)</span>
                    </label>
                </div>

                <!-- Adjustment Controls Card -->
                <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <!-- Scale Slider -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <label class="font-bold text-slate-700 text-[11px]">Skala Ukuran</label>
                            <span id="scaleValue" class="font-mono font-bold text-blue-600 text-[11px]">100%</span>
                        </div>
                        <input type="range" id="scaleSlider" min="10" max="300" value="100" 
                               class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-[#0870B8]">
                    </div>

                    <!-- Rotation Slider -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <label class="font-bold text-slate-700 text-[11px]">Rotasi</label>
                            <span id="rotateValue" class="font-mono font-bold text-slate-700 text-[11px]">0&deg;</span>
                        </div>
                        <input type="range" id="rotateSlider" min="0" max="360" value="0" 
                               class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-[#0870B8]">
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-0.5">
                        <!-- X Slider -->
                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-xs">
                                <label class="font-bold text-slate-700 text-[11px]">Posisi X</label>
                                <span id="xValue" class="font-mono font-bold text-slate-700 text-[10px]">540</span>
                            </div>
                            <input type="range" id="xSlider" min="0" max="1080" value="540" 
                                   class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-slate-800">
                        </div>

                        <!-- Y Slider -->
                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-xs">
                                <label class="font-bold text-slate-700 text-[11px]">Posisi Y</label>
                                <span id="yValue" class="font-mono font-bold text-slate-700 text-[10px]">540</span>
                            </div>
                            <input type="range" id="ySlider" min="0" max="1080" value="540" 
                                   class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-slate-800">
                        </div>
                    </div>
                </div>

                <!-- Download Action Button -->
                <button type="button" 
                        id="downloadBtn" 
                        class="w-full py-2.5 sm:py-3 px-4 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-bold shadow-sm transition-all flex items-center justify-center gap-2 active:scale-[0.99] cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Unduh Hasil Twibbon</span>
                </button>
            </div>

        </div>
    </div>

    <!-- Social Submission & Official Caption Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6">
        
        <!-- Official Caption Copy Box (5 Cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs p-4 sm:p-6 space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="iconify text-lg text-blue-600" data-icon="solar:copy-bold-duotone"></span>
                    <h3 class="text-xs sm:text-sm font-extrabold text-slate-900">Salin Teks Caption</h3>
                </div>
                <button type="button" 
                        onclick="copyCaption(this)"
                        class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 text-[10px] font-bold border border-blue-200 transition-colors">
                    Salin Teks
                </button>
            </div>

            <div id="captionText" class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 text-[11px] text-slate-600 space-y-2 max-h-48 overflow-y-auto leading-relaxed select-text font-mono">
                <p>[MNC FUTURE LEADERS SCHOLARSHIP AJARAN 2027]</p>
                <p>Halo semuanya! Saya {{ Auth::user()->nama }}, calon penerima beasiswa MNC Future Leaders Scholarship Ajaran 2027 di MNC University.</p>
                <p>Saya siap menjadi bagian dari generasi pemimpin masa depan yang berintegritas, unggul secara akademis, dan siap berkontribusi nyata bagi kemajuan bangsa.</p>
                <p>Bergabunglah bersama kami dan wujudkan mimpi bersama MNC University!</p>
                <p class="text-blue-600 font-semibold">#MFLS2027 #MFLSAjaran2027 #MNCUniversity #BeasiswaMNC #MNCFutureLeaders #Scholarship2027</p>
            </div>
        </div>

        <!-- Social Submission Form (7 Cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs p-4 sm:p-6 space-y-4">
            <div>
                <h3 class="text-xs sm:text-sm font-extrabold text-slate-900">Verifikasi Unggahan Media Sosial</h3>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Kirimkan tautan postingan Instagram atau TikTok Anda yang menggunakan twibbon dan caption resmi.</p>
            </div>

            <form action="{{ route('pendaftar.twibbon.store') }}" method="POST" class="space-y-3.5">
                @csrf
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Tautan Postingan Media Sosial</label>
                    <div class="relative">
                        <input type="url" 
                               name="link_twibbon" 
                               value="{{ old('link_twibbon', $peserta->link_twibbon ?? '') }}" 
                               placeholder="https://www.instagram.com/p/... atau https://vt.tiktok.com/..." 
                               required
                               class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 outline-none transition-all">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <span class="iconify text-base" data-icon="solar:link-bold"></span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    @if(!empty($peserta->link_twibbon))
                        <span class="inline-flex items-center gap-1 text-[10px] text-emerald-600 font-bold">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Tautan Tersimpan</span>
                        </span>
                    @else
                        <span></span>
                    @endif

                    <button type="submit" 
                            class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-all shadow-xs cursor-pointer">
                        Simpan Tautan
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>

<!-- Interactive Twibbon Studio Script -->
<script>
function initTwibbonStudio() {
    const canvas = document.getElementById('twibonCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const photoUpload = document.getElementById('photoUpload');
    const scaleSlider = document.getElementById('scaleSlider');
    const rotateSlider = document.getElementById('rotateSlider');
    const xSlider = document.getElementById('xSlider');
    const ySlider = document.getElementById('ySlider');
    const scaleValue = document.getElementById('scaleValue');
    const rotateValue = document.getElementById('rotateValue');
    const xValue = document.getElementById('xValue');
    const yValue = document.getElementById('yValue');
    const downloadBtn = document.getElementById('downloadBtn');
    const dragHint = document.getElementById('dragHint');

    let userPhoto = null;
    let photoX = 540;
    let photoY = 540;
    let photoScale = 1.0;
    let photoRotation = 0;
    let isDragging = false;
    let startX = 0;
    let startY = 0;

    const twibonFrame = new Image();
    twibonFrame.crossOrigin = 'anonymous';
    twibonFrame.src = '{{ asset('icon/twiibon.webp') }}';
    twibonFrame.onload = () => drawCanvas();

    if (photoUpload) {
        photoUpload.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    userPhoto = new Image();
                    userPhoto.onload = function() {
                        photoX = 540;
                        photoY = 540;
                        if (xSlider) xSlider.value = 540;
                        if (ySlider) ySlider.value = 540;
                        if (xValue) xValue.textContent = '540';
                        if (yValue) yValue.textContent = '540';

                        const maxSide = Math.max(userPhoto.width, userPhoto.height);
                        photoScale = 1080 / maxSide;
                        if (scaleSlider) scaleSlider.value = Math.round(photoScale * 100);
                        if (scaleValue) scaleValue.textContent = Math.round(photoScale * 100) + '%';

                        photoRotation = 0;
                        if (rotateSlider) rotateSlider.value = 0;
                        if (rotateValue) rotateValue.textContent = '0°';

                        if (dragHint) dragHint.classList.add('opacity-0');
                        drawCanvas();
                    };
                    userPhoto.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (scaleSlider) {
        scaleSlider.addEventListener('input', function() {
            photoScale = this.value / 100;
            if (scaleValue) scaleValue.textContent = this.value + '%';
            drawCanvas();
        });
    }

    if (rotateSlider) {
        rotateSlider.addEventListener('input', function() {
            photoRotation = (this.value * Math.PI) / 180;
            if (rotateValue) rotateValue.textContent = this.value + '°';
            drawCanvas();
        });
    }

    if (xSlider) {
        xSlider.addEventListener('input', function() {
            photoX = parseInt(this.value);
            if (xValue) xValue.textContent = this.value;
            drawCanvas();
        });
    }

    if (ySlider) {
        ySlider.addEventListener('input', function() {
            photoY = parseInt(this.value);
            if (yValue) yValue.textContent = this.value;
            drawCanvas();
        });
    }

    // Touch & Mouse Dragging on Canvas
    function getCanvasCoords(e) {
        const rect = canvas.getBoundingClientRect();
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        const scale = 1080 / rect.width;
        return {
            x: (clientX - rect.left) * scale,
            y: (clientY - rect.top) * scale
        };
    }

    function onDragStart(e) {
        if (!userPhoto) return;
        isDragging = true;
        const coords = getCanvasCoords(e);
        startX = coords.x - photoX;
        startY = coords.y - photoY;
        if (e.type === 'touchstart') e.preventDefault();
    }

    function onDragMove(e) {
        if (!isDragging || !userPhoto) return;
        const coords = getCanvasCoords(e);
        photoX = Math.round(coords.x - startX);
        photoY = Math.round(coords.y - startY);
        if (xSlider) xSlider.value = photoX;
        if (ySlider) ySlider.value = photoY;
        if (xValue) xValue.textContent = photoX;
        if (yValue) yValue.textContent = photoY;
        drawCanvas();
        if (e.type === 'touchmove') e.preventDefault();
    }

    canvas.addEventListener('mousedown', onDragStart);
    window.addEventListener('mousemove', onDragMove);
    window.addEventListener('mouseup', () => isDragging = false);

    canvas.addEventListener('touchstart', onDragStart, { passive: false });
    window.addEventListener('touchmove', onDragMove, { passive: false });
    window.addEventListener('touchend', () => isDragging = false);

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
            if (dragHint) dragHint.classList.remove('opacity-0');
        }
        if (twibonFrame.complete) {
            ctx.drawImage(twibonFrame, 0, 0, canvas.width, canvas.height);
        }
    }

    if (downloadBtn) {
        downloadBtn.addEventListener('click', function() {
            if (!userPhoto) {
                if (window.modernAlert) {
                    window.modernAlert({
                        type: 'warning',
                        title: 'Foto Belum Dipilih',
                        message: 'Silakan pilih dan unggah foto diri Anda terlebih dahulu.',
                        confirmText: 'Mengerti'
                    });
                }
                return;
            }
            const link = document.createElement('a');
            link.download = 'twibbon-mfls-' + Date.now() + '.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    }

    drawCanvas();
}

function copyCaption(button) {
    const captionEl = document.getElementById('captionText');
    if (!captionEl) return;
    const paragraphs = Array.from(captionEl.querySelectorAll('p'));
    const textToCopy = paragraphs.map(p => p.innerText.trim()).join('\n\n');

    navigator.clipboard.writeText(textToCopy).then(() => {
        const originalContent = button.innerHTML;
        button.innerHTML = '<span class="text-xs font-bold text-blue-600">Disalin!</span>';
        setTimeout(() => {
            button.innerHTML = originalContent;
        }, 2000);
    });
}

document.addEventListener('DOMContentLoaded', initTwibbonStudio);
window.addEventListener('portal:content-loaded', initTwibbonStudio);
</script>
@endsection
