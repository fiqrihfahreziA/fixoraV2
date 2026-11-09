<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Keuangan' }} | FixoraV2</title>
    <link rel="icon" href="{{ asset('gambar/simrs.png') }}" type="image/png">

    <!-- Font (sama gaya dengan halaman login: rapat & tegas) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* =========================================================
           TOKEN — diambil dari halaman login
           ========================================================= */
        :root {
            --bg-deep:      #0e5a54;   /* dasar bawah (hijau teal sedang) */
            --bg-panel:     #147a70;   /* dasar atas (hijau teal sedang) */
            --glow:         #2bb5a5;   /* cahaya teal di sudut */
            --teal:         #0f766e;   /* tombol "Masuk" */
            --teal-bright:  #14b8a6;
            --mint:         #d9efec;   /* kotak info login */
            --paper:        #f7f7f5;   /* panel kanan login */
            --ink:          #0f2a2e;

            --st-green:  #4ade80;      /* Disetujui */
            --st-amber:  #f59e0b;      /* Proses    */
            --st-blue:   #7dd3fc;      /* Diajukan  */
            --st-violet: #a78bfa;      /* Selesai   */

            --line: rgba(255,255,255,.16);
            --line-soft: rgba(255,255,255,.08);
            --glass: rgba(255,255,255,.05);
        }

        * { box-sizing: border-box; }

        html, body { height: 100%; margin: 0; }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, 'Segoe UI', sans-serif;
            letter-spacing: -0.01em;
            color: var(--ink);
            background: var(--bg-panel);
            position: relative;
            overflow-x: hidden;
        }

        /* =========================================================
           BACKGROUND ANIMASI
           Aurora teal yang bergerak pelan + grid halus +
           titik status (hijau/kuning/biru/ungu) naik perlahan
           ========================================================= */
        .bg-stage {
            position: fixed;
            inset: 0;
            z-index: -1;
            overflow: hidden;
            background:
                radial-gradient(120% 90% at 85% -10%, rgba(94,234,212,.38) 0%, transparent 55%),
                linear-gradient(180deg, var(--bg-panel) 0%, var(--bg-deep) 100%);
        }

        /* Aurora blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: .55;
            will-change: transform;
        }
        .blob-1 {
            width: 560px; height: 560px;
            top: -180px; right: -120px;
            background: radial-gradient(circle, var(--glow) 0%, transparent 70%);
            animation: drift-a 22s ease-in-out infinite;
        }
        .blob-2 {
            width: 460px; height: 460px;
            bottom: -160px; left: -100px;
            background: radial-gradient(circle, rgba(94,234,212,.40) 0%, transparent 70%);
            animation: drift-b 28s ease-in-out infinite;
        }
        .blob-3 {
            width: 380px; height: 380px;
            top: 38%; left: 42%;
            background: radial-gradient(circle, rgba(167,139,250,.22) 0%, transparent 70%);
            animation: drift-c 34s ease-in-out infinite;
        }

        @keyframes drift-a {
            0%,100% { transform: translate(0,0) scale(1); }
            33%     { transform: translate(-90px, 70px) scale(1.12); }
            66%     { transform: translate(40px, 130px) scale(.94); }
        }
        @keyframes drift-b {
            0%,100% { transform: translate(0,0) scale(1); }
            40%     { transform: translate(120px,-80px) scale(1.15); }
            75%     { transform: translate(60px, 40px) scale(.92); }
        }
        @keyframes drift-c {
            0%,100% { transform: translate(0,0) scale(1); }
            50%     { transform: translate(-160px,-60px) scale(1.2); }
        }

        /* Grid halus, memudar di tepi */
        .bg-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(var(--line-soft) 1px, transparent 1px),
                linear-gradient(90deg, var(--line-soft) 1px, transparent 1px);
            background-size: 56px 56px;
            -webkit-mask-image: radial-gradient(ellipse 70% 60% at 50% 35%, #000 20%, transparent 75%);
                    mask-image: radial-gradient(ellipse 70% 60% at 50% 35%, #000 20%, transparent 75%);
            animation: grid-shift 40s linear infinite;
        }
        @keyframes grid-shift {
            to { background-position: 56px 56px, 56px 56px; }
        }

        /* Titik status naik pelan */
        .status-dot {
            position: absolute;
            bottom: -20px;
            left: var(--x);
            width: var(--s);
            height: var(--s);
            border-radius: 50%;
            background: var(--c);
            box-shadow: 0 0 14px 2px var(--c);
            opacity: 0;
            animation: rise var(--d) linear var(--delay) infinite;
        }
        @keyframes rise {
            0%   { transform: translateY(0) translateX(0); opacity: 0; }
            10%  { opacity: .75; }
            50%  { transform: translateY(-55vh) translateX(18px); }
            90%  { opacity: .6; }
            100% { transform: translateY(-115vh) translateX(-14px); opacity: 0; }
        }

        /* =========================================================
           NAVBAR
           ========================================================= */
        .navbar-glass {
            position: sticky;
            top: 0;
            z-index: 1050;
            padding: 12px 0;
            background: rgba(14,90,84,.62) !important;
            backdrop-filter: blur(18px) saturate(160%);
            -webkit-backdrop-filter: blur(18px) saturate(160%);
            border-bottom: 1px solid var(--line);
            flex-shrink: 0;
            animation: nav-in .6s cubic-bezier(.2,.7,.2,1) both;
        }
        @keyframes nav-in {
            from { opacity: 0; transform: translateY(-14px); }
            to   { opacity: 1; transform: none; }
        }

        .navbar-glass .navbar-brand { color: #fff !important; }

        .brand-icon {
            width: 44px; height: 44px;
            background: #fff;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 6px 20px rgba(0,0,0,.25);
            transition: transform .3s ease;
        }
        .brand-icon:hover { transform: rotate(-6deg) scale(1.05); }
        .brand-icon img { width: 30px; height: 30px; object-fit: contain; }

        .app-title-main {
            font-size: 1.05rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #fff;
            line-height: 1.15;
        }
        .app-subtitle-main {
            font-size: .72rem;
            font-weight: 500;
            color: rgba(255,255,255,.6);
            letter-spacing: -0.01em;
        }

        .navbar-glass .nav-link {
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px !important;
            border-radius: 10px;
            font-weight: 600;
            font-size: .9rem;
            color: rgba(255,255,255,.72) !important;
            transition: background .2s ease, color .2s ease;
        }
        .navbar-glass .nav-link:hover {
            color: #fff !important;
            background: rgba(255,255,255,.08);
        }
        /* Menu aktif: pill gelap + titik status hijau (seperti kartu di login) */
        .navbar-glass .nav-link.active {
            color: #fff !important;
            background: rgba(255,255,255,.09);
            box-shadow: inset 0 0 0 1px var(--line);
        }
        .navbar-glass .nav-link.active::before {
            content: '';
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--st-green);
            box-shadow: 0 0 8px var(--st-green);
        }

        .navbar-toggler { border: 0 !important; }
        .navbar-toggler:focus { box-shadow: 0 0 0 3px rgba(20,184,166,.35); }
        .navbar-toggler-icon { filter: invert(1) brightness(2); }

        /* ---------- User toggle ---------- */
        .navbar-glass .user-dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 12px 5px 5px !important;
            border-radius: 999px !important;
            background: var(--glass);
            box-shadow: inset 0 0 0 1px var(--line);
            cursor: pointer;
            transition: background .2s ease;
        }
        .user-dropdown-toggle:hover { background: rgba(255,255,255,.10); }
        .user-dropdown-toggle::after { display: none; }

        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-weight: 700;
            font-size: .85rem;
            color: #fff;
            background: linear-gradient(135deg, var(--teal) 0%, var(--teal-bright) 100%);
            box-shadow: 0 0 0 2px rgba(255,255,255,.15);
        }
        .user-name-display { color: #fff; font-weight: 600; font-size: .88rem; }
        .user-caret { color: rgba(255,255,255,.55); font-size: .7rem; transition: transform .25s ease; }
        .user-dropdown-toggle.show .user-caret { transform: rotate(180deg); }

        /* ---------- Dropdown (panel terang seperti form login) ---------- */
        .dropdown-glass {
            min-width: 230px;
            margin-top: 12px !important;
            padding: 8px;
            background: var(--paper) !important;
            border: 1px solid rgba(15,42,46,.08);
            border-radius: 14px;
            box-shadow: 0 24px 60px rgba(0,0,0,.35);
            animation: dd-in .22s cubic-bezier(.2,.7,.2,1);
        }
        @keyframes dd-in {
            from { opacity: 0; transform: translateY(-8px) scale(.97); }
            to   { opacity: 1; transform: none; }
        }
        .dropdown-glass .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 10px;
            font-weight: 600;
            font-size: .9rem;
            color: var(--ink) !important;
            transition: background .15s ease, color .15s ease;
        }
        .dropdown-glass .dropdown-item i { font-size: 1.05rem; width: 20px; text-align: center; color: var(--teal); }
        .dropdown-glass .dropdown-item:hover { background: var(--mint) !important; }
        .dropdown-glass .dropdown-divider { margin: 6px 4px; border-color: rgba(15,42,46,.10); }

        .dropdown-glass .dropdown-item.text-danger { color: #b42318 !important; }
        .dropdown-glass .dropdown-item.text-danger i { color: #b42318; }
        .dropdown-glass .dropdown-item.text-danger:hover { background: #fde8e6 !important; }

        /* =========================================================
           KONTEN
           ========================================================= */
        .content-wrapper {
            flex: 1 0 auto;
            width: 100%;
            padding: 28px 0;
        }

        /* Satu-satunya momen masuk: kartu naik dari bawah */
        .content-card-glass {
            position: relative;
            min-height: 400px;
            padding: 32px;
            background: var(--paper);
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,.35);
            box-shadow:
                0 0 0 1px rgba(0,0,0,.04),
                0 30px 80px rgba(0,0,0,.35);
            animation: card-in .7s cubic-bezier(.2,.7,.2,1) .12s both;
            overflow: hidden;
        }
        /* Garis aksen tipis di atas kartu, bergerak pelan */
        .content-card-glass::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg,
                var(--teal) 0%, var(--teal-bright) 30%,
                var(--st-green) 55%, var(--teal-bright) 80%, var(--teal) 100%);
            background-size: 200% 100%;
            animation: accent-slide 8s linear infinite;
        }
        @keyframes accent-slide { to { background-position: -200% 0; } }
        @keyframes card-in {
            from { opacity: 0; transform: translateY(26px); }
            to   { opacity: 1; transform: none; }
        }

        /* Penyelarasan komponen Bootstrap di dalam konten dengan tema teal */
        .content-card-glass .btn-primary {
            --bs-btn-bg: var(--teal);
            --bs-btn-border-color: var(--teal);
            --bs-btn-hover-bg: #0d645d;
            --bs-btn-hover-border-color: #0d645d;
            --bs-btn-active-bg: #0b5650;
            --bs-btn-active-border-color: #0b5650;
            --bs-btn-focus-shadow-rgb: 20,184,166;
            font-weight: 600;
        }
        .content-card-glass .btn-outline-primary {
            --bs-btn-color: var(--teal);
            --bs-btn-border-color: var(--teal);
            --bs-btn-hover-bg: var(--teal);
            --bs-btn-hover-border-color: var(--teal);
            --bs-btn-active-bg: var(--teal);
            --bs-btn-active-border-color: var(--teal);
        }
        .content-card-glass .form-control:focus,
        .content-card-glass .form-select:focus {
            border-color: var(--teal);
            box-shadow: 0 0 0 .22rem rgba(20,184,166,.20);
        }
        .content-card-glass .form-check-input:checked {
            background-color: var(--teal);
            border-color: var(--teal);
        }
        .content-card-glass a:not(.btn):not(.dropdown-item):not(.page-link) { color: var(--teal); }
        .content-card-glass .page-link { color: var(--teal); }
        .content-card-glass .page-item.active .page-link {
            background-color: var(--teal);
            border-color: var(--teal);
            color: #fff;
        }
        .content-card-glass .table > thead { --bs-table-bg: var(--mint); color: var(--ink); }

        /* =========================================================
           FOOTER
           ========================================================= */
        .footer-glass {
            flex-shrink: 0;
            margin-top: auto;
            width: 100%;
            padding: 18px 0;
            background: rgba(14,90,84,.55) !important;
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-top: 1px solid var(--line);
            color: rgba(255,255,255,.6) !important;
            font-size: .85rem;
            font-weight: 500;
        }
        .footer-glass a { color: rgba(255,255,255,.7); text-decoration: none; }
        .footer-glass a:hover { color: #fff; }

        /* =========================================================
           RESPONSIVE
           ========================================================= */
        @media (max-width: 991.98px) {
            .navbar-glass .navbar-collapse {
                margin-top: 12px;
                padding-top: 12px;
                border-top: 1px solid var(--line);
            }
            .navbar-glass .user-dropdown-toggle { display: inline-flex; margin-top: 8px; }
        }

        @media (max-width: 768px) {
            .content-wrapper { padding: 14px 0; }
            .content-card-glass { padding: 20px; border-radius: 16px; min-height: 300px; }
            .brand-icon { width: 40px; height: 40px; }
            .brand-icon img { width: 26px; height: 26px; }
            .user-name-display { display: none; }
            .blob { filter: blur(60px); }
        }

        @media (max-width: 576px) {
            .app-title-main { font-size: .92rem; }
            .app-subtitle-main { font-size: .65rem; }
            .content-card-glass { padding: 16px; border-radius: 14px; }

            .dropdown-glass {
                position: fixed !important;
                left: 16px !important;
                right: 16px !important;
                top: auto !important;
                bottom: 16px !important;
                transform: none !important;
                min-width: auto !important;
                width: auto !important;
                margin: 0 !important;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--bg-deep); }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,.22); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,.38); }

        /* Fokus keyboard terlihat jelas */
        .navbar-glass .nav-link:focus-visible,
        .navbar-brand:focus-visible,
        .dropdown-item:focus-visible {
            outline: 2px solid var(--teal-bright);
            outline-offset: 2px;
        }

        /* Hormati pengguna yang mematikan animasi */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .001ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .001ms !important;
            }
            .status-dot { display: none; }
        }
    </style>
