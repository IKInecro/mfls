{{-- Modern Lightweight Modal Alert System & SweetAlert2 Custom Skin --}}
<style>
    /* ============================================================
       1. GLOBAL SWEETALERT2 MODERN OVERRIDES (SNAPPY & MINIMALIST)
       ============================================================ */
    .swal2-container {
        z-index: 99999 !important;
    }
    .swal2-backdrop-show {
        background: rgba(15, 23, 42, 0.45) !important;
        backdrop-filter: blur(6px) !important;
        -webkit-backdrop-filter: blur(6px) !important;
    }
    .swal2-popup {
        width: 25rem !important;
        max-width: calc(100vw - 2rem) !important;
        border-radius: 1.25rem !important;
        padding: 1.5rem !important;
        background: #ffffff !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.18), 0 0 1px 1px rgba(15, 23, 42, 0.05) !important;
        font-family: inherit !important;
    }
    .swal2-show {
        animation: modernSwalIn 0.16s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
    }
    .swal2-hide {
        animation: modernSwalOut 0.12s ease-out forwards !important;
    }
    @keyframes modernSwalIn {
        0% { opacity: 0; transform: scale(0.96) translateY(4px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes modernSwalOut {
        0% { opacity: 1; transform: scale(1); }
        100% { opacity: 0; transform: scale(0.96); }
    }
    /* Matikan animasi berputar/bouncing SweetAlert yang berat */
    .swal2-icon {
        width: 3rem !important;
        height: 3rem !important;
        margin: 0.25rem auto 1rem auto !important;
        border-width: 2px !important;
        animation: none !important;
    }
    .swal2-icon * {
        animation: none !important;
    }
    .swal2-icon.swal2-error {
        border-color: #fecdd3 !important;
        background-color: #fff1f2 !important;
        color: #e11d48 !important;
    }
    .swal2-icon.swal2-success {
        border-color: #bbf7d0 !important;
        background-color: #f0fdf4 !important;
        color: #16a34a !important;
    }
    .swal2-icon.swal2-warning, .swal2-icon.swal2-info {
        border-color: #fde68a !important;
        background-color: #fffbeb !important;
        color: #d97706 !important;
    }
    .swal2-title {
        font-size: 1.125rem !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        letter-spacing: -0.02em !important;
        padding: 0 !important;
        margin-bottom: 0.35rem !important;
    }
    .swal2-html-container {
        font-size: 0.875rem !important;
        font-weight: 500 !important;
        color: #64748b !important;
        line-height: 1.5 !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .swal2-actions {
        margin-top: 1.25rem !important;
        gap: 0.5rem !important;
        width: 100% !important;
    }
    .swal2-confirm {
        flex: 1 1 auto !important;
        background-color: #0f172a !important;
        color: #ffffff !important;
        font-size: 0.8125rem !important;
        font-weight: 700 !important;
        padding: 0.7rem 1.25rem !important;
        border-radius: 0.75rem !important;
        border: none !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        transition: all 0.15s ease !important;
        cursor: pointer !important;
    }
    .swal2-confirm:hover {
        background-color: #000000 !important;
    }
    .swal2-cancel {
        flex: 1 1 auto !important;
        background-color: #f8fafc !important;
        color: #475569 !important;
        font-size: 0.8125rem !important;
        font-weight: 700 !important;
        padding: 0.7rem 1.25rem !important;
        border-radius: 0.75rem !important;
        border: 1px solid #e2e8f0 !important;
        transition: all 0.15s ease !important;
        cursor: pointer !important;
    }
    .swal2-cancel:hover {
        background-color: #f1f5f9 !important;
        color: #1e293b !important;
    }
</style>

<!-- ============================================================
     2. NATIVE ULTRA-FAST MODERN MODAL ALERT (ZERO CDN DELAY)
     ============================================================ -->
<div id="modernAlertBackdrop" 
     class="fixed inset-0 z-[99999] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity duration-150 opacity-0 pointer-events-none"
     aria-modal="true" 
     role="dialog">
    
    <div id="modernAlertCard" 
         class="relative w-full max-w-md bg-white rounded-2xl p-6 sm:p-7 shadow-[0_20px_50px_-10px_rgba(15,23,42,0.25)] border border-slate-100 transform scale-95 transition-all duration-150 ease-out select-none">
        
        <!-- Close Button -->
        <button type="button" 
                id="modernAlertCloseBtn" 
                class="absolute top-4 right-4 p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"
                aria-label="Tutup">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Icon Container -->
        <div id="modernAlertIconWrap" class="w-12 h-12 rounded-2xl flex items-center justify-center mb-4 transition-colors">
            <!-- Icon is injected dynamically -->
        </div>

        <!-- Content -->
        <div class="space-y-1.5">
            <h3 id="modernAlertTitle" class="text-lg font-extrabold text-slate-900 tracking-tight leading-snug">
                <!-- Title -->
            </h3>
            <div id="modernAlertMessage" class="text-sm text-slate-500 font-medium leading-relaxed">
                <!-- Message -->
            </div>
        </div>

        <!-- Action Buttons -->
        <div id="modernAlertActions" class="mt-6 flex flex-col-reverse sm:flex-row items-center gap-2.5">
            <button type="button" 
                    id="modernAlertCancelBtn" 
                    class="hidden w-full sm:w-auto flex-1 py-2.5 px-4 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer">
                Batal
            </button>
            <button type="button" 
                    id="modernAlertConfirmBtn" 
                    class="w-full sm:w-auto flex-1 py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-bold transition-all shadow-xs active:scale-[0.99] cursor-pointer">
                Mengerti
            </button>
        </div>

    </div>
</div>

<script>
(function() {
    // Icons Dictionary
    const ICONS = {
        closed: `
            <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200/80 flex items-center justify-center text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
        `,
        error: `
            <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-200/80 flex items-center justify-center text-rose-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        `,
        success: `
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-200/80 flex items-center justify-center text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        `,
        info: `
            <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-200/80 flex items-center justify-center text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        `,
        warning: `
            <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200/80 flex items-center justify-center text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        `,
        logout: `
            <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-200/80 flex items-center justify-center text-rose-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </div>
        `
    };

    const backdrop = document.getElementById('modernAlertBackdrop');
    const card = document.getElementById('modernAlertCard');
    const iconWrap = document.getElementById('modernAlertIconWrap');
    const titleEl = document.getElementById('modernAlertTitle');
    const msgEl = document.getElementById('modernAlertMessage');
    const confirmBtn = document.getElementById('modernAlertConfirmBtn');
    const cancelBtn = document.getElementById('modernAlertCancelBtn');
    const closeBtn = document.getElementById('modernAlertCloseBtn');

    let activeConfirmHandler = null;
    let activeCancelHandler = null;

    function closeModernModal() {
        if (!backdrop) return;
        backdrop.classList.add('opacity-0', 'pointer-events-none');
        backdrop.classList.remove('opacity-100');
        if (card) {
            card.classList.add('scale-95');
            card.classList.remove('scale-100');
        }
        activeConfirmHandler = null;
        activeCancelHandler = null;
    }

    function openModernModal(options) {
        if (!backdrop || !card) return;

        const type = options.type || 'info';
        const title = options.title || 'Pemberitahuan';
        const message = options.message || '';
        const confirmText = options.confirmText || 'Mengerti';
        const cancelText = options.cancelText || 'Batal';
        const showCancel = options.showCancel || false;

        // Set Icon
        iconWrap.innerHTML = ICONS[type] || ICONS.info;

        // Set Title & Message
        titleEl.textContent = title;
        if (typeof message === 'string' && message.trim().startsWith('<')) {
            msgEl.innerHTML = message;
        } else {
            msgEl.textContent = message;
        }

        // Set Buttons
        confirmBtn.textContent = confirmText;
        if (options.confirmClass) {
            confirmBtn.className = 'w-full sm:w-auto flex-1 py-2.5 px-4 rounded-xl text-white text-xs font-bold transition-all shadow-xs active:scale-[0.99] cursor-pointer ' + options.confirmClass;
        } else if (type === 'logout' || type === 'error') {
            confirmBtn.className = 'w-full sm:w-auto flex-1 py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-all shadow-md shadow-rose-600/20 active:scale-[0.99] cursor-pointer';
        } else {
            confirmBtn.className = 'w-full sm:w-auto flex-1 py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-bold transition-all shadow-xs active:scale-[0.99] cursor-pointer';
        }

        if (showCancel) {
            cancelBtn.textContent = cancelText;
            cancelBtn.classList.remove('hidden');
        } else {
            cancelBtn.classList.add('hidden');
        }

        // Handlers
        activeConfirmHandler = options.onConfirm || null;
        activeCancelHandler = options.onCancel || null;

        // Animate In Snappily
        backdrop.classList.remove('opacity-0', 'pointer-events-none');
        backdrop.classList.add('opacity-100');
        card.classList.remove('scale-95');
        card.classList.add('scale-100');
    }

    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            if (activeConfirmHandler) activeConfirmHandler();
            closeModernModal();
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            if (activeCancelHandler) activeCancelHandler();
            closeModernModal();
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeModernModal);
    }

    if (backdrop) {
        backdrop.addEventListener('click', function(e) {
            if (e.target === backdrop) closeModernModal();
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && backdrop && !backdrop.classList.contains('pointer-events-none')) {
            closeModernModal();
        }
    });

    // Expose global methods
    window.modernAlert = openModernModal;
    window.closeModernAlert = closeModernModal;

    // Helper khusus: Pendaftaran Ditutup Modal
    window.showPendaftaranTutupModal = function(e) {
        if (e && e.preventDefault) e.preventDefault();
        openModernModal({
            type: 'closed',
            title: 'Pendaftaran Periode Ini Sedang Ditutup',
            message: 'Saat ini periode pendaftaran MFLS Ajaran 2027 belum dibuka atau telah berakhir. Silakan pantau jadwal dan linimasa seleksi untuk pengumuman gelombang berikutnya.',
            confirmText: 'Pantau Linimasa Seleksi',
            showCancel: true,
            cancelText: 'Tutup',
            onConfirm: function() {
                const timeline = document.getElementById('timeline');
                if (timeline) {
                    timeline.scrollIntoView({ behavior: 'smooth' });
                } else {
                    window.location.href = '/#timeline';
                }
            }
        });
    };

    // Helper khusus: Modal Konfirmasi Logout / Keluar Akun
    window.confirmLogout = function(e, formElement) {
        if (e && e.preventDefault) e.preventDefault();
        const form = formElement || (e && e.target ? (e.target.tagName === 'FORM' ? e.target : e.target.closest('form')) : null) || document.querySelector('form[action*="logout"]');

        openModernModal({
            type: 'logout',
            title: 'Konfirmasi Keluar Akun',
            message: 'Apakah Anda yakin ingin keluar dari sesi akun Anda? Pastikan seluruh pengerjaan atau data penting telah tersimpan.',
            confirmText: 'Ya, Keluar Akun',
            confirmClass: 'bg-rose-600 hover:bg-rose-700 text-white shadow-md shadow-rose-600/20',
            showCancel: true,
            cancelText: 'Batal',
            onConfirm: function() {
                if (form) {
                    form.dataset.confirmed = 'true';
                    form.submit();
                } else {
                    const fallback = document.querySelector('form[action*="logout"]');
                    if (fallback) {
                        fallback.dataset.confirmed = 'true';
                        fallback.submit();
                    }
                }
            }
        });
    };

    // Interseptor Global: Mencegah logout mendadak tanpa konfirmasi modal
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form && form.tagName === 'FORM') {
            const action = (form.getAttribute('action') || form.action || '').toLowerCase();
            if (action.includes('logout') && !form.dataset.confirmed) {
                e.preventDefault();
                e.stopPropagation();
                window.confirmLogout(e, form);
            }
        }
    }, true);

    // Auto-detect Laravel Flash Sessions on Load
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('error'))
            const rawError = @json(session('error'));
            const isClosedMsg = rawError.toLowerCase().includes('tutup') || rawError.toLowerCase().includes('ditutup');
            
            if (isClosedMsg) {
                window.showPendaftaranTutupModal();
            } else {
                openModernModal({
                    type: 'error',
                    title: 'Perhatian',
                    message: rawError,
                    confirmText: 'Mengerti'
                });
            }
        @elseif(session('success'))
            openModernModal({
                type: 'success',
                title: 'Berhasil',
                message: @json(session('success')),
                confirmText: 'Lanjutkan'
            });
        @elseif(session('warning'))
            openModernModal({
                type: 'warning',
                title: 'Perhatian',
                message: @json(session('warning')),
                confirmText: 'Mengerti'
            });
        @elseif(session('info'))
            openModernModal({
                type: 'info',
                title: 'Informasi',
                message: @json(session('info')),
                confirmText: 'Mengerti'
            });
        @elseif(session('loginError'))
            openModernModal({
                type: 'error',
                title: 'Gagal Masuk',
                message: @json(session('loginError')),
                confirmText: 'Coba Lagi'
            });
        @elseif(isset($errors) && $errors->any())
            const errList = @json($errors->all());
            const errHtml = '<ul class="list-disc pl-4 space-y-1 text-xs text-rose-600 text-left mt-2">' + errList.map(function(e) { return '<li>' + e + '</li>'; }).join('') + '</ul>';
            openModernModal({
                type: 'error',
                title: 'Mohon Periksa Kembali',
                message: errHtml,
                confirmText: 'Perbaiki'
            });
        @endif
    });
})();
</script>
