<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use App\Services\ThemeService;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    protected $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    /**
     * List all themes.
     */
    public function index()
    {
        $themes = Theme::all();
        $activeTheme = Theme::getActive();
        return view('admin.themes.index', compact('themes', 'activeTheme'));
    }

    /**
     * Show install theme form.
     */
    public function create()
    {
        return view('admin.themes.create');
    }

    /**
     * Upload and install a new theme.
     */
    public function store(Request $request)
    {
        $request->validate([
            'theme_zip' => 'required|file|mimes:zip|max:51200', // 50MB max
        ]);

        $result = $this->themeService->installFromZip($request->file('theme_zip'));

        if ($result['success']) {
            return redirect()->route('admin.themes.index')
                ->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Activate a theme.
     */
    public function activate($id)
    {
        $theme = Theme::findOrFail($id);
        $theme->setAsActive();

        return response()->json([
            'success' => true,
            'message' => "Theme '{$theme->name}' activated successfully!",
        ]);
    }

    /**
     * Delete a theme.
     */
    public function destroy($id)
    {
        $theme = Theme::findOrFail($id);

        // Don't allow deleting the active theme
        if ($theme->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete the active theme. Activate another theme first.',
            ], 400);
        }

        // Don't allow deleting builtin themes
        if ($theme->is_builtin) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete built-in themes.',
            ], 400);
        }

        // Delete theme folder
        $themePath = public_path($theme->theme_path);
        if (is_dir($themePath)) {
            $this->deleteDirectory($themePath);
        }

        $theme->delete();

        return response()->json([
            'success' => true,
            'message' => 'Theme deleted successfully!',
        ]);
    }

	        public function customize($id)
    {
        $theme = Theme::findOrFail($id);
        $theme->setAsActive();
        
        $page = (object) [
            'id' => null,
            'title' => 'Theme: ' . $theme->name,
            'content' => json_encode([
                'components' => $theme->settings['components'] ?? [], 
                'globalStyles' => $theme->settings['globalStyles'] ?? []
            ]),
            'status' => 'draft',
        ];
        
        return view('admin.pages.create', [
            'page' => $page,
            'themeId' => $theme->id,
            'isThemeMode' => true,
        ]);
    }

    public function saveThemeContent(Request $request, $id)
    {
        $theme = Theme::findOrFail($id);
        $settings = $theme->settings ?? [];
        $settings['components'] = $request->components ?? [];
        $settings['globalStyles'] = $request->globalStyles ?? [];
        $theme->settings = $settings;
        $theme->save();
        
        return response()->json(['success' => true, 'message' => 'Theme saved!']);
    }
    /**
     * Preview a theme.
     */
    public function preview($id)
    {
        $theme = Theme::findOrFail($id);
        $themeSettings = $theme->settings ?? [];
        
        return view('admin.themes.preview', compact('theme', 'themeSettings'));
    }

	    /**
     * Update theme settings.
     */
    public function updateSettings(Request $request, $id)
    {
        $theme = Theme::findOrFail($id);
        $theme->settings = $request->settings;
        $theme->save();

        return response()->json([
            'success' => true,
            'message' => 'Theme settings updated!',
        ]);
    }

    /**
     * Recursively delete a directory.
     */
    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) return;
        
        $items = array_diff(scandir($dir), ['.', '..']);
        foreach ($items as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        
        rmdir($dir);
    }
}