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
            $table->tinyInteger('is_borrowed')->nullable()->after('kondisi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_internals', function (Blueprint $table) {
            $table->dropColumn('is_borrowed');
        });
    }
};
