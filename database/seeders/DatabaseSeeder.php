<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            DepartmentsSeeder::class,
            CoursesSeeder::class,
            SubjectsSeeder::class,
            AdminSeeder::class,
        ]);
    }
}