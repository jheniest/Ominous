<x-guest-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Success Message -->
        <div class="bg-green-950/50 border border-green-800 rounded-lg p-6 mb-8">
            <div class="flex items-center gap-4">
                <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h1 class="text-2xl font-bold text-green-400 mb-1">Pagamento Confirmado!</h1>
                    <p class="text-neutral-400">Seus convites foram gerados com sucesso.</p>
                </div>
            </div>
        </div>

        <!-- Purchase Details -->
        <div class="bg-neutral-900/80 backdrop-blur-lg border border-neutral-800 rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-neutral-200 mb-4">Detalhes da Compra</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-neutral-500">Nome:</span>
                    <p class="text-neutral-200 font-medium">{{ $purchase->guest_name }}</p>
                </div>
                <div>
                    <span class="text-neutral-500">Email:</span>
                    <p class="text-neutral-200 font-medium">{{ $purchase->guest_email }}</p>
                </div>
                <div>
                    <span class="text-neutral-500">Quantidade:</span>
                    <p class="text-neutral-200 font-medium">{{ $purchase->quantity }} convite(s)</p>
                </div>
                <div>
                    <span class="text-neutral-500">Valor Total:</span>
                    <p class="text-neutral-200 font-medium">R$ {{ number_format($purchase->total_price, 2, ',', '.') }}</p>
                </div>
                <div>
                    <span class="text-neutral-500">Método de Pagamento:</span>
                    <p class="text-neutral-200 font-medium">{{ strtoupper($purchase->payment_method) }}</p>
                </div>
                <div>
                    <span class="text-neutral-500">Data:</span>
                    <p class="text-neutral-200 font-medium">{{ $purchase->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Invite Codes -->
        <div class="bg-neutral-900/80 backdrop-blur-lg border border-neutral-800 rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-neutral-200 mb-2">Seus Códigos de Convite</h2>
            <p class="text-neutral-400 text-sm mb-6">Guarde estes códigos em local seguro. Você precisará deles para criar sua conta.</p>
            
            <div class="space-y-3">
                @foreach($purchase->invites as $invite)
                <div class="flex items-center gap-3 bg-neutral-950/80 border border-neutral-700 rounded-lg p-4">
                    <code class="flex-1 text-green-400 font-mono text-lg font-bold">{{ $invite->code }}</code>
                    <button onclick="copyInviteCode('{{ $invite->code }}')" 
                            class="px-4 py-2 bg-neutral-800 hover:bg-neutral-700 text-neutral-300 rounded-lg transition">
                        Copiar
                    </button>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Important Information -->
        <div class="bg-yellow-950/30 border border-yellow-800/50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-yellow-500 mb-3 flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Informações Importantes
            </h3>
            <ul class="space-y-2 text-neutral-300 text-sm">
                <li class="flex items-start gap-2">
                    <span class="text-yellow-500 mt-1">•</span>
                    <span>Cada código pode ser usado apenas uma vez para criar uma conta.</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-yellow-500 mt-1">•</span>
                    <span>Os códigos não expiram, mas recomendamos usá-los o quanto antes.</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-yellow-500 mt-1">•</span>
                    <span>Uma confirmação foi enviada para o email <strong>{{ $purchase->guest_email }}</strong></span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-yellow-500 mt-1">•</span>
                    <span>Se você compartilhar estes códigos, outras pessoas poderão criar contas.</span>
                </li>
            </ul>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4">
            <a href="{{ route('invite.validate') }}" class="flex-1 text-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition">
                Usar Meu Convite Agora
            </a>
            <a href="{{ route('home') }}" class="flex-1 text-center px-6 py-3 bg-neutral-800 hover:bg-neutral-700 text-neutral-300 rounded-lg font-semibold transition">
                Voltar ao Início
            </a>
        </div>
    </div>

    <script>
        function copyInviteCode(code) {
            navigator.clipboard.writeText(code).then(() => {
                // Visual feedback
                event.target.textContent = 'Copiado!';
                event.target.classList.add('bg-green-600', 'text-white');
                event.target.classList.remove('bg-neutral-800', 'text-neutral-300');
                
                setTimeout(() => {
                    event.target.textContent = 'Copiar';
                    event.target.classList.remove('bg-green-600', 'text-white');
                    event.target.classList.add('bg-neutral-800', 'text-neutral-300');
                }, 2000);
            });
        }
    </script>
</x-guest-layout>
