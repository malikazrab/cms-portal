<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
    class Post extends Model
{
    protected $fillable = ['user_id', 'title', 'slug', 'content', 'excerpt', 'featured_image', 'status', 'category_id', 'meta_title', 'meta_description', 'published_at'];

    public function user() { return $this->belongsTo(User::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function tags() { return $this->belongsToMany(Tag::class); }
}

