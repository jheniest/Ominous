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
        1 => 20.00,      // R$20.00 (preço base)
        2 => 28.00,      // R$14.00 cada (30% desconto)
        3 => 36.00,      // R$12.00 cada (40% desconto)
        4 => 44.00,      // R$11.00 cada (45% desconto)
        5 => 50.00,      // R$10.00 cada (50% desconto)
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
            'method' => 'pix',
            'pix_code' => $pixCode,
            'qr_code_url' => $this->generateQrCodeUrl($pixCode),
            'amount' => $purchase->amount_paid,
            'expires_at' => now()->addMinutes(30),
        ];
    }

    public function simulateBoletoPayment(Purchase $purchase): array
    {
        // Simulação de Boleto
        $boletoCode = $this->generateBoletoCode();
        
        $purchase->update([
            'payment_details' => [
                'boleto_code' => $boletoCode,
                'boleto_url' => 'https://ominous.app/boleto/' . $purchase->id,
                'expires_at' => now()->addDays(3)->toIso8601String(),
            ],
        ]);

        return [
            'method' => 'boleto',
            'boleto_code' => $boletoCode,
            'boleto_url' => 'https://ominous.app/boleto/' . $purchase->id,
            'barcode' => $boletoCode,
            'amount' => $purchase->amount_paid,
            'expires_at' => now()->addDays(3),
        ];
    }

    public function simulateCryptoPayment(Purchase $purchase): array
    {
        // Simulação de Crypto (Bitcoin)
        $btcAddress = $this->generateBtcAddress();
        $btcAmount = $this->convertToBtc($purchase->amount_paid);
        
        $purchase->update([
            'payment_details' => [
                'crypto_type' => 'BTC',
                'wallet_address' => $btcAddress,
                'amount_crypto' => $btcAmount,
                'expires_at' => now()->addHours(2)->toIso8601String(),
            ],
        ]);

        return [
            'method' => 'crypto',
            'crypto_type' => 'Bitcoin (BTC)',
            'wallet_address' => $btcAddress,
            'amount_brl' => $purchase->amount_paid,
            'amount_crypto' => $btcAmount,
            'qr_code_url' => $this->generateQrCodeUrl('bitcoin:' . $btcAddress . '?amount=' . $btcAmount),
            'expires_at' => now()->addHours(2),
        ];
    }

    public function simulateMercadoPagoPayment(Purchase $purchase): array
    {
        // Simulação de Mercado Pago
        $paymentId = 'MP-' . strtoupper(uniqid());
        
        $purchase->update([
            'payment_details' => [
                'payment_id' => $paymentId,
                'payment_url' => 'https://mercadopago.com.br/checkout/' . $paymentId,
            ],
        ]);

        return [
            'method' => 'mercado_pago',
            'payment_id' => $paymentId,
            'payment_url' => 'https://mercadopago.com.br/checkout/' . $paymentId,
            'amount' => $purchase->amount_paid,
        ];
    }

    private function generateBoletoCode(): string
    {
        return '23793.38128 60000.000001 00000.000000 1 ' . now()->format('Ymd') . '0000' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }

    private function generateBtcAddress(): string
    {
        return 'bc1q' . bin2hex(random_bytes(32));
    }

    private function convertToBtc(float $brlAmount): string
    {
        // Simulação: 1 BTC = R$ 500.000,00
        $btcPrice = 500000;
        return number_format($brlAmount / $btcPrice, 8, '.', '');
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
