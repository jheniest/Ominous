<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert emergency access key setting
        DB::table('site_settings')->insertOrIgnore([
            'key' => 'emergency_access_key',
            'value' => \Illuminate\Support\Str::random(32),
            'type' => 'string',
            'description' => 'Chave de acesso de emergência para login durante manutenção',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('site_settings')->where('key', 'emergency_access_key')->delete();
    }
};
