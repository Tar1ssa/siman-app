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
        Schema::create('data_atributs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_internal_id')->constrained('data_internals')->cascadeOnDelete();
            $table->foreignId('atributs_id')->constrained('atributs')->cascadeOnDelete();

            // Typed values (important for performance)
            $table->string('value_string')->nullable();
            $table->integer('value_integer')->nullable();
            $table->date('value_date')->nullable();

            $table->index(['atributs_id', 'value_integer']);
            $table->index(['atributs_id', 'value_string']);
            $table->unique(['data_internal_id', 'atributs_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_atributs');
    }
};
