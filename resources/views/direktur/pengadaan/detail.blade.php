@extends('layouts.pengadaan.direktur')

@section('content')
@php
    $st = $pengajuan->status ?? 'draft';

    // [label, tone] tone: success | danger | warning | info | neutral
    $statusMap = [
        'draft'                       => ['Draft', 'neutral'],
        'diajukan'                    => ['Diajukan', 'warning'],
        'revisi'                      => ['Revisi', 'warning'],
        'disetujui_koordinator'       => ['Disetujui Koordinator', 'info'],
        'disetujui_kabid'             => ['Disetujui Kabid', 'info'],
        'menunggu_direktur'           => ['Menunggu Direktur', 'warning'],
        'disetujui'                   => ['Disetujui Keuangan', 'success'],
        'ditolak'                     => ['Ditolak', 'danger'],
        'ditolak_penerima'            => ['Ditolak Penerima', 'danger'],
        'ditolak_keuangan'            => ['Ditolak Keuangan', 'danger'],
        'disetujui_direktur'          => ['Disetujui Direktur', 'success'],
        'disetujui_sebagian_direktur' => ['Disetujui Sebagian', 'info'],
        'ditunda'                     => ['Ditunda', 'warning'],
        'ditolak_direktur'            => ['Ditolak Direktur', 'danger'],
    ];
    [$statusLabel, $statusTone] = $statusMap[$st] ?? [$st ?: 'Unknown', 'neutral'];

    $fmtDate = fn($d) => $d ? date('d M Y, H:i', strtotime($d)) : null;
    $rp = fn($n) => number_format($n ?? 0, 0, ',', '.');

    $bisaMemutuskan = in_array($st, ['disetujui', 'menunggu_direktur', 'ditunda']);
    $sudahDirektur  = in_array($st, ['disetujui_direktur', 'disetujui_sebagian_direktur', 'ditolak_direktur']);

    $atasanOk = !empty($pengajuan->disetujui_kabid_at) || in_array($st, ['disetujui_kabid','menunggu_direktur','disetujui','ditunda','disetujui_direktur','disetujui_sebagian_direktur','ditolak_direktur']);
    $keuanganOk = !empty($pengajuan->verified_at) || in_array($st, ['disetujui','menunggu_direktur','ditunda','disetujui_direktur','disetujui_sebagian_direktur','ditolak_direktur']);

    $direkturState = match ($st) {
        'disetujui_direktur', 'disetujui_sebagian_direktur' => 'done',
        'ditolak_direktur' => 'rejected',
        default => $keuanganOk ? 'current' : 'pending',
    };
    $direkturNote = match ($st) {
        'disetujui_direktur' => 'Disetujui',
        'disetujui_sebagian_direktur' => 'Disetujui sebagian',
        'ditolak_direktur' => 'Ditolak',
        'ditunda' => 'Ditunda, menunggu keputusan',
        default => $keuanganOk ? 'Menunggu keputusan Anda' : 'Belum sampai tahap ini',
    };

    $steps = [
        ['title' => 'Pengajuan', 'note' => $fmtDate($pengajuan->created_at ?? null) ?? '-', 'state' => 'done'],
        ['title' => 'Persetujuan atasan', 'note' => $atasanOk ? ($fmtDate($pengajuan->disetujui_kabid_at ?? null) ?? 'Disetujui') : 'Menunggu', 'state' => $atasanOk ? 'done' : 'current'],
        ['title' => 'Verifikasi keuangan', 'note' => $keuanganOk ? ($fmtDate($pengajuan->verified_at ?? null) ?? 'Terverifikasi') : 'Menunggu', 'state' => $keuanganOk ? 'done' : ($atasanOk ? 'current' : 'pending')],
        ['title' => 'Keputusan direktur', 'note' => $direkturNote, 'state' => $direkturState],
    ];

    $totalPengajuan = $pengajuan->total_pengajuan ?? 0;
    $totalSetuju = $pengajuan->total_disetujui_direktur ?? $totalPengajuan;
    $catatanDirektur = $pengajuan->catatan_direktur ?? null;
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&display=swap" rel="stylesheet">

