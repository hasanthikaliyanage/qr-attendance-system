<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')->nullable()->constrained('lecturers')->onDelete('cascade');
            $table->string('session_name');
            $table->text('description')->nullable();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes');
            $table->string('qr_token')->unique();
            $table->string('qr_code_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_global_one_time')->default(false);
            $table->boolean('is_scanned')->default(false);
            $table->timestamp('scanned_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index(['department_id', 'course_id', 'subject_id']);
            $table->index(['session_date', 'start_time']);
            $table->index('is_active');
            $table->index('qr_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_sessions');
    }
};