<?php

namespace App\Http\Controllers;

use App\Services\PurchaseService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    private PurchaseService $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    public function index()
    {
        $pricing = PurchaseService::PRICING;
        return view('purchase.index', compact('pricing'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'in:1,2,3,4,5'],
        ]);

        $pricing = PurchaseService::PRICING;
        $quantity = $request->quantity;
        $amount = $pricing[$quantity];

        return view('purchase.checkout', compact('quantity', 'amount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'in:1,2,3,4,5'],
            'payment_method' => ['required', 'string', 'in:mercado_pago,crypto,pix,boleto'],
        ]);

        $purchase = $this->purchaseService->createPurchase(
            auth()->user(),
            $request->quantity,
            $request->payment_method
        );

        // Redirecionar baseado no método de pagamento
        switch ($request->payment_method) {
            case 'pix':
                $paymentData = $this->purchaseService->simulatePixPayment($purchase);
                return view('purchase.payment', compact('purchase', 'paymentData'));
            
            case 'boleto':
                $paymentData = $this->purchaseService->simulateBoletoPayment($purchase);
                return view('purchase.payment', compact('purchase', 'paymentData'));
            
            case 'crypto':
                $paymentData = $this->purchaseService->simulateCryptoPayment($purchase);
                return view('purchase.payment', compact('purchase', 'paymentData'));
            
            case 'mercado_pago':
                $paymentData = $this->purchaseService->simulateMercadoPagoPayment($purchase);
                return view('purchase.payment', compact('purchase', 'paymentData'));
            
            default:
                return redirect()->route('purchase.index')->with('error', 'Método de pagamento inválido');
        }
    }

    public function confirmPayment(Request $request, int $id)
    {
        $purchase = auth()->user()->purchases()->findOrFail($id);

        if ($purchase->status !== 'pending') {
            return redirect()->route('dashboard.purchases.show', $purchase);
        }

        // Simulação - em produção, verificar com gateway
        $result = $this->purchaseService->completePurchase($purchase);

        return view('purchase.success', [
            'purchase' => $result['purchase'],
            'invites' => $result['invites'],
        ]);
    }

    public function show(int $id)
    {
        $purchase = auth()->user()->purchases()->with('invites')->findOrFail($id);
        return view('dashboard.purchases.show', compact('purchase'));
    }
}
