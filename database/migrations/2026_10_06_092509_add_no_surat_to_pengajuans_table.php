<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuans', function (Blueprint $table) {
            // 1. Tambahkan kolomnya dulu
            // $table->unsignedBigInteger('id_acc_kabidPelayanan')->nullable()->after('status');
            // $table->unsignedBigInteger('id_acc_kabidPenunjang')->nullable()->after('id_acc_kabidPelayanan');

            // // 2. Baru tambahkan foreign key
            // $table->foreign('id_acc_kabidPelayanan')
            //       ->references('id')->on('karyawans')
            //       ->onDelete('set null')
            //       ->onUpdate('cascade');

            // $table->foreign('id_acc_kabidPenunjang')
            //       ->references('id')->on('karyawans')
            //       ->onDelete('set null')
            //       ->onUpdate('cascade');
        $table->string('no_surat')->nullable();
            //    $table->timestamp('selesai_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pengajuans', function (Blueprint $table) {
            // Drop foreign key dulu, baru drop kolomnya
            $table->dropForeign(['id_acc_kabidPelayanan']);
            $table->dropForeign(['id_acc_kabidPenunjang']);
            $table->dropColumn(['id_acc_kabidPelayanan', 'id_acc_kabidPenunjang']);
        });
    }
};