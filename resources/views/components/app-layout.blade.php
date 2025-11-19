<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Atrocidades' }}</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-black text-neutral-300 antialiased">
    <div class="min-h-screen">
        <nav class="border-b border-neutral-800 bg-neutral-950/80 backdrop-blur-lg sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ auth()->check() ? route('videos.index') : route('home') }}" class="text-2xl font-bold text-neutral-400 hover:text-red-700 transition">
                            ATROCIDADES
                        </a>
                    </div>
                    
                    <div class="flex items-center gap-4 md:gap-6">
                        @auth
                            <!-- Desktop Navigation -->
                            <a href="{{ route('videos.index') }}" class="hidden md:block text-neutral-400 hover:text-neutral-200 transition">
                                Videos
                            </a>
                            <a href="{{ route('videos.create') }}" class="hidden md:block text-neutral-400 hover:text-neutral-200 transition">
                                Upload
                            </a>
                            @if(auth()->user()->is_admin)
                                <a href="{{ route('dashboard') }}" class="hidden md:block text-neutral-400 hover:text-neutral-200 transition">
                                    Dashboard
                                </a>
                                <a href="{{ route('admin.dashboard') }}" class="hidden md:block text-red-700 hover:text-red-500 transition">
                                    Admin
                                </a>
                            @endif
                            
                            <!-- Notification Dropdown -->
                            <x-notification-dropdown />
                            
                            <!-- Profile Dropdown -->
                            <div class="relative group">
                                <button class="flex items-center gap-2 text-neutral-400 hover:text-neutral-200 transition">
                                    @if(auth()->user()->avatar)
                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full border-2 border-neutral-700">
                                    @else
                                    <div class="w-8 h-8 bg-gradient-to-br from-red-900 to-red-950 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                    @endif
                                    <!-- Nome visível apenas no desktop -->
                                    <span class="hidden md:inline">{{ auth()->user()->name }}</span>
                                    @if(auth()->user()->is_verified)
                                        <svg class="hidden md:block w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20" title="Verificado">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                                <div class="absolute right-0 mt-2 w-48 bg-neutral-900 border border-neutral-800 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                                    <!-- Mobile Navigation Items -->
                                    <a href="{{ route('videos.index') }}" class="md:hidden block px-4 py-2 text-neutral-300 hover:bg-neutral-800 border-b border-neutral-800">
                                        🎬 Videos
                                    </a>
                                    <a href="{{ route('videos.create') }}" class="md:hidden block px-4 py-2 text-neutral-300 hover:bg-neutral-800 border-b border-neutral-800">
                                        📤 Upload
                                    </a>
                                    @if(auth()->user()->is_admin)
                                        <a href="{{ route('dashboard') }}" class="md:hidden block px-4 py-2 text-neutral-300 hover:bg-neutral-800 border-b border-neutral-800">
                                            📊 Dashboard
                                        </a>
                                        <a href="{{ route('admin.dashboard') }}" class="md:hidden block px-4 py-2 text-red-400 hover:bg-neutral-800 border-b border-neutral-800">
                                            ⚙️ Admin
                                        </a>
                                    @endif
                                    
                                    <!-- Profile Items (sempre visíveis) -->
                                    <a href="{{ route('profile.show', auth()->user()) }}" class="block px-4 py-2 text-neutral-300 hover:bg-neutral-800 {{ auth()->user()->is_admin ? '' : 'rounded-t-lg' }}">
                                        👤 Ver Perfil
                                    </a>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-neutral-300 hover:bg-neutral-800">
                                        ✏️ Editar Perfil
                                    </a>
                                    <a href="{{ route('profile.invites') }}" class="block px-4 py-2 text-neutral-300 hover:bg-neutral-800">
                                        📧 Convites
                                    </a>
                                    <a href="{{ route('videos.my-videos') }}" class="block px-4 py-2 text-neutral-300 hover:bg-neutral-800">
                                        🎥 Meus Vídeos
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-red-400 hover:bg-neutral-800 rounded-b-lg">
                                            🚪 Sair
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-neutral-400 hover:text-neutral-200 transition">
                                Login
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Dynamic Category Submenu -->
            <div class="border-t border-neutral-800/50 bg-neutral-950/60 backdrop-blur-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3 py-2.5 overflow-x-auto scrollbar-hide">
                        <span class="text-xs uppercase tracking-wider text-neutral-600 font-semibold whitespace-nowrap">
                            Hoje:
                        </span>
                        @foreach($categoryMenu as $item)
                        <a href="{{ route('videos.index', ['category' => $item['category']]) }}" 
                           class="group flex items-center gap-2 px-3 py-1.5 rounded-full transition-all hover:scale-105 whitespace-nowrap"
                           style="background-color: {{ $item['color'] }}15; border: 1px solid {{ $item['color'] }}30;">
                            <span class="text-sm font-medium transition-colors group-hover:brightness-110" 
                                  style="color: {{ $item['color'] }};">
                                {{ $item['name'] }}
                            </span>
                            @if($item['count'] > 0)
                            <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-xs font-bold rounded-full transition-all"
                                  style="background-color: {{ $item['color'] }}; color: #000;">
                                {{ $item['count'] }}
                            </span>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </nav>

        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-green-950/40 border border-green-900/50 text-green-400 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-red-950/40 border border-red-900/50 text-red-400 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <main>
            {{ $slot }}
        </main>
    </div>
</body>
</html>
