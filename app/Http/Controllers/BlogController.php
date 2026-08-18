<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * Show blog homepage with featured articles and latest posts.
     */
    public function index(Request $request): View
    {
        $search = $request->query('q');
        $selectedCategorySlug = $request->query('category');

        $query = Post::with(['user', 'categories', 'tags'])
            ->published()
            ->latest('published_at');

        if (!empty($search)) {
            $query->search($search);
        }

        if (!empty($selectedCategorySlug)) {
            $query->whereHas('categories', function ($q) use ($selectedCategorySlug) {
                $q->where('slug', $selectedCategorySlug);
            });
        }

        // Grab a featured post for the hero section if not searching/filtering
        $featuredPost = null;
        if (empty($search) && empty($selectedCategorySlug)) {
            $featuredPost = (clone $query)->where('is_featured', true)->first()
                ?? (clone $query)->first();
        }

        // Paginate remaining posts
        if ($featuredPost) {
            $query->where('id', '!=', $featuredPost->id);
        }

        $posts = $query->paginate(9)->withQueryString();

        $categories = Category::withCount('publishedPosts')
            ->having('published_posts_count', '>', 0)
            ->get();

        $popularTags = Tag::withCount('publishedPosts')
            ->having('published_posts_count', '>', 0)
            ->orderByDesc('published_posts_count')
            ->take(12)
            ->get();

        return view('blog.index', [
            'featuredPost' => $featuredPost,
            'posts' => $posts,
            'categories' => $categories,
            'popularTags' => $popularTags,
            'search' => $search,
            'selectedCategorySlug' => $selectedCategorySlug,
        ]);
    }

    /**
     * Display a single blog article.
     */
    public function show(string $slug): View
    {
        $post = Post::with(['user', 'categories', 'tags', 'approvedRootComments.approvedReplies'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment view count
        $post->increment('views_count');

        // Fetch related posts from matching categories
        $categoryIds = $post->categories->pluck('id')->toArray();
        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            })
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('blog.show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }

    /**
     * Display archive by category.
     */
    public function category(string $slug): View
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $posts = $category->publishedPosts()
            ->with(['user', 'categories', 'tags'])
            ->latest('published_at')
            ->paginate(9);

        return view('blog.archive', [
            'type' => 'Category',
            'title' => $category->name,
            'description' => $category->description ?: "Articles categorized under {$category->name}",
            'posts' => $posts,
        ]);
    }

    /**
     * Display archive by tag.
     */
    public function tag(string $slug): View
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        $posts = $tag->publishedPosts()
            ->with(['user', 'categories', 'tags'])
            ->latest('published_at')
            ->paginate(9);

        return view('blog.archive', [
            'type' => 'Tag',
            'title' => '#' . $tag->name,
            'description' => $tag->description ?: "Articles tagged with #{$tag->name}",
            'posts' => $posts,
        ]);
    }

    /**
     * Display the About page.
     */
    public function about(): View
    {
        return view('blog.about');
    }
}
