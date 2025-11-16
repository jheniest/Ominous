<x-app-layout>
    <div class="min-h-screen bg-black text-gray-100 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-red-500 mb-2">Video Moderation</h1>
                <p class="text-gray-400">Review and moderate user-submitted videos</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gray-900 border border-yellow-700 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm">Pending</p>
                            <p class="text-3xl font-bold text-yellow-400">{{ $stats['pending'] }}</p>
                        </div>
                        <svg class="w-12 h-12 text-yellow-700" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div class="bg-gray-900 border border-green-700 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm">Approved</p>
                            <p class="text-3xl font-bold text-green-400">{{ $stats['approved'] }}</p>
                        </div>
                        <svg class="w-12 h-12 text-green-700" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div class="bg-gray-900 border border-red-700 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm">Rejected</p>
                            <p class="text-3xl font-bold text-red-400">{{ $stats['rejected'] }}</p>
                        </div>
                        <svg class="w-12 h-12 text-red-700" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div class="bg-gray-900 border border-gray-600 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm">Hidden</p>
                            <p class="text-3xl font-bold text-gray-400">{{ $stats['hidden'] }}</p>
                        </div>
                        <svg class="w-12 h-12 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-gray-900 rounded-lg border border-red-900/30 p-4 mb-6">
                <form method="GET" action="{{ route('admin.videos.index') }}" class="flex gap-4">
                    <select name="status" class="bg-black border border-red-900/50 rounded-lg px-4 py-2 text-gray-100 focus:outline-none focus:border-red-500">
                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="hidden" {{ $status == 'hidden' ? 'selected' : '' }}>Hidden</option>
                    </select>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                        Filter
                    </button>
                </form>
            </div>

            <!-- Videos List -->
            @if($videos->count() > 0)
            <div class="space-y-4">
                @foreach($videos as $video)
                <div class="bg-gray-900 rounded-lg border border-red-900/30 p-6">
                    <div class="flex gap-6">
                        <!-- Thumbnail -->
                        <div class="flex-shrink-0">
                            <a href="{{ route('videos.show', $video) }}" target="_blank">
                                @if($video->thumbnail_url)
                                <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="w-48 h-32 object-cover rounded-lg border border-red-900/50">
                                @else
                                <div class="w-48 h-32 bg-gradient-to-br from-gray-800 to-black rounded-lg flex items-center justify-center border border-red-900/50">
                                    <svg class="w-12 h-12 text-red-900" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                    </svg>
                                </div>
                                @endif
                            </a>
                        </div>

                        <!-- Info -->
                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <a href="{{ route('videos.show', $video) }}" target="_blank" class="text-xl font-bold text-white hover:text-red-400 transition-colors">
                                        {{ $video->title }}
                                    </a>
                                    <p class="text-sm text-gray-400 mt-1">
                                        by {{ $video->user->name }} • {{ $video->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                
                                <!-- Status Badge -->
                                @if($video->status === 'pending')
                                <span class="px-3 py-1 bg-yellow-900/30 text-yellow-400 text-xs font-semibold rounded border border-yellow-700">PENDING</span>
                                @elseif($video->status === 'approved')
                                <span class="px-3 py-1 bg-green-900/30 text-green-400 text-xs font-semibold rounded border border-green-700">APPROVED</span>
                                @elseif($video->status === 'rejected')
                                <span class="px-3 py-1 bg-red-900/30 text-red-400 text-xs font-semibold rounded border border-red-700">REJECTED</span>
                                @else
                                <span class="px-3 py-1 bg-gray-700 text-gray-300 text-xs font-semibold rounded border border-gray-600">HIDDEN</span>
                                @endif
                            </div>

                            <p class="text-gray-300 text-sm mb-3 line-clamp-2">{{ $video->description }}</p>

                            <div class="flex items-center gap-4 text-xs text-gray-500 mb-4">
                                <span class="px-2 py-1 bg-red-900/30 text-red-400 rounded">{{ ucfirst(str_replace('_', ' ', $video->category)) }}</span>
                                <span>{{ $video->views_count }} views</span>
                                <span>{{ $video->comments_count }} comments</span>
                                <span>{{ $video->reports_count }} reports</span>
                                @if($video->is_nsfw)
                                <span class="px-2 py-1 bg-black text-red-500 rounded border border-red-500">NSFW</span>
                                @endif
                                @if($video->is_featured)
                                <span class="px-2 py-1 bg-red-600 text-white rounded">FEATURED</span>
                                @endif
                            </div>

                            @if($video->rejection_reason)
                            <div class="bg-red-900/20 border border-red-700 rounded p-2 mb-4">
                                <p class="text-red-300 text-sm"><strong>Rejection Reason:</strong> {{ $video->rejection_reason }}</p>
                            </div>
                            @endif

                            <!-- Actions -->
                            <div class="flex flex-wrap gap-2">
                                @if($video->status !== 'approved')
                                <form method="POST" action="{{ route('admin.videos.approve', $video) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg font-semibold transition-colors">
                                        Approve
                                    </button>
                                </form>
                                @endif

                                @if($video->status !== 'rejected')
                                <button onclick="showRejectModal({{ $video->id }}, '{{ $video->title }}')" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg font-semibold transition-colors">
                                    Reject
                                </button>
                                @endif

                                @if($video->status !== 'hidden')
                                <form method="POST" action="{{ route('admin.videos.hide', $video) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm rounded-lg font-semibold transition-colors">
                                        Hide
                                    </button>
                                </form>
                                @endif

                                <form method="POST" action="{{ route('admin.videos.toggle-featured', $video) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 {{ $video->is_featured ? 'bg-orange-600 hover:bg-orange-700' : 'bg-yellow-600 hover:bg-yellow-700' }} text-white text-sm rounded-lg font-semibold transition-colors">
                                        {{ $video->is_featured ? 'Unfeature' : 'Feature' }}
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.videos.destroy', $video) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this video permanently?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 bg-black hover:bg-red-950 text-red-400 text-sm rounded-lg font-semibold transition-colors border border-red-900">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $videos->links() }}
            </div>
            @else
            <div class="bg-gray-900 rounded-lg border border-red-900/30 p-12 text-center">
                <p class="text-gray-400">No videos found with the selected filter.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
        <div class="bg-gray-900 rounded-lg border-2 border-red-900 max-w-md w-full p-6">
            <h3 class="text-xl font-bold text-white mb-4">Reject Video</h3>
            <form id="rejectForm" method="POST">
                @csrf
                <p class="text-gray-300 mb-4">Video: <span id="rejectVideoTitle" class="font-semibold text-white"></span></p>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-300 mb-2">Rejection Reason *</label>
                    <textarea name="reason" rows="4" required class="w-full bg-black border border-red-900/50 rounded-lg px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-red-500" placeholder="Explain why this video is being rejected..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-semibold transition-colors">
                        Reject Video
                    </button>
                    <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-2 rounded-lg font-semibold transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showRejectModal(videoId, videoTitle) {
            document.getElementById('rejectVideoTitle').textContent = videoTitle;
            document.getElementById('rejectForm').action = '/admin/videos/' + videoId + '/reject';
            document.getElementById('rejectModal').classList.remove('hidden');
        }
    </script>
</x-app-layout>
