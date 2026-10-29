<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bidang;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class AdminController extends Controller
{
    public function index()
    {
        $authUser = Auth::user();

    return view('admin.dashboard', [
        'authUser'     => $authUser,
    ]);
    }

    // public function karyawan()
    // {
    //       $user = Auth::user();       // siapa yang login
    //         $karyawan = Karyawan::orderBy('nama', 'asc')->paginate(20);

    //    return view('admin.karyawan', [
    //         'user' => $user,
    //         'karyawan' => $karyawan,
    //     ]);
    // }
    
//     public function karyawan(Request $request)
// {
//     $authUser = Auth::user();
//     $bidangs = bidang::all();

//     $karyawan = Karyawan::when($request->search, function ($query, $search) {
//         $query->where(function ($q) use ($search) {
//             $q->where('nama', 'like', "%{$search}%")
//               ->orWhere('nip', 'like', "%{$search}%")
//               ->orWhere('jabatan', 'like', "%{$search}%")
//               ->orWhere('ruangan', 'like', "%{$search}%");
//         });
//     })
//     ->orderBy('nama', 'asc')
//     ->paginate(10)
//     ->withQueryString(); // 🔥 biar search ikut pagination

//     return view('admin.karyawan', [
//         'authUser'     => $authUser,
//         'karyawan' => $karyawan,
//         'bidangs' => $bidangs,
//     ]);
// }

public function karyawan(Request $request)
{
    $authUser = Auth::user();
    $bidangs = Bidang::all();

    // Daftar ruangan (biar konsisten, dipakai filter & form)
    $daftarRuanganFilter = [
        'CSSD/LAUNDRY','CODE BLUE','CSSD','DIREKTUR','UNIT HEMODIALISIS',
        'PENUNJANG','RUANG FLAMBOYAN','IGD','IPAL','INSTALASI HEMODIALISA',
        'INSTALASI BEDAH SENTRAL (IBS)','INSTALASI PEMELIHARAAN SARANA',
        'LABORATORIUM','RADIOLOGI','RUANG BOUGENVILE','RUANG SAKURA',
        'RUANG CEMPAKA','RUANG MATAHARI','RUANG MAWAR','RUANG MELATI',
        'RUANG ANGGREK','RUANG KENANGA','RUANG DAHLIA','RUANG TULIP',
        'Recovery Room (RR)','BRONKOSKOPI RUANGAN','RUANG GRAHA UTAMA',
        'RUANG TERATAI','RUANG TRANSIT','PPI LAYANAN','POLI RAWAT JALAN',
        'KEUANGAN','KESLING','MCU','SIMRS','KOMITE MEDIS','LAUNDRY',
        'GIZI','FARMASI','FLAMBOYAN','MANAJEMEN','TATA USAHA','RUANG WADIR','CASEMIX','REKAM MEDIS',
    ];

    $karyawan = Karyawan::query()
        ->when($request->filled('search'), function ($query) use ($request) {
            $search = $request->search;
            return $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%")
                  ->orWhere('ruangan', 'like', "%{$search}%");
            });
        })
        ->when($request->filled('ruangan'), function ($query) use ($request) {
            return $query->where('ruangan', $request->ruangan);
        })
        ->orderBy('nama', 'asc')
        ->paginate(10)
        ->withQueryString();

    return view('admin.karyawan', compact('authUser', 'karyawan', 'bidangs', 'daftarRuanganFilter'));
}

// public function karyawan(Request $request)
// {
//     $authUser = Auth::user();
//     $bidangs = Bidang::all();

//     $karyawan = Karyawan::query()
//         ->when($request->filled('search'), function ($query) use ($request) {
//             $search = $request->search;
//             return $query->where(function ($q) use ($search) {
//                 $q->where('nama', 'like', "%{$search}%")
//                   ->orWhere('nip', 'like', "%{$search}%")
//                   ->orWhere('jabatan', 'like', "%{$search}%")
//                   ->orWhere('ruangan', 'like', "%{$search}%");
//             });
//         })
//         ->orderBy('nama', 'asc')
//         ->paginate(10)
//         ->withQueryString();

//     return view('admin.karyawan', compact('authUser', 'karyawan', 'bidangs'));
// }

//   public function store(Request $request)
// {
//     $request->validate([
//         'nama'     => 'required|string|max:255',
//         'nip'      => 'required|unique:karyawans,nip',
//         'jabatan'  => 'required',
//         'ruangan'  => 'required',
//         'ttd'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
//     ]);

//     $ttdPath = null;

//     // 🔽 upload tanda tangan jika ada
//     if ($request->hasFile('ttd')) {
//         // folder "ttd" akan otomatis dibuat jika belum ada
//         $ttdPath = $request->file('ttd')->store('ttd', 'public');
//     }

//     Karyawan::create([
//         'nama'     => $request->nama,
//         'nip'      => $request->nip,
//         'jabatan'  => $request->jabatan,
//         'ruangan'  => $request->ruangan,
//         'ttd'      => $ttdPath,
//     ]);

