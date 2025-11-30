<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SEO Base -->
    <title>@yield('title', 'Atrocidades - Portal de Notícias')</title>
    <meta name="description" content="@yield('meta_description', 'Portal de notícias com cobertura de eventos mundiais, conflitos e acontecimentos relevantes. Informação verificada e atualizada.')">
    <meta name="keywords" content="@yield('meta_keywords', 'notícias, atualidades, mundo, conflitos, eventos, informação')">
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">
    <meta name="author" content="Atrocidades">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', 'Atrocidades - Portal de Notícias')">
    <meta property="og:description" content="@yield('og_description', 'Portal de notícias com cobertura de eventos mundiais.')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">
    <meta property="og:site_name" content="Atrocidades">
    <meta property="og:locale" content="pt_BR">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', 'Atrocidades - Portal de Notícias')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Portal de notícias com cobertura de eventos mundiais.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/og-default.jpg'))">
    
    <!-- Schema.org JSON-LD -->
    @stack('schema')
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css'])
    @stack('styles')
</head>
<body class="bg-[#0a0a0a] text-neutral-300 antialiased">
    <div class="min-h-screen">
        <nav class="border-b border-neutral-800 bg-neutral-950/80 backdrop-blur-lg sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('news.index') }}" class="text-2xl font-bold text-neutral-400 hover:text-red-700 transition">
                            ATROCIDADES
                        </a>
                    </div>
                    
                    <div class="flex items-center gap-4 md:gap-6">
                        <!-- Search Bar (News Only) -->
                        <div class="relative hidden md:block" x-data="{ open: false, query: '' }">
                            <div class="flex items-center">
                                <button @click="open = !open" class="text-neutral-400 hover:text-neutral-200 transition p-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </button>
                                <form action="{{ route('news.search') }}" method="GET" 
                                      class="overflow-hidden transition-all duration-300"
                                      :class="open ? 'w-64 opacity-100' : 'w-0 opacity-0'">
                                    <input type="text" 
                                           name="q" 
                                           x-model="query"
                                           placeholder="Pesquisar notícias..."
                                           class="w-full bg-neutral-900 border border-neutral-700 rounded-lg px-4 py-2 text-sm text-neutral-200 placeholder-neutral-500 focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600"
                                           @keydown.escape="open = false">
                                </form>
                            </div>
                        </div>

                        <!-- Mobile Search Button -->
                        <a href="{{ route('news.search') }}?q=" class="md:hidden text-neutral-400 hover:text-neutral-200 transition p-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </a>

                        @auth
                            <!-- Desktop Navigation -->
                            <a href="{{ route('news.index') }}" class="hidden md:block text-neutral-400 hover:text-neutral-200 transition">
                                Notícias
                            </a>
                            <a href="{{ route('news.create') }}" class="hidden md:block text-neutral-400 hover:text-neutral-200 transition">
                                Enviar
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
                            @if(View::exists('components.notification-dropdown'))
                            <x-notification-dropdown />
                            @endif
                            
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
                                    <a href="{{ route('news.index') }}" class="md:hidden block px-4 py-2 text-neutral-300 hover:bg-neutral-800 border-b border-neutral-800 rounded-t-lg">
                                        📰 Notícias
                                    </a>
                                    <a href="{{ route('news.create') }}" class="md:hidden block px-4 py-2 text-neutral-300 hover:bg-neutral-800 border-b border-neutral-800">
                                        📤 Enviar
                                    </a>
                                    @if(auth()->user()->is_admin)
                                        <a href="{{ route('dashboard') }}" class="md:hidden block px-4 py-2 text-neutral-300 hover:bg-neutral-800 border-b border-neutral-800">
                                            📊 Dashboard
                                        </a>
                                        <a href="{{ route('admin.dashboard') }}" class="md:hidden block px-4 py-2 text-red-400 hover:bg-neutral-800 border-b border-neutral-800">
                                            ⚙️ Admin
                                        </a>
                                    @endif
                                    
                                    <!-- Profile Items -->
                                    <a href="{{ route('profile.show', auth()->user()) }}" class="block px-4 py-2 text-neutral-300 hover:bg-neutral-800 {{ !auth()->user()->is_admin ? 'rounded-t-lg' : '' }}">
                                        👤 Ver Perfil
                                    </a>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-neutral-300 hover:bg-neutral-800">
                                        ✏️ Editar Perfil
                                    </a>
                                    <a href="{{ route('profile.invites') }}" class="block px-4 py-2 text-neutral-300 hover:bg-neutral-800">
                                        📧 Convites
                                    </a>
                                    <a href="{{ route('news.my-submissions') }}" class="block px-4 py-2 text-neutral-300 hover:bg-neutral-800">
                                        📋 Minhas Publicações
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
                            <a href="{{ route('news.index') }}" class="hidden md:block text-neutral-400 hover:text-neutral-200 transition">
                                Notícias
                            </a>
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
                    <div class="flex items-center gap-6 py-2.5 overflow-x-auto scrollbar-hide">
                        @php
                            $currentCategory = request()->route('category') ?? request()->get('category');
                        @endphp
                        @foreach($categoryMenu as $item)
                        <a href="{{ route('news.category', ['category' => $item['category']]) }}" 
                           class="relative text-sm font-semibold uppercase tracking-wider whitespace-nowrap transition-all pb-2
                                  {{ $currentCategory === $item['category'] 
                                     ? 'text-red-500 border-b-2 border-red-500' 
                                     : 'text-neutral-300 hover:text-red-400 border-b-2 border-transparent hover:border-red-400/50' }}">
                            {{ $item['name'] }}
                            @if($item['count'] > 0)
                            <span class="ml-1 text-xs text-neutral-500">({{ $item['count'] }})</span>
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
            @yield('content')
        </main>
    </div>
    
    @stack('scripts')
</body>
</html>
