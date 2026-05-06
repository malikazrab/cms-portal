@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="rounded bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-semibold text-gray-900">Admin Dashboard</h1>
        <p class="mt-2 text-gray-600">Manage every draft and published page or post from the tabs below.</p>
    </div>

    <!-- Stats Cards Row 1 - ORIGINAL -->
    <div class="grid gap-4 md:grid-cols-4">
        <a href="{{ route('admin.pages.index') }}" class="rounded bg-white p-5 shadow-sm hover:shadow">
            <p class="text-sm text-gray-500">Pages</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $totalPages }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ $publishedPages }} published, {{ $draftPages }} draft</p>
        </a>
        <a href="{{ route('admin.posts.index') }}" class="rounded bg-white p-5 shadow-sm hover:shadow">
            <p class="text-sm text-gray-500">Posts</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $totalPosts }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ $publishedPosts }} published, {{ $draftPosts }} draft</p>
        </a>
        <a href="{{ route('admin.pages.create') }}" class="rounded bg-white p-5 shadow-sm hover:shadow">
            <p class="text-sm text-gray-500">Create</p>
            <p class="mt-2 text-lg font-semibold text-blue-700">New Page</p>
            <p class="mt-1 text-xs text-gray-500">Open the page builder</p>
        </a>
        <a href="{{ route('admin.posts.create') }}" class="rounded bg-white p-5 shadow-sm hover:shadow">
            <p class="text-sm text-gray-500">Create</p>
            <p class="mt-2 text-lg font-semibold text-blue-700">New Post</p>
            <p class="mt-1 text-xs text-gray-500">Write a blog post</p>
        </a>
    </div>

    <!-- ============================================ -->
    <!-- TASK FE-1: MENU SECTION WIDGET (NEW - ADDED) -->
    <!-- ============================================ -->
    <div class="rounded bg-white p-6 shadow-sm" x-data="menuWidget()" x-init="fetchMenus()">
        <div class="mb-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <h2 class="text-lg font-semibold text-gray-900">Menus</h2>
                <span x-show="!loading" class="px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                    <span x-text="menus.length"></span> Total
                </span>
            </div>
            <a href="/admin/menus/create" 
               class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create New Menu
            </a>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="py-6 text-center">
            <svg class="animate-spin h-6 w-6 text-gray-400 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-2 text-sm text-gray-500">Loading menus...</p>
        </div>

        <!-- Menus List -->
        <div x-show="!loading && menus.length > 0" class="divide-y divide-gray-200">
            <template x-for="menu in menus" :key="menu.id">
                <div class="py-3 first:pt-0 last:pb-0">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-gray-900" x-text="menu.name"></span>
                                <span x-show="menu.is_default" 
                                      class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    Default
                                </span>
                            </div>
                            <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                                <span x-text="menu.item_count + ' items'"></span>
                                <span>slug: <span x-text="menu.slug" class="font-mono"></span></span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            <button x-show="!menu.is_default"
                                    @click="setDefault(menu.id)"
                                    class="p-1.5 text-gray-400 hover:text-green-600"
                                    title="Set as Default">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </button>
                            <a :href="'/admin/menus/' + menu.id + '/edit'"
                               class="p-1.5 text-gray-400 hover:text-blue-600"
                               title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                            </a>
                            <button @click="deleteMenu(menu.id, menu.name)"
                                    class="p-1.5 text-gray-400 hover:text-red-600"
                                    title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="!loading && menus.length === 0" class="py-6 text-center">
            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
            <p class="text-sm text-gray-500">No menus created yet</p>
            <a href="/admin/menus/create" class="mt-2 inline-block text-sm text-blue-600 hover:text-blue-700">
                Create your first menu →
            </a>
        </div>
    </div>

    <!-- Stats Cards Row 2 - Latest Pages & Posts - ORIGINAL -->
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Latest Pages</h2>
                <a href="{{ route('admin.pages.index') }}" class="text-sm text-blue-600 hover:text-blue-700">View all</a>
            </div>
            <div class="space-y-3">
                @forelse ($latestPages as $page)
                    <div class="flex items-center justify-between gap-4 border-b pb-3 last:border-b-0 last:pb-0">
                        <div>
                            <p class="font-medium text-gray-900">{{ $page->title }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst($page->status) }} · /pages/{{ $page->slug }}</p>
                        </div>
                        <a href="{{ route('admin.pages.edit', $page) }}" class="text-sm text-blue-600 hover:text-blue-700">Edit</a>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No pages yet.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Latest Posts</h2>
                <a href="{{ route('admin.posts.index') }}" class="text-sm text-blue-600 hover:text-blue-700">View all</a>
            </div>
            <div class="space-y-3">
                @forelse ($latestPosts as $post)
                    <div class="flex items-center justify-between gap-4 border-b pb-3 last:border-b-0 last:pb-0">
                        <div>
                            <p class="font-medium text-gray-900">{{ $post->title }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst($post->status) }} · /blog/{{ $post->slug }}</p>
                        </div>
                        <a href="{{ route('admin.posts.edit', $post) }}" class="text-sm text-blue-600 hover:text-blue-700">Edit</a>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No posts yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js Component for Menu Widget -->
<script>
function menuWidget() {
    return {
        loading: false,
        menus: [],
        
        async fetchMenus() {
            this.loading = true;
            try {
                const response = await fetch('/admin/menus?dashboard=1');
                if (response.ok) {
                    const data = await response.json();
                    this.menus = data.menus || [];
                } else {
                    this.menus = [];
                }
            } catch (error) {
                console.error('Error fetching menus:', error);
                this.menus = [];
            } finally {
                this.loading = false;
            }
        },
        
        async setDefault(menuId) {
            if (!confirm('Set this menu as default?')) return;
            
            try {
                const response = await fetch(`/admin/menus/${menuId}/set-default`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    await this.fetchMenus();
                    alert('Default menu updated!');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        },
        
        async deleteMenu(menuId, menuName) {
            if (!confirm(`Delete "${menuName}"?`)) return;
            
            try {
                const response = await fetch(`/admin/menus/${menuId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    await this.fetchMenus();
                    alert('Menu deleted!');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
    }
}
</script>
@endsection