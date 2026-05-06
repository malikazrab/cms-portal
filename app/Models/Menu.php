<?php

namespace App\Models;

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