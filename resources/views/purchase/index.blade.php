<x-app-layout>
    <x-slot name="title">Comprar Convites</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-red-500">Adquira Convites</h1>
                <p class="mt-3 text-lg text-neutral-400">Escolha o pacote ideal para você e ganhe descontos progressivos</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                @foreach($pricing as $quantity => $price)
                    @php
                        $basePrice = 20.00;
                        $totalBase = $basePrice * $quantity;
                        $discount = $quantity > 1 ? (($totalBase - $price) / $totalBase * 100) : 0;
                    @endphp
                    <div class="bg-neutral-950/60 backdrop-blur-lg border @if($quantity == 5) border-red-900/60 @else border-neutral-800 @endif rounded-lg overflow-hidden hover:border-red-900/40 transition group">
                        @if($quantity == 5)
                            <div class="bg-red-950/40 border-b border-red-900/50 px-4 py-2 text-center">
                                <span class="text-xs font-semibold text-red-400 uppercase tracking-wider">50% OFF</span>
                            </div>
                        @elseif($discount > 0)
                            <div class="bg-neutral-900/60 border-b border-neutral-800 px-4 py-2 text-center">
                                <span class="text-xs font-semibold text-green-400 uppercase tracking-wider">{{ number_format($discount, 0) }}% OFF</span>
                            </div>
                        @else
                            <div class="h-10"></div>
                        @endif
                        
                        <div class="p-6">
                            <div class="text-center mb-6">
                                <div class="text-4xl font-bold text-neutral-200 mb-2">{{ $quantity }}</div>
                                <div class="text-neutral-500 text-sm">{{ $quantity == 1 ? 'Convite' : 'Convites' }}</div>
                            </div>

                            <div class="text-center mb-6">
                                <div class="text-3xl font-bold text-red-400">R$ {{ number_format($price, 2, ',', '.') }}</div>
                                <div class="text-sm text-neutral-500 mt-1">R$ {{ number_format($price / $quantity, 2, ',', '.') }} cada</div>
                                @if($discount > 0)
                                    <div class="text-xs text-green-400 mt-1">Economize R$ {{ number_format($totalBase - $price, 2, ',', '.') }}</div>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('purchase.create') }}">
                                @csrf
                                <input type="hidden" name="quantity" value="{{ $quantity }}">
                                <button type="submit" class="w-full px-4 py-3 @if($quantity == 5) bg-red-600 hover:bg-red-700 text-white @else bg-neutral-800 hover:bg-neutral-700 text-neutral-200 @endif rounded-lg transition font-semibold text-sm">
                                    Comprar
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-12 bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-8">
                <h3 class="text-lg font-semibold text-neutral-200 mb-4">Informações Importantes</h3>
                <ul class="space-y-2 text-neutral-400 text-sm">
                    <li class="flex items-start gap-2">
                        <span class="text-red-500">•</span>
                        <span>Os convites nunca expiram após a compra</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-red-500">•</span>
                        <span>Cada convite pode ser usado uma única vez</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-red-500">•</span>
                        <span>Múltiplas opções de pagamento: PIX, Boleto, Crypto e Mercado Pago</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-red-500">•</span>
                        <span>Seus convites ficam disponíveis imediatamente após confirmação do pagamento</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-red-500">•</span>
                        <span>Quanto mais você compra, maior o desconto (até 50% OFF)</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
