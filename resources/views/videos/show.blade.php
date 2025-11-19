<x-app-layout>
    <div class="min-h-screen bg-black text-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content Column - News Article Style -->
                <div class="lg:col-span-2">
                    <!-- News Header Section -->
                    <article class="bg-gray-900 rounded-lg border border-red-900/30 overflow-hidden">
                        <!-- Alert Banner (Category) -->
                        <div class="bg-red-900/90 px-6 py-2 border-b border-red-800">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="px-3 py-1 bg-red-600 text-white rounded-sm text-xs font-bold uppercase tracking-wider">
                                        {{ \App\Helpers\CategoryHelper::getPortugueseName($video->category) }}
                                    </span>
                                    @if($video->is_nsfw)
                                    <span class="px-3 py-1 bg-black text-red-500 rounded-sm text-xs font-bold border border-red-500">
                                        CONTEÚDO SENSÍVEL
                                    </span>
                                    @endif
                                    @if($video->is_featured)
                                    <span class="px-3 py-1 bg-yellow-600 text-black rounded-sm text-xs font-bold">
                                        DESTAQUE
                                    </span>
                                    @endif
                                </div>
                                @auth
                                @if(!$video->user->is_admin)
                                <button onclick="document.getElementById('reportModal').classList.remove('hidden')" class="px-3 py-1 bg-red-900/50 hover:bg-red-900/70 text-red-300 rounded-sm text-xs font-semibold transition-colors border border-red-800">
                                    Denunciar
                                </button>
                                @endif
                                @endauth
                            </div>
                        </div>

                        <!-- Title Section -->
                        <div class="px-6 pt-8 pb-4 border-b border-red-900/20">
                            <h1 class="text-4xl md:text-5xl font-bold text-white leading-tight mb-4">
                                {{ $video->title }}
                            </h1>
                            
                            @if($video->subtitle)
                            <h2 class="text-xl md:text-2xl text-gray-300 font-medium leading-relaxed">
                                {{ $video->subtitle }}
                            </h2>
                            @endif
                        </div>

                        <!-- Metadata Section -->
                        <div class="px-6 py-4 border-b border-red-900/20 bg-black/30">
                            <div class="flex flex-wrap items-center gap-4 text-sm">
                                <!-- Author -->
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-500 font-semibold">Por:</span>
                                    <a href="{{ route('profile.show', $video->user) }}" class="flex items-center gap-2 text-white hover:text-red-400 transition-colors font-semibold">
                                        @if($video->user->avatar)
                                        <img src="{{ asset('storage/' . $video->user->avatar) }}" alt="{{ $video->user->name }}" class="w-6 h-6 rounded-full object-cover">
                                        @else
                                        <div class="w-6 h-6 bg-gradient-to-br from-red-900 to-red-950 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                            {{ substr($video->user->name, 0, 1) }}
                                        </div>
                                        @endif
                                        {{ $video->user->name }}
                                        @if($video->user->is_admin)
                                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        @endif
                                    </a>
                                </div>

                                <span class="text-gray-700">|</span>

                                <!-- Date -->
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                    <time datetime="{{ $video->created_at->toIso8601String() }}" class="text-gray-400">
                                        {{ $video->created_at->format('d \d\e F \d\e Y') }} às {{ $video->created_at->format('H:i') }}
                                    </time>
                                </div>

                                <span class="text-gray-700">|</span>

                                <!-- Views -->
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-400">{{ number_format($video->views_count) }} visualizações</span>
                                </div>

                                @if($video->approved_at && $video->approvedBy)
                                <span class="text-gray-700">|</span>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-green-400 text-xs">Verificado por {{ $video->approvedBy->name }}</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Video/Media Content (Starting the paragraph) -->
                        <div class="p-6">
                            <div class="bg-black rounded-lg overflow-hidden border-2 border-red-900/50 shadow-2xl shadow-red-900/30 mb-6">
                                @if($video->media && $video->media->count() > 0)
                                    <!-- Multiple Media Carousel -->
                                    <x-media-carousel :media="$video->media" />
                                @elseif($video->video_file)
                                    <!-- Legacy Single Video Player -->
                                    <div class="aspect-video bg-gradient-to-br from-gray-900 to-black flex items-center justify-center">
                                        <video controls class="w-full h-full" controlsList="nodownload">
                                            <source src="{{ asset('storage/' . $video->video_file) }}" type="video/mp4">
                                            Seu navegador não suporta a reprodução de vídeo.
                                        </video>
                                    </div>
                                @elseif($video->video_url)
                                    <!-- External Video Embed -->
                                    <div class="aspect-video bg-gradient-to-br from-gray-900 to-black flex items-center justify-center">
                                        <iframe src="{{ $video->video_url }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                                    </div>
                                @else
                                    <!-- No Video Available -->
                                    <div class="aspect-video bg-gradient-to-br from-gray-900 to-black flex items-center justify-center">
                                        <div class="text-center">
                                            <svg class="w-24 h-24 mx-auto text-red-900 mb-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                            </svg>
                                            <p class="text-gray-500">Video unavailable</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Description/Article Content -->
                            <div class="prose prose-invert prose-lg max-w-none">
                                <div class="text-gray-300 leading-relaxed whitespace-pre-line text-lg">
                                    {{ $video->description }}
                                </div>
                            </div>

                            <!-- Tags Section -->
                            @if($video->tags && $video->tags->count() > 0)
                            <div class="mt-8 pt-6 border-t border-red-900/30">
                                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">TAGS:</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($video->tags as $tag)
                                        <a href="{{ route('videos.index', ['search' => $tag->name]) }}" 
                                           class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-red-900/30 text-red-400 border border-red-800/50 hover:bg-red-900/50 hover:border-red-700 transition-all hover:scale-105">
                                            #{{ $tag->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        @if($video->status !== 'approved' && Auth::user()?->is_admin)
                        <div class="px-6 pb-6">
                            <div class="bg-yellow-900/20 border border-yellow-700 rounded-lg p-4">
                                <p class="text-yellow-400 font-semibold">Admin Note: This video is {{ $video->status }}</p>
                                @if($video->rejection_reason)
                                <p class="text-yellow-300 text-sm mt-2">Reason: {{ $video->rejection_reason }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </article>

                    <!-- Comments Section -->
                    <div class="mt-8">
                        <h2 class="text-xl font-bold text-white mb-4">Comments ({{ $video->comments_count }})</h2>

                        @auth
                        <!-- Comment Form -->
                        <form method="POST" action="{{ route('videos.comments.store', $video) }}" class="mb-8">
                            @csrf
                            <textarea name="content" rows="3" placeholder="Add a comment..." class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-red-500" required></textarea>
                            <button type="submit" class="mt-2 px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-colors">
                                Comment
                            </button>
                        </form>
                        @else
                        <p class="text-gray-500 mb-8">Please <a href="{{ route('login') }}" class="text-red-400 hover:underline">login</a> to comment.</p>
                        @endauth

                        <!-- Comments List -->
                        @foreach($comments as $comment)
                        <div class="bg-gray-900 rounded-lg p-4 border border-red-900/30 mb-4">
                            <div class="flex items-start gap-4">
                                <a href="{{ route('profile.show', $comment->user) }}" class="w-10 h-10 bg-gradient-to-br from-red-900 to-red-950 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0 hover:ring-2 hover:ring-red-500 transition-all">
                                    @if($comment->user->avatar)
                                    <img src="{{ asset('storage/' . $comment->user->avatar) }}" alt="{{ $comment->user->name }}" class="w-full h-full rounded-full object-cover">
                                    @else
                                    {{ substr($comment->user->name, 0, 1) }}
                                    @endif
                                </a>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('profile.show', $comment->user) }}" class="font-semibold text-white hover:text-red-400 flex items-center gap-1">
                                            {{ $comment->user->name }}
                                            @if($comment->user->is_admin)
                                            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            @endif
                                        </a>
                                        <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-gray-300 mt-2">{{ $comment->content }}</p>
                                    
                                    @auth
                                    <button onclick="toggleReplyForm({{ $comment->id }})" class="text-sm text-red-400 hover:text-red-300 mt-2">
                                        Reply
                                    </button>
                                    @endauth

                                    <!-- Reply Form -->
                                    <form id="reply-form-{{ $comment->id }}" method="POST" action="{{ route('videos.comments.store', $video) }}" class="hidden mt-4">
                                        @csrf
                                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                        <textarea name="content" rows="2" placeholder="Write a reply..." class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-2 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-red-500" required></textarea>
                                        <div class="flex gap-2 mt-2">
                                            <button type="submit" class="px-4 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm font-semibold transition-colors">
                                                Reply
                                            </button>
                                            <button type="button" onclick="toggleReplyForm({{ $comment->id }})" class="px-4 py-1 bg-gray-700 hover:bg-gray-600 text-white rounded text-sm transition-colors">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>

                                    <!-- Replies -->
                                    @if($comment->replies->count() > 0)
                                    <div class="mt-4 space-y-4">
                                        @foreach($comment->replies as $reply)
                                        <div class="flex items-start gap-3 pl-4 border-l-2 border-red-900/30">
                                            <a href="{{ route('profile.show', $reply->user) }}" class="w-8 h-8 bg-gradient-to-br from-red-900 to-red-950 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0 hover:ring-2 hover:ring-red-500 transition-all">
                                                @if($reply->user->avatar)
                                                <img src="{{ asset('storage/' . $reply->user->avatar) }}" alt="{{ $reply->user->name }}" class="w-full h-full rounded-full object-cover">
                                                @else
                                                {{ substr($reply->user->name, 0, 1) }}
                                                @endif
                                            </a>
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('profile.show', $reply->user) }}" class="font-semibold text-white text-sm hover:text-red-400 flex items-center gap-1">
                                                        {{ $reply->user->name }}
                                                        @if($reply->user->is_admin)
                                                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        @endif
                                                    </a>
                                                    <span class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="text-gray-300 text-sm mt-1">{{ $reply->content }}</p>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach

                        {{ $comments->links() }}
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <h3 class="text-lg font-bold text-white mb-4">Related Videos</h3>
                    <div class="space-y-4">
                        @foreach($related as $relatedVideo)
                        <a href="{{ route('videos.show', $relatedVideo) }}" class="block group">
                            <div class="bg-gray-900 rounded-lg overflow-hidden border border-red-900/30 hover:border-red-500/50 transition-all">
                                @if($relatedVideo->thumbnail_url)
                                <img src="{{ $relatedVideo->thumbnail_url }}" alt="{{ $relatedVideo->title }}" class="w-full h-32 object-cover group-hover:opacity-80 transition-opacity">
                                @else
                                <div class="w-full h-32 bg-gradient-to-br from-gray-800 to-black flex items-center justify-center">
                                    <svg class="w-10 h-10 text-red-900" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                    </svg>
                                </div>
                                @endif
                                <div class="p-3">
                                    <h4 class="font-semibold text-white text-sm group-hover:text-red-400 transition-colors line-clamp-2">{{ $relatedVideo->title }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">{{ $relatedVideo->user->name }}</p>
                                    <p class="text-xs text-gray-600 mt-1">{{ $relatedVideo->views_count }} views</p>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Modal -->
    @auth
    <div id="reportModal" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
        <div class="bg-gray-900 rounded-lg border-2 border-red-900 max-w-md w-full p-6">
            <h3 class="text-xl font-bold text-white mb-4">Report Video</h3>
            <form method="POST" action="{{ route('videos.report', $video) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Reason</label>
                        <select name="reason" class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-2 text-gray-100 focus:outline-none focus:border-red-500" required>
                            <option value="inappropriate">Inappropriate Content</option>
                            <option value="fake">Fake/Misleading</option>
                            <option value="spam">Spam</option>
                            <option value="violence">Graphic Violence</option>
                            <option value="copyright">Copyright Violation</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Additional Details (Optional)</label>
                        <textarea name="description" rows="3" class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-2 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-red-500"></textarea>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-semibold transition-colors">
                        Submit Report
                    </button>
                    <button type="button" onclick="document.getElementById('reportModal').classList.add('hidden')" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-2 rounded-lg font-semibold transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endauth

    <script>
        function toggleReplyForm(commentId) {
            const form = document.getElementById('reply-form-' + commentId);
            form.classList.toggle('hidden');
        }
    </script>
</x-app-layout>
