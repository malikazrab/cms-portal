{{--
|--------------------------------------------------------------------------
| Partial: _panel_left.blade.php
| Path:    resources/views/admin/pages/partials/_panel_left.blade.php
|--------------------------------------------------------------------------
| Contains the full left sidebar with four tabs:
|
|   1. Widgets   — searchable drag-and-drop widget library
|   2. Styles    — global colors, typography, page background, templates
|   3. Tree      — component tree with nested children
|   4. Rev.      — quick revision history with restore button
|
| Hidden automatically when livePreview is active.
|--------------------------------------------------------------------------
--}}

<aside
  x-show="!livePreview"
  class="w-64 flex-shrink-0 bg-white dark:bg-gray-900 border-r dark:border-gray-700 flex flex-col overflow-hidden">

  {{-- ── Tab nav ─────────────────────────────────────────────────────── --}}
  <div class="flex border-b dark:border-gray-700 text-xs font-medium">
    <button
      @click="leftTab = 'widgets'"
      :class="leftTab === 'widgets' ? 'border-b-2 border-brand-500 text-brand-500' : 'text-gray-500 hover:text-gray-700'"
      class="flex-1 py-2.5 px-1 text-center">
      Widgets
    </button>
    <button
      @click="leftTab = 'styles'"
      :class="leftTab === 'styles' ? 'border-b-2 border-brand-500 text-brand-500' : 'text-gray-500 hover:text-gray-700'"
      class="flex-1 py-2.5 px-1 text-center">
      Styles
    </button>
    <button
      @click="leftTab = 'tree'"
      :class="leftTab === 'tree' ? 'border-b-2 border-brand-500 text-brand-500' : 'text-gray-500 hover:text-gray-700'"
      class="flex-1 py-2.5 px-1 text-center">
      Tree
    </button>
    <button
      @click="leftTab = 'revisions'"
      :class="leftTab === 'revisions' ? 'border-b-2 border-brand-500 text-brand-500' : 'text-gray-500 hover:text-gray-700'"
      class="flex-1 py-2.5 px-1 text-center">
      Rev.
    </button>
  </div>

  {{-- ════════════════════════════════════════════════════════════════════
       TAB 1 — WIDGETS
  ═════════════════════════════════════════════════════════════════════ --}}
  <div x-show="leftTab === 'widgets'" class="flex-1 flex flex-col overflow-hidden">

    {{-- Search bar --}}
    <div class="p-2">
      <div class="relative">
        <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
        <input
          x-model="widgetSearch"
          type="text"
          placeholder="Search widgets..."
          class="w-full pl-8 pr-3 py-2 border dark:border-gray-600 rounded-lg text-xs dark:bg-gray-800 focus:outline-none focus:ring-1 focus:ring-brand-500">
      </div>
    </div>

    {{-- Category accordion + widget grid --}}
    <div class="flex-1 overflow-y-auto px-2 pb-2 space-y-2">
      <template x-for="cat in filteredWidgetCategories()" :key="cat.name">
        <div>

          {{-- Category header --}}
          <button
            @click="cat.open = !cat.open"
            class="flex items-center justify-between w-full py-1.5 px-2 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide hover:text-gray-700 dark:hover:text-gray-200">
            <span x-text="cat.name"></span>
            <i class="fas text-[10px]" :class="cat.open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
          </button>

          {{-- Widget cards (2-column grid) --}}
          <div x-show="cat.open" class="grid grid-cols-2 gap-1">
            <template x-for="widget in cat.widgets" :key="widget.type">
              <div
                class="widget-lib-item flex flex-col items-center gap-1 p-2 border dark:border-gray-700 rounded-lg hover:border-brand-500 hover:bg-brand-50 dark:hover:bg-brand-900/20 cursor-grab transition-colors text-center"
                draggable="true"
                @dragstart="startDragFromLibrary($event, widget.type)"
                @click="addWidgetToCanvas(widget.type)">
                <i class="fas text-brand-500 text-sm" :class="widget.icon"></i>
                <span class="text-[10px] text-gray-600 dark:text-gray-400 leading-tight" x-text="widget.label"></span>
              </div>
            </template>
          </div>

        </div>
      </template>
    </div>
  </div>

  {{-- ════════════════════════════════════════════════════════════════════
       TAB 2 — GLOBAL STYLES
  ═════════════════════════════════════════════════════════════════════ --}}
  <div x-show="leftTab === 'styles'" class="flex-1 overflow-y-auto p-3 space-y-4">

    {{-- Global Colors --}}
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

    {{-- Typography --}}
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

    {{-- Page Background --}}
    <div>
      <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Page Background</h4>
      <div class="space-y-2">

        <div class="flex items-center gap-2">
          <label class="text-xs text-gray-600 dark:text-gray-400 w-16">Color</label>
          <input type="color" x-model="globalStyles.bgColor" class="w-8 h-8 rounded cursor-pointer border-0">
        </div>

        <div>
          <label class="text-xs text-gray-600 dark:text-gray-400 block mb-1">Image URL</label>
          <input
            type="url"
            x-model="globalStyles.bgImage"
            placeholder="https://..."
            class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800">
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

    {{-- Templates shortcut --}}
    <div>
      <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Templates</h4>
      <button
        @click="showTemplatesModal = true"
        class="w-full py-2 bg-brand-500 text-white rounded-lg text-xs font-medium hover:bg-brand-600">
        <i class="fas fa-layer-group mr-1"></i> Manage Templates
      </button>
    </div>

  </div>

  {{-- ════════════════════════════════════════════════════════════════════
       TAB 3 — COMPONENT TREE
  ═════════════════════════════════════════════════════════════════════ --}}
  <div x-show="leftTab === 'tree'" class="flex-1 overflow-y-auto p-3">

    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Component Tree</h4>

    <div class="space-y-1">

      <template x-if="components.length === 0">
        <p class="text-xs text-gray-400 text-center py-4">No components yet</p>
      </template>

      <template x-for="(comp, i) in components" :key="comp.id">
        <div>

          {{-- Top-level row --}}
          <div
            class="flex items-center gap-1.5 py-1 px-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer text-xs"
            :class="selectedId === comp.id ? 'bg-brand-50 dark:bg-brand-900/20 text-brand-600' : ''"
            @click="selectWidget(comp.id)">
            <i class="fas fa-grip-dots text-gray-300 text-[10px]"></i>
            <i class="fas text-brand-400 text-[10px]" :class="getWidgetIcon(comp.type)"></i>
            <span class="truncate flex-1" x-text="comp.settings.label || comp.type"></span>
            <button
              @click.stop="deleteWidget(comp.id)"
              class="text-gray-300 hover:text-red-400 text-[10px]">
              <i class="fas fa-times"></i>
            </button>
          </div>

          {{-- Nested children --}}
          <template x-if="comp.children && comp.children.length">
            <div class="ml-4 border-l dark:border-gray-700 pl-2 space-y-0.5">
              <template x-for="child in comp.children" :key="child.id">
                <div
                  class="flex items-center gap-1.5 py-0.5 px-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer text-xs"
                  :class="selectedId === child.id ? 'bg-brand-50 dark:bg-brand-900/20 text-brand-600' : ''"
                  @click="selectWidget(child.id)">
                  <i class="fas text-gray-300 text-[10px]" :class="getWidgetIcon(child.type)"></i>
                  <span class="truncate" x-text="child.settings.label || child.type"></span>
                </div>
              </template>
            </div>
          </template>

        </div>
      </template>

    </div>
  </div>

  {{-- ════════════════════════════════════════════════════════════════════
       TAB 4 — REVISIONS
  ═════════════════════════════════════════════════════════════════════ --}}
  <div x-show="leftTab === 'revisions'" class="flex-1 overflow-y-auto p-3">

    <div class="flex items-center justify-between mb-2">
      <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide">Revisions</h4>
      <button
        @click="saveRevision('Manual')"
        class="text-xs text-brand-500 hover:text-brand-600">
        + Save
      </button>
    </div>

    <div class="space-y-1">

      <template x-if="revisions.length === 0">
        <p class="text-xs text-gray-400 text-center py-4">No revisions</p>
      </template>

      <template x-for="(rev, i) in revisions" :key="i">
        <div class="flex items-center gap-2 py-1.5 px-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-xs">
          <i class="fas fa-history text-gray-400"></i>
          <div class="flex-1 min-w-0">
            <p class="font-medium truncate" x-text="rev.label"></p>
            <p class="text-gray-400 text-[10px]" x-text="new Date(rev.date).toLocaleString()"></p>
          </div>
          <button
            @click="restoreRevision(i)"
            class="text-brand-500 hover:text-brand-600 font-medium"
            title="Restore this revision">
            ↩
          </button>
        </div>
      </template>

    </div>
  </div>

</aside>
