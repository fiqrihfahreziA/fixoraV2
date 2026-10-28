<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
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
use App\Traits\StatusTrait; 


class PpkController extends Controller
{
    // public function index(Request $request)
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
    //      $tahunAnggaran = $request->input('tahun_anggaran');

    //     /**
    //      * ============================
    //      * QUERY DASAR PENGADAAN
    //      * Filter berdasarkan bidang_id user (PENERIMA)
    //      * ============================
    //      */
    //         $query = pengajuan::with(['items', 'karyawan', 'bidang'])
    //         // ->where('bidang_id', $bidangId)
    //         ->whereNotIn('status', ['diajukan', 'draft', 'revisi', 'disetujui_koordinator','sesuai standard','perlu survei perbaikan'])
    //         ->where('log_acc_kabidPenunjang', 'disetujui oleh kabid penunjang')
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
    //         ->when($tahunAnggaran, function ($q) use ($tahunAnggaran) { // <-- TAMBAHKAN FILTER TAHUN
    //         $q->where('tahun_anggaran', $tahunAnggaran);
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
    //      * DATA UNTUK TAB PERMINTAAN 
    //      * ============================
    //      */
    //     $permintaanQuery = clone $query;
    //     $permintaanPengajuan = $permintaanQuery->where(function($q) {
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
    //      * DATA UNTUK TAB PERBAIKAN 
    //      * ============================
    //      */
    //     $perbaikanQuery = clone $query;
    //     $perbaikanPengajuan = $perbaikanQuery->where(function($q) {
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
    //      * STATISTIK DASHBOARD
    //      * ============================
    //      */
    //     $stats = [
    //         'total' => Pengajuan::where('bidang_id', $bidangId)->count(),
    //         'draft' => Pengajuan::where('bidang_id', $bidangId)->where('status', 'draft')->count(),
    //         'diajukan' => Pengajuan::where('bidang_id', $bidangId)->where('status', 'diajukan')->count(),
    //         'disetujui' => Pengajuan::where('bidang_id', $bidangId)->where('status', 'disetujui')->count(),
    //         'ditolak' => Pengajuan::where('bidang_id', $bidangId)->where('status', 'ditolak')->count(),
    //         'revisi' => Pengajuan::where('bidang_id', $bidangId)->where('status', 'revisi')->count(),
    //         'menunggu_direktur' => Pengajuan::where('bidang_id', $bidangId)->where('status', 'menunggu_direktur')->count(),
            
    //         'permintaan_count' => Pengajuan::where('bidang_id', $bidangId)
    //             ->where(function($q) {
    //                 $q->where('dasar_usulan', 'LIKE', '%Program Kerja%')
    //                   ->orWhere('dasar_usulan', 'LIKE', '%Kebutuhan Operasional%')
    //                   ->orWhere('dasar_usulan', 'LIKE', '%Penambahan Kapasitas Pelayanan%')
    //                   ->orWhere('dasar_usulan', 'LIKE', '%Pemenuhan Standar Akreditasi%')
    //                   ->orWhere('dasar_usulan', 'LIKE', '%Keselamatan Pasien%')
    //                   ->orWhereNull('dasar_usulan');
    //             })->count(),
                
    //         'perbaikan_count' => Pengajuan::where('bidang_id', $bidangId)
    //             ->where(function($q) {
    //                 $q->where('dasar_usulan', 'LIKE', '%Penggantian Barang Rusak%')
    //                   ->orWhere('dasar_usulan', 'LIKE', '%perbaikan%')
    //                   ->orWhere('dasar_usulan', 'LIKE', '%service%')
    //                   ->orWhere('dasar_usulan', 'LIKE', '%repair%');
    //             })->count(),
    //     ];

