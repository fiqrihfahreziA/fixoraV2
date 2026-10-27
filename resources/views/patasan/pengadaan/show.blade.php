@extends('layouts.pengadaan.atasan')

@php use Illuminate\Support\Facades\Storage; @endphp

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark">
            <i class="bi bi-file-earmark-text text-primary me-2"></i>
            Detail Pengajuan
        </h4>
        <span class="text-muted">{{ $pengajuan->no_pengajuan }}</span>
    </div>
    <div class="d-flex gap-2">
        {{-- TOMBOL EDIT ITEM --}}
        <!-- <a href="{{ route('atasan.pengadaan.editItem', $pengajuan->id) }}" 
           class="btn btn-warning rounded-pill px-4">
            <i class="bi bi-pencil-square me-1"></i> Edit Item
        </a> -->
        <a href="{{ route('atasan.pengadaan') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<!-- Status -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
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
                        'diterima' => 'success',
                        'menunggu_diterima' => 'warning',
                        'ditolak_penerima' => 'danger',
                        'diverifikasi' => 'success',
                        'survei' => 'info'
                    ][$pengajuan->status] ?? 'secondary';
                    
                    $statusLabel = [
                        'draft' => 'Draft',
                        'diajukan' => 'Diajukan',
                        'revisi' => 'Revisi',
                        'disetujui_koordinator' => 'Disetujui Koordinator',
                        'disetujui_kabid' => 'Disetujui Kabid',
                        'menunggu_direktur' => 'Menunggu Direktur',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                        'diterima' => 'Diterima',
                        'menunggu_diterima' => 'Menunggu Diterima',
                        'ditolak_penerima' => 'Ditolak Penerima',
                        'diverifikasi' => 'Diverifikasi',
                        'survei' => 'Survei'
                    ][$pengajuan->status] ?? $pengajuan->status;
                @endphp
                <span class="badge bg-{{ $statusBadge }} fs-6 px-3 py-2">
                    <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>
                    {{ $statusLabel }}
                </span>
                <small class="text-muted ms-3">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ date('d M Y H:i', strtotime($pengajuan->created_at)) }}
                </small>
                @if($pengajuan->diterima_at)
                <small class="text-success ms-3">
                    <i class="bi bi-check-circle me-1"></i>
                    Diterima: {{ date('d M Y H:i', strtotime($pengajuan->diterima_at)) }}
                </small>
                @endif
            </div>
            <div>
                <span class="fw-bold">Total: </span>
                <span class="text-primary fw-bold fs-5">
                    Rp {{ number_format($pengajuan->total_pengajuan, 0, ',', '.') }}
                </span>
            </div>
        </div>
        @if($pengajuan->catatan_unit)
        <div class="mt-3 p-3 bg-light rounded">
            <small class="text-muted fw-semibold">Catatan Penerima:</small>
            <p class="mb-0 text-secondary">{{ $pengajuan->catatan_unit }}</p>
        </div>
        @endif

        {{-- TAMPILAN LOG STATUS BIDANG --}}
        @if($pengajuan->log_status_bidang)
        <div class="mt-2 p-2 bg-info bg-opacity-10 rounded">
            <small class="text-muted">
                <i class="bi bi-clock-history me-1"></i>
                Log Verifikasi Bidang: 
                <span class="fw-bold text-primary">{{ $pengajuan->log_status_bidang }}</span>
            </small>
        </div>
        @endif
    </div>
</div>

