<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $table = 'news';
    
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'media_url',
        'author',
        'is_highlight',
    ];

    protected $casts = [
        'title' => 'array',
        'excerpt' => 'array',
        'content' => 'array',
        'author' => 'array',
        'is_highlight' => 'boolean',
    ];
}