<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Models\Karyawan;
use App\Models\detail_barang;
use App\Models\RequestModel;
use App\Models\Penerima;
use App\Models\pengajuan_item;
use App\Models\bidang;
use App\Models\pengajuan;
use App\Models\App\Models\barang_tersedia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KpaController extends Controller
{
//    public function index(Request $request)
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
//             ->whereNotIn('status', ['diajukan', 'draft', 'revisi', 'disetujui_koordinator', 'disetujui_kabid','sesuai standard','perlu survei perbaikan','dipertimbangkan','menunggu_keuangan'])
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
//         $stats = [
//             'total' => Pengajuan::where('bidang_id', $bidangId)->count(),
//             'draft' => Pengajuan::where('bidang_id', $bidangId)->where('status', 'draft')->count(),
//             'diajukan' => Pengajuan::where('bidang_id', $bidangId)->where('status', 'diajukan')->count(),
//             'disetujui' => Pengajuan::where('bidang_id', $bidangId)->where('status', 'disetujui')->count(),
//             'ditolak' => Pengajuan::where('bidang_id', $bidangId)->where('status', 'ditolak')->count(),
//             'revisi' => Pengajuan::where('bidang_id', $bidangId)->where('status', 'revisi')->count(),
//             'menunggu_direktur' => Pengajuan::where('bidang_id', $bidangId)->where('status', 'menunggu_direktur')->count(),
            
//             'permintaan_count' => Pengajuan::where('bidang_id', $bidangId)
//                 ->where(function($q) {
//                     $q->where('dasar_usulan', 'LIKE', '%Program Kerja%')
//                       ->orWhere('dasar_usulan', 'LIKE', '%Kebutuhan Operasional%')
//                       ->orWhere('dasar_usulan', 'LIKE', '%Penambahan Kapasitas Pelayanan%')
//                       ->orWhere('dasar_usulan', 'LIKE', '%Pemenuhan Standar Akreditasi%')
//                       ->orWhere('dasar_usulan', 'LIKE', '%Keselamatan Pasien%')
//                       ->orWhereNull('dasar_usulan');
//                 })->count(),
                
//             'perbaikan_count' => Pengajuan::where('bidang_id', $bidangId)
//                 ->where(function($q) {
//                     $q->where('dasar_usulan', 'LIKE', '%Penggantian Barang Rusak%')
//                       ->orWhere('dasar_usulan', 'LIKE', '%perbaikan%')
//                       ->orWhere('dasar_usulan', 'LIKE', '%service%')
//                       ->orWhere('dasar_usulan', 'LIKE', '%repair%');
//                 })->count(),
//         ];

//         return view('kpa.dashboard', compact(
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
//     } //

public function index(Request $request)
{
    $authUser = Auth::user();

    $bidangId = $authUser->karyawan->bidang_id ?? null;
    $ruangan  = $authUser->karyawan->ruangan ?? null;

    $bidangs = \App\Models\Bidang::all();

    $search         = $request->input('search');
    $bidangFilter   = $request->input('bidang');
    $tahunFilter    = $request->input('tahun'); // ✅ Filter Tahun
    $bulanFilter    = $request->input('bulan'); // ✅ Filter Bulan
    $typeFilter     = $request->input('type');
    $kategoriFilter = $request->input('kategori', 'pengadaan'); // default pengadaan

    // ✅ Ambil List Tahun Dinamis dari tanggal_pengajuan
    $listTahun = \App\Models\Pengajuan::selectRaw('YEAR(tanggal_pengajuan) as tahun')
        ->whereNotNull('tanggal_pengajuan')
        ->distinct()
        ->orderBy('tahun', 'desc')
        ->pluck('tahun');

    $excludeStatus = [
        'diajukan', 'draft', 'revisi', 'disetujui_koordinator',
        'disetujui_kabid', 'sesuai standard', 'perlu survei perbaikan',
        'dipertimbangkan', 'menunggu_keuangan'
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
     * STATISTIK (Sinkron dengan Filter)
     * ============================
     */
    // Menggunakan clone $query agar statistik ikut terfilter Tahun/Bulan/Bidang/Search
    $statsBase = $query; 

    $stats = [
        'total'             => (clone $statsBase)->count(),
        'selesai'             => (clone $statsBase)->where('status', 'selesai')->count(),
        'diajukan'          => (clone $statsBase)->where('status', 'diajukan')->count(),
        'disetujui'         => (clone $statsBase)->where('log_status_direktur', 'disetujui')->count(),
        'ditolak'           => (clone $statsBase)->where('status', 'ditolak')->count(),
        'revisi'            => (clone $statsBase)->where('status', 'revisi')->count(),
        'menunggu_direktur' => (clone $statsBase)->where('status', 'menunggu_direktur')->count(),

        // 'permintaan_count' => (clone $statsBase)
        //     ->where(function ($q) {
        //         $q->where('dasar_usulan', 'LIKE', '%Program Kerja%')
        //           ->orWhere('dasar_usulan', 'LIKE', '%Kebutuhan Operasional%')
        //           ->orWhere('dasar_usulan', 'LIKE', '%Penambahan Kapasitas Pelayanan%')
        //           ->orWhere('dasar_usulan', 'LIKE', '%Pemenuhan Standar Akreditasi%')
        //           ->orWhere('dasar_usulan', 'LIKE', '%Keselamatan Pasien%')
        //           ->orWhereNull('dasar_usulan');
        //     })->count(),

        // 'perbaikan_count' => (clone $statsBase)
        //     ->where(function ($q) {
        //         $q->where('dasar_usulan', 'LIKE', '%Penggantian Barang Rusak%')
        //           ->orWhere('dasar_usulan', 'LIKE', '%perbaikan%')
        //           ->orWhere('dasar_usulan', 'LIKE', '%service%')
        //           ->orWhere('dasar_usulan', 'LIKE', '%repair%');
        //     })->count(),
    ];

    /**
     * ============================
     * COUNTER BADGE TAB KATEGORI
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

    return view('kpa.dashboard', compact(
        'authUser',
        'allPengajuan',
        'bidangs',
        'search',
        'bidangFilter',
        'typeFilter',
        'stats',
        'listTahun',
        'tahunFilter',
        'bulanFilter',
        'bidangId',
        'ruangan',
        'kategoriFilter',
        'countPengadaan',
        'countPemeliharaan'
    ));
}

// public function index(Request $request)
// {
//     $authUser = Auth::user();

//     $bidangId = $authUser->karyawan->bidang_id ?? null;
//     $ruangan  = $authUser->karyawan->ruangan ?? null;

//     $bidangs = bidang::all();

//     $search         = $request->input('search');
//     $statusFilter   = $request->input('status');
//     $bidangFilter   = $request->input('bidang');
//     $typeFilter     = $request->input('type');
//     $tahunAnggaran  = $request->input('tahun_anggaran');
//     $kategoriFilter = $request->input('kategori', 'pengadaan'); // default pengadaan

//     $excludeStatus = [
//         'diajukan', 'draft', 'revisi', 'disetujui_koordinator',
//         'disetujui_kabid', 'sesuai standard', 'perlu survei perbaikan',
//         'dipertimbangkan', 'menunggu_keuangan'
//     ];

//     /**
//      * ============================
//      * QUERY DASAR (PER KATEGORI)
//      * ============================
//      */
//     $query = pengajuan::with(['items', 'karyawan', 'bidang'])
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
//         })
//         ->when($bidangFilter, function ($q) use ($bidangFilter) {
//             $q->where('bidang_id', $bidangFilter);
//         })
//         ->when($tahunAnggaran, function ($q) use ($tahunAnggaran) {
//             $q->where('tahun_anggaran', $tahunAnggaran);
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
//      * TAB PERMINTAAN (per kategori)
//      * ============================
//      */
//     $permintaanPengajuan = (clone $query)
//         ->where(function ($q) {
//             $q->where('dasar_usulan', 'LIKE', '%Program Kerja%')
//               ->orWhere('dasar_usulan', 'LIKE', '%Kebutuhan Operasional%')
//               ->orWhere('dasar_usulan', 'LIKE', '%Penambahan Kapasitas Pelayanan%')
//               ->orWhere('dasar_usulan', 'LIKE', '%Pemenuhan Standar Akreditasi%')
//               ->orWhere('dasar_usulan', 'LIKE', '%Keselamatan Pasien%')
//               ->orWhereNull('dasar_usulan');
//         })
//         ->orderBy('created_at', 'desc')
//         ->paginate(10)
//         ->appends($request->all());

//     /**
//      * ============================
//      * TAB PERBAIKAN (per kategori)
//      * ============================
//      */
//     $perbaikanPengajuan = (clone $query)
//         ->where(function ($q) {
//             $q->where('dasar_usulan', 'LIKE', '%Penggantian Barang Rusak%')
//               ->orWhere('dasar_usulan', 'LIKE', '%perbaikan%')
//               ->orWhere('dasar_usulan', 'LIKE', '%service%')
//               ->orWhere('dasar_usulan', 'LIKE', '%repair%');
//         })
//         ->orderBy('created_at', 'desc')
//         ->paginate(10)
//         ->appends($request->all());

//     /**
//      * ============================
//      * STATISTIK (per kategori)
//      * ============================
//      */
//     $statsBase = pengajuan::where('kategori', $kategoriFilter);

//     $stats = [
//         'total'             => (clone $statsBase)->count(),
//         'draft'             => (clone $statsBase)->where('status', 'draft')->count(),
//         'diajukan'          => (clone $statsBase)->where('status', 'diajukan')->count(),
//         'disetujui'         => (clone $statsBase)->where('status', 'disetujui')->count(),
//         'ditolak'           => (clone $statsBase)->where('status', 'ditolak')->count(),
//         'revisi'            => (clone $statsBase)->where('status', 'revisi')->count(),
//         'menunggu_direktur' => (clone $statsBase)->where('status', 'menunggu_direktur')->count(),

//         'permintaan_count' => (clone $statsBase)
//             ->where(function ($q) {
//                 $q->where('dasar_usulan', 'LIKE', '%Program Kerja%')
//                   ->orWhere('dasar_usulan', 'LIKE', '%Kebutuhan Operasional%')
//                   ->orWhere('dasar_usulan', 'LIKE', '%Penambahan Kapasitas Pelayanan%')
//                   ->orWhere('dasar_usulan', 'LIKE', '%Pemenuhan Standar Akreditasi%')
//                   ->orWhere('dasar_usulan', 'LIKE', '%Keselamatan Pasien%')
//                   ->orWhereNull('dasar_usulan');
//             })->count(),

//         'perbaikan_count' => (clone $statsBase)
//             ->where(function ($q) {
//                 $q->where('dasar_usulan', 'LIKE', '%Penggantian Barang Rusak%')
//                   ->orWhere('dasar_usulan', 'LIKE', '%perbaikan%')
//                   ->orWhere('dasar_usulan', 'LIKE', '%service%')
//                   ->orWhere('dasar_usulan', 'LIKE', '%repair%');
//             })->count(),
//     ];

//     /**
//      * ============================
//      * COUNTER BADGE TAB KATEGORI
//      * ============================
//      */
//     $countPengadaan = pengajuan::whereNotIn('status', $excludeStatus)
//         ->where('kategori', 'pengadaan')->count();

//     $countPemeliharaan = pengajuan::whereNotIn('status', $excludeStatus)
//         ->where('kategori', 'pemeliharaan')->count();

//     return view('kpa.dashboard', compact(
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
//         'ruangan',
//         'kategoriFilter',
//         'countPengadaan',
//         'countPemeliharaan'
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

        return view('profile.karyawan.editkpa', compact('authUser', 'daftarRuangan', 'daftarJabatan'));
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
        ->route('kpa.pengadaan')
        ->with('success', 'Profile dan data Karyawan berhasil diperbarui.');
}

         public function detail($id)
    {
        $pengajuan = Pengajuan::with([
            'karyawan',
            'penerima',
            'atasan',
            'bidang',
            'items',
        ])->findOrFail($id);

        return view('kpa.detail', compact('pengajuan'));
    }


    

public function verifikasi(Request $request, $id)
{
    $validated = $request->validate([
        'status_verifikasi' => [
            'required',
            'in:disetujui,sebagian,ditunda,ditolak',
        ],
        'catatan_verifikasi' => 'required|string|min:5|max:255',
        'kpa_id' => 'required|integer',
    ]);

    try {
        $pengajuan = Pengajuan::findOrFail($id);

        // Mapping status tombol ke status database
        $statusMapping = [
            'disetujui' => 'menunggu_direktur',
            'sebagian'  => 'disetujui sebagian',
            'ditunda'   => 'ditunda',
            'ditolak'   => 'ditolak',
        ];

        // Mapping log aktivitas KPA
        $logMapping = [
            'disetujui' => 'Disetujui oleh KPA',
            'sebagian'  => 'Disetujui sebagian oleh KPA',
            'ditunda'   => 'Ditunda oleh KPA',
            'ditolak'   => 'Ditolak oleh KPA',
        ];

        $pilihan = $validated['status_verifikasi'];

        $data = [
            'status'      => $statusMapping[$pilihan],
            'kpa_id'      => $validated['kpa_id'],
            'catatan_kpa' => $validated['catatan_verifikasi'],
            'log_kpa'     => $logMapping[$pilihan],
            'kpa'         => now(),
        ];

        // Kalau disetujui penuh, total_disetujui_direktur otomatis
        // diambil dari total_disetujui
        if ($pilihan === 'disetujui') {
            $data['total_disetujui_direktur'] = $pengajuan->total_disetujui;
        }

        $pengajuan->update($data);

        return redirect()
            ->route('kpa.dashboard')
            ->with('success', 'Pengajuan berhasil diverifikasi!');

    } catch (\Throwable $e) {
        report($e);

        return back()
            ->withInput()
            ->with('error', 'Gagal verifikasi. Periksa log aplikasi.');
    }
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

        return view('kpa.laporan', compact(
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
