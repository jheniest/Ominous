<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SEO Base -->
    <title>@yield('title', 'Atrocidades - Portal de Notícias e Acontecimentos')</title>
    <meta name="description" content="@yield('meta_description', 'Portal de notícias com cobertura completa de eventos mundiais, conflitos e acontecimentos relevantes. Informação verificada e atualizada em tempo real.')">
    <meta name="keywords" content="@yield('meta_keywords', 'notícias, atualidades, mundo, eventos, informação, cobertura ao vivo, últimas notícias')">
    <meta name="robots" content="@yield('meta_robots', 'index, follow, max-image-preview:large')">
    <meta name="author" content="Atrocidades">
    <meta name="publisher" content="Atrocidades">
    <meta name="rating" content="mature">
    <meta name="distribution" content="global">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    
    <!-- Mobile & PWA -->
    <meta name="theme-color" content="#0a0a0a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="format-detection" content="telephone=no">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', 'Atrocidades - Portal de Notícias')">
    <meta property="og:description" content="@yield('og_description', 'Portal de notícias com cobertura de eventos mundiais e acontecimentos relevantes.')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="Atrocidades">
    <meta property="og:locale" content="pt_BR">
    @hasSection('og_article')
        <meta property="article:published_time" content="@yield('og_published_time')">
        <meta property="article:modified_time" content="@yield('og_modified_time')">
        <meta property="article:section" content="@yield('og_section')">
        <meta property="article:author" content="Atrocidades">
    @endif
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@atrocidades">
    <meta name="twitter:title" content="@yield('twitter_title', 'Atrocidades - Portal de Notícias')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Portal de notícias com cobertura de eventos mundiais.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/og-default.jpg'))">
    
    <!-- Google News -->
    <meta name="news_keywords" content="@yield('news_keywords', 'notícias, atualidades')">
    <meta name="original-source" content="{{ url()->current() }}">
    
    <!-- Schema.org JSON-LD - Organization -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "NewsMediaOrganization",
        "name": "Atrocidades",
        "url": "{{ config('app.url') }}",
        "logo": {
            "@@type": "ImageObject",
            "url": "{{ asset('images/logo.png') }}",
            "width": 600,
            "height": 60
        },
        "sameAs": [],
        "potentialAction": {
            "@@type": "SearchAction",
            "target": "{{ route('news.search') }}?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>
    
    <!-- Page-specific Schema -->
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
            <div class="border-t border-neutral-800/50 bg-neutral-950/60 backdrop-blur-sm" x-data="{
                scrollContainer: null,
                showLeftArrow: false,
                showRightArrow: true,
                init() {
                    this.scrollContainer = this.$refs.categoryScroll;
                    this.checkArrows();
                    this.scrollContainer.addEventListener('scroll', () => this.checkArrows());
                    window.addEventListener('resize', () => this.checkArrows());
                },
                checkArrows() {
                    if (!this.scrollContainer) return;
                    this.showLeftArrow = this.scrollContainer.scrollLeft > 10;
                    this.showRightArrow = this.scrollContainer.scrollLeft < (this.scrollContainer.scrollWidth - this.scrollContainer.clientWidth - 10);
                },
                scrollLeft() {
                    this.scrollContainer.scrollBy({ left: -200, behavior: 'smooth' });
                },
                scrollRight() {
                    this.scrollContainer.scrollBy({ left: 200, behavior: 'smooth' });
                }
            }">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
                    <!-- Left Arrow Button -->
                    <button 
                        x-show="showLeftArrow"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        @click="scrollLeft()"
                        class="absolute left-0 top-1/2 -translate-y-1/2 z-20 w-8 h-8 flex items-center justify-center bg-neutral-900/95 hover:bg-red-900/80 border border-neutral-700 hover:border-red-600 rounded-full shadow-lg transition-all duration-200"
                        style="display: none;">
                        <svg class="w-4 h-4 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    
                    <!-- Right Arrow Button -->
                    <button 
                        x-show="showRightArrow"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        @click="scrollRight()"
                        class="absolute right-0 top-1/2 -translate-y-1/2 z-20 w-8 h-8 flex items-center justify-center bg-neutral-900/95 hover:bg-red-900/80 border border-neutral-700 hover:border-red-600 rounded-full shadow-lg transition-all duration-200"
                        style="display: none;">
                        <svg class="w-4 h-4 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    
                    <!-- Fade indicators for mobile -->
                    <div x-show="showLeftArrow" class="absolute left-0 top-0 bottom-0 w-12 bg-gradient-to-r from-neutral-950 via-neutral-950/80 to-transparent pointer-events-none z-10"></div>
                    <div x-show="showRightArrow" class="absolute right-0 top-0 bottom-0 w-12 bg-gradient-to-l from-neutral-950 via-neutral-950/80 to-transparent pointer-events-none z-10"></div>
                    
                    <div x-ref="categoryScroll" 
                         class="flex items-center gap-3 sm:gap-4 py-2.5 overflow-x-auto scroll-smooth
                                [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                        @php
                            $currentCategory = request()->route('category') ?? request()->get('category');
                        @endphp
                        @foreach($categoryMenu as $item)
                        <a href="{{ route('news.category', ['category' => $item['category']]) }}" 
                           class="relative text-xs sm:text-sm font-semibold uppercase tracking-wider whitespace-nowrap transition-all py-1.5 px-3 sm:px-3 rounded-full flex-shrink-0
                                  {{ $currentCategory === $item['category'] 
                                     ? 'text-white bg-red-600 shadow-lg shadow-red-900/30' 
                                     : 'text-neutral-300 bg-neutral-800/50 hover:bg-red-900/40 hover:text-red-400' }}">
                            {{ $item['name'] }}
                            @if($item['count'] > 0)
                            <span class="ml-1 text-[10px] sm:text-xs {{ $currentCategory === $item['category'] ? 'text-red-200' : 'text-neutral-500' }}">({{ $item['count'] }})</span>
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

        <!-- Footer -->
        <footer class="border-t border-neutral-800 bg-neutral-950 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- Brand -->
                    <div class="md:col-span-1">
                        <a href="{{ route('news.index') }}" class="text-2xl font-bold text-red-600 hover:text-red-500 transition">
                            ATROCIDADES
                        </a>
                        <p class="mt-3 text-sm text-neutral-500 leading-relaxed">
                            Portal de notícias com cobertura de eventos mundiais, conflitos e acontecimentos relevantes.
                        </p>
                        <div class="mt-4 flex items-center gap-3">
                            <a href="#" class="text-neutral-500 hover:text-red-500 transition" aria-label="Twitter">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            </a>
                            <a href="#" class="text-neutral-500 hover:text-red-500 transition" aria-label="Telegram">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h3 class="text-sm font-semibold text-neutral-300 uppercase tracking-wider mb-4">Navegação</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('news.index') }}" class="text-sm text-neutral-500 hover:text-red-500 transition">Últimas Notícias</a></li>
                            <li><a href="{{ route('news.updating') }}" class="text-sm text-neutral-500 hover:text-red-500 transition">Em Atualização</a></li>
                            @auth
                            <li><a href="{{ route('news.create') }}" class="text-sm text-neutral-500 hover:text-red-500 transition">Enviar Notícia</a></li>
                            <li><a href="{{ route('news.my-submissions') }}" class="text-sm text-neutral-500 hover:text-red-500 transition">Minhas Publicações</a></li>
                            @endauth
                        </ul>
                    </div>

                    <!-- Categories -->
                    <div x-data="{ showMore: false }">
                        <h3 class="text-sm font-semibold text-neutral-300 uppercase tracking-wider mb-4">Categorias</h3>
                        <ul class="space-y-2">
                            @foreach($categoryMenu->take(3) as $item)
                            <li>
                                <a href="{{ route('news.category', ['category' => $item['category']]) }}" class="text-sm text-neutral-500 hover:text-red-500 transition">
                                    {{ $item['name'] }}
                                </a>
                            </li>
                            @endforeach
                            @if($categoryMenu->count() > 3)
                            <li class="relative">
                                <button @click="showMore = !showMore" class="text-sm text-neutral-500 hover:text-red-500 transition flex items-center gap-1">
                                    <span>Mais categorias</span>
                                    <svg class="w-3 h-3 transition-transform" :class="showMore ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <ul x-show="showMore" 
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    class="mt-2 space-y-2 pl-3 border-l border-neutral-800">
                                    @foreach($categoryMenu->skip(3) as $item)
                                    <li>
                                        <a href="{{ route('news.category', ['category' => $item['category']]) }}" class="text-sm text-neutral-500 hover:text-red-500 transition">
                                            {{ $item['name'] }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </li>
                            @endif
                        </ul>
                    </div>

                    <!-- Legal -->
                    <div>
                        <h3 class="text-sm font-semibold text-neutral-300 uppercase tracking-wider mb-4">Legal</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-sm text-neutral-500 hover:text-red-500 transition">Termos de Uso</a></li>
                            <li><a href="#" class="text-sm text-neutral-500 hover:text-red-500 transition">Política de Privacidade</a></li>
                            <li><a href="#" class="text-sm text-neutral-500 hover:text-red-500 transition">Diretrizes da Comunidade</a></li>
                            <li><a href="#" class="text-sm text-neutral-500 hover:text-red-500 transition">Contato</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Bottom Bar -->
                <div class="mt-10 pt-6 border-t border-neutral-800 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <p class="text-xs text-neutral-600">
                        &copy; {{ date('Y') }} Atrocidades. Todos os direitos reservados.
                    </p>
                    <p class="text-xs text-neutral-600">
                        <span class="text-red-600">⚠</span> Conteúdo adulto. Requer idade mínima de 18 anos.
                    </p>
                </div>
            </div>
        </footer>
    </div>
    
    @stack('scripts')
</body>
</html>
