<?php

namespace App\Services\WordPress;

use Illuminate\Support\Str;

class WordPressContentTransformer
{
    /**
     * Clean raw WordPress post content.
     */
    public function transform(string $content, ?string $siteUrl = null): string
    {
        if (empty($content)) {
            return '';
        }

        // 1. Remove Gutenberg block comments: <!-- wp:... --> and <!-- /wp:... -->
        $cleaned = preg_replace('/<!--\s*\/?wp:[^\>]*-->/s', '', $content);

        // 2. Normalize newlines
        $cleaned = str_replace(["\r\n", "\r"], "\n", $cleaned);

        // 3. Convert WordPress caption shortcodes [caption id="..." align="..." width="..."]...[/caption]
        $cleaned = preg_replace_callback(
            '/\[caption[^\]]*\](.*?)\[\/caption\]/s',
            function ($matches) {
                return '<figure class="my-6 rounded-2xl overflow-hidden">' . $matches[1] . '</figure>';
            },
            $cleaned
        );

        // 4. Remove leftover generic WordPress shortcodes if any
        $cleaned = preg_replace('/\[(\/?[a-zA-Z0-9_\-]+)([^\]]*)\]/', '', $cleaned);

        // 5. Clean consecutive empty paragraph tags
        $cleaned = preg_replace('/<p>\s*(<br\s*\/?>)?\s*<\/p>/i', '', $cleaned);

        // 6. Fix broken encoded characters
        $cleaned = html_entity_decode($cleaned, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim($cleaned);
    }

    /**
     * Generate an excerpt from content.
     */
    public function makeExcerpt(string $content, int $limit = 180): string
    {
        $plain = strip_tags($this->transform($content));
        return Str::limit(trim(preg_replace('/\s+/', ' ', $plain)), $limit);
    }
}
