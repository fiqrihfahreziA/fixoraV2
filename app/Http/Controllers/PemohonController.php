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

        return view('profile.karyawan.editpemohon', compact('authUser', 'daftarRuangan', 'daftarJabatan'));
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

//    public function Showpermintaan(Request $request)
//     {
//         $authUser = Auth::user();
//         $ruangan  = $authUser->karyawan->ruangan;
//         $search   = $request->input('search');
//         $status   = $request->input('status');
//         $kategori = $request->input('kategori');
//         $bidangId = $request->input('bidang');

//         // ============ BASE QUERY (reusable) ============
//         $baseQuery = RequestModel::query()
//             ->where('ruangan', $ruangan)
//             ->when($search, function ($q) use ($search) {
//                 $q->where(function ($sub) use ($search) {
//                     $sub->where('no_pengajuan', 'like', "%{$search}%")
//                         ->orWhereHas('detailBarang', function ($d) use ($search) {
//                             $d->where('nama_barang', 'like', "%{$search}%")
//                               ->orWhere('deskripsi', 'like', "%{$search}%");
//                         });
//                 });
//             })
//             ->when($status, fn($q) => $q->where('status', $status))
//             ->when($kategori, fn($q) => $q->where('kategori', $kategori))
//             ->when($bidangId, fn($q) => $q->where('bidang_id', $bidangId));

//         // ============ STATISTIK (1 query agregat) ============
//         $statsRaw = (clone $baseQuery)
//             ->selectRaw("
//                 COUNT(*) as total,
//                 SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft,
//                 SUM(CASE WHEN status = 'revisi' THEN 1 ELSE 0 END) as revisi,
//                 SUM(CASE WHEN status = 'diajukan' THEN 1 ELSE 0 END) as diajukan,
//                 SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) as disetujui,
//                 SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) as ditolak,
//                 SUM(CASE WHEN status = 'menunggu_direktur' THEN 1 ELSE 0 END) as menunggu_direktur,
//                 SUM(CASE WHEN kategori = 'pemeliharaan' THEN 1 ELSE 0 END) as pemeliharaan_count,
//                 SUM(CASE WHEN kategori = 'pengadaan' THEN 1 ELSE 0 END) as pengadaan_count
//             ")
//             ->first();

//         $stats = [
//             'total'              => (int) ($statsRaw->total ?? 0),
//             'draft'              => (int) ($statsRaw->draft ?? 0),
//             'revisi'             => (int) ($statsRaw->revisi ?? 0),
//             'diajukan'           => (int) ($statsRaw->diajukan ?? 0),
//             'disetujui'          => (int) ($statsRaw->disetujui ?? 0),
//             'ditolak'            => (int) ($statsRaw->ditolak ?? 0),
//             'menunggu_direktur'  => (int) ($statsRaw->menunggu_direktur ?? 0),
//             'pemeliharaan_count' => (int) ($statsRaw->pemeliharaan_count ?? 0),
//             'pengadaan_count'    => (int) ($statsRaw->pengadaan_count ?? 0),
//         ];

//         // ============ EAGER LOAD (batasi kolom biar ringan) ============
//         $itemsRelation = function ($q) {
//             $q->select('id', 'request_id', 'nama_barang', 'jumlah', 'satuan', 'harga_satuan', 'spesifikasi');
//         };

//         $withRelations = [
//             'detailBarang' => $itemsRelation,
//             'bidang:id,nama_bidang',
//             'karyawan:id,nama',
//         ];

//         // ============ TAB: SEMUA (paginate biar ringan) ============
//         $allPengajuan = (clone $baseQuery)
//             ->with($withRelations)
//             ->latest('created_at')
//             ->paginate(10, ['*'], 'page_semua')
//             ->withQueryString();

//         // ============ TAB: PEMELIHARAAN ============
//         $pemeliharaanPengajuan = (clone $baseQuery)
//             ->with($withRelations)
//             ->where('kategori', 'pemeliharaan')
//             ->latest('created_at')
//             ->paginate(10, ['*'], 'page_pemeliharaan')
//             ->withQueryString();

//         // ============ TAB: PENGADAAN ============
//         $pengadaanPengajuan = (clone $baseQuery)
//             ->with($withRelations)
//             ->where('kategori', 'pengadaan')
//             ->latest('created_at')
//             ->paginate(10, ['*'], 'page_pengadaan')
//             ->withQueryString();

//         $bidangs = Bidang::select('id', 'nama_bidang')->orderBy('nama_bidang')->get();

