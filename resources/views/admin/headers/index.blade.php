@extends('admin.layouts.app')

@section('title', 'Header Templates')

@section('content')
<div class="container-fluid px-4" x-data="headersManager()">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Header Templates</h1>
        <a href="{{ route('admin.headers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Header
        </a>
    </div>

    <!-- Grid of header templates -->
    <div class="row">
        <template x-for="header in headers" :key="header.id">
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100">
                    <!-- Thumbnail Preview -->
                    <div class="card-img-top bg-light p-3" style="height: 150px;">
                        <div x-html="header.thumbnail_preview" class="scale-down"></div>
                    </div>
                    
                    <div class="card-body">
                        <h5 class="card-title" x-text="header.name"></h5>
                        
                        <!-- Default Badge -->
                        <span x-show="header.is_default" class="badge bg-success mb-2">
                            Default
                        </span>
                        
                        <div class="mt-2">
                            <a :href="'/admin/headers/' + header.id + '/edit'" 
                               class="btn btn-sm btn-outline-primary">Edit</a>
                            <button @click="deleteHeader(header.id)" 
                                    class="btn btn-sm btn-outline-danger">Delete</button>
                            <button x-show="!header.is_default" 
                                    @click="setDefault(header.id)"
                                    class="btn btn-sm btn-outline-secondary">
                                Set as Default
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Empty state -->
    <div x-show="headers.length === 0" class="text-center py-5">
        <p class="text-muted">No header templates yet. Create your first one!</p>
    </div>
</div>

<script>
function headersManager() {
    return {
        headers: [],
        
        init() {
            this.fetchHeaders();
        },
        
        async fetchHeaders() {
            const response = await fetch('/admin/headers');
            this.headers = await response.json();
        },
        
        async setDefault(id) {
            if (confirm('Set this header as default?')) {
                await fetch(`/admin/headers/${id}/set-default`, { method: 'POST' });
                this.fetchHeaders();
            }
        },
        
        async deleteHeader(id) {
            if (confirm('Delete this header template? This cannot be undone.')) {
                await fetch(`/admin/headers/${id}`, { method: 'DELETE' });
                this.fetchHeaders();
            }
        }
    }
}
</script>

<style>
.scale-down {
    transform: scale(0.5);
    transform-origin: top left;
    width: 200%;
    height: 200%;
    overflow: hidden;
}
</style>
@endsection