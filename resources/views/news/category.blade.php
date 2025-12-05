@extends('layouts.app')

@php
use App\Helpers\CategoryHelper;
use App\Helpers\SeoHelper;

$categoryDisplay = CategoryHelper::format($category);
$seoCategoryName = SeoHelper::getCategoryName($category);
$isSensitiveCategory = in_array($category, ['suicidio', 'chacina', 'massacre']);
@endphp

@section('title', $seoCategoryName . ' - Notícias - Atrocidades')
@section('meta_description', 'Notícias sobre ' . strtolower($seoCategoryName) . '. Acompanhe a cobertura completa e atualizada dos últimos acontecimentos.')
@section('meta_keywords', strtolower($seoCategoryName) . ', notícias, atualidades, cobertura, acontecimentos')
@section('meta_robots', $isSensitiveCategory ? 'index, follow, noimageindex' : 'index, follow')
@section('canonical', route('news.category', $category))

@section('og_title', $seoCategoryName . ' - Atrocidades')
@section('og_description', 'Notícias sobre ' . strtolower($seoCategoryName) . '. Cobertura completa e atualizada.')
@section('twitter_title', $seoCategoryName . ' - Atrocidades')
@section('twitter_description', 'Notícias sobre ' . strtolower($seoCategoryName) . '.')

@push('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "CollectionPage",
    "name": "{{ $seoCategoryName }}",
    "description": "Notícias sobre {{ strtolower($seoCategoryName) }}",
    "url": "{{ route('news.category', $category) }}",
    "isPartOf": {
        "@@type": "WebSite",
        "name": "Atrocidades",
        "url": "{{ config('app.url') }}"
    },
    "breadcrumb": {
        "@@type": "BreadcrumbList",
        "itemListElement": [
            {"@@type": "ListItem", "position": 1, "name": "Início", "item": "{{ url('/') }}"},
            {"@@type": "ListItem", "position": 2, "name": "Notícias", "item": "{{ route('news.index') }}"},
            {"@@type": "ListItem", "position": 3, "name": "{{ $seoCategoryName }}", "item": "{{ route('news.category', $category) }}"}
        ]
    }
}
</script>
@endpush

@section('content')
<div class="min-h-screen bg-black">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Cabeçalho -->
        <header class="mb-8">
            <nav class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                <a href="{{ route('news.index') }}" class="hover:text-white transition-colors">Notícias</a>
                <span>/</span>
                <span class="text-gray-400">Categoria</span>
            </nav>
            
            <h1 class="text-white text-2xl md:text-3xl font-bold flex items-center gap-3">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                {{ $categoryDisplay }}
            </h1>
            <p class="text-gray-500 mt-2">
                {{ $news->total() }} {{ $news->total() === 1 ? 'notícia' : 'notícias' }} nesta categoria
            </p>
        </header>

        <!-- Grid de notícias -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($news as $item)
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
                            <span class="bg-black/70 text-white text-xs px-2 py-0.5 rounded">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            @endguest
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
                <p class="text-gray-500">Nenhuma notícia nesta categoria.</p>
            </div>
            @endforelse
        </div>

        <!-- Paginação -->
        @if($news->hasPages())
        <div class="mt-10">
            {{ $news->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
