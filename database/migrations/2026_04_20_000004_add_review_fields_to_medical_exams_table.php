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
        Schema::table('medical_exams', function (Blueprint $table) {
            $table->foreignId('reviewed_by_doctor_id')->nullable()->after('uploaded_by')->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_by_doctor_at')->nullable()->after('uploaded_at');

            $table->index('reviewed_by_doctor_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_exams', function (Blueprint $table) {
            $table->dropIndex(['reviewed_by_doctor_at']);
            $table->dropConstrainedForeignId('reviewed_by_doctor_id');
            $table->dropColumn('reviewed_by_doctor_at');
        });
    }
};
