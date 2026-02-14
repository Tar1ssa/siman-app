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
        Schema::create('identitas_atributs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('identitas_id')->constrained('identitas')->cascadeOnDelete();
            $table->foreignId('atributs_id')->constrained('atributs')->cascadeOnDelete();

            // Validation & UI metadata
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->string('placeholder')->nullable();
            $table->string('help_text')->nullable();

            $table->unique(['identitas_id', 'atributs_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('identitas_atributs');
    }
};
