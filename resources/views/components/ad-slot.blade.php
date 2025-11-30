@props([
    'type' => 'banner', // banner, sidebar, inline, popup, interstitial
    'position' => 'default', // header, footer, sidebar, content, between-posts
    'size' => 'medium', // small, medium, large, responsive
    'id' => null, // ID único para o slot (útil para tracking)
])

{{-- Anúncios só aparecem para visitantes NÃO logados --}}
@guest
    @php
        $slotId = $id ?? 'ad-' . $type . '-' . $position . '-' . uniqid();
        
        // Classes baseadas no tipo e tamanho
        $sizeClasses = match($size) {
            'small' => 'max-w-[300px] h-[100px]',
            'medium' => 'max-w-[728px] h-[90px]',
            'large' => 'max-w-[970px] h-[250px]',
            'responsive' => 'w-full min-h-[100px]',
            default => 'max-w-[728px] h-[90px]',
        };
        
        $typeClasses = match($type) {
            'banner' => 'mx-auto',
            'sidebar' => 'w-full',
            'inline' => 'my-4',
            'popup' => 'fixed bottom-4 right-4 z-40',
            'interstitial' => 'fixed inset-0 z-50 flex items-center justify-center',
            default => '',
        };
    @endphp

    @if($type === 'interstitial')
        {{-- Anúncio Intersticial (tela cheia com X para fechar) --}}
        <div 
            id="{{ $slotId }}" 
            class="ad-slot ad-interstitial {{ $typeClasses }} bg-black/90 hidden"
            x-data="{ show: false }"
            x-init="setTimeout(() => show = true, 3000)"
            x-show="show"
            x-transition
        >
            <div class="relative bg-gray-900 rounded-lg p-4 max-w-2xl w-full mx-4 border border-gray-700">
                <button 
                    @click="show = false" 
                    class="absolute top-2 right-2 text-gray-400 hover:text-white"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <div class="ad-content text-center py-8">
                    {{-- INSERIR CÓDIGO DO ANÚNCIO AQUI --}}
                    <div class="text-gray-500 text-sm">
                        <span class="block text-xs uppercase tracking-wider mb-2">Publicidade</span>
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>

    @elseif($type === 'popup')
        {{-- Anúncio Popup (canto da tela) --}}
        <div 
            id="{{ $slotId }}" 
            class="ad-slot ad-popup {{ $typeClasses }}"
            x-data="{ show: true, dismissed: localStorage.getItem('ad-{{ $slotId }}-dismissed') }"
            x-show="show && !dismissed"
            x-transition
        >
            <div class="bg-gray-900 rounded-lg p-3 border border-gray-700 shadow-xl max-w-[300px]">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] text-gray-500 uppercase tracking-wider">Publicidade</span>
                    <button 
                        @click="show = false; localStorage.setItem('ad-{{ $slotId }}-dismissed', 'true')" 
                        class="text-gray-500 hover:text-white text-xs"
                    >✕</button>
                </div>
                <div class="ad-content">
                    {{-- INSERIR CÓDIGO DO ANÚNCIO AQUI --}}
                    {{ $slot }}
                </div>
            </div>
        </div>

    @else
        {{-- Anúncios padrão (banner, sidebar, inline) --}}
        <div 
            id="{{ $slotId }}" 
            class="ad-slot ad-{{ $type }} {{ $typeClasses }} {{ $sizeClasses }}"
            data-ad-type="{{ $type }}"
            data-ad-position="{{ $position }}"
        >
            <div class="bg-gray-900/50 border border-gray-800 rounded {{ $sizeClasses }} flex items-center justify-center">
                <div class="ad-content text-center w-full">
                    @if($slot->isEmpty())
                        {{-- Placeholder quando não há anúncio configurado --}}
                        <span class="text-gray-600 text-xs uppercase tracking-wider">Espaço Publicitário</span>
                    @else
                        <span class="block text-[10px] text-gray-600 uppercase tracking-wider mb-1">Publicidade</span>
                        {{ $slot }}
                    @endif
                </div>
            </div>
        </div>
    @endif
@endguest
