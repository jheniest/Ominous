<x-app-layout>
    <x-slot name="title">Compra Concluída</x-slot>

    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-2xl w-full">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-green-950/40 border-2 border-green-900/50 mb-6">
                    <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-neutral-200 mb-3">Pagamento Confirmado!</h1>
                <p class="text-lg text-neutral-500">Seus convites estão prontos para uso</p>
            </div>

            <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-8 mb-6">
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-500">{{ $purchase->quantity }}</div>
                        <div class="text-sm text-neutral-500 mt-1">{{ $purchase->quantity == 1 ? 'Convite' : 'Convites' }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-500">R$ {{ number_format($purchase->amount, 2, ',', '.') }}</div>
                        <div class="text-sm text-neutral-500 mt-1">Total Pago</div>
                    </div>
                </div>

                <div class="border-t border-neutral-800 pt-6">
                    <h3 class="text-lg font-semibold text-neutral-200 mb-4">Seus Convites:</h3>
                    <div class="space-y-2">
                        @foreach($invites as $invite)
                            <div class="flex items-center justify-between p-4 bg-neutral-900/50 rounded border border-neutral-800">
                                <code class="text-lg text-red-400 font-mono">{{ $invite->code }}</code>
                                <button 
                                    onclick="copyInviteCode('{{ $invite->code }}')"
                                    class="px-4 py-2 bg-neutral-800/60 hover:bg-neutral-700/60 border border-neutral-700 text-neutral-300 rounded transition text-sm">
                                    Copiar
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('profile.edit') }}" class="flex-1 px-6 py-3 bg-red-950/60 hover:bg-red-900/60 border border-red-900 hover:border-red-700 text-red-400 hover:text-red-300 rounded-lg transition font-medium text-center">
                    Ver Todos os Convites
                </a>
                <a href="{{ route('dashboard') }}" class="flex-1 px-6 py-3 bg-neutral-900/60 hover:bg-neutral-800/60 border border-neutral-800 hover:border-neutral-700 text-neutral-400 hover:text-neutral-300 rounded-lg transition font-medium text-center">
                    Ir para Dashboard
                </a>
            </div>
        </div>
    </div>

    <script>
        function copyInviteCode(code) {
            navigator.clipboard.writeText(code).then(() => {
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = 'Copiado!';
                button.classList.add('bg-green-900/60', 'border-green-800', 'text-green-300');
                button.classList.remove('bg-neutral-800/60', 'border-neutral-700', 'text-neutral-300');
                
                setTimeout(() => {
                    button.textContent = originalText;
                    button.classList.remove('bg-green-900/60', 'border-green-800', 'text-green-300');
                    button.classList.add('bg-neutral-800/60', 'border-neutral-700', 'text-neutral-300');
                }, 2000);
            });
        }
    </script>
</x-app-layout>
