<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Display a listing of posts for admin.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = $request->query('q');

        $query = Post::with(['user', 'categories', 'tags'])
            ->latest('created_at');

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->search($search);
        }

        $posts = $query->paginate(15)->withQueryString();

        return view('admin.posts.index', [
            'posts' => $posts,
            'currentStatus' => $status,
            'search' => $search,
            'totalCount' => Post::count(),
            'publishedCount' => Post::where('status', 'published')->count(),
            'draftCount' => Post::where('status', 'draft')->count(),
        ]);
    }

    /**
     * Show form for creating a new post.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.posts.create', [
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }

    /**
     * Store a newly created post.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:posts,slug'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'featured_image' => ['nullable', 'string'],
            'status' => ['required', 'in:published,draft'],
            'is_featured' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
            'tags_input' => ['nullable', 'string'],
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $isFeatured = $request->boolean('is_featured');
        $publishedAt = $validated['status'] === 'published' ? now() : null;

        $post = Post::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'featured_image' => $validated['featured_image'] ?? null,
            'status' => $validated['status'],
            'published_at' => $publishedAt,
            'is_featured' => $isFeatured,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
        ]);

        if (!empty($validated['categories'])) {
            $post->categories()->sync($validated['categories']);
        }

        // Process comma-separated tags
        if (!empty($validated['tags_input'])) {
            $tagNames = array_filter(array_map('trim', explode(',', $validated['tags_input'])));
            $tagIds = [];
            foreach ($tagNames as $name) {
                $tag = Tag::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
                $tagIds[] = $tag->id;
            }
            $post->tags()->sync($tagIds);
        }

        return redirect()->route('admin.posts.index')->with('status', 'Article created successfully!');
    }

    /**
     * Show form for editing a post.
     */
    public function edit(Post $post): View
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $selectedCategoryIds = $post->categories->pluck('id')->toArray();
        $tagsString = $post->tags->pluck('name')->implode(', ');

        return view('admin.posts.edit', [
            'post' => $post,
            'categories' => $categories,
            'tags' => $tags,
            'selectedCategoryIds' => $selectedCategoryIds,
            'tagsString' => $tagsString,
        ]);
    }

    /**
     * Update an existing post.
     */
    public function update(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:posts,slug,' . $post->id],
            'excerpt' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'featured_image' => ['nullable', 'string'],
            'status' => ['required', 'in:published,draft'],
            'is_featured' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
            'tags_input' => ['nullable', 'string'],
        ]);

        $publishedAt = $post->published_at;
        if ($validated['status'] === 'published' && empty($publishedAt)) {
            $publishedAt = now();
        }

        $post->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['slug']),
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'featured_image' => $validated['featured_image'] ?? null,
            'status' => $validated['status'],
            'published_at' => $publishedAt,
            'is_featured' => $request->boolean('is_featured'),
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
        ]);

        $post->categories()->sync($validated['categories'] ?? []);

        if (isset($validated['tags_input'])) {
            $tagNames = array_filter(array_map('trim', explode(',', $validated['tags_input'])));
            $tagIds = [];
            foreach ($tagNames as $name) {
                $tag = Tag::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
                $tagIds[] = $tag->id;
            }
            $post->tags()->sync($tagIds);
        }

        return redirect()->route('admin.posts.index')->with('status', 'Article updated successfully!');
    }

    /**
     * Delete a post.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();
        return redirect()->route('admin.posts.index')->with('status', 'Article removed.');
    }
}
