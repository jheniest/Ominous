@extends('layouts.app')

@section('title', $video->title . ' - Atrocidades')

@push('styles')
<style>
    /* Proteção contra seleção e DevTools */
    .protected-media {
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        pointer-events: none;
    }
    .protected-container {
        position: relative;
    }
    .protected-container::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 10;
    }
    .protected-container video {
        pointer-events: auto;
    }
    /* Desabilitar menu de contexto no container */
    .no-context {
        -webkit-touch-callout: none;
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
                    <div class="flex items-center gap-2 mb-3">
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

                <!-- Player de Mídia (Protegido) -->
                <div class="mb-8">
                    @if($canViewMedia)
                        <!-- Container de mídia protegido -->
                        <div 
                            id="media-container" 
                            class="protected-container no-context bg-black rounded-lg overflow-hidden"
                            oncontextmenu="return false;"
                        >
                            <div id="media-player" class="aspect-video bg-zinc-900 flex items-center justify-center">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-600 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <p class="text-gray-500 mt-2">Carregando mídia segura...</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Aviso de proteção -->
                        <p class="text-gray-600 text-xs mt-2 text-center">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Conteúdo protegido - Download não disponível
                        </p>
                    @else
                        <!-- PayWall -->
                        <div class="aspect-video bg-gradient-to-br from-zinc-900 to-black rounded-lg flex items-center justify-center relative overflow-hidden">
                            <!-- Thumbnail borrada de fundo -->
                            @if($video->thumbnail_url)
                            <img 
                                src="{{ $video->thumbnail_url }}" 
                                alt="" 
                                class="absolute inset-0 w-full h-full object-cover filter blur-xl opacity-30"
                            >
                            @endif
                            
                            <div class="relative z-10 text-center p-4 sm:p-6 md:p-8">
                                <div class="bg-red-600/20 rounded-full p-3 sm:p-4 inline-block mb-3 sm:mb-4">
                                    <svg class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                
                                <h3 class="text-white text-lg sm:text-xl font-bold mb-2">Conteúdo para Membros</h3>
                                <p class="text-gray-400 text-sm sm:text-base mb-4 sm:mb-6 max-w-md mx-auto">
                                    Este conteúdo sensível está disponível apenas para membros registrados da comunidade.
                                </p>
                                
                                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 justify-center">
                                    <a href="{{ route('login') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg transition-colors text-sm sm:text-base">
                                        Fazer Login
                                    </a>
                                    <a href="{{ route('invite.validate') }}" class="bg-zinc-800 hover:bg-zinc-700 text-white font-bold px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg transition-colors text-sm sm:text-base">
                                        Entrar com Convite
                                    </a>
                                </div>
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
                    <div class="text-gray-400 leading-relaxed mt-4 whitespace-pre-line">
                        {{ $video->description }}
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
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="{{ route('profile.show', $comment->user) }}" class="text-white font-medium text-sm hover:text-red-500 transition-colors">
                                            {{ $comment->user->name }}
                                        </a>
                                        @if($comment->user->is_admin)
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
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <a href="{{ route('profile.show', $reply->user) }}" class="text-white font-medium text-sm hover:text-red-500 transition-colors">
                                                {{ $reply->user->name }}
                                            </a>
                                            @if($reply->user->is_admin)
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
@if($canViewMedia && $mediaToken)
<script>
(function() {
    'use strict';
    
    // Proteção contra DevTools
    const devtools = {
        isOpen: false,
        orientation: undefined
    };
    
    const threshold = 160;
    
    const emitEvent = (isOpen, orientation) => {
        if (isOpen && devtools.isOpen !== isOpen) {
            console.clear();
            // Não bloquear completamente, mas dificultar
        }
    };
    
    setInterval(() => {
        const widthThreshold = window.outerWidth - window.innerWidth > threshold;
        const heightThreshold = window.outerHeight - window.innerHeight > threshold;
        const orientation = widthThreshold ? 'vertical' : 'horizontal';
        
        if (!(heightThreshold && widthThreshold) && ((window.Firebug && window.Firebug.chrome && window.Firebug.chrome.isInitialized) || widthThreshold || heightThreshold)) {
            devtools.isOpen = true;
            devtools.orientation = orientation;
        } else {
            devtools.isOpen = false;
            devtools.orientation = undefined;
        }
        
        emitEvent(devtools.isOpen, devtools.orientation);
    }, 500);
    
    // Desabilitar teclas de atalho comuns
    document.addEventListener('keydown', function(e) {
        // F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
        if (e.key === 'F12' || 
            (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C')) ||
            (e.ctrlKey && e.key === 'U')) {
            e.preventDefault();
            return false;
        }
    });
    
    // Desabilitar clique direito no player
    document.getElementById('media-container')?.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        return false;
    });
    
    // Carregar mídia de forma segura
    const token = @json($mediaToken);
    const videoId = @json($video->id);
    
    async function loadSecureMedia() {
        try {
            const response = await fetch(`/media/generate-url/{{ $video->id }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) {
                throw new Error('Falha ao carregar mídia');
            }
            
            const data = await response.json();
            const container = document.getElementById('media-player');
            
            if (data.media && data.media.length > 0) {
                const media = data.media[0];
                
                if (media.type === 'video' || media.url.includes('video')) {
                    container.innerHTML = `
                        <video 
                            controls 
                            controlsList="nodownload noremoteplayback" 
                            disablePictureInPicture
                            oncontextmenu="return false;"
                            class="w-full h-full protected-media"
                            playsinline
                        >
                            <source src="${media.url}" type="video/mp4">
                            Seu navegador não suporta o elemento de vídeo.
                        </video>
                    `;
                } else {
                    container.innerHTML = `
                        <img 
                            src="${media.url}" 
                            alt=""
                            class="w-full h-full object-contain protected-media"
                            oncontextmenu="return false;"
                            draggable="false"
                        >
                    `;
                }
            } else {
                // Fallback para thumbnail
                container.innerHTML = `
                    <div class="flex items-center justify-center h-full">
                        <p class="text-gray-500">Mídia não disponível</p>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Erro ao carregar mídia:', error);
            document.getElementById('media-player').innerHTML = `
                <div class="flex items-center justify-center h-full">
                    <p class="text-red-500">Erro ao carregar mídia. Tente novamente.</p>
                </div>
            `;
        }
    }
    
    // Carregar mídia quando a página carregar
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadSecureMedia);
    } else {
        loadSecureMedia();
    }
})();
</script>
@endif
@endpush
