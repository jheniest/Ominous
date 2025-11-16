<div x-data="notificationDropdown()" @click.away="open = false" class="relative">
    <!-- Notification Bell Button -->
    <button @click="open = !open; if(open) loadNotifications()" 
            class="relative p-2 text-neutral-400 hover:text-neutral-200 transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        
        <!-- Unread Badge -->
        <span x-show="unreadCount > 0" 
              x-text="unreadCount" 
              class="absolute -top-1 -right-1 bg-red-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
        </span>
    </button>

    <!-- Dropdown -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="absolute right-0 mt-2 w-96 bg-neutral-900 border border-neutral-800 rounded-lg shadow-xl z-50 max-h-[32rem] overflow-hidden flex flex-col"
         style="display: none;">
        
        <!-- Header -->
        <div class="px-4 py-3 border-b border-neutral-800 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-neutral-200">Notificações</h3>
            <div class="flex gap-2">
                <button @click="markAllAsRead()" 
                        x-show="unreadCount > 0"
                        class="text-xs text-red-500 hover:text-red-400 transition">
                    Marcar todas como lida
                </button>
                <a href="{{ route('notifications.index') }}" class="text-xs text-neutral-500 hover:text-neutral-400 transition">
                    Ver todas
                </a>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="p-8 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-red-500"></div>
        </div>

        <!-- Empty State -->
        <div x-show="!loading && notifications.length === 0" class="p-8 text-center">
            <svg class="w-12 h-12 mx-auto text-neutral-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <p class="text-neutral-500 text-sm">Nenhuma notificação</p>
        </div>

        <!-- Notifications List -->
        <div x-show="!loading && notifications.length > 0" class="overflow-y-auto flex-1">
            <template x-for="notification in notifications" :key="notification.id">
                <div @click="markAsRead(notification.id)" 
                     :class="notification.is_read ? 'bg-neutral-900' : 'bg-neutral-800/50'"
                     class="px-4 py-3 border-b border-neutral-800 hover:bg-neutral-800/80 cursor-pointer transition">
                    <div class="flex items-start gap-3">
                        <!-- Icon -->
                        <div :class="getColorClass(notification.type)" class="mt-1">
                            <span x-text="getIcon(notification.type)" class="text-lg"></span>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-neutral-200 mb-1" x-text="notification.title"></h4>
                            <p class="text-xs text-neutral-400 mb-2 line-clamp-2" x-text="notification.message"></p>
                            <div class="flex items-center gap-2 text-xs text-neutral-500">
                                <span x-text="formatDate(notification.created_at)"></span>
                                <span x-show="notification.action_by_user" class="text-neutral-600">•</span>
                                <span x-show="notification.action_by_user" x-text="notification.action_by_user?.name"></span>
                            </div>
                        </div>

                        <!-- Unread Indicator -->
                        <div x-show="!notification.is_read" class="mt-2">
                            <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function notificationDropdown() {
    return {
        open: false,
        loading: false,
        notifications: [],
        unreadCount: 0,

        async loadNotifications() {
            this.loading = true;
            try {
                const response = await fetch('{{ route("notifications.unread") }}');
                const data = await response.json();
                this.notifications = data.notifications;
                this.unreadCount = data.unread_count;
            } catch (error) {
                console.error('Erro ao carregar notificações:', error);
            } finally {
                this.loading = false;
            }
        },

        async markAsRead(id) {
            try {
                await fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                });
                
                const notification = this.notifications.find(n => n.id === id);
                if (notification) {
                    notification.is_read = true;
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
            } catch (error) {
                console.error('Erro ao marcar como lida:', error);
            }
        },

        async markAllAsRead() {
            try {
                await fetch('{{ route("notifications.mark-all-read") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                
                this.notifications.forEach(n => n.is_read = true);
                this.unreadCount = 0;
            } catch (error) {
                console.error('Erro ao marcar todas como lidas:', error);
            }
        },

        getIcon(type) {
            const icons = {
                'video_approved': '✓',
                'video_rejected': '✗',
                'video_hidden': '🚫',
                'comment_approved': '✓',
                'comment_hidden': '🚫',
                'account_suspended': '⚠',
                'account_unsuspended': '✓',
            };
            return icons[type] || '🔔';
        },

        getColorClass(type) {
            if (['video_approved', 'comment_approved', 'account_unsuspended'].includes(type)) {
                return 'text-green-500';
            }
            if (['video_rejected', 'video_hidden', 'comment_hidden', 'account_suspended'].includes(type)) {
                return 'text-red-500';
            }
            return 'text-blue-500';
        },

        formatDate(date) {
            const d = new Date(date);
            const now = new Date();
            const diff = Math.floor((now - d) / 1000);

            if (diff < 60) return 'Agora';
            if (diff < 3600) return Math.floor(diff / 60) + 'm';
            if (diff < 86400) return Math.floor(diff / 3600) + 'h';
            if (diff < 604800) return Math.floor(diff / 86400) + 'd';
            
            return d.toLocaleDateString('pt-BR');
        },

        init() {
            // Carregar contador inicial
            this.loadNotifications();

            // Atualizar a cada 30 segundos
            setInterval(() => {
                if (!this.open) {
                    this.loadNotifications();
                }
            }, 30000);
        }
    }
}
</script>
