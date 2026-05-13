<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('order', 'asc');
    }

    public function topLevelItems()
    {
        return $this->menuItems()
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->orderBy('order', 'asc');
            }])
            ->orderBy('order', 'asc');
    }

    public function render(): string
    {
        $items = $this->topLevelItems()->get();
        
        if ($items->isEmpty()) {
            return '';
        }

        return $this->renderMenuItems($items);
    }

    protected function renderMenuItems($items, $depth = 0): string
    {
        if ($items->isEmpty()) {
            return '';
        }

        $html = '<ul class="' . ($depth === 0 ? 'space-y-2' : 'ml-4 mt-2 space-y-1') . '">';
        
        foreach ($items as $item) {
            $hasChildren = $item->children->isNotEmpty();
            $html .= '<li>';
            $html .= '<a href="' . e($item->url ?? '#') . '" class="block px-4 py-2 hover:bg-gray-100 rounded">';
            $html .= e($item->label ?? $item->title ?? 'Menu Item');
            $html .= '</a>';
            
            if ($hasChildren) {
                $html .= $this->renderMenuItems($item->children, $depth + 1);
            }
            
            $html .= '</li>';
        }
        
        $html .= '</ul>';
        
        return $html;
    }
}