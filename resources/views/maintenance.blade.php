<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>🛠️ Manutenção - Atrocidades</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .glitch { animation: glitch 1s infinite; }
        @keyframes glitch {
            0%, 100% { transform: translate(0); }
            20% { transform: translate(-2px, 2px); }
            40% { transform: translate(-2px, -2px); }
            60% { transform: translate(2px, 2px); }
            80% { transform: translate(2px, -2px); }
        }
        .pulse-slow { animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    </style>
</head>
<body class="bg-black text-gray-100 min-h-screen flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-red-950/20 via-black to-black"></div>
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-red-900/10 rounded-full blur-3xl pulse-slow"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-red-900/10 rounded-full blur-3xl pulse-slow" style="animation-delay: 1.5s;"></div>
    </div>

    <div class="relative z-10 max-w-2xl mx-auto px-6 text-center">
        <div class="mb-8">
            <div class="inline-block mb-6">
                <svg class="w-32 h-32 text-red-500 glitch" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>

            <h1 class="text-6xl font-bold mb-4 glitch text-red-500">🛠️ Em Manutenção</h1>
            <p class="text-2xl text-gray-400 mb-6">Estamos realizando melhorias no sistema</p>

            <div class="bg-gray-900/50 border border-red-900/30 rounded-lg p-6 mb-8 backdrop-blur-sm">
                <p class="text-gray-300 mb-4">O site está temporariamente indisponível enquanto realizamos manutenção no sistema.</p>
                <p class="text-sm text-gray-500">Pedimos desculpas pelo inconveniente. Voltaremos em breve.</p>
            </div>
        </div>

        <div class="space-y-4 mb-8">
            @auth
                <div class="bg-yellow-900/20 border border-yellow-700/50 rounded-lg p-4 mb-4">
                    <p class="text-yellow-400 text-sm mb-3">⚠️ Você está conectado como: <strong>{{ auth()->user()->name }}</strong></p>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-all transform hover:scale-105">
                            🚪 Fazer Logout
                        </button>
                    </form>
                </div>
            @else
                <button onclick="showEmergencyLogin()" class="px-6 py-3 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg font-semibold transition-all border border-gray-700 hover:border-red-700">
                    🔑 Acesso de Emergência
                </button>
            @endauth
        </div>

        @guest
        <div id="emergencyLoginContainer" class="hidden">
            <div class="bg-gray-900 border border-red-900/50 rounded-lg p-6 backdrop-blur-sm">
                <h3 class="text-xl font-bold text-red-400 mb-4">🔐 Acesso de Emergência</h3>
                
                <div id="keyStep">
                    <p class="text-sm text-gray-400 mb-4">Digite a chave de acesso de emergência para continuar:</p>
                    <form onsubmit="validateEmergencyKey(event)" class="space-y-4">
                        <input type="text" id="emergencyKey" placeholder="Chave de emergência" class="w-full px-4 py-3 bg-black border border-gray-700 rounded-lg text-white focus:border-red-500 focus:ring-2 focus:ring-red-500/50 transition-all" autocomplete="off" required>
                        <div class="flex gap-3">
                            <button type="submit" class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-all">Validar Chave</button>
                            <button type="button" onclick="hideEmergencyLogin()" class="px-4 py-3 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg transition-all">Cancelar</button>
                        </div>
                    </form>
                </div>

                <div id="loginStep" class="hidden">
                    <p class="text-sm text-green-400 mb-4">✓ Chave válida! Faça login para acessar:</p>
                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf
                        <input type="email" name="email" placeholder="Email" class="w-full px-4 py-3 bg-black border border-gray-700 rounded-lg text-white focus:border-red-500 focus:ring-2 focus:ring-red-500/50 transition-all" required>
                        <input type="password" name="password" placeholder="Senha" class="w-full px-4 py-3 bg-black border border-gray-700 rounded-lg text-white focus:border-red-500 focus:ring-2 focus:ring-red-500/50 transition-all" required>
                        <div class="flex items-center mb-4">
                            <input type="checkbox" name="remember" id="remember" class="w-4 h-4 bg-gray-800 border-gray-700 rounded text-red-600 focus:ring-red-500">
                            <label for="remember" class="ml-2 text-sm text-gray-400">Lembrar-me</label>
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-all">🔓 Entrar</button>
                            <button type="button" onclick="backToKeyStep()" class="px-4 py-3 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg transition-all">Voltar</button>
                        </div>
                    </form>
                </div>

                <div id="errorMessage" class="hidden mt-4 p-3 bg-red-900/30 border border-red-700 rounded-lg text-red-400 text-sm"></div>
            </div>
        </div>
        @endguest

        <div class="mt-12 text-gray-600 text-sm">
            <p>&copy; {{ date('Y') }} Atrocidades. Voltaremos em breve.</p>
        </div>
    </div>

    <script>
        function showEmergencyLogin() { document.getElementById('emergencyLoginContainer').classList.remove('hidden'); }
        function hideEmergencyLogin() {
            document.getElementById('emergencyLoginContainer').classList.add('hidden');
            document.getElementById('keyStep').classList.remove('hidden');
            document.getElementById('loginStep').classList.add('hidden');
            document.getElementById('errorMessage').classList.add('hidden');
            document.getElementById('emergencyKey').value = '';
        }
        function backToKeyStep() {
            document.getElementById('keyStep').classList.remove('hidden');
            document.getElementById('loginStep').classList.add('hidden');
            document.getElementById('errorMessage').classList.add('hidden');
        }
        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
        }
        async function validateEmergencyKey(event) {
            event.preventDefault();
            const key = document.getElementById('emergencyKey').value;
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.classList.add('hidden');
            try {
                const response = await fetch('{{ route('maintenance.validate-key') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: JSON.stringify({ key: key }),
                    credentials: 'same-origin',
                });
                const data = await response.json();
                if (data.success) {
                    document.getElementById('keyStep').classList.add('hidden');
                    document.getElementById('loginStep').classList.remove('hidden');
                } else {
                    showError(data.message || 'Chave de emergência inválida');
                }
            } catch (error) {
                console.error('Validation error:', error);
                showError('Erro ao validar chave. Tente novamente.');
            }
        }
    </script>
</body>
</html>
