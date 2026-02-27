<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'is_published',
        'seo_title',
        'seo_description',
    ];

    protected $casts = [
        'content' => 'array',
        'is_published' => 'boolean',
    ];
}
