<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Invite extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'created_by_user_id',
        'purchase_id',
        'max_uses',
        'current_uses',
        'expires_at',
        'is_active',
        'status',
        'source',
        'notes',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'max_uses' => 'integer',
        'current_uses' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($invite) {
            if (empty($invite->code)) {
                $invite->code = static::generateUniqueCode();
            }
        });
    }

    public static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(3) . '-' . Str::random(4) . '-' . Str::random(3));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(InviteRedemption::class);
    }

    public function isValid(): bool
    {
        if (!$this->is_active || $this->status !== 'active') {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            $this->updateStatus('expired');
            return false;
        }

        if ($this->current_uses >= $this->max_uses) {
            $this->updateStatus('consumed');
            return false;
        }

        return true;
    }

    public function updateStatus(string $status): void
    {
        $this->update([
            'status' => $status,
            'is_active' => $status === 'active',
        ]);
    }

    public function incrementUses(): void
    {
        $this->increment('current_uses');
        
        if ($this->current_uses >= $this->max_uses) {
            $this->updateStatus('consumed');
        }
    }

    public function getRemainingUsesAttribute(): int
    {
        return max(0, $this->max_uses - $this->current_uses);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getIsConsumedAttribute(): bool
    {
        return $this->current_uses >= $this->max_uses;
    }
}
