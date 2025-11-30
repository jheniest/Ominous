@extends('layouts.auth')

@section('title', 'Validar Convite - Atrocidades')

@section('content')
<div class="min-h-[calc(100vh-57px)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-sm">
        <!-- Title -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-neutral-200 mb-2">Validar Convite</h1>
            <p class="text-neutral-500 text-sm">Insira seu código de acesso</p>
        </div>

        <!-- Form Card -->
        <div class="bg-neutral-950/70 backdrop-blur-xl border border-neutral-800/50 rounded-xl p-6">
            <form method="POST" action="{{ route('invite.check') }}" class="space-y-5">
                @csrf

                <!-- Code Input -->
                <div>
                    <label for="code" class="block text-sm font-medium text-neutral-400 mb-1.5">
                        Código de Convite
                    </label>
                    <input 
                        id="code" 
                        name="code" 
                        type="text" 
                        required 
                        autofocus
                        placeholder="XXX-XXXX-XXX"
                        class="w-full px-4 py-3 bg-neutral-900/80 border @error('code') border-red-800 @else border-neutral-700 @enderror rounded-lg text-neutral-200 placeholder-neutral-600 focus:outline-none focus:ring-2 focus:ring-red-900/50 focus:border-transparent transition uppercase tracking-widest text-center text-lg font-mono"
                        value="{{ old('code') }}"
                    >
                    @error('code')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Error Messages -->
                @if(session('error_type'))
                    <div class="p-3 rounded-lg text-sm
                        @if(session('error_type') === 'corrupted') bg-red-950/50 border border-red-900/50 
                        @elseif(session('error_type') === 'expired') bg-amber-950/50 border border-amber-900/50 
                        @elseif(session('error_type') === 'consumed') bg-neutral-800/50 border border-neutral-700/50 
                        @else bg-red-950/50 border border-red-900/50 
                        @endif">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">
                                @if(session('error_type') === 'corrupted') ⚠️
                                @elseif(session('error_type') === 'expired') ⏳
                                @elseif(session('error_type') === 'consumed') 🔒
                                @else ❌
                                @endif
                            </span>
                            <span class="
                                @if(session('error_type') === 'corrupted') text-red-400 
                                @elseif(session('error_type') === 'expired') text-amber-400 
                                @elseif(session('error_type') === 'consumed') text-neutral-400 
                                @else text-red-400 
                                @endif">
                                {{ session('error') }}
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Submit -->
                <button type="submit" class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-medium text-sm">
                    Validar Convite
                </button>
            </form>
        </div>

        <!-- Links -->
        <div class="mt-6 text-center space-y-2">
            <a href="{{ route('guest.purchase.index') }}" class="block text-sm text-neutral-500 hover:text-neutral-400 transition">
                Não tem convite? Compre aqui
            </a>
            <a href="{{ route('login') }}" class="block text-sm text-neutral-500 hover:text-neutral-400 transition">
                Já tem conta? Faça login
            </a>
        </div>
    </div>
</div>
@endsection
