<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class upload_barang extends Model
{
    protected $fillable = [
        'nama_upload_barang'
    ];
     public function pengajuan()
    {
        return $this->hasOne(pengajuan::class, 'upload_barang_id');
    }
    use HasFactory;

}
