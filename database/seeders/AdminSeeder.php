<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@university.edu',
            'password' => Hash::make('admin123'),
            'role_id' => 1,
            'must_change_password' => false,
            'nic' => '123456789V',
            'phone' => '0771234567',
            'address' => 'University Administration Building',
        ]);
    }
}