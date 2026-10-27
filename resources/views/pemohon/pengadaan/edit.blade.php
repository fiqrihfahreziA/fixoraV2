@extends('layouts.pengadaan.pemohon')

@section('content')

<div class="container-fluid px-0">

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-x-circle-fill me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i>Ada kesalahan validasi!</h5>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Header -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h4 class="mb-1 fw-bold text-dark">
                        <i class="bi bi-pencil-square text-warning me-2"></i>Edit Pengajuan
                    </h4>
                    <p class="text-muted mb-0">Perbarui formulir pengajuan Anda</p>
                </div>

                <div>
                    <a href="{{ route('pemohon.pengadaan') }}"
                       class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">

            <form action="{{ route('pemohon.pengadaan.update', $pengajuan->id) }}" method="POST" id="formPengajuan" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Step Indicator -->
                <div class="d-flex justify-content-center mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="step-indicator active">
                            <span class="step-number">1</span>
                            <span class="step-label">Identitas</span>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-indicator">
                            <span class="step-number">2</span>
                            <span class="step-label">Detail Barang</span>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-indicator">
                            <span class="step-number">3</span>
                            <span class="step-label">Dokumen</span>
                        </div>
                    </div>
                </div>

                <!-- STEP 1 -->
                <div class="step-content" id="step1">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="bi bi-person-badge me-2"></i>Informasi Pengajuan
                    </h6>

                    <div class="row g-3">
                        <!-- Tanggal Pengajuan -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">
                                Tanggal Pengajuan <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   name="tanggal_pengajuan"
                                   class="form-control"
                                   value="{{ old('tanggal_pengajuan', $pengajuan->tanggal_pengajuan) }}"
                                   required>
                        </div>

                        <!-- Pilih Barjas -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Jenis</label>
                            <select name="barjas" id="barjas" class="form-select" onchange="toggleBarjasLainnya()">
                                <option value="">Pilih Jenis</option>
                                <option value="barang" {{ old('barjas', $pengajuan->barjas) == 'barang' ? 'selected' : '' }}>Barang</option>
                                <option value="jasa" {{ old('barjas', $pengajuan->barjas) == 'jasa' ? 'selected' : '' }}>Jasa</option>
                                <option value="lainnya" {{ old('barjas', $pengajuan->barjas) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>

                        <!-- Input manual kalau pilih "Lainnya" -->
                        <div class="col-md-3 {{ old('barjas', $pengajuan->barjas) == 'lainnya' ? '' : 'd-none' }}" id="wrapper_barjas_lainnya">
                            <label class="form-label fw-semibold small">Barjas Lainnya</label>
                            <input type="text" name="barajas_lainnya" class="form-control" placeholder="Masukkan barjas lainnya..."
                                   value="{{ old('barajas_lainnya', $pengajuan->barajas_lainnya) }}">
                        </div>

                        <!-- Tahun Anggaran -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold small">Tahun Anggaran</label>
                            <input type="number"
                                   name="tahun_anggaran"
                                   class="form-control"
                                   value="{{ old('tahun_anggaran', $pengajuan->tahun_anggaran ?? date('Y')) }}"
                                   min="2000"
                                   max="{{ date('Y') + 1 }}">
                        </div>

                        <!-- Dasar Usulan -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">
                                Dasar Usulan <span class="text-danger">*</span>
                            </label>
                            <select name="dasar_usulan" id="dasar_usulan" class="form-select" required onchange="toggleDasarUsulanLainnya()">
                                <option value="">Pilih Dasar Usulan</option>
                                <option value="Program Kerja" {{ old('dasar_usulan', $pengajuan->dasar_usulan) == 'Program Kerja' ? 'selected' : '' }}>Program Kerja</option>
                                <option value="Kebutuhan Operasional" {{ old('dasar_usulan', $pengajuan->dasar_usulan) == 'Kebutuhan Operasional' ? 'selected' : '' }}>Kebutuhan Operasional</option>
                                <option value="Penggantian Barang Rusak" {{ old('dasar_usulan', $pengajuan->dasar_usulan) == 'Penggantian Barang Rusak' ? 'selected' : '' }}>Penggantian Barang Rusak</option>
                                <option value="Penambahan Kapasitas Pelayanan" {{ old('dasar_usulan', $pengajuan->dasar_usulan) == 'Penambahan Kapasitas Pelayanan' ? 'selected' : '' }}>Penambahan Kapasitas Pelayanan</option>
                                <option value="Pemenuhan Standar Akreditasi" {{ old('dasar_usulan', $pengajuan->dasar_usulan) == 'Pemenuhan Standar Akreditasi' ? 'selected' : '' }}>Pemenuhan Standar Akreditasi</option>
                                <option value="Keselamatan Pasien" {{ old('dasar_usulan', $pengajuan->dasar_usulan) == 'Keselamatan Pasien' ? 'selected' : '' }}>Keselamatan Pasien</option>
                                <option value="lainnya" {{ old('dasar_usulan', $pengajuan->dasar_usulan) == 'lainnya' ? 'selected' : '' }}>Lainnya...</option>
                            </select>
                        </div>

                        <!-- Input manual kalau pilih "Lainnya" -->
                        <div class="col-md-4 {{ old('dasar_usulan', $pengajuan->dasar_usulan) == 'lainnya' ? '' : 'd-none' }}" id="wrapper_dasar_usulan_lainnya">
                            <label class="form-label fw-semibold small">
                                Dasar Usulan Lainnya <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="dasar_usulan_lainnya" class="form-control" placeholder="Masukkan dasar usulan lainnya..."
                                   value="{{ old('dasar_usulan_lainnya', $pengajuan->dasar_usulan_lainnya) }}">
                        </div>

                        <!-- Nama Pengaju dengan Hidden ID -->
                        <div class="col-md-4">
                            <input type="hidden" 
                                   name="karyawan_id" 
                                   value="{{ $authUser->karyawan->id ?? '' }}">
                            <label class="form-label fw-semibold small mb-1">
                                Nama Pengaju <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control bg-light"
                                   value="{{ $authUser->karyawan->nama ?? $authUser->name ?? '' }}"
                                   readonly>
                        </div>

                        <!-- Jabatan -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small mb-1">
                                Jabatan <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control bg-light"
                                   value="{{ $authUser->karyawan->jabatan ?? $authUser->jabatan ?? '' }}"
                                   readonly>
                        </div>

                        <!-- NIP -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small mb-1">
                                NIP <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control bg-light"
                                   value="{{ $authUser->karyawan->nip ?? '' }}"
                                   readonly>
                        </div>

                        <!-- Instalasi -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Instalasi</label>
                            <input type="text"
                                   name="instalasi"
                                   class="form-control bg-light"
                                   value="{{ old('instalasi', $pengajuan->instalasi ?? $authUser->karyawan->ruangan ?? '') }}"
                                   readonly>
                        </div>

                        <!-- Kondisi Barang Lama -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">
                                Kondisi Barang Lama
                            </label>
                            <select name="kondisi_barang_lama" class="form-select">
                                <option value="">Pilih Kondisi</option>
                                <option value="Rusak berat" {{ old('kondisi_barang_lama', $pengajuan->kondisi_barang_lama) == 'Rusak berat' ? 'selected' : '' }}>Rusak berat</option>
                                <option value="Rusak Ringan" {{ old('kondisi_barang_lama', $pengajuan->kondisi_barang_lama) == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                <option value="Tidak Memadai" {{ old('kondisi_barang_lama', $pengajuan->kondisi_barang_lama) == 'Tidak Memadai' ? 'selected' : '' }}>Tidak Memadai</option>
                                <option value="Kapasitas Kurang" {{ old('kondisi_barang_lama', $pengajuan->kondisi_barang_lama) == 'Kapasitas Kurang' ? 'selected' : '' }}>Kapasitas Kurang</option>
                                <option value="Tidak Tersedia" {{ old('kondisi_barang_lama', $pengajuan->kondisi_barang_lama) == 'Tidak Tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
                            </select>
                        </div>

                        <!-- Keterangan Barang Lama -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">
                                Keterangan Barang Lama
                            </label>
                            <textarea name="ket_barang_lama" 
                                      class="form-control" 
                                      rows="2"
                                      placeholder="Jelaskan kondisi barang lama...">{{ old('ket_barang_lama', $pengajuan->ket_barang_lama) }}</textarea>
                        </div>
                    </div>

                    <!-- BARIS KEDUA: Alasan Justifikasi, Dampak, & Manfaat -->
                    <div class="row g-3 mt-2">
                        <!-- Alasan Justifikasi -->
                        <div class="col-md-4">
                            <div class="card bg-light border-0 p-3 h-100">
                                <label class="form-label fw-semibold small">
                                    <i class="bi bi-clipboard-check text-success me-2"></i>
                                    Alasan Justifikasi <span class="text-danger">*</span>
                                </label>
                                <textarea name="alasan_justifikasi"
                                        class="form-control"
                                        rows="4"
                                        placeholder="Jelaskan alasan dan justifikasi..."
                                        required>{{ old('alasan_justifikasi', $pengajuan->alasan_justifikasi) }}</textarea>
                                <small class="text-muted mt-1">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Contoh: Meningkatkan efisiensi kerja, mengganti alat usang, dll.
                                </small>
                            </div>
                        </div>

                        <!-- Dampak -->
                        <div class="col-md-4">
                            <div class="card bg-light border-0 p-3 h-100">
                                <label class="form-label fw-semibold small">
                                    <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                                    Dampak Jika Tidak Dilaksanakan <span class="text-danger">*</span>
                                </label>
                                <textarea name="dampak"
                                        class="form-control"
                                        rows="4"
                                        placeholder="Jelaskan dampak yang akan terjadi..."
                                        required>{{ old('dampak', $pengajuan->dampak) }}</textarea>
                                <small class="text-muted mt-1">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Contoh: Terganggunya pelayanan, kerugian operasional, dll.
                                </small>
                            </div>
                        </div>

                        <!-- Manfaat -->
                        <div class="col-md-4">
                            <div class="card bg-light border-0 p-3 h-100">
                                <label class="form-label fw-semibold small">
                                    <i class="bi bi-star-fill text-primary me-2"></i>
                                    Manfaat yang Diharapkan <span class="text-danger">*</span>
                                </label>
                                <textarea name="manfaat"
                                        class="form-control"
                                        rows="4"
                                        placeholder="Jelaskan manfaat yang akan diperoleh..."
                                        required>{{ old('manfaat', $pengajuan->manfaat) }}</textarea>
                                <small class="text-muted mt-1">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Contoh: Meningkatkan kualitas pelayanan, mempercepat proses kerja, dll.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2 -->
                <div class="step-content d-none" id="step2">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-primary mb-0">
                            <i class="bi bi-list-check me-2"></i>Daftar Barang
                        </h6>

                        <button type="button"
                                class="btn btn-sm btn-outline-primary"
                                onclick="tambahItem()">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Item
                        </button>
                    </div>

                    <div id="itemsContainer">
                        @foreach($pengajuan->items as $index => $item)
                        <div class="item-row card p-3 mb-3 bg-light">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold small">
                                        Nama Barang <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="items[{{ $index }}][nama_barang]"
                                           class="form-control item-required"
                                           placeholder="Nama barang"
                                           value="{{ old('items.'.$index.'.nama_barang', $item->nama_barang) }}"
                                           required>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label fw-semibold small">Spesifikasi</label>
                                    <input type="text"
                                           name="items[{{ $index }}][spesifikasi]"
                                           class="form-control"
                                           placeholder="Spesifikasi"
                                           value="{{ old('items.'.$index.'.spesifikasi', $item->spesifikasi) }}">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label fw-semibold small">Satuan</label>
                                    <select name="items[{{ $index }}][satuan]" class="form-select">
                                        <option value="Unit" {{ old('items.'.$index.'.satuan', $item->satuan) == 'Unit' ? 'selected' : '' }}>Unit</option>
                                        <option value="Buah" {{ old('items.'.$index.'.satuan', $item->satuan) == 'Buah' ? 'selected' : '' }}>Buah</option>
                                        <option value="Pcs" {{ old('items.'.$index.'.satuan', $item->satuan) == 'Pcs' ? 'selected' : '' }}>Pcs</option>
                                        <option value="Box" {{ old('items.'.$index.'.satuan', $item->satuan) == 'Box' ? 'selected' : '' }}>Box</option>
                                        <option value="Paket" {{ old('items.'.$index.'.satuan', $item->satuan) == 'Paket' ? 'selected' : '' }}>Paket</option>
                                    </select>
                                </div>

                                <div class="col-md-1">
                                    <label class="form-label fw-semibold small">
                                        Jumlah <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                           name="items[{{ $index }}][jumlah]"
                                           class="form-control item-required hitung-total"
                                           placeholder="0"
                                           min="1"
                                           value="{{ old('items.'.$index.'.jumlah', $item->jumlah) }}"
                                           required
                                           oninput="hitungTotalItem(this)">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label fw-semibold small">
                                        Harga Satuan (Rp) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                           name="items[{{ $index }}][harga_satuan]"
                                           class="form-control item-required hitung-total"
                                           placeholder="0"
                                           min="0"
                                           value="{{ old('items.'.$index.'.harga_satuan', $item->harga_satuan) }}"
                                           required
                                           oninput="hitungTotalItem(this)">
                                </div>

                                <div class="col-md-1">
                                    <label class="form-label fw-semibold small">Total (Rp)</label>
                                    <input type="text"
                                           name="items[{{ $index }}][total]"
                                           class="form-control bg-light total-harga"
                                           value="{{ number_format($item->jumlah * $item->harga_satuan, 0, ',', '.') }}"
                                           readonly>
                                </div>

                                <div class="col-md-1">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger w-100 mt-4"
                                            onclick="hapusItem(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- BARIS KEDUA: Data Barang Tersedia -->
                            <div class="row g-3 mt-3 pt-3 border-top">
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="items[{{ $index }}][ada_barang_tersedia]" 
                                               id="barang_tersedia_{{ $index }}"
                                               value="1"
                                               {{ $item->barangTersedia ? 'checked' : '' }}
                                               onchange="toggleBarangTersedia(this, {{ $index }})">
                                        <label class="form-check-label fw-semibold text-success" for="barang_tersedia_{{ $index }}">
                                            <i class="bi bi-box-seam me-1"></i>
                                            Ada Barang Tersedia (Centang jika ada barang yang sudah tersedia)
                                        </label>
                                    </div>
                                </div>

                                <!-- Detail Barang Tersedia -->
                                <div class="col-12" id="detail_barang_tersedia_{{ $index }}" style="{{ $item->barangTersedia ? 'display: block;' : 'display: none;' }}">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">
                                                Nama Barang Tersedia <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                   name="items[{{ $index }}][barang_tersedia][nama_barang]"
                                                   class="form-control"
                                                   placeholder="Nama barang yang sudah tersedia"
                                                   value="{{ old('items.'.$index.'.barang_tersedia.nama_barang', $item->barangTersedia->nama_barang ?? '') }}"
                                                   {{ $item->barangTersedia ? '' : 'disabled' }}>
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold small">
                                                Jumlah Tersedia <span class="text-danger">*</span>
                                            </label>
                                            <input type="number"
                                                   name="items[{{ $index }}][barang_tersedia][jumlah]"
                                                   class="form-control"
                                                   placeholder="0"
                                                   min="0"
                                                   value="{{ old('items.'.$index.'.barang_tersedia.jumlah', $item->barangTersedia->jumlah ?? '') }}"
                                                   {{ $item->barangTersedia ? '' : 'disabled' }}>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">
                                                Tahun Perolehan <span class="text-danger">*</span>
                                            </label>
                                            <input type="number"
                                                   name="items[{{ $index }}][barang_tersedia][tahun_perolehan]"
                                                   class="form-control"
                                                   placeholder="2020"
                                                   min="1900"
                                                   max="{{ date('Y') }}"
                                                   value="{{ old('items.'.$index.'.barang_tersedia.tahun_perolehan', $item->barangTersedia->tahun_perolehan ?? '') }}"
                                                   {{ $item->barangTersedia ? '' : 'disabled' }}>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">
                                                Kondisi <span class="text-danger">*</span>
                                            </label>
                                            <select name="items[{{ $index }}][barang_tersedia][kondisi]" class="form-select" {{ $item->barangTersedia ? '' : 'disabled' }}>
                                                <option value="">Pilih Kondisi</option>
                                                <option value="Baik" {{ old('items.'.$index.'.barang_tersedia.kondisi', $item->barangTersedia->kondisi ?? '') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                                <option value="Cukup Baik" {{ old('items.'.$index.'.barang_tersedia.kondisi', $item->barangTersedia->kondisi ?? '') == 'Cukup Baik' ? 'selected' : '' }}>Cukup Baik</option>
                                                <option value="Rusak Ringan" {{ old('items.'.$index.'.barang_tersedia.kondisi', $item->barangTersedia->kondisi ?? '') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                                <option value="Rusak Berat" {{ old('items.'.$index.'.barang_tersedia.kondisi', $item->barangTersedia->kondisi ?? '') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                                                <option value="Usang" {{ old('items.'.$index.'.barang_tersedia.kondisi', $item->barangTersedia->kondisi ?? '') == 'Usang' ? 'selected' : '' }}>Usang</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- BARIS KETIGA: Dokumen Upload per Item (toggle) -->
                            <div class="row g-3 mt-3 pt-3 border-top">
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="items[{{ $index }}][ada_dokumen]" 
                                               id="ada_dokumen_{{ $index }}"
                                               value="1"
                                               {{ $item->dokumen_upload ? 'checked' : '' }}
                                               onchange="toggleDokumenItem(this, {{ $index }})">
                                        <label class="form-check-label fw-semibold text-primary" for="ada_dokumen_{{ $index }}">
                                            <i class="bi bi-paperclip me-1"></i>Upload Dokumen
                                        </label>
                                        @if($item->dokumen_upload)
                                            <small class="text-success d-block ms-4 mt-1">
                                                <i class="bi bi-check-circle me-1"></i>File sudah diupload: {{ basename($item->dokumen_upload) }}
                                            </small>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12" id="detail_dokumen_{{ $index }}" style="{{ $item->dokumen_upload ? 'display: block;' : 'display: none;' }}">
                                    <label class="form-label fw-semibold small">
                                        Dokumen Upload (Kosongkan jika tidak ingin mengubah)
                                    </label>
                                    <input type="file"
                                           name="items[{{ $index }}][dokumen_upload]"
                                           class="form-control"
                                           accept=".pdf,.jpg,.jpeg,.png,.xlsx,.xls"
                                           {{ $item->dokumen_upload ? '' : 'disabled' }}>
                                    <small class="text-muted">Format: PDF, JPG, PNG, Excel (Max: 5MB)</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Ringkasan Total -->
                    <div class="bg-light p-3 rounded-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-semibold">Total Item:</span>
                                    <span id="totalItem">{{ $pengajuan->items->count() }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-semibold">Grand Total (Rp):</span>
                                    <span id="grandTotal" class="fw-bold text-primary">{{ number_format($pengajuan->total_pengajuan, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 3 -->
                <div class="step-content d-none" id="step3">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="bi bi-paperclip me-2"></i>Dokumen Pendukung
                    </h6>
                    <p class="text-muted small mb-3">
                        <i class="bi bi-info-circle me-1"></i>Centang dokumen yang ingin diupload. <strong>Semua opsional.</strong>
                    </p>

                    <div class="row g-3">

                        <!-- 1. Data Kerusakan -->
                        <div class="col-md-6">
                            <div class="card p-3 border-0 bg-light h-100">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="ada_data_kerusakan" 
                                           id="ada_data_kerusakan"
                                           value="1"
                                           {{ $pengajuan->data_kerusakan ? 'checked' : '' }}
                                           onchange="toggleDokumenPendukung(this, 'data_kerusakan')">
                                    <label class="form-check-label fw-semibold text-danger" for="ada_data_kerusakan">
                                        <i class="bi bi-file-earmark-pdf me-1"></i>Data Kerusakan
                                    </label>
                                    @if($pengajuan->data_kerusakan)
                                        <small class="text-success d-block ms-4 mt-1">
                                            <i class="bi bi-check-circle me-1"></i>File sudah ada
                                        </small>
                                    @endif
                                </div>
                                <div class="mt-2 {{ $pengajuan->data_kerusakan ? '' : 'd-none' }}" id="detail_data_kerusakan">
                                    <input type="file"
                                           name="data_kerusakan"
                                           class="form-control"
                                           accept=".pdf"
                                           {{ $pengajuan->data_kerusakan ? '' : 'disabled' }}>
                                    <small class="text-muted">Format: PDF (Max: 5MB)</small>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Analisis Kebutuhan -->
                        <div class="col-md-6">
                            <div class="card p-3 border-0 bg-light h-100">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="ada_analisis_kebutuhan" 
                                           id="ada_analisis_kebutuhan"
                                           value="1"
                                           {{ $pengajuan->analisis_kebutuhan ? 'checked' : '' }}
                                           onchange="toggleDokumenPendukung(this, 'analisis_kebutuhan')">
                                    <label class="form-check-label fw-semibold text-primary" for="ada_analisis_kebutuhan">
                                        <i class="bi bi-clipboard-data me-1"></i>Analisis Kebutuhan
                                    </label>
                                    @if($pengajuan->analisis_kebutuhan)
                                        <small class="text-success d-block ms-4 mt-1">
                                            <i class="bi bi-check-circle me-1"></i>File sudah ada
                                        </small>
                                    @endif
                                </div>
                                <div class="mt-2 {{ $pengajuan->analisis_kebutuhan ? '' : 'd-none' }}" id="detail_analisis_kebutuhan">
                                    <input type="file"
                                           name="analisis_kebutuhan"
                                           class="form-control"
                                           accept=".pdf,.doc,.docx,.xlsx,.xls"
                                           {{ $pengajuan->analisis_kebutuhan ? '' : 'disabled' }}>
                                    <small class="text-muted">Format: PDF, Word, Excel (Max: 5MB)</small>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Spesifikasi Teknis -->
                        <div class="col-md-6">
                            <div class="card p-3 border-0 bg-light h-100">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="ada_spesifikasi_teknis" 
                                           id="ada_spesifikasi_teknis"
                                           value="1"
                                           {{ $pengajuan->spesifikasi_teknis ? 'checked' : '' }}
                                           onchange="toggleDokumenPendukung(this, 'spesifikasi_teknis')">
                                    <label class="form-check-label fw-semibold text-primary" for="ada_spesifikasi_teknis">
                                        <i class="bi bi-gear me-1"></i>Spesifikasi Teknis
                                    </label>
                                    @if($pengajuan->spesifikasi_teknis)
                                        <small class="text-success d-block ms-4 mt-1">
                                            <i class="bi bi-check-circle me-1"></i>File sudah ada
                                        </small>
                                    @endif
                                </div>
                                <div class="mt-2 {{ $pengajuan->spesifikasi_teknis ? '' : 'd-none' }}" id="detail_spesifikasi_teknis">
                                    <input type="file"
                                           name="spesifikasi_teknis"
                                           class="form-control"
                                           accept=".pdf,.doc,.docx,.xlsx,.xls"
                                           {{ $pengajuan->spesifikasi_teknis ? '' : 'disabled' }}>
                                    <small class="text-muted">Format: PDF, Word, Excel (Max: 5MB)</small>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Foto Barang -->
                        <div class="col-md-6">
                            <div class="card p-3 border-0 bg-light h-100">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="ada_foto_barang" 
                                           id="ada_foto_barang"
                                           value="1"
                                           {{ $pengajuan->foto_barang ? 'checked' : '' }}
                                           onchange="toggleDokumenPendukung(this, 'foto_barang')">
                                    <label class="form-check-label fw-semibold text-primary" for="ada_foto_barang">
                                        <i class="bi bi-image me-1"></i>Foto Barang
                                    </label>
                                    @if($pengajuan->foto_barang)
                                        <small class="text-success d-block ms-4 mt-1">
                                            <i class="bi bi-check-circle me-1"></i>File sudah ada
                                        </small>
                                    @endif
                                </div>
                                <div class="mt-2 {{ $pengajuan->foto_barang ? '' : 'd-none' }}" id="detail_foto_barang">
                                    <input type="file"
                                           name="foto_barang[]"
                                           class="form-control"
                                           accept="image/*"
                                           multiple
                                           {{ $pengajuan->foto_barang ? '' : 'disabled' }}>
                                    <small class="text-muted">Format: JPG, PNG (bisa pilih beberapa file)</small>
                                </div>
                            </div>
                        </div>

                        <!-- 5. Penawaran Harga -->
                        <div class="col-md-6">
                            <div class="card p-3 border-0 bg-light h-100">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="ada_penawaran_harga" 
                                           id="ada_penawaran_harga"
                                           value="1"
                                           {{ $pengajuan->penawaran_harga ? 'checked' : '' }}
                                           onchange="toggleDokumenPendukung(this, 'penawaran_harga')">
                                    <label class="form-check-label fw-semibold text-primary" for="ada_penawaran_harga">
                                        <i class="bi bi-cash-coin me-1"></i>Penawaran Harga
                                    </label>
                                    @if($pengajuan->penawaran_harga)
                                        <small class="text-success d-block ms-4 mt-1">
                                            <i class="bi bi-check-circle me-1"></i>File sudah ada
                                        </small>
                                    @endif
                                </div>
                                <div class="mt-2 {{ $pengajuan->penawaran_harga ? '' : 'd-none' }}" id="detail_penawaran_harga">
                                    <input type="file"
                                           name="penawaran_harga"
                                           class="form-control"
                                           accept=".pdf,.xlsx,.xls,.doc,.docx"
                                           {{ $pengajuan->penawaran_harga ? '' : 'disabled' }}>
                                    <small class="text-muted">Format: PDF, Excel, Word (Max: 5MB)</small>
                                </div>
                            </div>
                        </div>

                        <!-- 6. Data Pengguna -->
                        <div class="col-md-6">
                            <div class="card p-3 border-0 bg-light h-100">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="ada_data_pengguna" 
                                           id="ada_data_pengguna"
                                           value="1"
                                           {{ $pengajuan->data_pengguna ? 'checked' : '' }}
                                           onchange="toggleDokumenPendukung(this, 'data_pengguna')">
                                    <label class="form-check-label fw-semibold text-primary" for="ada_data_pengguna">
                                        <i class="bi bi-graph-up me-1"></i>Data Penggunaan / Pemakaian
                                    </label>
                                    @if($pengajuan->data_pengguna)
                                        <small class="text-success d-block ms-4 mt-1">
                                            <i class="bi bi-check-circle me-1"></i>File sudah ada
                                        </small>
                                    @endif
                                </div>
                                <div class="mt-2 {{ $pengajuan->data_pengguna ? '' : 'd-none' }}" id="detail_data_pengguna">
                                    <input type="file"
                                           name="data_pengguna"
                                           class="form-control"
                                           accept=".pdf,.xlsx,.xls,.csv,.doc,.docx"
                                           {{ $pengajuan->data_pengguna ? '' : 'disabled' }}>
                                    <small class="text-muted">Format: PDF, Excel, CSV, Word (Max: 5MB)</small>
                                </div>
                            </div>
                        </div>

                        <!-- 7. Dokumen Pendukung Lainnya -->
                        <div class="col-md-6">
                            <div class="card p-3 border-0 bg-light h-100">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="ada_dokumen_lainnya" 
                                           id="ada_dokumen_lainnya"
                                           value="1"
                                           {{ $pengajuan->dokumen_pendukung_lainnya ? 'checked' : '' }}
                                           onchange="toggleDokumenPendukung(this, 'dokumen_lainnya')">
                                    <label class="form-check-label fw-semibold text-primary" for="ada_dokumen_lainnya">
                                        <i class="bi bi-folder-plus me-1"></i>Dokumen Pendukung Lainnya
                                    </label>
                                    @if($pengajuan->dokumen_pendukung_lainnya)
                                        <small class="text-success d-block ms-4 mt-1">
                                            <i class="bi bi-check-circle me-1"></i>File sudah ada
                                        </small>
                                    @endif
                                </div>
                                <div class="mt-2 {{ $pengajuan->dokumen_pendukung_lainnya ? '' : 'd-none' }}" id="detail_dokumen_lainnya">
                                    <input type="file"
                                           name="dokumen_pendukung_lainnya"
                                           class="form-control"
                                           accept=".pdf,.doc,.docx,.xlsx,.xls,.jpg,.jpeg,.png"
                                           {{ $pengajuan->dokumen_pendukung_lainnya ? '' : 'disabled' }}>
                                    <small class="text-muted">Format: PDF, Word, Excel, Gambar (Max: 5MB)</small>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('pemohon.pengadaan') }}"
                           class="btn btn-outline-secondary rounded-pill px-4">
                            Batal
                        </a>

                        <div class="d-flex gap-2">
                            <button type="button"
                                    class="btn btn-outline-primary rounded-pill px-4"
                                    id="btnPrevStep"
                                    onclick="keStepSebelumnya()"
                                    style="display:none;">
                                Sebelumnya
                            </button>

                            <button type="button"
                                    class="btn btn-primary rounded-pill px-4"
                                    id="btnNextStep"
                                    onclick="keStepBerikutnya()">
                                Selanjutnya
                            </button>

                            <button type="submit"
                                    class="btn btn-success rounded-pill px-4"
                                    id="submitBtn"
                                    style="display:none;">
                                Update Pengajuan
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<style>
.step-indicator{
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:4px;
    opacity:.5;
    transition:.3s;
}

.step-indicator.active{
    opacity:1;
}

.step-number{
    width:32px;
    height:32px;
    border-radius:50%;
    background:#e9ecef;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700;
}

.step-indicator.active .step-number{
    background:#0d6efd;
    color:#fff;
}

.step-line{
    width:40px;
    height:2px;
    background:#dee2e6;
}

.step-indicator.active + .step-line{
    background:#0d6efd;
}
</style>

<script>
let indeksItem = {{ $pengajuan->items->count() }};
let stepSekarang = 1;

// =========================
// TAMBAH ITEM
// =========================
function tambahItem() {
    const container = document.getElementById('itemsContainer');

    const itemRow = document.createElement('div');
    itemRow.className = 'item-row card p-3 mb-3 bg-light';

    itemRow.innerHTML = `
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold small">
                    Nama Barang <span class="text-danger">*</span>
                </label>
                <input type="text"
                       name="items[\${indeksItem}][nama_barang]"
                       class="form-control item-required"
                       placeholder="Nama barang"
                       required>
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold small">Spesifikasi</label>
                <input type="text"
                       name="items[\${indeksItem}][spesifikasi]"
                       class="form-control"
                       placeholder="Spesifikasi">
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold small">Satuan</label>
                <select name="items[\${indeksItem}][satuan]" class="form-select">
                    <option value="Unit">Unit</option>
                    <option value="Buah">Buah</option>
                    <option value="Pcs">Pcs</option>
                    <option value="Box">Box</option>
                    <option value="Paket">Paket</option>
                </select>
            </div>

            <div class="col-md-1">
                <label class="form-label fw-semibold small">
                    Jumlah <span class="text-danger">*</span>
                </label>
                <input type="number"
                       name="items[\${indeksItem}][jumlah]"
                       class="form-control item-required hitung-total"
                       placeholder="0"
                       min="1"
                       required
                       oninput="hitungTotalItem(this)">
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold small">
                    Harga Satuan (Rp) <span class="text-danger">*</span>
                </label>
                <input type="number"
                       name="items[\${indeksItem}][harga_satuan]"
                       class="form-control item-required hitung-total"
                       placeholder="0"
                       min="0"
                       required
                       oninput="hitungTotalItem(this)">
            </div>

            <div class="col-md-1">
                <label class="form-label fw-semibold small">Total (Rp)</label>
                <input type="text"
                       name="items[\${indeksItem}][total]"
                       class="form-control bg-light total-harga"
                       value="0"
                       readonly>
            </div>

            <div class="col-md-1">
                <button type="button"
                        class="btn btn-sm btn-outline-danger w-100 mt-4"
                        onclick="hapusItem(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>

        <!-- BARIS KEDUA: Data Barang Tersedia -->
        <div class="row g-3 mt-3 pt-3 border-top">
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" 
                           type="checkbox" 
                           name="items[\${indeksItem}][ada_barang_tersedia]" 
                           id="barang_tersedia_\${indeksItem}"
                           value="1"
                           onchange="toggleBarangTersedia(this, \${indeksItem})">
                    <label class="form-check-label fw-semibold text-success" for="barang_tersedia_\${indeksItem}">
                        <i class="bi bi-box-seam me-1"></i>
                        Ada Barang Tersedia (Centang jika ada barang yang sudah tersedia)
                    </label>
                </div>
            </div>

            <div class="col-12" id="detail_barang_tersedia_\${indeksItem}" style="display: none;">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">
                            Nama Barang Tersedia <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="items[\${indeksItem}][barang_tersedia][nama_barang]"
                               class="form-control"
                               placeholder="Nama barang yang sudah tersedia"
                               disabled>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold small">
                            Jumlah Tersedia <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                               name="items[\${indeksItem}][barang_tersedia][jumlah]"
                               class="form-control"
                               placeholder="0"
                               min="0"
                               disabled>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">
                            Tahun Perolehan <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                               name="items[\${indeksItem}][barang_tersedia][tahun_perolehan]"
                               class="form-control"
                               placeholder="2020"
                               min="1900"
                               max="\${new Date().getFullYear()}"
                               disabled>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">
                            Kondisi <span class="text-danger">*</span>
                        </label>
                        <select name="items[\${indeksItem}][barang_tersedia][kondisi]" class="form-select" disabled>
                            <option value="">Pilih Kondisi</option>
                            <option value="Baik">Baik</option>
                            <option value="Cukup Baik">Cukup Baik</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                            <option value="Usang">Usang</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- BARIS KETIGA: Dokumen Upload per Item (toggle) -->
        <div class="row g-3 mt-3 pt-3 border-top">
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" 
                           type="checkbox" 
                           name="items[\${indeksItem}][ada_dokumen]" 
                           id="ada_dokumen_\${indeksItem}"
                           value="1"
                           onchange="toggleDokumenItem(this, \${indeksItem})">
                    <label class="form-check-label fw-semibold text-primary" for="ada_dokumen_\${indeksItem}">
                        <i class="bi bi-paperclip me-1"></i>Upload Dokumen
                    </label>
                </div>
            </div>

            <div class="col-12" id="detail_dokumen_\${indeksItem}" style="display: none;">
                <label class="form-label fw-semibold small">
                    Dokumen Upload
                </label>
                <input type="file"
                       name="items[\${indeksItem}][dokumen_upload]"
                       class="form-control"
                       accept=".pdf,.jpg,.jpeg,.png,.xlsx,.xls"
                       disabled>
                <small class="text-muted">Format: PDF, JPG, PNG, Excel (Max: 5MB)</small>
            </div>
        </div>
    `;

    container.appendChild(itemRow);
    indeksItem++;
    perbaruiTotalItem();
}

// =========================
// TOGGLE BARJAS LAINNYA
// =========================
function toggleBarjasLainnya() {
    const val = document.getElementById('barjas').value;
    const wrapper = document.getElementById('wrapper_barjas_lainnya');
    const input = wrapper.querySelector('input[name="barajas_lainnya"]');

    if (val === 'lainnya') {
        wrapper.classList.remove('d-none');
        input.setAttribute('required', 'required');
    } else {
        wrapper.classList.add('d-none');
        input.removeAttribute('required');
        input.value = '';
    }
}

// =========================
// TOGGLE DASAR USULAN LAINNYA
// =========================
function toggleDasarUsulanLainnya() {
    const val = document.getElementById('dasar_usulan').value;
    const wrapper = document.getElementById('wrapper_dasar_usulan_lainnya');
    const input = wrapper.querySelector('input[name="dasar_usulan_lainnya"]');

    if (val === 'lainnya') {
        wrapper.classList.remove('d-none');
        input.setAttribute('required', 'required');
    } else {
        wrapper.classList.add('d-none');
        input.removeAttribute('required');
        input.value = '';
    }
}

// =========================
// TOGGLE BARANG TERSEDIA
// =========================
function toggleBarangTersedia(checkbox, index) {
    const detailDiv = document.getElementById('detail_barang_tersedia_' + index);
    const inputs = detailDiv.querySelectorAll('input, select');
    
    if (checkbox.checked) {
        detailDiv.style.display = 'block';
        inputs.forEach(input => input.disabled = false);
    } else {
        detailDiv.style.display = 'none';
        inputs.forEach(input => {
            input.disabled = true;
            input.value = '';
        });
    }
}

// =========================
// TOGGLE DOKUMEN PER ITEM
// =========================
function toggleDokumenItem(checkbox, index) {
    const detailDiv = document.getElementById('detail_dokumen_' + index);
    if (!detailDiv) return;

    const inputs = detailDiv.querySelectorAll('input');

    if (checkbox.checked) {
        detailDiv.style.display = 'block';
        inputs.forEach(input => input.disabled = false);
    } else {
        detailDiv.style.display = 'none';
        inputs.forEach(input => {
            input.disabled = true;
            input.value = '';
        });
    }
}

// =========================
// TOGGLE DOKUMEN PENDUKUNG (Step 3)
// =========================
function toggleDokumenPendukung(checkbox, fieldName) {
    const detailDiv = document.getElementById('detail_' + fieldName);
    if (!detailDiv) return;

    const inputs = detailDiv.querySelectorAll('input');

    if (checkbox.checked) {
        detailDiv.classList.remove('d-none');
        detailDiv.style.display = 'block';
        inputs.forEach(input => input.disabled = false);
    } else {
        detailDiv.classList.add('d-none');
        detailDiv.style.display = 'none';
        inputs.forEach(input => {
            input.disabled = true;
            input.value = '';
        });
    }
}

// =========================
// HITUNG TOTAL PER ITEM
// =========================
function hitungTotalItem(element) {
    const row = element.closest('.item-row');
    
    const jumlahInput = row.querySelector('input[name*="[jumlah]"]');
    const hargaInput = row.querySelector('input[name*="[harga_satuan]"]');
    const totalInput = row.querySelector('input[name*="[total]"]');
    
    const jumlah = parseInt(jumlahInput.value) || 0;
    const harga = parseInt(hargaInput.value) || 0;
    
    const total = jumlah * harga;
    
    totalInput.value = formatRupiah(total);
    
    hitungGrandTotal();
}

// =========================
// FORMAT RUPIAH
// =========================
function formatRupiah(angka) {
    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// =========================
// HITUNG GRAND TOTAL
// =========================
function hitungGrandTotal() {
    const totalInputs = document.querySelectorAll('.total-harga');
    let grandTotal = 0;
    
    totalInputs.forEach(input => {
        const value = input.value.replace(/\./g, '');
        grandTotal += parseInt(value) || 0;
    });
    
    document.getElementById('grandTotal').textContent = formatRupiah(grandTotal);
}

// =========================
// HAPUS ITEM
// =========================
function hapusItem(button) {
    const rows = document.querySelectorAll('.item-row');

    if (rows.length > 1) {
        button.closest('.item-row').remove();
        perbaruiTotalItem();
        hitungGrandTotal();
    } else {
        alert('Minimal 1 item harus diisi!');
    }
}

// =========================
// PERBARUI TOTAL ITEM
// =========================
function perbaruiTotalItem() {
    const totalItem = document.querySelectorAll('.item-row').length;
    document.getElementById('totalItem').textContent = totalItem;
    hitungGrandTotal();
}

// =========================
// VALIDASI STEP AKTIF
// =========================
function validasiStepSekarang() {
    const step = document.getElementById('step' + stepSekarang);
    const requiredFields = step.querySelectorAll('[required]');

    for (const field of requiredFields) {
        if (field.disabled) continue;
        if (!field.value) {
            field.reportValidity();
            field.focus();
            return false;
        }
    }
    return true;
}

// =========================
// KE STEP BERIKUTNYA
// =========================
function keStepBerikutnya() {
    if (!validasiStepSekarang()) return;
    if (stepSekarang < 3) {
        stepSekarang++;
        perbaruiUI();
    }
}

// =========================
// KE STEP SEBELUMNYA
// =========================
function keStepSebelumnya() {
    if (stepSekarang > 1) {
        stepSekarang--;
        perbaruiUI();
    }
}

// =========================
// PERBARUI UI STEP
// =========================
function perbaruiUI() {
    for (let i = 1; i <= 3; i++) {
        const step = document.getElementById('step' + i);
        if (i === stepSekarang) {
            step.classList.remove('d-none');
            step.style.display = 'block';
            step.querySelectorAll('input:not([readonly]), select, textarea, button').forEach(el => {
                if (el.tagName === 'BUTTON' && el.closest('.item-row')) {
                    const rows = document.querySelectorAll('.item-row');
                    el.disabled = rows.length <= 1;
                } else if (el.tagName !== 'BUTTON') {
                    el.disabled = false;
                } else if (el.id === 'btnPrevStep' || el.id === 'btnNextStep' || el.id === 'submitBtn') {
                    // skip
                } else {
                    el.disabled = false;
                }
            });
        } else {
            step.classList.add('d-none');
            step.style.display = 'none';
            step.querySelectorAll('input, select, textarea, button').forEach(el => {
                if (el.id === 'btnPrevStep' || el.id === 'btnNextStep' || el.id === 'submitBtn') {
                    return;
                }
                if (el.hasAttribute('readonly')) {
                    return;
                }
                el.disabled = true;
            });
        }
    }

    document.querySelectorAll('.step-indicator').forEach((el, index) => {
        el.classList.toggle('active', index + 1 === stepSekarang);
    });

    const btnPrev = document.getElementById('btnPrevStep');
    const btnNext = document.getElementById('btnNextStep');
    const btnSubmit = document.getElementById('submitBtn');

    if (stepSekarang === 1) {
        btnPrev.style.display = 'none';
        btnNext.style.display = 'inline-block';
        btnSubmit.style.display = 'none';
    } else if (stepSekarang === 2) {
        btnPrev.style.display = 'inline-block';
        btnNext.style.display = 'inline-block';
        btnSubmit.style.display = 'none';
    } else if (stepSekarang === 3) {
        btnPrev.style.display = 'inline-block';
        btnNext.style.display = 'none';
        btnSubmit.style.display = 'inline-block';
    }
}

// =========================
// INIT SAAT LOAD
// =========================
document.addEventListener('DOMContentLoaded', function () {
    stepSekarang = 1;
    perbaruiUI();
    perbaruiTotalItem();

    // Cek toggle barjas & dasar usulan saat load
    toggleBarjasLainnya();
    toggleDasarUsulanLainnya();

    const deleteButtons = document.querySelectorAll('.item-row .btn-outline-danger');
    if (deleteButtons.length <= 1) {
        deleteButtons.forEach(btn => btn.disabled = true);
    }
});

// =========================
// SUBMIT FORM
// =========================
document.getElementById('formPengajuan').addEventListener('submit', function () {
    this.querySelectorAll('input, select, textarea').forEach(el => {
        el.disabled = false;
    });

    const formData = new FormData(this);
    console.log('📤 DATA YANG DIKIRIM:');
    for (let [key, value] of formData.entries()) {
        console.log(key, '=>', value);
    }

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
});
</script>

@endsection