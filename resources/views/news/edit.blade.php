<x-app-layout>
    <div class="min-h-screen bg-black text-gray-100 py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-900 rounded-lg border-2 border-red-900/50 shadow-2xl shadow-red-900/30 p-8">
                <h1 class="text-3xl font-bold text-red-500 mb-6">Editar Publicação</h1>

                <form method="POST" action="{{ route('news.update', $video) }}" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-semibold text-gray-300 mb-2">Título *</label>
                        <input type="text" id="title" name="title" value="{{ old('title', $video->title) }}" required maxlength="255" class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-red-500 @error('title') border-red-500 @enderror">
                        @error('title')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subtitle -->
                    <div>
                        <label for="subtitle" class="block text-sm font-semibold text-gray-300 mb-2">Subtítulo</label>
                        <input type="text" id="subtitle" name="subtitle" value="{{ old('subtitle', $video->subtitle) }}" maxlength="500" placeholder="Um breve resumo ou linha secundária" class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-red-500 @error('subtitle') border-red-500 @enderror">
                        @error('subtitle')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-300 mb-2">Descrição *</label>
                        <textarea id="description" name="description" rows="6" required maxlength="5000" class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-red-500 @error('description') border-red-500 @enderror">{{ old('description', $video->description) }}</textarea>
                        @error('description')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Media Info (read-only) -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Mídia</label>
                        <div class="bg-gray-800 border border-red-900/50 rounded-lg px-4 py-3 text-gray-400">
                            @if($video->media && $video->media->count() > 0)
                                {{ $video->media->count() }} arquivo(s) de mídia
                            @else
                                Nenhuma mídia
                            @endif
                        </div>
                        <p class="text-gray-500 text-xs mt-1">A mídia não pode ser alterada após o upload</p>
                    </div>

                    <!-- Thumbnail URL -->
                    <div>
                        <label for="thumbnail_url" class="block text-sm font-semibold text-gray-300 mb-2">URL da Thumbnail (Opcional)</label>
                        <input type="url" id="thumbnail_url" name="thumbnail_url" value="{{ old('thumbnail_url', $video->thumbnail_url) }}" maxlength="500" placeholder="https://example.com/thumbnail.jpg" class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-red-500 @error('thumbnail_url') border-red-500 @enderror">
                        @error('thumbnail_url')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-semibold text-gray-300 mb-2">Categoria *</label>
                        <select id="category" name="category" required class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-red-500 @error('category') border-red-500 @enderror">
                            <option value="guerra" {{ old('category', $video->category) == 'guerra' ? 'selected' : '' }}>Guerra</option>
                            <option value="terrorismo" {{ old('category', $video->category) == 'terrorismo' ? 'selected' : '' }}>Terrorismo</option>
                            <option value="chacina" {{ old('category', $video->category) == 'chacina' ? 'selected' : '' }}>Chacina</option>
                            <option value="massacre" {{ old('category', $video->category) == 'massacre' ? 'selected' : '' }}>Massacre</option>
                            <option value="suicidio" {{ old('category', $video->category) == 'suicidio' ? 'selected' : '' }}>Suicídio</option>
                            <option value="tribunal-do-crime" {{ old('category', $video->category) == 'tribunal-do-crime' ? 'selected' : '' }}>Tribunal do Crime</option>
                        </select>
                        @error('category')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NSFW Checkbox -->
                    <div class="flex items-start">
                        <input type="checkbox" id="is_nsfw" name="is_nsfw" value="1" {{ old('is_nsfw', $video->is_nsfw) ? 'checked' : '' }} class="mt-1 w-4 h-4 bg-black border-red-900/50 rounded text-red-600 focus:ring-red-500">
                        <label for="is_nsfw" class="ml-3">
                            <span class="block text-sm font-semibold text-gray-300">Marcar como NSFW</span>
                            <span class="block text-xs text-gray-500">Marque se o conteúdo é gráfico ou sensível</span>
                        </label>
                    </div>

                    <!-- Tags -->
                    <div>
                        <label for="tags" class="block text-sm font-semibold text-gray-300 mb-2">Tags</label>
                        <input type="text" id="tags" name="tags" value="{{ old('tags', $video->tags->pluck('name')->implode(', ')) }}" placeholder="Separe as tags por vírgula (ex: violência, crime, polícia)" class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 placeholder-gray-600 focus:outline-none focus:border-red-500 @error('tags') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-1">Use vírgulas para separar múltiplas tags</p>
                        @error('tags')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-4 pt-6 border-t border-red-900/30">
                        <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-bold text-lg transition-colors shadow-lg shadow-red-900/50">
                            Salvar Alterações
                        </button>
                        <a href="{{ route('news.show', $video) }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-semibold transition-colors">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
