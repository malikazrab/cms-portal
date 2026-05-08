@extends('layouts.admin')

@section('title', 'Footer Templates')

@section('content')
<div class="container-fluid px-4" x-data="footersManager()">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Footer Templates</h1>
        <a href="{{ route('admin.footers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Footer
        </a>
    </div>

    <!-- Grid of footer templates -->
    <div class="row">
        <template x-for="footer in footers" :key="footer.id">
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100">
                    <!-- Thumbnail Preview -->
                    <div class="card-img-top bg-dark text-white p-3" style="height: 200px;">
                        <div x-html="footer.thumbnail_preview" class="scale-down-footer"></div>
                    </div>
                    
                    <div class="card-body">
                        <h5 class="card-title" x-text="footer.name"></h5>
                        
                        <span x-show="footer.is_default" class="badge bg-success mb-2">
                            Default Footer
                        </span>
                        
                        <div class="mt-2">
                            <a :href="'/admin/footers/' + footer.id + '/edit'" 
                               class="btn btn-sm btn-outline-primary">Edit</a>
                            <button @click="deleteFooter(footer.id)" 
                                    class="btn btn-sm btn-outline-danger">Delete</button>
                            <button x-show="!footer.is_default" 
                                    @click="setDefault(footer.id)"
                                    class="btn btn-sm btn-outline-secondary">
                                Set as Default
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <div x-show="footers.length === 0" class="text-center py-5">
        <p class="text-muted">No footer templates yet. Create your first one!</p>
    </div>
</div>

<script>
function footersManager() {
    return {
        footers: [],
        
        init() {
            this.fetchFooters();
        },
        
        async fetchFooters() {
            const response = await fetch('/admin/footers');
            this.footers = await response.json();
        },
        
        async setDefault(id) {
            if (confirm('Set this footer as default?')) {
                await fetch(`/admin/footers/${id}/set-default`, { method: 'POST' });
                this.fetchFooters();
            }
        },
        
        async deleteFooter(id) {
            if (confirm('Delete this footer template? This cannot be undone.')) {
                await fetch(`/admin/footers/${id}`, { method: 'DELETE' });
                this.fetchFooters();
            }
        }
    }
}
</script>

<style>
.scale-down-footer {
    transform: scale(0.4);
    transform-origin: top left;
    width: 250%;
    height: 250%;
    overflow: hidden;
}
</style>
@endsection