</head>
<body>

<!-- ===== BACKGROUND ANIMASI ===== -->
<div class="bg-stage" aria-hidden="true">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
    <div class="bg-grid"></div>

    <span class="status-dot" style="--x:8%;  --s:6px; --c:var(--st-green);  --d:26s; --delay:0s"></span>
    <span class="status-dot" style="--x:22%; --s:8px; --c:var(--st-amber);  --d:32s; --delay:-9s"></span>
    <span class="status-dot" style="--x:37%; --s:5px; --c:var(--st-blue);   --d:28s; --delay:-17s"></span>
    <span class="status-dot" style="--x:51%; --s:7px; --c:var(--st-violet); --d:36s; --delay:-4s"></span>
    <span class="status-dot" style="--x:66%; --s:6px; --c:var(--st-green);  --d:30s; --delay:-21s"></span>
    <span class="status-dot" style="--x:79%; --s:8px; --c:var(--st-blue);   --d:34s; --delay:-12s"></span>
    <span class="status-dot" style="--x:91%; --s:5px; --c:var(--st-amber);  --d:27s; --delay:-25s"></span>
</div>

<!-- ===== NAVBAR ===== -->
<nav class="navbar navbar-expand-lg navbar-glass">
    <div class="container">
        @php
            $user = Auth::user();
            // Cek apakah user punya role tambahan (role2) dan role utamanya atasan
            $isMultirole = ($user->role === 'atasan' && $user->role2);

            // Kalau multirole, arahkan ke dashboard multirole.
            $homeUrl = $isMultirole ? route('multirole.dashboard') : '#';
        @endphp

        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center gap-3" href="{{ $homeUrl }}">
            <div class="brand-icon">
                <img src="{{ asset('gambar/simrs.png') }}" alt="Logo RSMZ">
            </div>
            <div>
                <div class="app-title-main d-flex align-items-center gap-2">
                    <span>Aplikasi Permintaan</span>
                    <i class="bi bi-box-seam" style="color: var(--teal-bright); font-size:.95rem;"></i>
                </div>
                <div class="app-subtitle-main d-flex align-items-center gap-2">
                    <span>dan Perbaikan Barang</span>
                    <i class="bi bi-tools" style="color: var(--st-amber);"></i>
                </div>
            </div>
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarPemohon"
                aria-controls="navbarPemohon" aria-expanded="false" aria-label="Buka menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarPemohon">
            <!-- Navigation -->
            <ul class="navbar-nav ms-lg-4 me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('keuangan.pengadaan*') ? 'active' : '' }}"
                       href="{{ route('keuangan.pengadaan') }}">
                        <i class="bi bi-box"></i>
                        Pengadaan Barang
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('keuangan.reportPengadaan') ? 'active' : '' }}"
                       href="{{ route('keuangan.reportPengadaan') }}">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                        Laporan
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('pemohon.pengadaan.create') ? 'active' : '' }}"
                       href="{{ route('pemohon.pengadaan.create') }}">
                        <i class="bi bi-plus-circle"></i>
                        Buat Pengajuan
                    </a>
                </li> --}}
            </ul>

            <!-- User Dropdown -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link user-dropdown-toggle"
                       href="#"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                        <div class="user-avatar">
                            {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <span class="user-name-display">{{ Auth::user()->name }}</span>
                        <i class="bi bi-chevron-down user-caret"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-glass dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('keuangan.profile.karyawan.edit') }}">
                                <i class="bi bi-person-circle"></i> Profil saya
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-block">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- ===== CONTENT WRAPPER ===== -->
<div class="content-wrapper">
    <div class="container">
        <div class="content-card-glass">
            @yield('content')
        </div>
    </div>
</div>

<!-- ===== FOOTER ===== -->
<footer class="footer-glass">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('gambar/simrs.png') }}" alt="RSMZ" style="height: 22px; width: 22px; object-fit: contain;">
                <span>© SIMRS RSMZ 2026</span>
            </div>
            <span>FixoraV2</span>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Tutup menu navbar di mobile setelah klik link (kecuali tombol dropdown user)
    const navbarCollapse = document.getElementById('navbarPemohon');
    document.querySelectorAll('.navbar-nav .nav-link:not([data-bs-toggle])').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 992) {
                bootstrap.Collapse.getInstance(navbarCollapse)?.hide();
            }
        });
    });
});
</script>

</body>
</html>