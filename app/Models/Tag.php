<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'wp_id',
    ];

    /**
     * Boot model events to automatically decode entities and generate slug.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saving(function ($tag) {
            if (!empty($tag->name)) {
                $tag->name = html_entity_decode(html_entity_decode($tag->name, ENT_QUOTES | ENT_HTML5, 'UTF-8'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }

            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    /**
     * Get clean tag name with decoded entities (&amp; -> &).
     */
    public function getNameAttribute($value): string
    {
        return html_entity_decode($value ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Posts attached to this tag.
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tag');
    }

    /**
     * Published posts attached to this tag.
     */
    public function publishedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tag')
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
