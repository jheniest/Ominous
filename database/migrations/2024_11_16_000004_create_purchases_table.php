<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('transaction_id')->unique();
            $table->integer('invite_quantity');
            $table->decimal('amount_paid', 10, 2);
            $table->string('currency', 3)->default('BRL');
            $table->enum('payment_method', ['pix', 'credit_card', 'boleto', 'mercado_pago', 'crypto', 'other'])->default('pix');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->text('payment_details')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('transaction_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
