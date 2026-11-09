@extends('layouts.admin')

@section('content')

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show flash" role="alert">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m8 12 3 3 5-6"/></svg>
        <span>{{ session('success') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show flash-err" role="alert">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif

{{-- ================= HEADER HALAMAN ================= --}}
<div class="page-head">
    <div>
        <h2>Akun Pengguna</h2>
        <p>{{ $userr->total() }} akun terdaftar</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.users.export') }}" class="btn btn-outline-teal">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
            Export Excel
        </a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahAkun">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
            Tambah akun
        </button>
    </div>
</div>

<div class="card data-card">
    {{-- ================= SEARCH ================= --}}
    <form method="GET" action="" class="toolbar">
        <div class="search-field">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" name="search" class="form-control"
                   placeholder="Cari nama, email, karyawan, jabatan, role..."
                   value="{{ request('search') }}" aria-label="Cari akun">
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary">Cari</button>
            @if(request('search'))
                <a href="{{ url()->current() }}" class="btn btn-outline-teal">Reset</a>
            @endif
        </div>
    </form>

    {{-- ================= TABEL ================= --}}
    <div class="table-responsive">
        <table class="table align-middle data-table">
            <thead>
                <tr>
                    <th style="width:60px">No</th>
                    <th>Pengguna</th>
                    <th>Jabatan</th>
                    <th>Ruangan</th>
                    <th>Bidang</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th class="text-end" style="width:130px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($userr as $user)
                    @php
                        $namaUser = $user->karyawan->nama ?? null;
                        $bidangNama = optional(optional($user->karyawan)->bidang)->nama_bidang;
                        $subLabel = match($user->subrole ?? '') {
                            'kabid_pelayanan' => 'Kabid Pelayanan',
                            'kabid_penunjang' => 'Kabid Penunjang',
                            default => null,
                        };
                    @endphp
                    <tr style="--i: {{ $loop->index }}">
                        <td class="text-muted">{{ $userr->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="person">
                                <span class="avatar-sm">{{ strtoupper(mb_substr($namaUser ?? $user->email, 0, 1)) }}</span>
                                <div>
                                    <div class="fw-semibold">{{ $namaUser ?? '-' }}</div>
                                    <div class="small text-muted">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->karyawan->jabatan ?? '-' }}</td>
                        <td>{{ $user->karyawan->ruangan ?? '-' }}</td>
                        <td>
                            {{ $bidangNama ?? '-' }}
                            @if($subLabel)
                                <div class="small text-muted">{{ $subLabel }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                <span class="chip tier-2">{{ ucfirst($user->role) }}</span>
                                @if(!empty($user->role2))
                                    <span class="chip tier-3">+ {{ ucfirst($user->role2) }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if ($user->is_active)
                                <span class="status status-ok">Aktif</span>
                            @else
                                <span class="status status-no">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-end gap-1">
                                <button type="button" class="icon-btn" title="Edit"
                                        data-bs-toggle="modal" data-bs-target="#modalEditUser{{ $user->id }}"
                                        aria-label="Edit akun {{ $namaUser ?? $user->email }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                </button>

                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('AKUN AKAN DIHAPUS PERMANEN. Lanjutkan?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="icon-btn danger" title="Hapus" aria-label="Hapus akun {{ $namaUser ?? $user->email }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="8">
                            <div class="empty">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1"/></svg>
                                <p>Belum ada akun pengguna.</p>
                                @if(request('search'))
                                    <small>Coba ubah kata kunci atau reset pencarian.</small>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="table-foot">
        <div class="text-muted small">
            Menampilkan {{ $userr->firstItem() ?? 0 }} sampai {{ $userr->lastItem() ?? 0 }} dari {{ $userr->total() }} akun
        </div>
        <div>{{ $userr->links('pagination::bootstrap-5') }}</div>
    </div>
</div>

{{-- ================= MODAL EDIT (di luar tabel agar HTML valid) ================= --}}
@foreach ($userr as $user)
<div class="modal fade" id="modalEditUser{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit akun pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Karyawan</label>
                        <input type="text" class="form-control" value="{{ $user->karyawan->nama ?? '-' }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password baru</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah" autocomplete="new-password">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="pemohon"  {{ $user->role == 'pemohon' ? 'selected' : '' }}>Pemohon</option>
                            <option value="penerima" {{ $user->role == 'penerima' ? 'selected' : '' }}>Penerima (pengurus barang)</option>
                            <option value="atasan"   {{ $user->role == 'atasan' ? 'selected' : '' }}>Atasan (kabid)</option>
                            <option value="keuangan" {{ $user->role == 'keuangan' ? 'selected' : '' }}>Keuangan</option>
                            <option value="ppk"      {{ $user->role == 'ppk' ? 'selected' : '' }}>PPK (Pejabat Pembuat Komitmen)</option>
                            <option value="kpa"      {{ $user->role == 'kpa' ? 'selected' : '' }}>KPA (Kuasa Pengguna Anggaran)</option>
                            <option value="direktur" {{ $user->role == 'direktur' ? 'selected' : '' }}>Direktur</option>
                            <option value="admin"    {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>

                    {{-- BIDANG EDIT --}}
                    <div class="mb-3">
                        <label class="form-label">Bidang</label>
                        <select name="bidang_id" class="form-select bidang-edit" data-user="{{ $user->id }}">
                            <option value="">-- Pilih Bidang --</option>
                            @foreach($bidangs as $bidang)
                                <option value="{{ $bidang->id }}"
                                        data-nama="{{ strtolower($bidang->nama_bidang) }}"
                                        {{ old('bidang_id', optional($user->karyawan)->bidang_id) == $bidang->id ? 'selected' : '' }}>
                                    {{ $bidang->nama_bidang }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- SUB BAGIAN EDIT --}}
                    <div class="mb-3 d-none" id="subBagianEdit{{ $user->id }}">
                        <label class="form-label">Sub bagian</label>
                        <select name="sub_bagian" class="form-select">
                            <option value="">-- Pilih Sub Bagian --</option>
                            <option value="kabid_pelayanan" {{ ($user->sub_bagian ?? '') == 'kabid_pelayanan' ? 'selected' : '' }}>Kabid Pelayanan</option>
                            <option value="kabid_penunjang" {{ ($user->sub_bagian ?? '') == 'kabid_penunjang' ? 'selected' : '' }}>Kabid Penunjang</option>
                        </select>
                    </div>

                    <div class="mb-1">
                        <label class="form-label">Status akun</label>
                        <select name="is_active" class="form-select" required>
                            <option value="1" {{ $user->is_active ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ ! $user->is_active ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-teal" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary">Simpan perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- ================= MODAL TAMBAH AKUN ================= --}}
<div class="modal fade" id="modalTambahAkun" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.pengguna.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Tambah akun pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Karyawan</label>
                        <select name="karyawan_id" class="form-select" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach ($karyawans as $k)
                                <option value="{{ $k->id }}">{{ $k->nama }} – {{ $k->ruangan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" autocomplete="new-password" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" id="roleTambah" class="form-select" required>
                            <option value="pemohon">Pemohon</option>
                            <option value="penerima">Penerima</option>
                            <option value="atasan">Atasan</option>
                            <option value="keuangan">Keuangan</option>
                            <option value="ppk">PPK (Pejabat Pembuat Komitmen)</option>
                            <option value="kpa">KPA (Kuasa Pengguna Anggaran)</option>
                            <option value="direktur">Direktur</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="fieldRoleTambahan">
                        <label class="form-label">Jadikan juga sebagai (opsional)</label>
                        <select name="role2" class="form-select" id="selectRoleTambahan">
                            <option value="">-- Tidak ada --</option>
                            <option value="penerima">Penerima (pengurus barang)</option>
                            <option value="ppk">PPK (Pejabat Pembuat Komitmen)</option>
                            <option value="keuangan">Keuangan (Pejabat Keuangan)</option>
                        </select>
                    </div>

                    {{-- BIDANG TAMBAH --}}
                    <div class="mb-3 d-none" id="bidangTambah">
                        <label class="form-label">Bidang</label>
                        <select name="bidang_id" id="bidangSelectTambah" class="form-select">
                            <option value="">-- Pilih Bidang --</option>
                            @foreach($bidangs as $bidang)
                                <option value="{{ $bidang->id }}" data-nama="{{ strtolower($bidang->nama_bidang) }}">
                                    {{ $bidang->nama_bidang }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- SUB BAGIAN TAMBAH --}}
                    <div class="mb-1 d-none" id="subBagianTambah">
                        <label class="form-label">Sub bagian</label>
                        <select name="sub_bagian" class="form-select">
                            <option value="">-- Pilih Sub Bagian --</option>
                            <option value="kabid_pelayanan">Kabid Pelayanan</option>
                            <option value="kabid_penunjang">Kabid Penunjang</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-teal" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

{{-- ================= CSS ================= --}}
@push('styles')
<style>
    /* ---------- Animasi (tipis) ---------- */
    @keyframes rise { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
    @keyframes slideIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: none; } }

    .page-head, .data-card { animation: rise .45s cubic-bezier(.2,.7,.2,1) both; }
    .data-card { animation-delay: .06s; }
    .data-table tbody tr:not(.empty-row) {
        animation: rise .4s cubic-bezier(.2,.7,.2,1) both;
        animation-delay: calc(.12s + var(--i, 0) * 28ms);
    }
    .flash, .flash-err { animation: slideIn .35s ease both; }

    /* ---------- Header ---------- */
    .page-head {
        display: flex; flex-wrap: wrap; align-items: flex-end;
        justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem;
    }
    .page-head h2 { font-size: 1.7rem; font-weight: 800; letter-spacing: -.02em; margin: 0; }
    .page-head p { color: var(--muted); margin: .2rem 0 0; }

    .btn svg { width: 16px; height: 16px; margin-right: .4rem; margin-top: -2px; }
    .btn { transition: background .15s, border-color .15s, color .15s, transform .12s; }
    .btn:active { transform: scale(.98); }
    .btn-outline-teal { background: #fff; border: 1px solid var(--line); color: var(--teal-700); }
    .btn-outline-teal:hover { background: var(--teal-100); border-color: var(--teal-100); color: var(--teal-900); }

    .flash {
        display: flex; align-items: center; gap: .6rem;
        border: 1px solid #b7e3d4; background: #ecfaf4; color: #0b5d45; border-radius: 12px;
    }
    .flash svg { width: 20px; height: 20px; flex: none; }
    .flash-err { border-radius: 12px; }

    /* ---------- Kartu & toolbar ---------- */
    .data-card { overflow: hidden; }
    .toolbar {
        display: flex; flex-wrap: wrap; gap: .6rem;
        padding: 1rem 1.25rem; border-bottom: 1px solid var(--line);
    }
    .search-field { position: relative; flex: 1 1 280px; }
    .search-field svg {
        position: absolute; left: 13px; top: 50%; width: 17px; height: 17px;
        transform: translateY(-50%); color: var(--muted); pointer-events: none;
    }
    .search-field .form-control { padding-left: 40px; }
    .toolbar .form-control, .toolbar .btn { height: 42px; }

    /* ---------- Tabel ---------- */
    .data-table { margin: 0; }
    .data-table thead th {
        background: #f7fafa; color: var(--muted); font-size: .82rem; font-weight: 600;
        padding: .8rem 1.25rem; border-bottom: 1px solid var(--line); white-space: nowrap;
    }
    .data-table tbody td { padding: .85rem 1.25rem; border-bottom: 1px solid #edf2f2; font-size: .93rem; }
    .data-table tbody tr { transition: background .15s; }
    .data-table tbody tr:not(.empty-row):hover { background: #f7fafa; }
    .data-table tbody tr:last-child td { border-bottom: 0; }

    .person { display: flex; align-items: center; gap: .75rem; }
    .avatar-sm {
        width: 38px; height: 38px; border-radius: 50%;
        background: var(--teal-100); color: var(--teal-700);
        display: grid; place-items: center; font-weight: 700; flex: none;
    }

    .chip { display: inline-block; padding: .25rem .7rem; border-radius: 999px; font-size: .8rem; font-weight: 600; white-space: nowrap; }
    .tier-2 { background: var(--teal-100); color: var(--teal-700); }
    .tier-3 { background: #eef2f3; color: #4a5d62; }

    .status { display: inline-flex; align-items: center; gap: .4rem; font-size: .85rem; font-weight: 600; }
    .status::before { content: ""; width: 7px; height: 7px; border-radius: 50%; background: currentColor; }
    .status-ok { color: #0f7a55; }
    .status-no { color: #b42318; }

    .icon-btn {
        width: 34px; height: 34px; border: 0; border-radius: 9px; background: transparent;
        color: var(--muted); display: grid; place-items: center;
        transition: background .15s, color .15s, transform .12s;
    }
    .icon-btn svg { width: 17px; height: 17px; }
    .icon-btn:hover { background: var(--teal-100); color: var(--teal-700); transform: translateY(-1px); }
    .icon-btn.danger:hover { background: #fef3f2; color: #b42318; }
    .icon-btn:focus-visible { outline: 2px solid var(--teal-600); }

    .empty { text-align: center; padding: 2.5rem 1rem; color: var(--muted); }
    .empty svg { width: 44px; height: 44px; color: #aebcbf; margin-bottom: .6rem; }
    .empty p { margin: 0; font-weight: 600; color: var(--ink); }

    .table-foot {
        display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center;
        gap: .75rem; padding: .9rem 1.25rem; border-top: 1px solid var(--line);
    }
    .table-foot .pagination { margin: 0; gap: 4px; }
    .page-link { border: 0; border-radius: 9px !important; color: var(--ink); font-size: .88rem; transition: background .15s; }
    .page-link:hover { background: var(--teal-100); color: var(--teal-700); }
    .page-item.active .page-link { background: var(--teal-700); color: #fff; }
    .page-item.disabled .page-link { background: transparent; color: #aebcbf; }

    /* ---------- Modal ---------- */
    .modal-content { border: 0; border-radius: 18px; box-shadow: 0 24px 60px rgba(11,59,69,.22); }
    .modal-header, .modal-footer { border: 0; padding: 1.25rem 1.5rem; }
    .modal-header { padding-bottom: .5rem; }
    .modal-body { padding: .75rem 1.5rem 1rem; }
    .modal-title { font-weight: 700; font-size: 1.15rem; }
    .modal-body .form-label { font-weight: 600; font-size: .88rem; margin-bottom: .35rem; }
    .modal-body .form-control, .modal-body .form-select { min-height: 44px; }
    .modal-body .form-control[readonly] { background: var(--page); }
    .modal.fade .modal-dialog { transform: translateY(14px) scale(.98); transition: transform .25s cubic-bezier(.2,.7,.2,1); }
    .modal.show .modal-dialog { transform: none; }
    .modal-backdrop.show { opacity: .4; }

    /* Field yang muncul/hilang sesuai pilihan */
    .field-reveal { animation: slideIn .25s ease both; }

    @media (prefers-reduced-motion: reduce) {
        .page-head, .data-card, .flash, .flash-err, .data-table tbody tr, .field-reveal { animation: none !important; }
        .modal.fade .modal-dialog { transition: none; transform: none; }
    }
</style>
@endpush

{{-- ================= SCRIPT ================= --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Tampilkan / sembunyikan field dengan animasi tipis
    function show(el) { el.classList.remove('d-none'); el.classList.add('field-reveal'); }
    function hide(el) { el.classList.add('d-none'); el.classList.remove('field-reveal'); }
    function resetSelect(wrapper) {
        var s = wrapper.querySelector('select');
        if (s) s.value = '';
    }

    /* ---------- MODAL TAMBAH ---------- */
    var roleTambah = document.getElementById('roleTambah');
    var bidangTambah = document.getElementById('bidangTambah');
    var subBagianTambah = document.getElementById('subBagianTambah');
    var fieldRoleTambahan = document.getElementById('fieldRoleTambahan');
    var bidangSelectTambah = document.getElementById('bidangSelectTambah');

    // Bidang muncul kalau role = penerima / atasan
    if (roleTambah && bidangTambah) {
        roleTambah.addEventListener('change', function () {
            if (this.value === 'penerima' || this.value === 'atasan') {
                show(bidangTambah);
            } else {
                hide(bidangTambah);
                resetSelect(bidangTambah);
                if (subBagianTambah) { hide(subBagianTambah); resetSelect(subBagianTambah); }
            }
        });
    }

    // "Jadikan juga sebagai" muncul kalau role = atasan
    if (roleTambah && fieldRoleTambahan) {
        roleTambah.addEventListener('change', function () {
            if (this.value === 'atasan') {
                show(fieldRoleTambahan);
            } else {
                hide(fieldRoleTambahan);
                resetSelect(fieldRoleTambahan);
            }
        });
    }

    // Sub bagian muncul kalau bidang = "alat kesehatan"
    if (bidangSelectTambah && subBagianTambah) {
        bidangSelectTambah.addEventListener('change', function () {
            var opt = this.options[this.selectedIndex];
            var nama = ((opt && opt.getAttribute('data-nama')) || '').trim();
            if (nama === 'alat kesehatan') {
                show(subBagianTambah);
            } else {
                hide(subBagianTambah);
                resetSelect(subBagianTambah);
            }
        });
    }

    /* ---------- MODAL EDIT (banyak modal, ID pakai user id) ---------- */
    document.querySelectorAll('.bidang-edit').forEach(function (select) {
        var subBagian = document.getElementById('subBagianEdit' + select.dataset.user);
        if (!subBagian) return;

        function cekBidang() {
            var opt = select.options[select.selectedIndex];
            var nama = ((opt && opt.getAttribute('data-nama')) || '').trim();
            if (nama === 'alat kesehatan') {
                show(subBagian);
            } else {
                hide(subBagian);
                resetSelect(subBagian);
            }
        }

        select.addEventListener('change', cekBidang);

        var modal = select.closest('.modal');
        if (modal) modal.addEventListener('shown.bs.modal', cekBidang);
    });

    // Alert sukses hilang sendiri setelah 5 detik
    var flash = document.querySelector('.flash');
    if (flash) {
        setTimeout(function () { bootstrap.Alert.getOrCreateInstance(flash).close(); }, 5000);
    }
});
</script>
@endpush