<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'post_status',
        'event_status',
    ];

    public function categories()
    {
        return $this->belongsToMany(PostCategory::class, 'category_posts', 'post_id', 'category_id');
    }

    public function videos()
    {
        return $this->hasMany(PostVideo::class);
    }

    public function images()
    {
        return $this->hasMany(PostImage::class);
    }
}
