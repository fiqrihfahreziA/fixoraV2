@extends('layouts.admin')

@section('content')

@php
    $tier = [
        'Administrator'  => 'tier-1',
        'Direktur'       => 'tier-1',
        'Wakil Direktur' => 'tier-1',
        'Kepala Bidang'  => 'tier-2',
        'Koordinator'    => 'tier-2',
        'Kepala Ruangan' => 'tier-2',
    ];
    $daftarJabatan = ['Administrator', 'Direktur', 'Wakil Direktur', 'Kepala Bidang', 'Koordinator', 'Kepala Ruangan', 'Staff'];
@endphp

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show flash" role="alert">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m8 12 3 3 5-6"/></svg>
        <span>{{ session('success') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif

{{-- ================= HEADER HALAMAN ================= --}}
<div class="page-head">
    <div>
        <h2>Data Karyawan</h2>
        <p>{{ $karyawan->total() }} karyawan terdaftar</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-outline-teal" data-bs-toggle="modal" data-bs-target="#modalTambahbidang">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
            Tambah bidang
        </button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKaryawan">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
            Tambah karyawan
        </button>
    </div>
</div>

<div class="card data-card">
    {{-- ================= FORM SEARCH + FILTER RUANGAN ================= --}}
    <form method="GET" action="{{ route('admin.karyawan') }}" class="toolbar">
        <div class="search-field">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" name="search" class="form-control"
                   placeholder="Cari nama, NIP, jabatan..."
                   value="{{ request('search') }}" aria-label="Cari karyawan">
        </div>

        <select name="ruangan" class="form-select filter-select" aria-label="Filter ruangan">
            <option value="">Semua ruangan</option>
            @foreach($daftarRuanganFilter ?? [] as $r)
                <option value="{{ $r }}" {{ request('ruangan') === $r ? 'selected' : '' }}>{{ $r }}</option>
            @endforeach
        </select>

        <div class="d-flex gap-2">
            <button class="btn btn-primary">Cari</button>
            @if(request('search') || request('ruangan'))
                <a href="{{ route('admin.karyawan') }}" class="btn btn-outline-teal">Reset</a>
            @endif
        </div>
    </form>

    {{-- ================= TABEL DATA ================= --}}
    <div class="table-responsive">
        <table class="table align-middle data-table">
            <thead>
                <tr>
                    <th style="width:60px">No</th>
                    <th>Karyawan</th>
                    <th>Jabatan</th>
                    <th>Ruangan</th>
                    <th>Tanda tangan</th>
                    <th class="text-end" style="width:130px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($karyawan as $row)
                    <tr style="--i: {{ $loop->index }}">
                        <td class="text-muted">{{ $loop->iteration + ($karyawan->currentPage() - 1) * $karyawan->perPage() }}</td>
                        <td>
                            <div class="person">
                                <span class="avatar-sm">{{ strtoupper(mb_substr($row->nama, 0, 1)) }}</span>
                                <div>
                                    <div class="fw-semibold">{{ $row->nama }}</div>
                                    <div class="small text-muted">NIP {{ $row->nip }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="chip {{ $tier[$row->jabatan] ?? 'tier-3' }}">{{ $row->jabatan }}</span></td>
                        <td>{{ $row->ruangan }}</td>
                        <td>
                            @if (empty($row->ttd))
                                <span class="status status-no">Belum ada</span>
                            @else
                                <a href="#" class="status status-ok" data-bs-toggle="modal" data-bs-target="#modalTTD{{ $row->id }}">
                                    Sudah ditandatangani
                                </a>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-end gap-1">
                                <button type="button" class="icon-btn" title="Edit"
                                        data-bs-toggle="modal" data-bs-target="#modalEdit{{ $row->id }}" aria-label="Edit {{ $row->nama }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                </button>

                                <form action="{{ route('admin.karyawan.destroy', $row->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="icon-btn danger" title="Hapus" aria-label="Hapus {{ $row->nama }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="6">
                            <div class="empty">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="4"/><path d="M2 21v-1a6 6 0 0 1 6-6h2a6 6 0 0 1 6 6v1M17 11l4 4m0-4-4 4"/></svg>
                                <p>Data karyawan belum ada.</p>
                                @if(request('search') || request('ruangan'))
                                    <small>Coba ubah kata kunci atau reset filter.</small>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ================= PAGINATION ================= --}}
    <div class="table-foot">
        <div class="text-muted small">
            Menampilkan {{ $karyawan->firstItem() ?? 0 }} sampai {{ $karyawan->lastItem() ?? 0 }} dari {{ $karyawan->total() }} data
        </div>
        <div>{{ $karyawan->links('pagination::bootstrap-5') }}</div>
    </div>
</div>

{{-- ================= MODAL TAMBAH KARYAWAN ================= --}}
<div class="modal fade" id="modalTambahKaryawan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.karyawan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Tambah karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jabatan</label>
                            <select name="jabatan" class="form-select" required>
                                <option value="">-- Pilih Jabatan --</option>
                                <option value="Administrator">Admin</option>
                                <option value="Direktur">Direktur</option>
                                <option value="Wakil Direktur">Wadir</option>
                                <option value="Kepala Bidang">Kepala Bidang</option>
                                <option value="Koordinator">Koordinator</option>
                                <option value="Kepala Ruangan">Kepala Ruangan</option>
                                <option value="Staff">Staff</option>
                            </select>
                        </div>

                        {{-- DROPDOWN RUANGAN (SEARCHABLE) --}}
                        <div class="col-md-6">
                            <label class="form-label">Ruangan</label>
                            <select name="ruangan" id="ruanganTambah" class="form-select" required>
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach($daftarRuanganFilter ?? [] as $r)
                                    <option value="{{ $r }}">{{ $r }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Tanda tangan (gambar)</label>
                            <input type="file" name="ttd" class="form-control" accept="image/png,image/jpg,image/jpeg">
                            <small class="text-muted">Format JPG atau PNG, maksimal 2 MB.</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-teal" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================= MODAL EDIT ================= --}}
@foreach ($karyawan as $row)
<div class="modal fade" id="modalEdit{{ $row->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form action="{{ route('admin.karyawan.update', $row->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit data karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" value="{{ $row->nama }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control" value="{{ $row->nip }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jabatan</label>
                            <select name="jabatan" class="form-select" required>
                                @foreach ($daftarJabatan as $jabatan)
                                    <option value="{{ $jabatan }}" {{ $row->jabatan === $jabatan ? 'selected' : '' }}>{{ $jabatan }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- DROPDOWN RUANGAN EDIT (SEARCHABLE) --}}
                        <div class="col-md-6">
                            <label class="form-label">Ruangan</label>
                            <select name="ruangan" class="form-select select-ruangan" required>
                                @foreach($daftarRuanganFilter ?? [] as $r)
                                    <option value="{{ $r }}" {{ $row->ruangan === $r ? 'selected' : '' }}>{{ $r }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Tanda tangan</label>
                            @if ($row->ttd)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/ttd/'.$row->ttd) }}" alt="TTD" class="img-thumbnail" style="max-height:120px">
                                </div>
                            @else
                                <p class="text-muted fst-italic mb-2">Belum ada tanda tangan.</p>
                            @endif

                            <input type="file" name="ttd" class="form-control" accept="image/*">
                            <small class="text-muted">Kosongkan jika tidak ingin mengganti tanda tangan.</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-teal" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- ================= MODAL LIHAT TTD ================= --}}
@foreach ($karyawan as $row)
@if ($row->ttd)
<div class="modal fade" id="modalTTD{{ $row->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tanda tangan {{ $row->nama }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ asset('storage/ttd/'.$row->ttd) }}" alt="TTD {{ $row->nama }}" class="img-fluid border rounded">
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

{{-- ================= MODAL TAMBAH BIDANG ================= --}}
<div class="modal fade" id="modalTambahbidang" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.bidang.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah bidang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama bidang</label>
                            <input type="text" name="nama_bidang" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-teal me-auto" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#modalDaftarBidang">
                        Daftar bidang
                    </button>
                    <button type="button" class="btn btn-outline-teal" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================= MODAL DAFTAR BIDANG ================= --}}
<div class="modal fade" id="modalDaftarBidang" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Daftar bidang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <table class="table data-table mb-0">
                    <thead>
                        <tr>
                            <th style="width:60px">No</th>
                            <th>Nama bidang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bidangs as $item)
                        <tr>
                            <td class="text-muted">{{ $loop->iteration }}</td>
                            <td>{{ $item->nama_bidang }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- ================= CSS ================= --}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
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
    .flash { animation: slideIn .35s ease both; }

    /* ---------- Header halaman ---------- */
    .page-head {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }
    .page-head h2 { font-size: 1.7rem; font-weight: 800; letter-spacing: -.02em; margin: 0; }
    .page-head p { color: var(--muted); margin: .2rem 0 0; }

    .btn svg { width: 16px; height: 16px; margin-right: .4rem; margin-top: -2px; }
    .btn { transition: background .15s, border-color .15s, color .15s, transform .12s; }
    .btn:active { transform: scale(.98); }
    .btn-outline-teal {
        background: #fff;
        border: 1px solid var(--line);
        color: var(--teal-700);
    }
    .btn-outline-teal:hover { background: var(--teal-100); border-color: var(--teal-100); color: var(--teal-900); }

    .flash {
        display: flex;
        align-items: center;
        gap: .6rem;
        border: 1px solid #b7e3d4;
        background: #ecfaf4;
        color: #0b5d45;
        border-radius: 12px;
    }
    .flash svg { width: 20px; height: 20px; flex: none; }

    /* ---------- Kartu & toolbar ---------- */
    .data-card { overflow: hidden; }
    .toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: .6rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--line);
    }
    .search-field { position: relative; flex: 1 1 260px; }
    .search-field svg {
        position: absolute; left: 13px; top: 50%;
        width: 17px; height: 17px;
        transform: translateY(-50%);
        color: var(--muted);
        pointer-events: none;
    }
    .search-field .form-control { padding-left: 40px; }
    .toolbar .form-control, .toolbar .form-select, .toolbar .btn { height: 42px; }
    .filter-select { flex: 0 1 240px; }

    /* ---------- Tabel ---------- */
    .data-table { margin: 0; --bs-table-hover-bg: #f7fafa; }
    .data-table thead th {
        background: #f7fafa;
        color: var(--muted);
        font-size: .82rem;
        font-weight: 600;
        padding: .8rem 1.25rem;
        border-bottom: 1px solid var(--line);
        white-space: nowrap;
    }
    .data-table tbody td {
        padding: .85rem 1.25rem;
        border-bottom: 1px solid #edf2f2;
        font-size: .93rem;
    }
    .data-table tbody tr { transition: background .15s; }
    .data-table tbody tr:not(.empty-row):hover { background: #f7fafa; }
    .data-table tbody tr:last-child td { border-bottom: 0; }

    .person { display: flex; align-items: center; gap: .75rem; }
    .avatar-sm {
        width: 38px; height: 38px;
        border-radius: 50%;
        background: var(--teal-100);
        color: var(--teal-700);
        display: grid; place-items: center;
        font-weight: 700;
        flex: none;
    }

    .chip {
        display: inline-block;
        padding: .25rem .7rem;
        border-radius: 999px;
        font-size: .8rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .tier-1 { background: var(--teal-900); color: #fff; }
    .tier-2 { background: var(--teal-100); color: var(--teal-700); }
    .tier-3 { background: #eef2f3; color: #4a5d62; }

    .status {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        font-size: .85rem;
        font-weight: 600;
        text-decoration: none;
    }
    .status::before { content: ""; width: 7px; height: 7px; border-radius: 50%; background: currentColor; }
    .status-ok { color: #0f7a55; }
    .status-ok:hover { text-decoration: underline; }
    .status-no { color: #b42318; font-weight: 500; }

    .icon-btn {
        width: 34px; height: 34px;
        border: 0;
        border-radius: 9px;
        background: transparent;
        color: var(--muted);
        display: grid; place-items: center;
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
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: .75rem;
        padding: .9rem 1.25rem;
        border-top: 1px solid var(--line);
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
    .modal.fade .modal-dialog { transform: translateY(14px) scale(.98); transition: transform .25s cubic-bezier(.2,.7,.2,1); }
    .modal.show .modal-dialog { transform: none; }
    .modal-backdrop.show { opacity: .4; }

    /* ---------- Select2 mengikuti gaya form ---------- */
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 44px;
        border-radius: 10px;
        border-color: var(--line);
        display: flex;
        align-items: center;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: var(--teal-600);
        box-shadow: 0 0 0 4px rgba(17,128,137,.15);
    }
    .select2-container--bootstrap-5 .select2-dropdown { border-color: var(--line); border-radius: 12px; overflow: hidden; }
    .select2-container--bootstrap-5 .select2-results__option--highlighted { background: var(--teal-100) !important; color: var(--teal-900) !important; }
    .select2-container--bootstrap-5 .select2-results__option--selected { background: var(--teal-700) !important; color: #fff !important; }

    @media (prefers-reduced-motion: reduce) {
        .page-head, .data-card, .flash, .data-table tbody tr { animation: none !important; }
        .modal.fade .modal-dialog { transition: none; transform: none; }
    }
</style>
@endpush

{{-- ================= SCRIPT SELECT2 ================= --}}
@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {
    if (typeof $.fn.select2 === 'undefined') {
        console.error('Select2 gagal dimuat. Periksa koneksi internet atau CDN.');
        return;
    }

    // 1. Modal tambah
    $('#modalTambahKaryawan').on('shown.bs.modal', function () {
        if (!$('#ruanganTambah').hasClass('select2-hidden-accessible')) {
            $('#ruanganTambah').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modalTambahKaryawan'),
                placeholder: '-- Cari / Pilih Ruangan --',
                allowClear: true,
                width: '100%'
            });
        }
    });

    // 2. Semua modal edit
    $('[id^="modalEdit"]').each(function () {
        var modal = $(this);
        modal.on('shown.bs.modal', function () {
            var select = modal.find('.select-ruangan');
            if (!select.hasClass('select2-hidden-accessible')) {
                select.select2({
                    theme: 'bootstrap-5',
                    dropdownParent: modal,
                    width: '100%'
                });
            }
        });
    });

    // Alert sukses hilang sendiri setelah 5 detik
    var flash = document.querySelector('.flash');
    if (flash) {
        setTimeout(function () {
            bootstrap.Alert.getOrCreateInstance(flash).close();
        }, 5000);
    }
});
</script>
@endpush