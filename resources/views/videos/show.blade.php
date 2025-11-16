<x-app-layout>
    <div class="min-h-screen bg-black text-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Video Column -->
                <div class="lg:col-span-2">
                    <!-- Video Player -->
                    <div class="bg-black rounded-lg overflow-hidden border-2 border-red-900/50 shadow-2xl shadow-red-900/30">
                        <div class="aspect-video bg-gradient-to-br from-gray-900 to-black flex items-center justify-center">
                            @if($video->video_file)
                            <!-- Local Video Player -->
                            <video controls class="w-full h-full" controlsList="nodownload">
                                <source src="{{ asset('storage/' . $video->video_file) }}" type="video/mp4">
                                Seu navegador não suporta a reprodução de vídeo.
                            </video>
                            @elseif($video->video_url)
                            <!-- External Video Embed -->
                            <iframe src="{{ $video->video_url }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                            @else
                            <!-- No Video Available -->
                            <div class="text-center">
                                <svg class="w-24 h-24 mx-auto text-red-900 mb-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                </svg>
                                <p class="text-gray-500">Video unavailable</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Video Info -->
                    <div class="mt-6 bg-gray-900 rounded-lg p-6 border border-red-900/30">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h1 class="text-2xl font-bold text-white">{{ $video->title }}</h1>
                                <div class="flex items-center gap-4 mt-3 text-sm text-gray-400">
                                    <span>{{ $video->views_count }} views</span>
                                    <span>{{ $video->created_at->format('M d, Y') }}</span>
                                    <span class="px-2 py-1 bg-red-900/30 text-red-400 rounded text-xs">{{ ucfirst(str_replace('_', ' ', $video->category)) }}</span>
                                    @if($video->is_nsfw)
                                    <span class="px-2 py-1 bg-black text-red-500 rounded text-xs border border-red-500">NSFW</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Report Button -->
                            @auth
                            <button onclick="document.getElementById('reportModal').classList.remove('hidden')" class="px-4 py-2 bg-red-900/30 hover:bg-red-900/50 text-red-400 rounded-lg text-sm font-semibold transition-colors border border-red-900">
                                Report
                            </button>
                            @endauth
                        </div>

                        <!-- User Info -->
                        <div class="flex items-center gap-4 mt-6 pt-6 border-t border-red-900/30">
                            <a href="{{ route('profile.show', $video->user) }}" class="w-12 h-12 bg-gradient-to-br from-red-900 to-red-950 rounded-full flex items-center justify-center text-white font-bold text-lg hover:ring-2 hover:ring-red-500 transition-all">
                                @if($video->user->avatar)
                                <img src="{{ asset('storage/' . $video->user->avatar) }}" alt="{{ $video->user->name }}" class="w-full h-full rounded-full object-cover">
                                @else
                                {{ substr($video->user->name, 0, 1) }}
                                @endif
                            </a>
                            <div>
                                <a href="{{ route('profile.show', $video->user) }}" class="font-semibold text-white hover:text-red-400 flex items-center gap-1">
                                    {{ $video->user->name }}
                                    @if($video->user->is_admin)
                                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    @endif
                                </a>
                                <p class="text-sm text-gray-500">Posted {{ $video->created_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mt-6 pt-6 border-t border-red-900/30">
                            <h3 class="font-semibold text-white mb-2">Description</h3>
                            <p class="text-gray-300 whitespace-pre-line">{{ $video->description }}</p>
                        </div>

                        @if($video->status !== 'approved' && Auth::user()?->is_admin)
                        <div class="mt-6 pt-6 border-t border-red-900/30">
                            <div class="bg-yellow-900/20 border border-yellow-700 rounded-lg p-4">
                                <p class="text-yellow-400 font-semibold">Admin Note: This video is {{ $video->status }}</p>
                                @if($video->rejection_reason)
                                <p class="text-yellow-300 text-sm mt-2">Reason: {{ $video->rejection_reason }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>

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
