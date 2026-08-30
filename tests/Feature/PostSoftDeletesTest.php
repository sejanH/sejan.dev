<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PostSoftDeletesTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'sejan840@protonmail.com')->first();
    }

    public function test_admin_can_soft_delete_post_to_trash(): void
    {
        $post = Post::where('status', 'published')->first();
        $slug = $post->slug;

        $response = $this->actingAs($this->admin)->delete(route('admin.posts.destroy', $post));

        $response->assertRedirect(route('admin.posts.index'));
        $response->assertSessionHas('status');

        // Assert soft deleted in database
        $this->assertSoftDeleted('posts', ['id' => $post->id]);

        // Trashed post must not be accessible publicly
        $publicResponse = $this->get('/posts/' . $slug);
        $publicResponse->assertStatus(404);
    }

    public function test_admin_can_view_trashed_posts(): void
    {
        $post = Post::first();
        $post->delete();

        $response = $this->actingAs($this->admin)->get(route('admin.posts.index', ['status' => 'trashed']));

        $response->assertStatus(200);
        $response->assertSee('Trash');
        $response->assertSee($post->title);
        $response->assertSee('Restore');
        $response->assertSee('Delete Forever');
    }

    public function test_admin_can_restore_trashed_post(): void
    {
        $post = Post::first();
        $post->delete();
        $this->assertTrue($post->trashed());

        $response = $this->actingAs($this->admin)->put(route('admin.posts.restore', $post->id));

        $response->assertRedirect(route('admin.posts.index', ['status' => 'trashed']));
        $response->assertSessionHas('status');

        $post->refresh();
        $this->assertFalse($post->trashed());
        $this->assertNull($post->deleted_at);
    }

    public function test_admin_can_force_delete_trashed_post_permanently(): void
    {
        $post = Post::first();
        $postId = $post->id;
        $post->delete();

        $response = $this->actingAs($this->admin)->delete(route('admin.posts.forceDelete', $postId));

        $response->assertRedirect(route('admin.posts.index', ['status' => 'trashed']));
        $response->assertSessionHas('status');

        // Permanently gone from database
        $this->assertDatabaseMissing('posts', ['id' => $postId]);
    }

    public function test_admin_posts_listing_defaults_to_newest_first(): void
    {
        $oldPost = Post::create([
            'user_id' => $this->admin->id,
            'title' => 'Ancient Article From Long Ago',
            'slug' => 'ancient-article-long-ago',
            'content' => '<p>Ancient post content</p>',
            'status' => 'published',
            'created_at' => now()->subMonths(12),
        ]);

        $newPost = Post::create([
            'user_id' => $this->admin->id,
            'title' => 'Brand New Article Today',
            'slug' => 'brand-new-article-today',
            'content' => '<p>Brand new content</p>',
            'status' => 'published',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.posts.index'));
        $response->assertStatus(200);

        $posts = $response->viewData('posts');
        $this->assertEquals($newPost->id, $posts->first()->id);
    }

    public function test_admin_posts_listing_supports_sorting_by_views_and_title(): void
    {
        $postA = Post::create([
            'user_id' => $this->admin->id,
            'title' => 'AAA First Alpha Article',
            'slug' => 'aaa-first-alpha-article',
            'content' => '<p>Alpha content</p>',
            'status' => 'published',
            'views_count' => 10,
            'created_at' => now()->subDays(2),
        ]);

        $postB = Post::create([
            'user_id' => $this->admin->id,
            'title' => 'ZZZ Last Alpha Article',
            'slug' => 'zzz-last-alpha-article',
            'content' => '<p>Last content</p>',
            'status' => 'published',
            'views_count' => 500,
            'created_at' => now()->subDays(1),
        ]);

        // Test title ascending (A-Z)
        $responseAlpha = $this->actingAs($this->admin)->get(route('admin.posts.index', ['sort' => 'title_asc']));
        $responseAlpha->assertStatus(200);
        $this->assertEquals($postA->id, $responseAlpha->viewData('posts')->first()->id);

        // Test views descending (Most views first)
        $responseViews = $this->actingAs($this->admin)->get(route('admin.posts.index', ['sort' => 'views_desc']));
        $responseViews->assertStatus(200);
        $this->assertEquals($postB->id, $responseViews->viewData('posts')->first()->id);
    }
}