    //     return view('ppk.dashboard', compact(
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
    //         'tahunAnggaran', // <-- TAMBAHKAN INI
    //         'bidangId',
    //         'ruangan'
    //     ));
    // }

// public function index(Request $request)
// {
//     $authUser = Auth::user();
    
//     // Ambil bidang_id dari karyawan yang login (PENERIMA)
//     $bidangId = $authUser->karyawan->bidang_id ?? null;
//     $ruangan = $authUser->karyawan->ruangan ?? null;
    
//     // Ambil bidang untuk filter
//     $bidangs = Bidang::all();

//     // Ambil query pencarian
//     $search = $request->input('search');
//     $statusFilter = $request->input('status');
//     $bidangFilter = $request->input('bidang');
//     $typeFilter = $request->input('type', 'permintaan'); // Default tab: Pengadaan
//     $tahunFilter = $request->input('tahun');
//     $bulanFilter = $request->input('bulan'); // ✅ Tambah filter bulan

//     // ============================
//     // AMBIL LIST TAHUN DINAMIS DARI TANGGAL_PENGAJUAN
//     // ============================
//     $listTahun = Pengajuan::selectRaw('YEAR(tanggal_pengajuan) as tahun')
//         ->whereNotNull('tanggal_pengajuan')
//         ->distinct()
//         ->orderBy('tahun', 'desc')
//         ->pluck('tahun');

//     /**
//      * ============================
//      * QUERY DASAR PENGADAAN (PPK)
//      * ============================
//      */
//     $query = Pengajuan::with(['items', 'karyawan', 'bidang'])
//         ->whereNotIn('status', ['diajukan', 'draft', 'revisi', 'disetujui_koordinator','sesuai standard','perlu survei perbaikan'])
//         ->where('log_acc_kabidPenunjang', 'disetujui oleh kabid penunjang')
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
//         // ✅ Filter berdasarkan TAHUN dari tanggal_pengajuan
//         ->when($tahunFilter, function ($q) use ($tahunFilter) {
//             $q->whereYear('tanggal_pengajuan', $tahunFilter);
//         })
//         // ✅ Filter berdasarkan BULAN dari tanggal_pengajuan
//         ->when($bulanFilter, function ($q) use ($bulanFilter) {
//             $q->whereMonth('tanggal_pengajuan', $bulanFilter);
//         });

//     /**
//      * ============================
//      * CLOSURE FILTER TIPE (Berdasarkan KATEGORI)
//      * ============================
//      */
//     $filterPermintaan = function ($q) {
//         $q->where('kategori', 'pengadaan');
//     };

//     $filterPerbaikan = function ($q) {
//         $q->where('kategori', 'pemeliharaan');
//     };

//     /**
//      * ============================
//      * DATA PER TAB
//      * ============================
//      */
//     $permintaanPengajuan = (clone $query)
//         ->where($filterPermintaan)
//         ->orderBy('created_at', 'desc')
//         ->paginate(10, ['*'], 'permintaan_page')
//         ->appends($request->all());

//     $perbaikanPengajuan = (clone $query)
//         ->where($filterPerbaikan)
//         ->orderBy('created_at', 'desc')
//         ->paginate(10, ['*'], 'perbaikan_page')
//         ->appends($request->all());

//     // Data untuk tab yang sedang aktif
//     $allPengajuan = ($typeFilter === 'perbaikan') ? $perbaikanPengajuan : $permintaanPengajuan;

//     /**
//      * ============================
//      * STATISTIK DASHBOARD
//      * ============================
//      */
//     // ✅ Statistik mengambil dari $query agar sinkron dengan filter Tahun, Bulan, Bidang, dll
//     $statsQuery = $query; 

//     $stats = [
//         'total' => (clone $statsQuery)->count(),
//         'disetujui' => (clone $statsQuery)->where('status', 'disetujui')->count(),
//         'ditolak' => (clone $statsQuery)->where('status', 'ditolak')->count(),
//         'menunggu_direktur' => (clone $statsQuery)->where('status', 'menunggu_direktur')->count(),
        
//         // Tambahan untuk hitungan di tab
//         'permintaan_count' => (clone $statsQuery)->where($filterPermintaan)->count(),
//         'perbaikan_count' => (clone $statsQuery)->where($filterPerbaikan)->count(),
//     ];

//     return view('ppk.dashboard', compact(
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
//         'listTahun',
//         'tahunFilter',
//         'bulanFilter',
//         'bidangId',
//         'ruangan'
//     ));
// }

public function index(Request $request)
{
    $authUser = Auth::user();
    
    // Ambil bidang_id dari karyawan yang login (PENERIMA)
    $bidangId = $authUser->karyawan->bidang_id ?? null;
    $ruangan = $authUser->karyawan->ruangan ?? null;
    
    // Ambil bidang untuk filter
    $bidangs = Bidang::all();

    // Ambil query pencarian
    $search = $request->input('search');
    $bidangFilter = $request->input('bidang'); // ✅ Ganti Status jadi Bidang
    $typeFilter = $request->input('type', 'permintaan'); // Default tab: Pengadaan
    $tahunFilter = $request->input('tahun');
    $bulanFilter = $request->input('bulan');

    // ============================
    // AMBIL LIST TAHUN DINAMIS DARI TANGGAL_PENGAJUAN
    // ============================
    $listTahun = Pengajuan::selectRaw('YEAR(tanggal_pengajuan) as tahun')
        ->whereNotNull('tanggal_pengajuan')
        ->distinct()
        ->orderBy('tahun', 'desc')
        ->pluck('tahun');

    /**
     * ============================
     * QUERY DASAR PENGADAAN (PPK)
     * ============================
     */
    $query = Pengajuan::with(['items', 'karyawan', 'bidang'])
        ->whereNotIn('status', ['diajukan', 'draft', 'revisi', 'disetujui_koordinator','sesuai standard','perlu survei perbaikan'])
        ->where('log_acc_kabidPenunjang', 'disetujui oleh kabid penunjang')
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
        // ✅ Filter Bidang (bukan Status lagi)
        ->when($bidangFilter, function ($q) use ($bidangFilter) {
            $q->where('bidang_id', $bidangFilter);
        })
        // ✅ Filter berdasarkan TAHUN dari tanggal_pengajuan
        ->when($tahunFilter, function ($q) use ($tahunFilter) {
            $q->whereYear('tanggal_pengajuan', $tahunFilter);
        })
        // ✅ Filter berdasarkan BULAN dari tanggal_pengajuan
        ->when($bulanFilter, function ($q) use ($bulanFilter) {
            $q->whereMonth('tanggal_pengajuan', $bulanFilter);
        });

    /**
     * ============================
     * CLOSURE FILTER TIPE (Berdasarkan KATEGORI)
     * ============================
     */
    $filterPermintaan = function ($q) {
        $q->where('kategori', 'pengadaan');
    };

    $filterPerbaikan = function ($q) {
        $q->where('kategori', 'pemeliharaan');
    };

    /**
     * ============================
     * DATA PER TAB
     * ============================
     */
    $permintaanPengajuan = (clone $query)
        ->where($filterPermintaan)
        ->orderBy('created_at', 'desc')
        ->paginate(10, ['*'], 'permintaan_page')
        ->appends($request->all());

    $perbaikanPengajuan = (clone $query)
        ->where($filterPerbaikan)
        ->orderBy('created_at', 'desc')
        ->paginate(10, ['*'], 'perbaikan_page')
        ->appends($request->all());

    // Data untuk tab yang sedang aktif
    $allPengajuan = ($typeFilter === 'perbaikan') ? $perbaikanPengajuan : $permintaanPengajuan;

    /**
     * ============================
     * STATISTIK DASHBOARD
     * ============================
     */
    $statsQuery = $query; 

    $stats = [
        'total' => (clone $statsQuery)->count(),
        'disetujui' => (clone $statsQuery)->where('log_status_direktur', 'disetujui')->count(),
        'ditolak' => (clone $statsQuery)->where('status', 'ditolak')->count(),
        'selesai' => (clone $statsQuery)->where('status', 'selesai')->count(),
        
        // Tambahan untuk hitungan di tab
        'permintaan_count' => (clone $statsQuery)->where($filterPermintaan)->count(),
        'perbaikan_count' => (clone $statsQuery)->where($filterPerbaikan)->count(),
    ];

    return view('ppk.dashboard', compact(
        'authUser',
        'allPengajuan',
        'permintaanPengajuan',
        'perbaikanPengajuan',
        'bidangs',
        'search',
        'bidangFilter',
        'typeFilter',
        'stats',
        'listTahun',
        'tahunFilter',
        'bulanFilter',
        'bidangId',
        'ruangan'
    ));
}
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


