


@extends('layouts.pengadaan.direktur')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark">
            <i class="bi bi-file-earmark-text text-primary me-2"></i>
            Detail Pengajuan - {{ $pengajuan->no_pengajuan ?? 'N/A' }}
        </h4>
        <span class="text-muted">Pengajuan oleh: {{ $pengajuan->karyawan->nama ?? '-' }}</span>
    </div>
    <a href="{{ route('direktur.pengadaan') }}" class="btn btn-outline-secondary">
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
                            'ditolak_keuangan' => 'danger',
                            'disetujui_direktur' => 'success',
                            'disetujui_sebagian_direktur' => 'info',
                            'ditunda' => 'warning',
                            'ditolak_direktur' => 'danger'
                        ][$pengajuan->status ?? 'draft'] ?? 'secondary';
                        
                        $statusLabel = [
                            'draft' => 'Draft',
                            'diajukan' => 'Diajukan',
                            'revisi' => 'Revisi',
                            'disetujui_koordinator' => 'Disetujui Koordinator',
                            'disetujui_kabid' => 'Disetujui Kabid',
                            'menunggu_direktur' => 'Menunggu Direktur',
                            'disetujui' => 'Disetujui Keuangan',
                            'ditolak' => 'Ditolak',
                            'ditolak_penerima' => 'Ditolak Penerima',
                            'ditolak_keuangan' => 'Ditolak Keuangan',
                            'disetujui_direktur' => '✅ Disetujui Direktur',
                            'disetujui_sebagian_direktur' => '⚠️ Disetujui Sebagian',
                            'ditunda' => '⏳ Ditunda',
                            'ditolak_direktur' => '❌ Ditolak Direktur'
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
                    @if(isset($pengajuan->verified_at) && $pengajuan->verified_at)
                    <small class="text-info">
                        <i class="bi bi-check-circle me-1"></i>
                        Diverifikasi Keuangan: {{ date('d M Y H:i', strtotime($pengajuan->verified_at)) }}
                    </small>
                    @endif
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <span class="fw-bold">Total Pengajuan: </span>
                <span class="text-primary fw-bold fs-5">
                    Rp {{ number_format($pengajuan->total_pengajuan ?? 0, 0, ',', '.') }}
                </span>
                @if(isset($pengajuan->total_disetujui) && $pengajuan->total_disetujui)
                <br>
                <span class="fw-bold">Total Disetujui Keuangan: </span>
                <span class="text-success fw-bold fs-6">
                    Rp {{ number_format($pengajuan->total_disetujui, 0, ',', '.') }}
                </span>
                @endif
                @if(isset($pengajuan->total_disetujui_direktur) && $pengajuan->total_disetujui_direktur)
                <br>
                <span class="fw-bold">Total Disetujui Direktur: </span>
                <span class="text-primary fw-bold fs-6">
                    Rp {{ number_format($pengajuan->total_disetujui_direktur, 0, ',', '.') }}
                </span>
                @endif
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

        @if(isset($pengajuan->catatan_keuangan) && $pengajuan->catatan_keuangan)
        <div class="mt-3 p-3 bg-info bg-opacity-10 rounded">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-chat-quote text-info mt-1"></i>
                <div>
                    <small class="text-muted fw-semibold">Catatan Verifikasi Keuangan:</small>
                    <p class="mb-0 text-secondary">{{ $pengajuan->catatan_keuangan }}</p>
                </div>
            </div>
        </div>
        @endif

        @if(isset($pengajuan->catatan_direktur) && $pengajuan->catatan_direktur)
        <div class="mt-3 p-3 bg-primary bg-opacity-10 rounded">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-chat-quote text-primary mt-1"></i>
                <div>
                    <small class="text-muted fw-semibold">Catatan Direktur:</small>
                    <p class="mb-0 text-secondary">{{ $pengajuan->catatan_direktur }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Status Keputusan Direktur -->
@if(in_array($pengajuan->status ?? '', ['disetujui_direktur', 'disetujui_sebagian_direktur', 'ditunda', 'ditolak_direktur']))
<div class="card border-0 shadow-sm mb-4 
    @if(($pengajuan->status ?? '') == 'disetujui_direktur') bg-success bg-opacity-10
    @elseif(($pengajuan->status ?? '') == 'disetujui_sebagian_direktur') bg-warning bg-opacity-10
    @elseif(($pengajuan->status ?? '') == 'ditunda') bg-warning bg-opacity-10
    @else bg-danger bg-opacity-10 @endif
">
    <div class="card-body">
        <div class="d-flex align-items-center">
            @if(($pengajuan->status ?? '') == 'disetujui_direktur')
                <i class="bi bi-check-circle-fill text-success fs-4 me-3"></i>
            @elseif(($pengajuan->status ?? '') == 'disetujui_sebagian_direktur')
                <i class="bi bi-check-circle-fill text-warning fs-4 me-3"></i>
            @elseif(($pengajuan->status ?? '') == 'ditunda')
                <i class="bi bi-clock-fill text-warning fs-4 me-3"></i>
            @else
                <i class="bi bi-x-circle-fill text-danger fs-4 me-3"></i>
            @endif
            <div>
                <h6 class="fw-bold mb-0 
                    @if(($pengajuan->status ?? '') == 'disetujui_direktur') text-success
                    @elseif(($pengajuan->status ?? '') == 'disetujui_sebagian_direktur') text-warning
                    @elseif(($pengajuan->status ?? '') == 'ditunda') text-warning
                    @else text-danger @endif
                ">
                    @if(($pengajuan->status ?? '') == 'disetujui_direktur')
                        Pengajuan Disetujui oleh Direktur
                    @elseif(($pengajuan->status ?? '') == 'disetujui_sebagian_direktur')
                        Pengajuan Disetujui Sebagian oleh Direktur
                    @elseif(($pengajuan->status ?? '') == 'ditunda')
                        Pengajuan Ditunda oleh Direktur
                    @else
                        Pengajuan Ditolak oleh Direktur
                    @endif
                </h6>
                <small class="text-muted">
                    @if(($pengajuan->status ?? '') == 'disetujui_direktur')
                        Disetujui oleh: {{ $pengajuan->direktur_approved_by ?? 'Direktur' }} 
                    @elseif(($pengajuan->status ?? '') == 'disetujui_sebagian_direktur')
                        Disetujui sebagian oleh: {{ $pengajuan->direktur_approved_by ?? 'Direktur' }} 
                    @elseif(($pengajuan->status ?? '') == 'ditunda')
                        Ditunda oleh: {{ $pengajuan->direktur_postponed_by ?? 'Direktur' }} 
                    @else
                        Ditolak oleh: {{ $pengajuan->direktur_rejected_by ?? 'Direktur' }} 
                    @endif
                    pada {{ isset($pengajuan->direktur_action_at) ? date('d M Y H:i', strtotime($pengajuan->direktur_action_at)) : '-' }}
                </small>
                @if(isset($pengajuan->alasan_direktur) && $pengajuan->alasan_direktur)
                <br>
                <small class="text-secondary">
                    <strong>Catatan/Keterangan:</strong> {{ $pengajuan->alasan_direktur }}
                </small>
                @endif
                @if(isset($pengajuan->total_disetujui_direktur) && $pengajuan->total_disetujui_direktur)
                <br>
                <small class="fw-bold text-primary">
                    Total Disetujui: Rp {{ number_format($pengajuan->total_disetujui_direktur, 0, ',', '.') }}
                </small>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

  <!-- ============================================ -->
        <!-- AKSI PERSETUJUAN DIREKTUR (4 OPSI) -->
        <!-- ============================================ -->
        @if(($pengajuan->status ?? '') == 'disetujui' || ($pengajuan->status ?? '') == 'menunggu_direktur' || ($pengajuan->status ?? '') == 'ditunda')
        <div class="card border-0 shadow-sm mb-4" style="background-color: #cfe2ff; border-left: 4px solid #0d6efd;">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <h6 class="fw-bold mb-2">
                            <i class="bi bi-building text-primary me-2"></i>
                            Keputusan Direktur
                        </h6>
                        <p class="text-muted small mb-3">
                            ✅ Silakan periksa seluruh data pengajuan di atas, termasuk verifikasi dari Keuangan,
                            sebelum memberikan keputusan akhir.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalSetujuiDirektur">
                                <i class="bi bi-check-circle me-1"></i> Setujui
                            </button>
                            
                             <!-- {{-- Setujui Sebagian --}}
                        @if(($pengajuan->log_status_keuangan ?? '') != 'disetujui_keuangan')
                            <button class="btn btn-warning rounded-pill px-4 text-dark"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalSetujuiSebagianDirektur">
                                <i class="bi bi-check-circle me-1"></i>
                                Setujui Sebagian
                            </button>
                        @endif -->

                            <!-- <button class="btn btn-secondary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTundaDirektur">
                                <i class="bi bi-clock me-1"></i> Tunda
                            </button> -->
                            <button class="btn btn-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTolakDirektur">
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
                    {{-- <div class="col-12">
                        <a href="#" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalPengaju">
                            <i class="bi bi-eye me-1"></i> Lihat Detail Lengkap Pengaju
                        </a>
                    </div> --}}
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
                    {{-- <div class="col-12">
                        <a href="#" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalPenerima">
                            <i class="bi bi-eye me-1"></i> Lihat Detail Lengkap Penerima
                        </a>
                    </div> --}}
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
                    {{-- <div class="col-12">
                        <a href="#" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalAtasan">
                            <i class="bi bi-eye me-1"></i> Lihat Detail Lengkap Atasan
                        </a>
                    </div> --}}
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
                                @if(($pengajuan->status ?? '') == 'disetujui_sebagian_direktur' || ($pengajuan->status ?? '') == 'disetujui_direktur')
                                <th class="text-center">Status</th>
                                @endif
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
                                @if(($pengajuan->status ?? '') == 'disetujui_sebagian_direktur' || ($pengajuan->status ?? '') == 'disetujui_direktur')
                                <td class="text-center">
                                    @if(isset($item->disetujui_direktur) && $item->disetujui_direktur)
                                        <span class="badge bg-success">✓ Disetujui</span>
                                    @elseif(isset($item->disetujui_direktur) && $item->disetujui_direktur === false)
                                        <span class="badge bg-danger">✗ Ditolak</span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ ($pengajuan->status ?? '') == 'disetujui' || ($pengajuan->status ?? '') == 'disetujui' ? 5 : 4 }}" class="text-center text-muted">Tidak ada data barang</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="{{ ($pengajuan->status ?? '') == 'disetujui_sebagian_direktur' || ($pengajuan->status ?? '') == 'disetujui_direktur' ? 3 : 3 }}" class="text-end fw-bold">Total Pengajuan</td>
                                <td class="text-end fw-bold text-primary">
                                    Rp {{ number_format($pengajuan->total_pengajuan ?? 0, 0, ',', '.') }}
                                </td>
                                @if(($pengajuan->status ?? '') == 'disetujui_sebagian_direktur' || ($pengajuan->status ?? '') == 'disetujui_direktur')
                                <td></td>
                                @endif
                            </tr>
                            @if(isset($pengajuan->total_disetujui) && $pengajuan->total_disetujui)
                            <tr>
                                <td colspan="{{ ($pengajuan->status ?? '') == 'disetujui_sebagian_direktur' || ($pengajuan->status ?? '') == 'disetujui_direktur' ? 3 : 3 }}" class="text-end fw-bold text-success">Total Disetujui Keuangan</td>
                                <td class="text-end fw-bold text-success">
                                    Rp {{ number_format($pengajuan->total_disetujui, 0, ',', '.') }}
                                </td>
                                @if(($pengajuan->status ?? '') == 'disetujui_sebagian_direktur' || ($pengajuan->status ?? '') == 'disetujui_direktur')
                                <td></td>
                                @endif
                            </tr>
                            @endif
                            @if(isset($pengajuan->total_disetujui_direktur) && $pengajuan->total_disetujui_direktur)
                            <tr>
                                <td colspan="{{ ($pengajuan->status ?? '') == 'disetujui_sebagian_direktur' || ($pengajuan->status ?? '') == 'disetujui_direktur' ? 3 : 3 }}" class="text-end fw-bold text-primary">Total Disetujui Direktur</td>
                                <td class="text-end fw-bold text-primary">
                                    Rp {{ number_format($pengajuan->total_disetujui_direktur, 0, ',', '.') }}
                                </td>
                                @if(($pengajuan->status ?? '') == 'disetujui_sebagian_direktur' || ($pengajuan->status ?? '') == 'disetujui_direktur')
                                <td></td>
                                @endif
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
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                    <span class="text-muted">Total Pengajuan</span>
                    <span class="text-primary fw-bold">Rp {{ number_format($pengajuan->total_pengajuan ?? 0, 0, ',', '.') }}</span>
                </div>
                @if(isset($pengajuan->total_disetujui) && $pengajuan->total_disetujui)
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                    <span class="text-muted">Total Disetujui Keuangan</span>
                    <span class="text-success fw-bold">Rp {{ number_format($pengajuan->total_disetujui, 0, ',', '.') }}</span>
                </div>
                @endif
                @if(isset($pengajuan->total_disetujui_direktur) && $pengajuan->total_disetujui_direktur)
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                    <span class="text-muted">Total Disetujui Direktur</span>
                    <span class="text-primary fw-bold">Rp {{ number_format($pengajuan->total_disetujui_direktur, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Status</span>
                    <span class="badge bg-{{ $statusBadge }}">{{ $statusLabel }}</span>
                </div>
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
                        <div class="timeline-icon 
                            @if(($pengajuan->status ?? '') == 'disetujui_direktur') bg-success
                            @elseif(($pengajuan->status ?? '') == 'disetujui_sebagian_direktur') bg-warning
                            @elseif(($pengajuan->status ?? '') == 'ditunda') bg-warning
                            @elseif(($pengajuan->status ?? '') == 'ditolak_direktur') bg-danger
                            @else bg-secondary @endif
                        ">
                            <i class="bi bi-building text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="fw-bold mb-0">5. Keputusan Direktur</h6>
                            <small class="text-muted">Direktur</small>
                            @if(($pengajuan->status ?? '') == 'disetujui_direktur')
                                <p class="mb-0 small text-success">
                                    <i class="bi bi-check-circle"></i> Disetujui
                                    @if(isset($pengajuan->direktur_action_at))
                                        {{ date('d M Y', strtotime($pengajuan->direktur_action_at)) }}
                                    @endif
                                </p>
                            @elseif(($pengajuan->status ?? '') == 'disetujui_sebagian_direktur')
                                <p class="mb-0 small text-warning">
                                    <i class="bi bi-check-circle"></i> Disetujui Sebagian
                                    @if(isset($pengajuan->direktur_action_at))
                                        {{ date('d M Y', strtotime($pengajuan->direktur_action_at)) }}
                                    @endif
                                </p>
                            @elseif(($pengajuan->status ?? '') == 'ditunda')
                                <p class="mb-0 small text-warning">
                                    <i class="bi bi-clock"></i> Ditunda
                                    @if(isset($pengajuan->direktur_action_at))
                                        {{ date('d M Y', strtotime($pengajuan->direktur_action_at)) }}
                                    @endif
                                </p>
                            @elseif(($pengajuan->status ?? '') == 'ditolak_direktur')
                                <p class="mb-0 small text-danger">
                                    <i class="bi bi-x-circle"></i> Ditolak
                                    @if(isset($pengajuan->direktur_action_at))
                                        {{ date('d M Y', strtotime($pengajuan->direktur_action_at)) }}
                                    @endif
                                </p>
                            @else
                                <p class="mb-0 small text-warning">⏳ Menunggu keputusan</p>
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
                    <i class="bi bi-lightbulb text-info me-2"></i>Panduan Keputusan Direktur
                </h6>
                <ul class="small text-muted mb-0 ps-3">
                    <li class="mb-2">📋 Pastikan seluruh dokumen lengkap</li>
                    <li class="mb-2">💰 Periksa hasil verifikasi keuangan</li>
                    <li class="mb-2">✅ Pastikan anggaran tersedia</li>
                    <li class="mb-2">📝 Baca catatan dari atasan & keuangan</li>
                    <li class="mb-2">🎯 Pertimbangkan manfaat & dampak</li>
                    <li>🔍 Verifikasi kesesuaian dengan kebutuhan</li>
                </ul>
            </div>
        </div>
    </div>
</div>


<<!-- ============================================ -->
<!-- MODAL SETUJUI DIREKTUR (FULL) -->
<!-- ============================================ -->
<div class="modal fade" id="modalSetujuiDirektur" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle me-2"></i>Setujui Pengajuan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('direktur.setujui', $pengajuan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="bi bi-info-circle me-2"></i>
                        Total pengajuan  
                        <strong>Rp {{ number_format($pengajuan->total_disetujui_direktur ?? 0, 0, ',', '.') }}</strong>
                        {{-- <strong>Rp {{ number_format($pengajuan->total_disetujui_direktur ?? $pengajuan->total_disetujui_direktur ?? 0, 0, ',', '.') }}</strong> --}}
                        akan disetujui seluruhnya oleh direktur.
                    </div>
                    
                    <input type="hidden" name="direktur_id" value="{{ $authUser->karyawan->id }}">
                    <input type="hidden" name="total_disetujui_direktur" value="{{ $pengajuan->total_disetujui_direktur ?? $pengajuan->total_disetujui_direktur ?? 0 }}">
                    
                    <div class="mb-3">
                        <label for="catatan_direktur" class="form-label">Catatan Persetujuan (Opsional)</label>
                        <textarea name="catatan_direktur" id="catatan_direktur" class="form-control" rows="3" placeholder="Isi catatan jika diperlukan..."></textarea>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="setuju_direktur_full" required>
                        <label class="form-check-label" for="setuju_direktur">
                            Saya menyatakan bahwa anggaran tersedia penuh dan pengajuan ini layak disetujui.
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btnSetujuiFull" disabled>
                        <i class="bi bi-check2-circle me-1"></i> Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL SETUJUI SEBAGIAN DIREKTUR -->
<!-- ============================================ -->
<div class="modal fade" id="modalSetujuiSebagianDirektur" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle me-2"></i>Setujui Sebagian
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('direktur.setujui.sebagian', $pengajuan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Total yang disetujui Keuangan: <strong>Rp {{ number_format($pengajuan->total_disetujui ?? $pengajuan->total_pengajuan ?? 0, 0, ',', '.') }}</strong>
                        <br>
                       Sebagai Direktur/Wakil Direktur, saya menyetujui sebagian pengajuan tersebut dengan menyesuaikan pada ketersediaan dan alokasi anggaran yang telah disiapkan oleh bagian Keuangan
                    </div>
                    
                    <input type="hidden" name="direktur_id" value="{{ $authUser->karyawan->id }}">
                    <!-- Input Nominal -->
                    <div id="area_nominal" class="mb-3" style="display: none;">
                        <label for="total_disetujui_sebagian_direktur" class="form-label fw-bold">
                            Total Anggaran yang Disetujui <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   name="total_disetujui_direktur" 
                                   id="total_disetujui_sebagian_direktur" 
                                   class="form-control" 
                                   placeholder="Masukkan nominal yang disetujui"
                                   min="1"
                                   max="{{ $pengajuan->total_disetujui ?? $pengajuan->total_pengajuan ?? 0 }}"
                                   oninput="validasiAnggaranDirektur(this)">
                            <span class="input-group-text">.00</span>
                        </div>
                        <small class="text-muted">
                            Maksimal: Rp {{ number_format($pengajuan->total_disetujui ?? $pengajuan->total_pengajuan ?? 0, 0, ',', '.') }}
                        </small>
                        <div id="peringatanAnggaranDirektur" class="text-danger small mt-1" style="display: none;">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            Nominal tidak boleh melebihi total yang disetujui Keuangan!
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="catatan_direktur_sebagian" class="form-label">Catatan Persetujuan Sebagian (Opsional)</label>
                        <textarea name="catatan_direktur" id="catatan_direktur_sebagian" class="form-control" rows="3" placeholder="Isi catatan jika diperlukan..."></textarea>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="setuju_direktur_sebagian" required>
                        <label class="form-check-label" for="setuju_direktur_sebagian">
                            Saya menyatakan bahwa pengajuan ini disetujui sebagian dan sesuai dengan ketentuan yang berlaku.
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark" id="btnSetujuiSebagian" >
                        <i class="bi bi-check2-circle me-1"></i> Setujui Sebagian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL TUNDA DIREKTUR -->
<!-- ============================================ -->
<div class="modal fade" id="modalTundaDirektur" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-clock me-2"></i>Tunda Pengajuan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('direktur.tunda', $pengajuan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-secondary">
                        <i class="bi bi-info-circle me-2"></i>
                        Anda akan menunda pengajuan <strong>{{ $pengajuan->no_pengajuan ?? 'N/A' }}</strong>.
                        Pengajuan akan kembali ke status <strong>Menunggu Direktur</strong> dan dapat diproses kembali nanti.
                    </div>
                    
                    <input type="hidden" name="penerima_id" value="{{ $authUser->karyawan->id }}">
                    
                    <div class="mb-3">
                        <label for="alasan_tunda_direktur" class="form-label">Alasan Penundaan <span class="text-danger">*</span></label>
                        <textarea name="alasan_direktur" id="alasan_tunda_direktur" class="form-control" rows="3" required placeholder="Isi alasan penundaan..."></textarea>
                    </div>
                    
                    {{-- <div class="mb-3">
                        <label for="rencana_tindak_lanjut" class="form-label">Rencana Tindak Lanjut (Opsional)</label>
                        <textarea name="rencana_tindak_lanjut" id="rencana_tindak_lanjut" class="form-control" rows="2" placeholder="Kapan akan diproses kembali?"></textarea>
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-secondary">
                        <i class="bi bi-clock me-1"></i> Tunda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL TOLAK DIREKTUR -->
<!-- ============================================ -->
<div class="modal fade" id="modalTolakDirektur" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-x-circle me-2"></i>Tolak Pengajuan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('direktur.tolak', $pengajuan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Anda akan menolak pengajuan <strong>{{ $pengajuan->no_pengajuan ?? 'N/A' }}</strong>
                    </div>
                    
                    <input type="hidden" name="direktur_id" value="{{ $authUser->karyawan->id }}">
                    
                    <div class="mb-3">
                        <label for="alasan_tolak_direktur" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="catatan_direktur" id="alasan_tolak_direktur" class="form-control" rows="3" required placeholder="Isi alasan penolakan..."></textarea>
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

/* Modal Styles */
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

.modal.fade .modal-dialog {
    transform: scale(0.92) translateY(-30px);
    opacity: 0;
    transition: transform 0.25s ease-out, opacity 0.25s ease-out;
}

.modal.show .modal-dialog {
    transform: scale(1) translateY(0);
    opacity: 1;
}

/* Checkbox item styling */
#area_pilih_item .form-check {
    padding: 8px 12px;
    background: #f8f9fa;
    border-radius: 6px;
    transition: background 0.2s;
}
#area_pilih_item .form-check:hover {
    background: #e9ecef;
}
#area_pilih_item .form-check-input:checked + .form-check-label {
    color: #198754;
    font-weight: 500;
}
</style>

<script>
// Toggle metode setujui sebagian
function toggleMetodeSebagian(metode) {
    var areaItem = document.getElementById('area_pilih_item');
    var areaNominal = document.getElementById('area_nominal');
    var btnSubmit = document.getElementById('btnSetujuiSebagian');
    var checkbox = document.getElementById('setuju_direktur_sebagian');
    
    if (metode === 'item') {
        areaItem.style.display = 'block';
        areaNominal.style.display = 'none';
        // Cek apakah ada item yang dicentang
        var checkedItems = document.querySelectorAll('input[name="item_disetujui[]"]:checked');
        if (checkbox.checked && checkedItems.length > 0) {
            btnSubmit.disabled = false;
        } else {
            btnSubmit.disabled = true;
        }
    } else {
        areaItem.style.display = 'none';
        areaNominal.style.display = 'block';
        var nominal = document.getElementById('total_disetujui_sebagian_direktur');
        if (checkbox.checked && nominal.value && parseInt(nominal.value) > 0) {
            btnSubmit.disabled = false;
        } else {
            btnSubmit.disabled = true;
        }
    }
}

// Validasi anggaran untuk setujui sebagian (nominal)
function validasiAnggaranDirektur(input) {
    var max = {{ $pengajuan->total_disetujui ?? $pengajuan->total_pengajuan ?? 0 }};
    var nilai = parseInt(input.value) || 0;
    var peringatan = document.getElementById('peringatanAnggaranDirektur');
    var btnSubmit = document.getElementById('btnSetujuiSebagian');
    var checkbox = document.getElementById('setuju_direktur_sebagian');
    
    if (nilai > max) {
        peringatan.style.display = 'block';
        input.classList.add('is-invalid');
        btnSubmit.disabled = true;
    } else if (nilai <= 0) {
        peringatan.style.display = 'none';
        input.classList.remove('is-invalid');
        btnSubmit.disabled = true;
    } else {
        peringatan.style.display = 'none';
        input.classList.remove('is-invalid');
        if (checkbox.checked) {
            btnSubmit.disabled = false;
        }
    }
}

// Event listener untuk checkbox
document.addEventListener('DOMContentLoaded', function() {
    // Setujui Full
    var checkboxFull = document.getElementById('setuju_direktur_full');
    var btnFull = document.getElementById('btnSetujuiFull');
    if (checkboxFull && btnFull) {
        checkboxFull.addEventListener('change', function() {
            btnFull.disabled = !this.checked;
        });
    }
    
    // Setujui Sebagian
    var checkboxSebagian = document.getElementById('setuju_direktur_sebagian');
    var btnSebagian = document.getElementById('btnSetujuiSebagian');
    var metodeItem = document.getElementById('metode_item');
    var metodeNominal = document.getElementById('metode_nominal');
    
    if (checkboxSebagian && btnSebagian) {
        checkboxSebagian.addEventListener('change', function() {
            if (this.checked) {
                // Cek metode yang dipilih
                if (metodeItem.checked) {
                    var checkedItems = document.querySelectorAll('input[name="item_disetujui[]"]:checked');
                    btnSebagian.disabled = checkedItems.length === 0;
                } else {
                    var nominal = document.getElementById('total_disetujui_sebagian_direktur');
                    btnSebagian.disabled = !nominal.value || parseInt(nominal.value) <= 0;
                }
            } else {
                btnSebagian.disabled = true;
            }
        });
    }
    
    // Event listener untuk checkbox item
    document.querySelectorAll('input[name="item_disetujui[]"]').forEach(function(item) {
        item.addEventListener('change', function() {
            if (document.getElementById('metode_item').checked && checkboxSebagian.checked) {
                var checkedItems = document.querySelectorAll('input[name="item_disetujui[]"]:checked');
                btnSebagian.disabled = checkedItems.length === 0;
            }
        });
    });
    
    // Event listener untuk radio metode
    if (metodeItem) {
        metodeItem.addEventListener('change', function() {
            toggleMetodeSebagian('item');
        });
    }
    if (metodeNominal) {
        metodeNominal.addEventListener('change', function() {
            toggleMetodeSebagian('nominal');
        });
    }
});
</script>
@endsection