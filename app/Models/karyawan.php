<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class karyawan extends Model
{
     protected $table = 'karyawans';

    protected $fillable = [
        'nama',
        'nip',
        'jabatan',
        'ruangan',
        'ttd',
        'bidang_id'
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pengguna()
    {
        return $this->hasOne(User::class, 'karyawan_id');
    }

      public function bidang()
    {
        return $this->belongsTo(Bidang::class, 'bidang_id');
    }

      public function pengajuans()
    {
        return $this->hasMany(Pengajuan::class, 'atasan_id');
    }

     public function pengajuanKaryawan()
    {
        return $this->hasMany(Pengajuan::class, 'karyawan_id');
    }

    /**
     * Relasi ke pengajuan sebagai penerima
     */
    public function pengajuanPenerima()
    {
        return $this->hasMany(Pengajuan::class, 'penerima_id');
    }

    /**
     * Relasi ke pengajuan sebagai direktur
     */
    public function pengajuanDirektur()
    {
        return $this->hasMany(Pengajuan::class, 'direktur_id');
    }

    /**
     * Relasi ke pengajuan sebagai keuangan
     */
    public function pengajuanKeuangan()
    {
        return $this->hasMany(Pengajuan::class, 'id_keuangan');
    }


    use HasFactory;
}
