<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::table('pets', function (Blueprint $table) {
      $table->boolean('available_for_adoption')->default(false)->after('notes');
      $table->text('adoption_description')->nullable()->after('available_for_adoption');
    });
  }

  public function down(): void
  {
    Schema::table('pets', function (Blueprint $table) {
      $table->dropColumn(['available_for_adoption', 'adoption_description']);
    });
  }
};