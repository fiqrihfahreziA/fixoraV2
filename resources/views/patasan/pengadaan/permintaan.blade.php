@extends('layouts.pengadaan.atasan')

@section('content')
@php
    /* ====== PETA STATUS (didefinisikan sekali, dipakai tabel & modal) ====== */
    $statusColorMap = [
        'draft' => 'secondary','revisi' => 'purple','diajukan' => 'warning',
        'disetujui_koordinator' => 'info','perlu_survei' => 'orange','sesuai kebutuhan' => 'info',
        'disetujui_kabid' => 'primary','sesuai standard' => 'primary','dipertimbangkan' => 'primary',
        'keuangan' => 'indigo','anggaran tidak tersedia' => 'danger','sebagian anggaran' => 'warning',
        'menunggu_keuangan' => 'indigo','menunggu_kpa' => 'indigo','disetujui_pembuat_komitmen' => 'primary',
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

    $statCards = [
        ['label' => 'Total',           'key' => 'total',                 'icon' => 'bi-grid-3x3-gap-fill', 'tone' => 'primary'],
        ['label' => 'Menunggu kabid',  'key' => 'disetujui_koordinator', 'icon' => 'bi-pencil-square',     'tone' => 'warning'],
        ['label' => 'Proses',          'key' => 'proses',                'icon' => 'bi-clock-history',     'tone' => 'info'],
        ['label' => 'Disetujui',       'key' => 'disetujui',             'icon' => 'bi-check-circle-fill', 'tone' => 'success'],
        ['label' => 'Ditolak',         'key' => 'ditolak',               'icon' => 'bi-x-circle-fill',     'tone' => 'danger'],
        ['label' => 'Selesai',         'key' => 'selesai',               'icon' => 'bi-flag-fill',         'tone' => 'purple'],
    ];

    $activeType = $typeFilter ?? 'pengadaan';
@endphp

<div class="pengadaan-page">

<div class="container-fluid px-0">

    <!-- Header Section -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="page-icon"><i class="bi bi-box-seam"></i></div>
            <div>
                <h4 class="mb-0 fw-bold page-title">Verifikasi Pengadaan Barang</h4>
                <p class="text-muted mb-0 small">Kelola verifikasi pengadaan dan pemeliharaan barang</p>
            </div>
        </div>
        <span class="bidang-chip">
            <i class="bi bi-building"></i>{{ $authUser->karyawan->bidang->nama_bidang ?? 'Semua Bidang' }}
        </span>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-3 mb-4">
        @foreach($statCards as $card)
        <div class="col-xl-2 col-lg-4 col-md-4 col-6">
            <div class="stat-card">
                <div class="stat-icon t-{{ $card['tone'] }}">
                    <i class="bi {{ $card['icon'] }}"></i>
                </div>
                <div class="min-w-0">
                    <div class="stat-label">{{ $card['label'] }}</div>
                    <div class="stat-value">{{ $stats[$card['key']] ?? 0 }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Filter & Search Section -->
    <div class="panel mb-4">
        <form action="{{ route('atasan.pengadaan') }}" method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="type" value="{{ $activeType }}">

            <div class="col-md-3">
                <label class="filter-label"><i class="bi bi-search me-1"></i>Cari</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari no pengajuan..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-2">
                <label class="filter-label"><i class="bi bi-calendar me-1"></i>Tahun</label>
                <select name="tahun" class="form-select">
                    <option value="">Semua Tahun</option>
                    {{-- Loop dari data tanggal_pengajuan --}}
                    @foreach($listTahun as $th)
                        <option value="{{ $th }}" {{ request('tahun') == $th ? 'selected' : '' }}>{{ $th }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="filter-label"><i class="bi bi-building me-1"></i>Bidang</label>
                <select name="bidang" class="form-select">
                    <option value="">Semua Bidang</option>
                    @foreach($bidangs as $b)
                        <option value="{{ $b->id }}" {{ request('bidang') == $b->id ? 'selected' : '' }}>
                            {{ $b->nama_bidang }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="filter-label"><i class="bi bi-calendar-month me-1"></i>Bulan</label>
                <select name="bulan" class="form-select">
                    <option value="">Semua Bulan</option>
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('M') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-brand flex-fill" title="Filter">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(request('search') || request('status') || request('bidang') || request('tahun') || request('bulan'))
                    <a href="{{ route('atasan.pengadaan', ['type' => $activeType]) }}" class="btn btn-reset" title="Reset">
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabs -->
    <div class="tabs-line mb-4">
        <ul class="nav" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $activeType === 'pengadaan' ? 'active' : '' }}"
                   href="{{ request()->fullUrlWithQuery(['type' => 'pengadaan', 'page' => null]) }}">
                    <i class="bi bi-cart"></i> Pengadaan
                    <span class="tab-count tab-count-green">{{ $stats['pengadaan_count'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $activeType === 'pemeliharaan' ? 'active' : '' }}"
                   href="{{ request()->fullUrlWithQuery(['type' => 'pemeliharaan', 'page' => null]) }}">
                    <i class="bi bi-tools"></i> Pemeliharaan
                    <span class="tab-count tab-count-yellow">{{ $stats['pemeliharaan_count'] ?? 0 }}</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Tabel -->
    <div class="panel p-0">
        <div class="table-responsive p-3 p-md-4">
            <table class="table table-hover align-middle mb-0 main-table">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="15%">No Pengajuan</th>
                        <th width="12%">Tanggal</th>
                        <th width="25%">Nama Barang</th>
                        <th width="17%">Status</th>
                        <th width="14%">Total</th>
                        <th class="text-center" width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allPengajuan as $pengajuan)
                    @php
                        $statusColor = $statusColorMap[$pengajuan->status] ?? 'secondary';
                        $statusLabel = $statusLabelMap[$pengajuan->status] ?? $pengajuan->status;
                    @endphp
                    <tr>
                        <td class="text-muted">{{ $loop->iteration + ($allPengajuan->currentPage() - 1) * $allPengajuan->perPage() }}</td>
                        <td>
                            <span class="no-pengajuan">{{ $pengajuan->no_pengajuan }}</span>
                            <small class="text-muted d-block">{{ $pengajuan->bidang->nama_bidang ?? '-' }}</small>
                        </td>
                        <td class="text-nowrap">{{ date('d/m/Y', strtotime($pengajuan->tanggal_pengajuan)) }}</td>
                        <td>
                            @foreach($pengajuan->items->take(2) as $item)
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-dot item-dot"></i>
                                    <span class="small">{{ $item->nama_barang }}</span>
                                    <span class="qty-chip">x{{ $item->jumlah }}</span>
                                </div>
                            @endforeach
                            @if($pengajuan->items->count() > 2)
                                <small class="text-muted">+ {{ $pengajuan->items->count() - 2 }} item lainnya</small>
                            @endif
                        </td>
                        <td>
                            <span class="st-badge st-{{ $statusColor }}">
                                <i class="bi bi-circle-fill"></i>{{ $statusLabel }}
                            </span>
                            @if($pengajuan->status == 'disetujui')
                                <div class="mt-1">
                                    <span class="approved-chip">
                                        <i class="bi bi-check-circle me-1"></i>
                                        {{ $pengajuan->log_status_direktur }}: Rp {{ number_format($pengajuan->total_disetujui_direktur, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td class="text-nowrap fw-semibold">Rp {{ number_format($pengajuan->total_pengajuan, 0, ',', '.') }}</td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <button type="button" class="btn btn-sm btn-icon" onclick="openModal({{ $pengajuan->id }})" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @if($pengajuan->status == 'draft')
                                    <button class="btn btn-sm btn-icon btn-icon-danger" onclick="confirmDelete({{ $pengajuan->id }})" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row">
                        <td colspan="7" class="text-center py-5">
                            <div class="empty-icon"><i class="bi bi-inbox"></i></div>
                            <h6 class="fw-bold mb-1">Belum ada data</h6>
                            <p class="text-muted small mb-0">
                                @if($activeType === 'pemeliharaan')
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

        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center px-4 py-3 table-foot">
            <div class="text-muted small">
                Menampilkan {{ $allPengajuan->firstItem() ?? 0 }} - {{ $allPengajuan->lastItem() ?? 0 }}
                dari {{ $allPengajuan->total() }} data
            </div>
            {{ $allPengajuan->links() }}
        </div>
    </div>
</div>

<!-- ============================================
MODAL DETAIL
============================================ -->
@foreach($allPengajuan as $pengajuan)
@php
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

    /* ====== USER ====== */
    $user    = auth()->user();
    $subrole = strtolower(trim((string) ($user->subrole ?? '')));
    $role    = strtolower(trim((string) ($user->role ?? '')));

    /* ====== BIDANG PENGAJUAN (tahan string / relasi) ====== */
    $rawBidang = is_object($pengajuan->bidang ?? null)
        ? ($pengajuan->bidang->nama_bidang ?? ($pengajuan->bidang->nama ?? ''))
        : ($pengajuan->bidang ?? '');

    $bidang = strtolower(trim((string) $rawBidang));

    $bidangButuhDuaKabid = in_array($bidang, ['alat kesehatan', 'alkes'], true)
        || str_contains($bidang, 'alat kesehatan')
        || str_contains($bidang, 'alkes');

    /* ====== HITUNG $bisaRespon ====== */
    if ($bidangButuhDuaKabid) {
        // ===== ALKES: alur kabid tetap =====
        if ($subrole === 'kabid_pelayanan') {
            $bisaRespon = $pengajuan->status === 'disetujui_koordinator';
        } elseif ($subrole === 'kabid_penunjang') {
            $kabidPelayananSetuju   = trim(strtolower((string) $pengajuan->log_acc_kabidPelayanan))
                                       === 'disetujui oleh kabid pelayanan';
            $belumDiresponPenunjang = empty($pengajuan->log_acc_kabidPenunjang);
            $bisaRespon = $kabidPelayananSetuju && $belumDiresponPenunjang;
        } else {
            $bisaRespon = false;
        }
    } else {
        // ===== NON-ALKES: atasan bebas respon (tanpa peduli bidang) =====
        $isAtasan = empty($subrole)
            || in_array($subrole, ['atasan', 'multirole1'], true)
            || in_array($role, ['atasan', 'multirole1'], true);

        $bisaRespon = $isAtasan
            && $pengajuan->status === 'disetujui_koordinator'
            && empty($pengajuan->log_acc_kabidPelayanan)
            && empty($pengajuan->log_acc_kabidPenunjang);
    }

    /* ====== ROUTE RESPON ====== */
    // Route multirole belum tersedia → selalu arahkan ke /atasan/pengadaan/{id}
    $routeRespon = url('atasan/pengadaan/' . $pengajuan->id);
@endphp
<div class="modal fade" id="modalDetail{{ $pengajuan->id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" data-bs-keyboard="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 overflow-hidden">
            <div class="modal-header-modern border-0 p-4">
                <button type="button" class="btn-close-modern" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-icon-badge st-solid st-{{ $statusColor }}">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <div class="modal-no">{{ $pengajuan->no_pengajuan }}</div>
                        <span class="st-badge st-solid st-{{ $statusColor }}">
                            <i class="bi bi-circle-fill"></i>{{ $statusLabel }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="modal-body p-4">
                <div class="row g-3 mb-3">
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
                            <span class="info-tile-value value-brand">Rp {{ number_format($pengajuan->total_pengajuan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                @if($pengajuan->status == 'disetujui')
                <div class="approved-box mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="approved-check"><i class="bi bi-check-lg"></i></div>
                        <div>
                            <span class="small d-block approved-sub">Status Pengajuan</span>
                            <h6 class="fw-bold mb-0">Telah Disetujui</h6>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="small d-block approved-sub">Total Disetujui</span>
                        <h5 class="fw-bold mb-0">Rp {{ number_format($pengajuan->total_pengajuan, 0, ',', '.') }}</h5>
                        <small class="approved-sub">Disetujui pada: {{ date('d M Y', strtotime($pengajuan->updated_at)) }}</small>
                    </div>
                </div>
                @endif

                <div class="row g-3 mb-3">
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
                    <h6 class="section-title mb-3">
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
                <div class="notice notice-danger mb-4">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <span class="small fw-semibold">Pengajuan ini ditolak</span>
                </div>
                @elseif($pengajuan->status == 'ditunda')
                <div class="notice notice-warning mb-4">
                    <i class="bi bi-pause-circle-fill fs-5"></i>
                    <span class="small fw-semibold">Pengajuan ini ditunda</span>
                </div>
                @endif

                <h6 class="section-title mb-3">
                    <i class="bi bi-basket me-1"></i>Daftar Barang
                </h6>
                <div class="table-responsive rounded-3 border items-table">
                    <table class="table table-sm mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="py-2 px-3">Nama Barang</th>
                                <th class="py-2 px-3 text-center">Jumlah</th>
                                <th class="py-2 px-3 text-end">Harga Satuan</th>
                                <th class="py-2 px-3 text-end">Subtotal</th>
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
                            <tr>
                                <td colspan="3" class="px-3 text-end fw-bold small">Total</td>
                                <td class="px-3 text-end fw-bold value-brand">Rp {{ number_format($pengajuan->total_pengajuan, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if(!empty($pengajuan->dampak))
                <div class="mt-4">
                    <h6 class="section-title mb-2">
                        <i class="bi bi-exclamation-triangle me-1"></i>Dampak Jika Tidak Dilaksanakan
                    </h6>
                    <div class="text-box small">{{ $pengajuan->dampak }}</div>
                </div>
                @endif

                @if(!empty($pengajuan->kondisi_barang_lama) || !empty($pengajuan->ket_barang_lama))
                <div class="mt-4">
                    <h6 class="section-title mb-2">
                        <i class="bi bi-info-circle me-1"></i>Kondisi Barang Lama
                    </h6>
                    <div class="text-box small">
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

            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-reset px-4" data-bs-dismiss="modal">Tutup</button>

                @if($bisaRespon)
                    <a href="{{ $routeRespon }}"
                       class="btn btn-respon px-4"
                       target="_blank">
                        <i class="bi bi-pencil me-1"></i>Respon
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

</div>{{-- /.pengadaan-page --}}

<!-- ============================================
STYLES
============================================ -->
<style>
/* ===== Token halaman (selaras tema login: teal) ===== */
.pengadaan-page {
    --fx-teal: #0f766e;
    --fx-teal-2: #14958a;
    --fx-teal-dark: #0b5e57;
    --fx-mint: #e6f3f1;
    --fx-mint-2: #d9efec;
    --fx-line: #e4ecea;
    --fx-ink: #20373a;
    --fx-muted: #5a7370;

    --bs-primary: #0f766e;
    --bs-primary-rgb: 15,118,110;
    --bs-link-color: #0f766e;
    --bs-link-color-rgb: 15,118,110;
    --bs-link-hover-color: #0b5e57;
    --bs-link-hover-color-rgb: 11,94,87;
    --bs-border-color: #e3ebe9;

    color: var(--fx-ink);
}

/* ===== Header ===== */
.pengadaan-page .page-icon {
    width: 48px; height: 48px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    color: #fff;
    background: linear-gradient(135deg, var(--fx-teal) 0%, var(--fx-teal-2) 100%);
    box-shadow: 0 8px 20px rgba(15,118,110,.28);
    flex-shrink: 0;
}
.pengadaan-page .page-title { color: var(--fx-ink); letter-spacing: -0.02em; }
.pengadaan-page .bidang-chip {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 8px 16px;
    border-radius: 999px;
    font-size: .85rem; font-weight: 600;
    color: var(--fx-teal-dark);
    background: var(--fx-mint);
    border: 1px solid #cfe7e3;
}

/* ===== Panel / kartu ===== */
.pengadaan-page .panel {
    background: #fff;
    border: 1px solid var(--fx-line);
    border-radius: 18px;
    padding: 20px;
    box-shadow: 0 1px 2px rgba(15,42,46,.04);
}
.pengadaan-page .panel.p-0 { padding: 0; }

/* ===== Statistik ===== */
.pengadaan-page .stat-card {
    display: flex; align-items: center; gap: 12px;
    height: 100%;
    padding: 14px;
    background: #fff;
    border: 1px solid var(--fx-line);
    border-radius: 16px;
    box-shadow: 0 1px 2px rgba(15,42,46,.04);
    transition: transform .2s ease, box-shadow .2s ease;
}
.pengadaan-page .stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(15,118,110,.12);
}
.pengadaan-page .stat-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.pengadaan-page .stat-label { font-size: .75rem; color: var(--fx-muted); font-weight: 600; line-height: 1.2; }
.pengadaan-page .stat-value { font-size: 1.35rem; font-weight: 800; letter-spacing: -0.02em; line-height: 1.2; }
.pengadaan-page .min-w-0 { min-width: 0; }

.pengadaan-page .t-primary { background: rgba(15,118,110,.12);  color: #0f766e; }
.pengadaan-page .t-warning { background: rgba(245,158,11,.15);  color: #b45309; }
.pengadaan-page .t-info    { background: rgba(8,145,178,.13);   color: #0e7490; }
.pengadaan-page .t-success { background: rgba(22,163,74,.13);   color: #15803d; }
.pengadaan-page .t-danger  { background: rgba(220,38,38,.12);   color: #b91c1c; }
.pengadaan-page .t-purple  { background: rgba(124,58,237,.12);  color: #6d28d9; }

/* ===== Filter ===== */
.pengadaan-page .filter-label {
    display: block;
    margin-bottom: 6px;
    font-size: .75rem; font-weight: 600;
    color: var(--fx-muted);
}
.pengadaan-page .form-control,
.pengadaan-page .form-select,
.pengadaan-page .input-group-text {
    background-color: #fff;
    border: 1px solid #d9e5e3;
    color: var(--fx-ink);
    font-size: .9rem;
}
.pengadaan-page .input-group-text { color: #6b8683; border-right: 0; }
.pengadaan-page .input-group .form-control { border-left: 0; }
.pengadaan-page .form-control:focus,
.pengadaan-page .form-select:focus {
    border-color: var(--fx-teal);
    box-shadow: 0 0 0 .22rem rgba(20,184,166,.18);
}
.pengadaan-page .input-group:focus-within .input-group-text { border-color: var(--fx-teal); }

.pengadaan-page .btn-brand {
    color: #fff;
    font-weight: 600;
    border: 0;
    border-radius: 999px;
    padding: 9px 18px;
    background: linear-gradient(135deg, var(--fx-teal) 0%, var(--fx-teal-2) 100%);
    box-shadow: 0 6px 16px rgba(15,118,110,.25);
    transition: transform .15s ease, box-shadow .15s ease;
}
.pengadaan-page .btn-brand:hover {
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 10px 20px rgba(15,118,110,.32);
}
.pengadaan-page .btn-reset {
    color: var(--fx-muted);
    font-weight: 600;
    background: #f1f6f5;
    border: 1px solid transparent;
    border-radius: 999px;
    padding: 9px 16px;
}
.pengadaan-page .btn-reset:hover { background: #e3efed; color: var(--fx-teal-dark); }
.pengadaan-page .btn-respon {
    color: #fff;
    font-weight: 600;
    border: 0;
    border-radius: 999px;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    box-shadow: 0 6px 16px rgba(217,119,6,.28);
}
.pengadaan-page .btn-respon:hover { color: #fff; filter: brightness(1.05); }

/* ===== Tab (gaya garis bawah) ===== */
.pengadaan-page .tabs-line { border-bottom: 1px solid var(--fx-line); }
.pengadaan-page .tabs-line .nav {
    flex-wrap: nowrap;
    gap: 28px;
    overflow-x: auto;
    scrollbar-width: none;
}
.pengadaan-page .tabs-line .nav::-webkit-scrollbar { display: none; }
.pengadaan-page .tabs-line .nav-link {
    display: flex; align-items: center; gap: 8px;
    margin-bottom: -1px;
    padding: 12px 4px;
    border: 0;
    border-bottom: 3px solid transparent;
    border-radius: 0;
    background: transparent;
    color: #6b7280;
    font-weight: 500;
    white-space: nowrap;
    transition: color .2s ease, border-color .2s ease;
}
.pengadaan-page .tabs-line .nav-link i { font-size: 1.05rem; }
.pengadaan-page .tabs-line .nav-link:hover { color: var(--fx-ink); border-bottom-color: #cfe0dd; }
.pengadaan-page .tabs-line .nav-link.active {
    color: var(--fx-teal-dark);
    font-weight: 700;
    border-bottom-color: var(--fx-teal);
}
.pengadaan-page .tab-count {
    padding: 2px 9px;
    border-radius: 999px;
    font-size: .7rem; font-weight: 700;
    line-height: 1.5;
}
.pengadaan-page .tab-count-green  { color: #fff;    background: #198754; }
.pengadaan-page .tab-count-yellow { color: #5c4400; background: #ffc107; }

/* ===== Tabel utama ===== */
.pengadaan-page .main-table { --bs-table-hover-bg: #f0f8f6; --bs-table-border-color: #e8efee; }
.pengadaan-page .main-table > :not(caption) > * > * { padding: 14px 12px; }
.pengadaan-page .main-table thead th,
.pengadaan-page .items-table thead th {
    background: var(--fx-mint);
    color: var(--fx-teal-dark);
    font-size: .74rem; font-weight: 700;
    letter-spacing: .05em; text-transform: uppercase;
    border-bottom: 0;
    padding-top: 12px; padding-bottom: 12px;
}
.pengadaan-page .main-table thead th:first-child { border-radius: 10px 0 0 10px; }
.pengadaan-page .main-table thead th:last-child  { border-radius: 0 10px 10px 0; }

.pengadaan-page .no-pengajuan { font-weight: 700; color: var(--fx-teal); }
.pengadaan-page .item-dot { color: var(--fx-teal); font-size: 1.2rem; margin: -4px; }
.pengadaan-page .qty-chip {
    padding: 1px 8px;
    border-radius: 999px;
    font-size: .7rem; font-weight: 700;
    color: var(--fx-muted);
    background: #eef3f2;
}
.pengadaan-page .approved-chip {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: .65rem; font-weight: 600;
    color: #166534;
    background: #d7f3e3;
}
.pengadaan-page .table-foot { border-top: 1px solid var(--fx-line); }

.pengadaan-page .empty-icon {
    width: 64px; height: 64px;
    margin: 0 auto 12px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.8rem;
    color: var(--fx-teal);
    background: var(--fx-mint);
}
.pengadaan-page .empty-row:hover > * { --bs-table-bg-state: transparent; }

/* Tombol aksi ikon */
.pengadaan-page .btn-icon {
    width: 34px; height: 34px;
    padding: 0;
    display: inline-flex; align-items: center; justify-content: center;
    border: 1.5px solid #bfe0db;
    border-radius: 10px;
    color: var(--fx-teal);
    background: #fff;
    transition: background .15s ease, color .15s ease, border-color .15s ease;
}
.pengadaan-page .btn-icon:hover { color: #fff; background: var(--fx-teal); border-color: var(--fx-teal); }
.pengadaan-page .btn-icon-danger { border-color: #f4c7c3; color: #b91c1c; }
.pengadaan-page .btn-icon-danger:hover { color: #fff; background: #dc2626; border-color: #dc2626; }

/* ===== Badge status =====
   --st-bg: latar lembut, --st-fg: teks, --st-solid: versi solid */
.pengadaan-page .st-secondary { --st-bg:#e6ebea; --st-fg:#44575a; --st-solid:#64748b; }
.pengadaan-page .st-purple    { --st-bg:#ece4fa; --st-fg:#5b34b0; --st-solid:#7c3aed; }
.pengadaan-page .st-warning   { --st-bg:#fdf0cc; --st-fg:#8a5a00; --st-solid:#d97706; }
.pengadaan-page .st-info      { --st-bg:#d6eef7; --st-fg:#0b6b85; --st-solid:#0891b2; }
.pengadaan-page .st-orange    { --st-bg:#ffe7d3; --st-fg:#9a4a0b; --st-solid:#ea580c; }
.pengadaan-page .st-primary   { --st-bg:#d9efec; --st-fg:#0b5e57; --st-solid:#0f766e; }
.pengadaan-page .st-indigo    { --st-bg:#e0e7ff; --st-fg:#3730a3; --st-solid:#4f46e5; }
.pengadaan-page .st-danger    { --st-bg:#fde4e1; --st-fg:#a4281c; --st-solid:#dc2626; }
.pengadaan-page .st-success   { --st-bg:#d7f3e3; --st-fg:#166534; --st-solid:#16a34a; }

.pengadaan-page .st-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 12px;
    border-radius: 999px;
    font-size: .74rem; font-weight: 700;
    line-height: 1.2;
    color: var(--st-fg);
    background: var(--st-bg);
}
.pengadaan-page .st-badge i { font-size: 6px; }
.pengadaan-page .st-badge.st-solid,
.pengadaan-page .st-solid { color: #fff; background: var(--st-solid); }

/* ===== Pagination ===== */
.pengadaan-page .pagination { margin-bottom: 0; }
.pengadaan-page .pagination .page-link {
    border: 0;
    border-radius: 8px;
    margin: 0 2px;
    color: #4b6663;
    background: transparent;
}
.pengadaan-page .pagination .page-link:hover { background: #e3efed; color: var(--fx-teal-dark); }
.pengadaan-page .pagination .page-item.active .page-link { background: var(--fx-teal); color: #fff; }

@media (max-width: 768px) {
    .pengadaan-page .stat-icon { width: 40px; height: 40px; font-size: 1.1rem; }
    .pengadaan-page .table-responsive { font-size: 0.85rem; }
    .pengadaan-page .panel { padding: 16px; }
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
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
body.modal-open {
    overflow: auto !important;
    padding-right: 0 !important;
}

.pengadaan-page .modal-header-modern,
.modal-header-modern {
    position: relative;
    background: linear-gradient(135deg, #eaf6f4 0%, #d6ece8 100%);
}
.modal-no { font-weight: 800; font-size: 1.05rem; letter-spacing: -0.02em; color: #0f2a2e; margin-bottom: 4px; }
.btn-close-modern {
    position: absolute;
    top: 16px; right: 16px;
    width: 36px; height: 36px;
    border: none;
    border-radius: 50%;
    background: rgba(255,255,255,0.8);
    color: #495057;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s ease;
    z-index: 10;
}
.btn-close-modern:hover { background: #fff; color: #b42318; transform: rotate(90deg); }

.modal-icon-badge {
    width: 52px; height: 52px;
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
    box-shadow: 0 6px 16px rgba(0,0,0,0.15);
}
.modal .st-secondary { --st-bg:#e6ebea; --st-fg:#44575a; --st-solid:#64748b; }
.modal .st-purple    { --st-bg:#ece4fa; --st-fg:#5b34b0; --st-solid:#7c3aed; }
.modal .st-warning   { --st-bg:#fdf0cc; --st-fg:#8a5a00; --st-solid:#d97706; }
.modal .st-info      { --st-bg:#d6eef7; --st-fg:#0b6b85; --st-solid:#0891b2; }
.modal .st-orange    { --st-bg:#ffe7d3; --st-fg:#9a4a0b; --st-solid:#ea580c; }
.modal .st-primary   { --st-bg:#d9efec; --st-fg:#0b5e57; --st-solid:#0f766e; }
.modal .st-indigo    { --st-bg:#e0e7ff; --st-fg:#3730a3; --st-solid:#4f46e5; }
.modal .st-danger    { --st-bg:#fde4e1; --st-fg:#a4281c; --st-solid:#dc2626; }
.modal .st-success   { --st-bg:#d7f3e3; --st-fg:#166534; --st-solid:#16a34a; }

.info-tile {
    background: #f4f8f7;
    border: 1px solid #e6eeec;
    border-radius: 14px;
    padding: 14px 16px;
    display: flex; flex-direction: column; gap: 4px;
    height: 100%;
    transition: background .2s ease;
}
.info-tile:hover { background: #eef5f3; }
.info-tile-label {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: #5a7370;
    font-weight: 600;
}
.info-tile-value { font-size: 0.95rem; color: #20373a; font-weight: 600; }
.value-brand { color: #0f766e !important; font-weight: 800; }

.section-title { font-size: .8rem; font-weight: 700; color: #5a7370; }
.text-box { background: #f4f8f7; border: 1px solid #e6eeec; border-radius: 12px; padding: 14px; color: #20373a; }

.approved-box {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
    padding: 16px;
    border-radius: 14px;
    background: #d7f3e3;
    color: #166534;
}
.approved-check {
    width: 44px; height: 44px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    color: #fff;
    background: #16a34a;
}
.approved-sub { color: #3f7a55; }

.notice {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 16px;
    border-radius: 12px;
}
.notice-danger  { background: #fde4e1; color: #a4281c; }
.notice-warning { background: #fdf0cc; color: #8a5a00; }

.items-table .table { --bs-table-border-color: #e8efee; }
.items-table tfoot td { background: #f1f8f6; border-top: 1px solid #e8efee; }

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
    top: 14px; left: 30px; right: 30px;
    height: 3px;
    background: #e3ebe9;
    z-index: 0;
    border-radius: 3px;
}
.tracker-step {
    position: relative;
    z-index: 1;
    display: flex; flex-direction: column; align-items: center;
    gap: 6px;
    flex: 1;
    min-width: 0;
}
.tracker-dot {
    width: 30px; height: 30px;
    border-radius: 50%;
    background: #e3ebe9;
    color: #a9bab8;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.8rem;
    transition: all 0.3s ease;
    border: 3px solid #fff;
    box-shadow: 0 0 0 1px #e3ebe9;
    flex-shrink: 0;
}
.tracker-step.active .tracker-dot {
    background: linear-gradient(135deg, #0f766e 0%, #4ade80 100%);
    color: #fff;
    box-shadow: 0 0 0 1px #0f766e, 0 4px 10px rgba(15,118,110,.35);
}
.tracker-label {
    font-size: 0.68rem;
    text-align: center;
    color: #a9bab8;
    font-weight: 600;
    max-width: 90px;
    line-height: 1.2;
    word-wrap: break-word;
}
.tracker-step.active .tracker-label { color: #0f766e; }
.tracker-date {
    font-size: 0.6rem;
    color: #a9bab8;
    font-weight: 500;
    text-align: center;
    line-height: 1.2;
    min-height: 12px;
    letter-spacing: 0.2px;
}
.tracker-step.active .tracker-date { color: #0f766e; }

@media (max-width: 576px) {
    .status-tracker { overflow-x: auto; padding-bottom: 8px; }
    .tracker-label { max-width: 65px; font-size: 0.62rem; }
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
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

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