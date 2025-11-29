@extends('layouts.app')

@section('title', 'Feed de Notícias - ' . config('app.name'))

@push('styles')
<style>
    /* Breaking news ticker */
    .ticker-wrapper {
        overflow: hidden;
        white-space: nowrap;
    }
    .ticker-content {
        display: inline-block;
        animation: ticker 30s linear infinite;
    }
    @keyframes ticker {
        0% { transform: translateX(100%); }
        100% { transform: translateX(-100%); }
    }
    
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
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-b from-[#0a0a0a] via-[#1a0a0a] to-[#0a0a0a]">
    
    <!-- Breaking News Ticker -->
    @if($news->count() > 0)
    <div class="bg-gradient-to-r from-red-900/80 via-red-800/80 to-red-900/80 border-b border-red-700/50">
        <div class="max-w-7xl mx-auto px-4 flex items-center">
            <div class="bg-red-600 px-3 sm:px-4 py-2 flex items-center gap-2 flex-shrink-0 -ml-4">
                <span class="animate-pulse w-2 h-2 bg-white rounded-full"></span>
                <span class="text-white font-bold text-xs sm:text-sm uppercase tracking-wider">Urgente</span>
            </div>
            <div class="ticker-wrapper flex-1 py-2 px-4">
                <div class="ticker-content">
                    @foreach($news->take(5) as $item)
                        <a href="{{ route('news.show', $item->slug) }}" class="text-white/90 hover:text-white mx-6 sm:mx-8 text-sm">
                            <span class="text-red-400">●</span> {{ $item->title }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Hero Section - Featured News -->
    @if($featured->count() > 0)
    @php $featuredVideo = $featured->first(); @endphp
    <section class="relative">
        <div class="max-w-7xl mx-auto px-4 py-6 sm:py-8">
            <a href="{{ route('news.show', $featuredVideo->slug) }}" class="block group">
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
                                @if($featuredVideo->category)
                                    <span class="bg-red-900/30 text-red-400 text-xs px-2 sm:px-3 py-1 rounded-full uppercase">
                                        {{ \App\Helpers\CategoryHelper::format($featuredVideo->category) }}
                                    </span>
                                @endif
                                @if($featuredVideo->is_sensitive)
                                    <span class="bg-yellow-600/80 text-yellow-100 text-xs px-2 py-1 rounded flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="hidden sm:inline">MEMBROS</span>
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
            </a>
        </div>
    </section>
    @endif

    <!-- Main Content Grid -->
    <section class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                
                <!-- Main News Feed -->
                <div class="lg:col-span-2">
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
                            <a href="{{ route('news.show', $video->slug) }}" 
                               class="flex-shrink-0 w-[calc(50%-0.5rem)] sm:w-[calc(50%-0.5rem)] group"
                               style="scroll-snap-align: start;">
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
                                        
                                        <!-- Sensitive Badge (+18) - TOP LEFT -->
                                        @if($video->is_sensitive)
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
                            </a>
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
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        @forelse($news as $video)
                        <a href="{{ route('news.show', $video->slug) }}" class="group">
                            <article class="news-card bg-gradient-to-b from-gray-800/30 to-gray-900/30 rounded-xl overflow-hidden border border-gray-700/20 hover:border-red-600/40 transition-all duration-300 hover:shadow-lg hover:shadow-red-900/10">
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
                                        
                                        <!-- Gradient overlay -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                                        
                                        <!-- Sensitive Badge (+18) - TOP LEFT -->
                                        @if($video->is_sensitive)
                                            <span class="absolute top-2 left-2 bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded">
                                                +18
                                            </span>
                                        @endif
                                        
                                        <!-- Category Badge - TOP RIGHT -->
                                        @if($video->category)
                                            <span class="absolute top-2 right-2 bg-black/70 text-white text-xs px-2 py-1 rounded uppercase">
                                                {{ \App\Helpers\CategoryHelper::format($video->category) }}
                                            </span>
                                        @endif
                                    </div>                                <!-- Content -->
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
                        </a>
                        @empty
                        <div class="col-span-full text-center py-12">
                            <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <p class="text-gray-400 text-lg">Nenhuma notícia encontrada</p>
                        </div>
                        @endforelse
                    </div>
                    
                    <!-- Pagination -->
                    @if($news->hasPages())
                    <div class="mt-6 sm:mt-8">
                        {{ $news->links() }}
                    </div>
                    @endif
                </div>
                
                <!-- Sidebar -->
                <aside class="space-y-6">
                    <!-- Most Viewed (Trending) - Moved to top -->
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
