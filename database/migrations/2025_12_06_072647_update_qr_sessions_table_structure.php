<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qr_sessions', function (Blueprint $table) {
            // Drop existing columns if they exist
            $table->dropColumn(['token', 'date', 'is_global_one_time']);
            
            // Add new columns to match your database structure
            if (!Schema::hasColumn('qr_sessions', 'lecturer_id')) {
                $table->foreignId('lecturer_id')->nullable()->constrained('lecturers')->onDelete('cascade')->after('id');
            }
            
            if (!Schema::hasColumn('qr_sessions', 'session_name')) {
                $table->string('session_name')->after('lecturer_id');
            }
            
            if (!Schema::hasColumn('qr_sessions', 'description')) {
                $table->text('description')->nullable()->after('session_name');
            }
            
            if (!Schema::hasColumn('qr_sessions', 'session_date')) {
                $table->date('session_date')->after('description');
            }
            
            if (!Schema::hasColumn('qr_sessions', 'duration_minutes')) {
                $table->integer('duration_minutes')->after('end_time');
            }
            
            if (!Schema::hasColumn('qr_sessions', 'qr_token')) {
                $table->string('qr_token')->unique()->after('duration_minutes');
            }
            
            if (!Schema::hasColumn('qr_sessions', 'qr_code_path')) {
                $table->string('qr_code_path')->nullable()->after('qr_token');
            }
            
            if (!Schema::hasColumn('qr_sessions', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('qr_code_path');
            }
            
            if (!Schema::hasColumn('qr_sessions', 'is_global_one_time')) {
                $table->boolean('is_global_one_time')->default(false)->after('is_active');
            }
            
            if (!Schema::hasColumn('qr_sessions', 'is_scanned')) {
                $table->boolean('is_scanned')->default(false)->after('is_global_one_time');
            }
            
            if (!Schema::hasColumn('qr_sessions', 'scanned_at')) {
                $table->timestamp('scanned_at')->nullable()->after('is_scanned');
            }
            
            if (!Schema::hasColumn('qr_sessions', 'deleted_at')) {
                $table->softDeletes();
            }
            
            // Rename columns if needed
            Schema::table('qr_sessions', function (Blueprint $table) {
                $table->renameColumn('date', 'session_date');
            });
        });
    }

    public function down(): void
    {
        Schema::table('qr_sessions', function (Blueprint $table) {
            // Revert changes
            $table->dropColumn([
                'lecturer_id',
                'session_name',
                'description',
                'duration_minutes',
                'qr_token',
                'qr_code_path',
                'is_active',
                'is_global_one_time',
                'is_scanned',
                'scanned_at',
            ]);
            
            $table->dropSoftDeletes();
            $table->renameColumn('session_date', 'date');
        });
    }
};