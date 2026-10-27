@extends('layouts.pengadaan.atasan')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h4 class="mb-1 fw-bold text-dark">
                        <i class="bi bi-box-seam text-primary me-2"></i>Verifikasi Pengadaan Barang
                    </h4>
                    <p class="text-muted mb-0">Kelola verifikasi pengadaan dan pemeliharaan barang</p>
                </div>
                <div>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 rounded-pill">
                        <i class="bi bi-building me-2"></i>{{ $authUser->karyawan->bidang->nama_bidang ?? 'Semua Bidang' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-lg-3 col-md-4 col-6">
            <div class="card border-0 shadow-sm hover-shadow transition-all rounded-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-grid-3x3-gap-fill fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0 small">Total</h6>
                            <h5 class="fw-bold mb-0">{{ $stats['total'] ?? 0 }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-6">
            <div class="card border-0 shadow-sm hover-shadow transition-all rounded-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-pencil-square fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0 small">Menunggu kabid</h6>
                            <h5 class="fw-bold mb-0">{{ $stats['disetujui_koordinator'] ?? 0 }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-6">
            <div class="card border-0 shadow-sm hover-shadow transition-all rounded-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon bg-info bg-opacity-10 text-info rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-clock-history fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0 small">Proses</h6>
                            <h5 class="fw-bold mb-0">{{ $stats['proses'] ?? 0 }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-6">
            <div class="card border-0 shadow-sm hover-shadow transition-all rounded-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-check-circle-fill fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0 small">Disetujui</h6>
                            <h5 class="fw-bold mb-0">{{ $stats['disetujui'] ?? 0 }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-6">
            <div class="card border-0 shadow-sm hover-shadow transition-all rounded-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon bg-danger bg-opacity-10 text-danger rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-x-circle-fill fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0 small">Ditolak</h6>
                            <h5 class="fw-bold mb-0">{{ $stats['ditolak'] ?? 0 }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-6">
            <div class="card border-0 shadow-sm hover-shadow transition-all rounded-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon bg-purple bg-opacity-10 text-purple rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-flag-fill fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0 small">Selesai</h6>
                            <h5 class="fw-bold mb-0">{{ $stats['selesai'] ?? 0 }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('atasan.pengadaan') }}" method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="type" value="{{ $typeFilter ?? 'pengadaan' }}">

                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">
                        <i class="bi bi-search me-1"></i> Cari
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-0 bg-light" placeholder="Cari no pengajuan..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-muted">
                        <i class="bi bi-calendar me-1"></i> Tahun
                    </label>
                    <select name="tahun" class="form-select bg-light border-0">
                        <option value="">Semua Tahun</option>
                        {{-- ✅ Loop dari data tanggal_pengajuan --}}
                        @foreach($listTahun as $th)
                            <option value="{{ $th }}" {{ request('tahun') == $th ? 'selected' : '' }}>{{ $th }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-muted">
                        <i class="bi bi-building me-1"></i> Bidang
                    </label>
                    <select name="bidang" class="form-select bg-light border-0">
                        <option value="">Semua Bidang</option>
                        @foreach($bidangs as $b)
                            <option value="{{ $b->id }}" {{ request('bidang') == $b->id ? 'selected' : '' }}>
                                {{ $b->nama_bidang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-muted">
                        <i class="bi bi-calendar me-1"></i> Bulan
                    </label>
                    <select name="bulan" class="form-select bg-light border-0">
                        <option value="">Semua Bulan</option>
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('M') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill px-3" title="Filter">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    @if(request('search') || request('status') || request('bidang') || request('tahun') || request('bulan'))
                        <a href="{{ route('atasan.pengadaan', ['type' => $typeFilter ?? 'pengadaan']) }}" class="btn btn-outline-secondary rounded-pill px-3" title="Reset">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Tabs -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-2">
            <ul class="nav nav-pills nav-fill gap-2" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ ($typeFilter ?? 'pengadaan') === 'pengadaan' ? 'active' : '' }} rounded-pill fw-semibold py-2"
                       href="{{ request()->fullUrlWithQuery(['type' => 'pengadaan', 'page' => null]) }}">
                        <i class="bi bi-cart-plus me-1"></i> Pengadaan Barang
                        <span class="badge {{ ($typeFilter ?? 'pengadaan') === 'pengadaan' ? 'bg-white text-primary' : 'bg-primary bg-opacity-25 text-primary' }} ms-1">
                            {{ $stats['pengadaan_count'] ?? 0 }}
                        </span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ ($typeFilter ?? 'pengadaan') === 'pemeliharaan' ? 'active' : '' }} rounded-pill fw-semibold py-2"
                       href="{{ request()->fullUrlWithQuery(['type' => 'pemeliharaan', 'page' => null]) }}">
                        <i class="bi bi-tools me-1"></i> Pemeliharaan
                        <span class="badge {{ ($typeFilter ?? 'pengadaan') === 'pemeliharaan' ? 'bg-white text-warning' : 'bg-warning bg-opacity-25 text-warning-emphasis' }} ms-1">
                            {{ $stats['pemeliharaan_count'] ?? 0 }}
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tabel -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive p-4">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-3" width="5%">#</th>
                            <th class="py-3 px-3" width="15%">No Pengajuan</th>
                            <th class="py-3 px-3" width="15%">Tanggal</th>
                            <th class="py-3 px-3" width="25%">Nama Barang</th>
                            <th class="py-3 px-3" width="15%">Status</th>
                            <th class="py-3 px-3" width="10%">Total</th>
                            <th class="py-3 px-3 text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allPengajuan as $pengajuan)
                        <tr>
                            <td>{{ $loop->iteration + ($allPengajuan->currentPage() - 1) * $allPengajuan->perPage() }}</td>
                            <td>
                                <span class="fw-semibold text-primary">{{ $pengajuan->no_pengajuan }}</span>
                                <br>
                                <small class="text-muted">{{ $pengajuan->bidang->nama_bidang ?? '-' }}</small>
                            </td>
                            <td>{{ date('d/m/Y', strtotime($pengajuan->tanggal_pengajuan)) }}</td>
                            <td>
                                @foreach($pengajuan->items->take(2) as $item)
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="bi bi-dot text-primary"></i>
                                        <span class="small">{{ $item->nama_barang }}</span>
                                        <span class="badge bg-light text-dark">x{{ $item->jumlah }}</span>
                                    </div>
                                @endforeach
                                @if($pengajuan->items->count() > 2)
                                    <small class="text-muted">+ {{ $pengajuan->items->count() - 2 }} item lainnya</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusColorMap = [
                                        'draft' => 'secondary','revisi' => 'purple','diajukan' => 'warning',
                                        'disetujui_koordinator' => 'info','perlu_survei' => 'orange','sesuai kebutuhan' => 'info',
                                        'disetujui_kabid' => 'primary','sesuai standard' => 'primary','dipertimbangkan' => 'primary',
                                        'keuangan' => 'teal','anggaran tidak tersedia' => 'danger','sebagian anggaran' => 'warning',
                                        'menunggu_keuangan' => 'teal','menunggu_kpa' => 'teal','disetujui_pembuat_komitmen' => 'primary',
                                        'disetujui_kuasa_pengguna' => 'primary','menunggu_direktur' => 'purple',
                                        'disetujui' => 'success','disetujui sebagian' => 'success','ditunda' => 'warning',
                                        'tersedia layak dan diutamakan' => 'success','tidak direkomendasikan' => 'danger',
                                        'ditolak' => 'danger',
                                    ];
                                    $statusLabelMap = [
                                        'draft' => 'Draft','revisi' => 'Revisi','diajukan' => 'Diajukan',
                                        'disetujui_koordinator' => 'Disetujui Koordinator','perlu_survei' => 'Perlu Survei','sesuai kebutuhan' => 'Sesuai Kebutuhan',
                                        'disetujui_kabid' => 'Disetujui Kabid','sesuai standard' => 'Sesuai Standard','dipertimbangkan' => 'Dipertimbangkan',
                                        'keuangan' => 'Keuangan','anggaran tidak tersedia' => 'Anggaran Tidak Tersedia','sebagian anggaran' => 'Sebagian Anggaran',
                                        'menunggu_keuangan' => 'Menunggu Keuangan','menunggu_kpa' => 'Menunggu KPA','disetujui_pembuat_komitmen' => 'Disetujui Pembuat Komitmen',
                                        'disetujui_kuasa_pengguna' => 'Disetujui Kuasa Pengguna','menunggu_direktur' => 'Menunggu Direktur',
                                        'disetujui' => 'Disetujui','disetujui sebagian' => 'Disetujui Sebagian','ditunda' => 'Ditunda',
                                        'tersedia layak dan diutamakan' => 'Tersedia Layak & Diutamakan','tidak direkomendasikan' => 'Tidak Direkomendasikan',
                                        'ditolak' => 'Ditolak',
                                    ];
                                    $statusColor = $statusColorMap[$pengajuan->status] ?? 'secondary';
                                    $statusLabel = $statusLabelMap[$pengajuan->status] ?? $pengajuan->status;
                                @endphp
                                <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} px-3 py-2 rounded-pill d-inline-block">
                                    <i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i>
                                    {{ $statusLabel }}
                                </span>
                                @if($pengajuan->status == 'disetujui')
                                    <div class="mt-1">
                                        <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill" style="font-size: 0.6rem;">
                                            <i class="bi bi-check-circle me-1"></i>
                                            {{ $pengajuan->log_status_direktur }}: Rp {{ number_format($pengajuan->total_disetujui_direktur, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="fw-semibold">Rp {{ number_format($pengajuan->total_pengajuan, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-3" onclick="openModal({{ $pengajuan->id }})" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if($pengajuan->status == 'draft')
                                        <button class="btn btn-sm btn-outline-danger rounded-3" onclick="confirmDelete({{ $pengajuan->id }})" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                <h6 class="text-muted">Belum ada data</h6>
                                <p class="text-muted small">
                                    @if(($typeFilter ?? 'pengadaan') === 'pemeliharaan')
                                        Tidak ada pengajuan pemeliharaan untuk bidang Anda
                                    @else
                                        Tidak ada pengajuan pengadaan barang untuk bidang Anda
                                    @endif
                                </p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center p-4 border-top">
                <div class="text-muted small">
                    Menampilkan {{ $allPengajuan->firstItem() ?? 0 }} - {{ $allPengajuan->lastItem() ?? 0 }} 
                    dari {{ $allPengajuan->total() }} data
                </div>
                {{ $allPengajuan->links() }}
            </div>
        </div>
    </div>
</div>

<!-- ============================================
MODAL DETAIL
============================================ -->
@foreach($allPengajuan as $pengajuan)
@php
    $statusColorMap = [
        'draft' => 'secondary','revisi' => 'purple','diajukan' => 'warning',
        'disetujui_koordinator' => 'info','perlu_survei' => 'orange','sesuai kebutuhan' => 'info',
        'disetujui_kabid' => 'primary','sesuai standard' => 'primary','dipertimbangkan' => 'primary',
        'keuangan' => 'teal','anggaran tidak tersedia' => 'danger','sebagian anggaran' => 'warning',
        'menunggu_keuangan' => 'teal','menunggu_kpa' => 'teal','disetujui_pembuat_komitmen' => 'primary',
        'disetujui_kuasa_pengguna' => 'primary','menunggu_direktur' => 'purple',
        'disetujui' => 'success','disetujui sebagian' => 'success','ditunda' => 'warning',
        'tersedia layak dan diutamakan' => 'success','tidak direkomendasikan' => 'danger',
        'ditolak' => 'danger',
    ];
    $statusLabelMap = [
        'draft' => 'Draft','revisi' => 'Revisi','diajukan' => 'Diajukan',
        'disetujui_koordinator' => 'Disetujui Koordinator','perlu_survei' => 'Perlu Survei','sesuai kebutuhan' => 'Sesuai Kebutuhan',
        'disetujui_kabid' => 'Disetujui Kabid','sesuai standard' => 'Sesuai Standard','dipertimbangkan' => 'Dipertimbangkan',
        'keuangan' => 'Keuangan','anggaran tidak tersedia' => 'Anggaran Tidak Tersedia','sebagian anggaran' => 'Sebagian Anggaran',
        'menunggu_keuangan' => 'Menunggu Keuangan','menunggu_kpa' => 'Menunggu KPA','disetujui_pembuat_komitmen' => 'Disetujui Pembuat Komitmen',
        'disetujui_kuasa_pengguna' => 'Disetujui Kuasa Pengguna','menunggu_direktur' => 'Menunggu Direktur',
        'disetujui' => 'Disetujui','disetujui sebagian' => 'Disetujui Sebagian','ditunda' => 'Ditunda',
        'tersedia layak dan diutamakan' => 'Tersedia Layak & Diutamakan','tidak direkomendasikan' => 'Tidak Direkomendasikan',
        'ditolak' => 'Ditolak',
    ];

    $statusColor = $statusColorMap[$pengajuan->status] ?? 'secondary';
    $statusLabel = $statusLabelMap[$pengajuan->status] ?? $pengajuan->status;

    $steps = [
        ['disetujui_koordinator', 'perlu_survei', 'sesuai kebutuhan'],
        ['disetujui_kabid', 'sesuai standard'],
        ['menunggu_keuangan'],
        ['keuangan', 'anggaran tidak tersedia', 'sebagian anggaran'],
        ['menunggu_kpa', 'dipertimbangkan'],
        ['menunggu_direktur','disetujui'],
        ['selesai'],
    ];

    $currentStepIndex = null;
    foreach ($steps as $idx => $group) {
        if (in_array($pengajuan->status, $group)) {
            $currentStepIndex = $idx;
            break;
        }
    }

    $stepDates = [
        0 => $pengajuan->diterima_at ?? null,
        1 => $pengajuan->disetujui_kabid_at ?? null,
        3 => $pengajuan->ppk_at ?? null,
        2 => $pengajuan->disetujui_keuangan_at ?? null,
        4 => $pengajuan->kpa ?? null,
        5 => $pengajuan->disetujui_direktur_at ?? null,
        6 => $pengajuan->selesai_at ?? null,
    ];

    $isTrackable = !in_array($pengajuan->status, ['draft', 'revisi', 'ditolak', 'ditunda']);
@endphp
<div class="modal fade" id="modalDetail{{ $pengajuan->id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" data-bs-keyboard="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 overflow-hidden">
            <div class="modal-header-modern border-0 p-4" style="background: linear-gradient(135deg, #f8f9ff 0%, #e8edff 100%);">
                <button type="button" class="btn-close-modern" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-icon-badge bg-{{ $statusColor }} text-white">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <span class="badge bg-{{ $statusColor }} px-3 py-2 rounded-pill">
                            <i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i>{{ $statusLabel }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="modal-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="info-tile">
                            <span class="info-tile-label"><i class="bi bi-calendar3 me-1"></i>Tanggal Pengajuan</span>
                            <span class="info-tile-value">{{ date('d M Y', strtotime($pengajuan->tanggal_pengajuan)) }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-tile">
                            <span class="info-tile-label"><i class="bi bi-building me-1"></i>Bidang</span>
                            <span class="info-tile-value">{{ $pengajuan->bidang->nama_bidang ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-tile">
                            <span class="info-tile-label"><i class="bi bi-cash-coin me-1"></i>Total Pengajuan</span>
                            <span class="info-tile-value fw-bold text-primary">Rp {{ number_format($pengajuan->total_pengajuan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                @if($pengajuan->status == 'disetujui')
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <div class="alert alert-success bg-success-subtle border-0 rounded-3 p-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="bi bi-check-lg text-white fs-4"></i>
                                    </div>
                                    <div>
                                        <span class="text-muted small d-block">Status Pengajuan</span>
                                        <h6 class="fw-bold text-success mb-0">Telah Disetujui</h6>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="text-muted small d-block">Total Disetujui</span>
                                    <h5 class="fw-bold text-success mb-0">Rp {{ number_format($pengajuan->total_pengajuan, 0, ',', '.') }}</h5>
                                    <small class="text-muted">Disetujui pada: {{ date('d M Y', strtotime($pengajuan->updated_at)) }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="info-tile">
                            <span class="info-tile-label"><i class="bi bi-person me-1"></i>Pemohon</span>
                            <span class="info-tile-value">{{ $pengajuan->karyawan->nama ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-tile">
                            <span class="info-tile-label"><i class="bi bi-briefcase me-1"></i>Jabatan</span>
                            <span class="info-tile-value">{{ $pengajuan->karyawan->jabatan ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="info-tile">
                            <span class="info-tile-label"><i class="bi bi-clipboard-check me-1"></i>Alasan Justifikasi</span>
                            <span class="info-tile-value">{{ $pengajuan->alasan_justifikasi ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-tile">
                            <span class="info-tile-label"><i class="bi bi-star me-1"></i>Manfaat</span>
                            <span class="info-tile-value">{{ $pengajuan->manfaat ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                @if($isTrackable && $currentStepIndex !== null)
                <div class="mb-4">
                    <h6 class="fw-semibold small text-muted mb-3">
                        <i class="bi bi-signpost-split me-1"></i>Progres Persetujuan
                    </h6>
                    <div class="status-tracker">
                        @php
                            $trackerSteps = [
                                ['label' => 'Koordinator'],
                                ['label' => 'Kabid'],
                                ['label' => 'Keuangan'],
                                ['label' => 'PPK'],
                                ['label' => 'KPA'],
                                ['label' => 'Direktur'],
                                ['label' => 'Selesai'],
                            ];
                        @endphp

                        @foreach($trackerSteps as $i => $step)
                            @php
                                $stepActive = $i <= $currentStepIndex;
                                $stepDate   = $stepDates[$i] ?? null;
                            @endphp
                            <div class="tracker-step {{ $stepActive ? 'active' : '' }}">
                                <div class="tracker-dot"><i class="bi bi-check-lg"></i></div>
                                <span class="tracker-label">{{ $step['label'] }}</span>
                                <span class="tracker-date">
                                    @if($stepActive && $stepDate)
                                        {{ date('d/m/y', strtotime($stepDate)) }}
                                    @else
                                        —
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @elseif($pengajuan->status == 'ditolak')
                <div class="alert alert-danger-subtle bg-danger-subtle border-0 rounded-3 d-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                    <span class="small text-danger fw-semibold">Pengajuan ini ditolak</span>
                </div>
                @elseif($pengajuan->status == 'ditunda')
                <div class="alert alert-warning-subtle bg-warning-subtle border-0 rounded-3 d-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-pause-circle-fill text-warning fs-5"></i>
                    <span class="small text-warning-emphasis fw-semibold">Pengajuan ini ditunda</span>
                </div>
                @endif

                <h6 class="fw-semibold small text-muted mb-3">
                    <i class="bi bi-basket me-1"></i>Daftar Barang
                </h6>
                <div class="table-responsive rounded-3 border">
                    <table class="table table-sm mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-2 px-3 small">Nama Barang</th>
                                <th class="py-2 px-3 small text-center">Jumlah</th>
                                <th class="py-2 px-3 small text-end">Harga Satuan</th>
                                <th class="py-2 px-3 small text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengajuan->items as $item)
                            <tr>
                                <td class="px-3">
                                    <span class="fw-semibold small d-block">{{ $item->nama_barang }}</span>
                                    @if(!empty($item->spesifikasi))
                                        <span class="text-muted" style="font-size: 0.75rem;">{{ $item->spesifikasi }}</span>
                                    @endif
                                </td>
                                <td class="px-3 text-center small">{{ $item->jumlah }} {{ $item->satuan ?? '' }}</td>
                                <td class="px-3 text-end small">Rp {{ number_format($item->harga_satuan ?? 0, 0, ',', '.') }}</td>
                                <td class="px-3 text-end small fw-semibold">Rp {{ number_format(($item->harga_satuan ?? 0) * $item->jumlah, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <td colspan="3" class="px-3 text-end fw-bold small">Total</td>
                                <td class="px-3 text-end fw-bold text-primary">Rp {{ number_format($pengajuan->total_pengajuan, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if(!empty($pengajuan->dampak))
                <div class="mt-4">
                    <h6 class="fw-semibold small text-muted mb-2">
                        <i class="bi bi-exclamation-triangle me-1"></i>Dampak Jika Tidak Dilaksanakan
                    </h6>
                    <div class="bg-light rounded-3 p-3 small">{{ $pengajuan->dampak }}</div>
                </div>
                @endif

                @if(!empty($pengajuan->kondisi_barang_lama) || !empty($pengajuan->ket_barang_lama))
                <div class="mt-4">
                    <h6 class="fw-semibold small text-muted mb-2">
                        <i class="bi bi-info-circle me-1"></i>Kondisi Barang Lama
                    </h6>
                    <div class="bg-light rounded-3 p-3 small">
                        @if(!empty($pengajuan->kondisi_barang_lama))
                            <span class="fw-semibold">Kondisi:</span> {{ $pengajuan->kondisi_barang_lama }}
                        @endif
                        @if(!empty($pengajuan->ket_barang_lama))
                            <br><span class="fw-semibold">Keterangan:</span> {{ $pengajuan->ket_barang_lama }}
                        @endif
                    </div>
                </div>
                @endif
            </div>

            {{-- @php
                $subrole = auth()->user()->subrole;

                if ($subrole === 'kabid_pelayanan') {
                    $bisaRespon = in_array($pengajuan->status, ['disetujui_koordinator']);
                } elseif ($subrole === 'kabid_penunjang') {
                    $kabidPelayananSetuju = $pengajuan->log_acc_kabidPelayanan === 'disetujui oleh kabid pelayanan';
                    $belumDiresponPenunjang = empty($pengajuan->log_acc_kabidPenunjang);
                    $bisaRespon = $kabidPelayananSetuju && $belumDiresponPenunjang;
                } else {
                    $bisaRespon = false;
                }
            @endphp --}}

            @php
                $subrole = auth()->user()->subrole;
                $bidang = strtolower(trim($pengajuan->bidang ?? ''));

                // Bidang yang butuh alur kabid_pelayanan -> kabid_penunjang
                $bidangButuhDuaKabid = in_array($bidang, ['alat kesehatan', 'alkes']);

                if ($bidangButuhDuaKabid) {
                    if ($subrole === 'kabid_pelayanan') {
                        $bisaRespon = $pengajuan->status === 'disetujui_koordinator';
                    } elseif ($subrole === 'kabid_penunjang') {
                        $kabidPelayananSetuju = $pengajuan->log_acc_kabidPelayanan === 'disetujui oleh kabid pelayanan';
                        $belumDiresponPenunjang = empty($pengajuan->log_acc_kabidPenunjang);
                        $bisaRespon = $kabidPelayananSetuju && $belumDiresponPenunjang;
                    } else {
                        $bisaRespon = false;
                    }
                } else {
                    // Bidang komputer, AT, dll -> atasan tanpa subrole langsung respon
                    // sesuaikan kondisi "selanjutnya" di sini
                    $bisaRespon = empty($subrole) 
                        && $pengajuan->status === 'disetujui_koordinator'
                        && empty($pengajuan->log_acc_kabidPelayanan) // belum direspon siapapun
                        && empty($pengajuan->log_acc_kabidPenunjang);
                }
            @endphp

            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>

                @if($bisaRespon)
                    <a href="{{ route('atasan.pengadaan.show', $pengajuan->id) }}" class="btn btn-warning rounded-pill px-4 text-white" target="_blank">
                        <i class="bi bi-pencil me-1"></i>Respon
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- ============================================
STYLES
============================================ -->
<style>
.bg-purple { background-color: #6f42c1; }
.bg-purple-subtle { background-color: #e7d8f5 !important; }
.text-purple { color: #6f42c1 !important; }
.bg-orange { background-color: #fd7e14 !important; }
.bg-orange-subtle { background-color: #ffe5d0 !important; }
.text-orange { color: #fd7e14 !important; }
.bg-teal { background-color: #20c997 !important; }
.bg-teal-subtle { background-color: #d2f4ea !important; }
.text-teal { color: #20c997 !important; }
.bg-success-subtle { background-color: #d4edda !important; }
.bg-danger-subtle { background-color: #f8d7da !important; }
.bg-warning-subtle { background-color: #fff3cd !important; }
.bg-info-subtle { background-color: #d1ecf1 !important; }
.bg-secondary-subtle { background-color: #e2e3e5 !important; }
.bg-primary-subtle { background-color: #cfe2ff !important; }

.hover-shadow { transition: all 0.3s ease; }
.hover-shadow:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}
.transition-all { transition: all 0.3s ease; }

.table > :not(caption) > * > * {
    border-bottom-width: 1px;
    padding: 12px 12px;
}
.table thead th {
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
}

.stats-icon {
    width: 48px;
    height: 48px;
    flex-shrink: 0;
}

.btn-outline-primary,
.btn-outline-success,
.btn-outline-warning,
.btn-outline-danger {
    border-width: 1.5px;
}
.btn-outline-primary:hover,
.btn-outline-success:hover,
.btn-outline-warning:hover,
.btn-outline-danger:hover {
    transform: scale(1.05);
}

.pagination .page-item .page-link {
    border-radius: 8px;
    margin: 0 3px;
    border: none;
    color: #6c757d;
}
.pagination .page-item.active .page-link {
    background: #0d6efd;
    color: white;
}
.pagination .page-item .page-link:hover {
    background: #e9ecef;
    color: #0d6efd;
}

/* Tab Pills */
.nav-pills .nav-link {
    color: #6c757d;
    background: #f8f9fa;
    transition: all 0.2s ease;
}
.nav-pills .nav-link:hover {
    background: #e9ecef;
    color: #495057;
}
.nav-pills .nav-link.active {
    background: #0d6efd;
    color: #fff;
    box-shadow: 0 4px 12px rgba(13,110,253,0.3);
}

@media (max-width: 768px) {
    .stats-icon { width: 40px; height: 40px; }
    .stats-icon i { font-size: 1.2rem !important; }
    .table-responsive { font-size: 0.85rem; }
    .btn-sm { padding: 0.2rem 0.4rem; }
}

/* ===== MODAL ===== */
.modal-backdrop { display: none !important; }

.modal {
    background: rgba(0,0,0,0.01);
    pointer-events: none;
}
.modal-dialog {
    pointer-events: auto;
    margin: 1.75rem auto;
}
.modal-content {
    pointer-events: auto;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25);
    animation: modalPopIn 0.25s ease;
}

@keyframes modalPopIn {
    from { opacity: 0; transform: translateY(12px) scale(0.98); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

body.modal-open {
    overflow: auto !important;
    padding-right: 0 !important;
}

.modal-header-modern { position: relative; }
.btn-close-modern {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 36px;
    height: 36px;
    border: none;
    border-radius: 50%;
    background: rgba(255,255,255,0.8);
    color: #495057;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    z-index: 10;
}
.btn-close-modern:hover {
    background: #fff;
    color: #dc3545;
    transform: rotate(90deg);
}

.modal-icon-badge {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
    box-shadow: 0 6px 16px rgba(0,0,0,0.15);
}

.info-tile {
    background: #f8f9fa;
    border-radius: 14px;
    padding: 14px 16px;
    display: flex;
    flex-direction: column;
    gap: 4px;
    height: 100%;
    transition: all 0.2s ease;
}
.info-tile:hover { background: #f1f3f5; }
.info-tile-label {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: #6c757d;
    font-weight: 600;
}
.info-tile-value {
    font-size: 0.95rem;
    color: #212529;
    font-weight: 600;
}

/* Status Tracker */
.status-tracker {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    position: relative;
    padding: 0 4px;
}
.status-tracker::before {
    content: '';
    position: absolute;
    top: 14px;
    left: 30px;
    right: 30px;
    height: 3px;
    background: #e9ecef;
    z-index: 0;
    border-radius: 3px;
}
.tracker-step {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    flex: 1;
    min-width: 0;
}
.tracker-dot {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #e9ecef;
    color: #adb5bd;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    transition: all 0.3s ease;
    border: 3px solid #fff;
    box-shadow: 0 0 0 1px #e9ecef;
    flex-shrink: 0;
}
.tracker-step.active .tracker-dot {
    background: linear-gradient(135deg, #0d6efd, #6610f2);
    color: #fff;
    box-shadow: 0 0 0 1px #0d6efd, 0 4px 10px rgba(13,110,253,0.35);
}
.tracker-label {
    font-size: 0.68rem;
    text-align: center;
    color: #adb5bd;
    font-weight: 600;
    max-width: 90px;
    line-height: 1.2;
    word-wrap: break-word;
}
.tracker-step.active .tracker-label { color: #0d6efd; }

.tracker-date {
    font-size: 0.6rem;
    color: #adb5bd;
    font-weight: 500;
    text-align: center;
    line-height: 1.2;
    min-height: 12px;
    letter-spacing: 0.2px;
}
.tracker-step.active .tracker-date { color: #0d6efd; }

@media (max-width: 576px) {
    .status-tracker {
        overflow-x: auto;
        padding-bottom: 8px;
    }
    .tracker-label {
        max-width: 65px;
        font-size: 0.62rem;
    }
    .tracker-date { font-size: 0.55rem; }
}
</style>

<!-- ============================================
SCRIPTS
============================================ -->
<script>
function openModal(id) {
    var modalElement = document.getElementById('modalDetail' + id);
    if (modalElement) {
        var modal = new bootstrap.Modal(modalElement, {
            backdrop: false,
            keyboard: true
        });
        modal.show();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.modal').forEach(function(modal) {
        modal.addEventListener('hidden.bs.modal', function() {
            document.querySelectorAll('.modal-backdrop').forEach(function(el) {
                el.remove();
            });
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
    });
});
</script>
@endsection