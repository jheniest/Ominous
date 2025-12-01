<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    
    <title>@yield('title') - Atrocidades</title>
    
    @vite(['resources/css/app.css'])
    
    <style>
        .glitch {
            animation: glitch 1s linear infinite;
        }
        
        @keyframes glitch {
            2%, 64% {
                transform: translate(2px, 0) skew(0deg);
            }
            4%, 60% {
                transform: translate(-2px, 0) skew(0deg);
            }
            62% {
                transform: translate(0, 0) skew(5deg);
            }
        }
        
        .error-code {
            text-shadow: 
                0 0 10px rgba(239, 68, 68, 0.5),
                0 0 20px rgba(239, 68, 68, 0.3),
                0 0 30px rgba(239, 68, 68, 0.2);
        }
        
        .pulse-border {
            animation: pulseBorder 2s ease-in-out infinite;
        }
        
        @keyframes pulseBorder {
            0%, 100% {
                border-color: rgba(127, 29, 29, 0.3);
            }
            50% {
                border-color: rgba(239, 68, 68, 0.6);
            }
        }
        
        .scanline {
            background: linear-gradient(
                transparent 50%,
                rgba(0, 0, 0, 0.1) 50%
            );
            background-size: 100% 4px;
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-[#0a0a0a] text-neutral-300 antialiased overflow-hidden">
    <!-- Scanline overlay -->
    <div class="fixed inset-0 scanline z-50 pointer-events-none"></div>
    
    <!-- Background effects -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-red-900/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-red-950/30 rounded-full blur-3xl"></div>
    </div>
    
    <div class="min-h-screen flex flex-col items-center justify-center px-4 relative z-10">
        <!-- Logo -->
        <a href="{{ url('/') }}" class="absolute top-8 left-1/2 -translate-x-1/2">
            <span class="text-2xl font-bold text-neutral-400 hover:text-red-700 transition">ATROCIDADES</span>
        </a>
        
        <!-- Error Content -->
        <div class="text-center max-w-xl">
            <!-- Error Code -->
            <div class="mb-6">
                <span class="text-[10rem] sm:text-[12rem] font-black text-red-600/90 error-code glitch leading-none">
                    @yield('code')
                </span>
            </div>
            
            <!-- Error Message -->
            <div class="bg-neutral-900/80 backdrop-blur-sm border-2 border-red-900/30 rounded-xl p-8 pulse-border">
                <h1 class="text-2xl sm:text-3xl font-bold text-white mb-4">
                    @yield('message')
                </h1>
                
                <p class="text-neutral-400 mb-8 text-lg">
                    @yield('description')
                </p>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ url('/') }}" 
                       class="w-full sm:w-auto px-8 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-all hover:scale-105 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Página Inicial
                    </a>
                    
                    <button onclick="history.back()" 
                            class="w-full sm:w-auto px-8 py-3 bg-neutral-800 hover:bg-neutral-700 text-white font-semibold rounded-lg transition-all hover:scale-105 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Voltar
                    </button>
                </div>
            </div>
            
            <!-- Additional Info -->
            <div class="mt-8 text-neutral-500 text-sm">
                <p>Se o problema persistir, entre em contato com o suporte.</p>
            </div>
        </div>
        
        <!-- Decorative Elements -->
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-2 text-neutral-600 text-xs">
            <span class="w-2 h-2 bg-red-600 rounded-full animate-pulse"></span>
            <span>ERRO DE SISTEMA</span>
        </div>
    </div>
</body>
</html>
