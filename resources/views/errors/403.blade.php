<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Acesso Negado</title>
    @vite(['resources/css/app.css'])
    <style>
        .float-animation {
            animation: float 4s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 15px rgba(220, 38, 38, 0.2); }
            50% { box-shadow: 0 0 25px rgba(220, 38, 38, 0.4); }
        }
    </style>
</head>
<body class="bg-black text-white min-h-screen flex items-center justify-center overflow-hidden">
    <!-- Background -->
    <div class="fixed inset-0 bg-gradient-to-br from-red-950/20 via-black to-black"></div>
    <div class="fixed top-1/4 -left-20 w-72 h-72 bg-red-600/5 rounded-full blur-3xl"></div>
    <div class="fixed bottom-1/4 -right-20 w-72 h-72 bg-red-600/5 rounded-full blur-3xl"></div>

    <div class="relative z-10 max-w-lg mx-auto px-6 text-center">
        <!-- Lock Icon -->
        <div class="float-animation mb-6">
            <div class="inline-block p-5 rounded-full bg-red-900/30 border border-red-800/50 pulse-glow">
                <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
        </div>

        <!-- Error Code -->
        <h1 class="text-6xl font-bold text-red-500 mb-2">403</h1>
        <h2 class="text-lg font-medium text-gray-400 mb-6">Acesso Negado</h2>

        <!-- Message Box -->
        <div class="bg-gray-900/70 backdrop-blur-sm border border-gray-800 rounded-xl p-5 mb-6">
            <p class="text-gray-300 text-sm">
                {{ $exception->getMessage() ?: 'Você não tem permissão para acessar este recurso.' }}
            </p>
        </div>

        <!-- Specific message for uploads disabled -->
        @if(str_contains($exception->getMessage() ?? '', 'Uploads'))
        <div class="bg-yellow-900/20 border border-yellow-700/30 rounded-xl p-4 mb-6">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span class="text-yellow-400 text-xs font-medium">Modo Restrito</span>
            </div>
            <p class="text-gray-400 text-xs">
                Os uploads estão temporariamente desabilitados. Tente novamente mais tarde.
            </p>
        </div>
        @endif

        <!-- Actions -->
        <div class="flex gap-3 justify-center">
            <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('news.index') }}" 
               class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm rounded-lg transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Voltar
            </a>
            <a href="{{ route('news.index') }}" 
               class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Início
            </a>
        </div>

        <!-- Footer -->
        <p class="mt-8 text-gray-600 text-xs">
            Se isso é um erro, contate um administrador.
        </p>
    </div>
</body>
</html>
