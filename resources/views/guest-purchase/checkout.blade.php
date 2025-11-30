@extends('layouts.guest')

@section('title', 'Checkout - Atrocidades')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-neutral-200">Finalizar Compra</h1>
            <p class="mt-1 text-neutral-500 text-sm">Preencha seus dados e escolha o pagamento</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-neutral-900/60 backdrop-blur-xl border border-neutral-800/50 rounded-xl p-5 sticky top-20">
                    <h3 class="text-sm font-semibold text-neutral-300 mb-4">Resumo</h3>
                    
                    <div class="space-y-2 mb-4 text-sm">
                        <div class="flex justify-between text-neutral-400">
                            <span>Quantidade</span>
                            <span class="font-medium text-neutral-200">{{ $quantity }} {{ $quantity == 1 ? 'convite' : 'convites' }}</span>
                        </div>
                        <div class="flex justify-between text-neutral-400">
                            <span>Unitário</span>
                            <span>R$ {{ number_format($amount / $quantity, 2, ',', '.') }}</span>
                        </div>
                        @if($quantity > 1)
                        <div class="flex justify-between text-green-400">
                            <span>Desconto</span>
                            <span>-{{ number_format((1 - ($amount / ($quantity * 20))) * 100, 0) }}%</span>
                        </div>
                        @endif
                    </div>

                    <div class="border-t border-neutral-800 pt-3 flex justify-between text-neutral-200 font-bold">
                        <span>Total</span>
                        <span class="text-red-500">R$ {{ number_format($amount, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="lg:col-span-2">
                <form method="POST" action="{{ route('guest.purchase.store') }}" id="payment-form">
                    @csrf
                    <input type="hidden" name="quantity" value="{{ $quantity }}">
                    <input type="hidden" name="payment_method" id="payment_method" value="">

                    <!-- Guest Info -->
                    <div class="bg-neutral-900/60 backdrop-blur-xl border border-neutral-800/50 rounded-xl p-5 mb-5">
                        <h3 class="text-sm font-semibold text-neutral-300 mb-4">Seus Dados</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm text-neutral-400 mb-1.5">Nome Completo</label>
                                <input type="text" id="name" name="name" required value="{{ old('name') }}" 
                                    class="w-full px-4 py-2.5 bg-neutral-900/80 border border-neutral-700 rounded-lg text-neutral-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-900/50 focus:border-transparent">
                                @error('name')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm text-neutral-400 mb-1.5">Email</label>
                                <input type="email" id="email" name="email" required value="{{ old('email') }}" 
                                    class="w-full px-4 py-2.5 bg-neutral-900/80 border border-neutral-700 rounded-lg text-neutral-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-900/50 focus:border-transparent">
                                <p class="text-xs text-neutral-500 mt-1">Convites serão enviados aqui</p>
                                @error('email')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="bg-neutral-900/60 backdrop-blur-xl border border-neutral-800/50 rounded-xl p-5">
                        <h3 class="text-sm font-semibold text-neutral-300 mb-4">Método de Pagamento</h3>

                        <div class="space-y-3">
                            <!-- PIX -->
                            <label class="block cursor-pointer">
                                <input type="radio" name="payment_option" value="pix" class="peer sr-only" required>
                                <div class="border border-neutral-700 peer-checked:border-green-500 rounded-lg p-4 hover:border-neutral-600 transition peer-checked:bg-green-950/20">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-green-950/40 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-medium text-neutral-200 text-sm">PIX</h4>
                                            <p class="text-xs text-neutral-500">Instantâneo</p>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Boleto -->
                            <label class="block cursor-pointer">
                                <input type="radio" name="payment_option" value="boleto" class="peer sr-only">
                                <div class="border border-neutral-700 peer-checked:border-blue-500 rounded-lg p-4 hover:border-neutral-600 transition peer-checked:bg-blue-950/20">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-950/40 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M4 4h16v2H4V4zm0 4h16v2H4V8zm0 4h16v2H4v-2zm0 4h10v2H4v-2z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-medium text-neutral-200 text-sm">Boleto</h4>
                                            <p class="text-xs text-neutral-500">Até 2 dias úteis</p>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Crypto -->
                            <label class="block cursor-pointer">
                                <input type="radio" name="payment_option" value="crypto" class="peer sr-only">
                                <div class="border border-neutral-700 peer-checked:border-orange-500 rounded-lg p-4 hover:border-neutral-600 transition peer-checked:bg-orange-950/20">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-orange-950/40 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-medium text-neutral-200 text-sm">Bitcoin</h4>
                                            <p class="text-xs text-neutral-500">Anônimo</p>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Mercado Pago -->
                            <label class="block cursor-pointer">
                                <input type="radio" name="payment_option" value="mercado_pago" class="peer sr-only">
                                <div class="border border-neutral-700 peer-checked:border-cyan-500 rounded-lg p-4 hover:border-neutral-600 transition peer-checked:bg-cyan-950/20">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-cyan-950/40 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-cyan-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-medium text-neutral-200 text-sm">Mercado Pago</h4>
                                            <p class="text-xs text-neutral-500">Cartão de crédito</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="mt-5 flex gap-3">
                        <a href="{{ route('guest.purchase.index') }}" class="flex-1 px-4 py-2.5 bg-neutral-800 border border-neutral-700 text-neutral-400 rounded-lg hover:border-neutral-600 transition font-medium text-center text-sm">
                            Voltar
                        </a>
                        <button type="submit" id="submit-btn" class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            Continuar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const form = document.getElementById('payment-form');
    const paymentMethodInput = document.getElementById('payment_method');
    const submitButton = document.getElementById('submit-btn');

    document.querySelectorAll('input[name="payment_option"]').forEach(radio => {
        radio.addEventListener('change', function() {
            paymentMethodInput.value = this.value;
            submitButton.disabled = false;
        });
    });
</script>
@endsection
