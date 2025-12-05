@extends('layouts.app')

@section('title', 'Notícias em Atualização - ' . config('app.name'))

@push('styles')
<style>
    .updating-pulse {
        animation: updating-glow 2s ease-in-out infinite;
    }
    @keyframes updating-glow {
        0%, 100% { box-shadow: 0 0 5px rgba(220, 38, 38, 0.3); }
        50% { box-shadow: 0 0 20px rgba(220, 38, 38, 0.5); }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-b from-[#0a0a0a] via-[#1a0a0a] to-[#0a0a0a]">
    
    <!-- Header Section -->
    <section class="pt-8 pb-6">
        <div class="max-w-5xl mx-auto px-4">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
                <a href="{{ route('news.index') }}" class="hover:text-red-400 transition-colors">Feed</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-400">Em Atualização</span>
            </nav>

            <!-- Title -->
            <div class="flex items-center gap-4 mb-4">
                <div class="relative">
                    <span class="relative flex h-4 w-4">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500"></span>
                    </span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold text-white">
                    Notícias em <span class="text-red-500">Atualização</span>
                </h1>
            </div>
            
            <p class="text-gray-400 max-w-2xl">
                Estas notícias estão sendo atualizadas em tempo real conforme novas informações são confirmadas.
            </p>
        </div>
    </section>

    <!-- News List -->
    <section class="pb-12">
        <div class="max-w-5xl mx-auto px-4">
            @if($updatingNews->count() > 0)
            <div class="space-y-4">
                @foreach($updatingNews as $news)
                <article class="updating-pulse bg-gradient-to-r from-gray-900/90 via-gray-900/80 to-red-950/30 rounded-xl border border-red-900/40 overflow-hidden hover:border-red-600/60 transition-all duration-300 group">
                    <a href="{{ route('news.show', $news->slug) }}" class="flex flex-col sm:flex-row">
                        <!-- Thumbnail -->
                        <div class="sm:w-48 md:w-64 flex-shrink-0">
                            <div class="relative aspect-video sm:aspect-square sm:h-full w-full">
                                @if($news->thumbnail_url)
                                <img 
                                    src="{{ $news->thumbnail_url }}" 
                                    alt="{{ $news->title }}"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    loading="lazy"
                                >
                                @else
                                <div class="absolute inset-0 bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                @endif
                                
                                <!-- Live indicator overlay -->
                                <div class="absolute top-2 left-2 flex items-center gap-1.5 bg-black/70 backdrop-blur-sm rounded-full px-2 py-1">
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-600"></span>
                                    </span>
                                    <span class="text-[10px] text-red-400 font-bold uppercase">Ao Vivo</span>
                                </div>
                                
                                @if($news->is_members_only)
                                <div class="absolute top-2 right-2 bg-red-800/90 backdrop-blur-sm rounded px-1.5 py-0.5">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 p-4 sm:p-5 flex flex-col justify-center">
                            <!-- Meta -->
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                @if($news->category)
                                <span class="text-red-400/80 text-xs uppercase font-medium">
                                    {{ \App\Helpers\CategoryHelper::format($news->category) }}
                                </span>
                                <span class="text-gray-600">•</span>
                                @endif
                                <span class="text-gray-500 text-xs flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Atualizado {{ $news->updating_since ? $news->updating_since->diffForHumans() : $news->created_at->diffForHumans() }}
                                </span>
                            </div>
                            
                            <!-- Title -->
                            <h2 class="text-lg sm:text-xl font-bold text-white group-hover:text-red-400 transition-colors mb-2 line-clamp-2">
                                {{ $news->title }}
                            </h2>
                            
                            <!-- Description -->
                            @if($news->summary || $news->description)
                            <p class="text-gray-400 text-sm line-clamp-2 mb-3">
                                {{ Str::limit($news->summary ?? $news->description, 180) }}
                            </p>
                            @endif
                            
                            <!-- Footer stats -->
                            <div class="flex items-center gap-4 text-xs text-gray-500">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    {{ number_format($news->views_count) }}
                                </span>
                                @if($news->comments_count > 0)
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                    </svg>
                                    {{ $news->comments_count }}
                                </span>
                                @endif
                                @if($news->user)
                                <span class="text-gray-600">
                                    por <span class="text-gray-400">{{ $news->user->name }}</span>
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Arrow indicator -->
                        <div class="hidden sm:flex items-center justify-center px-4 text-gray-600 group-hover:text-red-500 transition-colors">
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>
                </article>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-8">
                {{ $updatingNews->links() }}
            </div>
            @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="w-20 h-20 mx-auto mb-6 bg-gray-800/50 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Nenhuma notícia em atualização</h3>
                <p class="text-gray-400 mb-6 max-w-md mx-auto">
                    No momento não há notícias sendo atualizadas em tempo real. Volte em breve para acompanhar novos acontecimentos.
                </p>
                <a href="{{ route('news.index') }}" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-medium px-6 py-3 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Voltar ao Feed
                </a>
            </div>
            @endif
        </div>
    </section>
</div>
@endsection
