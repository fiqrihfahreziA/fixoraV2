<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\bidang;
use App\Models\detail_barang;
use App\Models\barang_tersedia;
use App\Models\pengajuan;
use App\Models\pengajuan_item;
use App\Models\karyawan;
use Illuminate\Support\Facades\DB; 
use App\Models\RequestModel;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;


class direkturController extends Controller
{
   public function index()
    {
        $authUser = Auth::user();

        
        return view('direktur.dashboard', [
            'authUser' => $authUser,
        ]);
    }

    //   public function showpengadaan(Request $request)
    // {
    //     $authUser = Auth::user();
        
    //     // Ambil query pencarian
    //     $search = $request->input('search');
    //     $statusFilter = $request->input('status');

    //     /**
    //      * ============================
    //      * QUERY DASAR PENGADAAN (SEMUA)
    //      * TAMPILKAN SEMUA STATUS KECUALI:
    //      * - draft, revisi, diajukan, disetujui_koordinator
    //      * ============================
    //      */
    //     $query = Pengajuan::with(['items', 'karyawan', 'bidang'])
    //         ->whereNotIn('status', ['diajukan', 'draft', 'revisi', 'disetujui_koordinator', 'disetujui_kabid','perlu survei perbaikan','menunggu_keuangan','menunggu_kpa'])
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
    //      * STATISTIK DASHBOARD
    //      * ============================
    //      */
    //     $stats = [
    //         'total' => Pengajuan::whereNotIn('status', ['diajukan', 'draft', 'revisi', 'disetujui_koordinator'])->count(),
    //         'draft' => Pengajuan::where('status', 'draft')->count(),
    //         'diajukan' => Pengajuan::where('status', 'diajukan')->count(),
    //         'disetujui' => Pengajuan::where('status', 'disetujui')->count(),
    //         'ditolak' => Pengajuan::where('status', 'ditolak')->count(),
    //         'menunggu_direktur' => Pengajuan::where('status', 'menunggu_direktur')->count(),
    //         'disetujui_kabid' => Pengajuan::where('status', 'disetujui_kabid')->count(),
    //     ];

    //     return view('direktur.pengadaan.pengadaan', compact(
    //         'authUser',
    //         'allPengajuan',
    //         'search',
    //         'statusFilter',
    //         'stats'
    //     ));
    // }

