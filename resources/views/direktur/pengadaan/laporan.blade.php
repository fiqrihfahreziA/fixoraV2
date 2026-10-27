@extends('layouts.pengadaan.direktur')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Laporan Pengadaan</h1>

    <!-- Form Filter & Preview & Export -->
    <form action="{{ route('direktur.reportPengadaan') }}" method="GET" target="_blank" class="mb-4">
        <div class="row">
            <!-- Filter TAHUN -->
            <div class="col-md-3">
                <label>Tahun</label>
                <select name="tahun" class="form-control">
                    <option value="">Semua</option>
                    @foreach($tahuns as $tahun)
                        <option value="{{ $tahun }}">{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter BULAN -->
            <div class="col-md-3">
                <label>Bulan</label>
                <select name="bulan" class="form-control">
                    <option value="">Semua</option>
                    <option value="1">Januari</option>
                    <option value="2">Februari</option>
                    <option value="3">Maret</option>
                    <option value="4">April</option>
                    <option value="5">Mei</option>
                    <option value="6">Juni</option>
                    <option value="7">Juli</option>
                    <option value="8">Agustus</option>
                    <option value="9">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                </select>
            </div>

            <!-- Filter STATUS -->
            {{-- <div class="col-md-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $key => $status)
                        <option value="{{ $key }}">{{ $status }}</option>
                    @endforeach
                </select>
            </div> --}}

            <!-- Filter BIDANG -->
            {{-- <div class="col-md-3">
                <label>Bidang</label>
                <select name="bidang_id" class="form-control">
                    <option value="">Semua Bidang</option>
                    @foreach($bidangs as $bidang)
                        <option value="{{ $bidang->id }}">
                            {{ $bidang->nama_bidang ?? $bidang->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div> --}}

         <!-- Filter ATASAN (PERUBAHAN: dari Bidang ke Atasan) -->
            <div class="col-md-3">
                <label>Atasan</label>
                <select name="atasan_id" class="form-control">
                    <option value="">Semua Atasan</option>
                    @foreach($atasans as $atasan)
                        <option value="{{ $atasan->id }}">
                            {{ $atasan->nama_karyawan ?? $atasan->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- <!-- Tombol Preview -->
        <button class="btn btn-primary mt-3">
            <i class="fas fa-eye"></i> Preview
        </button> --}}

        <!-- Tombol Export Excel (Pakai formaction, persis seperti contoh) -->
        <button
            formaction="{{ route('direktur.export.csv') }}"
            formmethod="GET"
            class="btn btn-success mt-3 ms-2">
            <i class="fas fa-file-excel"></i> Export Excel
        </button>

    </form>
</div>
@endsection