@if(! empty($data))
    <div class="rounded-sm border border-slate-300 bg-white shadow-sm">
        @include('themes.default.partials.card-title', ['title' => $widget->title])
        <div class="flex flex-wrap gap-2 p-4">
            @foreach($data as $tag)
                <a href="{{ $tag['url'] }}"
                    class="rounded border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-medium text-slate-600 transition hover:border-primary-600 hover:bg-primary-50 hover:text-primary-800">
                    {{ $tag['name'] }} <span class="opacity-60">({{ $tag['count'] }})</span>
                </a>
            @endforeach
        </div>
    </div>
@endif