<div class="dx">

    {{-- ============ HEADER ============ --}}
    <a href="{{ route('direktur.pengadaan') }}" class="dx-back"><i class="bi bi-arrow-left"></i> Daftar pengajuan</a>
    <div class="dx-header">
        <h1 class="dx-title">{{ $pengajuan->no_pengajuan ?? 'N/A' }}</h1>
        <span class="dx-pill dx-pill-{{ $statusTone }}"><i class="bi bi-circle-fill"></i> {{ $statusLabel }}</span>
    </div>
    <p class="dx-sub">Diajukan oleh {{ $pengajuan->karyawan->nama ?? '-' }} pada {{ $fmtDate($pengajuan->created_at ?? null) ?? '-' }}</p>

    <div class="row g-4 g-xl-5">

        {{-- ============ PANEL KEPUTUSAN (sticky) ============ --}}
        <div class="col-lg-4 order-first order-lg-last">
            <aside class="dx-rail">

                <div class="dx-decision">
                    <div class="dx-decision-label">Total pengajuan</div>
                    <div class="dx-amount"><span>Rp</span>{{ $rp($totalPengajuan) }}</div>

                    @if($sudahDirektur && $totalSetuju != $totalPengajuan)
                        <div class="dx-decision-sub">Disetujui direktur: <strong>Rp {{ $rp($totalSetuju) }}</strong></div>
                    @endif

                    @if($bisaMemutuskan)
                        <p class="dx-decision-hint">Periksa seluruh data di samping sebelum memberi keputusan akhir.</p>
                        <div class="dx-decision-actions">
                            <button type="button" class="dx-btn dx-btn-light" data-bs-toggle="modal" data-bs-target="#modalSetujuiDirektur">
                                <i class="bi bi-check2-circle"></i> Setujui
                            </button>
                            <button type="button" class="dx-btn dx-btn-outline" data-bs-toggle="modal" data-bs-target="#modalTolakDirektur">
                                <i class="bi bi-x-circle"></i> Tolak
                            </button>
                        </div>
                    @elseif($sudahDirektur)
                        <p class="dx-decision-hint">Keputusan sudah diberikan: <strong>{{ $statusLabel }}</strong>.</p>
                        @if($catatanDirektur)
                            <blockquote class="dx-note">{{ $catatanDirektur }}</blockquote>
                        @endif
                    @else
                        <p class="dx-decision-hint">Pengajuan ini belum sampai pada tahap keputusan direktur.</p>
                    @endif
                </div>

                <div class="dx-timeline-wrap">
                    <h2 class="dx-h2">Alur persetujuan</h2>
                    <ol class="dx-timeline">
                        @foreach($steps as $s)
                        <li class="is-{{ $s['state'] }}">
                            <span class="dx-dot">
                                @if($s['state'] === 'done')<i class="bi bi-check-lg"></i>
                                @elseif($s['state'] === 'rejected')<i class="bi bi-x-lg"></i>
                                @endif
                            </span>
                            <div class="dx-step-title">{{ $s['title'] }}</div>
                            <div class="dx-step-note">{{ $s['note'] }}</div>
                        </li>
                        @endforeach
                    </ol>
                </div>

            </aside>
        </div>

        {{-- ============ MEMO ============ --}}
        <div class="col-lg-8">
            <article class="dx-memo">

                <section class="dx-sec">
                    <h2 class="dx-h2">Pengaju</h2>
                    <dl class="dx-dl">
                        <div><dt>Nama</dt><dd>{{ $pengajuan->karyawan->nama ?? '-' }}</dd></div>
                        <div><dt>Jabatan</dt><dd>{{ $pengajuan->karyawan->jabatan ?? '-' }}</dd></div>
                        <div><dt>Bidang</dt><dd>{{ $pengajuan->bidang->nama_bidang ?? '-' }}</dd></div>
                        <div><dt>Instalasi</dt><dd>{{ $pengajuan->instalasi ?? '-' }}</dd></div>
                    </dl>
                </section>

                <section class="dx-sec">
                    <h2 class="dx-h2">Penerima</h2>
                    <dl class="dx-dl">
                        <div><dt>Nama</dt><dd>{{ $pengajuan->penerima->nama ?? 'Belum ditentukan' }}</dd></div>
                        <div><dt>Jabatan</dt><dd>{{ $pengajuan->penerima->jabatan ?? 'Belum ditentukan' }}</dd></div>
                        <div><dt>Bidang</dt><dd>{{ $pengajuan->penerima->bidang->nama_bidang ?? 'Belum ditentukan' }}</dd></div>
                        <div>
                            <dt>Status penerimaan</dt>
                            <dd>
                                @if(($pengajuan->status_penerimaan ?? '') == 'diterima')
                                    <span class="dx-pill dx-pill-success dx-pill-sm">Sudah menerima</span>
                                @elseif(($pengajuan->status_penerimaan ?? '') == 'ditolak')
                                    <span class="dx-pill dx-pill-danger dx-pill-sm">Ditolak</span>
                                @else
                                    <span class="dx-pill dx-pill-warning dx-pill-sm">Menunggu konfirmasi</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </section>

                <section class="dx-sec">
                    <h2 class="dx-h2">Alasan justifikasi</h2>
                    <p class="dx-prose">{{ $pengajuan->alasan_justifikasi ?? '-' }}</p>
                </section>

                <section class="dx-sec">
                    <h2 class="dx-h2">Manfaat</h2>
                    <p class="dx-prose">{{ $pengajuan->manfaat ?? '-' }}</p>
                </section>

                <section class="dx-sec dx-sec-wide">
                    <h2 class="dx-h2">Barang yang diajukan</h2>
                    <div class="table-responsive">
                        <table class="dx-table">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end">Harga satuan</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pengajuan->items ?? [] as $item)
                                <tr>
                                    <td>
                                        <div class="dx-item-name">{{ $item->nama_barang ?? '-' }}</div>
                                        <div class="dx-item-spec">{{ $item->spesifikasi ?? '-' }}</div>
                                    </td>
                                    <td class="text-center">{{ $item->jumlah ?? 0 }} {{ $item->satuan ?? 'Unit' }}</td>
                                    <td class="text-end">Rp {{ $rp($item->harga_satuan ?? 0) }}</td>
                                    <td class="text-end dx-strong">Rp {{ $rp($item->harga ?? 0) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center dx-empty">Belum ada barang pada pengajuan ini.</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end">Total pengajuan</td>
                                    <td class="text-end">Rp {{ $rp($totalPengajuan) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>

            </article>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- MODAL SETUJUI                                --}}
{{-- ============================================ --}}
<div class="modal fade dx-modal" id="modalSetujuiDirektur" tabindex="-1" aria-labelledby="modalSetujuiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSetujuiLabel">Setujui pengajuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="{{ route('direktur.setujui', $pengajuan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="dx-modal-amount">
                        <div class="dx-on-light">Total yang akan disetujui</div>
                        <div class="dx-amount dx-amount-sm"><span>Rp</span>{{ $rp($totalSetuju) }}</div>
                    </div>

                    <input type="hidden" name="direktur_id" value="{{ $authUser->karyawan->id ?? '' }}">
                    <input type="hidden" name="total_disetujui_direktur" value="{{ $totalSetuju }}">

                    <label class="dx-field-label" for="catatan_setuju">Catatan persetujuan <span class="dx-opt">(opsional)</span></label>
                    <textarea id="catatan_setuju" name="catatan_direktur" class="dx-input" rows="3" placeholder="Tambahkan catatan bila diperlukan"></textarea>

                    <label class="dx-check" for="setuju_direktur_full">
                        <input type="checkbox" id="setuju_direktur_full" required>
                        <span>Saya menyatakan anggaran tersedia penuh dan pengajuan ini layak disetujui.</span>
                    </label>
                </div>
                <div class="modal-footer">
                    <button type="button" class="dx-btn dx-btn-ghost" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="dx-btn dx-btn-solid" id="btnSetujuiFull" disabled>
                        <i class="bi bi-check2-circle"></i> Ya, setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- MODAL TOLAK                                  --}}
{{-- ============================================ --}}
<div class="modal fade dx-modal" id="modalTolakDirektur" tabindex="-1" aria-labelledby="modalTolakLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTolakLabel">Tolak pengajuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="{{ route('direktur.tolak', $pengajuan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p class="dx-modal-note">
                        Anda akan menolak pengajuan <strong>{{ $pengajuan->no_pengajuan ?? 'N/A' }}</strong>.
                        Alasan penolakan akan dikirim ke pengaju.
                    </p>

                    <input type="hidden" name="direktur_id" value="{{ $authUser->karyawan->id ?? '' }}">

                    <label class="dx-field-label" for="catatan_tolak">Alasan penolakan <span class="dx-req">wajib diisi</span></label>
                    <textarea id="catatan_tolak" name="catatan_direktur" class="dx-input" rows="3" required placeholder="Jelaskan alasan penolakan dengan jelas"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="dx-btn dx-btn-ghost" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="dx-btn dx-btn-danger">
                        <i class="bi bi-x-circle"></i> Tolak pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* ===== Token (indigo selaras dengan navbar layout) ===== */
