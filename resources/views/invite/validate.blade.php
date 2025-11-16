<x-app-layout>
    <x-slot name="title">Validar Convite</x-slot>

    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-neutral-200">Validar Convite</h2>
                <p class="mt-2 text-neutral-500">Insira o código para iniciar o ritual</p>
            </div>

            <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-8">
                <form method="POST" action="{{ route('invite.check') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="code" class="block text-sm font-medium text-neutral-400 mb-2">
                            Código de Convite
                        </label>
                        <input 
                            id="code" 
                            name="code" 
                            type="text" 
                            required 
                            autofocus
                            placeholder="XXX-XXXX-XXX"
                            class="w-full px-4 py-3 bg-neutral-900 border @error('code') border-red-900 @else border-neutral-800 @enderror rounded-lg text-neutral-200 placeholder-neutral-600 focus:outline-none focus:ring-2 focus:ring-red-900/50 focus:border-transparent transition uppercase tracking-widest text-center text-lg"
                            value="{{ old('code') }}"
                        >
                        @error('code')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    @if(session('error_type'))
                        <div class="p-4 rounded-lg @if(session('error_type') === 'corrupted') bg-red-950/40 border-red-900/50 @elseif(session('error_type') === 'expired') bg-amber-950/40 border-amber-900/50 @elseif(session('error_type') === 'consumed') bg-neutral-800/40 border-neutral-700/50 @else bg-red-950/40 border-red-900/50 @endif border">
                            <div class="flex items-center gap-3">
                                <div class="text-2xl">
                                    @if(session('error_type') === 'corrupted') ⚠️
                                    @elseif(session('error_type') === 'expired') ⏳
                                    @elseif(session('error_type') === 'consumed') 🔒
                                    @else ❌
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold @if(session('error_type') === 'corrupted') text-red-400 @elseif(session('error_type') === 'expired') text-amber-400 @elseif(session('error_type') === 'consumed') text-neutral-400 @else text-red-400 @endif">
                                        {{ session('error') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <button type="submit" class="w-full px-6 py-3 bg-red-950/60 hover:bg-red-900/60 border border-red-900 hover:border-red-700 text-red-400 hover:text-red-300 rounded-lg transition font-medium">
                        Validar Convite
                    </button>

                    <div class="text-center">
                        <a href="{{ route('guest.purchase.index') }}" class="text-sm text-neutral-500 hover:text-neutral-400 transition">
                            Não tem convite? Compre aqui
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
