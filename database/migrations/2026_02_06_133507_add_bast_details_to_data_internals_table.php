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
            $table->string('nip_pengguna')->nullable()->after('nama_pengguna');
            $table->text('alamat_pengguna')->nullable()->after('nip_pengguna');
            $table->string('jabatan_pengguna')->nullable()->after('alamat_pengguna');
            $table->string('nama_pihak_pertama')->nullable()->after('jabatan_pengguna');
            $table->string('nip_pihak_pertama')->nullable()->after('nama_pihak_pertama');
            $table->string('jabatan_pihak_pertama')->nullable()->after('nip_pihak_pertama');
            $table->text('alamat_pihak_pertama')->nullable()->after('jabatan_pihak_pertama');
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
