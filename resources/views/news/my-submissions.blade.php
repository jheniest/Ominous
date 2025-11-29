<x-app-layout>
    <div class="min-h-screen bg-black text-gray-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-red-500">Minhas Publicações</h1>
                <a href="{{ route('news.create') }}" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-colors shadow-lg shadow-red-900/50">
                    Enviar Notícia
                </a>
            </div>

            @if($videos->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($videos as $video)
                <div class="bg-gray-900 rounded-lg overflow-hidden border border-red-900/30 shadow-lg">
                    <a href="{{ route('news.show', $video) }}">
                        @if($video->thumbnail_url)
                        <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="w-full h-48 object-cover hover:opacity-80 transition-opacity">
                        @else
                        <div class="w-full h-48 bg-gradient-to-br from-gray-800 to-black flex items-center justify-center">
                            <svg class="w-16 h-16 text-red-900" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                            </svg>
                        </div>
                        @endif
                    </a>
                    
                    <div class="p-4">
                        <a href="{{ route('news.show', $video) }}">
                            <h3 class="font-semibold text-white hover:text-red-400 transition-colors line-clamp-2">{{ $video->title }}</h3>
                        </a>
                        
                        <!-- Status Badge -->
                        <div class="mt-3">
                            @if($video->status === 'pending')
                            <span class="inline-flex items-center px-3 py-1 bg-yellow-900/30 text-yellow-400 text-xs font-semibold rounded border border-yellow-700">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                Aguardando Revisão
                            </span>
                            @elseif($video->status === 'approved')
                            <span class="inline-flex items-center px-3 py-1 bg-green-900/30 text-green-400 text-xs font-semibold rounded border border-green-700">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Aprovado
                            </span>
                            @elseif($video->status === 'rejected')
                            <span class="inline-flex items-center px-3 py-1 bg-red-900/30 text-red-400 text-xs font-semibold rounded border border-red-700">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                Rejeitado
                            </span>
                            @elseif($video->status === 'hidden')
                            <span class="inline-flex items-center px-3 py-1 bg-gray-700 text-gray-300 text-xs font-semibold rounded border border-gray-600">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/>
                                    <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/>
                                </svg>
                                Oculto
                            </span>
                            @endif
                        </div>

                        @if($video->rejection_reason)
                        <div class="mt-2 p-2 bg-red-900/20 border border-red-700 rounded text-xs text-red-300">
                            <strong>Motivo:</strong> {{ $video->rejection_reason }}
                        </div>
                        @endif

                        <div class="flex items-center justify-between text-sm text-gray-400 mt-3 pt-3 border-t border-red-900/30">
                            <div class="flex items-center gap-3">
                                <span>{{ $video->views_count }} views</span>
                                <span>{{ $video->comments_count }} comentários</span>
                            </div>
                        </div>

                        <div class="text-xs text-gray-500 mt-2">
                            {{ $video->created_at->format('d/m/Y') }}
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2 mt-4">
                            <a href="{{ route('news.edit', $video) }}" class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white text-sm font-semibold rounded-lg text-center transition-colors">
                                Editar
                            </a>
                            <form method="POST" action="{{ route('news.destroy', $video) }}" onsubmit="return confirm('Tem certeza que deseja deletar este conteúdo? Esta ação não pode ser desfeita.')" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors">
                                    Deletar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $videos->links() }}
            </div>
            @else
            <div class="text-center py-16 bg-gray-900 rounded-lg border border-red-900/30">
                <svg class="w-24 h-24 mx-auto text-red-900 mb-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                </svg>
                <p class="text-xl text-gray-400 mb-4">Você ainda não publicou nenhum conteúdo</p>
                <a href="{{ route('news.create') }}" class="inline-block px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-colors">
                    Enviar Sua Primeira Notícia
                </a>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
