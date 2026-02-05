<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('enrollable_type'); // App\Models\Course or App\Models\Subject
            $table->unsignedBigInteger('enrollable_id');
            $table->enum('status', ['enrolled', 'dropped', 'completed'])->default('enrolled');
            $table->date('enrollment_date');
            $table->date('completion_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Polymorphic relationship index
            $table->index(['enrollable_type', 'enrollable_id']);
            // Ensure a student can only be enrolled once in a specific course/subject
            $table->unique(['student_id', 'enrollable_type', 'enrollable_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('enrollments');
    }
};