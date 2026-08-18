<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Media;
use App\Models\Post;
use App\Models\Redirect;
use App\Models\Tag;
use App\Services\WordPress\WordPressDatabaseMigrator;
use Illuminate\Console\Command;

class WordPressMigrateCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'wp:migrate
                            {--test : Test WordPress database connection only}
                            {--fresh : Truncate posts, categories, tags, media, and comments before migration}
                            {--taxonomies : Ingest categories and tags only}
                            {--media : Ingest media and copy physical upload files only}
                            {--posts : Ingest posts, pages, and redirects only}
                            {--comments : Ingest comments only}
                            {--uploads= : Path to WordPress wp-content/uploads directory}';

    /**
     * The console command description.
     */
    protected $description = 'Ingest WordPress blog data into modern Laravel 12 database';

    public function handle(WordPressDatabaseMigrator $migrator): int
    {
        $this->info('====================================================');
        $this->info(' 🚀 WordPress to Laravel 12 Migration Engine');
        $this->info('====================================================');

        if ($this->option('test')) {
            $this->comment('Testing WordPress database connection...');
            $result = $migrator->testConnection();
            if ($result['success']) {
                $this->info('✅ ' . $result['message']);
                return self::SUCCESS;
            }

            $this->error('❌ ' . $result['message']);
            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            if ($this->confirm('Are you sure you want to truncate existing posts, categories, tags, media, and comments?', true)) {
                $this->warn('Truncating tables...');
                Comment::query()->delete();
                Post::query()->delete();
                Category::query()->delete();
                Tag::query()->delete();
                Media::query()->delete();
                Redirect::query()->delete();
                $this->info('Tables truncated.');
            }
        }

        $this->comment('Connecting to WordPress database...');

        try {
            $pdo = $migrator->getConnection();
            $prefix = config('wordpress.database.prefix', env('WP_TABLE_PREFIX', 'wp_'));
            $uploadsDir = $this->option('uploads') ?: config('wordpress.media.uploads_path', env('WP_UPLOADS_PATH', '/var/www/sejan.xyz/wp-content/uploads'));

            if ($this->option('taxonomies')) {
                $this->info('Ingesting categories and tags...');
                $stats = $migrator->migrateTaxonomies($pdo, $prefix);
                $this->table(['Type', 'Count'], [
                    ['Categories', $stats['categories']],
                    ['Tags', $stats['tags']],
                ]);
                return self::SUCCESS;
            }

            if ($this->option('media')) {
                $this->info("Ingesting media library and copying files from {$uploadsDir}...");
                $stats = $migrator->migrateMedia($pdo, $prefix, $uploadsDir);
                $this->table(['Type', 'Count'], [
                    ['Media Attachments', $stats['media']],
                ]);
                return self::SUCCESS;
            }

            if ($this->option('posts')) {
                $this->info('Ingesting posts, pages, and generating 301 redirects...');
                $stats = $migrator->migratePosts($pdo, $prefix);
                $this->table(['Type', 'Count'], [
                    ['Posts / Pages', $stats['posts']],
                    ['301 Redirects', $stats['redirects']],
                ]);
                return self::SUCCESS;
            }

            if ($this->option('comments')) {
                $this->info('Ingesting comments and building threaded trees...');
                $stats = $migrator->migrateComments($pdo, $prefix);
                $this->table(['Type', 'Count'], [
                    ['Comments', $stats['comments']],
                ]);
                return self::SUCCESS;
            }

            $this->info('Executing full migration pipeline...');
            $stats = $migrator->migrateAll(['uploads_path' => $uploadsDir]);

            $this->newLine();
            $this->info('✨ Full Migration completed successfully!');
            $this->table(['Resource', 'Migrated Count'], [
                ['Categories', $stats['categories']],
                ['Tags', $stats['tags']],
                ['Media Files', $stats['media']],
                ['Posts & Pages', $stats['posts']],
                ['Comments', $stats['comments']],
                ['301 SEO Redirects', $stats['redirects']],
            ]);

            if (!empty($stats['errors'])) {
                $this->warn('Encountered ' . count($stats['errors']) . ' non-fatal errors during migration:');
                foreach ($stats['errors'] as $err) {
                    $this->line('  - ' . $err);
                }
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Migration aborted: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
