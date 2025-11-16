<x-app-layout>
    <x-slot name="title">Comprar Convites</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-neutral-200">Adquira Convites</h1>
                <p class="mt-3 text-lg text-neutral-500">Escolha o pacote ideal para você</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($packages as $quantity => $price)
                    <div class="bg-neutral-950/60 backdrop-blur-lg border @if($quantity == 5) border-red-900/60 @else border-neutral-800 @endif rounded-lg overflow-hidden hover:border-red-900/40 transition group">
                        @if($quantity == 5)
                            <div class="bg-red-950/40 border-b border-red-900/50 px-4 py-2 text-center">
                                <span class="text-xs font-semibold text-red-400 uppercase tracking-wider">Mais Popular</span>
                            </div>
                        @endif
                        
                        <div class="p-8">
                            <div class="text-center mb-6">
                                <div class="text-5xl font-bold text-neutral-200 mb-2">{{ $quantity }}</div>
                                <div class="text-neutral-500">{{ $quantity == 1 ? 'Convite' : 'Convites' }}</div>
                            </div>

                            <div class="text-center mb-6">
                                <div class="text-3xl font-bold text-red-400">R$ {{ number_format($price, 2, ',', '.') }}</div>
                                @if($quantity > 1)
                                    <div class="text-sm text-neutral-600 mt-1">R$ {{ number_format($price / $quantity, 2, ',', '.') }} cada</div>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('purchase.create') }}">
                                @csrf
                                <input type="hidden" name="quantity" value="{{ $quantity }}">
                                <button type="submit" class="w-full px-6 py-3 @if($quantity == 5) bg-red-950/60 hover:bg-red-900/60 border-red-900 hover:border-red-700 text-red-400 hover:text-red-300 @else bg-neutral-900/60 hover:bg-neutral-800/60 border-neutral-800 hover:border-neutral-700 text-neutral-400 hover:text-neutral-300 @endif border rounded-lg transition font-medium">
                                    Comprar Agora
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
                        <span class="text-red-700">•</span>
                        <span>Os convites nunca expiram após a compra</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-red-700">•</span>
                        <span>Cada convite pode ser usado uma única vez</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-red-700">•</span>
                        <span>Pagamento via PIX com confirmação instantânea</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-red-700">•</span>
                        <span>Seus convites ficam disponíveis imediatamente após o pagamento</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
