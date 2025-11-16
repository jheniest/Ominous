<x-app-layout>
    <x-slot name="title">Gerenciar Convites</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-red-500">Convites</h1>
                    <p class="mt-1 text-neutral-500">Todos os convites do sistema</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-neutral-900/60 border border-neutral-800 text-neutral-400 rounded-lg hover:border-neutral-700 transition text-sm">
                    ← Voltar
                </a>
            </div>

            <!-- Filters -->
            <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('admin.invites') }}" class="flex gap-4">
                    <input 
                        type="text" 
                        name="code" 
                        placeholder="Buscar por código..."
                        value="{{ request('code') }}"
                        class="flex-1 px-4 py-2 bg-neutral-900 border border-neutral-800 rounded-lg text-neutral-200 placeholder-neutral-600 focus:outline-none focus:ring-2 focus:ring-red-900/50"
                    >
                    <select 
                        name="status"
                        class="px-4 py-2 bg-neutral-900 border border-neutral-800 rounded-lg text-neutral-200 focus:outline-none focus:ring-2 focus:ring-red-900/50">
                        <option value="">Todos os status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativos</option>
                        <option value="consumed" {{ request('status') === 'consumed' ? 'selected' : '' }}>Consumidos</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expirados</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspensos</option>
                    </select>
                    <select 
                        name="source"
                        class="px-4 py-2 bg-neutral-900 border border-neutral-800 rounded-lg text-neutral-200 focus:outline-none focus:ring-2 focus:ring-red-900/50">
                        <option value="">Todas as origens</option>
                        <option value="manual" {{ request('source') === 'manual' ? 'selected' : '' }}>Manual</option>
                        <option value="purchase" {{ request('source') === 'purchase' ? 'selected' : '' }}>Compra</option>
                        <option value="admin" {{ request('source') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    <button type="submit" class="px-6 py-2 bg-red-950/60 border border-red-900 text-red-400 rounded-lg hover:bg-red-900/60 transition">
                        Buscar
                    </button>
                </form>
            </div>

            <!-- Invites Table -->
            <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-neutral-800">
                    <thead class="bg-neutral-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase">Criado por</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase">Usos</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase">Origem</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase">Expira em</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase">Criado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-800">
                        @foreach($invites as $invite)
                            <tr class="hover:bg-neutral-900/30 transition">
                                <td class="px-6 py-4">
                                    <code class="text-red-400 font-mono">{{ $invite->code }}</code>
                                </td>
                                <td class="px-6 py-4">
                                    @if($invite->createdBy)
                                        <a href="{{ route('admin.users.show', $invite->createdBy->id) }}" class="text-cyan-500 hover:text-cyan-400">
                                            {{ $invite->createdBy->name }}
                                        </a>
                                    @else
                                        <span class="text-neutral-600">Sistema</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded 
                                        @if($invite->status === 'active') bg-green-950/40 text-green-500 border border-green-900/50
                                        @elseif($invite->status === 'consumed') bg-neutral-800/40 text-neutral-500 border border-neutral-700/50
                                        @elseif($invite->status === 'expired') bg-amber-950/40 text-amber-500 border border-amber-900/50
                                        @else bg-red-950/40 text-red-500 border border-red-900/50
                                        @endif">
                                        {{ ucfirst($invite->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-neutral-400">
                                    {{ $invite->current_uses }}/{{ $invite->max_uses }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded bg-neutral-800/40 text-neutral-400 border border-neutral-700/50">
                                        {{ ucfirst($invite->source) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-neutral-400">
                                    @if($invite->expires_at)
                                        {{ $invite->expires_at->format('d/m/Y') }}
                                    @else
                                        Nunca
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-neutral-500">
                                    {{ $invite->created_at->format('d/m/Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $invites->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
