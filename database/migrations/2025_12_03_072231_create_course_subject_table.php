<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('course_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->integer('semester')->nullable();
            $table->boolean('is_core')->default(true);
            $table->timestamps();
            
            $table->unique(['course_id', 'subject_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_subject');
    }
};