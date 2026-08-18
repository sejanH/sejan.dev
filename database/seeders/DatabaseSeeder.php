<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Roles and Permissions
        $this->call(RolesAndPermissionsSeeder::class);

        $adminEmail = env('ADMIN_EMAIL', 'admin@sejan.dev');
        $adminPassword = env('ADMIN_PASSWORD', 'password');
        $adminName = env('ADMIN_NAME', 'Sejan (Admin)');

        $admin = User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName,
                'password' => bcrypt($adminPassword),
                'is_admin' => true,
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['admin']);

        // Seed Categories
        $catLaravel = \App\Models\Category::firstOrCreate(['slug' => 'laravel'], ['name' => 'Laravel 12', 'description' => 'Modern PHP & Laravel framework techniques, features, and optimizations.']);
        $catArchitecture = \App\Models\Category::firstOrCreate(['slug' => 'architecture'], ['name' => 'System Architecture', 'description' => 'Scalable system designs, database schemas, and microservices.']);
        $catMigration = \App\Models\Category::firstOrCreate(['slug' => 'migrations'], ['name' => 'WordPress Migration', 'description' => 'Data pipeline ingestion, asset extraction, and SEO preservation.']);
        $catDevops = \App\Models\Category::firstOrCreate(['slug' => 'devops'], ['name' => 'DevOps & Cloud', 'description' => 'Deployment pipelines, Docker, Redis, and high availability.']);

        // Seed Tags
        $tagPhp = \App\Models\Tag::firstOrCreate(['slug' => 'php82'], ['name' => 'PHP 8.2+']);
        $tagSeo = \App\Models\Tag::firstOrCreate(['slug' => 'seo-301'], ['name' => 'SEO & 301']);
        $tagPerformance = \App\Models\Tag::firstOrCreate(['slug' => 'performance'], ['name' => 'Performance']);
        $tagTailwind = \App\Models\Tag::firstOrCreate(['slug' => 'tailwindcss'], ['name' => 'TailwindCSS']);

        // Seed Sample Posts
        $post1 = \App\Models\Post::updateOrCreate(
            ['slug' => 'migrating-from-wordpress-to-laravel-12-complete-guide'],
            [
                'user_id' => $admin->id,
                'title' => 'Migrating from WordPress to Laravel 12: The Complete Architectural Blueprint',
                'excerpt' => 'How we transitioned an enterprise WordPress blog into a hyper-fast, clean Laravel 12 application while preserving 100% of SEO rankings and historical permalinks.',
                'content' => '<h2>Why We Moved Away from WordPress</h2><p>While WordPress has served the web for decades, modern web applications often hit friction points around plugin bloat, sluggish database schemas, and security patch overhead. By migrating to <strong>Laravel 12</strong>, we unlock native Eloquent ORM performance, structured data schemas, and modern Blade/Tailwind frontend rendering.</p><h2>The Migration Pipeline</h2><p>Our migration engine processes legacy data in discrete, deterministic stages:</p><ul><li><strong>Taxonomy Mapping:</strong> Categories and tags are ingested while preserving parent-child hierarchy.</li><li><strong>Content Transformation:</strong> Gutenberg comment blocks (<code>&lt;!-- wp:... --&gt;</code>) and legacy shortcodes are parsed into clean HTML.</li><li><strong>Media Optimization:</strong> Embedded images are ingested into Laravel storage with WebP compression.</li><li><strong>SEO 301 Permanent Redirects:</strong> Historical permalink patterns (e.g. <code>/%year%/%month%/%slug%/</code>) are automatically mapped to maintain zero link rot.</li></ul><blockquote>Zero downtime and zero broken links were our non-negotiable requirements during the migration.</blockquote><h2>Key Takeaways</h2><p>The result is a sub-50ms TTFB response time, zero dependency conflicts, and a joy to develop with using Laravel 12 and Vite.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=1200&q=80',
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'is_featured' => true,
                'reading_time' => 5,
                'meta_title' => 'Migrating from WordPress to Laravel 12: The Complete Architectural Blueprint',
                'meta_description' => 'Learn how to migrate an existing WordPress blog to Laravel 12 with full SEO permalink preservation and clean architecture.',
                'views_count' => 342,
            ]
        );
        $post1->categories()->sync([$catLaravel->id, $catMigration->id]);
        $post1->tags()->sync([$tagPhp->id, $tagSeo->id, $tagPerformance->id]);

        $post2 = \App\Models\Post::updateOrCreate(
            ['slug' => 'mastering-laravel-12-eloquent-query-optimization'],
            [
                'user_id' => $admin->id,
                'title' => 'Mastering Laravel 12 Eloquent Query Optimization for High-Traffic Publications',
                'excerpt' => 'Deep dive into avoiding N+1 queries, leveraging compound composite indexes, and implementing cache tags with Redis in Laravel 12.',
                'content' => '<h2>The Cost of Inefficient Database Queries</h2><p>When scaling a publication platform to millions of page views, unoptimized database queries quickly become the primary bottleneck. In Laravel 12, Eloquent provides incredible expressiveness, but requires thoughtful indexing and eager loading.</p><h2>1. Eager Loading Relationships</h2><p>Avoid the notorious N+1 query problem by eager loading author profiles, categories, and tags in a single trip:</p><pre><code>$posts = Post::with([\'user\', \'categories\', \'tags\'])->published()->paginate(12);</code></pre><h2>2. Database Indexing Strategy</h2><p>Ensure composite indexes on <code>(status, published_at)</code> and unique indexes on <code>slug</code> to keep lookups under 2 milliseconds.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=1200&q=80',
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'is_featured' => false,
                'reading_time' => 4,
                'meta_title' => 'Mastering Laravel 12 Eloquent Query Optimization',
                'meta_description' => 'Techniques for speeding up database queries, eliminating N+1 issues, and tuning Redis cache in Laravel 12.',
                'views_count' => 195,
            ]
        );
        $post2->categories()->sync([$catLaravel->id, $catArchitecture->id]);
        $post2->tags()->sync([$tagPhp->id, $tagPerformance->id]);

        $post3 = \App\Models\Post::updateOrCreate(
            ['slug' => 'preserving-seo-rankings-with-dynamic-301-redirect-middleware'],
            [
                'user_id' => $admin->id,
                'title' => 'Preserving SEO Link Equity with Dynamic 301 Redirect Middleware in Laravel',
                'excerpt' => 'How to write custom Laravel middleware that seamlessly matches legacy URL permutations and redirects search engine bots with HTTP 301.',
                'content' => '<h2>Why 301 Redirects Matter</h2><p>When restructuring blog URLs from <code>/2022/04/post-title/</code> to <code>/posts/post-title</code>, search engines must be informed that the content has moved permanently. Failing to do so causes immediate ranking drops and broken backlinks.</p><h2>Building the Middleware</h2><p>By capturing incoming requests in custom middleware and executing fast index lookups against a redirects table, incoming visitors and search crawlers are immediately routed to the canonical destination without impacting application latency.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200&q=80',
                'status' => 'published',
                'published_at' => now()->subDays(9),
                'is_featured' => false,
                'reading_time' => 3,
                'meta_title' => 'Preserving SEO Link Equity with Dynamic 301 Redirect Middleware',
                'meta_description' => 'Guide on building 301 redirect middleware in Laravel to safeguard Google SEO rankings.',
                'views_count' => 128,
            ]
        );
        $post3->categories()->sync([$catMigration->id, $catDevops->id]);
        $post3->tags()->sync([$tagSeo->id, $tagTailwind->id]);

        // Seed Sample Media Files
        \App\Models\Media::updateOrCreate(
            ['filename' => 'laravel-12-banner.webp'],
            [
                'user_id' => $admin->id,
                'original_name' => 'Laravel 12 Cloud Architecture.webp',
                'disk' => 'public',
                'path' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=1200&q=80',
                'mime_type' => 'image/webp',
                'size' => 184320,
                'width' => 1200,
                'height' => 800,
                'alt_text' => 'Laravel 12 Cloud Architecture Banner',
                'caption' => 'High throughput Laravel 12 application architecture overview.',
            ]
        );

        \App\Models\Media::updateOrCreate(
            ['filename' => 'database-indexing-diagram.webp'],
            [
                'user_id' => $admin->id,
                'original_name' => 'Database Indexing Optimization.webp',
                'disk' => 'public',
                'path' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=1200&q=80',
                'mime_type' => 'image/webp',
                'size' => 245760,
                'width' => 1200,
                'height' => 800,
                'alt_text' => 'Database Indexing Optimization Diagram',
                'caption' => 'Composite B-Tree index lookup speed comparison.',
            ]
        );

        // Seed Sample Comments
        $comment1 = \App\Models\Comment::updateOrCreate(
            ['author_email' => 'michael@techlead.io', 'post_id' => $post1->id],
            [
                'author_name' => 'Michael Vance',
                'author_url' => 'https://techlead.io',
                'content' => 'Outstanding guide! The Gutenberg block cleaner and 301 redirect engine solved our exact migration challenges without losing Google SEO rankings.',
                'status' => 'approved',
                'created_at' => now()->subDays(1),
            ]
        );

        // Admin Reply to Comment 1
        \App\Models\Comment::updateOrCreate(
            ['parent_id' => $comment1->id, 'user_id' => $admin->id],
            [
                'post_id' => $post1->id,
                'author_name' => $admin->name,
                'author_email' => $admin->email,
                'content' => 'Thank you Michael! Glad the 301 redirect pipeline helped safeguard your search traffic.',
                'status' => 'approved',
                'created_at' => now()->subHours(18),
            ]
        );

        // Pending Comment for Admin Review
        \App\Models\Comment::updateOrCreate(
            ['author_email' => 'sarah.dev@example.com', 'post_id' => $post1->id],
            [
                'author_name' => 'Sarah Connor',
                'content' => 'How does this migration handle large wp-content/uploads directories with over 50GB of images? Does it support direct S3 transfer?',
                'status' => 'pending',
                'created_at' => now()->subHours(2),
            ]
        );

        // Sample 301 Redirect for legacy WP post
        \App\Models\Redirect::updateOrCreate(
            ['source_url' => '/2024/01/sample-old-post/'],
            [
                'target_url' => '/posts/migrating-from-wordpress-to-laravel-12-complete-guide',
                'status_code' => 301,
            ]
        );
    }
}
