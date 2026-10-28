@extends('layouts.admin')

@section('content')

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<h2 class="mb-4">Data Karyawan</h2>

<div class="card shadow-sm">
    <div class="card-body">

        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKaryawan" style="margin-bottom:10px">
            + Tambah Karyawan
        </button>

        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahbidang" style="margin-bottom:10px">
            + Bidang
        </button>

        {{-- ================= FORM SEARCH + FILTER RUANGAN ================= --}}
        <form method="GET" action="{{ route('admin.karyawan') }}" class="mb-3">
            <div class="row g-2">
                {{-- Input Keyword --}}
                <div class="col-md-5">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Cari nama, NIP, jabatan..."
                           value="{{ request('search') }}">
                </div>

                {{-- Filter Ruangan --}}
                <div class="col-md-4">
                    <select name="ruangan" class="form-select">
                        <option value="">-- Semua Ruangan --</option>
                        @foreach($daftarRuanganFilter ?? [] as $r)
                            <option value="{{ $r }}" {{ request('ruangan') === $r ? 'selected' : '' }}>
                                {{ $r }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tombol Aksi --}}
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary w-100">🔍 Cari</button>
                    @if(request('search') || request('ruangan'))
                        <a href="{{ route('admin.karyawan') }}" class="btn btn-secondary">Reset</a>
                    @endif
                </div>
            </div>
        </form>

        {{-- ================= TABEL DATA ================= --}}
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-primary">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Jabatan</th>
                    <th>Ruangan</th>
                    <th>Tanda Tangan</th>
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($karyawan as $row)
                    <tr>
                        <td>{{ $loop->iteration + ($karyawan->currentPage() - 1) * $karyawan->perPage() }}</td>
                        <td>{{ $row->nama }}</td>
                        <td>{{ $row->nip }}</td>
                        <td>{{ $row->jabatan }}</td>
                        <td>{{ $row->ruangan }}</td>
                        <td>
                            @if (empty($row->ttd))
                                <span class="text-danger fst-italic">
                                    Belum ada tanda tangan
                                </span>
                            @else
                                <a href="#"
                                class="text-success text-decoration-underline"
                                data-bs-toggle="modal"
                                data-bs-target="#modalTTD{{ $row->id }}">
                                    Sudah ditandatangani
                                </a>
                            @endif
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEdit{{ $row->id }}">
                                Edit
                            </button>

                            <form action="{{ route('admin.karyawan.destroy', $row->id) }}"
                                method="POST"
                                class="d-inline"
                                onsubmit="return confirm('Yakin hapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            Data karyawan belum ada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- ================= PAGINATION ================= --}}
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Menampilkan
                {{ $karyawan->firstItem() ?? 0 }}
                sampai
                {{ $karyawan->lastItem() ?? 0 }}
                dari
                {{ $karyawan->total() }}
                data
            </div>
            <div>
                {{ $karyawan->links('pagination::bootstrap-5') }}
            </div>
        </div>

    </div>
</div>

{{-- ================= MODAL TAMBAH KARYAWAN ================= --}}
<div class="modal fade" id="modalTambahKaryawan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form action="{{ route('admin.karyawan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                            <label class="form-label">Tanda Tangan (Gambar)</label>
                            <input type="file" name="ttd" class="form-control" accept="image/png,image/jpg,image/jpeg">
                            <small class="text-muted">Format: JPG / PNG (maks 2MB)</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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

                <div class="modal-header bg-warning-subtle">
                    <h5 class="modal-title">✏️ Edit Data Karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama</label>
                            <input type="text" name="nama" class="form-control" value="{{ $row->nama }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NIP</label>
                            <input type="text" name="nip" class="form-control" value="{{ $row->nip }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jabatan</label>
                            <select name="jabatan" class="form-select" required>
                                @php
                                    $daftarJabatan = ['Administrator', 'Direktur', 'Wakil Direktur', 'Kepala Bidang', 'Kepala Ruangan', 'Staff'];
                                @endphp
                                @foreach ($daftarJabatan as $jabatan)
                                    <option value="{{ $jabatan }}" {{ $row->jabatan === $jabatan ? 'selected' : '' }}>
                                        {{ $jabatan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- DROPDOWN RUANGAN EDIT (SEARCHABLE) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ruangan</label>
                            <select name="ruangan" class="form-select select-ruangan" required>
                                @foreach($daftarRuanganFilter ?? [] as $r)
                                    <option value="{{ $r }}" {{ $row->ruangan === $r ? 'selected' : '' }}>
                                        {{ $r }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Tanda Tangan</label>
                            @if ($row->ttd)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/ttd/'.$row->ttd) }}" alt="TTD" class="img-thumbnail" style="max-height:120px">
                                </div>
                            @else
                                <p class="text-muted fst-italic">Belum ada tanda tangan</p>
                            @endif

                            <input type="file" name="ttd" class="form-control" accept="image/*">
                            <small class="text-muted">Kosongkan jika tidak ingin mengganti TTD</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">💾 Update</button>
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
                <h5 class="modal-title">Tanda Tangan - {{ $row->nama }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ asset('storage/ttd/'.$row->ttd) }}" alt="TTD" class="img-fluid border rounded">
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
                    <h5 class="modal-title">Tambah Bidang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Bidang</label>
                            <input type="text" name="nama_bidang" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#modalDaftarBidang">
                        Daftar Bidang
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
                <h5 class="modal-title">Daftar Bidang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Bidang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bidangs as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
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

{{-- ================= CSS SELECT2 ================= --}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

{{-- ================= SCRIPT SELECT2 ================= --}}
@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {
    // Cek kesiapan Select2
    if (typeof $.fn.select2 === 'undefined') {
        console.error('❌ Select2 gagal dimuat! Periksa koneksi internet atau CDN.');
        return;
    }

    // 1. Inisialisasi untuk Modal Tambah
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

    // 2. Inisialisasi untuk Semua Modal Edit
    $('[id^="modalEdit"]').each(function () {
        var modal = $(this);
        modal.on('shown.bs.modal', function () {
            var select = modal.find('.select-ruangan');
            if (!select.hasClass('select2-hidden-accessible')) {
                select.select2({
                    theme: 'bootstrap-5',
                    dropdownParent: modal,
                    placeholder: '-- Cari / Pilih Ruangan --',
                    allowClear: true,
                    width: '100%'
                });
            }
        });
    });
});
</script>
@endpush