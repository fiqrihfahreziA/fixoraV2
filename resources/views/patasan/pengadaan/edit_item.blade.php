@extends('layouts.pengadaan.atasan')

@section('content')
<div class="container-fluid px-0">

    {{-- ============================ --}}
    {{-- HEADER --}}
    {{-- ============================ --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark">
                <i class="bi bi-pencil-square text-warning me-2"></i>Edit Item Pengajuan
            </h4>
            <span class="text-muted">
                <i class="bi bi-hash me-1"></i>{{ $pengajuan->no_pengajuan }}
            </span>
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- ============================ --}}
    {{-- ALERT --}}
    {{-- ============================ --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong><i class="bi bi-exclamation-triangle me-1"></i>Ada kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ============================ --}}
    {{-- INFO PENGAJUAN --}}
    {{-- ============================ --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <small class="text-muted d-block">Pemohon</small>
                    <strong>{{ $pengajuan->karyawan->nama ?? '-' }}</strong>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Bidang</small>
                    <strong>{{ $pengajuan->bidang->nama_bidang ?? '-' }}</strong>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Status</small>
                    @php
                        $statusColor = [
                            'draft' => 'secondary',
                            'revisi' => 'warning',
                            'diajukan' => 'warning',
                            'disetujui_koordinator' => 'info',
                            'disetujui_kabid' => 'primary',
                            'menunggu_direktur' => 'warning',
                            'disetujui' => 'success',
                            'ditolak' => 'danger',
                        ][$pengajuan->status] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $statusColor }}">
                        {{ ucfirst(str_replace('_', ' ', $pengajuan->status)) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================ --}}
    {{-- INFO TIPS --}}
    {{-- ============================ --}}
    <div class="alert alert-info d-flex align-items-center gap-2">
        <i class="bi bi-info-circle-fill fs-5"></i>
        <div>
            Anda dapat <strong>mengubah dan menambah</strong> item di halaman ini.
            Klik <strong>Simpan Semua Perubahan</strong> untuk menyimpan.
        </div>
    </div>

    {{-- ============================ --}}
    {{-- FORM EDIT ITEM --}}
    {{-- ============================ --}}
    <form action="{{ route('atasan.pengadaan.updateItem', $pengajuan->id) }}" 
          method="POST" 
          enctype="multipart/form-data" 
          id="formEditItem">
        @csrf
        @method('PUT')

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning bg-opacity-10 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-list-check me-2"></i>Daftar Item
                </h6>
                <span class="badge bg-warning text-dark" id="badgeJumlahItem">
                    {{ count($pengajuan->items) }} Item
                </span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" id="tableItems">
                        <thead class="table-light">
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th width="200">Nama Barang</th>
                                <th width="180">Spesifikasi</th>
                                <th width="90">Satuan</th>
                                <th width="100">Jumlah</th>
                                <th width="160">Harga Satuan (Rp)</th>
                                <th width="160" class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyItems">
                            @foreach($pengajuan->items as $index => $item)
                            <tr class="item-row" id="item-{{ $item->id }}" data-id="{{ $item->id }}">
                                <td class="text-center row-number">{{ $index + 1 }}</td>
                                <td>
                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                    <input type="text" 
                                           name="items[{{ $index }}][nama_barang]" 
                                           class="form-control form-control-sm" 
                                           value="{{ $item->nama_barang }}" 
                                           required>
                                </td>
                                <td>
                                    <input type="text" 
                                           name="items[{{ $index }}][spesifikasi]" 
                                           class="form-control form-control-sm" 
                                           value="{{ $item->spesifikasi }}">
                                </td>
                                <td>
                                    <input type="text" 
                                           name="items[{{ $index }}][satuan]" 
                                           class="form-control form-control-sm" 
                                           value="{{ $item->satuan ?? 'Unit' }}">
                                </td>
                                <td>
                                    <input type="number" 
                                           name="items[{{ $index }}][jumlah]" 
                                           class="form-control form-control-sm input-jumlah" 
                                           value="{{ $item->jumlah }}" 
                                           min="1" 
                                           required>
                                </td>
                                <td>
                                    <input type="number" 
                                           name="items[{{ $index }}][harga_satuan]" 
                                           class="form-control form-control-sm input-harga" 
                                           value="{{ $item->harga_satuan }}" 
                                           min="0" 
                                           required>
                                </td>
                                <td class="text-end fw-bold subtotal">
                                    Rp {{ number_format($item->harga, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="6" class="text-end fw-bold fs-6">TOTAL</td>
                                <td class="text-end fw-bold text-primary fs-6" id="grandTotal">
                                    Rp {{ number_format($pengajuan->total_pengajuan, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
                <button type="button" 
                        class="btn btn-sm btn-outline-primary rounded-pill px-3" 
                        id="btnTambahItem">
                    <i class="bi bi-plus-circle me-1"></i>Tambah Item
                </button>
                <div class="d-flex gap-2">
                    <a href="{{ url()->previous() }}" class="btn btn-light rounded-pill px-4">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </a>
                    <button type="submit" 
                            class="btn btn-warning rounded-pill px-4" 
                            id="btnSimpan">
                        <i class="bi bi-save me-1"></i>Simpan Semua Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- ============================ --}}
{{-- STYLE --}}
{{-- ============================ --}}
<style>
.item-row td { vertical-align: middle; }
.input-jumlah, .input-harga { text-align: right; }
.subtotal { white-space: nowrap; }

/* Highlight saat scroll ke item yang diklik dari detail */
.item-row:target {
    background: #fff8e1;
    box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.3);
    animation: highlight 2s ease;
}
@keyframes highlight {
    0%, 100% { background: #fff8e1; }
    50% { background: #ffecb3; }
}

/* Style input */
#tableItems input.form-control-sm {
    border-radius: 6px;
    border: 1.5px solid #e9ecef;
    transition: all 0.2s ease;
}
#tableItems input.form-control-sm:focus {
    border-color: #ffc107;
    box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.15);
    outline: none;
}

/* Responsive mobile */
@media (max-width: 768px) {
    #tableItems { font-size: 0.8rem; }
    #tableItems input.form-control-sm { font-size: 0.8rem; padding: 4px 6px; }
    .btn { font-size: 0.8rem; }
}
</style>

{{-- ============================ --}}
{{-- SCRIPT --}}
{{-- ============================ --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    let rowIndex = {{ count($pengajuan->items) }};
    const tbody = document.getElementById('tbodyItems');
    const grandTotalEl = document.getElementById('grandTotal');
    const badgeJumlah = document.getElementById('badgeJumlahItem');

    // ============================
    // HITUNG TOTAL SEMUA
    // ============================
    function hitungTotal() {
        let grandTotal = 0;
        tbody.querySelectorAll('.item-row').forEach(tr => {
            const jumlah = parseFloat(tr.querySelector('.input-jumlah')?.value) || 0;
            const harga = parseFloat(tr.querySelector('.input-harga')?.value) || 0;
            const subtotal = jumlah * harga;
            tr.querySelector('.subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            grandTotal += subtotal;
        });
        grandTotalEl.textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
    }

    // ============================
    // NOMOR ULANG
    // ============================
    function renumberRows() {
        tbody.querySelectorAll('.item-row').forEach((tr, idx) => {
            tr.querySelector('.row-number').textContent = idx + 1;
        });
        badgeJumlah.textContent = tbody.querySelectorAll('.item-row').length + ' Item';
    }

    // ============================
    // TAMBAH ITEM BARU
    // ============================
    document.getElementById('btnTambahItem').addEventListener('click', function () {
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td class="text-center row-number"></td>
            <td>
                <input type="text" name="items[${rowIndex}][nama_barang]" 
                       class="form-control form-control-sm" required>
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][spesifikasi]" 
                       class="form-control form-control-sm">
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][satuan]" 
                       class="form-control form-control-sm" value="Unit">
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][jumlah]" 
                       class="form-control form-control-sm input-jumlah" 
                       value="1" min="1" required>
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][harga_satuan]" 
                       class="form-control form-control-sm input-harga" 
                       value="0" min="0" required>
            </td>
            <td class="text-end fw-bold subtotal">Rp 0</td>
        `;
        tbody.appendChild(tr);
        rowIndex++;
        renumberRows();
        hitungTotal();

        // Fokus ke input nama barang
        tr.querySelector('input[name*="[nama_barang]"]').focus();
    });

    // ============================
    // HITUNG ULANG SAAT INPUT BERUBAH
    // ============================
    tbody.addEventListener('input', function (e) {
        if (e.target.classList.contains('input-jumlah') || 
            e.target.classList.contains('input-harga')) {
            hitungTotal();
        }
    });

    // ============================
    // SUBMIT FORM
    // ============================
    document.getElementById('formEditItem').addEventListener('submit', function (e) {
        const jumlahItem = tbody.querySelectorAll('.item-row').length;
        if (jumlahItem === 0) {
            e.preventDefault();
            alert('⚠️ Minimal harus ada 1 item!');
            return false;
        }

        const btn = document.getElementById('btnSimpan');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
    });

    // ============================
    // INIT
    // ============================
    renumberRows();
    hitungTotal();
});
</script>
@endsection