<div class="row">
    <!-- Kolom Kiri: Informasi -->
    <div class="col-md-8">
        <!-- Info Grid -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent">
                <h6 class="fw-bold mb-0"><i class="bi bi-info-circle me-2"></i>Informasi Pengajuan</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="label">Pemohon</div>
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
                            <div class="label">Instalasi</div>
                            <div class="value">{{ $pengajuan->instalasi ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="label">Dasar Usulan</div>
                            <div class="value">
                                @if($pengajuan->dasar_usulan === 'lainnya')
                                    {{ $pengajuan->dasar_usulan_lainnya ?? '-' }}
                                @else
                                    {{ $pengajuan->dasar_usulan ?? '-' }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="label">Tahun Anggaran</div>
                            <div class="value">{{ $pengajuan->tahun_anggaran ?? '-' }}</div>
                        </div>
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
                <p class="mb-0 text-secondary" style="word-wrap: break-word; max-height: 100px; overflow-y: auto;">
                    {{ $pengajuan->dampak ?? '-' }}
                </p>
            </div>
        </div>

        <!-- Kondisi Barang Lama -->
        @if($pengajuan->kondisi_barang_lama)
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
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-list-check me-2"></i>Daftar Barang</h6>
                <a href="{{ route('atasan.pengadaan.editItem', $pengajuan->id) }}" 
                   class="btn btn-sm btn-warning rounded-pill px-3">
                    <i class="bi bi-pencil-square me-1"></i>Edit Item
                </a>
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
                            @foreach($pengajuan->items as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->nama_barang }}</strong>
                                    <br>
                                    <small class="text-muted">Spesifikasi: {{ $item->spesifikasi ?? '-' }}</small>

                                    @if($item->dokumen_upload && Storage::disk('public')->exists($item->dokumen_upload))
                                        <br>
                                        <small class="text-primary">
                                            <i class="bi bi-paperclip me-1"></i>
                                            <a href="{{ asset('storage/' . $item->dokumen_upload) }}" 
                                            target="_blank" 
                                            class="text-decoration-none">
                                                {{ basename($item->dokumen_upload) }}
                                            </a>
                                            <a href="{{ asset('storage/' . $item->dokumen_upload) }}" 
                                            download
                                            class="ms-1 text-primary">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </small>
                                    @else
                                        <br>
                                        <small class="text-muted fst-italic">
                                            <i class="bi bi-paperclip me-1"></i>Tidak ada dokumen
                                        </small>
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->jumlah }} {{ $item->satuan ?? 'Unit' }}</td>
                                <td class="text-end">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold text-primary">
                                    Rp {{ number_format($item->harga, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold text-primary">
                                    Rp {{ number_format($pengajuan->total_pengajuan, 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- SEMUA LAMPIRAN --}}
        {{-- ============================================ --}}

        {{-- 1. DATA KERUSAKAN --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="fw-bold mb-0 text-danger">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Data Kerusakan
                </h6>
            </div>
            <div class="card-body">
                @if($pengajuan->data_kerusakan && Storage::disk('public')->exists($pengajuan->data_kerusakan))
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
                                        Diupload: {{ date('d M Y H:i', strtotime($pengajuan->created_at)) }}
                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        <i class="bi bi-filetype-pdf me-1"></i>
                                        Ukuran: {{ number_format(Storage::disk('public')->size($pengajuan->data_kerusakan) / 1024, 2) }} KB
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

                    <div class="mt-3">
                        <div class="border rounded-3 p-2" style="background: #f8f9fa;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Preview</small>
                                <a href="{{ asset('storage/' . $pengajuan->data_kerusakan) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-link text-primary">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Buka baru
                                </a>
                            </div>
                            <embed src="{{ asset('storage/' . $pengajuan->data_kerusakan) }}" 
                                   type="application/pdf" 
                                   width="100%" 
                                   height="350px" 
                                   style="border-radius: 6px;">
                            <small class="text-muted d-block text-center mt-2">
                                <i class="bi bi-info-circle me-1"></i>
                                Jika tidak muncul, klik tombol "Lihat"
                            </small>
                        </div>
                    </div>
                @else
                    <p class="mb-0 text-muted fst-italic">
                        <i class="bi bi-info-circle me-1"></i>Tidak ada dokumen data kerusakan
                    </p>
                @endif
            </div>
        </div>

        {{-- 2. ANALISIS KEBUTUHAN --}}
        @php $fileAnalisis = $pengajuan->analisis_kebutuhan ?? null; @endphp
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="fw-bold mb-0 text-primary">
                    <i class="bi bi-clipboard-data me-2"></i>Analisis Kebutuhan
                </h6>
            </div>
            <div class="card-body">
                @if($fileAnalisis && Storage::disk('public')->exists($fileAnalisis))
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-file-earmark-text-fill text-primary" style="font-size: 32px;"></i>
                            <div>
                                <p class="fw-semibold mb-1">{{ basename($fileAnalisis) }}</p>
                                <small class="text-muted">
                                    Ukuran: {{ number_format(Storage::disk('public')->size($fileAnalisis) / 1024, 2) }} KB
                                </small>
                            </div>
                        </div>
                        <div>
                            <a href="{{ asset('storage/' . $fileAnalisis) }}" target="_blank"
                               class="btn btn-primary btn-sm rounded-pill px-3">
                                <i class="bi bi-eye me-1"></i> Lihat
                            </a>
                            <a href="{{ asset('storage/' . $fileAnalisis) }}" download
                               class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                @else
                    <p class="mb-0 text-muted fst-italic">
                        <i class="bi bi-info-circle me-1"></i>Tidak ada dokumen analisis kebutuhan
                    </p>
                @endif
            </div>
        </div>

        {{-- 3. SPESIFIKASI TEKNIS --}}
        @php $fileSpesifikasi = $pengajuan->spesifikasi_teknis ?? null; @endphp
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="fw-bold mb-0 text-primary">
                    <i class="bi bi-gear me-2"></i>Spesifikasi Teknis
                </h6>
            </div>
            <div class="card-body">
                @if($fileSpesifikasi && Storage::disk('public')->exists($fileSpesifikasi))
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-file-earmark-text-fill text-primary" style="font-size: 32px;"></i>
                            <div>
                                <p class="fw-semibold mb-1">{{ basename($fileSpesifikasi) }}</p>
                                <small class="text-muted">
                                    Ukuran: {{ number_format(Storage::disk('public')->size($fileSpesifikasi) / 1024, 2) }} KB
                                </small>
                            </div>
                        </div>
                        <div>
                            <a href="{{ asset('storage/' . $fileSpesifikasi) }}" target="_blank"
                               class="btn btn-primary btn-sm rounded-pill px-3">
                                <i class="bi bi-eye me-1"></i> Lihat
                            </a>
                            <a href="{{ asset('storage/' . $fileSpesifikasi) }}" download
                               class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                @else
                    <p class="mb-0 text-muted fst-italic">
                        <i class="bi bi-info-circle me-1"></i>Tidak ada dokumen spesifikasi teknis
                    </p>
                @endif
            </div>
        </div>

        {{-- 4. FOTO BARANG --}}
        @php
            $fotoBarang = $pengajuan->foto_barang;
            if (is_string($fotoBarang)) {
                $fotoBarang = json_decode($fotoBarang, true) ?? [];
            }
            if (!is_array($fotoBarang)) {
                $fotoBarang = [];
            }
            $fotoBarang = array_values(array_filter($fotoBarang, function($p) {
                return $p && Storage::disk('public')->exists($p);
            }));
        @endphp
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="fw-bold mb-0 text-primary">
                    <i class="bi bi-image me-2"></i>Foto Barang
                </h6>
            </div>
            <div class="card-body">
                @if(count($fotoBarang) > 0)
                    <div class="row g-3">
                        @foreach($fotoBarang as $foto)
                        <div class="col-md-4">
                            <div class="border rounded-3 p-2">
                                <img src="{{ asset('storage/' . $foto) }}" 
                                     alt="Foto Barang"
                                     class="img-fluid rounded-2 mb-2"
                                     style="width: 100%; height: 180px; object-fit: cover; cursor: pointer;"
                                     onclick="window.open('{{ asset('storage/' . $foto) }}', '_blank')">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted text-truncate">{{ basename($foto) }}</small>
                                    <a href="{{ asset('storage/' . $foto) }}" download
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="mb-0 text-muted fst-italic">
                        <i class="bi bi-info-circle me-1"></i>Tidak ada foto barang
                    </p>
                @endif
            </div>
        </div>

        {{-- 5. PENAWARAN HARGA --}}
        @php $filePenawaran = $pengajuan->penawaran_harga ?? null; @endphp
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="fw-bold mb-0 text-primary">
                    <i class="bi bi-cash-coin me-2"></i>Penawaran Harga
                </h6>
            </div>
            <div class="card-body">
                @if($filePenawaran && Storage::disk('public')->exists($filePenawaran))
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-file-earmark-text-fill text-primary" style="font-size: 32px;"></i>
                            <div>
                                <p class="fw-semibold mb-1">{{ basename($filePenawaran) }}</p>
                                <small class="text-muted">
                                    Ukuran: {{ number_format(Storage::disk('public')->size($filePenawaran) / 1024, 2) }} KB
                                </small>
                            </div>
                        </div>
                        <div>
                            <a href="{{ asset('storage/' . $filePenawaran) }}" target="_blank"
                               class="btn btn-primary btn-sm rounded-pill px-3">
                                <i class="bi bi-eye me-1"></i> Lihat
                            </a>
                            <a href="{{ asset('storage/' . $filePenawaran) }}" download
                               class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                @else
                    <p class="mb-0 text-muted fst-italic">
                        <i class="bi bi-info-circle me-1"></i>Tidak ada dokumen penawaran harga
                    </p>
                @endif
            </div>
        </div>

        {{-- 6. DATA PENGGUNA --}}
        @php $fileDataPengguna = $pengajuan->data_pengguna ?? null; @endphp
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="fw-bold mb-0 text-primary">
                    <i class="bi bi-graph-up me-2"></i>Data Penggunaan / Pemakaian
                </h6>
            </div>
            <div class="card-body">
                @if($fileDataPengguna && Storage::disk('public')->exists($fileDataPengguna))
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-file-earmark-text-fill text-primary" style="font-size: 32px;"></i>
                            <div>
                                <p class="fw-semibold mb-1">{{ basename($fileDataPengguna) }}</p>
                                <small class="text-muted">
                                    Ukuran: {{ number_format(Storage::disk('public')->size($fileDataPengguna) / 1024, 2) }} KB
                                </small>
                            </div>
                        </div>
                        <div>
                            <a href="{{ asset('storage/' . $fileDataPengguna) }}" target="_blank"
                               class="btn btn-primary btn-sm rounded-pill px-3">
                                <i class="bi bi-eye me-1"></i> Lihat
                            </a>
                            <a href="{{ asset('storage/' . $fileDataPengguna) }}" download
                               class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                @else
                    <p class="mb-0 text-muted fst-italic">
                        <i class="bi bi-info-circle me-1"></i>Tidak ada dokumen data penggunaan
                    </p>
                @endif
            </div>
        </div>

        {{-- 7. DOKUMEN PENDUKUNG LAINNYA --}}
        @php $fileDokLain = $pengajuan->dokumen_pendukung_lainnya ?? null; @endphp
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="fw-bold mb-0 text-primary">
                    <i class="bi bi-folder-plus me-2"></i>Dokumen Pendukung Lainnya
                </h6>
            </div>
            <div class="card-body">
                @if($fileDokLain && Storage::disk('public')->exists($fileDokLain))
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-file-earmark-text-fill text-primary" style="font-size: 32px;"></i>
                            <div>
                                <p class="fw-semibold mb-1">{{ basename($fileDokLain) }}</p>
                                <small class="text-muted">
                                    Ukuran: {{ number_format(Storage::disk('public')->size($fileDokLain) / 1024, 2) }} KB
                                </small>
                            </div>
                        </div>
                        <div>
                            <a href="{{ asset('storage/' . $fileDokLain) }}" target="_blank"
                               class="btn btn-primary btn-sm rounded-pill px-3">
                                <i class="bi bi-eye me-1"></i> Lihat
                            </a>
                            <a href="{{ asset('storage/' . $fileDokLain) }}" download
                               class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                @else
                    <p class="mb-0 text-muted fst-italic">
                        <i class="bi bi-info-circle me-1"></i>Tidak ada dokumen pendukung lainnya
                    </p>
                @endif
            </div>
        </div>

    </div>

    <!-- ===== KOLOM KANAN: FORM VERIFIKASI (MODERN) ===== -->
    <div class="col-md-4">
        <div class="verify-sticky">

            <!-- Petunjuk -->
            <div class="verify-tips mb-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="tips-icon">
                        <i class="bi bi-lightbulb-fill"></i>
                    </div>
                    <span class="fw-bold small">Petunjuk Verifikasi</span>
                </div>
                <div class="tips-list">
                    <div class="tips-item">
                        <span class="dot dot-success"></span>
                        <span><strong>Setujui</strong> — Pengajuan diteruskan</span>
                    </div>
                    <div class="tips-item">
                        <span class="dot dot-danger"></span>
                        <span><strong>Tolak</strong> — Pengajuan ditolak</span>
                    </div>
                    <div class="tips-item">
                        <span class="dot dot-warning"></span>
                        <span><strong>Revisi</strong> — Kembali ke pemohon</span>
                    </div>
                    <div class="tips-item">
                        <span class="dot dot-info"></span>
                        <span><strong>Sesuai Standard</strong> — Sesuai Standard</span>
                    </div>
                </div>
            </div>

            <!-- Form Verifikasi -->
            <div class="verify-card">
                <div class="verify-card-head">
                    <div class="verify-avatar">
                        {{ strtoupper(substr($authUser->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <div class="verify-name">{{ $authUser->name }}</div>
                        <div class="verify-email">{{ $authUser->email }}</div>
                    </div>
                    <span class="verify-badge">
                        <i class="bi bi-shield-check"></i>
                    </span>
                </div>

                <div class="verify-card-body">
                    <form action="{{ route('atasan.pengadaan.verifikasi', $pengajuan->id) }}" method="POST" id="verifikasiForm">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="atasan_id" value="{{ $authUser->karyawan->id }}">

                        <div class="field-label">
                            Status Verifikasi <span class="req">*</span>
                        </div>

                        <div class="status-options" id="statusOptions">
                            <label class="status-option" data-value="disetujui_kabid">
                                <input type="radio" name="status_verifikasi" value="disetujui_kabid" required>
                                <div class="option-inner">
                                    <div class="option-icon icon-success">
                                        <i class="bi bi-check-lg"></i>
                                    </div>
                                    <div class="option-text">
                                        <div class="option-title">Setujui</div>
                                        <div class="option-desc">Teruskan pengajuan</div>
                                    </div>
                                    <div class="option-check">
                                        <i class="bi bi-circle"></i>
                                    </div>
                                </div>
                            </label>

                            <label class="status-option" data-value="ditolak">
                                <input type="radio" name="status_verifikasi" value="ditolak" required>
                                <div class="option-inner">
                                    <div class="option-icon icon-danger">
                                        <i class="bi bi-x-lg"></i>
                                    </div>
                                    <div class="option-text">
                                        <div class="option-title">Tolak</div>
                                        <div class="option-desc">Pengajuan ditolak</div>
                                    </div>
                                    <div class="option-check">
                                        <i class="bi bi-circle"></i>
                                    </div>
                                </div>
                            </label>

                            <label class="status-option" data-value="revisi">
                                <input type="radio" name="status_verifikasi" value="revisi" required>
                                <div class="option-inner">
                                    <div class="option-icon icon-warning">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </div>
                                    <div class="option-text">
                                        <div class="option-title">Minta Revisi</div>
                                        <div class="option-desc">Kembali ke pemohon</div>
                                    </div>
                                    <div class="option-check">
                                        <i class="bi bi-circle"></i>
                                    </div>
                                </div>
                            </label>

                            <label class="status-option" data-value="survei">
                                <input type="radio" name="status_verifikasi" value="survei" required>
                                <div class="option-inner">
                                    <div class="option-icon icon-info">
                                        <i class="bi bi-clipboard-check"></i>
                                    </div>
                                    <div class="option-text">
                                        <div class="option-title">Sesuai Standard</div>
                                        <div class="option-desc"> Sesuai Standard</div>
                                    </div>
                                    <div class="option-check">
                                        <i class="bi bi-circle"></i>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="status-preview-wrap">
                            <span class="status-preview-label">Status tersimpan:</span>
                            <span id="statusPreview" class="status-preview-chip chip-empty">—</span>
                        </div>

                        <div class="field-group">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="field-label mb-0">
                                    Catatan Verifikasi <span class="req">*</span>
                                </div>
                                <span class="char-counter" id="charCounter">0/5</span>
                            </div>
                            <textarea name="catatan_verifikasi" id="catatanVerifikasi"
                                      class="form-control modern-textarea" rows="3"
                                      placeholder="Tuliskan catatan verifikasi (min. 5 karakter)..."
                                      required></textarea>
                            <div id="catatanError" class="field-error" style="display: none;">
                                <i class="bi bi-exclamation-circle-fill me-1"></i>
                                Catatan minimal 5 karakter
                            </div>
                        </div>

                        <button type="submit" class="btn btn-submit w-100" id="submitBtn">
                            <span class="btn-text">
                                <i class="bi bi-send-check me-2"></i>Kirim Verifikasi
                            </span>
                        </button>

                        <div class="verify-note">
                            <i class="bi bi-info-circle me-1"></i>
                            Pastikan data sudah diperiksa sebelum mengirim.
                        </div>
                    </form>
                </div>

                @if($pengajuan->log_status_bidang)
                <div class="verify-card-foot">
                    <i class="bi bi-clock-history me-1"></i>
                    Status terakhir:
                    <span class="fw-bold">{{ $pengajuan->log_status_bidang }}</span>
                </div>
                @endif
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

.verify-sticky { position: sticky; top: 90px; }

.verify-tips {
    background: linear-gradient(135deg, #fff9e6 0%, #fff3cd 100%);
    border: 1px solid rgba(255, 193, 7, 0.25);
    border-radius: 16px;
    padding: 14px 16px;
}
.tips-icon {
    width: 30px; height: 30px;
    background: linear-gradient(135deg, #ffc107, #ff9800);
    border-radius: 9px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 0.9rem;
    box-shadow: 0 4px 10px rgba(255, 152, 0, 0.3);
}
.tips-list { display: flex; flex-direction: column; gap: 8px; }
.tips-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.82rem;
    color: #6c757d;
}
.dot {
    width: 9px; height: 9px;
    border-radius: 50%;
    flex-shrink: 0;
}
.dot-success { background: #198754; box-shadow: 0 0 0 3px rgba(25,135,84,0.15); }
.dot-danger  { background: #dc3545; box-shadow: 0 0 0 3px rgba(220,53,69,0.15); }
.dot-warning { background: #ffc107; box-shadow: 0 0 0 3px rgba(255,193,7,0.2); }
.dot-info    { background: #0dcaf0; box-shadow: 0 0 0 3px rgba(13,202,240,0.2); }

.verify-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
    overflow: hidden;
    border: 1px solid rgba(0, 0, 0, 0.04);
}
.verify-card-head {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 18px;
    background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
    color: #fff;
}
.verify-avatar {
    width: 44px; height: 44px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.1rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
}
.verify-name { font-weight: 700; font-size: 0.95rem; line-height: 1.2; }
.verify-email { font-size: 0.75rem; opacity: 0.85; }
.verify-badge {
    width: 32px; height: 32px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

.verify-card-body { padding: 20px; }

.verify-card-foot {
    padding: 12px 20px;
    background: #f8f9fa;
    font-size: 0.8rem;
    color: #6c757d;
    border-top: 1px solid #eee;
}

.field-label {
    font-size: 0.78rem;
    font-weight: 700;
    color: #495057;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    margin-bottom: 10px;
}
.field-label .req { color: #dc3545; }

.status-options {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 14px;
}
.status-option { margin: 0; cursor: pointer; }
.status-option input { display: none; }
.option-inner {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    border: 2px solid #e9ecef;
    border-radius: 14px;
    background: #fff;
    transition: all 0.25s ease;
    position: relative;
}
.option-inner:hover {
    border-color: #cbd5e1;
    transform: translateY(-1px);
}
.option-icon {
    width: 38px; height: 38px;
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    font-weight: 700;
    flex-shrink: 0;
    transition: all 0.25s ease;
}
.icon-success { background: #e7f6ee; color: #198754; }
.icon-danger  { background: #fdeaec; color: #dc3545; }
.icon-warning { background: #fff6db; color: #b98900; }
.icon-info    { background: #e0f7fb; color: #0aa2c0; }

.option-text { flex: 1; }
.option-title { font-weight: 700; font-size: 0.9rem; color: #1a1a2e; line-height: 1.2; }
.option-desc { font-size: 0.75rem; color: #94a3b8; margin-top: 2px; }
.option-check { font-size: 1.1rem; color: #cbd5e1; transition: all 0.25s ease; }

.status-option input:checked + .option-inner {
    border-color: #0d6efd;
    background: linear-gradient(135deg, #f0f6ff, #eef4ff);
    box-shadow: 0 6px 20px rgba(13, 110, 253, 0.12);
}
.status-option[data-value="disetujui_kabid"] input:checked + .option-inner {
    border-color: #198754;
    background: linear-gradient(135deg, #f0fbf5, #e6f7ee);
    box-shadow: 0 6px 20px rgba(25, 135, 84, 0.12);
}
.status-option[data-value="ditolak"] input:checked + .option-inner {
    border-color: #dc3545;
    background: linear-gradient(135deg, #fef5f6, #fdebed);
    box-shadow: 0 6px 20px rgba(220, 53, 69, 0.12);
}
.status-option[data-value="revisi"] input:checked + .option-inner {
    border-color: #ffc107;
    background: linear-gradient(135deg, #fffcf0, #fff7db);
    box-shadow: 0 6px 20px rgba(255, 193, 7, 0.15);
}
.status-option[data-value="survei"] input:checked + .option-inner {
    border-color: #0dcaf0;
    background: linear-gradient(135deg, #eefcff, #e0f7fb);
    box-shadow: 0 6px 20px rgba(13, 202, 240, 0.18);
}
.status-option input:checked + .option-inner .option-check { color: #0d6efd; }
.status-option input:checked + .option-inner .option-check i::before {
    content: "\f26a";
    font-family: "bootstrap-icons";
}
.status-option[data-value="disetujui_kabid"] input:checked + .option-inner .option-check { color: #198754; }
.status-option[data-value="ditolak"] input:checked + .option-inner .option-check { color: #dc3545; }
.status-option[data-value="revisi"] input:checked + .option-inner .option-check { color: #b98900; }
.status-option[data-value="sesuai standard"] input:checked + .option-inner .option-check { color: #0aa2c0; }

.status-preview-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 14px;
    background: #f8fafc;
    border-radius: 12px;
    margin-bottom: 18px;
}
.status-preview-label { font-size: 0.75rem; color: #64748b; font-weight: 600; }
.status-preview-chip {
    font-size: 0.78rem;
    font-weight: 700;
    padding: 5px 12px;
    border-radius: 999px;
    transition: all 0.3s ease;
}
.chip-empty { background: #e2e8f0; color: #94a3b8; }
.chip-success { background: #d1fae5; color: #065f46; }
.chip-danger  { background: #fee2e2; color: #991b1b; }
.chip-warning { background: #fef3c7; color: #92400e; }
.chip-info    { background: #cffafe; color: #155e75; }

.field-group { margin-bottom: 18px; }
.char-counter { font-size: 0.7rem; color: #94a3b8; font-weight: 600; }
.char-counter.ok { color: #198754; }

.modern-textarea {
    border: 2px solid #e9ecef;
    border-radius: 14px;
    padding: 12px 14px;
    font-size: 0.88rem;
    transition: all 0.25s ease;
    resize: vertical;
    min-height: 90px;
    background: #fbfcfe;
}
.modern-textarea:focus {
    border-color: #0d6efd;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
    outline: none;
}
.modern-textarea.is-invalid {
    border-color: #dc3545;
    background: #fef5f6;
    animation: shake 0.3s;
}
@keyframes shake {
    0%,100% { transform: translateX(0); }
    25% { transform: translateX(-4px); }
    75% { transform: translateX(4px); }
}
.field-error { color: #dc3545; font-size: 0.75rem; margin-top: 6px; font-weight: 500; }

.btn-submit {
    background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
    background-size: 200% 200%;
    color: #fff;
    border: none;
    border-radius: 14px;
    padding: 13px 18px;
    font-weight: 700;
    font-size: 0.92rem;
    transition: all 0.35s ease;
    box-shadow: 0 8px 20px rgba(13, 110, 253, 0.28);
    position: relative;
    overflow: hidden;
}
.btn-submit::before {
    content: '';
    position: absolute;
    top: 0; left: -100%;
    width: 100%; height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.35), transparent);
    transition: left 0.5s ease;
}
.btn-submit:hover::before { left: 100%; }
.btn-submit:hover {
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(102, 16, 242, 0.35);
    background-position: 100% 0;
}
.btn-submit:disabled { opacity: 0.75; cursor: not-allowed; transform: none; }

.verify-note { text-align: center; font-size: 0.72rem; color: #94a3b8; margin-top: 12px; }

@media (max-width: 768px) {
    .verify-sticky { position: static; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('verifikasiForm');
    const radios = document.querySelectorAll('input[name="status_verifikasi"]');
    const catatanInput = document.getElementById('catatanVerifikasi');
    const submitBtn = document.getElementById('submitBtn');
    const statusPreview = document.getElementById('statusPreview');
    const catatanError = document.getElementById('catatanError');
    const charCounter = document.getElementById('charCounter');

    const statusMap = {
        'disetujui_kabid': { label: 'Disetujui Kabid', cls: 'chip-success' },
        'ditolak':         { label: 'Ditolak',         cls: 'chip-danger' },
        'revisi':          { label: 'Revisi',          cls: 'chip-warning' },
        'survei':          { label: 'Survei',          cls: 'chip-info' }
    };

    const confirmMap = {
        'disetujui_kabid': 'Yakin MENYETUJUI pengajuan ini?',
        'ditolak':         'Yakin MENOLAK pengajuan ini?',
        'revisi':          'Yakin MEMINTA REVISI pengajuan ini?',
        'sesuai_standard': 'Yakin MENGAJUKAN SURVEI untuk pengajuan ini?'
    };

    function updatePreview() {
        const selected = document.querySelector('input[name="status_verifikasi"]:checked');
        if (selected && statusMap[selected.value]) {
            statusPreview.textContent = statusMap[selected.value].label;
            statusPreview.className = 'status-preview-chip ' + statusMap[selected.value].cls;
        } else {
            statusPreview.textContent = '—';
            statusPreview.className = 'status-preview-chip chip-empty';
        }
    }
    radios.forEach(r => r.addEventListener('change', updatePreview));
    updatePreview();

    function validateCatatan() {
        const val = catatanInput.value.trim();
        charCounter.textContent = val.length + '/5';
        charCounter.classList.toggle('ok', val.length >= 5);
        if (val.length < 5) {
            catatanError.style.display = 'block';
            catatanInput.classList.add('is-invalid');
            return false;
        }
        catatanError.style.display = 'none';
        catatanInput.classList.remove('is-invalid');
        return true;
    }

    catatanInput.addEventListener('input', () => {
        const val = catatanInput.value.trim();
        charCounter.textContent = val.length + '/5';
        charCounter.classList.toggle('ok', val.length >= 5);
        if (catatanInput.classList.contains('is-invalid')) validateCatatan();
    });
    catatanInput.addEventListener('blur', validateCatatan);

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const selected = document.querySelector('input[name="status_verifikasi"]:checked');
        if (!selected) {
            alert('⚠️ Silakan pilih status verifikasi terlebih dahulu!');
            return false;
        }
        if (!validateCatatan()) {
            alert('⚠️ Catatan verifikasi minimal 5 karakter!');
            catatanInput.focus();
            return false;
        }
        if (!confirm(confirmMap[selected.value])) return false;

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Memproses...';
        this.submit();
    });

    if (sessionStorage.getItem('formError')) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<span class="btn-text"><i class="bi bi-send-check me-2"></i>Kirim Verifikasi</span>';
        sessionStorage.removeItem('formError');
    }
});

@if(session('error'))
    alert('❌ {{ session('error') }}');
@endif
@if(session('success'))
    alert('✅ {{ session('success') }}');
@endif
</script>
@endsection