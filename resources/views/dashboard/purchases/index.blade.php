<x-app-layout>
    <x-slot name="title">Minhas Compras</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-neutral-200">Minhas Compras</h1>
                <p class="mt-1 text-neutral-500">Histórico de transações</p>
            </div>

            @if($purchases->isEmpty())
                <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-12 text-center">
                    <p class="text-neutral-500">Você ainda não fez nenhuma compra.</p>
                    <a href="{{ route('purchase.index') }}" class="mt-4 inline-block text-red-700 hover:text-red-500">
                        Comprar convites →
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($purchases as $purchase)
                        <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <div class="text-neutral-200 font-semibold">Pedido #{{ $purchase->id }}</div>
                                    <div class="text-sm text-neutral-500">{{ $purchase->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                                <span class="px-3 py-1 text-sm rounded 
                                    @if($purchase->status === 'completed') bg-green-950/40 text-green-500 border border-green-900/50
                                    @elseif($purchase->status === 'pending') bg-amber-950/40 text-amber-500 border border-amber-900/50
                                    @elseif($purchase->status === 'refunded') bg-blue-950/40 text-blue-500 border border-blue-900/50
                                    @else bg-red-950/40 text-red-500 border border-red-900/50
                                    @endif">
                                    {{ ucfirst($purchase->status) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="p-4 bg-neutral-900/50 rounded border border-neutral-800">
                                    <div class="text-xs text-neutral-500 mb-1">Quantidade</div>
                                    <div class="text-lg font-semibold text-neutral-200">{{ $purchase->quantity }} {{ $purchase->quantity == 1 ? 'convite' : 'convites' }}</div>
                                </div>
                                <div class="p-4 bg-neutral-900/50 rounded border border-neutral-800">
                                    <div class="text-xs text-neutral-500 mb-1">Valor Total</div>
                                    <div class="text-lg font-semibold text-cyan-400">R$ {{ number_format($purchase->amount, 2, ',', '.') }}</div>
                                </div>
                                <div class="p-4 bg-neutral-900/50 rounded border border-neutral-800">
                                    <div class="text-xs text-neutral-500 mb-1">Método</div>
                                    <div class="text-lg font-semibold text-neutral-200">{{ ucfirst($purchase->payment_method) }}</div>
                                </div>
                            </div>

                            @if($purchase->status === 'pending')
                                <div class="flex gap-2">
                                    <a href="{{ route('purchase.checkout', $purchase->id) }}" class="flex-1 px-4 py-2 bg-amber-950/60 border border-amber-900 text-amber-400 rounded-lg hover:bg-amber-900/60 transition text-center">
                                        Continuar Pagamento
                                    </a>
                                </div>
                            @endif

                            @if($purchase->status === 'completed' && $purchase->invites->isNotEmpty())
                                <div class="border-t border-neutral-800 pt-4">
                                    <div class="text-sm text-neutral-400 mb-2">Convites desta compra:</div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        @foreach($purchase->invites as $invite)
                                            <div class="flex items-center justify-between p-2 bg-neutral-900/50 rounded text-sm">
                                                <code class="text-red-400 font-mono">{{ $invite->code }}</code>
                                                <span class="text-xs text-neutral-600">{{ $invite->current_uses }}/{{ $invite->max_uses }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $purchases->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
