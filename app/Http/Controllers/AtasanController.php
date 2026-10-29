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
use App\Models\Bidang;
use App\Models\Pengajuan;
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


class AtasanController extends Controller
{
    public function index()
    {
        $authUser = Auth::user();
        $prefix = $authUser->multirolePrefix();

        if (! $prefix) {
            abort(403, 'Anda tidak punya role tambahan.');
        }

        return view('dualrole.dashboard', [
            'authuser' => $authUser, // <--- Ubah di sini jadi huruf kecil
            'prefix'   => $prefix,
        ]);
    }

    public function Showpermintaann(Request $request)
{
    $authUser = Auth::user();
    $bidangId = Auth::user()->karyawan->bidang_id ?? null;

    // Ambil query pencarian dari request
    $search = $request->input('search', '');

    // ?? Logic penggabungan bidang
    if ($bidangId == 2) {
        $bidangFilter = [2, 4]; // komputer + lainnya
    } else {
        $bidangFilter = [$bidangId];
    }

    // ================= SEMUA DATA (MODAL) =================
    $requests = RequestModel::with('detailBarang')
        ->whereIn('status', ['verified', 'approved', 'rejected'])
        ->whereIn('bidang_id', $bidangFilter) // ?? tambahkan ini
        ->where(function ($query) use ($search) {
            $query->where('request_type', 'like', '%' . $search . '%')
                ->orWhereHas('detailBarang', function ($q) use ($search) {
                    $q->where('nama_barang', 'like', '%' . $search . '%')
                      ->orWhere('deskripsi', 'like', '%' . $search . '%');
                });
        })
        ->orderBy('created_at', 'desc')
        ->get();

    // ================= PERMINTAAN BARANG =================
    $permintaanRequests = RequestModel::with('detailBarang')
        ->where('request_type', 'permintaan')
        ->whereIn('bidang_id', $bidangFilter) // ?? ganti ini
        ->whereIn('status', ['verified', 'approved', 'rejected'])
        ->where(function ($query) use ($search) {
            $query->whereHas('detailBarang', function ($q) use ($search) {
                $q->where('nama_barang', 'like', '%' . $search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $search . '%');
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    // ================= PERBAIKAN BARANG =================
    $perbaikanRequests = RequestModel::with('detailBarang')
        ->where('request_type', 'perbaikan')
        ->whereIn('bidang_id', $bidangFilter) // ?? ganti ini juga
        ->whereIn('status', ['verified', 'approved', 'rejected'])
        ->where(function ($query) use ($search) {
            $query->whereHas('detailBarang', function ($q) use ($search) {
                $q->where('nama_barang', 'like', '%' . $search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $search . '%');
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('patasan.permintaan', [
        'authUser'           => $authUser,
        'modalreq'           => $requests,
        'search'             => $search,
        'permintaanRequests' => $permintaanRequests,
        'perbaikanRequests'  => $perbaikanRequests,
    ]);
}

public function edit($id)
{
    $authUser = Auth::user();
    // Ambil permintaan berdasarkan ID
    $permintaan = RequestModel::findOrFail($id);
    
    // Kirim data ke halaman edit
    return view('patasan.permintaanedit', [
        'authUser' => $authUser,
        'permintaan' => $permintaan,
    ]);
}

public function lihatGambar($id)
{
    $req = RequestModel::with('detailBarang')->findOrFail($id);

    return view('pemohon.detail_gambar', compact('req'));
}


    public function view($id)
    {
        $permintaan = RequestModel::with(['detailBarang', 'user'])->findOrFail($id);

        return view('form.formpermintaan', compact('permintaan'));
    }

public function update(Request $request, $id)
{
    $authUser = Auth::user();

    // ================= VALIDASI UMUM =================
    $validated = $request->validate([
        'request_type' => 'required|in:permintaan,perbaikan',
        'status'       => 'required|in:pending,verified,approved,rejected',

        // PERMINTAAN BARANG
        'nama_barang'  => 'required|string|max:255',
        'jumlah'       => 'nullable|integer',
        'spesifikasi'  => 'nullable|string',
        'alasan'       => 'nullable|string',

        // PERBAIKAN BARANG
        'kode_aset'        => 'nullable|string|max:255',
        'deskripsi'        => 'nullable|string',
        'tanggal_kerusakan'=> 'nullable|date',
    ]);

    $permintaan = RequestModel::with('detailBarang')->findOrFail($id);

    // ================= UPDATE STATUS =================
    $permintaan->status = $request->status;

    // ===== Jika Approved / Rejected =====
    if (in_array($request->status, ['approved', 'rejected'])) {

        if ($permintaan->atasan_id === null) {
            $permintaan->atasan_id = $authUser->karyawan->id;
        }

    }

    // ===== Jika Status Kembali ke Pending =====
    if ($request->status === 'verified') {
        $permintaan->atasan_id = null;
    }

    $permintaan->save();

    // ================= SIAPKAN DATA DETAIL =================
    $dataUpdate = [
        'nama_barang' => $request->nama_barang,
    ];

    // ===== LOGIC TANGGAL ACC =====
    if (in_array($request->status, ['approved', 'rejected'])) {
        if ($permintaan->detailBarang->tanggal_acc === null) {
            $dataUpdate['tanggal_acc'] = now();
        }
    }

    if ($request->status === 'verified') {
        $dataUpdate['tanggal_acc'] = null;
    }

    // ================= LOGIC BERDASARKAN TYPE =================
    if ($permintaan->request_type === 'permintaan') {

        $dataUpdate += [
            'jumlah'      => $request->jumlah,
            'spesifikasi' => $request->spesifikasi,
            'alasan'      => $request->alasan,

            'kode_aset'         => null,
            'deskripsi'         => null,
            'tanggal_kerusakan' => null,
        ];
    }

    if ($permintaan->request_type === 'perbaikan') {

        $dataUpdate += [
            'kode_aset'         => $request->kode_aset,
            'deskripsi'         => $request->deskripsi,
            'tanggal_kerusakan' => $request->tanggal_kerusakan,

            'jumlah'      => null,
            'spesifikasi' => null,
            'alasan'      => null,
        ];
    }

    // ===== UPDATE DETAIL =====
    $permintaan->detailBarang->update($dataUpdate);

    return redirect()
        ->route('atasan.permintaan')
        ->with('success', 'Permintaan berhasil diupdate');
}

// pengadaan

//  public function pengadaanshow(Request $request)
// {
//     $authUser = Auth::user();
//     $bidangId = $authUser->karyawan->bidang_id ?? null;
//     $ruangan = $authUser->karyawan->ruangan ?? null;
//     $bidangs = Bidang::all();

//     $search        = $request->input('search');
//     $statusFilter  = $request->input('status');
//     $bidangFilter  = $request->input('bidang');
//     $typeFilter    = $request->input('type', 'pengadaan'); // Default ke Pengadaan
//     $tahunAnggaran = $request->input('tahun_anggaran');
//     $bulanFilter   = $request->input('bulan');
//     $tahunpengajuan   = $request->input('tahun_pengajuan');

//     // ============================
//     // QUERY DASAR
//     // ============================
//     $baseQuery = Pengajuan::with(['items', 'karyawan', 'bidang'])
//         ->whereNotIn('status', ['draft', 'revisi', 'diajukan', 'perlu survei perbaikan'])
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
//         ->when($statusFilter, fn($q) => $q->where('status', $statusFilter))
//         ->when($bidangFilter, fn($q) => $q->where('bidang_id', $bidangFilter))
//         ->when($tahunAnggaran, fn($q) => $q->where('tahun_anggaran', $tahunAnggaran))
//         ->when($bulanFilter, fn($q) => $q->whereMonth('tanggal_pengajuan', $bulanFilter))
//         ->when($tahunpengajuan, fn($q) => $q->whereMonth('tanggal_pengajuan', $bulanFilter));

//     // ============================
//     // FILTER KATEGORI
//     // ============================
//     $filterPengadaan = function ($q) {
//         $q->where('kategori', 'pengadaan');
//     };

//     $filterPemeliharaan = function ($q) {
//         $q->where('kategori', 'pemeliharaan');
//     };

//     // ============================
//     // DATA PER TAB
//     // ============================
//     $pengadaanPengajuan = (clone $baseQuery)
//         ->where($filterPengadaan)
//         ->orderBy('created_at', 'desc')
//         ->paginate(10, ['*'], 'pengadaan_page')
//         ->appends($request->all());

//     $pemeliharaanPengajuan = (clone $baseQuery)
//         ->where($filterPemeliharaan)
//         ->orderBy('created_at', 'desc')
//         ->paginate(10, ['*'], 'pemeliharaan_page')
//         ->appends($request->all());

//     // Data tab aktif
//     $allPengajuan = ($typeFilter === 'pemeliharaan') ? $pemeliharaanPengajuan : $pengadaanPengajuan;

//     // ============================
//     // STATISTIK DASHBOARD
//     // ============================
//     $statsQuery = Pengajuan::query(); 

//     $stats = [
//         'total'                 => (clone $statsQuery)->count(),
//         // 'disetujui_koordinator' => (clone $statsQuery)->where('status', 'disetujui_koordinator')->count(),
//         'disetujui_koordinator' => (clone $statsQuery)
//         ->where('status', 'menunggu_koordinator')
//         ->where('acc_kabidPelayanan_at', 'disetujui oleh kabid pelayanan')
//         ->count(),
//         'diajukan'              => (clone $statsQuery)->where('status', 'diajukan')->count(),
//         'disetujui'             => (clone $statsQuery)->where('log_status_direktur', 'disetujui')->count(),
//         'ditolak'               => (clone $statsQuery)->where('status', 'ditolak')->count(),
//         'revisi'                => (clone $statsQuery)->where('status', 'revisi')->count(),
//         'selesai'     => (clone $statsQuery)->where('status', 'selesai')->count(),

//         // Hitungan Badge
//         'pengadaan_count'       => (clone $statsQuery)->where('kategori', 'pengadaan')->count(),
//         'pemeliharaan_count'    => (clone $statsQuery)->where('kategori', 'pemeliharaan')->count(),
//     ];

//     return view('patasan.pengadaan.permintaan', compact(
//         'authUser', 'allPengajuan', 'pengadaanPengajuan', 'pemeliharaanPengajuan',
//         'bidangs', 'search', 'statusFilter', 'bidangFilter', 'typeFilter',
//         'stats', 'tahunAnggaran', 'bidangId', 'ruangan','tahunpengajuan'
//     ));
// }

public function pengadaanshow(Request $request)
{
    $authUser = Auth::user();
    $bidangId = $authUser->karyawan->bidang_id ?? null;
    $ruangan = $authUser->karyawan->ruangan ?? null;
    $bidangs = Bidang::all();

    $search        = $request->input('search');
    $statusFilter  = $request->input('status');
    $bidangFilter  = $request->input('bidang');
    $typeFilter    = $request->input('type', 'pengadaan'); // Default ke Pengadaan
    $tahunFilter   = $request->input('tahun'); // Parameter di URL sekarang 'tahun'
    $bulanFilter   = $request->input('bulan');

    // ============================
    // AMBIL LIST TAHUN DINAMIS DARI TANGGAL_PENGAJUAN
    // ============================
    $listTahun = Pengajuan::selectRaw('YEAR(tanggal_pengajuan) as tahun')
        ->whereNotNull('tanggal_pengajuan')
        ->distinct()
        ->orderBy('tahun', 'desc')
        ->pluck('tahun');

    // ============================
    // QUERY DASAR (Untuk Tabel & Statistik)
    // ============================
    $baseQuery = Pengajuan::with(['items', 'karyawan', 'bidang'])
        ->whereNotIn('status', ['draft', 'revisi', 'diajukan', 'perlu survei perbaikan'])
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
        ->when($statusFilter, fn($q) => $q->where('status', $statusFilter))
        ->when($bidangFilter, fn($q) => $q->where('bidang_id', $bidangFilter))
        
        // ✅ Filter berdasarkan TAHUN dari tanggal_pengajuan
        ->when($tahunFilter, fn($q) => $q->whereYear('tanggal_pengajuan', $tahunFilter))
        
        ->when($bulanFilter, fn($q) => $q->whereMonth('tanggal_pengajuan', $bulanFilter));

    // ============================
    // DATA PER TAB
    // ============================
    $pengadaanPengajuan = (clone $baseQuery)
        ->where('kategori', 'pengadaan')
        ->orderBy('created_at', 'desc')
        ->paginate(10, ['*'], 'pengadaan_page')
        ->appends($request->all());

    $pemeliharaanPengajuan = (clone $baseQuery)
        ->where('kategori', 'pemeliharaan')
        ->orderBy('created_at', 'desc')
        ->paginate(10, ['*'], 'pemeliharaan_page')
        ->appends($request->all());

    // Data tab aktif
    $allPengajuan = ($typeFilter === 'pemeliharaan') ? $pemeliharaanPengajuan : $pengadaanPengajuan;

    // ============================
    // STATISTIK DASHBOARD
    // ============================
    // Statistik mengambil dari $baseQuery agar sinkron dengan filter Tahun/Bulan/Bidang
    $statsQuery = $baseQuery; 

    $stats = [
        'total'                 => (clone $statsQuery)->count(),
        
        // Menghitung data yang statusnya menunggu_koordinator DAN sudah di-ACC kabid pelayanan
        'disetujui_koordinator' => (clone $statsQuery)
            ->where('status', 'menunggu_koordinator')
            ->where('acc_kabidPelayanan_at', 'disetujui oleh kabid pelayanan')
            ->count(),
        'diajukan'              => (clone $statsQuery)->where('status', 'diajukan')->count(),
  
        'proses'                => (clone $statsQuery)->whereNotIn('status', ['selesai', 'draft', 'ditolak'])->count(),
        'disetujui'             => (clone $statsQuery)->where('log_status_direktur', 'disetujui')->count(),
        'ditolak'               => (clone $statsQuery)->where('status', 'ditolak')->count(),
        'revisi'                => (clone $statsQuery)->where('status', 'revisi')->count(),
        'selesai'               => (clone $statsQuery)->where('status', 'selesai')->count(),

        // Hitungan Badge Tab (Sinkron dengan filter)
        'pengadaan_count'       => (clone $statsQuery)->where('kategori', 'pengadaan')->count(),
        'pemeliharaan_count'    => (clone $statsQuery)->where('kategori', 'pemeliharaan')->count(),
    ];

    return view('patasan.pengadaan.permintaan', compact(
        'authUser', 'allPengajuan', 'pengadaanPengajuan', 'pemeliharaanPengajuan',
        'bidangs', 'search', 'statusFilter', 'bidangFilter', 'typeFilter',
        'stats', 'listTahun', 'tahunFilter', 'bulanFilter', 'bidangId', 'ruangan'
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

        return view('profile.karyawan.editatasan', compact('authUser', 'daftarRuangan', 'daftarJabatan'));
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
        ->route('atasan.pengadaan')
        ->with('success', 'Profile dan data Karyawan berhasil diperbarui.');
}


     public function showpengadaan($id)
{
    $authUser = Auth::user();
    
    $pengajuan = Pengajuan::with([
        'karyawan',
        'bidang',
        'items',
        'items.barangTersedia',
    ])->findOrFail($id); // <-- HAPUS where('penerima_id', $authUser->id)

    return view('patasan.pengadaan.show', compact('pengajuan', 'authUser'));
}


public function verifikasi(Request $request, $id)
{
    $request->validate([
        'status_verifikasi'  => 'required|in:disetujui_kabid,ditolak,revisi,survei',
        'catatan_verifikasi' => 'required|string|min:5',
        'atasan_id'          => 'required',
    ]);

    try {
        $pengajuan = Pengajuan::findOrFail($id);
        $authUser  = Auth::user();

        // sesuaikan nama field & value bidangnya
        $isAlatKesehatan = $pengajuan->bidang->nama_bidang === 'alat kesehatan';

        // Mapping status untuk konsistensi
        $statusMap = [
            'disetujui_kabid' => 'disetujui_kabid',
            'ditolak'         => 'ditolak_bidang',
            'revisi'          => 'revisi_bidang',
            'survei'          => 'sesuai standard',
        ];

        $newStatus = $statusMap[$request->status_verifikasi];

        $data = [
            'status'            => $newStatus,
            'atasan_id'         => $request->atasan_id,
            'catatan_bidang'    => $request->catatan_verifikasi,
            'log_status_atasan' => $newStatus,
        ];

        if ($isAlatKesehatan) {
            // --- Khusus alat kesehatan: pakai subrole ---
            if ($authUser->subrole === 'kabid_pelayanan') {
                $data['id_acc_kabidPelayanan']  = $request->atasan_id;
                $data['acc_kabidPelayanan_at']  = now();
                $data['log_acc_kabidPelayanan'] = 'disetujui oleh kabid pelayanan';

            } elseif ($authUser->subrole === 'kabid_penunjang') {
                $data['id_acc_kabidPenunjang']  = $request->atasan_id;
                $data['acc_kabidPenunjang_at']  = now();
                $data['log_acc_kabidPenunjang'] = 'disetujui oleh kabid penunjang';

            } else {
                // Subrole lain / tidak dikenali: tidak mengisi kolom ACC khusus
            }
        } else {
            // --- Selain alat kesehatan: tetap isi log_acc_kabidPenunjang ---
            // tanpa peduli subrole-nya apa (kabid tanpa subrole tetap bisa)
            $data['id_acc_kabidPenunjang']  = $request->atasan_id;
            $data['acc_kabidPenunjang_at']  = now();
            $data['log_acc_kabidPenunjang'] = 'disetujui oleh kabid penunjang';
        }

        // Hanya set tanggal jika statusnya disetujui
        if (in_array($request->status_verifikasi, ['disetujui_kabid', 'survei'])) {
            $data['disetujui_kabid_at'] = now();
        }

        $pengajuan->update($data);

        return redirect()
            ->route('atasan.pengadaan')
            ->with('success', '✅ Pengajuan berhasil diverifikasi!');

    } catch (\Exception $e) {
        return back()
            ->withInput()
            ->with('error', '❌ Gagal verifikasi: ' . $e->getMessage());
    }
}

// public function verifikasi(Request $request, $id)
// {
//     $request->validate([
//         'status_verifikasi'  => 'required|in:disetujui_kabid,ditolak,revisi,survei',
//         'catatan_verifikasi' => 'required|string|min:5',
//         'atasan_id'          => 'required',
//     ]);

//     try {
//         $pengajuan = Pengajuan::findOrFail($id);
//         $authUser  = Auth::user();

//         // Mapping status untuk konsistensi
//         $statusMap = [
//             'disetujui_kabid' => 'disetujui_kabid',
//             'ditolak'         => 'ditolak_bidang',
//             'revisi'          => 'revisi_bidang',
//             'survei'          => 'sesuai standard',
//         ];

//         $newStatus = $statusMap[$request->status_verifikasi];

//         $data = [
//             'status'            => $newStatus,
//             'atasan_id'         => $request->atasan_id,
//             'catatan_bidang'    => $request->catatan_verifikasi,
//             'log_status_atasan' => $newStatus,
//         ];

//         // Isi kolom sesuai subrole yang login
//         if ($authUser->subrole === 'kabid_pelayanan') {
//             $data['id_acc_kabidPelayanan']  = $request->atasan_id;
//             $data['acc_kabidPelayanan_at']  = now();
//             $data['log_acc_kabidPelayanan'] = 'disetujui oleh kabid pelayanan';

//         } elseif ($authUser->subrole === 'kabid_penunjang') {
//             $data['id_acc_kabidPenunjang']  = $request->atasan_id;
//             $data['acc_kabidPenunjang_at']  = now();
//             $data['log_acc_kabidPenunjang'] = 'disetujui oleh kabid penunjang';

//         } else {
//             // Subrole lain / tidak dikenali: tidak mengisi kolom ACC khusus
//         }

//         // Hanya set tanggal jika statusnya disetujui
//         if (in_array($request->status_verifikasi, ['disetujui_kabid', 'survei'])) {
//             $data['disetujui_kabid_at'] = now();
//         }

//         $pengajuan->update($data);

//         return redirect()
//             ->route('atasan.pengadaan')
//             ->with('success', '✅ Pengajuan berhasil diverifikasi!');

//     } catch (\Exception $e) {
//         return back()
//             ->withInput()
//             ->with('error', '❌ Gagal verifikasi: ' . $e->getMessage());
//     }
// }

// public function verifikasi(Request $request, $id)
// {
    
//     $request->validate([
//         'status_verifikasi' => 'required|in:disetujui_kabid,ditolak,revisi,survei',
//         'catatan_verifikasi' => 'required|string|min:5',
//         'atasan_id' => 'required',
//     ]);

//     try {
//         $pengajuan = pengajuan::findOrFail($id);
//          $authUser = Auth::user();
        
//         // Mapping status untuk konsistensi
//         $statusMap = [
//             'disetujui_kabid' => 'disetujui_kabid',
//             'ditolak' => 'ditolak_bidang',
//             'revisi' => 'revisi_bidang',
//             'survei' => 'sesuai standard',
//         ];
        
//         // Mapping untuk log (CUKUP STATUS SAJA)
//         $logMap = [
//             'disetujui_kabid' => 'disetujui_bidang',
//             'ditolak' => 'ditolak_bidang',
//             'revisi' => 'revisi_bidang',
//             'survei' => 'sesuai standard'
//             ];
        
//         $newStatus = $statusMap[$request->status_verifikasi];
        
//         $data = [
//             'status' => $newStatus,
//             'atasan_id' => $request->atasan_id,
//             'catatan_bidang' => $request->catatan_verifikasi,
//             'log_status_atasan' => $newStatus, // ✅ CUKUP STATUS\
//         ];

//          //  Jika yang login adalah kabid pelayanan, simpan id user-nya
//         if ($authUser->subrole === 'kabid_pelayanan') {
//             $data['id_acc_kabidPelayanan'] = $request->atasan_id;
//             $data['acc_kabidPelayanan_at'] = now();
//             $data['log_acc_kabidPelayanan'] = 'disetujui oleh kabid pelayanan';
//         }

//         // Hanya set tanggal jika statusnya disetujui
//         if (in_array($request->status_verifikasi, ['disetujui_kabid','survei'])) {
//             $data['disetujui_kabid_at'] = now();
//         }
//         // if ($request->status_verifikasi == 'disetujui_kabid') {
//         //     $data['disetujui_kabid_at'] = now();
//         // }

//         $pengajuan->update($data);

//         return redirect()
//             ->route('atasan.pengadaan')
//             ->with('success', '✅ Pengajuan berhasil diverifikasi!');

//     } catch (\Exception $e) {
//         return back()
//             ->withInput()
//             ->with('error', '❌ Gagal verifikasi: ' . $e->getMessage());
//     }
// }


   public function chartIndex(Request $request)
    {
        $authUser = Auth::user();
        $bidangId = $authUser->karyawan->bidang_id ?? null;
        
        // Ambil data untuk filter tahun
        $tahunList = Pengajuan::where('bidang_id', $bidangId)
            ->select('tahun_anggaran')
            ->distinct()
            ->orderBy('tahun_anggaran', 'desc')
            ->pluck('tahun_anggaran')
            ->toArray();

        // Ambil data bidang untuk filter
        $bidangs = Bidang::all();

        // Statistik ringkasan
        $stats = [
            'total_pengajuan' => Pengajuan::where('bidang_id', $bidangId)->count(),
            'total_disetujui' => Pengajuan::where('bidang_id', $bidangId)
                ->where('status', 'disetujui')->count(),
            'total_ditolak' => Pengajuan::where('bidang_id', $bidangId)
                ->where('status', 'ditolak')->count(),
            'total_menunggu' => Pengajuan::where('bidang_id', $bidangId)
                ->whereIn('status', ['menunggu_direktur', 'disetujui_koordinator', 'disetujui_kabid'])
                ->count(),
            'total_nominal' => Pengajuan::where('bidang_id', $bidangId)
                ->where('status', 'disetujui')
                ->sum('total_pengajuan'),
            'total_disetujui' => Pengajuan::where('bidang_id', $bidangId)
                ->where('status', 'disetujui')
                ->sum('total_disetujui_direktur'),
            'total_draft' => Pengajuan::where('bidang_id', $bidangId)
                ->where('status', 'draft')->count(),
            'total_diajukan' => Pengajuan::where('bidang_id', $bidangId)
                ->where('status', 'diajukan')->count(),
        ];

        return view('patasan.pengadaan.chart', compact(
            'authUser',
            'stats',
            'tahunList',
            'bidangs',
            'bidangId'
        ));
    }

 
    public function getChartData(Request $request)
    {
        try {
            $authUser = Auth::user();
            $bidangId = $authUser->karyawan->bidang_id ?? null;
            
            $tahun = $request->input('tahun');
            $bidangFilter = $request->input('bidang');
            $periode = $request->input('periode', 'tahunan');

            // Base query
            $query = Pengajuan::query()
                ->when($bidangId, function($q) use ($bidangId) {
                    $q->where('bidang_id', $bidangId);
                })
                ->when($tahun, function($q) use ($tahun) {
                    $q->where('tahun_anggaran', $tahun);
                })
                ->when($bidangFilter, function($q) use ($bidangFilter) {
                    $q->where('bidang_id', $bidangFilter);
                });

            // 1. Data Status Pengajuan (Pie Chart)
            $statusData = (clone $query)
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get()
                ->map(function($item) {
                    $statusLabels = [
                        'draft' => 'Draft',
                        'diajukan' => 'Diajukan',
                        'disetujui_koordinator' => 'Disetujui Koordinator',
                        'disetujui_kabid' => 'Disetujui Kabid',
                        'menunggu_direktur' => 'Menunggu Direktur',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                        'revisi' => 'Revisi'
                    ];
                    
                    $colors = [
                        'draft' => '#6c757d',
                        'diajukan' => '#0dcaf0',
                        'disetujui_koordinator' => '#0d6efd',
                        'disetujui_kabid' => '#6610f2',
                        'menunggu_direktur' => '#ffc107',
                        'disetujui' => '#198754',
                        'ditolak' => '#dc3545',
                        'revisi' => '#fd7e14'
                    ];
                    
                    return [
                        'label' => $statusLabels[$item->status] ?? $item->status,
                        'value' => $item->total,
                        'color' => $colors[$item->status] ?? '#6c757d'
                    ];
                });

            // 2. Data Trend Pengajuan (Line/Bar Chart)
            if ($periode == 'tahunan') {
                $trendData = (clone $query)
                    ->select(
                        DB::raw('YEAR(created_at) as year'),
                        DB::raw('count(*) as total'),
                        DB::raw('SUM(total_pengajuan) as nominal')
                    )
                    ->groupBy('year')
                    ->orderBy('year')
                    ->get()
                    ->map(function($item) {
                        $item->label = (string) $item->year;
                        return $item;
                    });
            } elseif ($periode == 'triwulan') {
                $trendData = (clone $query)
                    ->select(
                        DB::raw('YEAR(created_at) as year'),
                        DB::raw('QUARTER(created_at) as quarter'),
                        DB::raw('count(*) as total'),
                        DB::raw('SUM(total_pengajuan) as nominal')
                    )
                    ->groupBy('year', 'quarter')
                    ->orderBy('year')
                    ->orderBy('quarter')
                    ->get()
                    ->map(function($item) {
                        $item->label = "Q{$item->quarter} {$item->year}";
                        return $item;
                    });
            } else { // bulanan
                $trendData = (clone $query)
                    ->select(
                        DB::raw('YEAR(created_at) as year'),
                        DB::raw('MONTH(created_at) as month'),
                        DB::raw('count(*) as total'),
                        DB::raw('SUM(total_pengajuan) as nominal')
                    )
                    ->groupBy('year', 'month')
                    ->orderBy('year')
                    ->orderBy('month')
                    ->get()
                    ->map(function($item) {
                        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 
                                       'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                        $item->label = $monthNames[$item->month - 1] . ' ' . $item->year;
                        return $item;
                    });
            }

            // 3. Data Per Bidang (Bar Chart)
            $bidangData = (clone $query)
                ->select('bidang_id', DB::raw('count(*) as total'), DB::raw('SUM(total_pengajuan) as nominal'))
                ->with('bidang')
                ->groupBy('bidang_id')
                ->get()
                ->map(function($item) {
                    return [
                        'label' => $item->bidang->nama_bidang ?? 'Unknown',
                        'total' => $item->total,
                        'nominal' => $item->nominal ?? 0
                    ];
                });

            // 4. Data Rata-rata Pengajuan (per status)
            $averageData = (clone $query)
                ->select('status', DB::raw('AVG(total_pengajuan) as rata_rata'))
                ->groupBy('status')
                ->get()
                ->map(function($item) {
                    $statusLabels = [
                        'draft' => 'Draft',
                        'diajukan' => 'Diajukan',
                        'disetujui_koordinator' => 'Disetujui Koordinator',
                        'disetujui_kabid' => 'Disetujui Kabid',
                        'menunggu_direktur' => 'Menunggu Direktur',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                        'revisi' => 'Revisi'
                    ];
                    return [
                        'label' => $statusLabels[$item->status] ?? $item->status,
                        'value' => round($item->rata_rata, 0)
                    ];
                });

            return response()->json([
                'success' => true,
                'status' => $statusData,
                'trend' => $trendData,
                'bidang' => $bidangData,
                'average' => $averageData,
                'periode' => $periode
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading chart data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data chart: ' . $e->getMessage()
            ], 500);
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
        $atasans = Karyawan::whereIn('id', function($query) {
            $query->select('atasan_id')
                  ->from('pengajuans')
                  ->whereNotNull('atasan_id');
        })->get();

        // AMBIL TAHUN
        $tahuns = Pengajuan::selectRaw('DISTINCT YEAR(tanggal_pengajuan) as tahun')
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
        $query = Pengajuan::with([
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

        return view('patasan.pengadaan.laporan', compact(
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
        $query = Pengajuan::with([
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

    /**
 * Tampilkan halaman edit semua item
 */
public function editItem($id)
{
    $pengajuan = Pengajuan::with('items')->findOrFail($id);

    return view('patasan.pengadaan.edit_item', compact('pengajuan'));
}

/**
 * Update semua item sekaligus
 */
public function updateItem(Request $request, $id)
{
    $pengajuan = Pengajuan::findOrFail($id);

    $request->validate([
        'items'                => 'required|array|min:1',
        'items.*.id'           => 'nullable|integer|exists:pengajuan_items,id',
        'items.*.nama_barang'  => 'required|string',
        'items.*.jumlah'       => 'required|integer|min:1',
        'items.*.harga_satuan' => 'required|numeric|min:0',
        'items.*.spesifikasi'  => 'nullable|string',
        'items.*.satuan'       => 'nullable|string',
    ]);

    DB::beginTransaction();
    try {
        $totalPengajuan = 0;
        $idsFromRequest = [];

        foreach ($request->items as $itemData) {
            $totalItem = $itemData['jumlah'] * $itemData['harga_satuan'];
            $totalPengajuan += $totalItem;

            if (!empty($itemData['id'])) {
                // Update item lama
                $item = Pengajuan_item::where('pengajuan_id', $pengajuan->id)
                    ->findOrFail($itemData['id']);

                $item->update([
                    'nama_barang'  => $itemData['nama_barang'],
                    'spesifikasi'  => $itemData['spesifikasi'] ?? null,
                    'satuan'       => $itemData['satuan'] ?? 'Unit',
                    'jumlah'       => $itemData['jumlah'],
                    'harga_satuan' => $itemData['harga_satuan'],
                    'harga'        => $totalItem,
                ]);
                $idsFromRequest[] = $item->id;
            } else {
                // Create item baru
                $newItem = Pengajuan_item::create([
                    'pengajuan_id'     => $pengajuan->id,
                    'nama_barang'      => $itemData['nama_barang'],
                    'spesifikasi'      => $itemData['spesifikasi'] ?? null,
                    'satuan'           => $itemData['satuan'] ?? 'Unit',
                    'jumlah'           => $itemData['jumlah'],
                    'harga_satuan'     => $itemData['harga_satuan'],
                    'harga'            => $totalItem,
                    'jumlah_disetujui' => $itemData['jumlah'],
                ]);
                $idsFromRequest[] = $newItem->id;
            }
        }

        // Hapus item yang tidak ada di request (user hapus)
        Pengajuan_item::where('pengajuan_id', $pengajuan->id)
            ->whereNotIn('id', $idsFromRequest)
            ->delete();

        // Update total pengajuan
        $pengajuan->update(['total_pengajuan' => $totalPengajuan]);

        DB::commit();

        return redirect()
            ->route('atasan.pengadaan.show', $pengajuan->id)
            ->with('success', '✅ Semua item berhasil diperbarui!');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('❌ ERROR UPDATE ITEM:', [
            'message' => $e->getMessage(),
            'line'    => $e->getLine(),
        ]);

        return back()
            ->withInput()
            ->with('error', '❌ Gagal: ' . $e->getMessage());
    }
}

}
