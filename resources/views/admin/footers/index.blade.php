@extends('layouts.admin')

@section('title', 'Footers')

@section('content')
<div class="space-y-6">
    <div class="rounded bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Footers</h1>
                <p class="mt-2 text-gray-600">Manage reusable footer templates for your website.</p>
            </div>
            <a href="{{ route('admin.footers.create') }}"
               class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Create New Footer
            </a>
        </div>
    </div>

    <div class="overflow-hidden rounded bg-white shadow-sm">
        <div class="divide-y divide-gray-200">
            @forelse ($templates as $template)
                <div class="p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="text-lg font-medium text-gray-900">{{ $template->name }}</h3>
                                @if ($template->is_default)
                                    <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">
                                        Default
                                    </span>
                                @endif
                            </div>
                            <div class="mt-1 flex items-center gap-4 text-sm text-gray-500">
                                <span>Type: Footer</span>
                                <span>Created: {{ $template->created_at->format('M d, Y') }}</span>
                                <span>Updated: {{ $template->updated_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <form action="{{ route('admin.footers.set-default', $template) }}" method="POST" class="inline-flex items-center gap-2">
                                @csrf
                                <label class="inline-flex items-center gap-2 rounded px-3 py-2 text-sm text-gray-600 hover:bg-gray-100">
                                    <input type="checkbox" name="is_default" value="1" onchange="this.form.submit()" @checked($template->is_default) {{ $template->is_default ? 'disabled' : '' }}>
                                    <span>{{ $template->is_default ? 'Default' : 'Make Default' }}</span>
                                </label>
                            </form>
                            <a href="{{ route('admin.footers.edit', $template) }}" class="rounded px-3 py-2 text-sm text-blue-600 hover:bg-blue-50">
                                Edit
                            </a>
                            <form action="{{ route('admin.footers.destroy', $template) }}" method="POST" class="inline" onsubmit="return confirm('Delete this footer template?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded px-3 py-2 text-sm text-red-600 hover:bg-red-50">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <p class="text-gray-500">No footers created yet.</p>
                    <a href="{{ route('admin.footers.create') }}" class="mt-3 inline-block text-sm text-blue-600 hover:text-blue-700">
                        Create your first footer →
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
