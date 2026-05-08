@extends('layouts.admin')

@section('title', 'Edit Header Template')

@section('content')
<div class="container-fluid px-4" x-data="headerBuilder()">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Edit Header: <span x-text="headerName"></span></h1>
        <div>
            <button @click="saveHeader()" class="btn btn-success">Save Header</button>
            <a href="{{ route('admin.headers.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </div>

    <div class="row">
        <!-- Builder Canvas (Left) -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Header Builder Canvas</h5>
                    <small>Drag and drop widgets to build your header</small>
                </div>
                <div class="card-body" 
                     x-ref="builderCanvas"
                     @dragover.prevent
                     @drop.prevent="handleDrop($event)">
                    
                    <!-- Available Widgets Panel -->
                    <div class="widgets-panel bg-light p-3 mb-3 rounded">
                        <h6>Available Widgets</h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <div draggable="true" 
                                 @dragstart="dragStart($event, 'logo')"
                                 class="widget-drag badge bg-secondary p-2">
                                Logo Widget
                            </div>
                            <div draggable="true" 
                                 @dragstart="dragStart($event, 'menu')"
                                 class="widget-drag badge bg-secondary p-2">
                                Menu Widget
                            </div>
                            <div draggable="true" 
                                 @dragstart="dragStart($event, 'search')"
                                 class="widget-drag badge bg-secondary p-2">
                                Search Widget
                            </div>
                            <div draggable="true" 
                                 @dragstart="dragStart($event, 'cta')"
                                 class="widget-drag badge bg-secondary p-2">
                                CTA Button Widget
                            </div>
                        </div>
                    </div>
                    
                    <!-- Builder Content Area -->
                    <div x-ref="builderContent" class="builder-content min-vh-50 border p-3">
                        <template x-for="(widget, index) in widgets" :key="index">
                            <div class="widget-item border mb-2 p-2 rounded" 
                                 :data-widget-type="widget.type">
                                <div class="d-flex justify-content-between">
                                    <strong x-text="widget.type.toUpperCase() + ' Widget'"></strong>
                                    <button @click="removeWidget(index)" class="btn btn-sm btn-danger">×</button>
                                </div>
                                
                                <!-- Menu Widget specific settings -->
                                <div x-show="widget.type === 'menu'" class="mt-2">
                                    <label>Select Menu:</label>
                                    <select x-model="widget.settings.menu_id" class="form-control form-control-sm">
                                        <option value="">-- Select a menu --</option>
                                        <template x-for="menu in availableMenus">
                                            <option :value="menu.id" x-text="menu.name"></option>
                                        </template>
                                    </select>
                                </div>
                                
                                <!-- Logo Widget settings -->
                                <div x-show="widget.type === 'logo'" class="mt-2">
                                    <label>Logo URL:</label>
                                    <input type="text" x-model="widget.settings.logo_url" class="form-control form-control-sm">
                                    <label class="mt-1">Logo Width (px):</label>
                                    <input type="number" x-model="widget.settings.logo_width" class="form-control form-control-sm">
                                </div>
                            </div>
                        </template>
                        <div x-show="widgets.length === 0" class="text-muted text-center py-4">
                            Drag widgets here to build your header
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Settings Panel (Right) -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Header Settings</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label>Header Name</label>
                        <input type="text" x-model="headerName" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Background Color</label>
                        <input type="color" x-model="settings.backgroundColor" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Container Width</label>
                        <select x-model="settings.containerWidth" class="form-control">
                            <option value="full">Full Width</option>
                            <option value="boxed">Boxed (1200px)</option>
                            <option value="fluid">Fluid (90%)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Padding (px)</label>
                        <div class="row">
                            <div class="col-6"><input type="number" x-model="settings.paddingTop" placeholder="Top" class="form-control"></div>
                            <div class="col-6"><input type="number" x-model="settings.paddingBottom" placeholder="Bottom" class="form-control"></div>
                            <div class="col-6 mt-2"><input type="number" x-model="settings.paddingLeft" placeholder="Left" class="form-control"></div>
                            <div class="col-6 mt-2"><input type="number" x-model="settings.paddingRight" placeholder="Right" class="form-control"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function headerBuilder() {
    return {
        headerName: 'New Header',
        widgets: [],
        availableMenus: [],
        settings: {
            backgroundColor: '#ffffff',
            containerWidth: 'full',
            paddingTop: 10,
            paddingBottom: 10,
            paddingLeft: 20,
            paddingRight: 20
        },
        
        init() {
            this.fetchMenus();
            const headerId = {{ isset($header) ? $header->id : 'null' }};
            if (headerId) {
                this.loadHeader(headerId);
            }
        },
        
        async fetchMenus() {
            const response = await fetch('/admin/menus');
            this.availableMenus = await response.json();
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
        
        async loadHeader(id) {
            const response = await fetch(`/admin/headers/${id}`);
            const data = await response.json();
            this.headerName = data.name;
            this.widgets = data.content.widgets || [];
            this.settings = data.content.settings || this.settings;
        },
        
        async saveHeader() {
            const headerData = {
                name: this.headerName,
                content: {
                    widgets: this.widgets,
                    settings: this.settings
                }
            };
            
            const response = await fetch(`/admin/headers/{{ $header->id ?? '' }}`, {
                method: '{{ isset($header) ? 'PUT' : 'POST' }}',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(headerData)
            });
            
            if (response.ok) {
                window.location.href = '/admin/headers';
            }
        }
    }
}
</script>
@endsection
