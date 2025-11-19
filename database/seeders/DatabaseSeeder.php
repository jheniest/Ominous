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
        // Admin Principal
        User::create([
            'name' => 'Admin Master',
            'email' => 'admin@atrocidades.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
            'is_admin' => true,
            'is_verified' => true,
        ]);

        // Admin Moderador
        User::create([
            'name' => 'Moderador',
            'email' => 'moderador@atrocidades.com',
            'password' => Hash::make('mod123'),
            'email_verified_at' => now(),
            'is_admin' => true,
            'is_verified' => true,
        ]);

        // Admin Editor
        User::create([
            'name' => 'Editor',
            'email' => 'editor@atrocidades.com',
            'password' => Hash::make('editor123'),
            'email_verified_at' => now(),
            'is_admin' => true,
            'is_verified' => true,
        ]);

        // Usuário Teste
        User::create([
            'name' => 'Usuário Teste',
            'email' => 'teste@atrocidades.com',
            'password' => Hash::make('teste123'),
            'email_verified_at' => now(),
            'is_admin' => false,
            'is_verified' => true,
        ]);

        // Usuário Regular
        User::create([
            'name' => 'João Silva',
            'email' => 'joao@atrocidades.com',
            'password' => Hash::make('senha123'),
            'email_verified_at' => now(),
            'is_admin' => false,
            'is_verified' => false,
        ]);
    }
}
