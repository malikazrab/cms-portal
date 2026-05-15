{{--
|--------------------------------------------------------------------------
| Partial: _panel_right.blade.php
| Path:    resources/views/admin/pages/partials/_panel_right.blade.php
|--------------------------------------------------------------------------
| Contains the full right sidebar with three tabs (shown when a widget
| is selected) plus a fallback SEO/page-settings view (no selection):
|
|   Header  — selected widget icon + type name  /  "Page Settings & SEO"
|   Tabs    — Content  |  Style  |  Advanced
|
|   Content  tab — renderSettingsPanel() (widget-specific HTML from JS)
|   Style    tab — Size, Padding, Margin, Layout (Flex/Grid),
|                  Background, Border, Shadow, Typography,
|                  Hover States, Opacity & Transform, Custom CSS
|   Advanced tab — Element ID, CSS Classes, Hide on breakpoint,
|                  Animation, Widget Label
|
|   No-selection view — SEO: Page Title bar, Meta Description bar,
|                        page-stats summary card
|
|   Footer  — Canvas Zoom slider (always visible, pinned to bottom)
|
| Hidden automatically when livePreview is active.
|--------------------------------------------------------------------------
--}}

<aside
  x-show="!livePreview"
  class="w-72 flex-shrink-0 bg-white dark:bg-gray-900 border-l dark:border-gray-700 flex flex-col overflow-hidden">

  {{-- ── Panel header ─────────────────────────────────────────────────── --}}
  <div class="p-3 border-b dark:border-gray-700">

    {{-- Widget selected: show icon + type --}}
    <template x-if="selectedWidget()">
      <div class="flex items-center gap-2">
        <i class="fas text-brand-500" :class="getWidgetIcon(selectedWidget().type)"></i>
        <span class="font-semibold text-sm capitalize" x-text="selectedWidget().type"></span>
      </div>
    </template>

    {{-- Nothing selected: page-level heading --}}
    <template x-if="!selectedWidget()">
      <p class="text-sm font-semibold text-gray-600 dark:text-gray-400" x-text="builderMode === 'header' ? 'Header Settings' : 'Page Settings & SEO'"></p>
    </template>

  </div>

  {{-- ── Tab nav (only visible when a widget is selected) ────────────── --}}
  <div x-show="selectedWidget()" class="flex border-b dark:border-gray-700 text-xs">
    <button
      @click="rightTab = 'content'"
      :class="rightTab === 'content' ? 'border-b-2 border-brand-500 text-brand-500' : 'text-gray-500 hover:text-gray-700'"
      class="flex-1 py-2 text-center">
      Content
    </button>
    <button
      @click="rightTab = 'style'"
      :class="rightTab === 'style' ? 'border-b-2 border-brand-500 text-brand-500' : 'text-gray-500 hover:text-gray-700'"
      class="flex-1 py-2 text-center">
      Style
    </button>
    <button
      @click="rightTab = 'advanced'"
      :class="rightTab === 'advanced' ? 'border-b-2 border-brand-500 text-brand-500' : 'text-gray-500 hover:text-gray-700'"
      class="flex-1 py-2 text-center">
      Advanced
    </button>
  </div>

  {{-- ── Scrollable panel body ────────────────────────────────────────── --}}
  <div class="flex-1 overflow-y-auto p-3 space-y-3 text-sm">

    {{-- ══════════════════════════════════════════════════════════════════
         NO SELECTION — SEO & Page Stats
    ═══════════════════════════════════════════════════════════════════ --}}
    <div x-show="!selectedWidget()">
      <div class="space-y-3">
        <div>
          <h4 class="font-bold text-xs text-gray-500 uppercase tracking-wide mb-2">SEO Analysis</h4>
          <div class="space-y-2">

            {{-- Page Title / Header Name --}}
            <div>
              <label class="text-xs text-gray-600 dark:text-gray-400 flex justify-between mb-1">
                <span x-text="builderMode === 'header' ? 'Header Name' : 'Page Title'"></span>
                <span
                  :class="(builderMode === 'header' ? headerName.length : seoData.title.length) > 60 ? 'text-red-500' : (builderMode === 'header' ? headerName.length : seoData.title.length) > 30 ? 'text-green-500' : 'text-yellow-500'"
                  x-text="(builderMode === 'header' ? headerName.length : seoData.title.length) + '/60'">
                </span>
              </label>
              <input
                x-model="builderMode === 'header' ? headerName : seoData.title"
                type="text"
                class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800">
              <div class="mt-1 h-1 bg-gray-100 dark:bg-gray-700 rounded">
                <div
                  class="h-full rounded transition-all"
                  :class="(builderMode === 'header' ? headerName.length : seoData.title.length) > 60 ? 'bg-red-500' : (builderMode === 'header' ? headerName.length : seoData.title.length) > 30 ? 'bg-green-500' : 'bg-yellow-500'"
                  :style="`width:${Math.min(100,((builderMode === 'header' ? headerName.length : seoData.title.length)/60)*100)}%`">
                </div>
              </div>
            </div>

            {{-- Meta Description --}}
            <div>
              <label class="text-xs text-gray-600 dark:text-gray-400 flex justify-between mb-1">
                <span>Meta Description</span>
                <span
                  :class="seoData.meta.length > 160 ? 'text-red-500' : seoData.meta.length > 60 ? 'text-green-500' : 'text-yellow-500'"
                  x-text="seoData.meta.length + '/160'">
                </span>
              </label>
              <textarea
                x-model="seoData.meta"
                rows="3"
                class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800 resize-none">
              </textarea>
            </div>

            {{-- Page stats summary --}}
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

    {{-- ══════════════════════════════════════════════════════════════════
         TAB 1 — CONTENT  (widget-specific settings rendered by JS)
    ═══════════════════════════════════════════════════════════════════ --}}
    <div x-show="selectedWidget() && rightTab === 'content'">
      <div x-html="renderSettingsPanel()"></div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         TAB 2 — STYLE
    ═══════════════════════════════════════════════════════════════════ --}}
    <div x-show="selectedWidget() && rightTab === 'style'">
      <template x-if="selectedWidget()">
        <div class="space-y-4 text-xs">

          {{-- SIZE ────────────────────────────────────────────────────── --}}
          <div>
            <label class="text-xs text-gray-500 uppercase tracking-wide font-bold block mb-2">Size</label>
            <div class="grid grid-cols-2 gap-2">
              <div>
                <label class="text-gray-500 block mb-0.5">Width</label>
                <input
                  type="text"
                  x-model="selectedWidget().settings.width"
                  placeholder="100% or 400px"
                  class="w-full border dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800"
                  @change="pushHistory();markDirty()">
              </div>
              <div>
                <label class="text-gray-500 block mb-0.5">Height</label>
                <input
                  type="text"
                  x-model="selectedWidget().settings.height"
                  placeholder="auto or 200px"
                  class="w-full border dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800"
                  @change="pushHistory();markDirty()">
              </div>
            </div>
            {{-- Quick-width presets --}}
            <div class="flex gap-1 mt-1.5">
              <button @click="selectedWidget().settings.width='25%';pushHistory()" class="flex-1 py-1 bg-gray-100 dark:bg-gray-700 rounded text-center hover:bg-brand-100 hover:text-brand-600">25%</button>
              <button @click="selectedWidget().settings.width='50%';pushHistory()" class="flex-1 py-1 bg-gray-100 dark:bg-gray-700 rounded text-center hover:bg-brand-100 hover:text-brand-600">50%</button>
              <button @click="selectedWidget().settings.width='75%';pushHistory()" class="flex-1 py-1 bg-gray-100 dark:bg-gray-700 rounded text-center hover:bg-brand-100 hover:text-brand-600">75%</button>
              <button @click="selectedWidget().settings.width='100%';pushHistory()" class="flex-1 py-1 bg-gray-100 dark:bg-gray-700 rounded text-center hover:bg-brand-100 hover:text-brand-600">100%</button>
            </div>
          </div>

          {{-- PADDING ──────────────────────────────────────────────────── --}}
          <div>
            <label class="text-xs text-gray-500 uppercase tracking-wide font-bold block mb-2">
              Padding <span class="text-gray-400 normal-case font-normal">(px)</span>
            </label>
            <div class="grid grid-cols-3 gap-1 items-center justify-items-center">
              <div></div>
              <input type="number" x-model.number="selectedWidget().settings.pt" placeholder="T" title="Top"
                class="w-12 text-center text-xs border dark:border-gray-600 rounded px-1 py-1 dark:bg-gray-800"
                @change="pushHistory();markDirty()" min="0">
              <div></div>
              <input type="number" x-model.number="selectedWidget().settings.pl" placeholder="L" title="Left"
                class="w-12 text-center text-xs border dark:border-gray-600 rounded px-1 py-1 dark:bg-gray-800"
                @change="pushHistory();markDirty()" min="0">
              <div class="w-10 h-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded flex items-center justify-center text-[9px] text-gray-400">P</div>
              <input type="number" x-model.number="selectedWidget().settings.pr" placeholder="R" title="Right"
                class="w-12 text-center text-xs border dark:border-gray-600 rounded px-1 py-1 dark:bg-gray-800"
                @change="pushHistory();markDirty()" min="0">
              <div></div>
              <input type="number" x-model.number="selectedWidget().settings.pb" placeholder="B" title="Bottom"
                class="w-12 text-center text-xs border dark:border-gray-600 rounded px-1 py-1 dark:bg-gray-800"
                @change="pushHistory();markDirty()" min="0">
              <div></div>
            </div>
          </div>

          {{-- MARGIN ───────────────────────────────────────────────────── --}}
          <div>
            <label class="text-xs text-gray-500 uppercase tracking-wide font-bold block mb-2">
              Margin <span class="text-gray-400 normal-case font-normal">(px)</span>
            </label>
            <div class="grid grid-cols-3 gap-1 items-center justify-items-center">
              <div></div>
              <input type="number" x-model.number="selectedWidget().settings.mt" placeholder="T"
                class="w-12 text-center text-xs border dark:border-gray-600 rounded px-1 py-1 dark:bg-gray-800"
                @change="pushHistory();markDirty()">
              <div></div>
              <input type="number" x-model.number="selectedWidget().settings.ml" placeholder="L"
                class="w-12 text-center text-xs border dark:border-gray-600 rounded px-1 py-1 dark:bg-gray-800"
                @change="pushHistory();markDirty()">
              <div class="w-10 h-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded flex items-center justify-center text-[9px] text-gray-400">M</div>
              <input type="number" x-model.number="selectedWidget().settings.mr" placeholder="R"
                class="w-12 text-center text-xs border dark:border-gray-600 rounded px-1 py-1 dark:bg-gray-800"
                @change="pushHistory();markDirty()">
              <div></div>
              <input type="number" x-model.number="selectedWidget().settings.mb" placeholder="B"
                class="w-12 text-center text-xs border dark:border-gray-600 rounded px-1 py-1 dark:bg-gray-800"
                @change="pushHistory();markDirty()">
              <div></div>
            </div>
          </div>

          {{-- LAYOUT (FLEX / GRID) ──────────────────────────────────────── --}}
          <div>
            <label class="text-xs text-gray-500 uppercase tracking-wide font-bold block mb-2">Layout (Flex/Grid)</label>
            <div class="space-y-2">

              {{-- Display mode selector --}}
              <div>
                <label class="text-gray-400 block mb-0.5">Display</label>
                <div class="flex gap-1">
                  <template x-for="d in ['block','flex','grid','inline-flex']" :key="d">
                    <button
                      @click="selectedWidget().settings.display = d; pushHistory(); markDirty()"
                      :class="selectedWidget().settings.display === d ? 'bg-brand-500 text-white' : 'bg-gray-100 dark:bg-gray-700'"
                      class="flex-1 py-1 rounded text-center text-[10px] font-mono"
                      x-text="d">
                    </button>
                  </template>
                </div>
              </div>

              {{-- Flex options --}}
              <template x-if="selectedWidget().settings.display === 'flex' || selectedWidget().settings.display === 'inline-flex'">
                <div class="space-y-1.5">
                  <div>
                    <label class="text-gray-400 block mb-0.5">Direction</label>
                    <div class="flex gap-1">
                      <template x-for="d in ['row','column','row-reverse','column-reverse']" :key="d">
                        <button
                          @click="selectedWidget().settings.flexDir = d; pushHistory(); markDirty()"
                          :class="selectedWidget().settings.flexDir === d ? 'bg-brand-500 text-white' : 'bg-gray-100 dark:bg-gray-700'"
                          class="flex-1 py-1 rounded text-[9px] font-mono"
                          x-text="d.replace('-reverse','↩')">
                        </button>
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
                    <label class="text-gray-400 flex justify-between mb-0.5">
                      <span>Gap (px)</span>
                      <span x-text="selectedWidget().settings.flexGap || 0"></span>
                    </label>
                    <input type="range" min="0" max="60" x-model.number="selectedWidget().settings.flexGap" @change="pushHistory();markDirty()" class="w-full accent-brand-500">
                  </div>
                  <label class="flex items-center gap-2">
                    <input type="checkbox" x-model="selectedWidget().settings.flexWrap" @change="pushHistory();markDirty()">
                    <span class="text-gray-400">Wrap</span>
                  </label>
                </div>
              </template>

              {{-- Grid options --}}
              <template x-if="selectedWidget().settings.display === 'grid'">
                <div class="space-y-1.5">
                  <div>
                    <label class="text-gray-400 block mb-0.5">Grid Columns</label>
                    <input type="text" x-model="selectedWidget().settings.gridCols" placeholder="repeat(3,1fr) or 1fr 2fr"
                      @change="pushHistory();markDirty()"
                      class="w-full border dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800 font-mono text-xs">
                  </div>
                  <div>
                    <label class="text-gray-400 block mb-0.5">Grid Rows</label>
                    <input type="text" x-model="selectedWidget().settings.gridRows" placeholder="auto or 200px auto"
                      @change="pushHistory();markDirty()"
                      class="w-full border dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800 font-mono text-xs">
                  </div>
                  <div>
                    <label class="text-gray-400 flex justify-between mb-0.5">
                      <span>Gap (px)</span>
                      <span x-text="selectedWidget().settings.gridGap || 0"></span>
                    </label>
                    <input type="range" min="0" max="60" x-model.number="selectedWidget().settings.gridGap" @change="pushHistory();markDirty()" class="w-full accent-brand-500">
                  </div>
                </div>
              </template>

            </div>
          </div>

          {{-- BACKGROUND ───────────────────────────────────────────────── --}}
          <div>
            <label class="text-xs text-gray-500 uppercase tracking-wide font-bold block mb-2">Background</label>
            <div class="flex items-center gap-2 mb-2">
              <input type="color" x-model="selectedWidget().settings.bgColor" class="w-8 h-8 rounded border-0 cursor-pointer" @change="pushHistory();markDirty()">
              <span class="text-xs font-mono text-gray-400" x-text="selectedWidget().settings.bgColor"></span>
            </div>
            <input
              type="text"
              x-model="selectedWidget().settings.bgGradient"
              placeholder="linear-gradient(135deg,#667eea,#764ba2)"
              class="w-full border dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800 font-mono text-xs mb-1"
              @change="pushHistory();markDirty()">
            <label class="text-gray-400 text-xs">Gradient (CSS)</label>
          </div>

          {{-- BORDER ───────────────────────────────────────────────────── --}}
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
                <input type="number" x-model.number="selectedWidget().settings.borderWidth" min="0" max="20"
                  class="w-full border dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800"
                  @change="pushHistory();markDirty()">
              </div>
            </div>
            <div class="flex items-center gap-2 mb-2">
              <input type="color" x-model="selectedWidget().settings.borderColor" class="w-8 h-8 rounded border-0" @change="pushHistory();markDirty()">
              <span class="text-xs text-gray-400">Border Color</span>
            </div>
            <div>
              <label class="text-gray-400 flex justify-between mb-0.5">
                <span>Border Radius (px)</span>
                <span x-text="selectedWidget().settings.borderRadius || 0"></span>
              </label>
              <input type="range" min="0" max="80" x-model.number="selectedWidget().settings.borderRadius" @change="pushHistory();markDirty()" class="w-full accent-brand-500">
            </div>
          </div>

          {{-- SHADOW ───────────────────────────────────────────────────── --}}
          <div>
            <label class="text-xs text-gray-500 uppercase tracking-wide font-bold block mb-2">Shadow</label>
            <select x-model="selectedWidget().settings.shadow" @change="pushHistory();markDirty()" class="w-full border dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800">
              <option value="">None</option>
              <option value="0 1px 3px rgba(0,0,0,.1)">Small</option>
              <option value="0 4px 12px rgba(0,0,0,.1)">Medium</option>
              <option value="0 8px 24px rgba(0,0,0,.15)">Large</option>
              <option value="0 20px 60px rgba(0,0,0,.2)">XL</option>
            </select>
          </div>

          {{-- TYPOGRAPHY ───────────────────────────────────────────────── --}}
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
                    <span class="text-xs font-mono text-gray-400 text-[10px]" x-text="selectedWidget().settings.textColor || '—'"></span>
                  </div>
                </div>
              </div>

              <div>
                <label class="text-gray-400 flex justify-between mb-0.5">
                  <span>Line Height</span>
                  <span x-text="(selectedWidget().settings.lineHeight || 1.5).toFixed(1)"></span>
                </label>
                <input type="range" min="0.8" max="3" step="0.1" x-model.number="selectedWidget().settings.lineHeight" @change="pushHistory();markDirty()" class="w-full accent-brand-500">
              </div>

              <div>
                <label class="text-gray-400 flex justify-between mb-0.5">
                  <span>Letter Spacing (em)</span>
                  <span x-text="(selectedWidget().settings.letterSpacing || 0).toFixed(2)"></span>
                </label>
                <input type="range" min="-0.1" max="0.5" step="0.01" x-model.number="selectedWidget().settings.letterSpacing" @change="pushHistory();markDirty()" class="w-full accent-brand-500">
              </div>

              <div>
                <label class="text-gray-400 block mb-0.5">Text Transform</label>
                <div class="flex gap-1">
                  <template x-for="tx in ['none','uppercase','lowercase','capitalize']" :key="tx">
                    <button
                      @click="selectedWidget().settings.textTransform = tx; pushHistory()"
                      :class="selectedWidget().settings.textTransform === tx ? 'bg-brand-500 text-white' : 'bg-gray-100 dark:bg-gray-700'"
                      class="flex-1 py-1 rounded text-[9px]"
                      x-text="tx === 'none' ? '—' : tx.slice(0,3).toUpperCase()">
                    </button>
                  </template>
                </div>
              </div>

            </div>
          </div>

          {{-- HOVER STATES ─────────────────────────────────────────────── --}}
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
                <label class="text-gray-400 flex justify-between mb-0.5">
                  <span>Transition (s)</span>
                  <span x-text="(selectedWidget().settings.transition || 0.3).toFixed(1)"></span>
                </label>
                <input type="range" min="0" max="1" step="0.1" x-model.number="selectedWidget().settings.transition" @change="pushHistory();markDirty()" class="w-full accent-brand-500">
              </div>
            </div>
          </div>

          {{-- OPACITY & TRANSFORM ──────────────────────────────────────── --}}
          <div>
            <label class="text-xs text-gray-500 uppercase tracking-wide font-bold block mb-2">Opacity & Transform</label>
            <div class="space-y-2">
              <div>
                <label class="text-gray-400 flex justify-between mb-0.5">
                  <span>Opacity</span>
                  <span x-text="(selectedWidget().settings.opacity !== undefined ? selectedWidget().settings.opacity : 1).toFixed(1)"></span>
                </label>
                <input type="range" min="0" max="1" step="0.05" x-model.number="selectedWidget().settings.opacity" @change="pushHistory();markDirty()" class="w-full accent-brand-500">
              </div>
              <div>
                <label class="text-gray-400 flex justify-between mb-0.5">
                  <span>Rotate (deg)</span>
                  <span x-text="selectedWidget().settings.rotate || 0"></span>
                </label>
                <input type="range" min="-180" max="180" x-model.number="selectedWidget().settings.rotate" @change="pushHistory();markDirty()" class="w-full accent-brand-500">
              </div>
              <div>
                <label class="text-gray-400 flex justify-between mb-0.5">
                  <span>Scale</span>
                  <span x-text="(selectedWidget().settings.scale || 1).toFixed(2)"></span>
                </label>
                <input type="range" min="0.5" max="2" step="0.05" x-model.number="selectedWidget().settings.scale" @change="pushHistory();markDirty()" class="w-full accent-brand-500">
              </div>
            </div>
          </div>

          {{-- CUSTOM CSS ───────────────────────────────────────────────── --}}
          <div>
            <label class="text-xs text-gray-500 uppercase tracking-wide font-bold block mb-1">Custom CSS</label>
            <textarea
              x-model="selectedWidget().settings.customCss"
              rows="4"
              placeholder="color: red; font-size: 20px;"
              class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800 font-mono resize-none"
              @change="pushHistory();markDirty()">
            </textarea>
          </div>

        </div>
      </template>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         TAB 3 — ADVANCED
    ═══════════════════════════════════════════════════════════════════ --}}
    <div x-show="selectedWidget() && rightTab === 'advanced'">
      <template x-if="selectedWidget()">
        <div class="space-y-3 text-xs">

          <div>
            <label class="text-gray-500 uppercase tracking-wide font-bold block mb-1">Element ID</label>
            <input
              type="text"
              x-model="selectedWidget().settings.elementId"
              placeholder="my-element"
              class="w-full border dark:border-gray-600 rounded px-2 py-1.5 dark:bg-gray-800"
              @change="pushHistory()">
          </div>

          <div>
            <label class="text-gray-500 uppercase tracking-wide font-bold block mb-1">CSS Classes</label>
            <input
              type="text"
              x-model="selectedWidget().settings.cssClasses"
              placeholder="class1 class2"
              class="w-full border dark:border-gray-600 rounded px-2 py-1.5 dark:bg-gray-800"
              @change="pushHistory()">
          </div>

          <div>
            <label class="text-gray-500 uppercase tracking-wide font-bold block mb-1">Hide on</label>
            <div class="space-y-1">
              <label class="flex items-center gap-2">
                <input type="checkbox" x-model="selectedWidget().settings.hideDesktop" @change="pushHistory()">
                <span>Desktop</span>
              </label>
              <label class="flex items-center gap-2">
                <input type="checkbox" x-model="selectedWidget().settings.hideTablet" @change="pushHistory()">
                <span>Tablet</span>
              </label>
              <label class="flex items-center gap-2">
                <input type="checkbox" x-model="selectedWidget().settings.hideMobile" @change="pushHistory()">
                <span>Mobile</span>
              </label>
            </div>
          </div>

          <div>
            <label class="text-gray-500 uppercase tracking-wide font-bold block mb-1">Animation</label>
            <select
              x-model="selectedWidget().settings.animation"
              class="w-full border dark:border-gray-600 rounded px-2 py-1.5 dark:bg-gray-800"
              @change="pushHistory()">
              <option value="">None</option>
              <option value="fade-in">Fade In</option>
              <option value="slide-up">Slide Up</option>
              <option value="slide-left">Slide Left</option>
              <option value="zoom-in">Zoom In</option>
            </select>
          </div>

          <div>
            <label class="text-gray-500 uppercase tracking-wide font-bold block mb-1">Widget Label</label>
            <input
              type="text"
              x-model="selectedWidget().settings.label"
              placeholder="Custom label for tree view"
              class="w-full border dark:border-gray-600 rounded px-2 py-1.5 dark:bg-gray-800"
              @change="pushHistory()">
          </div>

        </div>
      </template>
    </div>

  </div>

  {{-- ── Canvas Zoom — pinned to bottom, always visible ─────────────── --}}
  <div x-show="!livePreview" class="flex-shrink-0 border-t dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2">
    <p class="text-[10px] text-gray-400 uppercase tracking-wide font-bold mb-1.5">Canvas Zoom</p>
    <div class="flex items-center gap-2">
      <button
        @click="canvasZoom = Math.max(50, canvasZoom - 10)"
        class="w-6 h-6 flex items-center justify-center rounded bg-gray-100 dark:bg-gray-800 hover:bg-brand-100 hover:text-brand-600 text-sm font-bold">
        −
      </button>
      <input
        type="range"
        x-model.number="canvasZoom"
        min="50" max="150" step="5"
        class="flex-1 accent-brand-500">
      <button
        @click="canvasZoom = Math.min(150, canvasZoom + 10)"
        class="w-6 h-6 flex items-center justify-center rounded bg-gray-100 dark:bg-gray-800 hover:bg-brand-100 hover:text-brand-600 text-sm font-bold">
        +
      </button>
      <button
        @click="canvasZoom = 100"
        class="text-[10px] text-gray-400 hover:text-brand-500 font-mono border dark:border-gray-700 rounded px-1.5 py-0.5"
        title="Reset to 100%">
        <span x-text="canvasZoom + '%'"></span>
      </button>
    </div>
  </div>

</aside>
