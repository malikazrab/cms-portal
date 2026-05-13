@extends('layouts.admin')

@section('title', isset($footer) ? 'Edit Footer Template' : 'Create Footer Template')

@section('content')
<div class="space-y-6" x-data="footerBuilder()" x-init="init()">
    <div class="rounded bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ isset($footer) ? 'Edit Footer' : 'Create Footer' }}:
                    <span x-text="footerName"></span>
                </h1>
                <p class="mt-2 text-sm text-gray-600">Build and save a reusable site footer.</p>
            </div>
            <div class="flex gap-3">
                <button @click="saveFooter()" class="rounded bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                    Save Footer
                </button>
                <a href="{{ route('admin.footers.index') }}" class="rounded bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300">
                    Cancel
                </a>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(320px,1fr)]">
        <div class="rounded bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Footer Builder Canvas</h2>
                <p class="mt-1 text-sm text-gray-600">Create column-based footer sections and reusable navigation blocks.</p>
            </div>

            <div class="p-6">
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Number of Columns</label>
                    <select x-model="columnCount" @change="rebuildColumns()" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="1">1 Column</option>
                        <option value="2">2 Columns</option>
                        <option value="3">3 Columns</option>
                        <option value="4">4 Columns</option>
                    </select>
                </div>

                <div class="grid gap-4 md:grid-cols-2" x-ref="columnsContainer">
                    <template x-for="(column, idx) in columns" :key="idx">
                        <div class="rounded border border-gray-200 bg-gray-50 p-4">
                            <div class="mb-3">
                                <strong class="text-sm font-semibold text-gray-900">Column <span x-text="idx + 1"></span></strong>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Column Title</label>
                                    <input type="text" x-model="column.title" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Menu</label>
                                    <select x-model="column.menu_id" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">-- No menu --</option>
                                        <template x-for="menu in availableMenus" :key="menu.id">
                                            <option :value="menu.id" x-text="menu.name"></option>
                                        </template>
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Custom HTML Content</label>
                                    <textarea x-model="column.content" rows="4" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="rounded bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Footer Settings</h2>
            </div>

            <div class="space-y-4 p-6">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Footer Name</label>
                    <input type="text" x-model="footerName" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" x-model="isDefault" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span>Use as default footer</span>
                </label>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Background Color</label>
                    <input type="color" x-model="settings.backgroundColor" class="h-11 w-full rounded border border-gray-300 bg-white p-1">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Text Color</label>
                    <input type="color" x-model="settings.textColor" class="h-11 w-full rounded border border-gray-300 bg-white p-1">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Copyright Text</label>
                    <textarea x-model="settings.copyright" rows="3" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Social Media Links (JSON)</label>
                    <textarea
                        x-model="settings.socialLinks"
                        rows="4"
                        class="w-full rounded border-gray-300 font-mono text-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder='{"facebook":"url","twitter":"url"}'
                    ></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function footerBuilder() {
    const initialFooter = @json($footer);

    return {
        footerName: initialFooter?.name ?? 'New Footer',
        isDefault: initialFooter?.is_default ?? false,
        columnCount: 4,
        columns: [],
        availableMenus: @json($availableMenus),
        settings: {
            backgroundColor: '#1a1a1a',
            textColor: '#ffffff',
            copyright: '© 2026 CMS Portal. All rights reserved.',
            socialLinks: '{}'
        },

        init() {
            if (initialFooter?.content) {
                this.columnCount = initialFooter.content.columnCount || 4;
                this.columns = initialFooter.content.columns || [];
                this.settings = {
                    ...this.settings,
                    ...(initialFooter.content.settings || {}),
                };
            } else {
                this.rebuildColumns();
            }
        },

        rebuildColumns() {
            const newColumns = [];
            for (let i = 0; i < this.columnCount; i++) {
                newColumns.push({
                    title: '',
                    menu_id: null,
                    content: ''
                });
            }
            this.columns = newColumns;
        },

        async saveFooter() {
            const footerData = {
                name: this.footerName,
                is_default: this.isDefault,
                content: {
                    columnCount: this.columnCount,
                    columns: this.columns,
                    settings: this.settings
                }
            };

            try {
                const url = '{{ isset($footer) ? "/admin/footers/" . $footer->id : "/admin/footers" }}';
                const method = '{{ isset($footer) ? "PUT" : "POST" }}';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(footerData)
                });

                if (response.ok) {
                    const data = await response.json();
                    window.location.href = data.redirect || '{{ route('admin.footers.index') }}';
                } else {
                    const error = await response.json().catch(() => ({}));
                    console.error('Save failed:', error);
                    alert('Failed to save footer: ' + (error.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error saving footer:', error);
                alert('An error occurred while saving the footer');
            }
        }
    }
}
</script>
@endsection
