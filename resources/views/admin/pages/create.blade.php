{{--
|--------------------------------------------------------------------------
| CMS Pro Builder - create.blade.php
|--------------------------------------------------------------------------
| Laravel Usage Instructions:
|
| 1. ROUTE (routes/web.php):
|    Route::get('/admin/pages/create', [PageController::class, 'create'])->name('admin.pages.create');
|    Route::post('/admin/pages', [PageController::class, 'store'])->name('admin.pages.store');
|    Route::get('/admin/pages/{page}/edit', [PageController::class, 'edit'])->name('admin.pages.edit');
|    Route::put('/admin/pages/{page}', [PageController::class, 'update'])->name('admin.pages.update');
|
| 2. CONTROLLER (app/Http/Controllers/PageController.php):
|    public function create() { return view('admin.pages.create'); }
|    public function store(Request $request) {
|        $page = Page::create([
|            'title'            => $request->title,
|            'slug'             => $request->slug,
|            'content'          => $request->content,
|            'status'           => $request->status ?? 'draft',
|            'meta_title'       => $request->meta_title,
|            'meta_description' => $request->meta_description,
|        ]);
|        return response()->json(['success' => true, 'page_id' => $page->id]);
|    }
|    public function edit(Page $page) { return view('admin.pages.create', compact('page')); }
|    public function update(Request $request, Page $page) {
|        $page->update($request->only(['title','slug','content','status','meta_title','meta_description']));
|        return response()->json(['success' => true, 'page_id' => $page->id]);
|    }
|
| 3. MODEL (app/Models/Page.php):
|    protected $fillable = ['title','slug','content','status','meta_title','meta_description'];
|
| 4. MIGRATION:
|    Schema::create('pages', function (Blueprint $table) {
|        $table->id();
|        $table->string('title');
|        $table->string('slug')->unique();
|        $table->longText('content')->nullable();
|        $table->string('status')->default('draft');
|        $table->string('meta_title')->nullable();
|        $table->text('meta_description')->nullable();
|        $table->timestamps();
|    });
|
| 5. FILE PATH: resources/views/admin/pages/create.blade.php
|
| 6. EDIT MODE: Pass $page from controller and page-id meta tag auto-fills.
|    Add in blade: <meta name="page-id" content="{{ $page->id ?? '' }}">
|--------------------------------------------------------------------------
--}}
<!DOCTYPE html>
<html lang="en" x-data="pageBuilderV5()" x-init="init()" :class="darkMode ? 'dark' : ''" class="h-full">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="page-id" content="{{ isset($page) ? $page->id : '' }}">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ isset($page) ? $page->title : "Create Page" }} | CMS Pro Builder</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
const initialHeaderTemplates = @json($headerTemplates ?? []);
const initialFooterTemplates = @json($footerTemplates ?? []);
const initialMenus = @json($menus ?? []);
const initialPageData = @json(isset($page) ? json_decode($page->content, true) : null);

