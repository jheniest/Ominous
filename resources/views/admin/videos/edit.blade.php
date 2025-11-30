<x-app-layout>
    <div class="min-h-screen bg-black text-gray-100 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('admin.videos.index') }}" class="text-red-400 hover:text-red-300 text-sm mb-4 inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Voltar para Moderação
                </a>
                <h1 class="text-3xl font-bold text-red-500 mb-2">Editar Notícia</h1>
                <p class="text-gray-400">Editando: <span class="text-white">{{ $video->title }}</span></p>
                <p class="text-gray-500 text-sm mt-1">Autor original: {{ $video->user->name }} • Publicado {{ $video->created_at->diffForHumans() }}</p>
            </div>

            @if($video->editedBy)
            <div class="bg-blue-900/20 border border-blue-700 rounded-lg p-4 mb-6">
                <p class="text-blue-300 text-sm">
                    <strong>⚠️ Última edição:</strong> por {{ $video->editedBy->name }} em {{ $video->edited_at->format('d/m/Y H:i') }}
                </p>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.videos.update', $video->id) }}" class="space-y-6">
                @csrf
                @method('PATCH')

                <!-- Título -->
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-300 mb-2">Título *</label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title', $video->title) }}"
                           required
                           class="w-full bg-gray-900 border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                    @error('title')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Subtítulo -->
                <div>
                    <label for="subtitle" class="block text-sm font-semibold text-gray-300 mb-2">Subtítulo</label>
                    <input type="text" 
                           name="subtitle" 
                           id="subtitle" 
                           value="{{ old('subtitle', $video->subtitle) }}"
                           class="w-full bg-gray-900 border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                    @error('subtitle')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Categoria -->
                <div>
                    <label for="category" class="block text-sm font-semibold text-gray-300 mb-2">Categoria *</label>
                    <select name="category" 
                            id="category" 
                            required
                            class="w-full bg-gray-900 border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        <option value="guerra" {{ old('category', $video->category) === 'guerra' ? 'selected' : '' }}>Guerra</option>
                        <option value="terrorismo" {{ old('category', $video->category) === 'terrorismo' ? 'selected' : '' }}>Terrorismo</option>
                        <option value="chacina" {{ old('category', $video->category) === 'chacina' ? 'selected' : '' }}>Chacina</option>
                        <option value="massacre" {{ old('category', $video->category) === 'massacre' ? 'selected' : '' }}>Massacre</option>
                        <option value="suicidio" {{ old('category', $video->category) === 'suicidio' ? 'selected' : '' }}>Suicídio</option>
                        <option value="tribunal-do-crime" {{ old('category', $video->category) === 'tribunal-do-crime' ? 'selected' : '' }}>Tribunal do Crime</option>
                    </select>
                    @error('category')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descrição -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-300 mb-2">Descrição *</label>
                    <textarea name="description" 
                              id="description" 
                              rows="6" 
                              required
                              class="w-full bg-gray-900 border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">{{ old('description', $video->description) }}</textarea>
                    @error('description')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Resumo -->
                <div>
                    <label for="summary" class="block text-sm font-semibold text-gray-300 mb-2">Resumo</label>
                    <textarea name="summary" 
                              id="summary" 
                              rows="3" 
                              maxlength="500"
                              placeholder="Breve resumo para exibição em cards (máx 500 caracteres)"
                              class="w-full bg-gray-900 border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">{{ old('summary', $video->summary) }}</textarea>
                    @error('summary')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Fonte -->
                    <div>
                        <label for="source" class="block text-sm font-semibold text-gray-300 mb-2">Fonte</label>
                        <input type="text" 
                               name="source" 
                               id="source" 
                               value="{{ old('source', $video->source) }}"
                               placeholder="Ex: Reuters, CNN, etc."
                               class="w-full bg-gray-900 border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        @error('source')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Localização -->
                    <div>
                        <label for="location" class="block text-sm font-semibold text-gray-300 mb-2">Localização</label>
                        <input type="text" 
                               name="location" 
                               id="location" 
                               value="{{ old('location', $video->location) }}"
                               placeholder="Ex: São Paulo, Brasil"
                               class="w-full bg-gray-900 border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        @error('location')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Flags -->
                <div class="bg-gray-900 border border-red-900/30 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-gray-300 mb-4">Classificação do Conteúdo</h3>
                    <div class="flex flex-wrap gap-6">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" 
                                   name="is_members_only" 
                                   value="1"
                                   {{ old('is_members_only', $video->is_members_only) ? 'checked' : '' }}
                                   class="w-5 h-5 bg-gray-900 border-red-900 rounded text-red-600 focus:ring-red-500">
                            <span class="text-gray-300">
                                <strong class="text-yellow-400">Apenas Membros</strong>
                                <span class="block text-xs text-gray-500">Requer login para visualizar a notícia</span>
                            </span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" 
                                   name="is_sensitive" 
                                   value="1"
                                   {{ old('is_sensitive', $video->is_sensitive) ? 'checked' : '' }}
                                   class="w-5 h-5 bg-gray-900 border-red-900 rounded text-red-600 focus:ring-red-500">
                            <span class="text-gray-300">
                                <strong class="text-red-400">Conteúdo Sensível (+18)</strong>
                                <span class="block text-xs text-gray-500">Requer login para visualizar mídia</span>
                            </span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" 
                                   name="is_nsfw" 
                                   value="1"
                                   {{ old('is_nsfw', $video->is_nsfw) ? 'checked' : '' }}
                                   class="w-5 h-5 bg-gray-900 border-red-900 rounded text-red-600 focus:ring-red-500">
                            <span class="text-gray-300">
                                <strong class="text-red-400">NSFW</strong>
                                <span class="block text-xs text-gray-500">Conteúdo adulto/explícito</span>
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Aviso -->
                <div class="bg-yellow-900/20 border border-yellow-700 rounded-lg p-4">
                    <p class="text-yellow-300 text-sm">
                        <strong>⚠️ Atenção:</strong> Ao salvar, seu nome será registrado como editor desta notícia e será exibido publicamente.
                    </p>
                </div>

                <!-- Botões -->
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-bold transition-colors">
                        Salvar Alterações
                    </button>
                    <a href="{{ route('admin.videos.index') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-semibold transition-colors text-center">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
