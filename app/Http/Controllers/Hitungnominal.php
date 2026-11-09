<?php

namespace App\Http\Controllers\Direktur\Concerns;

use App\Models\Bidang;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

/**
 * Statistik nominal (Rupiah) untuk dashboard direktur.
 *
 * Nama kolom uang dideteksi otomatis dari daftar kandidat di bawah.
 * Kalau kolom Anda tidak ikut terdeteksi, tambahkan namanya ke array yang sesuai.
 *
 * Trait ini dipakai di ChartController dan memanfaatkan konstanta BUCKETS
 * serta method namaBidang() milik controller tersebut.
 */
trait HitungNominal
{
    /** Kolom total langsung di tabel pengajuan (dicek berurutan). */
    private array $kolomTotalPengajuan = [
        'total_harga', 'total_biaya', 'total_anggaran', 'grand_total',
        'total_estimasi', 'estimasi_biaya', 'nominal', 'total',
    ];

    /** Kolom subtotal per baris item. */
    private array $kolomSubtotalItem = ['subtotal', 'sub_total', 'total_harga', 'jumlah_harga', 'total'];

    /** Jika tidak ada subtotal: jumlah x harga satuan. */
    private array $kolomQtyItem   = ['jumlah', 'qty', 'kuantitas', 'volume'];
    private array $kolomHargaItem = ['harga_satuan', 'harga', 'estimasi_harga', 'harga_estimasi'];

    protected function nominalData(int $tahun, ?string $kategori): array
    {
        try {
            $expr = $this->ekspresiNominal();
        } catch (RuntimeException $e) {
            return ['tersedia' => false, 'pesan' => $e->getMessage()];
        }

        $t = (new Pengajuan)->getTable();

        // ---------- Per status ----------
        $perStatus = $this->nominalBase($tahun, $kategori)
            ->selectRaw("status, SUM({$expr}) as nilai")
            ->groupBy('status')
            ->pluck('nilai', 'status');

        $total = (float) $perStatus->sum();

        $statusChart = [];
        $assigned    = 0.0;
        foreach (self::BUCKETS as $key => $bucket) {
            $v = (float) collect($bucket['status'])->sum(fn ($s) => (float) ($perStatus[$s] ?? 0));
            $assigned += $v;
            $statusChart[] = ['key' => $key, 'label' => $bucket['label'], 'value' => (int) round($v)];
        }
        $statusChart[] = ['key' => 'lainnya', 'label' => 'Lainnya', 'value' => (int) round(max($total - $assigned, 0))];

        $disetujui = $statusChart[0]['value'];
        $ditolak   = $statusChart[1]['value'];

        // ---------- KPI ----------
        $perKategori = $this->nominalBase($tahun, null)
            ->selectRaw("kategori, SUM({$expr}) as nilai")
            ->groupBy('kategori')
            ->pluck('nilai', 'kategori');

        $jumlah = $this->nominalBase($tahun, $kategori)->count();

        $prevRow   = $this->nominalBase($tahun - 1, $kategori)->selectRaw("SUM({$expr}) as n")->first();
        $prevTotal = (float) ($prevRow->n ?? 0);

        $kpi = [
            'total'        => (int) round($total),
            'pengadaan'    => (int) round((float) ($perKategori['pengadaan'] ?? 0)),
            'pemeliharaan' => (int) round((float) ($perKategori['pemeliharaan'] ?? 0)),
            'disetujui'    => $disetujui,
            'ditolak'      => $ditolak,
            'rata'         => $jumlah > 0 ? (int) round($total / $jumlah) : 0,
            'prevTotal'    => (int) round($prevTotal),
            'growth'       => $prevTotal > 0 ? round(($total - $prevTotal) / $prevTotal * 100, 1) : null,
        ];

        // ---------- Per bulan (pengadaan, pemeliharaan, disetujui) ----------
        $bulan = [
            'pengadaan'    => $this->nilaiPerBulan($tahun, 'pengadaan', $expr),
            'pemeliharaan' => $this->nilaiPerBulan($tahun, 'pemeliharaan', $expr),
            'disetujui'    => $this->nilaiPerBulan($tahun, null, $expr, self::BUCKETS['disetujui']['status']),
        ];

        // ---------- Per tahun ----------
        $rows = Pengajuan::query()
            ->whereNotIn("{$t}.status", ['draft'])
            ->whereNotNull("{$t}.tanggal_pengajuan")
            ->selectRaw("YEAR(tanggal_pengajuan) as tahun, kategori, SUM({$expr}) as nilai")
            ->groupBy('tahun', 'kategori')
            ->get();

        $labels = $rows->pluck('tahun')->unique()->sort()->values();

        $perTahun = [
            'labels'       => $labels,
            'pengadaan'    => $labels->map(fn ($th) => (int) round((float) $rows->where('tahun', $th)->where('kategori', 'pengadaan')->sum('nilai')))->values(),
            'pemeliharaan' => $labels->map(fn ($th) => (int) round((float) $rows->where('tahun', $th)->where('kategori', 'pemeliharaan')->sum('nilai')))->values(),
        ];

        // ---------- Per bidang (10 teratas) ----------
        $perBidang = $this->nominalBase($tahun, $kategori)
            ->selectRaw("bidang_id, SUM({$expr}) as nilai")
            ->groupBy('bidang_id')
            ->orderByDesc('nilai')
            ->limit(10)
            ->get();

        $namaBidang = Bidang::whereKey($perBidang->pluck('bidang_id')->filter())->get()
            ->mapWithKeys(fn ($b) => [$b->getKey() => $this->namaBidang($b)]);

        return [
            'tersedia' => true,
            'kpi'      => $kpi,
            'chart'    => [
                'bulan'    => $bulan,
                'status'   => $statusChart,
                'perTahun' => $perTahun,
                'bidang'   => [
                    'labels' => $perBidang->map(fn ($r) => $namaBidang[$r->bidang_id] ?? 'Tanpa bidang')->values(),
                    'values' => $perBidang->map(fn ($r) => (int) round((float) $r->nilai))->values(),
                ],
            ],
        ];
    }