//         return view('pemohon.pengadaan', compact(
//             'authUser',
//             'search',
//             'stats',
//             'allPengajuan',
//             'pemeliharaanPengajuan',
//             'pengadaanPengajuan',
//             'bidangs'
//         ));
//     }
    
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

    // Ambil query pencarian & filter
    $search        = $request->input('search');
    $statusFilter  = $request->input('status');
    $bidangFilter  = $request->input('bidang');
    $typeFilter    = $request->input('type');       // 'permintaan' atau 'perbaikan'
    $kategoriFilter = $request->input('kategori');  // 'pemeliharaan' atau 'pengadaan'

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
        })
        // FILTER KATEGORI: pemeliharaan / pengadaan
        ->when($kategoriFilter, function ($q) use ($kategoriFilter) {
            $q->where('kategori', $kategoriFilter);
        });

    /**
     * ============================
     * DATA UNTUK TABEL SEMUA PENGAJUAN
     * ============================
     */
    $allPengajuan = (clone $query)
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

    /**
     * ============================
     * DATA UNTUK TAB PERMINTAAN
     * Menggunakan dasar_usulan untuk filter
     * ============================
     */
    $permintaanPengajuan = (clone $query)
        ->where(function ($q) {
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
    $perbaikanPengajuan = (clone $query)
        ->where(function ($q) {
            $q->where('dasar_usulan', 'LIKE', '%perbaikan%')
              ->orWhere('dasar_usulan', 'LIKE', '%service%')
              ->orWhere('dasar_usulan', 'LIKE', '%repair%');
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

    /**
     * ============================
     * DATA UNTUK TAB PEMELIHARAAN & PENGADAAN (BARU)
     * ============================
     */
    $pemeliharaanPengajuan = (clone $query)
        ->where('kategori', 'pemeliharaan')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

    $pengadaanPengajuan = (clone $query)
        ->where('kategori', 'pengadaan')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

    /**
     * ============================
     * STATISTIK DASHBOARD
     * ============================
     */
    $stats = [
        'total'             => Pengajuan::where('instalasi', $ruangan)->count(),
        'proses'            => Pengajuan::where('instalasi', $ruangan)->whereNotIn('status', ['selesai', 'draft', 'ditolak']),
        'draft'             => Pengajuan::where('instalasi', $ruangan)->where('status', 'draft')->count(),
        'diajukan'          => Pengajuan::where('instalasi', $ruangan)->where('status', 'diajukan')->count(),
        'disetujui'         => Pengajuan::where('instalasi', $ruangan)->where('status', 'disetujui')->count(),
        'ditolak'           => Pengajuan::where('instalasi', $ruangan)->where('status', 'ditolak')->count(),
        'menunggu_direktur' => Pengajuan::where('instalasi', $ruangan)->where('status', 'menunggu_direktur')->count(),

        // Statistik berdasarkan jenis (menggunakan dasar_usulan)
        'permintaan_count' => Pengajuan::where('instalasi', $ruangan)
            ->where(function ($q) {
                $q->where('dasar_usulan', 'LIKE', '%permintaan%')
                  ->orWhere('dasar_usulan', 'LIKE', '%pengadaan%')
                  ->orWhere('dasar_usulan', 'LIKE', '%beli%')
                  ->orWhereNull('dasar_usulan');
            })->count(),

        'perbaikan_count' => Pengajuan::where('instalasi', $ruangan)
            ->where(function ($q) {
                $q->where('dasar_usulan', 'LIKE', '%perbaikan%')
                  ->orWhere('dasar_usulan', 'LIKE', '%service%')
                  ->orWhere('dasar_usulan', 'LIKE', '%repair%');
            })->count(),

        // Statistik kategori (BARU)
        'pemeliharaan_count' => Pengajuan::where('instalasi', $ruangan)
            ->where('kategori', 'pemeliharaan')->count(),

        'pengadaan_count' => Pengajuan::where('instalasi', $ruangan)
            ->where('kategori', 'pengadaan')->count(),
    ];

    return view('pemohon.pengadaan.pengadaan', compact(
        'authUser',
        'allPengajuan',
        'permintaanPengajuan',
        'perbaikanPengajuan',
        'pemeliharaanPengajuan',
        'pengadaanPengajuan',
        'bidangs',
        'search',
        'statusFilter',
        'bidangFilter',
        'typeFilter',
        'kategoriFilter',
        'stats'
    ));
}

// public function showpengadaan(Request $request)
// {
//     $authUser = Auth::user();
    
//     // Ambil ruangan dari karyawan yang login
//     $ruangan = $authUser->karyawan->ruangan ?? null;
    
//     // Ambil bidang untuk filter
//     $bidangs = Bidang::all();

//     // Ambil query pencarian
//     $search = $request->input('search');
//     $statusFilter = $request->input('status');
//     $bidangFilter = $request->input('bidang');
//     $typeFilter = $request->input('type'); // 'permintaan' atau 'perbaikan'

//     /**
//      * ============================
//      * QUERY DASAR PENGADAAN (SEMUA)
//      * ============================
//      */
//     $query = Pengajuan::with(['items', 'karyawan', 'bidang'])
//         ->where('instalasi', $ruangan)
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
//      * Menggunakan dasar_usulan untuk filter
//      * ============================
//      */
//     $permintaanQuery = clone $query;
//     $permintaanPengajuan = $permintaanQuery->where(function($q) {
//             $q->where('dasar_usulan', 'LIKE', '%permintaan%')
//               ->orWhere('dasar_usulan', 'LIKE', '%pengadaan%')
//               ->orWhere('dasar_usulan', 'LIKE', '%beli%')
//               ->orWhereNull('dasar_usulan'); // Default dianggap permintaan
//         })
//         ->orderBy('created_at', 'desc')
//         ->paginate(10)
//         ->appends($request->all());

//     /**
//      * ============================
//      * DATA UNTUK TAB PERBAIKAN 
//      * Menggunakan dasar_usulan untuk filter
//      * ============================
//      */
//     $perbaikanQuery = clone $query;
//     $perbaikanPengajuan = $perbaikanQuery->where(function($q) {
//             $q->where('dasar_usulan', 'LIKE', '%perbaikan%')
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
//         // 'total' => Pengajuan::where('instalasi', $ruangan)->count(),
//         'total' => Pengajuan::where('instalasi', $ruangan)->count(),
//         'draft' => Pengajuan::where('instalasi', $ruangan)->where('status', 'draft')->count(),
//         'diajukan' => Pengajuan::where('instalasi', $ruangan)->where('status', 'diajukan')->count(),
//         'disetujui' => Pengajuan::where('instalasi', $ruangan)->where('status', 'disetujui')->count(),
//         'ditolak' => Pengajuan::where('instalasi', $ruangan)->where('status', 'ditolak')->count(),
//         'menunggu_direktur' => Pengajuan::where('instalasi', $ruangan)->where('status', 'menunggu_direktur')->count(),
        
//         // Statistik berdasarkan jenis (menggunakan dasar_usulan)
//         'permintaan_count' => Pengajuan::where('instalasi', $ruangan)
//             ->where(function($q) {
//                 $q->where('dasar_usulan', 'LIKE', '%permintaan%')
//                   ->orWhere('dasar_usulan', 'LIKE', '%pengadaan%')
//                   ->orWhere('dasar_usulan', 'LIKE', '%beli%')
//                   ->orWhereNull('dasar_usulan');
//             })->count(),
            
//         'perbaikan_count' => Pengajuan::where('instalasi', $ruangan)
//             ->where(function($q) {
//                 $q->where('dasar_usulan', 'LIKE', '%perbaikan%')
//                   ->orWhere('dasar_usulan', 'LIKE', '%service%')
//                   ->orWhere('dasar_usulan', 'LIKE', '%repair%');
//             })->count(),
//     ];

//     return view('pemohon.pengadaan.pengadaan', compact(
//         'authUser',
//         'allPengajuan',
//         'permintaanPengajuan',
//         'perbaikanPengajuan',
//         'bidangs',
//         'search',
//         'statusFilter',
//         'bidangFilter',
//         'typeFilter',
//         'stats'
//     ));
// }

public function create()
{
    $bidangs = bidang::all();
     $authUser = Auth::user();
    
    return view('pemohon.pengadaan.create', compact('bidangs', 'authUser'));
}

public function storePengadaan(Request $request)
{
    Log::info('📤 DATA MASUK:', $request->all());

    // VALIDASI
    $validated = $request->validate([
        'karyawan_id'          => 'required|exists:karyawans,id',
        'tanggal_pengajuan'    => 'required|date',
        'bidang_id'            => 'nullable|exists:bidangs,id',
        'barjas'               => 'nullable|string',
        'barajas_lainnya'      => 'nullable|string',
        'dasar_usulan'         => 'required|string',
        'dasar_usulan_lainnya' => 'nullable|string',
        'instalasi'            => 'nullable|string',
        'alasan_justifikasi'   => 'required|string',
        'manfaat'              => 'required|string',
        'dampak'               => 'required|string',
        'tahun_anggaran'       => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
        'kondisi_barang_lama'  => 'nullable|string',
        'ket_barang_lama'      => 'nullable|string',
        'kategori'             => 'nullable|string',

        'items' => 'required|array|min:1',
        'items.*.nama_barang'    => 'required|string',
        'items.*.jumlah'         => 'required|integer|min:1',
        'items.*.harga_satuan'   => 'required|numeric|min:0',
        'items.*.spesifikasi'    => 'nullable|string',
        'items.*.satuan'         => 'nullable|string',
        'items.*.dokumen_upload' => 'nullable|file|mimes:pdf,jpg,jpeg,png,xlsx,xls|max:5120',
        'items.*.kepemilikan'    => 'nullable|string|max:150',

        'data_kerusakan'            => 'nullable|file|mimes:pdf|max:5120',
        'analisis_kebutuhan'        => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls|max:5120',
        'spesifikasi_teknis'        => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls|max:5120',
        'penawaran_harga'           => 'nullable|file|mimes:pdf,xlsx,xls,doc,docx|max:5120',
        'data_pengguna'             => 'nullable|file|mimes:pdf,xlsx,xls,csv,doc,docx|max:5120',
        'dokumen_pendukung_lainnya' => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls,jpg,jpeg,png|max:5120',
        'foto_barang'               => 'nullable|array',
        'foto_barang.*'             => 'file|mimes:jpg,jpeg,png|max:5120',
    ]);

    DB::beginTransaction();

    try {
        $totalPengajuan = 0;
        foreach ($request->items as $item) {
            $totalPengajuan += $item['jumlah'] * $item['harga_satuan'];
        }

        $noPengajuan = 'PGD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        while (pengajuan::where('no_pengajuan', $noPengajuan)->exists()) {
            $noPengajuan = 'PGD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }

        // ============================
        // UPLOAD SEMUA LAMPIRAN
        // ============================

        $dataKerusakanPath = null;
        if ($request->hasFile('data_kerusakan')) {
            $file = $request->file('data_kerusakan');
            $fileName = time() . '_data_kerusakan.' . $file->getClientOriginalExtension();
            $dataKerusakanPath = $file->storeAs('pengajuan/data_kerusakan', $fileName, 'public');
        }

        $analisisKebutuhanPath = null;
        if ($request->hasFile('analisis_kebutuhan')) {
            $file = $request->file('analisis_kebutuhan');
            $fileName = time() . '_analisis_kebutuhan.' . $file->getClientOriginalExtension();
            $analisisKebutuhanPath = $file->storeAs('pengajuan/analisis_kebutuhan', $fileName, 'public');
        }

        $spesifikasiTeknisPath = null;
        if ($request->hasFile('spesifikasi_teknis')) {
            $file = $request->file('spesifikasi_teknis');
            $fileName = time() . '_spesifikasi_teknis.' . $file->getClientOriginalExtension();
            $spesifikasiTeknisPath = $file->storeAs('pengajuan/spesifikasi_teknis', $fileName, 'public');
        }

        $penawaranHargaPath = null;
        if ($request->hasFile('penawaran_harga')) {
            $file = $request->file('penawaran_harga');
            $fileName = time() . '_penawaran_harga.' . $file->getClientOriginalExtension();
            $penawaranHargaPath = $file->storeAs('pengajuan/penawaran_harga', $fileName, 'public');
        }

        $dataPenggunaPath = null;
        if ($request->hasFile('data_pengguna')) {
            $file = $request->file('data_pengguna');
            $fileName = time() . '_data_pengguna.' . $file->getClientOriginalExtension();
            $dataPenggunaPath = $file->storeAs('pengajuan/data_pengguna', $fileName, 'public');
        }

        $dokumenLainnyaPath = null;
        if ($request->hasFile('dokumen_pendukung_lainnya')) {
            $file = $request->file('dokumen_pendukung_lainnya');
            $fileName = time() . '_dokumen_pendukung_lainnya.' . $file->getClientOriginalExtension();
            $dokumenLainnyaPath = $file->storeAs('pengajuan/dokumen_lainnya', $fileName, 'public');
        }

        $fotoBarangJson = null;
        if ($request->hasFile('foto_barang')) {
            $fotoBarangPaths = [];
            foreach ($request->file('foto_barang') as $idx => $file) {
                $fileName = time() . '_' . $idx . '_foto_barang.' . $file->getClientOriginalExtension();
                $fotoBarangPaths[] = $file->storeAs('pengajuan/foto_barang', $fileName, 'public');
            }
            if (!empty($fotoBarangPaths)) {
                $fotoBarangJson = json_encode($fotoBarangPaths);
            }
        }

        // ============================
        // CREATE PENGAJUAN
        // ============================
        $pengajuan = pengajuan::create([
            'karyawan_id'          => $request->karyawan_id,
            'bidang_id'            => $request->bidang_id,
            'tanggal_pengajuan'    => $request->tanggal_pengajuan,
            'instalasi'            => $request->instalasi ?? '-',
            'barjas'               => $request->barjas,
            'barajas_lainnya'      => $request->barajas_lainnya,
            'dasar_usulan'         => $request->dasar_usulan,
            'dasar_usulan_lainnya' => $request->dasar_usulan_lainnya,
            'alasan_justifikasi'   => $request->alasan_justifikasi,
            'manfaat'              => $request->manfaat,
            'dampak'               => $request->dampak,
            'tahun_anggaran'       => $request->tahun_anggaran ?? date('Y'),
            'kondisi_barang_lama'  => $request->kondisi_barang_lama,
            'ket_barang_lama'      => $request->ket_barang_lama,
            'total_pengajuan'      => $totalPengajuan,
            'no_pengajuan'         => $noPengajuan,
            'kategori'             => $request->kategori,

            'data_kerusakan'            => $dataKerusakanPath,
            'analisis_kebutuhan'        => $analisisKebutuhanPath,
            'spesifikasi_teknis'        => $spesifikasiTeknisPath,
            'penawaran_harga'           => $penawaranHargaPath,
            'data_pengguna'             => $dataPenggunaPath,
            'dokumen_pendukung_lainnya' => $dokumenLainnyaPath,
            'foto_barang'               => $fotoBarangJson,
        ]);

        Log::info('✅ PENGAJUAN TERSIMPAN:', $pengajuan->toArray());

        // ============================
        // CREATE ITEMS
        // ============================
        foreach ($request->items as $item) {
            $totalItem = $item['jumlah'] * $item['harga_satuan'];

            $dokumenUploadPath = null;
            if (isset($item['dokumen_upload']) && $item['dokumen_upload'] instanceof \Illuminate\Http\UploadedFile) {
                $file = $item['dokumen_upload'];
                $fileName = time() . '_item_dokumen.' . $file->getClientOriginalExtension();
                $dokumenUploadPath = $file->storeAs('pengajuan/item_dokumen', $fileName, 'public');
            }

            $pengajuanItem = pengajuan_item::create([
                'pengajuan_id'     => $pengajuan->id,
                'nama_barang'      => $item['nama_barang'],
                'spesifikasi'      => $item['spesifikasi'] ?? null,
                'satuan'           => $item['satuan'] ?? 'Unit',
                'jumlah'           => $item['jumlah'],
                'harga_satuan'     => $item['harga_satuan'],
                'harga'            => $totalItem,
                'jumlah_disetujui' => $item['jumlah'],
                'kepemilikan'      => $item['kepemilikan'],
                'dokumen_upload'   => $dokumenUploadPath,
            ]);

            Log::info('✅ ITEM TERSIMPAN:', $pengajuanItem->toArray());

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
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTraceAsString()
        ]);

        return back()
            ->withInput()
            ->with('error', '❌ Gagal menyimpan pengajuan: ' . $e->getMessage());
    }
}

// public function storePengadaan(Request $request)
// {
//     // Log data masuk untuk debug
//     Log::info('📤 DATA MASUK:', $request->all());

//     // VALIDASI
//     $validated = $request->validate([
//         'karyawan_id'          => 'required|exists:karyawans,id',
//         'tanggal_pengajuan'    => 'required|date',
//         'bidang_id'            => 'nullable|exists:bidangs,id',
//         'dasar_usulan'         => 'required|string',
//         'dasar_usulan_lainnya' => 'nullable|string',   // ← TAMBAH
//         'instalasi'            => 'nullable|string',
//         'alasan_justifikasi'   => 'required|string',
//         'manfaat'              => 'required|string',
//         'dampak'               => 'required|string',
//         'tahun_anggaran'       => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
//         'kondisi_barang_lama'  => 'nullable|string',
//         'ket_barang_lama'      => 'nullable|string',

//         'items' => 'required|array|min:1',
//         'items.*.nama_barang'    => 'required|string',
//         'items.*.jumlah'         => 'required|integer|min:1',
//         'items.*.harga_satuan'   => 'required|numeric|min:0',
//         'items.*.spesifikasi'    => 'nullable|string',
//         'items.*.satuan'         => 'nullable|string',
//         'items.*.dokumen_upload' => 'nullable|file|mimes:pdf,jpg,jpeg,png,xlsx,xls|max:5120',

//         // LAMPIRAN
//         'data_kerusakan'            => 'nullable|file|mimes:pdf|max:5120',
//         'analisis_kebutuhan'        => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls|max:5120',
//         'spesifikasi_teknis'        => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls|max:5120',
//         'penawaran_harga'           => 'nullable|file|mimes:pdf,xlsx,xls,doc,docx|max:5120',
//         'data_pengguna'             => 'nullable|file|mimes:pdf,xlsx,xls,csv,doc,docx|max:5120',
//         'dokumen_pendukung_lainnya' => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls,jpg,jpeg,png|max:5120',
//         'foto_barang'               => 'nullable|array',
//         'foto_barang.*'             => 'file|mimes:jpg,jpeg,png|max:5120',
//     ]);

//     DB::beginTransaction();

//     try {
//         // Hitung total pengajuan
//         $totalPengajuan = 0;
//         foreach ($request->items as $item) {
//             $totalPengajuan += $item['jumlah'] * $item['harga_satuan'];
//         }

//         // Generate nomor pengajuan unik
//         $noPengajuan = 'PGD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
//         while (pengajuan::where('no_pengajuan', $noPengajuan)->exists()) {
//             $noPengajuan = 'PGD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
//         }

//         // ============================
//         // UPLOAD SEMUA LAMPIRAN
//         // ============================

//         // Data Kerusakan (PDF)
//         $dataKerusakanPath = null;
//         if ($request->hasFile('data_kerusakan')) {
//             $file = $request->file('data_kerusakan');
//             $fileName = time() . '_data_kerusakan.' . $file->getClientOriginalExtension();
//             $dataKerusakanPath = $file->storeAs('pengajuan/data_kerusakan', $fileName, 'public');
//         }

//         // Analisis Kebutuhan
//         $analisisKebutuhanPath = null;
//         if ($request->hasFile('analisis_kebutuhan')) {
//             $file = $request->file('analisis_kebutuhan');
//             $fileName = time() . '_analisis_kebutuhan.' . $file->getClientOriginalExtension();
//             $analisisKebutuhanPath = $file->storeAs('pengajuan/analisis_kebutuhan', $fileName, 'public');
//         }

//         // Spesifikasi Teknis
//         $spesifikasiTeknisPath = null;
//         if ($request->hasFile('spesifikasi_teknis')) {
//             $file = $request->file('spesifikasi_teknis');
//             $fileName = time() . '_spesifikasi_teknis.' . $file->getClientOriginalExtension();
//             $spesifikasiTeknisPath = $file->storeAs('pengajuan/spesifikasi_teknis', $fileName, 'public');
//         }

//         // Penawaran Harga
//         $penawaranHargaPath = null;
//         if ($request->hasFile('penawaran_harga')) {
//             $file = $request->file('penawaran_harga');
//             $fileName = time() . '_penawaran_harga.' . $file->getClientOriginalExtension();
//             $penawaranHargaPath = $file->storeAs('pengajuan/penawaran_harga', $fileName, 'public');
//         }

//         // Data Pengguna
//         $dataPenggunaPath = null;
//         if ($request->hasFile('data_pengguna')) {
//             $file = $request->file('data_pengguna');
//             $fileName = time() . '_data_pengguna.' . $file->getClientOriginalExtension();
//             $dataPenggunaPath = $file->storeAs('pengajuan/data_pengguna', $fileName, 'public');
//         }

//         // Dokumen Pendukung Lainnya (single file)
//         $dokumenLainnyaPath = null;
//         if ($request->hasFile('dokumen_pendukung_lainnya')) {
//             $file = $request->file('dokumen_pendukung_lainnya');
//             $fileName = time() . '_dokumen_pendukung_lainnya.' . $file->getClientOriginalExtension();
//             $dokumenLainnyaPath = $file->storeAs('pengajuan/dokumen_lainnya', $fileName, 'public');
//         }

//         // Foto Barang (multiple → JSON array)
//         $fotoBarangJson = null;
//         if ($request->hasFile('foto_barang')) {
//             $fotoBarangPaths = [];
//             foreach ($request->file('foto_barang') as $idx => $file) {
//                 $fileName = time() . '_' . $idx . '_foto_barang.' . $file->getClientOriginalExtension();
//                 $fotoBarangPaths[] = $file->storeAs('pengajuan/foto_barang', $fileName, 'public');
//             }
//             if (!empty($fotoBarangPaths)) {
//                 $fotoBarangJson = json_encode($fotoBarangPaths);
//             }
//         }

//         // ============================
//         // CREATE PENGAJUAN
//         // ============================
//         $pengajuan = pengajuan::create([
//             'karyawan_id'          => $request->karyawan_id,
//             'bidang_id'            => $request->bidang_id,
//             'tanggal_pengajuan'    => $request->tanggal_pengajuan,
//             'instalasi'            => $request->instalasi ?? '-',
//             'dasar_usulan'         => $request->dasar_usulan,
//             'dasar_usulan_lainnya' => $request->dasar_usulan_lainnya,   // ← TAMBAH
//             'alasan_justifikasi'   => $request->alasan_justifikasi,
//             'manfaat'              => $request->manfaat,
//             'dampak'               => $request->dampak,
//             'tahun_anggaran'       => $request->tahun_anggaran ?? date('Y'),
//             'kondisi_barang_lama'  => $request->kondisi_barang_lama,
//             'ket_barang_lama'      => $request->ket_barang_lama,
//             'total_pengajuan'      => $totalPengajuan,
//             'no_pengajuan'         => $noPengajuan,

//             // SEMUA LAMPIRAN DI SINI
//             'data_kerusakan'            => $dataKerusakanPath,
//             'analisis_kebutuhan'        => $analisisKebutuhanPath,
//             'spesifikasi_teknis'        => $spesifikasiTeknisPath,
//             'penawaran_harga'           => $penawaranHargaPath,
//             'data_pengguna'             => $dataPenggunaPath,
//             'dokumen_pendukung_lainnya' => $dokumenLainnyaPath,
//             'foto_barang'               => $fotoBarangJson,
//         ]);

//         Log::info('✅ PENGAJUAN TERSIMPAN:', $pengajuan->toArray());

//         // ============================
//         // CREATE ITEMS
//         // ============================
//         foreach ($request->items as $item) {
//             $totalItem = $item['jumlah'] * $item['harga_satuan'];

//             // Upload dokumen per item (jika ada)
//             $dokumenUploadPath = null;
//             if (isset($item['dokumen_upload']) && $item['dokumen_upload'] instanceof \Illuminate\Http\UploadedFile) {
//                 $file = $item['dokumen_upload'];
//                 $fileName = time() . '_item_dokumen.' . $file->getClientOriginalExtension();
//                 $dokumenUploadPath = $file->storeAs('pengajuan/item_dokumen', $fileName, 'public');
//             }

//             $pengajuanItem = pengajuan_item::create([
//                 'pengajuan_id'     => $pengajuan->id,
//                 'nama_barang'      => $item['nama_barang'],
//                 'spesifikasi'      => $item['spesifikasi'] ?? null,
//                 'satuan'           => $item['satuan'] ?? 'Unit',
//                 'jumlah'           => $item['jumlah'],
//                 'harga_satuan'     => $item['harga_satuan'],
//                 'harga'            => $totalItem,
//                 'jumlah_disetujui' => $item['jumlah'],
//                 'dokumen_upload'   => $dokumenUploadPath,
//             ]);

//             Log::info('✅ ITEM TERSIMPAN:', $pengajuanItem->toArray());

//             // CREATE BARANG TERSEDIA JIKA ADA
//             if (!empty($item['ada_barang_tersedia']) && !empty($item['barang_tersedia'])) {
//                 $bt = $item['barang_tersedia'];

//                 barang_tersedia::create([
//                     'pengajuan_item_id' => $pengajuanItem->id,
//                     'nama_barang'       => $bt['nama_barang'] ?? $item['nama_barang'],
//                     'jumlah'            => $bt['jumlah'] ?? 0,
//                     'tahun_perolehan'   => $bt['tahun_perolehan'] ?? null,
//                     'kondisi'           => $bt['kondisi'] ?? 'Baik',
//                     'keterangan'        => $bt['keterangan'] ?? null,
//                 ]);

//                 Log::info('✅ BARANG TERSEDIA TERSIMPAN');
//             }
//         }

//         DB::commit();

//         return redirect()
//             ->route('pemohon.pengadaan')
//             ->with('success', '✅ Pengajuan berhasil disimpan! Nomor: ' . $noPengajuan);

//     } catch (\Exception $e) {
//         DB::rollBack();

//         Log::error('❌ ERROR SAVE PENGAJUAN:', [
//             'message' => $e->getMessage(),
//             'file'    => $e->getFile(),
//             'line'    => $e->getLine(),
//             'trace'   => $e->getTraceAsString()
//         ]);

//         return back()
//             ->withInput()
//             ->with('error', '❌ Gagal menyimpan pengajuan: ' . $e->getMessage());
//     }
// }

// public function storePengadaan(Request $request)
// {
//     // Log data masuk untuk debug
//     Log::info('📤 DATA MASUK:', $request->all());

//     // VALIDASI
//     $validated = $request->validate([
//         'karyawan_id'        => 'required|exists:karyawans,id',
//         'tanggal_pengajuan'  => 'required|date',
//         'bidang_id'          => 'nullable|exists:bidangs,id',
//         'dasar_usulan'       => 'required|string',
//         'instalasi'          => 'nullable|string',
//         'alasan_justifikasi' => 'required|string',
//         'manfaat'            => 'required|string',
//         'dampak'             => 'required|string',
//         'tahun_anggaran'     => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
//         'kondisi_barang_lama' => 'nullable|string',
//         'ket_barang_lama'    => 'nullable|string',

//         'items' => 'required|array|min:1',
//         'items.*.nama_barang'    => 'required|string',
//         'items.*.jumlah'         => 'required|integer|min:1',
//         'items.*.harga_satuan'   => 'required|numeric|min:0',
//         'items.*.spesifikasi'    => 'nullable|string',
//         'items.*.satuan'         => 'nullable|string',
//         'items.*.dokumen_upload' => 'nullable|file|mimes:pdf,jpg,jpeg,png,xlsx,xls|max:5120',

//         // LAMPIRAN
//         'data_kerusakan'            => 'nullable|file|mimes:pdf|max:5120',
//         'analisis_kebutuhan'        => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls|max:5120',
//         'spesifikasi_teknis'        => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls|max:5120',
//         'penawaran_harga'           => 'nullable|file|mimes:pdf,xlsx,xls,doc,docx|max:5120',
//         'data_pengguna'             => 'nullable|file|mimes:pdf,xlsx,xls,csv,doc,docx|max:5120',
//         'dokumen_pendukung_lainnya' => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls,jpg,jpeg,png|max:5120',
//         'foto_barang'               => 'nullable|array',
//         'foto_barang.*'             => 'file|mimes:jpg,jpeg,png|max:5120',
//     ]);

//     DB::beginTransaction();

//     try {
//         // Hitung total pengajuan
//         $totalPengajuan = 0;
//         foreach ($request->items as $item) {
//             $totalPengajuan += $item['jumlah'] * $item['harga_satuan'];
//         }

//         // Generate nomor pengajuan unik
//         $noPengajuan = 'PGD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
//         while (pengajuan::where('no_pengajuan', $noPengajuan)->exists()) {
//             $noPengajuan = 'PGD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
//         }

//         // ============================
//         // UPLOAD SEMUA LAMPIRAN
//         // ============================

//         // Data Kerusakan (PDF)
//         $dataKerusakanPath = null;
//         if ($request->hasFile('data_kerusakan')) {
//             $file = $request->file('data_kerusakan');
//             $fileName = time() . '_data_kerusakan.' . $file->getClientOriginalExtension();
//             $dataKerusakanPath = $file->storeAs('pengajuan/data_kerusakan', $fileName, 'public');
//         }

//         // Analisis Kebutuhan
//         $analisisKebutuhanPath = null;
//         if ($request->hasFile('analisis_kebutuhan')) {
//             $file = $request->file('analisis_kebutuhan');
//             $fileName = time() . '_analisis_kebutuhan.' . $file->getClientOriginalExtension();
//             $analisisKebutuhanPath = $file->storeAs('pengajuan/analisis_kebutuhan', $fileName, 'public');
//         }

//         // Spesifikasi Teknis
//         $spesifikasiTeknisPath = null;
//         if ($request->hasFile('spesifikasi_teknis')) {
//             $file = $request->file('spesifikasi_teknis');
//             $fileName = time() . '_spesifikasi_teknis.' . $file->getClientOriginalExtension();
//             $spesifikasiTeknisPath = $file->storeAs('pengajuan/spesifikasi_teknis', $fileName, 'public');
//         }

//         // Penawaran Harga
//         $penawaranHargaPath = null;
//         if ($request->hasFile('penawaran_harga')) {
//             $file = $request->file('penawaran_harga');
//             $fileName = time() . '_penawaran_harga.' . $file->getClientOriginalExtension();
//             $penawaranHargaPath = $file->storeAs('pengajuan/penawaran_harga', $fileName, 'public');
//         }

//         // Data Pengguna
//         $dataPenggunaPath = null;
//         if ($request->hasFile('data_pengguna')) {
//             $file = $request->file('data_pengguna');
//             $fileName = time() . '_data_pengguna.' . $file->getClientOriginalExtension();
//             $dataPenggunaPath = $file->storeAs('pengajuan/data_pengguna', $fileName, 'public');
//         }

//         // Dokumen Pendukung Lainnya (single file)
//         $dokumenLainnyaPath = null;
//         if ($request->hasFile('dokumen_pendukung_lainnya')) {
//             $file = $request->file('dokumen_pendukung_lainnya');
//             $fileName = time() . '_dokumen_pendukung_lainnya.' . $file->getClientOriginalExtension();
//             $dokumenLainnyaPath = $file->storeAs('pengajuan/dokumen_lainnya', $fileName, 'public');
//         }

//         // Foto Barang (multiple → JSON array)
//         $fotoBarangJson = null;
//         if ($request->hasFile('foto_barang')) {
//             $fotoBarangPaths = [];
//             foreach ($request->file('foto_barang') as $idx => $file) {
//                 $fileName = time() . '_' . $idx . '_foto_barang.' . $file->getClientOriginalExtension();
//                 $fotoBarangPaths[] = $file->storeAs('pengajuan/foto_barang', $fileName, 'public');
//             }
//             if (!empty($fotoBarangPaths)) {
//                 $fotoBarangJson = json_encode($fotoBarangPaths);
//             }
//         }

//         // ============================
//         // CREATE PENGAJUAN
//         // ============================
//         $pengajuan = pengajuan::create([
//             'karyawan_id'         => $request->karyawan_id,
//             'bidang_id'           => $request->bidang_id,
//             'tanggal_pengajuan'   => $request->tanggal_pengajuan,
//             'instalasi'           => $request->instalasi ?? '-',
//             'dasar_usulan'        => $request->dasar_usulan,
//             'alasan_justifikasi'  => $request->alasan_justifikasi,
//             'manfaat'             => $request->manfaat,
//             'dampak'              => $request->dampak,
//             'tahun_anggaran'      => $request->tahun_anggaran ?? date('Y'),
//             'kondisi_barang_lama' => $request->kondisi_barang_lama,
//             'ket_barang_lama'     => $request->ket_barang_lama,
//             'total_pengajuan'     => $totalPengajuan,
//             'no_pengajuan'        => $noPengajuan,

//             // SEMUA LAMPIRAN DI SINI
//             'data_kerusakan'            => $dataKerusakanPath,
//             'analisis_kebutuhan'        => $analisisKebutuhanPath,
//             'spesifikasi_teknis'        => $spesifikasiTeknisPath,
//             'penawaran_harga'           => $penawaranHargaPath,
//             'data_pengguna'             => $dataPenggunaPath,
//             'dokumen_pendukung_lainnya' => $dokumenLainnyaPath,
//             'foto_barang'               => $fotoBarangJson,
//         ]);

//         Log::info('✅ PENGAJUAN TERSIMPAN:', $pengajuan->toArray());

//         // ============================
//         // CREATE ITEMS
//         // ============================
//         foreach ($request->items as $item) {
//             $totalItem = $item['jumlah'] * $item['harga_satuan'];

//             // Upload dokumen per item (jika ada)
//             $dokumenUploadPath = null;
//             if (isset($item['dokumen_upload']) && $item['dokumen_upload'] instanceof \Illuminate\Http\UploadedFile) {
//                 $file = $item['dokumen_upload'];
//                 $fileName = time() . '_item_dokumen.' . $file->getClientOriginalExtension();
//                 $dokumenUploadPath = $file->storeAs('pengajuan/item_dokumen', $fileName, 'public');
//             }

//             $pengajuanItem = pengajuan_item::create([
//                 'pengajuan_id'     => $pengajuan->id,
//                 'nama_barang'      => $item['nama_barang'],
//                 'spesifikasi'      => $item['spesifikasi'] ?? null,
//                 'satuan'           => $item['satuan'] ?? 'Unit',
//                 'jumlah'           => $item['jumlah'],
//                 'harga_satuan'     => $item['harga_satuan'],
//                 'harga'            => $totalItem,
//                 'jumlah_disetujui' => $item['jumlah'],
//                 'dokumen_upload'   => $dokumenUploadPath,
//             ]);

//             Log::info('✅ ITEM TERSIMPAN:', $pengajuanItem->toArray());

//             // CREATE BARANG TERSEDIA JIKA ADA
//             if (!empty($item['ada_barang_tersedia']) && !empty($item['barang_tersedia'])) {
//                 $bt = $item['barang_tersedia'];

//                 barang_tersedia::create([
//                     'pengajuan_item_id' => $pengajuanItem->id,
//                     'nama_barang'       => $bt['nama_barang'] ?? $item['nama_barang'],
//                     'jumlah'            => $bt['jumlah'] ?? 0,
//                     'tahun_perolehan'   => $bt['tahun_perolehan'] ?? null,
//                     'kondisi'           => $bt['kondisi'] ?? 'Baik',
//                     'keterangan'        => $bt['keterangan'] ?? null,
//                 ]);

//                 Log::info('✅ BARANG TERSEDIA TERSIMPAN');
//             }
//         }

//         DB::commit();

//         return redirect()
//             ->route('pemohon.pengadaan')
//             ->with('success', '✅ Pengajuan berhasil disimpan! Nomor: ' . $noPengajuan);

//     } catch (\Exception $e) {
//         DB::rollBack();

//         Log::error('❌ ERROR SAVE PENGAJUAN:', [
//             'message' => $e->getMessage(),
//             'file'    => $e->getFile(),
//             'line'    => $e->getLine(),
//             'trace'   => $e->getTraceAsString()
//         ]);

//         return back()
//             ->withInput()
//             ->with('error', '❌ Gagal menyimpan pengajuan: ' . $e->getMessage());
//     }
// }

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
    
    $bidangs = Bidang::all();

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

    Log::info('📤 DATA UPDATE MASUK:', $request->all());

    // VALIDASI
    $validated = $request->validate([
        'tanggal_pengajuan'    => 'required|date',
        'bidang_id'            => 'nullable|exists:bidangs,id',
        'barjas'               => 'nullable|string',
        'barajas_lainnya'      => 'nullable|string',
        'dasar_usulan'         => 'required|string',
        'dasar_usulan_lainnya' => 'nullable|string',
        'instalasi'            => 'nullable|string',
        'alasan_justifikasi'   => 'required|string',
        'manfaat'              => 'required|string',
        'dampak'               => 'required|string',
        'tahun_anggaran'       => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
        'kondisi_barang_lama'  => 'nullable|string',
        'ket_barang_lama'      => 'nullable|string',

        'items' => 'required|array|min:1',
        'items.*.nama_barang'    => 'required|string',
        'items.*.jumlah'         => 'required|integer|min:1',
        'items.*.harga_satuan'   => 'required|numeric|min:0',
        'items.*.spesifikasi'    => 'nullable|string',
        'items.*.satuan'         => 'nullable|string',
        'items.*.dokumen_upload' => 'nullable|file|mimes:pdf,jpg,jpeg,png,xlsx,xls|max:5120',

        // LAMPIRAN
        'data_kerusakan'            => 'nullable|file|mimes:pdf|max:5120',
        'analisis_kebutuhan'        => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls|max:5120',
        'spesifikasi_teknis'        => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls|max:5120',
        'penawaran_harga'           => 'nullable|file|mimes:pdf,xlsx,xls,doc,docx|max:5120',
        'data_pengguna'             => 'nullable|file|mimes:pdf,xlsx,xls,csv,doc,docx|max:5120',
        'dokumen_pendukung_lainnya' => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls,jpg,jpeg,png|max:5120',
        'foto_barang'               => 'nullable|array',
        'foto_barang.*'             => 'file|mimes:jpg,jpeg,png|max:5120',
    ]);

    DB::beginTransaction();

    try {
        // Hitung total pengajuan
        $totalPengajuan = 0;
        foreach ($request->items as $item) {
            $totalPengajuan += $item['jumlah'] * $item['harga_satuan'];
        }

        // ============================
        // UPLOAD LAMPIRAN (jika ada file baru, replace)
        // ============================

        // Data Kerusakan
        $dataKerusakanPath = $pengajuan->data_kerusakan;
        if ($request->hasFile('data_kerusakan')) {
            if ($pengajuan->data_kerusakan && Storage::disk('public')->exists($pengajuan->data_kerusakan)) {
                Storage::disk('public')->delete($pengajuan->data_kerusakan);
            }
            $file = $request->file('data_kerusakan');
            $fileName = time() . '_data_kerusakan.' . $file->getClientOriginalExtension();
            $dataKerusakanPath = $file->storeAs('pengajuan/data_kerusakan', $fileName, 'public');
        }

        // Analisis Kebutuhan
        $analisisKebutuhanPath = $pengajuan->analisis_kebutuhan;
        if ($request->hasFile('analisis_kebutuhan')) {
            if ($pengajuan->analisis_kebutuhan && Storage::disk('public')->exists($pengajuan->analisis_kebutuhan)) {
                Storage::disk('public')->delete($pengajuan->analisis_kebutuhan);
            }
            $file = $request->file('analisis_kebutuhan');
            $fileName = time() . '_analisis_kebutuhan.' . $file->getClientOriginalExtension();
            $analisisKebutuhanPath = $file->storeAs('pengajuan/analisis_kebutuhan', $fileName, 'public');
        }

        // Spesifikasi Teknis
        $spesifikasiTeknisPath = $pengajuan->spesifikasi_teknis;
        if ($request->hasFile('spesifikasi_teknis')) {
            if ($pengajuan->spesifikasi_teknis && Storage::disk('public')->exists($pengajuan->spesifikasi_teknis)) {
                Storage::disk('public')->delete($pengajuan->spesifikasi_teknis);
            }
            $file = $request->file('spesifikasi_teknis');
            $fileName = time() . '_spesifikasi_teknis.' . $file->getClientOriginalExtension();
            $spesifikasiTeknisPath = $file->storeAs('pengajuan/spesifikasi_teknis', $fileName, 'public');
        }

        // Penawaran Harga
        $penawaranHargaPath = $pengajuan->penawaran_harga;
        if ($request->hasFile('penawaran_harga')) {
            if ($pengajuan->penawaran_harga && Storage::disk('public')->exists($pengajuan->penawaran_harga)) {
                Storage::disk('public')->delete($pengajuan->penawaran_harga);
            }
            $file = $request->file('penawaran_harga');
            $fileName = time() . '_penawaran_harga.' . $file->getClientOriginalExtension();
            $penawaranHargaPath = $file->storeAs('pengajuan/penawaran_harga', $fileName, 'public');
        }

        // Data Pengguna
        $dataPenggunaPath = $pengajuan->data_pengguna;
        if ($request->hasFile('data_pengguna')) {
            if ($pengajuan->data_pengguna && Storage::disk('public')->exists($pengajuan->data_pengguna)) {
                Storage::disk('public')->delete($pengajuan->data_pengguna);
            }
            $file = $request->file('data_pengguna');
            $fileName = time() . '_data_pengguna.' . $file->getClientOriginalExtension();
            $dataPenggunaPath = $file->storeAs('pengajuan/data_pengguna', $fileName, 'public');
        }

        // Dokumen Pendukung Lainnya
        $dokumenLainnyaPath = $pengajuan->dokumen_pendukung_lainnya;
        if ($request->hasFile('dokumen_pendukung_lainnya')) {
            if ($pengajuan->dokumen_pendukung_lainnya && Storage::disk('public')->exists($pengajuan->dokumen_pendukung_lainnya)) {
                Storage::disk('public')->delete($pengajuan->dokumen_pendukung_lainnya);
            }
            $file = $request->file('dokumen_pendukung_lainnya');
            $fileName = time() . '_dokumen_pendukung_lainnya.' . $file->getClientOriginalExtension();
            $dokumenLainnyaPath = $file->storeAs('pengajuan/dokumen_lainnya', $fileName, 'public');
        }

        // Foto Barang (multiple)
        $fotoBarangJson = $pengajuan->foto_barang;
        if ($request->hasFile('foto_barang')) {
            // Hapus file lama
            if ($pengajuan->foto_barang) {
                $oldFiles = json_decode($pengajuan->foto_barang, true) ?? [];
                foreach ($oldFiles as $old) {
                    if (Storage::disk('public')->exists($old)) {
                        Storage::disk('public')->delete($old);
                    }
                }
            }
            $fotoBarangPaths = [];
            foreach ($request->file('foto_barang') as $idx => $file) {
                $fileName = time() . '_' . $idx . '_foto_barang.' . $file->getClientOriginalExtension();
                $fotoBarangPaths[] = $file->storeAs('pengajuan/foto_barang', $fileName, 'public');
            }
            if (!empty($fotoBarangPaths)) {
                $fotoBarangJson = json_encode($fotoBarangPaths);
            }
        }

        // UPDATE PENGAJUAN
        $pengajuan->update([
            'tanggal_pengajuan'    => $request->tanggal_pengajuan,
            'bidang_id'            => $request->bidang_id,
            'instalasi'            => $request->instalasi ?? '-',
            'barjas'               => $request->barjas,
            'barajas_lainnya'      => $request->barajas_lainnya,
            'dasar_usulan'         => $request->dasar_usulan,
            'dasar_usulan_lainnya' => $request->dasar_usulan_lainnya,
            'alasan_justifikasi'   => $request->alasan_justifikasi,
            'manfaat'              => $request->manfaat,
            'dampak'               => $request->dampak,
            'tahun_anggaran'       => $request->tahun_anggaran ?? date('Y'),
            'kondisi_barang_lama'  => $request->kondisi_barang_lama,
            'ket_barang_lama'      => $request->ket_barang_lama,
            'total_pengajuan'      => $totalPengajuan,

            'data_kerusakan'            => $dataKerusakanPath,
            'analisis_kebutuhan'        => $analisisKebutuhanPath,
            'spesifikasi_teknis'        => $spesifikasiTeknisPath,
            'penawaran_harga'           => $penawaranHargaPath,
            'data_pengguna'             => $dataPenggunaPath,
            'dokumen_pendukung_lainnya' => $dokumenLainnyaPath,
            'foto_barang'               => $fotoBarangJson,

            'status' => $pengajuan->status == 'revisi' ? 'draft' : $pengajuan->status,
        ]);

        Log::info('✅ PENGAJUAN UPDATED:', $pengajuan->toArray());

        // HAPUS ITEMS LAMA + BARANG TERSEDIA (cascade)
        $oldItems = pengajuan_item::where('pengajuan_id', $pengajuan->id)->get();
        foreach ($oldItems as $oldItem) {
            // Hapus dokumen upload item lama
            if ($oldItem->dokumen_upload && Storage::disk('public')->exists($oldItem->dokumen_upload)) {
                Storage::disk('public')->delete($oldItem->dokumen_upload);
            }
            // barang_tersedia akan terhapus otomatis kalau ada cascade, atau manual:
            barang_tersedia::where('pengajuan_item_id', $oldItem->id)->delete();
            $oldItem->delete();
        }

        // CREATE ITEMS BARU
        foreach ($request->items as $item) {
            $totalItem = $item['jumlah'] * $item['harga_satuan'];

            // Upload dokumen per item (jika ada file baru)
            $dokumenUploadPath = null;
            if (isset($item['dokumen_upload']) && $item['dokumen_upload'] instanceof \Illuminate\Http\UploadedFile) {
                $file = $item['dokumen_upload'];
                $fileName = time() . '_item_dokumen.' . $file->getClientOriginalExtension();
                $dokumenUploadPath = $file->storeAs('pengajuan/item_dokumen', $fileName, 'public');
            }

            $pengajuanItem = pengajuan_item::create([
                'pengajuan_id'     => $pengajuan->id,
                'nama_barang'      => $item['nama_barang'],
                'spesifikasi'      => $item['spesifikasi'] ?? null,
                'satuan'           => $item['satuan'] ?? 'Unit',
                'jumlah'           => $item['jumlah'],
                'harga_satuan'     => $item['harga_satuan'],
                'harga'            => $totalItem,
                'jumlah_disetujui' => $item['jumlah'],
                'dokumen_upload'   => $dokumenUploadPath,
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

// public function updatePengadaan(Request $request, $id)
// {
//     $authUser = Auth::user();
//     $karyawanId = $authUser->karyawan->id ?? null;
    
//     // Cek apakah pengajuan milik user dan status draft/revisi
//     $pengajuan = pengajuan::where('karyawan_id', $karyawanId)
//         ->whereIn('status', ['draft', 'revisi'])
//         ->findOrFail($id);

//     // Log data masuk untuk debug
//     Log::info('📤 DATA UPDATE MASUK:', $request->all());

//     // VALIDASI
//     $validated = $request->validate([
//         'tanggal_pengajuan'  => 'required|date',
//         'bidang_id'          => 'nullable|exists:bidangs,id',
//         'dasar_usulan'       => 'required|string',
//         'instalasi'          => 'nullable|string',
//         'alasan_justifikasi' => 'required|string',
//         'manfaat'            => 'required|string',
//         'dampak'             => 'required|string',
//         'tahun_anggaran'     => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
//         'kondisi_barang_lama' => 'nullable|string',
//         'ket_barang_lama'    => 'nullable|string',

//         'items' => 'required|array|min:1',
//         'items.*.nama_barang'  => 'required|string',
//         'items.*.jumlah'       => 'required|integer|min:1',
//         'items.*.harga_satuan' => 'required|numeric|min:0',
//         'items.*.spesifikasi'  => 'nullable|string',
//         'items.*.satuan'       => 'nullable|string',

//         // HANYA DATA KERUSAKAN (PDF)
//         'data_kerusakan'     => 'nullable|file|mimes:pdf|max:5120',
//     ]);

//     DB::beginTransaction();

//     try {
//         // Hitung total pengajuan
//         $totalPengajuan = 0;
//         foreach ($request->items as $item) {
//             $totalPengajuan += $item['jumlah'] * $item['harga_satuan'];
//         }

//         // UPLOAD DATA KERUSAKAN (jika ada file baru)
//         $dataKerusakanPath = $pengajuan->data_kerusakan; // retain existing
//         if ($request->hasFile('data_kerusakan')) {
//             // Hapus file lama jika ada
//             if ($pengajuan->data_kerusakan && Storage::disk('public')->exists($pengajuan->data_kerusakan)) {
//                 Storage::disk('public')->delete($pengajuan->data_kerusakan);
//             }
            
//             $file = $request->file('data_kerusakan');
//             $fileName = time() . '_data_kerusakan.' . $file->getClientOriginalExtension();
//             $dataKerusakanPath = $file->storeAs('pengajuan/data_kerusakan', $fileName, 'public');
//         }

//         // UPDATE PENGAJUAN
//         $pengajuan->update([
//             'tanggal_pengajuan'  => $request->tanggal_pengajuan,
//             'bidang_id'          => $request->bidang_id,
//             'instalasi'          => $request->instalasi ?? '-',
//             'dasar_usulan'       => $request->dasar_usulan,
//             'alasan_justifikasi' => $request->alasan_justifikasi,
//             'manfaat'            => $request->manfaat,
//             'dampak'             => $request->dampak,
//             'tahun_anggaran'     => $request->tahun_anggaran ?? date('Y'),
//             'kondisi_barang_lama' => $request->kondisi_barang_lama,
//             'ket_barang_lama'    => $request->ket_barang_lama,
//             'total_pengajuan'    => $totalPengajuan,
//             'data_kerusakan'     => $dataKerusakanPath,
//             // Jika status revisi, ubah kembali ke draft setelah diedit
//             'status' => $pengajuan->status == 'revisi' ? 'draft' : $pengajuan->status,
//         ]);

//         Log::info('✅ PENGAJUAN UPDATED:', $pengajuan->toArray());

//         // HAPUS ITEMS LAMA
//         pengajuan_item::where('pengajuan_id', $pengajuan->id)->delete();

//         // CREATE ITEMS BARU
//         foreach ($request->items as $item) {
//             $totalItem = $item['jumlah'] * $item['harga_satuan'];

//             $pengajuanItem = pengajuan_item::create([
//                 'pengajuan_id' => $pengajuan->id,
//                 'nama_barang'  => $item['nama_barang'],
//                 'spesifikasi'  => $item['spesifikasi'] ?? null,
//                 'satuan'       => $item['satuan'] ?? 'Unit',
//                 'jumlah'       => $item['jumlah'],
//                 'harga_satuan' => $item['harga_satuan'],
//                 'harga'        => $totalItem,
//                 'jumlah_disetujui' => $item['jumlah'],
//             ]);

//             Log::info('✅ ITEM TERSIMPAN:', $pengajuanItem->toArray());

//             // CREATE BARANG TERSEDIA JIKA ADA
//             if (!empty($item['ada_barang_tersedia']) && !empty($item['barang_tersedia'])) {
//                 $bt = $item['barang_tersedia'];
                
//                 barang_tersedia::create([
//                     'pengajuan_item_id' => $pengajuanItem->id,
//                     'nama_barang'       => $bt['nama_barang'] ?? $item['nama_barang'],
//                     'jumlah'            => $bt['jumlah'] ?? 0,
//                     'tahun_perolehan'   => $bt['tahun_perolehan'] ?? null,
//                     'kondisi'           => $bt['kondisi'] ?? 'Baik',
//                     'keterangan'        => $bt['keterangan'] ?? null,
//                 ]);
                
//                 Log::info('✅ BARANG TERSEDIA TERSIMPAN');
//             }
//         }

//         DB::commit();

//         $message = $pengajuan->status == 'revisi' 
//             ? '✅ Pengajuan berhasil direvisi dan dikembalikan ke draft!' 
//             : '✅ Pengajuan berhasil diperbarui!';

//         return redirect()
//             ->route('pemohon.pengadaan')
//             ->with('success', $message);

//     } catch (\Exception $e) {
//         DB::rollBack();
        
//         Log::error('❌ ERROR UPDATE PENGAJUAN:', [
//             'message' => $e->getMessage(),
//             'file' => $e->getFile(),
//             'line' => $e->getLine(),
//             'trace' => $e->getTraceAsString()
//         ]);

//         return back()
//             ->withInput()
//             ->with('error', '❌ Gagal memperbarui pengajuan: ' . $e->getMessage());
//     }
// }
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