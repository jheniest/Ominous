<?php

namespace Database\Seeders;

use App\Models\Invite;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OminousSeeder extends Seeder
{
    public function run(): void
    {
        // Criar o Owner (O Dono)
        $owner = User::create([
            'name' => 'Tekinha',
            'email' => 'tekinha.dev@gmail.com',
            'password' => Hash::make('ominous123'),
            'is_admin' => true,
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        echo "✝ O Dono foi invocado: tekinha.dev@gmail.com / ominous123\n";

        // Criar convites iniciais do owner
        $invites = [];
        for ($i = 0; $i < 5; $i++) {
            $invite = Invite::create([
                'created_by_user_id' => $owner->id,
                'max_uses' => rand(1, 3),
                'expires_at' => now()->addDays(30),
                'source' => 'admin',
                'notes' => 'Convite ritual inicial',
            ]);
            $invites[] = $invite->code;
        }

        echo "\n☠ Convites iniciais gerados:\n";
        foreach ($invites as $code) {
            echo "   → {$code}\n";
        }

        // Criar usuário de teste
        $testUser = User::create([
            'name' => 'Acolyte Test',
            'email' => 'test@ominous.dark',
            'password' => Hash::make('test123'),
            'email_verified_at' => now(),
        ]);

        // Criar convites do usuário de teste
        for ($i = 0; $i < 3; $i++) {
            Invite::create([
                'created_by_user_id' => $testUser->id,
                'max_uses' => 1,
                'expires_at' => now()->addDays(15),
                'source' => 'manual',
            ]);
        }

        echo "\n👤 Usuário de teste criado: test@ominous.dark / test123\n";
        echo "\n🌑 O ritual está completo. O Ominous aguarda...\n";
    }
}