.dx, .dx-modal {
    --ink: #231F5C;          /* indigo tua */
    --ink-2: #3A2F85;
    --accent: #5B4BC4;       /* violet untuk aksen di latar putih */
    --gold: #D9BF86;         /* hanya dipakai di panel gelap */
    --tint: #F5F4FB;
    --line: #E6E4F0;
    --text: #25233A;
    --muted: #6F6D85;
    --ok: #2B7A57;
    --bad: #A63D40;
    --warn: #A06E1C;
    --info: #3B6790;
    --rail-top: 88px;        /* sesuaikan dengan tinggi navbar */
    font-family: inherit;    /* ikut font layout */
    color: var(--text);
}
.dx-title, .dx-h2, .dx-amount, .dx-modal .modal-title, .dx-table tfoot td:last-child {
    font-family: 'Fraunces', Georgia, serif;
    font-optical-sizing: auto;
    font-variant-numeric: lining-nums;
}

/* ===== Header ===== */
.dx-back { display: inline-flex; align-items: center; gap: 8px; color: var(--muted); text-decoration: none; font-size: .88rem; font-weight: 500; margin-bottom: 14px; }
.dx-back:hover { color: var(--accent); }
.dx-header { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
.dx-title { font-size: 2.4rem; font-weight: 500; letter-spacing: -.01em; margin: 0; color: var(--ink); line-height: 1.1; }
.dx-sub { color: var(--muted); margin: 8px 0 30px; font-size: .95rem; }

/* ===== Pill ===== */
.dx-pill { display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 999px; font-weight: 600; font-size: .82rem; border: 1px solid transparent; }
.dx-pill i { font-size: 6px; }
.dx-pill-sm { padding: 3px 11px; font-size: .76rem; }
.dx-pill-success { background: #E8F3ED; color: var(--ok);   border-color: #CBE3D6; }
.dx-pill-danger  { background: #F7E9E9; color: var(--bad);  border-color: #EACDCE; }
.dx-pill-warning { background: #F9F1E1; color: var(--warn); border-color: #EDDAB4; }
.dx-pill-info    { background: #E8EFF6; color: var(--info); border-color: #CDDCEA; }
.dx-pill-neutral { background: #EEEEF3; color: var(--muted); border-color: var(--line); }

/* ===== Memo: datar di atas kontainer putih layout, tanpa kartu bersarang ===== */
.dx-sec { display: grid; grid-template-columns: 140px 1fr; gap: 10px 28px; padding: 28px 0; border-top: 1px solid var(--line); }
.dx-sec:first-child { border-top: 0; padding-top: 6px; }
.dx-sec-wide { grid-template-columns: 1fr; }
.dx-h2 { font-size: 1.1rem; font-weight: 500; color: var(--ink); margin: 0; }
.dx-prose { margin: 0; line-height: 1.75; color: #433F5C; max-width: 62ch; }

.dx-dl { display: grid; grid-template-columns: 1fr 1fr; gap: 20px 28px; margin: 0; }
.dx-dl dt { font-size: .78rem; color: var(--muted); font-weight: 500; margin-bottom: 2px; }
.dx-dl dd { margin: 0; font-weight: 500; color: var(--text); }

/* ===== Tabel ===== */
.dx-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
.dx-table th { padding: 10px 0; font-size: .78rem; font-weight: 500; color: var(--muted); border-bottom: 1px solid var(--ink); }
.dx-table td { padding: 16px 0; border-bottom: 1px solid var(--line); vertical-align: top; }
.dx-table th:not(:first-child), .dx-table td:not(:first-child) { padding-left: 14px; }
.dx-item-name { font-weight: 600; }
.dx-item-spec { font-size: .83rem; color: var(--muted); margin-top: 2px; }
.dx-strong { font-weight: 600; }
.dx-empty { color: var(--muted); padding: 34px 0 !important; }
.dx-table tfoot td { padding-top: 20px; border: 0; font-weight: 500; }
.dx-table tfoot td:last-child { font-size: 1.5rem; color: var(--ink); white-space: nowrap; }

/* ===== Rail ===== */
.dx-rail { position: sticky; top: var(--rail-top); display: flex; flex-direction: column; gap: 28px; }
.dx-decision { color: #fff; border-radius: 18px; padding: 30px 26px 26px; position: relative; overflow: hidden;
    background: radial-gradient(120% 90% at 100% 0%, rgba(139,92,246,.45) 0%, rgba(139,92,246,0) 60%), linear-gradient(160deg, var(--ink) 0%, var(--ink-2) 100%);
    box-shadow: 0 26px 46px -28px rgba(35,31,92,.8); }
.dx-decision::before { content: ''; position: absolute; left: 26px; right: 26px; top: 0; height: 2px; background: var(--gold); }
.dx-decision-label { font-size: .84rem; color: #BDB8E6; }
.dx-amount { font-size: 2.5rem; font-weight: 500; line-height: 1.1; margin-top: 6px; letter-spacing: -.01em; }
.dx-amount span { font-size: 1.05rem; color: var(--gold); margin-right: 6px; font-weight: 500; }
.dx-decision-sub { margin-top: 10px; color: #D6D2F2; font-size: .88rem; }
.dx-decision-hint { color: #C7C3EA; font-size: .88rem; line-height: 1.6; margin: 18px 0 20px; }
.dx-decision-actions { display: grid; gap: 10px; }
.dx-note { margin: 0; padding: 4px 0 0 14px; border-left: 2px solid var(--gold); color: #E8E5FA; font-size: .9rem; line-height: 1.6; }

/* ===== Tombol ===== */
.dx-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 24px; border-radius: 999px; font-weight: 600; font-size: .9rem; border: 1px solid transparent; cursor: pointer; text-decoration: none; transition: background .2s, color .2s, border-color .2s; }
.dx-btn:focus-visible { outline: 2px solid var(--gold); outline-offset: 3px; }
.dx-btn:disabled { opacity: .4; cursor: not-allowed; }
.dx-btn-light { background: #fff; color: var(--ink); }
.dx-btn-light:hover { background: #EFEBFF; color: var(--ink); }
.dx-btn-outline { background: transparent; color: #fff; border-color: rgba(255,255,255,.4); }
.dx-btn-outline:hover { background: rgba(255,255,255,.1); border-color: rgba(255,255,255,.75); color: #fff; }
.dx-btn-ghost { background: #fff; color: var(--text); border-color: var(--line); }
.dx-btn-ghost:hover { border-color: var(--accent); }
.dx-btn-solid { background: var(--ink); color: #fff; }
.dx-btn-solid:hover:not(:disabled) { background: var(--ink-2); color: #fff; }
.dx-btn-danger { background: var(--bad); color: #fff; }
.dx-btn-danger:hover { background: #8E3235; color: #fff; }

/* ===== Timeline ===== */
.dx-timeline-wrap { padding: 0 6px; }
.dx-timeline-wrap .dx-h2 { margin-bottom: 18px; }
.dx-timeline { list-style: none; margin: 0; padding: 0 0 0 32px; position: relative; }
.dx-timeline::before { content: ''; position: absolute; left: 10px; top: 8px; bottom: 8px; width: 1px; background: var(--line); }
.dx-timeline li { position: relative; padding-bottom: 22px; }
.dx-timeline li:last-child { padding-bottom: 0; }
.dx-dot { position: absolute; left: -32px; top: 0; width: 21px; height: 21px; border-radius: 50%; background: #fff; border: 1.5px solid var(--line); display: flex; align-items: center; justify-content: center; font-size: .65rem; color: #fff; box-shadow: 0 0 0 4px #fff; }
.is-done .dx-dot { background: var(--ink); border-color: var(--ink); }
.is-current .dx-dot { border-color: var(--accent); }
.is-current .dx-dot::after { content: ''; width: 8px; height: 8px; border-radius: 50%; background: var(--accent); }
.is-rejected .dx-dot { background: var(--bad); border-color: var(--bad); }
.dx-step-title { font-weight: 600; font-size: .92rem; }
.is-pending .dx-step-title { color: var(--muted); font-weight: 500; }
.dx-step-note { font-size: .8rem; color: var(--muted); margin-top: 1px; }
.is-current .dx-step-note { color: var(--accent); font-weight: 600; }
.is-rejected .dx-step-note { color: var(--bad); }

/* ===== Modal (dipindah ke <body> lewat JS; z-index bawaan Bootstrap sudah cukup) ===== */
.dx-modal .modal-content { border: 0; border-radius: 18px; overflow: hidden; box-shadow: 0 34px 80px -24px rgba(35,31,92,.55); }
.dx-modal .modal-header { padding: 26px 30px 6px; border: 0; }
.dx-modal .modal-title { font-size: 1.6rem; font-weight: 500; color: var(--ink); }
.dx-modal .modal-body { padding: 14px 30px 8px; }
.dx-modal .modal-footer { padding: 18px 30px 26px; border: 0; gap: 10px; }
.dx-modal-amount { border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); padding: 16px 0 18px; margin-bottom: 22px; }
.dx-on-light { color: var(--muted); font-size: .84rem; }
.dx-amount-sm { font-size: 2.1rem; color: var(--ink); }
.dx-amount-sm span { color: var(--accent); }
.dx-modal-note { color: #433F5C; line-height: 1.65; margin-bottom: 20px; }
.dx-field-label { display: block; font-weight: 600; font-size: .9rem; margin-bottom: 8px; }
.dx-opt { font-weight: 400; color: var(--muted); }
.dx-req { font-weight: 500; color: var(--bad); font-size: .82rem; margin-left: 4px; }
.dx-input { width: 100%; border: 1px solid var(--line); border-radius: 12px; padding: 12px 14px; font: inherit; background: var(--tint); resize: vertical; transition: border-color .2s, box-shadow .2s, background .2s; }
.dx-input:focus { outline: 0; background: #fff; border-color: var(--accent); box-shadow: 0 0 0 3px rgba(91,75,196,.2); }
.dx-check { display: flex; gap: 12px; align-items: flex-start; margin-top: 18px; padding: 14px 16px; border: 1px solid var(--line); border-radius: 12px; cursor: pointer; font-size: .9rem; line-height: 1.5; }
.dx-check input { margin-top: 3px; accent-color: var(--ink); width: 16px; height: 16px; flex-shrink: 0; }
.modal-backdrop.show { opacity: .55; }

/* ===== Responsif ===== */
@media (max-width: 991.98px) {
    .dx-rail { position: static; }
}
@media (max-width: 767.98px) {
    .dx-sec { grid-template-columns: 1fr; gap: 12px; padding: 24px 0; }
}
@media (max-width: 575.98px) {
    .dx-title { font-size: 1.9rem; }
    .dx-dl { grid-template-columns: 1fr; gap: 16px; }
    .dx-amount { font-size: 2.2rem; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // FIX modal: pindahkan ke <body> supaya sejajar dengan backdrop Bootstrap
    // dan tidak terjebak di stacking context layout (sidebar/transform/z-index).
    document.querySelectorAll('.dx-modal').forEach(function (modal) {
        document.body.appendChild(modal);
    });

    var checkbox = document.getElementById('setuju_direktur_full');
    var btn = document.getElementById('btnSetujuiFull');
    if (checkbox && btn) {
        checkbox.addEventListener('change', function () { btn.disabled = !this.checked; });
    }

    document.querySelectorAll('.dx-modal').forEach(function (modal) {
        modal.addEventListener('shown.bs.modal', function () {
            var input = modal.querySelector('textarea');
            if (input) input.focus();
        });
        modal.addEventListener('hidden.bs.modal', function () {
            if (checkbox && btn && modal.contains(checkbox)) {
                checkbox.checked = false;
                btn.disabled = true;
            }
        });
    });
});
</script>
@endsection
