<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Redirect;
use App\Models\Tag;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BlogAndMigrationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_public_blog_homepage_renders_articles(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Sejan');
        $response->assertSee('Migrating from WordPress to Laravel 12');
    }

    public function test_homepage_pagination_fetches_13_on_page_one_and_12_on_subsequent_pages(): void
    {
        $response1 = $this->get('/');
        $response1->assertStatus(200);
        $postsPage1 = $response1->viewData('posts');
        $this->assertEquals(13, $postsPage1->perPage());

        $response2 = $this->get('/?page=2');
        $response2->assertStatus(200);
        $postsPage2 = $response2->viewData('posts');
        $this->assertEquals(12, $postsPage2->perPage());
    }

    public function test_single_post_renders_and_increments_views(): void
    {
        $post = Post::published()->first();
        $initialViews = $post->views_count;

        $response = $this->get('/posts/' . $post->slug);

        $response->assertStatus(200);
        $response->assertSee($post->title);
        $this->assertEquals($initialViews + 1, $post->fresh()->views_count);
    }

    public function test_draft_post_returns_404_not_found(): void
    {
        $draftPost = Post::create([
            'user_id' => User::first()->id,
            'title' => 'Unpublished Draft Article',
            'slug' => 'unpublished-draft-article',
            'content' => '<p>Draft content here</p>',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $response = $this->get('/posts/' . $draftPost->slug);

        $response->assertStatus(404);
    }

    public function test_category_archive_renders(): void
    {
        $category = Category::where('slug', 'laravel')->first();

        $response = $this->get('/category/' . $category->slug);

        $response->assertStatus(200);
        $response->assertSee($category->name);
    }

    public function test_tag_archive_renders(): void
    {
        $tag = Tag::where('slug', 'php82')->first();

        $response = $this->get('/tag/' . $tag->slug);

        $response->assertStatus(200);
        $response->assertSee('#' . $tag->name);
    }

    public function test_legacy_wordpress_301_redirect_works(): void
    {
        $response = $this->get('/2024/01/sample-old-post/');

        $response->assertStatus(301);
        $response->assertRedirect('/posts/migrating-from-wordpress-to-laravel-12-complete-guide');
    }

    public function test_admin_can_create_new_article(): void
    {
        $admin = User::where('is_admin', true)->first();

        $response = $this->actingAs($admin)->post('/admin/posts', [
            'title' => 'New Test Article on Laravel 12',
            'slug' => 'new-test-article-laravel-12',
            'excerpt' => 'A test excerpt',
            'content' => '<p>Test article body content here.</p>',
            'status' => 'published',
            'tags_input' => 'php, test',
        ]);

        $post = Post::where('slug', 'new-test-article-laravel-12')->first();
        $this->assertNotNull($post);
        $response->assertRedirect(route('admin.posts.edit', $post));
        $this->assertDatabaseHas('posts', [
            'slug' => 'new-test-article-laravel-12',
            'status' => 'published',
        ]);
    }

    public function test_public_registration_is_strictly_disabled(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(403);

        $postResponse = $this->post('/register', [
            'name' => 'Hacker',
            'email' => 'hacker@example.com',
            'password' => 'password123',
        ]);
        $postResponse->assertStatus(403);
    }

    public function test_admin_can_update_existing_article(): void
    {
        $admin = User::where('is_admin', true)->first();
        $post = Post::first();

        $response = $this->actingAs($admin)->put('/admin/posts/' . $post->id, [
            'title' => 'Updated Title for Architecture Post',
            'slug' => $post->slug,
            'excerpt' => 'Updated excerpt text.',
            'content' => '<h2>Updated Content</h2><p>Updated content body.</p>',
            'status' => 'published',
            'published_at' => '2026-08-20T12:00',
            'featured_image' => 'https://blog.sejan.dev/storage/media/2026/08/sample.jpg',
            'is_featured' => 1,
            'tags_input' => 'laravel, testing',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.posts.edit', $post));
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title for Architecture Post',
            'featured_image' => 'https://blog.sejan.dev/storage/media/2026/08/sample.jpg',
        ]);
    }
}
