<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Em Manutenção - Atrocidades</title>
    @vite(['resources/css/app.css'])
    <style>
        /* Animated background */
        .bg-grid {
            background-image: 
                linear-gradient(rgba(220, 38, 38, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(220, 38, 38, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: grid-move 20s linear infinite;
        }
        @keyframes grid-move {
            0% { background-position: 0 0; }
            100% { background-position: 50px 50px; }
        }

        /* Floating animation */
        .float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(2deg); }
        }

        /* Pulse ring effect */
        .pulse-ring {
            animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse-ring {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.05); }
        }

        /* Gear rotation */
        .gear-spin {
            animation: gear-spin 8s linear infinite;
        }
        @keyframes gear-spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .gear-spin-reverse {
            animation: gear-spin-reverse 6s linear infinite;
        }
        @keyframes gear-spin-reverse {
            from { transform: rotate(360deg); }
            to { transform: rotate(0deg); }
        }

        /* Progress bar */
        .progress-indeterminate {
            background: linear-gradient(
                90deg,
                transparent 0%,
                rgba(220, 38, 38, 0.8) 50%,
                transparent 100%
            );
            background-size: 200% 100%;
            animation: progress-move 1.5s ease-in-out infinite;
        }
        @keyframes progress-move {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        /* Glitch effect for title */
        .glitch {
            position: relative;
        }
        .glitch::before,
        .glitch::after {
            content: attr(data-text);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .glitch::before {
            animation: glitch-1 0.3s infinite linear alternate-reverse;
            color: #ff0000;
            z-index: -1;
        }
        .glitch::after {
            animation: glitch-2 0.3s infinite linear alternate-reverse;
            color: #00ffff;
            z-index: -2;
        }
        @keyframes glitch-1 {
            0% { clip-path: inset(20% 0 60% 0); transform: translate(-3px, 0); }
            20% { clip-path: inset(60% 0 20% 0); transform: translate(3px, 0); }
            40% { clip-path: inset(40% 0 40% 0); transform: translate(-3px, 0); }
            60% { clip-path: inset(80% 0 10% 0); transform: translate(3px, 0); }
            80% { clip-path: inset(10% 0 80% 0); transform: translate(-3px, 0); }
            100% { clip-path: inset(50% 0 30% 0); transform: translate(3px, 0); }
        }
        @keyframes glitch-2 {
            0% { clip-path: inset(60% 0 20% 0); transform: translate(3px, 0); }
            20% { clip-path: inset(20% 0 60% 0); transform: translate(-3px, 0); }
            40% { clip-path: inset(80% 0 10% 0); transform: translate(3px, 0); }
            60% { clip-path: inset(40% 0 40% 0); transform: translate(-3px, 0); }
            80% { clip-path: inset(30% 0 50% 0); transform: translate(3px, 0); }
            100% { clip-path: inset(10% 0 80% 0); transform: translate(-3px, 0); }
        }

        /* Blob animations */
        .blob {
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            animation: blob-morph 8s ease-in-out infinite;
        }
        @keyframes blob-morph {
            0%, 100% { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
            25% { border-radius: 58% 42% 75% 25% / 76% 46% 54% 24%; }
            50% { border-radius: 50% 50% 33% 67% / 55% 27% 73% 45%; }
            75% { border-radius: 33% 67% 58% 42% / 63% 68% 32% 37%; }
        }
    </style>
</head>
<body class="bg-black text-white min-h-screen overflow-hidden">
    <!-- Animated Background -->
    <div class="fixed inset-0 bg-grid"></div>
    <div class="fixed inset-0 bg-gradient-to-br from-black via-gray-950 to-black"></div>
    
    <!-- Floating Blobs -->
    <div class="fixed top-20 -left-32 w-96 h-96 bg-red-900/20 blob blur-3xl"></div>
    <div class="fixed -bottom-32 -right-32 w-[500px] h-[500px] bg-red-800/10 blob blur-3xl" style="animation-delay: -4s;"></div>
    <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-red-950/10 blob blur-3xl" style="animation-delay: -2s;"></div>

    <!-- Main Content -->
    <div class="relative z-10 min-h-screen flex items-center justify-center p-6">
        <div class="max-w-xl w-full">
            <!-- Error Message (if non-admin tried to login) -->
            @if(session('error'))
            <div class="mb-6 bg-red-900/50 border border-red-700/50 rounded-xl p-4 backdrop-blur-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-red-300 text-sm">{{ session('error') }}</p>
                </div>
            </div>
            @endif

            <!-- Gears Animation -->
            <div class="flex justify-center mb-8 relative">
                <div class="relative">
                    <!-- Main gear -->
                    <div class="gear-spin">
                        <svg class="w-32 h-32 text-red-600" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 15.5A3.5 3.5 0 0 1 8.5 12 3.5 3.5 0 0 1 12 8.5a3.5 3.5 0 0 1 3.5 3.5 3.5 3.5 0 0 1-3.5 3.5m7.43-2.53c.04-.32.07-.64.07-.97 0-.33-.03-.66-.07-1l2.11-1.63c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.31-.61-.22l-2.49 1c-.52-.39-1.06-.73-1.69-.98l-.37-2.65A.506.506 0 0 0 14 2h-4c-.25 0-.46.18-.5.42l-.37 2.65c-.63.25-1.17.59-1.69.98l-2.49-1c-.22-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64L4.57 11c-.04.34-.07.67-.07 1 0 .33.03.65.07.97l-2.11 1.66c-.19.15-.25.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1.01c.52.4 1.06.74 1.69.99l.37 2.65c.04.24.25.42.5.42h4c.25 0 .46-.18.5-.42l.37-2.65c.63-.26 1.17-.59 1.69-.99l2.49 1.01c.22.08.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.66z"/>
                        </svg>
                    </div>
                    <!-- Small gear -->
                    <div class="absolute -right-4 -bottom-2 gear-spin-reverse">
                        <svg class="w-16 h-16 text-red-500/70" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 15.5A3.5 3.5 0 0 1 8.5 12 3.5 3.5 0 0 1 12 8.5a3.5 3.5 0 0 1 3.5 3.5 3.5 3.5 0 0 1-3.5 3.5m7.43-2.53c.04-.32.07-.64.07-.97 0-.33-.03-.66-.07-1l2.11-1.63c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.31-.61-.22l-2.49 1c-.52-.39-1.06-.73-1.69-.98l-.37-2.65A.506.506 0 0 0 14 2h-4c-.25 0-.46.18-.5.42l-.37 2.65c-.63.25-1.17.59-1.69.98l-2.49-1c-.22-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64L4.57 11c-.04.34-.07.67-.07 1 0 .33.03.65.07.97l-2.11 1.66c-.19.15-.25.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1.01c.52.4 1.06.74 1.69.99l.37 2.65c.04.24.25.42.5.42h4c.25 0 .46-.18.5-.42l.37-2.65c.63-.26 1.17-.59 1.69-.99l2.49 1.01c.22.08.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.66z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Title -->
            <div class="text-center mb-8">
                <h1 class="glitch text-5xl md:text-6xl font-black text-red-500 mb-4" data-text="EM MANUTENÇÃO">
                    EM MANUTENÇÃO
                </h1>
                <p class="text-xl text-gray-400">Estamos trabalhando para melhorar sua experiência</p>
            </div>

            <!-- Progress Bar -->
            <div class="mb-8">
                <div class="h-1 bg-gray-800 rounded-full overflow-hidden">
                    <div class="h-full progress-indeterminate"></div>
                </div>
            </div>

            <!-- Info Card -->
            <div class="bg-gray-900/60 backdrop-blur-xl border border-red-900/30 rounded-2xl p-6 mb-8 float">
                <div class="flex items-start gap-4 mb-4">
                    <div class="p-3 bg-red-900/30 rounded-xl">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold mb-1">O que está acontecendo?</h3>
                        <p class="text-gray-400 text-sm">
                            O site está temporariamente indisponível enquanto realizamos atualizações 
                            e melhorias no sistema. Voltaremos em breve!
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-800">
                    <div class="text-center">
                        <div class="text-2xl mb-1">🔧</div>
                        <div class="text-xs text-gray-500">Atualizando</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl mb-1">⚡</div>
                        <div class="text-xs text-gray-500">Otimizando</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl mb-1">🛡️</div>
                        <div class="text-xs text-gray-500">Protegendo</div>
                    </div>
                </div>
            </div>

            <!-- Admin Login Section -->
            <div class="bg-gray-900/40 backdrop-blur-xl border border-gray-800 rounded-2xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span class="text-gray-400 text-sm">Acesso Administrativo</span>
                </div>

                <p class="text-gray-500 text-sm mb-4">
                    Se você é um administrador, faça login para acessar o sistema.
                </p>

                <a href="{{ route('login') }}" 
                   class="block w-full text-center px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white rounded-xl font-semibold transition-all hover:scale-[1.02] hover:shadow-lg hover:shadow-red-900/30">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Login de Administrador
                    </span>
                </a>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8 text-gray-600 text-sm">
                <p>&copy; {{ date('Y') }} Atrocidades. Voltaremos em breve.</p>
            </div>
        </div>
    </div>
</body>
</html>
