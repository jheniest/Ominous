@extends('layouts.app')

@php
use App\Helpers\SeoHelper;
@endphp

@section('title', 'Últimas Notícias - Atrocidades')
@section('meta_description', 'Acompanhe as últimas notícias e acontecimentos em tempo real. Cobertura completa de eventos mundiais, conflitos e informações verificadas.')
@section('meta_keywords', 'últimas notícias, notícias de hoje, acontecimentos, atualidades, cobertura ao vivo, notícias em tempo real')
@section('canonical', route('news.index'))

@section('og_title', 'Últimas Notícias - Atrocidades')
@section('og_description', 'Acompanhe as últimas notícias e acontecimentos em tempo real.')
@section('twitter_title', 'Últimas Notícias - Atrocidades')
@section('twitter_description', 'Acompanhe as últimas notícias e acontecimentos em tempo real.')

@push('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "CollectionPage",
    "name": "Últimas Notícias",
    "description": "Feed de notícias e acontecimentos em tempo real",
    "url": "{{ route('news.index') }}",
    "isPartOf": {
        "@@type": "WebSite",
        "name": "Atrocidades",
        "url": "{{ config('app.url') }}"
    }
}
</script>
@endpush

@push('styles')
<style>
    
    /* Custom scrollbar for carousel */
    .carousel-container::-webkit-scrollbar {
        height: 6px;
    }
    .carousel-container::-webkit-scrollbar-track {
        background: rgba(31, 31, 31, 0.5);
        border-radius: 3px;
    }
    .carousel-container::-webkit-scrollbar-thumb {
        background: rgba(139, 0, 0, 0.5);
        border-radius: 3px;
    }
    .carousel-container::-webkit-scrollbar-thumb:hover {
        background: rgba(139, 0, 0, 0.8);
    }
    
    /* Ensure consistent card heights */
    .news-card {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .news-card .media-wrapper {
        position: relative;
        width: 100%;
        aspect-ratio: 16/9;
        overflow: hidden;
        flex-shrink: 0;
    }
    .news-card .media-wrapper img,
    .news-card .media-wrapper video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .news-card .content-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    /* Members Only Badge Animation */
    .members-badge {
        background: linear-gradient(135deg, #b91c1c, #7f1d1d);
        animation: pulse-glow 2s ease-in-out infinite;
    }
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 5px rgba(185, 28, 28, 0.5); }
        50% { box-shadow: 0 0 15px rgba(185, 28, 28, 0.8); }
    }
    
    /* Updating card sticky effect */
    .updating-card {
        box-shadow: 0 4px 20px rgba(220, 38, 38, 0.15);
    }
    
    /* Sidebar scroll content clips behind updating card */
    @media (min-width: 1024px) {
        .sidebar-scroll-content {
            position: relative;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-b from-[#0a0a0a] via-[#1a0a0a] to-[#0a0a0a]" x-data="{ 
    showPaywall: false, 
    paywallTitle: '',
    paywallSlug: ''
}">
    
    <!-- Paywall Modal -->
    <div x-show="showPaywall" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display: none;">
        <div class="absolute inset-0 bg-black/90 backdrop-blur-sm" @click="showPaywall = false"></div>
        <div class="relative bg-gradient-to-b from-gray-900 to-black rounded-2xl max-w-md w-full p-8 border border-red-900/50 shadow-2xl shadow-red-900/20"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100">
            
            <!-- Close button -->
            <button @click="showPaywall = false" class="absolute top-4 right-4 text-gray-500 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            
            <!-- Lock Icon -->
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 bg-red-900/30 rounded-full flex items-center justify-center border-2 border-red-700/50">
                    <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
            </div>
            
            <!-- Title -->
            <h3 class="text-2xl font-bold text-white text-center mb-2">Conteúdo Exclusivo</h3>
            <p class="text-gray-400 text-center mb-2">para Membros</p>
            
            <!-- Article Title -->
            <div class="bg-gray-800/50 rounded-lg p-4 mb-6 border border-gray-700/50">
                <p class="text-gray-300 text-sm text-center line-clamp-2" x-text="paywallTitle"></p>
            </div>
            
            <!-- Description -->
            <p class="text-gray-400 text-center text-sm mb-6">
                Este conteúdo está disponível apenas para membros registrados da comunidade. 
                Faça login ou adquira um convite para ter acesso.
            </p>
            
            <!-- Benefits -->
            <div class="space-y-3 mb-6">
                <div class="flex items-center gap-3 text-sm text-gray-300">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Acesso a todo conteúdo exclusivo</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-300">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Comentar e interagir nas notícias</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-300">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Enviar seu próprio conteúdo</span>
                </div>
            </div>
            
            <!-- Buttons -->
            <div class="space-y-3">
                <a href="{{ route('login') }}" class="block w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg text-center transition-colors">
                    Fazer Login
                </a>
                <div class="flex gap-3">
                    <a href="{{ route('invite.validate') }}" class="flex-1 bg-gray-800 hover:bg-gray-700 text-white font-semibold py-3 rounded-lg text-center transition-colors border border-gray-700">
                        Tenho Convite
                    </a>
                    <a href="{{ route('guest.purchase.index') }}" class="flex-1 bg-gradient-to-r from-yellow-600 to-yellow-700 hover:from-yellow-500 hover:to-yellow-600 text-white font-semibold py-3 rounded-lg text-center transition-colors">
                        Comprar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section - Featured News -->
    @if($featured->count() > 0)
    @php $featuredVideo = $featured->first(); @endphp
    <section class="relative">
        <div class="max-w-7xl mx-auto px-4 py-6 sm:py-8">
            @if($featuredVideo->is_members_only && !auth()->check())
            <div @click="paywallTitle = '{{ addslashes($featuredVideo->title) }}'; paywallSlug = '{{ $featuredVideo->slug }}'; showPaywall = true" class="block group cursor-pointer">
            @else
            <a href="{{ route('news.show', $featuredVideo->slug) }}" class="block group">
            @endif
                <div class="relative rounded-xl overflow-hidden shadow-2xl shadow-red-900/20">
                    <!-- Media Container with Fixed Aspect Ratio -->
                    <div class="relative w-full aspect-video">
                        @if($featuredVideo->thumbnail_url)
                            <img 
                                src="{{ $featuredVideo->thumbnail_url }}" 
                                alt="{{ $featuredVideo->title }}"
                                class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700"
                            >
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center">
                                <svg class="w-16 h-16 sm:w-20 sm:h-20 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
                        
                        <!-- Content -->
                        <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6 lg:p-8">
                            <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-2 sm:mb-3">
                                <span class="bg-red-600 text-white text-xs font-bold px-2 sm:px-3 py-1 rounded uppercase tracking-wider">
                                    Destaque
                                </span>
                                @if($featuredVideo->is_members_only)
                                    <span class="members-badge text-white text-xs font-bold px-2 sm:px-3 py-1 rounded flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                        </svg>
                                        MEMBROS
                                    </span>
                                @endif
                                @if($featuredVideo->category)
                                    <span class="bg-red-900/30 text-red-400 text-xs px-2 sm:px-3 py-1 rounded-full uppercase">
                                        {{ \App\Helpers\CategoryHelper::format($featuredVideo->category) }}
                                    </span>
                                @endif
                                @if($featuredVideo->is_sensitive)
                                    <span class="bg-yellow-600/80 text-yellow-100 text-xs px-2 py-1 rounded">
                                        +18
                                    </span>
                                @endif
                            </div>
                            <h2 class="text-xl sm:text-2xl lg:text-4xl font-bold text-white mb-2 group-hover:text-red-400 transition-colors line-clamp-2">
                                {{ $featuredVideo->title }}
                            </h2>
                            <p class="text-gray-300 text-sm sm:text-base lg:text-lg line-clamp-2 mb-3 hidden sm:block">
                                {{ Str::limit($featuredVideo->description ?? $featuredVideo->summary, 150) }}
                            </p>
                            <div class="flex items-center text-xs sm:text-sm text-gray-400 gap-3 sm:gap-4">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $featuredVideo->created_at->diffForHumans() }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    {{ number_format($featuredVideo->views_count) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @if($featuredVideo->is_members_only && !auth()->check())
            </div>
            @else
            </a>
            @endif
        </div>
    </section>
    @endif

    <!-- Main Content Grid -->
    <section class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                
                <!-- Main News Feed -->
                <div class="lg:col-span-2">
                    <!-- Card EM ATUALIZAÇÃO - Sticky no topo do conteúdo principal -->
                    @if(isset($updatingNews) && $updatingNews->count() > 0)
                    <div class="lg:sticky lg:top-32 z-30 mb-6">
                        <div class="bg-gradient-to-b from-red-950/90 to-gray-900/90 rounded-xl border border-red-600/50 shadow-lg shadow-red-900/30 backdrop-blur-sm overflow-hidden">
                            <!-- Header -->
                            <div class="flex items-center justify-between px-4 py-3 bg-red-900/40 border-b border-red-800/50">
                                <div class="flex items-center gap-2">
                                    <span class="relative flex h-3 w-3">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                    </span>
                                    <span class="text-red-400 font-bold text-sm uppercase tracking-wider">Em Atualização</span>
                                    <span class="text-gray-500 text-xs">({{ $updatingNews->count() }})</span>
                                </div>
                                <a href="{{ route('news.updating') }}" 
                                   class="text-xs text-gray-400 hover:text-red-400 transition-colors flex items-center gap-1 group">
                                    Ver todas
                                    <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                            
                            <!-- Lista de Headlines -->
                            <ul class="divide-y divide-gray-800/50">
                                @foreach($updatingNews->take(3) as $updating)
                                <li>
                                    <a href="{{ route('news.show', $updating->slug) }}" 
                                       class="flex items-center gap-3 px-4 py-2.5 hover:bg-red-900/20 transition-colors group">
                                        <span class="relative flex h-2 w-2 flex-shrink-0">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-600"></span>
                                        </span>
                                        <span class="text-gray-200 text-sm group-hover:text-red-400 transition-colors line-clamp-1 flex-1">
                                            {{ $updating->title }}
                                        </span>
                                        <span class="text-gray-600 text-xs flex-shrink-0">
                                            {{ $updating->updating_since ? $updating->updating_since->diffForHumans(null, true) : $updating->created_at->diffForHumans(null, true) }}
                                        </span>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif

                    <!-- Em Alta Carousel (inside left section) -->
                    @if($trending->count() > 0)
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2 sm:gap-3">
                                <span class="w-1 h-6 sm:h-8 bg-red-600 rounded"></span>
                                Em Alta
                            </h2>
                            <div class="flex gap-2">
                                <button onclick="scrollCarousel(-1)" class="p-2 bg-gray-800/50 hover:bg-red-900/50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>
                                <button onclick="scrollCarousel(1)" class="p-2 bg-gray-800/50 hover:bg-red-900/50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <div id="newsCarousel" class="carousel-container flex gap-4 overflow-x-auto scroll-smooth pb-4" style="scroll-snap-type: x mandatory;">
                            @foreach($trending as $video)
                            @if($video->is_members_only && !auth()->check())
                            <div @click="paywallTitle = '{{ addslashes($video->title) }}'; paywallSlug = '{{ $video->slug }}'; showPaywall = true"
                               class="flex-shrink-0 w-[calc(50%-0.5rem)] sm:w-[calc(50%-0.5rem)] group cursor-pointer"
                               style="scroll-snap-align: start;">
                            @else
                            <a href="{{ route('news.show', $video->slug) }}" 
                               class="flex-shrink-0 w-[calc(50%-0.5rem)] sm:w-[calc(50%-0.5rem)] group"
                               style="scroll-snap-align: start;">
                            @endif
                                <div class="news-card bg-gradient-to-b from-gray-800/50 to-gray-900/50 rounded-xl overflow-hidden border border-gray-700/30 hover:border-red-600/50 transition-all duration-300 hover:shadow-lg hover:shadow-red-900/20">
                                    <!-- Media Container -->
                                    <div class="media-wrapper">
                                        @if($video->thumbnail_url)
                                            <img 
                                                src="{{ $video->thumbnail_url }}" 
                                                alt="{{ $video->title }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                            >
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-gray-700 to-gray-800 flex items-center justify-center">
                                                <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        
                                        <!-- Members Only Badge - TOP LEFT -->
                                        @if($video->is_members_only)
                                            <span class="absolute top-2 left-2 members-badge text-white text-[10px] font-bold px-1.5 py-0.5 rounded flex items-center gap-1">
                                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                </svg>
                                                MEMBROS
                                            </span>
                                        @elseif($video->is_sensitive)
                                            <span class="absolute top-2 left-2 bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded">
                                                +18
                                            </span>
                                        @endif
                                        
                                        <!-- Category Badge - TOP RIGHT -->
                                        @if($video->category)
                                            <span class="absolute top-2 right-2 bg-black/70 text-white text-[10px] px-1.5 py-0.5 rounded uppercase">
                                                {{ \App\Helpers\CategoryHelper::format($video->category) }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Content -->
                                    <div class="content-wrapper p-3 sm:p-4">
                                        <h3 class="text-white font-semibold mt-1 line-clamp-2 group-hover:text-red-400 transition-colors text-sm sm:text-base">
                                            {{ $video->title }}
                                        </h3>
                                        <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">
                                            <span>{{ $video->created_at->diffForHumans() }}</span>
                                            <span>{{ number_format($video->views_count) }} views</span>
                                        </div>
                                    </div>
                                </div>
                            @if($video->is_members_only && !auth()->check())
                            </div>
                            @else
                            </a>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Mais Comentadas -->
                    @if(isset($mostCommented) && $mostCommented->count() > 0)
                    <div class="mb-8">
                        <h2 class="text-xl sm:text-2xl font-bold text-white mb-4 flex items-center gap-2 sm:gap-3">
                            <span class="w-1 h-6 sm:h-8 bg-red-600 rounded"></span>
                            Mais Comentadas
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($mostCommented as $video)
                            <a href="{{ route('news.show', $video->slug) }}" class="flex gap-3 group bg-gradient-to-r from-gray-800/30 to-gray-900/30 rounded-lg p-3 border border-gray-700/20 hover:border-red-600/40 transition-all">
                                <!-- Thumbnail -->
                                <div class="w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden">
                                    @if($video->thumbnail_url)
                                        <img 
                                            src="{{ $video->thumbnail_url }}" 
                                            alt="{{ $video->title }}"
                                            class="w-full h-full object-cover"
                                        >
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-gray-700 to-gray-800 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm text-white font-medium line-clamp-2 group-hover:text-red-400 transition-colors">
                                        {{ $video->title }}
                                    </h4>
                                    <div class="flex items-center gap-2 mt-2 text-xs text-gray-500">
                                        <span class="flex items-center gap-1 text-red-400">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                            {{ $video->comments_count }}
                                        </span>
                                        <span>{{ $video->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <h2 class="text-xl sm:text-2xl font-bold text-white mb-4 sm:mb-6 flex items-center gap-2 sm:gap-3">
                        <span class="w-1 h-6 sm:h-8 bg-red-600 rounded"></span>
                        Todas as Notícias
                    </h2>
                    
                    <div x-data="{ 
                        page: 1, 
                        loading: false, 
                        hasMore: {{ $news->hasMorePages() ? 'true' : 'false' }},
                        async loadMore() {
                            if (this.loading || !this.hasMore) return;
                            this.loading = true;
                            this.page++;
                            try {
                                const response = await fetch(`{{ url()->current() }}?page=${this.page}`, {
                                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                                });
                                const html = await response.text();
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const newCards = doc.querySelectorAll('#news-grid .news-item');
                                const container = document.getElementById('news-grid');
                                newCards.forEach(card => container.appendChild(card.cloneNode(true)));
                                this.hasMore = doc.querySelector('[data-has-more=\"true\"]') !== null;
                            } catch (error) {
                                console.error('Erro ao carregar:', error);
                                this.page--;
                            }
                            this.loading = false;
                        }
                    }">
                        {{-- Substitua o bloco @forelse inteiro por este código corrigido --}}

                        <div id="news-grid" data-has-more="{{ $news->hasMorePages() ? 'true' : 'false' }}">
                        @forelse($news as $index => $video)
                        @if($index === 0)
                            {{-- PRIMEIRO CARD - Horizontal Layout --}}
                            <div class="news-item mb-6">
                                @if($video->is_members_only && !auth()->check())
                                <div @click="paywallTitle = '{{ addslashes($video->title) }}'; paywallSlug = '{{ $video->slug }}'; showPaywall = true" class="group cursor-pointer">
                                @else
                                <a href="{{ route('news.show', $video->slug) }}" class="group block">
                                @endif
                                    <article class="flex flex-col sm:flex-row bg-gradient-to-r from-gray-800/40 to-gray-900/40 rounded-xl overflow-hidden border border-gray-700/30 hover:border-red-600/50 transition-all duration-300 hover:shadow-lg hover:shadow-red-900/20">
                                        <!-- Image Left -->
                                        <div class="relative w-full sm:w-1/2 aspect-video sm:aspect-auto sm:min-h-[200px] overflow-hidden flex-shrink-0">
                                            @if($video->thumbnail_url)
                                                <img 
                                                    src="{{ $video->thumbnail_url }}" 
                                                    alt="{{ $video->title }}"
                                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                                >
                                            @else
                                                <div class="absolute inset-0 bg-gradient-to-br from-gray-700 to-gray-800 flex items-center justify-center">
                                                    <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                            
                                            <!-- Badges -->
                                            @if($video->is_members_only)
                                                <span class="absolute top-3 left-3 members-badge text-white text-xs font-bold px-2.5 py-1 rounded flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    MEMBROS
                                                </span>
                                            @elseif($video->is_sensitive)
                                                <span class="absolute top-3 left-3 bg-red-600 text-white text-xs font-bold px-2.5 py-1 rounded">
                                                    +18
                                                </span>
                                            @endif
                                            
                                            @if($video->category)
                                                <span class="absolute top-3 right-3 bg-black/70 text-white text-xs px-2.5 py-1 rounded uppercase">
                                                    {{ \App\Helpers\CategoryHelper::format($video->category) }}
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <!-- Content Right -->
                                        <div class="flex-1 p-4 sm:p-5 flex flex-col justify-center">
                                            <h3 class="text-white font-bold text-lg sm:text-xl line-clamp-2 group-hover:text-red-400 transition-colors">
                                                {{ $video->title }}
                                            </h3>
                                            <p class="text-gray-400 text-sm mt-2 line-clamp-3">
                                                {{ Str::limit($video->description ?? $video->summary, 150) }}
                                            </p>
                                            <div class="flex items-center gap-4 mt-4 text-xs text-gray-500">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $video->created_at->diffForHumans() }}
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    {{ number_format($video->views_count) }} views
                                                </span>
                                            </div>
                                        </div>
                                    </article>
                                @if($video->is_members_only && !auth()->check())
                                </div>
                                @else
                                </a>
                                @endif
                            </div>
                            
                            {{-- Início do grid DENTRO do loop --}}
                            @if($news->count() > 1)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            @endif
                            
                        @else
                            {{-- DEMAIS CARDS - Grid 2 colunas --}}
                            <div class="news-item">
                                @if($video->is_members_only && !auth()->check())
                                <div @click="paywallTitle = '{{ addslashes($video->title) }}'; paywallSlug = '{{ $video->slug }}'; showPaywall = true" class="group cursor-pointer h-full">
                                @else
                                <a href="{{ route('news.show', $video->slug) }}" class="group block h-full">
                                @endif
                                    <article class="news-card bg-gradient-to-b from-gray-800/30 to-gray-900/30 rounded-xl overflow-hidden border border-gray-700/20 hover:border-red-600/40 transition-all duration-300 hover:shadow-lg hover:shadow-red-900/10 h-full">
                                        <!-- Media Container -->
                                        <div class="media-wrapper">
                                            @if($video->thumbnail_url)
                                                <img 
                                                    src="{{ $video->thumbnail_url }}" 
                                                    alt="{{ $video->title }}"
                                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                                >
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-gray-700 to-gray-800 flex items-center justify-center">
                                                    <svg class="w-10 h-10 sm:w-12 sm:h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                            
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                                            
                                            @if($video->is_members_only)
                                                <span class="absolute top-2 left-2 members-badge text-white text-xs font-bold px-2 py-0.5 rounded flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    MEMBROS
                                                </span>
                                            @elseif($video->is_sensitive)
                                                <span class="absolute top-2 left-2 bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded">
                                                    +18
                                                </span>
                                            @endif
                                            
                                            @if($video->category)
                                                <span class="absolute top-2 right-2 bg-black/70 text-white text-xs px-2 py-1 rounded uppercase">
                                                    {{ \App\Helpers\CategoryHelper::format($video->category) }}
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <!-- Content -->
                                        <div class="content-wrapper p-3 sm:p-4">
                                            <h3 class="text-white font-semibold line-clamp-2 group-hover:text-red-400 transition-colors text-sm sm:text-base">
                                                {{ $video->title }}
                                            </h3>
                                            <p class="text-gray-400 text-xs sm:text-sm mt-2 line-clamp-2">
                                                {{ Str::limit($video->description ?? $video->summary, 80) }}
                                            </p>
                                            <div class="flex items-center justify-between mt-3 text-xs text-gray-500">
                                                <span>{{ $video->created_at->diffForHumans() }}</span>
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    {{ number_format($video->views_count) }}
                                                </span>
                                            </div>
                                        </div>
                                    </article>
                                @if($video->is_members_only && !auth()->check())
                                </div>
                                @else
                                </a>
                                @endif
                            </div>
                        @endif
                        @empty
                        <div class="col-span-full text-center py-12">
                            <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <p class="text-gray-400 text-lg">Nenhuma notícia encontrada</p>
                        </div>
                        @endforelse

                        {{-- Fecha o grid DENTRO do loop --}}
                        @if($news->count() > 1)
                        </div>
                        @endif
                        </div>
                        
                        <!-- Load More Button -->
                        <div class="mt-8 text-center" x-show="hasMore">
                            <button 
                                @click="loadMore()"
                                :disabled="loading"
                                class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-gradient-to-r from-red-700 to-red-800 hover:from-red-600 hover:to-red-700 disabled:from-gray-700 disabled:to-gray-800 text-white font-semibold rounded-lg transition-all duration-300 shadow-lg hover:shadow-red-900/30 disabled:cursor-not-allowed min-w-[200px]">
                                <!-- Loader Spinner -->
                                <svg x-show="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="loading ? 'Carregando...' : 'Carregar Mais Notícias'"></span>
                            </button>
                        </div>
                        
                        <!-- No more content message -->
                        <div class="mt-6 text-center" x-show="!hasMore && page > 1">
                            <p class="text-gray-500 text-sm">Você chegou ao fim das notícias</p>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <aside class="space-y-6">
                    <!-- Most Viewed (Trending) -->
                    @if($trending->count() > 0)
                    <div class="bg-gradient-to-b from-gray-800/40 to-gray-900/40 rounded-xl p-4 sm:p-5 border border-gray-700/30">
                        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z"/>
                            </svg>
                            Mais Lidas
                        </h3>
                        <div class="space-y-3">
                            @foreach($trending->take(5) as $index => $video)
                            <a href="{{ route('news.show', $video->slug) }}" class="flex items-start gap-3 group">
                                <span class="text-2xl font-bold text-red-600/50 group-hover:text-red-500 transition-colors w-6 flex-shrink-0">
                                    {{ $index + 1 }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm text-gray-300 group-hover:text-red-400 transition-colors line-clamp-2">
                                        {{ $video->title }}
                                    </h4>
                                    <span class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        {{ number_format($video->views_count) }}
                                    </span>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Categories -->
                    @if($categories->count() > 0)
                    <div class="bg-gradient-to-b from-gray-800/40 to-gray-900/40 rounded-xl p-4 sm:p-5 border border-gray-700/30">
                        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            Categorias
                        </h3>
                        <div class="space-y-2">
                            @foreach($categories as $cat)
                            <a href="{{ route('news.category', $cat) }}" 
                               class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-700/30 transition-colors group {{ $category === $cat ? 'bg-red-900/30' : '' }}">
                                <span class="text-gray-300 group-hover:text-red-400 transition-colors text-sm {{ $category === $cat ? 'text-red-400' : '' }}">
                                    {{ \App\Helpers\CategoryHelper::format($cat) }}
                                </span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Popular Tags -->
                    @if($popularTags->count() > 0)
                    <div class="bg-gradient-to-b from-gray-800/40 to-gray-900/40 rounded-xl p-4 sm:p-5 border border-gray-700/30">
                        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Tags Populares
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($popularTags as $tagItem)
                            <a href="{{ route('news.tag', $tagItem->slug) }}" 
                               class="bg-red-900/30 text-red-400 hover:bg-red-800/40 hover:text-red-300 px-3 py-1.5 rounded-full text-xs uppercase font-medium transition-colors {{ $tag === $tagItem->slug ? 'bg-red-700/50 text-red-300' : '' }}">
                                {{ strtoupper($tagItem->name) }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Members Only Section - Sticky -->
                    @if(isset($membersOnly) && $membersOnly->count() > 0)
                    <div class="lg:sticky lg:top-32 z-20">
                        <div class="bg-gradient-to-b from-red-950/40 to-gray-900/40 rounded-xl p-4 sm:p-5 border border-red-900/30">
                            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                                Exclusivo Membros
                            </h3>
                            <div class="space-y-3">
                                @foreach($membersOnly->take(5) as $item)
                                @if(auth()->check())
                                <a href="{{ route('news.show', $item->slug) }}" class="flex items-start gap-3 group">
                                @else
                                <div @click="paywallTitle = '{{ addslashes($item->title) }}'; paywallSlug = '{{ $item->slug }}'; showPaywall = true" class="flex items-start gap-3 group cursor-pointer">
                                @endif
                                    <!-- Thumbnail -->
                                    <div class="w-16 h-12 rounded-lg overflow-hidden flex-shrink-0 bg-gray-800">
                                        @if($item->thumbnail_url)
                                        <img 
                                            src="{{ $item->thumbnail_url }}" 
                                            alt="{{ $item->title }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                            loading="lazy"
                                        >
                                        @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        @endif
                                    </div>
                                    <!-- Content -->
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm text-gray-200 font-medium group-hover:text-red-400 transition-colors line-clamp-2 leading-snug">
                                            {{ $item->title }}
                                        </h4>
                                        <span class="text-xs text-gray-500 mt-1 block">
                                            {{ $item->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                @if(auth()->check())
                                </a>
                                @else
                                </div>
                                @endif
                                @endforeach
                            </div>
                            
                            @guest
                            <div class="mt-4 pt-4 border-t border-red-900/30">
                                <p class="text-xs text-gray-400 mb-3">Faça login para acessar conteúdo exclusivo</p>
                                <a href="{{ route('login') }}" class="block w-full text-center bg-red-600 hover:bg-red-700 text-white text-sm font-medium py-2 rounded-lg transition-colors">
                                    Entrar
                                </a>
                            </div>
                            @endguest
                        </div>
                    </div>
                    @endif
                </aside>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    function scrollCarousel(direction) {
        const carousel = document.getElementById('newsCarousel');
        const scrollAmount = 320; // Card width + gap
        carousel.scrollBy({
            left: direction * scrollAmount,
            behavior: 'smooth'
        });
    }
</script>
@endpush

@endsection
