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
        Schema::create('invalid_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('satker_id')->nullable()->constrained('satkers')->onDelete('cascade');
            $table->foreignId('barang_id')->nullable()->constrained('barangs')->onDelete('cascade');
            $table->string('nup')->nullable();
            $table->date('tgl_perolehan')->nullable();
            $table->string('merkRaw')->nullable();
            $table->string('merk')->nullable();
            $table->string('tipe')->nullable();
            $table->string('jumlah')->nullable();
            $table->bigInteger('nilai_aset')->nullable();
            $table->bigInteger('nilai_penyusutan')->nullable();
            $table->bigInteger('nilai_buku')->nullable();
            $table->string('kondisi')->nullable();
            $table->string('akun_neraca')->nullable();
            $table->string('pembukuan')->nullable();
            $table->foreignId('unit_kerja_id')->nullable()->constrained('unit_kerjas')->onDelete('cascade');
            $table->string('pengguna')->nullable();
            $table->string('lokasi_ruang')->nullable();
            $table->string('status_inven')->nullable();
            $table->string('update_kondisi')->nullable();
            $table->string('link_dokumentasi')->nullable();
            $table->string('link_lhi')->nullable();
            $table->string('no_bahi')->nullable();
            $table->date('tgl_bahi')->nullable();
            $table->string('kode_registrasi')->nullable();
            $table->biginteger('siman_id')->nullable();
            $table->integer('batch')->nullable();
            $table->text('label')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invalid_data');
    }
};
