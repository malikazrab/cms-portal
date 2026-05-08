<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeaderTemplate extends Model
{
    protected $table = 'header_footer_templates';

    protected $fillable = [
        'name',
        'content',
        'is_default',
        'created_by',
        'type',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    // Automatically scope to type='header'
    protected static function booted()
    {
        static::addGlobalScope('header', function ($query) {
            $query->where('type', 'header');
        });

        static::creating(function ($model) {
            $model->type = 'header';
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function getDefault()
    {
        return self::where('is_default', true)->first();
    }

    public function setAsDefault()
    {
        self::where('type', 'header')->update(['is_default' => false]);
        $this->is_default = true;
        $this->save();
    }
}