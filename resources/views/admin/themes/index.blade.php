@extends('layouts.admin')

@section('title', 'Themes')

@section('content')
<div class="p-6" x-data="themeManager()">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Themes</h1>
        <a href="{{ route('admin.themes.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium">
            <i class="fas fa-plus mr-1"></i> Install New Theme
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    {{-- Active Theme Banner --}}
    @php $activeTheme = \App\Models\Theme::getActive(); @endphp
    @if($activeTheme)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 flex items-center justify-between">
            <div>
                <span class="text-sm font-medium text-blue-700">Currently Active:</span>
                <span class="text-lg font-bold text-blue-800 ml-2">{{ $activeTheme->name }}</span>
                <span class="text-xs text-blue-500 ml-2">v{{ $activeTheme->version }}</span>
            </div>
            @if(!$activeTheme->is_builtin || $activeTheme->slug !== 'default')
            <a href="{{ route('admin.themes.customize', $activeTheme->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">
                <i class="fas fa-paint-brush mr-1"></i> Customize
            </a>
            @endif
        </div>
    @endif

    {{-- Themes Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($themes as $theme)
            <div class="bg-white rounded-lg border {{ $theme->is_active ? 'border-blue-400 ring-2 ring-blue-100' : 'border-gray-200' }} overflow-hidden hover:shadow-lg transition">
                
                {{-- Screenshot --}}
                <div class="h-48 bg-gray-100 flex items-center justify-center overflow-hidden relative">
                    @if($theme->screenshot && file_exists(public_path($theme->screenshot)))
                        <img src="{{ asset($theme->screenshot) }}" alt="{{ $theme->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="text-gray-400 text-center">
                            <i class="fas fa-image text-5xl block mb-2"></i>
                            <span class="text-sm">No Preview</span>
                        </div>
                    @endif
                    
                    {{-- Active Badge --}}
                    @if($theme->is_active)
                        <span class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                            ✓ Active
                        </span>
                    @endif
                </div>

                {{-- Theme Info --}}
                <div class="p-4">
                    <h3 class="font-bold text-gray-900 mb-1">{{ $theme->name }}</h3>
                    <p class="text-xs text-gray-500 mb-2">v{{ $theme->version }} by {{ $theme->author }}</p>
                    <p class="text-sm text-gray-600 mb-4">{{ Str::limit($theme->description, 80) }}</p>

                    {{-- Action Buttons --}}
                    <div class="flex flex-wrap gap-2">
                        {{-- Preview Button --}}
                        <a href="{{ route('admin.themes.preview', $theme->id) }}" 
                           class="flex-1 text-center px-3 py-2 border border-gray-300 rounded text-sm hover:bg-gray-50">
                            <i class="fas fa-eye mr-1"></i> Preview
                        </a>

                        {{-- Customize Button --}}
                        <a href="{{ route('admin.themes.customize', $theme->id) }}" 
                           class="flex-1 text-center px-3 py-2 bg-purple-600 text-white rounded text-sm hover:bg-purple-700">
                            <i class="fas fa-paint-brush mr-1"></i> Customize
                        </a>

                        {{-- Activate Button --}}
                        @if(!$theme->is_active)
                            <button @click="activateTheme({{ $theme->id }})" 
                                    class="w-full mt-1 px-3 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 font-medium">
                                <i class="fas fa-check-circle mr-1"></i> Activate & Publish
                            </button>
                        @else
                            <span class="w-full mt-1 text-center text-green-600 text-sm font-medium py-2">
                                <i class="fas fa-check mr-1"></i> Currently Active
                            </span>
                        @endif

                        {{-- Delete (non-builtin only) --}}
                        @if(!$theme->is_builtin && !$theme->is_active)
                            <button @click="deleteTheme({{ $theme->id }}, '{{ $theme->name }}')" 
                                    class="w-full mt-1 px-3 py-2 border border-red-300 text-red-600 rounded text-sm hover:bg-red-50">
                                <i class="fas fa-trash mr-1"></i> Delete
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Alpine.js --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('themeManager', () => ({
                async activateTheme(id) {
                    if (!confirm('Activate this theme? It will be published on your site immediately.')) return;
                    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const res = await fetch(`/admin/themes/${id}/activate`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                },
                async deleteTheme(id, name) {
                    if (!confirm(`Delete "${name}"? This cannot be undone.`)) return;
                    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const res = await fetch(`/admin/themes/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrf }
                    });
                    const data = await res.json();
                    if (data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                }
            }));
        });
    </script>
</div>
@endsection