<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'source_name',
        'source_identifier',
        'title',
        'description',
        'content',
        'author',
        'category',
        'language',
        'article_url',
        'image_url',
        'published_at',
        'fetched_at',
        'tags',
    ];

    protected $casts = [
    // 'tags' => 'array',
    'published_at' => 'datetime',
    'fetched_at' => 'datetime',
    ];
    
}
