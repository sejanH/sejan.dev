<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Redirect;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // System information & migration stats
        $systemInfo = [
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'db_driver' => config('database.default'),
            'db_name' => config('database.connections.' . config('database.default') . '.database'),
            'registration_enabled' => false,
            'wp_driver' => config('wordpress.driver', env('WP_MIGRATION_DRIVER', 'database')),
            'wp_db_database' => config('wordpress.database.database', env('WP_DB_DATABASE', 'wordpress_db')),
            'wp_api_url' => config('wordpress.api_url', env('WP_API_URL', 'Not configured')),
            'wp_media_download' => (bool) env('WP_DOWNLOAD_MEDIA', true),
        ];

        $stats = [
            'total_admins' => User::where('is_admin', true)->count(),
            'migrated_posts' => Post::count(),
            'migrated_categories' => Category::count(),
            'migrated_tags' => Tag::count(),
            'migrated_media' => Post::whereNotNull('featured_image')->count(),
            'active_redirects' => Redirect::count(),
        ];

        $recentPosts = Post::with(['categories', 'user'])
            ->latest('created_at')
            ->take(5)
            ->get();

        $recentActivities = [
            [
                'title' => 'Admin Initialized',
                'description' => 'Administrator account ' . $user->email . ' authenticated.',
                'time' => now()->diffForHumans(),
                'type' => 'success',
            ],
            [
                'title' => 'Public Registration Disabled',
                'description' => 'Locked down. Only seeded admin has system access.',
                'time' => now()->diffForHumans(),
                'type' => 'info',
            ],
            [
                'title' => 'Migration Engine Standby',
                'description' => 'Direct DB connection ready at ' . ($systemInfo['wp_db_database'] ?: 'wp_db'),
                'time' => now()->diffForHumans(),
                'type' => 'warning',
            ],
        ];

        return view('dashboard', [
            'user' => $user,
            'systemInfo' => $systemInfo,
            'stats' => $stats,
            'recentPosts' => $recentPosts,
            'recentActivities' => $recentActivities,
        ]);
    }
}
