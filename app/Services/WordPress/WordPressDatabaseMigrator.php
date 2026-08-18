<?php

namespace App\Services\WordPress;

use App\Models\Category;
use App\Models\Post;
use App\Models\Redirect;
use App\Models\Tag;
use App\Models\User;
use App\Models\WpSetting;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PDO;

class WordPressDatabaseMigrator
{
    protected WordPressContentTransformer $transformer;

    public function __construct(WordPressContentTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Get a PDO connection to the WordPress database.
     */
    public function getConnection(?array $config = null): PDO
    {
        $host = $config['host'] ?? config('wordpress.database.host', env('WP_DB_HOST', '127.0.0.1'));
        $port = $config['port'] ?? config('wordpress.database.port', env('WP_DB_PORT', '3306'));
        $database = $config['database'] ?? config('wordpress.database.database', env('WP_DB_DATABASE', 'wordpress_db'));
        $username = $config['username'] ?? config('wordpress.database.username', env('WP_DB_USERNAME', 'root'));
        $password = $config['password'] ?? config('wordpress.database.password', env('WP_DB_PASSWORD', ''));

        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

        return new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5,
        ]);
    }

    /**
     * Test connection to WordPress DB.
     */
    public function testConnection(?array $config = null): array
    {
        try {
            $pdo = $this->getConnection($config);
            $prefix = $config['prefix'] ?? config('wordpress.database.prefix', env('WP_TABLE_PREFIX', 'wp_'));

            $stmt = $pdo->query("SHOW TABLES LIKE '{$prefix}posts'");
            $hasPostsTable = (bool) $stmt->fetch();

            if (!$hasPostsTable) {
                return [
                    'success' => false,
                    'message' => "Connected to database, but WordPress table '{$prefix}posts' was not found.",
                ];
            }

            $countStmt = $pdo->query("SELECT COUNT(*) as cnt FROM `{$prefix}posts` WHERE post_type = 'post' AND post_status = 'publish'");
            $postsCount = $countStmt->fetch()['cnt'] ?? 0;

            return [
                'success' => true,
                'message' => "Successfully connected to WordPress database! Found {$postsCount} published posts.",
                'posts_count' => (int) $postsCount,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Run full WordPress migration.
     */
    public function migrateAll(?array $config = null): array
    {
        $pdo = $this->getConnection($config);
        $prefix = $config['prefix'] ?? config('wordpress.database.prefix', env('WP_TABLE_PREFIX', 'wp_'));

        $stats = [
            'categories' => 0,
            'tags' => 0,
            'posts' => 0,
            'redirects' => 0,
            'errors' => [],
        ];

        // 1. Taxonomies
        $taxStats = $this->migrateTaxonomies($pdo, $prefix);
        $stats['categories'] = $taxStats['categories'];
        $stats['tags'] = $taxStats['tags'];

        // 2. Posts
        $postStats = $this->migratePosts($pdo, $prefix);
        $stats['posts'] = $postStats['posts'];
        $stats['redirects'] = $postStats['redirects'];
        $stats['errors'] = array_merge($stats['errors'], $postStats['errors']);

        // Record last migration state in settings
        WpSetting::set('last_migration_at', now()->toDateTimeString());
        WpSetting::set('last_migration_stats', $stats);

        return $stats;
    }

    /**
     * Migrate Categories and Tags.
     */
    public function migrateTaxonomies(PDO $pdo, string $prefix): array
    {
        $categoriesCount = 0;
        $tagsCount = 0;

        $sql = "
            SELECT t.term_id, t.name, t.slug, tt.taxonomy, tt.description, tt.parent
            FROM `{$prefix}terms` t
            INNER JOIN `{$prefix}term_taxonomy` tt ON t.term_id = tt.term_id
            WHERE tt.taxonomy IN ('category', 'post_tag')
        ";

        $stmt = $pdo->query($sql);
        $terms = $stmt->fetchAll();

        foreach ($terms as $term) {
            if ($term['taxonomy'] === 'category') {
                Category::updateOrCreate(
                    ['wp_id' => $term['term_id']],
                    [
                        'name' => $term['name'],
                        'slug' => $term['slug'] ?: Str::slug($term['name']),
                        'description' => $term['description'] ?: null,
                    ]
                );
                $categoriesCount++;
            } elseif ($term['taxonomy'] === 'post_tag') {
                Tag::updateOrCreate(
                    ['wp_id' => $term['term_id']],
                    [
                        'name' => $term['name'],
                        'slug' => $term['slug'] ?: Str::slug($term['name']),
                        'description' => $term['description'] ?: null,
                    ]
                );
                $tagsCount++;
            }
        }

        return [
            'categories' => $categoriesCount,
            'tags' => $tagsCount,
        ];
    }

    /**
     * Migrate Posts, Pages, SEO metadata, and 301 Redirects.
     */
    public function migratePosts(PDO $pdo, string $prefix): array
    {
        $postsCount = 0;
        $redirectsCount = 0;
        $errors = [];

        // Fetch default admin user
        $defaultUser = User::where('is_admin', true)->first() ?? User::first();
        $defaultUserId = $defaultUser ? $defaultUser->id : 1;

        $sql = "
            SELECT ID, post_author, post_date, post_content, post_title, post_excerpt,
                   post_status, post_name, post_modified, post_type
            FROM `{$prefix}posts`
            WHERE post_type IN ('post', 'page')
              AND post_status IN ('publish', 'draft')
            ORDER BY post_date ASC
        ";

        $stmt = $pdo->query($sql);
        $posts = $stmt->fetchAll();

        // Prepare metadata helper
        $metaStmt = $pdo->prepare("SELECT meta_key, meta_value FROM `{$prefix}postmeta` WHERE post_id = ?");
        $termRelStmt = $pdo->prepare("
            SELECT tt.term_id, tt.taxonomy
            FROM `{$prefix}term_relationships` tr
            INNER JOIN `{$prefix}term_taxonomy` tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            WHERE tr.object_id = ?
        ");
        $attachmentStmt = $pdo->prepare("SELECT guid FROM `{$prefix}posts` WHERE ID = ? AND post_type = 'attachment'");

        foreach ($posts as $wpPost) {
            try {
                $metaStmt->execute([$wpPost['ID']]);
                $postMeta = $metaStmt->fetchAll(PDO::FETCH_KEY_PAIR);

                // Extract SEO metadata
                $metaTitle = $postMeta['_yoast_wpseo_title'] ?? $postMeta['rank_math_title'] ?? null;
                $metaDesc = $postMeta['_yoast_wpseo_metadesc'] ?? $postMeta['rank_math_description'] ?? null;

                // Extract Featured Image URL
                $featuredImage = null;
                if (!empty($postMeta['_thumbnail_id'])) {
                    $attachmentStmt->execute([$postMeta['_thumbnail_id']]);
                    $att = $attachmentStmt->fetch();
                    if ($att && !empty($att['guid'])) {
                        $featuredImage = $att['guid'];
                    }
                }

                $cleanContent = $this->transformer->transform($wpPost['post_content']);
                $slug = $wpPost['post_name'] ?: Str::slug($wpPost['post_title']);
                $publishedAt = $wpPost['post_date'] ? Carbon::parse($wpPost['post_date']) : now();

                // Unique slug constraint check
                $existingPostWithSlug = Post::where('slug', $slug)->where('wp_id', '!=', $wpPost['ID'])->first();
                if ($existingPostWithSlug) {
                    $slug .= '-' . $wpPost['ID'];
                }

                $post = Post::updateOrCreate(
                    ['wp_id' => $wpPost['ID']],
                    [
                        'user_id' => $defaultUserId,
                        'title' => $wpPost['post_title'] ?: 'Untitled Post',
                        'slug' => $slug,
                        'excerpt' => $wpPost['post_excerpt'] ? trim($wpPost['post_excerpt']) : $this->transformer->makeExcerpt($cleanContent),
                        'content' => $cleanContent,
                        'featured_image' => $featuredImage,
                        'status' => $wpPost['post_status'] === 'publish' ? 'published' : 'draft',
                        'published_at' => $wpPost['post_status'] === 'publish' ? $publishedAt : null,
                        'is_featured' => false,
                        'meta_title' => $metaTitle,
                        'meta_description' => $metaDesc,
                        'wp_post_type' => $wpPost['post_type'],
                    ]
                );

                $postsCount++;

                // Attach Categories & Tags
                $termRelStmt->execute([$wpPost['ID']]);
                $termRels = $termRelStmt->fetchAll();

                $categoryIds = [];
                $tagIds = [];

                foreach ($termRels as $rel) {
                    if ($rel['taxonomy'] === 'category') {
                        $cat = Category::where('wp_id', $rel['term_id'])->first();
                        if ($cat) {
                            $categoryIds[] = $cat->id;
                        }
                    } elseif ($rel['taxonomy'] === 'post_tag') {
                        $tag = Tag::where('wp_id', $rel['term_id'])->first();
                        if ($tag) {
                            $tagIds[] = $tag->id;
                        }
                    }
                }

                if (!empty($categoryIds)) {
                    $post->categories()->sync($categoryIds);
                }
                if (!empty($tagIds)) {
                    $post->tags()->sync($tagIds);
                }

                // 301 Redirect Preservation
                if ($wpPost['post_status'] === 'publish' && !empty($slug)) {
                    $year = $publishedAt->format('Y');
                    $month = $publishedAt->format('m');
                    $day = $publishedAt->format('d');

                    $oldUrls = [
                        "/{$year}/{$month}/{$slug}/",
                        "/{$year}/{$month}/{$slug}",
                        "/{$year}/{$month}/{$day}/{$slug}/",
                        "/?p={$wpPost['ID']}",
                    ];

                    $targetUrl = "/posts/{$slug}";

                    foreach ($oldUrls as $oldUrl) {
                        Redirect::updateOrCreate(
                            ['source_url' => $oldUrl],
                            ['target_url' => $targetUrl, 'status_code' => 301]
                        );
                        $redirectsCount++;
                    }
                }
            } catch (Exception $e) {
                Log::error("Failed to migrate WP Post ID {$wpPost['ID']}: " . $e->getMessage());
                $errors[] = "Post ID {$wpPost['ID']}: " . $e->getMessage();
            }
        }

        return [
            'posts' => $postsCount,
            'redirects' => $redirectsCount,
            'errors' => $errors,
        ];
    }
}
