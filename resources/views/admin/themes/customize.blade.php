@extends('layouts.admin')

@section('title', 'Customize: ' . $theme->name)

@section('content')
<div class="p-6" x-data="themeCustomizer({{ $theme->id }})" x-init="init()">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Customize Theme: {{ $theme->name }}</h1>
        <div class="flex gap-2">
            <button @click="saveSettings()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">
                <i class="fas fa-save mr-1"></i> Save Settings
            </button>
            <a href="{{ route('admin.themes.index') }}" class="border border-gray-300 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Settings Panel --}}
        <div class="lg:col-span-1 bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="font-bold text-lg mb-4 flex items-center gap-2">
                <i class="fas fa-sliders-h text-purple-500"></i> Theme Settings
            </h2>

            {{-- Colors Section --}}
            <div class="mb-6">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-3">🎨 Colors</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-gray-600 block mb-1">Primary Color</label>
                        <div class="flex gap-2">
                            <input type="color" x-model="settings.colors.primary" @change="updatePreview()" class="w-10 h-10 rounded border cursor-pointer">
                            <input type="text" x-model="settings.colors.primary" class="flex-1 border rounded px-2 py-1 text-sm" @change="updatePreview()">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 block mb-1">Secondary Color</label>
                        <div class="flex gap-2">
                            <input type="color" x-model="settings.colors.secondary" @change="updatePreview()" class="w-10 h-10 rounded border cursor-pointer">
                            <input type="text" x-model="settings.colors.secondary" class="flex-1 border rounded px-2 py-1 text-sm" @change="updatePreview()">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 block mb-1">Accent Color</label>
                        <div class="flex gap-2">
                            <input type="color" x-model="settings.colors.accent" @change="updatePreview()" class="w-10 h-10 rounded border cursor-pointer">
                            <input type="text" x-model="settings.colors.accent" class="flex-1 border rounded px-2 py-1 text-sm" @change="updatePreview()">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 block mb-1">Background Color</label>
                        <div class="flex gap-2">
                            <input type="color" x-model="settings.colors.background" @change="updatePreview()" class="w-10 h-10 rounded border cursor-pointer">
                            <input type="text" x-model="settings.colors.background" class="flex-1 border rounded px-2 py-1 text-sm" @change="updatePreview()">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 block mb-1">Text Color</label>
                        <div class="flex gap-2">
                            <input type="color" x-model="settings.colors.text" @change="updatePreview()" class="w-10 h-10 rounded border cursor-pointer">
                            <input type="text" x-model="settings.colors.text" class="flex-1 border rounded px-2 py-1 text-sm" @change="updatePreview()">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 block mb-1">Header Background</label>
                        <div class="flex gap-2">
                            <input type="color" x-model="settings.colors.header_bg" @change="updatePreview()" class="w-10 h-10 rounded border cursor-pointer">
                            <input type="text" x-model="settings.colors.header_bg" class="flex-1 border rounded px-2 py-1 text-sm" @change="updatePreview()">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 block mb-1">Footer Background</label>
                        <div class="flex gap-2">
                            <input type="color" x-model="settings.colors.footer_bg" @change="updatePreview()" class="w-10 h-10 rounded border cursor-pointer">
                            <input type="text" x-model="settings.colors.footer_bg" class="flex-1 border rounded px-2 py-1 text-sm" @change="updatePreview()">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Fonts Section --}}
            <div class="mb-6">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-3">🔤 Fonts</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-gray-600 block mb-1">Heading Font</label>
                        <select x-model="settings.fonts.heading" @change="updatePreview()" class="w-full border rounded px-2 py-1.5 text-sm">
                            <option value="Inter, sans-serif">Inter</option>
                            <option value="Georgia, serif">Georgia</option>
                            <option value="'Playfair Display', serif">Playfair Display</option>
                            <option value="'Roboto', sans-serif">Roboto</option>
                            <option value="'Montserrat', sans-serif">Montserrat</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 block mb-1">Body Font</label>
                        <select x-model="settings.fonts.body" @change="updatePreview()" class="w-full border rounded px-2 py-1.5 text-sm">
                            <option value="Inter, sans-serif">Inter</option>
                            <option value="Georgia, serif">Georgia</option>
                            <option value="'Roboto', sans-serif">Roboto</option>
                            <option value="'Open Sans', sans-serif">Open Sans</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 block mb-1">Heading Weight</label>
                        <select x-model="settings.fonts.heading_weight" @change="updatePreview()" class="w-full border rounded px-2 py-1.5 text-sm">
                            <option value="400">Normal</option>
                            <option value="600">Semi-Bold</option>
                            <option value="700">Bold</option>
                            <option value="900">Black</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 block mb-1">Body Font Size</label>
                        <select x-model="settings.fonts.body_size" @change="updatePreview()" class="w-full border rounded px-2 py-1.5 text-sm">
                            <option value="14px">14px</option>
                            <option value="16px">16px</option>
                            <option value="18px">18px</option>
                            <option value="20px">20px</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Layout Section --}}
            <div class="mb-6">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-3">📐 Layout</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-gray-600 block mb-1">Container</label>
                        <select x-model="settings.layout.container" @change="updatePreview()" class="w-full border rounded px-2 py-1.5 text-sm">
                            <option value="full-width">Full Width</option>
                            <option value="boxed">Boxed</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 block mb-1">Header Style</label>
                        <select x-model="settings.layout.header_style" @change="updatePreview()" class="w-full border rounded px-2 py-1.5 text-sm">
                            <option value="centered">Centered</option>
                            <option value="left">Left Aligned</option>
                            <option value="split">Logo Left, Nav Right</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 block mb-1">Sidebar</label>
                        <select x-model="settings.layout.sidebar" @change="updatePreview()" class="w-full border rounded px-2 py-1.5 text-sm">
                            <option value="none">No Sidebar</option>
                            <option value="right">Right Sidebar</option>
                            <option value="left">Left Sidebar</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 block mb-1">Footer Columns</label>
                        <select x-model="settings.layout.footer_columns" @change="updatePreview()" class="w-full border rounded px-2 py-1.5 text-sm">
                            <option value="2">2 Columns</option>
                            <option value="3">3 Columns</option>
                            <option value="4">4 Columns</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Live Preview --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 border-b px-4 py-2 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">Live Preview</span>
                    <span class="text-xs text-gray-400">Changes appear in real-time</span>
                </div>
                <div class="p-0">
                    <iframe id="theme-preview" class="w-full h-[600px] border-0" srcdoc=""></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('themeCustomizer', (themeId) => ({
            settings: {
                colors: {
                    primary: '#0ea5e9',
                    secondary: '#8b5cf6',
                    accent: '#f59e0b',
                    background: '#ffffff',
                    text: '#1e293b',
                    header_bg: '#ffffff',
                    footer_bg: '#f8fafc'
                },
                fonts: {
                    heading: 'Inter, sans-serif',
                    body: 'Inter, sans-serif',
                    heading_weight: '700',
                    body_size: '16px'
                },
                layout: {
                    container: 'full-width',
                    sidebar: 'right',
                    header_style: 'centered',
                    footer_columns: 4
                }
            },

            async init() {
                try {
                    const res = await fetch(`/admin/themes/${themeId}/preview`);
                    const html = await res.text();
                    // Extract settings from page
                    this.updatePreview();
                } catch (e) {
                    console.error('Error loading theme:', e);
                }
            },

            updatePreview() {
                const style = `
                    :root {
                        --primary-color: ${this.settings.colors.primary};
                        --secondary-color: ${this.settings.colors.secondary};
                        --accent-color: ${this.settings.colors.accent};
                        --bg-color: ${this.settings.colors.background};
                        --text-color: ${this.settings.colors.text};
                        --header-bg: ${this.settings.colors.header_bg};
                        --footer-bg: ${this.settings.colors.footer_bg};
                        --heading-font: ${this.settings.fonts.heading};
                        --body-font: ${this.settings.fonts.body};
                        --heading-weight: ${this.settings.fonts.heading_weight};
                        --body-size: ${this.settings.fonts.body_size};
                    }
                    body { 
                        font-family: var(--body-font); 
                        font-size: var(--body-size);
                        color: var(--text-color); 
                        background: var(--bg-color); 
                    }
                    h1, h2, h3, h4, h5, h6 { font-family: var(--heading-font); font-weight: var(--heading-weight); }
                    a { color: var(--primary-color); }
                    header { background: var(--header-bg) !important; }
                    footer { background: var(--footer-bg) !important; }
                    .btn-primary { background: var(--primary-color); color: white; padding: 10px 20px; border-radius: 8px; }
                    .btn-secondary { background: var(--secondary-color); color: white; padding: 10px 20px; border-radius: 8px; }
                `;
                
                const preview = document.getElementById('theme-preview');
                if (preview) {
                    preview.srcdoc = `
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <style>${style}</style>
                            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
                        </head>
                        <body style="margin:0; padding:0;">
                            <header style="padding:20px 40px; border-bottom:1px solid #e2e8f0;">
                                <div style="max-width:1200px; margin:0 auto; display:flex; justify-content:space-between; align-items:center;">
                                    <h2 style="margin:0;">My Website</h2>
                                    <nav style="display:flex; gap:20px;">
                                        <a href="#">Home</a>
                                        <a href="#">About</a>
                                        <a href="#">Services</a>
                                        <a href="#">Blog</a>
                                        <a href="#">Contact</a>
                                    </nav>
                                </div>
                            </header>
                            <main style="max-width:1200px; margin:0 auto; padding:60px 40px;">
                                <h1>Welcome to My Website</h1>
                                <p style="font-size:18px; line-height:1.6;">This is a live preview of your theme. Changes you make on the left will appear here instantly.</p>
                                <div style="display:flex; gap:15px; margin-top:30px;">
                                    <button class="btn-primary">Primary Button</button>
                                    <button class="btn-secondary">Secondary Button</button>
                                </div>
                                <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:20px; margin-top:40px;">
                                    <div style="padding:20px; background:#f8fafc; border-radius:12px;">
                                        <h3>Feature One</h3>
                                        <p>Your content here with custom styling from theme.</p>
                                    </div>
                                    <div style="padding:20px; background:#f8fafc; border-radius:12px;">
                                        <h3>Feature Two</h3>
                                        <p>Beautiful design that matches your brand.</p>
                                    </div>
                                    <div style="padding:20px; background:#f8fafc; border-radius:12px;">
                                        <h3>Feature Three</h3>
                                        <p>Fully customizable to your preferences.</p>
                                    </div>
                                </div>
                            </main>
                            <footer style="padding:40px; border-top:1px solid #e2e8f0; margin-top:40px;">
                                <div style="max-width:1200px; margin:0 auto; text-align:center; opacity:0.7;">
                                    <p>&copy; 2026 My Website. All rights reserved.</p>
                                </div>
                            </footer>
                        </body>
                        </html>
                    `;
                }
            },

            async saveSettings() {
                const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                try {
                    const res = await fetch(`/admin/themes/${themeId}/settings`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ settings: this.settings })
                    });
                    const data = await res.json();
                    if (data.success) {
                        alert('Settings saved! Activate the theme to publish changes.');
                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch (e) {
                    alert('Error saving settings');
                }
            }
        }));
    });
</script>
@endsection