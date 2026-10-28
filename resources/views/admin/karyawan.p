
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

        <form method="GET" action="{{ route('admin.karyawan') }}" class="mb-3">
    <div class="input-group">
        <input type="text"
               name="search"
               class="form-control"
               placeholder="Cari nama, NIP, jabatan, ruangan..."
               value="{{ request('search') }}">

        <button class="btn btn-primary">
            🔍 Cari
        </button>

        @if(request('search'))
            <a href="{{ route('admin.karyawan') }}" class="btn btn-secondary">
                Reset
            </a>
        @endif
    </div>
</form>

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
                        <td>{{ $loop->iteration }}</td>
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
        <!-- Edit -->
                    <button class="btn btn-sm btn-warning"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEdit{{ $row->id }}">
                        Edit
                    </button>

                    <!-- Hapus -->
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
                        <td colspan="6" class="text-center text-muted">
                            Data karyawan belum ada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

           <div class="d-flex justify-content-between align-items-center mt-3">
    <!-- Info -->
    <div class="text-muted small">
        Menampilkan
        {{ $karyawan->firstItem() }}
        sampai
        {{ $karyawan->lastItem() }}
        dari
        {{ $karyawan->total() }}
        data
    </div>

    <!-- Pagination -->
    <div>
        {{ $karyawan->links('pagination::bootstrap-5') }}
    </div>
</div>

    </div>
</div>

