@extends('layouts.pengadaan.keuangan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark">
            <i class="bi bi-file-earmark-text text-primary me-2"></i>
            Detail Pengajuan - {{ $pengajuan->no_pengajuan ?? 'N/A' }}
        </h4>
        <span class="text-muted">Pengajuan oleh: {{ $pengajuan->karyawan->nama ?? '-' }}</span>
    </div>
    <a href="{{ route('keuangan.pengadaan') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<!-- Status Pengajuan -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    @php
                        $statusBadge = [
                            'draft' => 'secondary','diajukan' => 'warning','revisi' => 'warning',
                            'disetujui_koordinator' => 'info','menunggu_keuangan' => 'primary',
                            'menunggu_direktur' => 'warning','disetujui' => 'success',
                            'ditolak' => 'danger','ditolak_penerima' => 'danger','ditolak_keuangan' => 'danger'
                        ][$pengajuan->status ?? 'draft'] ?? 'secondary';

                        $statusLabel = [
                            'draft' => 'Draft','diajukan' => 'Diajukan','revisi' => 'Revisi',
                            'disetujui_koordinator' => 'Disetujui Koordinator','menunggu_keuangan' => 'Disetujui PPK',
                            'menunggu_direktur' => 'Menunggu Direktur','disetujui' => 'Disetujui',
                            'ditolak' => 'Ditolak','ditolak_penerima' => 'Ditolak Penerima','ditolak_keuangan' => 'Ditolak Keuangan'
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
                    @if(isset($pengajuan->menunggu_keuangan_at) && $pengajuan->menunggu_keuangan_at)
                    <small class="text-success">
                        <i class="bi bi-check-circle me-1"></i>
                        Disetujui Atasan: {{ date('d M Y H:i', strtotime($pengajuan->menunggu_keuangan_at)) }}
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

        @if(isset($pengajuan->catatan_bidang) && $pengajuan->catatan_bidang)
        <div class="mt-3 p-3 bg-light rounded">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-chat-quote text-primary mt-1"></i>
                <div>
                    <small class="text-muted fw-semibold">Catatan dari Atasan (Kabid):</small>
                    <p class="mb-0 text-secondary">{{ $pengajuan->catatan_bidang }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Jika sudah diverifikasi -->
@if(($pengajuan->status ?? '') == 'disetujui' && isset($pengajuan->verified_by))
<div class="card border-0 shadow-sm mb-4 bg-success bg-opacity-10">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill text-success fs-4 me-3"></i>
            <div>
                <h6 class="fw-bold mb-0 text-success">Pengajuan Telah Diverifikasi</h6>
                <small class="text-muted">
                    Diverifikasi oleh: {{ $pengajuan->verified_by ?? 'Petugas Keuangan' }} 
                    pada {{ isset($pengajuan->verified_at) ? date('d M Y H:i', strtotime($pengajuan->verified_at)) : '-' }}
                </small>
                @if(isset($pengajuan->total_disetujui) && $pengajuan->total_disetujui)
                <br>
                <small class="text-primary fw-bold">
                    Total Disetujui: Rp {{ number_format($pengajuan->total_disetujui, 0, ',', '.') }}
                </small>
                @endif
            </div>
        </div>
        @if(isset($pengajuan->catatan_keuangan) && $pengajuan->catatan_keuangan)
        <div class="mt-2 p-2 bg-white rounded">
            <small class="text-muted fw-semibold">Catatan Verifikasi:</small>
            <p class="mb-0 text-secondary small">{{ $pengajuan->catatan_keuangan }}</p>
        </div>
        @endif
    </div>
</div>
@endif

<!-- Jika ditolak keuangan -->
@if(($pengajuan->status ?? '') == 'ditolak_keuangan')
<div class="card border-0 shadow-sm mb-4 bg-danger bg-opacity-10">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <i class="bi bi-x-circle-fill text-danger fs-4 me-3"></i>
            <div>
                <h6 class="fw-bold mb-0 text-danger">Pengajuan Ditolak oleh Keuangan</h6>
                <small class="text-muted">
                    Ditolak oleh: {{ $pengajuan->tolak_by ?? 'Petugas Keuangan' }} 
                    pada {{ isset($pengajuan->tolak_at) ? date('d M Y H:i', strtotime($pengajuan->tolak_at)) : '-' }}
                </small>
                @if(isset($pengajuan->alasan_tolak) && $pengajuan->alasan_tolak)
                <br>
                <small class="text-danger">Alasan: {{ $pengajuan->alasan_tolak }}</small>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- ============================================ -->
<!-- PANEL VERIFIKASI (SIMPLE) -->
<!-- ============================================ -->
@if(in_array($pengajuan->status ?? '', ['menunggu_keuangan', 'sesuai standard']))
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
                        <h5 class="mb-0 fw-bold">Anggaran Keuangan</h5>
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
                    <button type="button" class="btn btn-light btn-sm px-3" onclick="openDrawer('lengkap')">
                        <i class="bi bi-check-circle text-success me-1"></i> Setujui Penuh
                    </button>
                    <button type="button" class="btn btn-light btn-sm px-3" onclick="openDrawer('sebagian')">
                        <i class="bi bi-sliders text-warning me-1"></i> Sebagian
                    </button>
                    <button type="button" class="btn btn-light btn-sm px-3" onclick="openDrawer('tolak')">
                        <i class="bi bi-x-circle text-danger me-1"></i> Tolak
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">
    <!-- Kolom Kiri -->
    <div class="col-lg-8">
        <!-- Informasi Pengaju -->
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

        <!-- Informasi Penerima -->
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

        <!-- Informasi Atasan -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom border-primary">
                <h6 class="fw-bold mb-0 text-primary">
                    <i class="bi bi-person-up me-2"></i>Informasi Atasan (Approval)
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-item bg-primary bg-opacity-10">
                            <div class="label">Nama Atasan</div>
                            <div class="value">{{ $pengajuan->atasan->nama ?? 'Belum ditentukan' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item bg-primary bg-opacity-10">
                            <div class="label">Jabatan Atasan</div>
                            <div class="value">{{ $pengajuan->atasan->jabatan ?? 'Belum ditentukan' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item bg-primary bg-opacity-10">
                            <div class="label">Status Persetujuan</div>
                            <div class="value">
                                @if(isset($pengajuan->menunggu_keuangan_at) && $pengajuan->menunggu_keuangan_at)
                                    <span class="badge bg-success">Sudah Menyetujui</span>
                                @else
                                    <span class="badge bg-warning">Menunggu Persetujuan</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item bg-primary bg-opacity-10">
                            <div class="label">Tanggal Persetujuan</div>
                            <div class="value">{{ isset($pengajuan->menunggu_keuangan_at) && $pengajuan->menunggu_keuangan_at ? date('d M Y H:i', strtotime($pengajuan->menunggu_keuangan_at)) : '-' }}</div>
                        </div>
                    </div>
                    @if(isset($pengajuan->catatan_bidang) && $pengajuan->catatan_bidang)
                    <div class="col-12">
                        <div class="info-item bg-primary bg-opacity-10">
                            <div class="label">Catatan Atasan</div>
                            <div class="value">{{ $pengajuan->catatan_bidang }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Alasan & Manfaat -->
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

        <!-- Dampak -->
        <div class="card border-warning shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold text-warning mb-2">
                    <i class="bi bi-exclamation-triangle me-2"></i>Dampak Jika Tidak Dilaksanakan
                </h6>
                <p class="mb-0 text-secondary">{{ $pengajuan->dampak ?? '-' }}</p>
            </div>
        </div>

        <!-- Kondisi Barang Lama -->
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

        <!-- Daftar Barang -->
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
                            @if(isset($pengajuan->total_disetujui) && $pengajuan->total_disetujui)
                            <tr>
                                <td colspan="3" class="text-end fw-bold text-success">Total Disetujui</td>
                                <td class="text-end fw-bold text-success">
                                    Rp {{ number_format($pengajuan->total_disetujui, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endif
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- PDF Data Kerusakan -->
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
                        <a href="{{ asset('storage/' . $pengajuan->data_kerusakan) }}" 
                           download 
                           class="btn btn-outline-danger btn-sm rounded-pill px-3">
                            <i class="bi bi-download me-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Kolom Kanan: Timeline & Informasi -->
    <div class="col-lg-4">
        <!-- Ringkasan -->
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
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                    <span class="text-muted">Atasan</span>
                    <span class="fw-semibold">{{ $pengajuan->atasan->nama ?? 'Belum ditentukan' }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Total Pengajuan</span>
                    <span class="text-primary fw-bold">Rp {{ number_format($pengajuan->total_pengajuan ?? 0, 0, ',', '.') }}</span>
                </div>
                @if(isset($pengajuan->total_disetujui) && $pengajuan->total_disetujui)
                <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                    <span class="text-muted">Total Disetujui</span>
                    <span class="text-success fw-bold">Rp {{ number_format($pengajuan->total_disetujui, 0, ',', '.') }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Timeline Persetujuan -->
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
                        <div class="timeline-icon {{ isset($pengajuan->menunggu_keuangan_at) && $pengajuan->menunggu_keuangan_at ? 'bg-success' : 'bg-secondary' }}">
                            <i class="bi bi-person-up text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="fw-bold mb-0">2. Persetujuan Atasan</h6>
                            <small class="text-muted">{{ $pengajuan->atasan->nama ?? 'Atasan' }}</small>
                            @if(isset($pengajuan->menunggu_keuangan_at) && $pengajuan->menunggu_keuangan_at)
                                <p class="mb-0 small text-success">
                                    <i class="bi bi-check-circle"></i> Disetujui 
                                    {{ date('d M Y', strtotime($pengajuan->menunggu_keuangan_at)) }}
                                </p>
                            @else
                                <p class="mb-0 small text-warning">⏳ Menunggu persetujuan</p>
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
                            @elseif(($pengajuan->status_penerimaan ?? '') == 'ditolak')
                                <p class="mb-0 small text-danger">
                                    <i class="bi bi-x-circle"></i> Ditolak penerima
                                </p>
                            @else
                                <p class="mb-0 small text-warning">⏳ Menunggu konfirmasi penerima</p>
                            @endif
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-icon {{ ($pengajuan->status ?? '') == 'disetujui' ? 'bg-success' : (($pengajuan->status ?? '') == 'ditolak_keuangan' ? 'bg-danger' : 'bg-primary') }}">
                            <i class="bi bi-cash-stack text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="fw-bold mb-0">4. Verifikasi Keuangan</h6>
                            <small class="text-muted">Petugas Keuangan</small>
                            @if(($pengajuan->status ?? '') == 'disetujui')
                                <p class="mb-0 small text-success">
                                    <i class="bi bi-check-circle"></i> Telah diverifikasi
                                    @if(isset($pengajuan->verified_at))
                                        {{ date('d M Y', strtotime($pengajuan->verified_at)) }}
                                    @endif
                                </p>
                            @elseif(($pengajuan->status ?? '') == 'ditolak_keuangan')
                                <p class="mb-0 small text-danger">
                                    <i class="bi bi-x-circle"></i> Ditolak oleh Keuangan
                                </p>
                            @else
                                <p class="mb-0 small text-warning">⏳ Menunggu verifikasi</p>
                            @endif
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-icon {{ ($pengajuan->status ?? '') == 'disetujui_direktur' ? 'bg-success' : 'bg-secondary' }}">
                            <i class="bi bi-building text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="fw-bold mb-0">5. Persetujuan Direktur</h6>
                            <small class="text-muted">Direktur</small>
                            @if(($pengajuan->status ?? '') == 'disetujui_direktur')
                                <p class="mb-0 small text-success">
                                    <i class="bi bi-check-circle"></i> Telah disetujui
                                </p>
                            @elseif(($pengajuan->status ?? '') == 'ditolak_direktur')
                                <p class="mb-0 small text-danger">
                                    <i class="bi bi-x-circle"></i> Ditolak Direktur
                                </p>
                            @else
                                <p class="mb-0 small text-muted">⏳ Menunggu persetujuan</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Tambahan -->
        <div class="card border-0 shadow-sm bg-info bg-opacity-10">
            <div class="card-body">
                <h6 class="fw-bold mb-2">
                    <i class="bi bi-lightbulb text-info me-2"></i>Informasi Verifikasi
                </h6>
                <ul class="small text-muted mb-0 ps-3">
                    <li class="mb-2">📋 Pastikan dokumen pendukung lengkap</li>
                    <li class="mb-2">💰 Cek kesesuaian anggaran</li>
                    <li class="mb-2">✅ Periksa kelengkapan data barang</li>
                    <li class="mb-2">📝 Cek catatan dari atasan</li>
                    <li>🔍 Verifikasi identitas penerima</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- SIDE DRAWER VERIFIKASI -->
<!-- ============================================ -->
<div id="drawerOverlay" class="drawer-overlay" onclick="closeDrawer()"></div>

<div id="verifDrawer" class="verif-drawer">
    {{-- Header --}}
    <div class="drawer-header" id="drawerHeader">
        <div class="d-flex align-items-center gap-3">
            <div class="drawer-icon" id="drawerIcon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold" id="drawerTitle">Setujui Penuh</h5>
                <small class="text-muted" id="drawerSubtitle">Anggaran tersedia sesuai pengajuan</small>
            </div>
        </div>
        <button type="button" class="btn-close" onclick="closeDrawer()"></button>
    </div>

    {{-- Body --}}
    <div class="drawer-body">

        {{-- FORM LENGKAP --}}
        <form action="{{ route('keuangan.verifikasi.lengkap', $pengajuan->id) }}" method="POST" id="formLengkap" class="drawer-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="total_disetujui" value="{{ $pengajuan->total_pengajuan ?? 0 }}">
            <input type="hidden" name="id_keuangan" value="{{ $authUser->karyawan->id }}">

            <div class="p-3 rounded-3 mb-3 bg-success bg-opacity-10 border border-success border-opacity-25">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Nominal Disetujui</span>
                    <span class="badge bg-success">FULL</span>
                </div>
                <div class="fs-4 fw-bold text-success mt-1">
                    Rp {{ number_format($pengajuan->total_pengajuan ?? 0, 0, ',', '.') }}
                </div>
                <small class="text-muted">{{ count($pengajuan->items ?? []) }} item disetujui seluruhnya</small>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">
                    Catatan Verifikasi <span class="text-muted fw-normal">(opsional)</span>
                </label>
                <textarea name="catatan_keuangan" class="form-control" rows="3"
                          placeholder="Tambahkan catatan jika diperlukan..."></textarea>
            </div>

            <label class="confirm-check d-flex align-items-start gap-3 p-3 rounded-3 border">
                <input class="form-check-input mt-1" type="checkbox" id="setuju_lengkap" required>
                <span class="small text-secondary">
                    Saya menyatakan anggaran <strong>tersedia penuh</strong> dan pengajuan ini
                    layak diteruskan ke Direktur.
                </span>
            </label>
        </form>

        {{-- FORM SEBAGIAN --}}
        <form action="{{ route('keuangan.verifikasi.sebagian', $pengajuan->id) }}" method="POST" id="formSebagian" class="drawer-form d-none">
            @csrf
            @method('PUT')
            <input type="hidden" name="id_keuangan" value="{{ $authUser->karyawan->id }}">

            <div class="d-flex justify-content-between align-items-center mb-2 small">
                <span class="text-muted">Total Pengajuan</span>
                <span class="fw-semibold">Rp {{ number_format($pengajuan->total_pengajuan ?? 0, 0, ',', '.') }}</span>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">
                    Nominal Disetujui <span class="text-danger">*</span>
                </label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-light fw-semibold">Rp</span>
                    <input type="number"
                           name="total_disetujui"
                           id="total_disetujui_sebagian"
                           class="form-control fw-bold"
                           placeholder="0"
                           min="1"
                           max="{{ $pengajuan->total_pengajuan ?? 0 }}"
                           required
                           oninput="validasiAnggaran(this)">
                </div>
                <div class="d-flex justify-content-between mt-2 small">
                    <span class="text-muted">Maks: Rp {{ number_format($pengajuan->total_pengajuan ?? 0, 0, ',', '.') }}</span>
                    <span id="persentaseAnggaran" class="text-muted">0%</span>
                </div>
                <div class="progress mt-2" style="height: 6px;">
                    <div id="progressAnggaran" class="progress-bar bg-warning" style="width: 0%;"></div>
                </div>
                <div id="peringatanAnggaran" class="text-danger small mt-2" style="display: none;">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    Nominal tidak boleh melebihi total pengajuan!
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">
                    Catatan Verifikasi <span class="text-muted fw-normal">(opsional)</span>
                </label>
                <textarea name="catatan_keuangan" class="form-control" rows="3"
                          placeholder="Alasan penyusutan nominal..."></textarea>
            </div>

            <label class="confirm-check d-flex align-items-start gap-3 p-3 rounded-3 border">
                <input class="form-check-input mt-1" type="checkbox" id="setuju_sebagian" required>
                <span class="small text-secondary">
                    Saya menyatakan pengajuan ini <strong>disetujui sebagian</strong> dan layak
                    diteruskan ke Direktur.
                </span>
            </label>
        </form>

        {{-- FORM TOLAK --}}
        <form action="{{ route('keuangan.tolak', $pengajuan->id) }}" method="POST" id="formTolak" class="drawer-form d-none">
            @csrf
            @method('PUT')
            <input type="hidden" name="penerima_id" value="{{ $authUser->karyawan->id }}">

            <div class="p-3 rounded-3 mb-3 bg-danger bg-opacity-10 border border-danger border-opacity-25 small text-danger">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Pengajuan akan ditolak dan <strong>tidak dapat dilanjutkan</strong>. Tindakan ini tercatat permanen.
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">
                    Alasan Penolakan <span class="text-danger">*</span>
                </label>
                <textarea name="alasan_tolak" class="form-control" rows="4" required
                          placeholder="Jelaskan alasan penolakan secara detail..."></textarea>
            </div>
        </form>
    </div>

    {{-- Footer --}}
    <div class="drawer-footer">
        <button type="button" class="btn btn-light" onclick="closeDrawer()">Batal</button>

        <button type="submit" form="formLengkap" class="btn btn-success px-4 drawer-submit" id="btnLengkap" disabled>
            <i class="bi bi-check2-circle me-1"></i> Setujui Penuh
        </button>

        <button type="submit" form="formSebagian" class="btn btn-warning text-dark px-4 drawer-submit d-none" id="btnSebagian" disabled>
            <i class="bi bi-check2-circle me-1"></i> Setujui Sebagian
        </button>

        <button type="submit" form="formTolak" class="btn btn-danger px-4 drawer-submit d-none">
            <i class="bi bi-x-circle me-1"></i> Tolak Pengajuan
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

/* Timeline Styles */
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

/* Confirm Checkbox */
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

/* ============================================ */
/* SIDE DRAWER VERIFIKASI */
/* ============================================ */

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
    top: 0;
    right: 0;
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

/* Mobile: full width */
@media (max-width: 576px) {
    .verif-drawer { width: 100%; }
    .drawer-header { padding: 16px 20px; }
    .drawer-body { padding: 16px 20px; }
    .drawer-footer { padding: 12px 20px; flex-wrap: wrap; }
    .drawer-footer .btn { flex: 1; }
}
</style>

<script>
// ============================================
// CLEANUP: hapus sisa backdrop dari versi lama
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.modal-backdrop').forEach(function(b) { b.remove(); });
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';

    // Checkbox gate: lengkap
    var checkboxLengkap = document.getElementById('setuju_lengkap');
    var btnLengkap = document.getElementById('btnLengkap');
    if (checkboxLengkap && btnLengkap) {
        checkboxLengkap.addEventListener('change', function() {
            btnLengkap.disabled = !this.checked;
        });
    }

    // Checkbox gate: sebagian
    var checkboxSebagian = document.getElementById('setuju_sebagian');
    var btnSebagian = document.getElementById('btnSebagian');
    if (checkboxSebagian && btnSebagian) {
        checkboxSebagian.addEventListener('change', function() {
            btnSebagian.disabled = !this.checked;
        });
    }
});

// ============================================
// DRAWER CONTROL
// ============================================
function openDrawer(mode) {
    var drawer = document.getElementById('verifDrawer');
    var overlay = document.getElementById('drawerOverlay');
    var icon = document.getElementById('drawerIcon');
    var title = document.getElementById('drawerTitle');
    var subtitle = document.getElementById('drawerSubtitle');

    // Sembunyikan semua form & tombol submit
    document.querySelectorAll('.drawer-form').forEach(function(f) {
        f.classList.add('d-none');
    });
    document.querySelectorAll('.drawer-submit').forEach(function(b) {
        b.classList.add('d-none');
    });

    icon.classList.remove('icon-warning', 'icon-danger');

    if (mode === 'lengkap') {
        document.getElementById('formLengkap').classList.remove('d-none');
        document.getElementById('btnLengkap').classList.remove('d-none');
        icon.innerHTML = '<i class="bi bi-check-circle-fill"></i>';
        title.textContent = 'Setujui Penuh';
        subtitle.textContent = 'Anggaran tersedia sesuai pengajuan';
    } else if (mode === 'sebagian') {
        document.getElementById('formSebagian').classList.remove('d-none');
        document.getElementById('btnSebagian').classList.remove('d-none');
        icon.classList.add('icon-warning');
        icon.innerHTML = '<i class="bi bi-sliders"></i>';
        title.textContent = 'Setujui Sebagian';
        subtitle.textContent = 'Sesuaikan nominal anggaran';
    } else if (mode === 'tolak') {
        document.getElementById('formTolak').classList.remove('d-none');
        var tolakBtn = document.querySelectorAll('.drawer-submit')[2];
        if (tolakBtn) tolakBtn.classList.remove('d-none');
        icon.classList.add('icon-danger');
        icon.innerHTML = '<i class="bi bi-x-circle-fill"></i>';
        title.textContent = 'Tolak Pengajuan';
        subtitle.textContent = '{{ $pengajuan->no_pengajuan ?? "N/A" }}';
    }

    drawer.classList.add('show');
    overlay.classList.add('show');
    // NOTE: body TIDAK di-lock supaya halaman belakang tetap scrollable
}

function closeDrawer() {
    var drawer = document.getElementById('verifDrawer');
    var overlay = document.getElementById('drawerOverlay');
    if (drawer) drawer.classList.remove('show');
    if (overlay) overlay.classList.remove('show');
    document.body.style.overflow = '';
    document.body.classList.remove('modal-open');
}

// Esc untuk close
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDrawer();
});

// ============================================
// VALIDASI ANGGARAN
// ============================================
function validasiAnggaran(input) {
    var max = {{ $pengajuan->total_pengajuan ?? 0 }};
    var nilai = parseInt(input.value) || 0;
    var peringatan = document.getElementById('peringatanAnggaran');
    var btnSubmit = document.getElementById('btnSebagian');
    var progress = document.getElementById('progressAnggaran');
    var persen = document.getElementById('persentaseAnggaran');

    var pct = max > 0 ? Math.min((nilai / max) * 100, 100) : 0;
    if (progress) progress.style.width = pct + '%';
    if (persen) persen.textContent = pct.toFixed(0) + '%';

    if (progress) {
        progress.classList.remove('bg-warning', 'bg-danger', 'bg-success');
        if (nilai > max) progress.classList.add('bg-danger');
        else if (pct === 100) progress.classList.add('bg-success');
        else progress.classList.add('bg-warning');
    }

    if (nilai > max) {
        peringatan.style.display = 'block';
        input.classList.add('is-invalid');
        if (btnSubmit) btnSubmit.disabled = true;
    } else if (nilai <= 0) {
        peringatan.style.display = 'none';
        input.classList.remove('is-invalid');
        if (btnSubmit) btnSubmit.disabled = true;
    } else {
        peringatan.style.display = 'none';
        input.classList.remove('is-invalid');
        if (btnSubmit) btnSubmit.disabled = false;
    }
}
</script>
@endsection