    public function showpengadaan(Request $request)
{
    $authUser = Auth::user();

    $search         = $request->input('search');
    $bidangFilter   = $request->input('bidang'); // ✅ Filter Bidang
    $tahunFilter    = $request->input('tahun'); // ✅ Filter Tahun
    $bulanFilter    = $request->input('bulan'); // ✅ Filter Bulan
    $kategoriFilter = $request->input('kategori', 'pengadaan'); // default pengadaan

    // ✅ Ambil List Tahun Dinamis dari tanggal_pengajuan
    $listTahun = \App\Models\Pengajuan::selectRaw('YEAR(tanggal_pengajuan) as tahun')
        ->whereNotNull('tanggal_pengajuan')
        ->distinct()
        ->orderBy('tahun', 'desc')
        ->pluck('tahun');

    // Ambil List Bidang
    $bidangs = \App\Models\Bidang::all();

    $excludeStatus = [
        'diajukan', 'draft', 'revisi', 'disetujui_koordinator',
        'disetujui_kabid', 'perlu survei perbaikan',
        'menunggu_keuangan', 'menunggu_kpa'
    ];

    /**
     * ============================
     * QUERY DASAR (PER KATEGORI)
     * ============================
     */
    $query = \App\Models\Pengajuan::with(['items', 'karyawan', 'bidang'])
        ->whereNotIn('status', $excludeStatus)
        ->where('kategori', $kategoriFilter)
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
        ->when($bidangFilter, function ($q) use ($bidangFilter) {
            $q->where('bidang_id', $bidangFilter);
        })
        // ✅ Filter Tahun & Bulan (dari tanggal_pengajuan)
        ->when($tahunFilter, function ($q) use ($tahunFilter) {
            $q->whereYear('tanggal_pengajuan', $tahunFilter);
        })
        ->when($bulanFilter, function ($q) use ($bulanFilter) {
            $q->whereMonth('tanggal_pengajuan', $bulanFilter);
        });

    /**
     * ============================
     * DATA TABEL
     * ============================
     */
    $allPengajuan = (clone $query)
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

    /**
     * ============================
     * STATISTIK (PER KATEGORI)
     * ============================
     */
    // Menggunakan clone $query agar statistik ikut terfilter
    $statsBase = $query; 

    $stats = [
        'total'             => (clone $statsBase)->count(),
        'draft'             => (clone $statsBase)->where('status', 'draft')->count(),
        'diajukan'          => (clone $statsBase)->where('status', 'diajukan')->count(),
        'disetujui'         => (clone $statsBase)->where('status', 'disetujui')->count(),
        'ditolak'           => (clone $statsBase)->where('status', 'ditolak')->count(),
        'menunggu_direktur' => (clone $statsBase)->where('status', 'menunggu_direktur')->count(),
        'disetujui_kabid'   => (clone $statsBase)->where('status', 'disetujui_kabid')->count(),
    ];

    /**
     * ============================
     * COUNTER UNTUK BADGE TAB
     * ============================
     */
    $baseCount = \App\Models\Pengajuan::whereNotIn('status', $excludeStatus)
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
        ->when($bidangFilter, function ($q) use ($bidangFilter) {
            $q->where('bidang_id', $bidangFilter);
        })
        ->when($tahunFilter, function ($q) use ($tahunFilter) {
            $q->whereYear('tanggal_pengajuan', $tahunFilter);
        })
        ->when($bulanFilter, function ($q) use ($bulanFilter) {
            $q->whereMonth('tanggal_pengajuan', $bulanFilter);
        });

    $countPengadaan    = (clone $baseCount)->where('kategori', 'pengadaan')->count();
    $countPemeliharaan = (clone $baseCount)->where('kategori', 'pemeliharaan')->count();

    return view('direktur.pengadaan.pengadaan', compact(
        'authUser',
        'allPengajuan',
        'search',
        'bidangFilter',
        'tahunFilter',
        'bulanFilter',
        'kategoriFilter',
        'stats',
        'listTahun',
        'bidangs',
        'countPengadaan',
        'countPemeliharaan'
    ));
}

//     public function showpengadaan(Request $request)
// {
//     $authUser = Auth::user();

//     $search         = $request->input('search');
//     $statusFilter   = $request->input('status');
//     $kategoriFilter = $request->input('kategori', 'pengadaan'); // default pengadaan

//     $excludeStatus = [
//         'diajukan', 'draft', 'revisi', 'disetujui_koordinator',
//         'disetujui_kabid', 'perlu survei perbaikan',
//         'menunggu_keuangan', 'menunggu_kpa'
//     ];

//     /**
//      * ============================
//      * QUERY DASAR (PER KATEGORI)
//      * ============================
//      */
//     $query = Pengajuan::with(['items', 'karyawan', 'bidang'])
//         ->whereNotIn('status', $excludeStatus)
//         ->where('kategori', $kategoriFilter)
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
//         });

//     /**
//      * ============================
//      * DATA TABEL
//      * ============================
//      */
//     $allPengajuan = (clone $query)
//         ->orderBy('created_at', 'desc')
//         ->paginate(10)
//         ->appends($request->all());

//     /**
//      * ============================
//      * STATISTIK (PER KATEGORI)
//      * ============================
//      */
//     $statsBase = Pengajuan::whereNotIn('status', $excludeStatus)
//         ->where('kategori', $kategoriFilter);

//     $stats = [
//         'total'             => (clone $statsBase)->count(),
//         'draft'             => (clone $statsBase)->where('status', 'draft')->count(),
//         'diajukan'          => (clone $statsBase)->where('status', 'diajukan')->count(),
//         'disetujui'         => (clone $statsBase)->where('status', 'disetujui')->count(),
//         'ditolak'           => (clone $statsBase)->where('status', 'ditolak')->count(),
//         'menunggu_direktur' => (clone $statsBase)->where('status', 'menunggu_direktur')->count(),
//         'disetujui_kabid'   => (clone $statsBase)->where('status', 'disetujui_kabid')->count(),
//     ];

//     /**
//      * ============================
//      * COUNTER UNTUK BADGE TAB
//      * ============================
//      */
//     $countPengadaan = Pengajuan::whereNotIn('status', $excludeStatus)
//         ->where('kategori', 'pengadaan')->count();

//     $countPemeliharaan = Pengajuan::whereNotIn('status', $excludeStatus)
//         ->where('kategori', 'pemeliharaan')->count();

//     return view('direktur.pengadaan.pengadaan', compact(
//         'authUser',
//         'allPengajuan',
//         'search',
//         'statusFilter',
//         'kategoriFilter',
//         'stats',
//         'countPengadaan',
//         'countPemeliharaan'
//     ));
// }

