@extends('layouts.auth')

@section('title', 'Atrocidades')

@section('content')
<div class="min-h-[calc(100vh-57px)] flex items-center justify-center px-4 py-12">
    <div class="text-center max-w-lg mx-auto">
        <!-- Logo -->
        <h1 class="text-5xl md:text-6xl font-bold text-neutral-300 hover:text-red-600 transition mb-4">
            ATROCIDADES
        </h1>
        
        <p class="text-neutral-500 text-sm mb-10">
            Portal de notícias
        </p>

        <!-- Action Buttons -->
        <div class="flex flex-col gap-3 max-w-xs mx-auto">
            @auth
                <a href="{{ route('news.index') }}" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-medium text-sm text-center">
                    Ver Notícias
                </a>
            @else
                <a href="{{ route('news.index') }}" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-medium text-sm text-center">
                    Ver Notícias
                </a>
                <a href="{{ route('invite.validate') }}" class="px-6 py-3 bg-neutral-800 hover:bg-neutral-700 text-neutral-200 rounded-lg transition font-medium text-sm text-center border border-neutral-700">
                    Entrar com Convite
                </a>
                <a href="{{ route('guest.purchase.index') }}" class="px-6 py-3 text-neutral-400 hover:text-neutral-300 transition text-sm text-center">
                    Comprar Convite
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection
