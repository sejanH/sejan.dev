<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoAndStructuredDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_meta_tags_and_ld_json_schema(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('<meta name="robots"', false);
        $response->assertSee('<link rel="canonical" href="' . route('home') . '"', false);
        $response->assertSee('<meta property="og:site_name" content="Sejan · Blog"', false);
        $response->assertSee('<meta name="twitter:site" content="@sejanH"', false);
        $response->assertSee('<script type="application/ld+json">', false);
        $response->assertSee('"@type": "WebSite"', false);
        $response->assertSee('"@type": "Blog"', false);
        $response->assertSee('"@type": "BreadcrumbList"', false);
    }

    public function test_single_post_renders_blog_posting_schema_and_article_meta(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Software Architecture', 'slug' => 'software-architecture']);
        $tag = Tag::create(['name' => 'Laravel', 'slug' => 'laravel']);

        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Scaling MySQL Performance and Indexing',
            'slug' => 'scaling-mysql-performance-and-indexing',
            'excerpt' => 'A guide to optimizing database indexes for high throughput.',
            'content' => '<p>Detailed article content with benchmark graphs and explain queries.</p>',
            'status' => 'published',
            'meta_title' => 'Mastering MySQL Indexing — Sejan',
            'meta_description' => 'A guide to optimizing database indexes for high throughput.',
            'published_at' => now()->subDay(),
        ]);

        $post->categories()->attach($category->id);
        $post->tags()->attach($tag->id);

        $response = $this->get(route('blog.show', $post->slug));

        $response->assertStatus(200);
        $response->assertSee('<meta property="og:type" content="article"', false);
        $response->assertSee('<meta property="article:published_time"', false);
        $response->assertSee('<meta property="article:section" content="Software Architecture"', false);
        $response->assertSee('<meta property="article:tag" content="Laravel"', false);
        $response->assertSee('<script type="application/ld+json">', false);
        $response->assertSee('"@type": "BlogPosting"', false);
        $response->assertSee('"headline": "Scaling MySQL Performance and Indexing"', false);
        $response->assertSee('"name": "Software Architecture"', false);
        $response->assertSee('"@type": "BreadcrumbList"', false);
    }

    public function test_category_archive_renders_collection_page_schema(): void
    {
        $category = Category::create(['name' => 'DevOps & Cloud', 'slug' => 'devops-cloud']);

        $response = $this->get(route('blog.category', $category->slug));

        $response->assertStatus(200);
        $response->assertSee('<script type="application/ld+json">', false);
        $response->assertSee('"@type": "CollectionPage"', false);
        $response->assertSee('Category Archive', false);
        $response->assertSee('"@type": "BreadcrumbList"', false);
    }

    public function test_about_page_renders_person_and_about_schema(): void
    {
        $response = $this->get(route('blog.about'));

        $response->assertStatus(200);
        $response->assertSee('<script type="application/ld+json">', false);
        $response->assertSee('"AboutPage"', false);
        $response->assertSee('"ProfilePage"', false);
        $response->assertSee('"S. M. Mominul Haque (Sejan)"', false);
        $response->assertSee('"Senior Software Engineer & Architect"', false);
    }

    public function test_contact_page_renders_contact_schema(): void
    {
        $response = $this->get(route('blog.contact'));

        $response->assertStatus(200);
        $response->assertSee('<script type="application/ld+json">', false);
        $response->assertSee('"@type": "ContactPage"', false);
        $response->assertSee('"S. M. Mominul Haque (Sejan)"', false);
    }
}
