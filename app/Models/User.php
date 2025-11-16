<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'invited_by_user_id',
        'invite_code_used',
        'invited_at',
        'is_admin',
        'is_verified',
        'is_suspended',
        'suspended_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'invited_at' => 'datetime',
            'suspended_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
        ];
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_user_id');
    }

    public function invitedUsers()
    {
        return $this->hasMany(User::class, 'invited_by_user_id');
    }

    // Video relationships
    public function videos()
    {
        return $this->hasMany(\App\Models\Video::class);
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\Comment::class);
    }

    public function videoReports()
    {
        return $this->hasMany(\App\Models\VideoReport::class);
    }

    public function createdInvites(): HasMany
    {
        return $this->hasMany(Invite::class, 'created_by_user_id');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(InviteRedemption::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function suspend(?string $reason = null): void
    {
        $this->update([
            'is_suspended' => true,
            'suspended_at' => now(),
        ]);

        ActivityLog::log('user_suspended', $this->id, 'User', $this->id, $reason);
    }

    public function unsuspend(): void
    {
        $this->update([
            'is_suspended' => false,
            'suspended_at' => null,
        ]);

        ActivityLog::log('user_unsuspended', $this->id, 'User', $this->id);
    }

    public function getInviteTreeAttribute(): array
    {
        return $this->buildInviteTree();
    }

    private function buildInviteTree(int $depth = 0, int $maxDepth = 5): array
    {
        if ($depth >= $maxDepth) {
            return [];
        }

        return $this->invitedUsers->map(function ($user) use ($depth, $maxDepth) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'invited_at' => $user->invited_at,
                'children' => $user->buildInviteTree($depth + 1, $maxDepth),
            ];
        })->toArray();
    }
}
