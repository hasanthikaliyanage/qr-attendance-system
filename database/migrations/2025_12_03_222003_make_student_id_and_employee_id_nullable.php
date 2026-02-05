<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('student_id')->nullable()->change();
        });

        Schema::table('lecturers', function (Blueprint $table) {
            $table->string('employee_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('student_id')->nullable(false)->change();
        });

        Schema::table('lecturers', function (Blueprint $table) {
            $table->string('employee_id')->nullable(false)->change();
        });
    }
};