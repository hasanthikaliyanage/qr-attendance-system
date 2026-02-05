<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qr_session_id')->constrained('qr_sessions')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('status', ['present', 'late', 'absent'])->default('present');
            $table->timestamp('marked_at')->useCurrent();
            $table->string('ip_address')->nullable();
            $table->text('device_info')->nullable();
            $table->timestamps();
            
            // Prevent duplicate attendance for same student in same session
            $table->unique(['qr_session_id', 'student_id']);
            
            // Indexes for better performance
            $table->index('qr_session_id');
            $table->index('student_id');
            $table->index('marked_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};