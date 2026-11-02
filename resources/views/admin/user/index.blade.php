@extends('layouts.admin')

@section('content')
<div class="space-y-6 sm:space-y-8">

    <!-- Header Banner -->
    <div class="relative overflow-hidden bg-[#0B1120] text-white p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl shadow-slate-950/20">
        <div class="absolute -top-20 -right-20 w-72 h-72 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/5 border border-white/10 rounded-full text-[10px] font-bold uppercase tracking-widest text-blue-400 mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                    Kontrol Hak Akses &bull; Internal Operator
                </div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-white tracking-tight">
                    Manajemen Pengguna Sistem
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-xl">
                    Kelola akun operasional internal: Admin, Administrasi, Akademik, Mentor, Palugada, dan Dosen.
                </p>
            </div>
            
            @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'palugada' || auth()->user()->email === 'info@beasiswamncu.com'))
            <button onclick="openAddModal()" 
                class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#0870B8] hover:bg-[#065A96] text-white rounded-xl text-xs font-bold transition-all shadow-xs shrink-0 cursor-pointer">
                <span class="iconify text-base" data-icon="solar:user-plus-bold"></span>
                <span>Tambah User Baru</span>
            </button>
            @endif
        </div>
    </div>

    <!-- User Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @foreach($users as $user)
        @php
            $roleBadge = [
                'admin' => ['bg-rose-50 text-rose-700 border-rose-200', 'bg-rose-600'],
                'panitia' => ['bg-amber-50 text-amber-700 border-amber-200', 'bg-amber-600'],
                'akademik' => ['bg-blue-50 text-[#0870B8] border-blue-200', 'bg-[#0870B8]'],
                'mentor' => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'bg-emerald-600'],
                'palugada' => ['bg-purple-50 text-purple-700 border-purple-200', 'bg-purple-600'],
                'dosen' => ['bg-sky-50 text-sky-700 border-sky-200', 'bg-sky-600'],
            ][$user->role] ?? ['bg-slate-100 text-slate-700 border-slate-200', 'bg-slate-600'];
        @endphp
        <div class="bg-white p-6 rounded-3xl shadow-xs border border-slate-200/80 hover:border-blue-500/30 hover:shadow-md transition-all relative overflow-hidden flex flex-col justify-between group">
            <div class="absolute top-0 left-0 right-0 h-1 {{ $roleBadge[1] }}"></div>

            <div>
                <div class="flex items-center gap-3.5 mb-4">
                    <div class="w-11 h-11 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-700 font-black text-base border border-slate-200">
                        {{ strtoupper(substr($user->nama, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-bold text-slate-900 text-sm truncate leading-snug">{{ $user->nama }}</h3>
                        <span class="inline-block px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider border {{ $roleBadge[0] }} mt-0.5">
                            {{ $user->role === 'panitia' ? 'ADMINISTRASI' : strtoupper($user->role) }}
                        </span>
                    </div>
                </div>

                <div class="space-y-1.5 mb-5 text-xs text-slate-500 p-3 rounded-2xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center gap-2 truncate">
                        <span class="iconify text-slate-400 shrink-0" data-icon="solar:letter-bold"></span>
                        <span class="truncate">{{ $user->email }}</span>
                    </div>
                    @if($user->role === 'mentor')
                    <div class="flex items-center gap-2 text-slate-600 font-bold">
                        <span class="iconify text-emerald-500 shrink-0" data-icon="solar:users-group-two-rounded-bold"></span>
                        <span>Membimbing {{ $user->peserta_count }} Peserta</span>
                    </div>
                    @endif
                </div>
            </div>

            @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'palugada' || auth()->user()->email === 'info@beasiswamncu.com'))
            <div class="flex items-center gap-2 pt-3 border-t border-slate-100">
                <button onclick="openEditModal({{ json_encode($user) }})" class="flex-1 py-2 px-3 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-200 transition-colors text-center cursor-pointer">
                    Edit Akun
                </button>
                <button type="button" onclick="confirmQuickReset({{ $user->id }}, '{{ addslashes($user->nama) }}')" class="w-8 h-8 bg-amber-50 text-amber-700 rounded-xl flex items-center justify-center hover:bg-amber-100 border border-amber-200 transition-colors cursor-pointer" title="Reset Password">
                    <span class="iconify text-sm" data-icon="solar:key-minimalistic-bold-duotone"></span>
                </button>
                <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmDeleteUser(this, '{{ addslashes($user->nama) }}')" class="w-8 h-8 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center hover:bg-rose-100 border border-rose-200 transition-colors cursor-pointer" title="Hapus User">
                        <span class="iconify text-sm" data-icon="solar:trash-bin-trash-bold"></span>
                    </button>
                </form>
            </div>
            @endif
        </div>
        @endforeach
    </div>

</div>

<!-- Modal Add/Edit -->
<div id="userModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden z-[101] border border-slate-100">
            <form id="userForm" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="p-6 sm:p-8">
                    <h3 class="text-lg font-black text-slate-900 mb-6 uppercase tracking-tight" id="modalTitle">Tambah User Baru</h3>
                    <div class="space-y-4 text-left">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Lengkap</label>
                            <input type="text" name="nama" id="formNama" required class="w-full bg-slate-50 border border-slate-200 focus:bg-white rounded-xl px-4 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 outline-none focus:border-[#0870B8] focus:ring-4 focus:ring-blue-500/10 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Email Address</label>
                            <input type="email" name="email" id="formEmail" required class="w-full bg-slate-50 border border-slate-200 focus:bg-white rounded-xl px-4 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 outline-none focus:border-[#0870B8] focus:ring-4 focus:ring-blue-500/10 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Password</label>
                            <input type="password" name="password" id="formPassword" placeholder="Minimal 6 karakter" class="w-full bg-slate-50 border border-slate-200 focus:bg-white rounded-xl px-4 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 outline-none focus:border-[#0870B8] focus:ring-4 focus:ring-blue-500/10 transition-all">
                            <p class="text-[10px] text-slate-400 mt-1" id="passwordHint">Isi hanya jika ingin mengganti password.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Role Akses</label>
                            <div class="relative">
                                <select name="role" id="formRole" required class="w-full bg-slate-50 border border-slate-200 focus:bg-white rounded-xl px-4 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 outline-none focus:border-[#0870B8] focus:ring-4 focus:ring-blue-500/10 transition-all appearance-none cursor-pointer">
                                    <option value="admin">Admin (Akses Penuh)</option>
                                    <option value="panitia">Administrasi (Seleksi & Berkas)</option>
                                    <option value="akademik">Akademik (Bank Soal & CBT)</option>
                                    <option value="mentor">Mentor (Penilaian)</option>
                                    <option value="palugada">Palugada (Verifikasi Final)</option>
                                    <option value="dosen">Dosen / Interviewer (Wawancara)</option>
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                    <span class="iconify" data-icon="solar:alt-arrow-down-bold"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 px-6 sm:px-8 py-4 flex justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="closeModal()" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200 transition-colors">Batal</button>
                    <button type="submit" id="submitBtn" class="px-5 py-2.5 bg-[#0870B8] hover:bg-[#065A96] text-white rounded-xl text-xs font-bold transition-all shadow-xs">Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('modalTitle').innerText = 'TAMBAH USER BARU';
    document.getElementById('userForm').action = "{{ route('admin.user.store') }}";
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('formNama').value = '';
    document.getElementById('formEmail').value = '';
    document.getElementById('formPassword').value = '';
    document.getElementById('formPassword').required = true;
    document.getElementById('passwordHint').classList.add('hidden');
    document.getElementById('formRole').value = 'admin';
    document.getElementById('submitBtn').innerText = 'Simpan User';
    document.getElementById('userModal').classList.remove('hidden');
}

function openEditModal(user) {
    document.getElementById('modalTitle').innerText = 'EDIT USER: ' + user.nama;
    document.getElementById('userForm').action = "/admin/user/" + user.id;
    document.getElementById('methodField').innerHTML = '@method("PUT")';
    document.getElementById('formNama').value = user.nama;
    document.getElementById('formEmail').value = user.email;
    document.getElementById('formPassword').value = '';
    document.getElementById('formPassword').required = false;
    document.getElementById('passwordHint').classList.remove('hidden');
    document.getElementById('formRole').value = user.role;
    document.getElementById('submitBtn').innerText = 'Perbarui User';
    document.getElementById('userModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('userModal').classList.add('hidden');
}

function confirmDeleteUser(button, name) {
    Swal.fire({
        title: 'Hapus User?',
        text: `Akun "${name}" akan dihapus permanen dari sistem.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            button.closest('form').submit();
        }
    });
}

function confirmQuickReset(userId, name) {
    Swal.fire({
        title: 'Reset Password',
        text: `Masukkan kata sandi baru untuk ${name}:`,
        input: 'password',
        inputPlaceholder: 'Minimal 6 karakter',
        showCancelButton: true,
        confirmButtonColor: '#0870B8',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Simpan Password',
        cancelButtonText: 'Batal',
        preConfirm: (password) => {
            if (!password || password.length < 6) {
                Swal.showValidationMessage('Password minimal 6 karakter');
                return false;
            }
            return password;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/user/${userId}/reset-password`;
            form.innerHTML = `
                @csrf
                @method('PUT')
                <input type="hidden" name="password" value="${result.value}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endsection
