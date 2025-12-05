<x-app-layout>
    <x-slot name="title">Enviar Conteúdo</x-slot>

    <div class="min-h-screen bg-black text-gray-100 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('news.index') }}" class="text-red-400 hover:text-red-300 text-sm mb-4 inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Voltar
                </a>
                <h1 class="text-2xl font-bold text-white mb-2">📤 Enviar Conteúdo</h1>
                <p class="text-gray-400">Compartilhe vídeos e imagens com a comunidade.</p>
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

            @auth
            @if(!Auth::user()->is_admin)
            <div class="mb-6 flex items-start gap-3 p-4 bg-yellow-900/20 border border-yellow-800/50 rounded-lg">
                <svg class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-yellow-400 font-medium">Atenção</p>
                    <p class="text-yellow-300/70 text-sm">Todo conteúdo está sujeito a moderação. Seu envio será revisado antes de aparecer publicamente.</p>
                </div>
            </div>
            @endif
            @endauth

            <!-- Upload Form -->
            <div class="bg-gray-900 border border-red-900/30 rounded-lg p-6" x-data="uploadForm()">
                <form method="POST" action="{{ route('news.store') }}" enctype="multipart/form-data" @submit="handleSubmit">
                    @csrf

                    <!-- Title -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium text-gray-300 mb-2">
                            Título *
                        </label>
                        <input 
                            type="text" 
                            id="title" 
                            name="title" 
                            value="{{ old('title') }}" 
                            required 
                            maxlength="255"
                            placeholder="Digite o título do conteúdo"
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
                            value="{{ old('subtitle') }}" 
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
                            placeholder="Descreva o conteúdo em detalhes..."
                            class="w-full px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 resize-none @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Máximo de 5000 caracteres</p>
                        @error('description')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Media Upload -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Arquivos (Vídeos e/ou Imagens) *
                        </label>
                        <div 
                            class="border-2 border-dashed border-neutral-700 rounded-lg p-8 text-center hover:border-red-500 transition cursor-pointer"
                            :class="{ 'border-red-500 bg-red-950/20': isDragging }"
                            @click="$refs.fileInput.click()"
                            @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="handleDrop($event)"
                        >
                            <svg class="w-12 h-12 mx-auto text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="text-gray-400 mb-2">
                                <span class="text-red-500 font-medium">Clique para selecionar</span> ou arraste arquivos aqui
                            </p>
                            <p class="text-gray-500 text-sm">Vídeos: MP4, MOV, AVI, WMV | Imagens: JPG, PNG, GIF, WEBP</p>
                            <p class="text-gray-500 text-sm">Máximo de 10 arquivos (100MB cada)</p>
                        </div>
                        
                        <input 
                            type="file" 
                            x-ref="fileInput"
                            name="media_files[]" 
                            multiple 
                            accept="video/*,image/*" 
                            class="hidden" 
                            @change="handleFileSelect($event)"
                        >
                        
                        @error('media_files')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        @error('media_files.*')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror

                        <!-- Preview Grid -->
                        <div x-show="selectedFiles.length > 0" class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-4">
                            <template x-for="(file, index) in selectedFiles" :key="index">
                                <div class="relative bg-neutral-800 border border-neutral-700 rounded-lg p-3 hover:border-red-500 transition">
                                    <template x-if="file.type.startsWith('video/')">
                                        <div class="aspect-video bg-black rounded flex items-center justify-center mb-2">
                                            <svg class="w-10 h-10 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                            </svg>
                                        </div>
                                    </template>
                                    <template x-if="file.type.startsWith('image/')">
                                        <img :src="URL.createObjectURL(file)" class="aspect-video object-cover rounded mb-2">
                                    </template>
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-gray-300 truncate" x-text="file.name"></p>
                                            <p class="text-xs text-gray-500" x-text="formatSize(file.size)"></p>
                                        </div>
                                        <button type="button" @click="removeFile(index)" class="ml-2 text-red-400 hover:text-red-300">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
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
                            <option value="">Selecione uma categoria...</option>
                            <optgroup label="⚠️ Violência Extrema">
                                <option value="guerra" {{ old('category') == 'guerra' ? 'selected' : '' }}>⚔️ Guerra</option>
                                <option value="terrorismo" {{ old('category') == 'terrorismo' ? 'selected' : '' }}>💣 Terrorismo</option>
                                <option value="chacina" {{ old('category') == 'chacina' ? 'selected' : '' }}>🔪 Chacina</option>
                                <option value="massacre" {{ old('category') == 'massacre' ? 'selected' : '' }}>💀 Massacre</option>
                                <option value="suicidio" {{ old('category') == 'suicidio' ? 'selected' : '' }}>⚠️ Suicídio</option>
                                <option value="tribunal-do-crime" {{ old('category') == 'tribunal-do-crime' ? 'selected' : '' }}>⚖️ Tribunal do Crime</option>
                            </optgroup>
                            <optgroup label="🔫 Crimes Violentos">
                                <option value="homicidio" {{ old('category') == 'homicidio' ? 'selected' : '' }}>🩸 Homicídio</option>
                                <option value="assalto" {{ old('category') == 'assalto' ? 'selected' : '' }}>🔫 Assalto</option>
                                <option value="sequestro" {{ old('category') == 'sequestro' ? 'selected' : '' }}>🚐 Sequestro</option>
                                <option value="tiroteio" {{ old('category') == 'tiroteio' ? 'selected' : '' }}>💥 Tiroteio</option>
                            </optgroup>
                            <optgroup label="🚨 Acidentes & Tragédias">
                                <option value="acidentes" {{ old('category') == 'acidentes' ? 'selected' : '' }}>🚗 Acidentes</option>
                                <option value="desastres" {{ old('category') == 'desastres' ? 'selected' : '' }}>🌊 Desastres</option>
                            </optgroup>
                            <optgroup label="🚔 Policial & Segurança">
                                <option value="operacao-policial" {{ old('category') == 'operacao-policial' ? 'selected' : '' }}>🚔 Operação Policial</option>
                                <option value="faccoes" {{ old('category') == 'faccoes' ? 'selected' : '' }}>💀 Facções</option>
                            </optgroup>
                            <optgroup label="🌍 Internacional">
                                <option value="conflitos" {{ old('category') == 'conflitos' ? 'selected' : '' }}>🔥 Conflitos</option>
                                <option value="execucoes" {{ old('category') == 'execucoes' ? 'selected' : '' }}>☠️ Execuções</option>
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
                                {{ old('is_nsfw') ? 'checked' : '' }} 
                                class="w-5 h-5 text-red-600 bg-neutral-800 border-neutral-600 rounded focus:ring-red-500"
                            >
                            <div>
                                <span class="text-gray-300 font-medium">Conteúdo sensível (NSFW)</span>
                                <p class="text-xs text-gray-500">Marque se o conteúdo contém material gráfico explícito</p>
                            </div>
                        </label>
                    </div>

                    @auth
                    @if(Auth::user()->is_admin)
                    <!-- Members Only Toggle (Admin Only) -->
                    <div class="mb-6">
                        <label class="flex items-center gap-3 cursor-pointer p-4 bg-yellow-900/20 border border-yellow-800/50 rounded-lg hover:border-yellow-600 transition">
                            <input 
                                type="checkbox" 
                                id="is_members_only" 
                                name="is_members_only" 
                                value="1" 
                                {{ old('is_members_only') ? 'checked' : '' }} 
                                class="w-5 h-5 text-yellow-600 bg-neutral-800 border-yellow-700 rounded focus:ring-yellow-500"
                            >
                            <div>
                                <span class="text-yellow-400 font-medium">⭐ Apenas Membros</span>
                                <p class="text-xs text-gray-500">Somente usuários registrados podem ver este conteúdo</p>
                            </div>
                        </label>
                    </div>

                    <!-- Em Atualização / AO VIVO (Admin Only) -->
                    <div class="mb-6">
                        <label class="flex items-center gap-3 cursor-pointer p-4 bg-red-900/20 border border-red-800/50 rounded-lg hover:border-red-600 transition">
                            <input 
                                type="checkbox" 
                                id="is_updating" 
                                name="is_updating" 
                                value="1" 
                                {{ old('is_updating') ? 'checked' : '' }} 
                                class="w-5 h-5 text-red-600 bg-neutral-800 border-red-700 rounded focus:ring-red-500"
                            >
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="relative flex h-2.5 w-2.5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                                    </span>
                                    <span class="text-red-400 font-medium">Em Atualização (AO VIVO)</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Marque para notícias em andamento que serão atualizadas</p>
                            </div>
                        </label>
                    </div>
                    @endif
                    @endauth

                    <!-- Tags -->
                    <div class="mb-6">
                        <label for="tags" class="block text-sm font-medium text-gray-300 mb-2">
                            Tags
                        </label>
                        <input 
                            type="text" 
                            id="tags" 
                            name="tags" 
                            value="{{ old('tags') }}" 
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
                        <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('news.index') }}" 
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </template>
                            <template x-if="isSubmitting">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="isSubmitting ? 'Processando...' : '{{ Auth::check() && Auth::user()->is_admin ? 'Enviar' : 'Enviar para Moderação' }}'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function uploadForm() {
        return {
            selectedFiles: [],
            isDragging: false,
            isSubmitting: false,

            handleFileSelect(event) {
                const files = Array.from(event.target.files);
                this.selectedFiles = files.slice(0, 10);
            },

            handleDrop(event) {
                this.isDragging = false;
                const files = Array.from(event.dataTransfer.files);
                this.selectedFiles = files.slice(0, 10);
                
                // Update file input
                const dt = new DataTransfer();
                this.selectedFiles.forEach(file => dt.items.add(file));
                this.$refs.fileInput.files = dt.files;
            },

            removeFile(index) {
                this.selectedFiles.splice(index, 1);
                
                // Update file input
                const dt = new DataTransfer();
                this.selectedFiles.forEach(file => dt.items.add(file));
                this.$refs.fileInput.files = dt.files;
            },

            formatSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
            },

            handleSubmit(event) {
                if (this.isSubmitting) {
                    event.preventDefault();
                    return false;
                }
                this.isSubmitting = true;
            }
        };
    }
    </script>
    @endpush
</x-app-layout>
