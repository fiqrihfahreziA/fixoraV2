@extends('layouts.pengadaan.ppk')

@section('content')

{{-- ================= FLASH MESSAGE ================= --}}
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-3">
    <strong><i class="bi bi-exclamation-triangle me-1"></i>Validasi gagal:</strong>
    <ul class="mb-0 mt-2">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-3">
    <i class="bi bi-x-circle me-1"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3">
    <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-file-earmark-text text-primary me-2"></i>
            Detail Pengajuan - {{ $pengajuan->no_pengajuan ?? 'N/A' }}
        </h4>
        <span class="text-muted">Pengajuan oleh: {{ $pengajuan->karyawan->nama ?? '-' }}</span>
    </div>
    <a href="{{ route('ppk.dashboard') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

{{-- STATUS --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    @php
                        $statusBadge = [
                            'draft'                 => 'secondary',
                            'diajukan'              => 'warning',
                            'revisi'                => 'warning',
                            'disetujui_koordinator' => 'info',
                            'perlu_survei'          => 'orange',
                            'disetujui_kabid'       => 'primary',
                            'sesuai_standard'       => 'teal',
                            'keuangan'              => 'teal',
                            'menunggu ppk'          => 'purple',
                            'menunggu_ppk'          => 'purple',
                            'menunggu ppa'          => 'purple',
                            'menunggu_ppa'          => 'purple',
                            'menunggu kpa'          => 'purple',
                            'menunggu_keuangan'     => 'purple',
                            'dipertimbangkan'       => 'info',
                            'disetujui'             => 'success',
                            'ditolak'               => 'danger',
                            'ditolak_penerima'      => 'danger',
                            'ditolak_keuangan'      => 'danger',
                        ][$pengajuan->status ?? 'draft'] ?? 'secondary';

                        $statusLabel = [
                            'draft'                 => 'Draft',
                            'diajukan'              => 'Diajukan',
                            'revisi'                => 'Revisi',
                            'disetujui_koordinator' => 'Disetujui Koordinator',
                            'perlu_survei'          => 'Perlu Survei',
                            'disetujui_kabid'       => 'Disetujui Kabid',
                            'sesuai_standard'       => 'Sesuai Standard',
                            'keuangan'              => 'Keuangan',
                            'menunggu ppk'          => 'Menunggu PPK',
                            'menunggu_ppk'          => 'Menunggu PPK',
                            'menunggu ppa'          => 'Menunggu PPA',
                            'menunggu_ppa'          => 'Menunggu PPA',
                            'menunggu kpa'          => 'Menunggu KPA',
                            'menunggu_keuangan'     => 'Menunggu Keuangan',
                            'dipertimbangkan'       => 'Dipertimbangkan',
                            'disetujui'             => 'Disetujui',
                            'ditolak'               => 'Ditolak',
                            'ditolak_penerima'      => 'Ditolak Penerima',
                            'ditolak_keuangan'      => 'Ditolak Keuangan',
                        ][$pengajuan->status ?? 'draft'] ?? $pengajuan->status ?? 'Unknown';
                    @endphp
                    <span class="badge bg-{{ $statusBadge }} fs-6 px-3 py-2">
                        <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>
                        {{ $statusLabel }}
                    </span>
                    <small class="text-muted">
                        <i class="bi bi-calendar3 me-1"></i>
                        Diajukan: {{ isset($pengajuan->created_at) ? date('d M Y H:i', strtotime($pengajuan->created_at)) : '-' }}
                    </small>
                    @if(isset($pengajuan->disetujui_kabid_at) && $pengajuan->disetujui_kabid_at)
                    <small class="text-success">
                        <i class="bi bi-check-circle me-1"></i>
                        Disetujui Atasan: {{ date('d M Y H:i', strtotime($pengajuan->disetujui_kabid_at)) }}
                    </small>
                    @endif
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <span class="fw-bold">Total Pengajuan: </span>
                <span class="text-primary fw-bold fs-5">
                    Rp {{ number_format($pengajuan->total_pengajuan ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>

        @if(isset($pengajuan->catatan_ppk) && $pengajuan->catatan_ppk)
        <div class="mt-3 p-3 bg-light rounded">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-chat-quote text-primary mt-1"></i>
                <div>
                    <small class="text-muted fw-semibold">Catatan dari Atasan (PPK):</small>
                    <p class="mb-0 text-secondary">{{ $pengajuan->catatan_ppk }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- PANEL VERIFIKASI --}}
@if(in_array($pengajuan->log_acc_kabidPenunjang ?? '', ['menunggu ppk', 'disetujui oleh kabid penunjang']))
<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
    <div class="card-body p-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-3 text-white">
                    <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0"
                         style="width: 48px; height: 48px; background: rgba(255,255,255,.15);">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                    <div>
                        <div class="text-white-50 small text-uppercase fw-semibold" style="letter-spacing: 1px; font-size: .7rem;">
                            Tahap Verifikasi
                        </div>
                        <h5 class="mb-0 fw-bold">Verifikasi PPK</h5>
                        <small class="text-white-50">Periksa seluruh data sebelum mengambil keputusan</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 text-lg-end">
                <div class="text-white-50 small">Total Diajukan</div>
                <div class="text-white fw-bold fs-4 mb-2">
                    Rp {{ number_format($pengajuan->total_pengajuan ?? 0, 0, ',', '.') }}
                </div>
                <div class="d-flex gap-2 flex-wrap justify-content-lg-end">
                    <button type="button" class="btn btn-light btn-sm px-3" onclick="openDrawer('menunggu_keuangan')">
                        <i class="bi bi-check-circle text-success me-1"></i> Setujui
                    </button>
                    <button type="button" class="btn btn-light btn-sm px-3" onclick="openDrawer('dipertimbangkan')">
                        <i class="bi bi-clipboard-check text-info me-1"></i> Dipertimbangkan
                    </button>
                    <button type="button" class="btn btn-light btn-sm px-3" onclick="openDrawer('ditolak')">
                        <i class="bi bi-x-circle text-danger me-1"></i> Tolak
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- INFO GRID --}}
<div class="row">
    <div class="col-lg-8">

        {{-- Informasi Pengaju --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-person-badge me-2 text-primary"></i>Informasi Pengaju
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="label">Nama Pengaju</div>
                            <div class="value">{{ $pengajuan->karyawan->nama ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="label">Jabatan</div>
                            <div class="value">{{ $pengajuan->karyawan->jabatan ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="label">Bidang</div>
                            <div class="value">{{ $pengajuan->bidang->nama_bidang ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="label">Instalasi</div>
                            <div class="value">{{ $pengajuan->instalasi ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Informasi Penerima --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom border-success">
                <h6 class="fw-bold mb-0 text-success">
                    <i class="bi bi-person-check me-2"></i>Informasi Penerima (User)
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-item bg-success bg-opacity-10">
                            <div class="label">Nama Penerima</div>
                            <div class="value">{{ $pengajuan->penerima->nama ?? 'Belum ditentukan' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item bg-success bg-opacity-10">
                            <div class="label">Jabatan Penerima</div>
                            <div class="value">{{ $pengajuan->penerima->jabatan ?? 'Belum ditentukan' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item bg-success bg-opacity-10">
                            <div class="label">Bidang Penerima</div>
                            <div class="value">{{ $pengajuan->penerima->bidang->nama_bidang ?? 'Belum ditentukan' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item bg-success bg-opacity-10">
                            <div class="label">Status Penerimaan</div>
                            <div class="value">
                                @if(($pengajuan->status_penerimaan ?? '') == 'diterima')
                                    <span class="badge bg-success">Sudah Menerima</span>
                                @elseif(($pengajuan->status_penerimaan ?? '') == 'ditolak')
                                    <span class="badge bg-danger">Ditolak</span>
                                @else
                                    <span class="badge bg-warning">Menunggu Konfirmasi</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alasan & Manfaat --}}
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-bold text-primary mb-2">
                            <i class="bi bi-clipboard-check me-2"></i>Alasan Justifikasi
                        </h6>
                        <p class="mb-0 text-secondary" style="word-wrap: break-word; max-height: 150px; overflow-y: auto;">
                            {{ $pengajuan->alasan_justifikasi ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-bold text-success mb-2">
                            <i class="bi bi-star-fill me-2"></i>Manfaat
                        </h6>
                        <p class="mb-0 text-secondary" style="word-wrap: break-word; max-height: 150px; overflow-y: auto;">
                            {{ $pengajuan->manfaat ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dampak --}}
        <div class="card border-warning shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold text-warning mb-2">
                    <i class="bi bi-exclamation-triangle me-2"></i>Dampak Jika Tidak Dilaksanakan
                </h6>
                <p class="mb-0 text-secondary">{{ $pengajuan->dampak ?? '-' }}</p>
            </div>
        </div>

        {{-- Kondisi Barang Lama --}}
        @if(isset($pengajuan->kondisi_barang_lama) && $pengajuan->kondisi_barang_lama)
        <div class="card border-info shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold text-info mb-2">
                    <i class="bi bi-box-seam me-2"></i>Kondisi Barang Lama
                </h6>
                <div class="d-flex gap-3">
                    <span class="badge bg-warning text-dark">{{ $pengajuan->kondisi_barang_lama }}</span>
                    <small class="text-muted">{{ $pengajuan->ket_barang_lama ?? '-' }}</small>
                </div>
            </div>
        </div>
        @endif

        {{-- Daftar Barang --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent">
                <h6 class="fw-bold mb-0"><i class="bi bi-list-check me-2"></i>Daftar Barang yang Diajukan</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Barang</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuan->items ?? [] as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->nama_barang ?? '-' }}</strong>
                                    <br>
                                    <small class="text-muted">Spesifikasi: {{ $item->spesifikasi ?? '-' }}</small>
                                </td>
                                <td class="text-center">{{ $item->jumlah ?? 0 }} {{ $item->satuan ?? 'Unit' }}</td>
                                <td class="text-end">Rp {{ number_format($item->harga_satuan ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold text-primary">
                                    Rp {{ number_format($item->harga ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Tidak ada data barang</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total Pengajuan</td>
                                <td class="text-end fw-bold text-primary">
                                    Rp {{ number_format($pengajuan->total_pengajuan ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- PDF Data Kerusakan --}}
        @if(isset($pengajuan->data_kerusakan) && $pengajuan->data_kerusakan)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="fw-bold mb-0 text-danger">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Dokumen Pendukung
                </h6>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-3">
                            <div style="font-size: 40px; color: #dc3545;">
                                <i class="bi bi-file-earmark-pdf-fill"></i>
                            </div>
                            <div>
                                <p class="fw-semibold mb-1">{{ basename($pengajuan->data_kerusakan) }}</p>
                                <small class="text-muted">
                                    <i class="bi bi-calendar me-1"></i>
                                    Diupload: {{ isset($pengajuan->created_at) ? date('d M Y H:i', strtotime($pengajuan->created_at)) : '-' }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ asset('storage/' . $pengajuan->data_kerusakan) }}" 
                           target="_blank" 
                           class="btn btn-danger btn-sm rounded-pill px-3">
                            <i class="bi bi-eye me-1"></i> Lihat
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Kolom Kanan --}}
    <div class="col-lg-4">

        {{-- Ringkasan --}}
        <div class="card border-0 shadow-sm bg-light mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-info-circle text-primary me-2"></i>Ringkasan Pengajuan
                </h6>
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                    <span class="text-muted">No. Pengajuan</span>
                    <span class="fw-semibold">{{ $pengajuan->no_pengajuan ?? '-' }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                    <span class="text-muted">Pengaju</span>
                    <span class="fw-semibold">{{ $pengajuan->karyawan->nama ?? '-' }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                    <span class="text-muted">Penerima</span>
                    <span class="fw-semibold">{{ $pengajuan->penerima->nama ?? 'Belum ditentukan' }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Total Pengajuan</span>
                    <span class="text-primary fw-bold">Rp {{ number_format($pengajuan->total_pengajuan ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Timeline --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-clock-history me-2 text-primary"></i>Alur Persetujuan
                </h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-icon bg-warning">
                            <i class="bi bi-file-earmark-text text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="fw-bold mb-0">1. Pengajuan</h6>
                            <small class="text-muted">{{ isset($pengajuan->created_at) ? date('d M Y H:i', strtotime($pengajuan->created_at)) : '-' }}</small>
                            <p class="mb-0 small text-muted">{{ $pengajuan->karyawan->nama ?? '-' }} mengajukan permohonan</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-icon {{ isset($pengajuan->ppk_at) && $pengajuan->ppk_at ? 'bg-success' : 'bg-secondary' }}">
                            <i class="bi bi-person-up text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="fw-bold mb-0">2. Verifikasi PPK</h6>
                            <small class="text-muted">{{ $pengajuan->atasan->nama ?? 'Atasan' }}</small>
                            @if(isset($pengajuan->ppk_at) && $pengajuan->ppk_at)
                                <p class="mb-0 small text-success">
                                    <i class="bi bi-check-circle"></i> Disetujui 
                                    {{ date('d M Y', strtotime($pengajuan->ppk_at)) }}
                                </p>
                            @else
                                <p class="mb-0 small text-warning">⏳ Menunggu verifikasi</p>
                            @endif
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-icon {{ ($pengajuan->status_penerimaan ?? '') == 'diterima' ? 'bg-success' : 'bg-secondary' }}">
                            <i class="bi bi-person-check text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="fw-bold mb-0">3. Konfirmasi Penerima</h6>
                            <small class="text-muted">{{ $pengajuan->penerima->nama ?? 'Penerima' }}</small>
                            @if(($pengajuan->status_penerimaan ?? '') == 'diterima')
                                <p class="mb-0 small text-success">
                                    <i class="bi bi-check-circle"></i> Telah menerima barang
                                </p>
                            @else
                                <p class="mb-0 small text-warning">⏳ Menunggu konfirmasi penerima</p>
                            @endif
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-icon {{ ($pengajuan->status ?? '') == 'disetujui' ? 'bg-success' : 'bg-secondary' }}">
                            <i class="bi bi-cash-stack text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="fw-bold mb-0">4. Verifikasi Keuangan</h6>
                            <small class="text-muted">Petugas Keuangan</small>
                            @if(($pengajuan->status ?? '') == 'disetujui')
                                <p class="mb-0 small text-success">
                                    <i class="bi bi-check-circle"></i> Telah diverifikasi
                                </p>
                            @else
                                <p class="mb-0 small text-warning">⏳ Menunggu verifikasi</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panduan --}}
        <div class="card border-0 shadow-sm bg-info bg-opacity-10">
            <div class="card-body">
                <h6 class="fw-bold mb-2">
                    <i class="bi bi-lightbulb text-info me-2"></i>Panduan Keputusan
                </h6>
                <ul class="small text-muted mb-0 ps-3">
                    <li class="mb-2"><strong class="text-success">Disetujui</strong> — data lengkap & layak diteruskan</li>
                    <li class="mb-2"><strong class="text-info">Dipertimbangkan</strong> — masuk kriteria standar</li>
                    <li><strong class="text-danger">Ditolak</strong> — tidak sesuai kriteria, gugur</li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- SIDE DRAWER VERIFIKASI --}}
<div id="drawerOverlay" class="drawer-overlay" onclick="closeDrawer()"></div>

<div id="verifDrawer" class="verif-drawer">
    <div class="drawer-header">
        <div class="d-flex align-items-center gap-3">
            <div class="drawer-icon" id="drawerIcon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold" id="drawerTitle">Setujui Pengajuan</h5>
                <small class="text-muted" id="drawerSubtitle">Pengajuan akan diteruskan ke tahap berikutnya</small>
            </div>
        </div>
        <button type="button" class="btn-close" onclick="closeDrawer()"></button>
    </div>

    <div class="drawer-body">
        <form action="{{ route('ppk.verifikasi', $pengajuan->id) }}" method="POST" id="formVerifikasi">
            @csrf
            @method('PUT')

            <input type="hidden" name="status_verifikasi" id="inputStatus" value="menunggu_keuangan">
            {{-- ppk_id TIDAK dikirim dari form, diambil controller dari auth() --}}

            {{-- Info Panel --}}
            <div class="p-3 rounded-3 mb-3" id="infoPanel">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Total Pengajuan</span>
                    <span class="badge" id="infoBadge">DISETUJUI</span>
                </div>
                <div class="fs-4 fw-bold mt-1" id="infoAmount">
                    Rp {{ number_format($pengajuan->total_pengajuan ?? 0, 0, ',', '.') }}
                </div>
                <small class="text-muted" id="infoDesc">Pengajuan akan diteruskan ke Keuangan</small>
            </div>

            {{-- Catatan --}}
            <div class="mb-3">
                <label class="form-label fw-semibold small">
                    Catatan Verifikasi <span class="text-danger">*</span>
                </label>
                <textarea name="catatan_verifikasi"
                          id="inputCatatan"
                          class="form-control"
                          rows="4"
                          required
                          minlength="5"
                          maxlength="255"
                          placeholder="Tulis catatan verifikasi (5-255 karakter)..."></textarea>
                <small class="text-muted">Minimal 5, maksimal 255 karakter.</small>
            </div>

            {{-- Konfirmasi --}}
            <label class="confirm-check d-flex align-items-start gap-3 p-3 rounded-3 border">
                <input class="form-check-input mt-1" type="checkbox" id="checkKonfirmasi" required>
                <span class="small text-secondary" id="konfirmasiText">
                    Saya menyatakan pengajuan ini <strong>layak disetujui</strong>.
                </span>
            </label>
        </form>
    </div>

    <div class="drawer-footer">
        <button type="button" class="btn btn-light" onclick="closeDrawer()">Batal</button>
        <button type="submit" form="formVerifikasi" class="btn px-4" id="btnSubmit" disabled>
            <i class="bi bi-check2-circle me-1"></i> <span id="btnLabel">Setujui</span>
        </button>
    </div>
</div>

<style>
.info-item {
    background: #f8f9fa;
    padding: 10px 14px;
    border-radius: 8px;
}
.info-item .label {
    font-size: 0.7rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}
.info-item .value {
    font-size: 0.95rem;
    font-weight: 500;
    color: #1a1a2e;
    margin-top: 2px;
}

.timeline { position: relative; padding-left: 30px; }
.timeline::before {
    content: '';
    position: absolute;
    left: 8px; top: 0; bottom: 0;
    width: 2px;
    background: #dee2e6;
}
.timeline-item { position: relative; margin-bottom: 25px; }
.timeline-item:last-child { margin-bottom: 0; }
.timeline-icon {
    position: absolute;
    left: -22px; top: 0;
    width: 32px; height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
}
.timeline-icon i { font-size: 14px; }
.timeline-content { padding-left: 5px; }
.timeline-content h6 { font-size: 0.9rem; }
.timeline-content small { font-size: 0.75rem; }
.timeline-content p { font-size: 0.8rem; }

.confirm-check {
    cursor: pointer;
    background: #f9fafb;
    transition: all .15s ease;
    border-color: #e5e7eb !important;
}
.confirm-check:hover {
    background: #f3f4f6;
    border-color: #d1d5db !important;
}

.drawer-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, .35);
    opacity: 0;
    visibility: hidden;
    transition: opacity .25s ease, visibility .25s ease;
    z-index: 1040;
    backdrop-filter: blur(2px);
    pointer-events: none;
}
.drawer-overlay.show {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

.verif-drawer {
    position: fixed;
    top: 0; right: 0;
    height: 100vh;
    width: 480px;
    max-width: 100%;
    background: #fff;
    box-shadow: -20px 0 60px rgba(0, 0, 0, .15);
    transform: translateX(100%);
    transition: transform .3s cubic-bezier(.34, 1.15, .64, 1);
    z-index: 1050;
    display: flex;
    flex-direction: column;
    pointer-events: none;
    visibility: hidden;
}
.verif-drawer.show {
    transform: translateX(0);
    pointer-events: auto;
    visibility: visible;
}

.drawer-header {
    padding: 20px 24px;
    border-bottom: 1px solid #f1f3f5;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.drawer-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    background: #d1fae5;
    color: #059669;
    transition: background .2s ease, color .2s ease;
}
.drawer-icon.icon-info    { background: #cffafe; color: #0891b2; }
.drawer-icon.icon-warning { background: #fef3c7; color: #d97706; }
.drawer-icon.icon-danger  { background: #fee2e2; color: #dc2626; }

.drawer-body {
    flex: 1;
    overflow-y: auto;
    padding: 20px 24px;
}

.drawer-footer {
    padding: 16px 24px;
    border-top: 1px solid #f1f3f5;
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    flex-shrink: 0;
    background: #fff;
}

#infoPanel.panel-success { background: #f0fdf4; border: 1px solid #86efac; }
#infoPanel.panel-info    { background: #ecfeff; border: 1px solid #67e8f9; }
#infoPanel.panel-warning { background: #fffbeb; border: 1px solid #fde68a; }
#infoPanel.panel-danger  { background: #fef2f2; border: 1px solid #fca5a5; }

.bg-orange { background-color: #fd7e14 !important; }
.bg-teal   { background-color: #20c997 !important; }
.bg-purple { background-color: #6f42c1 !important; }

@media (max-width: 576px) {
    .verif-drawer { width: 100%; }
    .drawer-header { padding: 16px 20px; }
    .drawer-body { padding: 16px 20px; }
    .drawer-footer { padding: 12px 20px; flex-wrap: wrap; }
    .drawer-footer .btn { flex: 1; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.modal-backdrop').forEach(function(b) { b.remove(); });
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';

    var check = document.getElementById('checkKonfirmasi');
    var btn = document.getElementById('btnSubmit');
    if (check && btn) {
        check.addEventListener('change', function() {
            btn.disabled = !this.checked;
        });
    }
});

var MODES = {
    'menunggu_keuangan': {
        icon: 'bi-check-circle-fill', iconClass: '',
        title: 'Setujui Pengajuan',
        subtitle: 'Pengajuan akan diteruskan ke tahap berikutnya',
        panelClass: 'panel-success', badge: 'DISETUJUI', badgeClass: 'bg-success',
        desc: 'Pengajuan akan diteruskan ke Keuangan',
        btnLabel: 'Setujui', btnClass: 'btn-success',
        konfirmasi: 'Saya menyatakan pengajuan ini <strong>layak disetujui</strong>.'
    },
    'dipertimbangkan': {
        icon: 'bi-clipboard-check', iconClass: 'icon-info',
        title: 'Dipertimbangkan',
        subtitle: 'Pengajuan masuk kriteria standar',
        panelClass: 'panel-info', badge: 'Dipertimbangkan', badgeClass: 'bg-info',
        desc: 'Pengajuan akan diproses sesuai alur standar',
        btnLabel: 'Tandai Dipertimbangkan', btnClass: 'btn-info text-white',
        konfirmasi: 'Saya menyatakan pengajuan ini <strong>Dipertimbangkan</strong> yang berlaku.'
    },
    'ditolak': {
        icon: 'bi-x-circle-fill', iconClass: 'icon-danger',
        title: 'Tolak Pengajuan',
        subtitle: 'Pengajuan akan ditolak permanen',
        panelClass: 'panel-danger', badge: 'DITOLAK', badgeClass: 'bg-danger',
        desc: 'Pengajuan akan ditolak dan tidak dapat dilanjutkan',
        btnLabel: 'Tolak', btnClass: 'btn-danger',
        konfirmasi: 'Saya menyatakan pengajuan ini <strong>ditolak</strong> dan tidak dapat dilanjutkan.'
    }
};

function openDrawer(mode) {
    var cfg = MODES[mode];
    if (!cfg) return;

    var icon = document.getElementById('drawerIcon');
    icon.className = 'drawer-icon ' + (cfg.iconClass || '');
    icon.innerHTML = '<i class="bi ' + cfg.icon + '"></i>';

    document.getElementById('drawerTitle').textContent = cfg.title;
    document.getElementById('drawerSubtitle').textContent = cfg.subtitle;
    document.getElementById('inputStatus').value = mode;

    var panel = document.getElementById('infoPanel');
    panel.className = 'p-3 rounded-3 mb-3 ' + cfg.panelClass;

    var badge = document.getElementById('infoBadge');
    badge.className = 'badge ' + cfg.badgeClass;
    badge.textContent = cfg.badge;

    document.getElementById('infoDesc').textContent = cfg.desc;
    document.getElementById('konfirmasiText').innerHTML = cfg.konfirmasi;

    var btn = document.getElementById('btnSubmit');
    btn.className = 'btn px-4 ' + cfg.btnClass;
    document.getElementById('btnLabel').textContent = cfg.btnLabel;

    document.getElementById('inputCatatan').value = '';
    document.getElementById('checkKonfirmasi').checked = false;
    btn.disabled = true;

    document.getElementById('verifDrawer').classList.add('show');
    document.getElementById('drawerOverlay').classList.add('show');
}

function closeDrawer() {
    document.getElementById('verifDrawer').classList.remove('show');
    document.getElementById('drawerOverlay').classList.remove('show');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDrawer();
});
</script>
@endsection