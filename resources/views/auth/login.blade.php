<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk - FixoraV2</title>
<!-- <link rel="icon" href="{{ asset('gambar/rsmz.png') }}" type="image/png"> -->
 <link rel="icon" href="{{ asset('gambar/rsmz.png') }}" type="image/png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
:root {
    --ink: #0b2b2c;
    --teal: #0f766e;
    --teal-2: #14958a;
    --teal-dark: #0b5c56;
    --mint: #d8f0eb;
    --paper: #f5f8f7;
    --card: #ffffff;
    --text: #12302f;
    --muted: #61797a;
    --line: #d9e3e1;
    --danger: #c62d3b;
    --amber: #f2a93b;

    /* panel kiri: hijau teal sedang (tidak terlalu gelap) */
    --panel-top: #17897d;
    --panel-mid: #0f6f67;
    --panel-bottom: #0c5a54;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

html, body { height: 100%; }

body {
    font-family: 'Plus Jakarta Sans', system-ui, -apple-system, 'Segoe UI', sans-serif;
    color: var(--text);
    background: var(--paper);
    -webkit-font-smoothing: antialiased;
}

.page {
    min-height: 100%;
    display: grid;
    grid-template-columns: 1.05fr 1fr;
}

/* ---------- Panel kiri ---------- */
.brand-panel {
    background: linear-gradient(160deg, var(--panel-top) 0%, var(--panel-mid) 55%, var(--panel-bottom) 100%);
    color: #fff;
    padding: 48px 56px 36px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
}

/* Lapisan background beranimasi */
.fx { position: absolute; inset: 0; overflow: hidden; pointer-events: none; z-index: 0; }

.fx .blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(70px);
    will-change: transform;
}
.fx .b1 {
    width: 520px; height: 520px; right: -160px; top: -160px;
    background: radial-gradient(circle, rgba(94,234,212,.55), transparent 68%);
    animation: drift-a 22s ease-in-out infinite;
}
.fx .b2 {
    width: 440px; height: 440px; left: -140px; bottom: -140px;
    background: radial-gradient(circle, rgba(74,222,128,.38), transparent 68%);
    animation: drift-b 28s ease-in-out infinite;
}
.fx .b3 {
    width: 340px; height: 340px; left: 38%; top: 42%;
    background: radial-gradient(circle, rgba(167,139,250,.25), transparent 68%);
    animation: drift-c 34s ease-in-out infinite;
}
@keyframes drift-a { 0%,100%{transform:translate(0,0) scale(1)} 33%{transform:translate(-80px,60px) scale(1.12)} 66%{transform:translate(30px,110px) scale(.94)} }
@keyframes drift-b { 0%,100%{transform:translate(0,0) scale(1)} 40%{transform:translate(110px,-70px) scale(1.15)} 75%{transform:translate(50px,30px) scale(.92)} }
@keyframes drift-c { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-120px,-50px) scale(1.2)} }

