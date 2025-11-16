<x-app-layout>
    <div class="min-h-screen bg-black text-gray-100 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-red-500 mb-8">Editar Perfil</h1>

            <!-- Success/Error Messages -->
            @if(session('success'))
            <div class="mb-6 bg-green-950/50 border border-green-800 text-green-400 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 bg-red-950/50 border border-red-800 text-red-400 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
            @endif

            <!-- Avatar Section -->
            <div class="bg-gray-900 rounded-lg border border-red-900/30 p-6 mb-6">
                <h2 class="text-xl font-bold text-white mb-4">Foto de Perfil</h2>
                
                <div class="flex items-center gap-6">
                    @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full border-2 border-red-900/50 object-cover">
                    @else
                    <div class="w-24 h-24 bg-gradient-to-br from-red-900 to-red-950 rounded-full border-2 border-red-900/50 flex items-center justify-center text-white font-bold text-3xl">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    @endif

                    <div class="flex-1">
                        <form method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data" class="flex items-end gap-4">
                            @csrf
                            <div class="flex-1">
                                <input type="file" name="avatar" accept="image/*" class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-600 file:text-white hover:file:bg-red-700 file:cursor-pointer">
                                @error('avatar')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-colors">
                                Upload
                            </button>
                        </form>
                        
                        @if($user->avatar)
                        <form method="POST" action="{{ route('profile.avatar.destroy') }}" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-400 hover:text-red-300" onclick="return confirm('Remover foto de perfil?')">
                                Remover foto
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Profile Info -->
            <div class="bg-gray-900 rounded-lg border border-red-900/30 p-6">
                <h2 class="text-xl font-bold text-white mb-4">Informações do Perfil</h2>
                
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-300 mb-2">Nome</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-red-500 @error('name') border-red-500 @enderror">
                        @error('name')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-300 mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-red-500 @error('email') border-red-500 @enderror">
                        @error('email')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold transition-colors">
                            Salvar Alterações
                        </button>
                        <a href="{{ route('profile.show', $user) }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-semibold transition-colors">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
