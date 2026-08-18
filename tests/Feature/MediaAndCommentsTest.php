<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Media;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaAndCommentsTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\RateLimiter::clear('comment|127.0.0.1');
        $this->withoutMiddleware(\Spatie\Honeypot\ProtectAgainstSpam::class);
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_view_media_library(): void
    {
        $admin = User::where('is_admin', true)->first();

        $response = $this->actingAs($admin)->get('/admin/media');

        $response->assertStatus(200);
        $response->assertSee('Media Manager');
    }

    public function test_admin_can_upload_media(): void
    {
        Storage::fake('public');
        $admin = User::where('is_admin', true)->first();

        $file = UploadedFile::fake()->image('test-banner.jpg', 800, 600);

        $response = $this->actingAs($admin)->post('/admin/media', [
            'files' => [$file],
        ]);

        $response->assertRedirect('/admin/media');
        $this->assertDatabaseHas('media', [
            'original_name' => 'test-banner.jpg',
            'mime_type' => 'image/jpeg',
        ]);
    }

    public function test_admin_can_update_media_metadata(): void
    {
        $admin = User::where('is_admin', true)->first();
        $media = Media::first();

        $response = $this->actingAs($admin)->put('/admin/media/' . $media->id, [
            'alt_text' => 'Updated Alt Text Description',
            'caption' => 'Updated Caption for SEO',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('media', [
            'id' => $media->id,
            'alt_text' => 'Updated Alt Text Description',
            'caption' => 'Updated Caption for SEO',
        ]);
    }

    public function test_public_user_can_submit_comment_and_enters_pending_status(): void
    {
        $post = Post::first();

        $response = $this->post('/posts/' . $post->id . '/comments', [
            'author_name' => 'Jane Reader',
            'author_email' => 'jane@example.com',
            'content' => 'This is a fantastic technical explanation.',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'author_name' => 'Jane Reader',
            'author_email' => 'jane@example.com',
            'status' => 'pending', // Strictly pending
        ]);
    }

    public function test_admin_can_approve_pending_comment(): void
    {
        $admin = User::where('is_admin', true)->first();
        $pendingComment = Comment::where('status', 'pending')->first();

        $response = $this->actingAs($admin)->put('/admin/comments/' . $pendingComment->id . '/status', [
            'status' => 'approved',
        ]);

        $response->assertStatus(302);
        $this->assertEquals('approved', $pendingComment->fresh()->status);
    }

    public function test_admin_can_post_comment_reply(): void
    {
        $admin = User::where('is_admin', true)->first();
        $comment = Comment::first();

        $response = $this->actingAs($admin)->post('/admin/comments/' . $comment->id . '/reply', [
            'content' => 'Thanks for your feedback!',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('comments', [
            'parent_id' => $comment->id,
            'content' => 'Thanks for your feedback!',
            'status' => 'approved',
        ]);
    }
}
