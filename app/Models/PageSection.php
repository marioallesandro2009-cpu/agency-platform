<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'page', 'section_type', 'title', 'subtitle', 'content', 'image', 'is_visible', 'order',
    ];

    protected $casts = [
        'content' => 'array',
        'is_visible' => 'boolean',
    ];

    public function scopeForPage($query, string $page)
    {
        return $query->where('page', $page);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}