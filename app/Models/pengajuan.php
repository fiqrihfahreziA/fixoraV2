<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pengajuan extends Model
{
    protected $table = 'pengajuans';
   protected $fillable = [
    'tahun_anggaran',
    'karyawan_id', 
    'penerima_id', 
    'atasan_id',
    'id_keuangan',
    'direktur_id',
    'total_pengajuan',
    'bidang_id',
    'no_pengajuan',
    'tanggal_pengajuan',
    'instalasi',
    'status',
    'dasar_usulan',
    'alasan_justifikasi',
    'manfaat',
    'dampak',
    'kondisi_barang_lama',
    'ket_barang_lama',
    'foto_barang',
    'data_kerusakan',
    'penawaran_harga',
    'diterima_at',
    'disetujui_kabid_at',
    'disetujui_direktur_at',
    'catatan_unit',
    'catatan_bidang',
    'catatan_perencanaan',
    'catatan_ipsrs',
    'catatan_farmasi',
    'catatan_direktur',
    'catatan_keuangan',
    'total_disetujui',
    'log_status_penerima',
    'log_status_atasan',
    'log_status_keuangan',
    'disetujui_keuangan_at',
    'log_status_direktur',
    'status_keuangan',
    'total_disetujui_direktur',
    'analisis_kebutuhan',
    'spesifikasi_teknis',
    'penawaran_harga',
    'dasar_usulan_lainnya',
    'barjas',
    'barajas_lainnya',
    'data_pengguna',                // <- PASTIKAN ADA
    'dokumen_pendukung_lainnya',    // <- PASTIKAN ADA
    'foto_barang',                  // <- PASTIKAN ADA
    'tanggal_survei',
    'prioritas_survei',
    'catatan_survei',
    'catatan_ppk',
    'catatan_kpa',
    'ppk_id',
    'kpa_id',
    'log_ppk',      // ← TAMBAH
    'ppk_at',       // ← TAMBAH
    'log_kpa',      // ← TAMBAH kalau ada
    'kpa',       // ← TAMBAH kalau ada
    'kategori', //pemeliharaan atau pengadaan
    'id_acc_kabidPelayanan',
    'acc_kabidPelayanan_at',
    'log_acc_kabidPelayanan',
    'id_acc_kabidPenunjang',
    'acc_kabidPenunjang_at',
    'log_acc_kabidPenunjang',
    'selesai_at',
    
 

];

public function items()
    {
        return $this->hasMany(pengajuan_item::class);
    }

    public function karyawan()
    {
        return $this->belongsTo(karyawan::class);
    }

    public function bidang()
    {
        return $this->belongsTo(bidang::class);
    }

    public function penerima()
    {
        return $this->belongsTo(karyawan::class, 'penerima_id',);
    }

    public function atasan()
    {
        return $this->belongsTo(karyawan::class, 'atasan_id');
    }

    public function ppk()
    {
        return $this->belongsTo(karyawan::class, 'ppk_id');
    }

    
    public function keuangan()
    {
        return $this->belongsTo(karyawan::class, 'id_keuangan');
    }
    
    public function kpaa()
    {
        return $this->belongsTo(karyawan::class, 'kpa_id');
    }
    public function direktur()
    {
        return $this->belongsTo(karyawan::class, 'direktur_id');
    }

    public function lampiran()
    {
        return $this->belongsTo(Lampiran::class, 'lampiran_id');
    }

    public function uploadBarang()
    {
        return $this->belongsTo(UploadBarang::class, 'upload_barang_id');
    }
}