<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Drop existing columns if they exist
            $table->dropColumn(['scanned_at']);
            
            // Add new columns to match your database structure
            if (!Schema::hasColumn('attendances', 'marked_at')) {
                $table->timestamp('marked_at')->useCurrent()->after('status');
            }
            
            if (!Schema::hasColumn('attendances', 'ip_address')) {
                $table->string('ip_address')->nullable()->after('marked_at');
            }
            
            if (!Schema::hasColumn('attendances', 'device_info')) {
                $table->text('device_info')->nullable()->after('ip_address');
            }
            
            // Add 'late' to status enum if not exists
            DB::statement("ALTER TABLE attendances MODIFY COLUMN status ENUM('present', 'late', 'absent') DEFAULT 'present'");
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Revert changes
            $table->dropColumn(['marked_at', 'ip_address', 'device_info']);
            $table->timestamp('scanned_at')->useCurrent();
            
            // Revert status enum
            DB::statement("ALTER TABLE attendances MODIFY COLUMN status ENUM('present', 'absent') DEFAULT 'present'");
        });
    }
};