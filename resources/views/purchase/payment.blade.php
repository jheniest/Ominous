<x-app-layout>
    <x-slot name="title">Pagamento</x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($paymentData['method'] === 'pix')
            <!-- PIX Payment -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-950/40 border border-green-900/50 mb-4">
                    <svg class="w-8 h-8 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-neutral-200">Pagamento via PIX</h1>
                <p class="mt-2 text-neutral-400">Escaneie o QR Code ou copie o código</p>
            </div>

            <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-8 mb-6">
                <!-- QR Code -->
                <div class="bg-white rounded-lg p-8 mb-6">
                    <img src="{{ $paymentData['qr_code_url'] }}" alt="QR Code PIX" class="max-w-xs mx-auto">
                </div>

                <!-- PIX Code -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-neutral-400 mb-2">Código PIX</label>
                    <div class="flex gap-2">
                        <input type="text" readonly value="{{ $paymentData['pix_code'] }}" id="pix-code" class="flex-1 px-4 py-3 bg-neutral-900 border border-neutral-800 rounded-lg text-neutral-400 font-mono text-sm">
                        <button onclick="copyCode('pix-code', 'PIX')" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-medium whitespace-nowrap">
                            Copiar
                        </button>
                    </div>
                </div>

                <div class="bg-green-950/20 border border-green-900/50 rounded-lg p-4 mb-6">
                    <p class="text-green-400 text-sm">⏱️ Válido por 30 minutos • Expira às {{ $paymentData['expires_at']->format('H:i') }}</p>
                </div>

                <div class="bg-neutral-900/50 border border-neutral-800 rounded-lg p-6">
                    <h4 class="font-semibold text-neutral-300 mb-3">Como pagar:</h4>
                    <ol class="space-y-2 text-sm text-neutral-400">
                        <li class="flex gap-2"><span class="text-green-500">1.</span> Abra o app do seu banco</li>
                        <li class="flex gap-2"><span class="text-green-500">2.</span> Escolha pagar via PIX</li>
                        <li class="flex gap-2"><span class="text-green-500">3.</span> Escaneie o QR Code ou cole o código</li>
                        <li class="flex gap-2"><span class="text-green-500">4.</span> Confirme o pagamento</li>
                        <li class="flex gap-2"><span class="text-green-500">5.</span> Aguarde a confirmação automática</li>
                    </ol>
                </div>
            </div>

            @elseif($paymentData['method'] === 'boleto')
            <!-- Boleto Payment -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-950/40 border border-blue-900/50 mb-4">
                    <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4 4h16v2H4V4zm0 4h16v2H4V8zm0 4h16v2H4v-2zm0 4h10v2H4v-2z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-neutral-200">Boleto Bancário</h1>
                <p class="mt-2 text-neutral-400">Pague em qualquer banco ou lotérica</p>
            </div>

            <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-8 mb-6">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-neutral-400 mb-2">Código de Barras</label>
                    <div class="flex gap-2">
                        <input type="text" readonly value="{{ $paymentData['barcode'] }}" id="boleto-code" class="flex-1 px-4 py-3 bg-neutral-900 border border-neutral-800 rounded-lg text-neutral-400 font-mono text-sm">
                        <button onclick="copyCode('boleto-code', 'Boleto')" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium whitespace-nowrap">
                            Copiar
                        </button>
                    </div>
                </div>

                <a href="{{ $paymentData['boleto_url'] }}" target="_blank" class="block w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-semibold text-center mb-6">
                    Baixar Boleto PDF
                </a>

                <div class="bg-blue-950/20 border border-blue-900/50 rounded-lg p-4">
                    <p class="text-blue-400 text-sm">⏱️ Válido por 3 dias • Vence em {{ $paymentData['expires_at']->format('d/m/Y') }}</p>
                </div>
            </div>

            @elseif($paymentData['method'] === 'crypto')
            <!-- Crypto Payment -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-orange-950/40 border border-orange-900/50 mb-4">
                    <svg class="w-8 h-8 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-neutral-200">Pagamento em {{ $paymentData['crypto_type'] }}</h1>
                <p class="mt-2 text-neutral-400">Envie exatamente {{ $paymentData['amount_crypto'] }} BTC</p>
            </div>

            <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-8 mb-6">
                <div class="bg-white rounded-lg p-8 mb-6">
                    <img src="{{ $paymentData['qr_code_url'] }}" alt="QR Code Bitcoin" class="max-w-xs mx-auto">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-neutral-400 mb-2">Endereço da Carteira</label>
                    <div class="flex gap-2">
                        <input type="text" readonly value="{{ $paymentData['wallet_address'] }}" id="crypto-address" class="flex-1 px-4 py-3 bg-neutral-900 border border-neutral-800 rounded-lg text-neutral-400 font-mono text-xs">
                        <button onclick="copyCode('crypto-address', 'Endereço')" class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition font-medium whitespace-nowrap">
                            Copiar
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-neutral-900/50 rounded-lg p-4">
                        <p class="text-xs text-neutral-500 mb-1">Valor em BRL</p>
                        <p class="text-lg font-bold text-neutral-200">R$ {{ number_format($paymentData['amount_brl'], 2, ',', '.') }}</p>
                    </div>
                    <div class="bg-neutral-900/50 rounded-lg p-4">
                        <p class="text-xs text-neutral-500 mb-1">Valor em BTC</p>
                        <p class="text-lg font-bold text-orange-400">{{ $paymentData['amount_crypto'] }} BTC</p>
                    </div>
                </div>

                <div class="bg-orange-950/20 border border-orange-900/50 rounded-lg p-4">
                    <p class="text-orange-400 text-sm">⚠️ Envie exatamente o valor especificado • Confirmação em até 1 hora</p>
                </div>
            </div>

            @elseif($paymentData['method'] === 'mercado_pago')
            <!-- Mercado Pago Payment -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-cyan-950/40 border border-cyan-900/50 mb-4">
                    <svg class="w-8 h-8 text-cyan-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-neutral-200">Mercado Pago</h1>
                <p class="mt-2 text-neutral-400">Você será redirecionado para finalizar o pagamento</p>
            </div>

            <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-8 mb-6 text-center">
                <p class="text-neutral-400 mb-6">Valor: <span class="text-2xl font-bold text-cyan-400">R$ {{ number_format($paymentData['amount'], 2, ',', '.') }}</span></p>
                
                <a href="{{ $paymentData['payment_url'] }}" target="_blank" class="inline-block px-8 py-4 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg transition font-semibold text-lg mb-4">
                    Ir para Mercado Pago
                </a>

                <p class="text-sm text-neutral-500">ID do Pagamento: {{ $paymentData['payment_id'] }}</p>
            </div>
            @endif

            <!-- Simulate Payment Button -->
            <form method="POST" action="{{ route('purchase.confirm', $purchase->id) }}" class="mb-6">
                @csrf
                <button type="submit" class="w-full px-6 py-4 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-bold text-lg">
                    🎭 SIMULAR PAGAMENTO (DESENVOLVIMENTO)
                </button>
            </form>

            <div class="text-center">
                <a href="{{ route('dashboard') }}" class="text-neutral-400 hover:text-neutral-200 transition text-sm">
                    Voltar ao Dashboard
                </a>
            </div>
        </div>
    </div>

    <script>
        function copyCode(elementId, type) {
            const input = document.getElementById(elementId);
            input.select();
            document.execCommand('copy');
            alert(type + ' copiado para a área de transferência!');
        }
    </script>
</x-app-layout>
