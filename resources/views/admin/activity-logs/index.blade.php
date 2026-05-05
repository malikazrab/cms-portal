@extends('layouts.admin')

@section('title', 'Activity Logs')

@section('content')
<div class="rounded bg-white p-6 shadow-sm">
    <div class="mb-4 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Activity Logs</h1>
            <p class="mt-2 text-sm text-gray-600">Every tracked user action is saved to the database and `storage/logs/activity-*.log`.</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <input type="text" name="action" value="{{ request('action') }}" placeholder="Filter by action" class="rounded border-gray-300 text-sm">
            <button type="submit" class="rounded bg-blue-600 px-3 py-2 text-xs font-medium text-white hover:bg-blue-700">Filter</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left font-medium text-gray-600">Time</th>
                    <th class="px-3 py-2 text-left font-medium text-gray-600">User</th>
                    <th class="px-3 py-2 text-left font-medium text-gray-600">Action</th>
                    <th class="px-3 py-2 text-left font-medium text-gray-600">Description</th>
                    <th class="px-3 py-2 text-left font-medium text-gray-600">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($logs as $log)
                    <tr>
                        <td class="px-3 py-3 text-gray-600">{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
                        <td class="px-3 py-3 text-gray-900">{{ $log->user?->email ?? 'Guest/System' }}</td>
                        <td class="px-3 py-3"><code class="text-xs">{{ $log->action }}</code></td>
                        <td class="px-3 py-3 text-gray-700">{{ $log->description }}</td>
                        <td class="px-3 py-3 text-gray-600">{{ $log->ip_address ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-8 text-center text-gray-500">No activity logs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection
