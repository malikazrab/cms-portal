<!DOCTYPE html>
<html lang="en" x-data="pageBuilderV5('header', initialHeader, initialMenus)" :class="darkMode ? 'dark' : ''" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Header | CMS Pro Builder</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="h-full bg-gray-100 dark:bg-gray-950 text-gray-800 dark:text-gray-100 font-sans overflow-hidden">

  {{-- Page builder topbar and modals --}}
  @include('admin.pages.partials._topbar')
  @include('admin.pages.partials._modals')

  <div class="flex flex-col h-full">

    <div class="flex flex-1 overflow-hidden">
      {{-- Left panel --}}
      @include('admin.pages.partials._panel_left')

      {{-- Canvas area (use the canvas partial) --}}
      @include('admin.pages.partials._canvas')

      {{-- Right panel --}}
      @include('admin.pages.partials._panel_right')
    </div>
  </div>

  {{-- Inject initial header data for pageBuilderV5 to consume --}}
  <script>
    const initialHeader = @json($header ?? null);
    const initialMenus = @json($availableMenus ?? []);
    // mark this builder instance as a header builder so we can override save behavior
    const _BUILDER_MODE = 'header';
  </script>

  {{-- Page builder core scripts (renders widgets, panels, save/load, etc.) --}}
  @include('admin.pages.partials._scripts')


</body>
</html>
