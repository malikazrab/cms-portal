@extends('layouts.admin')

@section('title', 'Preview Theme')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Preview: {{ $theme->name }}</h1>
        <div class="flex gap-2">
            @if(!$theme->is_active)
                <button onclick="activateTheme({{ $theme->id }})" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                    Activate This Theme
                </button>
            @endif
            <a href="{{ route('admin.themes.index') }}" class="border border-gray-300 px-4 py-2 rounded text-sm hover:bg-gray-50">
                Back to Themes
            </a>
        </div>
    </div>

    {{-- Theme Details --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="font-bold text-lg mb-4">Theme Screenshot</h2>
                <div class="bg-gray-100 rounded-lg h-64 flex items-center justify-center">
                    @if($theme->screenshot && file_exists(public_path($theme->screenshot)))
                        <img src="{{ asset($theme->screenshot) }}" alt="{{ $theme->name }}" class="max-h-64 rounded">
                    @else
                        <span class="text-gray-400">No screenshot available</span>
                    @endif
                </div>
            </div>
        </div>
        <div>
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="font-bold text-lg mb-4">Theme Info</h2>
                <ul class="space-y-3 text-sm">
                    <li><strong>Name:</strong> {{ $theme->name }}</li>
                    <li><strong>Version:</strong> {{ $theme->version }}</li>
                    <li><strong>Author:</strong> {{ $theme->author }}</li>
                    <li><strong>Description:</strong> {{ $theme->description }}</li>
                    <li><strong>Active:</strong> 
                        @if($theme->is_active)
                            <span class="text-green-600 font-medium">Yes</span>
                        @else
                            <span class="text-gray-500">No</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Templates List --}}
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h2 class="font-bold text-lg mb-4">Templates</h2>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @foreach(['header', 'footer', 'home', 'page', 'blog'] as $template)
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <i class="fas fa-file-code text-2xl text-gray-400 mb-2 block"></i>
                    <span class="text-sm font-medium">{{ ucfirst($template) }}</span>
                    @php
                        $templatePath = public_path(str_replace('/', '/templates/', $theme->theme_path) . '/' . $template . '.blade.php');
                    @endphp
                    @if(file_exists($templatePath))
                        <span class="text-xs text-green-600 block mt-1">✓ Available</span>
                    @else
                        <span class="text-xs text-red-400 block mt-1">✗ Missing</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Settings Preview --}}
    @if($themeSettings)
    <div class="bg-white rounded-lg border border-gray-200 p-6 mt-6">
        <h2 class="font-bold text-lg mb-4">Theme Settings</h2>
        <pre class="bg-gray-50 p-4 rounded text-sm overflow-auto">{{ json_encode($themeSettings, JSON_PRETTY_PRINT) }}</pre>
    </div>
    @endif

    <script>
        async function activateTheme(id) {
            if (!confirm('Activate this theme?')) return;
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const res = await fetch(`/admin/themes/${id}/activate`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' }
            });
            const data = await res.json();
            alert(data.message);
            if (data.success) window.location.reload();
        }
    </script>
</div>
@endsection