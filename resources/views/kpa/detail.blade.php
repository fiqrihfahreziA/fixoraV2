@extends('layouts.pengadaan.ppk')

@section('content')

{{-- HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-file-earmark-text text-primary me-2"></i>
            Detail Pengajuan - {{ $pengajuan->no_pengajuan ?? 'N/A' }}
        </h4>
        <span class="text-muted">
            Pengajuan oleh: {{ $pengajuan->karyawan->nama ?? '-' }}
        </span>
    </div>

    <a href="{{ route('kpa.dashboard') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

@php
    $status = $pengajuan->status ?? 'draft';

    $statusBadge = [
        'draft'                 => 'secondary',
        'diajukan'              => 'warning',
        'revisi'                => 'warning',
        'disetujui_koordinator' => 'info',
        'perlu_survei'          => 'warning',
        'disetujui_kabid'       => 'primary',
        'sesuai_standard'       => 'info',
        'keuangan'              => 'info',
        'menunggu ppk'          => 'primary',
        'menunggu_ppk'          => 'primary',
        'menunggu ppa'          => 'primary',
        'menunggu_ppa'          => 'primary',
        'menunggu kpa'          => 'primary',
        'menunggu_kpa'          => 'primary',
        'dipertimbangkan'       => 'info',
        'disetujui'             => 'success',
        'sebagian'              => 'primary',
        'ditunda'               => 'warning',
        'ditolak'               => 'danger',
        'ditolak_penerima'      => 'danger',
        'ditolak_keuangan'      => 'danger',
    ];

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
        'menunggu_kpa'          => 'Menunggu KPA',
        'dipertimbangkan'       => 'Dipertimbangkan',
        'disetujui'             => 'Disetujui',
        'sebagian'              => 'Disetujui Sebagian',
        'ditunda'               => 'Ditunda',
        'ditolak'               => 'Ditolak',
        'ditolak_penerima'      => 'Ditolak Penerima',
        'ditolak_keuangan'      => 'Ditolak Keuangan',
    ];

    $statusAktif = in_array($status, [
        'menunggu_kpa',
        'ditunda',
    ]);

    $statusSudahDiproses = in_array($status, [
        'disetujui',
        'sebagian',
        'ditunda',
        'ditolak',
    ]);
@endphp

{{-- STATUS --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <span class="badge bg-{{ $statusBadge[$status] ?? 'secondary' }} fs-6 px-3 py-2">
                        <i class="bi bi-circle-fill me-1" style="font-size:8px"></i>
                        {{ $statusLabel[$status] ?? $status }}
                    </span>

                    <small class="text-muted">
                        <i class="bi bi-calendar3 me-1"></i>
                        Diajukan:
                        {{ $pengajuan->created_at ? date('d M Y H:i', strtotime($pengajuan->created_at)) : '-' }}
                    </small>

                    @if($pengajuan->disetujui_kabid_at)
                        <small class="text-success">
                            <i class="bi bi-check-circle me-1"></i>
                            Disetujui Atasan:
                            {{ date('d M Y H:i', strtotime($pengajuan->disetujui_kabid_at)) }}
                        </small>
                    @endif
                </div>
            </div>

            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <span class="fw-bold">Total Pengajuan:</span>
                <span class="text-primary fw-bold fs-5">
                    Rp {{ number_format($pengajuan->total_pengajuan ?? 0, 0, ',', '.') }}
                </span>
            </div>
            
        </div>

        @if($pengajuan->catatan_ppk)
            <div class="mt-3 p-3 bg-light rounded">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-chat-quote text-primary mt-1"></i>
                    <div>
                        <small class="text-muted fw-semibold">Catatan PPK:</small>
                        <p class="mb-0 text-secondary">{{ $pengajuan->catatan_ppk }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- PANEL VERIFIKASI --}}
@if($statusAktif)
<div class="card border-0 shadow-sm mb-4 verification-banner">
    <div class="card-body p-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-3 text-white">
                    <div class="banner-icon">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                    <div>
                        <div class="text-white-50 small text-uppercase fw-semibold">
                            Tahap Verifikasi
                        </div>
                        <h5 class="mb-0 fw-bold">Verifikasi KPA</h5>
                        <small class="text-white-50">
                            Periksa seluruh data sebelum mengambil keputusan
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 text-lg-end">
                <div class="text-white-50 small">Total Diajukan</div>
                <div class="text-white fw-bold fs-4 mb-3">
                    Rp {{ number_format($pengajuan->total_pengajuan ?? 0, 0, ',', '.') }}
                </div>

                <div class="d-flex gap-2 flex-wrap justify-content-lg-end">
                    <button type="button" class="btn btn-light btn-sm"
                            onclick="openDrawer('disetujui')">
                        <i class="bi bi-check-circle text-success me-1"></i>
                        Disetujui
                    </button>

                    <!-- <button type="button" class="btn btn-light btn-sm"
                            onclick="openDrawer('sebagian')">
                        <i class="bi bi-check2-square text-primary me-1"></i>
                        Sebagian
                    </button>

                    <button type="button" class="btn btn-light btn-sm"
                            onclick="openDrawer('ditunda')">
                        <i class="bi bi-pause-circle text-warning me-1"></i>
                        Ditunda
                    </button> -->

                    <button type="button" class="btn btn-light btn-sm"
                            onclick="openDrawer('ditolak')">
                        <i class="bi bi-x-circle text-danger me-1"></i>
                        Ditolak
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

        {{-- INFORMASI PENGAJU --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-person-badge me-2 text-primary"></i>
                    Informasi Pengaju
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

        {{-- INFORMASI PENERIMA --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom border-success">
                <h6 class="fw-bold mb-0 text-success">
                    <i class="bi bi-person-check me-2"></i>
                    Informasi Penerima (User)
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
                                    <span class="badge bg-warning text-dark">Menunggu Konfirmasi</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ALASAN DAN MANFAAT --}}
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-bold text-primary mb-2">
                            <i class="bi bi-clipboard-check me-2"></i>
                            Alasan Justifikasi
                        </h6>
                        <p class="mb-0 text-secondary text-break limited-text">
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
                        <p class="mb-0 text-secondary text-break limited-text">
                            {{ $pengajuan->manfaat ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- DAMPAK --}}
        <div class="card border-warning shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold text-warning mb-2">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Dampak Jika Tidak Dilaksanakan
                </h6>
                <p class="mb-0 text-secondary">{{ $pengajuan->dampak ?? '-' }}</p>
            </div>
        </div>

        {{-- KONDISI BARANG LAMA --}}
        @if($pengajuan->kondisi_barang_lama)
            <div class="card border-info shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold text-info mb-2">
                        <i class="bi bi-box-seam me-2"></i>
                        Kondisi Barang Lama
                    </h6>
                    <div class="d-flex gap-3 flex-wrap">
                        <span class="badge bg-warning text-dark">
                            {{ $pengajuan->kondisi_barang_lama }}
                        </span>
                        <small class="text-muted">{{ $pengajuan->ket_barang_lama ?? '-' }}</small>
                    </div>
                </div>
            </div>
        @endif

        {{-- DAFTAR BARANG --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-list-check me-2"></i>
                    Daftar Barang yang Diajukan
                </h6>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
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
                                        <small class="text-muted">
                                            Spesifikasi: {{ $item->spesifikasi ?? '-' }}
                                        </small>
                                    </td>

                                    <td class="text-center">
                                        {{ $item->jumlah ?? 0 }}
                                        {{ $item->satuan ?? 'Unit' }}
                                    </td>

                                    <td class="text-end">
                                        Rp {{ number_format($item->harga_satuan ?? 0, 0, ',', '.') }}
                                    </td>

                                    <td class="text-end fw-bold text-primary">
                                        Rp {{ number_format($item->harga ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        Tidak ada data barang
                                    </td>
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

        {{-- DOKUMEN PENDUKUNG --}}
        @if($pengajuan->data_kerusakan)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="fw-bold mb-0 text-danger">
                        <i class="bi bi-file-earmark-pdf me-2"></i>
                        Dokumen Pendukung
                    </h6>
                </div>

                <div class="card-body">
                    <div class="row align-items-center g-3">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center gap-3">
                                <div class="pdf-icon">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                </div>
                                <div>
                                    <p class="fw-semibold mb-1">
                                        {{ basename($pengajuan->data_kerusakan) }}
                                    </p>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar me-1"></i>
                                        Diupload:
                                        {{ $pengajuan->created_at ? date('d M Y H:i', strtotime($pengajuan->created_at)) : '-' }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 text-md-end">
                            <a href="{{ asset('storage/' . $pengajuan->data_kerusakan) }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="btn btn-danger btn-sm rounded-pill px-3">
                                <i class="bi bi-eye me-1"></i> Lihat
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- KOLOM KANAN --}}
    <div class="col-lg-4">

        {{-- RINGKASAN --}}
        <div class="card border-0 shadow-sm bg-light mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-info-circle text-primary me-2"></i>
                    Ringkasan Pengajuan
                </h6>

                <div class="summary-row">
                    <span>No. Pengajuan</span>
                    <strong>{{ $pengajuan->no_pengajuan ?? '-' }}</strong>
                </div>

                <div class="summary-row">
                    <span>Pengaju</span>
                    <strong>{{ $pengajuan->karyawan->nama ?? '-' }}</strong>
                </div>

                <div class="summary-row">
                    <span>Penerima</span>
                    <strong>{{ $pengajuan->penerima->nama ?? 'Belum ditentukan' }}</strong>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-2">
                    <span class="text-muted">Total Pengajuan</span>
                    <strong class="text-primary">
                        Rp {{ number_format($pengajuan->total_pengajuan ?? 0, 0, ',', '.') }}
                    </strong>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2">
                    <span class="text-muted">Disetujui Keuangan</span>
                    <strong class="text-primary">
                        Rp {{ number_format($pengajuan->total_disetujui ?? 0, 0, ',', '.') }}
                    </strong>
                </div>
            </div>
        </div>

        {{-- TIMELINE --}}
        <!-- <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-clock-history me-2 text-primary"></i>
                    Alur Persetujuan
                </h6>
            </div>

            <div class="card-body">
                <div class="timeline">

                    <div class="timeline-item">
                        <div class="timeline-icon bg-warning">
                            <i class="bi bi-file-earmark-text text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="fw-bold mb-1">1. Pengajuan</h6>
                            <small class="text-muted">
                                {{ $pengajuan->created_at ? date('d M Y H:i', strtotime($pengajuan->created_at)) : '-' }}
                            </small>
                            <p class="mb-0 small text-muted">
                                {{ $pengajuan->karyawan->nama ?? '-' }} mengajukan permohonan
                            </p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-icon {{ $pengajuan->kpa ? 'bg-success' : 'bg-secondary' }}">
                            <i class="bi bi-person-up text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="fw-bold mb-1">2. Verifikasi Kepala Bidang</h6>
                            <small class="text-muted">
                                {{ $pengajuan->atasan->nama ?? 'Atasan' }}
                            </small>

                            @if($pengajuan->kpa)
                                <p class="mb-0 small text-success">
                                    <i class="bi bi-check-circle"></i>
                                    Diproses {{ date('d M Y', strtotime($pengajuan->kpa)) }}
                                </p>
                            @else
                                <p class="mb-0 small text-warning">Menunggu verifikasi</p>
                            @endif

                            @if($statusSudahDiproses)
                                <span class="badge bg-{{ $statusBadge[$status] ?? 'secondary' }} mt-2">
                                    {{ $statusLabel[$status] ?? $status }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-icon {{ ($pengajuan->status_penerimaan ?? '') == 'diterima' ? 'bg-success' : 'bg-secondary' }}">
                            <i class="bi bi-person-check text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="fw-bold mb-1">3. Konfirmasi Penerima</h6>
                            <small class="text-muted">
                                {{ $pengajuan->penerima->nama ?? 'Penerima' }}
                            </small>

                            @if(($pengajuan->status_penerimaan ?? '') == 'diterima')
                                <p class="mb-0 small text-success">Telah menerima barang</p>
                            @elseif(($pengajuan->status_penerimaan ?? '') == 'ditolak')
                                <p class="mb-0 small text-danger">Penerimaan ditolak</p>
                            @else
                                <p class="mb-0 small text-warning">Menunggu konfirmasi penerima</p>
                            @endif
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-icon {{ $status == 'disetujui' ? 'bg-success' : 'bg-secondary' }}">
                            <i class="bi bi-cash-stack text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="fw-bold mb-1">4. Verifikasi Keuangan</h6>
                            <small class="text-muted">Petugas Keuangan</small>

                            @if($status == 'disetujui')
                                <p class="mb-0 small text-success">Pengajuan disetujui</p>
                            @elseif($status == 'sebagian')
                                <p class="mb-0 small text-primary">Persetujuan sebagian</p>
                            @elseif($status == 'ditunda')
                                <p class="mb-0 small text-warning">Pengajuan ditunda</p>
                            @elseif($status == 'ditolak')
                                <p class="mb-0 small text-danger">Pengajuan ditolak</p>
                            @else
                                <p class="mb-0 small text-warning">Menunggu proses berikutnya</p>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div> -->

        {{-- PANDUAN --}}
        <div class="card border-0 shadow-sm bg-info bg-opacity-10 mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-lightbulb text-info me-2"></i>
                    Panduan Keputusan
                </h6>

                <div class="guide-item">
                    <span class="guide-dot bg-success"></span>
                    <div>
                        <strong class="text-success">Disetujui</strong>
                        <p class="small text-muted mb-0">Pengajuan disetujui untuk dilanjutkan.</p>
                    </div>
                </div>

                <!-- <div class="guide-item">
                    <span class="guide-dot bg-primary"></span>
                    <div>
                        <strong class="text-primary">Sebagian</strong>
                        <p class="small text-muted mb-0">Hanya sebagian pengajuan yang disetujui.</p>
                    </div>
                </div>

                <div class="guide-item">
                    <span class="guide-dot bg-warning"></span>
                    <div>
                        <strong class="text-warning">Ditunda</strong>
                        <p class="small text-muted mb-0">Memerlukan tindak lanjut sebelum diproses.</p>
                    </div>
                </div> -->

                <div class="guide-item">
                    <span class="guide-dot bg-danger"></span>
                    <div>
                        <strong class="text-danger">Ditolak</strong>
                        <p class="small text-muted mb-0">Pengajuan tidak dapat dilanjutkan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- OVERLAY DRAWER --}}
<div id="drawerOverlay" class="drawer-overlay"></div>

{{-- SIDE DRAWER VERIFIKASI --}}
<div id="verifDrawer" class="verif-drawer"
     role="dialog"
     aria-modal="true"
     aria-labelledby="drawerTitle">

    <div class="drawer-header">
        <div class="d-flex align-items-center gap-3">
            <div class="drawer-icon" id="drawerIcon">
                <i class="bi bi-check-circle-fill"></i>
            </div>

            <div>
                <h5 class="mb-1 fw-bold" id="drawerTitle">Verifikasi Pengajuan</h5>
                <small class="text-muted" id="drawerSubtitle">
                    Periksa keputusan sebelum mengirim
                </small>
            </div>
        </div>

        <button type="button" class="btn-close"
                aria-label="Tutup"
                id="btnCloseDrawer"></button>
    </div>

    <div class="drawer-body">
        <form action="{{ route('kpa.verifikasi', $pengajuan->id) }}"
              method="POST"
              id="formVerifikasi">
            @csrf
            @method('PUT')

            <input type="hidden"
                   name="status_verifikasi"
                   id="inputStatus"
                   value="menunggu_direktur">

            <input type="hidden"
                   name="kpa_id"
                   value="{{ auth()->user()->karyawan->id ?? '' }}">

            {{-- INFO PANEL --}}
            <div class="p-3 rounded-3 mb-4" id="infoPanel">
                <div class="d-flex justify-content-between align-items-center gap-2">
                    <span class="text-muted small">Total Pengajuan</span>
                    <span class="badge bg-success" id="infoBadge">DISETUJUI</span>
                </div>

                <div class="fs-4 fw-bold mt-2" id="infoAmount">
                    Rp {{ number_format($pengajuan->total_pengajuan ?? 0, 0, ',', '.') }}
                </div>

                <small class="text-muted" id="infoDesc">
                    Pengajuan akan diteruskan ke tahap berikutnya
                </small>
            </div>

            {{-- CATATAN --}}
            <div class="mb-4">
                <label for="inputCatatan" class="form-label fw-semibold">
                    Catatan Verifikasi <span class="text-danger">*</span>
                </label>

                <textarea name="catatan_verifikasi"
                          id="inputCatatan"
                          class="form-control"
                          rows="4"
                          required
                          minlength="5"
                          maxlength="255"
                          placeholder="Tulis alasan atau catatan verifikasi (5-255 karakter)..."></textarea>

                <div class="d-flex justify-content-between mt-1">
                    <small class="text-muted">Minimal 5, maksimal 255 karakter.</small>
                    <small class="text-muted"><span id="charCount">0</span>/255</small>
                </div>
            </div>

            {{-- CHECKBOX KONFIRMASI --}}
            <label class="confirm-check d-flex align-items-start gap-3 p-3 rounded-3 border">
                <input class="form-check-input mt-1"
                       type="checkbox"
                       id="checkKonfirmasi"
                       required>

                <span class="small text-secondary" id="konfirmasiText">
                    Saya menyatakan keputusan ini benar dan dapat dipertanggungjawabkan.
                </span>
            </label>

            <div class="alert alert-warning small mt-3 mb-0" id="warningPanel" hidden>
                <i class="bi bi-exclamation-triangle me-1"></i>
                Pastikan catatan dan keputusan sudah benar sebelum dikirim.
            </div>
        </form>
    </div>

    <div class="drawer-footer">
        <button type="button" class="btn btn-light border" id="btnBatalDrawer">
            Batal
        </button>

        <button type="submit"
                form="formVerifikasi"
                class="btn btn-success px-4"
                id="btnSubmit"
                disabled>
            <i class="bi bi-check2-circle me-1"></i>
            <span id="btnLabel">Setujui</span>
        </button>
    </div>
</div>

<style>
.info-item {
    background: #f8f9fa;
    padding: 12px 14px;
    border-radius: 8px;
    height: 100%;
}
.info-item .label {
    font-size: .7rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 600;
}
.info-item .value {
    font-size: .95rem;
    font-weight: 500;
    color: #1a1a2e;
    margin-top: 4px;
    overflow-wrap: anywhere;
}
.verification-banner {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
}
.banner-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    flex-shrink: 0;
    border-radius: 12px;
    background: rgba(255,255,255,.15);
}
.limited-text {
    max-height: 150px;
    overflow-y: auto;
    white-space: pre-line;
}
.pdf-icon {
    font-size: 40px;
    color: #dc3545;
}
.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    padding-bottom: 10px;
    margin-bottom: 10px;
    border-bottom: 1px solid #dee2e6;
}
.summary-row span {
    color: #6c757d;
    flex-shrink: 0;
}
.summary-row strong {
    text-align: right;
    overflow-wrap: anywhere;
}
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
.timeline-content {
    padding-left: 5px;
    padding-top: 2px;
    overflow-wrap: anywhere;
}
.timeline-content h6 {
    font-size: .9rem;
}
.timeline-content small {
    font-size: .75rem;
}
.timeline-content p {
    font-size: .8rem;
    margin-top: 4px;
}
.guide-item {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    margin-bottom: 14px;
}
.guide-item:last-child {
    margin-bottom: 0;
}
.guide-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-top: 5px;
    flex-shrink: 0;
}
.confirm-check {
    cursor: pointer;
    background: #f9fafb;
    transition: background .15s ease;
    border-color: #e5e7eb !important;
}
.confirm-check:hover {
    background: #f3f4f6;
}

/* ===== DRAWER (z-index dinaikkan agar di atas sidebar/navbar layout) ===== */
.drawer-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,.4);
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity .25s ease, visibility .25s ease;
    z-index: 2000;
    backdrop-filter: blur(2px);
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
    height: 100dvh;
    width: 480px;
    max-width: 100%;
    background: #fff;
    box-shadow: -20px 0 60px rgba(0,0,0,.15);
    transform: translateX(100%);
    transition: transform .3s ease;
    z-index: 2010;
    display: flex;
    flex-direction: column;
    visibility: hidden;
    pointer-events: none;
}
.verif-drawer.show {
    transform: translateX(0);
    visibility: visible;
    pointer-events: auto;
}
.drawer-header {
    padding: 20px 24px;
    border-bottom: 1px solid #f1f3f5;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-shrink: 0;
}
.drawer-header h5 {
    font-size: 1.05rem;
}
.drawer-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.2rem;
    background: #d1fae5;
    color: #059669;
}
.drawer-icon.icon-info {
    background: #dbeafe;
    color: #2563eb;
}
.drawer-icon.icon-warning {
    background: #fef3c7;
    color: #d97706;
}
.drawer-icon.icon-danger {
    background: #fee2e2;
    color: #dc2626;
}
.drawer-body {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    padding: 24px;
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
#infoPanel.panel-success {
    background: #f0fdf4;
    border: 1px solid #86efac;
}
#infoPanel.panel-info {
    background: #eff6ff;
    border: 1px solid #93c5fd;
}
#infoPanel.panel-warning {
    background: #fffbeb;
    border: 1px solid #fcd34d;
}
#infoPanel.panel-danger {
    background: #fef2f2;
    border: 1px solid #fca5a5;
}
@media (max-width: 576px) {
    .verif-drawer {
        width: 100%;
    }
    .drawer-header {
        padding: 16px 20px;
    }
    .drawer-body {
        padding: 20px;
    }
    .drawer-footer {
        padding: 12px 20px;
    }
    .drawer-footer .btn {
        flex: 1;
    }
}
</style>

