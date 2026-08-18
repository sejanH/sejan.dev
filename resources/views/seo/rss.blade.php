{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<rss version="2.0"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>Sejan · Blog</title>
        <link>{{ route('home') }}</link>
        <description>Technology, Software Architecture &amp; Modern Engineering by Sejan</description>
        <language>en-US</language>
        <lastBuildDate>{{ $lastBuildDate }}</lastBuildDate>
        <atom:link href="{{ route('feed.rss') }}" rel="self" type="application/rss+xml" />

        @foreach ($posts as $post)
            <item>
                <title><![CDATA[{{ $post->title }}]]></title>
                <link>{{ route('blog.show', $post->slug) }}</link>
                <guid isPermaLink="true">{{ route('blog.show', $post->slug) }}</guid>
                <dc:creator><![CDATA[{{ $post->user?->name ?? 'S. M. Mominul Haque (Sejan)' }}]]></dc:creator>
                <pubDate>{{ $post->published_at ? $post->published_at->toRfc2822String() : $post->created_at->toRfc2822String() }}</pubDate>
                <description><![CDATA[{{ $post->excerpt }}]]></description>
                <content:encoded><![CDATA[{!! $post->content !!}]]></content:encoded>
                @foreach ($post->categories as $category)
                    <category><![CDATA[{{ $category->name }}]]></category>
                @endforeach
                @if ($post->featured_image)
                    <enclosure url="{{ $post->featured_image }}" type="image/jpeg" length="0" />
                @endif
            </item>
        @endforeach
    </channel>
</rss>
