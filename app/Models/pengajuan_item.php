<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pengajuan_item extends Model
{
      protected $table = 'pengajuan_items';
   protected $fillable = [
    'pengajuan_id', 'nama_barang', 'spesifikasi', 'satuan', 'jumlah', 'harga_satuan', 'harga','jumlah_disetujui','harga_disetujui','dokumen_upload'];

    
     public function pengajuan()
    {
        return $this->belongsTo(pengajuan::class);
    }

    public function barangTersedia()
    {
        return $this->hasOne(barang_tersedia::class);
    }
    use HasFactory;
}