     public function detail($id)
    {
        $pengajuan = Pengajuan::with([
            'karyawan',
            'penerima',
            'atasan',
            'bidang',
            'items',
        ])->findOrFail($id);

        return view('ppk.detail', compact('pengajuan'));
    }

    public function verifikasi(Request $request, $id)
{
    $request->validate([
        'status_verifikasi'  => 'required|in:menunggu_keuangan,dipertimbangkan,ditolak',
        'catatan_verifikasi' => 'required|string|min:5|max:255',
    ]);

    try {
        $pengajuan = Pengajuan::findOrFail($id);

        // Ambil karyawan dari user login (bukan dari form)
        $karyawan = auth()->user()->karyawan ?? null;
        if (!$karyawan) {
            return back()
                ->withInput()
                ->with('error', 'Akun Anda belum terhubung ke data karyawan. Hubungi admin.');
        }

        $newStatus = $request->status_verifikasi;

        $logMapping = [
            'menunggu_keuangan' => 'Disetujui oleh PPK',
            'dipertimbangkan'   => 'Dipertimbangkan oleh PPK',
            'ditolak'           => 'Ditolak oleh PPK',
        ];

        $data = [
            'status'      => $newStatus,
            'ppk_id'      => $karyawan->id,
            'catatan_ppk' => $request->catatan_verifikasi,
            'log_ppk'     => $logMapping[$newStatus],
            'ppk_at'      => now(),
        ];

        $pengajuan->update($data);

        return redirect()
            ->route('ppk.dashboard')
            ->with('success', 'Pengajuan berhasil diverifikasi!');

    } catch (\Throwable $e) {
        \Log::error('Verifikasi PPK gagal', [
            'pengajuan_id' => $id,
            'user_id'      => auth()->id(),
            'message'      => $e->getMessage(),
            'trace'        => $e->getTraceAsString(),
        ]);

        return back()
            ->withInput()
            ->with('error', 'Gagal verifikasi: ' . $e->getMessage());
    }
}

// public function verifikasi(Request $request, $id)
// {
//     $request->validate([
//         'status_verifikasi'  => 'required|in:menunggu_keuangan,dipertimbangkan,ditolak',
//         'catatan_verifikasi' => 'required|string|min:5|max:255',
//         'ppk_id'             => 'required',
//     ]);

//     try {
//         $pengajuan = Pengajuan::findOrFail($id);

//         $newStatus = $request->status_verifikasi;

//         // Tentukan log berdasarkan status yang dipilih
//         $logMapping = [
//             'menunggu_keuangan'    => 'Disetujui oleh PPK',
//             'dipertimbangkan' => 'Dipertimbangkan oleh PPK',
//             'ditolak'         => 'Ditolak oleh PPK',
//         ];

//         $data = [
//             'status'      => $newStatus,
//             'ppk_id'      => $request->ppk_id,
//             'catatan_ppk' => $request->catatan_verifikasi,
//             'log_ppk'     => $logMapping[$newStatus],
//         ];

//         if ($newStatus === 'menunggu_keuangan') {
//             $data['ppk_at'] = now();
//         }

//         $pengajuan->update($data);

//         return redirect()
//             ->route('ppk.dashboard')
//             ->with('success', 'Pengajuan berhasil diverifikasi!');

//     } catch (\Exception $e) {
//         report($e);

//         return back()
//             ->withInput()
//             ->with('error', 'Gagal verifikasi. Periksa log aplikasi.');
//     }
// }

// public function verifikasi(Request $request, $id)
// {
//     $request->validate([
//         'status_verifikasi'  => 'required|in:menunggu_kpa,dipertimbangkan,ditolak',
//         'catatan_verifikasi' => 'required|string|min:5|max:255',
//         'ppk_id'             => 'required',
//     ]);

//     try {
//         $pengajuan = Pengajuan::findOrFail($id);

//         // Enum DB sudah pakai underscore — tidak perlu mapping
//         $newStatus = $request->status_verifikasi;

//         $data = [
//             'status'      => $newStatus,
//             'ppk_id'      => $request->ppk_id,
//             'catatan_ppk' => $request->catatan_verifikasi,
//             'log_ppk'     => $newStatus,
//         ];

//         if ($newStatus === 'menunggu_kpa') {
//             $data['ppk_at'] = now();
//         }

//         $pengajuan->update($data);

//         return redirect()
//             ->route('ppk.dashboard')
//             ->with('success', '✅ Pengajuan berhasil diverifikasi!');

//     } catch (\Exception $e) {
//         return back()
//             ->withInput()
//             ->with('error', '❌ Gagal verifikasi: ' . $e->getMessage());
//     }
// }



