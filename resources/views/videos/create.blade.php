<x-app-layout>
    <div class="min-h-screen bg-black text-gray-100 py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-900 rounded-lg border-2 border-red-900/50 shadow-2xl shadow-red-900/30 p-8">
                <h1 class="text-3xl font-bold text-red-500 mb-6">Upload Video</h1>
                
                <div class="bg-red-900/20 border border-red-700 rounded-lg p-4 mb-6">
                    <p class="text-red-300 text-sm">
                        <strong>Note:</strong> All videos are subject to moderation. Your video will be reviewed before appearing publicly.
                    </p>
                </div>

                <form method="POST" action="{{ route('videos.store') }}" enctype="multipart/form-data" class="space-y-6">
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

                    <!-- Video Upload or URL -->
                    <div class="space-y-4">
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Vídeo * (escolha uma opção)</label>
                        
                        <!-- Tab Switcher -->
                        <div class="flex gap-4 border-b border-red-900/30">
                            <button type="button" onclick="showUploadMethod('file')" id="tab-file" class="px-4 py-2 font-semibold text-red-500 border-b-2 border-red-500">
                                Upload de Arquivo
                            </button>
                            <button type="button" onclick="showUploadMethod('url')" id="tab-url" class="px-4 py-2 font-semibold text-gray-400 border-b-2 border-transparent hover:text-gray-300">
                                URL Externa
                            </button>
                        </div>

                        <!-- File Upload -->
                        <div id="upload-file">
                            <input type="file" name="video_file" accept="video/*" class="block w-full text-sm text-gray-400 file:mr-4 file:py-3 file:px-6 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-600 file:text-white hover:file:bg-red-700 file:cursor-pointer">
                            <p class="text-gray-500 text-xs mt-2">Formatos aceitos: MP4, MOV, AVI, WMV (máx. 100MB)</p>
                            @error('video_file')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- URL Input -->
                        <div id="upload-url" class="hidden">
                            <input type="url" name="video_url" value="{{ old('video_url') }}" maxlength="500" placeholder="https://youtube.com/watch?v=... ou URL de embed" class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-red-500 @error('video_url') border-red-500 @enderror">
                            <p class="text-gray-500 text-xs mt-2">YouTube, Vimeo, ou URL direta de vídeo</p>
                            @error('video_url')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Thumbnail URL -->
                    <div>
                        <label for="thumbnail_url" class="block text-sm font-semibold text-gray-300 mb-2">Thumbnail URL (Optional)</label>
                        <input type="url" id="thumbnail_url" name="thumbnail_url" value="{{ old('thumbnail_url') }}" maxlength="500" placeholder="https://example.com/thumbnail.jpg" class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-red-500 @error('thumbnail_url') border-red-500 @enderror">
                        @error('thumbnail_url')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-semibold text-gray-300 mb-2">Category *</label>
                        <select id="category" name="category" required class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-red-500 @error('category') border-red-500 @enderror">
                            <option value="">Select a category</option>
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

                    <!-- NSFW Checkbox -->
                    <div class="flex items-start">
                        <input type="checkbox" id="is_nsfw" name="is_nsfw" value="1" {{ old('is_nsfw') ? 'checked' : '' }} class="mt-1 w-4 h-4 bg-black border-red-900/50 rounded text-red-600 focus:ring-red-500">
                        <label for="is_nsfw" class="ml-3">
                            <span class="block text-sm font-semibold text-gray-300">Mark as NSFW (Not Safe For Work)</span>
                            <span class="block text-xs text-gray-500">Check this if the video contains graphic or sensitive content</span>
                        </label>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-4 pt-6 border-t border-red-900/30">
                        <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-bold text-lg transition-colors shadow-lg shadow-red-900/50">
                            Submit for Review
                        </button>
                        <a href="{{ route('videos.my-videos') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-semibold transition-colors">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showUploadMethod(method) {
            const fileDiv = document.getElementById('upload-file');
            const urlDiv = document.getElementById('upload-url');
            const fileTab = document.getElementById('tab-file');
            const urlTab = document.getElementById('tab-url');
            const fileInput = document.querySelector('input[name="video_file"]');
            const urlInput = document.querySelector('input[name="video_url"]');

            if (method === 'file') {
                fileDiv.classList.remove('hidden');
                urlDiv.classList.add('hidden');
                fileTab.classList.add('text-red-500', 'border-red-500');
                fileTab.classList.remove('text-gray-400', 'border-transparent');
                urlTab.classList.remove('text-red-500', 'border-red-500');
                urlTab.classList.add('text-gray-400', 'border-transparent');
                // Clear URL input when switching to file
                if (urlInput) urlInput.value = '';
            } else {
                urlDiv.classList.remove('hidden');
                fileDiv.classList.add('hidden');
                urlTab.classList.add('text-red-500', 'border-red-500');
                urlTab.classList.remove('text-gray-400', 'border-transparent');
                fileTab.classList.remove('text-red-500', 'border-red-500');
                fileTab.classList.add('text-gray-400', 'border-transparent');
                // Clear file input when switching to URL
                if (fileInput) fileInput.value = '';
            }
        }
    </script>
</x-app-layout>
