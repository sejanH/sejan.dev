{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<feed xmlns="http://www.w3.org/2005/Atom">
    <title>Sejan · Blog</title>
    <subtitle>Technology, Software Architecture &amp; Modern Engineering by Sejan</subtitle>
    <link href="{{ route('feed.atom') }}" rel="self" type="application/atom+xml" />
    <link href="{{ route('home') }}" rel="alternate" type="text/html" />
    <id>{{ route('home') }}/</id>
    <updated>{{ $lastUpdated }}</updated>
    <author>
        <name>S. M. Mominul Haque (Sejan)</name>
        <uri>{{ route('blog.about') }}</uri>
    </author>

    @foreach ($posts as $post)
        <entry>
            <title><![CDATA[{{ $post->title }}]]></title>
            <link href="{{ route('blog.show', $post->slug) }}" rel="alternate" type="text/html" />
            <id>{{ route('blog.show', $post->slug) }}</id>
            <updated>{{ $post->updated_at->toAtomString() }}</updated>
            <published>{{ $post->published_at ? $post->published_at->toAtomString() : $post->created_at->toAtomString() }}</published>
            <summary type="text"><![CDATA[{{ $post->excerpt }}]]></summary>
            <content type="html"><![CDATA[{!! $post->content !!}]]></content>
            <author>
                <name>{{ $post->user?->name ?? 'S. M. Mominul Haque (Sejan)' }}</name>
            </author>
            @foreach ($post->categories as $category)
                <category term="{{ $category->slug }}" label="{{ $category->name }}" />
            @endforeach
        </entry>
    @endforeach
</feed>
