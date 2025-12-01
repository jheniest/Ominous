@extends('layouts.app')

@section('title', 'Editar Perfil - ' . config('app.name'))

@section('content')
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
        <div class="bg-gray-900 rounded-lg border border-red-900/30 p-6" x-data="profileEditForm()">
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
                    <label for="nickname" class="block text-sm font-semibold text-gray-300 mb-2">
                        Nickname <span class="text-gray-500 font-normal">(seu @)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">@</span>
                        <input 
                            type="text" 
                            id="nickname" 
                            name="nickname" 
                            x-model="nickname"
                            @input.debounce.400ms="checkNickname()"
                            value="{{ old('nickname', $user->nickname) }}" 
                            required 
                            maxlength="30"
                            class="w-full bg-black border rounded-lg pl-9 pr-10 py-3 text-gray-100 focus:outline-none focus:border-red-500"
                            :class="nicknameStatus === 'available' ? 'border-green-700' : (nicknameStatus === 'taken' ? 'border-red-500' : 'border-red-900/50')"
                        >
                        <!-- Status indicator -->
                        <div class="absolute right-4 top-1/2 -translate-y-1/2">
                            <template x-if="checking">
                                <svg class="w-5 h-5 text-gray-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <template x-if="!checking && nicknameStatus === 'available'">
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </template>
                            <template x-if="!checking && nicknameStatus === 'taken'">
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </template>
                        </div>
                    </div>
                    <p class="mt-1 text-sm" :class="nicknameStatus === 'available' ? 'text-green-500' : (nicknameStatus === 'taken' ? 'text-red-400' : 'text-gray-500')" x-text="nicknameMessage"></p>
                    @error('nickname')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-xs mt-2">
                        Seu perfil ficará em: <span class="text-gray-400">{{ url('/profile/@') }}<span x-text="nickname || '{{ $user->nickname }}'"></span></span>
                    </p>
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-300 mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-red-500 @error('email') border-red-500 @enderror">
                    @error('email')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-4">
                    <button type="submit" 
                            :disabled="nicknameStatus === 'taken' || checking"
                            class="px-6 py-3 bg-red-600 hover:bg-red-700 disabled:bg-gray-700 disabled:cursor-not-allowed text-white rounded-lg font-bold transition-colors">
                        Salvar Alterações
                    </button>
                    <a href="{{ route('profile.show', $user->nickname) }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-semibold transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function profileEditForm() {
    return {
        nickname: '{{ old("nickname", $user->nickname) }}',
        originalNickname: '{{ $user->nickname }}',
        nicknameStatus: '', // 'available', 'taken', or ''
        nicknameMessage: 'Letras, números, _ e - (3-30 caracteres)',
        checking: false,
        
        async checkNickname() {
            const nick = this.nickname.replace(/^@/, '').toLowerCase();
            
            // If same as original, it's available
            if (nick === this.originalNickname) {
                this.nicknameStatus = 'available';
                this.nicknameMessage = 'Seu nickname atual';
                return;
            }
            
            if (nick.length < 3) {
                this.nicknameStatus = '';
                this.nicknameMessage = 'Mínimo 3 caracteres';
                return;
            }
            
            if (!/^[a-zA-Z][a-zA-Z0-9_-]*$/.test(nick)) {
                this.nicknameStatus = 'taken';
                this.nicknameMessage = 'Deve começar com letra. Apenas letras, números, _ e -';
                return;
            }
            
            this.checking = true;
            
            try {
                const response = await fetch('{{ route("profile.check-nickname") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ nickname: nick })
                });
                
                const data = await response.json();
                this.nicknameStatus = data.available ? 'available' : 'taken';
                this.nicknameMessage = data.message;
            } catch (error) {
                console.error('Error checking nickname:', error);
                this.nicknameStatus = '';
                this.nicknameMessage = 'Erro ao verificar disponibilidade';
            }
            
            this.checking = false;
        }
    };
}
</script>
@endpush
@endsection
