<?php

namespace App\Console\Commands;

use App\Models\Media;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MigrateMediaToR2Command extends Command
{
    protected $signature = 'media:migrate-to-r2 {--delete-local : Delete local files after successful upload}';
    protected $description = 'Migrate all local media files and thumbnails to Cloudflare R2 under blog/media/ and update database URLs.';

    public function handle(): int
    {
        $this->info('Starting migration of local media files to Cloudflare R2 bucket (portfolio/blog/)...');

        $r2Disk = Storage::disk('s3');
        $localBasePath = storage_path('app/public/media');

        if (!File::isDirectory($localBasePath)) {
            $this->error("Local media directory does not exist: {$localBasePath}");
            return 1;
        }

        $files = File::allFiles($localBasePath);
        $totalFiles = count($files);
        $this->info("Found {$totalFiles} local files (including thumbnails).");

        $uploadedCount = 0;
        $failedCount = 0;
        $options = [
            'visibility' => 'public',
            'CacheControl' => env('AWS_CACHE_CONTROL', 'public, max-age=31536000, immutable'),
        ];

        $bar = $this->output->createProgressBar($totalFiles);
        $bar->start();

        foreach ($files as $file) {
            $relativePath = $file->getRelativePathname(); // e.g. "2026/08/xyz.jpg"
            $r2Path = 'blog/media/' . $relativePath;

            try {
                $contents = file_get_contents($file->getRealPath());
                $r2Disk->put($r2Path, $contents, $options);
                $uploadedCount++;
            } catch (\Throwable $e) {
                $this->error("\nFailed to upload {$relativePath}: " . $e->getMessage());
                $failedCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Uploaded {$uploadedCount} files to Cloudflare R2 ({$failedCount} failed).");

        // 2. Update Media records in database
        $this->info('Updating Media database records...');
        $mediaRecords = Media::where('disk', 'public')
            ->where('path', 'like', 'media/%')
            ->get();

        $updatedMediaCount = 0;
        foreach ($mediaRecords as $media) {
            $media->update([
                'disk' => 's3',
                'path' => 'blog/' . ltrim($media->path, '/'),
            ]);
            $updatedMediaCount++;
        }
        $this->info("Updated {$updatedMediaCount} Media records in database.");

        // 3. Update Posts table (featured_image and content)
        $this->info('Updating Post featured images and content...');
        $posts = Post::all();
        $updatedPostsCount = 0;

        foreach ($posts as $post) {
            $changed = false;
            $featuredImage = $post->featured_image;
            $content = $post->content;

            // Replace featured_image
            if (!empty($featuredImage)) {
                $newFeatured = $featuredImage;
                // e.g. "https://blog.sejan.dev/storage/media/2026/08/xyz.jpg" -> "https://cdn.sejan.dev/blog/media/2026/08/xyz.jpg"
                $newFeatured = preg_replace('#https?://[^/]+/storage/media/#', 'https://cdn.sejan.dev/blog/media/', $newFeatured);
                $newFeatured = preg_replace('#^/storage/media/#', 'https://cdn.sejan.dev/blog/media/', $newFeatured);
                $newFeatured = preg_replace('#^storage/media/#', 'https://cdn.sejan.dev/blog/media/', $newFeatured);

                if ($newFeatured !== $featuredImage) {
                    $post->featured_image = $newFeatured;
                    $changed = true;
                }
            }

            // Replace embedded images in post content
            if (!empty($content)) {
                $newContent = $content;
                $newContent = preg_replace('#https?://[^/]+/storage/media/#', 'https://cdn.sejan.dev/blog/media/', $newContent);
                $newContent = preg_replace('#/storage/media/#', 'https://cdn.sejan.dev/blog/media/', $newContent);
                $newContent = preg_replace('#storage/media/#', 'https://cdn.sejan.dev/blog/media/', $newContent);

                if ($newContent !== $content) {
                    $post->content = $newContent;
                    $changed = true;
                }
            }

            if ($changed) {
                $post->save();
                $updatedPostsCount++;
            }
        }
        $this->info("Updated {$updatedPostsCount} Posts with CDN image URLs.");

        $this->info('Migration to Cloudflare R2 completed successfully!');
        return 0;
    }
}
