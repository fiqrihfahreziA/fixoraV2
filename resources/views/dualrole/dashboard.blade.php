@extends('layouts.dualrole')

@section('content')
<div class="container">

    <div class="text-center mb-4">
        <h3 class="fw-bold">Pilih Mode</h3>
        <p class="text-muted">
            Anda login sebagai <strong>Atasan</strong>
            @if ($authuser->role2)
                dengan role tambahan <strong>{{ ucfirst($authuser->role2) }}</strong>
            @endif
        </p>
    </div>

    <div class="row justify-content-center g-4">

        {{-- CARD ATASAN (selalu ada) --}}
        <div class="col-md-4">
            <a href="{{ route($prefix . '.atasan') }}" class="text-decoration-none">
                <div class="card role-card shadow-sm border-0 text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-person-workspace role-icon text-primary"></i>
                        <h4 class="mt-3 fw-bold text-dark">Menu Atasan</h4>
                        <p class="text-muted">Persetujuan permintaan barang</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- CARD PENERIMA --}}
        @if ($authuser->role2 === 'penerima')
            <div class="col-md-4">
                <a href="{{ route('multirole1.penerima') }}" class="text-decoration-none">
                    <div class="card role-card shadow-sm border-0 text-center h-100">
                        <div class="card-body">
                            <i class="bi bi-box-seam role-icon text-success"></i>
                            <h4 class="mt-3 fw-bold text-dark">Menu Penerima</h4>
                            <p class="text-muted">Pengelolaan barang masuk</p>
                        </div>
                    </div>
                </a>
            </div>
        @endif

        {{-- CARD PPK --}}
        @if ($authuser->role2 === 'ppk')
            <div class="col-md-4">
                <a href="{{ route('multirole2.ppk') }}" class="text-decoration-none">
                    <div class="card role-card shadow-sm border-0 text-center h-100">
                        <div class="card-body">
                            <i class="bi bi-file-earmark-check role-icon text-warning"></i>
                            <h4 class="mt-3 fw-bold text-dark">Menu PPK</h4>
                            <p class="text-muted">Pejabat Pembuat Komitmen</p>
                        </div>
                    </div>
                </a>
            </div>
        @endif

        {{-- CARD KEUANGAN --}}
        @if ($authuser->role2 === 'keuangan')
            <div class="col-md-4">
                <a href="{{ route('multirole3.keuangan') }}" class="text-decoration-none">
                    <div class="card role-card shadow-sm border-0 text-center h-100">
                        <div class="card-body">
                            <i class="bi bi-cash-coin role-icon text-info"></i>
                            <h4 class="mt-3 fw-bold text-dark">Menu Keuangan</h4>
                            <p class="text-muted">Pengelolaan keuangan</p>
                        </div>
                    </div>
                </a>
            </div>
        @endif

    </div>
</div>
@endsection