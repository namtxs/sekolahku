@if(! empty($data))
    <div class="rounded-sm border border-slate-300 bg-white shadow-sm">
        @include('themes.default.partials.card-title', ['title' => $widget->title])
        <ul class="divide-y divide-slate-100 p-4 text-sm">
            @foreach($data as $archive)
                <li>
                    <a href="{{ $archive['url'] }}"
                        class="group flex items-center justify-between gap-2 py-2.5 first:pt-0 last:pb-0">
                        <span class="text-slate-700 group-hover:text-primary-800">{{ $archive['label'] }}</span>
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-500 group-hover:bg-primary-100 group-hover:text-primary-800">{{ $archive['count'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif
