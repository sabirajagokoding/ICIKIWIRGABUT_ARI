<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'test@example.com',
            'password' => 'password',
            'active'=> 1,
            'role' => 1,
        ]);
        // \App\Models\Mahasiswa::factory(50)->create();
    }
}
