<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-neutral-200">Dashboard</h1>
                <p class="mt-1 text-neutral-500">Bem-vindo, {{ auth()->user()->name }}</p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-6">
                    <div class="text-sm font-medium text-neutral-500">Convites Totais</div>
                    <div class="mt-2 text-3xl font-bold text-neutral-200">{{ $stats['total_invites'] }}</div>
                </div>

                <div class="bg-neutral-950/60 backdrop-blur-lg border border-green-900/30 rounded-lg p-6">
                    <div class="text-sm font-medium text-neutral-500">Convites Ativos</div>
                    <div class="mt-2 text-3xl font-bold text-green-500">{{ $stats['active_invites'] }}</div>
                </div>

                <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-6">
                    <div class="text-sm font-medium text-neutral-500">Usuários Convidados</div>
                    <div class="mt-2 text-3xl font-bold text-neutral-200">{{ $stats['total_invited'] }}</div>
                </div>
            </div>

            <!-- Recent Invites -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-neutral-200">Convites Recentes</h3>
                        <a href="{{ route('dashboard.invites.index') }}" class="text-sm text-red-700 hover:text-red-500">
                            Ver todos →
                        </a>
                    </div>
                    
                    @if($recentInvites->isEmpty())
                        <p class="text-neutral-500 text-sm">Nenhum convite criado ainda.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($recentInvites as $invite)
                                <div class="flex justify-between items-center p-3 bg-neutral-900/50 rounded border border-neutral-800">
                                    <div>
                                        <code class="text-red-400 font-mono">{{ $invite->code }}</code>
                                        <div class="text-xs text-neutral-600 mt-1">
                                            {{ $invite->current_uses }}/{{ $invite->max_uses }} usos
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded 
                                        @if($invite->status === 'active') bg-green-950/40 text-green-500 border border-green-900/50
                                        @elseif($invite->status === 'consumed') bg-neutral-800/40 text-neutral-500 border border-neutral-700/50
                                        @elseif($invite->status === 'expired') bg-amber-950/40 text-amber-500 border border-amber-900/50
                                        @else bg-red-950/40 text-red-500 border border-red-900/50
                                        @endif">
                                        {{ ucfirst($invite->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-neutral-200 mb-4">Usuários Convidados</h3>
                    
                    @if($recentInvited->isEmpty())
                        <p class="text-neutral-500 text-sm">Nenhum usuário convidado ainda.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($recentInvited as $user)
                                <div class="flex justify-between items-center p-3 bg-neutral-900/50 rounded border border-neutral-800">
                                    <div>
                                        <div class="text-neutral-300">{{ $user->name }}</div>
                                        <div class="text-xs text-neutral-600 mt-1">{{ $user->email }}</div>
                                    </div>
                                    <div class="text-xs text-neutral-500">
                                        {{ $user->invited_at->diffForHumans() }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
