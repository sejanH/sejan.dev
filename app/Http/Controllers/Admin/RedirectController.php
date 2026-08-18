<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Redirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RedirectController extends Controller
{
    /**
     * Display listing of 301 redirects.
     */
    public function index(Request $request): View
    {
        $search = $request->query('q');

        $query = Redirect::latest('hits');

        if (!empty($search)) {
            $query->where('source_url', 'like', "%{$search}%")
                  ->orWhere('target_url', 'like', "%{$search}%");
        }

        $redirects = $query->paginate(20)->withQueryString();

        return view('admin.redirects.index', [
            'redirects' => $redirects,
            'search' => $search,
            'totalRedirects' => Redirect::count(),
            'totalHits' => Redirect::sum('hits'),
        ]);
    }

    /**
     * Store a manual redirect.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'source_url' => ['required', 'string', 'max:255', 'unique:redirects,source_url'],
            'target_url' => ['required', 'string', 'max:255'],
            'status_code' => ['nullable', 'in:301,302'],
        ]);

        Redirect::create([
            'source_url' => '/' . ltrim($validated['source_url'], '/'),
            'target_url' => '/' . ltrim($validated['target_url'], '/'),
            'status_code' => $validated['status_code'] ?? 301,
        ]);

        return redirect()->route('admin.redirects.index')->with('status', 'Redirect rule added successfully!');
    }

    /**
     * Delete a redirect rule.
     */
    public function destroy(Redirect $redirect): RedirectResponse
    {
        $redirect->delete();
        return redirect()->route('admin.redirects.index')->with('status', 'Redirect rule deleted.');
    }
}
