<x-app-layout>
    <x-slot name="title">Admin Dashboard - Atrocidades</x-slot>

    <div class="min-h-screen bg-black text-gray-100 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-red-500 mb-2">⚡ Admin Dashboard</h1>
                <p class="text-gray-400">Sistema de controle e monitoramento</p>
            </div>

            <!-- Site Controls -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Public Uploads Toggle -->
                <div class="bg-gray-900 border border-red-900/30 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-white mb-1">🔒 Controle de Uploads</h3>
                            <p class="text-sm text-gray-400 mb-3">
                                @if($siteSettings['public_uploads_enabled'])
                                    Usuários podem fazer upload normalmente
                                @else
                                    <span class="text-red-400">Apenas admins podem fazer upload</span>
                                @endif
                            </p>
                        </div>
                        <button 
                            onclick="toggleSetting('public_uploads_enabled', {{ $siteSettings['public_uploads_enabled'] ? 'false' : 'true' }})"
                            class="relative inline-flex h-8 w-14 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-gray-900 {{ $siteSettings['public_uploads_enabled'] ? 'bg-green-600' : 'bg-gray-600' }}"
                            id="toggle-public-uploads">
                            <span class="inline-block h-6 w-6 transform rounded-full bg-white transition-transform {{ $siteSettings['public_uploads_enabled'] ? 'translate-x-7' : 'translate-x-1' }}"></span>
                        </button>
                    </div>
                </div>

                <!-- Maintenance Mode Toggle -->
                <div class="bg-gray-900 border border-red-900/30 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-white mb-1">🛠️ Modo Manutenção</h3>
                            <p class="text-sm text-gray-400 mb-3">
                                @if($siteSettings['maintenance_mode'])
                                    <span class="text-orange-400">Site em manutenção (admins podem acessar)</span>
                                @else
                                    Site público e operacional
                                @endif
                            </p>
                        </div>
                        <button 
                            onclick="toggleSetting('maintenance_mode', {{ $siteSettings['maintenance_mode'] ? 'false' : 'true' }})"
                            class="relative inline-flex h-8 w-14 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-gray-900 {{ $siteSettings['maintenance_mode'] ? 'bg-orange-600' : 'bg-gray-600' }}"
                            id="toggle-maintenance">
                            <span class="inline-block h-6 w-6 transform rounded-full bg-white transition-transform {{ $siteSettings['maintenance_mode'] ? 'translate-x-7' : 'translate-x-1' }}"></span>
                        </button>
                    </div>
                </div>

                <!-- Emergency Access Key -->
                <div class="bg-gray-900 border border-red-900/30 rounded-lg p-6">
                    <div class="flex flex-col">
                        <h3 class="text-lg font-bold text-white mb-1">🔑 Chave de Emergência</h3>
                        <p class="text-sm text-gray-400 mb-3">
                            Para login durante manutenção
                        </p>
                        <div class="flex gap-2 items-center">
                            <input 
                                type="text" 
                                id="emergencyKeyDisplay" 
                                value="{{ $siteSettings['emergency_access_key'] ?? 'Não configurada' }}" 
                                readonly
                                class="flex-1 px-3 py-2 bg-black border border-gray-700 rounded text-gray-300 text-sm font-mono"
                            >
                            <button 
                                onclick="copyEmergencyKey()"
                                class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm transition-all"
                                title="Copiar chave">
                                📋
                            </button>
                            <button 
                                onclick="regenerateEmergencyKey()"
                                class="px-3 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded text-sm transition-all"
                                title="Gerar nova chave">
                                🔄
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Users Stats -->
                <div class="bg-gray-900 border border-red-900/30 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-400">Usuários</h3>
                        <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 text-sm">Total</span>
                            <span class="text-white font-bold text-lg">{{ number_format($stats['total_users']) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-green-400 text-sm">⚪ Online</span>
                            <span class="text-green-400 font-bold">{{ number_format($stats['online_users']) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-red-400 text-sm">🔴 Banidos</span>
                            <span class="text-red-400 font-bold">{{ number_format($stats['suspended_users']) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Video Stats -->
                <div class="bg-gray-900 border border-red-900/30 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-400">Vídeos</h3>
                        <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                        </svg>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 text-sm">Total</span>
                            <span class="text-white font-bold text-lg">{{ number_format($videoStats['total_videos']) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-yellow-400 text-sm">⏳ Pendentes</span>
                            <span class="text-yellow-400 font-bold">{{ number_format($videoStats['pending_videos']) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-blue-400 text-sm">📅 Hoje</span>
                            <span class="text-blue-400 font-bold">{{ number_format($videoStats['videos_today']) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Reports Stats -->
                <div class="bg-gray-900 border border-red-900/30 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-400">Denúncias</h3>
                        <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 text-sm">Total</span>
                            <span class="text-white font-bold text-lg">{{ number_format($reportStats['total_reports']) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-orange-400 text-sm">🚨 Pendentes</span>
                            <span class="text-orange-400 font-bold">{{ number_format($reportStats['pending_reports']) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-purple-400 text-sm">📅 Hoje</span>
                            <span class="text-purple-400 font-bold">{{ number_format($reportStats['reports_today']) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="bg-gray-900 border border-red-900/30 rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-400 mb-4">Ações Rápidas</h3>
                    <div class="space-y-2">
                        <a href="{{ route('admin.videos.index') }}" class="block px-3 py-2 bg-red-900/30 hover:bg-red-900/50 rounded text-sm text-red-400 transition">
                            📹 Moderar Vídeos
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 bg-red-900/30 hover:bg-red-900/50 rounded text-sm text-red-400 transition">
                            👥 Gerenciar Usuários
                        </a>
                        <a href="{{ route('admin.activity') }}" class="block px-3 py-2 bg-red-900/30 hover:bg-red-900/50 rounded text-sm text-red-400 transition">
                            📊 Logs de Atividade
                        </a>
                    </div>
                </div>
            </div>

            <!-- Reports Breakdown -->
            @if(!empty($reportsByReason))
            <div class="bg-gray-900 border border-red-900/30 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-bold text-white mb-4">📊 Denúncias por Tipo (Pendentes)</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    @foreach($reportsByReason as $reason => $count)
                    <div class="bg-black/50 rounded-lg p-4 border border-red-900/20">
                        <div class="text-xs text-gray-500 uppercase mb-1">{{ ucfirst(str_replace('_', ' ', $reason)) }}</div>
                        <div class="text-2xl font-bold text-red-400">{{ $count }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Recent Pending Reports -->
            @if($recentReports->count() > 0)
            <div class="bg-gray-900 border border-red-900/30 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-bold text-white mb-4">🚨 Denúncias Recentes (Pendentes)</h3>
                <div class="space-y-3">
                    @foreach($recentReports as $report)
                    <div class="bg-black/50 rounded-lg p-4 border border-red-900/20 hover:border-red-700/50 transition">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-2 py-1 bg-red-900/50 text-red-400 rounded text-xs font-bold">
                                        {{ ucfirst(str_replace('_', ' ', $report->reason)) }}
                                    </span>
                                    <span class="text-xs text-gray-500">{{ $report->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-sm text-white mb-1">
                                    Vídeo: <a href="{{ route('news.show', $report->video) }}" class="text-red-400 hover:underline">{{ $report->video->title }}</a>
                                </div>
                                @if($report->description)
                                <p class="text-sm text-gray-400">{{ Str::limit($report->description, 100) }}</p>
                                @endif
                                <div class="text-xs text-gray-500 mt-2">
                                    Por: {{ $report->reporter->name ?? 'Anônimo' }}
                                </div>
                            </div>
                            <a href="{{ route('admin.videos.show', $report->video) }}" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm transition whitespace-nowrap">
                                Revisar
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Recent Pending Videos -->
            @if($recentVideos->count() > 0)
            <div class="bg-gray-900 border border-red-900/30 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-bold text-white mb-4">⏳ Vídeos Aguardando Aprovação</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($recentVideos as $video)
                    <div class="bg-black/50 rounded-lg overflow-hidden border border-red-900/20 hover:border-red-700/50 transition">
                        <div class="aspect-video bg-gradient-to-br from-gray-900 to-black flex items-center justify-center">
                            @if($video->thumbnail_url)
                            <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="w-full h-full object-cover">
                            @else
                            <svg class="w-12 h-12 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                            </svg>
                            @endif
                        </div>
                        <div class="p-4">
                            <h4 class="text-sm font-semibold text-white mb-2 line-clamp-2">{{ $video->title }}</h4>
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                                <span>{{ $video->user->name }}</span>
                                <span>{{ $video->created_at->diffForHumans() }}</span>
                            </div>
                            <a href="{{ route('admin.videos.show', $video) }}" class="block w-full px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm text-center transition">
                                Moderar
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Recent Activity -->
            <div class="bg-gray-900 border border-red-900/30 rounded-lg p-6">
                <h3 class="text-lg font-bold text-white mb-4">📋 Atividade Recente</h3>
                <div class="space-y-2">
                    @foreach($recentActivity as $activity)
                    <div class="flex items-center gap-3 text-sm py-2 border-b border-gray-800 last:border-0">
                        <span class="text-gray-500">{{ $activity->created_at->format('H:i') }}</span>
                        <span class="text-gray-400">
                            <span class="text-white font-semibold">{{ $activity->user->name ?? 'Sistema' }}</span>
                            {{ $activity->description }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        // Prevent rapid-fire toggle clicks (Rate Limiting)
        let toggleInProgress = false;

        function copyEmergencyKey() {
            const keyInput = document.getElementById('emergencyKeyDisplay');
            keyInput.select();
            document.execCommand('copy');
            alert('✓ Chave copiada para área de transferência!');
        }

        async function regenerateEmergencyKey() {
            if (!confirm('⚠️ Tem certeza que deseja gerar uma nova chave de emergência?\n\nA chave antiga deixará de funcionar.')) {
                return;
            }

            try {
                const response = await fetch('{{ route('dashboard.regenerate-key') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                });

                const data = await response.json();

                if (data.success) {
                    document.getElementById('emergencyKeyDisplay').value = data.new_key;
                    alert('✓ Nova chave de emergência gerada com sucesso!');
                } else {
                    alert('Erro ao gerar nova chave: ' + (data.message || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Regenerate key error:', error);
                alert('Erro ao gerar nova chave. Tente novamente.');
            }
        }

        function toggleSetting(key, value) {
            // Prevent double-click / rapid requests
            if (toggleInProgress) {
                console.warn('Toggle already in progress, please wait...');
                return;
            }

            // Validate inputs on client side
            const allowedKeys = ['public_uploads_enabled', 'maintenance_mode'];
            if (!allowedKeys.includes(key)) {
                alert('Configuração inválida');
                return;
            }

            if (typeof value !== 'boolean') {
                alert('Valor inválido');
                return;
            }

            toggleInProgress = true;
            const toggle = document.getElementById(`toggle-${key.replace('_', '-')}`);
            
            // Disable toggle button during request
            if (toggle) {
                toggle.disabled = true;
                toggle.style.opacity = '0.5';
            }

            fetch('{{ route('dashboard.settings.toggle') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ 
                    key: key, 
                    value: value 
                }),
                // Security: Don't send credentials to other domains
                credentials: 'same-origin',
            })
            .then(response => {
                // Check for HTTP errors
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Escape HTML to prevent XSS in alert
                    const message = String(data.message).replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    alert(message);
                    
                    // Reload page to reflect changes
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Erro desconhecido');
                }
            })
            .catch(error => {
                console.error('Toggle error:', error);
                
                // Safe error message (don't expose stack trace)
                const errorMsg = error.message || 'Erro ao alterar configuração';
                alert(errorMsg);
                
                // Re-enable toggle
                toggleInProgress = false;
                if (toggle) {
                    toggle.disabled = false;
                    toggle.style.opacity = '1';
                }
            });
        }

        // Prevent CSRF token expiration issues
        document.addEventListener('DOMContentLoaded', function() {
            // Check if CSRF token exists
            const csrfToken = '{{ csrf_token() }}';
            if (!csrfToken) {
                console.error('CSRF token missing! Forms may not work.');
            }
        });
    </script>
</x-app-layout>
