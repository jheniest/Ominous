<x-app-layout>
    <x-slot name="title">Criar Convite</x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-neutral-200">Criar Novo Convite</h1>
                <p class="mt-1 text-neutral-500">Configure o convite para novos membros</p>
            </div>

            <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-8">
                <form method="POST" action="{{ route('dashboard.invites.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="max_uses" class="block text-sm font-medium text-neutral-400 mb-2">
                            Número de Usos
                        </label>
                        <input 
                            id="max_uses" 
                            name="max_uses" 
                            type="number" 
                            min="1" 
                            max="100" 
                            required
                            value="{{ old('max_uses', 1) }}"
                            class="w-full px-4 py-3 bg-neutral-900 border @error('max_uses') border-red-900 @else border-neutral-800 @enderror rounded-lg text-neutral-200 focus:outline-none focus:ring-2 focus:ring-red-900/50 focus:border-transparent transition"
                        >
                        <p class="mt-2 text-xs text-neutral-600">Quantas vezes este convite pode ser usado</p>
                        @error('max_uses')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="expires_at" class="block text-sm font-medium text-neutral-400 mb-2">
                            Data de Expiração (Opcional)
                        </label>
                        <input 
                            id="expires_at" 
                            name="expires_at" 
                            type="date"
                            min="{{ now()->format('Y-m-d') }}"
                            value="{{ old('expires_at') }}"
                            class="w-full px-4 py-3 bg-neutral-900 border @error('expires_at') border-red-900 @else border-neutral-800 @enderror rounded-lg text-neutral-200 focus:outline-none focus:ring-2 focus:ring-red-900/50 focus:border-transparent transition"
                        >
                        <p class="mt-2 text-xs text-neutral-600">Deixe em branco para nunca expirar</p>
                        @error('expires_at')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" class="flex-1 px-6 py-3 bg-red-950/60 hover:bg-red-900/60 border border-red-900 hover:border-red-700 text-red-400 hover:text-red-300 rounded-lg transition font-medium">
                            Criar Convite
                        </button>
                        <a href="{{ route('profile.edit') }}" class="flex-1 px-6 py-3 bg-neutral-900/60 hover:bg-neutral-800/60 border border-neutral-800 hover:border-neutral-700 text-neutral-400 hover:text-neutral-300 rounded-lg transition font-medium text-center">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
