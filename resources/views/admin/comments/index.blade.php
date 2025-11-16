<x-app-layout>
    <div class="min-h-screen bg-black text-gray-100 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-red-500 mb-2">Comment Moderation</h1>
                <p class="text-gray-400">Manage and moderate user comments</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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
                <form method="GET" action="{{ route('admin.comments.index') }}" class="flex gap-4">
                    <select name="status" class="bg-black border border-red-900/50 rounded-lg px-4 py-2 text-gray-100 focus:outline-none focus:border-red-500">
                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="hidden" {{ $status == 'hidden' ? 'selected' : '' }}>Hidden</option>
                    </select>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                        Filter
                    </button>
                </form>
            </div>

            <!-- Comments List -->
            @if($comments->count() > 0)
            <div class="space-y-4">
                @foreach($comments as $comment)
                <div class="bg-gray-900 rounded-lg border border-red-900/30 p-6">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-start gap-4 flex-1">
                            <div class="w-10 h-10 bg-gradient-to-br from-red-900 to-red-950 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                                {{ substr($comment->user->name, 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-white">{{ $comment->user->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <a href="{{ route('videos.show', $comment->video) }}" target="_blank" class="text-sm text-gray-400 hover:text-red-400 transition-colors">
                                    on "{{ Str::limit($comment->video->title, 60) }}"
                                </a>
                            </div>
                        </div>
                        
                        <!-- Status Badge -->
                        @if($comment->status === 'approved')
                        <span class="px-3 py-1 bg-green-900/30 text-green-400 text-xs font-semibold rounded border border-green-700">APPROVED</span>
                        @elseif($comment->status === 'pending')
                        <span class="px-3 py-1 bg-yellow-900/30 text-yellow-400 text-xs font-semibold rounded border border-yellow-700">PENDING</span>
                        @else
                        <span class="px-3 py-1 bg-gray-700 text-gray-300 text-xs font-semibold rounded border border-gray-600">HIDDEN</span>
                        @endif
                    </div>

                    <div class="bg-black/50 border border-red-900/30 rounded-lg p-4 mb-4">
                        <p class="text-gray-300">{{ $comment->content }}</p>
                    </div>

                    @if($comment->parent_id)
                    <div class="bg-blue-900/20 border border-blue-700 rounded p-2 mb-4">
                        <p class="text-blue-300 text-xs">
                            <strong>Reply to:</strong> {{ Str::limit($comment->parent->content ?? '', 100) }}
                        </p>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="flex gap-2">
                        @if($comment->status !== 'approved')
                        <form method="POST" action="{{ route('admin.comments.approve', $comment) }}" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg font-semibold transition-colors">
                                Approve
                            </button>
                        </form>
                        @endif

                        @if($comment->status !== 'hidden')
                        <form method="POST" action="{{ route('admin.comments.hide', $comment) }}" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm rounded-lg font-semibold transition-colors">
                                Hide
                            </button>
                        </form>
                        @endif

                        <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this comment permanently?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-black hover:bg-red-950 text-red-400 text-sm rounded-lg font-semibold transition-colors border border-red-900">
                                Delete
                            </button>
                        </form>

                        <a href="{{ route('videos.show', $comment->video) }}" target="_blank" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg font-semibold transition-colors">
                            View Video
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $comments->links() }}
            </div>
            @else
            <div class="bg-gray-900 rounded-lg border border-red-900/30 p-12 text-center">
                <p class="text-gray-400">No comments found with the selected filter.</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
