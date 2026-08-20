<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $fillable = [
        'user_id',
        'filename',
        'original_name',
        'disk',
        'path',
        'mime_type',
        'size',
        'width',
        'height',
        'alt_text',
        'caption',
        'wp_attachment_id',
    ];

    protected $casts = [
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    protected $appends = [
        'url',
        'thumbnail_url',
        'formatted_size',
        'is_image',
        'created_at_human',
        'created_at_formatted',
    ];

    /**
     * Booted model hooks.
     */
    protected static function booted(): void
    {
        static::saved(function (Media $media) {
            if ($media->is_image && $media->mime_type !== 'image/svg+xml') {
                try {
                    app(\App\Services\Image\ThumbnailService::class)->generateMediaThumbnail($media);
                } catch (\Throwable $e) {
                    // Suppress or log silently without failing parent transaction
                }
            }
        });
    }

    /**
     * User / Author relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get direct public URL for the media asset.
     */
    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->path, 'http://') || str_starts_with($this->path, 'https://')) {
            return $this->path;
        }

        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * Get 300x300 thumbnail URL for the media asset.
     */
    public function getThumbnailUrlAttribute(): string
    {
        return app(\App\Services\Image\ThumbnailService::class)->getMediaThumbnailUrl($this);
    }

    /**
     * Human-readable file size accessor.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = (int) $this->size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }

        return $bytes . ' B';
    }

    /**
     * Check if asset is an image.
     */
    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'image/');
    }

    /**
     * Get human-friendly relative created at time.
     */
    public function getCreatedAtHumanAttribute(): string
    {
        return $this->created_at ? $this->created_at->diffForHumans() : '';
    }

    /**
     * Get formatted created at date.
     */
    public function getCreatedAtFormattedAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('M d, Y h:i A') : '';
    }

    /**
     * Scope for filtering images only.
     */
    public function scopeImages(Builder $query): Builder
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    /**
     * Scope for filtering non-image documents.
     */
    public function scopeDocuments(Builder $query): Builder
    {
        return $query->where('mime_type', 'not like', 'image/%');
    }

    /**
     * Scope for searching media files by name or alt text.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('original_name', 'like', "%{$term}%")
              ->orWhere('filename', 'like', "%{$term}%")
              ->orWhere('alt_text', 'like', "%{$term}%")
              ->orWhere('caption', 'like', "%{$term}%");
        });
    }
}
