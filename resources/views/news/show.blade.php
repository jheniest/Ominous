@extends('layouts.app')

@section('title', $video->title . ' - Atrocidades')

@push('styles')
<style>
    /* Media Slider Styles */
    .media-slider {
        position: relative;
        background: #000;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid rgba(185, 28, 28, 0.3);
        box-shadow: 0 0 20px rgba(185, 28, 28, 0.1);
    }
    
    .media-slider-container {
        position: relative;
        width: 100%;
        overflow: hidden;
        border-radius: 11px;
    }
    
    .media-slides {
        display: flex;
        transition: transform 0.4s ease-in-out;
    }
    
    .media-slide {
        min-width: 100%;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #000;
    }
    
    .media-slide video,
    .media-slide img {
        max-width: 100%;
        max-height: 70vh;
        object-fit: contain;
    }
    
    /* Slider Navigation */
    .slider-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 20;
        background: rgba(0, 0, 0, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: white;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .slider-nav:hover {
        background: rgba(185, 28, 28, 0.8);
        border-color: rgba(185, 28, 28, 0.5);
    }
    
    .slider-nav.prev { left: 12px; }
    .slider-nav.next { right: 12px; }
    
    /* Slider Dots */
    .slider-dots {
        position: absolute;
        bottom: 16px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 8px;
        z-index: 20;
    }
    
    .slider-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.4);
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .slider-dot.active {
        background: #dc2626;
        transform: scale(1.2);
    }
    
    .slider-dot:hover {
        background: rgba(255, 255, 255, 0.8);
    }
    
    /* Media Counter */
    .media-counter {
        position: absolute;
        top: 12px;
        right: 12px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 20;
    }
    
    /* Sensitive Content Warning Banner */
    .sensitive-warning {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.95), rgba(0, 0, 0, 0.8));
        padding: 12px 16px;
        z-index: 25;
        border-top: 1px solid rgba(185, 28, 28, 0.5);
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
    
    .sensitive-warning.hidden {
        opacity: 0;
        transform: translateY(100%);
        pointer-events: none;
    }
    
    .sensitive-warning-inner {
        display: flex;
        align-items: center;
        gap: 12px;
        max-width: 100%;
    }
    
    .sensitive-icon {
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        background: rgba(185, 28, 28, 0.3);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Blur Overlay for Sensitive Content */
    .sensitive-blur-overlay {
        position: absolute;
        inset: 0;
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        background: rgba(0, 0, 0, 0.5);
        z-index: 30;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: opacity 0.4s ease;
        border-radius: 11px;
    }
    
    .sensitive-blur-overlay.revealed {
        opacity: 0;
        pointer-events: none;
    }
    
    .sensitive-warning-icon {
        width: 48px;
        height: 48px;
        background: rgba(185, 28, 28, 0.4);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
    }
    
    @media (max-width: 640px) {
        .sensitive-warning-icon {
            width: 40px;
            height: 40px;
            margin-bottom: 8px;
        }
        .sensitive-warning-icon svg {
            width: 20px;
            height: 20px;
        }
    }
    
    .reveal-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: rgba(185, 28, 28, 0.9);
        color: white;
        font-weight: 600;
        padding: 12px 24px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        cursor: pointer;
        transition: all 0.2s;
        font-size: 14px;
    }
    
    @media (max-width: 640px) {
        .reveal-btn {
            padding: 10px 20px;
            font-size: 13px;
            gap: 6px;
        }
        .reveal-btn svg {
            width: 16px;
            height: 16px;
        }
    }
    
    .reveal-btn:hover {
        background: rgba(220, 38, 38, 1);
        transform: scale(1.05);
    }
    
    /* LEIA TAMBÉM Box */
    .read-also-box {
        background: linear-gradient(135deg, rgba(15, 15, 15, 0.95), rgba(25, 10, 10, 0.95));
        border: 1px solid rgba(185, 28, 28, 0.4);
        border-radius: 12px;
        padding: 20px;
        margin: 24px 0;
        box-shadow: 0 4px 20px rgba(185, 28, 28, 0.1);
    }
    
    .read-also-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(185, 28, 28, 0.3);
    }
    
    .read-also-header span {
        color: #dc2626;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .read-also-content {
        display: flex;
        gap: 16px;
        align-items: flex-start;
    }
    
    .read-also-thumb {
        flex-shrink: 0;
        width: 100px;
        height: 70px;
        border-radius: 8px;
        overflow: hidden;
        background: #1a1a1a;
    }
    
    .read-also-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .read-also-info {
        flex: 1;
        min-width: 0;
    }
    
    .read-also-title {
        color: #fff;
        font-weight: 600;
        font-size: 15px;
        line-height: 1.4;
        margin-bottom: 6px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .read-also-title:hover {
        color: #dc2626;
    }
    
    .read-also-meta {
        font-size: 12px;
        color: #6b7280;
    }
    
    @media (max-width: 640px) {
        .read-also-box {
            padding: 16px;
        }
        .read-also-thumb {
            width: 80px;
            height: 56px;
        }
        .read-also-title {
            font-size: 14px;
        }
    }
    
    /* Article Content Styling */
    .article-content p {
        margin-bottom: 1.25rem;
        line-height: 1.8;
    }
    
    .article-content p:last-child {
        margin-bottom: 0;
    }
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        cursor: pointer;
        transition: all 0.2s;
        font-size: 14px;
    }
    
    .reveal-btn:hover {
        background: rgba(220, 38, 38, 1);
        transform: scale(1.05);
    }
    
    /* Video Controls Override */
    video::-webkit-media-controls-download-button {
        display: none !important;
    }
    
    video::-webkit-media-controls-enclosure {
        overflow: hidden;
    }
</style>
@endpush

@section('content')
@php
use App\Helpers\CategoryHelper;
@endphp
<div class="min-h-screen bg-black">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Conteúdo Principal -->
            <article class="lg:col-span-2">
                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                    <a href="{{ route('news.index') }}" class="hover:text-white transition-colors">Notícias</a>
                    <span>/</span>
                    @if($video->category)
                    <a href="{{ route('news.category', $video->category) }}" class="hover:text-white transition-colors">
                        {{ CategoryHelper::format($video->category) }}
                    </a>
                    <span>/</span>
                    @endif
                    <span class="text-gray-400 truncate">{{ Str::limit($video->title, 40) }}</span>
                </nav>

                <!-- Cabeçalho -->
                <header class="mb-6">
                    <!-- Badges -->
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        @if($video->is_members_only)
                        <span class="bg-gradient-to-r from-red-700 to-red-900 text-white text-xs font-bold px-2 py-1 rounded flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            MEMBROS
                        </span>
                        @endif
                        @if($video->is_sensitive)
                        <span class="bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">18+</span>
                        @endif
                        @if($video->category)
                        <span class="bg-zinc-800 text-gray-300 text-xs px-2 py-1 rounded">{{ CategoryHelper::format($video->category) }}</span>
                        @endif
                    </div>

                    <h1 class="text-white text-2xl md:text-3xl font-bold leading-tight">
                        {{ $video->title }}
                    </h1>
                    
                    @if($video->subtitle)
                    <p class="text-gray-400 text-lg mt-2">{{ $video->subtitle }}</p>
                    @endif

                    <!-- Meta info -->
                    <div class="flex flex-wrap items-center gap-4 mt-4 text-sm text-gray-500">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $video->created_at->format('d/m/Y H:i') }}
                        </span>
                        
                        @if($video->editedBy)
                        <span class="flex items-center gap-1 text-blue-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editado por {{ $video->editedBy->name }}
                        </span>
                        @endif
                        
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            {{ number_format($video->views_count) }} visualizações
                        </span>
                        
                        @if($video->location)
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $video->location }}
                        </span>
                        @endif
                        
                        @if($video->source)
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                            {{ $video->source }}
                        </span>
                        @endif
                    </div>
                </header>

                <!-- Player de Mídia - Slider -->
                <div class="mb-8">
                    @if($canViewMedia && $video->media->count() > 0)
                        <!-- Media Slider -->
                        <div class="media-slider" x-data="mediaSlider({{ $video->media->count() }}, {{ ($video->is_sensitive || $video->is_nsfw) ? 'true' : 'false' }})">
                            <div class="media-slider-container aspect-video">
                                <div class="media-slides" :style="'transform: translateX(-' + (currentSlide * 100) + '%)'">
                                    @foreach($video->media as $index => $media)
                                    <div class="media-slide">
                                        @if($media->isVideo())
                                            <video 
                                                controls 
                                                controlsList="nodownload noremoteplayback" 
                                                disablePictureInPicture
                                                playsinline
                                                preload="metadata"
                                                @if($video->thumbnail_url) poster="{{ $video->thumbnail_url }}" @endif
                                                class="w-full h-full"
                                            >
                                                <source src="{{ $media->url }}" type="{{ $media->mime_type }}">
                                                Seu navegador não suporta o elemento de vídeo.
                                            </video>
                                        @else
                                            <img 
                                                src="{{ $media->url }}" 
                                                alt="{{ $video->title }}"
                                                class="w-full h-full object-contain"
                                                loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                            >
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                                
                                <!-- Navigation Arrows (show only if multiple media) -->
                                @if($video->media->count() > 1)
                                <button 
                                    @click="prevSlide()" 
                                    class="slider-nav prev"
                                    x-show="currentSlide > 0 && !sensitiveBlur"
                                >
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>
                                <button 
                                    @click="nextSlide()" 
                                    class="slider-nav next"
                                    x-show="currentSlide < totalSlides - 1 && !sensitiveBlur"
                                >
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                                
                                <!-- Media Counter -->
                                <div class="media-counter" x-show="!sensitiveBlur">
                                    <span x-text="currentSlide + 1"></span> / {{ $video->media->count() }}
                                </div>
                                
                                <!-- Dots Navigation -->
                                <div class="slider-dots" x-show="!sensitiveBlur">
                                    @foreach($video->media as $index => $media)
                                    <button 
                                        @click="goToSlide({{ $index }})" 
                                        class="slider-dot"
                                        :class="{ 'active': currentSlide === {{ $index }} }"
                                    ></button>
                                    @endforeach
                                </div>
                                @endif
                                
                                <!-- Sensitive Content Blur Overlay -->
                                @if($video->is_sensitive || $video->is_nsfw)
                                <div 
                                    class="sensitive-blur-overlay"
                                    :class="{ 'revealed': !sensitiveBlur }"
                                >
                                    <div class="text-center px-4 flex flex-col items-center">
                                        <div class="sensitive-warning-icon">
                                            <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-white font-bold text-base sm:text-lg mb-1">Conteúdo Sensível</h3>
                                        <p class="text-gray-400 text-xs sm:text-sm mb-4 max-w-xs">
                                            Este material pode conter imagens fortes.
                                        </p>
                                        <button 
                                            @click="revealContent()"
                                            class="reveal-btn"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            VER ASSIM MESMO
                                        </button>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    @elseif($video->thumbnail_url)
                        <!-- Fallback: Single Thumbnail -->
                        <div class="media-slider" x-data="{ sensitiveBlur: {{ ($video->is_sensitive || $video->is_nsfw) ? 'true' : 'false' }} }">
                            <div class="aspect-video bg-black flex items-center justify-center relative">
                                <img 
                                    src="{{ $video->thumbnail_url }}" 
                                    alt="{{ $video->title }}"
                                    class="w-full h-full object-contain"
                                >
                                
                                @if($video->is_sensitive || $video->is_nsfw)
                                <!-- Sensitive Content Blur Overlay -->
                                <div 
                                    class="sensitive-blur-overlay"
                                    :class="{ 'revealed': !sensitiveBlur }"
                                >
                                    <div class="text-center px-4 flex flex-col items-center">
                                        <div class="sensitive-warning-icon">
                                            <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-white font-bold text-base sm:text-lg mb-1">Conteúdo Sensível</h3>
                                        <p class="text-gray-400 text-xs sm:text-sm mb-4 max-w-xs">
                                            Este material pode conter imagens fortes.
                                        </p>
                                        <button 
                                            @click="sensitiveBlur = false"
                                            class="reveal-btn"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            VER ASSIM MESMO
                                        </button>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <!-- No Media Available -->
                        <div class="aspect-video bg-gradient-to-br from-zinc-900 to-black rounded-lg flex items-center justify-center">
                            <div class="text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <p>Mídia não disponível</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Descrição / Conteúdo -->
                <div class="prose prose-invert prose-red max-w-none mb-8">
                    @if($video->summary)
                    <p class="text-gray-300 text-lg leading-relaxed font-medium">
                        {{ $video->summary }}
                    </p>
                    @endif
                    
                    @if($video->description)
                    @php
                        $description = $video->description;
                        
                        // Verifica se tem tags <p> no conteúdo
                        $hasParagraphs = preg_match_all('/<p[^>]*>.*?<\/p>/s', $description, $matches);
                        
                        // Se tiver parágrafos e tiver "LEIA TAMBÉM" para mostrar
                        if ($hasParagraphs && count($matches[0]) >= 3 && isset($readAlso) && $readAlso) {
                            $paragraphs = $matches[0];
                            $totalParagraphs = count($paragraphs);
                            
                            // Posição aleatória entre o 2º parágrafo e o antepenúltimo
                            $minPos = 1; // Após o 1º parágrafo (índice 1 = após 2º)
                            $maxPos = max(1, $totalParagraphs - 2); // Antepenúltimo
                            $insertPosition = rand($minPos, $maxPos);
                            
                            // Renderiza o box "LEIA TAMBÉM"
                            $readAlsoHtml = '<div class="read-also-box not-prose">
                                <div class="read-also-header">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                    <span>Leia Também</span>
                                </div>
                                <a href="' . route('news.show', $readAlso->slug) . '" class="read-also-content group">
                                    <div class="read-also-thumb">'
                                        . ($readAlso->thumbnail_url 
                                            ? '<img src="' . $readAlso->thumbnail_url . '" alt="' . e($readAlso->title) . '">'
                                            : '<div class="w-full h-full bg-gradient-to-br from-red-900/50 to-zinc-800 flex items-center justify-center"><svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></div>')
                                    . '</div>
                                    <div class="read-also-info">
                                        <h4 class="read-also-title group-hover:text-red-500 transition-colors">' . e($readAlso->title) . '</h4>
                                        <span class="read-also-meta">' . number_format($readAlso->views_count) . ' visualizações</span>
                                    </div>
                                </a>
                            </div>';
                            
                            // Insere o box na posição calculada
                            $output = '';
                            foreach ($paragraphs as $index => $paragraph) {
                                $output .= $paragraph;
                                if ($index === $insertPosition) {
                                    $output .= $readAlsoHtml;
                                }
                            }
                            
                            // Remove os parágrafos originais e substitui pelo output processado
                            $description = preg_replace('/<p[^>]*>.*?<\/p>/s', '', $description);
                            $description = $output . $description;
                        }
                    @endphp
                    <div class="text-gray-400 leading-relaxed mt-4 article-content">
                        {!! $description !!}
                    </div>
                    @endif
                </div>

                <!-- Tags -->
                @if($video->tags->isNotEmpty())
                <div class="flex flex-wrap gap-2 mb-8">
                    @foreach($video->tags as $tag)
                    <a 
                        href="{{ route('news.tag', $tag->slug) }}"
                        class="bg-red-900/30 text-red-400 hover:bg-red-800/40 hover:text-red-300 px-3 py-1.5 rounded-full text-xs sm:text-sm uppercase font-medium transition-colors"
                    >
                        {{ strtoupper($tag->name) }}
                    </a>
                    @endforeach
                </div>
                @endif

                <!-- Autor -->
                @if($video->user)
                <div class="flex items-center gap-4 p-4 bg-zinc-900/50 rounded-lg border border-zinc-800 mb-8">
                    <a href="{{ route('profile.show', $video->user) }}" class="shrink-0">
                        @if($video->user->avatar)
                            <img 
                                src="{{ asset('storage/' . $video->user->avatar) }}" 
                                alt="{{ $video->user->name }}"
                                class="w-12 h-12 rounded-full object-cover"
                            >
                        @else
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-red-900 to-red-950 flex items-center justify-center text-white font-bold text-lg">
                                {{ substr($video->user->name, 0, 1) }}
                            </div>
                        @endif
                    </a>
                    <div>
                        <a href="{{ route('profile.show', $video->user) }}" class="text-white font-medium hover:text-red-500 transition-colors">
                            {{ $video->user->name }}
                        </a>
                        <p class="text-gray-500 text-sm">
                            @if($video->user->username)
                            @{{ $video->user->username }}
                            @endif
                        </p>
                    </div>
                    
                    @auth
                    @if(auth()->id() !== $video->user_id)
                    <button 
                        onclick="document.getElementById('report-modal').classList.remove('hidden')"
                        class="ml-auto text-gray-500 hover:text-red-500 text-sm flex items-center gap-1 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Reportar
                    </button>
                    @endif
                    @endauth
                </div>
                @else
                <div class="flex items-center gap-4 p-4 bg-zinc-900/50 rounded-lg border border-zinc-800 mb-8">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-gray-700 to-gray-800 flex items-center justify-center text-gray-500 font-bold text-lg shrink-0">
                        ?
                    </div>
                    <div>
                        <span class="text-gray-500 font-medium">Usuário Removido</span>
                    </div>
                </div>
                @endif

                <!-- Comentários -->
                @auth
                <section id="comments" class="bg-zinc-900/50 rounded-lg border border-zinc-800 p-6" x-data="{ 
                    replyTo: null, 
                    showAlert: false, 
                    alertMessage: '', 
                    alertType: 'success',
                    showDeleteModal: false,
                    deleteCommentId: null,
                    deleteForm: null
                }">
                    <!-- Popup Alert -->
                    <div 
                        x-show="showAlert" 
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform translate-y-2"
                        class="fixed bottom-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg"
                        :class="alertType === 'success' ? 'bg-green-900/90 text-green-200 border border-green-700' : 'bg-red-900/90 text-red-200 border border-red-700'"
                        @click="showAlert = false"
                    >
                        <div class="flex items-center gap-3">
                            <template x-if="alertType === 'success'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </template>
                            <template x-if="alertType === 'error'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </template>
                            <span x-text="alertMessage"></span>
                        </div>
                    </div>

                    <!-- Delete Confirmation Modal -->
                    <div 
                        x-show="showDeleteModal" 
                        x-transition
                        class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    >
                        <div class="absolute inset-0 bg-black/80" @click="showDeleteModal = false"></div>
                        <div class="relative bg-zinc-900 rounded-lg max-w-sm w-full p-6 border border-zinc-700">
                            <h3 class="text-white font-bold text-lg mb-4">Excluir Comentário</h3>
                            <p class="text-gray-400 mb-6">Tem certeza que deseja excluir este comentário? Esta ação não pode ser desfeita.</p>
                            <div class="flex justify-end gap-3">
                                <button 
                                    type="button" 
                                    @click="showDeleteModal = false" 
                                    class="text-gray-400 hover:text-white px-4 py-2"
                                >
                                    Cancelar
                                </button>
                                <button 
                                    type="button" 
                                    @click="if(deleteForm) deleteForm.submit()"
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded"
                                >
                                    Excluir
                                </button>
                            </div>
                        </div>
                    </div>

                    <h3 class="text-white font-bold text-lg mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        Comentários ({{ $video->comments->count() + $video->comments->sum(fn($c) => $c->replies->count()) }})
                    </h3>

                    <!-- Form de comentário principal -->
                    <form 
                        action="{{ route('news.comments.store', $video) }}" 
                        method="POST" 
                        class="mb-6"
                        x-show="replyTo === null"
                    >
                        @csrf
                        <textarea 
                            name="content" 
                            rows="3" 
                            placeholder="Deixe seu comentário..."
                            class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:border-red-500 focus:ring-1 focus:ring-red-500 resize-none"
                            required
                        ></textarea>
                        <div class="flex justify-end mt-2">
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded transition-colors">
                                Comentar
                            </button>
                        </div>
                    </form>

                    <!-- Lista de comentários -->
                    <div class="space-y-6">
                        @forelse($video->comments as $comment)
                        <div class="comment-thread">
                            <!-- Comentário principal -->
                            <div class="flex gap-3" id="comment-{{ $comment->id }}">
                                @if($comment->user)
                                    @if($comment->user->avatar)
                                        <img 
                                            src="{{ asset('storage/' . $comment->user->avatar) }}" 
                                            alt="{{ $comment->user->name }}"
                                            class="w-10 h-10 rounded-full object-cover shrink-0"
                                        >
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-900 to-red-950 flex items-center justify-center text-white font-bold shrink-0">
                                            {{ substr($comment->user->name, 0, 1) }}
                                        </div>
                                    @endif
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-700 to-gray-800 flex items-center justify-center text-gray-500 font-bold shrink-0">
                                        ?
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        @if($comment->user)
                                        <a href="{{ route('profile.show', $comment->user) }}" class="text-white font-medium text-sm hover:text-red-500 transition-colors">
                                            {{ $comment->user->name }}
                                        </a>
                                        @else
                                        <span class="text-gray-500 font-medium text-sm">Usuário Removido</span>
                                        @endif
                                        @if($comment->user && $comment->user->is_admin)
                                            <span class="bg-red-600 text-white text-[10px] px-1.5 py-0.5 rounded uppercase">Admin</span>
                                        @endif
                                        <span class="text-gray-600 text-xs">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-gray-300 text-sm mt-1">{{ $comment->content }}</p>
                                    
                                    <!-- Actions -->
                                    <div class="flex items-center gap-4 mt-2">
                                        <button 
                                            type="button"
                                            @click="replyTo = replyTo === {{ $comment->id }} ? null : {{ $comment->id }}"
                                            class="text-gray-500 hover:text-red-400 text-xs flex items-center gap-1 transition-colors"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                            </svg>
                                            Responder
                                        </button>
                                        
                                        @if(auth()->id() === $comment->user_id || auth()->user()->is_admin)
                                        <form 
                                            action="{{ route('news.comments.destroy', $comment) }}" 
                                            method="POST" 
                                            class="inline"
                                            x-ref="deleteForm{{ $comment->id }}"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="button"
                                                @click="deleteForm = $refs.deleteForm{{ $comment->id }}; showDeleteModal = true"
                                                class="text-gray-500 hover:text-red-500 text-xs flex items-center gap-1 transition-colors"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Excluir
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                    
                                    <!-- Reply Form -->
                                    <div x-show="replyTo === {{ $comment->id }}" x-transition class="mt-3">
                                        <form action="{{ route('news.comments.store', $video) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                            <textarea 
                                                name="content" 
                                                rows="2" 
                                                placeholder="Escreva sua resposta..."
                                                class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white placeholder-gray-500 focus:border-red-500 focus:ring-1 focus:ring-red-500 resize-none text-sm"
                                                required
                                            ></textarea>
                                            <div class="flex justify-end gap-2 mt-2">
                                                <button 
                                                    type="button" 
                                                    @click="replyTo = null"
                                                    class="text-gray-400 hover:text-white px-3 py-1.5 text-sm"
                                                >
                                                    Cancelar
                                                </button>
                                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium px-3 py-1.5 rounded text-sm transition-colors">
                                                    Responder
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Respostas (Replies) -->
                            @if($comment->replies->count() > 0)
                            <div class="ml-12 mt-3 space-y-3 border-l-2 border-zinc-700 pl-4">
                                @foreach($comment->replies as $reply)
                                <div class="flex gap-3" id="comment-{{ $reply->id }}">
                                    @if($reply->user)
                                        @if($reply->user->avatar)
                                            <img 
                                                src="{{ asset('storage/' . $reply->user->avatar) }}" 
                                                alt="{{ $reply->user->name }}"
                                                class="w-8 h-8 rounded-full object-cover shrink-0"
                                            >
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-red-900 to-red-950 flex items-center justify-center text-white font-bold text-sm shrink-0">
                                                {{ substr($reply->user->name, 0, 1) }}
                                            </div>
                                        @endif
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-gray-700 to-gray-800 flex items-center justify-center text-gray-500 font-bold text-sm shrink-0">
                                            ?
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            @if($reply->user)
                                            <a href="{{ route('profile.show', $reply->user) }}" class="text-white font-medium text-sm hover:text-red-500 transition-colors">
                                                {{ $reply->user->name }}
                                            </a>
                                            @else
                                            <span class="text-gray-500 font-medium text-sm">Usuário Removido</span>
                                            @endif
                                            @if($reply->user && $reply->user->is_admin)
                                                <span class="bg-red-600 text-white text-[10px] px-1.5 py-0.5 rounded uppercase">Admin</span>
                                            @endif
                                            <span class="text-gray-600 text-xs">{{ $reply->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-gray-300 text-sm mt-1">{{ $reply->content }}</p>
                                        
                                        <!-- Delete for replies -->
                                        @if(auth()->id() === $reply->user_id || auth()->user()->is_admin)
                                        <div class="mt-2">
                                            <form 
                                                action="{{ route('news.comments.destroy', $reply) }}" 
                                                method="POST" 
                                                class="inline"
                                                x-ref="deleteForm{{ $reply->id }}"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button 
                                                    type="button"
                                                    @click="deleteForm = $refs.deleteForm{{ $reply->id }}; showDeleteModal = true"
                                                    class="text-gray-500 hover:text-red-500 text-xs flex items-center gap-1 transition-colors"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Excluir
                                                </button>
                                            </form>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-4">Seja o primeiro a comentar.</p>
                        @endforelse
                    </div>
                </section>

                <!-- Flash message handler -->
                @if(session('success') || session('error'))
                <script>
                    document.addEventListener('alpine:init', () => {
                        Alpine.nextTick(() => {
                            const commentsSection = document.querySelector('#comments');
                            if (commentsSection && commentsSection.__x) {
                                commentsSection.__x.$data.alertMessage = '{{ session('success') ?? session('error') }}';
                                commentsSection.__x.$data.alertType = '{{ session('success') ? 'success' : 'error' }}';
                                commentsSection.__x.$data.showAlert = true;
                                setTimeout(() => {
                                    commentsSection.__x.$data.showAlert = false;
                                }, 4000);
                            }
                        });
                    });
                </script>
                @endif
                @else
                <div class="bg-zinc-900/50 rounded-lg border border-zinc-800 p-6 text-center">
                    <p class="text-gray-400 mb-4">Faça login para ver e deixar comentários.</p>
                    <a href="{{ route('login') }}" class="text-red-500 hover:text-red-400 font-medium">
                        Entrar
                    </a>
                </div>
                @endauth
            </article>

            <!-- Sidebar -->
            <aside class="space-y-8">
                <!-- Relacionadas -->
                @if($related->isNotEmpty())
                <div class="bg-zinc-900/50 rounded-lg border border-zinc-800 p-4">
                    <h3 class="text-white font-bold mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        Relacionadas
                    </h3>
                    
                    <div class="space-y-4">
                        @foreach($related as $item)
                        <a href="{{ route('news.show', $item->slug) }}" class="flex gap-3 group">
                            <div class="relative w-24 h-16 rounded overflow-hidden shrink-0">
                                <img 
                                    src="{{ $item->thumbnail_url ?? '/images/placeholder.jpg' }}" 
                                    alt="{{ $item->title }}"
                                    class="w-full h-full object-cover"
                                >
                                @if($item->is_sensitive)
                                <span class="absolute top-1 left-1 bg-red-600 text-white text-[10px] px-1 rounded">18+</span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-white text-sm font-medium line-clamp-2 group-hover:text-red-500 transition-colors">
                                    {{ $item->title }}
                                </h4>
                                <span class="text-gray-500 text-xs">{{ $item->created_at->diffForHumans() }}</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Mais do autor -->
                @if($moreFromAuthor->isNotEmpty())
                <div class="bg-zinc-900/50 rounded-lg border border-zinc-800 p-4">
                    <h3 class="text-white font-bold mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Mais de {{ $video->user->name }}
                    </h3>
                    
                    <div class="space-y-3">
                        @foreach($moreFromAuthor as $item)
                        <a href="{{ route('news.show', $item->slug) }}" class="block group">
                            <h4 class="text-gray-300 text-sm line-clamp-2 group-hover:text-red-500 transition-colors">
                                {{ $item->title }}
                            </h4>
                            <span class="text-gray-600 text-xs">{{ $item->created_at->diffForHumans() }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </aside>
        </div>
    </div>
</div>

<!-- Modal de Report -->
@auth
<div id="report-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/80" onclick="document.getElementById('report-modal').classList.add('hidden')"></div>
    <div class="relative bg-zinc-900 rounded-lg max-w-md w-full p-6 border border-zinc-700">
        <h3 class="text-white font-bold text-lg mb-4">Reportar Conteúdo</h3>
        
        <form action="{{ route('news.report', $video) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-400 text-sm mb-2">Motivo</label>
                <select name="reason" class="w-full bg-zinc-800 border border-zinc-700 rounded px-3 py-2 text-white" required>
                    <option value="">Selecione...</option>
                    <option value="spam">Spam</option>
                    <option value="violence">Violência excessiva</option>
                    <option value="illegal">Conteúdo ilegal</option>
                    <option value="misinformation">Informação falsa</option>
                    <option value="other">Outro</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-400 text-sm mb-2">Detalhes (opcional)</label>
                <textarea name="details" rows="3" class="w-full bg-zinc-800 border border-zinc-700 rounded px-3 py-2 text-white resize-none"></textarea>
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('report-modal').classList.add('hidden')" class="text-gray-400 hover:text-white px-4 py-2">
                    Cancelar
                </button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                    Enviar Report
                </button>
            </div>
        </form>
    </div>
</div>
@endauth
@endsection

@push('scripts')
<script>
// Media Slider Component
function mediaSlider(totalSlides, isSensitive = false) {
    return {
        currentSlide: 0,
        totalSlides: totalSlides,
        sensitiveBlur: isSensitive,
        
        revealContent() {
            this.sensitiveBlur = false;
        },
        
        nextSlide() {
            if (this.sensitiveBlur) return;
            if (this.currentSlide < this.totalSlides - 1) {
                this.pauseCurrentVideo();
                this.currentSlide++;
            }
        },
        
        prevSlide() {
            if (this.sensitiveBlur) return;
            if (this.currentSlide > 0) {
                this.pauseCurrentVideo();
                this.currentSlide--;
            }
        },
        
        goToSlide(index) {
            if (this.sensitiveBlur) return;
            if (index >= 0 && index < this.totalSlides) {
                this.pauseCurrentVideo();
                this.currentSlide = index;
            }
        },
        
        pauseCurrentVideo() {
            const slides = document.querySelectorAll('.media-slide');
            const currentSlideEl = slides[this.currentSlide];
            if (currentSlideEl) {
                const video = currentSlideEl.querySelector('video');
                if (video) {
                    video.pause();
                }
            }
        },
        
        init() {
            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (this.sensitiveBlur) return;
                if (e.key === 'ArrowLeft') {
                    this.prevSlide();
                } else if (e.key === 'ArrowRight') {
                    this.nextSlide();
                }
            });
            
            // Touch/swipe support
            let touchStartX = 0;
            let touchEndX = 0;
            
            const slider = this.$el;
            
            slider.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });
            
            slider.addEventListener('touchend', (e) => {
                if (this.sensitiveBlur) return;
                touchEndX = e.changedTouches[0].screenX;
                const diff = touchStartX - touchEndX;
                
                if (Math.abs(diff) > 50) {
                    if (diff > 0) {
                        this.nextSlide();
                    } else {
                        this.prevSlide();
                    }
                }
            }, { passive: true });
        }
    }
}
</script>
@endpush
