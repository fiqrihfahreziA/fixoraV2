@extends('layouts.pengadaan.penerima')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section dengan Statistik -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h4 class="mb-1 fw-bold text-dark">
                        <i class="bi bi-box-seam text-primary me-2"></i>Pengadaan Barang
                    </h4>
                    <p class="text-muted mb-0">Kelola permintaan dan perbaikan barang Anda</p>
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
                            <h6 class="text-muted mb-0 small">Draft</h6>
                            <h5 class="fw-bold mb-0">{{ $stats['draft'] ?? 0 }}</h5>
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
                            <h5 class="fw-bold mb-0">{{ $stats['diajukan'] ?? 0 }}</h5>
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
                            <i class="bi bi-hourglass-split fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0 small">Menunggu</h6>
                            <h5 class="fw-bold mb-0">{{ $stats['menunggu_direktur'] ?? 0 }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Section -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('penerima.chartp') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold small text-muted">
                    <i class="bi bi-search me-1"></i> Cari
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           class="form-control border-0 bg-light" 
                           placeholder="Cari no pengajuan, nama barang..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small text-muted">
                    <i class="bi bi-filter me-1"></i> Status
                </label>
                <select name="status" class="form-select bg-light border-0">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                    <option value="disetujui_koordinator" {{ request('status') == 'disetujui_koordinator' ? 'selected' : '' }}>Disetujui Koordinator</option>
                    <option value="disetujui_kabid" {{ request('status') == 'disetujui_kabid' ? 'selected' : '' }}>Disetujui Kabid</option>
                    <option value="menunggu_direktur" {{ request('status') == 'menunggu_direktur' ? 'selected' : '' }}>Menunggu Direktur</option>
                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small text-muted">
                    <i class="bi bi-calendar me-1"></i> Tahun Anggaran
                </label>
                <select name="tahun_anggaran" class="form-select bg-light border-0">
                    <option value="">Semua Tahun</option>
                    @for($i = date('Y'); $i >= date('Y')-5; $i--)
                        <option value="{{ $i }}" {{ request('tahun_anggaran') == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>
            {{-- <div class="col-md-2">
                <label class="form-label fw-semibold small text-muted">
                    <i class="bi bi-tag me-1"></i> Jenis
                </label>
                <select name="type" class="form-select bg-light border-0">
                    <option value="">Semua Jenis</option>
                    <option value="permintaan" {{ request('type') == 'permintaan' ? 'selected' : '' }}>Permintaan</option>
                    <option value="perbaikan" {{ request('type') == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                </select>
            </div> --}}
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 rounded-pill">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
        </form>
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
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="fw-semibold text-primary">{{ $pengajuan->no_pengajuan }}</span>
                                <br>
                                <small class="text-muted">{{ $pengajuan->bidang->nama_bidang ?? '-' }}</small>
                            </td>
                            <td>
                                {{ date('d/m/Y', strtotime($pengajuan->tanggal_pengajuan)) }}
                            </td>
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
                                    $statusColor = [
                                        'draft' => 'secondary',
                                        'diajukan' => 'warning',
                                        'disetujui_koordinator' => 'info',
                                        'disetujui_kabid' => 'primary',
                                        'menunggu_direktur' => 'warning',
                                        'disetujui' => 'success',
                                        'ditolak' => 'danger'
                                    ][$pengajuan->status] ?? 'secondary';
                                    
                                    $statusLabel = [
                                        'draft' => 'Draft',
                                        'diajukan' => 'Diajukan',
                                        'disetujui_koordinator' => 'Disetujui Koordinator',
                                        'disetujui_kabid' => 'Disetujui Kabid',
                                        'menunggu_direktur' => 'Menunggu Direktur',
                                        'disetujui' => 'Disetujui',
                                        'ditolak' => 'Ditolak'
                                    ][$pengajuan->status] ?? $pengajuan->status;
                                @endphp
                                <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} px-3 py-2 rounded-pill">
                                    <i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i>
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold">Rp {{ number_format($pengajuan->total_pengajuan, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-3" 
                                            onclick="openModal({{ $pengajuan->id }})" 
                                            title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if($pengajuan->status == 'draft')
                                        <button class="btn btn-sm btn-outline-danger rounded-3" 
                                                onclick="confirmDelete({{ $pengajuan->id }})" 
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                    {{-- @if($pengajuan->status == 'disetujui')
                                        <a href="#" class="btn btn-sm btn-outline-success rounded-3" title="Cetak">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    @endif --}}
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                <h6 class="text-muted">Belum ada data pengajuan</h6>
                                <p class="text-muted small">Tidak ada pengajuan untuk bidang Anda</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
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
    $statusColor = [
        'draft' => 'secondary',
        'diajukan' => 'warning',
        'disetujui_koordinator' => 'info',
        'disetujui_kabid' => 'primary',
        'menunggu_direktur' => 'purple',
        'disetujui' => 'success',
        'ditolak' => 'danger'
    ][$pengajuan->status] ?? 'secondary';

    $statusLabel = [
        'draft' => 'Draft',
        'diajukan' => 'Diajukan',
        'disetujui_koordinator' => 'Disetujui Koordinator',
        'disetujui_kabid' => 'Disetujui Kabid',
        'menunggu_direktur' => 'Menunggu Direktur',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak'
    ][$pengajuan->status] ?? $pengajuan->status;

    $steps = ['diajukan', 'disetujui_koordinator', 'disetujui_kabid', 'menunggu_direktur', 'disetujui'];
    $currentStepIndex = array_search($pengajuan->status, $steps);
@endphp
<div class="modal fade" id="modalDetail{{ $pengajuan->id }}" 
     tabindex="-1" 
     aria-hidden="true"
     data-bs-backdrop="false"
     data-bs-keyboard="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 overflow-hidden">

            <!-- Header -->
            <div class="modal-header-modern border-0 p-4" style="background: linear-gradient(135deg, #f8f9ff 0%, #e8edff 100%);">
                <button type="button" class="btn-close-modern" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-icon-badge bg-{{ $statusColor }} text-white">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">{{ $pengajuan->no_pengajuan }}</h5>
                        <span class="badge bg-{{ $statusColor }} px-3 py-2 rounded-pill">
                            <i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i>{{ $statusLabel }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="modal-body p-4">
                <!-- Info Ringkas -->
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

                <!-- Informasi Pemohon -->
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

                <!-- Alasan & Manfaat -->
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

                <!-- Progress Status -->
                @if($pengajuan->status !== 'ditolak' && $pengajuan->status !== 'draft')
                <div class="mb-4">
                    <h6 class="fw-semibold small text-muted mb-3">
                        <i class="bi bi-signpost-split me-1"></i>Progres Persetujuan
                    </h6>
                    <div class="status-tracker">
                        @foreach(['diajukan' => 'Diajukan', 'disetujui_koordinator' => 'Koordinator', 'disetujui_kabid' => 'Kabid', 'menunggu_direktur' => 'Direktur', 'disetujui' => 'Selesai'] as $key => $label)
                            @php $stepIndex = array_search($key, $steps); @endphp
                            <div class="tracker-step {{ $currentStepIndex !== false && $stepIndex <= $currentStepIndex ? 'active' : '' }}">
                                <div class="tracker-dot"><i class="bi bi-check-lg"></i></div>
                                <span class="tracker-label">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @elseif($pengajuan->status == 'ditolak')
                <div class="alert alert-danger-subtle bg-danger-subtle border-0 rounded-3 d-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                    <span class="small text-danger fw-semibold">Pengajuan ini ditolak</span>
                </div>
                @endif

                <!-- Daftar Barang -->
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

                <!-- Dampak -->
                @if(!empty($pengajuan->dampak))
                <div class="mt-4">
                    <h6 class="fw-semibold small text-muted mb-2">
                        <i class="bi bi-exclamation-triangle me-1"></i>Dampak Jika Tidak Dilaksanakan
                    </h6>
                    <div class="bg-light rounded-3 p-3 small">{{ $pengajuan->dampak }}</div>
                </div>
                @endif

                <!-- Kondisi Barang Lama -->
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

            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                {{-- @if($pengajuan->status == 'diajukan' || $pengajuan->status == 'disetujui_koordinator') --}}
                @if($pengajuan->status == 'diajukan')
                    <a href="{{ route('penerima.pengadaan.show', $pengajuan->id) }}" class="btn btn-warning rounded-pill px-4 text-white" target="_blank" >
                        <i class="bi bi-pencil me-1"></i>Respon
                    </a>
                @endif
                {{-- @if($pengajuan->status == 'disetujui')
                    <a href="#" class="btn btn-success rounded-pill px-4">
                        <i class="bi bi-printer me-1"></i>Cetak
                    </a>
                @endif --}}
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- ============================================
STYLES
============================================ -->
<style>
/* ===== BASE STYLES ===== */
.bg-purple {
    background-color: #6f42c1;
}
.bg-purple-subtle {
    background-color: #e7d8f5 !important;
}
.text-purple {
    color: #6f42c1 !important;
}
.bg-success-subtle {
    background-color: #d4edda !important;
}
.bg-danger-subtle {
    background-color: #f8d7da !important;
}
.bg-warning-subtle {
    background-color: #fff3cd !important;
}
.bg-info-subtle {
    background-color: #d1ecf1 !important;
}
.bg-secondary-subtle {
    background-color: #e2e3e5 !important;
}
.bg-primary-subtle {
    background-color: #cfe2ff !important;
}

.hover-shadow {
    transition: all 0.3s ease;
}
.hover-shadow:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}
.transition-all {
    transition: all 0.3s ease;
}

.nav-tabs-custom .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 500;
    border-radius: 10px 10px 0 0;
    transition: all 0.3s ease;
    position: relative;
}
.nav-tabs-custom .nav-link:hover {
    color: #0d6efd;
    background: rgba(13, 110, 253, 0.05);
}
.nav-tabs-custom .nav-link.active {
    color: #0d6efd;
    background: transparent;
    border-bottom: 3px solid #0d6efd;
}
.nav-tabs-custom .nav-link .badge {
    font-size: 10px;
    padding: 3px 8px;
}

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

/* Pagination */
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

@media (max-width: 768px) {
    .stats-icon {
        width: 40px;
        height: 40px;
    }
    .stats-icon i {
        font-size: 1.2rem !important;
    }
    .table-responsive {
        font-size: 0.85rem;
    }
    .btn-sm {
        padding: 0.2rem 0.4rem;
    }
}

/* ===== MODAL ===== */
.modal-backdrop {
    display: none !important;
}

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

.modal-header-modern {
    position: relative;
}
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
.info-tile:hover {
    background: #f1f3f5;
}
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
    gap: 8px;
    flex: 1;
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
    max-width: 70px;
}
.tracker-step.active .tracker-label {
    color: #0d6efd;
}

@media (max-width: 576px) {
    .status-tracker {
        overflow-x: auto;
        padding-bottom: 8px;
    }
    .tracker-label {
        max-width: 55px;
        font-size: 0.62rem;
    }
}
</style>

<!-- ============================================
SCRIPTS
============================================ -->
<script>
// ===== FUNGSI OPEN MODAL =====
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



// ===== TOOLTIP =====
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// ===== CLEANUP BACKDROP =====
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