<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'lecturer_id')) {
                $table->foreignId('lecturer_id')->nullable()->constrained('lecturers')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'lecturer_id')) {
                $table->dropForeign(['lecturer_id']);
                $table->dropColumn('lecturer_id');
            }
        });
    }
};