<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class FeedAndSeoController extends Controller
{
    /**
     * Generate dynamic XML sitemap (cached for 1 hour).
     */
    public function sitemap(): Response
    {
        $content = Cache::remember('seo_sitemap_xml', 3600, function () {
            $latestPost = Post::published()->latest('updated_at')->first();
            $latestMod = $latestPost ? $latestPost->updated_at->toAtomString() : now()->toAtomString();

            $posts = Post::published()
                ->with(['categories', 'tags'])
                ->latest('published_at')
                ->get();

            $categories = Category::whereHas('posts', function ($q) {
                $q->published();
            })->with(['posts' => function ($q) {
                $q->published()->latest('updated_at');
            }])->get();

            $tags = Tag::whereHas('posts', function ($q) {
                $q->published();
            })->with(['posts' => function ($q) {
                $q->published()->latest('updated_at');
            }])->get();

            return view('seo.sitemap', compact('posts', 'categories', 'tags', 'latestMod'))->render();
        });

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'X-Robots-Tag' => 'noindex, follow',
        ]);
    }

    /**
     * Generate RSS 2.0 feed (cached for 1 hour).
     */
    public function rss(): Response
    {
        $content = Cache::remember('seo_rss_feed_xml', 3600, function () {
            $posts = Post::published()
                ->with(['categories', 'tags', 'user'])
                ->latest('published_at')
                ->take(25)
                ->get();

            $lastBuildDate = $posts->first()
                ? $posts->first()->published_at->toRfc2822String()
                : now()->toRfc2822String();

            return view('seo.rss', compact('posts', 'lastBuildDate'))->render();
        });

        return response($content, 200, [
            'Content-Type' => 'application/rss+xml; charset=utf-8',
        ]);
    }

    /**
     * Generate Atom 1.0 feed (cached for 1 hour).
     */
    public function atom(): Response
    {
        $content = Cache::remember('seo_atom_feed_xml', 3600, function () {
            $posts = Post::published()
                ->with(['categories', 'tags', 'user'])
                ->latest('published_at')
                ->take(25)
                ->get();

            $lastUpdated = $posts->first()
                ? $posts->first()->updated_at->toAtomString()
                : now()->toAtomString();

            return view('seo.atom', compact('posts', 'lastUpdated'))->render();
        });

        return response($content, 200, [
            'Content-Type' => 'application/atom+xml; charset=utf-8',
        ]);
    }

    /**
     * Generate robots.txt
     */
    public function robots(): Response
    {
        $sitemapUrl = route('sitemap.xml');
        $robots = <<<ROBOTS
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /login
Disallow: /logout

Sitemap: {$sitemapUrl}
ROBOTS;

        return response($robots, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }
}
