{{--
|--------------------------------------------------------------------------
| Partial: _modals.blade.php
| Path:    resources/views/admin/pages/partials/_modals.blade.php
|--------------------------------------------------------------------------
| All floating / overlay UI layers rendered outside the main layout:
|
|   1. Toast notifications   — fixed top-right stack, auto-dismiss
|   2. Context menu          — right-click widget menu (fixed, positioned)
|   3. Media Library modal   — upload / URL / image grid picker
|   4. Templates modal       — save current layout, load/delete saved templates
|   5. Revisions modal       — full revision history with restore/delete
|   6. Keyboard Shortcuts    — 2-col shortcut reference grid
|   7. Accessibility Check   — issue list with error/warning badges
|   8. Page Versions modal   — named version snapshots with load/delete
|
| All modals use x-cloak so they are invisible until Alpine hydrates.
|--------------------------------------------------------------------------
--}}

{{-- ════════════════════════════════════════════════════════════════════════
     1. TOAST NOTIFICATIONS
     Fixed top-right stack; each toast slides in then auto-removes after 4s.
     Types: success (green) | error (red) | warning (yellow) | info (blue)
════════════════════════════════════════════════════════════════════════════ --}}
<div class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none" x-cloak>
  <template x-for="toast in toasts" :key="toast.id">
    <div
      class="toast pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-lg shadow-xl text-white text-sm font-medium min-w-[280px]"
      :class="{
        'bg-green-500' : toast.type === 'success',
        'bg-red-500'   : toast.type === 'error',
        'bg-yellow-500': toast.type === 'warning',
        'bg-blue-500'  : toast.type === 'info'
      }">
      <i class="fas" :class="{
        'fa-check-circle'        : toast.type === 'success',
        'fa-times-circle'        : toast.type === 'error',
        'fa-exclamation-triangle': toast.type === 'warning',
        'fa-info-circle'         : toast.type === 'info'
      }"></i>
      <span x-text="toast.message"></span>
      <button @click="removeToast(toast.id)" class="ml-auto opacity-70 hover:opacity-100">
        <i class="fas fa-times"></i>
      </button>
    </div>
  </template>
</div>

{{-- ════════════════════════════════════════════════════════════════════════
     2. CONTEXT MENU
     Right-click on any canvas widget; positioned via contextMenu.x / .y.
════════════════════════════════════════════════════════════════════════════ --}}
<div
  x-show="contextMenu.show"
  x-cloak
  :style="`left:${contextMenu.x}px; top:${contextMenu.y}px`"
  class="fixed z-[2000] bg-white dark:bg-gray-800 rounded-lg shadow-2xl border dark:border-gray-700 py-1 min-w-[160px] text-sm"
  @click.outside="contextMenu.show = false">

  <button @click="duplicateWidget(contextMenu.widgetId); contextMenu.show = false"
    class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
    <i class="fas fa-copy w-4"></i> Duplicate
  </button>
  <button @click="copyWidget(contextMenu.widgetId); contextMenu.show = false"
    class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
    <i class="fas fa-clipboard w-4"></i> Copy
  </button>
  <button @click="pasteWidget(); contextMenu.show = false"
    class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
    <i class="fas fa-paste w-4"></i> Paste
  </button>

  <hr class="my-1 dark:border-gray-700">

  <button @click="moveWidgetUp(contextMenu.widgetId); contextMenu.show = false"
    class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
    <i class="fas fa-arrow-up w-4"></i> Move Up
  </button>
  <button @click="moveWidgetDown(contextMenu.widgetId); contextMenu.show = false"
    class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
    <i class="fas fa-arrow-down w-4"></i> Move Down
  </button>

  <hr class="my-1 dark:border-gray-700">

  <button @click="deleteWidget(contextMenu.widgetId); contextMenu.show = false"
    class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-red-500 flex items-center gap-2">
    <i class="fas fa-trash w-4"></i> Delete
  </button>
</div>

