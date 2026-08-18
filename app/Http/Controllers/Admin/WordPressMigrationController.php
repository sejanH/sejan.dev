<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Redirect;
use App\Models\Tag;
use App\Models\WpSetting;
use App\Services\WordPress\WordPressDatabaseMigrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WordPressMigrationController extends Controller
{
    /**
     * Show WordPress Migration Control Center.
     */
    public function index(WordPressDatabaseMigrator $migrator): View
    {
        $settings = [
            'driver' => WpSetting::get('wp_driver', config('wordpress.driver', 'database')),
            'host' => WpSetting::get('wp_db_host', config('wordpress.database.host', '127.0.0.1')),
            'port' => WpSetting::get('wp_db_port', config('wordpress.database.port', '3306')),
            'database' => WpSetting::get('wp_db_database', config('wordpress.database.database', 'wordpress_db')),
            'username' => WpSetting::get('wp_db_username', config('wordpress.database.username', 'root')),
            'password' => WpSetting::get('wp_db_password', config('wordpress.database.password', '')),
            'prefix' => WpSetting::get('wp_table_prefix', config('wordpress.database.prefix', 'wp_')),
            'api_url' => WpSetting::get('wp_api_url', config('wordpress.api.url', 'https://blog.example.com/wp-json/wp/v2')),
            'download_media' => (bool) WpSetting::get('wp_download_media', config('wordpress.media.download', true)),
        ];

        $migrationStats = [
            'total_posts' => Post::count(),
            'wp_posts' => Post::whereNotNull('wp_id')->count(),
            'total_categories' => Category::count(),
            'total_tags' => Tag::count(),
            'total_redirects' => Redirect::count(),
            'last_migration_at' => WpSetting::get('last_migration_at', 'Never'),
            'last_stats' => WpSetting::get('last_migration_stats', null),
        ];

        $connectionStatus = $migrator->testConnection($settings);

        return view('admin.wordpress.index', [
            'settings' => $settings,
            'stats' => $migrationStats,
            'connectionStatus' => $connectionStatus,
        ]);
    }

    /**
     * Save WordPress Migration Settings.
     */
    public function saveSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'driver' => ['required', 'string', 'in:database,rest_api,xml'],
            'host' => ['required', 'string'],
            'port' => ['required', 'string'],
            'database' => ['required', 'string'],
            'username' => ['required', 'string'],
            'password' => ['nullable', 'string'],
            'prefix' => ['required', 'string'],
            'api_url' => ['nullable', 'string'],
            'download_media' => ['nullable', 'boolean'],
        ]);

        WpSetting::set('wp_driver', $validated['driver']);
        WpSetting::set('wp_db_host', $validated['host']);
        WpSetting::set('wp_db_port', $validated['port']);
        WpSetting::set('wp_db_database', $validated['database']);
        WpSetting::set('wp_db_username', $validated['username']);
        if (isset($validated['password'])) {
            WpSetting::set('wp_db_password', $validated['password']);
        }
        WpSetting::set('wp_table_prefix', $validated['prefix']);
        WpSetting::set('wp_api_url', $validated['api_url'] ?? '');
        WpSetting::set('wp_download_media', $request->boolean('download_media'));

        return redirect()->route('admin.wordpress.index')
            ->with('status', 'WordPress migration settings saved successfully!');
    }

    /**
     * Test WordPress Database Connection.
     */
    public function testConnection(Request $request, WordPressDatabaseMigrator $migrator): JsonResponse|RedirectResponse
    {
        $settings = [
            'host' => WpSetting::get('wp_db_host', config('wordpress.database.host', '127.0.0.1')),
            'port' => WpSetting::get('wp_db_port', config('wordpress.database.port', '3306')),
            'database' => WpSetting::get('wp_db_database', config('wordpress.database.database', 'wordpress_db')),
            'username' => WpSetting::get('wp_db_username', config('wordpress.database.username', 'root')),
            'password' => WpSetting::get('wp_db_password', config('wordpress.database.password', '')),
            'prefix' => WpSetting::get('wp_table_prefix', config('wordpress.database.prefix', 'wp_')),
        ];

        $result = $migrator->testConnection($settings);

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        $type = $result['success'] ? 'status' : 'error';
        return redirect()->route('admin.wordpress.index')->with($type, $result['message']);
    }

    /**
     * Execute WordPress Migration.
     */
    public function runMigration(Request $request, WordPressDatabaseMigrator $migrator): RedirectResponse
    {
        $settings = [
            'host' => WpSetting::get('wp_db_host', config('wordpress.database.host', '127.0.0.1')),
            'port' => WpSetting::get('wp_db_port', config('wordpress.database.port', '3306')),
            'database' => WpSetting::get('wp_db_database', config('wordpress.database.database', 'wordpress_db')),
            'username' => WpSetting::get('wp_db_username', config('wordpress.database.username', 'root')),
            'password' => WpSetting::get('wp_db_password', config('wordpress.database.password', '')),
            'prefix' => WpSetting::get('wp_table_prefix', config('wordpress.database.prefix', 'wp_')),
        ];

        try {
            $stats = $migrator->migrateAll($settings);

            $msg = "Migration successful! Ingested {$stats['posts']} posts, {$stats['categories']} categories, {$stats['tags']} tags, and generated {$stats['redirects']} 301 SEO redirects.";
            return redirect()->route('admin.wordpress.index')->with('status', $msg);
        } catch (\Exception $e) {
            return redirect()->route('admin.wordpress.index')->with('error', 'Migration failed: ' . $e->getMessage());
        }
    }
}
