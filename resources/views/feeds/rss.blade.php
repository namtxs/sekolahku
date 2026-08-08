{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ $settings->site_name ?? config('app.name') }}</title>
        <link>{{ route('home') }}</link>
        <atom:link href="{{ route('feed') }}" rel="self" type="application/rss+xml"/>
        <description>{{ $settings->meta_description ?? '' }}</description>
        <language>id</language>
        <lastBuildDate>{{ ($posts->first()?->published_at ?? now())->toRfc2822String() }}</lastBuildDate>
        @foreach($posts as $post)
        <item>
            <title>{{ $post->title }}</title>
            <link>{{ route('posts.show', $post->slug) }}</link>
            <guid isPermaLink="true">{{ route('posts.show', $post->slug) }}</guid>
            <pubDate>{{ $post->published_at->toRfc2822String() }}</pubDate>
            <description>{{ \Illuminate\Support\Str::limit(trim(strip_tags($post->excerpt ?: $post->body)), 300) }}</description>
        </item>
        @endforeach
    </channel>
</rss>
