
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
        <div class="table-responsive" >
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-primary">
                    <tr>
                        <th style="text-align: center">No</th>
                        <th style="text-align: center">Nama</th>
                        <th style="text-align: center">Email</th>
                        <th style="text-align: center">Jabatan</th>
                        <th style="text-align: center">Ruangan</th>
                        <th style="text-align: center">Role</th>
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
                            <span class="badge bg-info text-dark">
                                {{ ucfirst($user->role) }}
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

                    {{-- MODAL EDIT (HARUS DI DALAM LOOP) --}}
                    <div class="modal fade" id="modalEditUser{{ $user->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                                <form method="POST"
                                      action="{{ route('admin.users.update', $user->id) }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Akun Pengguna</h5>
                                        <button  type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                                            <select name="role" id="roleSelect" class="form-select" required>
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
                                        
                                        {{-- <div class="mb-3" id="">
                                            <label>Bidang</label>
                                            <select name="bidang_id" class="form-select">
                                                <option value="">-- Pilih Bidang --</option>
                                                @foreach($bidangs as $bidang)
                                                    <option value="{{ $bidang->id }}">
                                                        {{ $bidang->nama_bidang }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div> --}}

                                       <div class="mb-3">
                                            <label>Bidang</label>
                                            <select name="bidang_id" class="form-select">
                                                <option value="">-- Pilih Bidang --</option>

                                                @foreach($bidangs as $bidang)
                                                    <option value="{{ $bidang->id }}"
                                                        {{ old('bidang_id', optional($user->karyawan)->bidang_id) == $bidang->id ? 'selected' : '' }}>
                                                        {{ $bidang->nama_bidang }}
                                                    </option>
                                                @endforeach

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
                                        {{-- <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button> --}}
                                        <button class="btn btn-warning">Update</button>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>

                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
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

{{-- MODAL TAMBAH AKUN --}}
{{-- <div class="modal fade" id="modalTambahAkun" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form method="POST" action="{{ route('admin.pengguna.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Akun Pengguna</h5>
                    <button  type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                        <select name="role" class="form-select" required>
                            <option value="pemohon">Pemohon</option>
                            <option value="penerima">Penerima</option>
                            <option value="atasan">Atasan</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                 
                    <button class="btn btn-primary">Simpan</button>
                </div>

            </form>

        </div>
    </div>
</div> --}}

{{-- MODAL TAMBAH AKUN --}}
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
                    </div>


                    <div class="mb-3 d-none" id="bidangTambah">
                        <label>Bidang</label>
                        <select name="bidang_id" class="form-select">
                            <option value="">-- Pilih Bidang --</option>
                            @foreach($bidangs as $bidang)
                                <option value="{{ $bidang->id }}">
                                    {{ $bidang->nama_bidang }}
                                </option>
                            @endforeach
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
document.addEventListener('DOMContentLoaded', function() {

    // ===== TAMBAH =====
    const roleTambah = document.getElementById('roleTambah');
    const bidangTambah = document.getElementById('bidangTambah');

    if (roleTambah) {
        roleTambah.addEventListener('change', function() {
            if (this.value === 'penerima' || this.value === 'atasan') {
                bidangTambah.classList.remove('d-none');
            } else {
                bidangTambah.classList.add('d-none');
                bidangTambah.querySelector('select').value = '';
            }
        });
    }

    // ===== EDIT (multiple modal) =====
    document.querySelectorAll('.role-select').forEach(function(select) {
        select.addEventListener('change', function() {
            let userId = this.dataset.user;
            let bidangField = document.getElementById('bidang-edit-' + userId);

            if (this.value === 'penerima' || this.value === 'atasan') {
                bidangField.classList.remove('d-none');
            } else {
                bidangField.classList.add('d-none');
                bidangField.querySelector('select').value = '';
            }
        });
    });

});

document.addEventListener('DOMContentLoaded', function() {

    const roleSelect = document.getElementById('roleTambah');
    const fieldTambahan = document.getElementById('fieldRoleTambahan');
    const checkbox = document.getElementById('checkPenerima');

    roleSelect.addEventListener('change', function() {

        if (this.value === 'atasan') {
            fieldTambahan.classList.remove('d-none');
        } else {
            fieldTambahan.classList.add('d-none');
            checkbox.checked = false;
        }

    });

});
</script>


