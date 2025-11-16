<x-app-layout>
    <x-slot name="title">Meus Convites</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-neutral-200">Meus Convites</h1>
                    <p class="mt-1 text-neutral-500">Gerencie seus códigos de convite</p>
                </div>
                <a href="{{ route('dashboard.invites.create') }}" class="px-6 py-3 bg-red-950/60 hover:bg-red-900/60 border border-red-900 hover:border-red-700 text-red-400 hover:text-red-300 rounded-lg transition font-medium">
                    Criar Convite
                </a>
            </div>

            @if($invites->isEmpty())
                <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg p-12 text-center">
                    <p class="text-neutral-500">Você ainda não criou nenhum convite.</p>
                    <a href="{{ route('dashboard.invites.create') }}" class="mt-4 inline-block text-red-700 hover:text-red-500">
                        Criar primeiro convite →
                    </a>
                </div>
            @else
                <div class="bg-neutral-950/60 backdrop-blur-lg border border-neutral-800 rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-neutral-800">
                        <thead class="bg-neutral-900/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Código</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Usos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Origem</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Expira em</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Criado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-800">
                            @foreach($invites as $invite)
                                <tr class="hover:bg-neutral-900/30 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <code class="text-red-400 font-mono">{{ $invite->code }}</code>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded 
                                            @if($invite->status === 'active') bg-green-950/40 text-green-500 border border-green-900/50
                                            @elseif($invite->status === 'consumed') bg-neutral-800/40 text-neutral-500 border border-neutral-700/50
                                            @elseif($invite->status === 'expired') bg-amber-950/40 text-amber-500 border border-amber-900/50
                                            @else bg-red-950/40 text-red-500 border border-red-900/50
                                            @endif">
                                            {{ ucfirst($invite->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-400">
                                        {{ $invite->current_uses }} / {{ $invite->max_uses }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded bg-neutral-800/40 text-neutral-400 border border-neutral-700/50">
                                            {{ ucfirst($invite->source) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-400">
                                        @if($invite->expires_at)
                                            {{ $invite->expires_at->format('d/m/Y') }}
                                        @else
                                            Nunca
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
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
            @endif
        </div>
    </div>
</x-app-layout>
