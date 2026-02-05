<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CoursesSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            // IT Department
            ['department_id' => 1, 'name' => 'Diploma in IT', 'code' => 'DIT', 'duration_months' => 24],
            ['department_id' => 1, 'name' => 'HND in Computing', 'code' => 'HNDC', 'duration_months' => 24],
            ['department_id' => 1, 'name' => 'BSc in Information Technology', 'code' => 'BSCIT', 'duration_months' => 36],
            
            // English Department
            ['department_id' => 2, 'name' => 'Diploma in English', 'code' => 'DENG', 'duration_months' => 24],
            ['department_id' => 2, 'name' => 'HND in English', 'code' => 'HNDE', 'duration_months' => 24],
            ['department_id' => 2, 'name' => 'BA in English', 'code' => 'BAE', 'duration_months' => 36],
            
            // Management Department
            ['department_id' => 3, 'name' => 'Diploma in Business Management', 'code' => 'DBM', 'duration_months' => 24],
            ['department_id' => 3, 'name' => 'HND in Business Management', 'code' => 'HNDBM', 'duration_months' => 24],
            ['department_id' => 3, 'name' => 'BBA', 'code' => 'BBA', 'duration_months' => 36],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}