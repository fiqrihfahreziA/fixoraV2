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
        Schema::table('pengajuans', function (Blueprint $table) {
            // $table->foreignId('id_keuangan')
            //     ->nullable()
            //     ->after('status')
            //     ->constrained('karyawans')
            //     ->nullOnDelete();

            // $table->timestamp('disetujui_keuangan_at')
            //     ->nullable()
            //     ->after('id_keuangan');

            // $table->text('catatan_keuangan')
            //     ->nullable()
            //     ->after('disetujui_keuangan_at');

            $table->integer('status_keuangan')
                ->nullable()
                ->after('catatan_keuangan');

            $table->string('log_status_keuangan')
                ->nullable()
                ->after('status_keuangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuans', function (Blueprint $table) {
            $table->dropForeign(['id_keuangan']);

            $table->dropColumn([
                'id_keuangan',
                'disetujui_keuangan_at',
                'catatan_keuangan',
                'status_keuangan',
                'log_status_keuangan',
            ]);
        });
    }
};