<!-- Modal Tambah Karyawan -->
<div class="modal fade" id="modalTambahKaryawan" tabindex="-1" aria-labelledby="modalTambahKaryawanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form action="{{ route('admin.karyawan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahKaryawanLabel">Tambah Karyawan</h5>
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
                                <option value="Kepala Ruangan">Kepala Ruangan</option>
                                <option value="Staff">Staff</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Ruangan</label>
                            <select name="ruangan" class="form-select" required>
                                <option value="">-- Pilih Ruangan --</option>
                                <option value="CSSD/LAUNDRY">CSSD/LAUNDRY</option>
                                <option value="CODE BLUE">CODE BLUE</option>
                                <option value="CSSD">CSSD</option>
                                <option value="Direktur">DIREKTUR</option>
                                <option value="UNIT HEMODIALISIS">UNIT HEMODIALISIS</option>
                                <option value="Penunjang">PENUNJANG</option>
                                <option value="RUANG FLAMBOYAN">RUANG FLAMBOYAN</option>
                                <option value="IGD">IGD</option>
                                <option value="IPAL">IPAL</option>
                                <option value="INSTALASI HEMODIALISA">INSTALASI HEMODIALISA</option>
                                <option value="INSTALASI BEDAH SENTRAL (IBS)">INSTALASI BEDAH SENTRAL (IBS)</option>
                                <option value="INSTALASI PEMELIHARAAN SARANA">INSTALASI PEMELIHARAAN SARANA</option>
                                <option value="LABORATORIUM">LABORATORIUM</option>
                                <option value="RADIOLOGI">RADIOLOGI</option>
                                <option value="RUANG BOUGENVILE">RUANG BOUGENVILE</option>
                                <option value="RUANG SAKURA">RUANG SAKURA</option>
                                <option value="RUANG CEMPAKA">RUANG CEMPAKA</option>
                                <option value="RUANG MATAHARI">RUANG MATAHARI</option>
                                <option value="RUANG MAWAR">RUANG MAWAR</option>
                                <option value="RUANG MELATI">RUANG MELATI</option>
                                <option value="RUANG ANGGREK">RUANG ANGGREK</option>
                                <option value="RUANG KENANGA">RUANG KENANGA</option>
                                <option value="RUANG DAHLIA">RUANG DAHLIA</option>
                                <option value="RUANG TULIP">RUANG TULIP</option>
                                <option value="Recovery Room (RR)">Recovery Room (RR) </option>
                                <option value="BRONKOSKOPI RUANGAN">BRONKOSKOPI RUANGAN</option>
                                <option value="RUANG GRAHA UTAMA">RUANG GRAHA UTAMA</option>
                                <option value="RUANG TERATAI">RUANG TERATAI</option>
                                <option value="RUANG TRANSIT">RUANG TRANSIT</option>
                                <option value="PPI LAYANAN">PPI LAYANAN</option>
                                <option value="POLI RAWAT JALAN">POLI RAWAT JALAN</option>
                                <option value="KEUANGAN">KEUANGAN</option>
                                <option value="KESEHATAN LINGKUNGAN">KESLING</option>
                                <option value="RUANG MCU">MCU </option>
                                <option value="SIMRS">SIMRS</option>
                                <option value="KOMITE MEDIS">KOMITE MEDIS</option>
                                <option value="LAUNDRY">LAUNDRY</option>
                                <option value="GIZI">GIZI</option>
                                <option value="FARMASI">FARMASI</option>
                                <option value="FLAMBOYAN">FLAMBOYAN</option>
                                <option value="MANAJEMEN">MANAJEMEN</option>

                            </select>
                        </div>
                       

                    </div>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Tanda Tangan (Gambar)</label>
                    <input type="file"
                        name="ttd"
                        class="form-control"
                        accept="image/png,image/jpg,image/jpeg">
                    <small class="text-muted">
                        Format: JPG / PNG (maks 2MB)
                    </small>
                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- modal edit --}}
@foreach ($karyawan as $row)
<div class="modal fade" id="modalEdit{{ $row->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <form action="{{ route('admin.karyawan.update', $row->id) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @php
                $daftarRuangan = [
                    'CSSD/LAUNDRY','CODE BLUE','CSSD','DIREKTUR','UNIT HEMODIALISIS',
                    'RUANG FLAMBOYAN','IPAL','INSTALASI PEMELIHARAAN SARANA','KEUANGAN',
                    'RUANG BOUGENVILE','RUANG SAKURA','RUANG CEMPAKA',
                    'RUANG MATAHARI','RUANG MAWAR','RUANG MELATI',
                    'RUANG ANGGREK','RUANG KENANGA','RUANG DAHLIA',
                    'RUANG TULIP','RUANG RR','BRONKOSKOPI RUANGAN','RADIOLOGI',
                    'RUANG GRAHA UTAMA','RUANG TERATAI','MCU','RUANG TRANSIT',
                    'PPI LAYANAN','KEUANGAN','SIMRS','KOMITE MEDIS',
                    'LAUNDRY','GIZI','FARMASI','MANAJEMEN','POLI RAWAT JALAN','INSTALASI HEMODIALISA','INSTALASI BEDAH SENTRAL (IBS)','LABORATORIUM','KESEHATAN LINGKUNGAN','FLAMBOYAN'
                ];

                $daftarJabatan = [
                    'Administrator',
                    'Kepala Bidang',
                    'Kepala Ruangan',
                    'Staff',
                ];
                @endphp

                <div class="modal-header bg-warning-subtle">
                    <h5 class="modal-title">
                        ✏️ Edit Data Karyawan
                    </h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <!-- Nama -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama</label>
                            <input type="text"
                                   name="nama"
                                   class="form-control"
                                   value="{{ $row->nama }}"
                                   required>
                        </div>

                        <!-- NIP -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NIP</label>
                            <input type="text"
                                   name="nip"
                                   class="form-control"
                                   value="{{ $row->nip }}"
                                   required>
                        </div>

                        <!-- Jabatan -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jabatan</label>
                            <select name="jabatan"
                                    class="form-select"
                                    required>
                                @foreach ($daftarJabatan as $jabatan)
                                    <option value="{{ $jabatan }}"
                                        {{ $row->jabatan === $jabatan ? 'selected' : '' }}>
                                        {{ $jabatan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Ruangan -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ruangan</label>
                            <select name="ruangan"
                                    class="form-select"
                                    required>
                                @foreach ($daftarRuangan as $ruangan)
                                    <option value="{{ $ruangan }}"
                                        {{ $row->ruangan === $ruangan ? 'selected' : '' }}>
                                        {{ $ruangan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- TTD -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                Tanda Tangan
                            </label>

                            @if ($row->ttd)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/ttd/'.$row->ttd) }}"
                                         alt="TTD"
                                         class="img-thumbnail"
                                         style="max-height:120px">
                                </div>
                            @else
                                <p class="text-muted fst-italic">
                                    Belum ada tanda tangan
                                </p>
                            @endif

                            <input type="file"
                                   name="ttd"
                                   class="form-control"
                                   accept="image/*">
                            <small class="text-muted">
                                Kosongkan jika tidak ingin mengganti TTD
                            </small>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit"
                            class="btn btn-warning">
                        💾 Update
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endforeach


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
                <img src="{{ asset('storage/ttd/'.$row->ttd) }}"
                     alt="TTD"
                     class="img-fluid border rounded">
            </div>

        </div>
    </div>
</div>
@endif
@endforeach



<!-- Modal Tambah Bidang -->
{{-- <div class="modal fade" id="modalTambahbidang" tabindex="-1" aria-labelledby="modalTambahbidangLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form action="{{ route('admin.bidang.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahbidangLabel">Tambah Karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Nama Bidang</label>
                            <input type="text" name="nama_bidang" class="form-control" required>
                        </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalDaftarBidang"> Daftar Bidang </button>

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>

            </form>

        </div>
    </div>
</div> --}}

<!-- Modal Tambah Bidang -->
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

                    <!-- Tutup modal pertama dulu -->
                    <button type="button"
                            class="btn btn-info"
                            data-bs-dismiss="modal"
                            data-bs-toggle="modal"
                            data-bs-target="#modalDaftarBidang">
                        Daftar Bidang
                    </button>

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Simpan
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

<!-- Modal Daftar Bidang -->
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
