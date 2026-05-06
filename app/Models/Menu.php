<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'slug', 'is_default', 'created_by'];
    
    protected $casts = [
        'is_default' => 'boolean',
    ];
    
    public function items()
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public static function getDefault()
    {
        return static::where('is_default', true)->first();
    }
}
=======
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'is_default',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the user who created this menu.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the menu items for this menu.
     */
    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'menu_id');
    }

    /**
     * Get the menu items for this menu ordered by sort_order.
     */
    public function itemsOrdered(): HasMany
    {
        return $this->items()->orderBy('sort_order', 'asc');
    }

    /**
     * Get only the top-level menu items (parent_id is null).
     */
    public function topLevelItems(): HasMany
    {
        return $this->items()
            ->whereNull('parent_id')
            ->orderBy('sort_order', 'asc');
    }
}
>>>>>>> b4934cb55f4fb0378ecc8f031cf3563c449771b5
