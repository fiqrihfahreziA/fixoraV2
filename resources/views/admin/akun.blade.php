@extends('layouts.admin')

@section('content')

<div class="card shadow-sm" style="margin-bottom:30px">
    <div class="card-body">
        <h5 class="mb-0">Daftar Akun Pengguna</h5>
        <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
            <button class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#modalTambahAkun">
                + Tambah Akun
            </button>
            <a href="{{ route('admin.users.export') }}" class="btn btn-success">
                Export Excel
            </a>
        </div>

        <form method="GET" action="" class="mb-3">
            <div class="input-group">
                <input type="text"
                    name="search"
                    class="form-control"
                    placeholder="Cari nama, email, karyawan, jabatan, role..."
                    value="{{ request('search') }}">

                <button class="btn btn-primary">🔍 Cari</button>

                @if(request('search'))
                    <a href="{{ url()->current() }}" class="btn btn-secondary">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-primary">
                    <tr>
                        <th style="text-align: center">No</th>
                        <th style="text-align: center">Nama</th>
                        <th style="text-align: center">Email</th>
                        <th style="text-align: center">Jabatan</th>
                        <th style="text-align: center">Ruangan</th>
                        <th style="text-align: center">Bidang</th>
                        <th style="text-align: center">Sub Bagian</th>
                        <th style="text-align: center">Role</th>
                        <th style="text-align: center">Role2</th>
                        <th style="text-align: center">Status</th>
                        <th style="text-align: center" width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($userr as $user)
                    <tr>
                        <td style="text-align: center">{{ $userr->firstItem() + $loop->index }}</td>
                        <td style="text-align: center">{{ $user->karyawan->nama ?? '-' }}</td>
                        <td style="text-align: center">{{ $user->email }}</td>
                        <td style="text-align: center">{{ $user->karyawan->jabatan ?? '-' }}</td>
                        <td style="text-align: center">{{ $user->karyawan->ruangan ?? '-' }}</td>
                        <td style="text-align: center">
                            {{ optional(optional($user->karyawan)->bidang)->nama_bidang ?? '-' }}
                        </td>
                        <td style="text-align: center">
                            @if(($user->subrole ?? '') === 'kabid_pelayanan')
                                Kabid Pelayanan
                            @elseif(($user->subrole ?? '') === 'kabid_penunjang')
                                Kabid Penunjang
                            @else
                                -
                            @endif
                        </td>
                        <td style="text-align: center">
                            <span class="badge bg-info text-dark">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td style="text-align: center">
                            <span class="badge bg-info text-dark">
                                {{ ucfirst($user->role2) }}
                            </span>
                        </td>
                        <td>
                            @if ($user->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-nowrap">
                            <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditUser{{ $user->id }}">
                                Edit
                            </button>

                            <form action="{{ route('admin.users.destroy', $user->id) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('AKUN AKAN DIHAPUS PERMANEN. Lanjutkan?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>

                    {{-- ================= MODAL EDIT ================= --}}
                    <div class="modal fade" id="modalEditUser{{ $user->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                                <form method="POST"
                                      action="{{ route('admin.users.update', $user->id) }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Akun Pengguna</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">

                                        <div class="mb-3">
                                            <label>Karyawan</label>
                                            <input type="text"
                                                   class="form-control"
                                                   value="{{ $user->karyawan->nama ?? '-' }}"
                                                   readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label>Email</label>
                                            <input type="email"
                                                   name="email"
                                                   class="form-control"
                                                   value="{{ $user->email }}"
                                                   required>
                                        </div>

                                        <div class="mb-3">
                                            <label>Password Baru</label>
                                            <input type="password"
                                                   name="password"
                                                   class="form-control"
                                                   placeholder="Kosongkan jika tidak ingin mengubah">
                                        </div>

                                        <div class="mb-3">
                                            <label>Role</label>
                                            <select name="role" class="form-select" required>
                                                <option value="pemohon" {{ $user->role == 'pemohon' ? 'selected' : '' }}>Pemohon</option>
                                                <option value="penerima" {{ $user->role == 'penerima' ? 'selected' : '' }}>Penerima (pengurus barang)</option>
                                                <option value="atasan"  {{ $user->role == 'atasan' ? 'selected' : '' }}>Atasan (kabid)</option>
                                                <option value="keuangan"  {{ $user->role == 'keuangan' ? 'selected' : '' }}>Keuangan</option>
                                                <option value="ppk"  {{ $user->role == 'ppk' ? 'selected' : '' }}>PPK (Pejabat Pembuat Komitmen)</option>
                                                <option value="kpa"  {{ $user->role == 'kpa' ? 'selected' : '' }}>KPA (Kuasa Pengguna Anggaran)</option>
                                                <option value="direktur"  {{ $user->role == 'direktur' ? 'selected' : '' }}>Direktur</option>
                                                <option value="admin"   {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                            </select>
                                        </div>

                                        {{-- BIDANG EDIT --}}
                                        <div class="mb-3">
                                            <label>Bidang</label>
                                            <select name="bidang_id"
                                                    class="form-select bidang-edit"
                                                    data-user="{{ $user->id }}">
                                                <option value="">-- Pilih Bidang --</option>
                                                @foreach($bidangs as $bidang)
                                                    <option value="{{ $bidang->id }}"
                                                            data-nama="{{ strtolower($bidang->nama_bidang) }}"
                                                            {{ old('bidang_id', optional($user->karyawan)->bidang_id) == $bidang->id ? 'selected' : '' }}>
                                                        {{ $bidang->nama_bidang }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- SUB BAGIAN EDIT --}}
                                        <div class="mb-3 d-none" id="subBagianEdit{{ $user->id }}">
                                            <label>Sub Bagian</label>
                                            <select name="sub_bagian" class="form-select">
                                                <option value="">-- Pilih Sub Bagian --</option>
                                                <option value="kabid_pelayanan" {{ ($user->sub_bagian ?? '') == 'kabid_pelayanan' ? 'selected' : '' }}>Kabid Pelayanan</option>
                                                <option value="kabid_penunjang" {{ ($user->sub_bagian ?? '') == 'kabid_penunjang' ? 'selected' : '' }}>Kabid Penunjang</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label>Status Akun</label>
                                            <select name="is_active" class="form-select" required>
                                                <option value="1" {{ $user->is_active ? 'selected' : '' }}>Aktif</option>
                                                <option value="0" {{ ! $user->is_active ? 'selected' : '' }}>Nonaktif</option>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="modal-footer">
                                        <button class="btn btn-warning">Update</button>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>

                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted">
                            Belum ada akun pengguna
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $userr->links('pagination::bootstrap-5') }}

    </div>
