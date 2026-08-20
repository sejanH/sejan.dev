<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TagController extends Controller
{
    /**
     * Display tags management panel with listing and quick-add form.
     */
    public function index(Request $request): View
    {
        $search = $request->query('q');

        $query = Tag::withCount('posts')
            ->orderBy('name');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tags = $query->paginate(25)->withQueryString();

        return view('admin.taxonomies.tags', [
            'tags' => $tags,
            'search' => $search,
            'totalCount' => Tag::count(),
        ]);
    }

    /**
     * Store a newly created tag.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:tags,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);

        Tag::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('admin.tags.index')->with('status', "Tag '{$validated['name']}' created successfully.");
    }

    /**
     * Show form for editing a tag.
     */
    public function edit(Tag $tag): View
    {
        return view('admin.taxonomies.tag-edit', [
            'tag' => $tag,
        ]);
    }

    /**
     * Update an existing tag.
     */
    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:tags,slug,' . $tag->id],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $tag->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['slug']),
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('admin.tags.index')->with('status', "Tag '{$tag->name}' updated successfully.");
    }

    /**
     * Delete a tag.
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        $name = $tag->name;

        // Detach from all posts
        $tag->posts()->detach();

        $tag->delete();

        return redirect()->route('admin.tags.index')->with('status', "Tag '{$name}' deleted successfully.");
    }
}
