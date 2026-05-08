@extends('layouts.admin')

@section('title', 'Active Sessions')

@section('content')
<div class="rounded bg-white p-6 shadow-sm">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Active Sessions</h1>
        @if (count($enrichedSessions) > 1)
            <form action="{{ route('admin.sessions.destroy-all') }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700 transition" onclick="return confirm('This will log out all other users. Continue?')">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout All Others
                </button>
            </form>
        @endif
    </div>

    @if (session('success'))
        <div class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif

    @if (count($enrichedSessions) > 0)
        <div class="space-y-4">
            @foreach ($enrichedSessions as $session)
                <div class="rounded border border-gray-200 p-4">
                    <div class="grid gap-4 md:grid-cols-4">
                        <!-- User Info -->
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase">User</p>
                            <p class="mt-1 font-medium text-gray-900">
                                {{ $session->user?->name ?? 'Unknown' }}
                                @if ($session->is_current)
                                    <span class="ml-2 inline-block rounded bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-600">Current</span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-500">{{ $session->user?->email ?? 'N/A' }}</p>
                        </div>

                        <!-- Device & Browser -->
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase">Device</p>
                            <p class="mt-1 font-medium text-gray-900">
                                @if ($session->device === 'Mobile')
                                    <i class="fas fa-mobile-alt mr-2 text-blue-600"></i>
                                @elseif ($session->device === 'Tablet')
                                    <i class="fas fa-tablet-alt mr-2 text-green-600"></i>
                                @else
                                    <i class="fas fa-desktop mr-2 text-gray-600"></i>
                                @endif
                                {{ $session->device }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $session->browser }}</p>
                        </div>

                        <!-- IP Address & Activity -->
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase">IP Address</p>
                            <p class="mt-1 font-mono text-sm font-medium text-gray-900">{{ $session->ip_address }}</p>
                            <p class="text-xs text-gray-500">Last activity: <span class="font-semibold">{{ $session->last_activity_ago }}</span></p>
                        </div>

                        <!-- Action -->
                        <div class="flex items-end">
                            @if (!$session->is_current)
                                <form action="{{ route('admin.sessions.destroy', $session->id) }}" method="POST" style="display: inline; width: 100%;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full rounded bg-red-100 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-200 transition">
                                        <i class="fas fa-times mr-1"></i> End Session
                                    </button>
                                </form>
                            @else
                                <div class="w-full rounded bg-gray-100 px-4 py-2 text-center text-sm text-gray-600">
                                    This session
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 rounded border border-blue-200 bg-blue-50 p-4">
            <p class="text-sm text-blue-900">
                <i class="fas fa-info-circle mr-2"></i>
                Total active sessions: <strong>{{ count($enrichedSessions) }}</strong>
            </p>
        </div>
    @else
        <div class="rounded border border-gray-200 bg-gray-50 p-12 text-center">
            <i class="fas fa-wifi text-4xl text-gray-400"></i>
            <p class="mt-4 text-gray-600">No active sessions found.</p>
        </div>
    @endif

    <!-- Information Section -->
    <div class="mt-8 rounded border border-gray-200 p-6">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Session Management Info</h3>
        <ul class="space-y-2 text-sm text-gray-600">
            <li><i class="fas fa-check text-green-600 mr-2"></i> <strong>Current Session:</strong> This is your current login session</li>
            <li><i class="fas fa-check text-blue-600 mr-2"></i> <strong>Last Activity:</strong> Shows when the session was last active</li>
            <li><i class="fas fa-check text-purple-600 mr-2"></i> <strong>End Session:</strong> Forcefully log out a user session</li>
            <li><i class="fas fa-check text-red-600 mr-2"></i> <strong>Device Info:</strong> Browser and device type used for login</li>
        </ul>
    </div>
</div>
@endsection
