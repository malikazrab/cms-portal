<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;
    
    protected $fillable = ['menu_id', 'parent_id', 'label', 'url', 'page_id', 'sort_order'];
    
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
    
    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }
    
    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort_order');
    }
    
    public function page()
    {
        return $this->belongsTo(Page::class);
    }
    
    public function getUrlAttribute()
    {
        if ($this->page_id) {
            $page = $this->page;
            if ($page) {
                return '/pages/' . $page->slug;
            }
        }
        return $this->attributes['url'] ?? '#';
    }
}
=======
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'menu_id',
        'parent_id',
        'label',
        'url',
        'page_id',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Get the menu this item belongs to.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Get the parent menu item (for hierarchical structure).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    /**
     * Get the child menu items.
     */
    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')
            ->orderBy('sort_order', 'asc');
    }

    /**
     * Get all descendants (children, grandchildren, etc.).
     */
    public function descendants(): HasMany
    {
        return $this->children();
    }

    /**
     * Get the page this menu item links to.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get the URL for this menu item.
     * Returns page URL if page_id is set, otherwise returns the custom url.
     */
    public function getResolvedUrlAttribute(): ?string
    {
        if ($this->page_id && $this->page) {
            return route('pages.show', $this->page->slug);
        }

        return $this->url;
    }

    /**
     * Check if this is a root level item (has no parent).
     */
    public function isRootItem(): bool
    {
        return $this->parent_id === null;
    }

    /**
     * Check if this item has children.
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Get the depth level of this item in the hierarchy.
     */
    public function getDepthLevel(): int
    {
        $depth = 0;
        $current = $this;

        while ($current->parent_id !== null) {
            $depth++;
            $current = $current->parent;
        }

        return $depth;
    }
}
>>>>>>> b4934cb55f4fb0378ecc8f031cf3563c449771b5