<script>
(function () {
    const MODES = {
        disetujui: {
            icon: 'bi-check-circle-fill',
            iconClass: '',
            title: 'Setujui Pengajuan',
            subtitle: 'Pengajuan disetujui untuk dilanjutkan',
            panelClass: 'panel-success',
            badge: 'DISETUJUI',
            badgeClass: 'bg-success',
            desc: 'Pengajuan akan diteruskan ke tahap berikutnya.',
            btnLabel: 'Setujui',
            btnClass: 'btn-success',
            konfirmasi: 'Saya menyatakan pengajuan ini <strong>disetujui</strong> dan layak dilanjutkan.'
        },
        // sebagian: {
        //     icon: 'bi-check2-square',
        //     iconClass: 'icon-info',
        //     title: 'Setujui Sebagian',
        //     subtitle: 'Pengajuan disetujui sebagian',
        //     panelClass: 'panel-info',
        //     badge: 'DISETUJUI SEBAGIAN',
        //     badgeClass: 'bg-primary',
        //     desc: 'Tuliskan barang atau jumlah yang disetujui pada catatan verifikasi.',
        //     btnLabel: 'Setujui Sebagian',
        //     btnClass: 'btn-primary',
        //     konfirmasi: 'Saya menyatakan pengajuan ini <strong>disetujui sebagian</strong> sesuai catatan verifikasi.'
        // },
        // ditunda: {
        //     icon: 'bi-pause-circle-fill',
        //     iconClass: 'icon-warning',
        //     title: 'Tunda Pengajuan',
        //     subtitle: 'Pengajuan ditunda untuk sementara',
        //     panelClass: 'panel-warning',
        //     badge: 'DITUNDA',
        //     badgeClass: 'bg-warning text-dark',
        //     desc: 'Jelaskan alasan penundaan dan tindak lanjut yang diperlukan.',
        //     btnLabel: 'Tunda Pengajuan',
        //     btnClass: 'btn-warning',
        //     konfirmasi: 'Saya menyatakan pengajuan ini <strong>ditunda sementara</strong> dan memerlukan tindak lanjut.'
        // },
        ditolak: {
            icon: 'bi-x-circle-fill',
            iconClass: 'icon-danger',
            title: 'Tolak Pengajuan',
            subtitle: 'Pengajuan tidak dapat dilanjutkan',
            panelClass: 'panel-danger',
            badge: 'DITOLAK',
            badgeClass: 'bg-danger',
            desc: 'Jelaskan alasan penolakan secara jelas.',
            btnLabel: 'Tolak Pengajuan',
            btnClass: 'btn-danger',
            konfirmasi: 'Saya menyatakan pengajuan ini <strong>ditolak</strong> berdasarkan hasil verifikasi.'
        }
    };

    let previousFocus = null;

    function updateSubmitButton() {
        const note = document.getElementById('inputCatatan');
        const check = document.getElementById('checkKonfirmasi');
        const button = document.getElementById('btnSubmit');

        if (!note || !check || !button) return;

        const len = note.value.trim().length;
        button.disabled = !(len >= 5 && len <= 255 && check.checked);
    }

    window.openDrawer = function (mode) {
        const cfg = MODES[mode];
        if (!cfg) return;

        previousFocus = document.activeElement;

        const drawer = document.getElementById('verifDrawer');
        const overlay = document.getElementById('drawerOverlay');
        const form = document.getElementById('formVerifikasi');
        const icon = document.getElementById('drawerIcon');
        const panel = document.getElementById('infoPanel');
        const badge = document.getElementById('infoBadge');
        const button = document.getElementById('btnSubmit');
        const note = document.getElementById('inputCatatan');
        const check = document.getElementById('checkKonfirmasi');

        form.reset();

        document.getElementById('inputStatus').value = mode;

        icon.className = 'drawer-icon ' + cfg.iconClass;
        icon.innerHTML = '<i class="bi ' + cfg.icon + '"></i>';

        document.getElementById('drawerTitle').textContent = cfg.title;
        document.getElementById('drawerSubtitle').textContent = cfg.subtitle;

        panel.className = 'p-3 rounded-3 mb-4 ' + cfg.panelClass;

        badge.className = 'badge ' + cfg.badgeClass;
        badge.textContent = cfg.badge;

        document.getElementById('infoDesc').textContent = cfg.desc;
        document.getElementById('konfirmasiText').innerHTML = cfg.konfirmasi;

        button.className = 'btn px-4 ' + cfg.btnClass;
        document.getElementById('btnLabel').textContent = cfg.btnLabel;

        document.getElementById('charCount').textContent = '0';
        document.getElementById('warningPanel').hidden = mode !== 'ditolak';

        note.value = '';
        check.checked = false;
        button.disabled = true;

        drawer.classList.add('show');
        overlay.classList.add('show');
        document.body.style.overflow = 'hidden';

        window.setTimeout(function () {
            note.focus();
        }, 100);
    };

    window.closeDrawer = function () {
        const drawer = document.getElementById('verifDrawer');
        const overlay = document.getElementById('drawerOverlay');

        if (drawer) drawer.classList.remove('show');
        if (overlay) overlay.classList.remove('show');
        document.body.style.overflow = '';

        if (previousFocus && typeof previousFocus.focus === 'function') {
            previousFocus.focus();
        }
    };

    function init() {
        const overlay = document.getElementById('drawerOverlay');
        const drawer = document.getElementById('verifDrawer');
        const form = document.getElementById('formVerifikasi');
        const note = document.getElementById('inputCatatan');
        const check = document.getElementById('checkKonfirmasi');

        if (!overlay || !drawer || !form || !note || !check) return;

        // FIX UTAMA: pindahkan overlay & drawer ke <body>
        // supaya lepas dari stacking context / transform / overflow milik layout
        document.body.appendChild(overlay);
        document.body.appendChild(drawer);

        overlay.addEventListener('click', window.closeDrawer);
        document.getElementById('btnCloseDrawer').addEventListener('click', window.closeDrawer);
        document.getElementById('btnBatalDrawer').addEventListener('click', window.closeDrawer);

        note.addEventListener('input', function () {
            document.getElementById('charCount').textContent = this.value.length;
            updateSubmitButton();
        });

        check.addEventListener('change', updateSubmitButton);

        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                form.reportValidity();
                return;
            }

            if (note.value.trim().length < 5 || !check.checked) {
                event.preventDefault();
                updateSubmitButton();
            }
        });
    }

    // Jalan langsung jika DOM sudah siap, kalau belum tunggu DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            window.closeDrawer();
        }
    });
})();
</script>

@endsection