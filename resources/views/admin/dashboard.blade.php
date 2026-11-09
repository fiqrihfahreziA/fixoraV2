@extends('layouts.admin')

@push('styles')
<style>
    .hello h1 { font-size: 1.9rem; font-weight: 800; letter-spacing: -.02em; margin: 0 0 .25rem; }
    .hello p { color: var(--muted); margin: 0; }

    .clock-box {
        background: var(--teal-900);
        color: #fff;
        border-radius: 16px;
        padding: .9rem 1.3rem;
        text-align: right;
        min-width: 200px;
    }
    .clock-box .time { font-size: 1.7rem; font-weight: 800; letter-spacing: -.02em; line-height: 1.1; font-variant-numeric: tabular-nums; }
    .clock-box .date { font-size: .85rem; color: rgba(255,255,255,.72); }

    .stat {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.2rem 1.3rem;
        color: inherit;
        text-decoration: none;
        transition: border-color .15s, box-shadow .15s;
    }
    .stat:hover { border-color: var(--teal-600); box-shadow: 0 6px 20px rgba(11,59,69,.08); color: inherit; }
    .stat-icon {
        width: 48px; height: 48px;
        border-radius: 13px;
        background: var(--teal-100);
        color: var(--teal-700);
        display: grid; place-items: center;
        flex: none;
    }
    .stat-icon svg { width: 24px; height: 24px; }
    .stat .num { font-size: 1.6rem; font-weight: 800; line-height: 1.1; letter-spacing: -.02em; }
    .stat .lbl { color: var(--muted); font-size: .88rem; }

    .panel-title { font-weight: 700; font-size: 1.05rem; margin: 0; }

    /* ---------- Kalender ---------- */
    .cal-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
    .cal-nav { display: flex; gap: .35rem; }
    .cal-btn {
        border: 1px solid var(--line);
        background: #fff;
        width: 34px; height: 34px;
        border-radius: 10px;
        display: grid; place-items: center;
        color: var(--ink);
    }
    .cal-btn:hover { background: var(--page); }
    .cal-btn:focus-visible, .cal-day:focus-visible { outline: 2px solid var(--teal-600); outline-offset: 1px; }
    .cal-btn svg { width: 16px; height: 16px; }
    .cal-today-btn { width: auto; padding: 0 .75rem; font-size: .82rem; font-weight: 600; }

    .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }
    .cal-dow { text-align: center; font-size: .78rem; font-weight: 600; color: var(--muted); padding-bottom: .4rem; }
    .cal-dow.sun { color: #b42318; }
    .cal-day {
        position: relative;
        aspect-ratio: 1 / 1;
        border: 0;
        background: transparent;
        border-radius: 12px;
        font-size: .92rem;
        font-weight: 500;
        color: var(--ink);
        display: grid; place-items: center;
    }
    .cal-day:hover { background: var(--page); }
    .cal-day.other { color: #aebcbf; }
    .cal-day.sun { color: #b42318; }
    .cal-day.other.sun { color: #e0a8a3; }
    .cal-day.today { background: var(--teal-100); color: var(--teal-700); font-weight: 700; }
    .cal-day.selected { background: var(--teal-700); color: #fff; font-weight: 700; }
    .cal-day.has::after {
        content: "";
        position: absolute;
        bottom: 6px; left: 50%;
        width: 5px; height: 5px;
        margin-left: -2.5px;
        border-radius: 50%;
        background: var(--teal-600);
    }
    .cal-day.selected.has::after { background: #fff; }

    /* ---------- Agenda ---------- */
    .agenda-date { color: var(--muted); font-size: .88rem; }
    .agenda-list { list-style: none; margin: 0; padding: 0; }
    .agenda-item {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .7rem .85rem;
        border: 1px solid var(--line);
        border-radius: 12px;
        margin-bottom: .5rem;
        font-size: .93rem;
    }
    .agenda-item .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--teal-600); flex: none; }
    .agenda-item .txt { flex: 1; word-break: break-word; }
    .agenda-del {
        border: 0; background: transparent; color: var(--muted);
        width: 30px; height: 30px; border-radius: 8px; display: grid; place-items: center;
    }
    .agenda-del:hover { background: #fef3f2; color: #b42318; }
    .agenda-del svg { width: 16px; height: 16px; }
    .agenda-empty {
        border: 1.5px dashed var(--line);
        border-radius: 12px;
        padding: 1.5rem 1rem;
        text-align: center;
        color: var(--muted);
        font-size: .9rem;
        margin-bottom: .5rem;
    }

    /* ---------- Akses cepat ---------- */
    .quick {
        display: flex;
        align-items: center;
        gap: .8rem;
        padding: .8rem .9rem;
        border: 1px solid var(--line);
        border-radius: 12px;
        color: var(--ink);
        text-decoration: none;
        font-weight: 600;
        font-size: .93rem;
        transition: background .15s, border-color .15s;
    }
    .quick:hover { background: var(--page); border-color: var(--teal-600); color: var(--ink); }
    .quick .stat-icon { width: 38px; height: 38px; border-radius: 10px; }
    .quick .stat-icon svg { width: 19px; height: 19px; }

    /* ---------- Animasi tipis ---------- */
    @keyframes rise { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
    @keyframes grow { from { width: 0; } }
    .reveal { animation: rise .5s cubic-bezier(.2,.7,.2,1) both; animation-delay: var(--d, 0s); }

    .stat .sub { color: var(--muted); font-size: .78rem; margin-top: 2px; }

    /* ---------- Perlu perhatian ---------- */
    .notice-row { display: flex; flex-wrap: wrap; gap: .6rem; }
    .notice {
        display: flex; align-items: center; gap: .65rem;
        padding: .6rem .95rem .6rem .65rem;
        border-radius: 12px;
        background: #fff8e6; border: 1px solid #f5e1a4; color: #7a5200;
        font-size: .9rem; font-weight: 600; text-decoration: none;
        transition: transform .12s, box-shadow .15s;
    }
    .notice:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(122,82,0,.12); color: #7a5200; }
    .notice b {
        background: #f5d27a; border-radius: 999px; min-width: 28px; height: 28px; padding: 0 .45rem;
        display: grid; place-items: center; font-size: .85rem;
    }
    .notice-ok { background: #ecfaf4; border-color: #b7e3d4; color: #0b5d45; }
    .notice-ok:hover { transform: none; box-shadow: none; color: #0b5d45; }

    /* ---------- Grafik batang ---------- */
    .bar-row { margin-bottom: .9rem; }
    .bar-row:last-child { margin-bottom: 0; }
    .bar-top { display: flex; justify-content: space-between; gap: .5rem; font-size: .88rem; margin-bottom: .3rem; }
    .bar-top span:first-child { font-weight: 600; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .bar-top span:last-child { color: var(--muted); font-variant-numeric: tabular-nums; }
    .bar { height: 8px; background: #edf2f2; border-radius: 99px; overflow: hidden; }
    .bar > span {
        display: block; height: 100%; width: var(--w);
        background: var(--teal-600); border-radius: 99px;
        animation: grow .8s cubic-bezier(.2,.7,.2,1) .3s both;
    }

    /* ---------- Donut ---------- */
    .donut-wrap { display: flex; align-items: center; gap: 1.4rem; flex-wrap: wrap; }
    .donut {
        --p: 0;
        position: relative; flex: none;
        width: 130px; height: 130px; border-radius: 50%;
        background: conic-gradient(var(--teal-600) calc(var(--p) * 1%), #e6eded 0);
        display: grid; place-items: center;
    }
    .donut::before { content: ""; position: absolute; inset: 16px; background: #fff; border-radius: 50%; }
    .donut b { position: relative; font-size: 1.4rem; font-weight: 800; letter-spacing: -.02em; }
    .legend { list-style: none; margin: 0; padding: 0; font-size: .9rem; }
    .legend li { display: flex; align-items: center; gap: .55rem; margin-bottom: .55rem; }
    .legend li:last-child { margin-bottom: 0; }
    .legend i { width: 10px; height: 10px; border-radius: 50%; flex: none; }
    .legend strong { margin-left: .2rem; }

    /* ---------- Daftar terbaru ---------- */
    .mini-list { list-style: none; margin: 0; padding: 0; }
    .mini-item { display: flex; align-items: center; gap: .75rem; padding: .65rem 0; border-bottom: 1px solid #edf2f2; }
    .mini-item:last-child { border-bottom: 0; padding-bottom: 0; }
    .mini-item .meta { flex: 1; min-width: 0; }
    .mini-item .nm { font-weight: 600; font-size: .92rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .mini-item .sm { color: var(--muted); font-size: .82rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .avatar-sm {
        width: 38px; height: 38px; border-radius: 50%; flex: none;
        background: var(--teal-100); color: var(--teal-700);
        display: grid; place-items: center; font-weight: 700;
    }
    .chip { display: inline-block; padding: .22rem .65rem; border-radius: 999px; font-size: .78rem; font-weight: 600; background: var(--teal-100); color: var(--teal-700); white-space: nowrap; }
    .link-more { font-size: .85rem; font-weight: 600; text-decoration: none; }
    .link-more:hover { text-decoration: underline; }

    @media (prefers-reduced-motion: reduce) {
        .reveal, .bar > span { animation: none !important; }
    }

    @media (max-width: 575.98px) {
        .clock-box { text-align: left; width: 100%; }
    }
</style>
@endpush

@section('content')
@php $nama = $authUser?->karyawan?->nama ?? 'Admin'; @endphp

{{-- Sapaan --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div class="hello">
        <h1><span id="sapaan">Selamat datang</span>, {{ $nama }}</h1>
        <p>Ringkasan aktivitas dan jadwal Anda hari ini.</p>
    </div>
    <div class="clock-box">
        <div class="time" id="jam">--:--:--</div>
        <div class="date" id="tanggal">&nbsp;</div>
    </div>
</div>

@php
    $perRole     = collect($perRole ?? []);
    $topRuangan  = collect($topRuangan ?? []);
    $maxRole     = max(1, (int) $perRole->max());
    $maxRuang    = max(1, (int) $topRuangan->max());
    $totalUser   = (int) ($jumlahPengguna ?? 0);
    $aktif       = (int) ($penggunaAktif ?? 0);
    $nonaktif    = (int) ($penggunaNonaktif ?? 0);
    $persenAktif = $totalUser ? round($aktif / $totalUser * 100) : 0;

    $perhatian = array_filter([
        ['n' => (int) ($karyawanTanpaAkun ?? 0), 't' => 'karyawan belum punya akun',          'href' => route('admin.pengguna')],
        ['n' => (int) ($karyawanTanpaTtd ?? 0),  't' => 'karyawan belum ada tanda tangan',    'href' => route('admin.karyawan')],
        ['n' => $nonaktif,                       't' => 'akun nonaktif',                      'href' => route('admin.pengguna')],
    ], fn ($p) => $p['n'] > 0);
@endphp

{{-- Statistik --}}
<div class="row g-3 mb-3">
    <div class="col-sm-6 col-xl-3 reveal" style="--d:.05s">
        <a href="{{ route('admin.pengguna') }}" class="card stat h-100">
            <span class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="10" cy="7" r="4"/><path d="M21 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/></svg>
            </span>
            <span>
                <div class="num" data-count="{{ $totalUser }}">{{ $totalUser }}</div>
                <div class="lbl">Total pengguna</div>
                <div class="sub">{{ $aktif }} aktif</div>
            </span>
        </a>
    </div>
    <div class="col-sm-6 col-xl-3 reveal" style="--d:.1s">
        <a href="{{ route('admin.karyawan') }}" class="card stat h-100">
            <span class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
            </span>
            <span>
                <div class="num" data-count="{{ $jumlahKaryawan ?? 0 }}">{{ $jumlahKaryawan ?? 0 }}</div>
                <div class="lbl">Total karyawan</div>
                <div class="sub">di {{ $jumlahRuangan ?? 0 }} ruangan</div>
            </span>
        </a>
    </div>
    <div class="col-sm-6 col-xl-3 reveal" style="--d:.15s">
        <div class="card stat h-100">
            <span class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1m4 0h1M9 13h1m4 0h1M9 17h1m4 0h1"/></svg>
            </span>
            <span>
                <div class="num" data-count="{{ $jumlahBidang ?? 0 }}">{{ $jumlahBidang ?? 0 }}</div>
                <div class="lbl">Total bidang</div>
                <div class="sub">terdaftar</div>
            </span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3 reveal" style="--d:.2s">
        <div class="card stat h-100">
            <span class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            </span>
            <span>
                <div class="num" id="agendaCount">0</div>
                <div class="lbl">Agenda hari ini</div>
                <div class="sub">dari kalender Anda</div>
            </span>
        </div>
    </div>
</div>

{{-- Perlu perhatian --}}
<div class="notice-row mb-4 reveal" style="--d:.25s">
    @forelse ($perhatian as $p)
        <a href="{{ $p['href'] }}" class="notice"><b>{{ $p['n'] }}</b> {{ $p['t'] }}</a>
    @empty
        <div class="notice notice-ok">Semua data karyawan dan akun sudah lengkap.</div>
    @endforelse
</div>

{{-- Grafik ringkas --}}
<div class="row g-3 mb-3">
    <div class="col-lg-4 reveal" style="--d:.3s">
        <div class="card p-4 h-100">
            <h2 class="panel-title mb-3">Pengguna per role</h2>
            @forelse ($perRole as $role => $total)
                <div class="bar-row">
                    <div class="bar-top"><span>{{ ucfirst($role) }}</span><span>{{ $total }}</span></div>
                    <div class="bar"><span style="--w: {{ round($total / $maxRole * 100) }}%"></span></div>
                </div>
            @empty
                <p class="text-muted mb-0">Belum ada data.</p>
            @endforelse
        </div>
    </div>

    <div class="col-lg-4 reveal" style="--d:.36s">
        <div class="card p-4 h-100">
            <h2 class="panel-title mb-3">Karyawan per ruangan</h2>
            @forelse ($topRuangan as $ruang => $total)
                <div class="bar-row">
                    <div class="bar-top"><span title="{{ $ruang }}">{{ $ruang }}</span><span>{{ $total }}</span></div>
                    <div class="bar"><span style="--w: {{ round($total / $maxRuang * 100) }}%"></span></div>
                </div>
            @empty
                <p class="text-muted mb-0">Belum ada data.</p>
            @endforelse
        </div>
    </div>

    <div class="col-lg-4 reveal" style="--d:.42s">
        <div class="card p-4 h-100">
            <h2 class="panel-title mb-3">Status akun</h2>
            <div class="donut-wrap">
                <div class="donut" style="--p: {{ $persenAktif }}"><b>{{ $persenAktif }}%</b></div>
                <ul class="legend">
                    <li><i style="background: var(--teal-600)"></i>Aktif <strong>{{ $aktif }}</strong></li>
                    <li><i style="background: #e6eded"></i>Nonaktif <strong>{{ $nonaktif }}</strong></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Kalender --}}
    <div class="col-lg-5">
        <div class="card p-4 h-100">
            <div class="cal-head">
                <h2 class="panel-title" id="calTitle">Kalender</h2>
                <div class="cal-nav">
                    <button class="cal-btn cal-today-btn" id="calToday" type="button">Hari ini</button>
                    <button class="cal-btn" id="calPrev" type="button" aria-label="Bulan sebelumnya">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    </button>
                    <button class="cal-btn" id="calNext" type="button" aria-label="Bulan berikutnya">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                </div>
            </div>
            <div class="cal-grid" id="calGrid"></div>
        </div>
    </div>

    {{-- Agenda + akses cepat --}}
    <div class="col-lg-7">
        <div class="card p-4 mb-3">
            <div class="d-flex justify-content-between align-items-baseline mb-3">
                <h2 class="panel-title">Agenda</h2>
                <span class="agenda-date" id="agendaDate"></span>
            </div>

            <ul class="agenda-list" id="agendaList"></ul>

            <div class="input-group mt-2">
                <input type="text" class="form-control" id="agendaInput" maxlength="120" placeholder="Tambah agenda untuk tanggal ini" aria-label="Agenda baru">
                <button class="btn btn-primary" id="agendaAdd" type="button">Tambah</button>
            </div>
        </div>

        <div class="card p-4">
            <h2 class="panel-title mb-3">Akses cepat</h2>
            <div class="row g-2">
                <div class="col-sm-6">
                    <a class="quick" href="{{ route('admin.pengguna') }}">
                        <span class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1"/></svg></span>
                        Kelola pengguna
                    </a>
                </div>
                <div class="col-sm-6">
                    <a class="quick" href="{{ route('admin.karyawan') }}">
                        <span class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg></span>
                        Kelola karyawan
                    </a>
                </div>
                <!-- <div class="col-sm-6">
                    <a class="quick" href="#">
                        <span class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6M8 13h8M8 17h5"/></svg></span>
                        Lihat laporan
                    </a>
                </div>
                <div class="col-sm-6">
                    <a class="quick" href="#">
                        <span class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/></svg></span>
                        Pengaturan
                    </a>
                </div> -->
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-0">
    <div class="col-lg-6 reveal" style="--d:.1s">
        <div class="card p-4 h-100">
            <div class="d-flex justify-content-between align-items-baseline mb-2">
                <h2 class="panel-title">Akun terbaru</h2>
                <a href="{{ route('admin.pengguna') }}" class="link-more">Lihat semua</a>
            </div>
            <ul class="mini-list">
                @forelse (($akunTerbaru ?? []) as $u)
                    @php $nm = $u->karyawan->nama ?? $u->name ?? $u->email; @endphp
                    <li class="mini-item">
                        <span class="avatar-sm">{{ strtoupper(mb_substr($nm, 0, 1)) }}</span>
                        <div class="meta">
                            <div class="nm">{{ $nm }}</div>
                            <div class="sm">{{ $u->email }}{{ $u->created_at ? ' · ' . $u->created_at->locale('id')->diffForHumans() : '' }}</div>
                        </div>
                        <span class="chip">{{ ucfirst($u->role) }}</span>
                    </li>
                @empty
                    <li class="text-muted">Belum ada akun.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="col-lg-6 reveal" style="--d:.16s">
        <div class="card p-4 h-100">
            <div class="d-flex justify-content-between align-items-baseline mb-2">
                <h2 class="panel-title">Karyawan terbaru</h2>
                <a href="{{ route('admin.karyawan') }}" class="link-more">Lihat semua</a>
            </div>
            <ul class="mini-list">
                @forelse (($karyawanTerbaru ?? []) as $k)
                    <li class="mini-item">
                        <span class="avatar-sm">{{ strtoupper(mb_substr($k->nama, 0, 1)) }}</span>
                        <div class="meta">
                            <div class="nm">{{ $k->nama }}</div>
                            <div class="sm">{{ $k->ruangan }}</div>
                        </div>
                        <span class="chip">{{ $k->jabatan }}</span>
                    </li>
                @empty
                    <li class="text-muted">Belum ada karyawan.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var BULAN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    var HARI  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    var DOW   = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
    var KEY   = 'simrs_admin_agenda';

    function pad(n) { return String(n).padStart(2, '0'); }
    function keyOf(d) { return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()); }
    function longDate(d) { return HARI[d.getDay()] + ', ' + d.getDate() + ' ' + BULAN[d.getMonth()] + ' ' + d.getFullYear(); }

    /* ---------- Jam & sapaan ---------- */
    function tick() {
        var n = new Date(), h = n.getHours();
        document.getElementById('jam').textContent = pad(h) + ':' + pad(n.getMinutes()) + ':' + pad(n.getSeconds());
        document.getElementById('tanggal').textContent = longDate(n);
        document.getElementById('sapaan').textContent =
            h < 11 ? 'Selamat pagi' : h < 15 ? 'Selamat siang' : h < 18 ? 'Selamat sore' : 'Selamat malam';
    }
    tick();
    setInterval(tick, 1000);

    /* ---------- Penyimpanan agenda (di browser) ---------- */
    var store = {};
    try { store = JSON.parse(localStorage.getItem(KEY)) || {}; } catch (e) { store = {}; }
    function save() { try { localStorage.setItem(KEY, JSON.stringify(store)); } catch (e) {} }

    /* ---------- Kalender ---------- */
    var today = new Date();
    var view = new Date(today.getFullYear(), today.getMonth(), 1);
    var selected = keyOf(today);

    function renderCalendar() {
        var y = view.getFullYear(), m = view.getMonth();
        document.getElementById('calTitle').textContent = BULAN[m] + ' ' + y;

        var grid = document.getElementById('calGrid');
        grid.innerHTML = '';

        DOW.forEach(function (d, i) {
            var el = document.createElement('div');
            el.className = 'cal-dow' + (i === 6 ? ' sun' : '');
            el.textContent = d;
            grid.appendChild(el);
        });

        var offset = (new Date(y, m, 1).getDay() + 6) % 7; // minggu mulai Senin
        var start = new Date(y, m, 1 - offset);

        for (var i = 0; i < 42; i++) {
            var d = new Date(start.getFullYear(), start.getMonth(), start.getDate() + i);
            var k = keyOf(d);
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = d.getDate();
            btn.className = 'cal-day'
                + (d.getMonth() !== m ? ' other' : '')
                + (d.getDay() === 0 ? ' sun' : '')
                + (k === keyOf(today) ? ' today' : '')
                + (k === selected ? ' selected' : '')
                + (store[k] && store[k].length ? ' has' : '');
            btn.setAttribute('aria-label', longDate(d));
            btn.dataset.key = k;
            grid.appendChild(btn);
        }
    }

    document.getElementById('calGrid').addEventListener('click', function (e) {
        var b = e.target.closest('.cal-day');
        if (!b) return;
        selected = b.dataset.key;
        var p = selected.split('-');
        var d = new Date(+p[0], +p[1] - 1, +p[2]);
        view = new Date(d.getFullYear(), d.getMonth(), 1);
        renderCalendar();
        renderAgenda();
    });
    document.getElementById('calPrev').addEventListener('click', function () {
        view = new Date(view.getFullYear(), view.getMonth() - 1, 1); renderCalendar();
    });
    document.getElementById('calNext').addEventListener('click', function () {
        view = new Date(view.getFullYear(), view.getMonth() + 1, 1); renderCalendar();
    });
    document.getElementById('calToday').addEventListener('click', function () {
        selected = keyOf(today);
        view = new Date(today.getFullYear(), today.getMonth(), 1);
        renderCalendar(); renderAgenda();
    });

    /* ---------- Agenda ---------- */
    var list = document.getElementById('agendaList');
    var input = document.getElementById('agendaInput');

    function renderAgenda() {
        var p = selected.split('-');
        document.getElementById('agendaDate').textContent = longDate(new Date(+p[0], +p[1] - 1, +p[2]));

        var items = store[selected] || [];
        list.innerHTML = '';

        if (!items.length) {
            var empty = document.createElement('li');
            empty.className = 'agenda-empty';
            empty.textContent = 'Belum ada agenda di tanggal ini.';
            list.appendChild(empty);
        }

        items.forEach(function (it) {
            var li = document.createElement('li');
            li.className = 'agenda-item';
            li.innerHTML = '<span class="dot"></span><span class="txt"></span>'
                + '<button type="button" class="agenda-del" aria-label="Hapus agenda">'
                + '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg></button>';
            li.querySelector('.txt').textContent = it.text;
            li.querySelector('.agenda-del').addEventListener('click', function () {
                store[selected] = store[selected].filter(function (x) { return x.id !== it.id; });
                if (!store[selected].length) delete store[selected];
                save(); renderAgenda(); renderCalendar();
            });
            list.appendChild(li);
        });

        document.getElementById('agendaCount').textContent = (store[keyOf(today)] || []).length;
    }

    function addAgenda() {
        var text = input.value.trim();
        if (!text) { input.focus(); return; }
        (store[selected] = store[selected] || []).push({ id: Date.now(), text: text });
        input.value = '';
        save(); renderAgenda(); renderCalendar();
        input.focus();
    }
    document.getElementById('agendaAdd').addEventListener('click', addAgenda);
    input.addEventListener('keydown', function (e) { if (e.key === 'Enter') addAgenda(); });

    /* ---------- Angka naik pelan ---------- */
    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    document.querySelectorAll('[data-count]').forEach(function (el) {
        var target = +el.dataset.count;
        if (!target || reduce) { el.textContent = target; return; }
        var t0 = performance.now(), dur = 800;
        (function step(t) {
            var p = Math.min((t - t0) / dur, 1);
            el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3)));
            if (p < 1) requestAnimationFrame(step);
        })(t0);
    });

    renderCalendar();
    renderAgenda();
})();
</script>
@endpush