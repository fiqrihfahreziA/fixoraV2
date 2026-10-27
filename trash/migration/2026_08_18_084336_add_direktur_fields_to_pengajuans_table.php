<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDirekturFieldsToPengajuansTable extends Migration
{
    public function up()
    {
        Schema::table('pengajuans', function (Blueprint $table) {
            // Total yang disetujui direktur (hanya untuk setujui full/sebagian)
            $table->decimal('total_disetujui_direktur', 15, 2)->nullable()->after('total_disetujui');
            
            // Alasan penolakan/penundaan (untuk tolak & tunda)
            $table->text('alasan_direktur')->nullable()->after('catatan_direktur');
        });

        // Tambah field ke tabel pengajuan_items (untuk setujui sebagian)
        Schema::table('pengajuan_items', function (Blueprint $table) {
            $table->boolean('disetujui_direktur')->default(false)->after('harga');
        });
    }

    public function down()
    {
        Schema::table('pengajuans', function (Blueprint $table) {
            $table->dropColumn([
                'total_disetujui_direktur',
                'alasan_direktur',
            ]);
        });

        Schema::table('pengajuan_items', function (Blueprint $table) {
            $table->dropColumn('disetujui_direktur');
        });
    }
}