<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display category management panel with listing and quick-add form.
     */
    public function index(Request $request): View
    {
        $search = $request->query('q');

        $query = Category::with('parent')
            ->withCount('posts')
            ->orderBy('name');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $query->paginate(20)->withQueryString();
        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();

        return view('admin.taxonomies.categories', [
            'categories' => $categories,
            'parentCategories' => $parentCategories,
            'search' => $search,
            'totalCount' => Category::count(),
        ]);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);

        Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'parent_id' => !empty($validated['parent_id']) ? (int) $validated['parent_id'] : null,
        ]);

        return redirect()->route('admin.categories.index')->with('status', "Category '{$validated['name']}' created successfully.");
    }

    /**
     * Show form for editing a category.
     */
    public function edit(Category $category): View
    {
        $parentCategories = Category::where('id', '!=', $category->id)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('admin.taxonomies.category-edit', [
            'category' => $category,
            'parentCategories' => $parentCategories,
        ]);
    }

    /**
     * Update an existing category.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug,' . $category->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]);

        if (!empty($validated['parent_id']) && (int) $validated['parent_id'] === $category->id) {
            $validated['parent_id'] = null;
        }

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['slug']),
            'description' => $validated['description'] ?? null,
            'parent_id' => !empty($validated['parent_id']) ? (int) $validated['parent_id'] : null,
        ]);

        return redirect()->route('admin.categories.index')->with('status', "Category '{$category->name}' updated successfully.");
    }

    /**
     * Delete a category.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $name = $category->name;

        // Reset child categories parent_id
        Category::where('parent_id', $category->id)->update(['parent_id' => null]);

        // Detach related posts
        $category->posts()->detach();

        $category->delete();

        return redirect()->route('admin.categories.index')->with('status', "Category '{$name}' deleted successfully.");
    }
}
