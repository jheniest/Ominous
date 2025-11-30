@extends('layouts.guest')

@section('title', 'Comprar Convites - Atrocidades')

@section('content')
<div class="py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-neutral-200 mb-2">Adquira Convites</h1>
            <p class="text-neutral-500 text-sm">Descontos progressivos: quanto mais você compra, mais você economiza</p>
        </div>

        <!-- Pricing Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($pricing as $quantity => $price)
                @php
                    $basePrice = 20.00;
                    $totalBase = $basePrice * $quantity;
                    $discount = $quantity > 1 ? (($totalBase - $price) / $totalBase * 100) : 0;
                    $isPopular = $quantity == 5;
                @endphp
                <div class="bg-neutral-900/60 backdrop-blur-xl border {{ $isPopular ? 'border-red-600/50' : 'border-neutral-800/50' }} rounded-xl overflow-hidden hover:border-red-600/30 transition group relative">
                    @if($isPopular)
                        <div class="absolute -top-px left-1/2 -translate-x-1/2 px-3 py-0.5 bg-red-600 text-white text-[10px] font-semibold uppercase tracking-wider rounded-b">
                            Popular
                        </div>
                    @endif
                    
                    <div class="p-5 pt-6">
                        <!-- Quantity -->
                        <div class="text-center mb-4">
                            <div class="text-3xl font-bold text-neutral-200">{{ $quantity }}</div>
                            <div class="text-neutral-500 text-xs">{{ $quantity == 1 ? 'Convite' : 'Convites' }}</div>
                        </div>

                        <!-- Price -->
                        <div class="text-center mb-4">
                            <div class="text-2xl font-bold {{ $isPopular ? 'text-red-500' : 'text-neutral-200' }}">
                                R$ {{ number_format($price, 2, ',', '.') }}
                            </div>
                            <div class="text-xs text-neutral-500">
                                R$ {{ number_format($price / $quantity, 2, ',', '.') }}/cada
                            </div>
                            @if($discount > 0)
                                <div class="mt-1 inline-block px-2 py-0.5 bg-green-900/30 text-green-400 text-[10px] font-semibold rounded">
                                    -{{ number_format($discount, 0) }}%
                                </div>
                            @endif
                        </div>

                        <!-- Button -->
                        <form method="POST" action="{{ route('guest.purchase.checkout') }}">
                            @csrf
                            <input type="hidden" name="quantity" value="{{ $quantity }}">
                            <button type="submit" class="w-full px-4 py-2 {{ $isPopular ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-neutral-800 hover:bg-neutral-700 text-neutral-200' }} rounded-lg transition font-medium text-sm">
                                Comprar
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Info Section -->
        <div class="mt-10 bg-neutral-900/40 backdrop-blur-xl border border-neutral-800/50 rounded-xl p-6">
            <h3 class="text-sm font-semibold text-neutral-300 mb-3">Informações</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2 text-sm text-neutral-500">
                <div class="flex items-center gap-2">
                    <span class="text-red-500">•</span>
                    <span>Convites não expiram após a compra</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-red-500">•</span>
                    <span>Cada convite pode ser usado uma única vez</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-red-500">•</span>
                    <span>PIX, Boleto, Crypto e Mercado Pago</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-red-500">•</span>
                    <span>Entrega por email após confirmação</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
