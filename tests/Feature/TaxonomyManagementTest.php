<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TaxonomyManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('is_admin', true)->first();
    }

    public function test_admin_can_view_categories_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertSee('Taxonomies Management');
        $response->assertSee('Add New Category');
    }

    public function test_admin_can_create_new_category(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name' => 'Artificial Intelligence',
            'slug' => 'artificial-intelligence',
            'description' => 'Machine learning, LLMs and modern neural architectures.',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('categories', [
            'name' => 'Artificial Intelligence',
            'slug' => 'artificial-intelligence',
        ]);
    }

    public function test_admin_can_update_category(): void
    {
        $category = Category::first();

        $response = $this->actingAs($this->admin)->put(route('admin.categories.update', $category), [
            'name' => 'Updated Architecture Topic',
            'slug' => 'updated-architecture-topic',
            'description' => 'Updated topic description.',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Architecture Topic',
            'slug' => 'updated-architecture-topic',
        ]);
    }

    public function test_admin_can_delete_category(): void
    {
        $category = Category::create([
            'name' => 'Temporary Category',
            'slug' => 'temporary-category',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('status');

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_admin_can_view_tags_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.tags.index'));

        $response->assertStatus(200);
        $response->assertSee('Taxonomies Management');
        $response->assertSee('Add New Tag');
    }

    public function test_admin_can_create_new_tag(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.tags.store'), [
            'name' => 'Kubernetes',
            'slug' => 'kubernetes',
            'description' => 'Container orchestration and cloud deployments.',
        ]);

        $response->assertRedirect(route('admin.tags.index'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('tags', [
            'name' => 'Kubernetes',
            'slug' => 'kubernetes',
        ]);
    }

    public function test_admin_can_update_tag(): void
    {
        $tag = Tag::first();

        $response = $this->actingAs($this->admin)->put(route('admin.tags.update', $tag), [
            'name' => 'PHP 8.4',
            'slug' => 'php-8-4',
            'description' => 'Next gen PHP features.',
        ]);

        $response->assertRedirect(route('admin.tags.index'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => 'PHP 8.4',
            'slug' => 'php-8-4',
        ]);
    }

    public function test_admin_can_delete_tag(): void
    {
        $tag = Tag::create([
            'name' => 'Temporary Tag',
            'slug' => 'temporary-tag',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.tags.destroy', $tag));

        $response->assertRedirect(route('admin.tags.index'));
        $response->assertSessionHas('status');

        $this->assertDatabaseMissing('tags', [
            'id' => $tag->id,
        ]);
    }

    public function test_guest_cannot_access_taxonomy_management(): void
    {
        $response = $this->get(route('admin.categories.index'));
        $response->assertRedirect(route('login'));

        $responseTag = $this->get(route('admin.tags.index'));
        $responseTag->assertRedirect(route('login'));
    }
}
