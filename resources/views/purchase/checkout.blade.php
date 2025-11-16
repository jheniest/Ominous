<x-app-layout>
    <x-slot name="title">Checkout</x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-red-500">Finalizar Compra</h1>
                <p class="mt-1 text-neutral-400">Escolha o método de pagamento</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-6 sticky top-24">
                        <h3 class="text-lg font-semibold text-neutral-200 mb-4">Resumo do Pedido</h3>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-neutral-400">
                                <span>Quantidade</span>
                                <span class="font-semibold">{{ $quantity }} {{ $quantity == 1 ? 'convite' : 'convites' }}</span>
                            </div>
                            <div class="flex justify-between text-neutral-400">
                                <span>Valor unitário</span>
                                <span>R$ {{ number_format($amount / $quantity, 2, ',', '.') }}</span>
                            </div>
                            @if($quantity > 1)
                            <div class="flex justify-between text-green-400 text-sm">
                                <span>Desconto</span>
                                <span>{{ number_format((1 - ($amount / ($quantity * 20))) * 100, 0) }}%</span>
                            </div>
                            @endif
                            <div class="border-t border-neutral-800 pt-3 flex justify-between text-neutral-200 text-xl font-bold">
                                <span>Total</span>
                                <span class="text-red-400">R$ {{ number_format($amount, 2, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="text-xs text-neutral-500 bg-neutral-900/50 rounded p-3">
                            <p class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Compra 100% segura
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="lg:col-span-2">
                    <form method="POST" action="{{ route('purchase.store') }}" id="payment-form">
                        @csrf
                        <input type="hidden" name="quantity" value="{{ $quantity }}">
                        <input type="hidden" name="payment_method" id="payment_method" value="">

                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-neutral-200 mb-4">Selecione o método de pagamento:</h3>

                            <!-- PIX -->
                            <label class="block cursor-pointer">
                                <input type="radio" name="payment_option" value="pix" class="peer sr-only" required>
                                <div class="bg-neutral-950/60 border-2 border-neutral-800 peer-checked:border-green-500 rounded-lg p-6 hover:border-neutral-700 transition peer-checked:bg-green-950/20">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-green-950/40 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-neutral-200">PIX</h4>
                                            <p class="text-sm text-neutral-400">Pagamento instantâneo • Aprovação imediata</p>
                                        </div>
                                        <div class="w-5 h-5 rounded-full border-2 border-neutral-600 peer-checked:border-green-500 peer-checked:bg-green-500 flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Boleto -->
                            <label class="block cursor-pointer">
                                <input type="radio" name="payment_option" value="boleto" class="peer sr-only">
                                <div class="bg-neutral-950/60 border-2 border-neutral-800 peer-checked:border-blue-500 rounded-lg p-6 hover:border-neutral-700 transition peer-checked:bg-blue-950/20">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-blue-950/40 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M4 4h16v2H4V4zm0 4h16v2H4V8zm0 4h16v2H4v-2zm0 4h10v2H4v-2z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-neutral-200">Boleto Bancário</h4>
                                            <p class="text-sm text-neutral-400">Válido por 3 dias • Aprovação em até 2 dias úteis</p>
                                        </div>
                                        <div class="w-5 h-5 rounded-full border-2 border-neutral-600 peer-checked:border-blue-500 peer-checked:bg-blue-500 flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Crypto -->
                            <label class="block cursor-pointer">
                                <input type="radio" name="payment_option" value="crypto" class="peer sr-only">
                                <div class="bg-neutral-950/60 border-2 border-neutral-800 peer-checked:border-orange-500 rounded-lg p-6 hover:border-neutral-700 transition peer-checked:bg-orange-950/20">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-orange-950/40 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-6 h-6 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-neutral-200">Criptomoeda (Bitcoin)</h4>
                                            <p class="text-sm text-neutral-400">Anônimo • Confirmação em até 1 hora</p>
                                        </div>
                                        <div class="w-5 h-5 rounded-full border-2 border-neutral-600 peer-checked:border-orange-500 peer-checked:bg-orange-500 flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Mercado Pago -->
                            <label class="block cursor-pointer">
                                <input type="radio" name="payment_option" value="mercado_pago" class="peer sr-only">
                                <div class="bg-neutral-950/60 border-2 border-neutral-800 peer-checked:border-cyan-500 rounded-lg p-6 hover:border-neutral-700 transition peer-checked:bg-cyan-950/20">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-cyan-950/40 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-6 h-6 text-cyan-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm0 3v10h16V7H4zm2 2h8v2H6V9zm0 4h4v2H6v-2z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-neutral-200">Mercado Pago</h4>
                                            <p class="text-sm text-neutral-400">Cartão de crédito • Aprovação imediata</p>
                                        </div>
                                        <div class="w-5 h-5 rounded-full border-2 border-neutral-600 peer-checked:border-cyan-500 peer-checked:bg-cyan-500 flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="mt-8 flex gap-4">
                            <a href="{{ route('purchase.index') }}" class="flex-1 px-6 py-3 bg-neutral-900 border border-neutral-800 text-neutral-400 rounded-lg hover:border-neutral-700 transition font-medium text-center">
                                Voltar
                            </a>
                            <button type="submit" id="submit-btn" class="flex-1 px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-semibold disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                Continuar para Pagamento
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
</x-app-layout>
