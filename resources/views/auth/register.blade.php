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
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
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
                <button type="submit" class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-medium text-sm">
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
@endsection
