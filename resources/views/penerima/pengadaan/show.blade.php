@extends('layouts.pengadaan.penerima')

@php use Illuminate\Support\Facades\Storage; @endphp

@section('content')
<div class="container-fluid py-3">

    {{-- ============ HEADER ============ --}}
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="icon-box bg-primary-subtle text-primary">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <h4 class="fw-bold text-dark mb-0">Detail Pengajuan</h4>
            </div>
            <div class="d-flex align-items-center gap-2 text-muted small ps-1">
                <i class="bi bi-hash"></i>
                <span class="fw-semibold">{{ $pengajuan->no_pengajuan }}</span>
            </div>
        </div>
        <a href="{{ route('penerima.chartp') }}" class="btn btn-light border rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- ============ STATUS CARD ============ --}}
    @php
        $statusMap = [
            'draft' => ['secondary', 'Draft', 'pencil'],
            'diajukan' => ['warning', 'Diajukan', 'send'],
            'revisi' => ['warning', 'Revisi', 'arrow-counterclockwise'],
            'disetujui_koordinator' => ['info', 'Disetujui Koordinator', 'check-circle'],
            'disetujui_kabid' => ['primary', 'Disetujui Kabid', 'check-circle'],
            'menunggu_direktur' => ['warning', 'Menunggu Direktur', 'hourglass-split'],
            'disetujui' => ['success', 'Disetujui', 'check-circle-fill'],
            'ditolak' => ['danger', 'Ditolak', 'x-circle-fill'],
            'diterima' => ['success', 'Diterima', 'check2-circle'],
            'menunggu_diterima' => ['warning', 'Menunggu Diterima', 'hourglass'],
            'ditolak_penerima' => ['danger', 'Ditolak Penerima', 'x-circle'],
            'diverifikasi' => ['success', 'Diverifikasi', 'shield-check'],
            'survei' => ['info', 'Survei', 'clipboard-check'],
        ];
        [$badge, $label, $icon] = $statusMap[$pengajuan->status] ?? ['secondary', $pengajuan->status, 'circle'];
    @endphp

    <div class="card border-0 shadow-sm mb-4 overflow-hidden">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <span class="badge bg-{{ $badge }}-subtle text-{{ $badge }} border border-{{ $badge }}-subtle rounded-pill px-3 py-2 fs-6 d-inline-flex align-items-center gap-2">
                        <i class="bi bi-{{ $icon }}"></i>
                        {{ $label }}
                    </span>
                    <small class="text-muted d-inline-flex align-items-center gap-1">
                        <i class="bi bi-calendar3"></i>
                        {{ date('d M Y H:i', strtotime($pengajuan->created_at)) }}
                    </small>
                    @if($pengajuan->diterima_at)
                        <small class="text-success d-inline-flex align-items-center gap-1">
                            <i class="bi bi-check-circle"></i>
                            Diterima: {{ date('d M Y H:i', strtotime($pengajuan->diterima_at)) }}
                        </small>
                    @endif
                </div>
                <div class="text-end">
                    <div class="text-muted small">Total Pengajuan</div>
                    <div class="fw-bold fs-4 text-primary">
                        Rp {{ number_format($pengajuan->total_pengajuan, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            @if($pengajuan->catatan_unit)
            <div class="mt-3 p-3 rounded-3 bg-warning-subtle border-start border-warning border-4">
                <small class="fw-semibold text-warning-emphasis d-block mb-1">
                    <i class="bi bi-chat-left-text me-1"></i>Catatan Penerima
                </small>
                <p class="mb-0 small">{{ $pengajuan->catatan_unit }}</p>
            </div>
            @endif
        </div>
    </div>

    <div class="row g-4">
        {{-- ================= KOLOM KIRI ================= --}}
        <div class="col-lg-8">

            {{-- INFO GRID --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle text-primary"></i> Informasi Pengajuan
                    </h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        @php
                            $infos = [
                                ['Pemohon', $pengajuan->karyawan->nama ?? '-', 'person'],
                                ['Jabatan', $pengajuan->karyawan->jabatan ?? '-', 'briefcase'],
                                ['Bidang', $pengajuan->bidang->nama_bidang ?? '-', 'diagram-3'],
                                ['Instalasi', $pengajuan->instalasi ?? '-', 'building'],
                                ['Dasar Usulan', $pengajuan->dasar_usulan === 'lainnya' ? ($pengajuan->dasar_usulan_lainnya ?? '-') : ($pengajuan->dasar_usulan ?? '-'), 'file-text'],
                                ['Tahun Anggaran', $pengajuan->tahun_anggaran ?? '-', 'calendar-event'],
                            ];
                        @endphp
                        @foreach($infos as [$label, $value, $icon])
                        <div class="col-md-6">
                            <div class="info-item d-flex align-items-start gap-3">
                                <div class="info-icon"><i class="bi bi-{{ $icon }}"></i></div>
                                <div>
                                    <div class="label">{{ $label }}</div>
                                    <div class="value">{{ $value }}</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ALASAN & MANFAAT --}}
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100 accent-card accent-primary">
                        <div class="card-body">
                            <h6 class="fw-bold text-primary mb-2 d-flex align-items-center gap-2">
                                <i class="bi bi-clipboard-check"></i> Alasan Justifikasi
                            </h6>
                            <p class="mb-0 text-secondary small scrollable-box">
                                {{ $pengajuan->alasan_justifikasi ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100 accent-card accent-success">
                        <div class="card-body">
                            <h6 class="fw-bold text-success mb-2 d-flex align-items-center gap-2">
                                <i class="bi bi-star-fill"></i> Manfaat
                            </h6>
                            <p class="mb-0 text-secondary small scrollable-box">
                                {{ $pengajuan->manfaat ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DAMPAK --}}
            <div class="card border-0 shadow-sm mb-4 accent-card accent-warning">
                <div class="card-body">
                    <h6 class="fw-bold text-warning-emphasis mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill"></i> Dampak Jika Tidak Dilaksanakan
                    </h6>
                    <p class="mb-0 text-secondary small scrollable-box">
                        {{ $pengajuan->dampak ?? '-' }}
                    </p>
                </div>
            </div>

            {{-- KONDISI BARANG LAMA --}}
            @if($pengajuan->kondisi_barang_lama)
            <div class="card border-0 shadow-sm mb-4 accent-card accent-info">
                <div class="card-body">
                    <h6 class="fw-bold text-info-emphasis mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-box-seam"></i> Kondisi Barang Lama
                    </h6>
                    <div class="d-flex gap-3 align-items-center flex-wrap">
                        <span class="badge bg-warning text-dark rounded-pill px-3">{{ $pengajuan->kondisi_barang_lama }}</span>
                        <small class="text-muted">{{ $pengajuan->ket_barang_lama ?? '-' }}</small>
                    </div>
                </div>
            </div>
            @endif

            {{-- DAFTAR BARANG --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-list-check text-primary"></i> Daftar Barang
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-modern">
                            <thead>
                                <tr>
                                    <th class="ps-4">Nama Barang</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end">Harga Satuan</th>
                                    <th class="text-end pe-4">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pengajuan->items as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold">{{ $item->nama_barang }}</div>
                                        <small class="text-muted">Spesifikasi: {{ $item->spesifikasi ?? '-' }}</small>
                                        <div class="mt-1">
                                            @if($item->dokumen_upload && Storage::disk('public')->exists($item->dokumen_upload))
                                                <a href="{{ asset('storage/' . $item->dokumen_upload) }}" target="_blank"
                                                   class="badge bg-primary-subtle text-primary text-decoration-none">
                                                    <i class="bi bi-paperclip me-1"></i>{{ basename($item->dokumen_upload) }}
                                                </a>
                                                <a href="{{ asset('storage/' . $item->dokumen_upload) }}" download
                                                   class="badge bg-light text-secondary text-decoration-none">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            @else
                                                <small class="text-muted fst-italic">
                                                    <i class="bi bi-paperclip me-1"></i>Tidak ada dokumen
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border rounded-pill px-3">
                                            {{ $item->jumlah }} {{ $item->satuan ?? 'Unit' }}
                                        </span>
                                    </td>
                                    <td class="text-end text-muted">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold text-primary pe-4">
                                        Rp {{ number_format($item->harga, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-total">
                                    <td colspan="3" class="text-end fw-bold ps-4">Total</td>
                                    <td class="text-end fw-bold text-primary fs-6 pe-4">
                                        Rp {{ number_format($pengajuan->total_pengajuan, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ============ LAMPIRAN ============ --}}
            <div class="d-flex align-items-center gap-2 mb-3 mt-2">
                <i class="bi bi-paperclip text-primary"></i>
                <h6 class="fw-bold mb-0">Lampiran Dokumen</h6>
                <span class="badge bg-light text-muted border rounded-pill">7</span>
            </div>

            @php
                $lampiranList = [
                    ['Data Kerusakan', $pengajuan->data_kerusakan ?? null, 'file-earmark-pdf-fill', 'danger', true],
                    ['Analisis Kebutuhan', $pengajuan->analisis_kebutuhan ?? null, 'clipboard-data', 'primary', false],
                    ['Spesifikasi Teknis', $pengajuan->spesifikasi_teknis ?? null, 'gear', 'primary', false],
                    ['Penawaran Harga', $pengajuan->penawaran_harga ?? null, 'cash-coin', 'primary', false],
                    ['Data Penggunaan', $pengajuan->data_pengguna ?? null, 'graph-up', 'primary', false],
                    ['Dokumen Pendukung Lainnya', $pengajuan->dokumen_pendukung_lainnya ?? null, 'folder-plus', 'primary', false],
                ];
            @endphp

            @foreach($lampiranList as [$title, $file, $icon, $color, $isPdf])
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-box bg-{{ $color }}-subtle text-{{ $color }}">
                                <i class="bi bi-{{ $icon }}"></i>
                            </div>
                            <div>
                                <div class="fw-semibold small">{{ $title }}</div>
                                @if($file && Storage::disk('public')->exists($file))
                                    <small class="text-muted d-block">{{ basename($file) }}</small>
                                    <small class="text-muted">
                                        {{ number_format(Storage::disk('public')->size($file) / 1024, 2) }} KB
                                    </small>
                                @else
                                    <small class="text-muted fst-italic">Tidak ada dokumen</small>
                                @endif
                            </div>
                        </div>
                        @if($file && Storage::disk('public')->exists($file))
                        <div class="d-flex gap-2">
                            <a href="{{ asset('storage/' . $file) }}" target="_blank"
                               class="btn btn-{{ $color }} btn-sm rounded-pill px-3">
                                <i class="bi bi-eye me-1"></i> Lihat
                            </a>
                            <a href="{{ asset('storage/' . $file) }}" download
                               class="btn btn-outline-{{ $color }} btn-sm rounded-pill px-3">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                        @endif
                    </div>

                    @if($isPdf && $file && Storage::disk('public')->exists($file))
                    <div class="mt-3 border rounded-3 overflow-hidden">
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 bg-light">
                            <small class="text-muted"><i class="bi bi-eye me-1"></i>Preview</small>
                            <a href="{{ asset('storage/' . $file) }}" target="_blank" class="small text-decoration-none">
                                Buka baru <i class="bi bi-box-arrow-up-right ms-1"></i>
                            </a>
                        </div>
                        <embed src="{{ asset('storage/' . $file) }}" type="application/pdf"
                               width="100%" height="350px">
                    </div>
                    @endif
                </div>
            </div>
            @endforeach

            {{-- FOTO BARANG --}}
            @php
                $fotoBarang = $pengajuan->foto_barang;
                if (is_string($fotoBarang)) $fotoBarang = json_decode($fotoBarang, true) ?? [];
                if (!is_array($fotoBarang)) $fotoBarang = [];
                $fotoBarang = array_values(array_filter($fotoBarang, fn($p) => $p && Storage::disk('public')->exists($p)));
            @endphp
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-image text-primary"></i> Foto Barang
                        <span class="badge bg-light text-muted border rounded-pill">{{ count($fotoBarang) }}</span>
                    </h6>
                    @if(count($fotoBarang) > 0)
                        <div class="row g-3">
                            @foreach($fotoBarang as $foto)
                            <div class="col-md-4">
                                <div class="foto-item">
                                    <img src="{{ asset('storage/' . $foto) }}"
                                         onclick="window.open('{{ asset('storage/' . $foto) }}', '_blank')">
                                    <div class="foto-overlay">
                                        <small class="text-truncate">{{ basename($foto) }}</small>
                                        <a href="{{ asset('storage/' . $foto) }}" download
                                           class="btn btn-sm btn-light">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="mb-0 text-muted fst-italic small">
                            <i class="bi bi-info-circle me-1"></i>Tidak ada foto barang
                        </p>
                    @endif
                </div>
            </div>

        </div>

        {{-- ================= KOLOM KANAN: VERIFIKASI ================= --}}
        <div class="col-lg-4">
            <div class="verif-sticky">

                {{-- PETUNJUK --}}
                <div class="card border-0 shadow-sm mb-3 bg-gradient-primary-soft">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-lightbulb-fill text-warning"></i> Petunjuk Verifikasi
                        </h6>
                        <div class="d-flex flex-column gap-2 small">
                            <div class="d-flex align-items-start gap-2">
                                <span class="dot bg-success"></span>
                                <div><strong>Setujui</strong> — Pengajuan diteruskan ke tahap berikutnya</div>
                            </div>
                            <div class="d-flex align-items-start gap-2">
                                <span class="dot bg-danger"></span>
                                <div><strong>Tolak</strong> — Pengajuan dihentikan</div>
                            </div>
                            <div class="d-flex align-items-start gap-2">
                                <span class="dot bg-warning"></span>
                                <div><strong>Revisi</strong> — Dikembalikan ke pemohon</div>
                            </div>
                            <div class="d-flex align-items-start gap-2">
                                <span class="dot bg-info"></span>
                                <div><strong>Survei</strong> — Diajukan untuk survei lapangan</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FORM VERIFIKASI --}}
                <div class="card border-0 shadow-sm verif-card">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-shield-check text-primary"></i> Verifikasi Penerima
                        </h6>
                    </div>
                    <div class="card-body px-4 pb-4">

                        {{-- User Info --}}
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light mb-4">
                            <div class="avatar-circle">
                                {{ strtoupper(substr($authUser->name, 0, 1)) }}
                            </div>
                            <div class="overflow-hidden">
                                <div class="fw-semibold small text-truncate">{{ $authUser->name }}</div>
                                <small class="text-muted text-truncate d-block">{{ $authUser->email }}</small>
                            </div>
                        </div>

                        <form action="{{ route('penerima.pengadaan.verifikasi', $pengajuan->id) }}"
                              method="POST" id="formVerifikasi">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="penerima_id" value="{{ $authUser->karyawan->id }}">

                            {{-- RADIO CARD STATUS --}}
                            <label class="form-label fw-semibold small">
                                Status Verifikasi <span class="text-danger">*</span>
                            </label>
                            <div class="row g-2 mb-4">
                                @php
                                    $opsi = [
                                        ['disetujui_koordinator', 'Setujui', 'check-circle-fill', 'success'],
                                        ['ditolak', 'Tolak', 'x-circle-fill', 'danger'],
                                        ['revisi', 'Revisi', 'arrow-counterclockwise', 'warning'],
                                        ['survei', 'Survei', 'clipboard-check', 'info'],
                                    ];
                                @endphp
                                @foreach($opsi as [$val, $text, $icon, $color])
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="status_verifikasi"
                                           id="status_{{ $val }}" value="{{ $val }}" required>
                                    <label class="status-option status-{{ $color }}" for="status_{{ $val }}">
                                        <i class="bi bi-{{ $icon }}"></i>
                                        <span>{{ $text }}</span>
                                    </label>
                                </div>
                                @endforeach
                            </div>

                            {{-- CATATAN --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold small">
                                    Catatan Verifikasi <span class="text-danger">*</span>
                                </label>
                                <textarea name="catatan_verifikasi" id="catatanVerifikasi"
                                          class="form-control" rows="3" maxlength="500" required
                                          placeholder="Tuliskan catatan verifikasi..."></textarea>
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted">Wajib diisi</small>
                                    <small class="text-muted"><span id="charCount">0</span>/500</small>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold"
                                    id="btnSubmitVerif">
                                <i class="bi bi-send-check me-1"></i> Kirim Verifikasi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============ MODAL KONFIRMASI ============ --}}
<div class="modal fade" id="modalKonfirmasi" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body text-center p-4">
                <div class="modal-icon mb-3" id="modalIcon">
                    <i class="bi bi-question-lg"></i>
                </div>
                <h5 class="fw-bold mb-2" id="modalTitle">Konfirmasi Verifikasi</h5>
                <p class="text-muted mb-4" id="modalText">
                    Apakah Anda yakin ingin mengirim verifikasi ini?
                </p>
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button class="btn btn-primary rounded-pill px-4" id="btnConfirmSubmit">
                        <i class="bi bi-check-lg me-1"></i> Ya, Kirim
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============ STYLE ============ --}}
<style>
    .icon-box {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
    }
    .info-item {
        background: #f8f9fa;
        padding: 12px 14px;
        border-radius: 10px;
        height: 100%;
        transition: all .2s;
    }
    .info-item:hover { background: #eef2ff; }
    .info-item .info-icon {
        width: 32px; height: 32px;
        border-radius: 8px;
        background: #fff;
        color: #4f46e5;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
        box-shadow: 0 1px 3px rgba(0,0,0,.05);
    }
    .info-item .label {
        font-size: .68rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: .5px;
        font-weight: 600;
    }
    .info-item .value {
        font-size: .92rem;
        font-weight: 600;
        color: #1a1a2e;
        margin-top: 1px;
    }
    .scrollable-box { max-height: 150px; overflow-y: auto; }

    .accent-card { border-left: 4px solid transparent; }
    .accent-primary { border-left-color: #4f46e5; }
    .accent-success { border-left-color: #10b981; }
    .accent-warning { border-left-color: #f59e0b; }
    .accent-info    { border-left-color: #06b6d4; }

    .table-modern thead th {
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #6c757d;
        font-weight: 700;
        background: #f8f9fa;
        border: none;
        padding-top: 14px; padding-bottom: 14px;
    }
    .table-modern tbody tr { border-bottom: 1px solid #f1f3f5; }
    .table-modern tbody tr:hover { background: #fafbff; }
    .table-modern tbody td { padding-top: 14px; padding-bottom: 14px; }
    .table-total td { background: #f8f9fa; padding-top: 14px; padding-bottom: 14px; }

    .foto-item {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #e9ecef;
    }
    .foto-item img {
        width: 100%; height: 180px; object-fit: cover;
        cursor: pointer;
        transition: transform .3s;
        display: block;
    }
    .foto-item:hover img { transform: scale(1.05); }
    .foto-overlay {
        position: absolute; bottom: 0; left: 0; right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,.75));
        color: #fff;
        padding: 24px 10px 10px;
        display: flex; justify-content: space-between; align-items: center;
        font-size: .75rem;
    }

    /* ============ VERIFIKASI PANEL ============ */
    .verif-sticky { position: sticky; top: 20px; }
    .bg-gradient-primary-soft {
        background: linear-gradient(135deg, #eef2ff 0%, #f8f9ff 100%);
    }
    .dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-top: 6px;
        flex-shrink: 0;
    }
    .verif-card { border-radius: 14px; }
    .avatar-circle {
        width: 42px; height: 42px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700;
        font-size: 16px;
        flex-shrink: 0;
    }

    /* Radio card status */
    .status-option {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 14px 6px;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        cursor: pointer;
        transition: all .2s;
        font-size: .78rem;
        font-weight: 600;
        text-align: center;
        height: 100%;
        user-select: none;
    }
    .status-option i { font-size: 20px; }
    .status-option:hover { border-color: #c7d2fe; background: #fafbff; }

    .status-success { color: #10b981; }
    .status-danger  { color: #ef4444; }
    .status-warning { color: #f59e0b; }
    .status-info    { color: #06b6d4; }

    .btn-check:checked + .status-success {
        background: #d1fae5; border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16,185,129,.15);
    }
    .btn-check:checked + .status-danger {
        background: #fee2e2; border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239,68,68,.15);
    }
    .btn-check:checked + .status-warning {
        background: #fef3c7; border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245,158,11,.15);
    }
    .btn-check:checked + .status-info {
        background: #cffafe; border-color: #06b6d4;
        box-shadow: 0 0 0 3px rgba(6,182,212,.15);
    }

    /* Modal */
    .modal-icon {
        width: 64px; height: 64px;
        border-radius: 50%;
        background: #eef2ff; color: #4f46e5;
        display: flex; align-items: center; justify-content: center;
        font-size: 28px;
        margin: 0 auto;
    }
    #btnSubmitVerif { border-radius: 10px; }
</style>

{{-- ============ SCRIPT ============ --}}
<script>
    // Char counter
    const catatan = document.getElementById('catatanVerifikasi');
    const charCount = document.getElementById('charCount');
    catatan.addEventListener('input', () => {
        charCount.textContent = catatan.value.length;
    });

    // Ubah warna tombol submit sesuai pilihan
    const btnSubmit = document.getElementById('btnSubmitVerif');
    document.querySelectorAll('input[name="status_verifikasi"]').forEach(radio => {
        radio.addEventListener('change', () => {
            const colorMap = {
                disetujui_koordinator: 'success',
                ditolak: 'danger',
                revisi: 'warning',
                survei: 'info',
            };
            btnSubmit.className = 'btn w-100 py-2 fw-semibold rounded-3 btn-' + colorMap[radio.value];
        });
    });

    // Modal konfirmasi
    const form = document.getElementById('formVerifikasi');
    const modal = new bootstrap.Modal(document.getElementById('modalKonfirmasi'));
    const modalTitle = document.getElementById('modalTitle');
    const modalText = document.getElementById('modalText');
    const modalIcon = document.getElementById('modalIcon');

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const status = document.querySelector('input[name="status_verifikasi"]:checked');
        if (!status) return form.reportValidity();

        const map = {
            disetujui_koordinator: ['Setujui Pengajuan?', 'Pengajuan akan diteruskan ke tahap berikutnya.', 'check-circle-fill', '#10b981', '#d1fae5'],
            ditolak: ['Tolak Pengajuan?', 'Pengajuan akan dihentikan dan tidak dapat dilanjutkan.', 'x-circle-fill', '#ef4444', '#fee2e2'],
            revisi: ['Minta Revisi?', 'Pengajuan akan dikembalikan ke pemohon untuk diperbaiki.', 'arrow-counterclockwise', '#f59e0b', '#fef3c7'],
            survei: ['Ajukan Survei?', 'Pengajuan akan diajukan untuk proses survei lapangan terlebih dahulu.', 'clipboard-check', '#06b6d4', '#cffafe'],
        };
        const [title, text, icon, color, bg] = map[status.value];
        modalTitle.textContent = title;
        modalText.textContent = text;
        modalIcon.innerHTML = `<i class="bi bi-${icon}"></i>`;
        modalIcon.style.color = color;
        modalIcon.style.background = bg;

        modal.show();
    });

    document.getElementById('btnConfirmSubmit').addEventListener('click', () => {
        form.submit();
    });
</script>
@endsection