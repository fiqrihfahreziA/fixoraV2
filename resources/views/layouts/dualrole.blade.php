<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Multirole' }} | Aplikasi Permintaan & Perbaikan Barang</title>
    <link rel="icon" href="{{ asset('gambar/rsmz.png') }}" type="image/png">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --brand-primary: #1e40af;
            --brand-primary-2: #3b82f6;
            --brand-accent: #f59e0b;
        }

        body {
            background: #f4f6f9;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ===== NAVBAR ===== */
        .navbar {
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-primary-2) 100%);
            box-shadow: 0 4px 16px rgba(30, 64, 175, .25);
            padding: .75rem 0;
        }
        .navbar-brand {
            text-decoration: none;
        }
        .navbar-brand img {
            transition: transform .3s ease;
        }
        .navbar-brand:hover img {
            transform: rotate(-8deg) scale(1.08);
        }
        .app-title {
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: .2px;
        }
        .app-subtitle {
            font-size: .75rem;
            opacity: .85;
        }

        /* ===== ROLE BADGE DI NAVBAR ===== */
        .role-badge {
            font-size: .7rem;
            padding: .2rem .55rem;
            border-radius: 999px;
            font-weight: 600;
            background: rgba(255,255,255,.2);
            border: 1px solid rgba(255,255,255,.3);
        }

        /* ===== USER DROPDOWN ===== */
        .user-toggle {
            border-radius: 999px;
            padding: .35rem .9rem !important;
            transition: background .2s;
        }
        .user-toggle:hover {
            background: rgba(255,255,255,.12);
        }
        .dropdown-menu {
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: .4rem;
        }
        .dropdown-item {
            border-radius: 8px;
            padding: .5rem .75rem;
            font-size: .9rem;
            transition: background .15s;
        }
        .dropdown-item:hover {
            background: #f1f5f9;
        }
        .dropdown-header {
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #94a3b8;
            padding: .5rem .75rem .25rem;
        }

        /* ===== CONTENT ===== */
        .content-wrapper {
            flex: 1;
            padding: 2rem 0;
        }
        .content-card {
            background: #fff;
            border-radius: 18px;
            padding: 32px;
            box-shadow: 0 10px 30px rgba(0,0,0,.06);
            border: 1px solid #eef1f5;
            animation: fadeInUp .5s ease;
        }

        /* ===== ROLE CARD ===== */
        .role-card {
            border-radius: 18px;
            transition: all .3s cubic-bezier(.4,0,.2,1);
            cursor: pointer;
            border: 2px solid transparent !important;
            background: linear-gradient(180deg, #ffffff 0%, #fafbff 100%);
        }
        .role-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(30, 64, 175, .15) !important;
            border-color: var(--brand-primary-2) !important;
        }
        .role-icon {
            font-size: 3.5rem;
            display: inline-block;
            transition: transform .3s ease;
        }
        .role-card:hover .role-icon {
            transform: scale(1.15) rotate(-5deg);
        }

        /* ===== FOOTER ===== */
        footer {
            background: #fff;
            border-top: 1px solid #e5e7eb;
            margin-top: auto;
        }
        footer .footer-text {
            font-size: .85rem;
            color: #64748b;
        }
        footer .footer-brand {
            font-weight: 600;
            color: var(--brand-primary);
        }

        /* ===== ANIMASI ===== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .content-card { padding: 20px; border-radius: 14px; }
            .app-title { font-size: .9rem; }
            .app-subtitle { font-size: .7rem; }
            .role-icon { font-size: 2.8rem; }
        }
    </style>
</head>
<body>

<!-- ================= NAVBAR ================= -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">

        <!-- LOGO + JUDUL -->
        <a class="navbar-brand d-flex align-items-center gap-3" href="{{ route('multirole.dashboard') }}">
            <img src="{{ asset('gambar/rsmz.png') }}"
                 alt="Logo RSMZ"
                 class="bg-white rounded-circle p-1 animate__animated animate__fadeInDown"
                 style="height:44px;">

            <div>
                <div class="app-title d-flex align-items-center gap-1">
                    Aplikasi Permintaan
                    <i class="bi bi-box-seam text-warning"></i>
                </div>
                <div class="app-subtitle d-flex align-items-center gap-1">
                    dan Perbaikan Barang
                    <i class="bi bi-tools text-warning"></i>
                </div>
            </div>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarMultirole">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMultirole">

            <!-- USER DROPDOWN -->
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle user-toggle d-flex align-items-center gap-2"
                       href="#" data-bs-toggle="dropdown" role="button">
                        <i class="bi bi-person-circle fs-5"></i>
                        <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                        @if (Auth::user()->role2)
                            <span class="role-badge d-none d-md-inline">
                                {{ ucfirst(Auth::user()->role2) }}
                            </span>
                        @endif
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li class="dropdown-header">
                            Login sebagai
                        </li>
                        <li>
                            <span class="dropdown-item-text small text-muted px-3">
                                {{ ucfirst(Auth::user()->role) }}
                                @if (Auth::user()->role2)
                                    + {{ ucfirst(Auth::user()->role2) }}
                                @endif
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>

                        <li>
                            <a href="{{ route('multirole.dashboard') }}"
                               class="dropdown-item d-flex align-items-center gap-2">
                                <i class="bi bi-speedometer2 text-primary"></i>
                                Dashboard Multirole
                            </a>
                        </li>

                        <li><hr class="dropdown-divider"></li>

                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger d-flex align-items-center gap-2">
                                    <i class="bi bi-box-arrow-right"></i>
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>

        </div>
    </div>
</nav>

<!-- ================= CONTENT ================= -->
<div class="content-wrapper">
    <div class="container">
        <div class="content-card">
            @yield('content')
        </div>
    </div>
</div>

<!-- ================= FOOTER ================= -->
<footer class="text-center py-3">
    <div class="footer-text">
        © {{ date('Y') }} <span class="footer-brand">RSMZ</span> — Aplikasi Permintaan & Perbaikan Barang
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>