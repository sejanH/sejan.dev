<?php

namespace App\Services\Image;

use App\Models\Media;
use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ThumbnailService
{
    public const THUMB_MAX_WIDTH = 300;
    public const THUMB_MAX_HEIGHT = 300;
    public const THUMB_QUALITY = 85;

    /**
     * Generate 300x300 thumbnail for a given post while preserving aspect ratio.
     *
     * @param Post $post
     * @param bool $force Overwrite existing thumbnail if true
     * @return array{success: bool, url: ?string, path: ?string, width: ?int, height: ?int, error: ?string}
     */
    public function generatePostThumbnail(Post $post, bool $force = false): array
    {
        $featuredImage = $post->featured_image;
        if (empty($featuredImage)) {
            return [
                'success' => false,
                'url' => null,
                'path' => null,
                'width' => null,
                'height' => null,
                'error' => 'Post has no featured image.',
            ];
        }

        $localRelativePath = $this->extractRelativeStoragePath($featuredImage);

        // Determine destination thumbnail relative path in 'public' disk
        if ($localRelativePath) {
            $dir = dirname($localRelativePath);
            $filename = pathinfo($localRelativePath, PATHINFO_FILENAME);
            $extension = pathinfo($localRelativePath, PATHINFO_EXTENSION) ?: 'jpg';
            $thumbRelativePath = ($dir === '.' ? '' : $dir . '/') . $filename . '-300x300.' . $extension;
        } else {
            // External URL (e.g. Unsplash) - store in public/thumbnails/posts/
            $ext = pathinfo(parse_url($featuredImage, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'jpg';
            $thumbRelativePath = 'thumbnails/posts/post-' . $post->id . '-300x300.' . $ext;
        }

        $disk = Storage::disk('public');
        $fullThumbPath = $disk->path($thumbRelativePath);

        // Check if thumbnail already exists
        if (!$force && $disk->exists($thumbRelativePath)) {
            $dimensions = @getimagesize($fullThumbPath);
            return [
                'success' => true,
                'url' => $disk->url($thumbRelativePath),
                'path' => $fullThumbPath,
                'width' => $dimensions[0] ?? null,
                'height' => $dimensions[1] ?? null,
                'error' => null,
            ];
        }

        try {
            // Load image source
            $imageSource = null;
            if ($localRelativePath && $disk->exists($localRelativePath)) {
                $imageSource = $disk->path($localRelativePath);
            } else {
                // Fetch image content from URL
                $response = Http::timeout(15)->get($featuredImage);
                if (!$response->successful()) {
                    return [
                        'success' => false,
                        'url' => null,
                        'path' => null,
                        'width' => null,
                        'height' => null,
                        'error' => 'Failed to download external image: HTTP ' . $response->status(),
                    ];
                }
                $imageSource = $response->body();
            }

            // Read with Intervention Image
            $img = Image::read($imageSource);

            // Scale down keeping the aspect ratio intact within 300x300 bounding box
            $img->scaleDown(width: self::THUMB_MAX_WIDTH, height: self::THUMB_MAX_HEIGHT);

            $thumbWidth = $img->width();
            $thumbHeight = $img->height();

            // Ensure directory exists on disk
            $disk->makeDirectory(dirname($thumbRelativePath));

            // Save with optimal quality
            $img->save($fullThumbPath, quality: self::THUMB_QUALITY);

            return [
                'success' => true,
                'url' => $disk->url($thumbRelativePath),
                'path' => $fullThumbPath,
                'width' => $thumbWidth,
                'height' => $thumbHeight,
                'error' => null,
            ];
        } catch (\Throwable $e) {
            Log::error("Thumbnail generation failed for Post #{$post->id}: " . $e->getMessage());
            return [
                'success' => false,
                'url' => null,
                'path' => null,
                'width' => null,
                'height' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get the 300x300 thumbnail URL for a post, falling back to original featured image.
     */
    public function getPostThumbnailUrl(Post $post): ?string
    {
        $featuredImage = $post->featured_image;
        if (empty($featuredImage)) {
            return null;
        }

        $disk = Storage::disk('public');

        // 1. Check local media -300x300 naming convention
        $localRelativePath = $this->extractRelativeStoragePath($featuredImage);
        if ($localRelativePath) {
            $dir = dirname($localRelativePath);
            $filename = pathinfo($localRelativePath, PATHINFO_FILENAME);
            $extension = pathinfo($localRelativePath, PATHINFO_EXTENSION) ?: 'jpg';
            $thumbRelativePath = ($dir === '.' ? '' : $dir . '/') . $filename . '-300x300.' . $extension;

            if ($disk->exists($thumbRelativePath)) {
                return $disk->url($thumbRelativePath);
            }
        }

        // 2. Check external / post-specific thumbnail path
        $postThumbPath = 'thumbnails/posts/post-' . $post->id . '-300x300.jpg';
        if ($disk->exists($postThumbPath)) {
            return $disk->url($postThumbPath);
        }

        // Check for webp / png variants
        foreach (['png', 'webp', 'jpeg'] as $ext) {
            $variantPath = 'thumbnails/posts/post-' . $post->id . '-300x300.' . $ext;
            if ($disk->exists($variantPath)) {
                return $disk->url($variantPath);
            }
        }

        // 3. Fallback to full size featured image
        return $featuredImage;
    }

    /**
     * Generate 300x300 thumbnail for an uploaded Media model while preserving aspect ratio.
     *
     * @param Media $media
     * @param bool $force Overwrite existing thumbnail if true
     * @return array{success: bool, url: ?string, path: ?string, width: ?int, height: ?int, error: ?string}
     */
    public function generateMediaThumbnail(Media $media, bool $force = false): array
    {
        if (!$media->is_image || $media->mime_type === 'image/svg+xml') {
            return [
                'success' => false,
                'url' => $media->url,
                'path' => null,
                'width' => null,
                'height' => null,
                'error' => 'Asset is not a scalable image format.',
            ];
        }

        $localPath = $media->path;
        $disk = Storage::disk($media->disk ?: 'public');

        if (!$disk->exists($localPath)) {
            return [
                'success' => false,
                'url' => null,
                'path' => null,
                'width' => null,
                'height' => null,
                'error' => 'Original media file does not exist on disk.',
            ];
        }

        $dir = dirname($localPath);
        $filename = pathinfo($localPath, PATHINFO_FILENAME);
        $extension = pathinfo($localPath, PATHINFO_EXTENSION) ?: 'jpg';
        $thumbRelativePath = ($dir === '.' ? '' : $dir . '/') . $filename . '-300x300.' . $extension;
        $fullThumbPath = $disk->path($thumbRelativePath);

        if (!$force && $disk->exists($thumbRelativePath)) {
            $dimensions = @getimagesize($fullThumbPath);
            return [
                'success' => true,
                'url' => $disk->url($thumbRelativePath),
                'path' => $fullThumbPath,
                'width' => $dimensions[0] ?? null,
                'height' => $dimensions[1] ?? null,
                'error' => null,
            ];
        }

        try {
            $img = Image::read($disk->path($localPath));
            $img->scaleDown(width: self::THUMB_MAX_WIDTH, height: self::THUMB_MAX_HEIGHT);

            $disk->makeDirectory(dirname($thumbRelativePath));
            $img->save($fullThumbPath, quality: self::THUMB_QUALITY);

            return [
                'success' => true,
                'url' => $disk->url($thumbRelativePath),
                'path' => $fullThumbPath,
                'width' => $img->width(),
                'height' => $img->height(),
                'error' => null,
            ];
        } catch (\Throwable $e) {
            Log::error("Thumbnail generation failed for Media #{$media->id}: " . $e->getMessage());
            return [
                'success' => false,
                'url' => null,
                'path' => null,
                'width' => null,
                'height' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get the 300x300 thumbnail URL for a Media item, falling back to original URL.
     */
    public function getMediaThumbnailUrl(Media $media): string
    {
        if (!$media->is_image || $media->mime_type === 'image/svg+xml') {
            return $media->url;
        }

        $localPath = $media->path;
        $disk = Storage::disk($media->disk ?: 'public');

        $dir = dirname($localPath);
        $filename = pathinfo($localPath, PATHINFO_FILENAME);
        $extension = pathinfo($localPath, PATHINFO_EXTENSION) ?: 'jpg';
        $thumbRelativePath = ($dir === '.' ? '' : $dir . '/') . $filename . '-300x300.' . $extension;

        if ($disk->exists($thumbRelativePath)) {
            return $disk->url($thumbRelativePath);
        }

        return $media->url;
    }

    /**
     * Extract relative path inside public storage disk from a URL or string.
     */
    public function extractRelativeStoragePath(string $urlOrPath): ?string
    {
        $clean = trim($urlOrPath);

        // e.g. "https://blog.sejan.dev/storage/media/2026/08/xyz.jpg"
        if (str_contains($clean, '/storage/')) {
            $parts = explode('/storage/', $clean, 2);
            return ltrim($parts[1], '/');
        }

        // e.g. "storage/media/2026/08/xyz.jpg"
        if (str_starts_with($clean, 'storage/')) {
            return substr($clean, 8);
        }

        // e.g. "media/2026/08/xyz.jpg"
        if (str_starts_with($clean, 'media/')) {
            return $clean;
        }

        // If it starts with http/https but does NOT have /storage/, it's external
        if (str_starts_with($clean, 'http://') || str_starts_with($clean, 'https://')) {
            return null;
        }

        return ltrim($clean, '/');
    }
}
