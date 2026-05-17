<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_formulas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->date('formula_date');
            $table->text('instructions')->nullable();
            $table->text('medications'); // JSON serialized array storing prescription list
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_formulas');
    }
};
