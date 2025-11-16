<x-app-layout>
    <div class="min-h-screen bg-black text-gray-100 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-red-500">Meus Convites</h1>
                <a href="{{ route('profile.show', Auth::user()) }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-semibold transition-colors">
                    ← Voltar ao Perfil
                </a>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
            <div class="mb-6 bg-green-950/50 border border-green-800 text-green-400 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 bg-red-950/50 border border-red-800 text-red-400 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
            @endif

            <!-- Create Invite -->
            <div class="bg-gray-900 rounded-lg border border-red-900/30 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-white">Criar Novo Convite</h2>
                    @php
                        $activeCount = Auth::user()->createdInvites()->where('status', '!=', 'consumed')->count();
                    @endphp
                    @if(Auth::user()->is_admin)
                        <p class="text-green-400 text-sm font-semibold">Admin: Convites Ilimitados ∞</p>
                    @else
                        <p class="text-sm {{ $activeCount >= 3 ? 'text-yellow-400' : 'text-gray-400' }}">
                            <span class="font-bold">{{ $activeCount }}/3</span> convites ativos
                        </p>
                    @endif
                </div>

                @if(Auth::user()->is_admin || $activeCount < 3)
                <form method="POST" action="{{ route('profile.invites.store') }}" class="space-y-4">
                    @csrf
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="expires_at" class="block text-sm font-semibold text-gray-300 mb-2">Data de Expiração</label>
                            <input type="datetime-local" id="expires_at" name="expires_at" class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-red-500 @error('expires_at') border-red-500 @enderror">
                            <p class="text-xs text-gray-500 mt-1">Deixe em branco para nunca expirar</p>
                            @error('expires_at')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="max_uses" class="block text-sm font-semibold text-gray-300 mb-2">Máximo de Usos</label>
                            <input type="number" id="max_uses" name="max_uses" min="1" max="100" value="1" class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-red-500 @error('max_uses') border-red-500 @enderror">
                            <p class="text-xs text-gray-500 mt-1">Quantas pessoas podem usar este convite</p>
                            @error('max_uses')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="w-full px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold transition-colors">
                        + Criar Convite
                    </button>
                    @if(!Auth::user()->is_admin)
                    <p class="text-xs text-gray-500 text-center">Você pode criar até 3 convites ativos simultaneamente.</p>
                    @endif
                </form>
                @else
                <div class="bg-yellow-900/20 border border-yellow-700 rounded-lg p-4">
                    <p class="text-yellow-400 text-sm">
                        <strong>Limite atingido:</strong> Você atingiu o limite de 3 convites ativos. Delete um convite existente para criar um novo.
                    </p>
                </div>
                @endif
            </div>

            <!-- Invites List -->
            @php
                $invites = Auth::user()->createdInvites()->with('redeemedBy')->latest()->get();
            @endphp

            @if($invites->count() > 0)
            <div class="space-y-4">
                <h2 class="text-xl font-bold text-white mb-4">Seus Convites ({{ $invites->count() }})</h2>
                
                @foreach($invites as $invite)
                <div class="bg-gray-900 rounded-lg border border-red-900/30 p-6 hover:border-red-800 transition">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <code class="text-lg bg-black px-4 py-2 rounded text-red-400 font-mono font-bold">{{ $invite->code }}</code>
                                @if($invite->status === 'active')
                                <span class="px-3 py-1 bg-green-900/30 text-green-400 text-xs font-semibold rounded-full border border-green-700">✓ Ativo</span>
                                @elseif($invite->status === 'consumed')
                                <span class="px-3 py-1 bg-blue-900/30 text-blue-400 text-xs font-semibold rounded-full border border-blue-700">✓ Usado</span>
                                @else
                                <span class="px-3 py-1 bg-gray-700 text-gray-300 text-xs font-semibold rounded-full border border-gray-600">✗ Expirado</span>
                                @endif
                            </div>
                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-400">
                                @if($invite->status === 'consumed' && $invite->redeemedBy->count() > 0)
                                    <span>👤 Usado por <strong class="text-gray-300">{{ $invite->redeemedBy->first()->name }}</strong></span>
                                    <span class="text-gray-600">•</span>
                                    <span>📅 {{ $invite->redeemed_at?->diffForHumans() }}</span>
                                @else
                                    <span>📅 Criado {{ $invite->created_at->diffForHumans() }}</span>
                                    @if($invite->expires_at)
                                        <span class="text-gray-600">•</span>
                                        <span>⏰ Expira {{ $invite->expires_at->diffForHumans() }}</span>
                                    @endif
                                    @if($invite->max_uses > 1)
                                        <span class="text-gray-600">•</span>
                                        <span>🔢 Máx: {{ $invite->max_uses }} usos</span>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($invite->status === 'active')
                            <button onclick="copyToClipboard('{{ $invite->code }}')" class="px-4 py-2 bg-neutral-800 hover:bg-neutral-700 text-neutral-300 rounded-lg text-sm font-semibold transition">
                                📋 Copiar
                            </button>
                            <form method="POST" action="{{ route('profile.invites.destroy', $invite->id) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-900/30 hover:bg-red-900/50 text-red-400 rounded-lg text-sm font-semibold transition border border-red-900" onclick="return confirm('Deletar este convite?')">
                                    🗑️ Deletar
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="bg-gray-900 rounded-lg border border-red-900/30 p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <p class="text-gray-400 mb-2">Você ainda não criou nenhum convite.</p>
                <p class="text-gray-500 text-sm">Crie seu primeiro convite para convidar novos usuários!</p>
            </div>
            @endif
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                // Visual feedback
                event.target.textContent = '✓ Copiado!';
                event.target.classList.add('bg-green-600', 'text-white');
                event.target.classList.remove('bg-neutral-800', 'text-neutral-300');
                
                setTimeout(() => {
                    event.target.textContent = '📋 Copiar';
                    event.target.classList.remove('bg-green-600', 'text-white');
                    event.target.classList.add('bg-neutral-800', 'text-neutral-300');
                }, 2000);
            }).catch(err => {
                alert('Erro ao copiar: ' + err);
            });
        }
    </script>
</x-app-layout>
