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
        Schema::create('data_internals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('satker_id')->constrained('satkers')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->foreignId('unit_kerja_id')->nullable()->constrained('unit_kerjas')->onDelete('set null');
            $table->foreignId('lokasi_id')->nullable()->constrained('lokasi_ruangs')->onDelete('set null');
            $table->foreignId('identitas_id')->nullable()->constrained('identitas')->onDelete('set null');
            $table->foreignId('pengguna_unitkerja_id')->nullable()->constrained('unit_kerjas')->onDelete('set null');
            $table->foreignId('unit_teknis_id')->nullable()->constrained('unit_teknis')->onDelete('set null');
            $table->unique(['barang_id', 'nup']);
            $table->unsignedInteger('nup')->nullable();
            $table->date('tgl_perolehan')->nullable();
            $table->string('merk')->nullable();
            $table->string('tipe')->nullable();
            $table->integer('jumlah')->nullable();
            $table->bigInteger('nilai_aset')->nullable();
            $table->bigInteger('nilai_penyusutan')->nullable();
            $table->bigInteger('nilai_buku')->nullable();
            $table->string('kondisi')->nullable();
            $table->string('akun_neraca')->nullable();
            $table->string('pembukuan')->nullable();
            $table->string('penggunaRaw')->nullable();
            // $table->string('lokasi_ruang')->nullable();
            $table->string('status_inven')->nullable();
            $table->string('update_kondisi')->nullable();
            $table->string('link_dokumentasi')->nullable();
            $table->string('link_lhi')->nullable();
            $table->string('no_bahi')->nullable();
            $table->date('tgl_bahi')->nullable();
            $table->string('kode_registrasi')->nullable();
            $table->bigInteger('siman_id')->nullable();
            $table->integer('batch')->nullable();
            $table->text('label')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('profile_image_path')->nullable();
            $table->string('nama_pengguna')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_internals');
    }
};
