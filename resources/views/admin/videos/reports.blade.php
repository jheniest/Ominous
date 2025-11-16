<x-app-layout>
    <div class="min-h-screen bg-black text-gray-100 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-red-500 mb-2">Video Reports</h1>
                <p class="text-gray-400">Review user-submitted video reports</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gray-900 border border-yellow-700 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm">Pending</p>
                            <p class="text-3xl font-bold text-yellow-400">{{ $stats['pending'] }}</p>
                        </div>
                        <svg class="w-12 h-12 text-yellow-700" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div class="bg-gray-900 border border-green-700 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm">Reviewed</p>
                            <p class="text-3xl font-bold text-green-400">{{ $stats['reviewed'] }}</p>
                        </div>
                        <svg class="w-12 h-12 text-green-700" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div class="bg-gray-900 border border-gray-600 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm">Dismissed</p>
                            <p class="text-3xl font-bold text-gray-400">{{ $stats['dismissed'] }}</p>
                        </div>
                        <svg class="w-12 h-12 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-gray-900 rounded-lg border border-red-900/30 p-4 mb-6">
                <form method="GET" action="{{ route('admin.reports.index') }}" class="flex gap-4">
                    <select name="status" class="bg-black border border-red-900/50 rounded-lg px-4 py-2 text-gray-100 focus:outline-none focus:border-red-500">
                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="reviewed" {{ $status == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                        <option value="dismissed" {{ $status == 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                    </select>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                        Filter
                    </button>
                </form>
            </div>

            <!-- Reports List -->
            @if($reports->count() > 0)
            <div class="space-y-4">
                @foreach($reports as $report)
                <div class="bg-gray-900 rounded-lg border border-red-900/30 p-6">
                    <div class="flex gap-6">
                        <!-- Video Thumbnail -->
                        <div class="flex-shrink-0">
                            <a href="{{ route('videos.show', $report->video) }}" target="_blank">
                                @if($report->video->thumbnail_url)
                                <img src="{{ $report->video->thumbnail_url }}" alt="{{ $report->video->title }}" class="w-40 h-28 object-cover rounded-lg border border-red-900/50">
                                @else
                                <div class="w-40 h-28 bg-gradient-to-br from-gray-800 to-black rounded-lg flex items-center justify-center border border-red-900/50">
                                    <svg class="w-10 h-10 text-red-900" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                    </svg>
                                </div>
                                @endif
                            </a>
                        </div>

                        <!-- Report Info -->
                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <a href="{{ route('videos.show', $report->video) }}" target="_blank" class="text-lg font-bold text-white hover:text-red-400 transition-colors">
                                        {{ $report->video->title }}
                                    </a>
                                    <p class="text-sm text-gray-400 mt-1">
                                        Reported by {{ $report->user->name }} • {{ $report->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                
                                <!-- Status Badge -->
                                @if($report->status === 'pending')
                                <span class="px-3 py-1 bg-yellow-900/30 text-yellow-400 text-xs font-semibold rounded border border-yellow-700">PENDING</span>
                                @elseif($report->status === 'reviewed')
                                <span class="px-3 py-1 bg-green-900/30 text-green-400 text-xs font-semibold rounded border border-green-700">REVIEWED</span>
                                @else
                                <span class="px-3 py-1 bg-gray-700 text-gray-300 text-xs font-semibold rounded border border-gray-600">DISMISSED</span>
                                @endif
                            </div>

                            <!-- Reason Badge -->
                            <div class="mb-3">
                                @php
                                    $reasonColors = [
                                        'inappropriate' => 'bg-red-900/30 text-red-400 border-red-700',
                                        'fake' => 'bg-orange-900/30 text-orange-400 border-orange-700',
                                        'spam' => 'bg-yellow-900/30 text-yellow-400 border-yellow-700',
                                        'violence' => 'bg-purple-900/30 text-purple-400 border-purple-700',
                                        'copyright' => 'bg-blue-900/30 text-blue-400 border-blue-700',
                                        'other' => 'bg-gray-700 text-gray-300 border-gray-600',
                                    ];
                                    $colorClass = $reasonColors[$report->reason] ?? 'bg-gray-700 text-gray-300 border-gray-600';
                                @endphp
                                <span class="inline-block px-3 py-1 {{ $colorClass }} text-xs font-semibold rounded border">
                                    {{ ucfirst(str_replace('_', ' ', $report->reason)) }}
                                </span>
                            </div>

                            @if($report->description)
                            <div class="bg-black/50 border border-red-900/30 rounded-lg p-3 mb-3">
                                <p class="text-gray-300 text-sm">{{ $report->description }}</p>
                            </div>
                            @endif

                            @if($report->reviewed_at)
                            <div class="bg-green-900/20 border border-green-700 rounded p-2 mb-3">
                                <p class="text-green-300 text-sm">
                                    <strong>Reviewed by {{ $report->reviewedBy->name }}</strong> on {{ $report->reviewed_at->format('M d, Y H:i') }}
                                </p>
                            </div>
                            @endif

                            <!-- Actions -->
                            @if($report->status === 'pending')
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('admin.reports.review', $report) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="reviewed">
                                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg font-semibold transition-colors">
                                        Mark as Reviewed
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.reports.review', $report) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="dismissed">
                                    <button type="submit" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm rounded-lg font-semibold transition-colors">
                                        Dismiss
                                    </button>
                                </form>

                                <a href="{{ route('videos.show', $report->video) }}" target="_blank" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg font-semibold transition-colors">
                                    View Video
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $reports->links() }}
            </div>
            @else
            <div class="bg-gray-900 rounded-lg border border-red-900/30 p-12 text-center">
                <p class="text-gray-400">No reports found with the selected filter.</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
