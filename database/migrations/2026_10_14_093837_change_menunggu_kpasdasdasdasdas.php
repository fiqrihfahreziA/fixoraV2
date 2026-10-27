<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pengajuans', function (Blueprint $table) {
            // $table->foreign('acc_kabidPelayanan')
            //       ->references('id')->on('karyawans')
            //       ->onDelete('set null')
            //       ->onUpdate('cascade');

            // $table->foreign('acc_kabidPenunjang')
            //       ->references('id')->on('karyawans')
            //       ->onDelete('set null')
            //       ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::table('pengajuans', function (Blueprint $table) {
            $table->dropForeign(['acc_kabidPelayanan']);
            $table->dropForeign(['acc_kabidPenunjang']);
        });
    }
};