<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vaccination_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vaccination_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Propietario de la mascota
            $table->string('phone');
            $table->text('message');
            $table->string('status')->default('sent'); // sent, pending, failed
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vaccination_reminders');
    }
};
