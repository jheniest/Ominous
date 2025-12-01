<x-app-layout>
    <x-slot name="title">Editar Publicação</x-slot>

    <div class="min-h-screen bg-black text-gray-100 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('news.show', $video) }}" class="text-red-400 hover:text-red-300 text-sm mb-4 inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Voltar para publicação
                </a>
                <h1 class="text-2xl font-bold text-white mb-2">✏️ Editar Publicação</h1>
                <p class="text-gray-400">Atualize as informações da sua publicação.</p>
            </div>

            @if(session('success'))
            <div class="mb-6 p-4 bg-green-900/30 border border-green-800/50 rounded-lg">
                <p class="text-green-400">{{ session('success') }}</p>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 p-4 bg-red-900/30 border border-red-800/50 rounded-lg">
                <p class="text-red-400">{{ session('error') }}</p>
            </div>
            @endif

            <!-- Edit Form -->
            <div class="bg-gray-900 border border-red-900/30 rounded-lg p-6" x-data="{ isSubmitting: false }">
                <form method="POST" action="{{ route('news.update', $video) }}" @submit="isSubmitting = true">
                    @csrf
                    @method('PATCH')

                    <!-- Title -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium text-gray-300 mb-2">
                            Título *
                        </label>
                        <input 
                            type="text" 
                            id="title" 
                            name="title" 
                            value="{{ old('title', $video->title) }}" 
                            required 
                            maxlength="255"
                            class="w-full px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 @error('title') border-red-500 @enderror"
                        >
                        @error('title')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subtitle -->
                    <div class="mb-6">
                        <label for="subtitle" class="block text-sm font-medium text-gray-300 mb-2">
                            Subtítulo
                        </label>
                        <input 
                            type="text" 
                            id="subtitle" 
                            name="subtitle" 
                            value="{{ old('subtitle', $video->subtitle) }}" 
                            maxlength="500"
                            placeholder="Um breve resumo ou linha secundária"
                            class="w-full px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 @error('subtitle') border-red-500 @enderror"
                        >
                        @error('subtitle')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-300 mb-2">
                            Descrição *
                        </label>
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="5" 
                            required 
                            maxlength="5000"
                            class="w-full px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 resize-none @error('description') border-red-500 @enderror">{{ old('description', $video->description) }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Máximo de 5000 caracteres</p>
                        @error('description')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Media Info (read-only) -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Mídia
                        </label>
                        <div class="flex items-center gap-3 p-4 bg-neutral-800 border border-neutral-700 rounded-lg">
                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                            </svg>
                            <div>
                                <span class="text-gray-300">
                                    @if($video->media && $video->media->count() > 0)
                                        {{ $video->media->count() }} arquivo(s) de mídia
                                    @else
                                        Nenhuma mídia
                                    @endif
                                </span>
                                <p class="text-xs text-gray-500">A mídia não pode ser alterada após o upload</p>
                            </div>
                        </div>
                    </div>

                    <!-- Thumbnail URL -->
                    <div class="mb-6">
                        <label for="thumbnail_url" class="block text-sm font-medium text-gray-300 mb-2">
                            URL da Thumbnail (Opcional)
                        </label>
                        <input 
                            type="url" 
                            id="thumbnail_url" 
                            name="thumbnail_url" 
                            value="{{ old('thumbnail_url', $video->thumbnail_url) }}" 
                            maxlength="500" 
                            placeholder="https://example.com/thumbnail.jpg"
                            class="w-full px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 @error('thumbnail_url') border-red-500 @enderror"
                        >
                        @error('thumbnail_url')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div class="mb-6">
                        <label for="category" class="block text-sm font-medium text-gray-300 mb-2">
                            Categoria *
                        </label>
                        <select 
                            id="category" 
                            name="category" 
                            required 
                            class="w-full px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg text-white focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 @error('category') border-red-500 @enderror"
                        >
                            <optgroup label="⚠️ Violência Extrema">
                                <option value="guerra" {{ old('category', $video->category) == 'guerra' ? 'selected' : '' }}>⚔️ Guerra</option>
                                <option value="terrorismo" {{ old('category', $video->category) == 'terrorismo' ? 'selected' : '' }}>💣 Terrorismo</option>
                                <option value="chacina" {{ old('category', $video->category) == 'chacina' ? 'selected' : '' }}>🔪 Chacina</option>
                                <option value="massacre" {{ old('category', $video->category) == 'massacre' ? 'selected' : '' }}>💀 Massacre</option>
                                <option value="suicidio" {{ old('category', $video->category) == 'suicidio' ? 'selected' : '' }}>⚠️ Suicídio</option>
                                <option value="tribunal-do-crime" {{ old('category', $video->category) == 'tribunal-do-crime' ? 'selected' : '' }}>⚖️ Tribunal do Crime</option>
                            </optgroup>
                            <optgroup label="🔫 Crimes Violentos">
                                <option value="homicidio" {{ old('category', $video->category) == 'homicidio' ? 'selected' : '' }}>🩸 Homicídio</option>
                                <option value="assalto" {{ old('category', $video->category) == 'assalto' ? 'selected' : '' }}>🔫 Assalto</option>
                                <option value="sequestro" {{ old('category', $video->category) == 'sequestro' ? 'selected' : '' }}>🚐 Sequestro</option>
                                <option value="tiroteio" {{ old('category', $video->category) == 'tiroteio' ? 'selected' : '' }}>💥 Tiroteio</option>
                            </optgroup>
                            <optgroup label="🚨 Acidentes & Tragédias">
                                <option value="acidentes" {{ old('category', $video->category) == 'acidentes' ? 'selected' : '' }}>🚗 Acidentes</option>
                                <option value="desastres" {{ old('category', $video->category) == 'desastres' ? 'selected' : '' }}>🌊 Desastres</option>
                            </optgroup>
                            <optgroup label="🚔 Policial & Segurança">
                                <option value="operacao-policial" {{ old('category', $video->category) == 'operacao-policial' ? 'selected' : '' }}>🚔 Operação Policial</option>
                                <option value="faccoes" {{ old('category', $video->category) == 'faccoes' ? 'selected' : '' }}>💀 Facções</option>
                            </optgroup>
                            <optgroup label="🌍 Internacional">
                                <option value="conflitos" {{ old('category', $video->category) == 'conflitos' ? 'selected' : '' }}>🔥 Conflitos</option>
                                <option value="execucoes" {{ old('category', $video->category) == 'execucoes' ? 'selected' : '' }}>☠️ Execuções</option>
                            </optgroup>
                        </select>
                        @error('category')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NSFW Toggle -->
                    <div class="mb-6">
                        <label class="flex items-center gap-3 cursor-pointer p-4 bg-neutral-800 border border-neutral-700 rounded-lg hover:border-red-500 transition">
                            <input 
                                type="checkbox" 
                                id="is_nsfw" 
                                name="is_nsfw" 
                                value="1" 
                                {{ old('is_nsfw', $video->is_nsfw) ? 'checked' : '' }} 
                                class="w-5 h-5 text-red-600 bg-neutral-800 border-neutral-600 rounded focus:ring-red-500"
                            >
                            <div>
                                <span class="text-gray-300 font-medium">Conteúdo sensível (NSFW)</span>
                                <p class="text-xs text-gray-500">Marque se o conteúdo contém material gráfico explícito</p>
                            </div>
                        </label>
                    </div>

                    <!-- Tags -->
                    <div class="mb-6">
                        <label for="tags" class="block text-sm font-medium text-gray-300 mb-2">
                            Tags
                        </label>
                        <input 
                            type="text" 
                            id="tags" 
                            name="tags" 
                            value="{{ old('tags', $video->tags->pluck('name')->implode(', ')) }}" 
                            placeholder="Ex: violência, crime, polícia"
                            class="w-full px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 @error('tags') border-red-500 @enderror"
                        >
                        <p class="mt-1 text-xs text-gray-500">Separe as tags por vírgula</p>
                        @error('tags')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center justify-between gap-4 pt-4 border-t border-neutral-800">
                        <a href="{{ route('news.show', $video) }}" 
                           class="px-6 py-2.5 bg-neutral-700 hover:bg-neutral-600 text-white font-medium rounded-lg transition">
                            Cancelar
                        </a>
                        <button 
                            type="submit" 
                            :disabled="isSubmitting"
                            :class="{ 'opacity-50 cursor-not-allowed': isSubmitting }"
                            class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition flex items-center gap-2"
                        >
                            <template x-if="!isSubmitting">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </template>
                            <template x-if="isSubmitting">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="isSubmitting ? 'Salvando...' : 'Salvar Alterações'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
