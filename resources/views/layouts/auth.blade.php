<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Atrocidades')</title>
    @vite(['resources/css/app.css'])
    <style>
        /* Headlines Typewriter Background */
        .headlines-container {
            position: fixed;
            inset: 0;
            overflow: hidden;
            z-index: 0;
            pointer-events: none;
        }

        .headline-card {
            position: absolute;
            background: rgba(23, 23, 23, 0.6);
            border: 1px solid rgba(64, 64, 64, 0.3);
            border-radius: 8px;
            padding: 12px 16px;
            max-width: 300px;
            backdrop-filter: blur(4px);
            opacity: 0;
            transform: translateY(20px);
            animation: floatUp 15s ease-out forwards;
        }

        .headline-card .category {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #dc2626;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .headline-card .title {
            font-size: 13px;
            color: #a3a3a3;
            line-height: 1.4;
            overflow: hidden;
        }

        .headline-card .title .cursor {
            display: inline-block;
            width: 2px;
            height: 14px;
            background: #dc2626;
            margin-left: 2px;
            animation: blink 0.8s infinite;
            vertical-align: middle;
        }

        @keyframes floatUp {
            0% {
                opacity: 0;
                transform: translateY(100vh);
            }
            5% {
                opacity: 0.7;
            }
            90% {
                opacity: 0.7;
            }
            100% {
                opacity: 0;
                transform: translateY(-100px);
            }
        }

        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }

        /* Subtle grid background */
        .bg-grid {
            background-image: 
                linear-gradient(rgba(64, 64, 64, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(64, 64, 64, 0.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        /* Gradient overlay */
        .gradient-overlay {
            background: 
                radial-gradient(ellipse at top, rgba(0,0,0,0) 0%, rgba(0,0,0,0.8) 100%),
                radial-gradient(ellipse at bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.8) 100%);
        }
    </style>
</head>
<body class="bg-black text-neutral-300 antialiased min-h-screen">
    <!-- Background Grid -->
    <div class="fixed inset-0 bg-grid"></div>
    
    <!-- Gradient Overlay -->
    <div class="fixed inset-0 gradient-overlay"></div>

    <!-- Headlines Background -->
    <div class="headlines-container" id="headlines-bg"></div>

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
                    
                    <!-- News Link -->
                    <a href="{{ route('news.index') }}" class="text-neutral-400 hover:text-neutral-200 transition text-sm">
                        Notícias
                    </a>
                    
                    @auth
                        <!-- Profile Link for logged users -->
                        <a href="{{ route('profile.show', auth()->user()) }}" class="text-neutral-400 hover:text-neutral-200 transition text-sm">
                            Perfil
                        </a>
                    @else
                        <!-- Login Link (only show if not on login page) -->
                        @if(!request()->routeIs('login'))
                        <a href="{{ route('login') }}" class="text-neutral-400 hover:text-neutral-200 transition text-sm">
                            Login
                        </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="relative z-10">
        @yield('content')
    </main>

    <!-- Headlines Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const headlines = @json($headlines ?? []);
            const container = document.getElementById('headlines-bg');
            
            if (!headlines.length || !container) return;

            function createHeadlineCard(headline, index) {
                const card = document.createElement('div');
                card.className = 'headline-card';
                
                // Random position
                const left = Math.random() * (window.innerWidth - 320);
                card.style.left = left + 'px';
                card.style.animationDelay = (index * 2) + 's';
                
                card.innerHTML = `
                    <div class="category">${headline.category || 'Notícia'}</div>
                    <div class="title">
                        <span class="text"></span><span class="cursor"></span>
                    </div>
                `;
                
                container.appendChild(card);
                
                // Typewriter effect
                const textSpan = card.querySelector('.text');
                const title = headline.title || '';
                let charIndex = 0;
                
                setTimeout(() => {
                    const typeInterval = setInterval(() => {
                        if (charIndex < title.length) {
                            textSpan.textContent += title[charIndex];
                            charIndex++;
                        } else {
                            clearInterval(typeInterval);
                        }
                    }, 50);
                }, (index * 2000) + 750); // Wait for fade in + delay
                
                // Remove after animation
                setTimeout(() => {
                    card.remove();
                }, 15000 + (index * 2000));
            }

            // Initial batch
            headlines.slice(0, 5).forEach((headline, index) => {
                createHeadlineCard(headline, index);
            });

            // Continue spawning
            let currentIndex = 5;
            setInterval(() => {
                if (currentIndex >= headlines.length) {
                    currentIndex = 0;
                }
                createHeadlineCard(headlines[currentIndex], 0);
                currentIndex++;
            }, 3000);
        });
    </script>
</body>
</html>
