@extends('layouts.admin')

@section('title', 'Edit Footer Template')

@section('content')
<div class="container-fluid px-4" x-data="footerBuilder()">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Edit Footer: <span x-text="footerName"></span></h1>
        <button @click="saveFooter()" class="btn btn-success">Save Footer</button>
    </div>

    <div class="row">
        <!-- Builder Canvas -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Footer Builder - Column Layout</h5>
                </div>
                <div class="card-body">
                    <!-- Column layout selector -->
                    <div class="mb-3">
                        <label>Number of Columns</label>
                        <select x-model="columnCount" @change="rebuildColumns()" class="form-control">
                            <option value="1">1 Column</option>
                            <option value="2">2 Columns</option>
                            <option value="3">3 Columns</option>
                            <option value="4">4 Columns</option>
                        </select>
                    </div>
                    
                    <!-- Column contents -->
                    <div class="row" x-ref="columnsContainer">
                        <template x-for="(column, idx) in columns" :key="idx">
                            <div :class="'col-md-' + (12/columnCount)" class="mb-3">
                                <div class="card">
                                    <div class="card-header">
                                        <strong>Column <span x-text="idx + 1"></span></strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <label>Column Title</label>
                                            <input type="text" x-model="column.title" class="form-control">
                                        </div>
                                        
                                        <!-- Menu Widget for this column -->
                                        <div class="mb-2">
                                            <label>Menu (for navigation)</label>
                                            <select x-model="column.menu_id" class="form-control">
                                                <option value="">-- No menu --</option>
                                                <template x-for="menu in availableMenus" :key="menu.id">
                                                    <option :value="menu.id" x-text="menu.name"></option>
                                                </template>
                                            </select>
                                        </div>
                                        
                                        <!-- Custom content -->
                                        <div class="mb-2">
                                            <label>Custom HTML Content</label>
                                            <textarea x-model="column.content" rows="3" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Settings Panel -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Footer Settings</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label>Footer Name</label>
                        <input type="text" x-model="footerName" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Background Color</label>
                        <input type="color" x-model="settings.backgroundColor" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Text Color</label>
                        <input type="color" x-model="settings.textColor" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Copyright Text</label>
                        <textarea x-model="settings.copyright" rows="2" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Social Media Links (JSON)</label>
                        <textarea x-model="settings.socialLinks" rows="3" class="form-control" 
                                  placeholder='{"facebook":"url","twitter":"url"}'></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function footerBuilder() {
    return {
        footerName: 'New Footer',
        columnCount: 4,
        columns: [],
        availableMenus: @json($menus ?? []),
        settings: {
            backgroundColor: '#1a1a1a',
            textColor: '#ffffff',
            copyright: '© 2026 CMS Portal. All rights reserved.',
            socialLinks: '{}'
        },
        
        init() {
            this.fetchMenus();
            this.rebuildColumns();
            @if(isset($footer))
                this.loadFooter({{ $footer->id }});
            @endif
        },
        
        async fetchMenus() {
            try {
                const response = await fetch('/admin/menus');
                if (response.ok) {
                    this.availableMenus = await response.json();
                }
            } catch (error) {
                console.error('Error fetching menus:', error);
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
        
        async loadFooter(id) {
            try {
                const response = await fetch(`/admin/footers/${id}`);
                if (response.ok) {
                    const data = await response.json();
                    this.footerName = data.name;
                    this.columnCount = data.content.columnCount || 4;
                    this.columns = data.content.columns || [];
                    this.settings = { ...this.settings, ...data.content.settings };
                }
            } catch (error) {
                console.error('Error loading footer:', error);
            }
        },
        
        async saveFooter() {
            const footerData = {
                name: this.footerName,
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(footerData)
                });
                
                if (response.ok) {
                    window.location.href = '/admin/footers';
                } else {
                    const error = await response.json();
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