tailwind.config = {
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        brand: { 50:'#f0f9ff', 100:'#e0f2fe', 500:'#0ea5e9', 600:'#0284c7', 700:'#0369a1' }
      }
    }
  }
}
</script>
<style>
  [x-cloak] { display: none !important; }
  .canvas-widget { position: relative; }
  .canvas-widget:hover > .widget-toolbar { display: flex !important; }
  .widget-toolbar { display: none; }
  .widget-selected { outline: 2px solid #0ea5e9 !important; outline-offset: 2px; }
  .drop-zone-active { background: rgba(14,165,233,0.08) !important; border: 2px dashed #0ea5e9 !important; }
  .sortable-ghost { opacity: 0.4; background: #c8ebfb; }
  .sortable-drag { opacity: 0.8; }
  .progress-bar-fill { transition: width 0.6s ease; }
  .circle-progress { transform: rotate(-90deg); }
  .before-after-slider { position: relative; overflow: hidden; }
  .before-after-handle { position: absolute; top: 0; bottom: 0; width: 4px; background: white; cursor: ew-resize; }
  .tinymce-active { min-height: 60px; }
  ::-webkit-scrollbar { width: 6px; height: 6px; }
  ::-webkit-scrollbar-track { background: transparent; }
  ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
  .dark ::-webkit-scrollbar-thumb { background: #475569; }
  .panel-tab.active { border-bottom: 2px solid #0ea5e9; color: #0ea5e9; }
  .widget-lib-item { cursor: grab; }
  .widget-lib-item:active { cursor: grabbing; }
  @keyframes spin { to { transform: rotate(360deg); } }
  @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.5} }
  .animate-spin { animation: spin 1s linear infinite; }
  .animate-pulse { animation: pulse 2s cubic-bezier(0.4,0,0.6,1) infinite; }
  @keyframes slideIn { from{transform:translateX(100%);opacity:0} to{transform:translateX(0);opacity:1} }
  .toast { animation: slideIn 0.3s ease; }
  .canvas-zoom { transform-origin: top center; }
  .nested-dropzone { min-height: 60px; border: 2px dashed #94a3b8; border-radius: 4px; }
  .nested-dropzone.drag-over { background: rgba(14,165,233,0.1); border-color: #0ea5e9; }
  .col-dropzone { min-height: 80px; flex: 1; border: 1px dashed #cbd5e1; border-radius: 4px; padding: 4px; }
  /* Drag handle */
  .drag-handle { cursor: grab; color: #94a3b8; transition: color 0.15s; }
  .drag-handle:hover { color: #0ea5e9; }
  .drag-handle:active { cursor: grabbing; }
  .sortable-drag { opacity: 0.85; box-shadow: 0 8px 30px rgba(14,165,233,0.25); }
  /* Snap grid */
  .snap-grid { background-image: radial-gradient(circle, #cbd5e1 1px, transparent 1px); background-size: 20px 20px; }
  .dark .snap-grid { background-image: radial-gradient(circle, #374151 1px, transparent 1px); }
  /* Spacing controls */
  .spacing-box { display:grid; grid-template-columns:1fr 1fr 1fr; grid-template-rows:1fr 1fr 1fr; gap:2px; width:140px; margin:0 auto; }
  .spacing-box input { width:100%; text-align:center; font-size:10px; padding:2px; border:1px solid #e2e8f0; border-radius:3px; background:white; }
  .dark .spacing-box input { background:#374151; border-color:#4b5563; color:#f1f5f9; }
  /* Accessibility warning */
  .a11y-warn { outline: 2px dashed #f59e0b !important; }
  @keyframes fadeIn { from{opacity:0;transform:translateY(-4px)} to{opacity:1;transform:translateY(0)} }
  .fadeIn { animation: fadeIn 0.2s ease; }
  /* Hover state preview via CSS vars */
  [data-hover-bg]:hover { background-color: var(--hover-bg) !important; }
  [data-hover-color]:hover { color: var(--hover-color) !important; }
  /* Resize handle visual */
  .resize-indicator { position:absolute; bottom:2px; right:2px; width:10px; height:10px; cursor:se-resize; opacity:0; border-bottom:2px solid #0ea5e9; border-right:2px solid #0ea5e9; transition:opacity 0.2s; }
  .canvas-widget:hover .resize-indicator { opacity:1; }
</style>
</head>
<body class="h-full bg-gray-100 dark:bg-gray-950 text-gray-800 dark:text-gray-100 font-sans overflow-hidden" @keydown.window="handleKeydown($event)" @beforeunload.window="handleBeforeUnload($event)">

<!-- TOAST NOTIFICATIONS -->
<div class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none" x-cloak>
  <template x-for="toast in toasts" :key="toast.id">
    <div class="toast pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-lg shadow-xl text-white text-sm font-medium min-w-[280px]"
      :class="{'bg-green-500':toast.type==='success','bg-red-500':toast.type==='error','bg-yellow-500':toast.type==='warning','bg-blue-500':toast.type==='info'}">
      <i class="fas" :class="{'fa-check-circle':toast.type==='success','fa-times-circle':toast.type==='error','fa-exclamation-triangle':toast.type==='warning','fa-info-circle':toast.type==='info'}"></i>
      <span x-text="toast.message"></span>
      <button @click="removeToast(toast.id)" class="ml-auto opacity-70 hover:opacity-100"><i class="fas fa-times"></i></button>
    </div>
  </template>
</div>

<!-- MEDIA LIBRARY MODAL -->
<div x-show="showMediaLibrary" x-cloak class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/60">
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-[800px] max-h-[80vh] flex flex-col">
    <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
      <h3 class="font-bold text-lg">Media Library</h3>
      <button @click="showMediaLibrary=false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
    </div>
    <div class="p-4 flex-1 overflow-y-auto">
      <div class="flex gap-3 mb-4">
        <label class="flex items-center gap-2 px-4 py-2 bg-brand-500 text-white rounded-lg cursor-pointer hover:bg-brand-600 text-sm font-medium">
          <i class="fas fa-upload"></i> Upload Image
          <input type="file" accept="image/*" class="hidden" @change="uploadMediaImage($event)">
        </label>
        <input type="url" x-model="mediaUrlInput" placeholder="Or paste image URL..." class="flex-1 border dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700">
        <button @click="addMediaUrl()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300">Add URL</button>
      </div>
      <div class="grid grid-cols-4 gap-3">
        <template x-for="(img, i) in mediaImages" :key="i">
          <div class="relative cursor-pointer rounded-lg overflow-hidden border-2 hover:border-brand-500 transition-colors"
            :class="selectedMedia===img ? 'border-brand-500' : 'border-transparent'"
            @click="selectedMedia=img">
            <img :src="img" class="w-full h-24 object-cover">
            <div x-show="selectedMedia===img" class="absolute inset-0 bg-brand-500/20 flex items-center justify-center">
              <i class="fas fa-check-circle text-brand-500 text-2xl"></i>
            </div>
          </div>
        </template>
      </div>
    </div>
    <div class="p-4 border-t dark:border-gray-700 flex justify-end gap-3">
      <button @click="showMediaLibrary=false" class="px-4 py-2 border dark:border-gray-600 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Cancel</button>
      <button @click="confirmMedia()" class="px-4 py-2 bg-brand-500 text-white rounded-lg text-sm font-medium hover:bg-brand-600">Insert Image</button>
    </div>
  </div>
</div>

<!-- AI ASSISTANT MODAL -->


<!-- TEMPLATES MODAL -->
<div x-show="showTemplatesModal" x-cloak class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/60">
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-[600px] max-h-[80vh] flex flex-col">
    <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
      <h3 class="font-bold text-lg">Templates Library</h3>
      <button @click="showTemplatesModal=false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
    </div>
    <div class="p-4 border-b dark:border-gray-700">
      <div class="flex gap-2">
        <input x-model="newTemplateName" placeholder="Template name..." class="flex-1 border dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700">
        <button @click="saveTemplate()" class="px-4 py-2 bg-brand-500 text-white rounded-lg text-sm font-medium hover:bg-brand-600">Save Current</button>
      </div>
    </div>
    <div class="flex-1 overflow-y-auto p-4">
      <template x-if="templates.length===0">
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
            <button @click="loadTemplate(i)" class="px-3 py-1 bg-brand-500 text-white rounded text-xs hover:bg-brand-600">Load</button>
            <button @click="deleteTemplate(i)" class="px-3 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600">Delete</button>
          </div>
        </template>
      </div>
    </div>
  </div>
</div>

<!-- REVISIONS MODAL -->
<div x-show="showRevisionsModal" x-cloak class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/60">
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-[560px] max-h-[80vh] flex flex-col">
    <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
      <h3 class="font-bold text-lg">Revision History</h3>
      <button @click="showRevisionsModal=false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
    </div>
    <div class="flex-1 overflow-y-auto p-4 space-y-2">
      <template x-if="revisions.length===0">
        <p class="text-center text-gray-400 py-8">No revisions yet</p>
      </template>
      <template x-for="(rev, i) in revisions" :key="i">
        <div class="flex items-center gap-3 p-3 border dark:border-gray-700 rounded-lg">
          <i class="fas fa-history text-gray-400"></i>
          <div class="flex-1">
            <p class="font-medium text-sm" x-text="rev.label"></p>
            <p class="text-xs text-gray-400" x-text="new Date(rev.date).toLocaleString()"></p>
          </div>
          <button @click="restoreRevision(i)" class="px-3 py-1 bg-brand-500 text-white rounded text-xs hover:bg-brand-600">Restore</button>
          <button @click="revisions.splice(i,1); saveRevisionsToStorage()" class="px-3 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600">Delete</button>
        </div>
      </template>
    </div>
    <div class="p-4 border-t dark:border-gray-700 flex justify-between">
      <button @click="saveRevision('Manual save')" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg text-sm hover:bg-gray-300 dark:hover:bg-gray-600">Save Snapshot Now</button>
      <button @click="showRevisionsModal=false" class="px-4 py-2 border dark:border-gray-600 rounded-lg text-sm">Close</button>
    </div>
  </div>
</div>

<!-- KEYBOARD SHORTCUTS MODAL -->
<div x-show="showShortcutsModal" x-cloak class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/60">
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-[480px]">
    <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
      <h3 class="font-bold text-lg">Keyboard Shortcuts</h3>
      <button @click="showShortcutsModal=false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
    </div>
    <div class="p-4 grid grid-cols-2 gap-2 text-sm">
      <template x-for="sc in shortcuts" :key="sc.key">
        <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
          <span x-text="sc.desc"></span>
          <kbd class="px-2 py-0.5 bg-white dark:bg-gray-600 border dark:border-gray-500 rounded text-xs font-mono shadow" x-text="sc.key"></kbd>
        </div>
      </template>
    </div>
  </div>
</div>

<!-- ACCESSIBILITY CHECK MODAL -->
<div x-show="showA11yModal" x-cloak class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/60">
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-[560px] max-h-[80vh] flex flex-col">
    <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
      <h3 class="font-bold text-lg flex items-center gap-2"><i class="fas fa-universal-access text-purple-500"></i> Accessibility Check</h3>
      <button @click="showA11yModal=false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
    </div>
    <div class="flex-1 overflow-y-auto p-4 space-y-2">
      <template x-if="a11yIssues.length===0">
        <div class="text-center py-8 text-green-500"><i class="fas fa-check-circle text-4xl mb-2 block"></i><p class="font-semibold">No issues found!</p></div>
      </template>
      <template x-for="issue in a11yIssues" :key="issue.id">
        <div class="flex items-start gap-3 p-3 rounded-lg border" :class="issue.level==='error'?'border-red-200 bg-red-50 dark:bg-red-900/20':'border-yellow-200 bg-yellow-50 dark:bg-yellow-900/20'">
          <i class="fas mt-0.5" :class="issue.level==='error'?'fa-times-circle text-red-500':'fa-exclamation-triangle text-yellow-500'"></i>
          <div class="flex-1"><p class="font-semibold text-sm" x-text="issue.title"></p><p class="text-xs text-gray-500 mt-0.5" x-text="issue.desc"></p></div>
          <span class="text-xs px-2 py-0.5 rounded font-bold" :class="issue.level==='error'?'bg-red-100 text-red-600':'bg-yellow-100 text-yellow-700'" x-text="issue.level.toUpperCase()"></span>
        </div>
      </template>
    </div>
    <div class="p-4 border-t dark:border-gray-700 flex justify-between items-center">
      <span class="text-xs text-gray-400" x-text="`${a11yIssues.length} issue(s) found`"></span>
      <button @click="runA11yCheck()" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700">Re-check</button>
    </div>
  </div>
</div>

<!-- PAGE VERSIONS MODAL -->
<!-- CONTEXT MENU -->
<div x-show="contextMenu.show" x-cloak
  :style="`left:${contextMenu.x}px;top:${contextMenu.y}px`"
  class="fixed z-[2000] bg-white dark:bg-gray-800 rounded-lg shadow-2xl border dark:border-gray-700 py-1 min-w-[160px] text-sm"
  @click.outside="contextMenu.show=false">
  <button @click="duplicateWidget(contextMenu.widgetId);contextMenu.show=false" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"><i class="fas fa-copy w-4"></i> Duplicate</button>
  <button @click="copyWidget(contextMenu.widgetId);contextMenu.show=false" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"><i class="fas fa-clipboard w-4"></i> Copy</button>
  <button @click="pasteWidget();contextMenu.show=false" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"><i class="fas fa-paste w-4"></i> Paste</button>
  <hr class="my-1 dark:border-gray-700">
  <button @click="moveWidgetUp(contextMenu.widgetId);contextMenu.show=false" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"><i class="fas fa-arrow-up w-4"></i> Move Up</button>
  <button @click="moveWidgetDown(contextMenu.widgetId);contextMenu.show=false" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"><i class="fas fa-arrow-down w-4"></i> Move Down</button>
  <hr class="my-1 dark:border-gray-700">
  <button @click="deleteWidget(contextMenu.widgetId);contextMenu.show=false" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-red-500 flex items-center gap-2"><i class="fas fa-trash w-4"></i> Delete</button>
</div>

<!-- MAIN LAYOUT -->
<div class="flex flex-col h-full">

  <!-- TOP BAR -->
  <header class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-900 border-b dark:border-gray-700 shadow-sm flex-shrink-0 z-50">
    <!-- Logo -->
    <div class="flex items-center gap-2 mr-4">
      <div class="w-8 h-8 bg-gradient-to-br from-brand-500 to-purple-600 rounded-lg flex items-center justify-center">
        <i class="fas fa-layer-group text-white text-sm"></i>
      </div>
      <span class="font-bold text-sm tracking-tight hidden sm:block">CMS Pro Builder</span>
    </div>

    <!-- Undo/Redo -->
    <button @click="undo()" :disabled="undoStack.length===0" class="p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 disabled:opacity-40 text-sm" title="Undo (Ctrl+Z)"><i class="fas fa-undo"></i></button>
    <button @click="redo()" :disabled="redoStack.length===0" class="p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 disabled:opacity-40 text-sm" title="Redo (Ctrl+Y)"><i class="fas fa-redo"></i></button>

    <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>

    <!-- Responsive Preview -->
    <div class="flex bg-gray-100 dark:bg-gray-800 rounded-lg p-0.5 gap-0.5">
      <button @click="previewMode='desktop'" :class="previewMode==='desktop'?'bg-white dark:bg-gray-700 shadow':''" class="p-1.5 rounded text-xs" title="Desktop"><i class="fas fa-desktop"></i></button>
      <button @click="previewMode='tablet'" :class="previewMode==='tablet'?'bg-white dark:bg-gray-700 shadow':''" class="p-1.5 rounded text-xs" title="Tablet"><i class="fas fa-tablet-alt"></i></button>
      <button @click="previewMode='mobile'" :class="previewMode==='mobile'?'bg-white dark:bg-gray-700 shadow':''" class="p-1.5 rounded text-xs" title="Mobile"><i class="fas fa-mobile-alt"></i></button>
    </div>

    <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>

    <!-- Preview Mode Toggle -->
    <button @click="livePreview=!livePreview" :class="livePreview?'bg-green-500 text-white':'bg-gray-100 dark:bg-gray-800'" class="px-3 py-1.5 rounded-lg text-xs font-medium flex items-center gap-1.5 hover:opacity-90">
      <i class="fas" :class="livePreview?'fa-eye-slash':'fa-eye'"></i>
      <span x-text="livePreview?'Exit Preview':'Preview'"></span>
    </button>

    <!-- AI Assistant -->
 

    <div class="flex-1"></div>

    <!-- Auto-save indicator -->
    <span x-show="autoSaveIndicator" x-cloak class="text-xs text-green-500 flex items-center gap-1 animate-pulse">
      <i class="fas fa-circle text-[8px]"></i> Saved
    </span>
    <span x-show="isDirty && !autoSaveIndicator" class="text-xs text-yellow-500 flex items-center gap-1">
      <i class="fas fa-circle text-[8px]"></i> Unsaved
    </span>

    <!-- Snap Grid Toggle -->
    <button @click="snapGrid=!snapGrid" :class="snapGrid?'bg-brand-100 dark:bg-brand-900 text-brand-600':''" class="p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-sm" title="Toggle Snap Grid">
      <i class="fas fa-th-large"></i>
    </button>

    <!-- A11y Check -->
    <button @click="runA11yCheck();showA11yModal=true" class="p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-sm" title="Accessibility Check">
      <i class="fas fa-universal-access"></i>
    </button>

    <!-- Dark Mode -->
    <button @click="toggleDarkMode()" class="p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-sm">
      <i class="fas" :class="darkMode?'fa-sun':'fa-moon'"></i>
    </button>

    <!-- Shortcuts -->
    <button @click="showShortcutsModal=true" class="p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-sm" title="Keyboard Shortcuts">
      <i class="fas fa-question-circle"></i>
    </button>

    <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>

    <!-- Export/Import -->
    <button @click="exportJSON()" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 flex items-center gap-1.5">
      <i class="fas fa-download"></i> Export
    </button>
    <label class="px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 flex items-center gap-1.5 cursor-pointer">
      <i class="fas fa-upload"></i> Import
      <input type="file" accept=".json" class="hidden" @change="importJSON($event)">
    </label>

    <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>

    <!-- Save & Publish -->
    <button @click="savePage()" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 flex items-center gap-1.5">
      <i class="fas fa-save"></i> Save
    </button>
    <button @click="publishPage()" class="px-4 py-1.5 rounded-lg text-xs font-bold bg-brand-500 text-white hover:bg-brand-600 flex items-center gap-1.5">
      <i class="fas fa-globe"></i> Publish
    </button>
  </header>

  <!-- MAIN CONTENT AREA -->
  <div class="flex flex-1 overflow-hidden">

    <!-- LEFT PANEL -->
    <aside x-show="!livePreview" class="w-64 flex-shrink-0 bg-white dark:bg-gray-900 border-r dark:border-gray-700 flex flex-col overflow-hidden">
      <!-- Left Panel Tabs -->
      <div class="flex border-b dark:border-gray-700 text-xs font-medium">
        <button @click="leftTab='widgets'" :class="leftTab==='widgets'?'border-b-2 border-brand-500 text-brand-500':'text-gray-500 hover:text-gray-700'" class="flex-1 py-2.5 px-1 text-center">Widgets</button>
        <button @click="leftTab='styles'" :class="leftTab==='styles'?'border-b-2 border-brand-500 text-brand-500':'text-gray-500 hover:text-gray-700'" class="flex-1 py-2.5 px-1 text-center">Styles</button>
        <button @click="leftTab='tree'" :class="leftTab==='tree'?'border-b-2 border-brand-500 text-brand-500':'text-gray-500 hover:text-gray-700'" class="flex-1 py-2.5 px-1 text-center">Tree</button>
        <button @click="leftTab='revisions'" :class="leftTab==='revisions'?'border-b-2 border-brand-500 text-brand-500':'text-gray-500 hover:text-gray-700'" class="flex-1 py-2.5 px-1 text-center">Rev.</button>
      </div>

      <!-- Widgets Tab -->
      <div x-show="leftTab==='widgets'" class="flex-1 flex flex-col overflow-hidden">
        <div class="p-2">
          <div class="relative">
            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
            <input x-model="widgetSearch" type="text" placeholder="Search widgets..." class="w-full pl-8 pr-3 py-2 border dark:border-gray-600 rounded-lg text-xs dark:bg-gray-800 focus:outline-none focus:ring-1 focus:ring-brand-500">
          </div>
        </div>
        <div class="flex-1 overflow-y-auto px-2 pb-2 space-y-2">
          <template x-for="cat in filteredWidgetCategories()" :key="cat.name">
            <div>
              <button @click="cat.open=!cat.open" class="flex items-center justify-between w-full py-1.5 px-2 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide hover:text-gray-700 dark:hover:text-gray-200">
                <span x-text="cat.name"></span>
                <i class="fas" :class="cat.open?'fa-chevron-down':'fa-chevron-right'" class="text-[10px]"></i>
              </button>
              <div x-show="cat.open" class="grid grid-cols-2 gap-1">
                <template x-for="widget in cat.widgets" :key="widget.type">
                  <div class="widget-lib-item flex flex-col items-center gap-1 p-2 border dark:border-gray-700 rounded-lg hover:border-brand-500 hover:bg-brand-50 dark:hover:bg-brand-900/20 cursor-grab transition-colors text-center"
                    draggable="true"
                    @dragstart="startDragFromLibrary($event, widget)"
                    @click="addWidgetToCanvas(widget)">
                    <i class="fas text-brand-500 text-sm" :class="widget.icon"></i>
                    <span class="text-[10px] text-gray-600 dark:text-gray-400 leading-tight" x-text="widget.label"></span>
                  </div>
                </template>
              </div>
            </div>
          </template>
        </div>
      </div>

      <!-- Global Styles Tab -->
      <div x-show="leftTab==='styles'" class="flex-1 overflow-y-auto p-3 space-y-4">
        <div>
          <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Global Colors</h4>
          <div class="space-y-2">
            <div class="flex items-center gap-2">
              <label class="text-xs text-gray-600 dark:text-gray-400 w-20">Primary</label>
              <input type="color" x-model="globalStyles.primaryColor" class="w-8 h-8 rounded cursor-pointer border-0">
              <span class="text-xs font-mono text-gray-500" x-text="globalStyles.primaryColor"></span>
            </div>
            <div class="flex items-center gap-2">
              <label class="text-xs text-gray-600 dark:text-gray-400 w-20">Secondary</label>
              <input type="color" x-model="globalStyles.secondaryColor" class="w-8 h-8 rounded cursor-pointer border-0">
              <span class="text-xs font-mono text-gray-500" x-text="globalStyles.secondaryColor"></span>
            </div>
            <div class="flex items-center gap-2">
              <label class="text-xs text-gray-600 dark:text-gray-400 w-20">Accent</label>
              <input type="color" x-model="globalStyles.accentColor" class="w-8 h-8 rounded cursor-pointer border-0">
              <span class="text-xs font-mono text-gray-500" x-text="globalStyles.accentColor"></span>
            </div>
          </div>
        </div>
        <div>
          <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Typography</h4>
          <select x-model="globalStyles.fontFamily" class="w-full border dark:border-gray-600 rounded-lg px-2 py-1.5 text-xs dark:bg-gray-800">
            <option value="Inter, sans-serif">Inter</option>
            <option value="Georgia, serif">Georgia</option>
            <option value="'Playfair Display', serif">Playfair Display</option>
            <option value="'Roboto', sans-serif">Roboto</option>
            <option value="'Montserrat', sans-serif">Montserrat</option>
            <option value="'Open Sans', sans-serif">Open Sans</option>
          </select>
        </div>
        <div>
          <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Page Background</h4>
          <div class="space-y-2">
            <div class="flex items-center gap-2">
              <label class="text-xs text-gray-600 dark:text-gray-400 w-16">Color</label>
              <input type="color" x-model="globalStyles.bgColor" class="w-8 h-8 rounded cursor-pointer border-0">
            </div>
            <div>
              <label class="text-xs text-gray-600 dark:text-gray-400 block mb-1">Image URL</label>
              <input type="url" x-model="globalStyles.bgImage" placeholder="https://..." class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800">
            </div>
            <div>
              <label class="text-xs text-gray-600 dark:text-gray-400 block mb-1">BG Size</label>
              <select x-model="globalStyles.bgSize" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800">
                <option value="cover">Cover</option>
                <option value="contain">Contain</option>
                <option value="auto">Auto</option>
              </select>
            </div>
          </div>
        </div>
        <div>
          <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Templates</h4>
          <button @click="showTemplatesModal=true" class="w-full py-2 bg-brand-500 text-white rounded-lg text-xs font-medium hover:bg-brand-600">
            <i class="fas fa-layer-group mr-1"></i> Manage Templates
          </button>
        </div>
      </div>

      <!-- Tree View Tab -->
      <div x-show="leftTab==='tree'" class="flex-1 overflow-y-auto p-3">
        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Component Tree</h4>
        <div class="space-y-1">
          <template x-if="components.length===0">
            <p class="text-xs text-gray-400 text-center py-4">No components yet</p>
          </template>
          <template x-for="(comp, i) in components" :key="comp.id">
            <div>
              <div class="flex items-center gap-1.5 py-1 px-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer text-xs"
                :class="selectedId===comp.id?'bg-brand-50 dark:bg-brand-900/20 text-brand-600':''"
                @click="selectWidget(comp.id)">
                <i class="fas fa-grip-dots text-gray-300 text-[10px]"></i>
                <i class="fas text-brand-400 text-[10px]" :class="getWidgetIcon(comp.type)"></i>
                <span class="truncate flex-1" x-text="comp.settings.label||comp.type"></span>
                <button @click.stop="deleteWidget(comp.id)" class="text-gray-300 hover:text-red-400 text-[10px]"><i class="fas fa-times"></i></button>
              </div>
              <!-- Nested children in tree -->
              <template x-if="comp.children && comp.children.length">
                <div class="ml-4 border-l dark:border-gray-700 pl-2 space-y-0.5">
                  <template x-for="child in comp.children" :key="child.id">
                    <div class="flex items-center gap-1.5 py-0.5 px-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer text-xs"
                      :class="selectedId===child.id?'bg-brand-50 dark:bg-brand-900/20 text-brand-600':''"
                      @click="selectWidget(child.id)">
                      <i class="fas text-gray-300 text-[10px]" :class="getWidgetIcon(child.type)"></i>
                      <span class="truncate" x-text="child.settings.label||child.type"></span>
                    </div>
                  </template>
                </div>
              </template>
            </div>
          </template>
        </div>
      </div>

      <!-- Revisions Tab -->
      <div x-show="leftTab==='revisions'" class="flex-1 overflow-y-auto p-3">
        <div class="flex items-center justify-between mb-2">
          <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide">Revisions</h4>
          <button @click="saveRevision('Manual')" class="text-xs text-brand-500 hover:text-brand-600">+ Save</button>
        </div>
        <div class="space-y-1">
          <template x-if="revisions.length===0">
            <p class="text-xs text-gray-400 text-center py-4">No revisions</p>
          </template>
          <template x-for="(rev, i) in revisions" :key="i">
            <div class="flex items-center gap-2 py-1.5 px-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-xs">
              <i class="fas fa-history text-gray-400"></i>
              <div class="flex-1 min-w-0">
                <p class="font-medium truncate" x-text="rev.label"></p>
                <p class="text-gray-400 text-[10px]" x-text="new Date(rev.date).toLocaleString()"></p>
              </div>
              <button @click="restoreRevision(i)" class="text-brand-500 hover:text-brand-600 font-medium">↩</button>
            </div>
          </template>
        </div>
      </div>
    </aside>

    <!-- CANVAS AREA -->
    <main class="flex-1 overflow-auto relative bg-gray-200 dark:bg-gray-800" @click.self="selectedId=null">

      <!-- Canvas wrapper -->
      <div class="mx-auto py-8 px-4" :style="canvasContainerStyle()">
        <div class="canvas-zoom bg-white dark:bg-gray-900 shadow-xl rounded-lg min-h-[600px] relative"
          :class="snapGrid ? 'snap-grid' : ''"
          :style="`transform:scale(${canvasZoom/100});transform-origin:top center;${getPageBgStyle()};font-family:${globalStyles.fontFamily}`"
          id="canvas-root"
          @dragover.prevent="onCanvasDragOver($event)"
          @drop.prevent="dropOnCanvas($event)"
          @contextmenu.prevent="showCanvasContextMenu($event)">

          <!-- Empty state -->
          <div x-show="components.length===0" class="flex flex-col items-center justify-center py-32 text-center">
            <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
              <i class="fas fa-layer-group text-4xl text-gray-300"></i>
            </div>
            <p class="text-gray-400 font-medium mb-1">Drop widgets here to start building</p>
            <p class="text-gray-300 text-sm">Or click any widget in the left panel</p>
          </div>

          <!-- Widget Renderer -->
          <div id="sortable-canvas">
            <template x-for="(comp, idx) in components" :key="comp.id">
              <div class="canvas-widget relative"
                :class="selectedId===comp.id&&!livePreview?'widget-selected':''"
                :data-id="comp.id"
                @click.stop="!livePreview&&selectWidget(comp.id)"
                @contextmenu.prevent="!livePreview&&openContextMenu($event,comp.id)">

                <!-- Widget Toolbar -->
                <div x-show="!livePreview" class="widget-toolbar absolute -top-8 right-0 z-20 flex items-center gap-1 bg-brand-500 text-white rounded-t-lg px-2 py-1 text-xs">
                  <i class="fas fa-grip-lines drag-handle mr-1 text-white/70" title="Drag to reorder"></i>
                  <span class="mr-1 opacity-70 font-mono" x-text="comp.type"></span>
                  <button @click.stop="moveWidgetUp(comp.id)" title="Move Up (Alt+↑)" class="hover:bg-brand-600 px-1 py-0.5 rounded"><i class="fas fa-arrow-up"></i></button>
                  <button @click.stop="moveWidgetDown(comp.id)" title="Move Down (Alt+↓)" class="hover:bg-brand-600 px-1 py-0.5 rounded"><i class="fas fa-arrow-down"></i></button>
                  <button @click.stop="duplicateWidget(comp.id)" title="Duplicate (Ctrl+D)" class="hover:bg-brand-600 px-1 py-0.5 rounded"><i class="fas fa-copy"></i></button>
                  <button @click.stop="deleteWidget(comp.id)" title="Delete (Del)" class="hover:bg-red-500 px-1 py-0.5 rounded"><i class="fas fa-trash"></i></button>
                </div>

                <!-- WIDGET RENDERS -->
                <div x-html="renderWidget(comp)" class="w-full"></div>
                <!-- Resize hint -->
                <div x-show="!livePreview && selectedId===comp.id" class="resize-indicator"></div>

              </div>
            </template>
          </div>
        </div>
      </div>
    </main>

    <!-- RIGHT PANEL -->
    <aside x-show="!livePreview" class="w-72 flex-shrink-0 bg-white dark:bg-gray-900 border-l dark:border-gray-700 flex flex-col overflow-hidden">

      <!-- Panel Header -->
      <div class="p-3 border-b dark:border-gray-700">
        <template x-if="selectedWidget()">
          <div class="flex items-center gap-2">
            <i class="fas text-brand-500" :class="getWidgetIcon(selectedWidget().type)"></i>
            <span class="font-semibold text-sm capitalize" x-text="selectedWidget().type"></span>
          </div>
        </template>
        <template x-if="!selectedWidget()">
          <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Page Settings & SEO</p>
        </template>
      </div>

      <!-- Panel Tabs -->
      <div x-show="selectedWidget()" class="flex border-b dark:border-gray-700 text-xs">
        <button @click="rightTab='content'" :class="rightTab==='content'?'border-b-2 border-brand-500 text-brand-500':'text-gray-500 hover:text-gray-700'" class="flex-1 py-2 text-center">Content</button>
        <button @click="rightTab='style'" :class="rightTab==='style'?'border-b-2 border-brand-500 text-brand-500':'text-gray-500 hover:text-gray-700'" class="flex-1 py-2 text-center">Style</button>
        <button @click="rightTab='advanced'" :class="rightTab==='advanced'?'border-b-2 border-brand-500 text-brand-500':'text-gray-500 hover:text-gray-700'" class="flex-1 py-2 text-center">Advanced</button>
      </div>

      <!-- Panel Content -->
      <div class="flex-1 overflow-y-auto p-3 space-y-3 text-sm">

        <!-- SEO / Page Settings when nothing selected -->
        <div x-show="!selectedWidget()">
          <div class="space-y-3">
            <div>
              <h4 class="font-bold text-xs text-gray-500 uppercase tracking-wide mb-2">SEO Analysis</h4>
              <div class="space-y-2">
                <div>
                  <label class="text-xs text-gray-600 dark:text-gray-400 flex justify-between mb-1">
                    <span>Page Title</span>
                    <span :class="seoData.title.length>60?'text-red-500':seoData.title.length>30?'text-green-500':'text-yellow-500'" x-text="seoData.title.length+'/60'"></span>
                  </label>
                  <input x-model="seoData.title" type="text" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800">
                  <div class="mt-1 h-1 bg-gray-100 dark:bg-gray-700 rounded">
                    <div class="h-full rounded transition-all" :class="seoData.title.length>60?'bg-red-500':seoData.title.length>30?'bg-green-500':'bg-yellow-500'" :style="`width:${Math.min(100,(seoData.title.length/60)*100)}%`"></div>
                  </div>
                </div>
                <div>
                  <label class="text-xs text-gray-600 dark:text-gray-400 flex justify-between mb-1">
                    <span>Meta Description</span>
                    <span :class="seoData.meta.length>160?'text-red-500':seoData.meta.length>60?'text-green-500':'text-yellow-500'" x-text="seoData.meta.length+'/160'"></span>
                  </label>
                  <textarea x-model="seoData.meta" rows="3" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800 resize-none"></textarea>
                </div>
                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg space-y-1.5">
                  <div class="flex items-center gap-2 text-xs">
                    <i class="fas fa-image text-gray-400"></i>
                    <span class="text-gray-600 dark:text-gray-400">Images on page:</span>
                    <span class="font-bold" x-text="countImages()"></span>
                  </div>
                  <div class="flex items-center gap-2 text-xs">
                    <i class="fas fa-heading text-gray-400"></i>
                    <span class="text-gray-600 dark:text-gray-400">Headings:</span>
                    <span class="font-bold" x-text="countHeadings()"></span>
                  </div>
                  <div class="flex items-center gap-2 text-xs">
                    <i class="fas fa-puzzle-piece text-gray-400"></i>
                    <span class="text-gray-600 dark:text-gray-400">Components:</span>
                    <span class="font-bold" x-text="components.length"></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- WIDGET SETTINGS - Content Tab -->
        <div x-show="selectedWidget() && rightTab==='content'">
          <div x-html="renderSettingsPanel()"></div>
        </div>

        <!-- Style Tab -->
        <div x-show="selectedWidget() && rightTab==='style'">
          <template x-if="selectedWidget()">
            <div class="space-y-4 text-xs">

              <!-- SIZE CONTROLS -->
              <div>
                <label class="text-xs text-gray-500 uppercase tracking-wide font-bold block mb-2">Size</label>
                <div class="grid grid-cols-2 gap-2">
                  <div>
                    <label class="text-gray-500 block mb-0.5">Width</label>
                    <input type="text" x-model="selectedWidget().settings.width" placeholder="100% or 400px" class="w-full border dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800" @change="pushHistory();markDirty()">
                  </div>
                  <div>
                    <label class="text-gray-500 block mb-0.5">Height</label>
                    <input type="text" x-model="selectedWidget().settings.height" placeholder="auto or 200px" class="w-full border dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800" @change="pushHistory();markDirty()">
                  </div>
                </div>
                <div class="flex gap-1 mt-1.5">
                  <button @click="selectedWidget().settings.width='25%';pushHistory()" class="flex-1 py-1 bg-gray-100 dark:bg-gray-700 rounded text-center hover:bg-brand-100 hover:text-brand-600">25%</button>
                  <button @click="selectedWidget().settings.width='50%';pushHistory()" class="flex-1 py-1 bg-gray-100 dark:bg-gray-700 rounded text-center hover:bg-brand-100 hover:text-brand-600">50%</button>
                  <button @click="selectedWidget().settings.width='75%';pushHistory()" class="flex-1 py-1 bg-gray-100 dark:bg-gray-700 rounded text-center hover:bg-brand-100 hover:text-brand-600">75%</button>
                  <button @click="selectedWidget().settings.width='100%';pushHistory()" class="flex-1 py-1 bg-gray-100 dark:bg-gray-700 rounded text-center hover:bg-brand-100 hover:text-brand-600">100%</button>
                </div>
              </div>

              <!-- PER-SIDE PADDING -->
              <div>
                <label class="text-xs text-gray-500 uppercase tracking-wide font-bold block mb-2">Padding <span class="text-gray-400 normal-case font-normal">(px)</span></label>
                <div class="spacing-grid">
                  <div class="grid grid-cols-3 gap-1 items-center justify-items-center">
                    <div></div>
                    <input type="number" x-model.number="selectedWidget().settings.pt" placeholder="T" title="Top" class="w-12 text-center text-xs border dark:border-gray-600 rounded px-1 py-1 dark:bg-gray-800" @change="pushHistory();markDirty()" min="0">
                    <div></div>
                    <input type="number" x-model.number="selectedWidget().settings.pl" placeholder="L" title="Left" class="w-12 text-center text-xs border dark:border-gray-600 rounded px-1 py-1 dark:bg-gray-800" @change="pushHistory();markDirty()" min="0">
                    <div class="w-10 h-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded flex items-center justify-center text-[9px] text-gray-400">P</div>
                    <input type="number" x-model.number="selectedWidget().settings.pr" placeholder="R" title="Right" class="w-12 text-center text-xs border dark:border-gray-600 rounded px-1 py-1 dark:bg-gray-800" @change="pushHistory();markDirty()" min="0">
                    <div></div>
                    <input type="number" x-model.number="selectedWidget().settings.pb" placeholder="B" title="Bottom" class="w-12 text-center text-xs border dark:border-gray-600 rounded px-1 py-1 dark:bg-gray-800" @change="pushHistory();markDirty()" min="0">
                    <div></div>
                  </div>
                </div>
              </div>

              <!-- PER-SIDE MARGIN -->
              <div>
                <label class="text-xs text-gray-500 uppercase tracking-wide font-bold block mb-2">Margin <span class="text-gray-400 normal-case font-normal">(px)</span></label>
                <div class="spacing-grid">
                  <div class="grid grid-cols-3 gap-1 items-center justify-items-center">
                    <div></div>
                    <input type="number" x-model.number="selectedWidget().settings.mt" placeholder="T" class="w-12 text-center text-xs border dark:border-gray-600 rounded px-1 py-1 dark:bg-gray-800" @change="pushHistory();markDirty()">
                    <div></div>
                    <input type="number" x-model.number="selectedWidget().settings.ml" placeholder="L" class="w-12 text-center text-xs border dark:border-gray-600 rounded px-1 py-1 dark:bg-gray-800" @change="pushHistory();markDirty()">
                    <div class="w-10 h-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded flex items-center justify-center text-[9px] text-gray-400">M</div>
                    <input type="number" x-model.number="selectedWidget().settings.mr" placeholder="R" class="w-12 text-center text-xs border dark:border-gray-600 rounded px-1 py-1 dark:bg-gray-800" @change="pushHistory();markDirty()">
                    <div></div>
                    <input type="number" x-model.number="selectedWidget().settings.mb" placeholder="B" class="w-12 text-center text-xs border dark:border-gray-600 rounded px-1 py-1 dark:bg-gray-800" @change="pushHistory();markDirty()">
                    <div></div>
                  </div>
                </div>
              </div>

              <!-- FLEX / GRID LAYOUT -->
              <div>
                <label class="text-xs text-gray-500 uppercase tracking-wide font-bold block mb-2">Layout (Flex/Grid)</label>
                <div class="space-y-2">
                  <div>
                    <label class="text-gray-400 block mb-0.5">Display</label>
                    <div class="flex gap-1">
                      <template x-for="d in ['block','flex','grid','inline-flex']" :key="d">
                        <button @click="selectedWidget().settings.display=d;pushHistory();markDirty()"
                          :class="selectedWidget().settings.display===d?'bg-brand-500 text-white':'bg-gray-100 dark:bg-gray-700'"
                          class="flex-1 py-1 rounded text-center text-[10px] font-mono" x-text="d"></button>
                      </template>
                    </div>
                  </div>
                  <template x-if="selectedWidget().settings.display==='flex'||selectedWidget().settings.display==='inline-flex'">
                    <div class="space-y-1.5">
                      <div>
                        <label class="text-gray-400 block mb-0.5">Direction</label>
                        <div class="flex gap-1">
                          <template x-for="d in ['row','column','row-reverse','column-reverse']" :key="d">
                            <button @click="selectedWidget().settings.flexDir=d;pushHistory();markDirty()"
                              :class="selectedWidget().settings.flexDir===d?'bg-brand-500 text-white':'bg-gray-100 dark:bg-gray-700'"
                              class="flex-1 py-1 rounded text-[9px] font-mono" x-text="d.replace('-reverse','↩')"></button>
                          </template>
                        </div>
                      </div>
                      <div>
                        <label class="text-gray-400 block mb-0.5">Justify Content</label>
                        <select x-model="selectedWidget().settings.justifyContent" @change="pushHistory();markDirty()" class="w-full border dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800">
                          <option value="">default</option>
                          <option value="flex-start">Start</option>
                          <option value="center">Center</option>
                          <option value="flex-end">End</option>
                          <option value="space-between">Space Between</option>
                          <option value="space-around">Space Around</option>
                          <option value="space-evenly">Space Evenly</option>
                        </select>
                      </div>
                      <div>
                        <label class="text-gray-400 block mb-0.5">Align Items</label>
                        <select x-model="selectedWidget().settings.alignItems" @change="pushHistory();markDirty()" class="w-full border dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800">
                          <option value="">default</option>
                          <option value="flex-start">Start</option>
                          <option value="center">Center</option>
                          <option value="flex-end">End</option>
                          <option value="stretch">Stretch</option>
                          <option value="baseline">Baseline</option>
                        </select>
                      </div>
                      <div>
                        <label class="text-gray-400 flex justify-between mb-0.5"><span>Gap (px)</span><span x-text="selectedWidget().settings.flexGap||0"></span></label>
                        <input type="range" min="0" max="60" x-model.number="selectedWidget().settings.flexGap" @change="pushHistory();markDirty()" class="w-full accent-brand-500">
                      </div>
                      <label class="flex items-center gap-2"><input type="checkbox" x-model="selectedWidget().settings.flexWrap" @change="pushHistory();markDirty()"><span class="text-gray-400">Wrap</span></label>
                    </div>
                  </template>
                  <template x-if="selectedWidget().settings.display==='grid'">
                    <div class="space-y-1.5">
                      <div>
                        <label class="text-gray-400 block mb-0.5">Grid Columns</label>
                        <input type="text" x-model="selectedWidget().settings.gridCols" placeholder="repeat(3,1fr) or 1fr 2fr" @change="pushHistory();markDirty()" class="w-full border dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800 font-mono text-xs">
                      </div>
                      <div>
                        <label class="text-gray-400 block mb-0.5">Grid Rows</label>
                        <input type="text" x-model="selectedWidget().settings.gridRows" placeholder="auto or 200px auto" @change="pushHistory();markDirty()" class="w-full border dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800 font-mono text-xs">
                      </div>
                      <div>
                        <label class="text-gray-400 flex justify-between mb-0.5"><span>Gap (px)</span><span x-text="selectedWidget().settings.gridGap||0"></span></label>
                        <input type="range" min="0" max="60" x-model.number="selectedWidget().settings.gridGap" @change="pushHistory();markDirty()" class="w-full accent-brand-500">
                      </div>
                    </div>
                  </template>
                </div>
              </div>

              <!-- BACKGROUND -->
              <div>
                <label class="text-xs text-gray-500 uppercase tracking-wide font-bold block mb-2">Background</label>
                <div class="flex items-center gap-2 mb-2">
                  <input type="color" x-model="selectedWidget().settings.bgColor" class="w-8 h-8 rounded border-0 cursor-pointer" @change="pushHistory();markDirty()">
                  <span class="text-xs font-mono text-gray-400" x-text="selectedWidget().settings.bgColor"></span>
                </div>
                <input type="text" x-model="selectedWidget().settings.bgGradient" placeholder="linear-gradient(135deg,#667eea,#764ba2)" class="w-full border dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800 font-mono text-xs mb-1" @change="pushHistory();markDirty()">
                <label class="text-gray-400 text-xs">Gradient (CSS)</label>
              </div>

              <!-- BORDER -->
              <div>
                <label class="text-xs text-gray-500 uppercase tracking-wide font-bold block mb-2">Border</label>
                <div class="grid grid-cols-2 gap-2 mb-2">
                  <div>
                    <label class="text-gray-400 block mb-0.5">Style</label>
                    <select x-model="selectedWidget().settings.borderStyle" @change="pushHistory();markDirty()" class="w-full border dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800">
                      <option value="none">None</option>
                      <option value="solid">Solid</option>
                      <option value="dashed">Dashed</option>
                      <option value="dotted">Dotted</option>
                      <option value="double">Double</option>
                    </select>
                  </div>
                  <div>
                    <label class="text-gray-400 block mb-0.5">Width (px)</label>
                    <input type="number" x-model.number="selectedWidget().settings.borderWidth" min="0" max="20" class="w-full border dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800" @change="pushHistory();markDirty()">
                  </div>
                </div>
                <div class="flex items-center gap-2 mb-2">
                  <input type="color" x-model="selectedWidget().settings.borderColor" class="w-8 h-8 rounded border-0" @change="pushHistory();markDirty()">
                  <span class="text-xs text-gray-400">Border Color</span>
                </div>
                <div>
                  <label class="text-gray-400 flex justify-between mb-0.5"><span>Border Radius (px)</span><span x-text="selectedWidget().settings.borderRadius||0"></span></label>
                  <input type="range" min="0" max="80" x-model.number="selectedWidget().settings.borderRadius" @change="pushHistory();markDirty()" class="w-full accent-brand-500">
                </div>
              </div>

              <!-- SHADOW -->
              <div>
                <label class="text-xs text-gray-500 uppercase tracking-wide font-bold block mb-2">Shadow</label>
                <select x-model="selectedWidget().settings.shadow" @change="pushHistory();markDirty()" class="w-full border dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800 mb-2">
                  <option value="">None</option>
                  <option value="0 1px 3px rgba(0,0,0,.1)">Small</option>
                  <option value="0 4px 12px rgba(0,0,0,.1)">Medium</option>
                  <option value="0 8px 24px rgba(0,0,0,.15)">Large</option>
                  <option value="0 20px 60px rgba(0,0,0,.2)">XL</option>
                </select>
              </div>

              <!-- TYPOGRAPHY -->
              <div>
                <label class="text-xs text-gray-500 uppercase tracking-wide font-bold block mb-2">Typography</label>
                <div class="space-y-2">
                  <div class="grid grid-cols-2 gap-2">
                    <div>
                      <label class="text-gray-400 block mb-0.5">Font Weight</label>
                      <select x-model="selectedWidget().settings.fontWeight" @change="pushHistory();markDirty()" class="w-full border dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800">
                        <option value="">inherit</option>
                        <option value="300">Light 300</option>
                        <option value="400">Normal 400</option>
                        <option value="500">Medium 500</option>
                        <option value="600">Semi 600</option>
                        <option value="700">Bold 700</option>
                        <option value="900">Black 900</option>
                      </select>
                    </div>
                    <div>
                      <label class="text-gray-400 block mb-0.5">Text Color</label>
                      <div class="flex items-center gap-1">
                        <input type="color" x-model="selectedWidget().settings.textColor" class="w-8 h-8 rounded border-0" @change="pushHistory();markDirty()">
                        <span class="text-xs font-mono text-gray-400 text-[10px]" x-text="selectedWidget().settings.textColor||'—'"></span>
                      </div>
                    </div>
                  </div>
                  <div>
                    <label class="text-gray-400 flex justify-between mb-0.5"><span>Line Height</span><span x-text="(selectedWidget().settings.lineHeight||1.5).toFixed(1)"></span></label>
                    <input type="range" min="0.8" max="3" step="0.1" x-model.number="selectedWidget().settings.lineHeight" @change="pushHistory();markDirty()" class="w-full accent-brand-500">
                  </div>
                  <div>
                    <label class="text-gray-400 flex justify-between mb-0.5"><span>Letter Spacing (em)</span><span x-text="(selectedWidget().settings.letterSpacing||0).toFixed(2)"></span></label>
                    <input type="range" min="-0.1" max="0.5" step="0.01" x-model.number="selectedWidget().settings.letterSpacing" @change="pushHistory();markDirty()" class="w-full accent-brand-500">
                  </div>
                  <div>
                    <label class="text-gray-400 block mb-0.5">Text Transform</label>
                    <div class="flex gap-1">
                      <template x-for="tx in ['none','uppercase','lowercase','capitalize']" :key="tx">
                        <button @click="selectedWidget().settings.textTransform=tx;pushHistory()"
                          :class="selectedWidget().settings.textTransform===tx?'bg-brand-500 text-white':'bg-gray-100 dark:bg-gray-700'"
                          class="flex-1 py-1 rounded text-[9px]" x-text="tx==='none'?'—':tx.slice(0,3).toUpperCase()"></button>
                      </template>
                    </div>
                  </div>
                </div>
              </div>

              <!-- HOVER STATES -->
              <div>
                <label class="text-xs text-gray-500 uppercase tracking-wide font-bold block mb-2">Hover States</label>
                <div class="space-y-2">
                  <div class="flex items-center gap-2">
                    <input type="color" x-model="selectedWidget().settings.hoverBg" class="w-8 h-8 rounded border-0" @change="pushHistory();markDirty()">
                    <span class="text-xs text-gray-400">Hover Background</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <input type="color" x-model="selectedWidget().settings.hoverColor" class="w-8 h-8 rounded border-0" @change="pushHistory();markDirty()">
                    <span class="text-xs text-gray-400">Hover Text Color</span>
                  </div>
                  <div>
                    <label class="text-gray-400 block mb-0.5">Hover Shadow</label>
                    <select x-model="selectedWidget().settings.hoverShadow" @change="pushHistory();markDirty()" class="w-full border dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800">
                      <option value="">None</option>
                      <option value="0 2px 8px rgba(0,0,0,.12)">Small</option>
                      <option value="0 8px 24px rgba(0,0,0,.15)">Medium</option>
                      <option value="0 16px 48px rgba(0,0,0,.2)">Large</option>
                    </select>
                  </div>
                  <div>
                    <label class="text-gray-400 flex justify-between mb-0.5"><span>Transition (s)</span><span x-text="(selectedWidget().settings.transition||0.3).toFixed(1)"></span></label>
                    <input type="range" min="0" max="1" step="0.1" x-model.number="selectedWidget().settings.transition" @change="pushHistory();markDirty()" class="w-full accent-brand-500">
                  </div>
                </div>
              </div>

              <!-- OPACITY & TRANSFORM -->
              <div>
                <label class="text-xs text-gray-500 uppercase tracking-wide font-bold block mb-2">Opacity & Transform</label>
                <div class="space-y-2">
                  <div>
                    <label class="text-gray-400 flex justify-between mb-0.5"><span>Opacity</span><span x-text="(selectedWidget().settings.opacity!==undefined?selectedWidget().settings.opacity:1).toFixed(1)"></span></label>
                    <input type="range" min="0" max="1" step="0.05" x-model.number="selectedWidget().settings.opacity" @change="pushHistory();markDirty()" class="w-full accent-brand-500">
                  </div>
                  <div>
                    <label class="text-gray-400 flex justify-between mb-0.5"><span>Rotate (deg)</span><span x-text="selectedWidget().settings.rotate||0"></span></label>
                    <input type="range" min="-180" max="180" x-model.number="selectedWidget().settings.rotate" @change="pushHistory();markDirty()" class="w-full accent-brand-500">
                  </div>
                  <div>
                    <label class="text-gray-400 flex justify-between mb-0.5"><span>Scale</span><span x-text="(selectedWidget().settings.scale||1).toFixed(2)"></span></label>
                    <input type="range" min="0.5" max="2" step="0.05" x-model.number="selectedWidget().settings.scale" @change="pushHistory();markDirty()" class="w-full accent-brand-500">
                  </div>
                </div>
              </div>

              <!-- CUSTOM CSS -->
              <div>
                <label class="text-xs text-gray-500 uppercase tracking-wide font-bold block mb-1">Custom CSS</label>
                <textarea x-model="selectedWidget().settings.customCss" rows="4" placeholder="color: red; font-size: 20px;" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800 font-mono resize-none" @change="pushHistory();markDirty()"></textarea>
              </div>
            </div>
          </template>
        </div>

        <!-- Advanced Tab -->
        <div x-show="selectedWidget() && rightTab==='advanced'">
          <template x-if="selectedWidget()">
            <div class="space-y-3 text-xs">
              <div>
                <label class="text-gray-500 uppercase tracking-wide font-bold block mb-1">Element ID</label>
                <input type="text" x-model="selectedWidget().settings.elementId" placeholder="my-element" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 dark:bg-gray-800" @change="pushHistory()">
              </div>
              <div>
                <label class="text-gray-500 uppercase tracking-wide font-bold block mb-1">CSS Classes</label>
                <input type="text" x-model="selectedWidget().settings.cssClasses" placeholder="class1 class2" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 dark:bg-gray-800" @change="pushHistory()">
              </div>
              <div>
                <label class="text-gray-500 uppercase tracking-wide font-bold block mb-1">Hide on</label>
                <div class="space-y-1">
                  <label class="flex items-center gap-2"><input type="checkbox" x-model="selectedWidget().settings.hideDesktop" @change="pushHistory()"> <span>Desktop</span></label>
                  <label class="flex items-center gap-2"><input type="checkbox" x-model="selectedWidget().settings.hideTablet" @change="pushHistory()"> <span>Tablet</span></label>
                  <label class="flex items-center gap-2"><input type="checkbox" x-model="selectedWidget().settings.hideMobile" @change="pushHistory()"> <span>Mobile</span></label>
                </div>
              </div>
              <div>
                <label class="text-gray-500 uppercase tracking-wide font-bold block mb-1">Animation</label>
                <select x-model="selectedWidget().settings.animation" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 dark:bg-gray-800" @change="pushHistory()">
                  <option value="">None</option>
                  <option value="fade-in">Fade In</option>
                  <option value="slide-up">Slide Up</option>
                  <option value="slide-left">Slide Left</option>
                  <option value="zoom-in">Zoom In</option>
                </select>
              </div>
              <div>
                <label class="text-gray-500 uppercase tracking-wide font-bold block mb-1">Widget Label</label>
                <input type="text" x-model="selectedWidget().settings.label" placeholder="Custom label for tree view" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 dark:bg-gray-800" @change="pushHistory()">
              </div>
            </div>
          </template>
        </div>

      </div>

      <!-- ZOOM CONTROLS — fixed at bottom of right panel, always visible -->
      <div x-show="!livePreview" class="flex-shrink-0 border-t dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2">
        <p class="text-[10px] text-gray-400 uppercase tracking-wide font-bold mb-1.5">Canvas Zoom</p>
        <div class="flex items-center gap-2">
          <button @click="canvasZoom=Math.max(50,canvasZoom-10)" class="w-6 h-6 flex items-center justify-center rounded bg-gray-100 dark:bg-gray-800 hover:bg-brand-100 hover:text-brand-600 text-sm font-bold">−</button>
          <input type="range" x-model.number="canvasZoom" min="50" max="150" step="5" class="flex-1 accent-brand-500">
          <button @click="canvasZoom=Math.min(150,canvasZoom+10)" class="w-6 h-6 flex items-center justify-center rounded bg-gray-100 dark:bg-gray-800 hover:bg-brand-100 hover:text-brand-600 text-sm font-bold">+</button>
          <button @click="canvasZoom=100" class="text-[10px] text-gray-400 hover:text-brand-500 font-mono border dark:border-gray-700 rounded px-1.5 py-0.5" title="Reset to 100%">
            <span x-text="canvasZoom+'%'"></span>
          </button>
        </div>
      </div>
    </aside>

  </div>
</div>

<script>
function pageBuilderV5() {
  return {
    // State
    components: [],
    selectedId: null,
    darkMode: false,
    previewMode: 'desktop',
    livePreview: false,
    canvasZoom: 100,
    leftTab: 'widgets',
    rightTab: 'content',
    widgetSearch: '',
    isDirty: false,
    autoSaveIndicator: false,
    dragWidget: null,
    clipboard: null,

    // Modals
    showMediaLibrary: false,
    showAIModal: false,
    showTemplatesModal: false,
    showRevisionsModal: false,
    showShortcutsModal: false,
    mediaCallback: null,
    selectedMedia: null,
    mediaUrlInput: '',
    aiPrompt: '',
    aiResult: '',
    aiLoading: false,
    aiTargetWidget: null,
    newTemplateName: '',

    // Data
    templates: [],
    revisions: [],
    toasts: [],
    undoStack: [],
    redoStack: [],
    availableHeaderTemplates: initialHeaderTemplates,
    availableFooterTemplates: initialFooterTemplates,
    availableMenus: initialMenus,
    pageId: @json($page->id ?? null),
    isNewPage: @json(empty($page)),
    mediaImages: [
      'https://picsum.photos/400/300?random=1',
      'https://picsum.photos/400/300?random=2',
      'https://picsum.photos/400/300?random=3',
      'https://picsum.photos/400/300?random=4',
      'https://picsum.photos/400/300?random=5',
      'https://picsum.photos/400/300?random=6',
      'https://picsum.photos/800/400?random=7',
      'https://picsum.photos/400/400?random=8',
    ],
    contextMenu: { show: false, x: 0, y: 0, widgetId: null },

    // SEO
    seoData: {
      title: @json($page->title ?? 'My Page'),
      meta: @json($page->meta_description ?? ''),
    },

    // Global Styles
    globalStyles: {
      primaryColor: '#0ea5e9',
      secondaryColor: '#8b5cf6',
      accentColor: '#f59e0b',
      fontFamily: 'Inter, sans-serif',
      bgColor: '#ffffff',
      bgImage: '',
      bgSize: 'cover',
    },

    shortcuts: [
      {key:'Ctrl+S', desc:'Save Page'},
      {key:'Ctrl+Z', desc:'Undo'},
      {key:'Ctrl+Y', desc:'Redo'},
      {key:'Ctrl+C', desc:'Copy Widget'},
      {key:'Ctrl+V', desc:'Paste Widget'},
      {key:'Delete', desc:'Delete Widget'},
      {key:'Ctrl+D', desc:'Duplicate Widget'},
      {key:'Ctrl+P', desc:'Toggle Preview'},
      {key:'?', desc:'Show Shortcuts'},
    ],

    // Widget Categories
    widgetCategories: [
      {
        name: 'Layout', open: true,
        widgets: [
          {type:'section', label:'Section', icon:'fa-square'},
          {type:'container', label:'Container', icon:'fa-box'},
          {type:'columns', label:'Columns', icon:'fa-columns'},
          {type:'spacer', label:'Spacer', icon:'fa-arrows-alt-v'},
          {type:'divider', label:'Divider', icon:'fa-minus'},
        ]
      },
      {
        name: 'Basic', open: true,
        widgets: [
          {type:'heading', label:'Heading', icon:'fa-heading'},
          {type:'paragraph', label:'Paragraph', icon:'fa-paragraph'},
          {type:'button', label:'Button', icon:'fa-hand-pointer'},
          {type:'image', label:'Image', icon:'fa-image'},
          {type:'video', label:'Video', icon:'fa-video'},
          {type:'icon', label:'Icon', icon:'fa-star'},
          {type:'icon-list', label:'Icon List', icon:'fa-list-ul'},
        ]
      },
      {
        name: 'Content', open: false,
        widgets: [
          {type:'testimonial', label:'Testimonial', icon:'fa-quote-right'},
          {type:'team-member', label:'Team Member', icon:'fa-user-tie'},
          {type:'pricing', label:'Pricing', icon:'fa-tag'},
          {type:'accordion', label:'Accordion', icon:'fa-layer-group'},
          {type:'tabs', label:'Tabs', icon:'fa-folder'},
          {type:'counter', label:'Counter', icon:'fa-sort-numeric-up'},
          {type:'progress-bar', label:'Progress Bar', icon:'fa-tasks'},
          {type:'circle-progress', label:'Circle Progress', icon:'fa-circle-notch'},
          {type:'countdown', label:'Countdown', icon:'fa-clock'},
        ]
      },
      {
        name: 'Media', open: false,
        widgets: [
          {type:'image-carousel', label:'Carousel', icon:'fa-images'},
          {type:'before-after', label:'Before/After', icon:'fa-adjust'},
          {type:'lottie', label:'Lottie', icon:'fa-film'},
          {type:'google-maps', label:'Maps', icon:'fa-map-marker-alt'},
        ]
      },
      {
        name: 'Dynamic', open: false,
        widgets: [
          {type:'post-loop', label:'Post Loop', icon:'fa-rss'},
          {type:'post-meta', label:'Post Meta', icon:'fa-info'},
          {type:'author-box', label:'Author Box', icon:'fa-user'},
          {type:'custom-field', label:'Custom Field', icon:'fa-database'},
        ]
      },
      {
        name: 'Forms', open: false,
        widgets: [
          {type:'contact-form', label:'Contact Form', icon:'fa-envelope'},
          {type:'subscribe-form', label:'Subscribe', icon:'fa-bell'},
          {type:'search-form', label:'Search', icon:'fa-search'},
          {type:'raw-html', label:'Raw HTML', icon:'fa-code'},
        ]
      },
      {
        name: 'UI Elements', open: false,
        widgets: [
          {type:'alert-box', label:'Alert Box', icon:'fa-exclamation-circle'},
          {type:'breadcrumbs', label:'Breadcrumbs', icon:'fa-chevron-right'},
          {type:'table', label:'Table', icon:'fa-table'},
          {type:'modal-trigger', label:'Modal/Popup', icon:'fa-window-restore'},
          {type:'form-advanced', label:'Advanced Form', icon:'fa-wpforms'},
        ]
      }
    ],

    // New state variables
    snapGrid: false,
    showA11yModal: false,
    showVersionsModal: false,
    a11yIssues: [],
    pageVersions: [],
    newVersionName: '',

    init() {
      this.darkMode = localStorage.getItem('builder_dark') === 'true';
      this.snapGrid = localStorage.getItem('builder_snap') === 'true';
      this.injectTemplateWidgets();
      if (initialPageData) {
        this.loadInitialPageData();
      } else {
        this.loadFromStorage();
      }
      this.loadTemplates();
      this.loadRevisions();

      // Auto-save every 30 seconds
      setInterval(() => this.autoSave(), 30000);
      // Auto-revision every 5 minutes
      setInterval(() => this.saveRevision('Auto-snapshot'), 300000);
      // Persist snap grid
      this.$watch('snapGrid', v => localStorage.setItem('builder_snap', v));

      this.$nextTick(() => { this.initSortable(); });
    },

    injectTemplateWidgets() {
      const layoutCategory = this.widgetCategories.find((category) => category.name === 'Layout');

      if (!layoutCategory) {
        return;
      }

      const defaultHeader = this.availableHeaderTemplates.find((template) => template.is_default);
      const headerWidgets = [];

      if (defaultHeader) {
        headerWidgets.push({
          type: 'header-template',
          label: `Default Header: ${defaultHeader.name}`,
          icon: 'fa-window-maximize',
          templateType: 'header',
          templateId: defaultHeader.id,
          templateName: defaultHeader.name,
          templateContent: defaultHeader.content,
          isDefault: true,
        });
      }

      headerWidgets.push(...this.availableHeaderTemplates
        .filter((template) => !template.is_default)
        .map((template) => ({
          type: 'header-template',
          label: `Header: ${template.name}`,
          icon: 'fa-window-maximize',
          templateType: 'header',
          templateId: template.id,
          templateName: template.name,
          templateContent: template.content,
          isDefault: false,
        })));

      const footerWidgets = this.availableFooterTemplates.map((template) => ({
        type: 'footer-template',
        label: `Footer: ${template.name}`,
        icon: 'fa-grip-lines',
        templateType: 'footer',
        templateId: template.id,
        templateName: template.name,
        templateContent: template.content,
        isDefault: !!template.is_default,
      }));

      layoutCategory.widgets = [
        ...layoutCategory.widgets.filter((widget) => !['header-template', 'footer-template'].includes(widget.type)),
        ...headerWidgets,
        ...footerWidgets,
      ];
    },

    initSortable() {
      const el = document.getElementById('sortable-canvas');
      if (!el || !window.Sortable) return;
      Sortable.create(el, {
        animation: 150,
        ghostClass: 'sortable-ghost',
        dragClass: 'sortable-drag',
        handle: '.canvas-widget',
        onEnd: (evt) => {
          const moved = this.components.splice(evt.oldIndex, 1)[0];
          this.components.splice(evt.newIndex, 0, moved);
          this.pushHistory();
          this.markDirty();
        }
      });
    },

    // ==================== WIDGET DEFAULTS ====================
    getDefaultSettings(type) {
      const base = {
        // legacy compat
        paddingTop: 0, paddingBottom: 0, marginTop: 0, marginBottom: 0,
        // per-side (new)
        pt: 0, pr: 0, pb: 0, pl: 0,
        mt: 0, mr: 0, mb: 0, ml: 0,
        bgColor: 'transparent', bgGradient: '',
        borderRadius: 0, borderWidth: 0, borderStyle: 'solid',
        borderColor: '#000000', shadow: '', customCss: '',
        elementId: '', cssClasses: '', hideDesktop: false, hideTablet: false, hideMobile: false,
        animation: '', label: '',
        // size
        width: '', height: '',
        // layout
        display: '', flexDir: 'row', justifyContent: '', alignItems: '', flexGap: 0, flexWrap: false,
        gridCols: '', gridRows: '', gridGap: 0,
        // typography
        fontWeight: '', textColor: '', lineHeight: 1.5, letterSpacing: 0, textTransform: '',
        // hover
        hoverBg: '', hoverColor: '', hoverShadow: '', transition: 0.3,
        // transform
        opacity: 1, rotate: 0, scale: 1,
      };
      const map = {
        section: { ...base, bgColor: '#f8fafc', pt: 60, pb: 60, bgImage: '' },
        container: { ...base, maxWidth: '1200px', bgColor: 'transparent' },
        columns: { ...base, columnCount: 2, gap: 20, columns: [[], []] },
        spacer: { ...base, height: 40 },
        divider: { ...base, style: 'solid', color: '#e2e8f0', width: 100, thickness: 1, alignment: 'center' },
        heading: { ...base, text: 'Your Heading Here', tag: 'h2', alignment: 'left', color: '#1e293b', fontSize: 36, fontWeight: '700' },
        paragraph: { ...base, content: '<p>Click to edit this paragraph. You can add your content here and style it as needed.</p>', alignment: 'left' },
        button: { ...base, text: 'Click Me', link: '#', bgColor: '#0ea5e9', textColor: '#ffffff', borderRadius: 8, size: 'md', variant: 'filled' },
        image: { ...base, url: 'https://picsum.photos/800/400?random=10', alt: 'Image', width: 100, alignment: 'center', link: '' },
        video: { ...base, url: 'https://www.youtube.com/embed/dQw4w9WgXcQ', ratio: '16/9', autoplay: false, controls: true },
        icon: { ...base, iconClass: 'fas fa-star', size: 40, color: '#0ea5e9', link: '', alignment: 'center' },
        'icon-list': { ...base, items: [{icon:'fas fa-check', text:'Feature one'},{icon:'fas fa-check', text:'Feature two'},{icon:'fas fa-check', text:'Feature three'}], iconColor:'#0ea5e9', iconSize:16, alignment:'left' },
        testimonial: { ...base, text: 'This product is absolutely amazing! It has transformed the way I work.', author: 'Jane Smith', role: 'CEO, TechCorp', photo: 'https://i.pravatar.cc/80?img=1', rating: 5 },
        'team-member': { ...base, photo: 'https://i.pravatar.cc/200?img=5', name: 'John Doe', role: 'Lead Developer', bio: 'Passionate developer with 10 years of experience.', social: {twitter:'#',linkedin:'#',github:'#'} },
        pricing: { ...base, title: 'Pro Plan', price: '29', currency: '$', period: '/month', features: ['10 Projects','50GB Storage','Priority Support','Analytics'], buttonText: 'Get Started', highlighted: false },
        accordion: { ...base, items: [{title:'Section 1',content:'<p>Content for section 1</p>',open:true},{title:'Section 2',content:'<p>Content for section 2</p>',open:false}] },
        tabs: { ...base, activeTab: 0, items: [{label:'Tab 1',content:'<p>Content for tab 1</p>'},{label:'Tab 2',content:'<p>Content for tab 2</p>'}] },
        counter: { ...base, start: 0, end: 100, duration: 2000, prefix: '', suffix: '+', label: 'Happy Clients', color: '#0ea5e9', fontSize: 48 },
        'progress-bar': { ...base, label: 'Web Design', percentage: 75, color: '#0ea5e9', height: 12, striped: false },
        'circle-progress': { ...base, percentage: 75, size: 120, strokeWidth: 10, color: '#0ea5e9', label: '75%' },
        countdown: { ...base, targetDate: new Date(Date.now()+86400000*30).toISOString().slice(0,16), labelsDay:'Days',labelsHour:'Hours',labelsMin:'Minutes',labelsSec:'Seconds', color: '#0ea5e9' },
        'image-carousel': { ...base, images: ['https://picsum.photos/800/400?random=11','https://picsum.photos/800/400?random=12','https://picsum.photos/800/400?random=13'], autoplay: true, captions: ['Slide 1','Slide 2','Slide 3'], currentSlide: 0 },
        'before-after': { ...base, beforeUrl: 'https://picsum.photos/800/400?random=14', afterUrl: 'https://picsum.photos/800/400?random=15', sliderPos: 50 },
        lottie: { ...base, url: '', loop: true, autoplay: true, height: 300 },
        'google-maps': { ...base, address: 'New York, NY', height: 400 },
        'post-loop': { ...base, columns: 3, count: 6, layout: 'grid' },
        'post-meta': { ...base, author: true, date: true, category: true, comments: true },
        'author-box': { ...base, photo: 'https://i.pravatar.cc/100?img=9', name: 'Author Name', bio: 'Content creator and digital strategist.' },
        'custom-field': { ...base, fieldKey: 'custom_key', fieldValue: 'Custom Value', label: 'Custom Field' },
        'contact-form': { ...base, title: 'Contact Us', submitText: 'Send Message', successMsg: 'Thank you! We\'ll be in touch.' },
        'subscribe-form': { ...base, placeholder: 'Enter your email', buttonText: 'Subscribe', successMsg: 'You\'ve been subscribed!' },
        'search-form': { ...base, placeholder: 'Search...', buttonText: 'Search' },
        'raw-html': { ...base, code: '<div style="padding:20px;background:#f0f4f8;border-radius:8px;"><p>Your custom HTML here</p></div>' },
        // NEW WIDGETS
        'alert-box': { ...base, type: 'info', title: 'Notice', message: 'This is an informational alert.', dismissible: true, icon: true },
        'breadcrumbs': { ...base, items: [{label:'Home',link:'#'},{label:'Products',link:'#'},{label:'Current Page',link:''}], separator: '/' },
        'table': { ...base, headers: ['Name','Role','Email'], rows: [['Alice','Developer','alice@example.com'],['Bob','Designer','bob@example.com'],['Carol','Manager','carol@example.com']], striped: true, bordered: true },
        'modal-trigger': { ...base, triggerText: 'Open Modal', triggerBg: '#0ea5e9', modalTitle: 'Modal Title', modalContent: '<p>Modal content goes here. You can put any HTML content.</p>', modalId: 'm_'+Math.random().toString(36).substr(2,6) },
        'form-advanced': { ...base, title: 'Contact Form', fields: [{type:'text',label:'Full Name',required:true},{type:'email',label:'Email Address',required:true},{type:'select',label:'Subject',options:['General','Support','Sales']},{type:'radio',label:'Contact Method',options:['Email','Phone']},{type:'checkbox',label:'Subscribe to newsletter'},{type:'textarea',label:'Message',required:true},{type:'file',label:'Attachment'}], submitText: 'Submit', successMsg: 'Message sent!' },
        'header-template': { ...base, templateId: null, templateName: 'Header Template', templateType: 'header', templateContent: null },
        'footer-template': { ...base, templateId: null, templateName: 'Footer Template', templateType: 'footer', templateContent: null },
      };
      return map[type] || base;
    },

    // ==================== WIDGET RENDERING ====================
    renderWidget(comp) {
      const s = comp.settings;
      // Use per-side values (new) falling back to legacy top/bottom
      const pt = s.pt !== undefined ? s.pt : (s.paddingTop||0);
      const pb = s.pb !== undefined ? s.pb : (s.paddingBottom||0);
      const pl = s.pl !== undefined ? s.pl : 0;
      const pr = s.pr !== undefined ? s.pr : 0;
      const mt = s.mt !== undefined ? s.mt : (s.marginTop||0);
      const mb = s.mb !== undefined ? s.mb : (s.marginBottom||0);
      const ml = s.ml !== undefined ? s.ml : 0;
      const mr = s.mr !== undefined ? s.mr : 0;
      const bg = s.bgGradient ? s.bgGradient : (s.bgColor||'transparent');
      const borderStyle = s.borderWidth > 0 ? `border:${s.borderWidth}px ${s.borderStyle||'solid'} ${s.borderColor};` : '';
      const shadowStyle = s.shadow ? `box-shadow:${s.shadow};` : '';
      const widthStyle = s.width ? `width:${s.width};` : '';
      const heightStyle = s.height ? `height:${s.height};` : '';
      const displayStyle = s.display ? `display:${s.display};` : '';
      const flexStyle = s.display==='flex'||s.display==='inline-flex' ? `flex-direction:${s.flexDir||'row'};${s.justifyContent?'justify-content:'+s.justifyContent+';':''}${s.alignItems?'align-items:'+s.alignItems+';':''}${s.flexGap?'gap:'+s.flexGap+'px;':''}${s.flexWrap?'flex-wrap:wrap;':''}` : '';
      const gridStyle = s.display==='grid' ? `${s.gridCols?'grid-template-columns:'+s.gridCols+';':''}${s.gridRows?'grid-template-rows:'+s.gridRows+';':''}${s.gridGap?'gap:'+s.gridGap+'px;':''}` : '';
      const typoStyle = `${s.fontWeight?'font-weight:'+s.fontWeight+';':''}${s.textColor?'color:'+s.textColor+';':''}${s.lineHeight&&s.lineHeight!==1.5?'line-height:'+s.lineHeight+';':''}${s.letterSpacing?'letter-spacing:'+s.letterSpacing+'em;':''}${s.textTransform&&s.textTransform!=='none'?'text-transform:'+s.textTransform+';':''}`;
      const hoverStyle = (s.hoverBg||s.hoverColor||s.hoverShadow) ? `--hover-bg:${s.hoverBg||''};--hover-color:${s.hoverColor||''};transition:all ${s.transition||0.3}s ease;` : '';
      const transformStyle = `${s.opacity!==undefined&&s.opacity!==1?'opacity:'+s.opacity+';':''}${(s.rotate||s.scale)?`transform:${s.rotate?'rotate('+s.rotate+'deg)':''} ${s.scale&&s.scale!==1?'scale('+s.scale+')':''};`:''}`;

      const wrapStyle = `
        padding:${pt}px ${pr}px ${pb}px ${pl}px;
        margin:${mt}px ${mr}px ${mb}px ${ml}px;
        background:${bg};
        border-radius:${s.borderRadius||0}px;
        ${borderStyle}${shadowStyle}${widthStyle}${heightStyle}
        ${displayStyle}${flexStyle}${gridStyle}
        ${typoStyle}${hoverStyle}${transformStyle}
        ${s.customCss||''}
      `;
      const id = s.elementId ? `id="${s.elementId}"` : '';
      const cls = s.cssClasses || '';
      // Hover data attrs
      const hoverAttrs = s.hoverBg ? `data-hover-bg="${s.hoverBg}"` : '';

      const renderMenuPreview = (menuId, textColor = '#334155') => {
        const menu = this.availableMenus.find((item) => item.id == menuId);
        const items = menu?.top_level_items || [];

        if (!menu) {
          return `<span style="color:${textColor};font-size:14px;">Select a menu</span>`;
        }

        if (items.length === 0) {
          return `<span style="color:${textColor};font-size:14px;font-weight:600;">${menu.name}</span>`;
        }

        return `<nav style="display:flex;gap:16px;flex-wrap:wrap;align-items:center;">
          ${items.map((item) => `<span style="color:${textColor};font-size:14px;font-weight:500;">${item.label}</span>`).join('')}
        </nav>`;
      };

      const renderHeaderTemplateContent = (templateContent) => {
        const templateSettings = templateContent?.settings || {};
        const widgets = templateContent?.widgets || [];
        const bgColor = templateSettings.backgroundColor || '#ffffff';
        const paddingTop = templateSettings.paddingTop ?? 10;
        const paddingBottom = templateSettings.paddingBottom ?? 10;
        const paddingLeft = templateSettings.paddingLeft ?? 20;
        const paddingRight = templateSettings.paddingRight ?? 20;
        const containerWidth = templateSettings.containerWidth === 'boxed'
          ? 'max-width:1200px;margin:0 auto;'
          : templateSettings.containerWidth === 'fluid'
            ? 'max-width:90%;margin:0 auto;'
            : '';

        const inner = widgets.map((widget) => {
          if (widget.type === 'logo') {
            return `<img src="${widget.settings.logo_url || '/logo.png'}" alt="Logo" style="max-width:${widget.settings.logo_width || 150}px;height:auto;">`;
          }

          if (widget.type === 'menu') {
            return renderMenuPreview(widget.settings.menu_id);
          }

          if (widget.type === 'search') {
            return `<div style="display:flex;align-items:center;border:1px solid #cbd5e1;border-radius:8px;padding:8px 12px;min-width:220px;background:#fff;">
              <i class="fas fa-search" style="color:#94a3b8;margin-right:8px;"></i>
              <span style="color:#94a3b8;font-size:14px;">${widget.settings.placeholder || 'Search...'}</span>
            </div>`;
          }

          if (widget.type === 'cta') {
            return `<a href="${this.livePreview ? (widget.settings.url || '#') : '#'}" style="display:inline-block;background:#0ea5e9;color:#fff;padding:10px 18px;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none;">${widget.settings.text || 'Get Started'}</a>`;
          }

          return `<span style="font-size:14px;color:#64748b;">${widget.type}</span>`;
        }).join('');

        return `<div style="background:${bgColor};padding:${paddingTop}px ${paddingRight}px ${paddingBottom}px ${paddingLeft}px;border-radius:12px;border:1px solid #e2e8f0;">
          <div style="${containerWidth}display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
            ${inner || '<span style="color:#94a3b8;font-size:14px;">Empty header template</span>'}
          </div>
        </div>`;
      };

      const renderFooterTemplateContent = (templateContent) => {
        const templateSettings = templateContent?.settings || {};
        const columns = templateContent?.columns || [];
        const backgroundColor = templateSettings.backgroundColor || '#1a1a1a';
        const textColor = templateSettings.textColor || '#ffffff';
        const copyright = templateSettings.copyright || '';
        const columnCount = templateContent?.columnCount || Math.max(columns.length, 1);

        return `<div style="background:${backgroundColor};color:${textColor};padding:32px;border-radius:12px;">
          <div style="display:grid;grid-template-columns:repeat(${columnCount}, minmax(0, 1fr));gap:24px;">
            ${columns.map((column) => `
              <div>
                ${column.title ? `<h4 style="margin:0 0 12px;font-size:16px;font-weight:700;color:${textColor};">${column.title}</h4>` : ''}
                ${column.menu_id ? renderMenuPreview(column.menu_id, textColor) : ''}
                ${column.content ? `<div style="margin-top:${column.menu_id ? '12px' : '0'};font-size:14px;color:${textColor};opacity:0.92;">${column.content}</div>` : ''}
              </div>
            `).join('')}
          </div>
          ${copyright ? `<div style="margin-top:24px;padding-top:16px;border-top:1px solid rgba(255,255,255,0.15);font-size:13px;opacity:0.85;">${copyright}</div>` : ''}
        </div>`;
      };

      const renders = {
        section: () => `<div ${id} class="w-full ${cls}" style="${wrapStyle} background-image:${s.bgImage?`url(${s.bgImage})`:'none'};background-size:${s.bgSize||'cover'};min-height:100px;">
          <div class="mx-auto max-w-6xl px-6">${this.renderChildren(comp)}</div>
        </div>`,

        container: () => `<div ${id} class="mx-auto px-6 ${cls}" style="${wrapStyle} max-width:${s.maxWidth||'1200px'}">
          ${this.renderChildren(comp)}
        </div>`,

        columns: () => {
          const count = s.columnCount || 2;
          const cols = s.columns || Array(count).fill([]);
          return `<div ${id} class="flex gap-4 flex-wrap ${cls}" style="${wrapStyle}">
            ${cols.slice(0,count).map((col,i) => `<div class="col-dropzone flex-1 min-w-0 p-2" data-col="${comp.id}-${i}">
              ${col.length ? col.map(c => this.renderWidget(c)).join('') : `<p class="text-xs text-gray-300 text-center py-4">Drop here</p>`}
            </div>`).join('')}
          </div>`;
        },

        spacer: () => `<div ${id} class="${cls}" style="height:${s.height||40}px;${wrapStyle}"></div>`,

        divider: () => `<div ${id} class="flex justify-${s.alignment==='center'?'center':s.alignment==='right'?'end':'start'} ${cls}" style="${wrapStyle}">
          <hr style="border-top:${s.thickness||1}px ${s.style||'solid'} ${s.color||'#e2e8f0'};width:${s.width||100}%;margin:0">
        </div>`,

        heading: () => {
          const tag = s.tag || 'h2';
          const sizes = {h1:'2.5rem',h2:'2rem',h3:'1.75rem',h4:'1.5rem',h5:'1.25rem',h6:'1rem'};
          return `<${tag} ${id} class="w-full ${cls}" style="text-align:${s.alignment||'left'};color:${s.color||'#1e293b'};font-size:${s.fontSize||36}px;font-weight:${s.fontWeight||700};${wrapStyle}">${s.text||'Heading'}</${tag}>`;
        },

        paragraph: () => `<div ${id} class="${cls}" style="text-align:${s.alignment||'left'};${wrapStyle}">${s.content||'<p>Paragraph text</p>'}</div>`,

        button: () => `<div class="flex justify-${s.alignment==='center'?'center':s.alignment==='right'?'end':'start'} ${cls}" style="${wrapStyle}">
          <a href="${this.livePreview?s.link:'#'}" style="background-color:${s.bgColor||'#0ea5e9'};color:${s.textColor||'#fff'};border-radius:${s.borderRadius||8}px;padding:${s.size==='sm'?'8px 16px':s.size==='lg'?'14px 32px':'10px 24px'};font-size:${s.size==='sm'?'14px':s.size==='lg'?'18px':'16px'};font-weight:600;text-decoration:none;display:inline-block;">${s.text||'Button'}</a>
        </div>`,

        image: () => `<div class="flex justify-${s.alignment==='center'?'center':s.alignment==='right'?'end':'start'} ${cls}" style="${wrapStyle}">
          <img src="${s.url||'https://picsum.photos/800/400'}" alt="${s.alt||''}" style="width:${s.width||100}%;max-width:100%;border-radius:${s.borderRadius||0}px;">
        </div>`,

        video: () => `<div class="${cls}" style="${wrapStyle}">
          <div style="position:relative;padding-bottom:${s.ratio==='4/3'?'75%':s.ratio==='1/1'?'100%':'56.25%'};height:0;overflow:hidden;border-radius:${s.borderRadius||0}px;">
            <iframe src="${s.url||''}" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          </div>
        </div>`,

        icon: () => `<div class="flex justify-${s.alignment==='center'?'center':s.alignment==='right'?'end':'start'} ${cls}" style="${wrapStyle}">
          <i class="${s.iconClass||'fas fa-star'}" style="font-size:${s.size||40}px;color:${s.color||'#0ea5e9'}"></i>
        </div>`,

        'icon-list': () => `<ul class="${cls}" style="${wrapStyle};list-style:none;padding:0;margin:0;text-align:${s.alignment||'left'}">
          ${(s.items||[]).map(item => `<li style="display:flex;align-items:center;gap:8px;margin-bottom:8px;${s.alignment==='center'?'justify-content:center':''}">
            <i class="${item.icon||'fas fa-check'}" style="color:${s.iconColor||'#0ea5e9'};font-size:${s.iconSize||16}px;"></i>
            <span>${item.text||'List item'}</span>
          </li>`).join('')}
        </ul>`,

        testimonial: () => `<div ${id} class="${cls}" style="${wrapStyle};background:#f8fafc;border-radius:12px;padding:24px;">
          <div style="display:flex;gap:4px;margin-bottom:12px;">${Array(s.rating||5).fill('').map(()=>'<i class="fas fa-star" style="color:#f59e0b;font-size:16px;"></i>').join('')}</div>
          <p style="font-style:italic;margin:0 0 16px;color:#475569;">"${s.text||'Testimonial text'}"</p>
          <div style="display:flex;align-items:center;gap:12px;">
            <img src="${s.photo||'https://i.pravatar.cc/80'}" style="width:48px;height:48px;border-radius:50%;object-fit:cover;">
            <div>
              <p style="font-weight:700;margin:0;">${s.author||'Author'}</p>
              <p style="color:#64748b;font-size:14px;margin:0;">${s.role||'Role'}</p>
            </div>
          </div>
        </div>`,

        'team-member': () => `<div ${id} class="text-center ${cls}" style="${wrapStyle};background:#fff;border-radius:12px;padding:24px;box-shadow:0 1px 8px rgba(0,0,0,0.08);">
          <img src="${s.photo||'https://i.pravatar.cc/200'}" style="width:100px;height:100px;border-radius:50%;object-fit:cover;margin:0 auto 16px;">
          <h4 style="font-weight:700;font-size:18px;margin:0 0 4px;">${s.name||'Name'}</h4>
          <p style="color:#64748b;font-size:14px;margin:0 0 12px;">${s.role||'Role'}</p>
          <p style="color:#475569;font-size:14px;">${s.bio||'Bio'}</p>
          <div style="display:flex;justify-content:center;gap:12px;margin-top:16px;">
            ${s.social&&s.social.twitter?`<a href="${s.social.twitter}" style="color:#1da1f2;"><i class="fab fa-twitter"></i></a>`:''}
            ${s.social&&s.social.linkedin?`<a href="${s.social.linkedin}" style="color:#0077b5;"><i class="fab fa-linkedin"></i></a>`:''}
            ${s.social&&s.social.github?`<a href="${s.social.github}" style="color:#333;"><i class="fab fa-github"></i></a>`:''}
          </div>
        </div>`,

        pricing: () => `<div ${id} class="${cls}" style="${wrapStyle};background:${s.highlighted?this.globalStyles.primaryColor:'#fff'};color:${s.highlighted?'#fff':'inherit'};border-radius:16px;padding:32px 24px;box-shadow:0 4px 20px rgba(0,0,0,0.1);text-align:center;${s.highlighted?'transform:scale(1.05)':''}">
          <h3 style="font-weight:700;font-size:20px;margin:0 0 8px;">${s.title||'Plan'}</h3>
          <div style="font-size:48px;font-weight:900;margin:16px 0;">${s.currency||'$'}${s.price||'0'}<span style="font-size:16px;font-weight:400;">${s.period||'/mo'}</span></div>
          <ul style="list-style:none;padding:0;margin:0 0 24px;text-align:left;">
            ${(s.features||[]).map(f=>`<li style="padding:6px 0;display:flex;align-items:center;gap:8px;"><i class="fas fa-check-circle" style="color:${s.highlighted?'#fff':'#22c55e'}"></i>${f}</li>`).join('')}
          </ul>
          <button style="width:100%;padding:12px;background:${s.highlighted?'rgba(255,255,255,0.2)':'#0ea5e9'};color:#fff;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;">${s.buttonText||'Get Started'}</button>
        </div>`,

        accordion: () => `<div ${id} class="${cls}" style="${wrapStyle}">
          ${(s.items||[]).map((item,i) => `<div style="border:1px solid #e2e8f0;border-radius:8px;margin-bottom:8px;overflow:hidden;">
            <button onclick="this.nextElementSibling.style.display=this.nextElementSibling.style.display==='none'?'block':'none'" style="width:100%;text-align:left;padding:12px 16px;background:#f8fafc;border:none;font-weight:600;cursor:pointer;display:flex;justify-content:space-between;align-items:center;">
              ${item.title}<i class="fas fa-chevron-down"></i>
            </button>
            <div style="display:${item.open?'block':'none'};padding:16px;">${item.content||''}</div>
          </div>`).join('')}
        </div>`,

        tabs: () => `<div ${id} class="${cls}" style="${wrapStyle}">
          <div style="display:flex;border-bottom:2px solid #e2e8f0;margin-bottom:16px;">
            ${(s.items||[]).map((tab,i) => `<button onclick="this.closest('[data-tabs]').querySelectorAll('[data-tab-content]').forEach((c,ci)=>c.style.display=ci===parseInt(this.dataset.idx)?'block':'none');this.closest('[data-tabs]').querySelectorAll('[data-tab-btn]').forEach(b=>b.style.borderBottom=b===this?'2px solid #0ea5e9':'none');this.style.color=this===this?'#0ea5e9':'inherit'" data-tab-btn data-idx="${i}" style="padding:8px 16px;border:none;background:none;cursor:pointer;font-weight:600;color:${i===0?'#0ea5e9':'#64748b'};border-bottom:${i===0?'2px solid #0ea5e9':'none'};margin-bottom:-2px;">${tab.label||'Tab '+i}</button>`).join('')}
          </div>
          <div data-tabs>
            ${(s.items||[]).map((tab,i) => `<div data-tab-content style="display:${i===0?'block':'none'};">${tab.content||''}</div>`).join('')}
          </div>
        </div>`,

        counter: () => `<div ${id} class="text-center ${cls}" style="${wrapStyle}">
          <div style="font-size:${s.fontSize||48}px;font-weight:900;color:${s.color||'#0ea5e9'};">${s.prefix||''}${s.end||100}${s.suffix||'+'}</div>
          <p style="margin:4px 0 0;color:#64748b;">${s.label||'Counter'}</p>
        </div>`,

        'progress-bar': () => `<div ${id} class="${cls}" style="${wrapStyle}">
          <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
            <span style="font-weight:600;">${s.label||'Skill'}</span>
            <span style="color:#64748b;">${s.percentage||75}%</span>
          </div>
          <div style="background:#e2e8f0;border-radius:${s.height||12}px;height:${s.height||12}px;overflow:hidden;">
            <div style="width:${s.percentage||75}%;height:100%;background:${s.color||'#0ea5e9'};border-radius:${s.height||12}px;${s.striped?'background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-size:1rem 1rem;':''};transition:width 0.6s ease;"></div>
          </div>
        </div>`,

        'circle-progress': () => {
          const r = (s.size||120)/2 - (s.strokeWidth||10);
          const circ = 2*Math.PI*r;
          const dash = circ * (s.percentage||75)/100;
          return `<div ${id} class="flex flex-col items-center ${cls}" style="${wrapStyle}">
            <svg width="${s.size||120}" height="${s.size||120}" viewBox="0 0 ${s.size||120} ${s.size||120}" style="transform:rotate(-90deg)">
              <circle cx="${(s.size||120)/2}" cy="${(s.size||120)/2}" r="${r}" fill="none" stroke="#e2e8f0" stroke-width="${s.strokeWidth||10}"/>
              <circle cx="${(s.size||120)/2}" cy="${(s.size||120)/2}" r="${r}" fill="none" stroke="${s.color||'#0ea5e9'}" stroke-width="${s.strokeWidth||10}" stroke-dasharray="${dash} ${circ}" stroke-linecap="round"/>
            </svg>
            <p style="font-weight:700;font-size:18px;margin:8px 0 0;">${s.label||s.percentage+'%'}</p>
          </div>`;
        },

        countdown: () => {
          const target = new Date(s.targetDate||Date.now()).getTime();
          const now = Date.now();
          const diff = Math.max(0, target - now);
          const d = Math.floor(diff/86400000);
          const h = Math.floor((diff%86400000)/3600000);
          const m = Math.floor((diff%3600000)/60000);
          const sec = Math.floor((diff%60000)/1000);
          return `<div ${id} class="flex gap-4 justify-center flex-wrap ${cls}" style="${wrapStyle}">
            ${[{v:d,l:s.labelsDay||'Days'},{v:h,l:s.labelsHour||'Hours'},{v:m,l:s.labelsMin||'Min'},{v:sec,l:s.labelsSec||'Sec'}].map(x=>`
            <div style="text-align:center;background:#f8fafc;border-radius:12px;padding:16px 24px;">
              <div style="font-size:40px;font-weight:900;color:${s.color||'#0ea5e9'};line-height:1;">${String(x.v).padStart(2,'0')}</div>
              <p style="margin:4px 0 0;font-size:12px;color:#64748b;text-transform:uppercase;">${x.l}</p>
            </div>`).join('')}
          </div>`;
        },

        'image-carousel': () => `<div ${id} class="${cls}" style="${wrapStyle};position:relative;overflow:hidden;border-radius:${s.borderRadius||0}px;">
          <div style="display:flex;overflow:hidden;">
            ${(s.images||[]).map((img,i)=>`<div style="min-width:100%;position:relative;${i===0?'':'display:none'}">
              <img src="${img}" style="width:100%;display:block;">
              ${s.captions&&s.captions[i]?`<div style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,0.5);color:#fff;padding:12px;text-align:center;">${s.captions[i]}</div>`:''}
            </div>`).join('')}
          </div>
          <div style="position:absolute;bottom:12px;left:50%;transform:translateX(-50%);display:flex;gap:6px;">
            ${(s.images||[]).map((_,i)=>`<div style="width:8px;height:8px;border-radius:50%;background:${i===0?'#fff':'rgba(255,255,255,0.5)'}"></div>`).join('')}
          </div>
          <button onclick="this.closest('[style]').querySelector('div>div').scrollBy(-400,0)" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);background:rgba(0,0,0,0.5);color:#fff;border:none;border-radius:50%;width:36px;height:36px;cursor:pointer;font-size:16px;">‹</button>
          <button onclick="this.closest('[style]').querySelector('div>div').scrollBy(400,0)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:rgba(0,0,0,0.5);color:#fff;border:none;border-radius:50%;width:36px;height:36px;cursor:pointer;font-size:16px;">›</button>
        </div>`,

        'before-after': () => `<div ${id} class="${cls}" style="${wrapStyle};position:relative;overflow:hidden;user-select:none;" onmousedown="this.dataset.drag='1'" onmouseup="delete this.dataset.drag" onmousemove="if(this.dataset.drag){const r=this.getBoundingClientRect();const pct=((event.clientX-r.left)/r.width*100).toFixed(1);this.querySelector('.ba-after').style.clipPath='inset(0 0 0 '+pct+'%)';this.querySelector('.ba-handle').style.left=pct+'%'}">
          <img src="${s.beforeUrl||'https://picsum.photos/800/400?random=14'}" style="width:100%;display:block;">
          <div class="ba-after" style="position:absolute;top:0;left:0;right:0;bottom:0;clip-path:inset(0 0 0 50%);">
            <img src="${s.afterUrl||'https://picsum.photos/800/400?random=15'}" style="width:100%;display:block;">
          </div>
          <div class="ba-handle" style="position:absolute;top:0;bottom:0;left:50%;width:4px;background:white;cursor:ew-resize;transform:translateX(-50%);">
            <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:32px;height:32px;background:white;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.3);font-size:16px;">⟺</div>
          </div>
          <div style="position:absolute;top:12px;left:12px;background:rgba(0,0,0,0.6);color:#fff;padding:4px 10px;border-radius:4px;font-size:12px;">Before</div>
          <div style="position:absolute;top:12px;right:12px;background:rgba(0,0,0,0.6);color:#fff;padding:4px 10px;border-radius:4px;font-size:12px;">After</div>
        </div>`,

        lottie: () => `<div ${id} class="${cls}" style="${wrapStyle};height:${s.height||300}px;display:flex;align-items:center;justify-content:center;background:#f8fafc;border-radius:8px;">
          ${s.url ? `<div style="width:100%;height:100%;">Lottie: ${s.url}</div>` : '<p style="color:#94a3b8;">Add a Lottie JSON URL in settings</p>'}
        </div>`,

        'google-maps': () => `<div ${id} class="${cls}" style="${wrapStyle};border-radius:${s.borderRadius||0}px;overflow:hidden;">
          <iframe width="100%" height="${s.height||400}" frameborder="0" style="border:0" src="https://maps.google.com/maps?q=${encodeURIComponent(s.address||'New York')}&output=embed&hl=en" allowfullscreen></iframe>
        </div>`,

        'post-loop': () => {
          const posts = [
            {title:'Getting Started with AI',date:'May 1, 2026',cat:'Technology',excerpt:'Learn the fundamentals...',img:'https://picsum.photos/400/250?random=20'},
            {title:'Design Trends 2026',date:'Apr 28, 2026',cat:'Design',excerpt:'Explore the latest...',img:'https://picsum.photos/400/250?random=21'},
            {title:'Remote Work Tips',date:'Apr 25, 2026',cat:'Productivity',excerpt:'Boost your productivity...',img:'https://picsum.photos/400/250?random=22'},
            {title:'Web Performance Guide',date:'Apr 22, 2026',cat:'Development',excerpt:'Speed matters more...',img:'https://picsum.photos/400/250?random=23'},
            {title:'UX Research Methods',date:'Apr 20, 2026',cat:'UX',excerpt:'Understanding users...',img:'https://picsum.photos/400/250?random=24'},
            {title:'Marketing Automation',date:'Apr 18, 2026',cat:'Marketing',excerpt:'Save time with automation...',img:'https://picsum.photos/400/250?random=25'},
          ].slice(0, s.count||6);
          const cols = s.columns||3;
          return `<div ${id} class="${cls}" style="${wrapStyle}">
            <div style="display:grid;grid-template-columns:repeat(${cols},1fr);gap:24px;">
              ${posts.map(p=>`<article style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 8px rgba(0,0,0,0.08);">
                <img src="${p.img}" style="width:100%;height:180px;object-fit:cover;">
                <div style="padding:16px;">
                  <span style="font-size:11px;color:#0ea5e9;font-weight:600;text-transform:uppercase;">${p.cat}</span>
                  <h4 style="font-size:16px;font-weight:700;margin:8px 0;">${p.title}</h4>
                  <p style="font-size:13px;color:#64748b;margin:0 0 12px;">${p.excerpt}</p>
                  <div style="display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:12px;color:#94a3b8;">${p.date}</span>
                    <a href="#" style="font-size:12px;color:#0ea5e9;text-decoration:none;font-weight:600;">Read more →</a>
                  </div>
                </div>
              </article>`).join('')}
            </div>
          </div>`;
        },

        'post-meta': () => `<div ${id} class="flex flex-wrap gap-4 ${cls}" style="${wrapStyle};font-size:14px;color:#64748b;">
          ${s.author?`<span><i class="fas fa-user" style="margin-right:4px;"></i> John Smith</span>`:''}
          ${s.date?`<span><i class="fas fa-calendar" style="margin-right:4px;"></i> May 1, 2026</span>`:''}
          ${s.category?`<span><i class="fas fa-folder" style="margin-right:4px;"></i> Technology</span>`:''}
          ${s.comments?`<span><i class="fas fa-comment" style="margin-right:4px;"></i> 12 Comments</span>`:''}
        </div>`,

        'author-box': () => `<div ${id} class="${cls}" style="${wrapStyle};background:#f8fafc;border-radius:12px;padding:24px;display:flex;gap:20px;align-items:flex-start;">
          <img src="${s.photo||'https://i.pravatar.cc/100'}" style="width:80px;height:80px;border-radius:50%;object-fit:cover;flex-shrink:0;">
          <div>
            <h4 style="font-weight:700;margin:0 0 4px;">${s.name||'Author Name'}</h4>
            <p style="color:#64748b;font-size:14px;margin:0;">${s.bio||'Author bio'}</p>
          </div>
        </div>`,

        'custom-field': () => `<div ${id} class="${cls}" style="${wrapStyle};padding:12px;background:#f8fafc;border-radius:8px;display:flex;gap:8px;">
          <span style="font-weight:600;color:#64748b;">${s.fieldKey||'Key'}:</span>
          <span>${s.fieldValue||'Value'}</span>
        </div>`,

        'contact-form': () => `<div ${id} class="${cls}" style="${wrapStyle};background:#f8fafc;border-radius:12px;padding:32px;">
          <h3 style="font-weight:700;font-size:24px;margin:0 0 24px;">${s.title||'Contact Us'}</h3>
          <div style="display:flex;flex-direction:column;gap:16px;">
            <input type="text" placeholder="Your Name" style="width:100%;padding:12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;box-sizing:border-box;">
            <input type="email" placeholder="Email Address" style="width:100%;padding:12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;box-sizing:border-box;">
            <textarea placeholder="Your Message" rows="4" style="width:100%;padding:12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;box-sizing:border-box;resize:none;"></textarea>
            <button onclick="this.closest('div').querySelector('.form-success').style.display='block';this.style.display='none'" style="padding:12px 24px;background:#0ea5e9;color:#fff;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;">${s.submitText||'Send Message'}</button>
            <div class="form-success" style="display:none;padding:12px;background:#dcfce7;color:#16a34a;border-radius:8px;font-weight:600;">${s.successMsg||'Thank you!'}</div>
          </div>
        </div>`,

        'subscribe-form': () => `<div ${id} class="${cls}" style="${wrapStyle};display:flex;gap:8px;max-width:480px;">
          <input type="email" placeholder="${s.placeholder||'Enter your email'}" style="flex:1;padding:12px 16px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;">
          <button style="padding:12px 24px;background:#0ea5e9;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;white-space:nowrap;">${s.buttonText||'Subscribe'}</button>
        </div>`,

        'search-form': () => `<div ${id} class="${cls}" style="${wrapStyle};display:flex;gap:0;max-width:480px;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
          <input type="text" placeholder="${s.placeholder||'Search...'}" style="flex:1;padding:12px 16px;border:none;font-size:14px;outline:none;">
          <button style="padding:12px 20px;background:#0ea5e9;color:#fff;border:none;font-size:14px;font-weight:600;cursor:pointer;">${s.buttonText||'Search'}</button>
        </div>`,

        'raw-html': () => `<div ${id} class="${cls}" style="${wrapStyle}">${s.code||''}</div>`,

        // ---- NEW UI ELEMENT WIDGETS ----
        'alert-box': () => {
          const colors = {info:{bg:'#eff6ff',border:'#bfdbfe',text:'#1d4ed8',icon:'fa-info-circle'},success:{bg:'#f0fdf4',border:'#bbf7d0',text:'#15803d',icon:'fa-check-circle'},warning:{bg:'#fffbeb',border:'#fde68a',text:'#b45309',icon:'fa-exclamation-triangle'},error:{bg:'#fef2f2',border:'#fecaca',text:'#dc2626',icon:'fa-times-circle'}};
          const t = s.type||'info'; const c = colors[t]||colors.info;
          return `<div ${id} class="${cls}" style="${wrapStyle};background:${c.bg};border:1px solid ${c.border};border-radius:8px;padding:12px 16px;display:flex;align-items:flex-start;gap:12px;">
            ${s.icon!==false?`<i class="fas ${c.icon}" style="color:${c.text};margin-top:2px;flex-shrink:0;"></i>`:''}
            <div style="flex:1;">
              ${s.title?`<p style="font-weight:700;color:${c.text};margin:0 0 4px;">${s.title}</p>`:''}
              <p style="color:${c.text};margin:0;font-size:14px;">${s.message||'Alert message here'}</p>
            </div>
            ${s.dismissible?`<button onclick="this.closest('[style]').remove()" style="background:none;border:none;cursor:pointer;color:${c.text};opacity:0.6;font-size:16px;line-height:1;padding:0;">×</button>`:''}
          </div>`;
        },

        'breadcrumbs': () => `<nav ${id} class="${cls}" style="${wrapStyle};" aria-label="Breadcrumb">
          <ol style="display:flex;align-items:center;gap:4px;list-style:none;padding:0;margin:0;font-size:14px;flex-wrap:wrap;">
            ${(s.items||[]).map((item,i)=>`<li style="display:flex;align-items:center;gap:4px;">
              ${i>0?`<span style="color:#94a3b8;margin-right:4px;">${s.separator||'/'}</span>`:''}
              ${item.link && i<(s.items.length-1) ? `<a href="${item.link}" style="color:#0ea5e9;text-decoration:none;hover:underline;">${item.label}</a>` : `<span style="${i===(s.items.length-1)?'color:#64748b;font-weight:600':''}">${item.label}</span>`}
            </li>`).join('')}
          </ol>
        </nav>`,

        'table': () => `<div ${id} class="${cls}" style="${wrapStyle};overflow-x:auto;">
          <table style="width:100%;border-collapse:collapse;font-size:14px;${s.bordered?'border:1px solid #e2e8f0;':''}">
            <thead>
              <tr style="background:#f1f5f9;">
                ${(s.headers||[]).map(h=>`<th style="padding:10px 14px;text-align:left;font-weight:700;color:#475569;${s.bordered?'border:1px solid #e2e8f0;':''}">${h}</th>`).join('')}
              </tr>
            </thead>
            <tbody>
              ${(s.rows||[]).map((row,ri)=>`<tr style="${s.striped&&ri%2===1?'background:#f8fafc;':''}${this.livePreview?'':''}">
                ${row.map(cell=>`<td style="padding:10px 14px;${s.bordered?'border:1px solid #e2e8f0;':'border-bottom:1px solid #f1f5f9;'}color:#374151;">${cell}</td>`).join('')}
              </tr>`).join('')}
            </tbody>
          </table>
        </div>`,

        'modal-trigger': () => {
          const mid = s.modalId || ('m_'+comp.id.slice(-6));
          return `<div ${id} class="${cls}" style="${wrapStyle}">
            <button onclick="document.getElementById('modal_${mid}').style.display='flex'" style="padding:10px 24px;background:${s.triggerBg||'#0ea5e9'};color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;">${s.triggerText||'Open Modal'}</button>
            <div id="modal_${mid}" style="display:none;position:fixed;inset:0;z-index:9000;align-items:center;justify-content:center;background:rgba(0,0,0,0.5);" onclick="if(event.target===this)this.style.display='none'">
              <div style="background:#fff;border-radius:16px;padding:32px;max-width:500px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.2);position:relative;">
                <button onclick="document.getElementById('modal_${mid}').style.display='none'" style="position:absolute;top:12px;right:16px;background:none;border:none;font-size:22px;cursor:pointer;color:#94a3b8;">×</button>
                <h3 style="font-weight:700;font-size:20px;margin:0 0 16px;">${s.modalTitle||'Modal Title'}</h3>
                <div>${s.modalContent||'<p>Modal content here.</p>'}</div>
              </div>
            </div>
          </div>`;
        },

        'form-advanced': () => {
          const fid = 'f_'+comp.id.slice(-6);
          return `<div ${id} class="${cls}" style="${wrapStyle};background:#f8fafc;border-radius:12px;padding:32px;">
            ${s.title?`<h3 style="font-weight:700;font-size:22px;margin:0 0 20px;">${s.title}</h3>`:''}
            <div id="${fid}_success" style="display:none;padding:12px;background:#dcfce7;color:#16a34a;border-radius:8px;margin-bottom:16px;font-weight:600;">${s.successMsg||'Submitted!'}</div>
            <form onsubmit="event.preventDefault();document.getElementById('${fid}_success').style.display='block';this.style.display='none'" style="display:flex;flex-direction:column;gap:14px;">
              ${(s.fields||[]).map(f=>{
                if(f.type==='checkbox') return `<label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;"><input type="checkbox"> ${f.label}</label>`;
                if(f.type==='radio') return `<div><p style="font-size:13px;font-weight:600;color:#64748b;margin:0 0 6px;">${f.label}</p>${(f.options||[]).map(o=>`<label style="display:flex;align-items:center;gap:6px;font-size:14px;margin-bottom:4px;cursor:pointer;"><input type="radio" name="${fid}_${f.label}"> ${o}</label>`).join('')}</div>`;
                if(f.type==='select') return `<div><label style="font-size:13px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">${f.label}${f.required?'*':''}</label><select style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;">${(f.options||[]).map(o=>`<option>${o}</option>`).join('')}</select></div>`;
                if(f.type==='textarea') return `<div><label style="font-size:13px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">${f.label}${f.required?'*':''}</label><textarea rows="4" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;resize:none;box-sizing:border-box;" placeholder="${f.label}..."></textarea></div>`;
                if(f.type==='file') return `<div><label style="font-size:13px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">${f.label}</label><input type="file" style="width:100%;font-size:14px;"></div>`;
                return `<div><label style="font-size:13px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">${f.label}${f.required?'*':''}</label><input type="${f.type||'text'}" placeholder="${f.label}..." ${f.required?'required':''} style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;box-sizing:border-box;"></div>`;
              }).join('')}
              <button type="submit" style="padding:12px;background:#0ea5e9;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;">${s.submitText||'Submit'}</button>
            </form>
          </div>`;
        },

        'header-template': () => `<div ${id} class="${cls}" style="${wrapStyle}">
          ${renderHeaderTemplateContent(s.templateContent)}
        </div>`,

        'footer-template': () => `<div ${id} class="${cls}" style="${wrapStyle}">
          ${renderFooterTemplateContent(s.templateContent)}
        </div>`,
      };

      const renderer = renders[comp.type];
      return renderer ? renderer() : `<div class="p-4 bg-red-50 text-red-500 text-sm rounded">Unknown widget: ${comp.type}</div>`;
    },

    renderChildren(comp) {
      if (!comp.children || comp.children.length === 0) {
        return `<div style="min-height:60px;border:2px dashed #cbd5e1;border-radius:4px;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:12px;padding:16px;">Drop widgets here</div>`;
      }
      return comp.children.map(c => this.renderWidget(c)).join('');
    },

    // ==================== SETTINGS PANELS ====================
    renderSettingsPanel() {
      const w = this.selectedWidget();
      if (!w) return '';
      const s = w.settings;

      // Generate settings form based on widget type
      const field = (label, key, type='text', opts='') => {
        if (type === 'color') {
          return `<div class="flex items-center gap-2 mb-2">
            <label class="text-xs text-gray-500 w-24 shrink-0">${label}</label>
            <input type="color" x-model="getSelectedWidget().settings.${key}" @change="pushHistory();markDirty()" class="w-8 h-8 rounded border-0 cursor-pointer">
            <span class="text-xs font-mono text-gray-400" x-text="getSelectedWidget().settings.${key}"></span>
          </div>`;
        }
        if (type === 'select') {
          return `<div class="mb-2">
            <label class="text-xs text-gray-500 block mb-1">${label}</label>
            <select x-model="getSelectedWidget().settings.${key}" @change="pushHistory();markDirty()" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800">${opts}</select>
          </div>`;
        }
        if (type === 'textarea') {
          return `<div class="mb-2">
            <label class="text-xs text-gray-500 block mb-1">${label}</label>
            <textarea x-model="getSelectedWidget().settings.${key}" @change="pushHistory();markDirty()" rows="3" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800 resize-none font-mono">${opts}</textarea>
          </div>`;
        }
        if (type === 'checkbox') {
          return `<label class="flex items-center gap-2 mb-2 text-xs">
            <input type="checkbox" x-model="getSelectedWidget().settings.${key}" @change="pushHistory();markDirty()" class="rounded">
            <span class="text-gray-600 dark:text-gray-400">${label}</span>
          </label>`;
        }
        if (type === 'image') {
          return `<div class="mb-2">
            <label class="text-xs text-gray-500 block mb-1">${label}</label>
            <div class="flex gap-1">
              <input type="text" x-model="getSelectedWidget().settings.${key}" @change="pushHistory();markDirty()" placeholder="URL or browse..." class="flex-1 border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800">
              <button @click="openMediaLibrary('${key}')" class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded text-xs hover:bg-gray-300">📁</button>
            </div>
            <template x-if="getSelectedWidget().settings.${key}">
              <img :src="getSelectedWidget().settings.${key}" class="mt-1 w-full h-20 object-cover rounded border dark:border-gray-600">
            </template>
          </div>`;
        }
        if (type === 'number') {
          return `<div class="mb-2">
            <label class="text-xs text-gray-500 block mb-1">${label}</label>
            <input type="number" x-model.number="getSelectedWidget().settings.${key}" @change="pushHistory();markDirty()" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800" ${opts}>
          </div>`;
        }
        if (type === 'range') {
          const [min,max,step] = opts.split(',');
          return `<div class="mb-2">
            <label class="text-xs text-gray-500 flex justify-between mb-1"><span>${label}</span><span x-text="getSelectedWidget().settings.${key}"></span></label>
            <input type="range" min="${min||0}" max="${max||100}" step="${step||1}" x-model.number="getSelectedWidget().settings.${key}" @change="pushHistory();markDirty()" class="w-full accent-brand-500">
          </div>`;
        }
        return `<div class="mb-2">
          <label class="text-xs text-gray-500 block mb-1">${label}</label>
          <input type="${type}" x-model="getSelectedWidget().settings.${key}" @change="pushHistory();markDirty()" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800" placeholder="${opts}">
        </div>`;
      };

      const panels = {
        heading: `
          ${field('Text', 'text', 'text', 'Heading text')}
          ${field('Tag', 'tag', 'select', '<option value="h1">H1</option><option value="h2">H2</option><option value="h3">H3</option><option value="h4">H4</option><option value="h5">H5</option><option value="h6">H6</option>')}
          ${field('Alignment', 'alignment', 'select', '<option value="left">Left</option><option value="center">Center</option><option value="right">Right</option>')}
          ${field('Color', 'color', 'color')}
          ${field('Font Size (px)', 'fontSize', 'range', '12,96,1')}
          ${field('Font Weight', 'fontWeight', 'select', '<option value="400">Normal</option><option value="600">Semi-Bold</option><option value="700">Bold</option><option value="900">Black</option>')}
        `,
        paragraph: `
          <div class="mb-2">
            <label class="text-xs text-gray-500 block mb-1">Content <span class="text-gray-400">(double-click on canvas to edit)</span></label>
            <div x-html="getSelectedWidget().settings.content" class="text-xs p-2 bg-gray-50 dark:bg-gray-800 rounded border dark:border-gray-700 min-h-[60px]"></div>
          </div>
          ${field('Alignment', 'alignment', 'select', '<option value="left">Left</option><option value="center">Center</option><option value="right">Right</option><option value="justify">Justify</option>')}
        `,
        button: `
          ${field('Text', 'text', 'text', 'Button text')}
          ${field('Link URL', 'link', 'url', 'https://')}
          ${field('Background', 'bgColor', 'color')}
          ${field('Text Color', 'textColor', 'color')}
          ${field('Border Radius', 'borderRadius', 'range', '0,40,1')}
          ${field('Size', 'size', 'select', '<option value="sm">Small</option><option value="md">Medium</option><option value="lg">Large</option>')}
          ${field('Alignment', 'alignment', 'select', '<option value="left">Left</option><option value="center">Center</option><option value="right">Right</option>')}
        `,
        image: `
          ${field('Image URL', 'url', 'image')}
          ${field('Alt Text', 'alt', 'text', 'Description')}
          ${field('Width %', 'width', 'range', '10,100,5')}
          ${field('Alignment', 'alignment', 'select', '<option value="left">Left</option><option value="center">Center</option><option value="right">Right</option>')}
          ${field('Link URL', 'link', 'url', 'https://')}
        `,
        video: `
          ${field('Embed URL', 'url', 'url', 'YouTube/Vimeo embed URL')}
          ${field('Aspect Ratio', 'ratio', 'select', '<option value="16/9">16:9</option><option value="4/3">4:3</option><option value="1/1">1:1</option>')}
          ${field('Autoplay', 'autoplay', 'checkbox')}
          ${field('Show Controls', 'controls', 'checkbox')}
        `,
        icon: `
          ${field('Icon Class', 'iconClass', 'text', 'fas fa-star')}
          ${field('Size (px)', 'size', 'range', '12,120,4')}
          ${field('Color', 'color', 'color')}
          ${field('Link URL', 'link', 'url', 'https://')}
          ${field('Alignment', 'alignment', 'select', '<option value="left">Left</option><option value="center">Center</option><option value="right">Right</option>')}
        `,
        section: `
          ${field('Background Color', 'bgColor', 'color')}
          ${field('Background Image', 'bgImage', 'image')}
          ${field('Padding Top', 'paddingTop', 'range', '0,200,4')}
          ${field('Padding Bottom', 'paddingBottom', 'range', '0,200,4')}
        `,
        spacer: `${field('Height (px)', 'height', 'range', '0,300,4')}`,
        divider: `
          ${field('Style', 'style', 'select', '<option value="solid">Solid</option><option value="dashed">Dashed</option><option value="dotted">Dotted</option>')}
          ${field('Color', 'color', 'color')}
          ${field('Width (%)', 'width', 'range', '10,100,5')}
          ${field('Thickness (px)', 'thickness', 'range', '1,10,1')}
          ${field('Alignment', 'alignment', 'select', '<option value="left">Left</option><option value="center">Center</option><option value="right">Right</option>')}
        `,
        columns: `
          ${field('Columns', 'columnCount', 'select', '<option value="2">2 Columns</option><option value="3">3 Columns</option><option value="4">4 Columns</option>')}
          ${field('Gap (px)', 'gap', 'range', '0,60,4')}
        `,
        testimonial: `
          ${field('Quote Text', 'text', 'textarea')}
          ${field('Author Name', 'author', 'text', 'Jane Smith')}
          ${field('Role/Company', 'role', 'text', 'CEO, Company')}
          ${field('Photo URL', 'photo', 'image')}
          ${field('Rating (1-5)', 'rating', 'range', '1,5,1')}
        `,
        'team-member': `
          ${field('Photo', 'photo', 'image')}
          ${field('Name', 'name', 'text', 'Full Name')}
          ${field('Role', 'role', 'text', 'Job Title')}
          ${field('Bio', 'bio', 'textarea')}
        `,
        pricing: `
          ${field('Plan Title', 'title', 'text', 'Pro Plan')}
          ${field('Currency', 'currency', 'text', '$')}
          ${field('Price', 'price', 'text', '29')}
          ${field('Period', 'period', 'text', '/month')}
          ${field('Button Text', 'buttonText', 'text', 'Get Started')}
          ${field('Highlighted', 'highlighted', 'checkbox')}
          <div class="mb-2">
            <label class="text-xs text-gray-500 block mb-1">Features (one per line)</label>
            <textarea x-model.lazy="featuresText" @input="updateFeatures()" rows="5" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800 resize-none" :value="getSelectedWidget().settings.features&&getSelectedWidget().settings.features.join('\\n')"></textarea>
          </div>
        `,
        counter: `
          ${field('End Number', 'end', 'number', 'min=0')}
          ${field('Prefix', 'prefix', 'text', 'e.g. $')}
          ${field('Suffix', 'suffix', 'text', 'e.g. +')}
          ${field('Label', 'label', 'text', 'Happy Clients')}
          ${field('Color', 'color', 'color')}
          ${field('Font Size', 'fontSize', 'range', '20,80,2')}
        `,
        'progress-bar': `
          ${field('Label', 'label', 'text', 'Skill name')}
          ${field('Percentage', 'percentage', 'range', '0,100,1')}
          ${field('Color', 'color', 'color')}
          ${field('Height (px)', 'height', 'range', '4,40,2')}
          ${field('Striped', 'striped', 'checkbox')}
        `,
        'circle-progress': `
          ${field('Percentage', 'percentage', 'range', '0,100,1')}
          ${field('Size (px)', 'size', 'range', '60,300,10')}
          ${field('Stroke Width', 'strokeWidth', 'range', '2,30,2')}
          ${field('Color', 'color', 'color')}
          ${field('Label', 'label', 'text', 'e.g. 75%')}
        `,
        countdown: `
          ${field('Target Date/Time', 'targetDate', 'datetime-local')}
          ${field('Color', 'color', 'color')}
          ${field('Label: Days', 'labelsDay', 'text', 'Days')}
          ${field('Label: Hours', 'labelsHour', 'text', 'Hours')}
          ${field('Label: Min', 'labelsMin', 'text', 'Minutes')}
          ${field('Label: Sec', 'labelsSec', 'text', 'Seconds')}
        `,
        'google-maps': `
          ${field('Address', 'address', 'text', 'New York, NY')}
          ${field('Height (px)', 'height', 'range', '200,600,20')}
        `,
        'contact-form': `
          ${field('Form Title', 'title', 'text', 'Contact Us')}
          ${field('Submit Button', 'submitText', 'text', 'Send Message')}
          ${field('Success Message', 'successMsg', 'text', 'Thank you!')}
        `,
        'subscribe-form': `
          ${field('Placeholder', 'placeholder', 'text', 'Enter email')}
          ${field('Button Text', 'buttonText', 'text', 'Subscribe')}
        `,
        'search-form': `
          ${field('Placeholder', 'placeholder', 'text', 'Search...')}
          ${field('Button Text', 'buttonText', 'text', 'Search')}
        `,
        'raw-html': `${field('HTML Code', 'code', 'textarea')}`,
        'post-loop': `
          ${field('Columns', 'columns', 'select', '<option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option>')}
          ${field('Post Count', 'count', 'range', '1,12,1')}
        `,
        'image-carousel': `
          <div class="mb-2">
            <label class="text-xs text-gray-500 block mb-1">Image URLs (one per line)</label>
            <textarea rows="5" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800 resize-none font-mono" :value="getSelectedWidget().settings.images&&getSelectedWidget().settings.images.join('\\n')" @change="getSelectedWidget().settings.images=$event.target.value.split('\\n').filter(u=>u.trim());pushHistory()"></textarea>
          </div>
          ${field('Autoplay', 'autoplay', 'checkbox')}
        `,
        'before-after': `
          ${field('Before Image', 'beforeUrl', 'image')}
          ${field('After Image', 'afterUrl', 'image')}
        `,
        'author-box': `
          ${field('Photo', 'photo', 'image')}
          ${field('Name', 'name', 'text', 'Author')}
          ${field('Bio', 'bio', 'textarea')}
        `,
        'custom-field': `
          ${field('Key', 'fieldKey', 'text', 'field_name')}
          ${field('Value', 'fieldValue', 'text', 'Field value')}
        `,
        accordion: `
          <div class="mb-3">
            <div class="flex items-center justify-between mb-2">
              <label class="text-xs font-bold text-gray-500 uppercase">Items</label>
              <button @click="addAccordionItem()" class="text-xs text-brand-500 hover:text-brand-600 font-medium">+ Add</button>
            </div>
            <div class="space-y-2">
              <template x-for="(item, i) in getSelectedWidget().settings.items" :key="i">
                <div class="border dark:border-gray-700 rounded-lg p-2 space-y-1.5">
                  <input type="text" x-model="item.title" @change="pushHistory()" class="w-full border dark:border-gray-600 rounded px-2 py-1 text-xs dark:bg-gray-800" placeholder="Item title">
                  <textarea x-model="item.content" @change="pushHistory()" rows="2" class="w-full border dark:border-gray-600 rounded px-2 py-1 text-xs dark:bg-gray-800 resize-none" placeholder="Item content..."></textarea>
                  <div class="flex justify-between">
                    <label class="flex items-center gap-1 text-xs"><input type="checkbox" x-model="item.open" @change="pushHistory()"> Open</label>
                    <button @click="getSelectedWidget().settings.items.splice(i,1);pushHistory()" class="text-xs text-red-400 hover:text-red-600">Remove</button>
                  </div>
                </div>
              </template>
            </div>
          </div>
        `,
        tabs: `
          <div class="mb-3">
            <div class="flex items-center justify-between mb-2">
              <label class="text-xs font-bold text-gray-500 uppercase">Tabs</label>
              <button @click="addTabItem()" class="text-xs text-brand-500 hover:text-brand-600 font-medium">+ Add</button>
            </div>
            <div class="space-y-2">
              <template x-for="(item, i) in getSelectedWidget().settings.items" :key="i">
                <div class="border dark:border-gray-700 rounded-lg p-2 space-y-1.5">
                  <input type="text" x-model="item.label" @change="pushHistory()" class="w-full border dark:border-gray-600 rounded px-2 py-1 text-xs dark:bg-gray-800" placeholder="Tab label">
                  <textarea x-model="item.content" @change="pushHistory()" rows="2" class="w-full border dark:border-gray-600 rounded px-2 py-1 text-xs dark:bg-gray-800 resize-none" placeholder="Tab content..."></textarea>
                  <button @click="getSelectedWidget().settings.items.splice(i,1);pushHistory()" class="text-xs text-red-400 hover:text-red-600">Remove</button>
                </div>
              </template>
            </div>
          </div>
        `,
        'icon-list': `
          <div class="mb-3">
            <div class="flex items-center justify-between mb-2">
              <label class="text-xs font-bold text-gray-500 uppercase">Items</label>
              <button @click="getSelectedWidget().settings.items.push({icon:'fas fa-check',text:'New item'});pushHistory()" class="text-xs text-brand-500 font-medium">+ Add</button>
            </div>
            <div class="space-y-2">
              <template x-for="(item, i) in getSelectedWidget().settings.items" :key="i">
                <div class="flex gap-2 items-center">
                  <input x-model="item.icon" @change="pushHistory()" class="w-28 border dark:border-gray-600 rounded px-2 py-1 text-xs dark:bg-gray-800" placeholder="fas fa-check">
                  <input x-model="item.text" @change="pushHistory()" class="flex-1 border dark:border-gray-600 rounded px-2 py-1 text-xs dark:bg-gray-800" placeholder="Item text">
                  <button @click="getSelectedWidget().settings.items.splice(i,1);pushHistory()" class="text-red-400 text-xs">✕</button>
                </div>
              </template>
            </div>
          </div>
          ${field('Icon Color', 'iconColor', 'color')}
          ${field('Icon Size', 'iconSize', 'range', '10,40,2')}
          ${field('Alignment', 'alignment', 'select', '<option value="left">Left</option><option value="center">Center</option><option value="right">Right</option>')}
        `,
        'alert-box': `
          ${field('Type', 'type', 'select', '<option value="info">Info</option><option value="success">Success</option><option value="warning">Warning</option><option value="error">Error</option>')}
          ${field('Title', 'title', 'text', 'Notice')}
          ${field('Message', 'message', 'textarea')}
          ${field('Dismissible', 'dismissible', 'checkbox')}
          ${field('Show Icon', 'icon', 'checkbox')}
        `,
        'breadcrumbs': `
          <div class="mb-2">
            <div class="flex items-center justify-between mb-1">
              <label class="text-xs text-gray-500 font-bold uppercase">Items</label>
              <button @click="getSelectedWidget().settings.items.push({label:'Page',link:'#'});pushHistory()" class="text-xs text-brand-500">+ Add</button>
            </div>
            <div class="space-y-1.5">
              <template x-for="(item,i) in getSelectedWidget().settings.items" :key="i">
                <div class="flex gap-1 items-center">
                  <input x-model="item.label" @change="pushHistory()" class="flex-1 border dark:border-gray-600 rounded px-2 py-1 text-xs dark:bg-gray-800" placeholder="Label">
                  <input x-model="item.link" @change="pushHistory()" class="flex-1 border dark:border-gray-600 rounded px-2 py-1 text-xs dark:bg-gray-800" placeholder="Link">
                  <button @click="getSelectedWidget().settings.items.splice(i,1);pushHistory()" class="text-red-400 text-xs">✕</button>
                </div>
              </template>
            </div>
          </div>
          ${field('Separator', 'separator', 'text', '/')}
        `,
        'table': `
          <div class="mb-2">
            <label class="text-xs text-gray-500 font-bold uppercase block mb-1">Headers (comma-separated)</label>
            <input type="text" :value="getSelectedWidget().settings.headers&&getSelectedWidget().settings.headers.join(',')" @change="getSelectedWidget().settings.headers=$event.target.value.split(',').map(h=>h.trim());pushHistory()" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800">
          </div>
          <div class="mb-2">
            <label class="text-xs text-gray-500 font-bold uppercase block mb-1">Rows (one per line, cells comma-separated)</label>
            <textarea rows="5" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800 font-mono resize-none"
              :value="getSelectedWidget().settings.rows&&getSelectedWidget().settings.rows.map(r=>r.join(',')).join('\\n')"
              @change="getSelectedWidget().settings.rows=$event.target.value.split('\\n').filter(l=>l.trim()).map(l=>l.split(',').map(c=>c.trim()));pushHistory()"></textarea>
          </div>
          ${field('Striped Rows', 'striped', 'checkbox')}
          ${field('Bordered', 'bordered', 'checkbox')}
        `,
        'modal-trigger': `
          ${field('Trigger Button Text', 'triggerText', 'text', 'Open Modal')}
          ${field('Button Color', 'triggerBg', 'color')}
          ${field('Modal Title', 'modalTitle', 'text', 'Modal Title')}
          <div class="mb-2">
            <label class="text-xs text-gray-500 block mb-1">Modal Content (HTML)</label>
            <textarea x-model="getSelectedWidget().settings.modalContent" @change="pushHistory();markDirty()" rows="4" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800 font-mono resize-none"></textarea>
          </div>
        `,
        'form-advanced': `
          ${field('Form Title', 'title', 'text', 'Contact Form')}
          ${field('Submit Button', 'submitText', 'text', 'Submit')}
          ${field('Success Message', 'successMsg', 'text', 'Submitted!')}
          <div class="mt-2 p-2 bg-gray-50 dark:bg-gray-800 rounded text-xs text-gray-400">
            <p>Fields: text, email, select, radio, checkbox, textarea, file</p>
            <p class="mt-1">Edit fields in the JS settings or use the built-in template.</p>
          </div>
        `,
      };

      return panels[w.type] || `<p class="text-xs text-gray-400 text-center py-4">Settings for "${w.type}" — use Style tab for customization</p>`;
    },

    // ==================== CORE OPERATIONS ====================
    generateId() {
      return 'w_' + Math.random().toString(36).substr(2, 9);
    },

    createWidget(widgetDescriptor) {
      const descriptor = typeof widgetDescriptor === 'string'
        ? (() => {
            try {
              return JSON.parse(widgetDescriptor);
            } catch (error) {
              return { type: widgetDescriptor };
            }
          })()
        : widgetDescriptor;
      const type = descriptor.type;
      const widget = {
        id: this.generateId(),
        type,
        settings: this.getDefaultSettings(type),
        children: ['section','container'].includes(type) ? [] : undefined,
      };

      if (type === 'header-template' || type === 'footer-template') {
        widget.settings = {
          ...widget.settings,
          templateId: descriptor.templateId,
          templateName: descriptor.templateName,
          templateType: descriptor.templateType,
          templateContent: descriptor.templateContent,
          label: descriptor.templateName,
        };
      }

      return widget;
    },

    addWidgetToCanvas(widgetDescriptor) {
      this.pushHistory();
      const w = this.createWidget(widgetDescriptor);
      this.components.push(w);
      this.selectedId = w.id;
      this.markDirty();
      this.$nextTick(() => { this.initSortable(); });
      this.showToast(`${w.settings.label || w.type} added`, 'success');
    },

    selectWidget(id) {
      this.selectedId = id;
      this.rightTab = 'content';
    },

    selectedWidget() {
      return this.findWidget(this.selectedId, this.components);
    },

    getSelectedWidget() {
      return this.selectedWidget();
    },

    findWidget(id, list) {
      if (!id || !list) return null;
      for (const c of list) {
        if (c.id === id) return c;
        if (c.children) {
          const found = this.findWidget(id, c.children);
          if (found) return found;
        }
        if (c.settings && c.settings.columns) {
          for (const col of c.settings.columns) {
            const found = this.findWidget(id, col);
            if (found) return found;
          }
        }
      }
      return null;
    },

    deleteWidget(id) {
      this.pushHistory();
      this.components = this.removeFromList(id, this.components);
      if (this.selectedId === id) this.selectedId = null;
      this.markDirty();
    },

    removeFromList(id, list) {
      return list.filter(c => {
        if (c.id === id) return false;
        if (c.children) c.children = this.removeFromList(id, c.children);
        return true;
      });
    },

    duplicateWidget(id) {
      this.pushHistory();
      const w = this.findWidget(id, this.components);
      if (!w) return;
      const clone = JSON.parse(JSON.stringify(w));
      clone.id = this.generateId();
      const idx = this.components.findIndex(c => c.id === id);
      if (idx >= 0) this.components.splice(idx+1, 0, clone);
      else this.components.push(clone);
      this.selectedId = clone.id;
      this.markDirty();
    },

    copyWidget(id) {
      const w = this.findWidget(id, this.components);
      if (w) { this.clipboard = JSON.parse(JSON.stringify(w)); this.showToast('Copied!', 'info'); }
    },

    pasteWidget() {
      if (!this.clipboard) return;
      this.pushHistory();
      const clone = JSON.parse(JSON.stringify(this.clipboard));
      clone.id = this.generateId();
      this.components.push(clone);
      this.selectedId = clone.id;
      this.markDirty();
      this.showToast('Pasted!', 'success');
    },

    moveWidgetUp(id) {
      this.pushHistory();
      const idx = this.components.findIndex(c => c.id === id);
      if (idx > 0) {
        [this.components[idx-1], this.components[idx]] = [this.components[idx], this.components[idx-1]];
        this.markDirty();
      }
    },

    moveWidgetDown(id) {
      this.pushHistory();
      const idx = this.components.findIndex(c => c.id === id);
      if (idx < this.components.length-1) {
        [this.components[idx], this.components[idx+1]] = [this.components[idx+1], this.components[idx]];
        this.markDirty();
      }
    },

    // ==================== DRAG & DROP ====================
    startDragFromLibrary(e, widget) {
      const payload = JSON.stringify(widget);
      this.dragWidget = payload;
      e.dataTransfer.setData('text/plain', payload);
      e.dataTransfer.effectAllowed = 'copy';
    },

    onCanvasDragOver(e) {
      e.preventDefault();
      e.dataTransfer.dropEffect = 'copy';
    },

    dropOnCanvas(e) {
      e.preventDefault();
      const widget = e.dataTransfer.getData('text/plain') || this.dragWidget;
      if (widget) {
        this.addWidgetToCanvas(widget);
        this.dragWidget = null;
      }
    },

    // ==================== UNDO / REDO ====================
    pushHistory() {
      const state = JSON.stringify(this.components);
      if (this.undoStack.length === 0 || this.undoStack[this.undoStack.length-1] !== state) {
        this.undoStack.push(state);
        if (this.undoStack.length > 50) this.undoStack.shift();
        this.redoStack = [];
      }
    },

    undo() {
      if (this.undoStack.length === 0) return;
      this.redoStack.push(JSON.stringify(this.components));
      this.components = JSON.parse(this.undoStack.pop());
      this.markDirty();
    },

    redo() {
      if (this.redoStack.length === 0) return;
      this.undoStack.push(JSON.stringify(this.components));
      this.components = JSON.parse(this.redoStack.pop());
      this.markDirty();
    },

    // ==================== CONTEXT MENU ====================
    openContextMenu(e, widgetId) {
      this.contextMenu = { show: true, x: e.clientX, y: e.clientY, widgetId };
      this.selectWidget(widgetId);
    },

    showCanvasContextMenu(e) {
      // Only show if clicking canvas background
      if (e.target.closest('.canvas-widget')) return;
      this.contextMenu = { show: true, x: e.clientX, y: e.clientY, widgetId: null };
    },

    // ==================== MEDIA LIBRARY ====================
    openMediaLibrary(settingsKey) {
      this.selectedMedia = null;
      this.mediaCallback = settingsKey;
      this.showMediaLibrary = true;
    },

    confirmMedia() {
      if (this.selectedMedia && this.mediaCallback) {
        const w = this.selectedWidget();
        if (w) {
          w.settings[this.mediaCallback] = this.selectedMedia;
          this.pushHistory();
          this.markDirty();
        }
      }
      this.showMediaLibrary = false;
    },

    uploadMediaImage(e) {
      const file = e.target.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = (ev) => {
        this.mediaImages.push(ev.target.result);
        this.selectedMedia = ev.target.result;
      };
      reader.readAsDataURL(file);
    },

    addMediaUrl() {
      if (this.mediaUrlInput) {
        this.mediaImages.push(this.mediaUrlInput);
        this.selectedMedia = this.mediaUrlInput;
        this.mediaUrlInput = '';
      }
    },

    // ==================== AI ASSISTANT ====================
    openAIForWidget() {
      this.aiTargetWidget = this.selectedId;
      this.aiResult = '';
      this.aiPrompt = '';
      this.showAIModal = true;
    },

    async generateAIText() {
      if (!this.aiPrompt) return;
      this.aiLoading = true;
      this.aiResult = '';
      // Mock AI response after delay
      await new Promise(r => setTimeout(r, 1500));
      const responses = {
        'product': 'Experience the future of productivity with our cutting-edge solution. Designed for professionals who demand excellence, our platform combines intuitive design with powerful features to help you achieve more in less time.',
        'hero': 'Transform Your Digital Presence\n\nDiscover a smarter way to build, grow, and scale your online business. Join thousands of satisfied customers who\'ve already made the switch.',
        'testimonial': '"This product has completely transformed how our team works. The intuitive interface and powerful features have saved us countless hours every week. Highly recommended!" - Sarah Johnson, Marketing Director',
        'cta': 'Ready to take your business to the next level? Join our growing community of successful entrepreneurs and start your journey today. Limited spots available — act now and get your first month free!',
      };
      const key = Object.keys(responses).find(k => this.aiPrompt.toLowerCase().includes(k)) || 'product';
      this.aiResult = responses[key];
      this.aiLoading = false;
    },

    insertAIText() {
      const w = this.findWidget(this.aiTargetWidget, this.components);
      if (w) {
        if (w.type === 'heading') w.settings.text = this.aiResult;
        else if (w.type === 'paragraph') w.settings.content = `<p>${this.aiResult}</p>`;
        else if (w.type === 'testimonial') w.settings.text = this.aiResult;
        else if (w.settings.text !== undefined) w.settings.text = this.aiResult;
        else if (w.settings.content !== undefined) w.settings.content = `<p>${this.aiResult}</p>`;
        this.pushHistory();
        this.markDirty();
        this.showToast('AI text inserted!', 'success');
      }
      this.showAIModal = false;
    },

    // ==================== TEMPLATES ====================
    saveTemplate() {
      if (!this.newTemplateName.trim()) { this.showToast('Enter a template name', 'warning'); return; }
      const tpl = {
        name: this.newTemplateName,
        date: Date.now(),
        components: JSON.parse(JSON.stringify(this.components)),
        globalStyles: JSON.parse(JSON.stringify(this.globalStyles)),
      };
      this.templates.push(tpl);
      this.saveTemplatesToStorage();
      this.newTemplateName = '';
      this.showToast('Template saved!', 'success');
    },

    loadTemplate(i) {
      this.pushHistory();
      const tpl = this.templates[i];
      this.components = JSON.parse(JSON.stringify(tpl.components));
      this.globalStyles = { ...this.globalStyles, ...tpl.globalStyles };
      this.showTemplatesModal = false;
      this.markDirty();
      this.showToast('Template loaded!', 'success');
    },

    deleteTemplate(i) {
      this.templates.splice(i, 1);
      this.saveTemplatesToStorage();
      this.showToast('Template deleted', 'info');
    },

    loadTemplates() {
      try { this.templates = JSON.parse(localStorage.getItem('cms_templates') || '[]'); } catch(e) {}
    },

    saveTemplatesToStorage() {
      localStorage.setItem('cms_templates', JSON.stringify(this.templates));
    },

    // ==================== REVISIONS ====================
    saveRevision(label) {
      const rev = {
        label: label || 'Snapshot',
        date: Date.now(),
        components: JSON.parse(JSON.stringify(this.components)),
      };
      this.revisions.unshift(rev);
      if (this.revisions.length > 20) this.revisions.pop();
      this.saveRevisionsToStorage();
    },

    restoreRevision(i) {
      this.pushHistory();
      this.components = JSON.parse(JSON.stringify(this.revisions[i].components));
      this.showRevisionsModal = false;
      this.markDirty();
      this.showToast('Revision restored!', 'success');
    },

    loadRevisions() {
      try { this.revisions = JSON.parse(localStorage.getItem('cms_revisions') || '[]'); } catch(e) {}
    },

    saveRevisionsToStorage() {
      localStorage.setItem('cms_revisions', JSON.stringify(this.revisions));
    },

    // ==================== SAVE/LOAD ====================
    savePage(status) {
      const data = {
        components: this.components,
        globalStyles: this.globalStyles,
        seoData: this.seoData,
        savedAt: Date.now(),
      };
      localStorage.setItem('cms_page_data', JSON.stringify(data));
      this.isDirty = false;
      this.autoSaveIndicator = true;
      setTimeout(() => this.autoSaveIndicator = false, 3000);
      this.showToast('Page saved!', 'success');

      // Laravel server save
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const pageId = document.querySelector('meta[name="page-id"]')?.getAttribute('content') || null;
      const url = pageId
        ? `/admin/pages/${pageId}`
        : `{{ route("admin.pages.store") }}`;
      const method = pageId ? 'PUT' : 'POST';

      fetch(url, {
        method: method,
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          title: this.seoData.title || 'Untitled Page',
          slug: (this.seoData.title || 'untitled').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, ''),
          content: JSON.stringify(data),
          status: status || 'draft',
          meta_title: this.seoData.title,
          meta_description: this.seoData.meta,
        })
      })
      .then(async res => {
        const payload = await res.json().catch(() => ({}));

        if (!res.ok) {
          const message = payload.message || payload.errors?.title?.[0] || payload.errors?.slug?.[0] || 'Unable to save page. Change the page name and try again.';
          this.showToast(message, 'error');
          return null;
        }

        return payload;
      })
      .then(res => {
        if (!res) return;

        if (res.success && res.page_id) {
          let meta = document.querySelector('meta[name="page-id"]');
          if (!meta) { meta = document.createElement('meta'); meta.name = 'page-id'; document.head.appendChild(meta); }
          meta.setAttribute('content', res.page_id);
          if (res.redirect) window.location.href = res.redirect;
          if (status === 'published') {
            this.showToast('Page published successfully.', 'success');
          }
        }
      })
      .catch(err => {
        console.warn('Server save error:', err);
        this.showToast('Server save error. Please try again.', 'error');
      });
    },

    loadFromStorage() {
      if (!this.isNewPage) return;
      localStorage.removeItem('cms_page_data');
    },

    loadInitialPageData() {
      if (!initialPageData) return;
      try {
        const data = typeof initialPageData === 'string' ? JSON.parse(initialPageData) : initialPageData;
        this.components = data.components || [];
        if (data.globalStyles) this.globalStyles = { ...this.globalStyles, ...data.globalStyles };
        if (data.seoData) this.seoData = { ...this.seoData, ...data.seoData };
        if (data.title) this.seoData.title = data.title;
        if (data.meta_description) this.seoData.meta = data.meta_description;
      } catch (e) {
        console.warn('Failed to load initial page data:', e);
      }
    },

    autoSave() {
      if (this.isDirty) {
        this.savePage();
        this.autoSaveIndicator = true;
        setTimeout(() => this.autoSaveIndicator = false, 2000);
      }
    },

    publishPage() {
      this.savePage('published');
    },

    markDirty() {
      this.isDirty = true;
    },

    // ==================== EXPORT / IMPORT ====================
    exportJSON() {
      const data = {
        version: '1.0',
        components: this.components,
        globalStyles: this.globalStyles,
        seoData: this.seoData,
        exportedAt: new Date().toISOString(),
      };
      const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'page.json';
      a.click();
      URL.revokeObjectURL(url);
      this.showToast('Page exported as page.json', 'success');
    },

    importJSON(e) {
      const file = e.target.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = (ev) => {
        try {
          const data = JSON.parse(ev.target.result);
          this.pushHistory();
          this.components = data.components || [];
          if (data.globalStyles) this.globalStyles = { ...this.globalStyles, ...data.globalStyles };
          if (data.seoData) this.seoData = { ...this.seoData, ...data.seoData };
          this.markDirty();
          this.showToast('Page imported successfully!', 'success');
        } catch (err) {
          this.showToast('Invalid JSON file', 'error');
        }
      };
      reader.readAsText(file);
      e.target.value = '';
    },

    // ==================== KEYBOARD SHORTCUTS ====================
    handleKeydown(e) {
      const ctrl = e.ctrlKey || e.metaKey;
      const inField = e.target.matches('input,textarea,select,[contenteditable]');
      if (e.key === '?') { this.showShortcutsModal = !this.showShortcutsModal; return; }
      if (ctrl && e.key === 's') { e.preventDefault(); this.savePage(); return; }
      if (ctrl && e.key === 'z') { e.preventDefault(); this.undo(); return; }
      if (ctrl && e.key === 'y') { e.preventDefault(); this.redo(); return; }
      if (ctrl && e.key === 'c' && this.selectedId) { e.preventDefault(); this.copyWidget(this.selectedId); return; }
      if (ctrl && e.key === 'v') { e.preventDefault(); this.pasteWidget(); return; }
      if (ctrl && e.key === 'd' && this.selectedId) { e.preventDefault(); this.duplicateWidget(this.selectedId); return; }
      if (ctrl && e.key === 'p') { e.preventDefault(); this.livePreview = !this.livePreview; return; }
      if (ctrl && e.key === 'g') { e.preventDefault(); this.snapGrid = !this.snapGrid; return; }
      // Arrow keys to move selected widget (without ctrl, not in field)
      if (!inField && this.selectedId) {
        if (e.key === 'ArrowUp' && e.altKey) { e.preventDefault(); this.moveWidgetUp(this.selectedId); return; }
        if (e.key === 'ArrowDown' && e.altKey) { e.preventDefault(); this.moveWidgetDown(this.selectedId); return; }
        // Arrow keys to select adjacent widget
        if (e.key === 'ArrowUp') {
          e.preventDefault();
          const idx = this.components.findIndex(c=>c.id===this.selectedId);
          if (idx > 0) this.selectWidget(this.components[idx-1].id);
          return;
        }
        if (e.key === 'ArrowDown') {
          e.preventDefault();
          const idx = this.components.findIndex(c=>c.id===this.selectedId);
          if (idx < this.components.length-1) this.selectWidget(this.components[idx+1].id);
          return;
        }
        if (e.key === 'Escape') { this.selectedId = null; return; }
      }
      if ((e.key === 'Delete' || e.key === 'Backspace') && this.selectedId && !inField) {
        e.preventDefault();
        this.deleteWidget(this.selectedId);
      }
    },

    handleBeforeUnload(e) {
      if (this.isDirty) {
        e.preventDefault();
        e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
      }
    },

    // ==================== DARK MODE ====================
    toggleDarkMode() {
      this.darkMode = !this.darkMode;
      localStorage.setItem('builder_dark', this.darkMode);
    },

    // ==================== CANVAS STYLES ====================
    canvasContainerStyle() {
      const widths = { desktop: '100%', tablet: '800px', mobile: '420px' };
      return `max-width:${widths[this.previewMode]||'100%'};`;
    },

    getPageBgStyle() {
      let style = `background-color:${this.globalStyles.bgColor};`;
      if (this.globalStyles.bgImage) {
        style += `background-image:url(${this.globalStyles.bgImage});background-size:${this.globalStyles.bgSize};background-position:center;`;
      }
      return style;
    },

    // ==================== SEO HELPERS ====================
    countImages() {
      return this.components.filter(c => c.type === 'image' || c.type === 'image-carousel').length;
    },

    countHeadings() {
      return this.components.filter(c => c.type === 'heading').length;
    },

    // ==================== ACCESSIBILITY CHECK ====================
    runA11yCheck() {
      const issues = [];
      let id = 0;
      this.components.forEach(c => {
        // Image without alt
        if (c.type === 'image' && !c.settings.alt) issues.push({id:id++,level:'error',title:'Image missing alt text',desc:`Widget "${c.settings.label||c.id}" has no alt attribute. Screen readers won't describe it.`});
        // Heading hierarchy
        if (c.type === 'heading' && c.settings.tag === 'h1') {
          const h1count = this.components.filter(x=>x.type==='heading'&&x.settings.tag==='h1').length;
          if (h1count > 1) issues.push({id:id++,level:'error',title:'Multiple H1 headings',desc:'Page should have only one H1 tag for SEO and accessibility.'});
        }
        // Button without text
        if (c.type === 'button' && !c.settings.text) issues.push({id:id++,level:'error',title:'Button has no text',desc:'Buttons need descriptive text for keyboard and screen reader users.'});
        // Low contrast hint
        if (c.type === 'heading' && c.settings.color === '#ffffff' && !c.settings.bgColor) issues.push({id:id++,level:'warning',title:'Possible low contrast',desc:`Heading "${c.settings.text}" uses white text — check background contrast ratio.`});
        // Form without labels
        if (c.type === 'contact-form' || c.type === 'form-advanced') {
          // Check ok — fields have labels in our render
        }
        // Video without captions note
        if (c.type === 'video') issues.push({id:id++,level:'warning',title:'Video: add captions',desc:'Ensure embedded videos have closed captions for hearing-impaired users.'});
      });
      // No H1 at all
      if (!this.components.some(c=>c.type==='heading'&&c.settings.tag==='h1')) {
        if (this.components.length > 0) issues.push({id:id++,level:'warning',title:'No H1 heading found',desc:'Add an H1 heading to clearly define the page topic for SEO and screen readers.'});
      }
      // SEO title check
      if (!this.seoData.title || this.seoData.title.length < 10) issues.push({id:id++,level:'warning',title:'SEO title too short',desc:'Page title should be at least 10 characters for good SEO.'});
      if (!this.seoData.meta) issues.push({id:id++,level:'warning',title:'Missing meta description',desc:'Add a meta description (100-160 chars) for better search engine snippets.'});
      this.a11yIssues = issues;
    },

    // ==================== PAGE VERSIONS ====================
    saveVersion() {
      if (!this.newVersionName.trim()) { this.showToast('Enter a version name', 'warning'); return; }
      const v = { name: this.newVersionName, date: Date.now(), components: JSON.parse(JSON.stringify(this.components)), globalStyles: JSON.parse(JSON.stringify(this.globalStyles)) };
      this.pageVersions.unshift(v);
      if (this.pageVersions.length > 10) this.pageVersions.pop();
      this.saveVersionsToStorage();
      this.newVersionName = '';
      this.showToast('Version saved!', 'success');
    },

    loadVersion(i) {
      this.pushHistory();
      this.components = JSON.parse(JSON.stringify(this.pageVersions[i].components));
      this.globalStyles = { ...this.globalStyles, ...this.pageVersions[i].globalStyles };
      this.showVersionsModal = false;
      this.markDirty();
      this.showToast('Version loaded!', 'success');
    },

    loadVersions() {
      try { this.pageVersions = JSON.parse(localStorage.getItem('cms_versions') || '[]'); } catch(e) {}
    },

    saveVersionsToStorage() {
      localStorage.setItem('cms_versions', JSON.stringify(this.pageVersions));
    },

    // ==================== WIDGET LIBRARY FILTER ====================
    filteredWidgetCategories() {
      if (!this.widgetSearch) return this.widgetCategories;
      const q = this.widgetSearch.toLowerCase();
      return this.widgetCategories.map(cat => ({
        ...cat,
        open: true,
        widgets: cat.widgets.filter(w => w.label.toLowerCase().includes(q) || w.type.includes(q))
      })).filter(cat => cat.widgets.length > 0);
    },

    // ==================== WIDGET ICON HELPER ====================
    getWidgetIcon(type) {
      const icons = {
        section: 'fa-square', container: 'fa-box', columns: 'fa-columns', spacer: 'fa-arrows-alt-v',
        divider: 'fa-minus', heading: 'fa-heading', paragraph: 'fa-paragraph', button: 'fa-hand-pointer',
        image: 'fa-image', video: 'fa-video', icon: 'fa-star', 'icon-list': 'fa-list-ul',
        testimonial: 'fa-quote-right', 'team-member': 'fa-user-tie', pricing: 'fa-tag',
        accordion: 'fa-layer-group', tabs: 'fa-folder', counter: 'fa-sort-numeric-up',
        'progress-bar': 'fa-tasks', 'circle-progress': 'fa-circle-notch', countdown: 'fa-clock',
        'image-carousel': 'fa-images', 'before-after': 'fa-adjust', lottie: 'fa-film',
        'google-maps': 'fa-map-marker-alt', 'post-loop': 'fa-rss', 'post-meta': 'fa-info',
        'author-box': 'fa-user', 'custom-field': 'fa-database', 'contact-form': 'fa-envelope',
        'subscribe-form': 'fa-bell', 'search-form': 'fa-search', 'raw-html': 'fa-code',
        'header-template': 'fa-window-maximize', 'footer-template': 'fa-grip-lines',
      };
      return icons[type] || 'fa-puzzle-piece';
    },

    // ==================== WIDGET-SPECIFIC HELPERS ====================
    addAccordionItem() {
      const w = this.selectedWidget();
      if (w && w.settings.items) {
        w.settings.items.push({ title: 'New Section', content: '<p>New content</p>', open: false });
        this.pushHistory();
      }
    },

    addTabItem() {
      const w = this.selectedWidget();
      if (w && w.settings.items) {
        w.settings.items.push({ label: 'New Tab', content: '<p>Tab content</p>' });
        this.pushHistory();
      }
    },

    updateFeatures() {
      const w = this.selectedWidget();
      if (w && this.featuresText !== undefined) {
        w.settings.features = this.featuresText.split('\n').filter(f => f.trim());
        this.pushHistory();
      }
    },

    // ==================== TOAST NOTIFICATIONS ====================
    showToast(message, type = 'info') {
      const id = Date.now();
      this.toasts.push({ id, message, type });
      setTimeout(() => this.removeToast(id), 4000);
    },

    removeToast(id) {
      this.toasts = this.toasts.filter(t => t.id !== id);
    },
  };
}

// Alias so x-init="initV5()" works — Alpine calls the function on the data object
// pageBuilderV5().init() is called via x-init="initV5()" — we map it here:
// Actually Alpine x-init calls a method on the component, so we keep init() and add alias
function initV5() { /* not used directly — x-init on component uses init() */ }
</script>
</body>
</html>
