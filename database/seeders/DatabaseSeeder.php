<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@atrocidades.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
            'is_admin' => true,
            'is_verified' => true,
        ]);

        // Regular User
        User::create([
            'name' => 'User Test',
            'email' => 'user@ominous.com',
            'password' => Hash::make('user123'),
            'email_verified_at' => now(),
            'is_admin' => false,
            'is_verified' => false,
        ]);

        // Verified User
        User::create([
            'name' => 'Verified User',
            'email' => 'verified@ominous.com',
            'password' => Hash::make('verified123'),
            'email_verified_at' => now(),
            'is_admin' => false,
            'is_verified' => true,
        ]);
    }
}
