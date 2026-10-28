<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'direktur' }} | Aplikasi Permintaan & Perbaikan Barang</title>
    <link rel="icon" href="{{ asset('gambar/rsmz.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Animate.css (dipertahankan karena mungkin dipakai halaman lain) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --ink-deep: #17133F;
            --ink: #231F5C;
            --ink-2: #352B7E;
            --accent: #5B4BC4;
            --gold: #D9BF86;
            --gold-deep: #B8975A;
            --line: #E6E4F0;
            --text: #25233A;
            --muted: #6F6D85;
            --bs-body-font-family: 'Plus Jakarta Sans', system-ui, -apple-system, 'Segoe UI', sans-serif;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { height: 100%; }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            font-family: var(--bs-body-font-family);
            color: var(--text);
            background-color: var(--ink);
            background-image:
                radial-gradient(90% 60% at 85% -10%, rgba(139, 92, 246, .35) 0%, rgba(139, 92, 246, 0) 60%),
                linear-gradient(165deg, var(--ink-deep) 0%, var(--ink) 45%, #3B2F86 100%);
            background-attachment: fixed;
            -webkit-font-smoothing: antialiased;
        }

        /* ============ NAVBAR ============ */
        .navbar-glass {
            position: sticky;
            top: 0;
            z-index: 1040;
            flex-shrink: 0;
            padding: 12px 0;
            background: rgba(23, 19, 63, .72) !important;
            backdrop-filter: blur(16px) saturate(140%);
            -webkit-backdrop-filter: blur(16px) saturate(140%);
            border-bottom: 1px solid rgba(255, 255, 255, .08);
        }

        .brand-icon {
            width: 44px; height: 44px;
            background: #fff;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .25);
            flex-shrink: 0;
        }
        .brand-icon img { width: 30px; height: 30px; object-fit: contain; }

        .navbar-glass .navbar-brand { color: #fff !important; }
        .app-title-main {
            font-family: 'Fraunces', Georgia, serif;
            font-size: 1.2rem;
            font-weight: 500;
            letter-spacing: -.005em;
            color: #fff;
            line-height: 1.15;
        }
        .app-title-main i { color: rgba(255, 255, 255, .6); font-size: 1rem; }
        .app-subtitle-main { font-size: .74rem; color: rgba(255, 255, 255, .6); margin-top: 2px; }
        .app-subtitle-main i { color: var(--gold) !important; }

        .navbar-glass .nav-link {
            position: relative;
            color: rgba(255, 255, 255, .72) !important;
            font-weight: 500;
            font-size: .92rem;
            padding: 8px 16px !important;
            border-radius: 10px;
            transition: color .2s ease, background-color .2s ease;
        }
        .navbar-glass .nav-link:hover { color: #fff !important; background: rgba(255, 255, 255, .07); }
        .navbar-glass .nav-link.active { color: #fff !important; background: transparent; }
        .navbar-glass .nav-link.active::after {
            content: '';
            position: absolute;
            left: 16px; right: 16px; bottom: 0;
            height: 2px;
            background: var(--gold);
            border-radius: 2px;
        }
        .navbar-glass .nav-link:focus-visible,
        .navbar-glass .navbar-toggler:focus-visible { outline: 2px solid var(--gold); outline-offset: 2px; }
        .navbar-glass .navbar-toggler-icon { filter: invert(1); }

        /* ============ USER MENU ============ */
        .user-dropdown-toggle {
            cursor: pointer;
            padding: 5px 14px 5px 5px !important;
            border-radius: 999px !important;
            border: 1px solid rgba(255, 255, 255, .14);
            background: rgba(255, 255, 255, .06);
            transition: background-color .2s ease, border-color .2s ease;
        }
        .user-dropdown-toggle:hover { background: rgba(255, 255, 255, .12); border-color: rgba(255, 255, 255, .25); }
        .user-dropdown-toggle::after { display: none; }
        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #EAD6A6 0%, var(--gold-deep) 100%);
            color: var(--ink-deep);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .85rem;
            flex-shrink: 0;
        }
        .user-name-display { color: #fff; font-weight: 500; font-size: .88rem; }
        .user-caret { color: rgba(255, 255, 255, .55); font-size: .7rem; transition: transform .25s ease; }
        .user-dropdown-toggle.show .user-caret { transform: rotate(180deg); }

        .dropdown-glass {
            background: #fff !important;
            border: 1px solid var(--line);
            border-radius: 14px;
            box-shadow: 0 24px 60px -20px rgba(23, 19, 63, .45);
            padding: 8px;
            min-width: 220px;
            margin-top: 12px !important;
        }
        .dropdown-glass .dropdown-item {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 14px;
            border-radius: 10px;
            font-weight: 500; font-size: .9rem;
            color: var(--text) !important;
            transition: background-color .2s ease, color .2s ease;
        }
        .dropdown-glass .dropdown-item i { font-size: 1.05rem; width: 20px; text-align: center; color: var(--muted); }
        .dropdown-glass .dropdown-item:hover { background: #F3F1FC !important; color: var(--ink) !important; }
        .dropdown-glass .dropdown-item:hover i { color: var(--accent) !important; }
        .dropdown-glass .dropdown-divider { margin: 6px 0; border-color: var(--line); }
        .dropdown-glass .dropdown-item.text-danger { color: #A63D40 !important; }
        .dropdown-glass .dropdown-item.text-danger i { color: #A63D40; }
        .dropdown-glass .dropdown-item.text-danger:hover { background: #F8ECEC !important; color: #8E3235 !important; }
        .dropdown-glass .dropdown-item.text-danger:hover i { color: #8E3235 !important; }

        /* ============ KONTEN ============ */
        .content-wrapper { flex: 1 0 auto; width: 100%; padding: 32px 0 48px; }
        .content-card-glass {
            background: #fff;
            border-radius: 22px;
            padding: 40px 44px;
            min-height: 400px;
            box-shadow: 0 40px 90px -40px rgba(8, 6, 32, .7);
            animation: fadeIn .5s ease-out;
            /* Tanpa backdrop-filter / z-index / transform di sini:
               ketiganya membuat stacking context yang menjebak modal di bawah backdrop. */
        }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* ============ FOOTER ============ */
        .footer-glass {
            flex-shrink: 0;
            margin-top: auto;
            width: 100%;
            padding: 22px 0;
            background: transparent !important;
            border-top: 1px solid rgba(255, 255, 255, .1);
            color: rgba(255, 255, 255, .6) !important;
            font-size: .85rem;
        }
        .footer-glass a { color: rgba(255, 255, 255, .6); text-decoration: none; transition: color .2s ease, border-color .2s ease; }
        .footer-glass .social-icons { display: flex; gap: 10px; }
        .footer-glass .social-icons a {
            display: flex; align-items: center; justify-content: center;
            width: 34px; height: 34px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, .15);
            font-size: .85rem;
        }
        .footer-glass .social-icons a:hover { color: var(--gold); border-color: var(--gold); }

        /* ============ RESPONSIF ============ */
        @media (max-width: 991.98px) {
            .navbar-glass .navbar-collapse { padding: 14px 0 6px; }
            .navbar-glass .nav-link.active::after { left: 16px; right: auto; width: 20px; bottom: 2px; }
        }
        @media (max-width: 768px) {
            .content-wrapper { padding: 20px 0 32px; }
            .content-card-glass { padding: 26px 22px; border-radius: 18px; min-height: 300px; }
            .brand-icon { width: 40px; height: 40px; }
            .brand-icon img { width: 27px; height: 27px; }
            .user-name-display { display: none; }
            .user-dropdown-toggle { padding-right: 8px !important; }
            .footer-glass { padding: 16px 0; }
        }
        @media (max-width: 576px) {
            .dropdown-glass {
                position: fixed !important;
                left: 16px !important; right: 16px !important;
                top: auto !important; bottom: 16px !important;
                transform: none !important;
                min-width: auto !important; width: auto !important;
                margin: 0 !important;
            }
            .content-card-glass { padding: 22px 16px; border-radius: 16px; }
            .app-title-main { font-size: 1rem; }
            .app-subtitle-main { font-size: .66rem; }
        }
        @media (prefers-reduced-motion: reduce) {
            .content-card-glass { animation: none; }
            * { transition-duration: .01ms !important; }
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--ink-deep); }
        ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, .25); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, .4); }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-glass">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-3 me-0" href="#">
            <div class="brand-icon">
                <img src="{{ asset('gambar/rsmz.png') }}" alt="Logo RSMZ">
            </div>
            <div>
                <div class="app-title-main d-flex align-items-center gap-2">
                    <span>Aplikasi Permintaan</span>
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="app-subtitle-main d-flex align-items-center gap-2">
                    <span>dan Perbaikan Barang</span>
                    <i class="bi bi-tools"></i>
                </div>
            </div>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPemohon" aria-controls="navbarPemohon" aria-expanded="false" aria-label="Buka menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarPemohon">
            <ul class="navbar-nav ms-lg-4 me-auto">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('direktur.pengadaan') ? 'active' : '' }}" href="{{ route('direktur.pengadaan') }}">
                        <i class="bi bi-box"></i> Pengadaan Barang
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('direktur.reportPengadaan') ? 'active' : '' }}" href="{{ route('direktur.reportPengadaan') }}">
                        <i class="bi bi-graph-up-arrow"></i> <span>Laporan</span>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link user-dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar">{{ strtoupper(mb_substr(Auth::user()->name, 0, 2)) }}</div>
                        <span class="user-name-display">{{ Auth::user()->name }}</span>
                        <i class="bi bi-chevron-down user-caret"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-glass dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('direktur.profile.karyawan.edit') }}">
                                <i class="bi bi-person-circle"></i> Profile Saya
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-block">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- CONTENT WRAPPER -->
<div class="content-wrapper">
    <div class="container">
        <div class="content-card-glass">
            @yield('content')
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer class="footer-glass">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('gambar/rsmz.png') }}" alt="RSMZ" style="height: 22px; width: 22px; object-fit: contain;">
                <span>© 2026 RSMZ. Hak cipta dilindungi.</span>
            </div>
            <div class="social-icons mt-3 mt-md-0">
                <a href="#" aria-label="Twitter"><i class="bi bi-twitter"></i></a>
                <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                <a href="#" aria-label="GitHub"><i class="bi bi-github"></i></a>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Semua modal di dalam konten dipindah ke <body> supaya tidak pernah
    // terjebak di bawah backdrop. Berlaku untuk semua halaman direktur.
    document.querySelectorAll('.content-card-glass .modal').forEach(function (modal) {
        document.body.appendChild(modal);
    });

    // Tutup dropdown saat klik di luar
    document.addEventListener('click', function (e) {
        document.querySelectorAll('.dropdown').forEach(function (dropdown) {
            if (!dropdown.contains(e.target)) {
                var menu = dropdown.querySelector('.dropdown-menu');
                if (menu && menu.classList.contains('show')) {
                    bootstrap.Dropdown.getInstance(dropdown.querySelector('.dropdown-toggle'))?.hide();
                }
            }
        });
    });

    // Tutup menu mobile setelah link diklik
    var navbarCollapse = document.getElementById('navbarPemohon');
    document.querySelectorAll('.navbar-nav .nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth < 992 && !link.classList.contains('user-dropdown-toggle')) {
                bootstrap.Collapse.getInstance(navbarCollapse)?.hide();
            }
        });
    });
});
</script>

</body>
</html>
