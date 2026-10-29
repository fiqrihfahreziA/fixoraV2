<?php

namespace App\Http\Controllers\Pengadaan;

use App\Http\Controllers\Controller;
use App\Models\pengajuan;
use App\Models\pengajuan_item;
use App\Models\barang_tersedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PengajuanController extends Controller
{
    public function storepemohon(Request $request)
    {
        $request->validate([
            'tanggal_pengajuan' => 'required|date',
            'karyawan_id' => 'required|exists:karyawans,id',
            'dasar_usulan' => 'required|string',
            'alasan_justifikasi' => 'required|string',
            'manfaat' => 'required|string',
            'dampak' => 'required|string',
            'instalasi' => 'required|string',
            'items.*.nama_barang' => 'required|string',
            'items.*.jumlah' => 'required|numeric|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Generate nomor pengajuan
            $tahun = date('Y');
            $bulan = date('m');
            $lastPengajuan = Pengajuan::whereYear('created_at', $tahun)->count();
            $noPengajuan = 'PEN-' . $tahun . $bulan . '-' . str_pad($lastPengajuan + 1, 4, '0', STR_PAD_LEFT);

            // Simpan pengajuan
            $pengajuan = Pengajuan::create([
                'karyawan_id' => $request->karyawan_id,
                'bidang_id' => $request->bidang_id,
                'no_pengajuan' => $noPengajuan,
                'tanggal_pengajuan' => $request->tanggal_pengajuan,
                'tahun_anggaran' => $request->tahun_anggaran,
                'instalasi' => $request->instalasi,
                'dasar_usulan' => $request->dasar_usulan,
                'alasan_justifikasi' => $request->alasan_justifikasi,
                'manfaat' => $request->manfaat,
                'dampak' => $request->dampak,
                'foto_barang' => $request->has('foto_barang'),
                'data_kerusakan' => $request->has('data_kerusakan'),
                'penawaran_harga' => $request->has('penawaran_harga'),
                'status' => 'diajukan',
                'total_pengajuan' => 0, // akan diupdate nanti
            ]);

            $totalPengajuan = 0;

            // Simpan items
            foreach ($request->items as $itemData) {
                $total = $itemData['jumlah'] * $itemData['harga_satuan'];
                $totalPengajuan += $total;

                $item = Pengajuan_item::create([
                    'pengajuan_id' => $pengajuan->id,
                    'nama_barang' => $itemData['nama_barang'],
                    'spesifikasi' => $itemData['spesifikasi'] ?? null,
                    'satuan' => $itemData['satuan'] ?? 'Unit',
                    'jumlah' => $itemData['jumlah'],
                    'harga_satuan' => $itemData['harga_satuan'],
                    'harga' => $itemData['harga_satuan'],
                ]);

                // Simpan barang tersedia jika ada
                if (isset($itemData['ada_barang_tersedia']) && $itemData['ada_barang_tersedia']) {
                    if (isset($itemData['barang_tersedia'])) {
                        barang_tersedia::create([
                            'pengajuan_item_id' => $item->id,
                            'nama_barang' => $itemData['barang_tersedia']['nama_barang'],
                            'jumlah' => $itemData['barang_tersedia']['jumlah'],
                            'tahun_perolehan' => $itemData['barang_tersedia']['tahun_perolehan'],
                            'kondisi' => $itemData['barang_tersedia']['kondisi'],
                            'keterangan' => $itemData['barang_tersedia']['keterangan'] ?? null,
                        ]);
                    }
                }
            }

            // Update total pengajuan
            $pengajuan->update(['total_pengajuan' => $totalPengajuan]);

            DB::commit();

            return redirect()
                ->route('pemohon.pengadaan', $pengajuan->id)
                ->with('success', 'Pengajuan berhasil dibuat! Nomor: ' . $noPengajuan);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan pengajuan: ' . $e->getMessage());
        }
    }
}