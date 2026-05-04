<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $pages = Page::orderBy('title')->get(['id', 'title', 'status']);

        return view('admin.settings.index', compact('settings', 'pages'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_name'        => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'posts_per_page'   => 'integer|min:1|max:100',
            'admin_email'      => 'nullable|email',
            'home_page_id'     => 'nullable|integer|exists:pages,id',
            'header_page_id'   => 'nullable|integer|exists:pages,id',
            'footer_page_id'   => 'nullable|integer|exists:pages,id',
        ]);

        foreach ($data as $key => $value) {
            Setting::setValue($key, $value);
        }

        return redirect()->route('admin.settings.index')
                         ->with('success', 'Settings saved.');
    }

    public function editSection(Request $request, string $section): RedirectResponse
    {
        $sectionConfig = [
            'home' => ['key' => 'home_page_id', 'title' => 'Home Page', 'slug' => 'home', 'status' => 'published'],
            'header' => ['key' => 'header_page_id', 'title' => 'Site Header', 'slug' => 'site-header', 'status' => 'published'],
            'footer' => ['key' => 'footer_page_id', 'title' => 'Site Footer', 'slug' => 'site-footer', 'status' => 'published'],
        ];

        abort_unless(isset($sectionConfig[$section]), 404);

        $config = $sectionConfig[$section];
        $pageId = Setting::getValue($config['key']);
        $page = $pageId ? Page::find($pageId) : null;

        if (! $page) {
            $page = Page::where('slug', $config['slug'])->first();

            if (! $page) {
                $page = Page::create([
                    'user_id' => $request->user()->id,
                    'title' => $config['title'],
                    'slug' => $config['slug'],
                    'content' => json_encode($this->buildDefaultSectionData($section), JSON_UNESCAPED_SLASHES),
                    'status' => $config['status'],
                    'template' => 'site-section',
                    'meta_title' => $config['title'],
                    'meta_description' => null,
                ]);
            }

            Setting::setValue($config['key'], $page->id);
        }

        return redirect()->route('admin.pages.edit', $page);
    }

    protected function buildDefaultSectionData(string $section): array
    {
        $siteName = Setting::getValue('site_name', config('app.name', 'CMS Portal'));

        return match ($section) {
            'header' => [
                'components' => [
                    [
                        'id' => 'header-title',
                        'type' => 'heading',
                        'settings' => [
                            'text' => $siteName,
                            'tag' => 'h2',
                            'alignment' => 'left',
                            'color' => '#0f172a',
                            'fontSize' => 28,
                            'fontWeight' => '700',
                            'pt' => 12,
                            'pb' => 4,
                        ],
                    ],
                    [
                        'id' => 'header-copy',
                        'type' => 'paragraph',
                        'settings' => [
                            'content' => '<p>Update this header content from the page builder any time.</p>',
                            'alignment' => 'left',
                            'pt' => 0,
                            'pb' => 0,
                        ],
                    ],
                ],
                'globalStyles' => ['bgColor' => '#ffffff', 'bgImage' => '', 'fontFamily' => 'Inter, sans-serif'],
                'seoData' => ['title' => 'Site Header', 'meta' => ''],
            ],
            'footer' => [
                'components' => [
                    [
                        'id' => 'footer-copy',
                        'type' => 'paragraph',
                        'settings' => [
                            'content' => '<p>&copy; '.date('Y').' '.$siteName.'. Update this footer from the builder.</p>',
                            'alignment' => 'center',
                            'pt' => 0,
                            'pb' => 0,
                        ],
                    ],
                ],
                'globalStyles' => ['bgColor' => '#0f172a', 'bgImage' => '', 'fontFamily' => 'Inter, sans-serif'],
                'seoData' => ['title' => 'Site Footer', 'meta' => ''],
            ],
            default => [
                'components' => [
                    [
                        'id' => 'home-heading',
                        'type' => 'heading',
                        'settings' => [
                            'text' => 'Welcome to '.$siteName,
                            'tag' => 'h1',
                            'alignment' => 'center',
                            'color' => '#0f172a',
                            'fontSize' => 48,
                            'fontWeight' => '700',
                            'pt' => 30,
                            'pb' => 12,
                        ],
                    ],
                    [
                        'id' => 'home-copy',
                        'type' => 'paragraph',
                        'settings' => [
                            'content' => '<p class="text-lg">This is your default home page. Replace this content in the builder and keep blog posts on their own page.</p>',
                            'alignment' => 'center',
                            'pt' => 0,
                            'pb' => 12,
                        ],
                    ],
                    [
                        'id' => 'home-button',
                        'type' => 'button',
                        'settings' => [
                            'text' => 'View Blog',
                            'link' => '/blog',
                            'bgColor' => '#2563eb',
                            'textColor' => '#ffffff',
                            'alignment' => 'center',
                            'pt' => 8,
                            'pb' => 20,
                        ],
                    ],
                ],
                'globalStyles' => ['bgColor' => '#ffffff', 'bgImage' => '', 'fontFamily' => 'Inter, sans-serif'],
                'seoData' => ['title' => 'Home', 'meta' => ''],
            ],
        };
    }
}
