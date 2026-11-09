
@extends('layouts.pengadaan.direktur')

@php
    $uang = $uang ?? ['tersedia' => false, 'pesan' => 'Controller belum mengirim data nominal.'];
@endphp

@section('content')
<style>
    .dash-head { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 24px; }
    .dash-head h1 { font-size: 1.5rem; font-weight: 800; letter-spacing: -.03em; margin: 0; color: var(--ink); }
    .dash-head p { margin: 4px 0 0; color: #61797a; font-size: .9rem; }

    .filters { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
    .filters .form-select { width: auto; min-width: 140px; font-weight: 600; font-size: .88rem; border-radius: 10px; }

    /* Muncul bertahap saat dibuka */
    .reveal { animation: reveal .55s cubic-bezier(.2,.7,.2,1) backwards; animation-delay: calc(var(--i, 0) * 70ms); }
    @keyframes reveal { from { opacity: 0; translate: 0 16px; } to { opacity: 1; translate: 0 0; } }

    /* KPI */
    .kpi {
        display: flex; align-items: center; gap: 14px;
        background: #fff; border: 1px solid rgba(15,42,46,.08); border-radius: 16px;
        padding: 16px; height: 100%;
        transition: translate .2s ease, box-shadow .2s ease;
    }
    .kpi:hover { translate: 0 -3px; box-shadow: 0 14px 30px -18px rgba(15,42,46,.45); }
    .kpi .ico { width: 44px; height: 44px; border-radius: 12px; display: grid; place-items: center; font-size: 1.2rem; flex: none; }
    .kpi small { display: block; color: #61797a; font-weight: 600; font-size: .78rem; }
    .kpi b { display: block; font-size: 1.6rem; font-weight: 800; letter-spacing: -.03em; line-height: 1.15; color: var(--ink); }
    .ico-teal   { background: #d9efec; color: #0f766e; }
    .ico-blue   { background: #e0f2fe; color: #0284c7; }
    .ico-amber  { background: #fef3c7; color: #b45309; }
    .ico-green  { background: #dcfce7; color: #15803d; }
    .ico-red    { background: #fee2e2; color: #b91c1c; }
    .ico-violet { background: #ede9fe; color: #6d28d9; }

    .growth { display: inline-flex; align-items: center; gap: 4px; font-size: .72rem; font-weight: 700; padding: 2px 8px; border-radius: 999px; margin-top: 4px; }
    .growth.up   { background: #dcfce7; color: #15803d; }
    .growth.down { background: #fee2e2; color: #b91c1c; }
    .growth.flat { background: #eef2f1; color: #61797a; }

    .section-title { display: flex; align-items: center; gap: 10px; font-size: 1.05rem; font-weight: 800; letter-spacing: -.02em; color: var(--ink); margin: 4px 0 12px; }
    .section-title::after { content: ''; flex: 1; height: 1px; background: rgba(15,42,46,.08); }
    .kpi b.money { font-size: 1.3rem; white-space: nowrap; }

    /* Kartu chart */
    .chart-card {
        position: relative; height: 100%;
        background: #fff; border: 1px solid rgba(15,42,46,.08); border-radius: 16px; padding: 20px;
    }
    .chart-card h2 { font-size: 1rem; font-weight: 800; letter-spacing: -.02em; margin: 0; color: var(--ink); }
    .chart-card .sub { margin: 2px 0 14px; font-size: .8rem; color: #61797a; }
    .chart-box { position: relative; height: 300px; }
    .chart-box.tall { height: 340px; }
    .chart-box.sm { height: 260px; }
    .empty { position: absolute; inset: 0; display: none; align-items: center; justify-content: center; flex-direction: column; gap: 6px; color: #8aa0a1; font-size: .85rem; font-weight: 600; background: #fff; }
    .empty i { font-size: 1.8rem; }

    /* Rasio */
    .ratio-big { font-size: 2.6rem; font-weight: 800; letter-spacing: -.04em; line-height: 1; color: #15803d; }
    .ratio-bar { display: flex; height: 14px; border-radius: 999px; overflow: hidden; background: #eef2f1; margin: 18px 0 14px; }
    .ratio-bar span { display: block; width: 0; transition: width 1s cubic-bezier(.2,.7,.2,1) .3s; }
    .ratio-legend { display: grid; gap: 10px; }
    .ratio-legend div { display: flex; align-items: center; justify-content: space-between; font-size: .88rem; font-weight: 600; }
    .ratio-legend i { display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 8px; }

    /* Tabel terbaru */
    .mini-table { width: 100%; border-collapse: collapse; font-size: .85rem; }
    .mini-table th { font-size: .72rem; text-transform: uppercase; letter-spacing: .04em; color: #61797a; font-weight: 700; padding: 0 10px 10px; text-align: left; border-bottom: 1px solid rgba(15,42,46,.08); }
    .mini-table td { padding: 11px 10px; border-bottom: 1px solid rgba(15,42,46,.05); white-space: nowrap; }
    .mini-table tr:last-child td { border-bottom: 0; }
    .mini-table tbody tr { transition: background .15s ease; }
    .mini-table tbody tr:hover { background: #f1f7f6; }
    .pill { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: .74rem; font-weight: 700; }
    .pill-disetujui         { background: #dcfce7; color: #15803d; }
    .pill-ditolak           { background: #fee2e2; color: #b91c1c; }
    .pill-menunggu_direktur { background: #fef3c7; color: #b45309; }
    .pill-menunggu_keuangan { background: #e0f2fe; color: #0369a1; }
    .pill-lainnya           { background: #eef2f1; color: #52696a; }
    .pill-kat { background: #d9efec; color: #0f766e; text-transform: capitalize; }
</style>

@php
    $growth = $kpi['growth'];
    $growthClass = is_null($growth) ? 'flat' : ($growth > 0 ? 'up' : ($growth < 0 ? 'down' : 'flat'));
@endphp

{{-- ===== Header + filter ===== --}}
<div class="dash-head reveal">
    <div>
        <h1>Dashboard Pengajuan</h1>
        <p>Ringkasan pengadaan dan pemeliharaan barang tahun {{ $tahun }}.</p>
    </div>

    <form method="GET" action="{{ route('direktur.chart') }}" class="filters">
        <select name="tahun" class="form-select" onchange="this.form.submit()" aria-label="Tahun">
            @foreach($listTahun as $t)
                <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>Tahun {{ $t }}</option>
            @endforeach
        </select>
        <select name="kategori" class="form-select" onchange="this.form.submit()" aria-label="Kategori">
            <option value="">Semua kategori</option>
            <option value="pengadaan" {{ $kategori === 'pengadaan' ? 'selected' : '' }}>Pengadaan</option>
            <option value="pemeliharaan" {{ $kategori === 'pemeliharaan' ? 'selected' : '' }}>Pemeliharaan</option>
        </select>
        <a href="{{ route('direktur.pengadaan') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-list-ul"></i> Daftar pengajuan
        </a>
    </form>
</div>

{{-- ===== KPI ===== --}}
<div class="row row-cols-2 row-cols-md-3 row-cols-xl-6 g-3 mb-4">
    <div class="col reveal" style="--i:1">
        <div class="kpi">
            <span class="ico ico-teal"><i class="bi bi-grid-3x3-gap-fill"></i></span>
            <div>
                <small>Total permintaan</small>
                <b data-count="{{ $kpi['total'] }}">0</b>
                @if(!is_null($growth))
                    <span class="growth {{ $growthClass }}" title="Dibanding {{ $tahun - 1 }} ({{ $kpi['prevTotal'] }})">
                        <i class="bi bi-{{ $growth > 0 ? 'arrow-up-right' : ($growth < 0 ? 'arrow-down-right' : 'dash') }}"></i>
                        {{ abs($growth) }}%
                    </span>
                @endif
            </div>
        </div>
    </div>
    <div class="col reveal" style="--i:2">
        <div class="kpi">
            <span class="ico ico-blue"><i class="bi bi-cart3"></i></span>
            <div><small>Pengadaan</small><b data-count="{{ $kpi['pengadaan'] }}">0</b></div>
        </div>
    </div>
    <div class="col reveal" style="--i:3">
        <div class="kpi">
            <span class="ico ico-amber"><i class="bi bi-tools"></i></span>
            <div><small>Pemeliharaan</small><b data-count="{{ $kpi['pemeliharaan'] }}">0</b></div>
        </div>
    </div>
    <div class="col reveal" style="--i:4">
        <div class="kpi">
            <span class="ico ico-green"><i class="bi bi-check-circle-fill"></i></span>
            <div><small>Disetujui</small><b data-count="{{ $kpi['disetujui'] }}">0</b></div>
        </div>
    </div>
    <div class="col reveal" style="--i:5">
        <div class="kpi">
            <span class="ico ico-red"><i class="bi bi-x-circle-fill"></i></span>
            <div><small>Ditolak</small><b data-count="{{ $kpi['ditolak'] }}">0</b></div>
        </div>
    </div>
    <div class="col reveal" style="--i:6">
        <div class="kpi">
            <span class="ico ico-violet"><i class="bi bi-hourglass-split"></i></span>
            <div><small>Masih diproses</small><b data-count="{{ $kpi['diproses'] }}">0</b></div>
        </div>
    </div>
</div>

@if(!($uang['tersedia'] ?? false))
    <div class="alert alert-warning d-flex gap-2 align-items-start reveal" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div>Data nominal belum bisa ditampilkan. {{ $uang['pesan'] ?? '' }}</div>
    </div>
@else
    @php
        $u  = $uang['kpi'];
        $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
        $gu = $u['growth'];
        $guClass = is_null($gu) ? 'flat' : ($gu > 0 ? 'up' : ($gu < 0 ? 'down' : 'flat'));
    @endphp

    <div class="section-title reveal" style="--i:6"><i class="bi bi-cash-stack"></i> Nilai Pengajuan (Rupiah)</div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-3 mb-3">
        <div class="col reveal" style="--i:7">
            <div class="kpi">
                <span class="ico ico-teal"><i class="bi bi-cash-stack"></i></span>
                <div>
                    <small>Total nilai pengajuan</small>
                    <b class="money" data-count="{{ $u['total'] }}" data-money>{{ $rp($u['total']) }}</b>
                    @if(!is_null($gu))
                        <span class="growth {{ $guClass }}" title="Dibanding {{ $tahun - 1 }} ({{ $rp($u['prevTotal']) }})">
                            <i class="bi bi-{{ $gu > 0 ? 'arrow-up-right' : ($gu < 0 ? 'arrow-down-right' : 'dash') }}"></i>
                            {{ abs($gu) }}% dari {{ $tahun - 1 }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col reveal" style="--i:8">
            <div class="kpi">
                <span class="ico ico-blue"><i class="bi bi-cart3"></i></span>
                <div><small>Nilai pengadaan</small><b class="money" data-count="{{ $u['pengadaan'] }}" data-money>{{ $rp($u['pengadaan']) }}</b></div>
            </div>
        </div>
        <div class="col reveal" style="--i:9">
            <div class="kpi">
                <span class="ico ico-amber"><i class="bi bi-tools"></i></span>
                <div><small>Nilai pemeliharaan</small><b class="money" data-count="{{ $u['pemeliharaan'] }}" data-money>{{ $rp($u['pemeliharaan']) }}</b></div>
            </div>
        </div>
        <div class="col reveal" style="--i:10">
            <div class="kpi">
                <span class="ico ico-green"><i class="bi bi-patch-check-fill"></i></span>
                <div><small>Nilai disetujui</small><b class="money" data-count="{{ $u['disetujui'] }}" data-money>{{ $rp($u['disetujui']) }}</b></div>
            </div>
        </div>
        <div class="col reveal" style="--i:11">
            <div class="kpi">
                <span class="ico ico-red"><i class="bi bi-x-octagon-fill"></i></span>
                <div><small>Nilai ditolak</small><b class="money" data-count="{{ $u['ditolak'] }}" data-money>{{ $rp($u['ditolak']) }}</b></div>
            </div>
        </div>
        <div class="col reveal" style="--i:12">
            <div class="kpi">
                <span class="ico ico-violet"><i class="bi bi-calculator-fill"></i></span>
                <div><small>Rata-rata per pengajuan</small><b class="money" data-count="{{ $u['rata'] }}" data-money>{{ $rp($u['rata']) }}</b></div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-8 reveal" style="--i:13">
            <div class="chart-card">
                <h2>Nilai Pengajuan per Bulan</h2>
                <p class="sub">Batang: pengadaan + pemeliharaan (arahkan kursor untuk melihat total). Garis hijau: nilai yang disetujui.</p>
                <div class="chart-box">
                    <canvas id="chUangBulan"></canvas>
                    <div class="empty" id="chUangBulan-empty"><i class="bi bi-cash-stack"></i>Belum ada data</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 reveal" style="--i:14">
            <div class="chart-card">
                <h2>Nilai per Status</h2>
                <p class="sub">Porsi nilai pengajuan berdasarkan status.</p>
                <div class="chart-box">
                    <canvas id="chUangStatus"></canvas>
                    <div class="empty" id="chUangStatus-empty"><i class="bi bi-pie-chart"></i>Belum ada data</div>
                </div>
            </div>
        </div>
        <div class="col-lg-5 reveal" style="--i:15">
            <div class="chart-card">
                <h2>Total Nilai per Tahun</h2>
                <p class="sub">Semua tahun, dipisah pengadaan dan pemeliharaan.</p>
                <div class="chart-box sm">
                    <canvas id="chUangTahun"></canvas>
                    <div class="empty" id="chUangTahun-empty"><i class="bi bi-calendar3"></i>Belum ada data</div>
                </div>
            </div>
        </div>
        <div class="col-lg-7 reveal" style="--i:16">
            <div class="chart-card">
                <h2>Nilai Pengajuan per Bidang</h2>
                <p class="sub">10 bidang dengan nilai pengajuan terbesar.</p>
                <div class="chart-box sm">
                    <canvas id="chUangBidang"></canvas>
                    <div class="empty" id="chUangBidang-empty"><i class="bi bi-building"></i>Belum ada data</div>
                </div>
            </div>
        </div>
    </div>

    <div class="section-title reveal" style="--i:6"><i class="bi bi-bar-chart-line"></i> Jumlah Pengajuan</div>
@endif

<div class="row g-3">

    {{-- 1. Tren per bulan --}}
    <div class="col-lg-8 reveal" style="--i:7">
        <div class="chart-card">
            <h2>Tren Permintaan per Bulan</h2>
            <p class="sub">{{ $tahun }} dibanding {{ $tahun - 1 }}. Lihat kapan permintaan naik atau turun.</p>
            <div class="chart-box">
                <canvas id="chTren"></canvas>
                <div class="empty" id="chTren-empty"><i class="bi bi-graph-up"></i>Belum ada data</div>
            </div>
        </div>
    </div>

    {{-- 2. Distribusi status --}}
    <div class="col-lg-4 reveal" style="--i:8">
        <div class="chart-card">
            <h2>Distribusi Status</h2>
            <p class="sub">Kondisi seluruh pengajuan berdasarkan status.</p>
            <div class="chart-box">
                <canvas id="chStatus"></canvas>
                <div class="empty" id="chStatus-empty"><i class="bi bi-pie-chart"></i>Belum ada data</div>
            </div>
        </div>
    </div>

    {{-- 4. Pengadaan vs pemeliharaan --}}
    <div class="col-lg-7 reveal" style="--i:9">
        <div class="chart-card">
            <h2>Pengadaan vs Pemeliharaan</h2>
            <p class="sub">Kebutuhan barang baru dibanding kebutuhan pemeliharaan per bulan.</p>
            <div class="chart-box">
                <canvas id="chKategori"></canvas>
                <div class="empty" id="chKategori-empty"><i class="bi bi-bar-chart"></i>Belum ada data</div>
            </div>
        </div>
    </div>

    {{-- 5. Rasio persetujuan --}}
    <div class="col-lg-5 reveal" style="--i:10">
        <div class="chart-card">
            <h2>Rasio Persetujuan</h2>
            <p class="sub">Tingkat persetujuan permintaan tahun {{ $tahun }}.</p>
            <div class="d-flex align-items-end gap-3">
                <div class="ratio-big"><span data-count="{{ $kpi['rasio']['disetujui'] }}" data-decimals="1">0</span>%</div>
                <div class="text-muted fw-semibold pb-1" style="font-size:.85rem">disetujui dari {{ $kpi['total'] }} pengajuan</div>
            </div>
            <div class="ratio-bar" role="img" aria-label="Rasio disetujui, ditolak, diproses">
                <span style="background:#22c55e" data-w="{{ $kpi['rasio']['disetujui'] }}"></span>
                <span style="background:#ef4444" data-w="{{ $kpi['rasio']['ditolak'] }}"></span>
                <span style="background:#a78bfa" data-w="{{ $kpi['rasio']['diproses'] }}"></span>
            </div>
            <div class="ratio-legend">
                <div><span><i style="background:#22c55e"></i>Disetujui</span><span>{{ $kpi['rasio']['disetujui'] }}% &middot; {{ $kpi['disetujui'] }}</span></div>
                <div><span><i style="background:#ef4444"></i>Ditolak</span><span>{{ $kpi['rasio']['ditolak'] }}% &middot; {{ $kpi['ditolak'] }}</span></div>
                <div><span><i style="background:#a78bfa"></i>Masih diproses</span><span>{{ $kpi['rasio']['diproses'] }}% &middot; {{ $kpi['diproses'] }}</span></div>
            </div>
        </div>
    </div>

    {{-- 3. Per bidang --}}
    <div class="col-lg-6 reveal" style="--i:11">
        <div class="chart-card">
            <h2>Pengajuan per Tipe Usulan</h2>
            <p class="sub">Tipe Usulan yang paling aktif mengajukan kebutuhan (10 teratas).</p>
            <div class="chart-box tall">
                <canvas id="chBidang"></canvas>
                <div class="empty" id="chBidang-empty"><i class="bi bi-building"></i>Belum ada data</div>
            </div>
        </div>
    </div>

    {{-- 6. Top barang --}}
    <div class="col-lg-6 reveal" style="--i:12">
        <div class="chart-card">
            <h2>10 Barang Paling Banyak Diminta</h2>
            <p class="sub">Berdasarkan nama barang pada item pengajuan.</p>
            <div class="chart-box tall">
                <canvas id="chBarang"></canvas>
                <div class="empty" id="chBarang-empty"><i class="bi bi-box-seam"></i>Belum ada data</div>
            </div>
        </div>
    </div>

    {{-- Total per tahun --}}
    <div class="col-lg-5 reveal" style="--i:13">
        <div class="chart-card">
            <h2>Total Permintaan per Tahun</h2>
            <p class="sub">Semua tahun, dipisah pengadaan dan pemeliharaan.</p>
            <div class="chart-box sm">
                <canvas id="chTahun"></canvas>
                <div class="empty" id="chTahun-empty"><i class="bi bi-calendar3"></i>Belum ada data</div>
            </div>
        </div>
    </div>

    {{-- Terbaru --}}
    <div class="col-lg-7 reveal" style="--i:14">
        <div class="chart-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2>Pengajuan Terbaru</h2>
                    <p class="sub">6 pengajuan terakhir yang masuk.</p>
                </div>
                <a href="{{ route('direktur.pengadaan') }}" class="btn btn-sm btn-outline-primary">Lihat semua</a>
            </div>
            <div class="table-responsive">
                <table class="mini-table">
                    <thead>
                        <tr><th>No pengajuan</th><th>Bidang</th><th>Kategori</th><th>Status</th><th>Tanggal</th></tr>
                    </thead>
                    <tbody>
                        @forelse($terbaru as $row)
                            <tr>
                                <td class="fw-semibold">{{ $row['no'] }}</td>
                                <td>{{ $row['bidang'] }}</td>
                                <td><span class="pill pill-kat">{{ $row['kategori'] }}</span></td>
                                <td><span class="pill pill-{{ $row['bucket'] }}">{{ $row['status'] }}</span></td>
                                <td class="text-muted">{{ $row['tanggal'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pengajuan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    window.CHART_DATA = @json($chart);
    window.CHART_UANG = @json($uang['chart'] ?? null);
</script>
@verbatim
<script>
(function () {
    var D = window.CHART_DATA;
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var BULAN = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    var C = { teal:'#0f766e', bright:'#14b8a6', amber:'#f59e0b', blue:'#0ea5e9', violet:'#8b5cf6', red:'#ef4444', green:'#22c55e', gray:'#94a3b8' };
    var STATUS_COLOR = { disetujui: C.green, ditolak: C.red, menunggu_direktur: C.amber, menunggu_keuangan: C.blue, lainnya: C.gray };

    // ---------- Default Chart.js ----------
    Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#5b7072';
    Chart.defaults.borderColor = 'rgba(15,42,46,.07)';
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.boxWidth = 8;
    Chart.defaults.plugins.tooltip.backgroundColor = '#0f2a2e';
    Chart.defaults.plugins.tooltip.padding = 10;
    Chart.defaults.plugins.tooltip.cornerRadius = 8;
    Chart.defaults.plugins.tooltip.titleFont = { weight: '700' };
    if (reduce) { Chart.defaults.animation = false; }

    function sum(arr) { return (arr || []).reduce(function (a, b) { return a + b; }, 0); }

    // ---------- Format Rupiah ----------
    function rupiah(n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); }
    function rpShort(v) {
        var a = Math.abs(v), f = function (x) { return x.toLocaleString('id-ID', { maximumFractionDigits: 1 }); };
        if (a >= 1e12) { return f(v / 1e12) + ' T'; }
        if (a >= 1e9)  { return f(v / 1e9) + ' M'; }
        if (a >= 1e6)  { return f(v / 1e6) + ' jt'; }
        if (a >= 1e3)  { return f(v / 1e3) + ' rb'; }
        return String(v);
    }
    var moneyAxis = { beginAtZero: true, ticks: { callback: function (v) { return rpShort(v); } }, grid: { drawBorder: false } };

    // Tampilkan "Belum ada data" jika semua nilai nol
    function make(id, values, config) {
        var empty = document.getElementById(id + '-empty');
        if (!sum(values)) { empty.style.display = 'flex'; return; }
        new Chart(document.getElementById(id), config);
    }

    var intAxis = { beginAtZero: true, ticks: { precision: 0 }, grid: { drawBorder: false } };

    // ---------- 1. Tren per bulan (line) ----------
    make('chTren', D.tren.now.concat(D.tren.prev), {
        type: 'line',
        data: {
            labels: BULAN,
            datasets: [
                { label: String(D.tahun), data: D.tren.now, borderColor: C.teal, backgroundColor: 'rgba(15,118,110,.12)', fill: true, tension: .35, borderWidth: 3, pointRadius: 4, pointHoverRadius: 6, pointBackgroundColor: '#fff', pointBorderWidth: 2 },
                { label: String(D.tahunLalu), data: D.tren.prev, borderColor: C.gray, borderDash: [6, 6], tension: .35, borderWidth: 2, pointRadius: 0, pointHoverRadius: 5 }
            ]
        },
        options: {
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'top', align: 'end' } },
            scales: { y: intAxis, x: { grid: { display: false } } }
        }
    });

    // ---------- 2. Distribusi status (doughnut) ----------
    make('chStatus', D.status.map(function (s) { return s.value; }), {
        type: 'doughnut',
        data: {
            labels: D.status.map(function (s) { return s.label; }),
            datasets: [{
                data: D.status.map(function (s) { return s.value; }),
                backgroundColor: D.status.map(function (s) { return STATUS_COLOR[s.key]; }),
                borderColor: '#fff', borderWidth: 3, hoverOffset: 8
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutout: '66%',
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            var total = sum(ctx.dataset.data);
                            var pct = total ? Math.round(ctx.parsed / total * 1000) / 10 : 0;
                            return ' ' + ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });

    // ---------- 3. Per bidang (horizontal bar) ----------
    make('chBidang', D.bidang.values, {
        type: 'bar',
        data: {
            labels: D.bidang.labels,
            datasets: [{ label: 'Pengajuan', data: D.bidang.values, backgroundColor: C.teal, hoverBackgroundColor: C.bright, borderRadius: 6, maxBarThickness: 22 }]
        },
        options: {
            indexAxis: 'y',
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: intAxis, y: { grid: { display: false } } }
        }
    });

    // ---------- 4. Pengadaan vs pemeliharaan (grouped bar) ----------
    make('chKategori', D.kategori.pengadaan.concat(D.kategori.pemeliharaan), {
        type: 'bar',
        data: {
            labels: BULAN,
            datasets: [
                { label: 'Pengadaan', data: D.kategori.pengadaan, backgroundColor: C.teal, borderRadius: 6, maxBarThickness: 18 },
                { label: 'Pemeliharaan', data: D.kategori.pemeliharaan, backgroundColor: C.amber, borderRadius: 6, maxBarThickness: 18 }
            ]
        },
        options: {
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'top', align: 'end' } },
            scales: { y: intAxis, x: { grid: { display: false } } }
        }
    });

    // ---------- 6. Top 10 barang (horizontal bar) ----------
    make('chBarang', D.barang.values, {
        type: 'bar',
        data: {
            labels: D.barang.labels,
            datasets: [{ label: 'Diminta', data: D.barang.values, backgroundColor: C.violet, hoverBackgroundColor: '#a78bfa', borderRadius: 6, maxBarThickness: 22 }]
        },
        options: {
            indexAxis: 'y',
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { title: function (items) { return D.barang.labels[items[0].dataIndex]; } } }
            },
            scales: {
                x: intAxis,
                y: {
                    grid: { display: false },
                    ticks: {
                        callback: function (v) {
                            var l = this.getLabelForValue(v);
                            return l.length > 26 ? l.slice(0, 25) + '\u2026' : l;
                        }
                    }
                }
            }
        }
    });

    // ---------- Total per tahun (stacked bar) ----------
    make('chTahun', D.perTahun.pengadaan.concat(D.perTahun.pemeliharaan), {
        type: 'bar',
        data: {
            labels: D.perTahun.labels,
            datasets: [
                { label: 'Pengadaan', data: D.perTahun.pengadaan, backgroundColor: C.teal, borderRadius: 6, maxBarThickness: 44 },
                { label: 'Pemeliharaan', data: D.perTahun.pemeliharaan, backgroundColor: C.amber, borderRadius: 6, maxBarThickness: 44 }
            ]
        },
        options: {
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'top', align: 'end' } },
            scales: { x: { stacked: true, grid: { display: false } }, y: Object.assign({ stacked: true }, intAxis) }
        }
    });

    // ---------- Nilai (Rupiah) ----------
    var U = window.CHART_UANG;
    if (U) {
        // Nilai per bulan: batang bertumpuk + garis disetujui
        make('chUangBulan', U.bulan.pengadaan.concat(U.bulan.pemeliharaan), {
            type: 'bar',
            data: {
                labels: BULAN,
                datasets: [
                    { type: 'bar', label: 'Pengadaan', data: U.bulan.pengadaan, backgroundColor: C.teal, borderRadius: 6, maxBarThickness: 28, stack: 'nilai', order: 2 },
                    { type: 'bar', label: 'Pemeliharaan', data: U.bulan.pemeliharaan, backgroundColor: C.amber, borderRadius: 6, maxBarThickness: 28, stack: 'nilai', order: 2 },
                    { type: 'line', label: 'Disetujui', data: U.bulan.disetujui, borderColor: C.green, backgroundColor: C.green, tension: .35, borderWidth: 3, pointRadius: 4, pointHoverRadius: 6, order: 1 }
                ]
            },
            options: {
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top', align: 'end' },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) { return ' ' + ctx.dataset.label + ': ' + rupiah(ctx.parsed.y); },
                            footer: function (items) {
                                var t = 0;
                                items.forEach(function (i) { if (i.dataset.type !== 'line') { t += i.parsed.y; } });
                                return 'Total pengajuan: ' + rupiah(t);
                            }
                        }
                    }
                },
                scales: { x: { stacked: true, grid: { display: false } }, y: Object.assign({ stacked: true }, moneyAxis) }
            }
        });

        // Nilai per status (doughnut)
        make('chUangStatus', U.status.map(function (s) { return s.value; }), {
            type: 'doughnut',
            data: {
                labels: U.status.map(function (s) { return s.label; }),
                datasets: [{
                    data: U.status.map(function (s) { return s.value; }),
                    backgroundColor: U.status.map(function (s) { return STATUS_COLOR[s.key]; }),
                    borderColor: '#fff', borderWidth: 3, hoverOffset: 8
                }]
            },
            options: {
                maintainAspectRatio: false,
                cutout: '66%',
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                var total = sum(ctx.dataset.data);
                                var pct = total ? Math.round(ctx.parsed / total * 1000) / 10 : 0;
                                return ' ' + ctx.label + ': ' + rupiah(ctx.parsed) + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });

        // Total nilai per tahun (stacked)
        make('chUangTahun', U.perTahun.pengadaan.concat(U.perTahun.pemeliharaan), {
            type: 'bar',
            data: {
                labels: U.perTahun.labels,
                datasets: [
                    { label: 'Pengadaan', data: U.perTahun.pengadaan, backgroundColor: C.teal, borderRadius: 6, maxBarThickness: 44 },
                    { label: 'Pemeliharaan', data: U.perTahun.pemeliharaan, backgroundColor: C.amber, borderRadius: 6, maxBarThickness: 44 }
                ]
            },
            options: {
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top', align: 'end' },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) { return ' ' + ctx.dataset.label + ': ' + rupiah(ctx.parsed.y); },
                            footer: function (items) {
                                var t = 0;
                                items.forEach(function (i) { t += i.parsed.y; });
                                return 'Total: ' + rupiah(t);
                            }
                        }
                    }
                },
                scales: { x: { stacked: true, grid: { display: false } }, y: Object.assign({ stacked: true }, moneyAxis) }
            }
        });

        // Nilai per bidang (horizontal bar)
        make('chUangBidang', U.bidang.values, {
            type: 'bar',
            data: {
                labels: U.bidang.labels,
                datasets: [{ label: 'Nilai', data: U.bidang.values, backgroundColor: C.blue, hoverBackgroundColor: '#38bdf8', borderRadius: 6, maxBarThickness: 20 }]
            },
            options: {
                indexAxis: 'y',
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: function (items) { return U.bidang.labels[items[0].dataIndex]; },
                            label: function (ctx) { return ' ' + rupiah(ctx.parsed.x); }
                        }
                    }
                },
                scales: {
                    x: moneyAxis,
                    y: {
                        grid: { display: false },
                        ticks: {
                            callback: function (v) {
                                var l = this.getLabelForValue(v);
                                return l.length > 24 ? l.slice(0, 23) + '\u2026' : l;
                            }
                        }
                    }
                }
            }
        });
    }

    // ---------- Angka KPI berhitung naik ----------
    document.querySelectorAll('[data-count]').forEach(function (el) {
        var target = parseFloat(el.dataset.count) || 0;
        var dec = parseInt(el.dataset.decimals || '0', 10);
        var money = el.hasAttribute('data-money');
        var fmt = function (n) { return money ? rupiah(n) : (dec ? n.toFixed(dec) : Math.round(n).toLocaleString('id-ID')); };
        if (reduce || !target) { el.textContent = fmt(target); return; }
        var start = null, dur = 900;
        function step(ts) {
            if (start === null) { start = ts; }
            var p = Math.min((ts - start) / dur, 1);
            el.textContent = fmt(target * (1 - Math.pow(1 - p, 3)));
            if (p < 1) { requestAnimationFrame(step); }
        }
        requestAnimationFrame(step);
    });

    // ---------- Bar rasio melebar ----------
    requestAnimationFrame(function () {
        document.querySelectorAll('.ratio-bar [data-w]').forEach(function (el) {
            el.style.width = el.dataset.w + '%';
        });
    });
})();
</script>
@endverbatim
@endpush