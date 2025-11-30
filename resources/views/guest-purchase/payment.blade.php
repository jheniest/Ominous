@extends('layouts.guest')

@section('title', 'Pagamento - Atrocidades')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($paymentData['method'] === 'pix')
        <!-- PIX Payment -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-950/40 border border-green-800/50 mb-3">
                <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-neutral-200">Pagamento via PIX</h1>
            <p class="text-neutral-500 text-sm">Escaneie o QR Code ou copie o código</p>
        </div>

        <div class="bg-neutral-900/60 backdrop-blur-xl border border-neutral-800/50 rounded-xl p-6 mb-5">
            <div class="bg-white rounded-lg p-6 mb-5">
                <img src="{{ $paymentData['qr_code_url'] }}" alt="QR Code PIX" class="max-w-[200px] mx-auto">
            </div>

            <div class="mb-4">
                <label class="block text-xs text-neutral-500 mb-1.5">Código PIX</label>
                <div class="flex gap-2">
                    <input type="text" readonly value="{{ $paymentData['pix_code'] }}" id="pix-code" 
                        class="flex-1 px-3 py-2 bg-neutral-800 border border-neutral-700 rounded-lg text-neutral-400 font-mono text-xs">
                    <button onclick="copyCode('pix-code')" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition text-sm font-medium">
                        Copiar
                    </button>
                </div>
            </div>

            <div class="bg-green-950/30 border border-green-800/30 rounded-lg p-3 text-sm text-green-400">
                ⏱️ Válido por 30 minutos • Expira às {{ $paymentData['expires_at']->format('H:i') }}
            </div>
        </div>

        @elseif($paymentData['method'] === 'boleto')
        <!-- Boleto Payment -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-950/40 border border-blue-800/50 mb-3">
                <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4 4h16v2H4V4zm0 4h16v2H4V8zm0 4h16v2H4v-2zm0 4h10v2H4v-2z"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-neutral-200">Boleto Bancário</h1>
            <p class="text-neutral-500 text-sm">Pague em qualquer banco ou lotérica</p>
        </div>

        <div class="bg-neutral-900/60 backdrop-blur-xl border border-neutral-800/50 rounded-xl p-6 mb-5">
            <div class="mb-4">
                <label class="block text-xs text-neutral-500 mb-1.5">Código de Barras</label>
                <div class="flex gap-2">
                    <input type="text" readonly value="{{ $paymentData['barcode'] }}" id="boleto-code" 
                        class="flex-1 px-3 py-2 bg-neutral-800 border border-neutral-700 rounded-lg text-neutral-400 font-mono text-xs">
                    <button onclick="copyCode('boleto-code')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition text-sm font-medium">
                        Copiar
                    </button>
                </div>
            </div>

            <a href="{{ $paymentData['boleto_url'] }}" target="_blank" class="block w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium text-center text-sm mb-4">
                Baixar Boleto PDF
            </a>

            <div class="bg-blue-950/30 border border-blue-800/30 rounded-lg p-3 text-sm text-blue-400">
                ⏱️ Válido por 3 dias • Vence em {{ $paymentData['expires_at']->format('d/m/Y') }}
            </div>
        </div>

        @elseif($paymentData['method'] === 'crypto')
        <!-- Crypto Payment -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-orange-950/40 border border-orange-800/50 mb-3">
                <svg class="w-6 h-6 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-neutral-200">Bitcoin</h1>
            <p class="text-neutral-500 text-sm">Envie {{ $paymentData['amount_crypto'] }} BTC</p>
        </div>

        <div class="bg-neutral-900/60 backdrop-blur-xl border border-neutral-800/50 rounded-xl p-6 mb-5">
            <div class="bg-white rounded-lg p-6 mb-5">
                <img src="{{ $paymentData['qr_code_url'] }}" alt="QR Code Bitcoin" class="max-w-[200px] mx-auto">
            </div>

            <div class="mb-4">
                <label class="block text-xs text-neutral-500 mb-1.5">Endereço da Carteira</label>
                <div class="flex gap-2">
                    <input type="text" readonly value="{{ $paymentData['wallet_address'] }}" id="crypto-address" 
                        class="flex-1 px-3 py-2 bg-neutral-800 border border-neutral-700 rounded-lg text-neutral-400 font-mono text-[10px]">
                    <button onclick="copyCode('crypto-address')" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition text-sm font-medium">
                        Copiar
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-neutral-800/50 rounded-lg p-3 text-center">
                    <p class="text-xs text-neutral-500 mb-0.5">BRL</p>
                    <p class="font-bold text-neutral-200">R$ {{ number_format($paymentData['amount_brl'], 2, ',', '.') }}</p>
                </div>
                <div class="bg-neutral-800/50 rounded-lg p-3 text-center">
                    <p class="text-xs text-neutral-500 mb-0.5">BTC</p>
                    <p class="font-bold text-orange-400">{{ $paymentData['amount_crypto'] }}</p>
                </div>
            </div>

            <div class="bg-orange-950/30 border border-orange-800/30 rounded-lg p-3 text-sm text-orange-400">
                ⚠️ Envie exatamente o valor • Confirmação em até 1 hora
            </div>
        </div>

        @elseif($paymentData['method'] === 'mercado_pago')
        <!-- Mercado Pago -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-cyan-950/40 border border-cyan-800/50 mb-3">
                <svg class="w-6 h-6 text-cyan-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-neutral-200">Mercado Pago</h1>
            <p class="text-neutral-500 text-sm">Redirecionando para pagamento</p>
        </div>

        <div class="bg-neutral-900/60 backdrop-blur-xl border border-neutral-800/50 rounded-xl p-6 mb-5 text-center">
            <p class="text-neutral-400 mb-4 text-sm">Valor: <span class="text-xl font-bold text-cyan-400">R$ {{ number_format($paymentData['amount'], 2, ',', '.') }}</span></p>
            
            <a href="{{ $paymentData['payment_url'] }}" target="_blank" class="inline-block px-6 py-3 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg transition font-medium mb-3">
                Ir para Mercado Pago
            </a>

            <p class="text-xs text-neutral-500">ID: {{ $paymentData['payment_id'] }}</p>
        </div>
        @endif

        <!-- Simulate Payment Button -->
        <form method="POST" action="{{ route('guest.purchase.confirm', $purchase->id) }}" class="mb-5">
            @csrf
            <button type="submit" class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-bold text-sm">
                🎭 SIMULAR PAGAMENTO (DEV)
            </button>
        </form>

        <div class="text-center">
            <a href="{{ route('guest.purchase.index') }}" class="text-neutral-500 hover:text-neutral-400 transition text-sm">
                Voltar
            </a>
        </div>
    </div>
</div>

<script>
    function copyCode(elementId) {
        const input = document.getElementById(elementId);
        input.select();
        document.execCommand('copy');
        alert('Copiado!');
    }
</script>
@endsection
