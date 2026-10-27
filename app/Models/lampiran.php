<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class lampiran extends Model
{
   protected $fillable = [
        'nama_file_lampiran','analisis_kebutuhan','foto_barang','data_pengguna','spesifikasi_teknis','penawaran_harga','dokumen_pendukung_lainnya'
    ];
      protected $casts = [
        'foto_barang'               => 'array',
        'dokumen_pendukung_lainnya' => 'array',
    ];

    // 1 lampiran → 1 pengajuan
    public function pengajuan()
    {
        return $this->hasOne(pengajuan::class, 'lampiran_id');
    }
    use HasFactory;
}
