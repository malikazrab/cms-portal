{{--
|--------------------------------------------------------------------------
| Partial: _topbar.blade.php
| Path:    resources/views/admin/pages/partials/_topbar.blade.php
|--------------------------------------------------------------------------
| Contains the full top action bar:
|   • Logo
|   • Undo / Redo
|   • Responsive preview switcher (desktop / tablet / mobile)
|   • Live preview toggle
|   • Auto-save / unsaved indicator
|   • Snap grid toggle
|   • Accessibility check
|   • Page versions
|   • Dark mode toggle
|   • Keyboard shortcuts
|   • Export / Import JSON
|   • Save & Publish buttons
|--------------------------------------------------------------------------
--}}

<header class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-900 border-b dark:border-gray-700 shadow-sm flex-shrink-0 z-50">

  {{-- ── Logo ──────────────────────────────────────────────────────────── --}}
  <div class="flex items-center gap-2 mr-4">
    <div class="w-8 h-8 bg-gradient-to-br from-brand-500 to-purple-600 rounded-lg flex items-center justify-center">
      <i class="fas fa-layer-group text-white text-sm"></i>
    </div>
    <span class="font-bold text-sm tracking-tight hidden sm:block">CMS Pro Builder</span>
  </div>

  {{-- ── Undo / Redo ─────────────────────────────────────────────────── --}}
  <button @click="undo()"
    :disabled="undoStack.length === 0"
    class="p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 disabled:opacity-40 text-sm"
    title="Undo (Ctrl+Z)">
    <i class="fas fa-undo"></i>
  </button>
  <button @click="redo()"
    :disabled="redoStack.length === 0"
    class="p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 disabled:opacity-40 text-sm"
    title="Redo (Ctrl+Y)">
    <i class="fas fa-redo"></i>
  </button>

  <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>

  {{-- ── Responsive preview switcher ────────────────────────────────── --}}
  <div class="flex bg-gray-100 dark:bg-gray-800 rounded-lg p-0.5 gap-0.5">
    <button @click="previewMode = 'desktop'"
      :class="previewMode === 'desktop' ? 'bg-white dark:bg-gray-700 shadow' : ''"
      class="p-1.5 rounded text-xs"
      title="Desktop">
      <i class="fas fa-desktop"></i>
    </button>
    <button @click="previewMode = 'tablet'"
      :class="previewMode === 'tablet' ? 'bg-white dark:bg-gray-700 shadow' : ''"
      class="p-1.5 rounded text-xs"
      title="Tablet">
      <i class="fas fa-tablet-alt"></i>
    </button>
    <button @click="previewMode = 'mobile'"
      :class="previewMode === 'mobile' ? 'bg-white dark:bg-gray-700 shadow' : ''"
      class="p-1.5 rounded text-xs"
      title="Mobile">
      <i class="fas fa-mobile-alt"></i>
    </button>
  </div>

  <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>

  {{-- ── Live preview toggle ─────────────────────────────────────────── --}}
  <button @click="livePreview = !livePreview"
    :class="livePreview ? 'bg-green-500 text-white' : 'bg-gray-100 dark:bg-gray-800'"
    class="px-3 py-1.5 rounded-lg text-xs font-medium flex items-center gap-1.5 hover:opacity-90">
    <i class="fas" :class="livePreview ? 'fa-eye-slash' : 'fa-eye'"></i>
    <span x-text="livePreview ? 'Exit Preview' : 'Preview'"></span>
  </button>

  {{-- ── Spacer ───────────────────────────────────────────────────────── --}}
  <div class="flex-1"></div>

  {{-- ── Auto-save / unsaved indicator ─────────────────────────────── --}}
  <span x-show="autoSaveIndicator" x-cloak
    class="text-xs text-green-500 flex items-center gap-1 animate-pulse">
    <i class="fas fa-circle text-[8px]"></i> Saved
  </span>
  <span x-show="isDirty && !autoSaveIndicator"
    class="text-xs text-yellow-500 flex items-center gap-1">
    <i class="fas fa-circle text-[8px]"></i> Unsaved
  </span>

  {{-- ── Snap grid toggle ────────────────────────────────────────────── --}}
  <button @click="snapGrid = !snapGrid"
    :class="snapGrid ? 'bg-brand-100 dark:bg-brand-900 text-brand-600' : ''"
    class="p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-sm"
    title="Toggle Snap Grid">
    <i class="fas fa-th-large"></i>
  </button>

  {{-- ── Accessibility check ─────────────────────────────────────────── --}}
  <button @click="runA11yCheck(); showA11yModal = true"
    class="p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-sm"
    title="Accessibility Check">
    <i class="fas fa-universal-access"></i>
  </button>

  {{-- ── Page versions ───────────────────────────────────────────────── --}}
  <button @click="showVersionsModal = true"
    class="p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-sm"
    title="Page Versions">
    <i class="fas fa-code-branch"></i>
  </button>

  {{-- ── Dark mode toggle ────────────────────────────────────────────── --}}
  <button @click="toggleDarkMode()"
    class="p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-sm"
    title="Toggle Dark Mode">
    <i class="fas" :class="darkMode ? 'fa-sun' : 'fa-moon'"></i>
  </button>

  {{-- ── Keyboard shortcuts ──────────────────────────────────────────── --}}
  <button @click="showShortcutsModal = true"
    class="p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-sm"
    title="Keyboard Shortcuts">
    <i class="fas fa-question-circle"></i>
  </button>

  <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>

  {{-- ── Export JSON ─────────────────────────────────────────────────── --}}
  <button @click="exportJSON()"
    class="px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 flex items-center gap-1.5">
    <i class="fas fa-download"></i> Export
  </button>

  {{-- ── Import JSON ─────────────────────────────────────────────────── --}}
  <label class="px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 flex items-center gap-1.5 cursor-pointer">
    <i class="fas fa-upload"></i> Import
    <input type="file" accept=".json" class="hidden" @change="importJSON($event)">
  </label>

  <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>

  {{-- ── Save ────────────────────────────────────────────────────────── --}}
  <button @click="savePage()"
    class="px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 flex items-center gap-1.5">
    <i class="fas fa-save"></i> Save
  </button>

  {{-- ── Publish ─────────────────────────────────────────────────────── --}}
  <button @click="publishPage()"
    class="px-4 py-1.5 rounded-lg text-xs font-bold bg-brand-500 text-white hover:bg-brand-600 flex items-center gap-1.5">
    <i class="fas fa-globe"></i> Publish
  </button>

	  {{-- ── Theme Mode Buttons ────────────────────────────────────────── --}}
  @php $isTheme = isset($isThemeMode) && $isThemeMode; $themeId = $themeId ?? null; @endphp
  @if($isTheme && $themeId)
    <button @click="saveThemeContent()"
      class="px-3 py-1.5 rounded-lg text-xs font-medium bg-purple-600 text-white hover:bg-purple-700 flex items-center gap-1.5">
      <i class="fas fa-save"></i> Save Theme
    </button>
    <button @click="publishTheme()"
      class="px-4 py-1.5 rounded-lg text-xs font-bold bg-green-600 text-white hover:bg-green-700 flex items-center gap-1.5">
      <i class="fas fa-globe"></i> Publish
    </button>
    <a href="{{ route('admin.themes.index') }}"
      class="px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 flex items-center gap-1.5">
      <i class="fas fa-times"></i> Exit
    </a>
  @endif

</header>
