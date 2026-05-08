@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="rounded bg-white p-6 shadow-sm">
    <h1 class="mb-4 text-2xl font-semibold text-gray-900">Settings</h1>

    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700" for="site_name">Site Name</label>
            <input id="site_name" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? config('app.name')) }}" class="w-full rounded border-gray-300" required>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700" for="site_description">Site Description</label>
            <textarea id="site_description" name="site_description" rows="3" class="w-full rounded border-gray-300">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700" for="posts_per_page">Posts Per Page</label>
                <input id="posts_per_page" name="posts_per_page" type="number" min="1" max="100" value="{{ old('posts_per_page', $settings['posts_per_page'] ?? 10) }}" class="w-full rounded border-gray-300">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700" for="admin_email">Admin Email</label>
                <input id="admin_email" name="admin_email" type="email" value="{{ old('admin_email', $settings['admin_email'] ?? '') }}" class="w-full rounded border-gray-300">
            </div>
        </div>

        <div class="grid gap-4 border-t pt-4 md:grid-cols-3">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700" for="home_page_id">Home Page</label>
                <select id="home_page_id" name="home_page_id" class="w-full rounded border-gray-300">
                    <option value="">Use first published page</option>
                    @foreach ($pages as $page)
                        <option value="{{ $page->id }}" @selected(old('home_page_id', $settings['home_page_id'] ?? '') == $page->id)>
                            {{ $page->title }} ({{ ucfirst($page->status) }})
                        </option>
                    @endforeach
                </select>
                <a href="{{ route('admin.settings.sections.edit', 'home') }}" class="mt-2 inline-block text-sm text-blue-600 hover:text-blue-700">Create or edit home page</a>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700" for="header_page_id">Header Content</label>
                <select id="header_page_id" name="header_page_id" class="w-full rounded border-gray-300">
                    <option value="">No extra header content</option>
                    @foreach ($pages as $page)
                        <option value="{{ $page->id }}" @selected(old('header_page_id', $settings['header_page_id'] ?? '') == $page->id)>
                            {{ $page->title }} ({{ ucfirst($page->status) }})
                        </option>
                    @endforeach
                </select>
                <a href="{{ route('admin.settings.sections.edit', 'header') }}" class="mt-2 inline-block text-sm text-blue-600 hover:text-blue-700">Create or edit header</a>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700" for="footer_page_id">Footer Content</label>
                <select id="footer_page_id" name="footer_page_id" class="w-full rounded border-gray-300">
                    <option value="">No extra footer content</option>
                    @foreach ($pages as $page)
                        <option value="{{ $page->id }}" @selected(old('footer_page_id', $settings['footer_page_id'] ?? '') == $page->id)>
                            {{ $page->title }} ({{ ucfirst($page->status) }})
                        </option>
                    @endforeach
                </select>
                <a href="{{ route('admin.settings.sections.edit', 'footer') }}" class="mt-2 inline-block text-sm text-blue-600 hover:text-blue-700">Create or edit footer</a>
            </div>
        </div>

        <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Save Settings</button>
    </form>
        <!-- Backup & Restore Section -->
    <div class="mt-8 border-t pt-6" x-data="backupRestore()">
        <h2 class="mb-4 text-xl font-semibold text-gray-900">Backup & Restore</h2>

        <!-- Download Backup -->
        <div class="mb-6 rounded border bg-gray-50 p-4">
            <h3 class="font-medium text-gray-900">Download Backup</h3>
            <p class="mb-2 text-sm text-gray-600">Last backup: <span x-text="lastBackupDate || 'Never'"></span></p>
            <button @click="downloadBackup()" 
                    class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Download Backup
            </button>
        </div>

        <!-- Upload & Restore -->
        <div class="rounded border bg-gray-50 p-4">
            <h3 class="font-medium text-gray-900">Upload & Restore</h3>
            <div 
                @dragover.prevent 
                @drop.prevent="handleFileDrop($event)"
                class="mt-2 cursor-pointer rounded border-2 border-dashed border-gray-300 p-6 text-center hover:border-blue-400">
                <p class="text-sm text-gray-600">Drag & Drop a .zip backup file here</p>
                <p class="text-xs text-gray-400">or</p>
                <label class="cursor-pointer text-sm text-blue-600 hover:underline">
                    Browse Files
                    <input type="file" accept=".zip" @change="handleFileSelect($event)" class="hidden">
                </label>
            </div>
            <div x-show="selectedFileName" class="mt-2 text-sm">
                <span class="font-medium">Selected: </span><span x-text="selectedFileName"></span>
            </div>
            <button @click="uploadAndRestore()" 
                    x-bind:disabled="!selectedFile || uploading"
                    class="mt-3 rounded bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
                <span x-show="!uploading">Restore Backup</span>
                <span x-show="uploading">Uploading...</span>
            </button>
        </div>

        <!-- Loading Overlay -->
        <div x-show="uploading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 text-center">
                <div class="animate-spin h-8 w-8 border-4 border-blue-600 border-t-transparent rounded-full mx-auto mb-4"></div>
                <p class="font-bold">Restoring backup...</p>
                <p class="text-sm text-gray-600">Please do not close this page.</p>
            </div>
        </div>

        <!-- Confirm Restore Modal -->
        <div x-show="showConfirmModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full">
                <h3 class="font-bold text-lg mb-2 text-red-600">⚠️ Confirm Restore</h3>
                <p class="text-gray-600 mb-4">This will overwrite all current data. This action cannot be undone.</p>
                <h4 class="font-medium mb-2">What will be restored:</h4>
                <ul class="list-disc pl-5 mb-4 text-sm text-gray-600">
                    <li>Database tables and data</li>
                    <li>Uploaded files and media</li>
                    <li>Site settings</li>
                </ul>
                <div class="flex justify-end space-x-2">
                    <button @click="showConfirmModal = false" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                    <button @click="confirmRestore()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Confirm Restore</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('backupRestore', () => ({
            lastBackupDate: null,
            selectedFile: null,
            selectedFileName: '',
            uploading: false,
            showConfirmModal: false,

            async init() {
                await this.loadBackupStatus();
            },

            async loadBackupStatus() {
                try {
                    const response = await fetch('/admin/backup');
                    if (response.ok) {
                        const data = await response.json();
                        this.lastBackupDate = data.last_backup_at || null;
                    }
                } catch (e) {
                    console.error('Failed to load backup status', e);
                }
            },

            downloadBackup() {
                window.location.href = '/admin/backup/create';
            },

            handleFileDrop(event) {
                const file = event.dataTransfer.files[0];
                this.setFile(file);
            },

            handleFileSelect(event) {
                const file = event.target.files[0];
                this.setFile(file);
            },

            setFile(file) {
                if (file && file.name.endsWith('.zip')) {
                    this.selectedFile = file;
                    this.selectedFileName = file.name;
                } else {
                    alert('Please select a .zip file');
                }
            },

            uploadAndRestore() {
                if (!this.selectedFile) {
                    alert('Please select a backup file first');
                    return;
                }
                this.showConfirmModal = true;
            },

            async confirmRestore() {
                this.showConfirmModal = false;
                this.uploading = true;

                const formData = new FormData();
                formData.append('backup_file', this.selectedFile);
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                try {
                    const response = await fetch('/admin/backup/restore', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: formData
                    });
                    const data = await response.json();
                    if (data.success) {
                        alert('Backup restored successfully!');
                        window.location.reload();
                    } else {
                        alert('Restore failed: ' + (data.message || 'Unknown error'));
                    }
                } catch (e) {
                    alert('Restore failed: ' + e.message);
                }
                this.uploading = false;
            }
        }));
    });
</script>
@endsection
