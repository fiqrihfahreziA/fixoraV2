<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Pemohon' }} | Aplikasi Permintaan & Perbaikan Barang</title>
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
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --card-shadow: 0 20px 60px rgba(0,0,0,0.1);
            --glass-bg: rgba(255,255,255,0.15);
            --glass-border: rgba(255,255,255,0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            background: var(--primary-gradient);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(102, 126, 234, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(118, 75, 162, 0.3) 0%, transparent 50%);
            z-index: -1;
        }

        /* Floating Particles */
        .particle {
            position: fixed;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            animation: float 20s infinite ease-in-out;
            z-index: -1;
        }

        .particle:nth-child(1) { width: 300px; height: 300px; top: -150px; right: -100px; animation-delay: 0s; }
        .particle:nth-child(2) { width: 200px; height: 200px; bottom: -100px; left: -50px; animation-delay: -5s; }
        .particle:nth-child(3) { width: 150px; height: 150px; top: 50%; left: 50%; transform: translate(-50%, -50%); animation-delay: -10s; }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(30px, -30px) scale(1.1); }
            50% { transform: translate(-20px, 20px) scale(0.9); }
            75% { transform: translate(20px, 30px) scale(1.05); }
        }

        /* Glassmorphism Navbar */
        .navbar-glass {
            background: var(--glass-bg) !important;
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid var(--glass-border);
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            padding: 12px 0;
            position: relative;
            z-index: 1050;
        }

        .navbar-glass .navbar-brand {
            color: white !important;
            text-shadow: 0 2px 20px rgba(0,0,0,0.2);
        }

        .navbar-glass .nav-link {
            color: rgba(255,255,255,0.85) !important;
            transition: all 0.3s ease;
            position: relative;
            padding: 8px 16px !important;
            border-radius: 8px;
        }

        .navbar-glass .nav-link:hover {
            color: white !important;
            background: rgba(255,255,255,0.15);
            transform: translateY(-2px);
        }

        .navbar-glass .nav-link.active {
            color: white !important;
            background: rgba(255,255,255,0.2);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .navbar-glass .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 4px;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 2px;
            background: white;
            border-radius: 2px;
        }

        /* Brand Animation */
        .brand-icon {
            width: 48px;
            height: 48px;
            background: white;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            transition: transform 0.3s ease;
            animation: pulse-icon 3s infinite ease-in-out;
        }

        .brand-icon:hover {
            transform: rotate(10deg) scale(1.05);
        }

        @keyframes pulse-icon {
            0%, 100% { box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
            50% { box-shadow: 0 8px 40px rgba(102, 126, 234, 0.4); }
        }

        .brand-icon img {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }

        .app-title-main {
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            background: linear-gradient(to right, #fff, rgba(255,255,255,0.8));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .app-subtitle-main {
            font-size: 0.75rem;
            opacity: 0.9;
            color: rgba(255,255,255,0.8);
        }

        /* ===== DROPDOWN YANG DIPERBAIKI ===== */
        .dropdown-glass {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
            padding: 8px;
            min-width: 220px;
            margin-top: 12px !important;
            animation: dropdownSlide 0.3s ease-out;
        }

        @keyframes dropdownSlide {
            from {
                opacity: 0;
                transform: translateY(-10px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .dropdown-glass .dropdown-item {
            border-radius: 10px;
            padding: 12px 16px;
            transition: all 0.2s ease;
            font-weight: 500;
            color: #1a1a2e !important;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .dropdown-glass .dropdown-item i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .dropdown-glass .dropdown-item:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            transform: translateX(5px);
        }

        .dropdown-glass .dropdown-item:hover i {
            color: white !important;
        }

        .dropdown-glass .dropdown-divider {
            margin: 6px 0;
            border-color: rgba(0,0,0,0.08);
        }

        /* Logout button khusus */
        .dropdown-glass .dropdown-item.text-danger {
            color: #dc3545 !important;
        }

        .dropdown-glass .dropdown-item.text-danger:hover {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
            color: white !important;
        }

        .dropdown-glass .dropdown-item.text-danger:hover i {
            color: white !important;
        }

        /* User Avatar & Toggle */
        .user-dropdown-toggle {
            cursor: pointer;
            padding: 6px 12px 6px 6px !important;
            border-radius: 50px !important;
            background: rgba(255,255,255,0.1);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-dropdown-toggle:hover {
            background: rgba(255,255,255,0.2);
            transform: scale(1.02);
        }

        .user-dropdown-toggle::after {
            display: none; /* Hilangkan default caret */
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);
            flex-shrink: 0;
        }

        .user-name-display {
            color: white;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .user-caret {
            color: rgba(255,255,255,0.6);
            font-size: 0.7rem;
            transition: transform 0.3s ease;
        }

        .user-dropdown-toggle.show .user-caret {
            transform: rotate(180deg);
        }

        /* Content Card */
        .content-card-glass {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 32px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(255,255,255,0.3);
            animation: slideUp 0.6s ease-out;
            min-height: 500px;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Footer */
        .footer-glass {
            background: var(--glass-bg) !important;
            backdrop-filter: blur(20px);
            border-top: 1px solid var(--glass-border);
            color: rgba(255,255,255,0.8) !important;
            padding: 20px 0;
            margin-top: 40px;
        }

        .footer-glass a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-glass a:hover {
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .content-card-glass {
                padding: 20px;
                border-radius: 16px;
            }
            
            .brand-icon {
                width: 40px;
                height: 40px;
            }
            
            .brand-icon img {
                width: 26px;
                height: 26px;
            }

            .user-name-display {
                display: none;
            }

            .dropdown-glass {
                min-width: 200px;
                margin-top: 8px !important;
            }
        }

        @media (max-width: 576px) {
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
                border-radius: 16px;
            }
        }
    </style>
</head>
<body>

<!-- Particles -->
<div class="particle"></div>
<div class="particle"></div>
<div class="particle"></div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-glass">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center gap-3" href="#">
            <div class="brand-icon animate__animated animate__fadeInDown">
                <img src="{{ asset('gambar/rsmz.png') }}" alt="Logo RSMZ">
            </div>
            <div>
                <div class="app-title-main d-flex align-items-center gap-2">
                    <span>Aplikasi Permintaan</span>
                    <i class="bi bi-box-seam" style="-webkit-text-fill-color: initial; color: rgba(255,255,255,0.8);"></i>
                </div>
                <div class="app-subtitle-main d-flex align-items-center gap-2">
                    <span>dan Perbaikan Barang</span>
                    <i class="bi bi-tools" style="color: #fbbf24;"></i>
                </div>
            </div>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPemohon">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarPemohon">
            <!-- Navigation -->
            <ul class="navbar-nav ms-4 me-auto">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->is('pemohon/permintaan*') ? 'active' : '' }}"
                       href="#">
                        <i class="bi bi-box"></i>
                        Permintaan Barang
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->is('pemohon/dokumen*') ? 'active' : '' }}"
                       href="#">
                       
                       
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2" href="#">
                    
                      
                    </a>
                </li>
            </ul>

            <!-- ===== USER DROPDOWN YANG DIPERBAIKI ===== -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link user-dropdown-toggle d-flex align-items-center gap-2" 
                       href="#" 
                       data-bs-toggle="dropdown" 
                       aria-expanded="false">
                        <div class="user-avatar">
                            {{ substr(Auth::user()->name, 0, 2) }}
                        </div>
                        <span class="user-name-display">{{ Auth::user()->name }}</span>
                        <i class="bi bi-chevron-down user-caret"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-glass dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-person-circle"></i>
                                Profile Saya
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-gear"></i>
                                Pengaturan
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-bell"></i>
                                Notifikasi
                                <span class="badge bg-danger rounded-pill ms-auto">3</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-block">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>Logout</span>
                                    <i class="bi bi-arrow-right ms-auto" style="font-size: 0.7rem;"></i>
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Content -->
<div class="container my-4">
    <div class="content-card-glass">
        @yield('content')
    </div>
</div>

<!-- Footer -->
<footer class="footer-glass">
    <div class="container text-center">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <span>© 2026 RSMZ - All Rights Reserved</span>
            <div class="d-flex gap-3 mt-2 mt-md-0">
                <a href="#"><i class="bi bi-twitter"></i></a>
                <a href="#"><i class="bi bi-facebook"></i></a>
                <a href="#"><i class="bi bi-instagram"></i></a>
                <a href="#"><i class="bi bi-youtube"></i></a>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Optional: Close dropdown on outside click -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.querySelector('.dropdown');
        if (dropdown && !dropdown.contains(e.target)) {
            const dropdownMenu = dropdown.querySelector('.dropdown-menu');
            if (dropdownMenu && dropdownMenu.classList.contains('show')) {
                bootstrap.Dropdown.getInstance(dropdown.querySelector('.dropdown-toggle'))?.hide();
            }
        }
    });
});
</script>

</body>
</html>