{{-- ════════════════════════════════════════════════════════════════════════
     3. MEDIA LIBRARY MODAL
     Upload images or paste a URL; select one then confirm to insert.
════════════════════════════════════════════════════════════════════════════ --}}
<div x-show="showMediaLibrary" x-cloak
  class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/60">
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-[800px] max-h-[80vh] flex flex-col">

    {{-- Header --}}
    <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
      <h3 class="font-bold text-lg">Media Library</h3>
      <button @click="showMediaLibrary = false" class="text-gray-400 hover:text-gray-600">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>

    {{-- Body --}}
    <div class="p-4 flex-1 overflow-y-auto">

      {{-- Upload / URL row --}}
      <div class="flex gap-3 mb-4">
        <label class="flex items-center gap-2 px-4 py-2 bg-brand-500 text-white rounded-lg cursor-pointer hover:bg-brand-600 text-sm font-medium">
          <i class="fas fa-upload"></i> Upload Image
          <input type="file" accept="image/*" class="hidden" @change="uploadMediaImage($event)">
        </label>
        <input
          type="url"
          x-model="mediaUrlInput"
          placeholder="Or paste image URL..."
          class="flex-1 border dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700">
        <button @click="addMediaUrl()"
          class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300">
          Add URL
        </button>
      </div>

      {{-- Image grid --}}
      <div class="grid grid-cols-4 gap-3">
        <template x-for="(img, i) in mediaImages" :key="i">
          <div
            class="relative cursor-pointer rounded-lg overflow-hidden border-2 hover:border-brand-500 transition-colors"
            :class="selectedMedia === img ? 'border-brand-500' : 'border-transparent'"
            @click="selectedMedia = img">
            <img :src="img" class="w-full h-24 object-cover">
            <div x-show="selectedMedia === img"
              class="absolute inset-0 bg-brand-500/20 flex items-center justify-center">
              <i class="fas fa-check-circle text-brand-500 text-2xl"></i>
            </div>
          </div>
        </template>
      </div>

    </div>

    {{-- Footer --}}
    <div class="p-4 border-t dark:border-gray-700 flex justify-end gap-3">
      <button @click="showMediaLibrary = false"
        class="px-4 py-2 border dark:border-gray-600 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-700">
        Cancel
      </button>
      <button @click="confirmMedia()"
        class="px-4 py-2 bg-brand-500 text-white rounded-lg text-sm font-medium hover:bg-brand-600">
        Insert Image
      </button>
    </div>

  </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════
     4. TEMPLATES MODAL
     Save the current canvas as a named template; load or delete saved ones.
════════════════════════════════════════════════════════════════════════════ --}}
<div x-show="showTemplatesModal" x-cloak
  class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/60">
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-[600px] max-h-[80vh] flex flex-col">

    {{-- Header --}}
    <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
      <h3 class="font-bold text-lg">Templates Library</h3>
      <button @click="showTemplatesModal = false" class="text-gray-400 hover:text-gray-600">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>

    {{-- Save new template --}}
    <div class="p-4 border-b dark:border-gray-700">
      <div class="flex gap-2">
        <input
          x-model="newTemplateName"
          placeholder="Template name..."
          class="flex-1 border dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700">
        <button @click="saveTemplate()"
          class="px-4 py-2 bg-brand-500 text-white rounded-lg text-sm font-medium hover:bg-brand-600">
          Save Current
        </button>
      </div>
    </div>

    {{-- Template list --}}
    <div class="flex-1 overflow-y-auto p-4">
      <template x-if="templates.length === 0">
        <p class="text-center text-gray-400 py-8">No templates saved yet</p>
      </template>
      <div class="space-y-2">
        <template x-for="(tpl, i) in templates" :key="i">
          <div class="flex items-center gap-3 p-3 border dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-750">
            <i class="fas fa-file-code text-brand-500"></i>
            <div class="flex-1">
              <p class="font-medium text-sm" x-text="tpl.name"></p>
              <p class="text-xs text-gray-400" x-text="new Date(tpl.date).toLocaleDateString()"></p>
            </div>
            <button @click="loadTemplate(i)"
              class="px-3 py-1 bg-brand-500 text-white rounded text-xs hover:bg-brand-600">
              Load
            </button>
            <button @click="deleteTemplate(i)"
              class="px-3 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600">
              Delete
            </button>
          </div>
        </template>
      </div>
    </div>

  </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════
     5. REVISIONS MODAL
     Full-size revision history; restore or delete individual snapshots.
