<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Atrocidades' }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-black text-neutral-300 antialiased">
    <div class="min-h-screen">
        <nav class="border-b border-neutral-800 bg-neutral-950/80 backdrop-blur-lg sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="text-2xl font-bold text-neutral-400 hover:text-red-700 transition">
                            ATROCIDADES
                        </a>
                    </div>
                    
                    <div class="flex items-center gap-6">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-neutral-400 hover:text-neutral-200 transition">
                                Dashboard
                            </a>
                            <a href="{{ route('profile.edit') }}" class="text-neutral-400 hover:text-neutral-200 transition">
                                Convites
                            </a>
                            @if(auth()->user()->is_admin)
                                <a href="{{ route('admin.dashboard') }}" class="text-red-700 hover:text-red-500 transition">
                                    Admin
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-neutral-500 hover:text-neutral-300 transition">
                                    Sair
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-neutral-400 hover:text-neutral-200 transition">
                                Login
                            </a>
                        @endauth
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