    //  public function verifikasi(Request $request, $id)
    // {
    //     $request->validate([
    //         'status_verifikasi' => 'required|in:menunggu_kpa,dipertimbangkan,ditolak',
    //         'catatan_verifikasi' => 'required|string|min:5',
    //         'ppk_id' => 'required',
    //     ]);

    //     try {
    //         $pengajuan = Pengajuan::findOrFail($id);

    //         // Mapping status untuk konsistensi
    //         $statusMap = [
    //             'menunggu_kpa' => 'menunggu kpa',
    //             'ditolak' => 'ditolak',
    //             'dipertimbangkan' => 'dipertimbangkan',
    //         ];

    //         $newStatus = $statusMap[$request->status_verifikasi];

    //         $data = [
    //             'status' => $newStatus,
    //             'ppk_id' => $request->ppk_id,
    //             'catatan_ppk' => $request->catatan_verifikasi,
    //             'log_ppk' => $newStatus,
    //         ];

    //         if ($request->status_verifikasi == 'menunggu kpa') {
    //             $data['ppk_at'] = now();
    //         }

    //         $pengajuan->update($data);

    //         return redirect()
    //             ->route('ppk.dashboard')
    //             ->with('success', '✅ Pengajuan berhasil diverifikasi!');

    //     } catch (\Exception $e) {
    //         return back()
    //             ->withInput()
    //             ->with('error', '❌ Gagal verifikasi: ' . $e->getMessage());
    //     }
    // }
}
