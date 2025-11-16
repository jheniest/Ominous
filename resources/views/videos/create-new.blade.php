<x-app-layout>
    <div class="min-h-screen bg-black text-gray-100 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-900 rounded-lg border-2 border-red-900/50 shadow-2xl shadow-red-900/30 p-8">
                <h1 class="text-3xl font-bold text-red-500 mb-6">Enviar Conteúdo</h1>
                
                <div class="bg-red-900/20 border border-red-700 rounded-lg p-4 mb-6">
                    <p class="text-red-300 text-sm">
                        <strong>Atenção:</strong> Todo conteúdo está sujeito a moderação. Seu envio será revisado antes de aparecer publicamente.
                    </p>
                </div>

                <form method="POST" action="{{ route('videos.store') }}" enctype="multipart/form-data" class="space-y-6" id="uploadForm">
                    @csrf

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-semibold text-gray-300 mb-2">Título *</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required maxlength="255" class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-red-500 @error('title') border-red-500 @enderror">
                        @error('title')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-300 mb-2">Descrição *</label>
                        <textarea id="description" name="description" rows="6" required maxlength="5000" class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-red-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Multiple Media Upload -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Arquivos (Vídeos e/ou Imagens) *</label>
                        <div class="border-2 border-dashed border-red-900/50 rounded-lg p-8 text-center bg-black hover:border-red-700 transition cursor-pointer" onclick="document.getElementById('mediaFiles').click()">
                            <svg class="w-16 h-16 mx-auto text-red-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="text-gray-400 mb-2">
                                <span class="text-red-500 font-semibold">Clique para selecionar</span> ou arraste arquivos aqui
                            </p>
                            <p class="text-gray-500 text-sm">Vídeos: MP4, MOV, AVI, WMV | Imagens: JPG, PNG, GIF, WEBP</p>
                            <p class="text-gray-500 text-sm">Máximo de 10 arquivos (100MB cada)</p>
                        </div>
                        
                        <input type="file" id="mediaFiles" name="media_files[]" multiple accept="video/*,image/*" class="hidden" onchange="handleFileSelect(event)">
                        
                        @error('media_files')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        @error('media_files.*')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror

                        <!-- Preview Grid -->
                        <div id="filePreviewGrid" class="grid grid-cols-3 gap-4 mt-4 hidden"></div>
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-semibold text-gray-300 mb-2">Categoria *</label>
                        <select id="category" name="category" required class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-red-500 @error('category') border-red-500 @enderror">
                            <option value="">Selecione uma categoria</option>
                            <option value="breaking_news" {{ old('category') == 'breaking_news' ? 'selected' : '' }}>Breaking News</option>
                            <option value="footage" {{ old('category') == 'footage' ? 'selected' : '' }}>Footage</option>
                            <option value="investigation" {{ old('category') == 'investigation' ? 'selected' : '' }}>Investigation</option>
                            <option value="accident" {{ old('category') == 'accident' ? 'selected' : '' }}>Accident</option>
                            <option value="crime" {{ old('category') == 'crime' ? 'selected' : '' }}>Crime</option>
                            <option value="natural_disaster" {{ old('category') == 'natural_disaster' ? 'selected' : '' }}>Natural Disaster</option>
                            <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('category')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NSFW Toggle -->
                    <div class="flex items-center gap-3 bg-black border border-red-900/50 rounded-lg p-4">
                        <input type="checkbox" id="is_nsfw" name="is_nsfw" value="1" {{ old('is_nsfw') ? 'checked' : '' }} class="w-5 h-5 bg-gray-900 border-red-900 rounded text-red-600 focus:ring-red-500">
                        <label for="is_nsfw" class="text-sm text-gray-300">
                            Este conteúdo contém material sensível (NSFW)
                        </label>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-4 pt-4">
                        <button type="submit" class="flex-1 px-6 py-4 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold text-lg transition-colors shadow-lg shadow-red-900/50">
                            📤 Enviar para Moderação
                        </button>
                        <a href="{{ route('videos.my-videos') }}" class="px-6 py-4 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-semibold transition-colors">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let selectedFiles = [];

        function handleFileSelect(event) {
            const files = Array.from(event.target.files);
            selectedFiles = files.slice(0, 10); // Limit to 10 files
            
            displayFilePreviews();
        }

        function displayFilePreviews() {
            const grid = document.getElementById('filePreviewGrid');
            grid.innerHTML = '';
            
            if (selectedFiles.length === 0) {
                grid.classList.add('hidden');
                return;
            }
            
            grid.classList.remove('hidden');
            
            selectedFiles.forEach((file, index) => {
                const isVideo = file.type.startsWith('video/');
                const preview = document.createElement('div');
                preview.className = 'relative bg-gray-800 border border-red-900/50 rounded-lg p-3 hover:border-red-700 transition';
                
                let content = '';
                if (isVideo) {
                    content = `
                        <div class="aspect-video bg-black rounded flex items-center justify-center mb-2">
                            <svg class="w-12 h-12 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                            </svg>
                        </div>
                    `;
                } else {
                    const url = URL.createObjectURL(file);
                    content = `<img src="${url}" class="aspect-video object-cover rounded mb-2">`;
                }
                
                preview.innerHTML = `
                    ${content}
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-300 truncate">${file.name}</p>
                            <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                        </div>
                        <button type="button" onclick="removeFile(${index})" class="ml-2 text-red-400 hover:text-red-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                `;
                
                grid.appendChild(preview);
            });
        }

        function removeFile(index) {
            selectedFiles.splice(index, 1);
            
            // Update file input
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            document.getElementById('mediaFiles').files = dt.files;
            
            displayFilePreviews();
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        // Drag and drop
        const dropZone = document.querySelector('[onclick*="mediaFiles"]');
        
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-red-500', 'bg-red-950/20');
        });
        
        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-red-500', 'bg-red-950/20');
        });
        
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-red-500', 'bg-red-950/20');
            
            const files = Array.from(e.dataTransfer.files);
            selectedFiles = files.slice(0, 10);
            
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            document.getElementById('mediaFiles').files = dt.files;
            
            displayFilePreviews();
        });
    </script>
</x-app-layout>
