<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Atrocidades')</title>
    @vite(['resources/css/app.css'])
    <style>
        /* Animated background grid */
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
<body class="bg-black text-neutral-300 antialiased min-h-screen">
    <!-- Animated Background -->
    <div class="fixed inset-0 bg-grid"></div>
    <div class="fixed inset-0 bg-gradient-to-br from-black via-gray-950 to-black"></div>
    
    <!-- Floating Blobs -->
    <div class="fixed top-20 -left-32 w-96 h-96 bg-red-900/10 blob blur-3xl pointer-events-none"></div>
    <div class="fixed -bottom-32 -right-32 w-[500px] h-[500px] bg-red-800/5 blob blur-3xl pointer-events-none" style="animation-delay: -4s;"></div>

    <!-- Header -->
    <nav class="relative z-20 border-b border-neutral-800/50 bg-neutral-950/80 backdrop-blur-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-14">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-xl font-bold text-neutral-400 hover:text-red-600 transition">
                        ATROCIDADES
                    </a>
                </div>
                
                <div class="flex items-center gap-4">
                    <!-- Search (always redirects to search page) -->
                    <a href="{{ route('news.search') }}" class="text-neutral-400 hover:text-neutral-200 transition p-2" title="Pesquisar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </a>
                    
                    <a href="{{ route('news.index') }}" class="text-neutral-400 hover:text-neutral-200 transition text-sm">
                        Notícias
                    </a>
                    
                    @auth
                        <a href="{{ route('profile.show', auth()->user()) }}" class="text-neutral-400 hover:text-neutral-200 transition text-sm">
                            Perfil
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-neutral-400 hover:text-neutral-200 transition text-sm">
                            Login
                        </a>
                    @endauth
                    
                    <a href="{{ route('invite.validate') }}" class="px-4 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition text-sm font-medium">
                        Tenho um Convite
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="relative z-10">
        @yield('content')
    </main>
</body>
</html>
