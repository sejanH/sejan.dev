<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IndexNowService
{
    /**
     * Submit a single URL or array of URLs to the IndexNow protocol (Bing, Yandex, etc.)
     *
     * @param string|array $urls
     * @return bool
     */
    public static function submit(string|array $urls): bool
    {
        $urlList = (array) $urls;

        if (empty($urlList)) {
            return false;
        }

        $host = parse_url(config('app.url', 'https://sejan.dev'), PHP_URL_HOST) ?: 'sejan.dev';
        if ($host === 'localhost' || empty($host)) {
            $host = 'sejan.dev';
        }
        $key = config('services.indexnow.key', 'sejan_indexnow_key');
        $keyLocation = config('services.indexnow.key_location', "https://{$host}/{$key}.txt");

        try {
            $response = Http::timeout(5)
                ->withHeaders(['Content-Type' => 'application/json; charset=utf-8'])
                ->post('https://api.indexnow.org/indexnow', [
                    'host' => $host,
                    'key' => $key,
                    'keyLocation' => $keyLocation,
                    'urlList' => array_values($urlList),
                ]);

            if ($response->successful()) {
                Log::info('IndexNow: Successfully submitted URLs', ['urls' => $urlList, 'status' => $response->status()]);
                return true;
            }

            Log::warning('IndexNow: Submission returned non-success response', [
                'status' => $response->status(),
                'body' => $response->body(),
                'urls' => $urlList,
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('IndexNow: Failed to submit URLs to search engines', [
                'error' => $e->getMessage(),
                'urls' => $urlList,
            ]);

            return false;
        }
    }
}
