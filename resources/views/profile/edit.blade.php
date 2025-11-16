<x-app-layout>
    <div class="min-h-screen bg-black text-gray-100 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-red-500 mb-8">Editar Perfil</h1>

            <!-- Tabs -->
            <div class="flex border-b border-red-900/30 mb-8">
                <button onclick="showTab('profile')" id="tab-profile" class="px-6 py-3 font-semibold text-red-500 border-b-2 border-red-500">
                    Perfil
                </button>
                <button onclick="showTab('invites')" id="tab-invites" class="px-6 py-3 font-semibold text-gray-400 hover:text-gray-300 border-b-2 border-transparent">
                    Convites
                </button>
            </div>

            <!-- Profile Tab -->
            <div id="content-profile" class="tab-content">
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

            <!-- Invites Tab -->
            <div id="content-invites" class="tab-content hidden">
                <div class="bg-gray-900 rounded-lg border border-red-900/30 p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-white">Seus Convites</h2>
                        @if(!$user->is_admin && $invites->where('status', '!=', 'consumed')->count() >= 3)
                        <p class="text-yellow-400 text-sm">Limite de 3 convites ativos atingido</p>
                        @endif
                    </div>

                    @if($user->is_admin || $invites->where('status', '!=', 'consumed')->count() < 3)
                    <form method="POST" action="{{ route('profile.invites.store') }}" class="mb-6">
                        @csrf
                        <div class="flex gap-4">
                            <div class="flex-1">
                                <input type="text" name="email" placeholder="Email do convidado (opcional)" class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-red-500">
                            </div>
                            <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold transition-colors">
                                Criar Convite
                            </button>
                        </div>
                    </form>
                    @endif
                </div>

                <!-- Invites List -->
                @if($invites->count() > 0)
                <div class="space-y-4">
                    @foreach($invites as $invite)
                    <div class="bg-gray-900 rounded-lg border border-red-900/30 p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <code class="text-sm bg-black px-3 py-1 rounded text-red-400 font-mono">{{ $invite->code }}</code>
                                    @if($invite->status === 'active')
                                    <span class="px-2 py-1 bg-green-900/30 text-green-400 text-xs font-semibold rounded border border-green-700">Ativo</span>
                                    @elseif($invite->status === 'consumed')
                                    <span class="px-2 py-1 bg-blue-900/30 text-blue-400 text-xs font-semibold rounded border border-blue-700">Usado</span>
                                    @else
                                    <span class="px-2 py-1 bg-gray-700 text-gray-300 text-xs font-semibold rounded border border-gray-600">Expirado</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-400">
                                    @if($invite->status === 'consumed' && $invite->redeemedBy)
                                        Usado por {{ $invite->redeemedBy->name }} • {{ $invite->redeemed_at?->diffForHumans() }}
                                    @else
                                        Criado {{ $invite->created_at->diffForHumans() }}
                                        @if($invite->expires_at)
                                            • Expira {{ $invite->expires_at->diffForHumans() }}
                                        @endif
                                    @endif
                                </p>
                            </div>
                            @if($invite->status === 'active')
                            <form method="POST" action="{{ route('profile.invites.destroy', $invite->id) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 text-sm" onclick="return confirm('Deletar este convite?')">
                                    Deletar
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="bg-gray-900 rounded-lg border border-red-900/30 p-12 text-center">
                    <p class="text-gray-400">Você ainda não criou nenhum convite.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function showTab(tab) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('[id^="tab-"]').forEach(el => {
                el.classList.remove('text-red-500', 'border-red-500');
                el.classList.add('text-gray-400', 'border-transparent');
            });

            // Show selected tab
            document.getElementById('content-' + tab).classList.remove('hidden');
            document.getElementById('tab-' + tab).classList.remove('text-gray-400', 'border-transparent');
            document.getElementById('tab-' + tab).classList.add('text-red-500', 'border-red-500');
        }
    </script>
</x-app-layout>
