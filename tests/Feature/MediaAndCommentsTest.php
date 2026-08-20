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

    public function test_admin_can_fetch_media_picker_list_json(): void
    {
        $admin = User::where('is_admin', true)->first();

        $response = $this->actingAs($admin)->getJson('/admin/media/picker-list');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'current_page',
            'last_page',
            'total',
        ]);

        $response2 = $this->actingAs($admin)->getJson('/admin/media?json=1');
        $response2->assertStatus(200);
        $response2->assertJsonStructure(['data']);
    }

    public function test_admin_can_upload_media_via_ajax_and_receive_url(): void
    {
        Storage::fake('public');
        $admin = User::where('is_admin', true)->first();
        $file = UploadedFile::fake()->image('my-custom-cover.png', 1200, 630);

        $response = $this->actingAs($admin)->postJson('/admin/media', [
            'file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'url',
            'files',
        ]);

        $uploadedUrl = $response->json('url');
        $this->assertNotEmpty($uploadedUrl);
        $this->assertStringContainsString('my-custom-cover', $uploadedUrl);
    }

    public function test_admin_can_upload_unsupported_image_and_converts_to_jpeg(): void
    {
        Storage::fake('public');
        $admin = User::where('is_admin', true)->first();

        // Create an image with a non-native extension (e.g. .bmp / .tiff)
        $file = UploadedFile::fake()->image('diagram.bmp', 600, 400);

        $response = $this->actingAs($admin)->postJson('/admin/media', [
            'file' => $file,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('media', [
            'original_name' => 'diagram.bmp',
            'mime_type' => 'image/jpeg',
        ]);

        $media = Media::where('original_name', 'diagram.bmp')->first();
        $this->assertNotNull($media);
        $this->assertStringEndsWith('.jpg', $media->filename);
        $this->assertEquals(600, $media->width);
        $this->assertEquals(400, $media->height);
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

    public function test_media_library_shows_latest_images_first(): void
    {
        $admin = User::where('is_admin', true)->first();

        $olderMedia = Media::create([
            'user_id' => $admin->id,
            'filename' => 'older-image.jpg',
            'original_name' => 'Older Image',
            'disk' => 'public',
            'path' => 'media/2026/01/older-image.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024,
            'created_at' => now()->subDays(5),
        ]);

        $newerMedia = Media::create([
            'user_id' => $admin->id,
            'filename' => 'brand-new-image.jpg',
            'original_name' => 'Brand New Image',
            'disk' => 'public',
            'path' => 'media/2026/08/brand-new-image.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 2048,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($admin)->getJson('/admin/media?json=1&type=images');
        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertEquals($newerMedia->id, $data[0]['id']);
    }

    public function test_admin_can_filter_media_by_images_and_documents(): void
    {
        $admin = User::where('is_admin', true)->first();

        Media::create([
            'user_id' => $admin->id,
            'filename' => 'document.pdf',
            'original_name' => 'Sample Document PDF',
            'disk' => 'public',
            'path' => 'media/2026/08/document.pdf',
            'mime_type' => 'application/pdf',
            'size' => 5000,
        ]);

        $responseImages = $this->actingAs($admin)->getJson('/admin/media?json=1&type=images');
        $responseImages->assertStatus(200);
        foreach ($responseImages->json('data') as $item) {
            $this->assertStringStartsWith('image/', $item['mime_type']);
        }

        $responseDocs = $this->actingAs($admin)->getJson('/admin/media?json=1&type=documents');
        $responseDocs->assertStatus(200);
        foreach ($responseDocs->json('data') as $item) {
            $this->assertStringStartsNotWith('image/', $item['mime_type']);
        }
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
