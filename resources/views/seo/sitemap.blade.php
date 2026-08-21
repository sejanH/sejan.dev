{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    <!-- Static Main Pages -->
    <url>
        <loc>{{ route('home') }}</loc>
        <lastmod>{{ $latestMod }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('blog.about') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>{{ route('blog.privacy') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>{{ route('blog.terms') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>{{ route('blog.contact') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>

    <!-- Published Articles -->
    @foreach ($posts as $post)
        <url>
            <loc>{{ route('blog.show', $post->slug) }}</loc>
            <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>{{ $post->is_featured ? '0.95' : '0.85' }}</priority>
            @if ($post->featured_image)
                <image:image>
                    <image:loc>{{ $post->featured_image }}</image:loc>
                    <image:title>{{ $post->title }}</image:title>
                </image:image>
            @endif
        </url>
    @endforeach

    <!-- Categories -->
    @foreach ($categories as $category)
        <url>
            <loc>{{ route('blog.category', $category->slug) }}</loc>
            @if ($category->posts->first())
                <lastmod>{{ $category->posts->first()->updated_at->toAtomString() }}</lastmod>
            @endif
            <changefreq>weekly</changefreq>
            <priority>0.6</priority>
        </url>
    @endforeach

    <!-- Tags -->
    @foreach ($tags as $tag)
        <url>
            <loc>{{ route('blog.tag', $tag->slug) }}</loc>
            @if ($tag->posts->first())
                <lastmod>{{ $tag->posts->first()->updated_at->toAtomString() }}</lastmod>
            @endif
            <changefreq>weekly</changefreq>
            <priority>0.5</priority>
        </url>
    @endforeach
</urlset>
