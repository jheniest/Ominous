<x-app-layout>
    <x-slot name="title">Log de Atividades</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-red-500">Log de Atividades</h1>
                    <p class="mt-1 text-neutral-500">Registro de todas as ações do sistema</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-neutral-900/60 border border-neutral-800 text-neutral-400 rounded-lg hover:border-neutral-700 transition text-sm">
                    ← Voltar
                </a>
            </div>

            <!-- Filters -->
            <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('admin.activity') }}" class="flex gap-4">
                    <select 
                        name="type"
                        class="flex-1 px-4 py-2 bg-neutral-900 border border-neutral-800 rounded-lg text-neutral-200 focus:outline-none focus:ring-2 focus:ring-red-900/50">
                        <option value="">Todos os tipos</option>
                        <option value="user_registered" {{ request('type') === 'user_registered' ? 'selected' : '' }}>Registro de Usuário</option>
                        <option value="invite_created" {{ request('type') === 'invite_created' ? 'selected' : '' }}>Convite Criado</option>
                        <option value="invite_redeemed" {{ request('type') === 'invite_redeemed' ? 'selected' : '' }}>Convite Usado</option>
                        <option value="purchase_created" {{ request('type') === 'purchase_created' ? 'selected' : '' }}>Compra Iniciada</option>
                        <option value="purchase_completed" {{ request('type') === 'purchase_completed' ? 'selected' : '' }}>Compra Completada</option>
                        <option value="user_suspended" {{ request('type') === 'user_suspended' ? 'selected' : '' }}>Usuário Suspenso</option>
                        <option value="user_unsuspended" {{ request('type') === 'user_unsuspended' ? 'selected' : '' }}>Usuário Reativado</option>
                    </select>
                    <input 
                        type="date" 
                        name="date" 
                        value="{{ request('date') }}"
                        class="px-4 py-2 bg-neutral-900 border border-neutral-800 rounded-lg text-neutral-200 focus:outline-none focus:ring-2 focus:ring-red-900/50"
                    >
                    <button type="submit" class="px-6 py-2 bg-red-950/60 border border-red-900 text-red-400 rounded-lg hover:bg-red-900/60 transition">
                        Filtrar
                    </button>
                </form>
            </div>

            <!-- Activity Log -->
            <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg overflow-hidden">
                <div class="divide-y divide-neutral-800">
                    @foreach($activities as $activity)
                        <div class="p-6 hover:bg-neutral-900/30 transition">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center
                                    @if($activity->type === 'user_registered') bg-green-950/40
                                    @elseif($activity->type === 'invite_created') bg-blue-950/40
                                    @elseif($activity->type === 'invite_redeemed') bg-purple-950/40
                                    @elseif($activity->type === 'purchase_created') bg-amber-950/40
                                    @elseif($activity->type === 'purchase_completed') bg-cyan-950/40
                                    @elseif($activity->type === 'user_suspended') bg-red-950/40
                                    @elseif($activity->type === 'user_unsuspended') bg-green-950/40
                                    @else bg-neutral-900
                                    @endif">
                                    @if($activity->type === 'user_registered')
                                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6z"></path>
                                        </svg>
                                    @elseif(str_contains($activity->type, 'invite'))
                                        <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                        </svg>
                                    @elseif(str_contains($activity->type, 'purchase'))
                                        <svg class="w-5 h-5 text-cyan-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                                        </svg>
                                    @elseif($activity->type === 'user_suspended')
                                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-neutral-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="text-neutral-200 font-medium">{{ $activity->description }}</p>
                                            <div class="mt-1 flex items-center gap-4 text-xs text-neutral-600">
                                                <span>{{ $activity->created_at->format('d/m/Y H:i:s') }}</span>
                                                @if($activity->user)
                                                    <span>•</span>
                                                    <a href="{{ route('admin.users.show', $activity->user->id) }}" class="text-cyan-500 hover:text-cyan-400">
                                                        {{ $activity->user->name }}
                                                    </a>
                                                @endif
                                                @if($activity->ip_address)
                                                    <span>•</span>
                                                    <code class="text-neutral-500">{{ $activity->ip_address }}</code>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <span class="px-2 py-1 text-xs rounded bg-neutral-800/40 text-neutral-400 border border-neutral-700/50 whitespace-nowrap">
                                            {{ str_replace('_', ' ', ucfirst($activity->type)) }}
                                        </span>
                                    </div>
                                    
                                    @if($activity->metadata)
                                        <details class="mt-3">
                                            <summary class="text-xs text-neutral-500 cursor-pointer hover:text-neutral-400">Ver detalhes</summary>
                                            <pre class="mt-2 p-3 bg-neutral-900/50 rounded text-xs text-neutral-400 overflow-x-auto">{{ json_encode($activity->metadata, JSON_PRETTY_PRINT) }}</pre>
                                        </details>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-4">
                {{ $activities->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
