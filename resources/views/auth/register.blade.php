@extends('layouts.auth')

@section('title', 'Criar Conta - Atrocidades')

@section('content')
<div class="min-h-[calc(100vh-57px)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-sm">
        <!-- Title -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-neutral-200 mb-2">Criar Conta</h1>
            <p class="text-neutral-500 text-sm">Convite de: <span class="text-red-500">{{ $invited_by }}</span></p>
        </div>

        <!-- Register Form Card -->
        <div class="bg-neutral-950/70 backdrop-blur-xl border border-neutral-800/50 rounded-xl p-6">
            <form method="POST" action="{{ route('register') }}" class="space-y-5" x-data="registerForm()">
                @csrf
                
                <input type="hidden" name="invite_code" value="{{ $invite_code }}">

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-neutral-400 mb-1.5">Nome</label>
                    <input 
                        id="name" 
                        name="name" 
                        type="text" 
                        required 
                        autofocus
                        class="w-full px-4 py-2.5 bg-neutral-900/80 border @error('name') border-red-800 @else border-neutral-700 @enderror rounded-lg text-neutral-200 placeholder-neutral-600 text-sm focus:outline-none focus:ring-2 focus:ring-red-900/50 focus:border-transparent transition"
                        placeholder="Seu nome"
                        value="{{ old('name') }}"
                    >
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nickname -->
                <div>
                    <label for="nickname" class="block text-sm font-medium text-neutral-400 mb-1.5">
                        Nickname <span class="text-neutral-600">(seu @)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-500">@</span>
                        <input 
                            id="nickname" 
                            name="nickname" 
                            type="text" 
                            required
                            x-model="nickname"
                            @input.debounce.400ms="checkNickname()"
                            class="w-full pl-8 pr-10 py-2.5 bg-neutral-900/80 border rounded-lg text-neutral-200 placeholder-neutral-600 text-sm focus:outline-none focus:ring-2 focus:ring-red-900/50 focus:border-transparent transition"
                            :class="nicknameStatus === 'available' ? 'border-green-700' : (nicknameStatus === 'taken' ? 'border-red-800' : 'border-neutral-700')"
                            placeholder="seu_nickname"
                            value="{{ old('nickname') }}"
                            maxlength="30"
                            autocomplete="off"
                        >
                        <!-- Status indicator -->
                        <div class="absolute right-3 top-1/2 -translate-y-1/2">
                            <template x-if="checking">
                                <svg class="w-4 h-4 text-neutral-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <template x-if="!checking && nicknameStatus === 'available'">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </template>
                            <template x-if="!checking && nicknameStatus === 'taken'">
                                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </template>
                        </div>
                    </div>
                    <p class="mt-1.5 text-xs" :class="nicknameStatus === 'available' ? 'text-green-500' : (nicknameStatus === 'taken' ? 'text-red-500' : 'text-neutral-500')" x-text="nicknameMessage"></p>
                    @error('nickname')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-neutral-400 mb-1.5">Email</label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        required
                        class="w-full px-4 py-2.5 bg-neutral-900/80 border @error('email') border-red-800 @else border-neutral-700 @enderror rounded-lg text-neutral-200 placeholder-neutral-600 text-sm focus:outline-none focus:ring-2 focus:ring-red-900/50 focus:border-transparent transition"
                        placeholder="seu@email.com"
                        value="{{ old('email') }}"
                    >
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-neutral-400 mb-1.5">Senha</label>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        required
                        class="w-full px-4 py-2.5 bg-neutral-900/80 border @error('password') border-red-800 @else border-neutral-700 @enderror rounded-lg text-neutral-200 placeholder-neutral-600 text-sm focus:outline-none focus:ring-2 focus:ring-red-900/50 focus:border-transparent transition"
                        placeholder="••••••••"
                    >
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-neutral-400 mb-1.5">Confirmar Senha</label>
                    <input 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        type="password" 
                        required
                        class="w-full px-4 py-2.5 bg-neutral-900/80 border border-neutral-700 rounded-lg text-neutral-200 placeholder-neutral-600 text-sm focus:outline-none focus:ring-2 focus:ring-red-900/50 focus:border-transparent transition"
                        placeholder="••••••••"
                    >
                </div>

                <!-- Terms -->
                <div class="flex items-start">
                    <input 
                        id="terms" 
                        name="terms" 
                        type="checkbox" 
                        required
                        class="mt-0.5 w-4 h-4 bg-neutral-900 border-neutral-700 rounded text-red-600 focus:ring-red-900/50"
                    >
                    <label for="terms" class="ml-2 text-sm text-neutral-500">
                        Aceito os termos e condições
                    </label>
                </div>
                @error('terms')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror

                <!-- Submit -->
                <button type="submit" 
                        :disabled="nicknameStatus === 'taken' || checking"
                        class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 disabled:bg-neutral-700 disabled:cursor-not-allowed text-white rounded-lg transition font-medium text-sm">
                    Criar Conta
                </button>
            </form>
        </div>

        <!-- Links -->
        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm text-neutral-500 hover:text-neutral-400 transition">
                Já tem conta? Faça login
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
function registerForm() {
    return {
        nickname: '{{ old("nickname", "") }}',
        nicknameStatus: '', // 'available', 'taken', or ''
        nicknameMessage: 'Letras, números, _ e - (3-30 caracteres)',
        checking: false,
        
        async checkNickname() {
            const nick = this.nickname.replace(/^@/, '').toLowerCase();
            
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
                const response = await fetch('{{ route("api.check-nickname") }}', {
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
