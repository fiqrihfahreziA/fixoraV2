<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Admin | SIMRS RSMZ</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --ink: #12262b;
            --muted: #5d7177;
            --line: #d9e3e4;
            --surface: #ffffff;
            --page: #f4f8f8;
            --teal-900: #0b3b45;
            --teal-700: #0e6a73;
            --teal-600: #118089;
            --teal-100: #dff0f0;
            --danger: #b42318;
            --danger-bg: #fef3f2;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            color: var(--ink);
            background: var(--page);
            min-height: 100vh;
        }

        .auth { min-height: 100vh; }

        /* ---------- Panel kiri ---------- */
        .brand-panel {
            position: relative;
            overflow: hidden;
            background: var(--teal-900);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3rem 3.5rem;
        }
        .brand-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(60% 50% at 85% 10%, rgba(17,128,137,.55), transparent 70%),
                radial-gradient(50% 40% at 0% 100%, rgba(17,128,137,.35), transparent 70%);
        }
        .brand-panel > * { position: relative; }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: .75rem;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .brand-mark {
            width: 42px; height: 42px;
            border-radius: 12px;
            background: #fff;
            display: grid; place-items: center;
        }
        .brand-mark svg { width: 24px; height: 24px; }

        .brand-copy h1 {
            font-size: clamp(2rem, 3.2vw, 2.9rem);
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -.02em;
            max-width: 15ch;
            margin-bottom: 1rem;
        }
        .brand-copy p {
            color: rgba(255,255,255,.75);
            max-width: 38ch;
            margin: 0;
            line-height: 1.65;
        }

        /* Garis denyut (ECG) sebagai elemen khas */
        .pulse { width: 100%; height: 90px; margin-top: 2.5rem; }
        .pulse path {
            fill: none;
            stroke: #7fd6d6;
            stroke-width: 2.5;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-dasharray: 900;
            stroke-dashoffset: 900;
            animation: draw 2.4s ease-out .3s forwards;
        }
        @keyframes draw { to { stroke-dashoffset: 0; } }

        .brand-foot { font-size: .85rem; color: rgba(255,255,255,.6); }

        /* ---------- Panel form ---------- */
        .form-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
        }
        .form-wrap { width: 100%; max-width: 400px; }

        .mobile-brand {
            display: none;
            align-items: center;
            gap: .6rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: var(--teal-900);
        }
        .mobile-brand .brand-mark { background: var(--teal-900); }

        .form-wrap h2 {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -.02em;
            margin-bottom: .4rem;
        }
        .form-wrap .lead-text {
            color: var(--muted);
            margin-bottom: 2rem;
        }

        .form-label { font-weight: 600; font-size: .9rem; margin-bottom: .4rem; }

        .field { position: relative; }
        .field .icon {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            width: 18px; height: 18px;
            color: var(--muted);
            pointer-events: none;
        }
        .field .form-control {
            height: 52px;
            padding-left: 44px;
            border: 1.5px solid var(--line);
            border-radius: 12px;
            background: var(--surface);
            font-size: .95rem;
            transition: border-color .15s, box-shadow .15s;
        }
        .field .form-control::placeholder { color: #9bacb0; }
        .field .form-control:focus {
            border-color: var(--teal-600);
            box-shadow: 0 0 0 4px rgba(17,128,137,.15);
        }
        .field .form-control.is-invalid {
            border-color: var(--danger);
            background-image: none;
        }
        .field .form-control.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(180,35,24,.15);
        }

        .toggle-pass {
            position: absolute;
            right: 8px; top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            width: 38px; height: 38px;
            border-radius: 10px;
            color: var(--muted);
            display: grid; place-items: center;
        }
        .toggle-pass:hover { background: var(--teal-100); color: var(--teal-700); }
        .toggle-pass:focus-visible { outline: 2px solid var(--teal-600); }
        .toggle-pass svg { width: 18px; height: 18px; }
        #password { padding-right: 52px; }

        .invalid-text {
            color: var(--danger);
            font-size: .85rem;
            margin-top: .4rem;
        }

        .alert-error {
            display: flex;
            gap: .6rem;
            align-items: flex-start;
            background: var(--danger-bg);
            border: 1px solid #fecdca;
            color: var(--danger);
            border-radius: 12px;
            padding: .85rem 1rem;
            font-size: .9rem;
            margin-bottom: 1.25rem;
        }
        .alert-error svg { width: 18px; height: 18px; flex: none; margin-top: 2px; }
        .alert-error ul { margin: 0; padding-left: 1rem; }

        .btn-login {
            height: 52px;
            width: 100%;
            border: 0;
            border-radius: 12px;
            background: var(--teal-700);
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            display: flex; align-items: center; justify-content: center; gap: .6rem;
            transition: background .15s, transform .1s;
        }
        .btn-login:hover { background: var(--teal-900); color: #fff; }
        .btn-login:active { transform: scale(.99); }
        .btn-login:focus-visible { outline: 3px solid rgba(17,128,137,.4); outline-offset: 2px; }
        .btn-login:disabled { opacity: .85; cursor: progress; }

        .spinner {
            width: 18px; height: 18px;
            border: 2.5px solid rgba(255,255,255,.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .7s linear infinite;
            display: none;
        }
        .is-loading .spinner { display: inline-block; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .copy {
            margin-top: 2.5rem;
            text-align: center;
            color: var(--muted);
            font-size: .82rem;
        }

        @media (max-width: 991.98px) {
            .brand-panel { display: none; }
            .mobile-brand { display: flex; }
            .form-panel { align-items: flex-start; padding-top: 4rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            .pulse path { animation: none; stroke-dashoffset: 0; }
            .spinner { animation-duration: 1.6s; }
        }
    </style>
</head>
<body>
<main class="container-fluid auth">
    <div class="row min-vh-100">

        {{-- Panel kiri --}}
        <aside class="col-lg-6 brand-panel">
            <div class="brand-logo">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#0b3b45" stroke-width="2.6" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                </span>
                SIMRS RSMZ
            </div>

            <div class="brand-copy">
                <h1>Halaman Admin.</h1>
                <p>Kelolan akun dan data pegawai</p>

                <svg class="pulse" viewBox="0 0 600 90" preserveAspectRatio="none" aria-hidden="true">
                    <path d="M0 50 H170 L195 50 L215 15 L245 80 L270 30 L290 50 H600"/>
                </svg>
            </div>

            <div class="brand-foot">&copy; {{ date('Y') }} SIMRS RSMZ</div>
        </aside>

        {{-- Panel form --}}
        <section class="col-lg-6 form-panel">
            <div class="form-wrap">

                <div class="mobile-brand">
                    <span class="brand-mark" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.6" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                    </span>
                    SIMRS RSMZ
                </div>

                <h2>Masuk sebagai admin</h2>
                <p class="lead-text">Gunakan email dan kata sandi akun Anda.</p>

                @if ($errors->any())
                    <div class="alert-error" role="alert">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
                    @csrf
                    <input type="hidden" name="login_as" value="admin">

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="field">
                            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                            <input type="email" name="email" id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="admin@example.com"
                                   autocomplete="username" required autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Kata sandi</label>
                        <div class="field">
                            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                            <input type="password" name="password" id="password"
                                   class="form-control"
                                   placeholder="Masukkan kata sandi"
                                   autocomplete="current-password" required>
                            <button type="button" class="toggle-pass" id="togglePass" aria-label="Tampilkan kata sandi" aria-pressed="false">
                                <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-login" id="submitBtn">
                        <span class="spinner" aria-hidden="true"></span>
                        <span id="btnText">Masuk</span>
                    </button>
                </form>

                <p class="copy">&copy; {{ date('Y') }} SIMRS RSMZ</p>
            </div>
        </section>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Tampilkan / sembunyikan kata sandi
    const pass = document.getElementById('password');
    const toggle = document.getElementById('togglePass');
    const eyeOpen = '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>';
    const eyeOff = '<path d="M17.9 17.9A10.4 10.4 0 0 1 12 19C5.5 19 2 12 2 12a18 18 0 0 1 4.1-5.1M9.9 5.2A9.7 9.7 0 0 1 12 5c6.5 0 10 7 10 7a18 18 0 0 1-2.2 3.2M1 1l22 22"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/>';

    toggle.addEventListener('click', () => {
        const show = pass.type === 'password';
        pass.type = show ? 'text' : 'password';
        toggle.setAttribute('aria-pressed', show);
        toggle.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        document.getElementById('eyeIcon').innerHTML = show ? eyeOff : eyeOpen;
    });

    // Status loading saat submit
    document.getElementById('loginForm').addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        btn.classList.add('is-loading');
        btn.disabled = true;
        document.getElementById('btnText').textContent = 'Memproses...';
    });
</script>
</body>
</html>