    /** Query dasar nominal: bukan draft, tahun terpilih, (opsional) satu kategori. */
    private function nominalBase(int $tahun, ?string $kategori)
    {
        $t = (new Pengajuan)->getTable();

        return Pengajuan::query()
            ->whereNotIn("{$t}.status", ['draft'])
            ->whereYear("{$t}.tanggal_pengajuan", $tahun)
            ->when($kategori, fn ($q) => $q->where("{$t}.kategori", $kategori));
    }

    private function nilaiPerBulan(int $tahun, ?string $kategori, string $expr, ?array $statuses = null): array
    {
        $t = (new Pengajuan)->getTable();

        $rows = $this->nominalBase($tahun, $kategori)
            ->when($statuses, fn ($q) => $q->whereIn("{$t}.status", $statuses))
            ->selectRaw("MONTH(tanggal_pengajuan) as bulan, SUM({$expr}) as nilai")
            ->groupBy('bulan')
            ->pluck('nilai', 'bulan');

        return array_map(fn ($m) => (int) round((float) ($rows[$m] ?? 0)), range(1, 12));
    }

    /**
     * Ekspresi SQL nilai satu pengajuan:
     * kolom total di tabel pengajuan, atau jumlah dari item jika kolom itu tidak ada.
     */
    private function ekspresiNominal(): string
    {
        $p = new Pengajuan;
        $t = $p->getTable();

        foreach ($this->kolomTotalPengajuan as $col) {
            if (Schema::hasColumn($t, $col)) {
                return "COALESCE({$t}.{$col}, 0)";
            }
        }

        $rel  = $p->items();
        $it   = $rel->getRelated()->getTable();
        $item = $this->ekspresiItem($it);

        return "(SELECT COALESCE(SUM({$item}), 0) FROM {$it} WHERE {$rel->getQualifiedForeignKeyName()} = {$rel->getQualifiedParentKeyName()})";
    }

    private function ekspresiItem(string $it): string
    {
        foreach ($this->kolomSubtotalItem as $col) {
            if (Schema::hasColumn($it, $col)) {
                return "COALESCE({$it}.{$col}, 0)";
            }
        }

        $qty   = collect($this->kolomQtyItem)->first(fn ($c) => Schema::hasColumn($it, $c));
        $harga = collect($this->kolomHargaItem)->first(fn ($c) => Schema::hasColumn($it, $c));

        if ($qty && $harga) {
            return "COALESCE({$it}.{$qty}, 0) * COALESCE({$it}.{$harga}, 0)";
        }

        throw new RuntimeException(
            'Kolom nominal tidak ditemukan. Tambahkan nama kolom uang Anda ke $kolomTotalPengajuan atau $kolomSubtotalItem di HitungNominal.php.'
        );
    }
}