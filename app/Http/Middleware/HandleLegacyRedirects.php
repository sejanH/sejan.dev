<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleLegacyRedirects
{
    /**
     * Handle an incoming request for legacy WordPress permalinks.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $uri = '/' . ltrim($request->getRequestUri(), '/');

        // Look up direct URI or path
        $path = '/' . ltrim($request->path(), '/');

        $redirect = Redirect::where('source_url', $uri)
            ->orWhere('source_url', $path)
            ->orWhere('source_url', rtrim($path, '/') . '/')
            ->orWhere('source_url', rtrim($uri, '/') . '/')
            ->first();

        if ($redirect) {
            $redirect->recordHit();
            return redirect($redirect->target_url, $redirect->status_code);
        }

        return $next($request);
    }
}
