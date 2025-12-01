<x-app-layout>
    <x-slot name="title">Enviar Notificação - Admin</x-slot>

    <div class="min-h-screen bg-black text-gray-100 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('admin.dashboard') }}" class="text-red-400 hover:text-red-300 text-sm mb-4 inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Voltar ao Dashboard
                </a>
                <h1 class="text-2xl font-bold text-white mb-2">📢 Enviar Notificação</h1>
                <p class="text-gray-400">Envie mensagens para usuários específicos ou para todos os membros.</p>
            </div>

            @if(session('success'))
            <div class="mb-6 p-4 bg-green-900/30 border border-green-800/50 rounded-lg">
                <p class="text-green-400">{{ session('success') }}</p>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 p-4 bg-red-900/30 border border-red-800/50 rounded-lg">
                <p class="text-red-400">{{ session('error') }}</p>
            </div>
            @endif

            <!-- Notification Form -->
            <div class="bg-gray-900 border border-red-900/30 rounded-lg p-6 mb-8">
                <form action="{{ route('admin.notifications.send') }}" method="POST" x-data="notificationForm()">
                    @csrf
                    
                    <!-- Recipient Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-300 mb-3">Destinatário</label>
                        <div class="flex flex-wrap gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="recipient" value="all" x-model="recipientType" 
                                       class="w-4 h-4 text-red-600 bg-neutral-800 border-neutral-600 focus:ring-red-500">
                                <span class="text-gray-300">Todos os usuários</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="recipient" value="nickname" x-model="recipientType"
                                       class="w-4 h-4 text-red-600 bg-neutral-800 border-neutral-600 focus:ring-red-500">
                                <span class="text-gray-300">Por @nickname</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="recipient" value="specific" x-model="recipientType"
                                       class="w-4 h-4 text-red-600 bg-neutral-800 border-neutral-600 focus:ring-red-500">
                                <span class="text-gray-300">Selecionar da lista</span>
                            </label>
                        </div>
                        @error('recipient')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nickname Input -->
                    <div x-show="recipientType === 'nickname'" x-cloak class="mb-6">
                        <label for="nickname" class="block text-sm font-medium text-gray-300 mb-2">
                            Nickname do usuário
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">@</span>
                            <input 
                                type="text" 
                                name="nickname" 
                                id="nickname"
                                x-model="nickname"
                                @input.debounce.300ms="searchNickname()"
                                placeholder="digite o nickname"
                                class="w-full pl-8 pr-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"
                                autocomplete="off"
                            >
                            <!-- Autocomplete dropdown -->
                            <div x-show="showSuggestions && suggestions.length > 0" 
                                 x-cloak
                                 class="absolute z-10 w-full mt-1 bg-neutral-800 border border-neutral-700 rounded-lg shadow-xl max-h-60 overflow-y-auto">
                                <template x-for="user in suggestions" :key="user.id">
                                    <button type="button"
                                            @click="selectUser(user)"
                                            class="w-full px-4 py-2 flex items-center gap-3 hover:bg-neutral-700 transition text-left">
                                        <div class="w-8 h-8 rounded-full bg-red-900/50 flex items-center justify-center flex-shrink-0">
                                            <template x-if="user.avatar">
                                                <img :src="user.avatar" class="w-8 h-8 rounded-full object-cover">
                                            </template>
                                            <template x-if="!user.avatar">
                                                <span class="text-sm text-white" x-text="user.name.charAt(0).toUpperCase()"></span>
                                            </template>
                                        </div>
                                        <div>
                                            <p class="text-white text-sm" x-text="user.name"></p>
                                            <p class="text-gray-400 text-xs">
                                                <span>@</span><span x-text="user.nickname"></span>
                                            </p>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>
                        @error('nickname')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- User Selection Dropdown -->
                    <div x-show="recipientType === 'specific'" x-cloak class="mb-6">
                        <label for="user_id" class="block text-sm font-medium text-gray-300 mb-2">
                            Selecionar usuário
                        </label>
                        <select 
                            name="user_id" 
                            id="user_id"
                            class="w-full px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg text-white focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                            <option value="">Selecione um usuário...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ '@' . $user->nickname }}) - {{ $user->email }}</option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Warning for all users -->
                    <div x-show="recipientType === 'all'" x-cloak class="mb-6">
                        <div class="flex items-start gap-3 p-4 bg-yellow-900/20 border border-yellow-800/50 rounded-lg">
                            <svg class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-yellow-400 font-medium">Atenção</p>
                                <p class="text-yellow-300/70 text-sm">Esta notificação será enviada para <strong>todos os {{ $users->count() }} usuários ativos</strong>.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Title -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium text-gray-300 mb-2">
                            Título da notificação
                        </label>
                        <input 
                            type="text" 
                            name="title" 
                            id="title"
                            value="{{ old('title') }}"
                            placeholder="Ex: Atualização importante do sistema"
                            maxlength="255"
                            required
                            class="w-full px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"
                        >
                        @error('title')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message -->
                    <div class="mb-6">
                        <label for="message" class="block text-sm font-medium text-gray-300 mb-2">
                            Mensagem
                        </label>
                        <textarea 
                            name="message" 
                            id="message"
                            rows="4"
                            maxlength="1000"
                            required
                            placeholder="Escreva a mensagem da notificação..."
                            class="w-full px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 resize-none">{{ old('message') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Máximo de 1000 caracteres</p>
                        @error('message')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center justify-end">
                        <button type="submit" 
                                :disabled="isSubmitting"
                                @click="isSubmitting = true"
                                :class="{ 'opacity-50 cursor-not-allowed': isSubmitting }"
                                class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition flex items-center gap-2">
                            <template x-if="!isSubmitting">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </template>
                            <template x-if="isSubmitting">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="isSubmitting ? 'Enviando...' : 'Enviar Notificação'"></span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Recent Notifications -->
            @if($recentNotifications->count() > 0)
            <div class="bg-gray-900 border border-red-900/30 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-white mb-4">📋 Notificações Recentes Enviadas</h2>
                <div class="space-y-3">
                    @foreach($recentNotifications as $notification)
                    <div class="flex items-start gap-4 p-3 bg-black/50 rounded-lg border border-red-900/20">
                        <div class="w-10 h-10 rounded-full bg-yellow-900/30 flex items-center justify-center flex-shrink-0">
                            <span class="text-lg">📢</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <p class="text-white font-medium truncate">{{ $notification->title }}</p>
                                <span class="text-xs text-gray-500">→</span>
                                <span class="text-sm text-gray-400">
                                    @if($notification->user)
                                        {{ '@' . $notification->user->nickname }}
                                    @else
                                        <span class="text-gray-500">Usuário removido</span>
                                    @endif
                                </span>
                            </div>
                            <p class="text-gray-400 text-sm line-clamp-1">{{ $notification->message }}</p>
                            <p class="text-gray-500 text-xs mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        @if($notification->is_read)
                            <span class="px-2 py-0.5 bg-green-900/30 text-green-400 text-xs rounded">Lida</span>
                        @else
                            <span class="px-2 py-0.5 bg-yellow-900/30 text-yellow-400 text-xs rounded">Não lida</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
    function notificationForm() {
        return {
            recipientType: 'all',
            nickname: '',
            suggestions: [],
            showSuggestions: false,
            isSubmitting: false,
            
            async searchNickname() {
                if (this.nickname.length < 2) {
                    this.suggestions = [];
                    this.showSuggestions = false;
                    return;
                }
                
                try {
                    const response = await fetch(`{{ route('admin.notifications.search-users') }}?q=${encodeURIComponent(this.nickname)}`);
                    this.suggestions = await response.json();
                    this.showSuggestions = true;
                } catch (error) {
                    console.error('Error searching users:', error);
                }
            },
            
            selectUser(user) {
                this.nickname = user.nickname.replace('@', '');
                this.showSuggestions = false;
                this.suggestions = [];
            }
        };
    }
    </script>
    @endpush
</x-app-layout>
