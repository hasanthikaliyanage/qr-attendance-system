<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentsSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Information Technology Department', 'code' => 'IT'],
            ['name' => 'English Department', 'code' => 'ENG'],
            ['name' => 'Management Department', 'code' => 'MGT'],
            ['name' => 'Humanities Department', 'code' => 'HUM'],
            ['name' => 'Science Department', 'code' => 'SCI'],
            ['name' => 'Engineering Department', 'code' => 'ENGN'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}