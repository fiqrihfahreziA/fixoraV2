<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Formulir Perencanaan Pengadaan</title>
    <style>
        /* ========================================= */
        /* PENGATURAN KERTAS A4 PORTRAIT             */
        /* ========================================= */
        @page {
            size: A4 portrait;
            margin: 10mm 10mm; /* area cetak 190mm x 277mm */
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.35;
            color: #1a1a1a;
            background: #fff;
        }

        /* === FRAME UTAMA === */
        .main-frame {
            width: 190mm;          /* lebar A4 portrait (210mm) - margin kiri/kanan (2 x 10mm) */
            max-width: 190mm;
            margin: 0 auto;
            padding: 0;
            background: #fff;
            position: relative;
        }

        /* Tampilan di layar: tampak seperti lembar kertas */
        @media screen {
            body { background: #e9e9ec; padding: 24px 0 90px; }
            .main-frame {
                padding: 10mm;
                width: 210mm;
                max-width: 210mm;
                box-shadow: 0 4px 24px rgba(0,0,0,.15);
            }
        }

        /* === HEADER === */
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
        }
        .header-logo {
            width: 64px;
            flex-shrink: 0;
        }
        .header-logo img {
            width: 60px;
            height: auto;
            display: block;
        }
        .header-center {
            flex: 1;
            text-align: center;
            padding: 0 6px;
        }
        .header-title {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .header-sub {
            font-size: 11.5pt;
            font-weight: bold;
            letter-spacing: 0.2px;
        }
        .header-address {
            font-size: 8.5pt;
            letter-spacing: 0.1px;
        }
        .header-doc-title {
            margin-top: 6px;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            letter-spacing: 0.3px;
        }

        /* === NOMOR SURAT === */
        .doc-number {
            text-align: center;
            font-size: 10.5pt;
            font-weight: bold;
            margin: 8px 0 10px 0;
            letter-spacing: 0.3px;
        }

        /* === SECTION TITLE === */
        .section-title {
            font-weight: bold;
            font-size: 10pt;
            margin-top: 10px;
            margin-bottom: 4px;
            text-decoration: underline;
            letter-spacing: 0.2px;
            break-after: avoid;
            page-break-after: avoid;
        }

        /* === TABEL TANPA BORDER (IDENTITAS) === */
        .table-no-border {
            width: 100%;
            border-collapse: collapse;
        }
        .table-no-border td {
            border: none;
            padding: 1px 4px;
            vertical-align: top;
            font-size: 10pt;
        }
        .table-no-border .label {
            font-weight: bold;
            width: 150px;
        }
        .table-no-border .colon {
            width: 10px;
            text-align: center;
        }

        /* === TABEL BORDER (URAIAN KEBUTUHAN) === */
        .table-bordered {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0;
        }
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #000;
            padding: 4px 5px;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: anywhere;
        }
        .table-bordered th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 9pt;
        }
        .table-bordered td {
            font-size: 9.5pt;
        }
        .table-bordered input {
            border: none;
            background: transparent;
            width: 100%;
            text-align: center;
            font-family: 'Times New Roman', Times, serif;
            font-size: 9.5pt;
        }
        .table-bordered input:focus {
            outline: none;
            background: #f8f9fa;
        }
        .table-bordered .input-harga { text-align: right; }
        .table-bordered .input-jumlah { text-align: center; }

        /* === TABEL VERIFIKASI BERJENJANG === */
        .table-verification {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0;
            table-layout: fixed;
            break-inside: avoid;
            page-break-inside: avoid;
        }
        .table-verification th,
        .table-verification td {
            border: 1px solid #000;
            padding: 4px 3px;
            vertical-align: top;
            text-align: center;
            font-size: 8.5pt;
        }
        .table-verification th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 9pt;
        }

        /* Isi checklist */
        .table-verification .verif-options {
            text-align: left;
            padding-left: 5px;
            font-size: 8pt;
            line-height: 1.5;
        }
        .table-verification .verif-options div {
            margin-bottom: 2px;
            display: flex;
            align-items: flex-start;
            gap: 3px;
        }
        .table-verification .note-cell {
            font-size: 8pt;
            text-align: left;
            padding: 3px;
            height: 42px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: anywhere;
        }

        /* Kotak tanda tangan */
        .sign-box {
            text-align: center;
            padding-top: 6px;
            padding-bottom: 6px;
            height: auto;
            min-height: 100px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
        }
        .sign-title {
            font-size: 7.5pt;
            font-weight: bold;
            color: #333;
            margin-bottom: 3px;
        }
        .sign-name {
            font-weight: bold;
            font-size: 8.5pt;
            text-decoration: underline;
            line-height: 1.2;
            margin-bottom: 2px;
            word-wrap: break-word;
            overflow-wrap: anywhere;
        }
        .sign-nip {
            font-size: 7.5pt;
            color: #333;
            margin-bottom: 3px;
            word-wrap: break-word;
            overflow-wrap: anywhere;
        }
        .sign-qr img {
            display: block;
            margin: 0 auto;
            width: 42px;
            height: 42px;
        }
        .sign-date {
            font-size: 7pt;
            color: #333;
            margin-top: 3px;
        }

        /* === CHECKLIST === */
        .checklist {
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 2px;
            font-size: 9.5pt;
        }
        .checklist-box {
            display: inline-block;
            flex-shrink: 0;
            width: 12px;
            height: 12px;
            border: 1.5px solid #000;
            margin-right: 2px;
            text-align: center;
            line-height: 11px;
            font-size: 8pt;
            font-weight: bold;
        }
        .checklist-box.checked {
            background-color: transparent;
            color: #000;
        }

        /* === KEPUTUSAN MANAJEMEN BOX === */
        .border-box {
            border: 1px solid #000;
            padding: 10px 12px;
            background-color: #fff;
        }
        .border-dashed {
            border: 1px dashed #aaa;
            padding: 6px 8px;
            min-height: 35px;
            background: #fdfdfd;
        }
        .blok-keputusan {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        /* === TEXT UTILITY === */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .text-bold { font-weight: bold; }

        /* === LAMPIRAN & FOOTER === */
        .lampiran-list {
            padding-left: 18px;
            line-height: 1.5;
        }
        .footer-print {
            margin-top: 15px;
            text-align: center;
            font-size: 8.5pt;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 6px;
        }

        /* === PRINT STYLES === */
        @media print {
            .no-print { display: none !important; }

            html, body {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                background: #fff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            .main-frame {
                border: none;
                padding: 0;
                box-shadow: none;
                /* Jangan pakai width:100%, supaya tetap di tengah */
                width: 190mm !important;
                max-width: 190mm !important;
                margin-left: auto !important;
                margin-right: auto !important;
            }

            tr { page-break-inside: avoid; }
        }

        /* === TOMBOL CETAK === */
        .no-print {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 999;
            display: flex;
            gap: 8px;
        }
        .btn-print {
            padding: 8px 20px;
            background: #1a3c6e;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .btn-print:hover { background: #0f2a4f; }
        .btn-close {
            padding: 8px 16px;
            background: #a02828;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .btn-close:hover { background: #7a1e1e; }
    </style>
</head>
<body>

<!-- ===== FRAME UTAMA ===== -->
<div class="main-frame">

    <!-- ===== HEADER ===== -->
    <div class="header">
        <div class="header-top">
            <div class="header-logo">
                <img src="{{ asset('gambar/trunojoyoo.png') }}" alt="Logo Kabupaten">
            </div>
            <div class="header-center">
                <div class="header-title">PEMERINTAH KABUPATEN SAMPANG</div>
                <div class="header-sub">RSUD dr. MOHAMMAD ZYN</div>
                <div class="header-sub">KABUPATEN SAMPANG</div>
                <div class="header-address">Jalan Rajawali No. 10, Sampang (69214) Telp. (0323) 323956</div>
                <div class="header-address">Email : rsud.dr.mohammad.zyn@sampangkab.go.id &nbsp;|&nbsp; website : rsud.sampangkab.go.id</div>
            </div>
            <div class="header-logo">
                <img src="{{ asset('gambar/rsmz.png') }}" alt="Logo RSUD">
            </div>
        </div>
        <!-- ===== JUDUL DOKUMEN ===== -->
        <div class="header-doc-title">
            FORMULIR PERENCANAAN PENGADAAN / PEMELIHARAAN BARANG
        </div>
    </div>

    <!-- ===== NOMOR SURAT ===== -->
    <div class="doc-number">
        NO. SURAT : {{ $pengajuan->no_surat ?? '..... / ..... / .....' }}
    </div>

    <!-- ===== A. IDENTITAS PENGUSUL ===== -->
    <div class="section-title">A. IDENTITAS PENGUSUL</div>
    <table class="table-no-border">
        <tr>
            <td class="label">Ruangan / Instalasi</td>
            <td class="colon">:</td>
            <td class="value">{{ $pengajuan->karyawan->ruangan ?? '________________________' }}</td>
        </tr>
        <tr>
            <td class="label">Nama Pengusul</td>
            <td class="colon">:</td>
            <td class="value">{{ $pengajuan->karyawan->nama ?? '________________________' }}</td>
        </tr>
        <tr>
            <td class="label">Jabatan</td>
            <td class="colon">:</td>
            <td class="value">{{ $pengajuan->karyawan->jabatan ?? '________________________' }}</td>
        </tr>
        <tr>
            <td class="label">NIP / NIK</td>
            <td class="colon">:</td>
            <td class="value">{{ $pengajuan->karyawan->nip ?? '________________________' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Pengajuan</td>
            <td class="colon">:</td>
            <td class="value">{{ $pengajuan->tanggal_pengajuan ? date('d F Y', strtotime($pengajuan->tanggal_pengajuan)) : '________________________' }}</td>
        </tr>
        <tr>
            <td class="label">Tahun Anggaran</td>
            <td class="colon">:</td>
            <td class="value">{{ $pengajuan->tahun_anggaran ?? date('Y') }}</td>
        </tr>
    </table>

    <!-- ===== B. DASAR USULAN ===== -->
    <div class="section-title">B. DASAR USULAN</div>
    <div style="margin: 2px 0 4px 0;">
        @php
            $dasar = explode(',', $pengajuan->dasar_usulan ?? '');
            $dasar = array_map('trim', $dasar);
        @endphp
        <span class="checklist">
            <span class="checklist-box {{ in_array('Program Kerja', $dasar) ? 'checked' : '' }}">{{ in_array('Program Kerja', $dasar) ? '✓' : '' }}</span> Program Kerja
        </span>
        <span class="checklist">
            <span class="checklist-box {{ in_array('Kebutuhan Operasional', $dasar) ? 'checked' : '' }}">{{ in_array('Kebutuhan Operasional', $dasar) ? '✓' : '' }}</span> Kebutuhan Operasional
        </span>
        <span class="checklist">
            <span class="checklist-box {{ in_array('Penggantian Barang Rusak', $dasar) ? 'checked' : '' }}">{{ in_array('Penggantian Barang Rusak', $dasar) ? '✓' : '' }}</span> Penggantian Barang Rusak
        </span>
        <span class="checklist">
            <span class="checklist-box {{ in_array('Penambahan Kapasitas Pelayanan', $dasar) ? 'checked' : '' }}">{{ in_array('Penambahan Kapasitas Pelayanan', $dasar) ? '✓' : '' }}</span> Penambahan Kapasitas Pelayanan
        </span><br>
        <span class="checklist">
            <span class="checklist-box {{ in_array('Pemenuhan Standar Akreditasi', $dasar) ? 'checked' : '' }}">{{ in_array('Pemenuhan Standar Akreditasi', $dasar) ? '✓' : '' }}</span> Pemenuhan Standar Akreditasi
        </span>
        <span class="checklist">
            <span class="checklist-box {{ in_array('Keselamatan Pasien', $dasar) ? 'checked' : '' }}">{{ in_array('Keselamatan Pasien', $dasar) ? '✓' : '' }}</span> Keselamatan Pasien
        </span>
        <span class="checklist">
            <span class="checklist-box {{ in_array('Lainnya', $dasar) ? 'checked' : '' }}">{{ in_array('Lainnya', $dasar) ? '✓' : '' }}</span> Lainnya : {{ $pengajuan->dasar_lainnya ?? '' }}
        </span>
    </div>

    <!-- ===== C. URAIAN KEBUTUHAN ===== -->
    <div class="section-title">C. URAIAN KEBUTUHAN</div>
    <table class="table-bordered" id="table-kebutuhan">
        <thead>
            <tr>
                <th style="width: 28px;">No</th>
                <th style="width: 125px;">Nama Barang / Pemeliharaan</th>
                <th style="width: 115px;">Spesifikasi Teknis</th>
                <th style="width: 48px;">Satuan</th>
                <th style="width: 48px;">Jumlah</th>
                <th style="width: 100px;">Perkiraan Harga Satuan (Rp)</th>
                <th style="width: 100px;">Total (Rp)</th>
            </tr>
        </thead>
        <tbody id="tbody-kebutuhan">
            @if(isset($pengajuan->items) && count($pengajuan->items) > 0)
                @foreach($pengajuan->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->spesifikasi }}</td>
                    <td class="text-center">{{ $item->satuan }}</td>
                    <td class="text-center">
                        <input type="number" class="input-jumlah" value="{{ $item->jumlah }}" 
                               data-index="{{ $index }}" onchange="hitungTotalBaris(this)">
                    </td>
                    <td>
                        <input type="text" class="input-harga" value="{{ number_format($item->harga_satuan, 0, ',', '.') }}" 
                               data-index="{{ $index }}" onchange="hitungTotalBaris(this)">
                    </td>
                    <td class="text-right total-barang">{{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            @else
                @for($i = 0; $i < 5; $i++)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="text-center">
                        <input type="number" class="input-jumlah" value="" data-index="{{ $i }}" onchange="hitungTotalBaris(this)">
                    </td>
                    <td>
                        <input type="text" class="input-harga" value="" data-index="{{ $i }}" onchange="hitungTotalBaris(this)">
                    </td>
                    <td class="text-right total-barang">0</td>
                </tr>
                @endfor
            @endif
            <tr>
                <td colspan="6" class="text-right text-bold" style="padding-right: 8px;">TOTAL</td>
                <td class="text-right text-bold" id="grand-total">
                    {{ isset($pengajuan->total) ? number_format($pengajuan->total, 0, ',', '.') : '0' }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- ===== D. ALASAN DAN JUSTIFIKASI ===== -->
    <div class="section-title">D. ALASAN DAN JUSTIFIKASI PENGADAAN</div>
    <div class="border-dashed">
        {{ $pengajuan->alasan_justifikasi ?? '..............................................................................................................' }}
    </div>

    <!-- ===== E. MANFAAT ===== -->
    <div class="section-title">E. MANFAAT YANG DIHARAPKAN</div>
    <div class="border-dashed">
        {{ $pengajuan->manfaat ?? '1. .............................................................................' }}
    </div>

    <!-- ===== F. KONDISI BARANG YANG ADA ===== -->
    @if(isset($pengajuan->is_penggantian) && $pengajuan->is_penggantian)
    <div class="section-title">F. KONDISI BARANG YANG ADA (Jika Penggantian)</div>
    <table class="table-bordered">
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th style="width: 70px;">Jumlah</th>
                <th style="width: 110px;">Tahun Perolehan</th>
                <th>Kondisi</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($pengajuan->barang_lama) && count($pengajuan->barang_lama) > 0)
                @foreach($pengajuan->barang_lama as $item)
                <tr>
                    <td>{{ $item->nama }}</td>
                    <td class="text-center">{{ $item->jumlah }}</td>
                    <td class="text-center">{{ $item->tahun }}</td>
                    <td>{{ $item->kondisi }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            @endif
        </tbody>
    </table>
    @endif

    <!-- ===== G. DAMPAK ===== -->
    <div class="section-title">G. DAMPAK APABILA TIDAK DILAKSANAKAN</div>
    <div class="border-dashed">
        {{ $pengajuan->dampak ?? '..............................................................................................................' }}
    </div>

    <!-- ===== H. DOKUMEN PENDUKUNG ===== -->
    <div class="section-title">H. DOKUMEN PENDUKUNG</div>
    <div style="margin: 2px 0 4px 0;">
        @php
            $dataKerusakan = $pengajuan->data_kerusakan ?? '';
            $isAda = !empty($dataKerusakan) && $dataKerusakan != '' && $dataKerusakan != 'null';
        @endphp
        <span class="checklist">
            <span class="checklist-box {{ $isAda ? 'checked' : '' }}">
                {{ $isAda ? '✓' : '' }}
            </span> Ada
        </span>
        <span class="checklist">
            <span class="checklist-box {{ !$isAda ? 'checked' : '' }}">
                {{ !$isAda ? '✓' : '' }}
            </span> Tidak ada
        </span>
    </div>

    <!-- ===== I. VERIFIKASI BERJENJANG ===== -->
    <div class="section-title">I. VERIFIKASI BERJENJANG</div>
    <table class="table-verification">
        <thead>
            <tr>
                <th colspan="2" style="border-right: 2px solid #000;">Telaah Unit Terkait</th>
                <th colspan="4">Verifikasi Berjenjang</th>
            </tr>
            <tr>
                <th style="width: 14.2%;">Penerima</th>
                <th style="width: 14.2%; border-right: 2px solid #000;">Kabid</th>
                <th style="width: 17.9%;">PPK</th>
                <th style="width: 17.9%;">Keuangan</th>
                <th style="width: 17.9%;">KPA</th>
                <th style="width: 17.9%;">Direktur</th>
            </tr>
        </thead>
        <tbody>
            <!-- Baris 1: Checklist -->
            <tr>
                <!-- Kolom 1: Penerima -->
                <td class="verif-options">
                    @php $status_unit = $pengajuan->log_status_penerima ?? ''; @endphp
                    <div><span class="checklist-box {{ $status_unit == 'disetujui_koordinator' ? 'checked' : '' }}">{{ $status_unit == 'disetujui_koordinator' ? '✓' : '' }}</span> Layak</div>
                    <div><span class="checklist-box {{ $status_unit == 'revisi' ? 'checked' : '' }}">{{ $status_unit == 'revisi' ? '✓' : '' }}</span> Survei</div>
                    <div><span class="checklist-box {{ $status_unit == 'ditolak_penerima' ? 'checked' : '' }}">{{ $status_unit == 'ditolak_penerima' ? '✓' : '' }}</span> Tolak</div>
                </td>
                <!-- Kolom 2: Kabid -->
                <td class="verif-options" style="border-right: 2px solid #000;">
                    @php $status_atasan = $pengajuan->log_status_atasan ?? ''; @endphp
                    <div><span class="checklist-box {{ $status_atasan == 'disetujui_kabid' ? 'checked' : '' }}">{{ $status_atasan == 'disetujui_kabid' ? '✓' : '' }}</span> Setuju</div>
                    <div><span class="checklist-box {{ $status_atasan == 'dipertimbangkan' ? 'checked' : '' }}">{{ $status_atasan == 'dipertimbangkan' ? '✓' : '' }}</span> Pertimbang</div>
                    <div><span class="checklist-box {{ $status_atasan == 'ditolak_kabid' ? 'checked' : '' }}">{{ $status_atasan == 'ditolak_kabid' ? '✓' : '' }}</span> Tolak</div>
                </td>
                <!-- Kolom 3: PPK -->
                <td class="verif-options">
                    @php $status_ppk = $pengajuan->log_ppk ?? ''; @endphp
                    <div><span class="checklist-box {{ $status_ppk == 'disetujui_ppk' ? 'checked' : '' }}">{{ $status_ppk == 'Disetujui oleh PPK' ? '✓' : '' }}</span> Setuju</div>
                    <div><span class="checklist-box {{ $status_ppk == 'dipertimbangkan_ppk' ? 'checked' : '' }}">{{ $status_ppk == 'dipertimbangkan' ? '✓' : '' }}</span> Pertimbang</div>
                    <div><span class="checklist-box {{ $status_ppk == 'ditolak_ppk' ? 'checked' : '' }}">{{ $status_ppk == 'ditolak_ppk' ? '✓' : '' }}</span> Tolak</div>
                </td>
                <!-- Kolom 4: Keuangan -->
                <td class="verif-options">
                    @php $status_keuangan = $pengajuan->log_status_keuangan ?? ''; @endphp
                    <div><span class="checklist-box {{ $status_keuangan == 'disetujui_keuangan' ? 'checked' : '' }}">{{ $status_keuangan == 'disetujui_keuangan' ? '✓' : '' }}</span> Tersedia</div>
                    <div><span class="checklist-box {{ $status_keuangan == 'sebagian' ? 'checked' : '' }}">{{ $status_keuangan == 'sebagian' ? '✓' : '' }}</span> Sebagian</div>
                    <div><span class="checklist-box {{ $status_keuangan == 'tidak_tersedia' ? 'checked' : '' }}">{{ $status_keuangan == 'tidak_tersedia' ? '✓' : '' }}</span> Kosong</div>
                </td>
                <!-- Kolom 5: KPA -->
                <td class="verif-options">
                    @php $status_kpa = $pengajuan->log_kpa ?? ''; @endphp
                    <div><span class="checklist-box {{ $status_kpa == 'disetujui_kpa' ? 'checked' : '' }}">{{ $status_kpa == 'Disetujui oleh KPA' ? '✓' : '' }}</span> Setuju</div>
                    <div><span class="checklist-box {{ $status_kpa == 'dipertimbangkan_kpa' ? 'checked' : '' }}">{{ $status_kpa == 'dipertimbangkan_kpa' ? '✓' : '' }}</span> Pertimbang</div>
                    <div><span class="checklist-box {{ $status_kpa == 'ditolak_kpa' ? 'checked' : '' }}">{{ $status_kpa == 'ditolak_kpa' ? '✓' : '' }}</span> Tolak</div>
                </td>
                <!-- Kolom 6: Direktur -->
                <td class="verif-options">
                    @php $status_direktur = $pengajuan->log_status_direktur ?? ''; @endphp
                    <div><span class="checklist-box {{ $status_direktur == 'disetujui' ? 'checked' : '' }}">{{ $status_direktur == 'disetujui' ? '✓' : '' }}</span> Setuju</div>
                    <div><span class="checklist-box {{ $status_direktur == 'disetujui sebagian' ? 'checked' : '' }}">{{ $status_direktur == 'disetujui sebagian' ? '✓' : '' }}</span> Sebagian</div>
                    <div><span class="checklist-box {{ $status_direktur == 'ditolak_direktur' ? 'checked' : '' }}">{{ $status_direktur == 'ditolak_direktur' ? '✓' : '' }}</span> Tolak</div>
                </td>
            </tr>

            <!-- Baris 2: Catatan -->
            <tr>
                <td class="note-cell"><strong>Note :</strong><br> {{ $pengajuan->catatan_unit ?? '' }}</td>
                <td class="note-cell" style="border-right: 2px solid #000;"><strong>Note :</strong><br> {{ $pengajuan->catatan_bidang ?? '' }}</td>
                <td class="note-cell"><strong>Note :</strong><br> {{ $pengajuan->catatan_ppk ?? '' }}</td>
                <td class="note-cell"><strong>Note :</strong><br> {{ $pengajuan->catatan_keuangan ?? '' }}</td>
                <td class="note-cell"><strong>Note :</strong><br> {{ $pengajuan->catatan_kpa ?? '' }}</td>
                <td class="note-cell"><strong>Note :</strong><br> {{ $pengajuan->catatan_direktur ?? '' }}</td>
            </tr>

            <!-- Baris 3: NAMA & PARAF -->
            <tr>
                <!-- 1. PENERIMA -->
                <td>
                    <div class="sign-box">
                        <div class="sign-title">Nama & Paraf:</div>
                        <div class="sign-qr">
                            @if($pengajuan->penerima && $pengajuan->penerima->ttd)
                                @php
                                    $tgl = $pengajuan->diterima_at ? \Carbon\Carbon::parse($pengajuan->diterima_at) : now();
                                    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode("Ditandatangani oleh: " . $pengajuan->penerima->nama . "\nTanggal: " . $tgl->format('d-m-Y'));
                                @endphp
                                <img src="{{ $qrUrl }}" alt="QR Code" />
                            @else
                                <div style="font-size: 8pt; font-style: italic; color: #999;">(TTD)</div>
                            @endif
                        </div>
                        <div class="sign-name">{{ $pengajuan->penerima->nama ?? '' }}</div>
                        <div class="sign-nip">NIP. {{ $pengajuan->penerima->nip ?? '' }}</div>
                         <div class="sign-date" style="font-size: 7pt; color: #333; margin-top: 2px;">
                            @if($pengajuan->diterima_at)
                                {{ \Carbon\Carbon::parse($pengajuan->diterima_at)->translatedFormat('l, d F Y - H:i') }}
                            @endif
                        </div>
                    </div>
                </td>

                <!-- 2. KABID -->
                <td style="border-right: 2px solid #000;">
                    <div class="sign-box">
                        <div class="sign-title">Nama & Paraf:</div>
                        <div class="sign-qr">
                            @if($pengajuan->atasan && $pengajuan->atasan->ttd)
                                @php
                                    $tgl = $pengajuan->disetujui_kabid_at ? \Carbon\Carbon::parse($pengajuan->disetujui_kabid_at) : now();
                                    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode("Ditandatangani oleh: " . $pengajuan->atasan->nama . "\nTanggal: " . $tgl->format('d-m-Y'));
                                @endphp
                                <img src="{{ $qrUrl }}" alt="QR Code" />
                            @else
                                <div style="font-size: 8pt; font-style: italic; color: #999;">(TTD)</div>
                            @endif
                        </div>
                        <div class="sign-name">{{ $pengajuan->atasan->nama ?? '' }}</div>
                        <div class="sign-nip">NIP. {{ $pengajuan->atasan->nip ?? '' }}</div>
                        <div class="sign-date" style="font-size: 7pt; color: #333; margin-top: 2px;">
                            @if($pengajuan->disetujui_kabid_at)
                                {{ \Carbon\Carbon::parse($pengajuan->disetujui_kabid_at)->translatedFormat('l, d F Y - H:i') }}
                            @endif
                        </div>
                    </div>
                </td>

                <!-- 3. PPK -->
                <td>
                    <div class="sign-box">
                        <div class="sign-title">Nama & Paraf:</div>
                        <div class="sign-qr">
                            @if($pengajuan->ppk && $pengajuan->ppk->ttd)
                                @php
                                    $tgl = $pengajuan->disetujui_ppk_at ? \Carbon\Carbon::parse($pengajuan->disetujui_ppk_at) : now();
                                    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode("Ditandatangani oleh: " . $pengajuan->ppk->nama . "\nTanggal: " . $tgl->format('d-m-Y'));
                                @endphp
                                <img src="{{ $qrUrl }}" alt="QR Code" />
                            @else
                                <div style="font-size: 8pt; font-style: italic; color: #999;">(TTD)</div>
                            @endif
                        </div>
                        <div class="sign-name">{{ $pengajuan->ppk->nama ?? '' }}</div>
                        <div class="sign-nip">NIP. {{ $pengajuan->ppk->nip ?? '' }}</div>
                        <div class="sign-date" style="font-size: 7pt; color: #333; margin-top: 2px;">
                            @if($pengajuan->ppk_at)
                                {{ \Carbon\Carbon::parse($pengajuan->ppk_at)->translatedFormat('l, d F Y - H:i') }}
                            @endif
                        </div>
                    </div>
                </td>

                <!-- 4. KEUANGAN -->
                <td>
                    <div class="sign-box">
                        <div class="sign-title">Nama & Paraf:</div>
                        <div class="sign-qr">
                            @if($pengajuan->keuangan && $pengajuan->keuangan->ttd)
                                @php
                                    $tgl = $pengajuan->verified_at ? \Carbon\Carbon::parse($pengajuan->verified_at) : now();
                                    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode("Ditandatangani oleh: " . $pengajuan->keuangan->nama . "\nTanggal: " . $tgl->format('d-m-Y'));
                                @endphp
                                <img src="{{ $qrUrl }}" alt="QR Code" />
                            @else
                                <div style="font-size: 8pt; font-style: italic; color: #999;">(TTD)</div>
                            @endif
                        </div>
                        <div class="sign-name">{{ $pengajuan->keuangan->nama ?? '' }}</div>
                        <div class="sign-nip">NIP. {{ $pengajuan->keuangan->nip ?? '' }}</div>
                        <div class="sign-date" style="font-size: 7pt; color: #333; margin-top: 2px;">
                            @if($pengajuan->disetujui_keuangan_at)
                                {{ \Carbon\Carbon::parse($pengajuan->disetujui_keuangan_at)->translatedFormat('l, d F Y - H:i') }}
                            @endif
                        </div>
                    </div>
                </td>

                <!-- 5. KPA -->
                <td>
                    <div class="sign-box">
                        <div class="sign-title">Nama & Paraf:</div>
                        <div class="sign-qr">
                            @if($pengajuan->kpaa && $pengajuan->kpaa->ttd)
                                @php
                                    $tgl = $pengajuan->disetujui_kpa_at ? \Carbon\Carbon::parse($pengajuan->kpa) : now();
                                    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode("Ditandatangani oleh: " . $pengajuan->kpaa->nama . "\nTanggal: " . $tgl->format('d-m-Y'));
                                @endphp
                                <img src="{{ $qrUrl }}" alt="QR Code" />
                            @else
                                <div style="font-size: 8pt; font-style: italic; color: #999;">(TTD)</div>
                            @endif
                        </div>
                        <div class="sign-name">{{ $pengajuan->kpaa->nama ?? '' }}</div>
                        <div class="sign-nip">NIP. {{ $pengajuan->kpaa->nip ?? '' }}</div>
                        <div class="sign-date" style="font-size: 7pt; color: #333; margin-top: 2px;">
                            @if($pengajuan->kpa)
                                {{ \Carbon\Carbon::parse($pengajuan->kpa)->translatedFormat('l, d F Y - H:i') }}
                            @endif
                        </div>
                    </div>
                </td>

                <!-- 6. DIREKTUR -->
                <td>
                    <div class="sign-box">
                        <div class="sign-title">Nama & Paraf:</div>
                        <div class="sign-qr">
                            @if($pengajuan->direktur && $pengajuan->direktur->ttd)
                                @php
                                    $tgl = $pengajuan->disetujui_direktur_at ? \Carbon\Carbon::parse($pengajuan->disetujui_direktur_at) : now();
                                    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode("Ditandatangani oleh: " . $pengajuan->direktur->nama . "\nTanggal: " . $tgl->format('d-m-Y'));
                                @endphp
                                <img src="{{ $qrUrl }}" alt="QR Code" />
                            @else
                                <div style="font-size: 8pt; font-style: italic; color: #999;">(TTD)</div>
                            @endif
                        </div>
                        <div class="sign-name">{{ $pengajuan->direktur->nama ?? '' }}</div>
                        <div class="sign-nip">NIP. {{ $pengajuan->direktur->nip ?? '' }}</div>
                        <div class="sign-date" style="font-size: 7pt; color: #333; margin-top: 2px;">
                            @if($pengajuan->disetujui_direktur_at)
                                {{ \Carbon\Carbon::parse($pengajuan->disetujui_direktur_at)->translatedFormat('l, d F Y ') }}
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- ===== J. KEPUTUSAN MANAJEMEN & TTD DIREKTUR ===== -->
    <div class="blok-keputusan" style="margin-top: 10px; display: flex; justify-content: space-between; gap: 16px;">

        <!-- KIRI: KEPUTUSAN MANAJEMEN -->
        <div style="width: 63%;">
            <div class="section-title" style="margin-top: 0;">J. KEPUTUSAN DIREKTUR</div>
            <div class="border-box" style="padding: 8px 10px;">
                <p style="margin-bottom: 6px; font-size: 9.5pt;"><strong>Direksi memutuskan:</strong></p>
                <div style="margin-bottom: 8px; line-height: 1.5; font-size: 9pt;">
                    @php $keputusan = $pengajuan->log_status_direktur ?? ''; @endphp
                    <span class="checklist" style="margin-right: 8px;">
                        <span class="checklist-box {{ $keputusan == 'disetujui' ? 'checked' : '' }}">{{ $keputusan == 'disetujui' ? '✓' : '' }}</span> Disetujui
                    </span>
                    <span class="checklist" style="margin-right: 8px;">
                        <span class="checklist-box {{ $keputusan == 'disetujui sebagian' ? 'checked' : '' }}">{{ $keputusan == 'disetujui sebagian' ? '✓' : '' }}</span> Disetujui Sebagian
                    </span>
                    <span class="checklist" style="margin-right: 8px;">
                        <span class="checklist-box {{ $keputusan == 'ditunda' ? 'checked' : '' }}">{{ $keputusan == 'ditunda' ? '✓' : '' }}</span> Ditunda
                    </span>
                    <span class="checklist">
                        <span class="checklist-box {{ $keputusan == 'ditolak' ? 'checked' : '' }}">{{ $keputusan == 'ditolak' ? '✓' : '' }}</span> Ditolak
                    </span>
                </div>
                <div style="margin-bottom: 6px; font-size: 9pt;">
                    <strong>Nominal yang Disetujui :</strong> Rp {{ isset($pengajuan->total_disetujui_direktur) ? number_format($pengajuan->total_disetujui_direktur, 0, ',', '.') : '____________________________' }}
                </div>
                <div>
                    <strong style="font-size: 9pt;">Catatan Manajemen:</strong><br>
                    <div class="border-dashed" style="min-height: 30px; margin-top: 2px; font-size: 8.5pt;">
                        {{ $pengajuan->catatan_direktur ?? '' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- KANAN: TANDA TANGAN DIREKTUR -->
        <div style="width: 37%; text-align: center; padding-top: 20px;">
            <div style="font-size: 9.5pt; margin-bottom: 2px;">
                Sampang, 
                @if($pengajuan->disetujui_direktur_at)
                    {{ \Carbon\Carbon::parse($pengajuan->disetujui_direktur_at)->translatedFormat('d F Y') }}
                @else
                    ......................... {{ date('Y') }}
                @endif
            </div>

            <div style="font-size: 9.5pt; font-weight: bold; margin-bottom: 2px;">Direktur RSUD dr. Mohammad Zyn</div>
            <div style="font-size: 9.5pt; font-weight: bold; margin-bottom: 10px;">Kabupaten Sampang</div>
            <!-- QR Code Direktur -->
            <div style="margin-top: 10px;">
                @if($pengajuan->direktur && $pengajuan->direktur->ttd)
                    @php
                        $tgl = $pengajuan->disetujui_direktur_at ? \Carbon\Carbon::parse($pengajuan->disetujui_direktur_at) : now();
                        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode("Ditandatangani oleh: " . $pengajuan->direktur->nama . "\nTanggal: " . $tgl->format('d-m-Y'));
                    @endphp
                    <img src="{{ $qrUrl }}" alt="QR Code" style="width: 55px; height: 55px;" />
                @endif
            </div>
            <!-- Nama Direktur -->
            <div style="font-size: 10pt; font-weight: bold; text-decoration: underline;">
                {{ $pengajuan->direktur->nama ?? '............................................' }}
            </div>
            <div style="font-size: 9pt; margin-top: 2px;">
                NIP. {{ $pengajuan->direktur->nip ?? '............................................' }}
            </div>
        </div>

    </div>

    <!-- ===== LAMPIRAN ===== -->
    <div style="margin-top: 14px;">
        <div class="section-title">LAMPIRAN</div>
        <div class="lampiran-list">
            <div>1. Analisis kebutuhan.</div>
        </div>
    </div>

    <!-- ===== FOOTER ===== -->
    {{-- <div class="footer-print">
        Dicetak pada : {{ date('d F Y H:i:s') }} &nbsp;|&nbsp; Dokumen ini adalah cetakan resmi dari sistem FixoraV2
    </div> --}}

</div>
<!-- ===== END FRAME UTAMA ===== -->

<!-- ===== TOMBOL CETAK ===== -->
<div class="no-print">
    <button class="btn-print" onclick="window.print()">🖨️ Cetak</button>
    <button class="btn-close" onclick="window.close()">✕ Tutup</button>
</div>

<!-- ===== JAVASCRIPT UNTUK TOTAL OTOMATIS ===== -->
<script>
function formatRupiah(angka) {
    if (!angka || isNaN(angka)) return '0';
    var number_string = angka.toString();
    var sisa = number_string.length % 3;
    var rupiah = number_string.substr(0, sisa);
    var ribuan = number_string.substr(sisa).match(/\d{3}/g);
    if (ribuan) {
        var separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    return rupiah || '0';
}

function parseAngka(str) {
    if (!str) return 0;
    var cleaned = str.toString().replace(/[^0-9]/g, '');
    return parseFloat(cleaned) || 0;
}

function hitungTotalBaris(element) {
    var row = element.closest('tr');
    var jumlahInput = row.querySelector('.input-jumlah');
    var hargaInput = row.querySelector('.input-harga');
    var totalCell = row.querySelector('.total-barang');
    
    if (!jumlahInput || !hargaInput || !totalCell) return;

    var jumlah = parseInt(jumlahInput.value) || 0;
    var harga = parseAngka(hargaInput.value);
    
    var total = jumlah * harga;
    totalCell.textContent = formatRupiah(total);
    
    hitungGrandTotal();
}

function hitungGrandTotal() {
    var totalCells = document.querySelectorAll('.total-barang');
    var grandTotal = 0;
    
    totalCells.forEach(function(cell) {
        var value = parseAngka(cell.textContent); 
        grandTotal += value;
    });
    
    var grandTotalElement = document.getElementById('grand-total');
    if (grandTotalElement) {
        grandTotalElement.textContent = formatRupiah(grandTotal);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var hargaInputs = document.querySelectorAll('.input-harga');
    hargaInputs.forEach(function(input) {
        var initialValue = parseAngka(input.value);
        if(initialValue > 0) {
            input.value = formatRupiah(initialValue);
        }

        input.addEventListener('input', function(e) {
            var value = this.value.replace(/[^0-9]/g, '');
            if (value) {
                this.value = formatRupiah(parseInt(value));
            } else {
                this.value = '';
            }
            hitungTotalBaris(this);
        });
    });
    
    var jumlahInputs = document.querySelectorAll('.input-jumlah');
    jumlahInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            hitungTotalBaris(this);
        });
    });
    
    setTimeout(function() {
        var semuaInput = document.querySelectorAll('.input-jumlah, .input-harga');
        semuaInput.forEach(function(input) {
            if(input.closest('tr').querySelector('.total-barang')) {
                hitungTotalBaris(input);
            }
        });
    }, 200); 
});
</script>

</body>
</html>
