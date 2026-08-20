<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\Image\ThumbnailService;
use Illuminate\Console\Command;

class GeneratePostThumbnailsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'posts:generate-thumbnails
                            {--force : Regenerate thumbnails even if they already exist}
                            {--post= : Process only a specific post by ID or slug}
                            {--all-media : Also generate thumbnails for all images in the Media Library}';

    /**
     * The console command description.
     */
    protected $description = 'Generate 300x300 aspect-ratio-preserved thumbnails for post featured images and media uploads';

    /**
     * Additional command aliases.
     */
    protected $aliases = [
        'media:generate-thumbnails',
        'thumbnails:generate',
    ];

    public function handle(ThumbnailService $thumbnailService): int
    {
        $this->info('====================================================');
        $this->info(' 🖼️  Media & Post 300x300 Thumbnail Generator');
        $this->info('====================================================');

        $force = (bool) $this->option('force');
        $allMedia = (bool) $this->option('all-media');
        $postIdOrSlug = $this->option('post');

        if ($allMedia) {
            $mediaItems = \App\Models\Media::query()->images()->get();
            $this->comment("Processing {$mediaItems->count()} Media Library image(s)...");
            $mediaBar = $this->output->createProgressBar($mediaItems->count());
            $mediaBar->start();

            $mediaSuccess = 0;
            foreach ($mediaItems as $media) {
                $res = $thumbnailService->generateMediaThumbnail($media, $force);
                if ($res['success']) {
                    $mediaSuccess++;
                }
                $mediaBar->advance();
            }
            $mediaBar->finish();
            $this->newLine(2);
            $this->info("Generated thumbnails for {$mediaSuccess}/{$mediaItems->count()} Media library files.");
            $this->newLine();
        }

        $query = Post::query()->whereNotNull('featured_image')->where('featured_image', '!=', '');

        if (!empty($postIdOrSlug)) {
            $query->where(function ($q) use ($postIdOrSlug) {
                if (is_numeric($postIdOrSlug)) {
                    $q->where('id', (int) $postIdOrSlug);
                } else {
                    $q->where('slug', $postIdOrSlug);
                }
            });
        }

        $posts = $query->get();

        if ($posts->isEmpty()) {
            $this->warn('No posts with featured images found.');
            return self::SUCCESS;
        }

        $this->comment("Found {$posts->count()} post(s) to process. (Max Bounding Box: 300x300, Ratio: Intact)");
        if ($force) {
            $this->warn('Force mode active: Existing thumbnails will be overwritten.');
        }

        $bar = $this->output->createProgressBar($posts->count());
        $bar->start();

        $results = [];
        $successCount = 0;
        $failedCount = 0;

        foreach ($posts as $post) {
            $result = $thumbnailService->generatePostThumbnail($post, $force);

            if ($result['success']) {
                $successCount++;
                $results[] = [
                    'id' => $post->id,
                    'title' => \Illuminate\Support\Str::limit($post->title, 35),
                    'dimensions' => $result['width'] && $result['height'] ? "{$result['width']} × {$result['height']} px" : '300x300',
                    'status' => '✅ OK',
                ];
            } else {
                $failedCount++;
                $results[] = [
                    'id' => $post->id,
                    'title' => \Illuminate\Support\Str::limit($post->title, 35),
                    'dimensions' => '—',
                    'status' => '❌ ' . \Illuminate\Support\Str::limit($result['error'] ?? 'Failed', 30),
                ];
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(['Post ID', 'Post Title', 'Thumbnail Size', 'Status'], $results);

        $this->newLine();
        $this->info("✨ Finished! Processed {$posts->count()} post(s): {$successCount} successful, {$failedCount} failed.");

        return $failedCount === 0 ? self::SUCCESS : self::FAILURE;
    }
}
