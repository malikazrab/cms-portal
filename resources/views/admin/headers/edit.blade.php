@extends('layouts.admin')

@section('title', isset($header) ? 'Edit Header Template' : 'Create Header Template')

@section('content')
<div class="space-y-6" x-data="headerBuilder()" x-init="init()">
    <div class="rounded bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ isset($header) ? 'Edit Header' : 'Create Header' }}:
                    <span x-text="headerName"></span>
                </h1>
                <p class="mt-2 text-sm text-gray-600">Build and save a reusable site header.</p>
            </div>
            <div class="flex gap-3">
                <button @click="saveHeader()" class="rounded bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                    Save Header
                </button>
                <a href="{{ route('admin.headers.index') }}" class="rounded bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300">
                    Cancel
                </a>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(320px,1fr)]">
        <div class="rounded bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Header Builder Canvas</h2>
                <p class="mt-1 text-sm text-gray-600">Drag widgets into the canvas to compose your header.</p>
            </div>

            <div class="p-6" x-ref="builderCanvas" @dragover.prevent @drop.prevent="handleDrop($event)">
                <div class="mb-4 rounded border border-gray-200 bg-gray-50 p-4">
                    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500">Available Widgets</h3>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" draggable="true" @dragstart="dragStart($event, 'logo')" @click="addWidget('logo')" class="rounded bg-gray-700 px-3 py-2 text-sm font-medium text-white hover:bg-gray-800">
                            Logo Widget
                        </button>
                        <button type="button" draggable="true" @dragstart="dragStart($event, 'menu')" @click="addWidget('menu')" class="rounded bg-gray-700 px-3 py-2 text-sm font-medium text-white hover:bg-gray-800">
                            Menu Widget
                        </button>
                        <button type="button" draggable="true" @dragstart="dragStart($event, 'search')" @click="addWidget('search')" class="rounded bg-gray-700 px-3 py-2 text-sm font-medium text-white hover:bg-gray-800">
                            Search Widget
                        </button>
                        <button type="button" draggable="true" @dragstart="dragStart($event, 'cta')" @click="addWidget('cta')" class="rounded bg-gray-700 px-3 py-2 text-sm font-medium text-white hover:bg-gray-800">
                            CTA Button Widget
                        </button>
                    </div>
                </div>

                <div x-ref="builderContent" class="min-h-[320px] rounded border border-dashed border-gray-300 p-4">
                    <template x-for="(widget, index) in widgets" :key="index">
                        <div class="mb-3 rounded border border-gray-200 bg-white p-4 shadow-sm">
                            <div class="flex items-center justify-between gap-3">
                                <strong class="text-sm font-semibold text-gray-900" x-text="widget.type.toUpperCase() + ' Widget'"></strong>
                                <button @click="removeWidget(index)" class="rounded bg-red-600 px-2 py-1 text-xs font-medium text-white hover:bg-red-700">
                                    Remove
                                </button>
                            </div>

                            <div x-show="widget.type === 'menu'" class="mt-3">
                                <label class="mb-1 block text-sm font-medium text-gray-700">Select Menu</label>
                                <select x-model="widget.settings.menu_id" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">-- Select a menu --</option>
                                    <template x-for="menu in availableMenus" :key="menu.id">
                                        <option :value="menu.id" x-text="menu.name"></option>
                                    </template>
                                </select>
                            </div>

                            <div x-show="widget.type === 'logo'" class="mt-3 space-y-3">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Logo URL</label>
                                    <input type="text" x-model="widget.settings.logo_url" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Logo Width (px)</label>
                                    <input type="number" x-model="widget.settings.logo_width" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>

                            <div x-show="widget.type === 'search'" class="mt-3">
                                <label class="mb-1 block text-sm font-medium text-gray-700">Placeholder</label>
                                <input type="text" x-model="widget.settings.placeholder" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div x-show="widget.type === 'cta'" class="mt-3 space-y-3">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Button Text</label>
                                    <input type="text" x-model="widget.settings.text" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Button URL</label>
                                    <input type="text" x-model="widget.settings.url" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="widgets.length === 0" class="flex min-h-[240px] items-center justify-center rounded border border-dashed border-gray-200 bg-gray-50 text-center text-sm text-gray-500">
                        Drag widgets here to build your header
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Header Settings</h2>
            </div>
            <div class="space-y-4 p-6">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Header Name</label>
                    <input type="text" x-model="headerName" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" x-model="isDefault" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span>Use as default header</span>
                </label>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Background Color</label>
                    <input type="color" x-model="settings.backgroundColor" class="h-11 w-full rounded border border-gray-300 bg-white p-1">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Container Width</label>
                    <select x-model="settings.containerWidth" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="full">Full Width</option>
                        <option value="boxed">Boxed (1200px)</option>
                        <option value="fluid">Fluid (90%)</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Padding (px)</label>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="number" x-model="settings.paddingTop" placeholder="Top" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <input type="number" x-model="settings.paddingBottom" placeholder="Bottom" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <input type="number" x-model="settings.paddingLeft" placeholder="Left" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <input type="number" x-model="settings.paddingRight" placeholder="Right" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function headerBuilder() {
    const initialHeader = @json($header);
    const initialMenus = @json($availableMenus);

    return {
        headerName: initialHeader?.name ?? 'New Header',
        isDefault: initialHeader?.is_default ?? false,
        widgets: [],
        availableMenus: initialMenus,
        settings: {
            backgroundColor: '#ffffff',
            containerWidth: 'full',
            paddingTop: 10,
            paddingBottom: 10,
            paddingLeft: 20,
            paddingRight: 20
        },

        init() {
            if (initialHeader?.content) {
                this.widgets = initialHeader.content.widgets || [];
                this.settings = {
                    ...this.settings,
                    ...(initialHeader.content.settings || {}),
                };
            }
        },

        dragStart(event, widgetType) {
            event.dataTransfer.setData('text/plain', widgetType);
        },

        handleDrop(event) {
            const widgetType = event.dataTransfer.getData('text/plain');
            this.addWidget(widgetType);
        },

        addWidget(type) {
            let newWidget = {
                type: type,
                settings: {}
            };

            switch(type) {
                case 'menu':
                    newWidget.settings = { menu_id: null, alignment: 'horizontal' };
                    break;
                case 'logo':
                    newWidget.settings = { logo_url: '/logo.png', logo_width: 150 };
                    break;
                case 'search':
                    newWidget.settings = { placeholder: 'Search...' };
                    break;
                case 'cta':
                    newWidget.settings = { text: 'Get Started', url: '/contact' };
                    break;
            }

            this.widgets.push(newWidget);
        },

        removeWidget(index) {
            this.widgets.splice(index, 1);
        },

        async saveHeader() {
            const headerData = {
                name: this.headerName,
                is_default: this.isDefault,
                content: {
                    widgets: this.widgets,
                    settings: this.settings
                }
            };

            const response = await fetch(`{{ isset($header) ? route('admin.headers.update', $header) : route('admin.headers.store') }}`, {
                method: '{{ isset($header) ? 'PUT' : 'POST' }}',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(headerData)
            });

            if (response.ok) {
                const data = await response.json();
                window.location.href = data.redirect || '{{ route('admin.headers.index') }}';
            } else {
                alert('Failed to save header.');
            }
        }
    }
}
</script>
@endsection
