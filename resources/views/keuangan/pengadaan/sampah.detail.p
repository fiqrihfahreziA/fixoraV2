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
                            'draft' => 'secondary',
                            'diajukan' => 'warning',
                            'revisi' => 'warning',
                            'disetujui_koordinator' => 'info',
                            'disetujui_kabid' => 'primary',
                            'menunggu_direktur' => 'warning',
                            'disetujui' => 'success',
                            'ditolak' => 'danger',
                            'ditolak_penerima' => 'danger',
                            'ditolak_keuangan' => 'danger'
                        ][$pengajuan->status ?? 'draft'] ?? 'secondary';
                        
                        $statusLabel = [
                            'draft' => 'Draft',
                            'diajukan' => 'Diajukan',
                            'revisi' => 'Revisi',
                            'disetujui_koordinator' => 'Disetujui Koordinator',
                            'disetujui_kabid' => 'Disetujui Kabid',
                            'menunggu_direktur' => 'Menunggu Direktur',
                            'disetujui' => 'Disetujui',
                            'ditolak' => 'Ditolak',
                            'ditolak_penerima' => 'Ditolak Penerima',
                            'ditolak_keuangan' => 'Ditolak Keuangan'
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
        <!-- AKSI VERIFIKASI KEUANGAN (DI BAWAH SEMUA DATA) -->
        <!-- ============================================ -->
        @if(in_array($pengajuan->status ?? '', ['disetujui_kabid', 'sesuai standard']))
        <div class="card border-0 shadow-sm mb-4" style="background-color: #fff3cd; border-left: 4px solid #ffc107;">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <h6 class="fw-bold mb-2">
                            <i class="bi bi-check2-circle text-primary me-2"></i>
                            Verifikasi Anggaran Keuangan
                        </h6>
                        <p class="text-muted small mb-3">
                            ✅ Silakan periksa seluruh data pengajuan di atas sebelum melakukan verifikasi.
                            Pilih opsi verifikasi di bawah ini.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalVerifikasiLengkap">
                                <i class="bi bi-check-circle me-1"></i> Anggaran Tersedia (Setujui)
                            </button>
                            <button class="btn btn-warning rounded-pill px-4 text-dark" data-bs-toggle="modal" data-bs-target="#modalVerifikasiSebagian">
                                <i class="bi bi-pencil-square me-1"></i> Anggaran Sebagian
                            </button>
                            <button class="btn btn-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTolakKeuangan">
                                <i class="bi bi-x-circle me-1"></i> Tolak
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
                    <div class="col-12">
                        <!-- <a href="#" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalPengaju">
                            <i class="bi bi-eye me-1"></i> Lihat Detail Lengkap Pengaju
                        </a> -->
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
                    <div class="col-12">
                        <!-- <a href="#" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalPenerima">
                            <i class="bi bi-eye me-1"></i> Lihat Detail Lengkap Penerima
                        </a> -->
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
                                @if(isset($pengajuan->disetujui_kabid_at) && $pengajuan->disetujui_kabid_at)
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
                            <div class="value">{{ isset($pengajuan->disetujui_kabid_at) && $pengajuan->disetujui_kabid_at ? date('d M Y H:i', strtotime($pengajuan->disetujui_kabid_at)) : '-' }}</div>
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
                    <div class="col-12">
                        <!-- <a href="#" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalAtasan">
                            <i class="bi bi-eye me-1"></i> Lihat Detail Lengkap Atasan
                        </a> -->
                    </div>
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
                    <!-- 1. Pengajuan -->
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

                    <!-- 2. Atasan -->
                    <div class="timeline-item">
                        <div class="timeline-icon {{ isset($pengajuan->disetujui_kabid_at) && $pengajuan->disetujui_kabid_at ? 'bg-success' : 'bg-secondary' }}">
                            <i class="bi bi-person-up text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="fw-bold mb-0">2. Persetujuan Atasan</h6>
                            <small class="text-muted">{{ $pengajuan->atasan->nama ?? 'Atasan' }}</small>
                            @if(isset($pengajuan->disetujui_kabid_at) && $pengajuan->disetujui_kabid_at)
                                <p class="mb-0 small text-success">
                                    <i class="bi bi-check-circle"></i> Disetujui 
                                    {{ date('d M Y', strtotime($pengajuan->disetujui_kabid_at)) }}
                                </p>
                            @else
                                <p class="mb-0 small text-warning">⏳ Menunggu persetujuan</p>
                            @endif
                        </div>
                    </div>

                    <!-- 3. Penerima -->
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

                    <!-- 4. Keuangan -->
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

                    <!-- 5. Direktur -->
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
<!-- MODAL VERIFIKASI LENGKAP (Anggaran Tersedia) -->
<!-- ============================================ -->
<div class="modal fade" id="modalVerifikasiLengkap" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle me-2"></i>Verifikasi Anggaran Tersedia
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('keuangan.verifikasi.lengkap', $pengajuan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="bi bi-info-circle me-2"></i>
                        Total anggaran tersedia penuh sebesar 
                        <strong>Rp {{ number_format($pengajuan->total_pengajuan ?? 0, 0, ',', '.') }}</strong>
                        akan disetujui seluruhnya.
                    </div>
                    
                    <input type="hidden" name="total_disetujui" value="{{ $pengajuan->total_pengajuan ?? 0 }}">
                    <input type="hidden" name="id_keuangan" value="{{ $authUser->karyawan->id }}">
                    
                    <div class="mb-3">
                        <label for="catatan_keuangan_lengkap" class="form-label">Catatan Verifikasi (Opsional)</label>
                        <textarea name="catatan_keuangan" id="catatan_keuangan_lengkap" class="form-control" rows="3" placeholder="Isi catatan jika diperlukan..."></textarea>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="setuju_lengkap" required>
                        <label class="form-check-label" for="setuju_lengkap">
                            Saya menyatakan bahwa anggaran tersedia penuh dan pengajuan ini layak diteruskan ke Direktur.
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btnLengkap" disabled>
                        <i class="bi bi-check2-circle me-1"></i> Setujui Penuh
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL VERIFIKASI SEBAGIAN (Anggaran Sebagian) -->
<!-- ============================================ -->
<div class="modal fade" id="modalVerifikasiSebagian" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-2"></i>Verifikasi Anggaran Sebagian
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('keuangan.verifikasi.sebagian', $pengajuan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Total pengajuan: <strong>Rp {{ number_format($pengajuan->total_pengajuan ?? 0, 0, ',', '.') }}</strong>
                        <br>
                        Silakan input nominal yang disetujui (tidak boleh lebih dari total pengajuan).
                    </div>
                    
                    <input type="hidden" name="id_keuangan" value="{{ $authUser->karyawan->id }}">
                    
                    <div class="mb-3">
                        <label for="total_disetujui_sebagian" class="form-label fw-bold">
                            Total Anggaran yang Disetujui <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   name="total_disetujui" 
                                   id="total_disetujui_sebagian" 
                                   class="form-control" 
                                   placeholder="Masukkan nominal yang disetujui"
                                   min="1"
                                   max="{{ $pengajuan->total_pengajuan ?? 0 }}"
                                   required
                                   oninput="validasiAnggaran(this)">
                            <span class="input-group-text">.00</span>
                        </div>
                        <small class="text-muted">
                            Maksimal: Rp {{ number_format($pengajuan->total_pengajuan ?? 0, 0, ',', '.') }}
                        </small>
                        <div id="peringatanAnggaran" class="text-danger small mt-1" style="display: none;">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            Nominal tidak boleh melebihi total pengajuan!
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="catatan_keuangan_sebagian" class="form-label">Catatan Verifikasi (Opsional)</label>
                        <textarea name="catatan_keuangan" id="catatan_keuangan_sebagian" class="form-control" rows="3" placeholder="Isi catatan jika diperlukan..."></textarea>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="setuju_sebagian" required>
                        <label class="form-check-label" for="setuju_sebagian">
                            Saya menyatakan bahwa data pengajuan ini disetujui sebagian dan layak diteruskan ke Direktur.
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark" id="btnSebagian" disabled>
                        <i class="bi bi-check2-circle me-1"></i> Setujui Sebagian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL TOLAK KEUANGAN -->
<!-- ============================================ -->
<div class="modal fade" id="modalTolakKeuangan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-x-circle me-2"></i>Tolak Pengajuan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('keuangan.tolak', $pengajuan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Anda akan menolak pengajuan <strong>{{ $pengajuan->no_pengajuan ?? 'N/A' }}</strong>.
                    </div>
                    
                    <input type="hidden" name="penerima_id" value="{{ $authUser->karyawan->id }}">
                    
                    <div class="mb-3">
                        <label for="alasan_tolak" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="alasan_tolak" id="alasan_tolak" class="form-control" rows="3" required placeholder="Isi alasan penolakan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i> Tolak Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL PENGAJU -->
<div class="modal fade" id="modalPengaju" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-person-badge me-2"></i>Detail Lengkap Pengaju
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-item-modal">
                            <div class="label">Nama Lengkap</div>
                            <div class="value">{{ $pengajuan->karyawan->nama ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item-modal">
                            <div class="label">NIP / NIK</div>
                            <div class="value">{{ $pengajuan->karyawan->nip ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item-modal">
                            <div class="label">Jabatan</div>
                            <div class="value">{{ $pengajuan->karyawan->jabatan ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item-modal">
                            <div class="label">Bidang</div>
                            <div class="value">{{ $pengajuan->bidang->nama_bidang ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item-modal">
                            <div class="label">Instalasi</div>
                            <div class="value">{{ $pengajuan->instalasi ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item-modal">
                            <div class="label">Email</div>
                            <div class="value">{{ $pengajuan->karyawan->email ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item-modal">
                            <div class="label">No. Telepon</div>
                            <div class="value">{{ $pengajuan->karyawan->no_telepon ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item-modal">
                            <div class="label">Tanggal Bergabung</div>
                            <div class="value">{{ isset($pengajuan->karyawan->tanggal_bergabung) && $pengajuan->karyawan->tanggal_bergabung ? date('d M Y', strtotime($pengajuan->karyawan->tanggal_bergabung)) : '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PENERIMA -->
<div class="modal fade" id="modalPenerima" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-person-check me-2"></i>Detail Lengkap Penerima
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-item-modal">
                            <div class="label">Nama Lengkap</div>
                            <div class="value">{{ $pengajuan->penerima->nama ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item-modal">
                            <div class="label">NIP / NIK</div>
                            <div class="value">{{ $pengajuan->penerima->nip ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item-modal">
                            <div class="label">Jabatan</div>
                            <div class="value">{{ $pengajuan->penerima->jabatan ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item-modal">
                            <div class="label">Bidang</div>
                            <div class="value">{{ $pengajuan->penerima->bidang->nama_bidang ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item-modal">
                            <div class="label">Email</div>
                            <div class="value">{{ $pengajuan->penerima->email ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item-modal">
                            <div class="label">No. Telepon</div>
                            <div class="value">{{ $pengajuan->penerima->no_telepon ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item-modal">
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
                    <div class="col-md-6">
                        <div class="info-item-modal">
                            <div class="label">Tanggal Konfirmasi</div>
                            <div class="value">{{ isset($pengajuan->tanggal_konfirmasi_penerima) && $pengajuan->tanggal_konfirmasi_penerima ? date('d M Y H:i', strtotime($pengajuan->tanggal_konfirmasi_penerima)) : '-' }}</div>
                        </div>
                    </div>
                    @if(isset($pengajuan->catatan_penerima) && $pengajuan->catatan_penerima)
                    <div class="col-12">
                        <div class="info-item-modal">
                            <div class="label">Catatan Penerima</div>
                            <div class="value">{{ $pengajuan->catatan_penerima }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL ATASAN -->
<div class="modal fade" id="modalAtasan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-person-up me-2"></i>Detail Lengkap Atasan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-item-modal">
                            <div class="label">Nama Lengkap</div>
                            <div class="value">{{ $pengajuan->atasan->nama ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item-modal">
                            <div class="label">NIP / NIK</div>
                            <div class="value">{{ $pengajuan->atasan->nip ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item-modal">
                            <div class="label">Jabatan</div>
                            <div class="value">{{ $pengajuan->atasan->jabatan ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item-modal">
                            <div class="label">Bidang</div>
                            <div class="value">{{ $pengajuan->atasan->bidang->nama_bidang ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item-modal">
                            <div class="label">Email</div>
                            <div class="value">{{ $pengajuan->atasan->email ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item-modal">
                            <div class="label">No. Telepon</div>
                            <div class="value">{{ $pengajuan->atasan->no_telepon ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item-modal">
                            <div class="label">Status Persetujuan</div>
                            <div class="value">
                                @if(isset($pengajuan->disetujui_kabid_at) && $pengajuan->disetujui_kabid_at)
                                    <span class="badge bg-success">Sudah Menyetujui</span>
                                @else
                                    <span class="badge bg-warning">Menunggu Persetujuan</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item-modal">
                            <div class="label">Tanggal Persetujuan</div>
                            <div class="value">{{ isset($pengajuan->disetujui_kabid_at) && $pengajuan->disetujui_kabid_at ? date('d M Y H:i', strtotime($pengajuan->disetujui_kabid_at)) : '-' }}</div>
                        </div>
                    </div>
                    @if(isset($pengajuan->catatan_bidang) && $pengajuan->catatan_bidang)
                    <div class="col-12">
                        <div class="info-item-modal">
                            <div class="label">Catatan Atasan</div>
                            <div class="value">{{ $pengajuan->catatan_bidang }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
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

.info-item-modal {
    background: #f8f9fa;
    padding: 12px 16px;
    border-radius: 8px;
    border-left: 3px solid #0d6efd;
}
.info-item-modal .label {
    font-size: 0.7rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}
.info-item-modal .value {
    font-size: 1rem;
    font-weight: 500;
    color: #1a1a2e;
    margin-top: 2px;
}

/* Timeline Styles */
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}
.timeline-item {
    position: relative;
    margin-bottom: 25px;
}
.timeline-item:last-child {
    margin-bottom: 0;
}
.timeline-icon {
    position: absolute;
    left: -22px;
    top: 0;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
}
.timeline-icon i {
    font-size: 14px;
}
.timeline-content {
    padding-left: 5px;
}
.timeline-content h6 {
    font-size: 0.9rem;
}
.timeline-content small {
    font-size: 0.75rem;
}
.timeline-content p {
    font-size: 0.8rem;
}

/* Validasi Anggaran */
.input-group .form-control:focus {
    border-color: #ffc107;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
}

/* ============================================ */
/* MODAL - TANPA BACKDROP SAMA SEKALI */
/* ============================================ */
.modal-backdrop {
    display: none !important;
}

.modal {
    background: transparent !important;
    pointer-events: none;
}

.modal-dialog {
    pointer-events: auto;
    margin: 1.75rem auto;
}

.modal-content {
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    border: 1px solid rgba(0, 0, 0, 0.06);
    border-radius: 12px;
    background: #fff;
}

/* Animasi modal */
.modal.fade .modal-dialog {
    transform: scale(0.92) translateY(-30px);
    opacity: 0;
    transition: transform 0.25s ease-out, opacity 0.25s ease-out;
}

.modal.show .modal-dialog {
    transform: scale(1) translateY(0);
    opacity: 1;
}
</style>

<script>
// Validasi agar total_disetujui tidak melebihi total_pengajuan
function validasiAnggaran(input) {
    var max = {{ $pengajuan->total_pengajuan ?? 0 }};
    var nilai = parseInt(input.value) || 0;
    var peringatan = document.getElementById('peringatanAnggaran');
    var btnSubmit = document.getElementById('btnSebagian');
    
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

// Event listener untuk checkbox di modal verifikasi
document.addEventListener('DOMContentLoaded', function() {
    // Verifikasi Lengkap
    var checkboxLengkap = document.getElementById('setuju_lengkap');
    var btnLengkap = document.getElementById('btnLengkap');
    if (checkboxLengkap && btnLengkap) {
        checkboxLengkap.addEventListener('change', function() {
            btnLengkap.disabled = !this.checked;
        });
    }
    
    // Verifikasi Sebagian
    var checkboxSebagian = document.getElementById('setuju_sebagian');
    var btnSebagian = document.getElementById('btnSebagian');
    if (checkboxSebagian && btnSebagian) {
        checkboxSebagian.addEventListener('change', function() {
            btnSebagian.disabled = !this.checked;
        });
    }
});
</script>
@endsection