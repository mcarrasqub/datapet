<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('species');
            $table->string('breed')->nullable();
            $table->enum('gender', ['male', 'female', 'unknown'])->default('unknown');
            $table->string('color')->nullable();
            $table->integer('age')->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->string('size')->nullable();
            $table->string('reproductive_status')->nullable();
            $table->boolean('is_deceased')->default(false);
            $table->boolean('emotional_support')->default(false);
            $table->boolean('service_animal')->default(false);

            // Diet & Husbandry
            $table->string('diet', 1000)->nullable();
            $table->string('diet_quantity')->nullable();
            $table->string('diet_frequency')->nullable();
            $table->string('housing', 1000)->nullable();
            $table->string('bath_frequency')->nullable();
            $table->string('bath_products')->nullable();
            $table->string('other_pets')->nullable();
            $table->string('last_heat')->nullable();

            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
