<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['user_id', 'title', 'slug', 'content', 'status', 'template', 'meta_title', 'meta_description'];
public function user() { return $this->belongsTo(User::class); }
}
