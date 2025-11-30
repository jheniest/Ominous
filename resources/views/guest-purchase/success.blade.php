@extends('layouts.guest')

@section('title', 'Compra Confirmada - Atrocidades')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Success Message -->
    <div class="bg-green-950/40 border border-green-800/50 rounded-xl p-5 mb-6">
        <div class="flex items-center gap-3">
            <svg class="w-8 h-8 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h1 class="text-lg font-bold text-green-400">Pagamento Confirmado!</h1>
                <p class="text-neutral-400 text-sm">Seus convites foram gerados.</p>
            </div>
        </div>
    </div>

    <!-- Purchase Details -->
    <div class="bg-neutral-900/60 backdrop-blur-xl border border-neutral-800/50 rounded-xl p-5 mb-5">
        <h2 class="text-sm font-semibold text-neutral-300 mb-3">Detalhes da Compra</h2>
        <div class="grid grid-cols-2 gap-3 text-sm">
            <div>
                <span class="text-neutral-500 text-xs">Nome</span>
                <p class="text-neutral-200">{{ $purchase->guest_name }}</p>
            </div>
            <div>
                <span class="text-neutral-500 text-xs">Email</span>
                <p class="text-neutral-200">{{ $purchase->guest_email }}</p>
            </div>
            <div>
                <span class="text-neutral-500 text-xs">Quantidade</span>
                <p class="text-neutral-200">{{ $purchase->quantity }} convite(s)</p>
            </div>
            <div>
                <span class="text-neutral-500 text-xs">Total</span>
                <p class="text-neutral-200">R$ {{ number_format($purchase->total_price, 2, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Invite Codes -->
    <div class="bg-neutral-900/60 backdrop-blur-xl border border-neutral-800/50 rounded-xl p-5 mb-5">
        <h2 class="text-sm font-semibold text-neutral-300 mb-1">Seus Códigos de Convite</h2>
        <p class="text-neutral-500 text-xs mb-4">Guarde em local seguro.</p>
        
        <div class="space-y-2">
            @foreach($purchase->invites as $invite)
            <div class="flex items-center gap-2 bg-neutral-950/80 border border-neutral-700 rounded-lg p-3">
                <code class="flex-1 text-green-400 font-mono font-bold">{{ $invite->code }}</code>
                <button onclick="copyInviteCode('{{ $invite->code }}', this)" 
                        class="px-3 py-1.5 bg-neutral-800 hover:bg-neutral-700 text-neutral-300 rounded-lg transition text-xs">
                    Copiar
                </button>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Warning -->
    <div class="bg-yellow-950/30 border border-yellow-800/30 rounded-xl p-4 mb-6">
        <h3 class="text-sm font-semibold text-yellow-500 mb-2 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            Importante
        </h3>
        <ul class="space-y-1 text-neutral-400 text-xs">
            <li>• Cada código pode ser usado apenas uma vez</li>
            <li>• Códigos não expiram</li>
            <li>• Confirmação enviada para <strong>{{ $purchase->guest_email }}</strong></li>
        </ul>
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-3">
        <a href="{{ route('invite.validate') }}" class="flex-1 text-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition text-sm">
            Usar Convite Agora
        </a>
        <a href="{{ route('home') }}" class="flex-1 text-center px-4 py-2.5 bg-neutral-800 hover:bg-neutral-700 text-neutral-300 rounded-lg font-medium transition text-sm">
            Início
        </a>
    </div>
</div>

<script>
    function copyInviteCode(code, btn) {
        navigator.clipboard.writeText(code).then(() => {
            btn.textContent = '✓';
            btn.classList.add('bg-green-600', 'text-white');
            setTimeout(() => {
                btn.textContent = 'Copiar';
                btn.classList.remove('bg-green-600', 'text-white');
            }, 2000);
        });
    }
</script>
@endsection
