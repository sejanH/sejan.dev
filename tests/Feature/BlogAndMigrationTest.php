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

    public function test_single_post_renders_and_increments_views(): void
    {
        $post = Post::first();
        $initialViews = $post->views_count;

        $response = $this->get('/posts/' . $post->slug);

        $response->assertStatus(200);
        $response->assertSee($post->title);
        $this->assertEquals($initialViews + 1, $post->fresh()->views_count);
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
}
