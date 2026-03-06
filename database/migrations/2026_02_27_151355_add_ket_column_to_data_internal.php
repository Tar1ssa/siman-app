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
        Schema::table('data_internals', function (Blueprint $table) {
            $table->text('ket_lokasi')->nullable()->after('lokasi_id');
            $table->text('ket_penugasan')->nullable()->after('pengguna_unitkerja_id');
            $table->text('ket_unit_teknis')->nullable()->after('unit_teknis_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_internals', function (Blueprint $table) {
            //
        });
    }
};
