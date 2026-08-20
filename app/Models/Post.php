<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'status',
        'published_at',
        'is_featured',
        'reading_time',
        'meta_title',
        'meta_description',
        'canonical_url',
        'wp_id',
        'wp_post_type',
        'views_count',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'reading_time' => 'integer',
        'views_count' => 'integer',
    ];

    protected $appends = [
        'thumbnail_url',
    ];

    /**
     * Boot model events for automatic slug and reading time calculation.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saving(function ($post) {
            // Clean HTML entities (such as &amp; -> &) in title, excerpt, and SEO fields
            if (!empty($post->title)) {
                $post->title = html_entity_decode(html_entity_decode($post->title, ENT_QUOTES | ENT_HTML5, 'UTF-8'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }

            if (!empty($post->excerpt)) {
                $post->excerpt = html_entity_decode(html_entity_decode($post->excerpt, ENT_QUOTES | ENT_HTML5, 'UTF-8'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }

            if (!empty($post->meta_title)) {
                $post->meta_title = html_entity_decode(html_entity_decode($post->meta_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }

            if (!empty($post->meta_description)) {
                $post->meta_description = html_entity_decode(html_entity_decode($post->meta_description, ENT_QUOTES | ENT_HTML5, 'UTF-8'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }

            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }

            if (empty($post->reading_time) && !empty($post->content)) {
                $wordCount = str_word_count(strip_tags($post->content));
                $post->reading_time = max(1, (int) ceil($wordCount / 200));
            }

            if (empty($post->excerpt) && !empty($post->content)) {
                $post->excerpt = Str::limit(strip_tags($post->content), 180);
            }
        });

        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('seo_sitemap_xml');
            \Illuminate\Support\Facades\Cache::forget('seo_rss_feed_xml');
            \Illuminate\Support\Facades\Cache::forget('seo_atom_feed_xml');
            \Illuminate\Support\Facades\Cache::forget('admin_dashboard_analytics');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('seo_sitemap_xml');
            \Illuminate\Support\Facades\Cache::forget('seo_rss_feed_xml');
            \Illuminate\Support\Facades\Cache::forget('seo_atom_feed_xml');
            \Illuminate\Support\Facades\Cache::forget('admin_dashboard_analytics');
        });
    }

    /**
     * Author relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Categories relationship.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_post');
    }

    /**
     * Tags relationship.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    /**
     * Comments relationship.
     */
    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Approved root comments relationship for public display.
     */
    public function approvedRootComments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class)
            ->where('status', 'approved')
            ->whereNull('parent_id')
            ->latest();
    }

    /**
     * Scope query to only published posts.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope query to only featured posts.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope search query for posts.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('excerpt', 'like', "%{$term}%")
              ->orWhere('content', 'like', "%{$term}%");
        });
    }

    /**
     * Get clean title with decoded HTML entities (&amp; -> &).
     */
    public function getTitleAttribute($value): string
    {
        return html_entity_decode($value ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Get clean excerpt with decoded HTML entities (&amp; -> &).
     */
    public function getExcerptAttribute($value): ?string
    {
        return $value !== null ? html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8') : null;
    }

    /**
     * Helper to get SEO Title.
     */
    public function getSeoTitleAttribute(): string
    {
        $raw = $this->meta_title ?: $this->title . ' — sejan.dev';
        return html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Helper to get SEO Description.
     */
    public function getSeoDescriptionAttribute(): string
    {
        $raw = $this->meta_description ?: ($this->excerpt ?: Str::limit(strip_tags($this->content), 160));
        return html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Get 300x300 thumbnail URL for post cards (falls back to featured_image if no thumbnail exists).
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if (empty($this->featured_image)) {
            return null;
        }

        return app(\App\Services\Image\ThumbnailService::class)->getPostThumbnailUrl($this);
    }

    /**
     * Alias for thumbnail URL.
     */
    public function getFeaturedImageThumbnailAttribute(): ?string
    {
        return $this->getThumbnailUrlAttribute();
    }
}
