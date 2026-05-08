@extends('layouts.admin')

@section('content')
<div x-data="versionHistory({{ $page->id }})" class="p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-gray-900">Version History: {{ $page->title }}</h2>
        <a href="{{ route('admin.pages.edit', $page->id) }}" class="text-blue-600 hover:underline">
            &larr; Back to Editor
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-left text-sm font-medium text-gray-500">Version #</th>
                    <th class="p-3 text-left text-sm font-medium text-gray-500">Saved By</th>
                    <th class="p-3 text-left text-sm font-medium text-gray-500">Date</th>
                    <th class="p-3 text-left text-sm font-medium text-gray-500">Change Note</th>
                    <th class="p-3 text-left text-sm font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <template x-for="version in versions" :key="version.id">
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 text-sm" x-text="'v' + version.version_number"></td>
                        <td class="p-3 text-sm" x-text="version.saved_by?.name || 'Unknown'"></td>
                        <td class="p-3 text-sm" x-text="new Date(version.created_at).toLocaleString()"></td>
                        <td class="p-3 text-sm text-gray-500" x-text="version.change_note || '—'"></td>
                        <td class="p-3 text-sm space-x-2">
                            <button @click="previewVersion(version.id)" 
                                    class="text-blue-600 hover:underline">Preview</button>
                            <button @click="confirmRestore(version.id, version.version_number)" 
                                    class="text-green-600 hover:underline">Restore</button>
                        </td>
                    </tr>
                </template>
                <tr x-show="versions.length === 0">
                    <td colspan="5" class="p-6 text-center text-gray-500">No versions saved yet.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Restore Confirmation Modal -->
    <div x-show="showRestoreModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <h3 class="font-bold text-lg mb-2">Confirm Restore</h3>
            <p class="text-gray-600 mb-4">
                Are you sure you want to restore <strong x-text="'Version ' + versionToRestoreNumber"></strong>? 
                A new version will be saved before restoring.
            </p>
            <div class="flex justify-end space-x-2">
                <button @click="showRestoreModal = false" 
                        class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                <button @click="restoreVersion()" 
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Confirm Restore</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('versionHistory', (pageId) => ({
            versions: [],
            showRestoreModal: false,
            versionToRestore: null,
            versionToRestoreNumber: null,
            
            async init() {
                await this.loadVersions();
            },
            
            async loadVersions() {
                try {
                    const response = await fetch(`/admin/pages/${pageId}/versions`);
                    if (!response.ok) throw new Error('Failed to load versions');
                    const data = await response.json();
                    this.versions = data.versions || data.data || [];
                } catch (error) {
                    console.error('Error loading versions:', error);
                }
            },
            
            previewVersion(versionId) {
                window.open(`/admin/pages/${pageId}/versions/${versionId}`, '_blank');
            },
            
            confirmRestore(versionId, versionNumber) {
                this.versionToRestore = versionId;
                this.versionToRestoreNumber = versionNumber;
                this.showRestoreModal = true;
            },
            
            async restoreVersion() {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                try {
                    const response = await fetch(`/admin/pages/${pageId}/versions/${this.versionToRestore}/restore`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        alert('Version restored successfully!');
                        window.location.href = `/admin/pages/${pageId}/edit`;
                    } else {
                        alert('Restore failed: ' + (data.message || 'Unknown error'));
                    }
                } catch (error) {
                    alert('Restore failed: ' + error.message);
                }
                this.showRestoreModal = false;
            }
        }));
    });
</script>
@endsection