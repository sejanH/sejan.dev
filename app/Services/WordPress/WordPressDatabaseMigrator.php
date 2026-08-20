<?php

namespace App\Services\WordPress;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Media;
use App\Models\Post;
use App\Models\Redirect;
use App\Models\Tag;
use App\Models\User;
use App\Models\WpSetting;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
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
        $database = $config['database'] ?? config('wordpress.database.database', env('WP_DB_DATABASE', 'sejanxyz_blog'));
        $username = $config['username'] ?? config('wordpress.database.username', env('WP_DB_USERNAME', 'sejanwp'));
        $password = $config['password'] ?? config('wordpress.database.password', env('WP_DB_PASSWORD', 'sejanxyz@2025'));

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
        $uploadsDir = $config['uploads_path'] ?? config('wordpress.media.uploads_path', env('WP_UPLOADS_PATH', '/var/www/sejan.xyz/wp-content/uploads'));

        $stats = [
            'categories' => 0,
            'tags' => 0,
            'media' => 0,
            'posts' => 0,
            'comments' => 0,
            'redirects' => 0,
            'errors' => [],
        ];

        // 1. Taxonomies
        $taxStats = $this->migrateTaxonomies($pdo, $prefix);
        $stats['categories'] = $taxStats['categories'];
        $stats['tags'] = $taxStats['tags'];

        // 2. Media Library & Files
        $mediaStats = $this->migrateMedia($pdo, $prefix, $uploadsDir);
        $stats['media'] = $mediaStats['media'];
        $stats['errors'] = array_merge($stats['errors'], $mediaStats['errors']);

        // 3. Posts & Pages
        $postStats = $this->migratePosts($pdo, $prefix);
        $stats['posts'] = $postStats['posts'];
        $stats['redirects'] = $postStats['redirects'];
        $stats['errors'] = array_merge($stats['errors'], $postStats['errors']);

        // 4. Comments
        $commentStats = $this->migrateComments($pdo, $prefix);
        $stats['comments'] = $commentStats['comments'];
        $stats['errors'] = array_merge($stats['errors'], $commentStats['errors']);

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
            $cleanName = html_entity_decode(html_entity_decode($term['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($term['taxonomy'] === 'category') {
                Category::updateOrCreate(
                    ['wp_id' => $term['term_id']],
                    [
                        'name' => $cleanName,
                        'slug' => $term['slug'] ?: Str::slug($cleanName),
                        'description' => $term['description'] ?: null,
                    ]
                );
                $categoriesCount++;
            } elseif ($term['taxonomy'] === 'post_tag') {
                Tag::updateOrCreate(
                    ['wp_id' => $term['term_id']],
                    [
                        'name' => $cleanName,
                        'slug' => $term['slug'] ?: Str::slug($cleanName),
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
     * Migrate Media Attachments and Copy Physical Files.
     */
    public function migrateMedia(PDO $pdo, string $prefix, ?string $uploadsDir = null): array
    {
        $mediaCount = 0;
        $errors = [];

        $defaultUser = User::where('is_admin', true)->first() ?? User::first();
        $defaultUserId = $defaultUser ? $defaultUser->id : null;

        $sql = "
            SELECT ID, post_title, post_excerpt, post_mime_type, guid, post_date
            FROM `{$prefix}posts`
            WHERE post_type = 'attachment'
            ORDER BY ID ASC
        ";

        $stmt = $pdo->query($sql);
        $attachments = $stmt->fetchAll();

        $metaStmt = $pdo->prepare("SELECT meta_key, meta_value FROM `{$prefix}postmeta` WHERE post_id = ?");

        $destDir = storage_path('app/public/media');
        if (!File::isDirectory($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }

        foreach ($attachments as $att) {
            try {
                $metaStmt->execute([$att['ID']]);
                $meta = $metaStmt->fetchAll(PDO::FETCH_KEY_PAIR);

                $attachedFile = $meta['_wp_attached_file'] ?? null;
                $altText = $meta['_wp_attachment_image_alt'] ?? null;
                $caption = $att['post_excerpt'] ?: null;
                $mimeType = $att['post_mime_type'] ?: 'image/jpeg';

                $width = null;
                $height = null;
                if (!empty($meta['_wp_attachment_metadata'])) {
                    $rawMeta = @unserialize($meta['_wp_attachment_metadata']);
                    if (is_array($rawMeta)) {
                        $width = $rawMeta['width'] ?? null;
                        $height = $rawMeta['height'] ?? null;
                    }
                }

                $filename = $attachedFile ? basename($attachedFile) : basename(parse_url($att['guid'], PHP_URL_PATH) ?? 'file.jpg');
                $relativePath = $attachedFile ? 'media/' . $attachedFile : 'media/' . $filename;
                $targetAbsolutePath = storage_path('app/public/' . $relativePath);

                $size = 0;

                // Copy physical file if uploads directory is accessible
                if ($uploadsDir && $attachedFile) {
                    $sourcePath = rtrim($uploadsDir, '/') . '/' . ltrim($attachedFile, '/');
                    if (File::exists($sourcePath)) {
                        $targetDir = dirname($targetAbsolutePath);
                        if (!File::isDirectory($targetDir)) {
                            File::makeDirectory($targetDir, 0755, true);
                        }
                        File::copy($sourcePath, $targetAbsolutePath);
                        $size = File::size($targetAbsolutePath);
                    }
                }

                Media::updateOrCreate(
                    ['wp_attachment_id' => $att['ID']],
                    [
                        'user_id' => $defaultUserId,
                        'filename' => $filename,
                        'original_name' => $att['post_title'] ?: $filename,
                        'disk' => 'public',
                        'path' => $relativePath,
                        'mime_type' => $mimeType,
                        'size' => $size,
                        'width' => $width,
                        'height' => $height,
                        'alt_text' => $altText,
                        'caption' => $caption,
                    ]
                );

                $mediaCount++;
            } catch (Exception $e) {
                Log::error("Failed to migrate WP Media ID {$att['ID']}: " . $e->getMessage());
                $errors[] = "Media ID {$att['ID']}: " . $e->getMessage();
            }
        }

        return [
            'media' => $mediaCount,
            'errors' => $errors,
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
                    $mediaRecord = Media::where('wp_attachment_id', $postMeta['_thumbnail_id'])->first();
                    if ($mediaRecord) {
                        $featuredImage = $mediaRecord->url;
                    } else {
                        $attachmentStmt->execute([$postMeta['_thumbnail_id']]);
                        $att = $attachmentStmt->fetch();
                        if ($att && !empty($att['guid'])) {
                            $featuredImage = $att['guid'];
                        }
                    }
                }

                $cleanTitle = html_entity_decode(html_entity_decode($wpPost['post_title'] ?: 'Untitled Post', ENT_QUOTES | ENT_HTML5, 'UTF-8'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $slug = $wpPost['post_name'] ?: Str::slug($cleanTitle);
                $publishedAt = $wpPost['post_date'] ? Carbon::parse($wpPost['post_date']) : now();

                // Unique slug constraint check
                $existingPostWithSlug = Post::where('slug', $slug)->where('wp_id', '!=', $wpPost['ID'])->first();
                if ($existingPostWithSlug) {
                    $slug .= '-' . $wpPost['ID'];
                }

                $rawExcerpt = $wpPost['post_excerpt'] ? trim($wpPost['post_excerpt']) : $this->transformer->makeExcerpt($cleanContent);
                $cleanExcerpt = html_entity_decode($rawExcerpt, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                $post = Post::updateOrCreate(
                    ['wp_id' => $wpPost['ID']],
                    [
                        'user_id' => $defaultUserId,
                        'title' => $cleanTitle,
                        'slug' => $slug,
                        'excerpt' => $cleanExcerpt,
                        'content' => $cleanContent,
                        'featured_image' => $featuredImage,
                        'status' => $wpPost['post_status'] === 'publish' ? 'published' : 'draft',
                        'published_at' => $wpPost['post_status'] === 'publish' ? $publishedAt : null,
                        'is_featured' => false,
                        'meta_title' => $metaTitle ? html_entity_decode($metaTitle, ENT_QUOTES | ENT_HTML5, 'UTF-8') : null,
                        'meta_description' => $metaDesc ? html_entity_decode($metaDesc, ENT_QUOTES | ENT_HTML5, 'UTF-8') : null,
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

    /**
     * Migrate Comments and Nested Reply Trees.
     */
    public function migrateComments(PDO $pdo, string $prefix): array
    {
        $commentsCount = 0;
        $errors = [];

        $sql = "
            SELECT comment_ID, comment_post_ID, comment_author, comment_author_email,
                   comment_author_url, comment_author_IP, comment_date, comment_content,
                   comment_approved, comment_parent, comment_agent, user_id
            FROM `{$prefix}comments`
            WHERE comment_type IN ('', 'comment')
            ORDER BY comment_date ASC
        ";

        $stmt = $pdo->query($sql);
        $wpComments = $stmt->fetchAll();

        // 1st Pass: Ingest all comments
        foreach ($wpComments as $c) {
            try {
                $post = Post::where('wp_id', $c['comment_post_ID'])->first();
                if (!$post) {
                    continue;
                }

                $status = match ($c['comment_approved']) {
                    '1' => 'approved',
                    'spam' => 'spam',
                    'trash' => 'trash',
                    default => 'pending',
                };

                $createdAt = $c['comment_date'] ? Carbon::parse($c['comment_date']) : now();

                Comment::updateOrCreate(
                    ['wp_comment_id' => $c['comment_ID']],
                    [
                        'post_id' => $post->id,
                        'author_name' => $c['comment_author'] ?: 'Anonymous',
                        'author_email' => $c['comment_author_email'] ?: 'reader@sejan.dev',
                        'author_url' => $c['comment_author_url'] ?: null,
                        'content' => trim($c['comment_content']),
                        'status' => $status,
                        'ip_address' => $c['comment_author_IP'] ?: null,
                        'user_agent' => $c['comment_agent'] ?: null,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]
                );

                $commentsCount++;
            } catch (Exception $e) {
                Log::error("Failed to migrate WP Comment ID {$c['comment_ID']}: " . $e->getMessage());
                $errors[] = "Comment ID {$c['comment_ID']}: " . $e->getMessage();
            }
        }

        // 2nd Pass: Link parent_id for threaded replies
        foreach ($wpComments as $c) {
            if (!empty($c['comment_parent']) && $c['comment_parent'] > 0) {
                $parentComment = Comment::where('wp_comment_id', $c['comment_parent'])->first();
                $childComment = Comment::where('wp_comment_id', $c['comment_ID'])->first();

                if ($parentComment && $childComment) {
                    $childComment->update(['parent_id' => $parentComment->id]);
                }
            }
        }

        return [
            'comments' => $commentsCount,
            'errors' => $errors,
        ];
    }
}
