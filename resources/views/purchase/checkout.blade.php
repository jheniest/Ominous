<x-app-layout>
    <x-slot name="title">Checkout</x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-neutral-200">Finalizar Compra</h1>
                <p class="mt-1 text-neutral-500">Pedido #{{ $purchase->id }}</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Purchase Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-6 sticky top-24">
                        <h3 class="text-lg font-semibold text-neutral-200 mb-4">Resumo do Pedido</h3>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-neutral-400">
                                <span>Quantidade</span>
                                <span>{{ $purchase->quantity }} {{ $purchase->quantity == 1 ? 'convite' : 'convites' }}</span>
                            </div>
                            <div class="flex justify-between text-neutral-400">
                                <span>Valor unitário</span>
                                <span>R$ {{ number_format($purchase->amount / $purchase->quantity, 2, ',', '.') }}</span>
                            </div>
                            <div class="border-t border-neutral-800 pt-3 flex justify-between text-neutral-200 text-lg font-semibold">
                                <span>Total</span>
                                <span class="text-red-400">R$ {{ number_format($purchase->amount, 2, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="text-xs text-neutral-600">
                            <p>Status: <span class="text-amber-500">{{ ucfirst($purchase->status) }}</span></p>
                            <p class="mt-1">Criado: {{ $purchase->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- PIX Payment -->
                <div class="lg:col-span-2">
                    <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-8">
                        <div class="text-center mb-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-cyan-950/40 border border-cyan-900/50 mb-4">
                                <svg class="w-8 h-8 text-cyan-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-neutral-200">Pagamento via PIX</h2>
                            <p class="mt-2 text-neutral-500">Escaneie o QR Code ou copie o código</p>
                        </div>

                        <!-- QR Code Placeholder -->
                        <div class="bg-white rounded-lg p-8 mb-6">
                            <div class="aspect-square max-w-xs mx-auto bg-neutral-200 rounded flex items-center justify-center">
                                <div class="text-center text-neutral-600">
                                    <svg class="w-32 h-32 mx-auto mb-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z"/>
                                    </svg>
                                    <p class="text-sm">QR Code PIX</p>
                                </div>
                            </div>
                        </div>

                        <!-- PIX Code -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-neutral-400 mb-2">Código PIX</label>
                            <div class="flex gap-2">
                                <input 
                                    type="text" 
                                    readonly 
                                    value="{{ $pixCode }}"
                                    id="pix-code"
                                    class="flex-1 px-4 py-3 bg-neutral-900 border border-neutral-800 rounded-lg text-neutral-400 font-mono text-sm"
                                >
                                <button 
                                    onclick="copyPixCode()"
                                    class="px-6 py-3 bg-cyan-950/60 hover:bg-cyan-900/60 border border-cyan-900 hover:border-cyan-700 text-cyan-400 hover:text-cyan-300 rounded-lg transition font-medium whitespace-nowrap">
                                    Copiar
                                </button>
                            </div>
                            <p class="mt-2 text-xs text-neutral-600">Copie e cole no app do seu banco</p>
                        </div>

                        <!-- Instructions -->
                        <div class="bg-neutral-900/50 border border-neutral-800 rounded-lg p-6">
                            <h4 class="font-semibold text-neutral-300 mb-3">Como pagar:</h4>
                            <ol class="space-y-2 text-sm text-neutral-400">
                                <li class="flex gap-2">
                                    <span class="text-cyan-500">1.</span>
                                    <span>Abra o app do seu banco</span>
                                </li>
                                <li class="flex gap-2">
                                    <span class="text-cyan-500">2.</span>
                                    <span>Escolha pagar via PIX</span>
                                </li>
                                <li class="flex gap-2">
                                    <span class="text-cyan-500">3.</span>
                                    <span>Escaneie o QR Code ou cole o código copiado</span>
                                </li>
                                <li class="flex gap-2">
                                    <span class="text-cyan-500">4.</span>
                                    <span>Confirme o pagamento de R$ {{ number_format($purchase->amount, 2, ',', '.') }}</span>
                                </li>
                            </ol>
                        </div>

                        <!-- Simulate Payment (DEV) -->
                        <form method="POST" action="{{ route('purchase.complete', $purchase->id) }}" class="mt-6">
                            @csrf
                            <button type="submit" class="w-full px-6 py-3 bg-green-950/60 hover:bg-green-900/60 border border-green-900 hover:border-green-700 text-green-400 hover:text-green-300 rounded-lg transition font-medium">
                                [DEV] Simular Pagamento Aprovado
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyPixCode() {
            const pixCodeInput = document.getElementById('pix-code');
            pixCodeInput.select();
            document.execCommand('copy');
            
            const button = event.target;
            const originalText = button.textContent;
            button.textContent = 'Copiado!';
            button.classList.add('bg-green-950/60', 'border-green-900', 'text-green-400');
            button.classList.remove('bg-cyan-950/60', 'border-cyan-900', 'text-cyan-400');
            
            setTimeout(() => {
                button.textContent = originalText;
                button.classList.remove('bg-green-950/60', 'border-green-900', 'text-green-400');
                button.classList.add('bg-cyan-950/60', 'border-cyan-900', 'text-cyan-400');
            }, 2000);
        }
    </script>
</x-app-layout>
