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
        Schema::create('atributs', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();   // registration_number
            $table->string('label');           // Registration Number
            $table->string('data_type');        // string, number, date
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atributs');
    }
};
