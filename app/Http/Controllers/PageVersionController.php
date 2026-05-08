<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PageVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageVersionController extends Controller
{
    // GET /admin/pages/{id}/versions
    public function index($id)
    {
        $page = Page::findOrFail($id);
        
        $versions = $page->versions()
            ->with('user')
            ->get(['id', 'version_number', 'change_note', 'saved_by', 'created_at']);
        
        return response()->json([
            'success' => true,
            'data' => $versions
        ]);
    }
    
    // GET /admin/pages/{id}/versions/{vid}
    public function show($id, $vid)
    {
        $page = Page::findOrFail($id);
        
        $version = $page->versions()
            ->where('id', $vid)
            ->firstOrFail();
        
        return response()->json([
            'success' => true,
            'data' => [
                'content' => $version->content,
                'version_number' => $version->version_number,
                'change_note' => $version->change_note,
                'created_at' => $version->created_at,
                'saved_by' => $version->user?->name
            ]
        ]);
    }
    
    // POST /admin/pages/{id}/versions/{vid}/restore
    public function restore($id, $vid)
    {
        $page = Page::findOrFail($id);
        
        $version = $page->versions()
            ->where('id', $vid)
            ->firstOrFail();
        
        // Current content save kar lo (backup)
        $this->createVersionSnapshot($page, 'Before restore from version #' . $version->version_number);
        
        // Restore old content
        $page->content = $version->content;
        $page->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Page restored successfully',
            'data' => $page->content
        ]);
    }
    
    // Helper method for creating snapshot
    private function createVersionSnapshot($page, $note = null)
    {
        $latestVersion = $page->versions()->max('version_number') ?? 0;
        
        PageVersion::create([
            'page_id' => $page->id,
            'content' => $page->content,
            'version_number' => $latestVersion + 1,
            'saved_by' => Auth::id(),
            'change_note' => $note ?? 'Auto-saved before restore'
        ]);
    }
}