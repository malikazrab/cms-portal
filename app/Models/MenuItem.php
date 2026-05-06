<?php

namespace App\Models;

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