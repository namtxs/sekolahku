<div class="rounded-sm border border-slate-300 bg-white shadow-sm">
    @include('themes.default.partials.card-title', ['title' => $widget->title])
    <form action="{{ route('posts.search') }}" method="get" class="flex gap-2 p-4" role="search">
        <label for="widget-q-{{ $widget->id }}" class="sr-only">Cari berita</label>
        <input type="search" id="widget-q-{{ $widget->id }}" name="q" value="{{ request('q') }}" placeholder="Cari berita..."
            class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-primary-600 focus:outline-none focus:ring-1 focus:ring-primary-600">
        <button type="submit" aria-label="Cari"
            class="rounded-sm bg-primary-600 px-3.5 py-2 text-white transition hover:bg-primary-800">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
        </button>
    </form>
</div>
