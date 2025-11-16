@props(['media'])

@if($media && $media->count() > 0)
<div class="media-carousel relative bg-black rounded-lg overflow-hidden" x-data="{ 
    currentIndex: 0,
    totalMedia: {{ $media->count() }},
    next() {
        this.currentIndex = (this.currentIndex + 1) % this.totalMedia;
    },
    prev() {
        this.currentIndex = (this.currentIndex - 1 + this.totalMedia) % this.totalMedia;
    },
    goTo(index) {
        this.currentIndex = index;
    }
}">
    <!-- Media Slides -->
    <div class="relative aspect-video w-full">
        @foreach($media as $index => $item)
        <div x-show="currentIndex === {{ $index }}" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="absolute inset-0">
            
            @if($item->isVideo())
                <video controls class="w-full h-full object-contain bg-black">
                    <source src="{{ Storage::url($item->file_path) }}" type="{{ $item->mime_type }}">
                    Seu navegador não suporta vídeo.
                </video>
            @else
                <img src="{{ Storage::url($item->file_path) }}" 
                     alt="Imagem {{ $index + 1 }}" 
                     class="w-full h-full object-contain bg-black">
            @endif
        </div>
        @endforeach
    </div>

    <!-- Navigation Arrows (only if more than 1 item) -->
    @if($media->count() > 1)
    <button @click="prev()" 
            class="absolute left-4 top-1/2 -translate-y-1/2 bg-black/60 hover:bg-black/80 text-white rounded-full p-3 transition backdrop-blur-sm">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
    </button>

    <button @click="next()" 
            class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/60 hover:bg-black/80 text-white rounded-full p-3 transition backdrop-blur-sm">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
    </button>

    <!-- Dots Indicator -->
    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
        @foreach($media as $index => $item)
        <button @click="goTo({{ $index }})"
                :class="currentIndex === {{ $index }} ? 'bg-red-600' : 'bg-white/40 hover:bg-white/60'"
                class="w-2.5 h-2.5 rounded-full transition">
        </button>
        @endforeach
    </div>

    <!-- Media Counter -->
    <div class="absolute top-4 right-4 bg-black/60 backdrop-blur-sm text-white px-3 py-1 rounded-full text-sm font-semibold">
        <span x-text="currentIndex + 1"></span> / <span x-text="totalMedia"></span>
    </div>

    <!-- Media Type Badge -->
    <div class="absolute top-4 left-4">
        <span x-show="currentIndex >= 0" class="inline-block bg-black/60 backdrop-blur-sm text-white px-3 py-1 rounded-full text-xs font-semibold">
            @foreach($media as $index => $item)
            <span x-show="currentIndex === {{ $index }}">
                @if($item->isVideo())
                    🎬 Vídeo
                @else
                    🖼️ Imagem
                @endif
            </span>
            @endforeach
        </span>
    </div>
    @endif
</div>
@endif
