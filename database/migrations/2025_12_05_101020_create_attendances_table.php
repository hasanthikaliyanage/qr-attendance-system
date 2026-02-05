<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qr_session_id')->constrained('qr_sessions')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('status', ['present', 'late', 'absent'])->default('present');
            $table->timestamp('marked_at');
            $table->string('ip_address', 45)->nullable();
            $table->text('device_info')->nullable();
            $table->timestamps();

            // Unique constraint - one student can only mark attendance once per session
            $table->unique(['qr_session_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};