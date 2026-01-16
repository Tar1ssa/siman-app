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
        Schema::create('siman_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bmn_id')->constrained('bmns')->onDelete('cascade');
            $table->foreignId('satker_id')->constrained('satkers')->onDelete('cascade');
            $table->foreignId('import_batch_id')->constrained('siman_batches')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            // $table->unique(['barang_id', 'nup']);
            $table->string('nup')->nullable();
            $table->string('merk')->nullable();
            $table->string('tipe')->nullable();
            $table->string('kondisi')->nullable();
            $table->string('no_dokumen')->nullable();
            $table->string('no_BPKP')->nullable();
            $table->string('no_polisi')->nullable();
            $table->string('no_sertifikat')->nullable();
            $table->date('tgl_perolehan')->nullable();
            $table->bigInteger('nilai_perolehan')->nullable();
            $table->bigInteger('nilai_penyusutan')->nullable();
            $table->bigInteger('nilai_buku')->nullable();
            $table->string('kode_register')->unique()->nullable();
            $table->string('lokasi_ruang')->nullable();
            $table->string('update_lokasi_ruang')->nullable();
            $table->string('update_kondisi')->nullable();
            $table->string('nama_pengguna')->nullable();
            $table->string('link_dokumentasi')->nullable();
            $table->date('opname')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siman_data');
    }
};