════════════════════════════════════════════════════════════════════════════ --}}
<div x-show="showRevisionsModal" x-cloak
  class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/60">
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-[560px] max-h-[80vh] flex flex-col">

    {{-- Header --}}
    <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
      <h3 class="font-bold text-lg">Revision History</h3>
      <button @click="showRevisionsModal = false" class="text-gray-400 hover:text-gray-600">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>

    {{-- Revision list --}}
    <div class="flex-1 overflow-y-auto p-4 space-y-2">
      <template x-if="revisions.length === 0">
        <p class="text-center text-gray-400 py-8">No revisions yet</p>
      </template>
      <template x-for="(rev, i) in revisions" :key="i">
        <div class="flex items-center gap-3 p-3 border dark:border-gray-700 rounded-lg">
          <i class="fas fa-history text-gray-400"></i>
          <div class="flex-1">
            <p class="font-medium text-sm" x-text="rev.label"></p>
            <p class="text-xs text-gray-400" x-text="new Date(rev.date).toLocaleString()"></p>
          </div>
          <button @click="restoreRevision(i)"
            class="px-3 py-1 bg-brand-500 text-white rounded text-xs hover:bg-brand-600">
            Restore
          </button>
          <button @click="revisions.splice(i, 1); saveRevisionsToStorage()"
            class="px-3 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600">
            Delete
          </button>
        </div>
      </template>
    </div>

    {{-- Footer --}}
    <div class="p-4 border-t dark:border-gray-700 flex justify-between">
      <button @click="saveRevision('Manual save')"
        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg text-sm hover:bg-gray-300 dark:hover:bg-gray-600">
        Save Snapshot Now
      </button>
      <button @click="showRevisionsModal = false"
        class="px-4 py-2 border dark:border-gray-600 rounded-lg text-sm">
        Close
      </button>
    </div>

  </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════
     6. KEYBOARD SHORTCUTS MODAL
     2-column grid of all shortcut key bindings.
════════════════════════════════════════════════════════════════════════════ --}}
<div x-show="showShortcutsModal" x-cloak
  class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/60">
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-[480px]">

    {{-- Header --}}
    <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
      <h3 class="font-bold text-lg">Keyboard Shortcuts</h3>
      <button @click="showShortcutsModal = false" class="text-gray-400 hover:text-gray-600">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>

    {{-- Shortcut grid --}}
    <div class="p-4 grid grid-cols-2 gap-2 text-sm">
      <template x-for="sc in shortcuts" :key="sc.key">
        <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
          <span x-text="sc.desc"></span>
          <kbd class="px-2 py-0.5 bg-white dark:bg-gray-600 border dark:border-gray-500 rounded text-xs font-mono shadow"
            x-text="sc.key">
          </kbd>
        </div>
      </template>
    </div>

  </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════
     7. ACCESSIBILITY CHECK MODAL
     Lists errors and warnings found by runA11yCheck(); re-run button included.
