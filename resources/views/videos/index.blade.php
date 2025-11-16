<x-app-layout>
    <div class="min-h-screen bg-black text-gray-100">
        <!-- Featured Videos Section -->
        @if($featured->count() > 0)
        <div class="bg-gradient-to-b from-red-950/20 to-black border-b border-red-900/30 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-red-500 mb-6 flex items-center">
                    <svg class="w-8 h-8 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    Featured Content
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($featured as $featuredVideo)
                    <a href="{{ route('videos.show', $featuredVideo) }}" class="group block">
                        <div class="relative overflow-hidden rounded-lg border-2 border-red-500 shadow-lg shadow-red-900/50 hover:shadow-red-700/70 transition-all duration-300">
                            @if($featuredVideo->thumbnail_url)
                            <img src="{{ $featuredVideo->thumbnail_url }}" alt="{{ $featuredVideo->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                            <div class="w-full h-48 bg-gradient-to-br from-gray-900 to-black flex items-center justify-center">
                                <svg class="w-16 h-16 text-red-900" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                </svg>
                            </div>
                            @endif
                            <div class="absolute top-2 right-2">
                                <span class="px-2 py-1 bg-red-600 text-white text-xs font-bold rounded">FEATURED</span>
                            </div>
                            @if($featuredVideo->is_nsfw)
                            <div class="absolute top-2 left-2">
                                <span class="px-2 py-1 bg-black/80 text-red-400 text-xs font-bold rounded border border-red-500">NSFW</span>
                            </div>
                            @endif
                            <div class="p-4 bg-gradient-to-t from-black to-gray-900">
                                <h3 class="font-bold text-lg text-white group-hover:text-red-400 transition-colors line-clamp-2">{{ $featuredVideo->title }}</h3>
                                <p class="text-sm text-gray-400 mt-1 flex items-center gap-1">
                                    {{ $featuredVideo->user->name }}
                                    @if($featuredVideo->user->is_admin)
                                    <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    @endif
                                </p>
                                <div class="flex items-center text-xs text-gray-500 mt-2">
                                    <span>{{ $featuredVideo->views_count }} views</span>
                                    <span class="mx-2">•</span>
                                    <span>{{ $featuredVideo->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Filters & Search -->
        <div class="bg-gray-900 border-b border-red-900/30 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <form method="GET" action="{{ route('videos.index') }}" class="flex flex-col md:flex-row gap-4">
                    <!-- Search -->
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search videos..." class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-2 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-red-500">
                    </div>
                    
                    <!-- Category Filter -->
                    <select name="category" class="bg-black border border-red-900/50 rounded-lg px-4 py-2 text-gray-100 focus:outline-none focus:border-red-500">
                        <option value="all">All Categories</option>
                        <option value="breaking_news" {{ request('category') == 'breaking_news' ? 'selected' : '' }}>Breaking News</option>
                        <option value="footage" {{ request('category') == 'footage' ? 'selected' : '' }}>Footage</option>
                        <option value="investigation" {{ request('category') == 'investigation' ? 'selected' : '' }}>Investigation</option>
                        <option value="accident" {{ request('category') == 'accident' ? 'selected' : '' }}>Accident</option>
                        <option value="crime" {{ request('category') == 'crime' ? 'selected' : '' }}>Crime</option>
                        <option value="natural_disaster" {{ request('category') == 'natural_disaster' ? 'selected' : '' }}>Natural Disaster</option>
                        <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    
                    <!-- Sort -->
                    <select name="sort" class="bg-black border border-red-900/50 rounded-lg px-4 py-2 text-gray-100 focus:outline-none focus:border-red-500">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Viewed</option>
                        <option value="trending" {{ request('sort') == 'trending' ? 'selected' : '' }}>Trending</option>
                    </select>
                    
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                        Filter
                    </button>
                </form>
            </div>
        </div>

        <!-- Video Grid -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
                        @if($video->is_nsfw)
                        <div class="absolute top-2 left-2">
                            <span class="px-2 py-1 bg-black/80 text-red-400 text-xs font-bold rounded border border-red-500">NSFW</span>
                        </div>
                        @endif
                        <div class="p-4">
                            <h3 class="font-semibold text-white group-hover:text-red-400 transition-colors line-clamp-2">{{ $video->title }}</h3>
                            <p class="text-sm text-gray-400 mt-1 flex items-center gap-1">
                                {{ $video->user->name }}
                                @if($video->user->is_admin)
                                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                @endif
                            </p>
                            <div class="flex items-center justify-between text-xs text-gray-500 mt-2">
                                <span>{{ $video->views_count }} views</span>
                                <span>{{ $video->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="text-xs px-2 py-1 bg-red-900/30 text-red-400 rounded">{{ ucfirst(str_replace('_', ' ', $video->category)) }}</span>
                                @if($video->comments_count > 0)
                                <span class="text-xs text-gray-500">{{ $video->comments_count }} comments</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $videos->links() }}
            </div>
            @else
            <div class="text-center py-16">
                <svg class="w-24 h-24 mx-auto text-red-900 mb-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                </svg>
                <p class="text-xl text-gray-500">No videos found</p>
                <p class="text-gray-600 mt-2">Try adjusting your filters</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
