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
            'quantity' => ['required', 'integer', 'in:1,3,5,10'],
        ]);

        $pricing = PurchaseService::PRICING;
        $quantity = $request->quantity;
        $amount = $pricing[$quantity];

        return view('purchase.checkout', compact('quantity', 'amount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'in:1,3,5,10'],
            'payment_method' => ['required', 'string', 'in:pix'],
        ]);

        $purchase = $this->purchaseService->createPurchase(
            auth()->user(),
            $request->quantity,
            $request->payment_method
        );

        $pixData = $this->purchaseService->simulatePixPayment($purchase);

        return view('purchase.pix', compact('purchase', 'pixData'));
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
