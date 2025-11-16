<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transaction_id',
        'invite_quantity',
        'amount_paid',
        'currency',
        'payment_method',
        'status',
        'paid_at',
        'payment_details',
        'ip_address',
        'guest_name',
        'guest_email',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'invite_quantity' => 'integer',
        'paid_at' => 'datetime',
        'payment_details' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($purchase) {
            if (empty($purchase->transaction_id)) {
                $purchase->transaction_id = static::generateTransactionId();
            }
        });
    }

    public static function generateTransactionId(): string
    {
        do {
            $id = 'TXN-' . strtoupper(Str::random(12));
        } while (static::where('transaction_id', $id)->exists());

        return $id;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invites(): HasMany
    {
        return $this->hasMany(Invite::class);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    public function markAsRefunded(): void
    {
        $this->update(['status' => 'refunded']);
    }

    public function isGuestPurchase(): bool
    {
        return $this->user_id === null;
    }

    public function getPurchaserNameAttribute(): string
    {
        if ($this->isGuestPurchase()) {
            return $this->guest_name;
        }
        
        return $this->user->name;
    }

    public function getPurchaserEmailAttribute(): string
    {
        if ($this->isGuestPurchase()) {
            return $this->guest_email;
        }
        
        return $this->user->email;
    }
}
