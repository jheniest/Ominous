<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    private InviteService $inviteService;

    public function __construct(InviteService $inviteService)
    {
        $this->inviteService = $inviteService;
    }

    public const PRICING = [
        1 => 29.90,
        3 => 69.90,
        5 => 99.90,
        10 => 169.90,
    ];

    public function createPurchase(User $user, int $quantity, string $paymentMethod = 'pix'): Purchase
    {
        $amount = self::PRICING[$quantity] ?? throw new \InvalidArgumentException('Quantidade inválida');

        $purchase = Purchase::create([
            'user_id' => $user->id,
            'invite_quantity' => $quantity,
            'amount_paid' => $amount,
            'payment_method' => $paymentMethod,
            'status' => 'pending',
            'ip_address' => request()->ip(),
        ]);

        ActivityLog::log(
            'purchase_created',
            $user->id,
            'Purchase',
            $purchase->id,
            "Compra iniciada: {$quantity} convites por R$ {$amount}",
            ['quantity' => $quantity, 'amount' => $amount]
        );

        return $purchase;
    }

    public function completePurchase(Purchase $purchase): array
    {
        return DB::transaction(function () use ($purchase) {
            $purchase->markAsCompleted();

            $invites = $this->inviteService->createInvitesFromPurchase(
                $purchase->id,
                $purchase->user_id,
                $purchase->invite_quantity
            );

            ActivityLog::log(
                'purchase_completed',
                $purchase->user_id,
                'Purchase',
                $purchase->id,
                "Compra concluída: {$purchase->invite_quantity} convites gerados"
            );

            return [
                'purchase' => $purchase->fresh(),
                'invites' => $invites,
            ];
        });
    }

    public function simulatePixPayment(Purchase $purchase): array
    {
        // Simulação de PIX - em produção, integrar com gateway real
        $pixCode = $this->generatePixCode();
        
        $purchase->update([
            'payment_details' => [
                'pix_code' => $pixCode,
                'expires_at' => now()->addMinutes(30)->toIso8601String(),
            ],
        ]);

        return [
            'pix_code' => $pixCode,
            'qr_code_url' => $this->generateQrCodeUrl($pixCode),
            'amount' => $purchase->amount_paid,
            'expires_at' => now()->addMinutes(30),
        ];
    }

    private function generatePixCode(): string
    {
        return '00020126580014br.gov.bcb.pix0136' . uniqid() . '520400005303986540' . '5802BR5913OMINOUS6009DARKNESS';
    }

    private function generateQrCodeUrl(string $pixCode): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($pixCode);
    }

    public function refundPurchase(Purchase $purchase, ?string $reason = null): void
    {
        DB::transaction(function () use ($purchase, $reason) {
            $purchase->markAsRefunded();

            // Suspender convites gerados por esta compra
            foreach ($purchase->invites as $invite) {
                $this->inviteService->suspendInvite($invite, 'Compra reembolsada');
            }

            ActivityLog::log(
                'purchase_refunded',
                auth()->id(),
                'Purchase',
                $purchase->id,
                $reason ?? 'Compra reembolsada',
                ['reason' => $reason]
            );
        });
    }
}
