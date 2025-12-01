<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nickname', 30)->unique()->nullable()->after('name');
        });
        
        // Generate nicknames for existing users
        $users = \App\Models\User::whereNull('nickname')->get();
        foreach ($users as $user) {
            $baseNickname = \Illuminate\Support\Str::slug($user->name, '');
            $nickname = $baseNickname;
            $counter = 1;
            
            while (\App\Models\User::where('nickname', $nickname)->exists()) {
                $nickname = $baseNickname . $counter;
                $counter++;
            }
            
            $user->update(['nickname' => strtolower(substr($nickname, 0, 30))]);
        }
        
        // Now make it not nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('nickname', 30)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nickname');
        });
    }
};
