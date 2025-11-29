<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-neutral-200">Notificações</h1>
            
            <div class="flex gap-3">
                @if($notifications->where('is_read', false)->count() > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-neutral-800 hover:bg-neutral-700 text-neutral-300 rounded-lg transition text-sm">
                        Marcar todas como lida
                    </button>
                </form>
                @endif

                <form action="{{ route('notifications.destroy-all') }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir todas as notificações?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-900/50 hover:bg-red-900/70 text-red-400 rounded-lg transition text-sm">
                        Limpar Tudo
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-950/50 border border-green-800 text-green-400 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        <!-- Notifications List -->
        @if($notifications->count() > 0)
        <div class="space-y-3">
            @foreach($notifications as $notification)
            <div class="bg-neutral-900/80 backdrop-blur-lg border {{ $notification->is_read ? 'border-neutral-800' : 'border-red-900/50' }} rounded-lg p-5 transition hover:border-neutral-700">
                <div class="flex items-start gap-4">
                    <!-- Icon -->
                    <div class="mt-1">
                        <div class="w-10 h-10 rounded-full {{ $notification->is_read ? 'bg-neutral-800' : 'bg-red-950/50' }} flex items-center justify-center">
                            <span class="text-xl {{ $notification->color_class }}">{{ $notification->icon }}</span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="text-lg font-semibold text-neutral-200">{{ $notification->title }}</h3>
                            @if(!$notification->is_read)
                            <span class="ml-2 px-2 py-1 bg-red-900/50 text-red-400 text-xs font-semibold rounded">
                                Nova
                            </span>
                            @endif
                        </div>

                        <p class="text-neutral-400 mb-3">{{ $notification->message }}</p>

                        <div class="flex items-center gap-4 text-sm text-neutral-500">
                            <span>{{ $notification->created_at->diffForHumans() }}</span>
                            
                            @if($notification->actionByUser)
                            <span class="flex items-center gap-2">
                                <span class="text-neutral-600">•</span>
                                Por: <span class="text-neutral-400">{{ $notification->actionByUser->name }}</span>
                            </span>
                            @endif

                            @if($notification->relatedVideo)
                            <a href="{{ route('news.show', $notification->relatedVideo) }}" 
                               class="text-red-500 hover:text-red-400 transition flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                                Ver vídeo
                            </a>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        @if(!$notification->is_read)
                        <form action="{{ route('notifications.mark-read', $notification) }}" method="POST">
                            @csrf
                            <button type="submit" class="p-2 text-neutral-500 hover:text-neutral-300 transition" title="Marcar como lida">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                        </form>
                        @endif

                        <form action="{{ route('notifications.destroy', $notification) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta notificação?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-neutral-500 hover:text-red-400 transition" title="Excluir">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
        @endif

        @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <svg class="w-20 h-20 mx-auto text-neutral-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <p class="text-neutral-500 text-lg">Você não tem notificações</p>
        </div>
        @endif
    </div>
</x-app-layout>
