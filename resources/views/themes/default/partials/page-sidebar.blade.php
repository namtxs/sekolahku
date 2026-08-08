{{-- Sidebar halaman statis: widget aktif area "page_sidebar", sengaja lebih pendek dari sidebar berita. --}}
<aside class="space-y-8" aria-label="Sidebar halaman">
    @foreach($sidebarWidgets as $item)
        @includeIf('themes.default.partials.widgets.' . $item['widget']->type, [
            'widget' => $item['widget'],
            'data' => $item['data'],
        ])
    @endforeach
</aside>