════════════════════════════════════════════════════════════════════════════ --}}
<div x-show="showA11yModal" x-cloak
  class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/60">
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-[560px] max-h-[80vh] flex flex-col">

    {{-- Header --}}
    <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
      <h3 class="font-bold text-lg flex items-center gap-2">
        <i class="fas fa-universal-access text-purple-500"></i> Accessibility Check
      </h3>
      <button @click="showA11yModal = false" class="text-gray-400 hover:text-gray-600">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>

    {{-- Issue list --}}
    <div class="flex-1 overflow-y-auto p-4 space-y-2">

      {{-- All clear --}}
      <template x-if="a11yIssues.length === 0">
        <div class="text-center py-8 text-green-500">
          <i class="fas fa-check-circle text-4xl mb-2 block"></i>
          <p class="font-semibold">No issues found!</p>
        </div>
      </template>

      {{-- Issue rows --}}
      <template x-for="issue in a11yIssues" :key="issue.id">
        <div
          class="flex items-start gap-3 p-3 rounded-lg border"
          :class="issue.level === 'error'
            ? 'border-red-200 bg-red-50 dark:bg-red-900/20'
            : 'border-yellow-200 bg-yellow-50 dark:bg-yellow-900/20'">
          <i class="fas mt-0.5"
            :class="issue.level === 'error'
              ? 'fa-times-circle text-red-500'
              : 'fa-exclamation-triangle text-yellow-500'">
          </i>
          <div class="flex-1">
            <p class="font-semibold text-sm" x-text="issue.title"></p>
            <p class="text-xs text-gray-500 mt-0.5" x-text="issue.desc"></p>
          </div>
          <span
            class="text-xs px-2 py-0.5 rounded font-bold"
            :class="issue.level === 'error'
              ? 'bg-red-100 text-red-600'
              : 'bg-yellow-100 text-yellow-700'"
            x-text="issue.level.toUpperCase()">
          </span>
        </div>
      </template>

    </div>

    {{-- Footer --}}
    <div class="p-4 border-t dark:border-gray-700 flex justify-between items-center">
      <span class="text-xs text-gray-400" x-text="`${a11yIssues.length} issue(s) found`"></span>
      <button @click="runA11yCheck()"
        class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700">
        Re-check
      </button>
    </div>

  </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════
     8. PAGE VERSIONS MODAL
     Named snapshots (up to 10); save with Enter or button, load or delete.
════════════════════════════════════════════════════════════════════════════ --}}
<div x-show="showVersionsModal" x-cloak
  class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/60">
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-[560px] max-h-[80vh] flex flex-col">

    {{-- Header --}}
    <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
      <h3 class="font-bold text-lg flex items-center gap-2">
        <i class="fas fa-code-branch text-brand-500"></i> Page Versions
      </h3>
      <button @click="showVersionsModal = false" class="text-gray-400 hover:text-gray-600">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>

    {{-- Save new version --}}
    <div class="p-4 border-b dark:border-gray-700 flex gap-2">
      <input
        x-model="newVersionName"
        placeholder="Version name (e.g. v1.0 Homepage)..."
        class="flex-1 border dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700"
        @keydown.enter="saveVersion()">
      <button @click="saveVersion()"
        class="px-4 py-2 bg-brand-500 text-white rounded-lg text-sm font-medium hover:bg-brand-600">
        Save
      </button>
    </div>

    {{-- Version list --}}
    <div class="flex-1 overflow-y-auto p-4 space-y-2">
      <template x-if="pageVersions.length === 0">
        <p class="text-center text-gray-400 py-8">No versions saved yet</p>
      </template>
      <template x-for="(v, i) in pageVersions" :key="i">
        <div class="flex items-center gap-3 p-3 border dark:border-gray-700 rounded-lg transition-colors" 
          :class="i === 0 ? 'border-green-400 bg-green-50 dark:bg-green-900/20' : ''">
          <i class="fas fa-code-branch" :class="i === 0 ? 'text-green-500' : 'text-brand-400'"></i>
          <div class="flex-1">
            <p class="font-semibold text-sm" x-text="v.name"></p>
            <div class="flex items-center gap-2">
              <p class="text-xs text-gray-400"
                x-text="new Date(v.date).toLocaleString() + ' · ' + v.components.length + ' widgets'">
              </p>
              <span x-show="i === 0" class="text-xs px-2 py-0.5 rounded bg-green-200 dark:bg-green-800 text-green-700 dark:text-green-200 font-medium">Current</span>
            </div>
          </div>
          <button @click="loadVersion(i)"
            class="px-3 py-1 bg-brand-500 text-white rounded text-xs hover:bg-brand-600"
            :disabled="i === 0">
            Load
          </button>
          <button @click="pageVersions.splice(i, 1); saveVersionsToStorage()"
            class="px-3 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600">
            Del
          </button>
        </div>
      </template>
    </div>

  </div>
</div>
