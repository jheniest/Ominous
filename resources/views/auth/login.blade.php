@extends('layouts.auth')

@section('title', 'Login - Atrocidades')

@section('content')
<div class="min-h-[calc(100vh-57px)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-sm">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-neutral-200 mb-2">Entrar</h1>
            <p class="text-neutral-500 text-sm">Acesse sua conta</p>
        </div>

        <!-- Login Form Card -->
        <div class="bg-neutral-950/70 backdrop-blur-xl border border-neutral-800/50 rounded-xl p-6">
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                @if(session('error'))
                    <div class="bg-red-950/50 border border-red-900/50 text-red-400 px-4 py-3 rounded-lg text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-neutral-400 mb-1.5">Email</label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        required 
                        autofocus
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

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="remember"
                        id="remember"
                        class="w-4 h-4 bg-neutral-900 border-neutral-700 rounded text-red-600 focus:ring-red-900/50"
                    >
                    <label for="remember" class="ml-2 text-sm text-neutral-500">Lembrar de mim</label>
                </div>

                <!-- Submit -->
                <button type="submit" class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-medium text-sm">
                    Entrar
                </button>
            </form>
        </div>

        <!-- Links -->
        <div class="mt-6 text-center space-y-2">
            <a href="{{ route('invite.validate') }}" class="block text-sm text-neutral-500 hover:text-neutral-400 transition">
                Não tem conta? Use um convite
            </a>
            <a href="{{ route('guest.purchase.index') }}" class="block text-sm text-neutral-500 hover:text-neutral-400 transition">
                Comprar convite
            </a>
        </div>
    </div>
</div>
@endsection
