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

        $this->admin = User::where('email', 'admin@sejan.dev')->first();
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
}
