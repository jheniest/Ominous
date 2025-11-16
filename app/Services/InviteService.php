<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Invite;
use App\Models\InviteRedemption;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InviteService
{
    public function validateInviteCode(string $code): array
    {
        $invite = Invite::where('code', $code)->first();

        if (!$invite) {
            return [
                'valid' => false,
                'error' => 'corrupted',
                'message' => 'Este convite não existe no Ominous.',
            ];
        }

        if (!$invite->is_active || $invite->status === 'suspended') {
            return [
                'valid' => false,
                'error' => 'suspended',
                'message' => 'Este convite foi banido das sombras.',
            ];
        }

        if ($invite->is_expired) {
            $invite->updateStatus('expired');
            return [
                'valid' => false,
                'error' => 'expired',
                'message' => 'Este convite expirou. O ritual falhou.',
            ];
        }

        if ($invite->is_consumed) {
            return [
                'valid' => false,
                'error' => 'consumed',
                'message' => 'Este convite já foi completamente consumido.',
            ];
        }

        return [
            'valid' => true,
            'invite' => $invite,
            'remaining_uses' => $invite->remaining_uses,
        ];
    }

    public function redeemInvite(string $code, User $user): bool
    {
        return DB::transaction(function () use ($code, $user) {
            $invite = Invite::where('code', $code)->lockForUpdate()->first();

            if (!$invite || !$invite->isValid()) {
                return false;
            }

            InviteRedemption::create([
                'invite_id' => $invite->id,
                'user_id' => $user->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            $invite->incrementUses();

            $user->update([
                'invited_by_user_id' => $invite->created_by_user_id,
                'invite_code_used' => $code,
                'invited_at' => now(),
            ]);

            ActivityLog::log(
                'invite_redeemed',
                $user->id,
                'Invite',
                $invite->id,
                "Convite {$code} resgatado",
                ['invite_code' => $code]
            );

            return true;
        });
    }

    public function createInvite(User $creator, int $maxUses = 1, ?int $daysValid = 30, string $source = 'manual'): Invite
    {
        $invite = Invite::create([
            'created_by_user_id' => $creator->id,
            'max_uses' => $maxUses,
            'expires_at' => $daysValid ? now()->addDays($daysValid) : null,
            'source' => $source,
        ]);

        ActivityLog::log(
            'invite_created',
            $creator->id,
            'Invite',
            $invite->id,
            "Convite {$invite->code} criado"
        );

        return $invite;
    }

    public function createInvitesFromPurchase(int $purchaseId, int $userId, int $quantity): array
    {
        $invites = [];

        for ($i = 0; $i < $quantity; $i++) {
            $invites[] = Invite::create([
                'created_by_user_id' => $userId,
                'purchase_id' => $purchaseId,
                'max_uses' => 1,
                'expires_at' => now()->addDays(60),
                'source' => 'purchase',
            ]);
        }

        ActivityLog::log(
            'invites_purchased',
            $userId,
            'Purchase',
            $purchaseId,
            "{$quantity} convites adquiridos",
            ['quantity' => $quantity]
        );

        return $invites;
    }

    public function suspendInvite(Invite $invite, ?string $reason = null): void
    {
        $invite->updateStatus('suspended');

        ActivityLog::log(
            'invite_suspended',
            auth()->id(),
            'Invite',
            $invite->id,
            $reason ?? 'Convite suspenso',
            ['reason' => $reason]
        );
    }

    public function reactivateInvite(Invite $invite): void
    {
        $invite->updateStatus('active');

        ActivityLog::log(
            'invite_reactivated',
            auth()->id(),
            'Invite',
            $invite->id,
            'Convite reativado'
        );
    }
}