        public function detail($id)
    {
        $authUser = Auth::user();
        $pengajuan = Pengajuan::with(['items', 'karyawan', 'bidang'])->findOrFail($id);
        
        return view('direktur.pengadaan.detail', compact('authUser', 'pengajuan'));
    }

    public function setujuiiii(Request $request, $id)
{
    $request->validate([
        // 'status_verifikasi' => 'required|in:disetujui_koordinator,ditolak,revisi',
        // 'catatan_verifikasi' => 'required|string|min:5',
        'direktur_id' => 'required'
    ]);
    
    $pengajuan = Pengajuan::findOrFail($id);
    $pengajuan->update([
        'status' => 'disetujui',
        'log_status_direktur' => 'disetujui',
        // 'total_disetujui_direktur' => $pengajuan->total_pengajuan,
        // 'total_disetujui_direktur' => $pengajuan->total_disetujui,
        'direktur_id' =>  $request->direktur_id,
        'disetujui_direktur_at' => now(),
        'catatan_direktur' => request('catatan_direktur')
    ]);
    return redirect()->route('direktur.pengadaan')->with('success', 'Pengajuan berhasil diverifikasi (anggaran tersedia penuh).');
}


    public function setujuii(Request $request, $id)
{
    $request->validate([
        'direktur_id' => 'required'
    ]);
    
    try {
        $pengajuan = Pengajuan::findOrFail($id);
        
        // Cek status
        if (!in_array($pengajuan->status, ['disetujui', 'menunggu_direktur'])) {
            return redirect()->back()->with('error', 'Pengajuan tidak dapat disetujui karena status tidak sesuai.');
        }
        
        // Ambil total yang disetujui
        $totalDisetujui = $pengajuan->total_disetujui ?? $pengajuan->total_pengajuan ?? 0;
        
        // Update pengajuan - PAKAI STATUS 'disetujui' karena di enum hanya itu
        $pengajuan->update([
            'status' => 'disetujui', // status tetap disetujui
            'total_disetujui_direktur' => $totalDisetujui,
            'direktur_id' => $request->direktur_id,
            'disetujui_direktur_at' => now(),
            'catatan_direktur' => $request->catatan_direktur,
        ]);
        
        // Update semua item menjadi disetujui
        foreach ($pengajuan->items as $item) {
            $item->update([
                'disetujui_direktur' => true
            ]);
        }
        
        return redirect()->route('direktur.pengadaan.detail', $id)
            ->with('success', 'Pengajuan berhasil disetujui (Full).');
            
    } catch (\Exception $e) {
        \Log::error('Error setujui: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
    /**
     * ============================================
     * SETUJUI SEBAGIAN PENGAJUAN
     * ============================================
     */

     public function setujui(Request $request, $id)
{
    $request->validate([
        // 'status_verifikasi' => 'required|in:disetujui_koordinator,ditolak,revisi',
        // 'catatan_verifikasi' => 'required|string|min:5',
        'direktur_id' => 'required'
    ]);
    
    $pengajuan = Pengajuan::findOrFail($id);
    $pengajuan->update([
        'status' => 'disetujui',
        'log_status_direktur' => 'disetujui',
        'total_disetujui_direktur' => $pengajuan->total_disetujui,
        // 'total_disetujui_direktur' => $pengajuan->total_disetujui,
        'direktur_id' =>  $request->direktur_id,
        'disetujui_direktur_at' => now(),
        'catatan_direktur' => request('catatan_direktur')
    ]);
    return redirect()->route('direktur.pengadaan')->with('success', 'Pengajuan berhasil diverifikasi (anggaran tersedia penuh).');
}
   
    public function tunda(Request $request, $id)
    {
        $request->validate([
            'alasan_direktur' => 'required|string|min:5',
            // 'id_direktur' => 'required'
        ]);
        
        $pengajuan = Pengajuan::findOrFail($id);
        
         $totalDisetujui = $pengajuan->total_disetujui ?? $pengajuan->total_pengajuan ?? 0;
        // Cek status
        if (!in_array($pengajuan->status, ['disetujui', 'menunggu_direktur'])) {
            return redirect()->back()->with('error', 'Pengajuan tidak dapat ditunda karena status tidak sesuai.');
        }
        
        $pengajuan->update([
            'status' => 'ditunda',
            'total_disetujui_direktur' => $totalDisetujui,
            'direktur_id' => $request->direktur_id,
            'disetujui_direktur_at' => now(),
            'catatan_direktur' => request('catatan_direktur')
        ]);
        
        return redirect()->route('direktur.pengadaan.detail', $id)->with('warning', 'Pengajuan berhasil ditunda.');
    }

    /**
     * ============================================
     * TOLAK PENGAJUAN
     * ============================================
     */

    public function tolak(Request $request, $id)
    {
        $request->validate([
            'catatan_direktur' => 'required|string|min:5',
             'direktur_id' => 'required'
            // 'id_direktur' => 'required'
        ]);
        
        $pengajuan = Pengajuan::findOrFail($id);
        
         $totalDisetujui = 0;
        // Cek status
        if (!in_array($pengajuan->status, ['disetujui', 'menunggu_direktur','ditunda'])) {
            return redirect()->back()->with('error', 'Pengajuan tidak dapat ditunda karena status tidak sesuai.');
        }
        
        $pengajuan->update([
            'status' => 'ditolak',
            'total_disetujui_direktur' => $totalDisetujui,
            'direktur_id' => $request->direktur_id,
            'disetujui_direktur_at' => now(),
            'catatan_direktur' => $request->catatan_direktur
        ]);
        
        return redirect()->route('direktur.pengadaan.detail', $id)->with('warning', 'Pengajuan berhasil ditunda.');
    }
    
    public function tolakd(Request $request, $id)
    {
        $request->validate([
            'alasan_direktur' => 'required|string|min:5',
            // 'id_direktur' => 'required'
        ]);
        
        $pengajuan = Pengajuan::with('items')->findOrFail($id);
        
        // Cek status
        if (!in_array($pengajuan->status, ['disetujui', 'menunggu_direktur'])) {
            return redirect()->back()->with('error', 'Pengajuan tidak dapat ditolak karena status tidak sesuai.');
        }
        
        // Update pengajuan
        $pengajuan->update([
            'status' => 'ditolak',
            'direktur_id' => $request->direktur_id,
            'disetujui_direktur_at' => now(),
            'catatan_direktur' => request('catatan_direktur')
    
        ]);
        
        // Update semua item menjadi ditolak
        foreach ($pengajuan->items as $item) {
            $item->update([
                'disetujui_direktur' => false
            ]);
        }
        
        return redirect()->route('direktur.pengadaan.detail', $id)->with('danger', 'Pengajuan berhasil ditolak.');
    }

    /**
     * ============================================
     * RESET PENGAJUAN YANG DITUNDA
     * ============================================
     */
    public function resetTunda($id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        
        if ($pengajuan->status !== 'ditunda_direktur') {
            return redirect()->back()->with('error', 'Pengajuan tidak dalam status ditunda.');
        }
        
        $pengajuan->update([
            'status' => 'menunggu_direktur',
            'id_direktur' => null,
            'catatan_direktur' => null,
            'alasan_direktur' => null,
        ]);
        
        return redirect()->route('direktur.pengadaan.detail', $id)->with('success', 'Pengajuan berhasil di-reset dari status ditunda.');
    }

    public function cetak($id)
{
    $pengajuan = Pengajuan::with('items')->findOrFail($id);
    
    // Cek apakah user memiliki akses
    // Tambahkan authorization sesuai kebutuhan
    
    return view('direktur.pengadaan.print', compact('pengajuan'));
}

// Di controller untuk preview tanpa data
public function preview()
{
    // Buat data dummy jika perlu preview
    $pengajuan = new \stdClass();
    $pengajuan->ruangan = 'Ruangan Rawat Inap';
    $pengajuan->pengusul = 'dr. Ahmad';
    $pengajuan->jabatan = 'Kepala Ruangan';
    $pengajuan->nip = '197501012005011001';
    $pengajuan->tanggal_pengajuan = now();
    $pengajuan->tahun_anggaran = '2026';
    $pengajuan->dasar_usulan = 'Kebutuhan Operasional,Penggantian Barang Rusak';
    $pengajuan->alasan = 'Untuk meningkatkan pelayanan pasien';
    $pengajuan->manfaat = 'Meningkatkan efisiensi pelayanan';
    $pengajuan->dampak = 'Pelayanan menjadi terganggu';
    $pengajuan->total = 150000000;
    
    // Items dummy
    $pengajuan->items = [
        (object) ['nama_barang' => 'Infus Pump', 'spesifikasi' => 'Digital', 'satuan' => 'Unit', 'jumlah' => 5, 'harga_satuan' => 15000000, 'total' => 75000000],
        (object) ['nama_barang' => 'Bed Pasien', 'spesifikasi' => 'Elektrik', 'satuan' => 'Unit', 'jumlah' => 3, 'harga_satuan' => 25000000, 'total' => 75000000],
    ];
    
    return view('pengadaan.print', compact('pengajuan'));
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

        return view('direktur.pengadaan.laporan', compact(
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



   public function exportExcel(Request $request)
    {
        // ==========================================================
        // AMBIL PARAMETER FILTER
        // ==========================================================
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');
        $status = $request->input('status');
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
        // ==========================================================
        if ($pengadaan->isEmpty()) {
            return back()->with('error', 'Tidak ada data untuk diexport.');
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
        $sheet->mergeCells('A1:Q1');

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
        $sheet->mergeCells('A2:Q2');

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
            'Keuangan',
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
        $headerRange = 'A4:Q4';
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
                // Petuga Keuangan (Keuanagn)
                $sheet->setCellValue(
                    'G' . $row,
                    $item->keuangan?->nama_karyawan
                        ?? $item->keuangan?->nama
                        ?? '-'
                );

                // DIREKTUR
                $sheet->setCellValue(
                    'H' . $row,
                    $item->direktur?->nama_karyawan
                        ?? $item->direktur?->nama
                        ?? '-'
                );

                // NAMA BARANG
                $sheet->setCellValue('I' . $row, $barang->nama_barang ?? '-');

                // JUMLAH
                $sheet->setCellValue('J' . $row, (float) ($barang->jumlah ?? 0));

                // HARGA / UNIT
                $sheet->setCellValue('K' . $row, $hargaSatuan);

                // TOTAL HARGA
                $sheet->setCellValue('L' . $row, $totalHarga);

                // TOTAL DISETUJUI DIREKTUR
                $sheet->setCellValue('M' . $row, $totalDisetujuiDirektur);

                // BIDANG
                $sheet->setCellValue(
                    'N' . $row,
                    $item->bidang?->nama_bidang
                        ?? $item->bidang?->nama
                        ?? '-'
                );

                // STATUS
                $sheet->setCellValue(
                    'O' . $row,
                    $this->getStatusLabel($item->status)
                );

                // STATUS DIREKTUR
                $sheet->setCellValue(
                    'P' . $row,
                    $this->getStatusLabel($statusDirektur)
                );

                // TANGGAL DIREKTUR
                $sheet->setCellValue(
                    'Q' . $row,
                    $tanggalDirektur
                );
                
                

                // FORMAT RUPIAH
                $sheet->getStyle('J' . $row . ':L' . $row)
                    ->getNumberFormat()
                    ->setFormatCode('"Rp" #,##0');

                // ALIGNMENT
                $sheet->getStyle('A' . $row . ':Q' . $row)
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

                $sheet->getStyle('M' . $row . ':Q' . $row)
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
        foreach (range('A', 'Q') as $col) {
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
        $sheet->getColumnDimension('Q')->setWidth(18);

        // ==========================================================
        // BORDER
        // ==========================================================
        if ($row > 5) {
            $dataRange = 'A4:Q' . ($row - 1);
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
