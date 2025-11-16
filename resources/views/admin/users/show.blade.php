<x-app-layout>
    <x-slot name="title">{{ $user->name }}</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-neutral-200">{{ $user->name }}</h1>
                    <p class="mt-1 text-neutral-500">{{ $user->email }}</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-neutral-900/60 border border-neutral-800 text-neutral-400 rounded-lg hover:border-neutral-700 transition text-sm">
                    ← Voltar
                </a>
            </div>

            <!-- User Info & Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2 bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-neutral-200 mb-4">Informações</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between py-2 border-b border-neutral-800">
                            <span class="text-neutral-500">Status</span>
                            <span>
                                @if($user->is_suspended)
                                    <span class="px-2 py-1 text-xs rounded bg-red-950/40 text-red-500 border border-red-900/50">Suspenso</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded bg-green-950/40 text-green-500 border border-green-900/50">Ativo</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-neutral-800">
                            <span class="text-neutral-500">Função</span>
                            <span class="text-neutral-300">{{ $user->is_admin ? 'Administrador' : 'Usuário' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-neutral-800">
                            <span class="text-neutral-500">Convidado por</span>
                            <span class="text-neutral-300">
                                @if($user->invitedBy)
                                    <a href="{{ route('admin.users.show', $user->invitedBy->id) }}" class="text-red-700 hover:text-red-500">
                                        {{ $user->invitedBy->name }}
                                    </a>
                                @else
                                    Sistema
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-neutral-800">
                            <span class="text-neutral-500">Código usado</span>
                            <code class="text-red-400 font-mono">{{ $user->invite_code_used ?? 'N/A' }}</code>
                        </div>
                        <div class="flex justify-between py-2 border-b border-neutral-800">
                            <span class="text-neutral-500">Registro</span>
                            <span class="text-neutral-300">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($user->suspended_at)
                            <div class="flex justify-between py-2 border-b border-neutral-800">
                                <span class="text-neutral-500">Suspenso em</span>
                                <span class="text-neutral-300">{{ $user->suspended_at->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-neutral-200 mb-4">Estatísticas</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <div class="text-neutral-500 text-sm">Convites criados</div>
                            <div class="text-2xl font-bold text-neutral-200">{{ $stats['total_invites'] }}</div>
                        </div>
                        <div>
                            <div class="text-neutral-500 text-sm">Usuários convidados</div>
                            <div class="text-2xl font-bold text-neutral-200">{{ $stats['total_invited'] }}</div>
                        </div>
                        <div>
                            <div class="text-neutral-500 text-sm">Total gasto</div>
                            <div class="text-2xl font-bold text-cyan-400">R$ {{ number_format($stats['total_spent'], 2, ',', '.') }}</div>
                        </div>
                    </div>

                    @if(!$user->is_admin)
                        <div class="mt-6 pt-6 border-t border-neutral-800 space-y-2">
                            @if($user->is_suspended)
                                <form method="POST" action="{{ route('admin.users.unsuspend', $user->id) }}">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2 bg-green-950/60 border border-green-900 text-green-400 rounded-lg hover:bg-green-900/60 transition">
                                        Reativar Usuário
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.users.suspend', $user->id) }}">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2 bg-red-950/60 border border-red-900 text-red-400 rounded-lg hover:bg-red-900/60 transition">
                                        Suspender Usuário
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Invite Tree -->
            @if($inviteTree->isNotEmpty())
                <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-neutral-200 mb-4">Árvore de Convites</h3>
                    
                    <div class="space-y-2">
                        @foreach($inviteTree as $invited)
                            <div class="flex items-center gap-3 p-3 bg-neutral-900/50 rounded border border-neutral-800">
                                <div class="flex-1">
                                    <div class="text-neutral-200">{{ $invited->name }}</div>
                                    <div class="text-xs text-neutral-600">{{ $invited->email }}</div>
                                </div>
                                <div class="text-sm text-neutral-500">
                                    {{ $invited->invited_at->format('d/m/Y') }}
                                </div>
                                <a href="{{ route('admin.users.show', $invited->id) }}" class="text-red-700 hover:text-red-500 text-sm">
                                    Ver →
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- User's Invites -->
            @if($userInvites->isNotEmpty())
                <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-neutral-800">
                        <h3 class="text-lg font-semibold text-neutral-200">Convites Criados</h3>
                    </div>
                    <table class="min-w-full divide-y divide-neutral-800">
                        <thead class="bg-neutral-900/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase">Código</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase">Usos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase">Origem</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase">Criado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-800">
                            @foreach($userInvites as $invite)
                                <tr class="hover:bg-neutral-900/30 transition">
                                    <td class="px-6 py-4"><code class="text-red-400 font-mono">{{ $invite->code }}</code></td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs rounded 
                                            @if($invite->status === 'active') bg-green-950/40 text-green-500 border border-green-900/50
                                            @else bg-neutral-800/40 text-neutral-500 border border-neutral-700/50
                                            @endif">
                                            {{ ucfirst($invite->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-neutral-400">{{ $invite->current_uses }}/{{ $invite->max_uses }}</td>
                                    <td class="px-6 py-4 text-neutral-400">{{ ucfirst($invite->source) }}</td>
                                    <td class="px-6 py-4 text-neutral-500">{{ $invite->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
