@php
    $editingPage = isset($page) && $page;
    $builderPayload = $editingPage ? json_decode($page->content, true) : null;
    $initialPageData = [
        'pageTitle' => old('title', $builderPayload['title'] ?? $page->title ?? 'Welcome to Our Stunning Page'),
        'pageSlug' => old('slug', $builderPayload['slug'] ?? $page->slug ?? 'welcome-page'),
        'metaDescription' => old('meta_description', $builderPayload['metaDescription'] ?? $page->meta_description ?? 'This is a beautifully crafted page built with our drag-and-drop builder'),
        'components' => is_array($builderPayload['components'] ?? null) ? $builderPayload['components'] : null,
        'pageBackground' => $builderPayload['pageBackground'] ?? ['color' => '#ffffff', 'image' => '', 'size' => 'cover'],
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Portal | {{ $editingPage ? 'Edit Page' : 'Advanced Page Builder' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- TailwindCSS + Font Awesome 6 + Alpine.js + SortableJS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <!-- TinyMCE WYSIWYG -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
        [x-cloak] { display: none !important; }
        .dark { background-color: #0f172a; color: #e2e8f0; }
        .dark .bg-white { background-color: #1e293b !important; }
        .dark .border-gray-200 { border-color: #334155 !important; }
        .dark .bg-gray-50 { background-color: #0f172a !important; }
        .dark .bg-gray-100 { background-color: #1e293b !important; }
        .dragging-ghost { opacity: 0.4; background: #3b82f6; color: white; border: 2px dashed white; }
        .drop-zone-active { border: 2px solid #3b82f6 !important; background: #eff6ff !important; }
        .dark .drop-zone-active { background: #1e3a8a !important; }
        .toast { animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .component-hover { @apply ring-2 ring-blue-400 ring-inset; }
        .context-menu { position: fixed; background: white; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); z-index: 1000; min-width: 160px; }
        .dark .context-menu { background: #1e293b; border-color: #475569; }
        .preview-mode .component-controls { display: none; }
        .preview-mode .component-wrapper:hover { border-color: transparent !important; }
        .color-preview { width: 30px; height: 30px; border-radius: 6px; border: 1px solid #ddd; cursor: pointer; }
        .bg-preview-section { transition: all 0.3s ease; }
    </style>
</head>
<body x-data="pageBuilderApp()" x-init="initBuilder()" :class="{'dark': darkMode}" class="bg-gray-50 font-sans antialiased transition-colors duration-300">

<!-- Top Toolbar -->
<div class="sticky top-0 z-30 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 shadow-sm">
    <div class="px-4 py-2 flex items-center justify-between flex-wrap gap-2">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.pages.index') }}" class="text-sm text-blue-600 hover:text-blue-700"><i class="fa-solid fa-arrow-left mr-1"></i>Pages</a>
            <i class="fa-solid fa-wand-magic text-purple-600 text-2xl"></i>
            <h1 class="text-xl font-bold dark:text-white">{{ $editingPage ? 'Edit Page Builder' : 'CMS Pro Builder' }}</h1>
            <span class="text-xs bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 px-2 py-1 rounded">Live Editor</span>
        </div>
        
        <div class="flex items-center space-x-2 flex-wrap gap-2">
            <!-- Undo/Redo -->
            <button @click="undo()" :disabled="undoStack.length === 0" class="p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 disabled:opacity-30" title="Undo (Ctrl+Z)">
                <i class="fa-solid fa-undo"></i>
            </button>
            <button @click="redo()" :disabled="redoStack.length === 0" class="p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 disabled:opacity-30" title="Redo (Ctrl+Y)">
                <i class="fa-solid fa-redo"></i>
            </button>
            
            <!-- Dark Mode Toggle -->
            <button @click="darkMode = !darkMode" class="p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800">
                <i class="fa-solid" :class="darkMode ? 'fa-sun' : 'fa-moon'"></i>
            </button>
            
            <!-- Responsive Preview -->
            <div class="flex border rounded overflow-hidden">
                <button @click="previewDevice = 'desktop'" class="px-3 py-1 text-sm" :class="previewDevice === 'desktop' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-800'"><i class="fa-solid fa-desktop"></i></button>
                <button @click="previewDevice = 'tablet'" class="px-3 py-1 text-sm" :class="previewDevice === 'tablet' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-800'"><i class="fa-solid fa-tablet-alt"></i></button>
                <button @click="previewDevice = 'mobile'" class="px-3 py-1 text-sm" :class="previewDevice === 'mobile' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-800'"><i class="fa-solid fa-mobile-alt"></i></button>
            </div>
            
            <!-- Preview Mode Toggle (NEW) -->
            <button @click="togglePreviewMode()" class="px-4 py-2 rounded-lg text-sm font-medium" :class="previewMode ? 'bg-purple-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'">
                <i class="fa-solid" :class="previewMode ? 'fa-edit' : 'fa-eye'"></i>
                <span x-text="previewMode ? 'Exit Preview' : 'Preview'"></span>
            </button>
            
            <button @click="savePage('draft')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fa-solid fa-save mr-2"></i> Save
            </button>
            <button @click="publishPage()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fa-solid fa-globe mr-2"></i> Publish
            </button>
        </div>
    </div>
</div>

<div class="flex h-screen overflow-hidden" :class="{'preview-mode': previewMode}">
    <!-- LEFT SIDEBAR - Collapsible (hidden in preview mode) -->
    <div x-show="!previewMode" :class="leftCollapsed ? 'w-16' : 'w-80'" class="bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700 overflow-y-auto transition-all duration-300">
        <button @click="leftCollapsed = !leftCollapsed" class="w-full p-2 text-center hover:bg-gray-100 dark:hover:bg-gray-800">
            <i class="fa-solid" :class="leftCollapsed ? 'fa-chevron-right' : 'fa-chevron-left'"></i>
        </button>
        
        <div x-show="!leftCollapsed" class="p-4">
            <!-- Widget Library -->
            <h3 class="text-xs font-semibold uppercase mb-3 dark:text-gray-400 flex items-center"><i class="fa-solid fa-puzzle-piece mr-2"></i> Widgets</h3>
            <div class="space-y-2">
                <div draggable="true" @dragstart="dragStart($event, 'heading')" class="p-3 border rounded-lg cursor-move hover:bg-gray-50 dark:hover:bg-gray-800 flex items-center gap-2 transition"><i class="fa-solid fa-heading w-5"></i><span>Heading</span></div>
                <div draggable="true" @dragstart="dragStart($event, 'paragraph')" class="p-3 border rounded-lg cursor-move hover:bg-gray-50 dark:hover:bg-gray-800 flex items-center gap-2"><i class="fa-solid fa-paragraph w-5"></i><span>Paragraph</span></div>
                <div draggable="true" @dragstart="dragStart($event, 'image')" class="p-3 border rounded-lg cursor-move hover:bg-gray-50 dark:hover:bg-gray-800 flex items-center gap-2"><i class="fa-solid fa-image w-5"></i><span>Image</span></div>
                <div draggable="true" @dragstart="dragStart($event, 'button')" class="p-3 border rounded-lg cursor-move hover:bg-gray-50 dark:hover:bg-gray-800 flex items-center gap-2"><i class="fa-solid fa-link w-5"></i><span>Button</span></div>
                <div draggable="true" @dragstart="dragStart($event, 'iconbox')" class="p-3 border rounded-lg cursor-move hover:bg-gray-50 dark:hover:bg-gray-800 flex items-center gap-2"><i class="fa-solid fa-icons w-5"></i><span>Icon Box</span></div>
                <div draggable="true" @dragstart="dragStart($event, 'testimonial')" class="p-3 border rounded-lg cursor-move hover:bg-gray-50 dark:hover:bg-gray-800"><i class="fa-solid fa-quote-right w-5"></i> Testimonial</div>
                <div draggable="true" @dragstart="dragStart($event, 'accordion')" class="p-3 border rounded-lg cursor-move hover:bg-gray-50 dark:hover:bg-gray-800"><i class="fa-solid fa-chevron-down w-5"></i> Accordion</div>
                <div draggable="true" @dragstart="dragStart($event, 'progress')" class="p-3 border rounded-lg cursor-move hover:bg-gray-50 dark:hover:bg-gray-800"><i class="fa-solid fa-chart-line w-5"></i> Progress Bar</div>
                <div draggable="true" @dragstart="dragStart($event, 'social')" class="p-3 border rounded-lg cursor-move hover:bg-gray-50 dark:hover:bg-gray-800"><i class="fa-solid fa-share-alt w-5"></i> Social Share</div>
                <div draggable="true" @dragstart="dragStart($event, 'contactform')" class="p-3 border rounded-lg cursor-move hover:bg-gray-50 dark:hover:bg-gray-800"><i class="fa-solid fa-envelope w-5"></i> Contact Form</div>
                <div draggable="true" @dragstart="dragStart($event, 'columns')" class="p-3 border rounded-lg cursor-move hover:bg-gray-50 dark:hover:bg-gray-800"><i class="fa-solid fa-columns w-5"></i> 2 Columns</div>
            </div>

            <div class="mt-4 rounded-lg border border-blue-200 bg-blue-50 p-3 text-xs text-blue-900 dark:border-blue-800 dark:bg-blue-950 dark:text-blue-100">
                <p class="font-semibold">Adding widgets one by one</p>
                <p class="mt-1">Create a new key in the <code>defaults</code> object, add one draggable item that uses that key, add one preview block in the canvas, add matching settings on the right, then add the public renderer in <code>public/page.blade.php</code>.</p>
            </div>
            
            <!-- Global Styling Panel -->
            <div class="mt-6 border-t pt-4">
                <h3 class="text-xs font-semibold uppercase mb-3 dark:text-gray-400"><i class="fa-solid fa-palette mr-2"></i> Global Styles</h3>
                <div class="space-y-3">
                    <div><label class="text-sm">Primary Color</label><div class="flex gap-2"><input type="color" x-model="globalStyles.primaryColor" class="h-10 w-full rounded border"><span class="color-preview" :style="{backgroundColor: globalStyles.primaryColor}"></span></div></div>
                    <div><label class="text-sm">Font Family</label><select x-model="globalStyles.fontFamily" class="w-full border rounded p-2"><option>Inter</option><option>Arial</option><option>Georgia</option><option>Roboto</option></select></div>
                </div>
            </div>
            
            <!-- Templates Library -->
            <div class="mt-6 border-t pt-4">
                <h3 class="text-xs font-semibold uppercase mb-3"><i class="fa-solid fa-layer-group mr-2"></i> Templates</h3>
                <input type="text" x-model="templateName" placeholder="Template name" class="w-full border rounded p-2 text-sm mb-2">
                <button @click="saveTemplate()" class="bg-purple-600 text-white px-3 py-2 rounded text-sm w-full">Save as Template</button>
                <div class="mt-2 space-y-1 max-h-40 overflow-y-auto">
                    <template x-for="(tpl, idx) in savedTemplates" :key="idx">
                        <button @click="loadTemplate(tpl)" class="text-left text-sm p-2 hover:bg-gray-100 dark:hover:bg-gray-800 w-full rounded flex justify-between"><span x-text="tpl.name"></span><i class="fa-solid fa-download"></i></button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN CANVAS with preview mode and background customization -->
    <div class="flex-1 overflow-y-auto p-6 transition-all" :class="previewMode ? 'bg-gray-100 dark:bg-gray-800' : 'bg-gray-100 dark:bg-gray-800'" x-ref="canvasContainer" @dragover.prevent @drop="dropOnCanvas($event)" @contextmenu.prevent="showContextMenu($event)">
        <!-- Page Background Settings Bar -->
        <div x-show="!previewMode" class="max-w-6xl mx-auto mb-4 bg-white dark:bg-gray-900 rounded-lg shadow-sm p-3 flex items-center gap-4 flex-wrap">
            <span class="text-sm font-semibold"><i class="fa-solid fa-fill-drip mr-1"></i> Page Background:</span>
            <div class="flex items-center gap-2"><i class="fa-solid fa-palette"></i><input type="color" x-model="pageBackground.color" class="h-8 w-12 rounded border"><span class="text-xs">Color</span></div>
            <div class="flex items-center gap-2"><i class="fa-solid fa-image"></i><input type="text" x-model="pageBackground.image" placeholder="Image URL" class="border rounded p-1 text-sm w-64"><button @click="uploadBackgroundImage()" class="bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded text-xs">Upload</button><span class="text-xs">Image URL</span></div>
            <div class="flex items-center gap-2"><select x-model="pageBackground.size" class="border rounded p-1 text-sm"><option>auto</option><option>cover</option><option>contain</option></select><span class="text-xs">Size</span></div>
            <button @click="pageBackground.image = ''; pageBackground.color = '#ffffff'" class="text-red-500 text-sm"><i class="fa-solid fa-eraser"></i> Reset</button>
        </div>
        
        <div class="mx-auto transition-all" :style="{maxWidth: previewDevice === 'desktop' ? '1200px' : (previewDevice === 'tablet' ? '768px' : '375px'), transform: `scale(${zoomLevel})`, transformOrigin: 'top center'}">
            <!-- Main Content Area with Dynamic Background -->
            <div class="rounded-xl shadow-lg transition-all bg-preview-section" :style="{backgroundColor: pageBackground.color, backgroundImage: pageBackground.image ? `url(${pageBackground.image})` : 'none', backgroundSize: pageBackground.size, backgroundPosition: 'center', backgroundRepeat: 'no-repeat'}">
                
                <!-- Page Title Editor -->
                <div class="p-6" :class="previewMode ? '' : 'border-b border-gray-200'">
                    <div x-show="!previewMode">
                        <input type="text" x-model="pageTitle" placeholder="Page Title" class="text-3xl font-bold w-full bg-transparent focus:outline-none dark:text-white border-b-2 border-transparent focus:border-blue-400 pb-2">
                    </div>
                    <div x-show="previewMode">
                        <h1 class="text-3xl font-bold dark:text-white" x-text="pageTitle || 'Untitled Page'"></h1>
                    </div>
                    <div x-show="!previewMode" class="mt-2">
                        <input type="text" x-model="pageSlug" placeholder="page-slug" class="text-sm text-gray-500 w-full bg-transparent focus:outline-none">
                    </div>
                </div>
                
                <!-- Components Area -->
                <div id="builder-area" class="p-6 space-y-4 min-h-[500px]">
                    <template x-for="(component, idx) in components" :key="idx">
                        <div :data-idx="idx" @dragover.prevent="dragOverComponent($event, idx)" @drop="dropOnComponent($event, idx)" class="component-wrapper group relative border-2 border-transparent hover:border-blue-300 rounded-lg transition-all" :class="{'border-blue-500 bg-blue-50 dark:bg-blue-900/20': selectedComponentIdx === idx && !previewMode}">
                            
                            <!-- Component Controls (hidden in preview mode) -->
                            <div x-show="selectedComponentIdx === idx && !previewMode" class="component-controls absolute -top-10 right-0 flex space-x-1 bg-white dark:bg-gray-800 shadow-lg rounded-md p-1 z-10 border">
                                <button @click="duplicateComponent(idx)" class="p-1.5 hover:text-green-600 rounded"><i class="fa-regular fa-copy"></i></button>
                                <button @click="copyComponent(idx)" class="p-1.5 hover:text-blue-600 rounded"><i class="fa-regular fa-clipboard"></i></button>
                                <button @click="deleteComponent(idx)" class="p-1.5 hover:text-red-600 rounded"><i class="fa-regular fa-trash-alt"></i></button>
                                <button @click="moveUp(idx)" :disabled="idx===0" class="p-1.5 hover:text-gray-600"><i class="fa-solid fa-arrow-up"></i></button>
                                <button @click="moveDown(idx)" :disabled="idx===components.length-1" class="p-1.5 hover:text-gray-600"><i class="fa-solid fa-arrow-down"></i></button>
                            </div>
                            
                            <!-- Render Component -->
                            <div @click="!previewMode && selectComponent(idx)" @dblclick="!previewMode && inlineEdit(idx)" class="p-4 cursor-pointer">
                                <!-- Heading -->
                                <div x-show="component.type === 'heading'"><h2 :style="{textAlign: component.align, color: component.color, fontFamily: globalStyles.fontFamily}" class="text-2xl font-semibold" x-text="component.content || 'New Heading'"></h2></div>
                                <!-- Paragraph -->
                                <div x-show="component.type === 'paragraph'" class="prose max-w-none" x-html="component.content || '<p>Write your text content here. You can edit this text by selecting the component and using the settings panel.</p>'"></div>
                                <!-- Image -->
                                <div x-show="component.type === 'image'"><img :src="component.src || 'https://placehold.co/800x400?text=Click+to+select+image'" class="max-w-full rounded shadow-sm" loading="lazy"></div>
                                <!-- Button -->
                                <div x-show="component.type === 'button'"><a :href="component.link || '#'" class="inline-block px-6 py-2 rounded-lg font-medium transition" :style="{backgroundColor: component.bgColor || globalStyles.primaryColor, color: 'white'}" x-text="component.text || 'Click Me'"></a></div>
                                <!-- Icon Box -->
                                <div x-show="component.type === 'iconbox'" class="text-center p-6 rounded-lg transition" :style="{backgroundColor: component.bgColor || '#f8fafc'}"><i :class="component.icon || 'fa-solid fa-star'" :style="{fontSize: '48px', color: component.iconColor || globalStyles.primaryColor}"></i><h3 class="font-bold text-xl mt-3" x-text="component.title || 'Icon Title'"></h3><p class="mt-2 text-gray-600 dark:text-gray-300" x-text="component.description || 'Description text goes here'"></p></div>
                                <!-- Testimonial -->
                                <div x-show="component.type === 'testimonial'" class="bg-gray-100 dark:bg-gray-800 p-8 rounded-xl italic relative"><i class="fa-solid fa-quote-left absolute text-6xl opacity-10"></i><p class="relative z-10 text-lg" x-text="component.text || 'Amazing experience! This builder transformed our workflow completely.'"></p><div class="font-bold mt-4 flex items-center gap-2"><i class="fa-regular fa-circle-user"></i><span x-text="component.author || 'John Doe'"></span></div></div>
                                <!-- Accordion -->
                                <div x-show="component.type === 'accordion'" class="space-y-2"><template x-for="(item, i) in component.items"><div class="border rounded-lg overflow-hidden"><button @click="item.open = !item.open" class="w-full text-left p-3 font-semibold bg-gray-50 dark:bg-gray-800 flex justify-between items-center"><span x-text="item.title"></span><i :class="item.open ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'"></i></button><div x-show="item.open" class="p-3 border-t" x-html="item.content"></div></div></template></div>
                                <!-- Progress Bar -->
                                <div x-show="component.type === 'progress'"><div class="flex justify-between mb-1"><span x-text="component.label || 'Progress'"></span><span x-text="component.percent || 75 + '%'"></span></div><div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden"><div class="h-3 rounded-full transition-all duration-500" :style="{width: (component.percent || 75) + '%', backgroundColor: component.color || globalStyles.primaryColor}"></div></div></div>
                                <!-- Social Share -->
                                <div x-show="component.type === 'social'" class="flex space-x-4 justify-center"><button @click="share('facebook')" class="w-10 h-10 rounded-full bg-[#1877f2] text-white flex items-center justify-center hover:scale-110 transition"><i class="fa-brands fa-facebook-f"></i></button><button @click="share('twitter')" class="w-10 h-10 rounded-full bg-[#1da1f2] text-white flex items-center justify-center hover:scale-110"><i class="fa-brands fa-twitter"></i></button><button @click="share('linkedin')" class="w-10 h-10 rounded-full bg-[#0077b5] text-white flex items-center justify-center hover:scale-110"><i class="fa-brands fa-linkedin-in"></i></button><button @click="share('pinterest')" class="w-10 h-10 rounded-full bg-[#e60023] text-white flex items-center justify-center hover:scale-110"><i class="fa-brands fa-pinterest-p"></i></button></div>
                                <!-- Contact Form -->
                                <div x-show="component.type === 'contactform'"><form @submit.prevent="submitForm" class="space-y-3"><input type="text" placeholder="Your Name" class="w-full border rounded-lg p-3 dark:bg-gray-800"><input type="email" placeholder="Email Address" class="w-full border rounded-lg p-3 dark:bg-gray-800"><textarea placeholder="Your Message" rows="4" class="w-full border rounded-lg p-3 dark:bg-gray-800"></textarea><button type="submit" class="px-6 py-3 rounded-lg text-white font-semibold transition" :style="{backgroundColor: globalStyles.primaryColor}"><i class="fa-regular fa-paper-plane mr-2"></i>Send Message</button></form></div>
                                <!-- 2 Columns -->
                                <div x-show="component.type === 'columns'" class="grid md:grid-cols-2 gap-6"><div class="p-4 border rounded-lg bg-gray-50 dark:bg-gray-800" x-html="component.col1Content || '<h3>Column 1</h3><p>Content for first column</p>'"></div><div class="p-4 border rounded-lg bg-gray-50 dark:bg-gray-800" x-html="component.col2Content || '<h3>Column 2</h3><p>Content for second column</p>'"></div></div>
                            </div>
                        </div>
                    </template>
                    
                    <div x-show="components.length === 0 && !previewMode" class="text-center py-20 border-2 border-dashed rounded-lg bg-gray-50 dark:bg-gray-900"><i class="fa-solid fa-drag-drop text-5xl text-gray-400 mb-3"></i><p class="text-gray-500">Drag widgets from the left panel to start building your page</p></div>
                    <div x-show="components.length === 0 && previewMode" class="text-center py-20"><p class="text-gray-500">No content yet. Exit preview mode to add widgets.</p></div>
                </div>
            </div>
        </div>
        
        <!-- Zoom Controls -->
        <div class="fixed bottom-4 right-4 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-2 flex space-x-2 border">
            <button @click="zoomLevel = Math.max(0.5, zoomLevel - 0.1)" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300">−</button>
            <span class="px-2 min-w-[50px] text-center" x-text="Math.round(zoomLevel * 100) + '%'"></span>
            <button @click="zoomLevel = Math.min(1.5, zoomLevel + 0.1)" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300">+</button>
        </div>
    </div>

    <!-- RIGHT SIDEBAR - Settings Panel (hidden in preview mode) -->
    <div x-show="!previewMode" :class="rightCollapsed ? 'w-10' : 'w-80'" class="bg-white dark:bg-gray-900 border-l border-gray-200 dark:border-gray-700 overflow-y-auto transition-all duration-300">
        <button @click="rightCollapsed = !rightCollapsed" class="w-full p-2 hover:bg-gray-100 dark:hover:bg-gray-800"><i :class="rightCollapsed ? 'fa-chevron-left' : 'fa-chevron-right'" class="fa-solid"></i></button>
        <div x-show="!rightCollapsed && selectedComponentIdx !== null" class="p-4">
            <h3 class="font-bold mb-4 flex items-center gap-2"><i class="fa-solid fa-sliders-h"></i> Widget Settings</h3>
            
            <div x-show="selectedComp().type === 'heading'">
                <label class="block text-sm font-medium mb-1">Heading Text</label><input type="text" x-model="selectedComp().content" class="w-full border rounded-lg p-2 mb-3 dark:bg-gray-800">
                <label class="block text-sm font-medium mb-1">Alignment</label><select x-model="selectedComp().align" class="w-full border rounded-lg p-2 mb-3"><option>left</option><option>center</option><option>right</option></select>
                <label class="block text-sm font-medium mb-1">Color</label><input type="color" x-model="selectedComp().color" class="w-full h-10 rounded border mb-3">
            </div>
            
            <div x-show="selectedComp().type === 'paragraph'">
                <label class="block text-sm font-medium mb-1">HTML Content</label><textarea x-model="selectedComp().content" rows="6" class="w-full border rounded-lg p-2 font-mono text-sm"></textarea>
            </div>
            
            <div x-show="selectedComp().type === 'image'">
                <label class="block text-sm font-medium mb-1">Image URL</label><input type="text" x-model="selectedComp().src" class="w-full border rounded-lg p-2 mb-2"><button @click="openMediaLibrary()" class="bg-gray-200 dark:bg-gray-700 px-3 py-2 rounded text-sm w-full">Browse Media Library</button>
            </div>
            
            <div x-show="selectedComp().type === 'button'">
                <label class="block text-sm font-medium mb-1">Button Text</label><input type="text" x-model="selectedComp().text" class="w-full border rounded-lg p-2 mb-3">
                <label class="block text-sm font-medium mb-1">Link URL</label><input type="text" x-model="selectedComp().link" class="w-full border rounded-lg p-2 mb-3">
                <label class="block text-sm font-medium mb-1">Background Color</label><input type="color" x-model="selectedComp().bgColor" class="w-full h-10 rounded border">
            </div>
            
            <div x-show="selectedComp().type === 'iconbox'">
                <label class="block text-sm font-medium mb-1">Icon Class (Font Awesome)</label><input type="text" x-model="selectedComp().icon" placeholder="fa-solid fa-star" class="w-full border rounded-lg p-2 mb-3">
                <label class="block text-sm font-medium mb-1">Title</label><input type="text" x-model="selectedComp().title" class="w-full border rounded-lg p-2 mb-3">
                <label class="block text-sm font-medium mb-1">Description</label><textarea x-model="selectedComp().description" rows="3" class="w-full border rounded-lg p-2"></textarea>
            </div>
            
            <div x-show="selectedComp().type === 'progress'">
                <label class="block text-sm font-medium mb-1">Label</label><input type="text" x-model="selectedComp().label" class="w-full border rounded-lg p-2 mb-3">
                <label class="block text-sm font-medium mb-1">Percentage (0-100)</label><input type="number" x-model="selectedComp().percent" class="w-full border rounded-lg p-2 mb-3">
            </div>
            
            <div x-show="selectedComp().type === 'accordion'">
                <button @click="addAccordionItem()" class="bg-blue-600 text-white px-4 py-2 rounded text-sm w-full"><i class="fa-solid fa-plus mr-2"></i>Add Accordion Item</button>
            </div>
            
            <div x-show="selectedComp().type === 'testimonial'">
                <label class="block text-sm font-medium mb-1">Testimonial Text</label><textarea x-model="selectedComp().text" rows="3" class="w-full border rounded-lg p-2 mb-3"></textarea>
                <label class="block text-sm font-medium mb-1">Author</label><input type="text" x-model="selectedComp().author" class="w-full border rounded-lg p-2">
            </div>
        </div>
        
        <!-- SEO Analysis Panel -->
        <div x-show="!rightCollapsed && !previewMode" class="p-4 border-t mt-4">
            <h3 class="font-bold mb-3 flex items-center gap-2"><i class="fa-solid fa-chart-line"></i> SEO Analysis</h3>
            <div class="space-y-3 text-sm">
                <div :class="pageTitle.length >= 50 && pageTitle.length <= 60 ? 'text-green-600' : 'text-red-600'"><i class="fa-regular" :class="pageTitle.length >= 50 && pageTitle.length <= 60 ? 'fa-circle-check' : 'fa-circle-exclamation'"></i> Title: <span x-text="pageTitle.length"></span>/50-60 chars</div>
                <div><label class="block text-xs">Meta Description</label><textarea x-model="metaDescription" rows="2" class="w-full border rounded p-1 text-xs" maxlength="160"></textarea><span class="text-xs" :class="metaDescription.length > 120 && metaDescription.length <= 160 ? 'text-green-600' : 'text-orange-500'"><span x-text="metaDescription.length"></span>/160</span></div>
                <div :class="components.filter(c => c.type === 'image').length > 0 ? 'text-green-600' : 'text-orange-500'"><i class="fa-regular fa-image"></i> Images: <span x-text="components.filter(c => c.type === 'image').length"></span></div>
            </div>
        </div>
    </div>
</div>

<!-- Media Library Modal -->
<div x-show="showMediaModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-cloak>
    <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-2xl shadow-2xl">
        <div class="p-4 border-b flex justify-between items-center"><h3 class="font-bold">Media Library</h3><button @click="showMediaModal = false" class="text-gray-500 hover:text-gray-700"><i class="fa-solid fa-times"></i></button></div>
        <div class="p-5">
            <div class="mb-4"><input type="file" accept="image/*" @change="uploadNewImage($event)" class="w-full border rounded p-2"></div>
            <div class="grid grid-cols-3 gap-3 max-h-96 overflow-y-auto">
                <template x-for="(img, i) in mediaItems" :key="i">
                    <div class="border rounded p-2 cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900 transition" @click="selectMedia(img.url)"><img :src="img.url" class="h-28 w-full object-cover rounded"><p class="text-xs truncate mt-1" x-text="img.name"></p></div>
                </template>
            </div>
            <div class="mt-4 flex justify-end"><button @click="showMediaModal = false" class="px-4 py-2 bg-blue-600 text-white rounded">Close</button></div>
        </div>
    </div>
</div>

<!-- Context Menu -->
<div x-show="contextMenu.visible" :style="{top: contextMenu.y+'px', left: contextMenu.x+'px'}" class="context-menu p-2 space-y-1 shadow-xl" @click.away="contextMenu.visible = false">
    <button @click="duplicateComponent(contextMenu.componentIdx)" class="block w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-sm"><i class="fa-regular fa-copy mr-2"></i>Duplicate</button>
    <button @click="deleteComponent(contextMenu.componentIdx)" class="block w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-sm text-red-600"><i class="fa-regular fa-trash-alt mr-2"></i>Delete</button>
    <button @click="copyComponent(contextMenu.componentIdx)" class="block w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-sm"><i class="fa-regular fa-clipboard mr-2"></i>Copy</button>
</div>

<!-- Toast -->
<div x-show="toastMessage" x-transition.duration.300ms class="fixed bottom-6 right-6 bg-green-600 text-white px-5 py-3 rounded-lg shadow-lg z-50 text-sm toast flex items-center gap-2"><i class="fa-regular fa-circle-check"></i><span x-text="toastMessage"></span></div>

<script>
function pageBuilderApp() {
    return {
        darkMode: false,
        leftCollapsed: false,
        rightCollapsed: false,
        previewDevice: 'desktop',
        previewMode: false,
        zoomLevel: 1,
        initialPageData: @json($initialPageData),
        storageKey: @json($editingPage ? 'builder_page_'.$page->id : 'builder_page_new'),
        pageTitle: @json($initialPageData['pageTitle']),
        pageSlug: @json($initialPageData['pageSlug']),
        metaDescription: @json($initialPageData['metaDescription']),
        components: [],
        selectedComponentIdx: null,
        undoStack: [],
        redoStack: [],
        globalStyles: { primaryColor: '#3b82f6', fontFamily: 'Inter' },
        pageBackground: { color: '#ffffff', image: '', size: 'cover' },
        savedTemplates: [],
        templateName: '',
        clipboard: null,
        toastMessage: '',
        contextMenu: { visible: false, x: 0, y: 0, componentIdx: null },
        dragType: null,
        showMediaModal: false,
        mediaItems: [{ url: 'https://picsum.photos/id/100/400/300', name: 'landscape.jpg' }, { url: 'https://picsum.photos/id/20/400/300', name: 'coffee.jpg' }, { url: 'https://picsum.photos/id/26/400/300', name: 'venice.jpg' }],
        
        initBuilder() {
            if (Array.isArray(this.initialPageData.components)) {
                this.components = JSON.parse(JSON.stringify(this.initialPageData.components));
                this.pageBackground = this.initialPageData.pageBackground || { color: '#ffffff', image: '', size: 'cover' };
                this.saveToUndo();
            } else {
                this.loadSamplePage();
            }
            setInterval(() => this.autoSave(), 30000);
            window.addEventListener('keydown', (e) => {
                if (e.ctrlKey && e.key === 'z') { e.preventDefault(); this.undo(); }
                if (e.ctrlKey && e.key === 'y') { e.preventDefault(); this.redo(); }
                if (e.key === 'Delete' && this.selectedComponentIdx !== null) { this.deleteComponent(this.selectedComponentIdx); }
                if (e.ctrlKey && e.key === 'c' && this.selectedComponentIdx !== null) { this.copyComponent(this.selectedComponentIdx); }
                if (e.ctrlKey && e.key === 'v' && this.clipboard) { this.pasteComponent(); }
            });
            let stored = localStorage.getItem('builder_templates');
            if(stored) this.savedTemplates = JSON.parse(stored);
            let savedPage = localStorage.getItem(this.storageKey);
            if(savedPage && !@json($editingPage)) { let data = JSON.parse(savedPage); this.components = data.components; this.pageTitle = data.pageTitle; this.pageSlug = data.slug || data.pageSlug || this.pageSlug; this.metaDescription = data.metaDescription || this.metaDescription; this.pageBackground = data.pageBackground || { color: '#ffffff', image: '', size: 'cover' }; }
        },
        
        loadSamplePage() {
            this.components = [
                { type: 'heading', content: 'Build Beautiful Pages With Ease', align: 'center', color: '#3b82f6' },
                { type: 'paragraph', content: '<p class="text-lg">Our drag-and-drop page builder gives you complete creative freedom. No coding required.</p>' },
                { type: 'iconbox', icon: 'fa-regular fa-gem', title: 'Premium Quality', description: 'Crafted with attention to detail and user experience', iconColor: '#8b5cf6', bgColor: '#f5f3ff' },
                { type: 'iconbox', icon: 'fa-solid fa-bolt', title: 'Lightning Fast', description: 'Optimized performance for the best user experience', iconColor: '#f59e0b', bgColor: '#fffbeb' },
                { type: 'testimonial', text: 'This builder saved us weeks of development time. Highly recommended!', author: 'Sarah Johnson, TechLead' },
                { type: 'button', text: 'Get Started Now', link: '#', bgColor: '#3b82f6' }
            ];
            this.saveToUndo();
        },
        
        togglePreviewMode() { this.previewMode = !this.previewMode; if(this.previewMode) this.selectedComponentIdx = null; },
        
        saveToUndo() { this.undoStack.push(JSON.parse(JSON.stringify(this.components))); this.redoStack = []; },
        undo() { if(this.undoStack.length > 1) { this.redoStack.push(this.undoStack.pop()); this.components = JSON.parse(JSON.stringify(this.undoStack[this.undoStack.length-1])); this.showToast('Undo'); } },
        redo() { if(this.redoStack.length) { this.undoStack.push(this.redoStack.pop()); this.components = JSON.parse(JSON.stringify(this.undoStack[this.undoStack.length-1])); this.showToast('Redo'); } },
        
        dragStart(event, type) { this.dragType = type; event.dataTransfer.setData('text/plain', type); },
        dropOnCanvas(event) { event.preventDefault(); if(this.dragType) { this.components.push(this.createComponent(this.dragType)); this.saveToUndo(); this.showToast(`Added ${this.dragType}`); this.dragType = null; } },
        dropOnComponent(event, idx) { event.preventDefault(); if(this.dragType) { this.components.splice(idx+1, 0, this.createComponent(this.dragType)); this.saveToUndo(); this.dragType = null; } },
        dragOverComponent(event, idx) { event.preventDefault(); event.currentTarget.classList.add('drop-zone-active'); setTimeout(() => event.currentTarget.classList.remove('drop-zone-active'), 200); },
        
        createComponent(type) {
            const defaults = {
                heading: { type: 'heading', content: 'New Heading', align: 'left', color: '#000000' },
                paragraph: { type: 'paragraph', content: '<p>Write your text here. Double-click to edit quickly.</p>' },
                image: { type: 'image', src: 'https://picsum.photos/600/400?random=1' },
                button: { type: 'button', text: 'Click Me', link: '#', bgColor: '#3b82f6' },
                iconbox: { type: 'iconbox', icon: 'fa-regular fa-star', title: 'Icon Title', description: 'Add description here', iconColor: '#3b82f6', bgColor: '#f8fafc' },
                testimonial: { type: 'testimonial', text: 'Amazing product!', author: 'Happy Customer' },
                accordion: { type: 'accordion', items: [{ title: 'Accordion Item 1', content: 'Content goes here', open: false }] },
                progress: { type: 'progress', label: 'Skill Level', percent: 75, color: '#3b82f6' },
                social: { type: 'social' },
                contactform: { type: 'contactform' },
                columns: { type: 'columns', col1Content: '<h3>Column 1</h3><p>Content for first column</p>', col2Content: '<h3>Column 2</h3><p>Content for second column</p>' }
            };
            return JSON.parse(JSON.stringify(defaults[type] || defaults.paragraph));
        },
        
        selectComponent(idx) { this.selectedComponentIdx = idx; },
        selectedComp() { if(this.selectedComponentIdx !== null) return this.components[this.selectedComponentIdx]; return null; },
        deleteComponent(idx) { if(confirm('Delete this widget?')) { this.components.splice(idx,1); this.selectedComponentIdx = null; this.saveToUndo(); this.showToast('Deleted'); } },
        duplicateComponent(idx) { let copy = JSON.parse(JSON.stringify(this.components[idx])); this.components.splice(idx+1, 0, copy); this.saveToUndo(); this.showToast('Duplicated'); },
        copyComponent(idx) { this.clipboard = JSON.parse(JSON.stringify(this.components[idx])); this.showToast('Copied'); },
        pasteComponent() { if(this.clipboard) { this.components.push(JSON.parse(JSON.stringify(this.clipboard))); this.saveToUndo(); this.showToast('Pasted'); } },
        moveUp(idx) { if(idx>0) { [this.components[idx-1], this.components[idx]] = [this.components[idx], this.components[idx-1]]; this.selectedComponentIdx = idx-1; this.saveToUndo(); } },
        moveDown(idx) { if(idx<this.components.length-1) { [this.components[idx+1], this.components[idx]] = [this.components[idx], this.components[idx+1]]; this.selectedComponentIdx = idx+1; this.saveToUndo(); } },
        inlineEdit(idx) { alert('Quick edit: Click the component then use the right sidebar for detailed editing'); },
        
        addAccordionItem() { if(this.selectedComp() && this.selectedComp().type === 'accordion') { this.selectedComp().items.push({ title: 'New Item', content: 'Item content', open: false }); this.saveToUndo(); } },
        
        share(platform) { alert(`Share on ${platform}`); },
        submitForm() { alert('Message sent! (demo)'); },
        
        uploadBackgroundImage() { let url = prompt('Enter image URL for background:'); if(url) this.pageBackground.image = url; },
        uploadNewImage(event) { let file = event.target.files[0]; if(file) { let url = URL.createObjectURL(file); this.mediaItems.push({ url: url, name: file.name }); this.showToast('Image uploaded'); } },
        openMediaLibrary() { this.showMediaModal = true; },
        selectMedia(url) { if(this.selectedComp() && this.selectedComp().type === 'image') { this.selectedComp().src = url; } this.showMediaModal = false; this.showToast('Image selected'); },
        
        async savePage(status = 'draft') {
            let pageData = { title: this.pageTitle, slug: this.pageSlug, components: this.components, metaDescription: this.metaDescription, pageBackground: this.pageBackground };
            localStorage.setItem(this.storageKey, JSON.stringify(pageData));

            const response = await fetch('{{ $editingPage ? route('admin.pages.update', $page) : route('admin.pages.store') }}', {
                method: '{{ $editingPage ? 'PUT' : 'POST' }}',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    title: this.pageTitle || 'Untitled Page',
                    slug: this.pageSlug || this.pageTitle.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, ''),
                    content: JSON.stringify(pageData),
                    status: status,
                    template: 'v3-builder',
                    meta_title: this.pageTitle,
                    meta_description: this.metaDescription
                })
            });

            if (response.redirected) {
                localStorage.removeItem(this.storageKey);
                window.location.href = response.url;
                return;
            }

            if (!response.ok) {
                const data = await response.json().catch(() => ({}));
                const message = data.message || Object.values(data.errors || {}).flat().join(' ') || 'Unable to save page.';
                this.showToast(message);
                return;
            }

            localStorage.removeItem(this.storageKey);
            window.location.href = '{{ route('admin.pages.index') }}';
        },
        
        publishPage() { this.savePage('published'); },
        autoSave() { localStorage.setItem(this.storageKey, JSON.stringify({ title: this.pageTitle, slug: this.pageSlug, components: this.components, metaDescription: this.metaDescription, pageBackground: this.pageBackground })); },
        
        saveTemplate() { if(this.templateName) { let template = { name: this.templateName, data: { components: this.components, title: this.pageTitle } }; this.savedTemplates.push(template); localStorage.setItem('builder_templates', JSON.stringify(this.savedTemplates)); this.showToast('Template saved'); this.templateName = ''; } },
        loadTemplate(template) { this.components = JSON.parse(JSON.stringify(template.data.components)); this.pageTitle = template.data.title; this.saveToUndo(); this.showToast(`Loaded ${template.name}`); },
        
        showContextMenu(event) { let target = event.target.closest('.component-wrapper'); if(target && !this.previewMode) { let idx = target.getAttribute('data-idx'); this.contextMenu = { visible: true, x: event.pageX, y: event.pageY, componentIdx: parseInt(idx) }; } },
        
        showToast(msg) { this.toastMessage = msg; setTimeout(() => this.toastMessage = '', 2500); }
    }
}
</script>
</body>
</html>
