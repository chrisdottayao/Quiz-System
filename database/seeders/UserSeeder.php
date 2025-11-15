<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
   public function run(): void
{
    \App\Models\User::firstOrCreate(
        ['email' => 'teacher@example.com'],
        [
            'name' => 'Teacher Account',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]
    );

    \App\Models\User::firstOrCreate(
        ['email' => 'student@example.com'],
        [
            'name' => 'Student Account',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]
    );
}
}
