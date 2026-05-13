<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin.auth']);
    }

    
    // List all menus (for index page and dashboard widget)
    public function index(Request $request)
    {
        $menus = Menu::withCount('menuItems')->get();
        
        // If request is from dashboard widget
        if ($request->has('dashboard')) {
            return response()->json([
                'menus' => $menus->map(function ($menu) {
                    return [
                        'id' => $menu->id,
                        'name' => $menu->name,
                        'slug' => $menu->slug,
                        'is_default' => (bool) $menu->is_default,
                        'item_count' => $menu->items_count,
                        'created_at' => $menu->created_at,
                    ];
                }),
            ]);
        }
        
        return view('admin.menus.index', compact('menus'));
    }
    
    // Show form to create new menu
    public function create()
    {
        return view('admin.menus.create');
    }
    
    // Store new menu
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'is_default' => 'boolean',
        ]);
        
        $validated['slug'] = Str::slug($validated['name']);
        $validated['created_by'] = auth()->id();
        
        // If setting as default, unset others
        if ($request->boolean('is_default')) {
            Menu::where('is_default', true)->update(['is_default' => false]);
        }
        
        $menu = Menu::create($validated);
        
        return redirect()->route('admin.menus.edit', $menu)
            ->with('success', 'Menu created successfully!');
    }
    
    // Show form to edit menu (Nav Bar Builder)
    public function edit(Menu $menu)
    {
        $menu->load(['items' => function($query) {
            $query->orderBy('sort_order');
        }]);
        
        // Build tree structure
        $menuItems = $this->buildTree($menu->items);
        
        return view('admin.menus.edit', compact('menu', 'menuItems'));
    }
    
    // Update menu
    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'is_default' => 'boolean',
        ]);
        
        if ($request->boolean('is_default')) {
            Menu::where('is_default', true)->where('id', '!=', $menu->id)
                ->update(['is_default' => false]);
        }
        
        $menu->update($validated);
        
        // Update menu items if provided
        if ($request->has('items')) {
            $this->syncMenuItems($menu, json_decode($request->items, true) ?? []);
        }
        
        return redirect()->route('admin.menus.edit', $menu)
            ->with('success', 'Menu updated successfully!');
    }
    
    // Delete menu
    public function destroy(Menu $menu)
    {
        $menu->delete();
        return response()->json(['message' => 'Menu deleted successfully']);
    }
    
    // Set a menu as default
    public function setDefault(Menu $menu)
    {
        Menu::where('is_default', true)->update(['is_default' => false]);
        $menu->update(['is_default' => true]);
        
        if (request()->wantsJson()) {
            return response()->json(['message' => 'Default menu updated']);
        }
        
        return back()->with('success', 'Default menu updated');
    }
    
    // Build hierarchical tree from flat items
    private function buildTree($items, $parentId = null)
    {
        $tree = [];
        foreach ($items as $item) {
            if ($item->parent_id == $parentId) {
                $children = $this->buildTree($items, $item->id);
                if ($children) {
                    $item->children = $children;
                }
                $tree[] = $item;
            }
        }
        return $tree;
    }
    
    // Sync menu items from JSON structure
    private function syncMenuItems(Menu $menu, array $items, $parentId = null, &$sortOrder = 0)
    {
        $existingIds = [];
        
        foreach ($items as $itemData) {
            $sortOrder++;
            
            // Handle new page creation if needed
            $pageId = $itemData['page_id'] ?? null;
            if (empty($pageId) && !empty($itemData['new_page_name'])) {
                // Auto-create page logic (can be expanded)
                $pageId = null;
            }
            
            $menuItem = MenuItem::updateOrCreate(
                ['id' => $itemData['id'] ?? null],
                [
                    'menu_id' => $menu->id,
                    'parent_id' => $parentId,
                    'label' => $itemData['label'],
                    'url' => $itemData['url'] ?? null,
                    'page_id' => $pageId,
                    'sort_order' => $sortOrder,
                ]
            );
            
            $existingIds[] = $menuItem->id;
            
            // Process children recursively
            if (!empty($itemData['children'])) {
                $this->syncMenuItems($menu, $itemData['children'], $menuItem->id, $sortOrder);
            }
        }
        
        // Delete items that are no longer present
        MenuItem::where('menu_id', $menu->id)
            ->where('parent_id', $parentId)
            ->whereNotIn('id', $existingIds)
            ->delete();
    }
}