//     return redirect()
//         ->route('admin.karyawan')
//         ->with('success', 'Karyawan berhasil ditambahkan');
// }

public function store(Request $request)
{
    $request->validate([
        'nama'     => 'required|string|max:255',
        'nip'      => 'required|unique:karyawans,nip',
        'jabatan'  => 'required',
        'ruangan'  => 'required',
        'ttd'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        
    ]);

    $filename = null;

    // 🔽 upload tanda tangan jika ada (SAMA DENGAN UPDATE)
    if ($request->hasFile('ttd')) {
        $file = $request->file('ttd');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('ttd', $filename, 'public');
    }

    Karyawan::create([
        'nama'     => $request->nama,
        'nip'      => $request->nip,
        'jabatan'  => $request->jabatan,
        'ruangan'  => $request->ruangan,
        'ttd'      => $filename, // ⬅️ hanya nama file
    ]);

    return redirect()
        ->route('admin.karyawan')
        ->with('success', 'Karyawan berhasil ditambahkan');
}

public function update(Request $request, $id)
{
    $request->validate([
        'nama'    => 'required|string|max:255',
        'nip'     => 'required|unique:karyawans,nip,' . $id,
        'jabatan' => 'required',
        'ruangan' => 'required',
        'ttd'     => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
    ]);

    $karyawan = Karyawan::findOrFail($id);

    $data = [
        'nama'    => $request->nama,
        'nip'     => $request->nip,
        'jabatan' => $request->jabatan,
        'ruangan' => $request->ruangan,
    ];

    // 🔥 kalau upload TTD baru
    if ($request->hasFile('ttd')) {

        // hapus TTD lama
        if ($karyawan->ttd && Storage::disk('public')->exists('ttd/' . $karyawan->ttd)) {
            Storage::disk('public')->delete('ttd/' . $karyawan->ttd);
        }

        // simpan TTD baru
        $file = $request->file('ttd');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('ttd', $filename, 'public');

        $data['ttd'] = $filename;
    }

    $karyawan->update($data);

    return redirect()->route('admin.karyawan')
        ->with('success', 'Data karyawan berhasil diupdate');
}

    public function destroy($id)
{
    Karyawan::findOrFail($id)->delete();

    return redirect()
        ->route('admin.karyawan')
        ->with('success', 'Data karyawan berhasil dihapus');
}


public function akun(Request $request)
{
    $authUser = Auth::user();

    $userr = User::with('karyawan')
        ->when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%")
                  ->orWhereHas('karyawan', function ($k) use ($search) {
                      $k->where('nama', 'like', "%{$search}%")
                        ->orWhere('jabatan', 'like', "%{$search}%");
                  });
            });
        })
        ->orderBy('name', 'asc')
        ->paginate(10)
        ->withQueryString(); // 🔥 search ikut pagination

    // karyawan yang BELUM punya akun
    $karyawan = Karyawan::doesntHave('pengguna')
        ->orderBy('nama', 'asc')
        ->get();

        $bidang = bidang::all();

    return view('admin.akun', [
        'authUser'      => $authUser,
        'userr'     => $userr,
        'karyawans' => $karyawan,
        'bidangs' => $bidang,
    ]);
}

// public function storeuser(Request $request)
// {
//     $request->validate([
//         'karyawan_id' => 'required|exists:karyawans,id',
//         'email'       => 'required|email|unique:users,email',
//         'password'    => 'required|min:6',
//         'role'        => 'required',
//     ]);

//     $karyawan = Karyawan::findOrFail($request->karyawan_id);
    

//     if ($karyawan->user) {
//         return back()->with('error', 'Karyawan sudah punya akun');
//     }

//     User::create([
//         'karyawan_id' => $karyawan->id,
//         'name'        => $karyawan->nama,
//         'email'       => $request->email,
//         'password'    => Hash::make($request->password),
//         'role'        => $request->role,
//         'is_active'   => true,
//     ]);

//     return back()->with('success', 'Akun berhasil ditambahkan');
// }

// public function storeuser(Request $request)
// {
//     $request->validate([
//         'karyawan_id' => 'required|exists:karyawans,id',
//         'email'       => 'required|email|unique:users,email',
//         'password'    => 'required|min:6',
//         'role'        => 'required',
//         'bidang_id'   => 'nullable|exists:bidangs,id', // 🔥 tambahin ini
//     ]);

//     $karyawan = Karyawan::findOrFail($request->karyawan_id);

//     if ($karyawan->user) {
//         return back()->with('error', 'Karyawan sudah punya akun');
//     }

//     // 🔥 Update bidang ke tabel karyawans
//     if ($request->role === 'penerima' || $request->role === 'atasan') {
//         $karyawan->bidang_id = $request->bidang_id;
//     } else {
//         $karyawan->bidang_id = null;
//     }

