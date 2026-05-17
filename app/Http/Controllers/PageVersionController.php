<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PageVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageVersionController extends Controller
{
    // GET /admin/pages/{id}/versions (HTML view or API)
    public function versions($id)
    {
        $page = Page::findOrFail($id);
        $this->authorize('view', $page);
        
        // Check if this is an AJAX request
        if (request()->expectsJson() || request()->header('Accept') === 'application/json') {
            // Return JSON for API calls
            $versions = $page->versions()
                ->with('user')
                ->get(['id', 'version_number', 'change_note', 'saved_by', 'created_at']);
            
            return response()->json([
                'success' => true,
                'data' => $versions
            ]);
        }
        
        // Return HTML view for regular requests
        return view('admin.pages.versions', compact('page'));
    }
    
    // GET /admin/pages/{id}/versions/{vid}
    public function show($id, $vid)
    {
        $page = Page::findOrFail($id);
        $this->authorize('view', $page);
        
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
        $this->authorize('update', $page);
        
        $version = $page->versions()
            ->where('id', $vid)
            ->firstOrFail();
        
        // Current content save kar lo (backup)
        $this->createVersionSnapshot($page, 'Before restore from version #' . $version->version_number);
        
        $restoredContent = is_array($version->content) ? $version->content : [];

        $page->update([
            'title' => $restoredContent['title'] ?? $page->title,
            'slug' => $restoredContent['slug'] ?? $page->slug,
            'content' => $restoredContent['content'] ?? $page->content,
            'status' => $restoredContent['status'] ?? $page->status,
            'template' => $restoredContent['template'] ?? $page->template,
            'meta_title' => $restoredContent['meta_title'] ?? $page->meta_title,
            'meta_description' => $restoredContent['meta_description'] ?? $page->meta_description,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Page restored successfully',
            'data' => $restoredContent
        ]);
    }
    
    // Helper method for creating snapshot
    private function createVersionSnapshot($page, $note = null)
    {
        $latestVersion = $page->versions()->max('version_number') ?? 0;
        
        PageVersion::create([
            'page_id' => $page->id,
            'content' => [
                'title' => $page->title,
                'slug' => $page->slug,
                'content' => $page->content,
                'status' => $page->status,
                'template' => $page->template,
                'meta_title' => $page->meta_title,
                'meta_description' => $page->meta_description,
            ],
            'version_number' => $latestVersion + 1,
            'saved_by' => Auth::id(),
            'change_note' => $note ?? 'Auto-saved before restore'
        ]);
    }
}
