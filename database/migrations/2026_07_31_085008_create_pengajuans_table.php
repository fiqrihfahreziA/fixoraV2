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
        Schema::create('pengajuans', function (Blueprint $table) {
            $table->id();
            // identitas
            $table->foreignId('karyawan_id')->constrained('karyawans');
            $table->foreignId('bidang_id')->nullable()->constrained('bidangs')->nullOnDelete();
            $table->string('no_pengajuan')->unique();
            $table->date('tanggal_pengajuan');
            $table->year('tahun_anggaran')->nullable();
            $table->string('instalasi');
             // dasar usulan
            $table->string('dasar_usulan')->nullable();
            $table->string('log_status_penerima')->nullable();
            $table->string('log_status_atasan')->nullable();
            $table->string('log_status_keuangan')->nullable();
            $table->string('log_status_direktur')->nullable();
                    // uraian umum
            $table->text('alasan_justifikasi')->nullable();
            $table->text('manfaat')->nullable();
            $table->text('dampak')->nullable();
             // kondisi barang lama (digabung saja)
            $table->text('kondisi_barang_lama')->nullable();
            $table->text('ket_barang_lama')->nullable();
               // dokumen pendukung (opsional checkbox sederhana)
            $table->string('foto_barang')->nullable();
            $table->string('penawaran_harga')->nullable();
            $table->string('data_kerusakan')->nullable();
            $table->string('barjas')->nullable();
            $table->string('barajas_lainnya')->nullable();
            $table->string('dasar_usulan_lainnya')->nullable();
            // approval sederhana
            $table->foreignId('penerima_id')->nullable()->constrained('karyawans')->nullOnDelete();
            $table->foreignId('atasan_id')->nullable()->constrained('karyawans')->nullOnDelete();
            $table->foreignId('id_keuangan')->nullable()->constrained('karyawans')->nullOnDelete();
            $table->foreignId('direktur_id')->nullable()->constrained('karyawans')->nullOnDelete();
            $table->foreignId('ppk_id')->nullable()->constrained('karyawans')->nullOnDelete();
            $table->foreignId('kpa_id')->nullable()->constrained('karyawans')->nullOnDelete();

            $table->decimal('total_pengajuan', 15, 2)->default(0);
            $table->decimal('total_disetujui', 15, 2)->default(0);
            $table->decimal('total_disetujui_direktur', 15, 2)->default(0);
            $table->string('dasar_usulan_lainnya')->default('draft');
            // $table->enum('status', [
            //      'draft',
            // 'diajukan',
            // 'disetujui_koordinator',
            // 'disetujui_kabid',
            // 'dipertimbangkan',
            // 'menunggu_direktur',
            // 'disetujui',
            // 'disetujui sebagian',
            // 'ditolak',
            // 'revisi',
            // 'ditunda',
            // 'tersedia layak dan diutamakan',
            // 'perlu survei perbaikan',
            // 'tidak direkomendasikan',
            // 'sesuai kebutuhan',
            // 'sesuai standard',
            // 'sebagian anggaran',
            // 'anggaran tidak tersedia',
            // 'menunggu ppk',
            // 'menunggu_kpa'
                
            // ])->default('draft');
            // waktu approval
            $table->timestamp('diterima_at')->nullable();
            $table->timestamp('disetujui_kabid_at')->nullable();
            $table->timestamp('disetujui_keuangan_at')->nullable();
            $table->timestamp('disetujui_direktur_at')->nullable();
            $table->text('catatan_unit')->nullable();

            // Kepala Bidang
            $table->text('catatan_bidang')->nullable();

            // Perencanaan
            $table->text('catatan_perencanaan')->nullable();

            // IPSRS
            $table->text('catatan_ipsrs')->nullable();

            // Farmasi
            $table->text('catatan_farmasi')->nullable();

            // Keuangan
            $table->text('catatan_keuangan')->nullable();

            // Direktur
            $table->text('catatan_direktur')->nullable();
             $table->text('alasan_direktur')->nullable();

             //lampiran
             $table->string('analisis_kebutuhan')->nullable();
             $table->string('data_pengguna')->nullable();
             $table->string('spesifikasi_teknis')->nullable();
             $table->string('dokumen_pendukung_lainnya')->nullable();
            $table->date('tanggal_survei')->nullable();;
            $table->text('catatan_survei')->nullable();
            $table->text('prioritas')->nullable();

            $table->string('log_ppk')->nullable();
            $table->string('catatan_ppk')->nullable();
            $table->string('log_kpa')->nullable();
            $table->string('catatan_kpa')->nullable();
            $table->timestamp('kpa')->nullable();
            $table->string('acc_kabidPelayanan')->nullable(); //
            $table->string('acc_kabidPenunjang')->nullable(); //
            $table->timestamp('acc_kabidPelayanan_at')->nullable();
            $table->timestamp('acc_kabidPenunjang_at')->nullable();
            $table->string('log_acc_kabidPelayanan')->nullable(); //
            $table->string('log_acc_kabidPenunjang')->nullable(); //
             $table->string('no_surat')->nullable();
            
              
             $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};
