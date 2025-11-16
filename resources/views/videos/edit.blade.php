<x-app-layout>
    <div class="min-h-screen bg-black text-gray-100 py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-900 rounded-lg border-2 border-red-900/50 shadow-2xl shadow-red-900/30 p-8">
                <h1 class="text-3xl font-bold text-red-500 mb-6">Editar Vídeo</h1>

                <form method="POST" action="{{ route('videos.update', $video) }}" class="space-y-6">
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

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-300 mb-2">Descrição *</label>
                        <textarea id="description" name="description" rows="6" required maxlength="5000" class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-red-500 @error('description') border-red-500 @enderror">{{ old('description', $video->description) }}</textarea>
                        @error('description')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Video URL (read-only) -->
                    <div>
                        <label for="video_url" class="block text-sm font-semibold text-gray-300 mb-2">URL do Vídeo</label>
                        <input type="text" id="video_url" value="{{ $video->video_url }}" readonly class="w-full bg-gray-800 border border-red-900/50 rounded-lg px-4 py-3 text-gray-400 cursor-not-allowed">
                        <p class="text-gray-500 text-xs mt-1">O vídeo não pode ser alterado após o upload</p>
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
                            <option value="breaking_news" {{ old('category', $video->category) == 'breaking_news' ? 'selected' : '' }}>Breaking News</option>
                            <option value="footage" {{ old('category', $video->category) == 'footage' ? 'selected' : '' }}>Footage</option>
                            <option value="investigation" {{ old('category', $video->category) == 'investigation' ? 'selected' : '' }}>Investigation</option>
                            <option value="accident" {{ old('category', $video->category) == 'accident' ? 'selected' : '' }}>Accident</option>
                            <option value="crime" {{ old('category', $video->category) == 'crime' ? 'selected' : '' }}>Crime</option>
                            <option value="natural_disaster" {{ old('category', $video->category) == 'natural_disaster' ? 'selected' : '' }}>Natural Disaster</option>
                            <option value="other" {{ old('category', $video->category) == 'other' ? 'selected' : '' }}>Other</option>
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
                            <span class="block text-xs text-gray-500">Marque se o vídeo contém conteúdo gráfico ou sensível</span>
                        </label>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-4 pt-6 border-t border-red-900/30">
                        <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-bold text-lg transition-colors shadow-lg shadow-red-900/50">
                            Salvar Alterações
                        </button>
                        <a href="{{ route('videos.show', $video) }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-semibold transition-colors">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