//     $karyawan->save();

//     // 🔥 Buat user
//     User::create([
//         'karyawan_id' => $karyawan->id,
//         'name'        => $karyawan->nama,
//         'email'       => $request->email,
//         'password'    => Hash::make($request->password),
//         'role'        => $request->role,
//         'is_active'   => true,
//     ]);

//     return back()->with('success', 'Akun berhasil ditambahkan');
// }

public function storeuser(Request $request)
{
    $request->validate([
        'karyawan_id' => 'required|exists:karyawans,id',
        'email'       => 'required|email|unique:users,email',
        'password'    => 'required|min:6',
        'role'        => 'required',
        'role2'       => 'nullable',
        'sub_bagian'  => 'nullable',
        'bidang_id'   => 'nullable|exists:bidangs,id',
    ]);

    $karyawan = Karyawan::findOrFail($request->karyawan_id);

    if ($karyawan->user) {
        return back()->with('error', 'Karyawan sudah punya akun');
    }

    // 🔥 Cek apakah salah satu role butuh bidang
    $butuhBidang = in_array($request->role, ['penerima', 'atasan']) 
                || in_array($request->role2, ['penerima', 'atasan']);

    if ($butuhBidang) {
        $karyawan->bidang_id = $request->bidang_id;
    } else {
        $karyawan->bidang_id = null;
    }

    $karyawan->save();

    // 🔥 Buat user
    User::create([
        'karyawan_id' => $karyawan->id,
        'name'        => $karyawan->nama,
        'email'       => $request->email,
        'password'    => Hash::make($request->password),
        'role'        => $request->role,
        'role2'       => $request->role2 ?? null,
        'subrole'  => $request->sub_bagian,
        'is_active'   => true,
    ]);

    return back()->with('success', 'Akun berhasil ditambahkan');
}



public function storebidang(Request $request)
{
    $request->validate([

        'nama_bidang' => 'required',
    ]);

    bidang::create([
        'nama_bidang' => $request->nama_bidang,
    ]);

    return back()->with('success', 'bidang berhasil ditambahkan');
}


// public function updateUser(Request $request, User $user)
// {
//     $request->validate([
//         'email'     => 'required|email|unique:users,email,' . $user->id,
//         'role'      => 'required',
//         'is_active' => 'required|in:0,1', // ⬅️ FIX
//         'password'  => 'nullable|min:6',
//         'bidang_id'   => 'nullable|exists:bidangs,id',
//     ]);

//     $data = [
//         'email'     => $request->email,
//         'role'      => $request->role,
//         'is_active' => (int) $request->is_active, // ⬅️ FIX UTAMA
//     ];

//     if ($request->filled('password')) {
//         $data['password'] = Hash::make($request->password);
//     }

//     $user->update($data);

//     return back()->with('success', 'Akun berhasil diperbarui');
// }


public function updateUser(Request $request, User $user)
{
    $request->validate([
        'email'     => 'required|email|unique:users,email,' . $user->id,
        'role'      => 'required',
        'role2'     => 'nullable',
        'is_active' => 'required|in:0,1',
        'password'  => 'nullable|min:6',
        'bidang_id' => 'nullable|exists:bidangs,id',
    ]);

    // 🔥 Update data user
    $data = [
        'email'     => $request->email,
        'role'      => $request->role,
        'role2'     => $request->role2 ?? null,
        'is_active' => (int) $request->is_active,
    ];

    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
    }

    $user->update($data);

    // 🔥 Logic update bidang di tabel karyawans
    $karyawan = $user->karyawan;

    $butuhBidang = in_array($request->role, ['penerima', 'atasan']) 
                || in_array($request->role2, ['penerima', 'atasan']);

    if ($butuhBidang) {
        $karyawan->bidang_id = $request->bidang_id;
    } else {
        $karyawan->bidang_id = null;
    }

    $karyawan->save();

    return back()->with('success', 'Akun berhasil diperbarui');
}


  public function destroyakun($id)
{
    User::findOrFail($id)->delete();

    return redirect()
        ->route('admin.pengguna')
        ->with('success', 'Data karyawan berhasil dihapus');
}

public function login(){
    return view('auth.admin-login');
}

public function exportAkun()
{
    $users = User::with('karyawan')->get();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Akun Pengguna');

    // Header
    $sheet->fromArray([
        ['No','Nama','Ruangan','Email']
    ], null, 'A1');

    $row = 2;

    foreach ($users as $i => $user) {
        $sheet->fromArray([
            $i + 1,
            $user->karyawan->nama ?? '-',
            $user->karyawan->ruangan ?? '-',
            $user->email,
        ], null, "A$row");

        $row++;
    }

    // Nama file
    $filename = 'akun_pengguna.xlsx';

    $writer = new Xlsx($spreadsheet);
    $path = storage_path($filename);
    $writer->save($path);

    return response()->download($path)->deleteFileAfterSend(true);
}
           


}
