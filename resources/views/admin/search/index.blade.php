@extends('admin.layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Search Query Logs</h1>
                <p class="text-sm text-gray-500 mt-1">Monitor and analyze user search queries</p>
            </div>
            <div class="flex items-center space-x-2">
                <i class="fas fa-chart-line text-blue-500"></i>
                <span class="text-sm font-medium text-gray-700">{{ $logs->total() }} total searches</span>
            </div>
        </div>
    </div>

    <!-- Search Form -->
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <form method="GET" class="flex items-center space-x-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" 
                       name="q" 
                       value="{{ $query ?? '' }}" 
                       placeholder="Filter by search query..." 
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                <i class="fas fa-filter mr-2"></i>
                Filter
            </button>
            @if($query)
                <a href="{{ route('admin.search.logs') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Search Query</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($logs as $log)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500">
                            #{{ $log->id }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 max-w-xs truncate" title="{{ $log->query }}">
                                {{ $log->query }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($log->user_id)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-user mr-1"></i>
                                    User #{{ $log->user_id }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-user-secret mr-1"></i>
                                    Guest
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="flex items-center">
                                <i class="fas fa-clock mr-2 text-gray-400"></i>
                                {{ $log->created_at->format('M j, Y') }}
                                <span class="text-gray-400 ml-1">{{ $log->created_at->format('g:i A') }}</span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-search text-gray-300 text-4xl mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No search logs found</h3>
                                <p class="text-gray-500">Start searching to see query logs appear here.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $logs->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
