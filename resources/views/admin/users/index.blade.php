<x-app-layout>
    <x-slot name="title">Gerenciar Usuários</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-red-500">Usuários</h1>
                    <p class="mt-1 text-neutral-500">Gerenciar todos os usuários do sistema</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-neutral-900/60 border border-neutral-800 text-neutral-400 rounded-lg hover:border-neutral-700 transition text-sm">
                    ← Voltar
                </a>
            </div>

            <!-- Search & Filters -->
            <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-4">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Buscar por nome ou email..."
                        value="{{ request('search') }}"
                        class="flex-1 px-4 py-2 bg-neutral-900 border border-neutral-800 rounded-lg text-neutral-200 placeholder-neutral-600 focus:outline-none focus:ring-2 focus:ring-red-900/50"
                    >
                    <select 
                        name="status"
                        class="px-4 py-2 bg-neutral-900 border border-neutral-800 rounded-lg text-neutral-200 focus:outline-none focus:ring-2 focus:ring-red-900/50">
                        <option value="">Todos os status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativos</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspensos</option>
                    </select>
                    <button type="submit" class="px-6 py-2 bg-red-950/60 border border-red-900 text-red-400 rounded-lg hover:bg-red-900/60 transition">
                        Buscar
                    </button>
                </form>
            </div>

            <!-- Users Table -->
            <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-neutral-800">
                    <thead class="bg-neutral-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Usuário</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Convidado por</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Registro</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-800">
                        @foreach($users as $user)
                            <tr class="hover:bg-neutral-900/30 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div>
                                            <div class="text-neutral-200 font-medium">{{ $user->name }}</div>
                                            <div class="text-sm text-neutral-500">{{ $user->email }}</div>
                                        </div>
                                        @if($user->is_admin)
                                            <span class="px-2 py-1 text-xs rounded bg-red-950/40 text-red-400 border border-red-900/50">Admin</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->is_suspended)
                                        <span class="px-2 py-1 text-xs rounded bg-red-950/40 text-red-500 border border-red-900/50">Suspenso</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded bg-green-950/40 text-green-500 border border-green-900/50">Ativo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-400">
                                    @if($user->invitedBy)
                                        <a href="{{ route('admin.users.show', $user->invitedBy->id) }}" class="text-red-700 hover:text-red-500">
                                            {{ $user->invitedBy->name }}
                                        </a>
                                    @else
                                        <span class="text-neutral-600">Sistema</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="text-cyan-500 hover:text-cyan-400">Ver</a>
                                        @if(!$user->is_admin)
                                            @if($user->is_suspended)
                                                <form method="POST" action="{{ route('admin.users.unsuspend', $user->id) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-500 hover:text-green-400">Reativar</button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.users.suspend', $user->id) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-red-500 hover:text-red-400">Suspender</button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
