{{-- Sidebar: widget aktif area "sidebar" dari database, dikelola via panel admin (menu Widget). --}}
<aside class="space-y-8" aria-label="Sidebar">
    @foreach($sidebarWidgets as $item)
        @includeIf('themes.default.partials.widgets.' . $item['widget']->type, [
            'widget' => $item['widget'],
            'data' => $item['data'],
        ])
    @endforeach
</aside>
