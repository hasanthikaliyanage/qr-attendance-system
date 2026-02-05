<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Course;

class SubjectsSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            // IT Subjects
            ['name' => 'Computer Fundamentals', 'code' => 'CF101', 'credit_hours' => 3],
            ['name' => 'Programming Basics', 'code' => 'PB102', 'credit_hours' => 4],
            ['name' => 'Web Development', 'code' => 'WD103', 'credit_hours' => 4],
            ['name' => 'Database Systems', 'code' => 'DS104', 'credit_hours' => 4],
            ['name' => 'ICT Project', 'code' => 'IP105', 'credit_hours' => 2],
            ['name' => 'Object-Oriented Programming', 'code' => 'OOP201', 'credit_hours' => 4],
            ['name' => 'Data Structures', 'code' => 'DS202', 'credit_hours' => 4],
            ['name' => 'Software Engineering', 'code' => 'SE203', 'credit_hours' => 3],
            ['name' => 'Networking', 'code' => 'NET204', 'credit_hours' => 4],
            ['name' => 'Web Application Development', 'code' => 'WAD205', 'credit_hours' => 4],
            ['name' => 'Algorithms', 'code' => 'ALG301', 'credit_hours' => 4],
            ['name' => 'Operating Systems', 'code' => 'OS302', 'credit_hours' => 4],
            ['name' => 'Database Management Systems', 'code' => 'DBMS303', 'credit_hours' => 4],
            ['name' => 'Cloud Computing', 'code' => 'CC304', 'credit_hours' => 3],
            ['name' => 'Mobile Application Development', 'code' => 'MAD305', 'credit_hours' => 4],
            
            // English Subjects
            ['name' => 'Grammar', 'code' => 'GRAM101', 'credit_hours' => 3],
            ['name' => 'Spoken English', 'code' => 'SPE102', 'credit_hours' => 3],
            ['name' => 'Writing Skills', 'code' => 'WS103', 'credit_hours' => 3],
            ['name' => 'Communication Skills', 'code' => 'CS104', 'credit_hours' => 3],
            ['name' => 'Academic Writing', 'code' => 'AW201', 'credit_hours' => 3],
            ['name' => 'Literature Studies', 'code' => 'LS202', 'credit_hours' => 4],
            ['name' => 'Public Speaking', 'code' => 'PS203', 'credit_hours' => 3],
            ['name' => 'Linguistics', 'code' => 'LING204', 'credit_hours' => 4],
            ['name' => 'Drama Studies', 'code' => 'DS301', 'credit_hours' => 4],
            ['name' => 'Poetry', 'code' => 'POET302', 'credit_hours' => 4],
            ['name' => 'Advanced Composition', 'code' => 'AC303', 'credit_hours' => 3],
            ['name' => 'Literary Criticism', 'code' => 'LC304', 'credit_hours' => 4],
            
            // Management Subjects
            ['name' => 'Business Introduction', 'code' => 'BI101', 'credit_hours' => 3],
            ['name' => 'Marketing', 'code' => 'MKT102', 'credit_hours' => 3],
            ['name' => 'HR Basics', 'code' => 'HRB103', 'credit_hours' => 3],
            ['name' => 'Communication Skills', 'code' => 'CSM104', 'credit_hours' => 3],
            ['name' => 'Organizational Behaviour', 'code' => 'OB201', 'credit_hours' => 4],
            ['name' => 'Business Law', 'code' => 'BL202', 'credit_hours' => 3],
            ['name' => 'Business Strategy', 'code' => 'BS203', 'credit_hours' => 4],
            ['name' => 'Accounting', 'code' => 'ACC204', 'credit_hours' => 4],
            ['name' => 'Financial Management', 'code' => 'FM301', 'credit_hours' => 4],
            ['name' => 'Business Analytics', 'code' => 'BA302', 'credit_hours' => 4],
            ['name' => 'Operations Management', 'code' => 'OM303', 'credit_hours' => 4],
            ['name' => 'Strategic Management', 'code' => 'SM304', 'credit_hours' => 4],
        ];

        foreach ($subjects as $subjectData) {
            $subject = Subject::create($subjectData);
            
            // Associate subjects with courses based on subject codes
            if (str_contains($subject->code, '101') || str_contains($subject->code, '102') || 
                str_contains($subject->code, '103') || str_contains($subject->code, '104') || 
                str_contains($subject->code, '105')) {
                // Diploma level subjects
                if (str_starts_with($subject->code, 'CF') || str_starts_with($subject->code, 'PB') || 
                    str_starts_with($subject->code, 'WD') || str_starts_with($subject->code, 'DS') || 
                    str_starts_with($subject->code, 'IP')) {
                    $course = Course::where('code', 'DIT')->first();
                } elseif (str_starts_with($subject->code, 'GRAM') || str_starts_with($subject->code, 'SPE') || 
                         str_starts_with($subject->code, 'WS') || str_starts_with($subject->code, 'CS')) {
                    $course = Course::where('code', 'DENG')->first();
                } elseif (str_starts_with($subject->code, 'BI') || str_starts_with($subject->code, 'MKT') || 
                         str_starts_with($subject->code, 'HRB') || str_starts_with($subject->code, 'CSM')) {
                    $course = Course::where('code', 'DBM')->first();
                }
            } elseif (str_contains($subject->code, '201') || str_contains($subject->code, '202') || 
                     str_contains($subject->code, '203') || str_contains($subject->code, '204') || 
                     str_contains($subject->code, '205')) {
                // HND level subjects
                if (str_starts_with($subject->code, 'OOP') || str_starts_with($subject->code, 'DS') || 
                    str_starts_with($subject->code, 'SE') || str_starts_with($subject->code, 'NET') || 
                    str_starts_with($subject->code, 'WAD')) {
                    $course = Course::where('code', 'HNDC')->first();
                } elseif (str_starts_with($subject->code, 'AW') || str_starts_with($subject->code, 'LS') || 
                         str_starts_with($subject->code, 'PS') || str_starts_with($subject->code, 'LING')) {
                    $course = Course::where('code', 'HNDE')->first();
                } elseif (str_starts_with($subject->code, 'OB') || str_starts_with($subject->code, 'BL') || 
                         str_starts_with($subject->code, 'BS') || str_starts_with($subject->code, 'ACC')) {
                    $course = Course::where('code', 'HNDBM')->first();
                }
            } elseif (str_contains($subject->code, '301') || str_contains($subject->code, '302') || 
                     str_contains($subject->code, '303') || str_contains($subject->code, '304') || 
                     str_contains($subject->code, '305')) {
                // Degree level subjects
                if (str_starts_with($subject->code, 'ALG') || str_starts_with($subject->code, 'OS') || 
                    str_starts_with($subject->code, 'DBMS') || str_starts_with($subject->code, 'CC') || 
                    str_starts_with($subject->code, 'MAD')) {
                    $course = Course::where('code', 'BSCIT')->first();
                } elseif (str_starts_with($subject->code, 'DS') || str_starts_with($subject->code, 'POET') || 
                         str_starts_with($subject->code, 'AC') || str_starts_with($subject->code, 'LC')) {
                    $course = Course::where('code', 'BAE')->first();
                } elseif (str_starts_with($subject->code, 'FM') || str_starts_with($subject->code, 'BA') || 
                         str_starts_with($subject->code, 'OM') || str_starts_with($subject->code, 'SM')) {
                    $course = Course::where('code', 'BBA')->first();
                }
            }
            
            if (isset($course)) {
                $subject->courses()->attach($course->id, [
                    'semester' => $this->getSemesterFromCode($subject->code),
                    'is_core' => true
                ]);
            }
        }
    }
    
    private function getSemesterFromCode($code)
    {
        // Extract semester from subject code (e.g., 101 -> semester 1, 201 -> semester 3, etc.)
        $number = intval(substr($code, -3, 2));
        if ($number <= 10) return 1;
        if ($number <= 20) return 2;
        if ($number <= 30) return 3;
        if ($number <= 40) return 4;
        if ($number <= 50) return 5;
        if ($number <= 60) return 6;
        return 1;
    }
}