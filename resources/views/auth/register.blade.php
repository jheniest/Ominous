<x-app-layout>
    <x-slot name="title">Registro</x-slot>

    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-neutral-200">Ingressar no Ominous</h2>
                <p class="mt-2 text-neutral-500">Convite de: <span class="text-red-700">{{ $invited_by }}</span></p>
            </div>

            <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-8">
                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf
                    
                    <input type="hidden" name="invite_code" value="{{ $invite_code }}">

                    <div>
                        <label for="name" class="block text-sm font-medium text-neutral-400 mb-2">Nome</label>
                        <input 
                            id="name" 
                            name="name" 
                            type="text" 
                            required 
                            autofocus
                            class="w-full px-4 py-3 bg-neutral-900 border @error('name') border-red-900 @else border-neutral-800 @enderror rounded-lg text-neutral-200 placeholder-neutral-600 focus:outline-none focus:ring-2 focus:ring-red-900/50 focus:border-transparent transition"
                            value="{{ old('name') }}"
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-neutral-400 mb-2">Email</label>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            required
                            class="w-full px-4 py-3 bg-neutral-900 border @error('email') border-red-900 @else border-neutral-800 @enderror rounded-lg text-neutral-200 placeholder-neutral-600 focus:outline-none focus:ring-2 focus:ring-red-900/50 focus:border-transparent transition"
                            value="{{ old('email') }}"
                        >
                        @error('email')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-neutral-400 mb-2">Senha</label>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            required
                            class="w-full px-4 py-3 bg-neutral-900 border @error('password') border-red-900 @else border-neutral-800 @enderror rounded-lg text-neutral-200 placeholder-neutral-600 focus:outline-none focus:ring-2 focus:ring-red-900/50 focus:border-transparent transition"
                        >
                        @error('password')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-neutral-400 mb-2">Confirmar Senha</label>
                        <input 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            type="password" 
                            required
                            class="w-full px-4 py-3 bg-neutral-900 border border-neutral-800 rounded-lg text-neutral-200 placeholder-neutral-600 focus:outline-none focus:ring-2 focus:ring-red-900/50 focus:border-transparent transition"
                        >
                    </div>

                    <div class="flex items-start">
                        <input 
                            id="terms" 
                            name="terms" 
                            type="checkbox" 
                            required
                            class="mt-1 w-4 h-4 bg-neutral-900 border-neutral-700 rounded text-red-900 focus:ring-red-900/50"
                        >
                        <label for="terms" class="ml-3 text-sm text-neutral-400">
                            Aceito os termos e condições do Ominous
                        </label>
                    </div>
                    @error('terms')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="w-full px-6 py-3 bg-red-950/60 hover:bg-red-900/60 border border-red-900 hover:border-red-700 text-red-400 hover:text-red-300 rounded-lg transition font-medium">
                        Ingressar no Ominous
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
