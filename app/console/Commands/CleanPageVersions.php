<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\PageVersion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanPageVersions extends Command
{
    protected $signature = 'cms:clean-versions {--keep=50 : Number of versions to keep per page}';
    protected $description = 'Clean up old page versions, keeping only the most recent N versions per page';
    
    public function handle()
    {
        $keep = (int) $this->option('keep');
        
        if ($keep < 1) {
            $this->error('Keep count must be at least 1');
            return 1;
        }
        
        $this->info("Cleaning page versions, keeping {$keep} most recent per page...");
        
        DB::transaction(function () use ($keep) {
            $pages = Page::all();
            $totalDeleted = 0;
            
            foreach ($pages as $page) {
                $versionCount = PageVersion::where('page_id', $page->id)->count();
                
                if ($versionCount > $keep) {
                    $toDelete = $versionCount - $keep;
                    
                    // Get IDs of versions to delete (oldest first)
                    $idsToDelete = PageVersion::where('page_id', $page->id)
                        ->orderBy('version_number', 'asc')
                        ->limit($toDelete)
                        ->pluck('id');
                    
                    PageVersion::whereIn('id', $idsToDelete)->delete();
                    
                    $totalDeleted += count($idsToDelete);
                    $this->line("Page ID {$page->id}: Deleted " . count($idsToDelete) . " old versions");
                }
            }
            
            $this->info("Total versions deleted: {$totalDeleted}");
        });
        
        return 0;
    }
}