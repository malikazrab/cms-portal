<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') | {{ config('app.name', 'CMS Portal') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Awesome for icons (Phase 2) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen">
        <header class="border-b bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3">
                <a href="{{ route('admin.dashboard') }}" class="font-semibold text-gray-900">CMS Portal</a>
                <div class="flex items-center gap-4 text-sm text-gray-600">
                    <span>{{ auth()->user()->email ?? 'Admin' }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-700">Logout</button>
                    </form>
                </div>
            </div>
        </header>

        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-6 px-4 py-6 lg:grid-cols-[220px_1fr]">
            <aside class="rounded bg-white p-4 shadow-sm">
                <nav class="space-y-1 text-sm">
                    
                    <!-- ==================== MAIN SECTION ==================== -->
                    @if (auth()->user()?->hasPermission('dashboard.view'))
                        <a class="block rounded px-3 py-2 hover:bg-gray-100" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt w-4 mr-2 text-gray-400"></i> Dashboard
                        </a>
                    @endif

                    <!-- ==================== CONTENT SECTION ==================== -->
                    <div class="pt-2 mt-2 border-t border-gray-200">
                        <p class="px-3 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Content</p>
                        
                        @if (auth()->user()?->hasPermission('posts.view'))
                            <a class="block rounded px-3 py-2 hover:bg-gray-100" href="{{ route('admin.posts.index') }}">
                                <i class="fas fa-blog w-4 mr-2 text-gray-400"></i> Posts
                            </a>
                        @endif
                        @if (auth()->user()?->hasPermission('posts.create'))
                            <a class="block rounded px-3 py-2 hover:bg-gray-100" href="{{ route('admin.posts.create') }}">
                                <i class="fas fa-plus-circle w-4 mr-2 text-gray-400"></i> New Post
                            </a>
                        @endif
                        @if (auth()->user()?->hasPermission('pages.view'))
                            <a class="block rounded px-3 py-2 hover:bg-gray-100" href="{{ route('admin.pages.index') }}">
                                <i class="fas fa-file-alt w-4 mr-2 text-gray-400"></i> Pages
                            </a>
                        @endif
                        @if (auth()->user()?->hasPermission('pages.create'))
                            <a class="block rounded px-3 py-2 hover:bg-gray-100" href="{{ route('admin.pages.create') }}">
                                <i class="fas fa-plus-circle w-4 mr-2 text-gray-400"></i> New Page
                            </a>
                        @endif
                        @if (auth()->user()?->hasPermission('categories.view'))
                            <a class="block rounded px-3 py-2 hover:bg-gray-100" href="{{ route('admin.categories.index') }}">
                                <i class="fas fa-tags w-4 mr-2 text-gray-400"></i> Categories
                            </a>
                        @endif
                        @if (auth()->user()?->hasPermission('media.view'))
                            <a class="block rounded px-3 py-2 hover:bg-gray-100" href="{{ route('admin.media.index') }}">
                                <i class="fas fa-images w-4 mr-2 text-gray-400"></i> Media
                            </a>
                        @endif
                    </div>

                    <!-- ==================== APPEARANCE SECTION (NEW - PHASE 2) ==================== -->
                    <div class="pt-2 mt-2 border-t border-gray-200">
                        <p class="px-3 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Appearance</p>
                        
                        <!-- Menus - Task FE-1, FE-2 -->
                        @can('menus.view')
                            <a class="block rounded px-3 py-2 hover:bg-gray-100" href="{{ route('admin.menus.index') }}">
                                <i class="fas fa-bars w-4 mr-2 text-gray-400"></i> Menus
                            </a>
                        @endcan
                        
                        <!-- Headers - Task FE-4 -->
                        @can('headers.view')
                            <a class="block rounded px-3 py-2 hover:bg-gray-100" href="{{ route('admin.headers.index') }}">
                                <i class="fas fa-arrow-up w-4 mr-2 text-gray-400"></i> Headers
                            </a>
                        @endcan
                        
                        <!-- Footers - Task FE-4 -->
                        @can('footers.view')
                            <a class="block rounded px-3 py-2 hover:bg-gray-100" href="{{ route('admin.footers.index') }}">
                                <i class="fas fa-arrow-down w-4 mr-2 text-gray-400"></i> Footers
                            </a>
                        @endcan
                    </div>

                    <!-- ==================== SETTINGS SECTION ==================== -->
                    <div class="pt-2 mt-2 border-t border-gray-200">
                        <p class="px-3 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Settings</p>
                        
                        @if (auth()->user()?->hasPermission('settings.manage'))
                            <a class="block rounded px-3 py-2 hover:bg-gray-100" href="{{ route('admin.settings.index') }}">
                                <i class="fas fa-cog w-4 mr-2 text-gray-400"></i> Settings
                            </a>
                        @endif
                        @if (auth()->user()?->hasPermission('users.manage'))
                            <a class="block rounded px-3 py-2 hover:bg-gray-100" href="{{ route('admin.users.index') }}">
                                <i class="fas fa-users w-4 mr-2 text-gray-400"></i> Users
                            </a>
                        @endif
                        @if (auth()->user()?->hasPermission('users.manage'))
                            <a class="block rounded px-3 py-2 hover:bg-gray-100" href="{{ route('admin.roles.index') }}">
                                <i class="fas fa-lock w-4 mr-2 text-gray-400"></i> Roles & Permissions
                            </a>
                        @endif
                        @if (auth()->user()?->hasPermission('activity.view'))
                            <a class="block rounded px-3 py-2 hover:bg-gray-100" href="{{ route('admin.activity-logs.index') }}">
                                <i class="fas fa-history w-4 mr-2 text-gray-400"></i> Activity Logs
                            </a>
                        @endif
                    </div>
                </nav>
            </aside>

            <main>
                @if (session('success'))
                    <div class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <ul class="list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Toast Notification System for Phase 2 -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

    <script>
        // Toast notification helper
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;
            
            const toast = document.createElement('div');
            toast.className = `flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg text-white text-sm font-medium animate-slide-in`;
            
            if (type === 'success') {
                toast.classList.add('bg-green-500');
                toast.innerHTML = `<i class="fas fa-check-circle"></i><span>${message}</span>`;
            } else if (type === 'error') {
                toast.classList.add('bg-red-500');
                toast.innerHTML = `<i class="fas fa-times-circle"></i><span>${message}</span>`;
            } else if (type === 'warning') {
                toast.classList.add('bg-yellow-500');
                toast.innerHTML = `<i class="fas fa-exclamation-triangle"></i><span>${message}</span>`;
            } else {
                toast.classList.add('bg-blue-500');
                toast.innerHTML = `<i class="fas fa-info-circle"></i><span>${message}</span>`;
            }
            
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
        
        // Listen for toast events from Alpine components
        window.addEventListener('show-toast', (e) => {
            showToast(e.detail.message, e.detail.type);
        });
    </script>

    <style>
        @keyframes slide-in {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        .animate-slide-in {
            animation: slide-in 0.3s ease;
        }
    </style>

    @stack('scripts')
</body>
</html>