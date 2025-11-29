@extends('layouts.app')

@section('title', 'Pesquisa: ' . $query . ' - Atrocidades')

@section('content')
<div class="min-h-screen bg-black">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Cabeçalho -->
        <header class="mb-8">
            <nav class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                <a href="{{ route('news.index') }}" class="hover:text-white transition-colors">Notícias</a>
                <span>/</span>
                <span class="text-gray-400">Pesquisa</span>
            </nav>
            
            <h1 class="text-white text-2xl md:text-3xl font-bold">
                Resultados para "<span class="text-red-500">{{ $query }}</span>"
            </h1>
            <p class="text-gray-500 mt-2">
                {{ $results->total() }} {{ $results->total() === 1 ? 'resultado encontrado' : 'resultados encontrados' }}
            </p>
        </header>

        <!-- Barra de pesquisa -->
        <form action="{{ route('news.search') }}" method="GET" class="mb-8">
            <div class="relative max-w-xl">
                <input 
                    type="text" 
                    name="q" 
                    value="{{ $query }}"
                    placeholder="Pesquisar notícias..."
                    class="w-full bg-zinc-900 border border-zinc-700 rounded-lg pl-12 pr-4 py-3 text-white placeholder-gray-500 focus:border-red-500 focus:ring-1 focus:ring-red-500"
                >
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </form>

        <!-- Resultados -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($results as $item)
            <article class="group bg-zinc-900/50 rounded-lg overflow-hidden border border-zinc-800 hover:border-red-900/50 transition-colors">
                <a href="{{ route('news.show', $item->slug) }}" class="block">
                    <div class="relative aspect-video overflow-hidden">
                        <img 
                            src="{{ $item->thumbnail_url ?? '/images/placeholder.jpg' }}" 
                            alt="{{ $item->title }}"
                            class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500"
                        >
                        
                        @if($item->is_sensitive)
                        <div class="absolute top-2 left-2 flex items-center gap-1">
                            <span class="bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded">18+</span>
                            @guest
                            <span class="bg-black/70 text-white text-xs px-2 py-0.5 rounded flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            @endguest
                        </div>
                        @endif
                        
                        @if($item->category)
                        <div class="absolute top-2 right-2">
                            <span class="bg-black/70 text-white text-xs px-2 py-0.5 rounded">
                                {{ \App\Helpers\CategoryHelper::format($item->category) }}
                            </span>
                        </div>
                        @endif
                    </div>
                    
                    <div class="p-4">
                        <h3 class="text-white font-bold line-clamp-2 group-hover:text-red-500 transition-colors">
                            {{ $item->title }}
                        </h3>
                        
                        <p class="text-gray-400 text-sm mt-2 line-clamp-2">
                            {{ $item->getExcerpt(120) }}
                        </p>
                        
                        <!-- Tags relevantes -->
                        @if($item->tags->isNotEmpty())
                        <div class="flex flex-wrap gap-1 mt-3">
                            @foreach($item->tags->take(3) as $tag)
                            <span class="text-xs text-gray-500">#{{ $tag->name }}</span>
                            @endforeach
                        </div>
                        @endif
                        
                        <div class="flex items-center justify-between mt-4 text-gray-500 text-xs">
                            <span>{{ $item->created_at->diffForHumans() }}</span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                {{ number_format($item->views_count) }}
                            </span>
                        </div>
                    </div>
                </a>
            </article>
            @empty
            <div class="col-span-full text-center py-16">
                <svg class="w-20 h-20 mx-auto text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <h3 class="text-white text-xl font-bold mb-2">Nenhum resultado encontrado</h3>
                <p class="text-gray-500 mb-6">Tente usar palavras-chave diferentes ou mais gerais.</p>
                <a href="{{ route('news.index') }}" class="inline-block bg-red-600 hover:bg-red-700 text-white font-medium px-6 py-2 rounded transition-colors">
                    Ver todas as notícias
                </a>
            </div>
            @endforelse
        </div>

        <!-- Paginação -->
        @if($results->hasPages())
        <div class="mt-10">
            {{ $results->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
