<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Atrocidades' }}</title>
    @vite(['resources/css/app.css', 'resources/css/creepy-background.css', 'resources/js/creepy-background.js'])
</head>
<body class="antialiased">
    <div id="creepy-background-container">
        <div id="background-vignette"></div>
        <div id="background-noise"></div>
    </div>

    <div class="relative min-h-screen z-10">
        <!-- Header -->
        <header class="border-b border-neutral-800 bg-neutral-950/80 backdrop-blur-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                <a href="{{ route('home') }}" class="text-2xl font-bold text-neutral-400 hover:text-red-700 transition">
                    ATROCIDADES
                </a>
                <div class="flex gap-4">
                    <a href="{{ route('login') }}" class="px-4 py-2 text-neutral-400 hover:text-neutral-200 transition">
                        Login
                    </a>
                    <a href="{{ route('invite.validate') }}" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-semibold">
                        Tenho um Convite
                    </a>
                </div>
            </div>
        </header>

        {{ $slot }}
    </div>
</body>
</html>
