<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Services\IndexNowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SitemapAndFeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_xml_renders_correctly_with_published_posts(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Backend Engineering', 'slug' => 'backend-engineering']);
        $tag = Tag::create(['name' => 'PostgreSQL', 'slug' => 'postgresql']);

        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Postgres Index Tuning Guide',
            'slug' => 'postgres-index-tuning-guide',
            'excerpt' => 'Deep dive into B-Tree and GIN indexes in PostgreSQL.',
            'content' => '<p>PostgreSQL indexing guide content.</p>',
            'status' => 'published',
            'published_at' => now()->subHours(2),
            'featured_image' => 'https://sejan.dev/images/postgres.jpg',
        ]);

        $post->categories()->attach($category->id);
        $post->tags()->attach($tag->id);

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');
        $response->assertSee('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"', false);
        $response->assertSee(route('home'), false);
        $response->assertSee(route('blog.about'), false);
        $response->assertSee(route('blog.contact'), false);
        $response->assertSee(route('blog.show', $post->slug), false);
        $response->assertSee(route('blog.category', $category->slug), false);
        $response->assertSee(route('blog.tag', $tag->slug), false);
        $response->assertSee('https://sejan.dev/images/postgres.jpg', false);
    }

    public function test_rss_feed_renders_valid_rss2_xml(): void
    {
        $user = User::factory()->create(['name' => 'Sejan']);

        Post::create([
            'user_id' => $user->id,
            'title' => 'High Concurrency in Laravel',
            'slug' => 'high-concurrency-in-laravel',
            'excerpt' => 'Managing concurrency and database locking in production.',
            'content' => '<p>Detailed concurrency guide.</p>',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/feed');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/rss+xml; charset=utf-8');
        $response->assertSee('<rss version="2.0"', false);
        $response->assertSee('<title>Sejan · Blog</title>', false);
        $response->assertSee('High Concurrency in Laravel', false);
        $response->assertSee('Sejan', false);

        // Also test /rss.xml alias route
        $rssAlias = $this->get('/rss.xml');
        $rssAlias->assertStatus(200);
    }

    public function test_atom_feed_renders_valid_atom_xml(): void
    {
        $user = User::factory()->create();

        Post::create([
            'user_id' => $user->id,
            'title' => 'Building Event-Driven Systems',
            'slug' => 'building-event-driven-systems',
            'excerpt' => 'Architecture patterns for event-driven systems.',
            'content' => '<p>Full content of the post.</p>',
            'status' => 'published',
            'published_at' => now()->subDays(3),
        ]);

        $response = $this->get('/feed/atom');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/atom+xml; charset=utf-8');
        $response->assertSee('<feed xmlns="http://www.w3.org/2005/Atom"', false);
        $response->assertSee('<title>Sejan · Blog</title>', false);
        $response->assertSee('Building Event-Driven Systems', false);
    }

    public function test_robots_txt_renders_disallow_rules_and_sitemap(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');
        $response->assertSee('User-agent: *', false);
        $response->assertSee('Disallow: /admin/', false);
        $response->assertSee('Disallow: /login', false);
        $response->assertSee('Sitemap: ' . route('sitemap.xml'), false);
    }

    public function test_indexnow_service_submits_cleanly(): void
    {
        Http::fake([
            'https://api.indexnow.org/indexnow' => Http::response(['success' => true], 200),
        ]);

        $result = IndexNowService::submit('https://sejan.dev/posts/test-article');

        $this->assertTrue($result);
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.indexnow.org/indexnow'
                && $request['host'] === 'sejan.dev'
                && in_array('https://sejan.dev/posts/test-article', $request['urlList']);
        });
    }
}
