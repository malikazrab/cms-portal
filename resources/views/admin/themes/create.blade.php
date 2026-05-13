@extends('layouts.admin')

@section('title', 'Install Theme')

@section('content')
<div class="p-6 max-w-2xl">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Install New Theme</h1>
        <a href="{{ route('admin.themes.index') }}" class="text-blue-600 hover:underline text-sm">← Back to Themes</a>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <form action="{{ route('admin.themes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Theme ZIP File</label>
                
                {{-- Drag & Drop Zone --}}
                <div x-data="{ dragging: false, fileName: '' }"
                     @dragover.prevent="dragging = true"
                     @dragleave.prevent="dragging = false"
                     @drop.prevent="dragging = false; handleDrop($event)"
                     :class="dragging ? 'border-blue-500 bg-blue-50' : 'border-gray-300'"
                     class="border-2 border-dashed rounded-lg p-8 text-center transition cursor-pointer">
                    
                    <input type="file" name="theme_zip" accept=".zip" required
                           @change="fileName = $event.target.files[0]?.name || ''"
                           class="hidden" id="themeFileInput">
                    
                    <label for="themeFileInput" class="cursor-pointer">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3 block"></i>
                        <p class="text-gray-600 font-medium mb-1">Drag & Drop your theme ZIP file here</p>
                        <p class="text-sm text-gray-400 mb-2">or click to browse</p>
                        <p class="text-xs text-gray-400">Only .zip files accepted (max 50MB)</p>
                        
                        <template x-if="fileName">
                            <div class="mt-3 bg-blue-50 text-blue-700 px-3 py-2 rounded-lg inline-block">
                                <i class="fas fa-file-archive mr-2"></i>
                                <span x-text="fileName" class="font-medium"></span>
                            </div>
                        </template>
                    </label>
                </div>
            </div>

            {{-- Theme Format Info --}}
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="font-medium text-gray-700 mb-2">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i> Supported Theme Formats
                </h3>
                <ul class="text-sm text-gray-600 space-y-1 ml-6">
                    <li>• HTML templates (index.html, style.css)</li>
                    <li>• Bootstrap / Tailwind themes</li>
                    <li>• WordPress themes (style.css with metadata)</li>
                    <li>• Any ZIP with HTML/CSS/JS files</li>
                </ul>
                <p class="text-xs text-gray-500 mt-2">
                    Your theme will be automatically converted to our standard format.
                </p>
            </div>

            <button type="submit" 
                    class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium text-sm">
                <i class="fas fa-upload mr-2"></i> Install Theme
            </button>
        </form>
    </div>
</div>
@endsection