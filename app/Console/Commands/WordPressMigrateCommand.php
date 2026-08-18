<?php

namespace App\Console\Commands;

use App\Models\Category;
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
                            {--fresh : Truncate posts, categories, and tags before migration}
                            {--taxonomies : Ingest categories and tags only}
                            {--posts : Ingest posts, pages, and redirects only}';

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
            if ($this->confirm('Are you sure you want to truncate existing posts, categories, and tags?', true)) {
                $this->warn('Truncating tables...');
                Post::query()->delete();
                Category::query()->delete();
                Tag::query()->delete();
                Redirect::query()->delete();
                $this->info('Tables truncated.');
            }
        }

        $this->comment('Connecting to WordPress database...');

        try {
            $pdo = $migrator->getConnection();
            $prefix = config('wordpress.database.prefix', env('WP_TABLE_PREFIX', 'wp_'));

            if ($this->option('taxonomies')) {
                $this->info('Ingesting categories and tags...');
                $stats = $migrator->migrateTaxonomies($pdo, $prefix);
                $this->table(['Type', 'Count'], [
                    ['Categories', $stats['categories']],
                    ['Tags', $stats['tags']],
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

            $this->info('Executing full migration pipeline...');
            $stats = $migrator->migrateAll();

            $this->newLine();
            $this->info('✨ Migration completed successfully!');
            $this->table(['Resource', 'Migrated Count'], [
                ['Categories', $stats['categories']],
                ['Tags', $stats['tags']],
                ['Posts & Pages', $stats['posts']],
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
