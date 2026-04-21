<?php

namespace Database\Seeders;

use App\Models\User;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([

            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password'=> Hash::make('password'),
            'isAdmin' => 1,

            // 'name' => 'Test',
            // 'email' => 'test@example.com',
            // 'password'=> Hash::make('password'),
            // 'isAdmin' => 0,
        ]);


       
    }
}
