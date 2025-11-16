<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ominous</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/creepy-background.css', 'resources/js/creepy-background.js'])
</head>
<body class="antialiased">
    <div id="creepy-background-container">
        <div id="background-vignette"></div>
        <div id="background-noise"></div>
    </div>
    
    <div id="horror-controls">
        <button id="sound-toggle" class="horror-btn" title="Alternar Som">
            <svg class="icon sound-on" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                <path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path>
            </svg>
            <svg class="icon sound-off" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                <line x1="23" y1="9" x2="17" y2="15"></line>
                <line x1="17" y1="9" x2="23" y2="15"></line>
            </svg>
        </button>
        <button id="apparitions-toggle" class="horror-btn" title="Alternar Aparições">
            <svg class="icon apparitions-on" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
            <svg class="icon apparitions-off" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
            </svg>
        </button>
    </div>

    {{-- O conteúdo principal do seu site viria aqui.
         Para demonstração, deixarei um texto simples. --}}
    <div class="relative py-8 px-6 sm:py-16 lg:px-8 z-10 text-center">
        <h1 class="text-4xl font-extrabold tracking-tight text-neutral-300 sm:text-5xl md:text-6xl">
            <span class="block"><img src="{{ asset('creepy_logo/ominous_logo.png') }}" alt="Ominous" class="mx-auto h-[15rem] logo-glow"></span>
        </h1>
        <p class="mt-6 max-w-lg mx-auto text-xl text-neutral-400 sm:max-w-3xl">
            Algo te observa nas sombras.
        </p>
    </div>
</body>
</html>