.fx .grid {
    position: absolute; inset: 0;
    background-image:
        linear-gradient(rgba(255,255,255,.07) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,.07) 1px, transparent 1px);
    background-size: 52px 52px;
    -webkit-mask-image: radial-gradient(ellipse 80% 65% at 55% 40%, #000 15%, transparent 75%);
            mask-image: radial-gradient(ellipse 80% 65% at 55% 40%, #000 15%, transparent 75%);
    animation: grid-shift 40s linear infinite;
}
@keyframes grid-shift { to { background-position: 52px 52px, 52px 52px; } }

.fx .sd {
    position: absolute; bottom: -20px; left: var(--x);
    width: var(--s); height: var(--s);
    border-radius: 50%;
    background: var(--c);
    box-shadow: 0 0 14px 2px var(--c);
    opacity: 0;
    animation: rise var(--d) linear var(--delay) infinite;
}
@keyframes rise {
    0%   { transform: translate(0,0); opacity: 0; }
    10%  { opacity: .8; }
    50%  { transform: translate(16px,-55vh); }
    90%  { opacity: .6; }
    100% { transform: translate(-12px,-115vh); opacity: 0; }
}

.brand, .brand-copy, .brand-foot { position: relative; z-index: 1; }

.brand { display: flex; align-items: center; gap: 12px; }
.brand img { width: 44px; height: 44px; object-fit: contain; background: #fff; border-radius: 12px; padding: 5px; box-shadow: 0 8px 22px rgba(0,0,0,.18); }
.brand span { font-weight: 700; font-size: 18px; letter-spacing: -0.01em; }

.brand-copy { max-width: 440px; }
.brand-copy h2 {
    font-size: clamp(30px, 3.2vw, 42px);
    line-height: 1.15;
    font-weight: 800;
    letter-spacing: -0.03em;
}
.brand-copy p { margin-top: 16px; color: rgba(255,255,255,.84); font-size: 15px; line-height: 1.65; }

/* Contoh tiket: menampilkan isi aplikasi */
.tickets { margin-top: 32px; display: grid; gap: 10px; }
.ticket {
    display: flex; align-items: center; gap: 14px;
    background: rgba(255,255,255,.10);
    border: 1px solid rgba(255,255,255,.18);
    border-radius: 14px;
    padding: 14px 16px;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    animation: ticket-in .65s cubic-bezier(.2,.7,.2,1) both;
    animation-delay: calc(.2s + var(--i) * .09s);
}
.ticket .dot { width: 10px; height: 10px; border-radius: 50%; flex: none; box-shadow: 0 0 10px currentColor; }
.ticket .info { flex: 1; min-width: 0; }
.ticket .info b { display: block; font-size: 14px; font-weight: 600; }
.ticket .info small { color: rgba(255,255,255,.72); font-size: 12px; }
.ticket .badge {
    font-size: 12px; font-weight: 600;
    padding: 4px 10px; border-radius: 999px;
    background: rgba(255,255,255,.16);
}
.ticket:nth-child(even) { margin-left: 28px; }
@keyframes ticket-in { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }

/* ---------- Footer panel kiri ---------- */
.brand-foot {
    display: flex; align-items: center; justify-content: space-between; gap: 16px;
    margin-top: 32px;
    padding-top: 20px;
    border-top: 1px solid rgba(255,255,255,.2);
}
.foot-id {
    display: flex; align-items: center; gap: 12px;
    padding: 8px 16px 8px 8px;
    border-radius: 14px;
    background: rgba(255,255,255,.10);
    border: 1px solid rgba(255,255,255,.18);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}
.foot-id img {
    width: 36px; height: 36px; object-fit: contain;
    background: #fff; border-radius: 10px; padding: 4px;
}
.foot-id strong { display: block; font-size: 14px; font-weight: 800; letter-spacing: -0.01em; line-height: 1.2; }
.foot-id small { display: block; margin-top: 2px; font-size: 11.5px; color: rgba(255,255,255,.75); line-height: 1.3; }
.foot-meta { text-align: right; font-size: 12.5px; color: rgba(255,255,255,.78); line-height: 1.5; }
.foot-meta b { display: inline-block; margin-top: 4px; padding: 2px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; letter-spacing: .02em; color: #fff; background: rgba(255,255,255,.16); }

/* ---------- Panel kanan ---------- */
.form-panel {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    padding: 40px 24px;
}

.form-wrap { width: 100%; max-width: 400px; }

.mobile-brand { display: none; align-items: center; gap: 10px; margin-bottom: 28px; }
.mobile-brand img { width: 38px; height: 38px; object-fit: contain; }
.mobile-brand span { font-weight: 800; font-size: 20px; color: var(--ink); }

.form-wrap h1 { font-size: 30px; font-weight: 800; letter-spacing: -0.03em; color: var(--ink); }
.form-wrap .lead { margin: 8px 0 32px; color: var(--muted); font-size: 15px; line-height: 1.5; }

.field { margin-bottom: 20px; }
.field label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; }

.control { position: relative; }
.control > svg.lead-icon {
    position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
    width: 18px; height: 18px; color: var(--muted); pointer-events: none;
    transition: color .2s;
}
.control input {
    width: 100%;
    height: 50px;
    padding: 0 46px 0 44px;
    font: inherit; font-size: 15px; color: var(--text);
    background: var(--card);
    border: 1.5px solid var(--line);
    border-radius: 12px;
    transition: border-color .2s, box-shadow .2s;
}
.control input::placeholder { color: #9bb0ae; }
.control input:hover { border-color: #b9cbc8; }
.control input:focus {
    outline: none;
    border-color: var(--teal);
    box-shadow: 0 0 0 4px rgba(15,118,110,.14);
}
.control:focus-within > svg.lead-icon { color: var(--teal); }

.toggle {
    position: absolute; right: 6px; top: 50%; transform: translateY(-50%);
    width: 38px; height: 38px;
    display: grid; place-items: center;
    background: none; border: none; border-radius: 10px;
    color: var(--muted); cursor: pointer;
}
.toggle:hover { background: var(--paper); color: var(--text); }
.toggle:focus-visible { outline: 2px solid var(--teal); }
.toggle svg { width: 20px; height: 20px; }
.toggle .eye-off { display: none; }
.toggle.on .eye { display: none; }
.toggle.on .eye-off { display: block; }

.input-error, .input-error li {
    color: var(--danger); font-size: 13px; margin-top: 6px; list-style: none;
}

.row { display: flex; align-items: center; justify-content: space-between; margin: 4px 0 26px; }
.check { display: flex; align-items: center; gap: 10px; font-size: 14px; color: var(--muted); cursor: pointer; }
.check input { width: 18px; height: 18px; accent-color: var(--teal); cursor: pointer; }

.btn {
    width: 100%; height: 52px;
    display: flex; align-items: center; justify-content: center; gap: 10px;
    font: inherit; font-size: 16px; font-weight: 700;
    color: #fff; background: linear-gradient(135deg, var(--teal) 0%, var(--teal-2) 100%);
    border: none; border-radius: 12px; cursor: pointer;
    transition: filter .2s, transform .1s, box-shadow .2s;
    box-shadow: 0 8px 20px -8px rgba(15,118,110,.7);
}
.btn:hover { filter: brightness(.94); }
.btn:active { transform: scale(.985); }
.btn:focus-visible { outline: 3px solid var(--mint); outline-offset: 2px; }
.btn:disabled { opacity: .8; cursor: not-allowed; }

.spinner {
    width: 18px; height: 18px; display: none;
    border: 2.5px solid rgba(255,255,255,.4); border-top-color: #fff;
    border-radius: 50%; animation: spin .7s linear infinite;
}
.btn.loading .spinner { display: block; }
@keyframes spin { to { transform: rotate(360deg); } }

.help {
    margin-top: 28px; padding: 14px 16px;
    background: var(--mint); border-radius: 12px;
    font-size: 13px; line-height: 1.5; color: #134e4a;
}

/* Footer panel kanan (muncul saat panel kiri disembunyikan di layar kecil) */
.form-foot {
    display: none;
    align-items: center; justify-content: center; gap: 10px;
    margin-top: 32px;
    font-size: 12.5px; color: var(--muted);
}
.form-foot img { width: 22px; height: 22px; object-fit: contain; }
.form-foot b { color: var(--text); font-weight: 700; }

/* Satu animasi saat halaman dibuka */
.form-wrap { animation: rise-in .6s cubic-bezier(.2,.7,.2,1) both; }
@keyframes rise-in { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: none; } }

@media (prefers-reduced-motion: reduce) {
    .form-wrap, .ticket, .fx .blob, .fx .grid, .spinner { animation: none; }
    .fx .sd { display: none; }
}

/* ---------- Responsif ---------- */
@media (max-width: 920px) {
    .page { grid-template-columns: 1fr; }
    .brand-panel { display: none; }
    .mobile-brand { display: flex; }
    .form-panel { justify-content: flex-start; padding-top: 56px; }
    .form-foot { display: flex; }
}
</style>
</head>
<body>

<div class="page">

    <!-- Panel kiri -->
    <aside class="brand-panel" aria-hidden="true">

        <!-- Background beranimasi -->
        <div class="fx">
            <span class="blob b1"></span>
            <span class="blob b2"></span>
            <span class="blob b3"></span>
            <span class="grid"></span>
            <span class="sd" style="--x:8%;  --s:6px; --c:#4ade80; --d:26s; --delay:0s"></span>
            <span class="sd" style="--x:24%; --s:8px; --c:#f2a93b; --d:32s; --delay:-9s"></span>
            <span class="sd" style="--x:41%; --s:5px; --c:#7dd3fc; --d:28s; --delay:-17s"></span>
            <span class="sd" style="--x:58%; --s:7px; --c:#a78bfa; --d:36s; --delay:-4s"></span>
            <span class="sd" style="--x:73%; --s:6px; --c:#4ade80; --d:30s; --delay:-21s"></span>
            <span class="sd" style="--x:90%; --s:8px; --c:#7dd3fc; --d:34s; --delay:-12s"></span>
        </div>

        <div class="brand">
            <img src="{{ asset('gambar/rsmz.png') }}" alt="">
            <span>FixoraV2</span>
        </div>

        <div class="brand-copy">
            <h2>Pengadaan dan pemeliharaan barang, semua dalam satu aplikasi.</h2>
            <p>Ajukan pengadaan dan pemeliharaan perangkat komputer, alat kesehatan, ATK, dan perlengkapan lainnya, lalu pantau statusnya dari awal sampai selesai.</p>

            <div class="tickets">
                <div class="ticket" style="--i:0">
                    <span class="dot" style="background:#4ade80;color:#4ade80"></span>
                    <div class="info"><b>Pengadaan Laptop</b><small>Perangkat komputer, Unit SIMRS</small></div>
                    <span class="badge">Disetujui</span>
                </div>
                <div class="ticket" style="--i:1">
                    <span class="dot" style="background:var(--amber);color:var(--amber)"></span>
                    <div class="info"><b>Servis Tensimeter Digital</b><small>Alat kesehatan</small></div>
                    <span class="badge">Proses</span>
                </div>
                <div class="ticket" style="--i:2">
                    <span class="dot" style="background:#7dd3fc;color:#7dd3fc"></span>
                    <div class="info"><b>Pengadaan Kertas dan Tinta</b><small>ATK, Administrasi</small></div>
                    <span class="badge">Diajukan</span>
                </div>
                <div class="ticket" style="--i:3">
                    <span class="dot" style="background:#a78bfa;color:#a78bfa"></span>
                    <div class="info"><b>Pemeliharaan Printer</b><small>Perangkat komputer, Farmasi</small></div>
                    <span class="badge">Selesai</span>
                </div>
            </div>
        </div>

        <!-- Footer: identitas SIMRS RSMZ -->
        <div class="brand-foot">
            <div class="foot-id">
                <img src="{{ asset('gambar/simrs.png') }}" alt="">
                <div>
                    <strong>SIMRS RSMZ</strong>
                    <small>Sistem Informasi Manajemen Rumah Sakit</small>
                </div>
            </div>
            <div class="foot-meta">
                © 2026<br>
                <b>FixoraV2</b>
            </div>
        </div>
    </aside>

    <!-- Panel kanan -->
    <main class="form-panel">
        <div class="form-wrap">

            <div class="mobile-brand">
                <img src="{{ asset('gambar/rsmz.png') }}" alt="Logo RSMZ">
                <span>FixoraV2</span>
            </div>

            <h1>Masuk ke akun Anda</h1>
            <p class="lead">Aplikasi Pengadaan dan Pemeliharaan Barang RSMZ.</p>

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="field">
                    <label for="email">Email</label>
                    <div class="control">
                        <svg class="lead-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="m3.5 7 8.5 6 8.5-6"/></svg>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nama@rsmz.go.id" autocomplete="username" required autofocus>
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="input-error" />
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <div class="control">
                        <svg class="lead-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10.5" width="16" height="10" rx="2.5"/><path d="M8 10.5V8a4 4 0 0 1 8 0v2.5"/></svg>
                        <input type="password" id="password" name="password" placeholder="Masukkan password" autocomplete="current-password" required>
                        <button type="button" class="toggle" id="toggleBtn" aria-label="Tampilkan password">
                            <svg class="eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3l18 18"/><path d="M10.6 5.1A10 10 0 0 1 12 5c6.4 0 10 7 10 7a17 17 0 0 1-3.2 4M6.5 6.6A17 17 0 0 0 2 12s3.6 7 10 7a9.8 9.8 0 0 0 4.2-.9"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="input-error" />
                </div>

                <div class="row">
                    <label class="check">
                        <input type="checkbox" name="remember">
                        Ingat saya
                    </label>
                </div>

                <button type="submit" class="btn" id="loginBtn">
                    <span class="spinner" aria-hidden="true"></span>
                    <span id="btnText">Masuk</span>
                </button>
            </form>

            <p class="help">Lupa password atau belum punya akun? Hubungi tim IT RSMZ.</p>
        </div>

        <!-- Footer untuk layar kecil -->
        <div class="form-foot">
            <img src="{{ asset('gambar/simrs.png') }}" alt="">
            <span><b>SIMRS RSMZ</b> © 2026</span>
        </div>
    </main>
</div>

<script>
// Tampilkan / sembunyikan password
const passInput = document.getElementById('password');
const toggleBtn = document.getElementById('toggleBtn');

toggleBtn.addEventListener('click', function () {
    const show = passInput.type === 'password';
    passInput.type = show ? 'text' : 'password';
    toggleBtn.classList.toggle('on', show);
    toggleBtn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
});

// Status loading saat submit
document.getElementById('loginForm').addEventListener('submit', function () {
    const btn = document.getElementById('loginBtn');
    btn.classList.add('loading');
    btn.disabled = true;
    document.getElementById('btnText').textContent = 'Memproses...';
});
</script>

</body>
</html>