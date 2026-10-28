<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\bidang;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Models\Karyawan;
use App\Models\detail_barang;
use App\Models\barang_tersedia;
use App\Models\pengajuan;
use App\Models\pengajuan_item;
use Illuminate\Support\Facades\DB; 
use App\Models\RequestModel;
use App\Models\Penerima;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PenerimaController extends Controller
{

public function index()
    {
        $authUser = Auth::user();

        
        return view('penerima.dashboard', [
            'authUser' => $authUser,
        ]);
    }

    public function chart()
{
    $bidangId = Auth::user()->karyawan->bidang_id ?? null;

    if ($bidangId == 2) {
        $bidangFilter = [2, 4];
    } else {
        $bidangFilter = [$bidangId];
    }

    $totalRequest = RequestModel::whereIn('bidang_id', $bidangFilter)->count();

    $totalPermintaan = RequestModel::whereIn('bidang_id', $bidangFilter)
        ->where('request_type', 'permintaan')
        ->count();

    $totalPerbaikan = RequestModel::whereIn('bidang_id', $bidangFilter)
        ->where('request_type', 'perbaikan')
        ->count();

    $pending = RequestModel::whereIn('bidang_id', $bidangFilter)
        ->where('status', 'pending')
        ->count();

    $verified = RequestModel::whereIn('bidang_id', $bidangFilter)
    ->where('status', 'verified')
    ->count();

    $approved = RequestModel::whereIn('bidang_id', $bidangFilter)
        ->where('status', 'approved')
        ->count();

    $recentRequests = RequestModel::with('detailBarang')
    ->whereIn('bidang_id', $bidangFilter)
    ->latest()
    ->take(5)
    ->get();

    return view('penerima.chart', compact(
        'totalRequest',
        'totalPermintaan',
        'totalPerbaikan',
        'pending',
        'approved',
        'verified',
        'recentRequests'
    ));
}

//    public function Showpermintaan(Request $request)
// {
//     $authUser = Auth::user();
//     $bidangId = Auth::user()->karyawan->bidang_id ?? null;

//     // Ambil query pencarian dari request
//     $search = $request->input('search', '');

//     // Query untuk mengambil data permintaan berdasarkan pencarian
//     $requests = RequestModel::with('detailBarang')
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
//     ->where('request_type', 'permintaan')  // Filter for permintaan
//     ->where('bidang_id', $bidangId)
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
//     ->where('request_type', 'perbaikan')  // Filter for perbaikan
//     ->where('bidang_id', $bidangId)
//     ->where(function ($query) use ($search) {
//         $query->where('request_type', 'like', '%' . $search . '%')
//               ->orWhereHas('detailBarang', function ($q) use ($search) {
//                   $q->where('nama_barang', 'like', '%' . $search . '%')
//                     ->orWhere('deskripsi', 'like', '%' . $search . '%');
//               });
//     })
//     ->orderBy('created_at', 'desc')
//     ->paginate(10);  // Pagination for perbaikan


//     return view('penerima.permintaan', [
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
    $bidangId = Auth::user()->karyawan->bidang_id ?? null;

    // Ambil query pencarian dari request
    $search = $request->input('search', '');

    // ?? Tambahan logic di sini
    if ($bidangId == 2) {
        // Komputer ? gabung dengan lainnya
        $bidangFilter = [2, 4];
    } else {
        // Selain komputer ? hanya bidang masing-masing
        $bidangFilter = [$bidangId];
    }

    // Query untuk modal (opsional, kalau mau ikut filter juga bisa)
    $requests = RequestModel::with('detailBarang')
        ->whereIn('bidang_id', $bidangFilter)
        ->where(function ($query) use ($search) {
            $query->where('request_type', 'like', '%' . $search . '%')
                  ->orWhereHas('detailBarang', function ($q) use ($search) {
                      $q->where('nama_barang', 'like', '%' . $search . '%')
                        ->orWhere('deskripsi', 'like', '%' . $search . '%');
                  });
        })
        ->orderBy('created_at', 'desc')
        ->get();

    // ? Permintaan
    $permintaanRequests = RequestModel::with('detailBarang')
        ->where('request_type', 'permintaan')
        ->whereIn('bidang_id', $bidangFilter) // ?? pakai ini
        ->where(function ($query) use ($search) {
            $query->where('request_type', 'like', '%' . $search . '%')
                  ->orWhereHas('detailBarang', function ($q) use ($search) {
                      $q->where('nama_barang', 'like', '%' . $search . '%')
                        ->orWhere('deskripsi', 'like', '%' . $search . '%');
                  });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    // ? Perbaikan
    $perbaikanRequests = RequestModel::with('detailBarang')
        ->where('request_type', 'perbaikan')
        ->whereIn('bidang_id', $bidangFilter) // ?? pakai ini juga
        ->where(function ($query) use ($search) {
            $query->where('request_type', 'like', '%' . $search . '%')
                  ->orWhereHas('detailBarang', function ($q) use ($search) {
                      $q->where('nama_barang', 'like', '%' . $search . '%')
                        ->orWhere('deskripsi', 'like', '%' . $search . '%');
                  });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('penerima.permintaan', [
        'authUser' => $authUser,
        'modalreq' => $requests,
        'search' => $search,
        'permintaanRequests' => $permintaanRequests,
        'perbaikanRequests' => $perbaikanRequests,
    ]);
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
    return view('penerima.permintaanedit', [
        'authUser' => $authUser,
        'permintaan' => $permintaan,
    ]);
}

public function view($id)
{
    $permintaan = RequestModel::with(['detailBarang', 'user'])->findOrFail($id);

    return view('form.formpermintaan', compact('permintaan'));
}



public function destroy($id)
{
    RequestModel::findOrFail($id)->delete();

    return back()->with('success', 'Data berhasil dihapus');
}

public function laporan(){
    $authUser = Auth::user();
    $bidangs = bidang::all();

    return view('penerima.laporan', [
        'authuser' =>$authUser,
        
    ]);

}

public function updatee(Request $request, $id)
{
    $authUser = Auth::user();

    // ================= VALIDASI =================
    $validated = $request->validate([
        'request_type' => 'required|in:permintaan,perbaikan',
        'status'       => 'required|in:pending,verified,approved,rejected',

        // PERMINTAAN
        'nama_barang'  => 'required|string|max:255',
        'jumlah'       => 'nullable|integer',
        'spesifikasi'  => 'nullable|string',
        'alasan'       => 'nullable|string',

        // PERBAIKAN
        'kode_aset'         => 'nullable|string|max:255',
        'deskripsi'         => 'nullable|string',
        'tanggal_kerusakan' => 'nullable|date',
    ]);

    $permintaan = RequestModel::with('detailBarang')->findOrFail($id);

    // ================= UPDATE STATUS =================
    $permintaan->status = $request->status;

    // 🔵 SAAT VERIFIED
    if ($request->status === 'verified') {

        // set penerima jika belum ada
        if ($permintaan->penerima_id === null) {
            $permintaan->penerima_id = $authUser->karyawan->id;
        }

        // set tanggal verif di detail barang
        if ($permintaan->detailBarang->tanggal_verif === null) {
            $permintaan->detailBarang->tanggal_verif = now();
        }
    }

    // 🔴 SAAT BALIK KE PENDING
    if ($request->status === 'pending') {
        $permintaan->penerima_id = null;
        $permintaan->detailBarang->tanggal_verif = null;
    }

    $permintaan->save();
    $permintaan->detailBarang->save();

    // ================= AUTO GENERATE NO SURAT =================
    if (
        $request->status === 'verified' &&
        empty($permintaan->detailBarang->no_surat)
    ) {
        $tahun = now()->year;
        $startNumber = 15;

        $count = \App\Models\detail_barang::whereYear('created_at', $tahun)
            ->whereNotNull('no_surat')
            ->count();

        $nomorUrut = $startNumber + $count;

        $permintaan->detailBarang->no_surat =
            $nomorUrut . '/SIMRS/' . $tahun;

        $permintaan->detailBarang->save();
    }

    // ================= UPDATE DETAIL BARANG =================
    if ($permintaan->request_type === 'permintaan') {

        $permintaan->detailBarang->update([
            'nama_barang' => $request->nama_barang,
            'jumlah'      => $request->jumlah,
            'spesifikasi' => $request->spesifikasi,
            'alasan'      => $request->alasan,

            // reset field perbaikan
            'kode_aset'         => null,
            'deskripsi'         => null,
            'tanggal_kerusakan' => null,
        ]);
    }

    if ($permintaan->request_type === 'perbaikan') {

        $permintaan->detailBarang->update([
            'nama_barang'       => $request->nama_barang,
            'kode_aset'         => $request->kode_aset,
            'deskripsi'         => $request->deskripsi,
            'tanggal_kerusakan' => $request->tanggal_kerusakan,

            // reset field permintaan
            'jumlah'      => null,
            'spesifikasi' => null,
            'alasan'      => null,
        ]);
    }

    return redirect()
        ->route('penerima.permintaan')
        ->with('success', 'Permintaan berhasil diupdate');
}

public function preview(Request $request)
{
    $user = Auth::user();
    $bidangId = optional($user->karyawan)->bidang_id;

    $query = RequestModel::with(['user.karyawan', 'detailBarang'])
        ->where('status', 'approved')

        // 🔥 FILTER BIDANG LANGSUNG DI TABEL REQUEST
        ->where('bidang_id', $bidangId);

    // FILTER TAHUN
    if ($request->filled('tahun')) {
        $query->whereYear('created_at', $request->tahun);
    }

    // FILTER BULAN
    if ($request->filled('bulan')) {
        $query->whereMonth('created_at', $request->bulan);
    }

    // FILTER TANGGAL
    if ($request->filled('tanggal')) {
        $query->whereDay('created_at', $request->tanggal);
    }

    // FILTER JENIS PERMINTAAN
    if ($request->filled('request_type')) {
        $query->where('request_type', $request->request_type);
    }

    $data = $query->orderBy('created_at', 'desc')->get();

    return view('penerima.preview', compact('data'));
}


// public function preview(Request $request)
// {
//     $query = RequestModel::with(['user.karyawan', 'detailBarang'])
//         ->where('status', 'approved'); // 🔥 hanya data APPROVED

//     // FILTER TAHUN
//     if ($request->filled('tahun')) {
//         $query->whereYear('created_at', $request->tahun);
//     }

//     // FILTER BULAN
//     if ($request->filled('bulan')) {
//         $query->whereMonth('created_at', $request->bulan);
//     }

//     // FILTER TANGGAL
//     if ($request->filled('tanggal')) {
//         $query->whereDay('created_at', $request->tanggal);
//     }

//     // FILTER JENIS PERMINTAAN
//     if ($request->filled('request_type')) {
//         $query->where('request_type', $request->request_type);
//     }

//     $data = $query->orderBy('created_at', 'desc')->get();

//     return view('penerima.preview', compact('data'));
// }


public function exportCsv(Request $request)
{
    // ================= FILTER =================
    $baseQuery = RequestModel::with(['user.karyawan','detailBarang']);

    if ($request->filled('tahun')) {
        $baseQuery->whereYear('created_at', $request->tahun);
    }

    if ($request->filled('bulan')) {
        $baseQuery->whereMonth('created_at', $request->bulan);
    }

    if ($request->filled('tanggal')) {
        $baseQuery->whereDay('created_at', $request->tanggal);
    }

    // ================= DATA =================
    $permintaan = (clone $baseQuery)
        ->where('request_type','permintaan')
         ->where('status','approved')
        ->get();

    $perbaikan = (clone $baseQuery)
        ->where('request_type','perbaikan')
        ->where('status','approved')
        ->get();

    // ================= SPREADSHEET =================
    $spreadsheet = new Spreadsheet();

    // ================= SHEET PERMINTAAN =================
    $sheet1 = $spreadsheet->getActiveSheet();
    $sheet1->setTitle('Permintaan');

    $sheet1->fromArray([
        ['No','Nama Peminta','Barang','Ruangan','Tanggal Permintaan','Status','bidang']
    ], null, 'A1');

    // AUTO FILTER
    $sheet1->setAutoFilter('A1:G1');
    // $sheet1->freezePane('A2');

    $row = 2;
    foreach ($permintaan as $i => $item) {
        $sheet1->fromArray([
            $i + 1,
            $item->user->karyawan->nama ?? '-',
            $item->detailBarang->nama_barang ?? '-',
            $item->ruangan,
            $item->created_at->format('d-m-Y'),
            ucfirst($item->status),
            $item->bidang->nama_bidang,
        ], null, "A$row");
        $row++;
    }

    // ================= SHEET PERBAIKAN =================
    $sheet2 = $spreadsheet->createSheet();
    $sheet2->setTitle('Perbaikan');

    $sheet2->fromArray([
        ['No','Nama Peminta','Nama Barang','Kode Aset','Ruangan',
         'Kerusakan','Tanggal Kerusakan','Tanggal Permintaan','Status','bidang']
    ], null, 'A1');

    // AUTO FILTER
        $sheet2->setAutoFilter('A1:J1');
        // $sheet1->freezePane('A2');

    $row = 2;
    foreach ($perbaikan as $i => $item) {
        $sheet2->fromArray([
            $i + 1,
            $item->user->karyawan->nama ?? '-',
            $item->detailBarang->nama_barang ?? '-',
            $item->detailBarang->kode_aset ?? '-',
            $item->ruangan,
            $item->detailBarang->deskripsi ?? '-',
            $item->detailBarang->tanggal_kerusakan ?? '-',
            $item->created_at->format('d-m-Y'),
            ucfirst($item->status),
            $item->bidang->nama_bidang,
        ], null, "A$row");
        $row++;
    }

    // ================= NAMA FILE DINAMIS =================
    $filename = 'laporan';

    if ($request->filled('request_type')) {
        $filename .= '-' . $request->request_type;
    }

    if ($request->filled('tahun')) {
        $filename .= '-' . $request->tahun;
    }

    if ($request->filled('bulan')) {
        $filename .= '-' . str_pad($request->bulan, 2, '0', STR_PAD_LEFT);
    }

    if ($request->filled('tanggal')) {
        $filename .= '-' . str_pad($request->tanggal, 2, '0', STR_PAD_LEFT);
    }

    $filename .= '.xlsx';

    // ================= DOWNLOAD =================
    $writer = new Xlsx($spreadsheet);
    $path = storage_path($filename);
    $writer->save($path);

    return response()->download($path)->deleteFileAfterSend(true);
}

//pengadaan

//    public function chartpengadaan(Request $request)
//     {
//         $authUser = Auth::user();
        
//         // Ambil bidang_id dari karyawan yang login (PENERIMA)
//         $bidangId = $authUser->karyawan->bidang_id ?? null;
//         $ruangan = $authUser->karyawan->ruangan ?? null;
        
//         // Ambil bidang untuk filter
//         $bidangs = bidang::all();

//         // Ambil query pencarian
//         $search = $request->input('search');
//         $statusFilter = $request->input('status');
//         $bidangFilter = $request->input('bidang');
//         $typeFilter = $request->input('type');
//          $tahunAnggaran = $request->input('tahun_anggaran');

//         /**
//          * ============================
//          * QUERY DASAR PENGADAAN
//          * Filter berdasarkan bidang_id user (PENERIMA)
//          * ============================
//          */
//             $query = pengajuan::with(['items', 'karyawan', 'bidang'])
//             // ->where('bidang_id', $bidangId)
//             ->where('status', '!=', 'draft')
//             ->when($search, function ($q) use ($search) {
//                 $q->where(function ($query) use ($search) {
//                     $query->where('no_pengajuan', 'like', "%{$search}%")
//                         ->orWhere('dasar_usulan', 'like', "%{$search}%")
//                         ->orWhereHas('items', function ($itemQuery) use ($search) {
//                             $itemQuery->where('nama_barang', 'like', "%{$search}%")
//                                 ->orWhere('spesifikasi', 'like', "%{$search}%");
//                         });
//                 });
//             })
//             ->when($statusFilter, function ($q) use ($statusFilter) {
//                 $q->where('status', $statusFilter);
//             })
//             ->when($bidangFilter, function ($q) use ($bidangFilter) {
//                 $q->where('bidang_id', $bidangFilter);
//             })
//             ->when($tahunAnggaran, function ($q) use ($tahunAnggaran) { // <-- TAMBAHKAN FILTER TAHUN
//             $q->where('tahun_anggaran', $tahunAnggaran);
//             });

//         /**
//          * ============================
//          * DATA UNTUK TABEL SEMUA PENGAJUAN
//          * ============================
//          */
//         $allPengajuan = clone $query;
//         $allPengajuan = $allPengajuan->orderBy('created_at', 'desc')
//             ->paginate(10)
//             ->appends($request->all());

//         /**
//          * ============================
//          * DATA UNTUK TAB PERMINTAAN 
//          * ============================
//          */
//         $permintaanQuery = clone $query;
//         $permintaanPengajuan = $permintaanQuery->where(function($q) {
//                 $q->where('dasar_usulan', 'LIKE', '%Program Kerja%')
//                   ->orWhere('dasar_usulan', 'LIKE', '%Kebutuhan Operasional%')
//                   ->orWhere('dasar_usulan', 'LIKE', '%Penambahan Kapasitas Pelayanan%')
//                   ->orWhere('dasar_usulan', 'LIKE', '%Pemenuhan Standar Akreditasi%')
//                   ->orWhere('dasar_usulan', 'LIKE', '%Keselamatan Pasien%')
//                   ->orWhereNull('dasar_usulan');
//             })
//             ->orderBy('created_at', 'desc')
//             ->paginate(10)
//             ->appends($request->all());

//         /**
//          * ============================
//          * DATA UNTUK TAB PERBAIKAN 
//          * ============================
//          */
//         $perbaikanQuery = clone $query;
//         $perbaikanPengajuan = $perbaikanQuery->where(function($q) {
//                 $q->where('dasar_usulan', 'LIKE', '%Penggantian Barang Rusak%')
//                   ->orWhere('dasar_usulan', 'LIKE', '%perbaikan%')
//                   ->orWhere('dasar_usulan', 'LIKE', '%service%')
//                   ->orWhere('dasar_usulan', 'LIKE', '%repair%');
//             })
//             ->orderBy('created_at', 'desc')
//             ->paginate(10)
//             ->appends($request->all());

//         /**
//          * ============================
//          * STATISTIK DASHBOARD
//          * ============================
//          */
//         //         $stats = [
//         //     'total' => (clone $query)->count(),
            
//         //     // Pakai clone $query biar angkanya sinkron sama tabel
//         //     'diajukan' => (clone $query)->where('status', 'diajukan')->count(),
            
//         //     'disetujui' => (clone $query)->whereIn('status', ['disetujui', 'selesai'])->count(),
            
//         //     'ditolak' => (clone $query)->where('status', 'ditolak')->count(),
            
//         //     'selesai' => (clone $query)->where('status', 'selesai')->count(),

//         //     // BADGE TAB PENGAADAAN: Ngitung pakai KOLOM KATEGORI & STATUS DIAJUKAN
//         //     'permintaan_count' => (clone $query)
//         //         ->where('status', 'diajukan')
//         //         ->where('kategori', 'pengadaan')
//         //         ->count(),
                
//         //     // BADGE TAB PERBAIKAN: Ngitung pakai KOLOM KATEGORI
//         //     'perbaikan_count' => (clone $query)
//         //         ->where('status', 'diajukan')
//         //         ->where('kategori', 'pemeliharaan')
//         //         ->count(),
//         // ];

//                 $stats = [
//             'total' => (clone $query)->count(),
            
//             // Pakai clone $query biar angkanya sinkron sama tabel
//             'diajukan' => (clone $query)->where('status', 'diajukan')->count(),
            
//             'disetujui' => (clone $query)->whereIn('status', ['disetujui', 'selesai'])->count(),
            
//             'ditolak' => (clone $query)->where('status', 'ditolak')->count(),
            
//             'selesai' => (clone $query)->where('status', 'selesai')->count(),

//             // BADGE TAB PENGAADAAN: Ngitung pakai KOLOM KATEGORI & STATUS DIAJUKAN
//             'permintaan_count' => (clone $query)
//                 ->where('status', 'diajukan')
//                 ->where('kategori', 'pengadaan')
//                 ->count(),
                
//             // BADGE TAB PERBAIKAN: Ngitung pakai KOLOM KATEGORI
//             'perbaikan_count' => (clone $query)
//                 ->where('status', 'diajukan')
//                 ->where('kategori', 'pemeliharaan')
//                 ->count(),
//         ];

//         return view('penerima.pengadaan.permintaan', compact(
//             'authUser',
//             'allPengajuan',
//             'permintaanPengajuan',
//             'perbaikanPengajuan',
//             'bidangs',
//             'search',
//             'statusFilter',
//             'bidangFilter',
//             'typeFilter',
//             'stats',
//             'tahunAnggaran', // <-- TAMBAHKAN INI
//             'bidangId',
//             'ruangan'
//         ));
//     }

// public function chartpengadaan(Request $request)
// {
//     $authUser = Auth::user();
    
//     // Ambil bidang_id dari karyawan yang login (PENERIMA)
//     $bidangId = $authUser->karyawan->bidang_id ?? null;
//     $ruangan = $authUser->karyawan->ruangan ?? null;
    
//     // Ambil bidang untuk filter
//     $bidangs = bidang::all();

//     // Ambil query pencarian
//     $search = $request->input('search');
//     $statusFilter = $request->input('status');
//     $bidangFilter = $request->input('bidang');
//     $typeFilter = $request->input('type');
//     $tahunAnggaran = $request->input('tahun_anggaran');

//     /**
//      * ============================
//      * QUERY DASAR PENGADAAN
//      * ============================
//      */
//     $query = pengajuan::with(['items', 'karyawan', 'bidang'])
//         // ->where('bidang_id', $bidangId)
//         ->where('status', '!=', 'draft')
//         ->when($search, function ($q) use ($search) {
//             $q->where(function ($query) use ($search) {
//                 $query->where('no_pengajuan', 'like', "%{$search}%")
//                     ->orWhere('dasar_usulan', 'like', "%{$search}%")
//                     ->orWhereHas('items', function ($itemQuery) use ($search) {
//                         $itemQuery->where('nama_barang', 'like', "%{$search}%")
//                             ->orWhere('spesifikasi', 'like', "%{$search}%");
//                     });
//             });
//         })
//         ->when($statusFilter, function ($q) use ($statusFilter) {
//             $q->where('status', $statusFilter);
//         })
//         ->when($bidangFilter, function ($q) use ($bidangFilter) {
//             $q->where('bidang_id', $bidangFilter);
//         })
//         ->when($tahunAnggaran, function ($q) use ($tahunAnggaran) {
//             $q->where('tahun_anggaran', $tahunAnggaran);
//         });

//     /**
//      * ============================
//      * DATA UNTUK TABEL SEMUA PENGAJUAN
//      * ============================
//      */
//     $allPengajuan = clone $query;
//     $allPengajuan = $allPengajuan->orderBy('created_at', 'desc')
//         ->paginate(10)
//         ->appends($request->all());

//     /**
//      * ============================
//      * DATA UNTUK TAB PENGADAAN
//      * ============================
//      */
//     $permintaanQuery = clone $query;
//     $permintaanPengajuan = $permintaanQuery->where('kategori', 'pengadaan')
//         ->orderBy('created_at', 'desc')
//         ->paginate(10)
//         ->appends($request->all());

//     /**
//      * ============================
//      * DATA UNTUK TAB PEMELIHARAAN
//      * ============================
//      */
//     $perbaikanQuery = clone $query;
//     $perbaikanPengajuan = $perbaikanQuery->where('kategori', 'pemeliharaan')
//         ->orderBy('created_at', 'desc')
//         ->paginate(10)
//         ->appends($request->all());

//     /**
//      * ============================
//      * STATISTIK DASHBOARD
//      * ============================
//      */
//     $stats = [
//         'total' => (clone $query)->count(),
//         'diajukan' => (clone $query)->where('status', 'diajukan')->count(),
//         'disetujui' => (clone $query)->whereIn('status', ['disetujui', 'selesai'])->count(),
//         'ditolak' => (clone $query)->where('status', 'ditolak')->count(),
//         'selesai' => (clone $query)->where('status', 'selesai')->count(),

//         // Hitungan Badge Tab
//         // Hitungan Badge Tab
//             'permintaan_count' => (clone $query)
//                 ->where('status', 'LIKE', '%diajukan%')
//                 ->where(function($q) {
//                     $q->where('kategori', 'LIKE', '%pengadaan%')
//                     ->orWhereNull('kategori'); // Nangkep data lama yang kategorinya kosong
//                 })
//                 ->count(),

//             'perbaikan_count' => (clone $query)
//                 ->where('status', 'LIKE', '%diajukan%')
//                 ->where(function($q) {
//                     $q->where('kategori', 'LIKE', '%pemeliharaan%')
//                     ->orWhereNull('kategori');
//                 })
//                 ->count(),
//                 ];

//     return view('penerima.pengadaan.permintaan', compact(
//         'authUser',
//         'allPengajuan',
//         'permintaanPengajuan',
//         'perbaikanPengajuan',
//         'bidangs',
//         'search',
//         'statusFilter',
//         'bidangFilter',
//         'typeFilter',
//         'stats',
//         'tahunAnggaran',
//         'bidangId',
//         'ruangan'
//     ));
// }
 public function editprofile()
    {
        $authUser = Auth::user();

        // Daftar ruangan (sama seperti di admin)
        $daftarRuangan = [
            'CSSD/LAUNDRY','CODE BLUE','CSSD','DIREKTUR','UNIT HEMODIALISIS',
            'PENUNJANG','RUANG FLAMBOYAN','IGD','IPAL','INSTALASI HEMODIALISA',
            'INSTALASI BEDAH SENTRAL (IBS)','INSTALASI PEMELIHARAAN SARANA',
            'LABORATORIUM','RADIOLOGI','RUANG BOUGENVILE','RUANG SAKURA',
            'RUANG CEMPAKA','RUANG MATAHARI','RUANG MAWAR','RUANG MELATI',
            'RUANG ANGGREK','RUANG KENANGA','RUANG DAHLIA','RUANG TULIP',
            'Recovery Room (RR)','BRONKOSKOPI RUANGAN','RUANG GRAHA UTAMA',
            'RUANG TERATAI','RUANG TRANSIT','PPI LAYANAN','POLI RAWAT JALAN',
            'KEUANGAN','KESLING','MCU','SIMRS','KOMITE MEDIS','LAUNDRY',
            'GIZI','FARMASI','FLAMBOYAN','MANAJEMEN',
        ];

        $daftarJabatan = [
            'Administrator','Direktur','Wakil Direktur',
            'Kepala Bidang','Kepala Ruangan','Staff'
        ];

        return view('profile.karyawan.edit', compact('authUser', 'daftarRuangan', 'daftarJabatan'));
    }

    /**
     * Update profile karyawan.
     */
  public function updateprofile(Request $request)
{
    $user = Auth::user();
    $karyawan = $user->karyawan; // Ambil data relasi karyawan

    $validated = $request->validate([
        'nama'    => 'required|string|max:255',
        // Validasi NIP: cek unique di tabel karyawans, kecuali milik user ini sendiri
        'nip'     => 'nullable|string',
        'ruangan' => 'required|string|max:100',
        'ttd'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // 1. UPDATE TABEL USERS (Hanya kolom name)
    $user->update([
        'name' => $validated['nama'],
    ]);

    // 2. PERSIAPAN DATA UNTUK TABEL KARYAWANS
    $dataKaryawan = [
        'nama'    => $validated['nama'],
        'nip'     => $validated['nip'],
        'ruangan' => $validated['ruangan'],
    ];

    // Handle upload TTD
    if ($request->hasFile('ttd')) {
        // Hapus TTD lama di tabel karyawan jika ada
        if ($karyawan && $karyawan->ttd && Storage::disk('public')->exists('ttd/' . $karyawan->ttd)) {
            Storage::disk('public')->delete('ttd/' . $karyawan->ttd);
        }

        // Simpan TTD baru
        $file = $request->file('ttd');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('ttd', $filename, 'public');
        
        $dataKaryawan['ttd'] = $filename;
    }

    // 3. SIMPAN KE TABEL KARYAWANS
    if ($karyawan) {
        // Jika data karyawan sudah ada, update
        $karyawan->update($dataKaryawan);
    } else {
        // Jika belum ada, buat baru dan hubungkan ke user
        $user->karyawan()->create($dataKaryawan);
    }

    return redirect()
        ->route('pemohon.pengadaan')
        ->with('success', 'Profile dan data Karyawan berhasil diperbarui.');
}

public function chartpengadaan(Request $request)
{
    $authUser = Auth::user();
    
    // Ambil bidang_id dari karyawan yang login (PENERIMA)
    $bidangId = $authUser->karyawan->bidang_id ?? null;
    $ruangan = $authUser->karyawan->ruangan ?? null;
    
    // Ambil bidang untuk filter
    $bidangs = bidang::all();

    // Ambil query pencarian & filter
    $search = $request->input('search');
    $statusFilter = $request->input('status');
    $bidangFilter = $request->input('bidang');
    $typeFilter = $request->input('type');
    $tahunAnggaran = $request->input('tahun_anggaran');
    $bulanFilter = $request->input('bulan'); // <-- TAMBAHAN FILTER BULAN

    /**
     * ============================
     * QUERY DASAR PENGADAAN
     * ============================
     */
    $query = pengajuan::with(['items', 'karyawan', 'bidang'])
        ->where('status', '!=', 'draft')
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
        })
        ->when($tahunAnggaran, function ($q) use ($tahunAnggaran) {
            $q->where('tahun_anggaran', $tahunAnggaran);
        })
        ->when($bulanFilter, function ($q) use ($bulanFilter) {
            $q->whereMonth('tanggal_pengajuan', $bulanFilter); // <-- FILTER BULAN
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
     * DATA UNTUK TAB PENGADAAN
     * ============================
     */
    $permintaanQuery = clone $query;
    $permintaanPengajuan = $permintaanQuery->where('kategori', 'pengadaan')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

    /**
     * ============================
     * DATA UNTUK TAB PEMELIHARAAN
     * ============================
     */
    $perbaikanQuery = clone $query;
    $perbaikanPengajuan = $perbaikanQuery->where('kategori', 'pemeliharaan')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

    /**
     * ============================
     * STATISTIK DASHBOARD
     * ============================
     */
    $stats = [
        'total' => (clone $query)->count(),
        'diajukan' => (clone $query)->where('status', 'diajukan')->count(),
        'disetujui' => (clone $query)->whereIn('status', ['disetujui', 'selesai'])->count(),
        'ditolak' => (clone $query)->where('status', 'ditolak')->count(),
        'selesai' => (clone $query)->where('status', 'selesai')->count(),

        // Hitungan Badge Tab (Sinkron dengan filter)
        // 'permintaan_count' => (clone $query)
        //     ->where('status', 'LIKE', '%diajukan%')
        //     ->where(function($q) {
        //         $q->where('kategori', 'LIKE', '%pengadaan%')
        //           ->orWhereNull('kategori');
        //     })
        //     ->count(),
        'permintaan_count' => (clone $query)->where('kategori', 'pengadaan')->count(),
        
        'perbaikan_count'  => (clone $query)->where('kategori', 'pemeliharaan')->count(),
        // 'perbaikan_count' => (clone $query)
        //     ->where('status', 'LIKE', '%diajukan%')
        //     ->where(function($q) {
        //         $q->where('kategori', 'LIKE', '%pemeliharaan%')
        //           ->orWhereNull('kategori');
        //     })
        //     ->count(),
    ];

    return view('penerima.pengadaan.permintaan', compact(
        'authUser',
        'allPengajuan',
        'permintaanPengajuan',
        'perbaikanPengajuan',
        'bidangs',
        'search',
        'statusFilter',
        'bidangFilter',
        'typeFilter',
        'stats',
        'tahunAnggaran',
        'bidangId',
        'ruangan'
    ));
}

// public function chartpengadaan(Request $request)
// {
//     $authUser = Auth::user();

//     // Bidang & ruangan user atasan
//     $bidangId = $authUser->karyawan->bidang_id ?? null;
//     $ruangan  = $authUser->karyawan->ruangan ?? null;

//     $bidangs = bidang::all();

//     // Filter request
//     $search        = $request->input('search');
//     $statusFilter  = $request->input('status');
//     $bidangFilter  = $request->input('bidang');
//     $tahunAnggaran = $request->input('tahun_anggaran');

//     /**
//      * ============================
//      * QUERY DASAR PENGADAAN (ATASAN)public function chartpengadaan(Request $request)
// {
//     $authUser = Auth::user();

//     // Bidang & ruangan user atasan
//     $bidangId = $authUser->karyawan->bidang_id ?? null;
//     $ruangan  = $authUser->karyawan->ruangan ?? null;

//     $bidangs = bidang::all();

//     // Filter request
//     $search        = $request->input('search');
//     $statusFilter  = $request->input('status');
//     $bidangFilter  = $request->input('bidang');
//     $tahunAnggaran = $request->input('tahun_anggaran');

//     /**
//      * ============================
//      * QUERY DASAR PENGADAAN (ATASAN)
//      * ============================
//      */
//     $query = pengajuan::with(['items', 'karyawan', 'bidang'])
//         // ⚠️ Sesuaikan filter atasan ini dengan relasi di project Anda
//         ->where('penerima_id', $authUser->karyawan_id ?? $authUser->id)
//         ->where('status', '!=', 'draft')
//         ->when($search, function ($q) use ($search) {
//             $q->where(function ($query) use ($search) {
//                 $query->where('no_pengajuan', 'like', "%{$search}%")
//                       ->orWhere('dasar_usulan', 'like', "%{$search}%")
//                       ->orWhereHas('items', function ($itemQuery) use ($search) {
//                           $itemQuery->where('nama_barang', 'like', "%{$search}%")
//                                     ->orWhere('spesifikasi', 'like', "%{$search}%");
//                       });
//             });
//         })
//         ->when($statusFilter, function ($q) use ($statusFilter) {
//             $q->where('status', $statusFilter);
//         })
//         ->when($bidangFilter, function ($q) use ($bidangFilter) {
//             $q->where('bidang_id', $bidangFilter);
//         })
//         ->when($tahunAnggaran, function ($q) use ($tahunAnggaran) {
//             $q->where('tahun_anggaran', $tahunAnggaran);
//         });

//     /**
//      * ============================
//      * HELPER SCOPE: PERBAIKAN
//      * ============================
//      * Logic: Jika dasar_usulan mengandung kata kunci perbaikan
//      */
//     $scopePerbaikan = function ($q) {
//         $q->where(function ($sub) {
//             $sub->where('dasar_usulan', 'LIKE', '%Penggantian Barang Rusak%')
//                 ->orWhere('dasar_usulan', 'LIKE', '%perbaikan%')
//                 ->orWhere('dasar_usulan', 'LIKE', '%service%')
//                 ->orWhere('dasar_usulan', 'LIKE', '%repair%');
//         });
//     };

//     /**
//      * ============================
//      * HELPER SCOPE: PERMINTAAN
//      * ============================
//      * Logic: Semua data yang BUKAN perbaikan (termasuk NULL / kosong)
//      * Ini memastikan tidak ada data yang "hilang" dari kedua tab.
//      */
//     $scopePermintaan = function ($q) use ($scopePerbaikan) {
//         $q->where(function ($sub) use ($scopePerbaikan) {
//             $sub->where(function ($sub2) use ($scopePerbaikan) {
//                 // Cari yang tidak cocok dengan perbaikan
//                 $sub2->whereNot(function ($sub3) use ($scopePerbaikan) {
//                     $scopePerbaikan($sub3);
//                 })
//                 // Atau yang dasarnya NULL
//                 ->orWhereNull('dasar_usulan')
//                 // Atau yang dasarnya string kosong
//                 ->orWhere('dasar_usulan', '');
//             });
//         });
//     };

//     /**
//      * ============================
//      * 3 TABEL (SEMUA / PERMINTAAN / PERBAIKAN)
//      * ============================
//      */
//     $allPengajuan = (clone $query)
//         ->orderBy('created_at', 'desc')
//         ->paginate(10, ['*'], 'page_all')
//         ->appends($request->all());

//     $permintaanPengajuan = (clone $query)
//         ->tap($scopePermintaan)
//         ->orderBy('created_at', 'desc')
//         ->paginate(10, ['*'], 'page_permintaan')
//         ->appends($request->all());

//     $perbaikanPengajuan = (clone $query)
//         ->tap($scopePerbaikan)
//         ->orderBy('created_at', 'desc')
//         ->paginate(10, ['*'], 'page_perbaikan')
//         ->appends($request->all());

//     /**
//      * ============================
//      * STATISTIK DASHBOARD
//      * ============================
//      */
//     $baseCount = (clone $query);

//     $stats = [
//         'total'             => (clone $baseCount)->count(),
//         'draft'             => (clone $baseCount)->where('status', 'draft')->count(),
//         'revisi'            => (clone $baseCount)->where('status', 'revisi')->count(),
//         'diajukan'          => (clone $baseCount)->where('status', 'diajukan')->count(),
//         'disetujui'         => (clone $baseCount)->where('status', 'disetujui')->count(),
//         'ditolak'           => (clone $baseCount)->where('status', 'ditolak')->count(),
//         'menunggu_direktur' => (clone $baseCount)->where('status', 'menunggu_direktur')->count(),
        
//         // Hitung jumlah per kategori menggunakan scope yang baru
//         'permintaan_count'  => (clone $baseCount)->tap($scopePermintaan)->count(),
//         'perbaikan_count'   => (clone $baseCount)->tap($scopePerbaikan)->count(),
//     ];

//     return view('penerima.pengadaan.permintaan', compact(
//         'authUser',
//         'allPengajuan',
//         'permintaanPengajuan',
//         'perbaikanPengajuan',
//         'bidangs',
//         'search',
//         'statusFilter',
//         'bidangFilter',
//         'stats',
//         'tahunAnggaran',
//         'bidangId',
//         'ruangan'
//     ));
// }
//    public function chartpengadaan(Request $request)
//     {
//         $authUser = Auth::user();

//         // Bidang & ruangan user penerima
//         $bidangId = $authUser->karyawan->bidang_id ?? null;
//         $ruangan  = $authUser->karyawan->ruangan ?? null;

//         $bidangs = bidang::all();

//         // Filter request
//         $search        = $request->input('search');
//         $statusFilter  = $request->input('status');
//         $bidangFilter  = $request->input('bidang');
//         $typeFilter    = $request->input('type');
//         $tahunAnggaran = $request->input('tahun_anggaran');

//         /**
//          * ============================
//          * QUERY DASAR PENGADAAN
//          * ============================
//          */
//         $query = pengajuan::with(['items', 'karyawan', 'bidang'])
//             ->where('status', '!=', 'draft')
//             ->when($search, function ($q) use ($search) {
//                 $q->where(function ($query) use ($search) {
//                     $query->where('no_pengajuan', 'like', "%{$search}%")
//                           ->orWhere('dasar_usulan', 'like', "%{$search}%")
//                           ->orWhereHas('items', function ($itemQuery) use ($search) {
//                               $itemQuery->where('nama_barang', 'like', "%{$search}%")
//                                         ->orWhere('spesifikasi', 'like', "%{$search}%");
//                           });
//                 });
//             })
//             ->when($statusFilter, function ($q) use ($statusFilter) {
//                 $q->where('status', $statusFilter);
//             })
//             ->when($bidangFilter, function ($q) use ($bidangFilter) {
//                 $q->where('bidang_id', $bidangFilter);
//             })
//             ->when($tahunAnggaran, function ($q) use ($tahunAnggaran) {
//                 // Jika kolom tahun_anggaran ADA di tabel:
//                 $q->where('tahun_anggaran', $tahunAnggaran);
//                 // Jika TIDAK ada, pakai: $q->whereYear('tanggal_pengajuan', $tahunAnggaran);
//             });

//         // Helper closure logika permintaan / perbaikan
//         $scopePermintaan = function ($q) {
//             $q->where(function ($sub) {
//                 $sub->where('dasar_usulan', 'LIKE', '%Program Kerja%')
//                     ->orWhere('dasar_usulan', 'LIKE', '%Kebutuhan Operasional%')
//                     ->orWhere('dasar_usulan', 'LIKE', '%Penambahan Kapasitas Pelayanan%')
//                     ->orWhere('dasar_usulan', 'LIKE', '%Pemenuhan Standar Akreditasi%')
//                     ->orWhere('dasar_usulan', 'LIKE', '%Keselamatan Pasien%')
//                     ->orWhereNull('dasar_usulan');
//             });
//         };

//         $scopePerbaikan = function ($q) {
//             $q->where(function ($sub) {
//                 $sub->where('dasar_usulan', 'LIKE', '%Penggantian Barang Rusak%')
//                     ->orWhere('dasar_usulan', 'LIKE', '%perbaikan%')
//                     ->orWhere('dasar_usulan', 'LIKE', '%service%')
//                     ->orWhere('dasar_usulan', 'LIKE', '%repair%');
//             });
//         };

//         /**
//          * ============================
//          * TABEL: SEMUA PENGAJUAN
//          * ============================
//          */
//         $allPengajuan = (clone $query)
//             ->orderBy('created_at', 'desc')
//             ->paginate(10, ['*'], 'page_all')
//             ->appends($request->all());

//         /**
//          * ============================
//          * TABEL: PERMINTAAN
//          * ============================
//          */
//         $permintaanPengajuan = (clone $query)
//             ->tap($scopePermintaan)
//             ->orderBy('created_at', 'desc')
//             ->paginate(10, ['*'], 'page_permintaan')
//             ->appends($request->all());

//         /**
//          * ============================
//          * TABEL: PERBAIKAN
//          * ============================
//          */
//         $perbaikanPengajuan = (clone $query)
//             ->tap($scopePerbaikan)
//             ->orderBy('created_at', 'desc')
//             ->paginate(10, ['*'], 'page_perbaikan')
//             ->appends($request->all());

//         /**
//          * ============================
//          * STATISTIK DASHBOARD
//          * ============================
//          * Mengikuti query dasar (bukan query terpisah),
//          * supaya angka kartu konsisten dgn tabel.
//          */
//         $baseCount = (clone $query);

//         $stats = [
//             'total'             => (clone $baseCount)->count(),
//             'draft'             => (clone $baseCount)->where('status', 'draft')->count(),
//             'revisi'            => (clone $baseCount)->where('status', 'revisi')->count(),
//             'diajukan'          => (clone $baseCount)->where('status', 'diajukan')->count(),
//             'disetujui'         => (clone $baseCount)->where('status', 'disetujui')->count(),
//             'ditolak'           => (clone $baseCount)->where('status', 'ditolak')->count(),
//             'menunggu_direktur' => (clone $baseCount)->where('status', 'menunggu_direktur')->count(),
//             'permintaan_count'  => (clone $baseCount)->tap($scopePermintaan)->count(),
//             'perbaikan_count'   => (clone $baseCount)->tap($scopePerbaikan)->count(),
//         ];

//         return view('penerima.pengadaan.permintaan', compact(
//             'authUser',
//             'allPengajuan',
//             'permintaanPengajuan',
//             'perbaikanPengajuan',
//             'bidangs',
//             'search',
//             'statusFilter',
//             'bidangFilter',
//             'typeFilter',
//             'stats',
//             'tahunAnggaran',
//             'bidangId',
//             'ruangan'
//         ));
//     }

   public function showpengadaan($id)
{
    $authUser = Auth::user();
    
    $pengajuan = pengajuan::with([
        'karyawan',
        'bidang',
        'items',
        'items.barangTersedia',
    ])->findOrFail($id); // <-- HAPUS where('penerima_id', $authUser->id)

    return view('penerima.pengadaan.show', compact('pengajuan', 'authUser'));
}

public function verifikasi(Request $request, $id)
{
    $request->validate([
        'status_verifikasi' => 'required|in:disetujui_koordinator,ditolak,revisi,survei',
        'catatan_verifikasi' => 'required|string|min:5',
        'penerima_id' => 'required'
    ]);

    try {
        $pengajuan = pengajuan::findOrFail($id);
        
        $statusMap = [
            'disetujui_koordinator' => 'disetujui_koordinator',
            'ditolak' => 'ditolak',
            'revisi' => 'revisi',
            'survei' => 'perlu survei perbaikan'
            
        ];
        
        // Mapping untuk log_penerima (CUKUP STATUS SAJA)
        $logMap = [
            'disetujui_koordinator' => 'disetujui',
            'ditolak' => 'ditolak',
            'revisi' => 'revisi',
            'survei' => 'perlu survei perbaikan'
        ];
        
        $newStatus = $statusMap[$request->status_verifikasi];
        
        $pengajuan->update([
            'status' => $newStatus,
            'penerima_id' => $request->penerima_id,
            'catatan_unit' => $request->catatan_verifikasi,
            'diterima_at' => $request->status_verifikasi == 'disetujui_koordinator' ? now() : null,
            'log_status_penerima' => $newStatus, // ✅ CUKUP STATUS
        ]);

        return redirect()
            ->route('penerima.chartp')
            ->with('success', '✅ Pengajuan berhasil diverifikasi!');

    } catch (\Exception $e) {
        return back()
            ->withInput()
            ->with('error', '❌ Gagal verifikasi: ' . $e->getMessage());
    }
}


// public function reportPengadaan(Request $request)
// {
//     $statuses = [
//         'draft' => 'Draft',
//         'diajukan' => 'Diajukan',
//         'disetujui_koordinator' => 'Disetujui Koordinator',
//         'disetujui_kabid' => 'Disetujui Kabid',
//         'dipertimbangkan' => 'Dipertimbangkan',
//         'menunggu_direktur' => 'Menunggu Direktur',
//         'disetujui' => 'Disetujui',
//         'disetujui sebagian' => 'Disetujui Sebagian',
//         'ditolak' => 'Ditolak',
//         'revisi' => 'Revisi',
//         'ditunda' => 'Ditunda',
//     ];

//     // AMBIL DATA ATASAN
//     $atasans = karyawan::whereIn('id', function($query) {
//         $query->select('atasan_id')
//               ->from('pengajuans')
//               ->whereNotNull('atasan_id');
//     })->get();

//     // TETAP MENGGUNAKAN tanggal_pengajuan
//     $tahuns = pengajuan::selectRaw('DISTINCT YEAR(tanggal_pengajuan) as tahun')
//                 ->whereNotNull('tanggal_pengajuan') // Tambahkan ini untuk keamanan
//                 ->orderBy('tahun', 'desc')
//                 ->pluck('tahun')
//                 ->toArray();

//     // AMBIL FILTER DARI REQUEST
//     $tahunFilter = $request->input('tahun');
//     $bulanFilter = $request->input('bulan');
//     $statusFilter = $request->input('status');
//     $atasanIdFilter = $request->input('atasan_id');

//     // QUERY UNTUK PREVIEW - TETAP MENGGUNAKAN tanggal_pengajuan
//     $query = pengajuan::with([
//         'bidang',
//         'items',
//         'karyawan',
//         'penerima',
//         'atasan',
//         'direktur',
//     ]);

//     if ($tahunFilter) {
//         $query->whereYear('tanggal_pengajuan', $tahunFilter);
//     }

//     if ($bulanFilter) {
//         $query->whereMonth('tanggal_pengajuan', $bulanFilter);
//     }

//     if ($statusFilter !== null && $statusFilter !== '') {
//         $query->where('status', $statusFilter);
//     }

//     if ($atasanIdFilter) {
//         $query->where('atasan_id', $atasanIdFilter);
//     }

//     $pengadaans = $query->orderBy('tanggal_pengajuan', 'desc')->get();

//     return view('penerima.pengadaan.laporan', compact(
//         'statuses', 
//         'atasans', 
//         'tahuns',
//         'pengadaans',
//         'tahunFilter',
//         'bulanFilter',
//         'statusFilter',
//         'atasanIdFilter'
//     ));
// }

  public function reportPengadaan(Request $request)
    {
        $statuses = [
            'draft' => 'Draft',
            'diajukan' => 'Diajukan',
            'disetujui_koordinator' => 'Disetujui Koordinator',
            'disetujui_kabid' => 'Disetujui Kabid',
            'dipertimbangkan' => 'Dipertimbangkan',
            'menunggu_direktur' => 'Menunggu Direktur',
            'disetujui' => 'Disetujui',
            'disetujui sebagian' => 'Disetujui Sebagian',
            'ditolak' => 'Ditolak',
            'revisi' => 'Revisi',
            'ditunda' => 'Ditunda',
        ];

        // AMBIL DATA ATASAN
        $atasans = karyawan::whereIn('id', function($query) {
            $query->select('atasan_id')
                  ->from('pengajuans')
                  ->whereNotNull('atasan_id');
        })->get();

        // AMBIL TAHUN
        $tahuns = pengajuan::selectRaw('DISTINCT YEAR(tanggal_pengajuan) as tahun')
                    ->whereNotNull('tanggal_pengajuan')
                    ->orderBy('tahun', 'desc')
                    ->pluck('tahun')
                    ->toArray();

        // AMBIL FILTER DARI REQUEST
        $tahunFilter = $request->input('tahun');
        $bulanFilter = $request->input('bulan');
        $statusFilter = $request->input('status');
        $atasanIdFilter = $request->input('atasan_id');

        // QUERY UNTUK PREVIEW
        $query = pengajuan::with([
            'bidang',
            'items',
            'karyawan',
            'penerima',
            'atasan',
            'direktur',
        ]);

        if ($tahunFilter) {
            $query->whereYear('tanggal_pengajuan', $tahunFilter);
        }

        if ($bulanFilter) {
            $query->whereMonth('tanggal_pengajuan', $bulanFilter);
        }

        if ($statusFilter !== null && $statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        if ($atasanIdFilter) {
            $query->where('atasan_id', $atasanIdFilter);
        }

        $pengadaans = $query->orderBy('tanggal_pengajuan', 'desc')->get();

        return view('penerima.pengadaan.laporan', compact(
            'statuses', 
            'atasans', 
            'tahuns',
            'pengadaans',
            'tahunFilter',
            'bulanFilter',
            'statusFilter',
            'atasanIdFilter'
        ));
    }

    private function formatStatus($status)
{
    $map = [
        'draft' => 'Draft',
        'diajukan' => 'Diajukan',
        'disetujui_koordinator' => 'Disetujui Koordinator',
        'disetujui_kabid' => 'Disetujui Kabid',
        'dipertimbangkan' => 'Dipertimbangkan',
        'menunggu_direktur' => 'Menunggu Direktur',
        'disetujui' => 'Disetujui',
        'disetujui sebagian' => 'Disetujui Sebagian',
        'ditolak' => 'Ditolak',
        'revisi' => 'Revisi',
        'ditunda' => 'Ditunda',
    ];

    return $map[$status] ?? $status;
}

//    public function exportExcel(Request $request)
//     {
//         // ==========================================================
//         // AMBIL PARAMETER FILTER
//         // ==========================================================
//         $tahun = $request->input('tahun');
//         $bulan = $request->input('bulan');
//         $status = $request->input('status');
//         $atasanId = $request->input('atasan_id');

//         // ==========================================================
//         // QUERY DATA
//         // ==========================================================
//         $query = pengajuan::with([
//             'bidang',
//             'items',
//             'karyawan',
//             'penerima',
//             'atasan',
//             'direktur',
//         ]);

//         // Filter tahun
//         if ($tahun) {
//             $query->whereYear('tanggal_pengajuan', $tahun);
//         }

//         // Filter bulan
//         if ($bulan) {
//             $query->whereMonth('tanggal_pengajuan', $bulan);
//         }

//         // Filter status
//         if ($status !== null && $status !== '') {
//             $query->where('status', $status);
//         }

//         // Filter atasan
//         if ($atasanId) {
//             $query->where('atasan_id', $atasanId);
//         }

//         // Ambil data
//         $pengadaan = $query
//             ->orderBy('tanggal_pengajuan', 'desc')
//             ->get();

//         // ==========================================================
//         // CEK DATA KOSONG
//         // ==========================================================
//         if ($pengadaan->isEmpty()) {
//             return back()->with('error', 'Tidak ada data untuk diexport.');
//         }

//         // ==========================================================
//         // BUAT SPREADSHEET
//         // ==========================================================
//         $spreadsheet = new Spreadsheet();

//         // Hapus sheet default
//         $defaultSheet = $spreadsheet->getActiveSheet();
//         $spreadsheet->removeSheetByIndex($spreadsheet->getIndex($defaultSheet));

//         // ==========================================================
//         // SHEET 1 - SEMUA DATA
//         // ==========================================================
//         $sheetSemua = $spreadsheet->createSheet();
//         $sheetSemua->setTitle('Semua Data');

//         $this->buatSheetPengadaan(
//             $sheetSemua,
//             $pengadaan,
//             $tahun,
//             $bulan,
//             $status,
//             'Semua Atasan'
//         );

//         // ==========================================================
//         // KELOMPOKKAN DATA BERDASARKAN ATASAN
//         // ==========================================================
//         $dataPerAtasan = $pengadaan->groupBy('atasan_id');

//         foreach ($dataPerAtasan as $idAtasan => $dataAtasan) {

//             // Ambil data atasan dari pengajuan pertama
//             $pengajuanPertama = $dataAtasan->first();
//             $atasan = $pengajuanPertama?->atasan;

//             // Ambil nama atasan
//             $namaAtasan = $atasan?->nama_karyawan
//                 ?? $atasan?->nama
//                 ?? 'Tanpa Atasan';

//             // NAMA SHEET EXCEL MAKSIMAL 31 KARAKTER
//             $namaSheet = trim($namaAtasan);

//             // Hilangkan karakter yang tidak diperbolehkan Excel
//             $namaSheet = str_replace(
//                 ['\\', '/', '*', '[', ']', ':', '?'],
//                 '',
//                 $namaSheet
//             );

//             if ($namaSheet === '') {
//                 $namaSheet = 'Tanpa Atasan';
//             }

//             // Maksimal 31 karakter
//             $namaSheet = mb_substr($namaSheet, 0, 31);

//             // CEGAH DUPLIKAT NAMA SHEET
//             $namaSheetOriginal = $namaSheet;
//             $counter = 1;

//             while ($spreadsheet->sheetNameExists($namaSheet)) {
//                 $suffix = ' ' . $counter;
//                 $namaSheet = mb_substr($namaSheetOriginal, 0, 31 - mb_strlen($suffix)) . $suffix;
//                 $counter++;
//             }

//             // BUAT SHEET ATASAN
//             $sheetAtasan = $spreadsheet->createSheet();
//             $sheetAtasan->setTitle($namaSheet);

//             $this->buatSheetPengadaan(
//                 $sheetAtasan,
//                 $dataAtasan,
//                 $tahun,
//                 $bulan,
//                 $status,
//                 $namaAtasan
//             );
//         }

//         // ==========================================================
//         // AKTIFKAN SHEET PERTAMA
//         // ==========================================================
//         $spreadsheet->setActiveSheetIndex(0);

//         // ==========================================================
//         // DOWNLOAD
//         // ==========================================================
//         $writer = new Xlsx($spreadsheet);

//         $filename = 'Laporan_Pengadaan_' . date('Ymd_His') . '.xlsx';

//         return new StreamedResponse(
//             function () use ($writer) {
//                 $writer->save('php://output');
//             },
//             200,
//             [
//                 'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
//                 'Content-Disposition' => 'attachment; filename="' . $filename . '"',
//                 'Cache-Control' => 'max-age=0',
//                 'Pragma' => 'public',
//             ]
//         );
//     }

public function exportExcel(Request $request)
{
    // ==========================================================
    // AMBIL PARAMETER FILTER
    // ==========================================================
    $tahun    = $request->input('tahun');
    $bulan    = $request->input('bulan');
    $status   = $request->input('status');
    $atasanId = $request->input('atasan_id');

    // ==========================================================
    // QUERY DATA
    // ==========================================================
    $query = pengajuan::with([
        'bidang',
        'items',
        'karyawan',
        'penerima',
        'atasan',
        'direktur',
    ]);

    // Filter tahun
    if ($tahun) {
        $query->whereYear('tanggal_pengajuan', $tahun);
    }

    // Filter bulan
    if ($bulan) {
        $query->whereMonth('tanggal_pengajuan', $bulan);
    }

    // Filter status
    if ($status !== null && $status !== '') {
        $query->where('status', $status);
    }

    // Filter atasan
    if ($atasanId) {
        $query->where('atasan_id', $atasanId);
    }

    // Ambil data
    $pengadaan = $query
        ->orderBy('tanggal_pengajuan', 'desc')
        ->get();

    // ==========================================================
    // CEK DATA KOSONG
    // → TETAP EXPORT, isi tulisan "Tidak ada data"
    // ==========================================================
    if ($pengadaan->isEmpty()) {
        return $this->exportKosong($tahun, $bulan, $status, $atasanId);
    }

    // ==========================================================
    // BUAT SPREADSHEET
    // ==========================================================
    $spreadsheet = new Spreadsheet();

    // Hapus sheet default
    $defaultSheet = $spreadsheet->getActiveSheet();
    $spreadsheet->removeSheetByIndex($spreadsheet->getIndex($defaultSheet));

    // ==========================================================
    // SHEET 1 - SEMUA DATA
    // ==========================================================
    $sheetSemua = $spreadsheet->createSheet();
    $sheetSemua->setTitle('Semua Data');

    $this->buatSheetPengadaan(
        $sheetSemua,
        $pengadaan,
        $tahun,
        $bulan,
        $status,
        'Semua Atasan'
    );

    // ==========================================================
    // KELOMPOKKAN DATA BERDASARKAN ATASAN
    // ==========================================================
    $dataPerAtasan = $pengadaan->groupBy('atasan_id');

    foreach ($dataPerAtasan as $idAtasan => $dataAtasan) {

        // Ambil data atasan dari pengajuan pertama
        $pengajuanPertama = $dataAtasan->first();
        $atasan = $pengajuanPertama?->atasan;

        // Ambil nama atasan
        $namaAtasan = $atasan?->nama_karyawan
            ?? $atasan?->nama
            ?? 'Tanpa Atasan';

        // NAMA SHEET EXCEL MAKSIMAL 31 KARAKTER
        $namaSheet = trim($namaAtasan);

        // Hilangkan karakter yang tidak diperbolehkan Excel
        $namaSheet = str_replace(
            ['\\', '/', '*', '[', ']', ':', '?'],
            '',
            $namaSheet
        );

        if ($namaSheet === '') {
            $namaSheet = 'Tanpa Atasan';
        }

        // Maksimal 31 karakter
        $namaSheet = mb_substr($namaSheet, 0, 31);

        // CEGAH DUPLIKAT NAMA SHEET
        $namaSheetOriginal = $namaSheet;
        $counter = 1;

        while ($spreadsheet->sheetNameExists($namaSheet)) {
            $suffix = ' ' . $counter;
            $namaSheet = mb_substr($namaSheetOriginal, 0, 31 - mb_strlen($suffix)) . $suffix;
            $counter++;
        }

        // BUAT SHEET ATASAN
        $sheetAtasan = $spreadsheet->createSheet();
        $sheetAtasan->setTitle($namaSheet);

        $this->buatSheetPengadaan(
            $sheetAtasan,
            $dataAtasan,
            $tahun,
            $bulan,
            $status,
            $namaAtasan
        );
    }

    // ==========================================================
    // AKTIFKAN SHEET PERTAMA
    // ==========================================================
    $spreadsheet->setActiveSheetIndex(0);

    // ==========================================================
    // DOWNLOAD
    // ==========================================================
    $writer = new Xlsx($spreadsheet);

    $filename = 'Laporan_Pengadaan_' . date('Ymd_His') . '.xlsx';

    return new StreamedResponse(
        function () use ($writer) {
            $writer->save('php://output');
        },
        200,
        [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
            'Pragma' => 'public',
        ]
    );
}

    /**
     * Method untuk membuat sheet pengadaan
     */
    private function buatSheetPengadaan(
        $sheet,
        $pengadaan,
        $tahun,
        $bulan,
        $status,
        $namaAtasan
    ) {
        // ==========================================================
        // HEADER JUDUL
        // ==========================================================
        $sheet->setCellValue('A1', 'LAPORAN PENGADAAN');
        $sheet->mergeCells('A1:P1');

        $sheet->getStyle('A1')
            ->getFont()
            ->setBold(true)
            ->setSize(16);

        $sheet->getStyle('A1')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ==========================================================
        // INFO FILTER
        // ==========================================================
        $filterText = 'Periode: ';

        if ($bulan) {
            $filterText .= date('F', mktime(0, 0, 0, $bulan, 1)) . ' ';
        }

        $filterText .= $tahun ? $tahun : 'Semua Tahun';
        $filterText .= ' | Status: ';

        if ($status !== null && $status !== '') {
            $filterText .= $this->getStatusLabel($status);
        } else {
            $filterText .= 'Semua';
        }

        $filterText .= ' | Atasan: ' . $namaAtasan;

        $sheet->setCellValue('A2', $filterText);
        $sheet->mergeCells('A2:P2');

        $sheet->getStyle('A2')
            ->getFont()
            ->setSize(11);

        $sheet->getStyle('A2')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ==========================================================
        // HEADER TABEL
        // ==========================================================
        $headers = [
            'No',
            'Tanggal',
            'No Pengajuan',
            'Pengusul',
            'Penerima',
            'Kepala Bidang',
            'Direktur',
            'Nama Barang',
            'Jumlah',
            'Harga / Unit',
            'Total Harga',
            'Total Disetujui Direktur',
            'Bidang',
            'Status',
            'Status Direktur',
            'Tgl Direktur'
        ];

        $headerRow = 4;
        $column = 'A';

        foreach ($headers as $header) {
            $sheet->setCellValue($column . $headerRow, $header);
            $column++;
        }

        // ==========================================================
        // STYLE HEADER
        // ==========================================================
        $headerRange = 'A4:P4';
        $sheet->getStyle($headerRange)
            ->getFont()
            ->setBold(true)
            ->setSize(11);

        $sheet->getStyle($headerRange)
            ->getFont()
            ->getColor()
            ->setARGB('FFFFFFFF');

        $sheet->getStyle($headerRange)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle($headerRange)
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle($headerRange)
            ->getAlignment()
            ->setWrapText(true);

        $sheet->getStyle($headerRange)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID);

        $sheet->getStyle($headerRange)
            ->getFill()
            ->getStartColor()
            ->setARGB('FF4CAF50');

        // ==========================================================
        // DATA
        // ==========================================================
        $row = 5;
        $no = 1;

        // VARIABEL UNTUK TOTAL
        $totalHargaKeseluruhan = 0;
        $totalDisetujuiDirekturKeseluruhan = 0;
        $totalItem = 0;

        foreach ($pengadaan as $item) {

            // Satu pengajuan bisa memiliki banyak item
            foreach ($item->items as $barang) {

                // HARGA SATUAN
                $hargaSatuan = (float) ($barang->harga_satuan ?? 0);

                // TOTAL HARGA
                if ($barang->harga !== null) {
                    $totalHarga = (float) $barang->harga;
                } else {
                    $totalHarga = (float) ($barang->jumlah ?? 0) * $hargaSatuan;
                }

                // TOTAL DISETUJUI DIREKTUR
                $totalDisetujuiDirektur = (float) ($item->total_disetujui_direktur ?? 0);

                // AKUMULASI TOTAL
                $totalHargaKeseluruhan += $totalHarga;
                $totalDisetujuiDirekturKeseluruhan += $totalDisetujuiDirektur;
                $totalItem++;

                // STATUS DIREKTUR
                $statusDirektur = $item->log_status_direktur ?? $item->status;
                
                // TANGGAL DISETUJUI/DITOLAK DIREKTUR
                $tanggalDirektur = $item->disetujui_direktur_at 
                    ? date('d/m/Y H:i', strtotime($item->disetujui_direktur_at))
                    : '-';

                // NO
                $sheet->setCellValue('A' . $row, $no);

                // TANGGAL
                $sheet->setCellValue(
                    'B' . $row,
                    $item->tanggal_pengajuan
                        ? date('d/m/Y', strtotime($item->tanggal_pengajuan))
                        : '-'
                );

                // NO PENGAJUAN
                $sheet->setCellValue('C' . $row, $item->no_pengajuan ?? '-');

                // PENGUSUL
                $sheet->setCellValue(
                    'D' . $row,
                    $item->karyawan?->nama_karyawan
                        ?? $item->karyawan?->nama
                        ?? '-'
                );

                // PENERIMA
                $sheet->setCellValue(
                    'E' . $row,
                    $item->penerima?->nama_karyawan
                        ?? $item->penerima?->nama
                        ?? '-'
                );

                // KEPALA BIDANG (ATASAN)
                $sheet->setCellValue(
                    'F' . $row,
                    $item->atasan?->nama_karyawan
                        ?? $item->atasan?->nama
                        ?? '-'
                );

                // DIREKTUR
                $sheet->setCellValue(
                    'G' . $row,
                    $item->direktur?->nama_karyawan
                        ?? $item->direktur?->nama
                        ?? '-'
                );

                // NAMA BARANG
                $sheet->setCellValue('H' . $row, $barang->nama_barang ?? '-');

                // JUMLAH
                $sheet->setCellValue('I' . $row, (float) ($barang->jumlah ?? 0));

                // HARGA / UNIT
                $sheet->setCellValue('J' . $row, $hargaSatuan);

                // TOTAL HARGA
                $sheet->setCellValue('K' . $row, $totalHarga);

                // TOTAL DISETUJUI DIREKTUR
                $sheet->setCellValue('L' . $row, $totalDisetujuiDirektur);

                // BIDANG
                $sheet->setCellValue(
                    'M' . $row,
                    $item->bidang?->nama_bidang
                        ?? $item->bidang?->nama
                        ?? '-'
                );

                // STATUS
                $sheet->setCellValue(
                    'N' . $row,
                    $this->getStatusLabel($item->status)
                );

                // STATUS DIREKTUR
                $sheet->setCellValue(
                    'O' . $row,
                    $this->getStatusLabel($statusDirektur)
                );

                // TANGGAL DIREKTUR
                $sheet->setCellValue(
                    'P' . $row,
                    $tanggalDirektur
                );

                // FORMAT RUPIAH
                $sheet->getStyle('J' . $row . ':L' . $row)
                    ->getNumberFormat()
                    ->setFormatCode('"Rp" #,##0');

                // ALIGNMENT
                $sheet->getStyle('A' . $row . ':P' . $row)
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->getStyle('A' . $row)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('I' . $row)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // WRAP TEXT
                $sheet->getStyle('D' . $row . ':H' . $row)
                    ->getAlignment()
                    ->setWrapText(true);

                $sheet->getStyle('M' . $row . ':P' . $row)
                    ->getAlignment()
                    ->setWrapText(true);

                // WARNA BACKGROUND UNTUK STATUS DIREKTUR
                if ($statusDirektur == 'disetujui') {
                    $sheet->getStyle('O' . $row . ':P' . $row)
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('FFC6EFCE');
                } elseif ($statusDirektur == 'ditolak') {
                    $sheet->getStyle('O' . $row . ':P' . $row)
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('FFFFC7CE');
                }

                $row++;
                $no++;
            }
        }

        // ==========================================================
        // AUTO SIZE COLUMN
        // ==========================================================
        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Lebar minimum beberapa kolom
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(14);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(22);
        $sheet->getColumnDimension('E')->setWidth(22);
        $sheet->getColumnDimension('F')->setWidth(22);
        $sheet->getColumnDimension('G')->setWidth(22);
        $sheet->getColumnDimension('H')->setWidth(30);
        $sheet->getColumnDimension('I')->setWidth(10);
        $sheet->getColumnDimension('J')->setWidth(18);
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->getColumnDimension('L')->setWidth(25);
        $sheet->getColumnDimension('M')->setWidth(20);
        $sheet->getColumnDimension('N')->setWidth(18);
        $sheet->getColumnDimension('O')->setWidth(18);
        $sheet->getColumnDimension('P')->setWidth(18);

        // ==========================================================
        // BORDER
        // ==========================================================
        if ($row > 5) {
            $dataRange = 'A4:P' . ($row - 1);
            $sheet->getStyle($dataRange)
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
        }

        // ==========================================================
        // FOOTER - TOTAL (RAPIH)
        // ==========================================================
        $footerRow = $row + 2;

        // Total Data
        $sheet->setCellValue('C' . $footerRow, 'Total Data :');
        $sheet->mergeCells('C' . $footerRow . ':D' . $footerRow);
        $sheet->getStyle('C' . $footerRow)->getFont()->setBold(true);
        $sheet->getStyle('C' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $footerRow, $totalItem . ' item');
        $sheet->getStyle('E' . $footerRow)->getFont()->setBold(true);

        // Total Permintaan Unit
        $footerRow2 = $footerRow + 1;
        $sheet->setCellValue('C' . $footerRow2, 'Total Permintaan Unit :');
        $sheet->mergeCells('C' . $footerRow2 . ':D' . $footerRow2);
        $sheet->getStyle('C' . $footerRow2)->getFont()->setBold(true);
        $sheet->getStyle('C' . $footerRow2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $footerRow2, $totalHargaKeseluruhan);
        $sheet->getStyle('E' . $footerRow2)->getNumberFormat()->setFormatCode('"Rp" #,##0');
        $sheet->getStyle('E' . $footerRow2)->getFont()->setBold(true);

        // Total Disetujui Direktur
        $footerRow3 = $footerRow2 + 1;
        $sheet->setCellValue('C' . $footerRow3, 'Total Disetujui Direktur :');
        $sheet->mergeCells('C' . $footerRow3 . ':D' . $footerRow3);
        $sheet->getStyle('C' . $footerRow3)->getFont()->setBold(true);
        $sheet->getStyle('C' . $footerRow3)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $footerRow3, $totalDisetujuiDirekturKeseluruhan);
        $sheet->getStyle('E' . $footerRow3)->getNumberFormat()->setFormatCode('"Rp" #,##0');
        $sheet->getStyle('E' . $footerRow3)->getFont()->setBold(true);

        // ==========================================================
        // TANGGAL CETAK
        // ==========================================================
        $printRow = $footerRow3 + 2;

        $sheet->setCellValue('C' . $printRow, 'Dicetak pada: ' . date('d/m/Y H:i:s'));
        $sheet->mergeCells('C' . $printRow . ':E' . $printRow);

        $sheet->getStyle('C' . $printRow)
            ->getFont()
            ->setSize(9);

        $sheet->getStyle('C' . $printRow)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // ==========================================================
        // FREEZE HEADER
        // ==========================================================
        $sheet->freezePane('A5');

        // ==========================================================
        // PAGE SETUP
        // ==========================================================
        $sheet->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $sheet->getPageSetup()
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $sheet->getPageSetup()
            ->setFitToWidth(1);

        $sheet->getPageSetup()
            ->setFitToHeight(0);

        $sheet->getPageMargins()
            ->setTop(0.5)
            ->setRight(0.3)
            ->setLeft(0.3)
            ->setBottom(0.5);
    }

    private function exportKosong($tahun, $bulan, $status, $atasanId)
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Laporan Pengadaan');

    // ==========================================================
    // SUSUN KETERANGAN FILTER
    // ==========================================================
    $filterDesc = [];

    if ($bulan) {
        $bulanNama = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
            5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
            9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
        ][(int) $bulan] ?? $bulan;
        $filterDesc[] = "Bulan: {$bulanNama}";
    }

    if ($tahun) {
        $filterDesc[] = "Tahun: {$tahun}";
    }

    if ($atasanId) {
        $atasan = \App\Models\Karyawan::find($atasanId);
        if ($atasan) {
            $filterDesc[] = "Atasan: " . ($atasan->nama_karyawan ?? $atasan->nama);
        }
    }

    if ($status !== null && $status !== '') {
        $filterDesc[] = "Status: " . $this->getStatusLabel($status);
    }

    $filterText = count($filterDesc)
        ? implode(' | ', $filterDesc)
        : 'Semua Data';

    // ==========================================================
    // JUDUL
    // ==========================================================
    $sheet->setCellValue('A1', 'LAPORAN PENGADAAN');
    $sheet->mergeCells('A1:D1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // ==========================================================
    // INFO FILTER
    // ==========================================================
    $sheet->setCellValue('A2', $filterText);
    $sheet->mergeCells('A2:D2');
    $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A2')->getFont()->setSize(11);

    // ==========================================================
    // TULISAN "TIDAK ADA DATA"
    // ==========================================================
    $sheet->setCellValue('A5', 'Tidak ada data untuk filter yang dipilih.');
    $sheet->mergeCells('A5:D5');
    $sheet->getStyle('A5')->getFont()
        ->setBold(true)
        ->setSize(12)
        ->getColor()->setARGB('FFB00020');
    $sheet->getStyle('A5')->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->getRowDimension(5)->setRowHeight(30);

    // ==========================================================
    // TANGGAL CETAK
    // ==========================================================
    $sheet->setCellValue('A7', 'Dicetak pada: ' . date('d/m/Y H:i:s'));
    $sheet->mergeCells('A7:D7');
    $sheet->getStyle('A7')->getFont()->setSize(9);
    $sheet->getStyle('A7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

    // ==========================================================
    // AUTO SIZE
    // ==========================================================
    foreach (range('A', 'D') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // ==========================================================
    // DOWNLOAD
    // ==========================================================
    $writer   = new Xlsx($spreadsheet);
    $filename = 'Laporan_Pengadaan_KOSONG_' . date('Ymd_His') . '.xlsx';

    return new StreamedResponse(
        function () use ($writer) {
            $writer->save('php://output');
        },
        200,
        [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
            'Pragma' => 'public',
        ]
    );
}
     public function chartspengadaans(){

        return view('penerima.pengadaan.chart');
        
    }
    
  public function chartspengadaan(Request $request)
    {
        $authUser = Auth::user();
        $bidangId = $authUser->karyawan->bidang_id ?? null;

        // ===== AMBIL DATA BIDANG UNTUK DITAMPILKAN =====
        $bidangs = Bidang::orderBy('nama_bidang')->get();
        
        // ===== AMBIL RUANGAN UNIK DARI BIDANG USER =====
        $ruangans = Pengajuan::select('instalasi')
            ->distinct()
            ->whereNotNull('instalasi')
            ->when($bidangId, function($query) use ($bidangId) {
                return $query->where('bidang_id', $bidangId);
            })
            ->orderBy('instalasi')
            ->pluck('instalasi')
            ->toArray();

        // ===== FILTER REQUEST =====
        $filterRuangan = $request->filter_ruangan;
        $filterTahun = $request->tahun_anggaran;
        $filterStatus = $request->status;

        // ===== QUERY DASAR - OTOMATIS FILTER BIDANG USER =====
        $baseQuery = Pengajuan::query();

        // 🔥 WAJIB: Hanya data dari bidang user yang login
        if ($bidangId) {
            $baseQuery->where('bidang_id', $bidangId);
        }

        // Filter Ruangan
        if ($filterRuangan) {
            $baseQuery->where('instalasi', $filterRuangan);
        }

        // Filter Tahun
        if ($filterTahun) {
            $baseQuery->where('tahun_anggaran', $filterTahun);
        }

        // Filter Status
        if ($filterStatus) {
            $baseQuery->where('status', $filterStatus);
        }

        // ===== STATISTIK =====
        $stats = [
            'total' => (clone $baseQuery)->count(),
            'diajukan' => (clone $baseQuery)->where('status', 'diajukan')->count(),
            // 'diajukan' => (clone $baseQuery)->whereIn('status', ['diajukan', 'disetujui_koordinator', 'disetujui_kabid'])->count(),
            'disetujui' => (clone $baseQuery)->where('status', 'disetujui')->count(),
            'ditolak' => (clone $baseQuery)->where('status', 'ditolak')->count(),
            'menunggu_direktur' => (clone $baseQuery)->where('status', 'menunggu_direktur')->count(),
        ];

        // ===== CHART DATA =====
        $chartData = $this->getChartData($baseQuery, $bidangId, $filterRuangan, $filterTahun);

        // ===== REKAP BULANAN =====
        $rekapBulanan = $this->getRekapBulanan($baseQuery);

        // ===== DATA PAGINATION =====
        $allPengajuan = $baseQuery->with(['bidang', 'karyawan', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('penerima.pengadaan.chart', compact(
            'authUser',
            'stats',
            'chartData',
            'rekapBulanan',
            'allPengajuan',
            'bidangs',
            'ruangans',
            'filterRuangan',
            'filterTahun',
            'filterStatus'
        ));
    }

    /**
     * Get data untuk chart
     */
    private function getChartData($baseQuery, $bidangId, $filterRuangan, $filterTahun)
    {
        // ===== 1. STATUS DISTRIBUSI - HANYA DISETUJUI & DITOLAK =====
        $statusQuery = clone $baseQuery;
        
        $statusData = [
            'Disetujui' => (clone $statusQuery)->where('status', 'disetujui')->count(),
            'Ditolak' => (clone $statusQuery)->where('status', 'ditolak')->count(),
        ];

        // ===== 2. TREND PER BULAN (6 bulan terakhir) =====
        $trendQuery = clone $baseQuery;
        $trendQuery->where('status', '!=', 'draft')
            ->where('status', '!=', 'ditolak')
            ->where('tanggal_pengajuan', '>=', now()->subMonths(6));

        $trendData = $trendQuery
            ->select(
                DB::raw('MONTH(tanggal_pengajuan) as bulan'),
                DB::raw('YEAR(tanggal_pengajuan) as tahun'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

        $trend = [];
        $bulanNama = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        
        foreach ($trendData as $item) {
            $trend[] = [
                'bulan' => $bulanNama[$item->bulan - 1] . ' ' . $item->tahun,
                'total' => $item->total
            ];
        }

        // ===== 3. TOP BARANG =====
        $topBarangQuery = pengajuan_item::query()
            ->join('pengajuans', 'pengajuan_items.pengajuan_id', '=', 'pengajuans.id')
            ->where('pengajuans.status', '!=', 'draft')
            ->where('pengajuans.status', '!=', 'ditolak');

        if ($bidangId) {
            $topBarangQuery->where('pengajuans.bidang_id', $bidangId);
        }
        if ($filterRuangan) {
            $topBarangQuery->where('pengajuans.instalasi', $filterRuangan);
        }
        if ($filterTahun) {
            $topBarangQuery->where('pengajuans.tahun_anggaran', $filterTahun);
        }

        $topBarang = $topBarangQuery
            ->select(
                'pengajuan_items.nama_barang',
                DB::raw('SUM(pengajuan_items.jumlah) as total_jumlah')
            )
            ->groupBy('pengajuan_items.nama_barang')
            ->orderBy('total_jumlah', 'desc')
            ->limit(10)
            ->get();

        $topBarangData = [];
        foreach ($topBarang as $item) {
            $topBarangData[] = [
                'nama' => $item->nama_barang,
                'jumlah' => (int) $item->total_jumlah
            ];
        }

        // ===== 4. NILAI PER BULAN =====
        $nilaiQuery = clone $baseQuery;
        $nilaiQuery->where('status', 'disetujui')
            ->where('tanggal_pengajuan', '>=', now()->subMonths(6));

        $nilaiData = $nilaiQuery
            ->select(
                DB::raw('MONTH(tanggal_pengajuan) as bulan'),
                DB::raw('YEAR(tanggal_pengajuan) as tahun'),
                DB::raw('SUM(total_pengajuan) as total_nilai')
            )
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

        $nilai = [];
        foreach ($nilaiData as $item) {
            $nilai[] = [
                'bulan' => $bulanNama[$item->bulan - 1] . ' ' . $item->tahun,
                'total' => (float) $item->total_nilai
            ];
        }

        // ===== 5. PER RUANGAN =====
        $perRuanganQuery = clone $baseQuery;
        $perRuanganData = $perRuanganQuery
            ->select(
                'instalasi',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "disetujui" THEN 1 ELSE 0 END) as disetujui'),
                DB::raw('SUM(CASE WHEN status = "ditolak" THEN 1 ELSE 0 END) as ditolak'),
                DB::raw('SUM(total_pengajuan) as total_nilai')
            )
            ->whereNotNull('instalasi')
            ->groupBy('instalasi')
            ->orderBy('total', 'desc')
            ->get();

        $perRuangan = [];
        foreach ($perRuanganData as $item) {
            $perRuangan[] = [
                'ruangan' => $item->instalasi,
                'total' => $item->total,
                'disetujui' => $item->disetujui,
                'ditolak' => $item->ditolak,
                'nilai' => (float) $item->total_nilai
            ];
        }

        return [
            'status' => $statusData,
            'trend' => $trend,
            'topBarang' => $topBarangData,
            'nilai' => $nilai,
            'perRuangan' => $perRuangan
        ];
    }

    /**
     * Get rekap bulanan untuk tabel
     */
    private function getRekapBulanan($baseQuery)
    {
        $query = clone $baseQuery;
        
        $rekap = $query
            ->select(
                DB::raw('MONTH(tanggal_pengajuan) as bulan'),
                DB::raw('YEAR(tanggal_pengajuan) as tahun'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(total_pengajuan) as total_nilai'),
                DB::raw('SUM(CASE WHEN status = "disetujui" THEN 1 ELSE 0 END) as disetujui'),
                DB::raw('SUM(CASE WHEN status = "ditolak" THEN 1 ELSE 0 END) as ditolak'),
                DB::raw('SUM(CASE WHEN status IN ("diajukan", "disetujui_koordinator", "disetujui_kabid", "menunggu_direktur") THEN 1 ELSE 0 END) as proses')
            )
            ->whereNotNull('tanggal_pengajuan')
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->limit(12)
            ->get();

        $bulanNama = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $result = [];

        foreach ($rekap as $item) {
            $result[] = [
                'bulan' => $bulanNama[$item->bulan - 1] . ' ' . $item->tahun,
                'total' => $item->total,
                'nilai' => (float) $item->total_nilai,
                'disetujui' => $item->disetujui,
                'ditolak' => $item->ditolak,
                'proses' => $item->proses
            ];
        }

        return $result;
    }

    /**
     * Mendapatkan label status
     */
    private function getStatusLabel($status)
    {
        $statuses = [
            'draft' => 'Draft',
            'diajukan' => 'Diajukan',
            'disetujui_koordinator' => 'Disetujui Koordinator',
            'disetujui_kabid' => 'Disetujui Kabid',
            'dipertimbangkan' => 'Dipertimbangkan',
            'menunggu_direktur' => 'Menunggu Direktur',
            'disetujui' => 'Disetujui',
            'disetujui sebagian' => 'Disetujui Sebagian',
            'ditolak' => 'Ditolak',
            'revisi' => 'Revisi',
            'ditunda' => 'Ditunda',
        ];

        return $statuses[$status] ?? $status;
    }

   

}




