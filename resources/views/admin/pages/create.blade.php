<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Portal | Advanced Page Builder</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .canvas-widget { position: relative; transition: all 0.2s; margin-bottom: 12px; }
        .canvas-widget:hover > .widget-toolbar { display: flex !important; }
        .widget-toolbar { display: none; position: absolute; top: -32px; right: 0; z-index: 20; background: #0ea5e9; color: white; border-radius: 8px 8px 0 0; padding: 4px 8px; gap: 4px; font-size: 12px; }
        .widget-selected { outline: 2px solid #0ea5e9 !important; outline-offset: 2px; border-radius: 6px; }
        .sortable-ghost { opacity: 0.4; background: #c8ebfb; }
        .toast { animation: slideIn 0.3s ease; position: fixed; bottom: 20px; right: 20px; z-index: 9999; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .dark .bg-white { background-color: #1e293b !important; }
        .dark .border-gray-200 { border-color: #334155 !important; }
        .dark .bg-gray-50 { background-color: #0f172a !important; }
        .drag-handle { cursor: grab; }
        .drag-handle:active { cursor: grabbing; }
        .snap-grid { background-image: radial-gradient(circle, #cbd5e1 1px, transparent 1px); background-size: 20px 20px; }
        .dark .snap-grid { background-image: radial-gradient(circle, #374151 1px, transparent 1px); }
        .widget-lib-item { cursor: grab; transition: all 0.2s; }
        .widget-lib-item:active { cursor: grabbing; }
        .component-preview { min-height: 40px; }
        .progress-bar-fill { transition: width 0.6s ease; }
        .circle-progress { transform: rotate(-90deg); }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-950 text-gray-800 dark:text-gray-100 overflow-hidden" x-data="pageBuilderApp()" x-init="initBuilder()" @keydown.window="handleKeydown($event)" :class="{'dark': darkMode}">
@php
    $existingBuilderData = null;

    if (!empty($page?->content)) {
        $decodedContent = json_decode($page->content, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedContent)) {
            $existingBuilderData = $decodedContent;
        } else {
            $existingBuilderData = [
                'components' => [
                    [
                        'id' => 'legacy-html',
                        'type' => 'raw-html',
                        'settings' => [
                            'code' => $page->content,
                            'pt' => 10,
                            'pb' => 10,
                        ],
                    ],
                ],
                'globalStyles' => [
                    'primaryColor' => '#0ea5e9',
                    'fontFamily' => 'Inter, sans-serif',
                    'bgColor' => '#ffffff',
                    'bgImage' => '',
                ],
                'seoData' => [
                    'title' => $page->meta_title ?: $page->title,
                    'meta' => $page->meta_description ?? '',
                ],
            ];
        }
    }

    $pageBuilderConfig = [
        'pageId' => $page?->id,
        'saveUrl' => $page ? route('admin.pages.update', $page) : route('admin.pages.store'),
        'saveMethod' => $page ? 'PUT' : 'POST',
        'redirectUrl' => route('admin.pages.index'),
        'pageData' => $existingBuilderData,
    ];
@endphp
<script>
    window.pageBuilderConfig = @json($pageBuilderConfig);
</script>

<!-- TOAST NOTIFICATIONS -->
<div x-show="toastMessage" x-transition.duration.300ms class="toast bg-green-600 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-2">
    <i class="fa-regular fa-circle-check"></i>
    <span x-text="toastMessage"></span>
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
                <label class="flex items-center gap-2 px-4 py-2 bg-blue-500 text-white rounded-lg cursor-pointer hover:bg-blue-600 text-sm">
                    <i class="fas fa-upload"></i> Upload
                    <input type="file" accept="image/*" class="hidden" @change="uploadMediaImage($event)">
                </label>
                <input type="url" x-model="mediaUrlInput" placeholder="Image URL..." class="flex-1 border rounded-lg px-3 py-2 text-sm dark:bg-gray-700">
                <button @click="addMediaUrl()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg text-sm">Add URL</button>
            </div>
            <div class="grid grid-cols-4 gap-3 max-h-96 overflow-y-auto">
                <template x-for="img in mediaImages" :key="img">
                    <div class="cursor-pointer rounded-lg overflow-hidden border-2 hover:border-blue-500" :class="selectedMedia===img ? 'border-blue-500' : 'border-transparent'" @click="selectedMedia=img">
                        <img :src="img" class="w-full h-24 object-cover">
                    </div>
                </template>
            </div>
        </div>
        <div class="p-4 border-t flex justify-end gap-3">
            <button @click="showMediaLibrary=false" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
            <button @click="confirmMedia()" class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm">Insert</button>
        </div>
    </div>
</div>

<!-- AI MODAL -->
<div x-show="showAIModal" x-cloak class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/60">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-[500px]">
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="font-bold text-lg">✨ AI Writing Assistant</h3>
            <button @click="showAIModal=false" class="text-gray-400"><i class="fas fa-times text-xl"></i></button>
        </div>
        <div class="p-4 space-y-3">
            <textarea x-model="aiPrompt" rows="3" placeholder="Describe what you want to write..." class="w-full border rounded-lg px-3 py-2 text-sm dark:bg-gray-700"></textarea>
            <div class="flex gap-2 flex-wrap">
                <button @click="aiPrompt='Write a compelling product description'" class="text-xs px-2 py-1 bg-purple-100 rounded">Product</button>
                <button @click="aiPrompt='Write a hero section headline'" class="text-xs px-2 py-1 bg-purple-100 rounded">Hero</button>
                <button @click="aiPrompt='Write a testimonial'" class="text-xs px-2 py-1 bg-purple-100 rounded">Testimonial</button>
            </div>
            <div x-show="aiResult" class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm" x-text="aiResult"></div>
        </div>
        <div class="p-4 border-t flex justify-end gap-3">
            <button @click="showAIModal=false" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
            <button @click="generateAIText()" :disabled="aiLoading" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm">
                <span x-text="aiLoading ? 'Generating...' : 'Generate ✨'"></span>
            </button>
            <button x-show="aiResult" @click="insertAIText()" class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm">Insert</button>
        </div>
    </div>
</div>

<!-- TEMPLATES MODAL -->
<div x-show="showTemplatesModal" x-cloak class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/60">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-[500px] max-h-[80vh] flex flex-col">
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="font-bold text-lg">Templates</h3>
            <button @click="showTemplatesModal=false" class="text-gray-400"><i class="fas fa-times text-xl"></i></button>
        </div>
        <div class="p-4 border-b flex gap-2">
            <input x-model="newTemplateName" placeholder="Template name..." class="flex-1 border rounded-lg px-3 py-2 text-sm">
            <button @click="saveTemplate()" class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm">Save</button>
        </div>
        <div class="flex-1 overflow-y-auto p-4 space-y-2">
            <template x-for="(tpl, i) in templates" :key="i">
                <div class="flex items-center justify-between p-3 border rounded-lg">
                    <span x-text="tpl.name"></span>
                    <div class="flex gap-2">
                        <button @click="loadTemplate(i)" class="px-3 py-1 bg-blue-500 text-white rounded text-xs">Load</button>
                        <button @click="deleteTemplate(i)" class="px-3 py-1 bg-red-500 text-white rounded text-xs">Del</button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<!-- MAIN LAYOUT -->
<div class="flex flex-col h-screen">

    <!-- TOP BAR -->
    <header class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-900 border-b shadow-sm flex-shrink-0">
        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
            <i class="fas fa-layer-group text-white text-sm"></i>
        </div>
        <span class="font-bold">CMS Pro Builder</span>
        
        <div class="flex-1"></div>
        
        <button @click="undo()" :disabled="undoStack.length===0" class="p-2 rounded hover:bg-gray-100" title="Undo"><i class="fas fa-undo"></i></button>
        <button @click="redo()" :disabled="redoStack.length===0" class="p-2 rounded hover:bg-gray-100" title="Redo"><i class="fas fa-redo"></i></button>
        
        <div class="w-px h-6 bg-gray-300 mx-1"></div>
        
        <div class="flex bg-gray-100 rounded-lg p-0.5">
            <button @click="previewMode='desktop'" :class="previewMode==='desktop'?'bg-white shadow':''" class="p-1.5 rounded text-xs"><i class="fas fa-desktop"></i></button>
            <button @click="previewMode='tablet'" :class="previewMode==='tablet'?'bg-white shadow':''" class="p-1.5 rounded text-xs"><i class="fas fa-tablet-alt"></i></button>
            <button @click="previewMode='mobile'" :class="previewMode==='mobile'?'bg-white shadow':''" class="p-1.5 rounded text-xs"><i class="fas fa-mobile-alt"></i></button>
        </div>
        
        <button @click="livePreview=!livePreview" :class="livePreview?'bg-green-500 text-white':'bg-gray-200'" class="px-3 py-1 rounded-lg text-xs font-medium">
            <i class="fas" :class="livePreview?'fa-eye-slash':'fa-eye'"></i>
            <span x-text="livePreview?'Exit Preview':'Preview'"></span>
        </button>
        
        <button @click="showAIModal=true" class="px-3 py-1 rounded-lg text-xs bg-purple-100 text-purple-700">✨ AI</button>
        
        <span x-show="isDirty" class="text-xs text-yellow-500"><i class="fas fa-circle text-[8px]"></i> Unsaved</span>
        
        <button @click="snapGrid=!snapGrid" :class="snapGrid?'bg-blue-100':''" class="p-2 rounded"><i class="fas fa-th-large"></i></button>
        <button @click="toggleDarkMode()" class="p-2 rounded"><i class="fas" :class="darkMode?'fa-sun':'fa-moon'"></i></button>
        
        <button @click="exportJSON()" class="px-3 py-1 rounded-lg text-xs bg-gray-200"><i class="fas fa-download"></i> Export</button>
        <label class="px-3 py-1 rounded-lg text-xs bg-gray-200 cursor-pointer"><i class="fas fa-upload"></i> Import<input type="file" accept=".json" class="hidden" @change="importJSON($event)"></label>
        
        <button @click="savePage('draft')" class="px-3 py-1 rounded-lg text-xs bg-blue-600 text-white">Save</button>
        <button @click="publishPage()" class="px-3 py-1 rounded-lg text-xs bg-green-600 text-white">Publish</button>
    </header>

    <!-- MAIN CONTENT -->
    <div class="flex flex-1 overflow-hidden">
        
        <!-- LEFT SIDEBAR -->
        <aside x-show="!livePreview" class="w-64 flex-shrink-0 bg-white dark:bg-gray-900 border-r flex flex-col overflow-hidden">
            <div class="p-3 border-b">
                <input x-model="widgetSearch" type="text" placeholder="Search widgets..." class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="flex-1 overflow-y-auto p-3">
                <template x-for="cat in filteredWidgetCategories()" :key="cat.name">
                    <div class="mb-3">
                        <button @click="cat.open=!cat.open" class="flex items-center justify-between w-full py-1 text-xs font-bold uppercase">
                            <span x-text="cat.name"></span>
                            <i class="fas" :class="cat.open?'fa-chevron-down':'fa-chevron-right'"></i>
                        </button>
                        <div x-show="cat.open" class="grid grid-cols-2 gap-2 mt-2">
                            <template x-for="widget in cat.widgets" :key="widget.type">
                                <div class="widget-lib-item flex flex-col items-center gap-1 p-2 border rounded-lg hover:border-blue-500 cursor-grab text-center"
                                    draggable="true"
                                    @dragstart="startDragFromLibrary($event, widget.type)"
                                    @click.prevent="addWidgetToCanvas(widget.type)">
                                    <i class="fas text-blue-500" :class="widget.icon"></i>
                                    <span class="text-[10px]" x-text="widget.label"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </aside>

        <!-- CANVAS AREA -->
        <main class="flex-1 overflow-auto bg-gray-100 dark:bg-gray-800" @click.self="selectedId=null" @dragover.prevent @drop="dropOnCanvas($event)">
            
            <div class="mx-auto py-6 px-4 transition-all" :style="{ maxWidth: previewMode === 'desktop' ? '1200px' : (previewMode === 'tablet' ? '768px' : '420px'), transform: `scale(${canvasZoom})`, transformOrigin: 'top center' }">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg min-h-[600px] relative overflow-hidden"
                    :class="snapGrid ? 'snap-grid' : ''"
                    :style="`${getPageBgStyle()};font-family:${globalStyles.fontFamily}`">
                    
                    <!-- Empty State -->
                    <div x-show="components.length === 0" class="flex flex-col items-center justify-center py-32 text-center">
                        <i class="fas fa-layer-group text-5xl text-gray-300 mb-4"></i>
                        <p class="text-gray-400">Drop widgets here to start building</p>
                    </div>
                    
                    <!-- Components List -->
                    <div id="sortable-canvas" class="p-4 space-y-3">
                        <template x-for="comp in components" :key="comp.id">
                            <div class="canvas-widget relative border-2 border-transparent rounded-lg"
                                :class="selectedId === comp.id && !livePreview ? 'widget-selected border-blue-500' : ''"
                                :data-id="comp.id"
                                @click.stop="!livePreview && selectWidget(comp.id)">
                                
                                <!-- Widget Toolbar -->
                                <div x-show="!livePreview && selectedId === comp.id" class="widget-toolbar flex items-center gap-1">
                                    <i class="fas fa-grip-lines drag-handle cursor-grab"></i>
                                    <span class="text-xs opacity-70" x-text="comp.type"></span>
                                    <button @click.stop="duplicateWidget(comp.id)" class="hover:bg-white/20 px-1 rounded"><i class="fas fa-copy text-xs"></i></button>
                                    <button @click.stop="deleteWidget(comp.id)" class="hover:bg-red-500 px-1 rounded"><i class="fas fa-trash text-xs"></i></button>
                                </div>
                                
                                <!-- Widget Content -->
                                <div class="p-3 component-preview" x-html="renderWidgetContent(comp)"></div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            
            <!-- Zoom Controls -->
            <div class="fixed bottom-4 right-4 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-2 flex gap-2 border">
                <button @click="canvasZoom = Math.max(0.5, canvasZoom - 0.1)" class="px-3 py-1 bg-gray-200 rounded">-</button>
                <span class="px-2" x-text="Math.round(canvasZoom * 100) + '%'"></span>
                <button @click="canvasZoom = Math.min(1.5, canvasZoom + 0.1)" class="px-3 py-1 bg-gray-200 rounded">+</button>
            </div>
        </main>

        <!-- RIGHT SIDEBAR -->
        <aside x-show="!livePreview" class="w-80 flex-shrink-0 bg-white dark:bg-gray-900 border-l flex flex-col overflow-hidden">
            <div class="p-3 border-b">
                <template x-if="selectedWidget()">
                    <div class="flex items-center gap-2">
                        <i class="fas text-blue-500" :class="getWidgetIcon(selectedWidget().type)"></i>
                        <span class="font-semibold capitalize" x-text="selectedWidget().type"></span>
                    </div>
                </template>
                <template x-if="!selectedWidget()">
                    <p class="font-semibold">Page Settings</p>
                </template>
            </div>
            
            <div x-show="selectedWidget()" class="flex border-b text-xs">
                <button @click="rightTab='content'" :class="rightTab==='content'?'border-b-2 border-blue-500 text-blue-500':''" class="flex-1 py-2">Content</button>
                <button @click="rightTab='style'" :class="rightTab==='style'?'border-b-2 border-blue-500 text-blue-500':''" class="flex-1 py-2">Style</button>
            </div>
            
            <div class="flex-1 overflow-y-auto p-3 space-y-3">
                <!-- Widget Settings - Content Tab -->
                <div x-show="selectedWidget() && rightTab === 'content'">
                    <template x-if="selectedWidget().type === 'heading'">
                        <div class="space-y-3">
                            <div><label class="text-xs font-bold">Heading Text</label><input type="text" x-model="selectedWidget().settings.text" @change="pushHistory();markDirty()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Heading Tag</label><select x-model="selectedWidget().settings.tag" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"><option>h1</option><option>h2</option><option>h3</option><option>h4</option></select></div>
                            <div><label class="text-xs font-bold">Alignment</label><select x-model="selectedWidget().settings.alignment" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"><option>left</option><option>center</option><option>right</option></select></div>
                            <div><label class="text-xs font-bold">Color</label><input type="color" x-model="selectedWidget().settings.color" @change="pushHistory()" class="w-full h-8 rounded border"></div>
                            <div><label class="text-xs font-bold">Font Size (px)</label><input type="number" x-model="selectedWidget().settings.fontSize" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                        </div>
                    </template>
                    
                    <template x-if="selectedWidget().type === 'paragraph'">
                        <div class="space-y-3">
                            <div><label class="text-xs font-bold">Content</label><textarea x-model="selectedWidget().settings.content" rows="6" @change="pushHistory();markDirty()" class="w-full border rounded px-2 py-1 text-sm font-mono"></textarea></div>
                            <div><label class="text-xs font-bold">Alignment</label><select x-model="selectedWidget().settings.alignment" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"><option>left</option><option>center</option><option>right</option></select></div>
                        </div>
                    </template>
                    
                    <template x-if="selectedWidget().type === 'button'">
                        <div class="space-y-3">
                            <div><label class="text-xs font-bold">Button Text</label><input type="text" x-model="selectedWidget().settings.text" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Link URL</label><input type="text" x-model="selectedWidget().settings.link" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Background Color</label><input type="color" x-model="selectedWidget().settings.bgColor" @change="pushHistory()" class="w-full h-8 rounded border"></div>
                            <div><label class="text-xs font-bold">Text Color</label><input type="color" x-model="selectedWidget().settings.textColor" @change="pushHistory()" class="w-full h-8 rounded border"></div>
                            <div><label class="text-xs font-bold">Alignment</label><select x-model="selectedWidget().settings.alignment" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"><option>left</option><option>center</option><option>right</option></select></div>
                        </div>
                    </template>
                    
                    <template x-if="selectedWidget().type === 'image'">
                        <div class="space-y-3">
                            <div><label class="text-xs font-bold">Image URL</label><input type="text" x-model="selectedWidget().settings.url" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <button @click="openMediaLibraryForWidget('url')" class="w-full py-2 bg-gray-200 dark:bg-gray-700 rounded text-sm">Browse Media</button>
                            <div><label class="text-xs font-bold">Alt Text</label><input type="text" x-model="selectedWidget().settings.alt" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Alignment</label><select x-model="selectedWidget().settings.alignment" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"><option>left</option><option>center</option><option>right</option></select></div>
                        </div>
                    </template>
                    
                    <template x-if="selectedWidget().type === 'video'">
                        <div class="space-y-3">
                            <div><label class="text-xs font-bold">YouTube/Vimeo URL</label><input type="text" x-model="selectedWidget().settings.url" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Aspect Ratio</label><select x-model="selectedWidget().settings.ratio" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"><option>16/9</option><option>4/3</option><option>1/1</option></select></div>
                        </div>
                    </template>
                    
                    <template x-if="selectedWidget().type === 'icon'">
                        <div class="space-y-3">
                            <div><label class="text-xs font-bold">Icon Class</label><input type="text" x-model="selectedWidget().settings.iconClass" placeholder="fas fa-star" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Size (px)</label><input type="number" x-model="selectedWidget().settings.size" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Color</label><input type="color" x-model="selectedWidget().settings.color" @change="pushHistory()" class="w-full h-8 rounded border"></div>
                        </div>
                    </template>
                    
                    <template x-if="selectedWidget().type === 'icon-list'">
                        <div class="space-y-3">
                            <div class="flex justify-between"><label class="text-xs font-bold">Icon List Items</label><button @click="selectedWidget().settings.items.push({icon:'fas fa-check', text:'New Item'})" class="text-xs text-blue-500">+ Add</button></div>
                            <template x-for="(item, i) in selectedWidget().settings.items" :key="i">
                                <div class="flex gap-2">
                                    <input x-model="item.icon" placeholder="Icon class" class="flex-1 border rounded px-2 py-1 text-xs">
                                    <input x-model="item.text" placeholder="Text" class="flex-1 border rounded px-2 py-1 text-xs">
                                    <button @click="selectedWidget().settings.items.splice(i,1)" class="text-red-500">✕</button>
                                </div>
                            </template>
                        </div>
                    </template>
                    
                    <template x-if="selectedWidget().type === 'testimonial'">
                        <div class="space-y-3">
                            <div><label class="text-xs font-bold">Testimonial Text</label><textarea x-model="selectedWidget().settings.text" rows="3" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></textarea></div>
                            <div><label class="text-xs font-bold">Author</label><input type="text" x-model="selectedWidget().settings.author" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Role</label><input type="text" x-model="selectedWidget().settings.role" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Rating (1-5)</label><input type="number" min="1" max="5" x-model="selectedWidget().settings.rating" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                        </div>
                    </template>
                    
                    <template x-if="selectedWidget().type === 'team-member'">
                        <div class="space-y-3">
                            <div><label class="text-xs font-bold">Photo URL</label><input type="text" x-model="selectedWidget().settings.photo" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Name</label><input type="text" x-model="selectedWidget().settings.name" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Role</label><input type="text" x-model="selectedWidget().settings.role" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Bio</label><textarea x-model="selectedWidget().settings.bio" rows="3" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></textarea></div>
                        </div>
                    </template>
                    
                    <template x-if="selectedWidget().type === 'pricing'">
                        <div class="space-y-3">
                            <div><label class="text-xs font-bold">Plan Title</label><input type="text" x-model="selectedWidget().settings.title" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Price</label><input type="text" x-model="selectedWidget().settings.price" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Currency</label><input type="text" x-model="selectedWidget().settings.currency" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Period</label><input type="text" x-model="selectedWidget().settings.period" placeholder="/month" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Button Text</label><input type="text" x-model="selectedWidget().settings.buttonText" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <label class="flex items-center gap-2"><input type="checkbox" x-model="selectedWidget().settings.highlighted" @change="pushHistory()"> <span class="text-xs">Highlighted Plan</span></label>
                        </div>
                    </template>
                    
                    <template x-if="selectedWidget().type === 'accordion'">
                        <div class="space-y-3">
                            <div class="flex justify-between"><label class="text-xs font-bold">Accordion Items</label><button @click="addAccordionItem()" class="text-xs text-blue-500">+ Add</button></div>
                            <template x-for="(item, i) in selectedWidget().settings.items" :key="i">
                                <div class="border rounded p-2 space-y-2">
                                    <input x-model="item.title" placeholder="Title" class="w-full border rounded px-2 py-1 text-xs">
                                    <textarea x-model="item.content" placeholder="Content" rows="2" class="w-full border rounded px-2 py-1 text-xs"></textarea>
                                    <button @click="selectedWidget().settings.items.splice(i,1)" class="text-red-500 text-xs">Remove</button>
                                </div>
                            </template>
                        </div>
                    </template>
                    
                    <template x-if="selectedWidget().type === 'tabs'">
                        <div class="space-y-3">
                            <div class="flex justify-between"><label class="text-xs font-bold">Tab Items</label><button @click="addTabItem()" class="text-xs text-blue-500">+ Add</button></div>
                            <template x-for="(item, i) in selectedWidget().settings.items" :key="i">
                                <div class="border rounded p-2 space-y-2">
                                    <input x-model="item.label" placeholder="Tab Label" class="w-full border rounded px-2 py-1 text-xs">
                                    <textarea x-model="item.content" placeholder="Tab Content" rows="2" class="w-full border rounded px-2 py-1 text-xs"></textarea>
                                    <button @click="selectedWidget().settings.items.splice(i,1)" class="text-red-500 text-xs">Remove</button>
                                </div>
                            </template>
                        </div>
                    </template>
                    
                    <template x-if="selectedWidget().type === 'counter'">
                        <div class="space-y-3">
                            <div><label class="text-xs font-bold">End Number</label><input type="number" x-model="selectedWidget().settings.end" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Prefix</label><input type="text" x-model="selectedWidget().settings.prefix" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Suffix</label><input type="text" x-model="selectedWidget().settings.suffix" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Label</label><input type="text" x-model="selectedWidget().settings.label" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Color</label><input type="color" x-model="selectedWidget().settings.color" @change="pushHistory()" class="w-full h-8 rounded border"></div>
                        </div>
                    </template>
                    
                    <template x-if="selectedWidget().type === 'progress-bar'">
                        <div class="space-y-3">
                            <div><label class="text-xs font-bold">Label</label><input type="text" x-model="selectedWidget().settings.label" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Percentage (0-100)</label><input type="number" x-model="selectedWidget().settings.percentage" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Color</label><input type="color" x-model="selectedWidget().settings.color" @change="pushHistory()" class="w-full h-8 rounded border"></div>
                            <div><label class="text-xs font-bold">Height (px)</label><input type="number" x-model="selectedWidget().settings.height" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                        </div>
                    </template>
                    
                    <template x-if="selectedWidget().type === 'circle-progress'">
                        <div class="space-y-3">
                            <div><label class="text-xs font-bold">Percentage</label><input type="number" x-model="selectedWidget().settings.percentage" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Size (px)</label><input type="number" x-model="selectedWidget().settings.size" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Stroke Width</label><input type="number" x-model="selectedWidget().settings.strokeWidth" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Color</label><input type="color" x-model="selectedWidget().settings.color" @change="pushHistory()" class="w-full h-8 rounded border"></div>
                        </div>
                    </template>
                    
                    <template x-if="selectedWidget().type === 'countdown'">
                        <div class="space-y-3">
                            <div><label class="text-xs font-bold">Target Date</label><input type="datetime-local" x-model="selectedWidget().settings.targetDate" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Color</label><input type="color" x-model="selectedWidget().settings.color" @change="pushHistory()" class="w-full h-8 rounded border"></div>
                        </div>
                    </template>
                    
                    <template x-if="selectedWidget().type === 'image-carousel'">
                        <div class="space-y-3">
                            <div><label class="text-xs font-bold">Image URLs (one per line)</label><textarea x-model="selectedWidget().settings.imagesText" rows="4" @change="updateCarouselImages()" class="w-full border rounded px-2 py-1 text-sm font-mono"></textarea></div>
                        </div>
                    </template>
                    
                    <template x-if="selectedWidget().type === 'google-maps'">
                        <div class="space-y-3">
                            <div><label class="text-xs font-bold">Address/Location</label><input type="text" x-model="selectedWidget().settings.address" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Height (px)</label><input type="number" x-model="selectedWidget().settings.height" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                        </div>
                    </template>
                    
                    <template x-if="selectedWidget().type === 'contact-form'">
                        <div class="space-y-3">
                            <div><label class="text-xs font-bold">Form Title</label><input type="text" x-model="selectedWidget().settings.title" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Submit Button Text</label><input type="text" x-model="selectedWidget().settings.submitText" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Success Message</label><input type="text" x-model="selectedWidget().settings.successMsg" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                        </div>
                    </template>
                    
                    <template x-if="selectedWidget().type === 'subscribe-form'">
                        <div class="space-y-3">
                            <div><label class="text-xs font-bold">Placeholder</label><input type="text" x-model="selectedWidget().settings.placeholder" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Button Text</label><input type="text" x-model="selectedWidget().settings.buttonText" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                        </div>
                    </template>
                    
                    <template x-if="selectedWidget().type === 'search-form'">
                        <div class="space-y-3">
                            <div><label class="text-xs font-bold">Placeholder</label><input type="text" x-model="selectedWidget().settings.placeholder" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                            <div><label class="text-xs font-bold">Button Text</label><input type="text" x-model="selectedWidget().settings.buttonText" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                        </div>
                    </template>
                    
                    <template x-if="selectedWidget().type === 'raw-html'">
                        <div><label class="text-xs font-bold">HTML Code</label><textarea x-model="selectedWidget().settings.code" rows="8" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm font-mono"></textarea></div>
                    </template>
                    
                    <template x-if="selectedWidget().type === 'divider'">
                        <div class="space-y-3">
                            <div><label class="text-xs font-bold">Style</label><select x-model="selectedWidget().settings.style" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"><option>solid</option><option>dashed</option><option>dotted</option></select></div>
                            <div><label class="text-xs font-bold">Color</label><input type="color" x-model="selectedWidget().settings.color" @change="pushHistory()" class="w-full h-8 rounded border"></div>
                            <div><label class="text-xs font-bold">Width (%)</label><input type="number" x-model="selectedWidget().settings.width" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                        </div>
                    </template>
                    
                    <template x-if="selectedWidget().type === 'spacer'">
                        <div><label class="text-xs font-bold">Height (px)</label><input type="number" x-model="selectedWidget().settings.height" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                    </template>
                </div>
                
                <!-- Style Tab -->
                <div x-show="selectedWidget() && rightTab === 'style'">
                    <div class="space-y-3">
                        <div><label class="text-xs font-bold">Background Color</label><input type="color" x-model="selectedWidget().settings.bgColor" @change="pushHistory()" class="w-full h-8 rounded border"></div>
                        <div><label class="text-xs font-bold">Padding Top (px)</label><input type="number" x-model="selectedWidget().settings.pt" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                        <div><label class="text-xs font-bold">Padding Bottom (px)</label><input type="number" x-model="selectedWidget().settings.pb" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                        <div><label class="text-xs font-bold">Padding Left (px)</label><input type="number" x-model="selectedWidget().settings.pl" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                        <div><label class="text-xs font-bold">Padding Right (px)</label><input type="number" x-model="selectedWidget().settings.pr" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                        <div><label class="text-xs font-bold">Border Radius (px)</label><input type="number" x-model="selectedWidget().settings.borderRadius" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm"></div>
                        <div><label class="text-xs font-bold">Custom CSS</label><textarea x-model="selectedWidget().settings.customCss" rows="3" placeholder="color: red; font-size: 20px;" @change="pushHistory()" class="w-full border rounded px-2 py-1 text-sm font-mono"></textarea></div>
                    </div>
                </div>
                
                <!-- Page Settings when no widget selected -->
                <div x-show="!selectedWidget()" class="space-y-3">
                    <div><label class="text-xs font-bold">Page Title (SEO)</label><input type="text" x-model="seoData.title" class="w-full border rounded px-2 py-1 text-sm"></div>
                    <div><label class="text-xs font-bold">Meta Description</label><textarea x-model="seoData.meta" rows="3" class="w-full border rounded px-2 py-1 text-sm"></textarea></div>
                    <div><label class="text-xs font-bold">Background Color</label><input type="color" x-model="globalStyles.bgColor" class="w-full h-8 rounded border"></div>
                    <div><label class="text-xs font-bold">Background Image URL</label><input type="text" x-model="globalStyles.bgImage" class="w-full border rounded px-2 py-1 text-sm"></div>
                    <div><label class="text-xs font-bold">Font Family</label><select x-model="globalStyles.fontFamily" class="w-full border rounded px-2 py-1 text-sm"><option>Inter, sans-serif</option><option>Georgia, serif</option><option>Roboto, sans-serif</option><option>Montserrat, sans-serif</option></select></div>
                    <button @click="showTemplatesModal=true" class="w-full py-2 bg-purple-600 text-white rounded text-sm">Manage Templates</button>
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
function pageBuilderApp() {
    return {
        // State
        components: [],
        selectedId: null,
        darkMode: false,
        previewMode: 'desktop',
        livePreview: false,
        canvasZoom: 1,
        widgetSearch: '',
        isDirty: false,
        dragWidget: null,
        clipboard: null,
        snapGrid: false,
        undoStack: [],
        redoStack: [],
        rightTab: 'content',
        
        // Modals
        showMediaLibrary: false,
        showAIModal: false,
        showTemplatesModal: false,
        mediaCallback: null,
        selectedMedia: null,
        mediaUrlInput: '',
        aiPrompt: '',
        aiResult: '',
        aiLoading: false,
        aiTargetWidget: null,
        newTemplateName: '',
        toastMessage: '',
        
        // Data
        templates: [],
        revisions: [],
        mediaImages: ['https://picsum.photos/400/300?random=1', 'https://picsum.photos/400/300?random=2', 'https://picsum.photos/400/300?random=3'],
        
        // SEO
        seoData: { title: 'My Page', meta: '' },
        
        // Global Styles
        globalStyles: {
            primaryColor: '#0ea5e9',
            fontFamily: 'Inter, sans-serif',
            bgColor: '#ffffff',
            bgImage: '',
        },
        
        // Widget Categories (Full list from v4.html)
        widgetCategories: [
            { name: 'Layout', open: true, widgets: [
                {type:'section', label:'Section', icon:'fa-square'},
                {type:'container', label:'Container', icon:'fa-box'},
                {type:'columns', label:'Columns', icon:'fa-columns'},
                {type:'spacer', label:'Spacer', icon:'fa-arrows-alt-v'},
                {type:'divider', label:'Divider', icon:'fa-minus'},
            ]},
            { name: 'Basic', open: true, widgets: [
                {type:'heading', label:'Heading', icon:'fa-heading'},
                {type:'paragraph', label:'Paragraph', icon:'fa-paragraph'},
                {type:'button', label:'Button', icon:'fa-hand-pointer'},
                {type:'image', label:'Image', icon:'fa-image'},
                {type:'video', label:'Video', icon:'fa-video'},
                {type:'icon', label:'Icon', icon:'fa-star'},
                {type:'icon-list', label:'Icon List', icon:'fa-list-ul'},
            ]},
            { name: 'Content', open: false, widgets: [
                {type:'testimonial', label:'Testimonial', icon:'fa-quote-right'},
                {type:'team-member', label:'Team Member', icon:'fa-user-tie'},
                {type:'pricing', label:'Pricing', icon:'fa-tag'},
                {type:'accordion', label:'Accordion', icon:'fa-layer-group'},
                {type:'tabs', label:'Tabs', icon:'fa-folder'},
                {type:'counter', label:'Counter', icon:'fa-sort-numeric-up'},
                {type:'progress-bar', label:'Progress Bar', icon:'fa-tasks'},
                {type:'circle-progress', label:'Circle Progress', icon:'fa-circle-notch'},
                {type:'countdown', label:'Countdown', icon:'fa-clock'},
            ]},
            { name: 'Media', open: false, widgets: [
                {type:'image-carousel', label:'Carousel', icon:'fa-images'},
                {type:'before-after', label:'Before/After', icon:'fa-adjust'},
                {type:'google-maps', label:'Maps', icon:'fa-map-marker-alt'},
            ]},
            { name: 'Forms', open: false, widgets: [
                {type:'contact-form', label:'Contact Form', icon:'fa-envelope'},
                {type:'subscribe-form', label:'Subscribe', icon:'fa-bell'},
                {type:'search-form', label:'Search', icon:'fa-search'},
                {type:'raw-html', label:'Raw HTML', icon:'fa-code'},
            ]},
        ],
        
        // ==================== INIT ====================
        initBuilder() {
            if (window.pageBuilderConfig?.pageData) {
                this.loadExistingPageData(window.pageBuilderConfig.pageData);
            } else {
                this.loadSampleData();
                this.loadFromStorage();
            }

            this.loadTemplates();
            this.darkMode = localStorage.getItem('builder_dark') === 'true';
            this.snapGrid = localStorage.getItem('builder_snap') === 'true';
            
            setInterval(() => this.autoSave(), 30000);
            this.$nextTick(() => this.initSortable());
        },
        
        loadSampleData() {
            if (this.components.length === 0) {
                this.components = [
                    { id: this.generateId(), type: 'heading', settings: this.getDefaultSettings('heading') },
                    { id: this.generateId(), type: 'paragraph', settings: this.getDefaultSettings('paragraph') },
                    { id: this.generateId(), type: 'button', settings: this.getDefaultSettings('button') },
                ];
                // Update heading text
                this.components[0].settings.text = 'Welcome to CMS Pro Builder';
                this.components[0].settings.alignment = 'center';
                this.components[0].settings.color = '#0ea5e9';
                this.components[0].settings.fontSize = 48;
            }
        },

        loadExistingPageData(data) {
            if (Array.isArray(data.components) && data.components.length) {
                this.components = data.components;
            }

            if (data.globalStyles) {
                this.globalStyles = { ...this.globalStyles, ...data.globalStyles };
            }

            if (data.seoData) {
                this.seoData = { ...this.seoData, ...data.seoData };
            }
        },
        
        initSortable() {
            const el = document.getElementById('sortable-canvas');
            if (!el || !window.Sortable) return;
            Sortable.create(el, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                handle: '.drag-handle',
                onEnd: (evt) => {
                    const moved = this.components.splice(evt.oldIndex, 1)[0];
                    this.components.splice(evt.newIndex, 0, moved);
                    this.pushHistory();
                    this.markDirty();
                }
            });
        },
        
        filteredWidgetCategories() {
            if (!this.widgetSearch) return this.widgetCategories;
            const q = this.widgetSearch.toLowerCase();
            return this.widgetCategories.map(cat => ({
                ...cat,
                open: true,
                widgets: cat.widgets.filter(w => w.label.toLowerCase().includes(q) || w.type.includes(q))
            })).filter(cat => cat.widgets.length > 0);
        },
        
        // ==================== WIDGET DEFAULTS ====================
        getDefaultSettings(type) {
            const defaults = {
                // Layout
                section: { bgColor: '#f8fafc', pt: 60, pb: 60, pl: 0, pr: 0 },
                container: { maxWidth: '1200px', pt: 20, pb: 20, pl: 20, pr: 20 },
                columns: { columnCount: 2, gap: 20 },
                spacer: { height: 40 },
                divider: { style: 'solid', color: '#e2e8f0', width: 100, thickness: 1 },
                // Basic
                heading: { text: 'New Heading', tag: 'h2', alignment: 'left', color: '#1e293b', fontSize: 32, fontWeight: '700', pt: 10, pb: 10 },
                paragraph: { content: '<p>Click to edit this paragraph. You can add your content here.</p>', alignment: 'left', pt: 10, pb: 10 },
                button: { text: 'Click Me', link: '#', bgColor: '#0ea5e9', textColor: '#ffffff', alignment: 'center', pt: 10, pb: 10 },
                image: { url: 'https://picsum.photos/600/400', alt: 'Image', alignment: 'center', pt: 10, pb: 10 },
                video: { url: 'https://www.youtube.com/embed/dQw4w9WgXcQ', ratio: '16/9', pt: 10, pb: 10 },
                icon: { iconClass: 'fas fa-star', size: 40, color: '#0ea5e9', alignment: 'center', pt: 10, pb: 10 },
                'icon-list': { items: [{icon:'fas fa-check', text:'Feature 1'}, {icon:'fas fa-check', text:'Feature 2'}], iconColor: '#0ea5e9', pt: 10, pb: 10 },
                // Content
                testimonial: { text: 'This product is absolutely amazing!', author: 'Jane Smith', role: 'CEO, TechCorp', rating: 5, pt: 10, pb: 10 },
                'team-member': { photo: 'https://i.pravatar.cc/200', name: 'John Doe', role: 'Lead Developer', bio: 'Passionate developer with 10 years of experience.', pt: 10, pb: 10 },
                pricing: { title: 'Pro Plan', price: '29', currency: '$', period: '/month', features: ['10 Projects', '50GB Storage', 'Priority Support'], buttonText: 'Get Started', highlighted: false, pt: 10, pb: 10 },
                accordion: { items: [{title:'Section 1', content:'<p>Content for section 1</p>', open: false}], pt: 10, pb: 10 },
                tabs: { items: [{label:'Tab 1', content:'<p>Tab 1 content</p>'}, {label:'Tab 2', content:'<p>Tab 2 content</p>'}], activeTab: 0, pt: 10, pb: 10 },
                counter: { end: 100, prefix: '', suffix: '+', label: 'Happy Clients', color: '#0ea5e9', fontSize: 48, pt: 10, pb: 10 },
                'progress-bar': { label: 'Web Design', percentage: 75, color: '#0ea5e9', height: 12, pt: 10, pb: 10 },
                'circle-progress': { percentage: 75, size: 120, strokeWidth: 10, color: '#0ea5e9', pt: 10, pb: 10 },
                countdown: { targetDate: new Date(Date.now() + 86400000 * 30).toISOString().slice(0, 16), color: '#0ea5e9', pt: 10, pb: 10 },
                // Media
                'image-carousel': { images: ['https://picsum.photos/800/400?random=1', 'https://picsum.photos/800/400?random=2'], imagesText: 'https://picsum.photos/800/400?random=1\nhttps://picsum.photos/800/400?random=2', pt: 10, pb: 10 },
                'before-after': { beforeUrl: 'https://picsum.photos/800/400?random=3', afterUrl: 'https://picsum.photos/800/400?random=4', pt: 10, pb: 10 },
                'google-maps': { address: 'New York, NY', height: 400, pt: 10, pb: 10 },
                // Forms
                'contact-form': { title: 'Contact Us', submitText: 'Send Message', successMsg: 'Thank you!', pt: 10, pb: 10 },
                'subscribe-form': { placeholder: 'Enter your email', buttonText: 'Subscribe', pt: 10, pb: 10 },
                'search-form': { placeholder: 'Search...', buttonText: 'Search', pt: 10, pb: 10 },
                'raw-html': { code: '<div style="padding:20px;background:#f0f4f8;border-radius:8px;"><p>Your custom HTML here</p></div>', pt: 10, pb: 10 },
            };
            const base = { pt: 0, pr: 0, pb: 0, pl: 0, mt: 0, mr: 0, mb: 0, ml: 0, bgColor: 'transparent', borderRadius: 0, customCss: '' };
            return { ...base, ...(defaults[type] || { content: 'New widget', pt: 10, pb: 10 }) };
        },
        
        createWidget(type) {
            return {
                id: this.generateId(),
                type: type,
                settings: this.getDefaultSettings(type),
            };
        },
        
        generateId() { return 'w_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6); },
        
        // ==================== WIDGET RENDERING ====================
        renderWidgetContent(comp) {
            const s = comp.settings;
            const style = `padding:${s.pt||0}px ${s.pr||0}px ${s.pb||0}px ${s.pl||0}px;background:${s.bgColor||'transparent'};border-radius:${s.borderRadius||0}px;${s.customCss||''}`;
            
            const renders = {
                section: () => `<div style="${style};background:${s.bgColor};"><div style="max-width:1200px;margin:0 auto;padding:0 20px;">Section Container</div></div>`,
                container: () => `<div style="${style};max-width:${s.maxWidth};margin:0 auto;">Container Content</div>`,
                columns: () => `<div style="${style};display:flex;gap:${s.gap||20}px;"><div style="flex:1;border:1px dashed #ccc;padding:20px;">Column 1</div><div style="flex:1;border:1px dashed #ccc;padding:20px;">Column 2</div></div>`,
                spacer: () => `<div style="height:${s.height||40}px;${style}"></div>`,
                divider: () => `<hr style="border-top:${s.thickness||1}px ${s.style||'solid'} ${s.color||'#e2e8f0'};width:${s.width||100}%;margin:0 auto;">`,
                heading: () => `<${s.tag||'h2'} style="text-align:${s.alignment||'left'};color:${s.color||'#000'};font-size:${s.fontSize||24}px;font-weight:bold;margin:0;${style}">${this.escapeHtml(s.text || 'Heading')}</${s.tag||'h2'}>`,
                paragraph: () => `<div style="text-align:${s.alignment||'left'};${style}">${s.content || '<p>Click to edit this paragraph.</p>'}</div>`,
                button: () => `<div style="text-align:${s.alignment||'center'};${style}"><a href="${s.link||'#'}" style="display:inline-block;background:${s.bgColor||'#0ea5e9'};color:${s.textColor||'#fff'};padding:10px 24px;border-radius:8px;text-decoration:none;font-weight:600;">${s.text||'Button'}</a></div>`,
                image: () => `<div style="text-align:${s.alignment||'center'};${style}"><img src="${s.url||'https://picsum.photos/600/400'}" alt="${s.alt||''}" style="max-width:100%;border-radius:${s.borderRadius||0}px;"></div>`,
                video: () => `<div style="${style}"><div style="position:relative;padding-bottom:${s.ratio==='4/3'?'75%':s.ratio==='1/1'?'100%':'56.25%'};height:0;"><iframe src="${s.url}" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;" allowfullscreen></iframe></div></div>`,
                icon: () => `<div style="text-align:${s.alignment||'center'};${style}"><i class="${s.iconClass||'fas fa-star'}" style="font-size:${s.size||40}px;color:${s.color||'#0ea5e9'}"></i></div>`,
                'icon-list': () => `<div style="${style}"><ul style="list-style:none;padding:0;margin:0;">${(s.items||[]).map(item => `<li style="display:flex;align-items:center;gap:8px;margin-bottom:8px;"><i class="${item.icon}" style="color:${s.iconColor||'#0ea5e9'}"></i><span>${this.escapeHtml(item.text)}</span></li>`).join('')}</ul></div>`,
                testimonial: () => `<div style="background:#f8fafc;border-radius:12px;padding:20px;${style}"><div style="display:flex;gap:4px;margin-bottom:12px;">${Array(s.rating||5).fill('').map(()=>'<i class="fas fa-star" style="color:#f59e0b;"></i>').join('')}</div><p style="font-style:italic;margin:0 0 12px;">"${this.escapeHtml(s.text)}"</p><div style="display:flex;align-items:center;gap:12px;"><img src="${s.photo||'https://i.pravatar.cc/80'}" style="width:48px;height:48px;border-radius:50%;object-fit:cover;"><div><p style="font-weight:bold;margin:0;">${this.escapeHtml(s.author)}</p><p style="color:#64748b;font-size:12px;margin:0;">${this.escapeHtml(s.role)}</p></div></div></div>`,
                'team-member': () => `<div style="text-align:center;${style}"><img src="${s.photo||'https://i.pravatar.cc/200'}" style="width:100px;height:100px;border-radius:50%;object-fit:cover;margin:0 auto 16px;"><h4 style="font-weight:bold;margin:0 0 4px;">${this.escapeHtml(s.name)}</h4><p style="color:#64748b;font-size:14px;margin:0 0 12px;">${this.escapeHtml(s.role)}</p><p style="font-size:14px;">${this.escapeHtml(s.bio)}</p></div>`,
                pricing: () => `<div style="text-align:center;background:${s.highlighted?'#0ea5e9':'#fff'};color:${s.highlighted?'#fff':'inherit'};border-radius:16px;padding:32px 24px;box-shadow:0 4px 20px rgba(0,0,0,0.1);${style}"><h3 style="font-weight:bold;margin:0 0 8px;">${this.escapeHtml(s.title)}</h3><div style="font-size:48px;font-weight:900;margin:16px 0;">${s.currency}${s.price}<span style="font-size:16px;">${s.period}</span></div><ul style="list-style:none;padding:0;margin:0 0 24px;">${(s.features||[]).map(f=>`<li style="padding:6px 0;">${f}</li>`).join('')}</ul><button style="width:100%;padding:12px;background:${s.highlighted?'rgba(255,255,255,0.2)':'#0ea5e9'};color:#fff;border:none;border-radius:8px;cursor:pointer;">${s.buttonText}</button></div>`,
                accordion: () => `<div style="${style}">${(s.items||[]).map((item,i)=>`<div style="border:1px solid #e2e8f0;border-radius:8px;margin-bottom:8px;"><div style="padding:12px 16px;background:#f8fafc;font-weight:600;">${this.escapeHtml(item.title)}</div><div style="padding:16px;display:${item.open?'block':'none'};">${item.content}</div></div>`).join('')}</div>`,
                tabs: () => `<div style="${style}"><div style="display:flex;border-bottom:2px solid #e2e8f0;margin-bottom:16px;">${(s.items||[]).map((tab,i)=>`<div style="padding:8px 16px;font-weight:600;border-bottom:${i===0?'2px solid #0ea5e9':'none'};color:${i===0?'#0ea5e9':'#64748b'};">${tab.label}</div>`).join('')}</div><div>${(s.items||[])[0]?.content || ''}</div></div>`,
                counter: () => `<div style="text-align:center;${style}"><div style="font-size:${s.fontSize||48}px;font-weight:900;color:${s.color||'#0ea5e9'};">${s.prefix||''}${s.end||100}${s.suffix||'+'}</div><p style="margin:4px 0 0;color:#64748b;">${s.label}</p></div>`,
                'progress-bar': () => `<div style="${style}"><div style="display:flex;justify-content:space-between;margin-bottom:6px;"><span>${s.label}</span><span>${s.percentage||75}%</span></div><div style="background:#e2e8f0;border-radius:${s.height||12}px;height:${s.height||12}px;overflow:hidden;"><div style="width:${s.percentage||75}%;height:100%;background:${s.color||'#0ea5e9'};border-radius:${s.height||12}px;transition:width 0.3s;"></div></div></div>`,
                'circle-progress': () => `<div style="text-align:center;${style}"><svg width="${s.size||120}" height="${s.size||120}" style="transform:rotate(-90deg)"><circle cx="${(s.size||120)/2}" cy="${(s.size||120)/2}" r="${(s.size||120)/2 - (s.strokeWidth||10)}" fill="none" stroke="#e2e8f0" stroke-width="${s.strokeWidth||10}"/><circle cx="${(s.size||120)/2}" cy="${(s.size||120)/2}" r="${(s.size||120)/2 - (s.strokeWidth||10)}" fill="none" stroke="${s.color||'#0ea5e9'}" stroke-width="${s.strokeWidth||10}" stroke-dasharray="${2 * Math.PI * ((s.size||120)/2 - (s.strokeWidth||10)) * (s.percentage||75)/100} ${2 * Math.PI * ((s.size||120)/2 - (s.strokeWidth||10))}" stroke-linecap="round"/></svg><p style="margin-top:8px;">${s.percentage||75}%</p></div>`,
                countdown: () => `<div style="display:flex;gap:16px;justify-content:center;${style}"><div style="text-align:center;"><div style="font-size:32px;font-weight:900;color:${s.color||'#0ea5e9'};">30</div><div style="font-size:12px;">Days</div></div><div style="text-align:center;"><div style="font-size:32px;font-weight:900;color:${s.color||'#0ea5e9'};">12</div><div style="font-size:12px;">Hours</div></div><div style="text-align:center;"><div style="font-size:32px;font-weight:900;color:${s.color||'#0ea5e9'};">45</div><div style="font-size:12px;">Mins</div></div><div style="text-align:center;"><div style="font-size:32px;font-weight:900;color:${s.color||'#0ea5e9'};">30</div><div style="font-size:12px;">Secs</div></div></div>`,
                'image-carousel': () => `<div style="${style}"><div style="position:relative;overflow:hidden;"><img src="${(s.images||[])[0]}" style="width:100%;"></div></div>`,
                'before-after': () => `<div style="position:relative;${style}"><img src="${s.beforeUrl}" style="width:100%;"><div style="position:absolute;top:0;left:0;width:50%;height:100%;overflow:hidden;"><img src="${s.afterUrl}" style="width:100%;height:100%;object-fit:cover;"></div></div>`,
                'google-maps': () => `<div style="${style}"><iframe width="100%" height="${s.height||400}" frameborder="0" src="https://maps.google.com/maps?q=${encodeURIComponent(s.address||'New York')}&output=embed"></iframe></div>`,
                'contact-form': () => `<div style="${style}"><h3 style="margin:0 0 16px;">${s.title || 'Contact Us'}</h3><form><input type="text" placeholder="Name" style="width:100%;padding:10px;margin-bottom:10px;border:1px solid #e2e8f0;border-radius:8px;"><input type="email" placeholder="Email" style="width:100%;padding:10px;margin-bottom:10px;border:1px solid #e2e8f0;border-radius:8px;"><textarea placeholder="Message" rows="4" style="width:100%;padding:10px;margin-bottom:10px;border:1px solid #e2e8f0;border-radius:8px;"></textarea><button type="button" style="background:#0ea5e9;color:#fff;padding:10px 20px;border:none;border-radius:8px;">${s.submitText||'Send'}</button></form></div>`,
                'subscribe-form': () => `<div style="display:flex;gap:8px;${style}"><input type="email" placeholder="${s.placeholder||'Enter email'}" style="flex:1;padding:10px;border:1px solid #e2e8f0;border-radius:8px;"><button style="background:#0ea5e9;color:#fff;padding:10px 20px;border:none;border-radius:8px;">${s.buttonText||'Subscribe'}</button></div>`,
                'search-form': () => `<div style="display:flex;${style}"><input type="text" placeholder="${s.placeholder||'Search...'}" style="flex:1;padding:10px;border:1px solid #e2e8f0;border-radius:8px 0 0 8px;"><button style="background:#0ea5e9;color:#fff;padding:10px 20px;border:none;border-radius:0 8px 8px 0;">${s.buttonText||'Search'}</button></div>`,
                'raw-html': () => `<div style="${style}">${s.code || '<p>Custom HTML</p>'}</div>`,
            };
            
            return (renders[comp.type] || (() => `<div style="${style}">${s.content || comp.type}</div>`))();
        },
        
        escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        },
        
        updateCarouselImages() {
            const w = this.selectedWidget();
            if (w && w.type === 'image-carousel' && w.settings.imagesText) {
                w.settings.images = w.settings.imagesText.split('\n').filter(u => u.trim());
                this.pushHistory();
            }
        },
        
        addAccordionItem() {
            const w = this.selectedWidget();
            if (w && w.type === 'accordion') {
                w.settings.items.push({ title: 'New Section', content: '<p>New content</p>', open: false });
                this.pushHistory();
                this.markDirty();
            }
        },
        
        addTabItem() {
            const w = this.selectedWidget();
            if (w && w.type === 'tabs') {
                w.settings.items.push({ label: 'New Tab', content: '<p>Tab content</p>' });
                this.pushHistory();
                this.markDirty();
            }
        },
        
        // ==================== WIDGET OPERATIONS ====================
        addWidgetToCanvas(type) {
            this.pushHistory();
            const widget = this.createWidget(type);
            this.components.push(widget);
            this.selectedId = widget.id;
            this.markDirty();
            this.showToast(`${type} widget added!`);
            this.$nextTick(() => this.initSortable());
        },
        
        selectWidget(id) { this.selectedId = id; this.rightTab = 'content'; },
        selectedWidget() { return this.components.find(c => c.id === this.selectedId); },
        
        deleteWidget(id) {
            if (confirm('Delete this widget?')) {
                this.pushHistory();
                this.components = this.components.filter(c => c.id !== id);
                if (this.selectedId === id) this.selectedId = null;
                this.markDirty();
                this.showToast('Widget deleted');
            }
        },
        
        duplicateWidget(id) {
            this.pushHistory();
            const index = this.components.findIndex(c => c.id === id);
            if (index !== -1) {
                const clone = JSON.parse(JSON.stringify(this.components[index]));
                clone.id = this.generateId();
                this.components.splice(index + 1, 0, clone);
                this.selectedId = clone.id;
                this.markDirty();
                this.showToast('Widget duplicated');
            }
        },
        
        // ==================== UNDO/REDO ====================
        pushHistory() {
            const state = JSON.stringify(this.components);
            if (this.undoStack.length === 0 || this.undoStack[this.undoStack.length-1] !== state) {
                this.undoStack.push(state);
                if (this.undoStack.length > 50) this.undoStack.shift();
                this.redoStack = [];
            }
        },
        
        undo() {
            if (this.undoStack.length > 1) {
                this.redoStack.push(this.undoStack.pop());
                this.components = JSON.parse(this.undoStack[this.undoStack.length-1]);
                this.markDirty();
                this.showToast('Undo');
            }
        },
        
        redo() {
            if (this.redoStack.length > 0) {
                this.undoStack.push(this.redoStack.pop());
                this.components = JSON.parse(this.undoStack[this.undoStack.length-1]);
                this.markDirty();
                this.showToast('Redo');
            }
        },
        
        // ==================== DRAG & DROP ====================
        startDragFromLibrary(e, type) {
            this.dragWidget = type;
            e.dataTransfer.setData('text/plain', type);
            e.dataTransfer.effectAllowed = 'copy';
        },
        
        onCanvasDragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'copy';
        },
        
        dropOnCanvas(e) {
            e.preventDefault();
            const type = e.dataTransfer.getData('text/plain') || this.dragWidget;
            if (type) {
                this.addWidgetToCanvas(type);
                this.dragWidget = null;
            }
        },
        
        // ==================== MEDIA LIBRARY ====================
        openMediaLibraryForWidget(key) {
            this.mediaCallback = key;
            this.showMediaLibrary = true;
        },
        
        confirmMedia() {
            if (this.selectedMedia && this.mediaCallback) {
                const widget = this.selectedWidget();
                if (widget) {
                    widget.settings[this.mediaCallback] = this.selectedMedia;
                    this.pushHistory();
                    this.markDirty();
                }
            }
            this.showMediaLibrary = false;
        },
        
        uploadMediaImage(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    this.mediaImages.push(ev.target.result);
                    this.selectedMedia = ev.target.result;
                };
                reader.readAsDataURL(file);
            }
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
            await new Promise(r => setTimeout(r, 1000));
            this.aiResult = `✨ ${this.aiPrompt}\n\nThis is AI-generated content. You can edit it further in the settings panel.`;
            this.aiLoading = false;
        },
        
        insertAIText() {
            const widget = this.components.find(c => c.id === this.aiTargetWidget);
            if (widget) {
                if (widget.type === 'heading') widget.settings.text = this.aiResult.substring(0, 100);
                else if (widget.type === 'paragraph') widget.settings.content = `<p>${this.aiResult}</p>`;
                else if (widget.type === 'testimonial') widget.settings.text = this.aiResult;
                else widget.settings.text = this.aiResult;
                this.pushHistory();
                this.markDirty();
                this.showToast('AI text inserted!');
            }
            this.showAIModal = false;
        },
        
        // ==================== TEMPLATES ====================
        saveTemplate() {
            if (!this.newTemplateName) { this.showToast('Enter template name'); return; }
            this.templates.push({ name: this.newTemplateName, date: Date.now(), components: JSON.parse(JSON.stringify(this.components)) });
            localStorage.setItem('builder_templates', JSON.stringify(this.templates));
            this.newTemplateName = '';
            this.showToast('Template saved!');
        },
        
        loadTemplate(index) {
            this.pushHistory();
            this.components = JSON.parse(JSON.stringify(this.templates[index].components));
            this.showTemplatesModal = false;
            this.markDirty();
            this.showToast('Template loaded!');
        },
        
        deleteTemplate(index) {
            this.templates.splice(index, 1);
            localStorage.setItem('builder_templates', JSON.stringify(this.templates));
            this.showToast('Template deleted');
        },
        
        loadTemplates() {
            try {
                const stored = localStorage.getItem('builder_templates');
                if (stored) this.templates = JSON.parse(stored);
            } catch(e) {}
        },
        
        // ==================== SAVE/LOAD ====================
        savePage(status) {
            const pageData = { components: this.components, globalStyles: this.globalStyles, seoData: this.seoData };
            localStorage.setItem('cms_page_data', JSON.stringify(pageData));
            this.isDirty = false;
            this.showToast('Page saved!');
            
            // Send to server
            fetch(window.pageBuilderConfig.saveUrl, {
                method: window.pageBuilderConfig.saveMethod || 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                body: JSON.stringify({
                    title: this.seoData.title || 'Untitled Page',
                    slug: this.seoData.title ? this.seoData.title.toLowerCase().replace(/[^a-z0-9]+/g, '-') : 'untitled',
                    content: JSON.stringify(pageData),
                    status: status || 'draft',
                    meta_title: this.seoData.title,
                    meta_description: this.seoData.meta,
                    template: 'builder'
                })
            }).then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }

                window.location.href = window.pageBuilderConfig.redirectUrl;
            }).catch(err => console.error('Save error:', err));
        },
        
        publishPage() { this.savePage('published'); },
        
        autoSave() {
            if (this.isDirty) {
                const pageData = { components: this.components, globalStyles: this.globalStyles, seoData: this.seoData };
                localStorage.setItem('cms_page_data', JSON.stringify(pageData));
                this.isDirty = false;
            }
        },
        
        loadFromStorage() {
            try {
                const stored = localStorage.getItem('cms_page_data');
                if (stored) {
                    const data = JSON.parse(stored);
                    if (data.components && data.components.length) this.components = data.components;
                    if (data.globalStyles) this.globalStyles = { ...this.globalStyles, ...data.globalStyles };
                    if (data.seoData) this.seoData = { ...this.seoData, ...data.seoData };
                }
            } catch(e) {}
        },
        
        markDirty() { this.isDirty = true; },
        
        exportJSON() {
            const data = { version: '1.0', components: this.components, globalStyles: this.globalStyles, seoData: this.seoData };
            const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'page.json';
            a.click();
            URL.revokeObjectURL(url);
            this.showToast('Exported!');
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
                    this.markDirty();
                    this.showToast('Imported!');
                } catch(err) { this.showToast('Invalid JSON'); }
            };
            reader.readAsText(file);
            e.target.value = '';
        },
        
        // ==================== HELPERS ====================
        getWidgetIcon(type) {
            const icons = { 
                heading:'fa-heading', paragraph:'fa-paragraph', button:'fa-hand-pointer', 
                image:'fa-image', video:'fa-video', icon:'fa-star', 'icon-list':'fa-list-ul',
                testimonial:'fa-quote-right', 'team-member':'fa-user-tie', pricing:'fa-tag',
                accordion:'fa-layer-group', tabs:'fa-folder', counter:'fa-sort-numeric-up',
                'progress-bar':'fa-tasks', 'circle-progress':'fa-circle-notch', countdown:'fa-clock',
                'image-carousel':'fa-images', 'google-maps':'fa-map-marker-alt',
                'contact-form':'fa-envelope', 'subscribe-form':'fa-bell', 'search-form':'fa-search', 
                'raw-html':'fa-code', section:'fa-square', container:'fa-box', columns:'fa-columns',
                spacer:'fa-arrows-alt-v', divider:'fa-minus', 'before-after':'fa-adjust'
            };
            return icons[type] || 'fa-puzzle-piece';
        },
        
        getPageBgStyle() {
            let style = `background-color:${this.globalStyles.bgColor};`;
            if (this.globalStyles.bgImage) style += `background-image:url(${this.globalStyles.bgImage});background-size:cover;background-position:center;`;
            return style;
        },
        
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('builder_dark', this.darkMode);
        },
        
        handleKeydown(e) {
            const ctrl = e.ctrlKey || e.metaKey;
            if (ctrl && e.key === 's') { e.preventDefault(); this.savePage('draft'); }
            if (ctrl && e.key === 'z') { e.preventDefault(); this.undo(); }
            if (ctrl && e.key === 'y') { e.preventDefault(); this.redo(); }
            if (e.key === 'Delete' && this.selectedId) { e.preventDefault(); this.deleteWidget(this.selectedId); }
        },
        
        showToast(message) {
            this.toastMessage = message;
            setTimeout(() => this.toastMessage = '', 3000);
        },
    };
}
</script>

</body>
</html>
