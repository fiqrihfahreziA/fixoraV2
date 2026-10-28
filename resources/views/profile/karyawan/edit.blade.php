@extends('layouts.pengadaan.pemohon')

@section('content')

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-circle"></i> Profile Saya</h5>
                </div>
                <div class="card-body">

                    <form action="{{ route('pemohon.profile.karyawan.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">

                            <!-- Nama -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama</label>
                                <input type="text" name="nama" class="form-control"
                                       value="{{ old('nama', $authUser->name ?? ($authUser->karyawan->nama ?? '')) }}" required>
                            </div>

                            <!-- NIP -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">NIP</label>
                                <input type="text" name="nip" class="form-control"
                                       value="{{ old('nip', $authUser->karyawan->nip ?? '') }}" required>
                            </div>

                            <!-- Jabatan (Di-comment sesuai kode Anda) -->
                            {{-- <div class="col-md-6">
                                <label class="form-label fw-semibold">Jabatan</label>
                                <select name="jabatan" class="form-select" required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    @foreach($daftarJabatan as $j)
                                        <option value="{{ $j }}"
                                            {{ old('jabatan', $authUser->karyawan->jabatan ?? '') == $j ? 'selected' : '' }}>
                                            {{ $j }}
                                        </option>
                                    @endforeach
                                </select>
                            </div> --}}

                            <!-- Ruangan (pakai datalist, bisa diketik untuk cari) -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ruangan</label>
                                <input list="list-ruangan"
                                       name="ruangan"
                                       class="form-control"
                                       placeholder="Ketik untuk mencari ruangan..."
                                       value="{{ old('ruangan', $authUser->karyawan->ruangan ?? '') }}"
                                       required>
                                <datalist id="list-ruangan">
                                    @foreach($daftarRuangan as $r)
                                        <option value="{{ $r }}"></option>
                                    @endforeach
                                </datalist>
                            </div>

                            <!-- TTD -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Tanda Tangan</label>

                                {{-- PENGECEKAN YANG BENAR: Cek dari tabel karyawan --}}
                                @if($authUser->karyawan && $authUser->karyawan->ttd)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/ttd/' . $authUser->karyawan->ttd) }}"
                                             alt="TTD"
                                             class="img-thumbnail"
                                             style="max-height:120px">
                                    </div>
                                @else
                                    <p class="text-muted fst-italic mt-2">Belum ada tanda tangan</p>
                                @endif

                                <input type="file" name="ttd" class="form-control mt-2"
                                       accept="image/png,image/jpeg,image/jpg">
                                <small class="text-muted">
                                    Format: JPG / PNG (maks 2MB). Kosongkan jika tidak ingin mengganti.
                                </small>
                            </div>

                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                💾 Simpan Perubahan
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">Batal</a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

@endsection