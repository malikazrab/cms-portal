@extends('layouts.admin')

@section('title', 'Edit Menu - ' . $menu->name)

@section('content')
<div class="space-y-6" x-data="navBuilder()" x-init="initItems(@json($menuItems))">
    <div class="rounded bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Edit Menu: {{ $menu->name }}</h1>
                <p class="mt-2 text-gray-600">Drag and drop to reorder. Add items to build your navigation.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.menus.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    ← Back to Menus
                </a>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Menu Items Builder -->
        <div class="rounded bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Menu Items</h2>
                <button @click="addItem()" class="inline-flex items-center px-3 py-1.5 text-sm rounded-lg bg-blue-600 hover:bg-blue-700 text-white">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Item
                </button>
            </div>
            
            <div class="space-y-2" x-ref="sortableContainer">
                <template x-for="(item, index) in items" :key="item.id">
                    <div class="border rounded-lg bg-white hover:shadow-sm transition-shadow" :data-id="item.id">
                        <div class="flex items-center gap-3 p-3 cursor-move" style="cursor: grab;">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                            </svg>
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium" x-text="item.label || 'New Item'"></span>
                                    <span class="text-xs text-gray-400" x-show="item.children && item.children.length" x-text="'(' + item.children.length + ' children)'"></span>
                                </div>
                                <p class="text-xs text-gray-400" x-text="item.url || 'No URL set'"></p>
                            </div>
                            <button @click="editItem(item)" class="p-1 text-gray-400 hover:text-blue-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                            </button>
                            <button @click="removeItem(index)" class="p-1 text-gray-400 hover:text-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Child items (indented) -->
                        <div x-show="item.children && item.children.length" class="ml-8 pl-4 border-l-2 border-gray-200 space-y-2 pb-3">
                            <template x-for="(child, childIndex) in item.children" :key="child.id">
                                <div class="flex items-center gap-3 p-2 bg-gray-50 rounded">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                    </svg>
                                    <div class="flex-1">
                                        <span class="text-sm font-medium" x-text="child.label"></span>
                                    </div>
                                    <button @click="editChildItem(item, childIndex)" class="p-1 text-gray-400 hover:text-blue-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </button>
                                    <button @click="removeChildItem(item, childIndex)" class="p-1 text-gray-400 hover:text-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
            
            <div x-show="items.length === 0" class="py-12 text-center">
                <p class="text-gray-500">No menu items yet. Click "Add Item" to get started.</p>
            </div>
        </div>

        <!-- Item Edit Form -->
        <div class="rounded bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900 mb-4" x-text="editingItem ? 'Edit Item' : 'Add New Item'"></h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Label</label>
                    <input type="text" x-model="form.label" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                    <input type="text" x-model="form.url" placeholder="/pages/home or https://example.com" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Parent Item</label>
                    <select x-model="form.parent_id" class="w-full rounded-lg border border-gray-300 px-4 py-2">
                        <option value="">— Top Level —</option>
                        <template x-for="item in items" :key="item.id">
                            <option :value="item.id" x-text="item.label"></option>
                        </template>
                    </select>
                </div>
                
                <div class="pt-4 flex justify-end gap-3">
                    <button @click="cancelEdit()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button @click="saveItem()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">
                        Save Item
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Form -->
    <div class="rounded bg-white p-6 shadow-sm">
        <form action="{{ route('admin.menus.update', $menu) }}" method="POST">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="name" value="{{ $menu->name }}">
            <input type="hidden" name="is_default" value="{{ $menu->is_default ? '1' : '0' }}">
            <input type="hidden" name="items" x-model="itemsJson">
            
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.menus.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium">
                    Save Menu
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function navBuilder() {
    return {
        items: [],
        editingItem: null,
        editingIndex: null,
        editingChild: false,
        form: {
            id: null,
            label: '',
            url: '',
            parent_id: '',
            children: []
        },
        
        initItems(savedItems) {
            this.items = savedItems || [];
            this.$nextTick(() => {
                this.initSortable();
            });
        },
        
        initSortable() {
            const container = this.$refs.sortableContainer;
            if (!container || !window.Sortable) return;
            
            Sortable.create(container, {
                animation: 150,
                onEnd: () => {
                    // Update order after drag
                    const newItems = [];
                    const elements = container.querySelectorAll('[data-id]');
                    elements.forEach(el => {
                        const id = parseInt(el.dataset.id);
                        const item = this.items.find(i => i.id === id);
                        if (item) newItems.push(item);
                    });
                    this.items = newItems;
                }
            });
        },
        
        addItem() {
            this.editingItem = null;
            this.editingChild = false;
            this.form = {
                id: null,
                label: '',
                url: '',
                parent_id: '',
                children: []
            };
        },
        
        editItem(item) {
            this.editingItem = item;
            this.editingChild = false;
            this.form = {
                id: item.id,
                label: item.label,
                url: item.url || '',
                parent_id: item.parent_id || '',
                children: item.children || []
            };
        },
        
        editChildItem(parent, childIndex) {
            const child = parent.children[childIndex];
            this.editingItem = { ...child, parent: parent };
            this.editingChild = true;
            this.form = {
                id: child.id,
                label: child.label,
                url: child.url || '',
                parent_id: parent.id,
                children: []
            };
        },
        
        saveItem() {
            if (!this.form.label.trim()) {
                alert('Please enter a label');
                return;
            }
            
            const newItem = {
                id: this.form.id || Date.now(),
                label: this.form.label,
                url: this.form.url,
                parent_id: this.form.parent_id || null,
                children: []
            };
            
            if (this.editingItem && !this.editingChild) {
                // Update existing item
                const index = this.items.findIndex(i => i.id === this.editingItem.id);
                if (index !== -1) {
                    this.items[index] = newItem;
                }
            } else if (this.editingChild && this.editingItem && this.editingItem.parent) {
                // Update child item
                const parent = this.editingItem.parent;
                const childIndex = parent.children.findIndex(c => c.id === this.editingItem.id);
                if (childIndex !== -1) {
                    parent.children[childIndex] = newItem;
                }
            } else if (this.form.parent_id) {
                // Add as child
                const parent = this.items.find(i => i.id == this.form.parent_id);
                if (parent) {
                    if (!parent.children) parent.children = [];
                    parent.children.push(newItem);
                }
            } else {
                // Add as top level
                this.items.push(newItem);
            }
            
            this.cancelEdit();
        },
        
        removeItem(index) {
            if (confirm('Remove this menu item?')) {
                this.items.splice(index, 1);
            }
        },
        
        removeChildItem(parent, childIndex) {
            if (confirm('Remove this menu item?')) {
                parent.children.splice(childIndex, 1);
            }
        },
        
        cancelEdit() {
            this.editingItem = null;
            this.editingChild = false;
            this.form = {
                id: null,
                label: '',
                url: '',
                parent_id: '',
                children: []
            };
        },
        
        get itemsJson() {
            return JSON.stringify(this.items);
        }
    }
}
</script>

<!-- Load SortableJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
@endsection