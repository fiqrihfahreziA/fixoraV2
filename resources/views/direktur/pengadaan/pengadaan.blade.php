@extends('layouts.pengadaan.direktur')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h4 class="mb-1 fw-bold text-dark">
                        <i class="bi bi-{{ $kategoriFilter === 'pemeliharaan' ? 'tools' : 'box-seam' }} text-primary me-2"></i>
                        Verifikasi {{ $kategoriFilter === 'pemeliharaan' ? 'Pemeliharaan' : 'Pengadaan' }}
                    </h4>
                    <p class="text-muted mb-0">
                        Verifikasi pengajuan {{ $kategoriFilter }} yang sudah disetujui Kabid
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-lg-3 col-md-4 col-6">
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
        <div class="col-xl-3 col-lg-3 col-md-4 col-6">
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
        <div class="col-xl-3 col-lg-3 col-md-4 col-6">
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
        <div class="col-xl-3 col-lg-3 col-md-4 col-6">
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
            <form action="{{ route('direktur.pengadaan') }}" method="GET" class="row g-3 align-items-end">
                {{-- Pertahankan kategori saat filter --}}
                <input type="hidden" name="kategori" value="{{ $kategoriFilter }}">

                {{-- ✅ Kolom Pencarian --}}
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
                               placeholder="Cari pengajuan..." 
                               value="{{ request('search') }}">
                    </div>
                </div>

                {{-- ✅ Filter Bidang --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-muted">
                        <i class="bi bi-building me-1"></i> Bidang
                    </label>
                    <select name="bidang" class="form-select bg-light border-0">
                        <option value="">Semua</option>
                        @foreach($bidangs as $b)
                            <option value="{{ $b->id }}" {{ request('bidang') == $b->id ? 'selected' : '' }}>
                                {{ $b->nama_bidang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- ✅ Filter Tahun --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-muted">
                        <i class="bi bi-calendar me-1"></i> Tahun
                    </label>
                    <select name="tahun" class="form-select bg-light border-0">
                        <option value="">Semua</option>
                        @foreach($listTahun as $th)
                            <option value="{{ $th }}" {{ request('tahun') == $th ? 'selected' : '' }}>{{ $th }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- ✅ Filter Bulan --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-muted">
                        <i class="bi bi-calendar me-1"></i> Bulan
                    </label>
                    <select name="bulan" class="form-select bg-light border-0">
                        <option value="">Semua</option>
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('M') }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- ✅ Tombol Aksi (Filter & Reset) --}}
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill px-3" title="Filter">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    @if(request('search') || request('bidang') || request('tahun') || request('bulan'))
                        <a href="{{ route('direktur.pengadaan', ['kategori' => $kategoriFilter]) }}" class="btn btn-outline-secondary rounded-pill px-3" title="Reset">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Tabs -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <ul class="nav nav-tabs nav-tabs-custom border-0 px-4 pt-3" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="{{ route('direktur.pengadaan', array_merge(request()->except('page'), ['kategori' => 'pengadaan', 'page' => 1])) }}"
                       class="nav-link {{ $kategoriFilter === 'pengadaan' ? 'active' : '' }} px-4 py-2">
                        <i class="bi bi-box-seam me-2"></i>Pengadaan
                        <span class="badge bg-primary rounded-pill ms-2">{{ $countPengadaan ?? 0 }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('direktur.pengadaan', array_merge(request()->except('page'), ['kategori' => 'pemeliharaan', 'page' => 1])) }}"
                       class="nav-link {{ $kategoriFilter === 'pemeliharaan' ? 'active' : '' }} px-4 py-2">
                        <i class="bi bi-tools me-2"></i>Pemeliharaan
                        <span class="badge bg-success rounded-pill ms-2">{{ $countPemeliharaan ?? 0 }}</span>
                    </a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content p-4">
                <div class="tab-pane fade show active" id="data-{{ $kategoriFilter }}" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 px-3" width="5%">#</th>
                                    <th class="py-3 px-3" width="15%">No Pengajuan</th>
                                    <th class="py-3 px-3" width="15%">Tanggal</th>
                                    <th class="py-3 px-3" width="20%">Nama Barang</th>
                                    <th class="py-3 px-3" width="15%">Status</th>
                                    <th class="py-3 px-3" width="10%">Total</th>
                                    <th class="py-3 px-3 text-center" width="20%">Aksi</th>
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
                                            $statusColorMap = [
                                                'draft' => 'secondary','revisi' => 'purple','diajukan' => 'warning',
                                                'disetujui_koordinator' => 'info','perlu_survei' => 'orange','sesuai kebutuhan' => 'info',
                                                'disetujui_kabid' => 'primary','sesuai standard' => 'primary','dipertimbangkan' => 'primary',
                                                'keuangan' => 'teal','anggaran tidak tersedia' => 'danger','sebagian anggaran' => 'warning',
                                                'menunggu_keuangan' => 'teal','menunggu_kpa' => 'teal','disetujui_pembuat_komitmen' => 'primary',
                                                'disetujui_kuasa_pengguna' => 'primary','menunggu_direktur' => 'purple',
                                                'disetujui' => 'success','disetujui sebagian' => 'success','ditunda' => 'warning',
                                                'tersedia layak dan diutamakan' => 'success','tidak direkomendasikan' => 'danger',
                                                'ditolak' => 'danger','ditolak_penerima' => 'danger',
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
                                                'ditolak' => 'Ditolak','ditolak_penerima' => 'Ditolak Penerima',
                                            ];
                                            $statusColor = $statusColorMap[$pengajuan->status] ?? 'secondary';
                                            $statusLabel = $statusLabelMap[$pengajuan->status] ?? $pengajuan->status;
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
                                            {{-- TOMBOL DETAIL PAKAI DATA-ATTRIBUTES --}}
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-3 btn-detail" 
                                                    data-id="{{ $pengajuan->id }}"
                                                    data-status="{{ $pengajuan->status }}"
                                                    data-label="{{ $statusLabel }}"
                                                    data-color="{{ $statusColor }}"
                                                    data-kategori="{{ $kategoriFilter }}"
                                                    data-tanggal="{{ date('d M Y', strtotime($pengajuan->tanggal_pengajuan)) }}"
                                                    data-bidang="{{ $pengajuan->bidang->nama_bidang ?? '-' }}"
                                                    data-total="Rp {{ number_format($pengajuan->total_pengajuan, 0, ',', '.') }}"
                                                    data-pemohon="{{ $pengajuan->karyawan->nama ?? '-' }}"
                                                    data-jabatan="{{ $pengajuan->karyawan->jabatan ?? '-' }}"
                                                    data-alasan="{{ $pengajuan->alasan_justifikasi ?? '-' }}"
                                                    data-manfaat="{{ $pengajuan->manfaat ?? '-' }}"
                                                    data-dampak="{{ $pengajuan->dampak ?? '' }}"
                                                    data-kondisi="{{ $pengajuan->kondisi_barang_lama ?? '' }}"
                                                    data-ket="{{ $pengajuan->ket_barang_lama ?? '' }}"
                                                    data-items="{{ json_encode($pengajuan->items) }}"
                                                    data-tgl-diajukan="{{ $pengajuan->tanggal_pengajuan ?? $pengajuan->created_at }}"
                                                    data-tgl-koordinator="{{ $pengajuan->diterima_at }}"
                                                    data-tgl-kabid="{{ $pengajuan->disetujui_kabid_at }}"
                                                    data-tgl-keuangan="{{ $pengajuan->disetujui_keuangan_at }}"
                                                    data-tgl-ppk="{{ $pengajuan->ppk_at }}"
                                                    data-tgl-kpa="{{ $pengajuan->kpa }}"
                                                    data-tgl-direktur="{{ $pengajuan->disetujui_direktur_at }}"
                                                    data-tgl-selesai="{{ $pengajuan->updated_at }}"
                                                    onclick="showDetail(this)" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            
                                            {{-- TOMBOL VERIFIKASI --}}
                                            @if(in_array($pengajuan->status, ['menunggu_direktur', 'ditunda', 'ditolak']))
                                                <a href="{{ route('direktur.pengadaan.detail', $pengajuan->id) }}" 
                                                class="btn btn-sm btn-outline-success rounded-3" 
                                                title="Verifikasi">
                                                    <i class="bi bi-check2-circle"></i>
                                                </a>
                                            @endif
                                            
                                            {{-- TOMBOL CETAK --}}

                                            @if(in_array($pengajuan->status, ['disetujui', 'selesai']))
                                               <a href="{{ route('direktur.pengadaan.cetak', $pengajuan->id) }}" class="btn btn-sm btn-outline-success rounded-3" data-bs-toggle="tooltip" title="Cetak" target="_blank">
                                                    <i class="bi bi-printer"></i>
                                                </a>
                                            @endif
                                            <!-- @if($pengajuan->status == 'disetujui')
                                                <a href="{{ route('direktur.pengadaan.cetak', $pengajuan->id) }}" class="btn btn-sm btn-outline-success rounded-3" data-bs-toggle="tooltip" title="Cetak" target="_blank">
                                                    <i class="bi bi-printer"></i>
                                                </a>
                                            @endif -->
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                        <h6 class="text-muted">Belum ada data {{ $kategoriFilter }}</h6>
                                        <p class="text-muted small">Tidak ada pengajuan {{ $kategoriFilter }} yang perlu diverifikasi</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted small">
                            Menampilkan {{ $allPengajuan->firstItem() ?? 0 }} - {{ $allPengajuan->lastItem() ?? 0 }} 
                            dari {{ $allPengajuan->total() }} data
                        </div>
                        {{ $allPengajuan->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================= MODAL VERIFIKASI (TETAP BERDASARKAN LOOP) ================= --}}
@foreach($allPengajuan as $pengajuan)
@if($pengajuan->status == 'disetujui_kabid')
<div class="modal fade" id="modalVerifikasi{{ $pengajuan->id }}" tabindex="-1" aria-labelledby="modalVerifikasiLabel{{ $pengajuan->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #f8f9ff 0%, #e8edff 100%);">
                <h5 class="modal-title fw-bold" id="modalVerifikasiLabel{{ $pengajuan->id }}">
                    <i class="bi bi-check2-circle text-success me-2"></i>
                    Verifikasi Pengajuan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="#" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-file-text me-1"></i> No Pengajuan
                        </label>
                        <p class="fw-bold text-primary">{{ $pengajuan->no_pengajuan }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-person me-1"></i> Pemohon
                        </label>
                        <p>{{ $pengajuan->karyawan->nama ?? '-' }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-cash-coin me-1"></i> Total Pengajuan
                        </label>
                        <p class="fw-bold text-primary">Rp {{ number_format($pengajuan->total_pengajuan, 0, ',', '.') }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-check-circle me-1"></i> Status Verifikasi
                        </label>
                        <select name="status_verifikasi" class="form-select" required>
                            <option value="">Pilih Status</option>
                            <option value="disetujui">✅ Disetujui</option>
                            <option value="ditolak">❌ Ditolak</option>
                            <option value="revisi">🔄 Revisi</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-chat-text me-1"></i> Catatan Verifikasi
                        </label>
                        <textarea name="catatan_verifikasi" class="form-control" rows="3" placeholder="Masukkan catatan verifikasi..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-check2-circle me-1"></i> Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

{{-- ================= MODAL DETAIL GLOBAL (CUMA 1) ================= --}}
<div class="modal fade" id="modalDetailGlobal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" data-bs-keyboard="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 rounded-4 overflow-hidden detail-modal-content" id="modalContentGlobal">
            <!-- Isi akan di-render oleh JS -->
        </div>
    </div>
</div>

<!-- ============================================
STYLES
============================================ -->
<style>
/* ===== BASE STYLES ===== */
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

.nav-tabs-custom .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 500;
    border-radius: 10px 10px 0 0;
    transition: all 0.3s ease;
    position: relative;
    text-decoration: none;
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
    .stats-icon { width: 40px; height: 40px; }
    .stats-icon i { font-size: 1.2rem !important; }
    .table-responsive { font-size: 0.85rem; }
    .btn-sm { padding: 0.2rem 0.4rem; }
}

/* ===== MODAL SCROLL FIX ===== */
.modal-backdrop { display: none !important; }

.modal {
    background: rgba(0,0,0,0.01);
    pointer-events: none;
}
.modal-dialog {
    pointer-events: auto;
    margin: 2rem auto;
    height: calc(100% - 4rem); 
    max-height: 90vh;
}
.modal-content {
    pointer-events: auto;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25);
    animation: modalPopIn 0.25s ease;
    display: flex;
    flex-direction: column;
    height: 100%; 
    max-height: 100%;
}
.modal-body {
    overflow-y: auto;
    flex: 1 1 auto;
}
.modal-header-modern, .modal-footer {
    flex-shrink: 0;
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
// =============================================
// FUNGSI RENDER DETAIL GLOBAL
// =============================================
function showDetail(btn) {
    var data = btn.dataset;
    var items = JSON.parse(data.items || '[]');
    var itemsHtml = '';
    
    items.forEach(function(item) {
        var subtotal = (item.harga_satuan || 0) * item.jumlah;
        itemsHtml += `
            <tr>
                <td class="px-3">
                    <span class="fw-semibold small d-block">${item.nama_barang}</span>
                    ${item.spesifikasi ? `<span class="text-muted" style="font-size: 0.75rem;">${item.spesifikasi}</span>` : ''}
                </td>
                <td class="px-3 text-center small">${item.jumlah} ${item.satuan || ''}</td>
                <td class="px-3 text-end small">Rp ${new Intl.NumberFormat('id-ID').format(item.harga_satuan || 0)}</td>
                <td class="px-3 text-end small fw-semibold">Rp ${new Intl.NumberFormat('id-ID').format(subtotal)}</td>
            </tr>
        `;
    });

    // Logika Tracker
    var steps = [
        ['diajukan'], ['disetujui_koordinator', 'perlu_survei', 'sesuai kebutuhan'],
        ['disetujui_kabid', 'sesuai standard'], ['menunggu_keuangan'],
        ['keuangan', 'anggaran tidak tersedia', 'sebagian anggaran'],
        ['menunggu_kpa', 'dipertimbangkan'], ['menunggu_direktur','disetujui'], ['selesai'],
    ];
    var currentStepIndex = -1;
    steps.forEach(function(group, idx) {
        if (group.includes(data.status)) { currentStepIndex = idx; }
    });

    var isTrackable = !['draft', 'revisi', 'ditolak', 'ditolak_penerima', 'ditunda'].includes(data.status);
    var trackerHtml = '';

    if (isTrackable && currentStepIndex !== -1) {
        var trackerSteps = [
            {label: 'Diajukan', kolom: 'tglDiajukan'}, {label: 'Koordinator', kolom: 'tglKoordinator'},
            {label: 'Kabid', kolom: 'tglKabid'}, {label: 'Keuangan', kolom: 'tglKeuangan'},
            {label: 'PPK', kolom: 'tglPpk'}, {label: 'KPA', kolom: 'tglKpa'},
            {label: 'Direktur', kolom: 'tglDirektur'}, {label: 'Selesai', kolom: 'tglSelesai'},
        ];
        
        trackerHtml += `<div class="mb-4"><h6 class="fw-semibold small text-muted mb-3"><i class="bi bi-signpost-split me-1"></i>Progres Persetujuan</h6><div class="status-tracker">`;
        
        trackerSteps.forEach(function(step, i) {
            var isActive = i <= currentStepIndex;
            var tglRaw = data[step.kolom];
            var tglStr = '';
            if (isActive && tglRaw) {
                var d = new Date(tglRaw);
                tglStr = `<span class="tracker-date">${d.getDate().toString().padStart(2, '0')}/${(d.getMonth()+1).toString().padStart(2, '0')}/${d.getFullYear().toString().slice(-2)}</span>`;
            } else {
                tglStr = `<span class="tracker-date">—</span>`;
            }
            trackerHtml += `
                <div class="tracker-step ${isActive ? 'active' : ''}">
                    <div class="tracker-dot"><i class="bi bi-check-lg"></i></div>
                    <span class="tracker-label">${step.label}</span>
                    ${tglStr}
                </div>
            `;
        });
        trackerHtml += `</div></div>`;
    } else if (['ditolak', 'ditolak_penerima'].includes(data.status)) {
        trackerHtml = `<div class="alert alert-danger-subtle bg-danger-subtle border-0 rounded-3 d-flex align-items-center gap-2 mb-4"><i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i><span class="small text-danger fw-semibold">Pengajuan ini ditolak</span></div>`;
    } else if (data.status === 'ditunda') {
        trackerHtml = `<div class="alert alert-warning-subtle bg-warning-subtle border-0 rounded-3 d-flex align-items-center gap-2 mb-4"><i class="bi bi-pause-circle-fill text-warning fs-5"></i><span class="small text-warning-emphasis fw-semibold">Pengajuan ini ditunda</span></div>`;
    }

    // Render HTML
    var modalHtml = `
        <div class="modal-header-modern border-0 p-4" style="background: linear-gradient(135deg, #f8f9ff 0%, #e8edff 100%);">
            <button type="button" class="btn-close-modern" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-x-lg"></i></button>
            <div class="d-flex align-items-center gap-3">
                <div class="modal-icon-badge bg-${data.color} text-white"><i class="bi bi-${data.kategori === 'pemeliharaan' ? 'tools' : 'box-seam'}"></i></div>
                <div>
                    <h5 class="fw-bold mb-1 text-dark">${data.no_pengajuan || 'Detail'}</h5>
                    <span class="badge bg-${data.color} px-3 py-2 rounded-pill"><i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i>${data.label}</span>
                </div>
            </div>
        </div>
        <div class="modal-body p-4">
            <div class="row g-3 mb-4">
                <div class="col-md-4"><div class="info-tile"><span class="info-tile-label"><i class="bi bi-calendar3 me-1"></i>Tanggal Pengajuan</span><span class="info-tile-value">${data.tanggal}</span></div></div>
                <div class="col-md-4"><div class="info-tile"><span class="info-tile-label"><i class="bi bi-building me-1"></i>Bidang</span><span class="info-tile-value">${data.bidang}</span></div></div>
                <div class="col-md-4"><div class="info-tile"><span class="info-tile-label"><i class="bi bi-cash-coin me-1"></i>Total Pengajuan</span><span class="info-tile-value fw-bold text-primary">${data.total}</span></div></div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6"><div class="info-tile"><span class="info-tile-label"><i class="bi bi-person me-1"></i>Pemohon</span><span class="info-tile-value">${data.pemohon}</span></div></div>
                <div class="col-md-6"><div class="info-tile"><span class="info-tile-label"><i class="bi bi-briefcase me-1"></i>Jabatan</span><span class="info-tile-value">${data.jabatan}</span></div></div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6"><div class="info-tile"><span class="info-tile-label"><i class="bi bi-clipboard-check me-1"></i>Alasan Justifikasi</span><span class="info-tile-value">${data.alasan}</span></div></div>
                <div class="col-md-6"><div class="info-tile"><span class="info-tile-label"><i class="bi bi-star me-1"></i>Manfaat</span><span class="info-tile-value">${data.manfaat}</span></div></div>
            </div>
            ${trackerHtml}
            <h6 class="fw-semibold small text-muted mb-3"><i class="bi bi-basket me-1"></i>Daftar Barang</h6>
            <div class="table-responsive rounded-3 border">
                <table class="table table-sm mb-0 align-middle">
                    <thead class="bg-light"><tr><th class="py-2 px-3 small">Nama Barang</th><th class="py-2 px-3 small text-center">Jumlah</th><th class="py-2 px-3 small text-end">Harga Satuan</th><th class="py-2 px-3 small text-end">Subtotal</th></tr></thead>
                    <tbody>${itemsHtml}</tbody>
                    <tfoot><tr class="bg-light"><td colspan="3" class="px-3 text-end fw-bold small">Total</td><td class="px-3 text-end fw-bold text-primary">${data.total}</td></tr></tfoot>
                </table>
            </div>
            ${data.dampak ? `<div class="mt-4"><h6 class="fw-semibold small text-muted mb-2"><i class="bi bi-exclamation-triangle me-1"></i>Dampak Jika Tidak Dilaksanakan</h6><div class="bg-light rounded-3 p-3 small">${data.dampak}</div></div>` : ''}
            ${data.kondisi || data.ket ? `<div class="mt-4"><h6 class="fw-semibold small text-muted mb-2"><i class="bi bi-info-circle me-1"></i>Kondisi Barang Lama</h6><div class="bg-light rounded-3 p-3 small">${data.kondisi ? `<span class="fw-semibold">Kondisi:</span> ${data.kondisi}<br>` : ''}${data.ket ? `<span class="fw-semibold">Keterangan:</span> ${data.ket}` : ''}</div></div>` : ''}
        </div>
        <div class="modal-footer border-0 p-4 pt-0">
            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i>Tutup</button>
            ${data.status === 'disetujui_kabid' ? `<a href="/direktur/pengadaan/${data.id}/detail" class="btn btn-success rounded-pill px-4"><i class="bi bi-check2-circle me-1"></i>Verifikasi</a>` : ''}
            ${data.status === 'disetujui' ? `<a href="/direktur/pengadaan/${data.id}/cetak" target="_blank" class="btn btn-primary rounded-pill px-4"><i class="bi bi-printer me-1"></i>Cetak Formulir</a>` : ''}
        </div>
    `;

    document.getElementById('modalContentGlobal').innerHTML = modalHtml;
    
    var modalElement = document.getElementById('modalDetailGlobal');
    
    // HAPUS modal-dialog-centered BIAR NEMPEL KE ATAS DAN BISA DI-SCROLL
    var dialogEl = modalElement.querySelector('.modal-dialog');
    if (dialogEl) {
        dialogEl.classList.remove('modal-dialog-centered');
    }

    var modal = new bootstrap.Modal(modalElement, { backdrop: false, keyboard: true });
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(el) { return new bootstrap.Tooltip(el); });
    
    document.querySelectorAll('.modal').forEach(function(modal) {
        modal.addEventListener('hidden.bs.modal', function() {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
    });
});
</script>
@endsection