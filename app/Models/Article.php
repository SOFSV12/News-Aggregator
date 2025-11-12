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
        'url',
        'image_url',
        'published_at',
        'fetched_at',
        'tags',
    ];
}
