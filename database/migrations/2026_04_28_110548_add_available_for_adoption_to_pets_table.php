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
    Schema::table('pets', function (Blueprint $table) {
        // Añade la columna como booleano, por defecto en falso
        $table->boolean('available_for_adoption')->default(false)->after('species'); 
    });
}

public function down(): void
{
    Schema::table('pets', function (Blueprint $table) {
        $table->dropColumn('available_for_adoption');
    });
}
};