</div>

{{-- ================= MODAL TAMBAH AKUN ================= --}}
<div class="modal fade" id="modalTambahAkun" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form method="POST" action="{{ route('admin.pengguna.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Akun Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Karyawan</label>
                        <select name="karyawan_id" class="form-select" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach ($karyawans as $k)
                                <option value="{{ $k->id }}">
                                    {{ $k->nama }} – {{ $k->ruangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" id="roleTambah" class="form-select" required>
                            <option value="pemohon">Pemohon</option>
                            <option value="penerima">Penerima</option>
                            <option value="atasan">Atasan</option>
                            <option value="keuangan">Keuangan</option>
                            <option value="ppk">PPK (Pejabat Pembuat Komitmen)</option>
                            <option value="kpa">KPA (Kuasa Pengguna Anggaran)</option>
                            <option value="direktur">Direktur</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="fieldRoleTambahan">
                        <label>Jadikan juga sebagai (opsional)</label>
                        <select name="role2" class="form-select" id="selectRoleTambahan">
                            <option value="">-- Tidak ada --</option>
                            <option value="penerima">Penerima (pengurus barang)</option>
                            <option value="ppk">PPK (Pejabat Pembuat Komitmen)</option>
                            <option value="keuangan">Keuangan (Pejabat Keuangan)</option>
                        </select>
                    </div>
                    {{-- <div class="mb-3 d-none" id="fieldRoleTambahan">
                        <div class="form-check">
                            <input class="form-check-input"
                                type="checkbox"
                                name="role2"
                                value="penerima"
                                id="checkPenerima">
                            <label class="form-check-label">
                                Jadikan juga sebagai Penerima
                            </label>
                        </div>
                    </div> --}}

                    {{-- BIDANG TAMBAH --}}
                    <div class="mb-3 d-none" id="bidangTambah">
                        <label>Bidang</label>
                        <select name="bidang_id" id="bidangSelectTambah" class="form-select">
                            <option value="">-- Pilih Bidang --</option>
                            @foreach($bidangs as $bidang)
                                <option value="{{ $bidang->id }}"
                                        data-nama="{{ strtolower($bidang->nama_bidang) }}">
                                    {{ $bidang->nama_bidang }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- SUB BAGIAN TAMBAH --}}
                    <div class="mb-3 d-none" id="subBagianTambah">
                        <label>Sub Bagian</label>
                        <select name="sub_bagian" class="form-select">
                            <option value="">-- Pilih Sub Bagian --</option>
                            <option value="kabid_pelayanan">Kabid Pelayanan</option>
                            <option value="kabid_penunjang">Kabid Penunjang</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* =========================================================
       MODAL TAMBAH
    ========================================================= */

    // ---- Bidang muncul kalau role = penerima / atasan ----
    const roleTambah = document.getElementById('roleTambah');
    const bidangTambahWrapper = document.getElementById('bidangTambah');

    if (roleTambah && bidangTambahWrapper) {
        roleTambah.addEventListener('change', function () {
            if (this.value === 'penerima' || this.value === 'atasan') {
                bidangTambahWrapper.classList.remove('d-none');
            } else {
                bidangTambahWrapper.classList.add('d-none');
                const sel = bidangTambahWrapper.querySelector('select');
                if (sel) sel.value = '';
                // sembunyikan juga sub bagian
                const sub = document.getElementById('subBagianTambah');
                if (sub) {
                    sub.classList.add('d-none');
                    const subSel = sub.querySelector('select');
                    if (subSel) subSel.value = '';
                }
            }
        });
    }

    // ---- Checkbox "Jadikan juga sebagai Penerima" ----
    // ---- Select "Jadikan juga sebagai" (Penerima / PPK) ----
            const fieldRoleTambahan = document.getElementById('fieldRoleTambahan');
            const selectRoleTambahan = document.getElementById('selectRoleTambahan');

            if (roleTambah && fieldRoleTambahan && selectRoleTambahan) {
                roleTambah.addEventListener('change', function () {
                    if (this.value === 'atasan') {
                        fieldRoleTambahan.classList.remove('d-none');
                    } else {
                        fieldRoleTambahan.classList.add('d-none');
                        selectRoleTambahan.value = '';
                    }
                });
            }
    // const fieldRoleTambahan = document.getElementById('fieldRoleTambahan');
    // const checkPenerima = document.getElementById('checkPenerima');

    // if (roleTambah && fieldRoleTambahan && checkPenerima) {
    //     roleTambah.addEventListener('change', function () {
    //         if (this.value === 'atasan') {
    //             fieldRoleTambahan.classList.remove('d-none');
    //         } else {
    //             fieldRoleTambahan.classList.add('d-none');
    //             checkPenerima.checked = false;
    //         }
    //     });
    // }

    // ---- Sub Bagian muncul kalau Bidang = "alat kesehatan" (TAMBAH) ----
    const bidangSelectTambah = document.getElementById('bidangSelectTambah');
    const subBagianTambah = document.getElementById('subBagianTambah');

    if (bidangSelectTambah && subBagianTambah) {
        bidangSelectTambah.addEventListener('change', function () {
            const nama = (this.options[this.selectedIndex]?.getAttribute('data-nama') || '').trim();

            if (nama === 'alat kesehatan') {
                subBagianTambah.classList.remove('d-none');
            } else {
                subBagianTambah.classList.add('d-none');
                const subSel = subBagianTambah.querySelector('select');
                if (subSel) subSel.value = '';
            }
        });
    }

    /* =========================================================
       MODAL EDIT (banyak modal, ID pakai user id)
    ========================================================= */

    document.querySelectorAll('.bidang-edit').forEach(function (select) {

        const userId = select.dataset.user;
        const subBagian = document.getElementById('subBagianEdit' + userId);

        if (!subBagian) return;

        function cekBidang() {
            const nama = (select.options[select.selectedIndex]?.getAttribute('data-nama') || '').trim();

            if (nama === 'alat kesehatan') {
                subBagian.classList.remove('d-none');
            } else {
                subBagian.classList.add('d-none');
                const subSel = subBagian.querySelector('select');
                if (subSel) subSel.value = '';
            }
        }

        // cek saat diubah
        select.addEventListener('change', cekBidang);

        // cek saat modal dibuka (biar langsung tampil sesuai data tersimpan)
        const modal = select.closest('.modal');
        if (modal) {
            modal.addEventListener('shown.bs.modal', cekBidang);
        }
    });

});
</script>