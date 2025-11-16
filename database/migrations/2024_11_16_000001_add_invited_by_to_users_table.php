<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('invited_by_user_id')->nullable()->after('remember_token')->constrained('users')->onDelete('set null');
            $table->string('invite_code_used')->nullable()->after('invited_by_user_id');
            $table->timestamp('invited_at')->nullable()->after('invite_code_used');
            $table->boolean('is_admin')->default(false)->after('invited_at');
            $table->boolean('is_suspended')->default(false)->after('is_admin');
            $table->timestamp('suspended_at')->nullable()->after('is_suspended');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['invited_by_user_id']);
            $table->dropColumn([
                'invited_by_user_id',
                'invite_code_used',
                'invited_at',
                'is_admin',
                'is_suspended',
                'suspended_at'
            ]);
        });
    }
};
