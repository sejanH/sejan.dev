<?php

use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\ContactMessageController as AdminMessageController;
use App\Http\Controllers\Admin\MediaController as AdminMediaController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\RedirectController as AdminRedirectController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\WordPressMigrationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedAndSeoController;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

/*
|--------------------------------------------------------------------------
| Public Blog Platform Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [BlogController::class, 'index'])->name('home');
Route::get('/posts/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->middleware(ProtectAgainstSpam::class)->name('comments.store');
Route::get('/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/tag/{slug}', [BlogController::class, 'tag'])->name('blog.tag');
Route::get('/about', [BlogController::class, 'about'])->name('blog.about');
Route::get('/contact', [BlogController::class, 'contact'])->name('blog.contact');
Route::post('/contact', [BlogController::class, 'handleContact'])->middleware(ProtectAgainstSpam::class)->name('blog.contact.send');

/*
|--------------------------------------------------------------------------
| SEO Sitemaps, RSS/Atom Feeds & Robots.txt
|--------------------------------------------------------------------------
*/
Route::get('/sitemap.xml', [FeedAndSeoController::class, 'sitemap'])->name('sitemap.xml');
Route::get('/feed', [FeedAndSeoController::class, 'rss'])->name('feed.rss');
Route::get('/rss.xml', [FeedAndSeoController::class, 'rss'])->name('feed.rss.xml');
Route::get('/feed/atom', [FeedAndSeoController::class, 'atom'])->name('feed.atom');
Route::get('/robots.txt', [FeedAndSeoController::class, 'robots'])->name('robots.txt');

/*
|--------------------------------------------------------------------------
| Guest Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

// Explicitly Disabled Registration Routes
Route::match(['get', 'post'], '/register', function () {
    abort(403, 'Public registration is disabled. Only seeded administrators are allowed.');
})->name('register');

/*
|--------------------------------------------------------------------------
| Authenticated Admin Panel Routes (Strictly under /admin prefix)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::redirect('/dashboard', '/admin');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('admin')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

        // Blog Posts Management (/admin/posts)
        Route::put('/posts/{id}/restore', [AdminPostController::class, 'restore'])->name('admin.posts.restore');
        Route::delete('/posts/{id}/force-delete', [AdminPostController::class, 'forceDelete'])->name('admin.posts.forceDelete');
        Route::resource('posts', AdminPostController::class, ['as' => 'admin']);

        // Media Library (/admin/media)
        Route::get('/media', [AdminMediaController::class, 'index'])->name('admin.media.index');
        Route::post('/media', [AdminMediaController::class, 'store'])->name('admin.media.store');
        Route::put('/media/{medium}', [AdminMediaController::class, 'update'])->name('admin.media.update');
        Route::delete('/media/{medium}', [AdminMediaController::class, 'destroy'])->name('admin.media.destroy');

        // Comments Moderation (/admin/comments)
        Route::get('/comments', [AdminCommentController::class, 'index'])->name('admin.comments.index');
        Route::put('/comments/{comment}/status', [AdminCommentController::class, 'updateStatus'])->name('admin.comments.status');
        Route::post('/comments/{comment}/reply', [AdminCommentController::class, 'reply'])->name('admin.comments.reply');
        Route::delete('/comments/{comment}', [AdminCommentController::class, 'destroy'])->name('admin.comments.destroy');

        // WordPress Migration Engine (/admin/wordpress)
        Route::get('/wordpress', [WordPressMigrationController::class, 'index'])->name('admin.wordpress.index');
        Route::post('/wordpress/settings', [WordPressMigrationController::class, 'saveSettings'])->name('admin.wordpress.settings');
        Route::post('/wordpress/test', [WordPressMigrationController::class, 'testConnection'])->name('admin.wordpress.test');
        Route::post('/wordpress/migrate', [WordPressMigrationController::class, 'runMigration'])->name('admin.wordpress.migrate');

        // 301 SEO Redirects Management (/admin/redirects)
        Route::get('/redirects', [AdminRedirectController::class, 'index'])->name('admin.redirects.index');
        Route::post('/redirects', [AdminRedirectController::class, 'store'])->name('admin.redirects.store');
        Route::delete('/redirects/{redirect}', [AdminRedirectController::class, 'destroy'])->name('admin.redirects.destroy');

        // Contact Messages Inbox (/admin/messages)
        Route::get('/messages', [AdminMessageController::class, 'index'])->name('admin.messages.index');
        Route::get('/messages/{message}', [AdminMessageController::class, 'show'])->name('admin.messages.show');
        Route::put('/messages/{message}/toggle-read', [AdminMessageController::class, 'toggleRead'])->name('admin.messages.toggle');
        Route::delete('/messages/{message}', [AdminMessageController::class, 'destroy'])->name('admin.messages.destroy');

        // User & Role Management (/admin/users)
        Route::middleware('role:admin')->group(function () {
            Route::resource('users', AdminUserController::class, ['as' => 'admin']);
        });
    });
});

/*
|--------------------------------------------------------------------------
| Legacy WordPress 301 Redirect Fallback
|--------------------------------------------------------------------------
*/
Route::fallback(function (\Illuminate\Http\Request $request) {
    $uri = '/' . ltrim($request->getRequestUri(), '/');
    $path = '/' . ltrim($request->path(), '/');

    $redirect = \App\Models\Redirect::where('source_url', $uri)
        ->orWhere('source_url', $path)
        ->orWhere('source_url', rtrim($path, '/') . '/')
        ->orWhere('source_url', rtrim($uri, '/') . '/')
        ->first();

    if ($redirect) {
        $redirect->recordHit();
        return redirect($redirect->target_url, $redirect->status_code);
    }

    abort(404);
});

