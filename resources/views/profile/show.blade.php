<x-app-layout>
    <div class="min-h-screen bg-black text-gray-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Profile Header -->
            <div class="bg-gray-900 rounded-lg border-2 border-red-900/50 shadow-2xl shadow-red-900/30 p-8 mb-8">
                <div class="flex items-start gap-6">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-32 h-32 rounded-full border-4 border-red-900/50 object-cover">
                        @else
                        <div class="w-32 h-32 bg-gradient-to-br from-red-900 to-red-950 rounded-full border-4 border-red-900/50 flex items-center justify-center text-white font-bold text-5xl">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        @endif
                    </div>

                    <!-- User Info -->
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h1 class="text-3xl font-bold text-white">{{ $user->name }}</h1>
                            @if($user->is_verified)
                            <svg class="w-7 h-7 text-red-600" fill="currentColor" viewBox="0 0 20 20" title="Verificado">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            @endif
                            @if($user->is_admin)
                            <span class="px-3 py-1 bg-red-600 text-white text-xs font-bold rounded">ADMIN</span>
                            @endif
                        </div>
                        <p class="text-gray-400 mb-4">Membro desde {{ $user->created_at->format('M Y') }}</p>
                        
                        <div class="flex items-center gap-6 text-sm">
                            <div>
                                <span class="text-gray-500">Vídeos:</span>
                                <span class="text-white font-semibold ml-1">{{ $user->videos()->count() }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Comentários:</span>
                                <span class="text-white font-semibold ml-1">{{ $user->comments()->count() }}</span>
                            </div>
                        </div>

                        @if(Auth::id() === $user->id)
                        <div class="mt-6">
                            <a href="{{ route('profile.edit') }}" class="inline-block px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-colors">
                                Editar Perfil
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Videos Section -->
            <div>
                <h2 class="text-2xl font-bold text-red-500 mb-6">
                    Vídeos de {{ $user->name }}
                </h2>

                @if($videos->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($videos as $video)
                    <a href="{{ route('videos.show', $video) }}" class="group block">
                        <div class="bg-gray-900 rounded-lg overflow-hidden border border-red-900/30 hover:border-red-500/50 shadow-lg hover:shadow-red-900/50 transition-all duration-300">
                            @if($video->thumbnail_url)
                            <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="w-full h-40 object-cover group-hover:opacity-80 transition-opacity">
                            @else
                            <div class="w-full h-40 bg-gradient-to-br from-gray-800 to-black flex items-center justify-center">
                                <svg class="w-12 h-12 text-red-900" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                </svg>
                            </div>
                            @endif
                            <div class="p-4">
                                <h3 class="font-semibold text-white group-hover:text-red-400 transition-colors line-clamp-2">{{ $video->title }}</h3>
                                <div class="flex items-center justify-between text-xs text-gray-500 mt-2">
                                    <span>{{ $video->views_count }} views</span>
                                    <span>{{ $video->created_at->diffForHumans() }}</span>
                                </div>
                                @if($video->status !== 'approved')
                                <div class="mt-2">
                                    <span class="text-xs px-2 py-1 bg-yellow-900/30 text-yellow-400 rounded">{{ ucfirst($video->status) }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $videos->links() }}
                </div>
                @else
                <div class="text-center py-16 bg-gray-900 rounded-lg border border-red-900/30">
                    <svg class="w-24 h-24 mx-auto text-red-900 mb-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                    </svg>
                    <p class="text-xl text-gray-400">Nenhum vídeo ainda</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
