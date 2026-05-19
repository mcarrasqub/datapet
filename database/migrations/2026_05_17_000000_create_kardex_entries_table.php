<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kardex_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->date('entry_date');
            $table->string('animal_type');
            $table->text('parameters'); // JSON serialized array storing clinical values
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kardex_entries');
    }
};
