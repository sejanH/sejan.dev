<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
            ->take(7)
            ->get();

        // Fetch hierarchical categories with published post counts — cached 1 hour
        $sidebarCategories = Cache::remember('sidebar_categories', 3600, function () {
            return Category::with(['children' => function ($q) {
                    $q->withCount(['posts' => function ($q) {
                        $q->published();
                    }])->orderBy('name');
                }])
                ->whereNull('parent_id')
                ->withCount(['posts' => function ($q) {
                    $q->published();
                }])
                ->orderBy('name')
                ->get();
        });

        return view('blog.show', [
            'post'               => $post,
            'relatedPosts'       => $relatedPosts,
            'sidebarCategories'  => $sidebarCategories,
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

    /**
     * Display the Contact page.
     */
    public function contact(): View
    {
        return view('blog.contact');
    }

    /**
     * Handle public contact form submission.
     */
    public function handleContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|max:5000',
        ]);

        ContactMessage::create([
            'name' => trim(strip_tags($validated['name'])),
            'email' => trim(strtolower($validated['email'])),
            'subject' => !empty($validated['subject']) ? trim(strip_tags($validated['subject'])) : null,
            'message' => trim(strip_tags($validated['message'])),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'is_read' => false,
        ]);

        return back()->with('success', 'Thank you! Your message has been sent successfully.');
    }
}
