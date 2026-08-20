<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'wp_id',
    ];

    /**
     * Boot model events to automatically decode entities and generate slug if omitted.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saving(function ($category) {
            if (!empty($category->name)) {
                $category->name = html_entity_decode(html_entity_decode($category->name, ENT_QUOTES | ENT_HTML5, 'UTF-8'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }

            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Get clean category name with decoded entities (&amp; -> &).
     */
    public function getNameAttribute($value): string
    {
        return html_entity_decode($value ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Parent category relationship.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Child subcategories relationship.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Posts attached to this category.
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'category_post');
    }

    /**
     * Published posts attached to this category.
     */
    public function publishedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'category_post')
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
