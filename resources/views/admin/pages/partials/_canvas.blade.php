{{--
|--------------------------------------------------------------------------
| Partial: _canvas.blade.php
| Path:    resources/views/admin/pages/partials/_canvas.blade.php
|--------------------------------------------------------------------------
| Contains the central canvas area:
|
|   • Outer <main>  — grey background, click-away deselects widgets
|   • Canvas wrapper — respects canvasContainerStyle() for responsive
|                      preview widths (desktop / tablet / mobile)
|   • Canvas root   — the actual white page surface:
|       - snap grid overlay (optional)
|       - drag-over / drop event handlers
|       - right-click context menu trigger
|       - zoom transform via canvasZoom state
|       - global font-family + background from globalStyles
|   • Empty state   — illustrated placeholder shown when no components
|   • Sortable list — iterates components[], for each widget renders:
|       - Widget toolbar (move up/down, duplicate, delete, drag handle)
|       - Widget HTML  via renderWidget(comp)
|       - Resize corner indicator (shown when widget is selected)
|--------------------------------------------------------------------------
--}}

<main
  class="flex-1 overflow-auto relative bg-gray-200 dark:bg-gray-800"
  @click.self="selectedId = null">

  {{-- ── Responsive canvas wrapper ───────────────────────────────────── --}}
  <div class="mx-auto py-8 px-4" :style="canvasContainerStyle()">

    {{-- ── Canvas root (the "page") ─────────────────────────────────── --}}
    <div
      id="canvas-root"
      class="canvas-zoom bg-white dark:bg-gray-900 shadow-xl rounded-lg min-h-[600px] relative"
      :class="snapGrid ? 'snap-grid' : ''"
      :style="`transform:scale(${canvasZoom/100});transform-origin:top center;${getPageBgStyle()};font-family:${globalStyles.fontFamily}`"
      @dragover.prevent="onCanvasDragOver($event)"
      @drop.prevent="dropOnCanvas($event)"
      @contextmenu.prevent="showCanvasContextMenu($event)">

      {{-- ── Empty state ────────────────────────────────────────────── --}}
      <div
        x-show="components.length === 0"
        class="flex flex-col items-center justify-center py-32 text-center">
        <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
          <i class="fas fa-layer-group text-4xl text-gray-300"></i>
        </div>
        <p class="text-gray-400 font-medium mb-1">Drop widgets here to start building</p>
        <p class="text-gray-300 text-sm">Or click any widget in the left panel</p>
      </div>

      {{-- ── Sortable widget list ────────────────────────────────────── --}}
      <div id="sortable-canvas">
        <template x-for="(comp, idx) in components" :key="comp.id">

          <div
            class="canvas-widget relative"
            :class="selectedId === comp.id && !livePreview ? 'widget-selected' : ''"
            :data-id="comp.id"
            @click.stop="!livePreview && selectWidget(comp.id)"
            @contextmenu.prevent="!livePreview && openContextMenu($event, comp.id)">

            {{-- Widget toolbar (appears on hover, hidden in live preview) --}}
            <div
              x-show="!livePreview"
              class="widget-toolbar absolute -top-8 right-0 z-20 flex items-center gap-1 bg-brand-500 text-white rounded-t-lg px-2 py-1 text-xs">

              {{-- Drag handle --}}
              <i
                class="fas fa-grip-lines drag-handle mr-1 text-white/70"
                title="Drag to reorder">
              </i>

              {{-- Widget type label --}}
              <span class="mr-1 opacity-70 font-mono" x-text="comp.type"></span>

              {{-- Move up --}}
              <button
                @click.stop="moveWidgetUp(comp.id)"
                title="Move Up (Alt+↑)"
                class="hover:bg-brand-600 px-1 py-0.5 rounded">
                <i class="fas fa-arrow-up"></i>
              </button>

              {{-- Move down --}}
              <button
                @click.stop="moveWidgetDown(comp.id)"
                title="Move Down (Alt+↓)"
                class="hover:bg-brand-600 px-1 py-0.5 rounded">
                <i class="fas fa-arrow-down"></i>
              </button>

              {{-- Duplicate --}}
              <button
                @click.stop="duplicateWidget(comp.id)"
                title="Duplicate (Ctrl+D)"
                class="hover:bg-brand-600 px-1 py-0.5 rounded">
                <i class="fas fa-copy"></i>
              </button>

              {{-- Delete --}}
              <button
                @click.stop="deleteWidget(comp.id)"
                title="Delete (Del)"
                class="hover:bg-red-500 px-1 py-0.5 rounded">
                <i class="fas fa-trash"></i>
              </button>

            </div>
            {{-- end widget toolbar --}}

            {{-- Widget HTML output (rendered by JS renderWidget()) --}}
            <div x-html="renderWidget(comp)" class="w-full"></div>

            {{-- Resize corner indicator (only when selected, editor mode) --}}
            <div
              x-show="!livePreview && selectedId === comp.id"
              class="resize-indicator">
            </div>

          </div>
          {{-- end canvas-widget --}}

        </template>
      </div>
      {{-- end sortable-canvas --}}

    </div>
    {{-- end canvas-root --}}

  </div>
  {{-- end canvas wrapper --}}

</main>
