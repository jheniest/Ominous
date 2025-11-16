<x-app-layout>
    <x-slot name="title">Gerenciar Compras</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-red-500">Compras</h1>
                    <p class="mt-1 text-neutral-500">Todas as transações do sistema</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-neutral-900/60 border border-neutral-800 text-neutral-400 rounded-lg hover:border-neutral-700 transition text-sm">
                    ← Voltar
                </a>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-6">
                    <div class="text-sm font-medium text-neutral-500">Receita Total</div>
                    <div class="mt-2 text-3xl font-bold text-cyan-400">R$ {{ number_format($stats['total_revenue'], 2, ',', '.') }}</div>
                </div>
                <div class="bg-neutral-950/60 backdrop-blur-lg border border-green-900/30 rounded-lg p-6">
                    <div class="text-sm font-medium text-neutral-500">Compras Completadas</div>
                    <div class="mt-2 text-3xl font-bold text-green-500">{{ $stats['completed_purchases'] }}</div>
                </div>
                <div class="bg-neutral-950/60 backdrop-blur-lg border border-amber-900/30 rounded-lg p-6">
                    <div class="text-sm font-medium text-neutral-500">Compras Pendentes</div>
                    <div class="mt-2 text-3xl font-bold text-amber-400">{{ $stats['pending_purchases'] }}</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('admin.purchases') }}" class="flex gap-4">
                    <select 
                        name="status"
                        class="flex-1 px-4 py-2 bg-neutral-900 border border-neutral-800 rounded-lg text-neutral-200 focus:outline-none focus:ring-2 focus:ring-red-900/50">
                        <option value="">Todos os status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completado</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Falhou</option>
                        <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Reembolsado</option>
                    </select>
                    <button type="submit" class="px-6 py-2 bg-red-950/60 border border-red-900 text-red-400 rounded-lg hover:bg-red-900/60 transition">
                        Filtrar
                    </button>
                </form>
            </div>

            <!-- Purchases Table -->
            <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-neutral-800">
                    <thead class="bg-neutral-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase">Usuário</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase">Quantidade</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase">Valor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase">Pagamento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-800">
                        @foreach($purchases as $purchase)
                            <tr class="hover:bg-neutral-900/30 transition">
                                <td class="px-6 py-4 text-neutral-400">
                                    #{{ $purchase->id }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($purchase->user)
                                        <a href="{{ route('admin.users.show', $purchase->user->id) }}" class="text-cyan-500 hover:text-cyan-400">
                                            {{ $purchase->user->name }}
                                        </a>
                                    @else
                                        <span class="text-neutral-600">Deletado</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-neutral-400">
                                    {{ $purchase->quantity }} {{ $purchase->quantity == 1 ? 'convite' : 'convites' }}
                                </td>
                                <td class="px-6 py-4 text-cyan-400 font-semibold">
                                    R$ {{ number_format($purchase->amount, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded 
                                        @if($purchase->status === 'completed') bg-green-950/40 text-green-500 border border-green-900/50
                                        @elseif($purchase->status === 'pending') bg-amber-950/40 text-amber-500 border border-amber-900/50
                                        @elseif($purchase->status === 'refunded') bg-blue-950/40 text-blue-500 border border-blue-900/50
                                        @else bg-red-950/40 text-red-500 border border-red-900/50
                                        @endif">
                                        {{ ucfirst($purchase->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-neutral-400">
                                    {{ ucfirst($purchase->payment_method) }}
                                </td>
                                <td class="px-6 py-4 text-neutral-500">
                                    {{ $purchase->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $purchases->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
