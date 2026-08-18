<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ContactMessagesTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Spatie\Honeypot\ProtectAgainstSpam::class);
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@sejan.dev')->first();
    }

    public function test_contact_page_renders_successfully(): void
    {
        $response = $this->get(route('blog.contact'));

        $response->assertStatus(200);
        $response->assertSee('Contact Me');
        $response->assertSee('Send Message');
    }

    public function test_public_user_can_submit_contact_form_and_it_stores_in_database(): void
    {
        $payload = [
            'name' => 'Alex Developer',
            'email' => 'alex.dev@example.com',
            'subject' => 'Architecture Consultation Inquiry',
            'message' => 'Hello Sejan, I loved your Laravel 12 migration guide. We would like to consult on our platform.',
        ];

        $response = $this->post(route('blog.contact.send'), $payload);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Alex Developer',
            'email' => 'alex.dev@example.com',
            'subject' => 'Architecture Consultation Inquiry',
            'is_read' => false,
        ]);

        $message = ContactMessage::where('email', 'alex.dev@example.com')->first();
        $this->assertNotNull($message);
        $this->assertFalse($message->is_read);
        $this->assertNull($message->read_at);
    }

    public function test_admin_can_view_contact_messages_inbox(): void
    {
        ContactMessage::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Inquiry from John',
            'message' => 'This is a sample inquiry message.',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.messages.index'));

        $response->assertStatus(200);
        $response->assertSeeText('Contact Messages Inbox', false);
        $response->assertSee('John Doe');
        $response->assertSee('john@example.com');
        $response->assertSee('Inquiry from John');
    }

    public function test_admin_viewing_message_marks_it_as_read(): void
    {
        $message = ContactMessage::create([
            'name' => 'Mark Reader',
            'email' => 'mark@example.com',
            'subject' => 'Question on Redis',
            'message' => 'How do you handle Redis cache invalidation on cluster?',
            'is_read' => false,
        ]);

        $this->assertFalse($message->is_read);

        $response = $this->actingAs($this->admin)->get(route('admin.messages.show', $message));

        $response->assertStatus(200);
        $response->assertSee('Mark Reader');
        $response->assertSee('How do you handle Redis cache invalidation on cluster?');

        $message->refresh();
        $this->assertTrue($message->is_read);
        $this->assertNotNull($message->read_at);
    }

    public function test_admin_can_toggle_message_read_unread_status(): void
    {
        $message = ContactMessage::create([
            'name' => 'Status Tester',
            'email' => 'tester@example.com',
            'subject' => 'Status toggle test',
            'message' => 'Testing toggle read/unread.',
            'is_read' => true,
            'read_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.messages.toggle', $message));

        $response->assertRedirect();
        $message->refresh();
        $this->assertFalse($message->is_read);
        $this->assertNull($message->read_at);
    }

    public function test_admin_can_delete_contact_message(): void
    {
        $message = ContactMessage::create([
            'name' => 'Spam Sender',
            'email' => 'spam@example.com',
            'subject' => 'Spam subject',
            'message' => 'Spam content to be deleted.',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.messages.destroy', $message));

        $response->assertRedirect(route('admin.messages.index'));
        $this->assertDatabaseMissing('contact_messages', ['id' => $message->id]);
    }

    public function test_guest_cannot_access_messages_inbox(): void
    {
        $response = $this->get(route('admin.messages.index'));
        $response->assertRedirect(route('login'));
    }
}
