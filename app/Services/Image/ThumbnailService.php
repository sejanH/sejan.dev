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
        $diskName = (str_starts_with($localRelativePath ?? '', 'blog/') || config('filesystems.media_disk') === 's3') && Storage::disk('s3')->exists($localRelativePath ?? '') ? 's3' : 'public';
        $disk = Storage::disk($diskName);

        // Determine destination thumbnail relative path
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

        // Check if thumbnail already exists
        if (!$force && $disk->exists($thumbRelativePath)) {
            return [
                'success' => true,
                'url' => $disk->url($thumbRelativePath),
                'path' => $thumbRelativePath,
                'width' => null,
                'height' => null,
                'error' => null,
            ];
        }

        try {
            // Load image source
            $imageSource = null;
            if ($localRelativePath && $disk->exists($localRelativePath)) {
                $imageSource = $disk->get($localRelativePath);
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

            $encodedThumb = match(strtolower($extension ?? 'jpg')) {
                'png' => (string) $img->toPng(),
                'webp' => (string) $img->toWebp(self::THUMB_QUALITY),
                'gif' => (string) $img->toGif(),
                default => (string) $img->toJpeg(self::THUMB_QUALITY),
            };

            $options = $diskName === 's3' ? [
                'visibility' => 'public',
                'CacheControl' => env('AWS_CACHE_CONTROL', 'public, max-age=31536000, immutable'),
            ] : 'public';

            $disk->put($thumbRelativePath, $encodedThumb, $options);

            return [
                'success' => true,
                'url' => $disk->url($thumbRelativePath),
                'path' => $thumbRelativePath,
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

        // 1. Check if the image is on Cloudflare R2 CDN or has a relative storage path
        $localRelativePath = $this->extractRelativeStoragePath($featuredImage);
        if ($localRelativePath) {
            $dir = dirname($localRelativePath);
            $filename = pathinfo($localRelativePath, PATHINFO_FILENAME);
            $extension = pathinfo($localRelativePath, PATHINFO_EXTENSION) ?: 'jpg';
            $thumbRelativePath = ($dir === '.' ? '' : $dir . '/') . $filename . '-300x300.' . $extension;

            // If it's on Cloudflare R2 CDN
            if (str_starts_with($localRelativePath, 'blog/') || str_contains($featuredImage, 'cdn.sejan.dev')) {
                $cdnBase = rtrim(config('filesystems.disks.s3.url', 'https://cdn.sejan.dev'), '/');
                return $cdnBase . '/' . ltrim($thumbRelativePath, '/');
            }

            // If it's on local public disk
            $publicDisk = Storage::disk('public');
            if ($publicDisk->exists($thumbRelativePath)) {
                return $publicDisk->url($thumbRelativePath);
            }
        }

        // 2. Check external / post-specific thumbnail path
        $publicDisk = Storage::disk('public');
        $postThumbPath = 'thumbnails/posts/post-' . $post->id . '-300x300.jpg';
        if ($publicDisk->exists($postThumbPath)) {
            return $publicDisk->url($postThumbPath);
        }

        // Check for webp / png variants
        foreach (['png', 'webp', 'jpeg'] as $ext) {
            $variantPath = 'thumbnails/posts/post-' . $post->id . '-300x300.' . $ext;
            if ($publicDisk->exists($variantPath)) {
                return $publicDisk->url($variantPath);
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
        $diskName = $media->disk ?: config('filesystems.media_disk', 'public');
        $disk = Storage::disk($diskName);

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

        if (!$force && $disk->exists($thumbRelativePath)) {
            return [
                'success' => true,
                'url' => $disk->url($thumbRelativePath),
                'path' => $thumbRelativePath,
                'width' => null,
                'height' => null,
                'error' => null,
            ];
        }

        try {
            $imageBytes = $disk->get($localPath);
            $img = Image::read($imageBytes);
            $img->scaleDown(width: self::THUMB_MAX_WIDTH, height: self::THUMB_MAX_HEIGHT);

            $encodedThumb = match(strtolower($extension)) {
                'png' => (string) $img->toPng(),
                'webp' => (string) $img->toWebp(self::THUMB_QUALITY),
                'gif' => (string) $img->toGif(),
                default => (string) $img->toJpeg(self::THUMB_QUALITY),
            };

            $options = $diskName === 's3' ? [
                'visibility' => 'public',
                'CacheControl' => env('AWS_CACHE_CONTROL', 'public, max-age=31536000, immutable'),
            ] : 'public';

            $disk->put($thumbRelativePath, $encodedThumb, $options);

            return [
                'success' => true,
                'url' => $disk->url($thumbRelativePath),
                'path' => $thumbRelativePath,
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
        $diskName = $media->disk ?: config('filesystems.media_disk', 'public');
        $dir = dirname($localPath);
        $filename = pathinfo($localPath, PATHINFO_FILENAME);
        $extension = pathinfo($localPath, PATHINFO_EXTENSION) ?: 'jpg';
        $thumbRelativePath = ($dir === '.' ? '' : $dir . '/') . $filename . '-300x300.' . $extension;

        if ($diskName === 's3' || str_starts_with($localPath, 'blog/')) {
            $cdnBase = rtrim(config('filesystems.disks.s3.url', 'https://cdn.sejan.dev'), '/');
            return $cdnBase . '/' . ltrim($thumbRelativePath, '/');
        }

        $disk = Storage::disk($diskName);
        if ($disk->exists($thumbRelativePath)) {
            return $disk->url($thumbRelativePath);
        }

        return $media->url;
    }

    /**
     * Extract relative path inside public or S3 storage disk from a URL or string.
     */
    public function extractRelativeStoragePath(string $urlOrPath): ?string
    {
        $clean = trim($urlOrPath);

        // e.g. "https://cdn.sejan.dev/blog/media/2026/08/xyz.jpg"
        if (str_contains($clean, 'cdn.sejan.dev/')) {
            $parts = explode('cdn.sejan.dev/', $clean, 2);
            return ltrim($parts[1], '/');
        }

        // e.g. "https://blog.sejan.dev/storage/media/2026/08/xyz.jpg"
        if (str_contains($clean, '/storage/')) {
            $parts = explode('/storage/', $clean, 2);
            return ltrim($parts[1], '/');
        }

        // e.g. "storage/media/2026/08/xyz.jpg"
        if (str_starts_with($clean, 'storage/')) {
            return substr($clean, 8);
        }

        // e.g. "media/2026/08/xyz.jpg" or "blog/media/2026/08/xyz.jpg"
        if (str_starts_with($clean, 'media/') || str_starts_with($clean, 'blog/')) {
            return $clean;
        }

        // If it starts with http/https but does NOT match our known domains, it's external
        if (str_starts_with($clean, 'http://') || str_starts_with($clean, 'https://')) {
            return null;
        }

        return ltrim($clean, '/');
    }
}
