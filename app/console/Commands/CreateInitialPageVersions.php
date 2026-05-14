<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\PageVersion;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('pages:create-initial-versions')]
#[Description('Create initial version 1.0 for existing pages that don\'t have versions')]
class CreateInitialPageVersions extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pages = Page::all();
        $created = 0;

        foreach ($pages as $page) {
            $existingVersions = $page->versions()->count();
            
            if ($existingVersions == 0) {
                PageVersion::create([
                    'page_id' => $page->id,
                    'version_number' => 1.0,
                    'content' => [
                        'title' => $page->title,
                        'slug' => $page->slug,
                        'content' => $page->content,
                        'status' => $page->status,
                        'template' => $page->template,
                        'meta_title' => $page->meta_title,
                        'meta_description' => $page->meta_description,
                    ],
                    'change_note' => 'Initial page creation',
                    'saved_by' => $page->user_id,
                ]);
                
                $created++;
                $this->info("Created version 1.0 for page: {$page->title}");
            }
        }

        $this->info("Created {$created} initial versions for existing pages.");
        return Command::SUCCESS;
    }
}
