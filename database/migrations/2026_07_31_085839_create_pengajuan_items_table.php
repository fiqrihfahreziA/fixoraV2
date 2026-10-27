<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuans')->cascadeOnDelete();
            $table->string('nama_barang');
            $table->text('spesifikasi')->nullable();
            $table->string('dokumen_upload')->nullable();
            $table->string('satuan')->nullable();
            $table->integer('jumlah');
            $table->unsignedBigInteger('harga')->nullable();
            $table->unsignedInteger('jumlah_disetujui')->nullable();
            $table->unsignedBigInteger('harga_disetujui')->nullable();
            $table->decimal('harga_satuan', 15, 2);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_items');
    }
};
