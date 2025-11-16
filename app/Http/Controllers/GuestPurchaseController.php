<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\User;
use App\Models\Invite;
use App\Models\ActivityLog;
use App\Services\PurchaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GuestPurchaseController extends Controller
{
    private PurchaseService $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    public function index()
    {
        $pricing = PurchaseService::PRICING;
        return view('guest-purchase.index', compact('pricing'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'in:1,2,3,4,5'],
        ]);

        $pricing = PurchaseService::PRICING;
        $quantity = $request->quantity;
        $amount = $pricing[$quantity];

        // Armazenar quantidade na sessão
        session(['guest_purchase_quantity' => $quantity]);

        return view('guest-purchase.checkout', compact('quantity', 'amount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'quantity' => ['required', 'integer', 'in:1,2,3,4,5'],
            'payment_method' => ['required', 'string', 'in:mercado_pago,crypto,pix,boleto'],
        ]);

        $pricing = PurchaseService::PRICING;
        $amount = $pricing[$request->quantity];

        // Criar compra como guest
        $purchase = Purchase::create([
            'user_id' => null, // Guest purchase
            'invite_quantity' => $request->quantity,
            'amount_paid' => $amount,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'ip_address' => $request->ip(),
            'guest_name' => $request->name,
            'guest_email' => $request->email,
            'payment_details' => [
                'is_guest' => true,
            ],
        ]);

        // Armazenar dados na sessão
        session([
            'guest_purchase_id' => $purchase->id,
            'guest_name' => $request->name,
            'guest_email' => $request->email,
        ]);

        // Redirecionar baseado no método de pagamento
        switch ($request->payment_method) {
            case 'pix':
                $paymentData = $this->purchaseService->simulatePixPayment($purchase);
                return view('guest-purchase.payment', compact('purchase', 'paymentData'));
            
            case 'boleto':
                $paymentData = $this->purchaseService->simulateBoletoPayment($purchase);
                return view('guest-purchase.payment', compact('purchase', 'paymentData'));
            
            case 'crypto':
                $paymentData = $this->purchaseService->simulateCryptoPayment($purchase);
                return view('guest-purchase.payment', compact('purchase', 'paymentData'));
            
            case 'mercado_pago':
                $paymentData = $this->purchaseService->simulateMercadoPagoPayment($purchase);
                return view('guest-purchase.payment', compact('purchase', 'paymentData'));
            
            default:
                return redirect()->route('guest.purchase.index')->with('error', 'Método de pagamento inválido');
        }
    }

    public function confirmPayment(Request $request, int $id)
    {
        $purchase = Purchase::findOrFail($id);

        if ($purchase->status !== 'pending') {
            return redirect()->route('guest.purchase.success', $purchase->id);
        }

        // Marcar como completo
        $purchase->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Gerar convites
        $invites = [];
        for ($i = 0; $i < $purchase->invite_quantity; $i++) {
            $invite = Invite::create([
                'code' => Invite::generateUniqueCode(),
                'created_by_user_id' => null, // Sistema
                'purchase_id' => $purchase->id,
                'max_uses' => 1,
                'current_uses' => 0,
                'is_active' => true,
                'status' => 'active',
                'source' => 'guest_purchase',
                'notes' => "Compra guest: {$purchase->guest_name} ({$purchase->guest_email})",
            ]);
            $invites[] = $invite;
        }

        // Notificar admin
        ActivityLog::create([
            'type' => 'guest_purchase_completed',
            'user_id' => null,
            'description' => "Compra guest concluída: {$purchase->guest_name} ({$purchase->guest_email}) - {$purchase->invite_quantity} convites - R$ {$purchase->amount_paid}",
            'properties' => [
                'purchase_id' => $purchase->id,
                'guest_name' => $purchase->guest_name,
                'guest_email' => $purchase->guest_email,
                'quantity' => $purchase->invite_quantity,
                'amount' => $purchase->amount_paid,
            ],
        ]);

        return redirect()->route('guest.purchase.success', $purchase->id);
    }

    public function success(int $id)
    {
        $purchase = Purchase::with('invites')->findOrFail($id);
        
        if ($purchase->status !== 'completed') {
            return redirect()->route('guest.purchase.index')->with('error', 'Compra ainda não foi confirmada.');
        }

        return view('guest-purchase.success', compact('purchase'));
    }
}
