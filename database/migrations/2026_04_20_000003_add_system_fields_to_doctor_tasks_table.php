<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('doctor_tasks', function (Blueprint $table) {
            $table->boolean('is_system')->default(false)->after('priority');
            $table->string('source_type')->nullable()->after('is_system');
            $table->unsignedBigInteger('source_id')->nullable()->after('source_type');
            $table->string('task_key')->nullable()->after('source_id')->unique();

            $table->index(['doctor_id', 'is_system']);
            $table->index(['source_type', 'source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctor_tasks', function (Blueprint $table) {
            $table->dropIndex(['doctor_id', 'is_system']);
            $table->dropIndex(['source_type', 'source_id']);
            $table->dropUnique(['task_key']);

            $table->dropColumn([
                'is_system',
                'source_type',
                'source_id',
                'task_key',
            ]);
        });
    }
};
