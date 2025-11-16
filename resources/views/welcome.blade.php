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
