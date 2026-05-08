@extends('layouts.admin')

@section('title', 'Backup & Restore')

@section('content')
<div class="rounded bg-white p-6 shadow-sm">
    <h1 class="mb-6 text-2xl font-semibold text-gray-900">Backup & Restore</h1>

    <div class="grid gap-6 md:grid-cols-2">
        <!-- Create Backup Section -->
        <div class="rounded border border-gray-200 bg-gray-50 p-6">
            <div class="mb-4 flex items-center gap-3">
                <i class="fas fa-download text-2xl text-blue-600"></i>
                <h2 class="text-lg font-semibold text-gray-900">Create Backup</h2>
            </div>
            <p class="mb-4 text-sm text-gray-600">
                Download a complete backup of your database. This includes all posts, pages, menus, and settings.
            </p>
            <form action="{{ route('admin.backup.create') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="w-full rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 transition">
                    <i class="fas fa-download mr-2"></i> Download Backup
                </button>
            </form>
            <p class="mt-3 text-xs text-gray-500">
                ℹ️ Backups are automatically downloaded to your computer.
            </p>
        </div>

        <!-- Restore Backup Section -->
        <div class="rounded border border-gray-200 bg-gray-50 p-6">
            <div class="mb-4 flex items-center gap-3">
                <i class="fas fa-upload text-2xl text-green-600"></i>
                <h2 class="text-lg font-semibold text-gray-900">Restore Backup</h2>
            </div>
            <p class="mb-4 text-sm text-gray-600">
                Restore your database from a previously downloaded backup file. This will replace all current data.
            </p>
            <form action="{{ route('admin.backup.restore') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <input 
                        type="file" 
                        name="backup_file" 
                        accept=".zip" 
                        required
                        class="block w-full text-sm text-gray-500
                            file:mr-4 file:rounded file:border-0
                            file:bg-green-600 file:px-4 file:py-2
                            file:text-sm file:font-semibold
                            file:text-white hover:file:bg-green-700"
                    >
                    <p class="mt-2 text-xs text-gray-500">
                        📁 Only .zip backup files are accepted (max 50MB)
                    </p>
                </div>
                <button type="submit" class="w-full rounded bg-green-600 px-4 py-2 text-white hover:bg-green-700 transition" onclick="return confirm('Are you sure? This will replace all current data.')">
                    <i class="fas fa-upload mr-2"></i> Restore from Backup
                </button>
            </form>
        </div>
    </div>

    <!-- Important Notes Section -->
    <div class="mt-8 rounded border border-yellow-200 bg-yellow-50 p-4">
        <h3 class="mb-2 flex items-center gap-2 font-semibold text-yellow-900">
            <i class="fas fa-exclamation-triangle"></i> Important Notes
        </h3>
        <ul class="space-y-2 text-sm text-yellow-800">
            <li>✓ Always create backups before making major changes.</li>
            <li>✓ Restoring a backup will replace all current data. This action cannot be undone.</li>
            <li>✓ Backups are encrypted and contain your database structure and content.</li>
            <li>✓ Store backup files in a safe location, preferably on external storage or cloud.</li>
            <li>✓ Regularly schedule automatic backups as part of your maintenance routine.</li>
        </ul>
    </div>

    <!-- Backup History Section (Optional - for future implementation) -->
    <div class="mt-8 rounded border border-gray-200 p-6">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Backup Information</h3>
        <div class="grid gap-4 md:grid-cols-3 text-center">
            <div class="rounded bg-blue-50 p-4">
                <p class="text-xs text-gray-600 uppercase tracking-wide">Database Size</p>
                <p class="mt-2 text-2xl font-bold text-blue-600">
                    @php
                        $size = \Illuminate\Support\Facades\DB::select("SELECT SUM(data_length + index_length) as size FROM information_schema.tables WHERE table_schema = '" . env('DB_DATABASE') . "'");
                        $bytes = $size[0]->size ?? 0;
                        echo number_format($bytes / 1024 / 1024, 2) . ' MB';
                    @endphp
                </p>
            </div>
            <div class="rounded bg-green-50 p-4">
                <p class="text-xs text-gray-600 uppercase tracking-wide">Total Posts</p>
                <p class="mt-2 text-2xl font-bold text-green-600">
                    {{ \App\Models\Post::count() }}
                </p>
            </div>
            <div class="rounded bg-purple-50 p-4">
                <p class="text-xs text-gray-600 uppercase tracking-wide">Total Pages</p>
                <p class="mt-2 text-2xl font-bold text-purple-600">
                    {{ \App\Models\Page::count() }}
                </p>
            </div>
        </div>
    </div>
</div>

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('{{ $errors->first() }}', 'error');
        });
    </script>
@endif
@endsection
