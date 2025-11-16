<x-app-layout>
    <x-slot name="title">Admin Dashboard</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-red-500">Admin Dashboard</h1>
                <p class="mt-1 text-neutral-500">Painel de controle administrativo</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-6">
                    <div class="text-sm font-medium text-neutral-500">Total de Usuários</div>
                    <div class="mt-2 text-3xl font-bold text-neutral-200">{{ $stats['total_users'] }}</div>
                </div>

                <div class="bg-neutral-950/60 backdrop-blur-lg border border-green-900/30 rounded-lg p-6">
                    <div class="text-sm font-medium text-neutral-500">Usuários Ativos</div>
                    <div class="mt-2 text-3xl font-bold text-green-500">{{ $stats['active_users'] }}</div>
                </div>

                <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-6">
                    <div class="text-sm font-medium text-neutral-500">Total de Convites</div>
                    <div class="mt-2 text-3xl font-bold text-neutral-200">{{ $stats['total_invites'] }}</div>
                </div>

                <div class="bg-neutral-950/60 backdrop-blur-lg border border-cyan-900/30 rounded-lg p-6">
                    <div class="text-sm font-medium text-neutral-500">Receita Total</div>
                    <div class="mt-2 text-3xl font-bold text-cyan-400">R$ {{ number_format($stats['total_revenue'], 2, ',', '.') }}</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 p-4 bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg hover:border-red-900/40 transition">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-neutral-900 flex items-center justify-center">
                        <svg class="w-5 h-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-neutral-200 font-medium">Usuários</div>
                        <div class="text-xs text-neutral-600">Gerenciar usuários</div>
                    </div>
                </a>

                <a href="{{ route('admin.invites.index') }}" class="flex items-center gap-3 p-4 bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg hover:border-red-900/40 transition">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-neutral-900 flex items-center justify-center">
                        <svg class="w-5 h-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-neutral-200 font-medium">Convites</div>
                        <div class="text-xs text-neutral-600">Gerenciar convites</div>
                    </div>
                </a>

                <a href="{{ route('admin.videos.index') }}" class="flex items-center gap-3 p-4 bg-neutral-950/60 backdrop-blur-lg border border-yellow-900/30 rounded-lg hover:border-yellow-700/50 transition">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-neutral-900 flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-neutral-200 font-medium">Vídeos</div>
                        <div class="text-xs text-neutral-600">Moderar vídeos</div>
                    </div>
                </a>

                <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 p-4 bg-neutral-950/60 backdrop-blur-lg border border-red-900/30 rounded-lg hover:border-red-700/50 transition">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-neutral-900 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-neutral-200 font-medium">Denúncias</div>
                        <div class="text-xs text-neutral-600">Ver reportes</div>
                    </div>
                </a>

                <a href="{{ route('admin.purchases') }}" class="flex items-center gap-3 p-4 bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg hover:border-red-900/40 transition">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-neutral-900 flex items-center justify-center">
                        <svg class="w-5 h-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-neutral-200 font-medium">Compras</div>
                        <div class="text-xs text-neutral-600">Ver transações</div>
                    </div>
                </a>

                <a href="{{ route('admin.comments.index') }}" class="flex items-center gap-3 p-4 bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg hover:border-red-900/40 transition">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-neutral-900 flex items-center justify-center">
                        <svg class="w-5 h-5 text-neutral-400" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-neutral-200 font-medium">Comentários</div>
                        <div class="text-xs text-neutral-600">Moderar comentários</div>
                    </div>
                </a>

                <a href="{{ route('admin.activity') }}" class="flex items-center gap-3 p-4 bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg hover:border-red-900/40 transition">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-neutral-900 flex items-center justify-center">
                        <svg class="w-5 h-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-neutral-200 font-medium">Atividades</div>
                        <div class="text-xs text-neutral-600">Log de ações</div>
                    </div>
                </a>

                <a href="{{ route('videos.index') }}" class="flex items-center gap-3 p-4 bg-neutral-950/60 backdrop-blur-lg border border-cyan-900/30 rounded-lg hover:border-cyan-700/50 transition">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-neutral-900 flex items-center justify-center">
                        <svg class="w-5 h-5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-neutral-200 font-medium">Ver Rede</div>
                        <div class="text-xs text-neutral-600">Acessar rede pública</div>
                    </div>
                </a>
            </div>

            <!-- Recent Activity -->
            <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-neutral-200 mb-4">Atividade Recente</h3>
                
                @if($recentActivity->isEmpty())
                    <p class="text-neutral-500 text-sm">Nenhuma atividade registrada.</p>
                @else
                    <div class="space-y-3">
                        @foreach($recentActivity as $activity)
                            <div class="flex items-start gap-3 p-3 bg-neutral-900/50 rounded border border-neutral-800">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-neutral-800 flex items-center justify-center">
                                    @if($activity->type === 'user_registered')
                                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6z"></path>
                                        </svg>
                                    @elseif($activity->type === 'invite_created')
                                        <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                        </svg>
                                    @elseif($activity->type === 'purchase_completed')
                                        <svg class="w-4 h-4 text-cyan-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-neutral-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="text-neutral-300 text-sm">{{ $activity->description }}</div>
                                    <div class="text-xs text-neutral-600 mt-1">{{ $activity->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
