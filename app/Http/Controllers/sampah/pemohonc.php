<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;


use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\bidang;
use App\Models\detail_barang;
use App\Models\barang_tersedia;
use App\Models\pengajuan;
use App\Models\pengajuan_item;
use Illuminate\Support\Facades\DB; 
use App\Models\RequestModel;
use Illuminate\Support\Facades\Auth;

class PemohonController extends Controller
{

     public function index()
    {
        $authUser = Auth::user();

        // $requests = RequestModel::with('detailBarang')
        //     ->where('status', 'menunggu_simrs')
        //     ->orderBy('created_at', 'desc')
        //     ->get();

        return view('pemohon.dashboard', [
            'authUser' => $authUser,
        ]);
    }

    //    public function Showpermintaan()
    // {
    //             $authUser = Auth::user();

    //         $requests = RequestModel::with('detailBarang')
    //             ->where('karyawan_id', $authUser->id)
    //             ->orderBy('created_at', 'desc')
    //             ->get();

    //         return view('pemohon.permintaan', [
    //             'authUser' => $authUser,
    //             'modalreq' => $requests,
    //         ]);
    // }

//     public function Showpermintaan(Request $request)
// {
//     $authUser = Auth::user();

//     // Ambil query pencarian dari request
//     $search = $request->input('search', '');

//     // Query untuk mengambil data permintaan berdasarkan pencarian
//     $requests = RequestModel::with('detailBarang')
//         ->where('karyawan_id', $authUser->id)
//         ->where(function ($query) use ($search) {
//             $query->where('request_type', 'like', '%' . $search . '%')
//                   ->orWhereHas('detailBarang', function ($q) use ($search) {
//                       $q->where('nama_barang', 'like', '%' . $search . '%')
//                         ->orWhere('deskripsi', 'like', '%' . $search . '%');
//                   });
//         })
//         ->orderBy('created_at', 'desc')
//         ->get();

//     // Fetch Permintaan with pagination
// $permintaanRequests = RequestModel::with('detailBarang')
// ->where('karyawan_id', $authUser->id)
//     ->where('request_type', 'permintaan')  // Filter for permintaan
//     ->where(function ($query) use ($search) {
//         $query->where('request_type', 'like', '%' . $search . '%')
//               ->orWhereHas('detailBarang', function ($q) use ($search) {
//                   $q->where('nama_barang', 'like', '%' . $search . '%')
//                     ->orWhere('deskripsi', 'like', '%' . $search . '%');
//               });
//     })
//     ->orderBy('created_at', 'desc')
//     ->paginate(10);  // Pagination for permintaan

// // Fetch Perbaikan with pagination
// $perbaikanRequests = RequestModel::with('detailBarang')
// ->where('karyawan_id', $authUser->id)
//     ->where('request_type', 'perbaikan')  // Filter for perbaikan
//     ->where(function ($query) use ($search) {
//         $query->where('request_type', 'like', '%' . $search . '%')
//               ->orWhereHas('detailBarang', function ($q) use ($search) {
//                   $q->where('nama_barang', 'like', '%' . $search . '%')
//                     ->orWhere('deskripsi', 'like', '%' . $search . '%');
//               });
//     })
//     ->orderBy('created_at', 'desc')
//     ->paginate(10);  // Pagination for perbaikan


//     return view('pemohon.permintaan', [
//         'authUser' => $authUser,
//         'modalreq' => $requests,
//         'search' => $search,  // Pass search query to the view
//         'permintaanRequests' => $permintaanRequests,
//         'perbaikanRequests' => $perbaikanRequests,
//     ]);
// }

public function Showpermintaan(Request $request)
{
    $authUser = Auth::user();
    $ruangan = $authUser->karyawan->ruangan; // 🔑 RUANGAN USER LOGIN

    // Ambil query pencarian
    $search = $request->input('search');

    /**
     * ============================
     * MODAL / LIST SEMUA REQUEST (BERDASARKAN RUANGAN)
     * ============================
     */
    $requests = RequestModel::with('detailBarang')
        ->where('ruangan', $ruangan)
        
        ->when($search, function ($query) use ($search) {
            $query->whereHas('detailBarang', function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        })
        ->orderBy('created_at', 'desc')
        ->get();

    /**
     * ============================
     * PERMINTAAN (PAGINATION)
     * ============================
     */
    $permintaanRequests = RequestModel::with('detailBarang')
        ->where('ruangan', $ruangan)
        ->where('request_type', 'permintaan')
        ->when($search, function ($query) use ($search) {
            $query->whereHas('detailBarang', function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    /**
     * ============================
     * PERBAIKAN (PAGINATION)
     * ============================
     */
    $perbaikanRequests = RequestModel::with('detailBarang')
        ->where('ruangan', $ruangan)
        ->where('request_type', 'perbaikan')
        ->when($search, function ($query) use ($search) {
            $query->whereHas('detailBarang', function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

          $bidangs = bidang::all();

    return view('pemohon.permintaan', compact(
        'authUser',
        'requests',
        'search',
        'permintaanRequests',
        'perbaikanRequests',
        'bidangs'
    ));
}


public function storebarang(Request $request)
{
       $authUser = Auth::user();

    // Validasi data
    $request->validate([
        'nama_barang' => 'required|string|max:255',
        'bidang_id' => 'required|exists:bidangs,id',
        'kode_aset' => 'nullable|string|max:255', // Hanya diperlukan untuk perbaikan
        'jumlah' => 'nullable|integer',  // Jumlah hanya diperlukan untuk permintaan barang
        'deskripsi' => 'nullable|string',  // Deskripsi untuk perbaikan
        'ruangan' => 'nullable|string',   // Ruangan hanya untuk perbaikan
        'request_type' => 'required|string|in:permintaan,perbaikan',
        'tanggal_kerusakan' => 'nullable|date',  // Hanya untuk perbaikan
        'spesifikasi' => 'nullable|string',  // Spesifikasi hanya untuk permintaan
        'alasan' => 'nullable|string',  // Alasan hanya untuk permintaan
        'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // Simpan data permintaan ke database
    $req = RequestModel::create([
        'karyawan_id' => $authUser->karyawan->id,
        'ruangan' => $authUser->karyawan->ruangan,
        'request_type' => $request->request_type,
        'status' => 'pending',
        'bidang_id' => $request->bidang_id,
    ]);

    // Simpan detail barang (gabungkan spesifikasi/alasan ke deskripsi jika perlu)
    $deskripsi = $request->deskripsi;

        $pathImage = null;

    if ($request->hasFile('gambar')) {
        $pathImage = $request->file('gambar')->store('permintaan', 'public');
    }

    // Jika jenis permintaan adalah 'permintaan', gabungkan spesifikasi dan alasan ke deskripsi
    if ($request->request_type === 'permintaan') {
        $deskripsi = ($request->spesifikasi ?? '') . ' | Alasan: ' . ($request->alasan ?? '');
    }

    // Untuk perbaikan, pastikan deskripsi berisi data yang relevan
    if ($request->request_type === 'perbaikan') {
        // Pastikan tanggal_kerusakan ada jika request_type adalah perbaikan
        if (!$request->tanggal_kerusakan) {
            return redirect()->back()->with('error', 'Tanggal kerusakan harus diisi untuk perbaikan.');
        }
    }

    // Simpan data detail barang untuk permintaan atau perbaikan
    detail_barang::create([
        'request_id' => $req->id,
        'nama_barang' => $request->nama_barang ?? '-',
        'kode_aset' => $request->kode_aset ?? '-', // Untuk perbaikan
        'deskripsi' => $deskripsi ?? '-',
        'jumlah' => $request->jumlah ?? 0, // Untuk permintaan
        'tanggal_kerusakan' => $request->tanggal_kerusakan ?? null, // Untuk perbaikan
        'alasan'  => $request->alasan, // Untuk permintaan
        'gambar' => $pathImage,
        
        
    ]);

    Log::info('Request created: ' . $req->id);
    Log::info('Detail barang created for request: ' . $req->id);

    // Redirect
    return redirect()->route('pemohon.permintaan')->with('success', 'Permintaan berhasil ditambahkan');
}

public function lihatGambar($id)
{
    $req = RequestModel::with('detailBarang')->findOrFail($id);

    return view('pemohon.detail_gambar', compact('req'));
}

public function edit($id)
{
    $authUser = Auth::user();
    // Ambil permintaan berdasarkan ID
    $permintaan = RequestModel::findOrFail($id);
    
    // Kirim data ke halaman edit
    return view('pemohon.permintaanedit', [
        'authUser' => $authUser,
        'permintaan' => $permintaan,
    ]);
}


public function update(Request $request, $id)
{
    // Validasi input
    $validated = $request->validate([
        'nama_barang' => 'required|string|max:255',
        'kode_aset' => 'nullable|string|max:255',
        'jumlah' => 'nullable|integer',
        'deskripsi' => 'nullable|string',
        'ruangan' => 'nullable|string',
        'request_type' => 'required|string|in:permintaan,perbaikan',
        'tanggal_kerusakan' => 'nullable|date',
        'spesifikasi' => 'nullable|string',
        'alasan' => 'nullable|string',
        'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // Ambil permintaan berdasarkan ID
    $permintaan = RequestModel::findOrFail($id);

    $pathImage = $permintaan->detailBarang->gambar;

$pathImage = $permintaan->detailBarang->gambar;

if ($request->hasFile('gambar')) {

    // Hapus gambar lama jika ada
    if ($pathImage && Storage::disk('public')->exists($pathImage)) {
        Storage::disk('public')->delete($pathImage);
    }

    // Upload gambar baru
    $pathImage = $request->file('gambar')->store('permintaan', 'public');
}

    // Update data permintaan
    $permintaan->update([
        'request_type' => $request->request_type,
        'status' => 'pending',
    ]);

    // Ambil detail barang terkait permintaan
    $detailBarang = $permintaan->detailBarang;

    // Update detail barang (nama_barang, jumlah, dll)
    $detailBarang->update([
        'nama_barang' => $request->nama_barang,
        'kode_aset' => $request->kode_aset,
        'deskripsi' => $request->deskripsi,
        'jumlah' => $request->jumlah,
        'tanggal_kerusakan' => $request->tanggal_kerusakan,
        'alasan' => $request->alasan,
        'gambar' => $pathImage,
    ]);

    // Redirect ke halaman permintaan dengan pesan sukses
    return redirect()->route('pemohon.permintaan')->with('success', 'Permintaan berhasil diupdate');
}

// public function destroypermintaan($id)
// {
//     // Cari permintaan berdasarkan ID
//     $permintaan = RequestModel::findOrFail($id);
      
//     $permintaan->detailBarang()->delete();


//     // Hapus permintaan
//     $permintaan->delete();
    
//     // Redirect ke halaman dengan pesan sukses
//     return redirect()->route('pemohon.permintaan')->with('success', 'Permintaan berhasil dihapus!');
// }

public function destroypermintaan($id)
{
    // Cari permintaan
    $permintaan = RequestModel::findOrFail($id);

    // Ambil detail barang
    $detail = $permintaan->detailBarang;

    // Hapus gambar jika ada
    if ($detail && $detail->gambar) {
        if (Storage::disk('public')->exists($detail->gambar)) {
            Storage::disk('public')->delete($detail->gambar);
        }
    }

    // Hapus detail barang
    if ($detail) {
        $detail->delete();
    }

    // Hapus permintaan
    $permintaan->delete();

    return redirect()->route('pemohon.permintaan')
        ->with('success', 'Permintaan berhasil dihapus!');
}

// pengadaan

 public function showpengadaanm(){
     $authUser = Auth::user();
    return view('pemohon.pengadaan.pengadaan', [
        'title' => 'Pengadaan',
        'authUser' => $authUser
    ]);
   }



public function showpengadaan(Request $request)
{
    $authUser = Auth::user();
    
    // Ambil ruangan dari karyawan yang login
    $ruangan = $authUser->karyawan->ruangan ?? null;
    
    // Ambil bidang untuk filter
    $bidangs = Bidang::all();

    // Ambil query pencarian
    $search = $request->input('search');
    $statusFilter = $request->input('status');
    $bidangFilter = $request->input('bidang');
    $typeFilter = $request->input('type'); // 'permintaan' atau 'perbaikan'

    /**
     * ============================
     * QUERY DASAR PENGADAAN (SEMUA)
     * ============================
     */
    $query = Pengajuan::with(['items', 'karyawan', 'bidang'])
        ->where('instalasi', $ruangan)
        ->when($search, function ($q) use ($search) {
            $q->where(function ($query) use ($search) {
                $query->where('no_pengajuan', 'like', "%{$search}%")
                    ->orWhere('dasar_usulan', 'like', "%{$search}%")
                    ->orWhereHas('items', function ($itemQuery) use ($search) {
                        $itemQuery->where('nama_barang', 'like', "%{$search}%")
                            ->orWhere('spesifikasi', 'like', "%{$search}%");
                    });
            });
        })
        ->when($statusFilter, function ($q) use ($statusFilter) {
            $q->where('status', $statusFilter);
        })
        ->when($bidangFilter, function ($q) use ($bidangFilter) {
            $q->where('bidang_id', $bidangFilter);
        });

    /**
     * ============================
     * DATA UNTUK TABEL SEMUA PENGAJUAN
     * ============================
     */
    $allPengajuan = clone $query;
    $allPengajuan = $allPengajuan->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

    /**
     * ============================
     * DATA UNTUK TAB PERMINTAAN 
     * Menggunakan dasar_usulan untuk filter
     * ============================
     */
    $permintaanQuery = clone $query;
    $permintaanPengajuan = $permintaanQuery->where(function($q) {
            $q->where('dasar_usulan', 'LIKE', '%permintaan%')
              ->orWhere('dasar_usulan', 'LIKE', '%pengadaan%')
              ->orWhere('dasar_usulan', 'LIKE', '%beli%')
              ->orWhereNull('dasar_usulan'); // Default dianggap permintaan
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

    /**
     * ============================
     * DATA UNTUK TAB PERBAIKAN 
     * Menggunakan dasar_usulan untuk filter
     * ============================
     */
    $perbaikanQuery = clone $query;
    $perbaikanPengajuan = $perbaikanQuery->where(function($q) {
            $q->where('dasar_usulan', 'LIKE', '%perbaikan%')
              ->orWhere('dasar_usulan', 'LIKE', '%service%')
              ->orWhere('dasar_usulan', 'LIKE', '%repair%');
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

    /**
     * ============================
     * STATISTIK DASHBOARD
     * ============================
     */
    $stats = [
        'total' => Pengajuan::where('instalasi', $ruangan)->count(),
        'draft' => Pengajuan::where('instalasi', $ruangan)->where('status', 'draft')->count(),
        'diajukan' => Pengajuan::where('instalasi', $ruangan)->where('status', 'diajukan')->count(),
        'disetujui' => Pengajuan::where('instalasi', $ruangan)->where('status', 'disetujui')->count(),
        'ditolak' => Pengajuan::where('instalasi', $ruangan)->where('status', 'ditolak')->count(),
        'menunggu_direktur' => Pengajuan::where('instalasi', $ruangan)->where('status', 'menunggu_direktur')->count(),
        
        // Statistik berdasarkan jenis (menggunakan dasar_usulan)
        'permintaan_count' => Pengajuan::where('instalasi', $ruangan)
            ->where(function($q) {
                $q->where('dasar_usulan', 'LIKE', '%permintaan%')
                  ->orWhere('dasar_usulan', 'LIKE', '%pengadaan%')
                  ->orWhere('dasar_usulan', 'LIKE', '%beli%')
                  ->orWhereNull('dasar_usulan');
            })->count(),
            
        'perbaikan_count' => Pengajuan::where('instalasi', $ruangan)
            ->where(function($q) {
                $q->where('dasar_usulan', 'LIKE', '%perbaikan%')
                  ->orWhere('dasar_usulan', 'LIKE', '%service%')
                  ->orWhere('dasar_usulan', 'LIKE', '%repair%');
            })->count(),
    ];

    return view('pemohon.pengadaan.pengadaan', compact(
        'authUser',
        'allPengajuan',
        'permintaanPengajuan',
        'perbaikanPengajuan',
        'bidangs',
        'search',
        'statusFilter',
        'bidangFilter',
        'typeFilter',
        'stats'
    ));
}

public function create()
{
    $bidangs = bidang::all();
     $authUser = Auth::user();
    
    return view('pemohon.pengadaan.create', compact('bidangs', 'authUser'));
}

public function storePengadaan(Request $request)
{
    // Log data masuk untuk debug
    Log::info('📤 DATA MASUK:', $request->all());

    // VALIDASI
    $validated = $request->validate([
        'karyawan_id'        => 'required|exists:karyawans,id',
        'tanggal_pengajuan'  => 'required|date',
        'bidang_id'          => 'nullable|exists:bidangs,id',
        'dasar_usulan'       => 'required|string',
        'instalasi'          => 'nullable|string',
        'alasan_justifikasi' => 'required|string',
        'manfaat'            => 'required|string',
        'dampak'             => 'required|string',
        'tahun_anggaran'     => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
        'kondisi_barang_lama' => 'nullable|string',
        'ket_barang_lama'    => 'nullable|string',

        'items' => 'required|array|min:1',
        'items.*.nama_barang'  => 'required|string',
        'items.*.jumlah'       => 'required|integer|min:1',
        'items.*.harga_satuan' => 'required|numeric|min:0',
        'items.*.spesifikasi'  => 'nullable|string',
        'items.*.satuan'       => 'nullable|string',

        // HANYA DATA KERUSAKAN (PDF)
        'data_kerusakan'     => 'nullable|file|mimes:pdf|max:5120',
    ]);

    DB::beginTransaction();

    try {
        // Hitung total pengajuan
        $totalPengajuan = 0;
        foreach ($request->items as $item) {
            $totalPengajuan += $item['jumlah'] * $item['harga_satuan'];
        }

        // Generate nomor pengajuan unik
        $noPengajuan = 'PGD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        while (pengajuan::where('no_pengajuan', $noPengajuan)->exists()) {
            $noPengajuan = 'PGD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }

        // UPLOAD DATA KERUSAKAN (jika ada)
        $dataKerusakanPath = null;
        if ($request->hasFile('data_kerusakan')) {
            $file = $request->file('data_kerusakan');
            $fileName = time() . '_data_kerusakan.' . $file->getClientOriginalExtension();
            $dataKerusakanPath = $file->storeAs('pengajuan/data_kerusakan', $fileName, 'public');
        }

        // CREATE PENGAJUAN
        $pengajuan = pengajuan::create([
            'karyawan_id'        => $request->karyawan_id,
            'bidang_id'          => $request->bidang_id,
            'tanggal_pengajuan'  => $request->tanggal_pengajuan,
            'instalasi'          => $request->instalasi ?? '-',
            'dasar_usulan'       => $request->dasar_usulan,
            'alasan_justifikasi' => $request->alasan_justifikasi,
            'manfaat'            => $request->manfaat,
            'dampak'             => $request->dampak,
            'tahun_anggaran'     => $request->tahun_anggaran ?? date('Y'),
            'kondisi_barang_lama' => $request->kondisi_barang_lama,
            'ket_barang_lama'    => $request->ket_barang_lama,
            'total_pengajuan'    => $totalPengajuan,
            'no_pengajuan'       => $noPengajuan,
            'data_kerusakan'     => $dataKerusakanPath, // HANYA INI
            'foto_barang'        => null,
            'penawaran_harga'    => null,
            'dokumen_pendukung'  => null,
        ]);

        Log::info('✅ PENGAJUAN TERSIMPAN:', $pengajuan->toArray());

        // CREATE ITEMS
        foreach ($request->items as $item) {
            $totalItem = $item['jumlah'] * $item['harga_satuan'];

            $pengajuanItem = pengajuan_item::create([
                'pengajuan_id' => $pengajuan->id,
                'nama_barang'  => $item['nama_barang'],
                'spesifikasi'  => $item['spesifikasi'] ?? null,
                'satuan'       => $item['satuan'] ?? 'Unit',
                'jumlah'       => $item['jumlah'],
                'harga_satuan' => $item['harga_satuan'],
                'harga'        => $totalItem,
                'jumlah_disetujui' => $item['jumlah'],
            ]);

            Log::info('✅ ITEM TERSIMPAN:', $pengajuanItem->toArray());

            // CREATE BARANG TERSEDIA JIKA ADA
            if (!empty($item['ada_barang_tersedia']) && !empty($item['barang_tersedia'])) {
                $bt = $item['barang_tersedia'];
                
                barang_tersedia::create([
                    'pengajuan_item_id' => $pengajuanItem->id,
                    'nama_barang'       => $bt['nama_barang'] ?? $item['nama_barang'],
                    'jumlah'            => $bt['jumlah'] ?? 0,
                    'tahun_perolehan'   => $bt['tahun_perolehan'] ?? null,
                    'kondisi'           => $bt['kondisi'] ?? 'Baik',
                    'keterangan'        => $bt['keterangan'] ?? null,
                ]);
                
                Log::info('✅ BARANG TERSEDIA TERSIMPAN');
            }
        }

        DB::commit();

        return redirect()
            ->route('pemohon.pengadaan')
            ->with('success', '✅ Pengajuan berhasil disimpan! Nomor: ' . $noPengajuan);

    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('❌ ERROR SAVE PENGAJUAN:', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        return back()
            ->withInput()
            ->with('error', '❌ Gagal menyimpan pengajuan: ' . $e->getMessage());
    }
}
/**
 * Upload file helper
 */
private function uploadFile($request, $fieldName, $folderName)
{
    if ($request->hasFile($fieldName)) {
        $file = $request->file($fieldName);
        $fileName = time() . '_' . $fieldName . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs("pengajuan/{$folderName}", $fileName, 'public');
        return $path;
    }
    return null;
}

public function submit($id)
{
    try {
        $authUser = Auth::user();
        $karyawanId = $authUser->karyawan->id ?? null;
        
        $pengajuan = pengajuan::where('karyawan_id', $karyawanId)
            ->whereIn('status', ['draft', 'revisi'])
            ->findOrFail($id);

        $pengajuan->update([
            'status' => 'diajukan',
            'diajukan_at' => now(),
        ]);

        // ✅ PASTIKAN RETURN JSON
        return response()->json([
            'success' => true,
            'message' => 'Pengajuan berhasil diajukan!'
        ]);

    } catch (\Exception $e) {
        // ✅ PASTIKAN RETURN JSON
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
    
public function edittt($id)
{
    $authUser = Auth::user();
    $karyawanId = $authUser->karyawan->id ?? null;
    
    $pengajuan = pengajuan::with(['items', 'items.barangTersedia'])
        ->where('karyawan_id', $karyawanId)
        ->whereIn('status', ['draft', 'revisi']) // <-- TAMBAHKAN 'revisi'
        ->findOrFail($id);
    
    $bidangs = bidang::all();

    return view('pemohon.pengadaan.edit', compact('pengajuan', 'bidangs', 'authUser'));
}

/**
 * Update the specified resource in storage.
 */
public function updatePengadaan(Request $request, $id)
{
    $authUser = Auth::user();
    $karyawanId = $authUser->karyawan->id ?? null;
    
    // Cek apakah pengajuan milik user dan status draft/revisi
    $pengajuan = pengajuan::where('karyawan_id', $karyawanId)
        ->whereIn('status', ['draft', 'revisi'])
        ->findOrFail($id);

    // Log data masuk untuk debug
    Log::info('📤 DATA UPDATE MASUK:', $request->all());

    // VALIDASI
    $validated = $request->validate([
        'tanggal_pengajuan'  => 'required|date',
        'bidang_id'          => 'nullable|exists:bidangs,id',
        'dasar_usulan'       => 'required|string',
        'instalasi'          => 'nullable|string',
        'alasan_justifikasi' => 'required|string',
        'manfaat'            => 'required|string',
        'dampak'             => 'required|string',
        'tahun_anggaran'     => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
        'kondisi_barang_lama' => 'nullable|string',
        'ket_barang_lama'    => 'nullable|string',

        'items' => 'required|array|min:1',
        'items.*.nama_barang'  => 'required|string',
        'items.*.jumlah'       => 'required|integer|min:1',
        'items.*.harga_satuan' => 'required|numeric|min:0',
        'items.*.spesifikasi'  => 'nullable|string',
        'items.*.satuan'       => 'nullable|string',

        // HANYA DATA KERUSAKAN (PDF)
        'data_kerusakan'     => 'nullable|file|mimes:pdf|max:5120',
    ]);

    DB::beginTransaction();

    try {
        // Hitung total pengajuan
        $totalPengajuan = 0;
        foreach ($request->items as $item) {
            $totalPengajuan += $item['jumlah'] * $item['harga_satuan'];
        }

        // UPLOAD DATA KERUSAKAN (jika ada file baru)
        $dataKerusakanPath = $pengajuan->data_kerusakan; // retain existing
        if ($request->hasFile('data_kerusakan')) {
            // Hapus file lama jika ada
            if ($pengajuan->data_kerusakan && Storage::disk('public')->exists($pengajuan->data_kerusakan)) {
                Storage::disk('public')->delete($pengajuan->data_kerusakan);
            }
            
            $file = $request->file('data_kerusakan');
            $fileName = time() . '_data_kerusakan.' . $file->getClientOriginalExtension();
            $dataKerusakanPath = $file->storeAs('pengajuan/data_kerusakan', $fileName, 'public');
        }

        // UPDATE PENGAJUAN
        $pengajuan->update([
            'tanggal_pengajuan'  => $request->tanggal_pengajuan,
            'bidang_id'          => $request->bidang_id,
            'instalasi'          => $request->instalasi ?? '-',
            'dasar_usulan'       => $request->dasar_usulan,
            'alasan_justifikasi' => $request->alasan_justifikasi,
            'manfaat'            => $request->manfaat,
            'dampak'             => $request->dampak,
            'tahun_anggaran'     => $request->tahun_anggaran ?? date('Y'),
            'kondisi_barang_lama' => $request->kondisi_barang_lama,
            'ket_barang_lama'    => $request->ket_barang_lama,
            'total_pengajuan'    => $totalPengajuan,
            'data_kerusakan'     => $dataKerusakanPath,
            // Jika status revisi, ubah kembali ke draft setelah diedit
            'status' => $pengajuan->status == 'revisi' ? 'draft' : $pengajuan->status,
        ]);

        Log::info('✅ PENGAJUAN UPDATED:', $pengajuan->toArray());

        // HAPUS ITEMS LAMA
        pengajuan_item::where('pengajuan_id', $pengajuan->id)->delete();

        // CREATE ITEMS BARU
        foreach ($request->items as $item) {
            $totalItem = $item['jumlah'] * $item['harga_satuan'];

            $pengajuanItem = pengajuan_item::create([
                'pengajuan_id' => $pengajuan->id,
                'nama_barang'  => $item['nama_barang'],
                'spesifikasi'  => $item['spesifikasi'] ?? null,
                'satuan'       => $item['satuan'] ?? 'Unit',
                'jumlah'       => $item['jumlah'],
                'harga_satuan' => $item['harga_satuan'],
                'harga'        => $totalItem,
                'jumlah_disetujui' => $item['jumlah'],
            ]);

            Log::info('✅ ITEM TERSIMPAN:', $pengajuanItem->toArray());

            // CREATE BARANG TERSEDIA JIKA ADA
            if (!empty($item['ada_barang_tersedia']) && !empty($item['barang_tersedia'])) {
                $bt = $item['barang_tersedia'];
                
                barang_tersedia::create([
                    'pengajuan_item_id' => $pengajuanItem->id,
                    'nama_barang'       => $bt['nama_barang'] ?? $item['nama_barang'],
                    'jumlah'            => $bt['jumlah'] ?? 0,
                    'tahun_perolehan'   => $bt['tahun_perolehan'] ?? null,
                    'kondisi'           => $bt['kondisi'] ?? 'Baik',
                    'keterangan'        => $bt['keterangan'] ?? null,
                ]);
                
                Log::info('✅ BARANG TERSEDIA TERSIMPAN');
            }
        }

        DB::commit();

        $message = $pengajuan->status == 'revisi' 
            ? '✅ Pengajuan berhasil direvisi dan dikembalikan ke draft!' 
            : '✅ Pengajuan berhasil diperbarui!';

        return redirect()
            ->route('pemohon.pengadaan')
            ->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('❌ ERROR UPDATE PENGAJUAN:', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        return back()
            ->withInput()
            ->with('error', '❌ Gagal memperbarui pengajuan: ' . $e->getMessage());
    }
}

public function selesai(Request $request, $id)
{
    try {
        $pengajuan = Pengajuan::findOrFail($id);

        // Validasi agar hanya pengajuan yang 'disetujui' yang bisa diubah
        if ($pengajuan->status !== 'disetujui') {
            return back()->with('error', 'Status pengajuan tidak valid untuk diselesaikan.');
        }

        $pengajuan->update([
            'status' => 'selesai',
            'selesai_at' => now(), // Mengisi tanggal & waktu saat ini
        ]);

        return back()->with('success', '✅ Pengajuan berhasil diselesaikan dan barang telah diterima.');

    } catch (\Exception $e) {
        return back()->with('error', '❌ Gagal menyelesaikan pengajuan: ' . $e->getMessage());
    }
}
/**
 * Submit pengajuan (ubah status dari draft ke diajukan)
 *//**
 * Submit pengajuan (ubah status dari draft ke diajukan)
 */
// public function submit(Request $request)
// {
//     try {
//         $authUser = Auth::user();
//         $karyawanId = $authUser->karyawan->id ?? null;
        
//         // Ambil ID dari request body
//         $id = $request->input('id');
        
//         $pengajuan = pengajuan::where('karyawan_id', $karyawanId)
//             ->where('status', 'draft')
//             ->findOrFail($id);

//         $pengajuan->update([
//             'status' => 'diajukan',
//             'diajukan_at' => now(),
//         ]);

//         return response()->json([
//             'success' => true,
//             'message' => 'Pengajuan berhasil diajukan!'
//         ]);

//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => $e->getMessage()
//         ], 500);
//     }
// }

}
// gansharing99@gmail.com
// JANGANISENGYE12!