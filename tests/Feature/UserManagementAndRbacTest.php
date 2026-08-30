<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementAndRbacTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'sejan840@protonmail.com')->first();
    }

    public function test_admin_can_access_users_management_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertSeeText('Users & Roles Management', false);
        $response->assertSee($this->admin->email);
        $response->assertSeeText('Administrator', false);
    }

    public function test_admin_can_view_create_user_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.create'));

        $response->assertStatus(200);
        $response->assertSee('Provision System User');
        $response->assertSee('Administrator');
        $response->assertSee('Editor');
        $response->assertSee('Author');
    }

    public function test_admin_can_create_new_editor_user(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Sarah Editor',
            'email' => 'sarah.editor@example.com',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
            'role' => 'editor',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'email' => 'sarah.editor@example.com',
            'name' => 'Sarah Editor',
            'role' => 'editor',
        ]);

        $createdUser = User::where('email', 'sarah.editor@example.com')->first();
        $this->assertNotNull($createdUser);
        $this->assertTrue($createdUser->hasRole('editor'));
        $this->assertTrue($createdUser->hasPermissionTo('manage posts'));
        $this->assertTrue($createdUser->hasPermissionTo('manage comments'));
        $this->assertFalse($createdUser->hasPermissionTo('manage users'));
    }

    public function test_admin_can_create_new_admin_user(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Secondary Admin',
            'email' => 'secondary.admin@example.com',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $createdUser = User::where('email', 'secondary.admin@example.com')->first();
        $this->assertNotNull($createdUser);
        $this->assertTrue($createdUser->hasRole('admin'));
        $this->assertTrue($createdUser->isAdmin());
        $this->assertTrue($createdUser->hasPermissionTo('manage users'));
        $this->assertTrue($createdUser->hasPermissionTo('manage settings'));
    }

    public function test_admin_can_update_user_details_and_role(): void
    {
        $author = User::factory()->create([
            'name' => 'Original Author',
            'email' => 'original.author@example.com',
            'password' => Hash::make('password'),
            'role' => 'author',
        ]);
        $author->assignRole('author');

        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $author), [
            'name' => 'Promoted Editor',
            'email' => 'promoted.editor@example.com',
            'role' => 'editor',
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $author->refresh();
        $this->assertEquals('Promoted Editor', $author->name);
        $this->assertEquals('promoted.editor@example.com', $author->email);
        $this->assertTrue($author->hasRole('editor'));
        $this->assertFalse($author->hasRole('author'));
    }

    public function test_admin_cannot_revoke_own_admin_role(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $this->admin), [
            'name' => $this->admin->name,
            'email' => $this->admin->email,
            'role' => 'editor', // attempting to downgrade self
        ]);

        $response->assertSessionHasErrors('role');
        $this->admin->refresh();
        $this->assertTrue($this->admin->hasRole('admin'));
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->admin));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_admin_can_delete_other_user(): void
    {
        $userToDelete = User::factory()->create([
            'name' => 'User To Delete',
            'email' => 'delete.me@example.com',
            'password' => Hash::make('password'),
            'role' => 'author',
        ]);
        $userToDelete->assignRole('author');

        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $userToDelete));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

    public function test_non_admin_cannot_access_user_management(): void
    {
        $editor = User::factory()->create([
            'name' => 'Jane Editor',
            'email' => 'editor@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'role' => 'editor',
        ]);
        $editor->assignRole('editor');

        // Accessing user list
        $response = $this->actingAs($editor)->get(route('admin.users.index'));
        $response->assertStatus(403);

        // Accessing user creation form
        $createResponse = $this->actingAs($editor)->get(route('admin.users.create'));
        $createResponse->assertStatus(403);

        // Attempting to post new user
        $storeResponse = $this->actingAs($editor)->post(route('admin.users.store'), [
            'name' => 'Hacker User',
            'email' => 'hacker@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'admin',
        ]);
        $storeResponse->assertStatus(403);
    }
}
