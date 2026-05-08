<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageVersion extends Model
{
    protected $table = 'page_versions';
    
    protected $fillable = [
        'page_id', 'version_number', 'content', 'change_note', 'saved_by'
    ];
    
    protected $casts = [
        'content' => 'array',
    ];
    
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'saved_by');
    }
}