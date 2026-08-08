@if(! empty($data))
    <div class="rounded-sm border border-slate-300 bg-white shadow-sm">
        @include('themes.default.partials.card-title', ['title' => $widget->title])
        <ol class="divide-y divide-slate-100 p-4">
            @foreach($data as $index => $post)
                <li class="flex gap-3 py-3 first:pt-0 last:pb-0">
                    <span class="flex size-7 shrink-0 items-center justify-center rounded border border-slate-200 bg-slate-50 text-xs font-bold text-primary-800">{{ $index + 1 }}</span>
                    <div class="min-w-0">
                        <a href="{{ $post['url'] }}"
                            class="line-clamp-2 text-sm font-semibold leading-snug text-slate-800 hover:text-primary-800">
                            {{ $post['title'] }}
                        </a>
                        <p class="mt-1 text-xs text-slate-400">{{ $post['date'] }} &middot; Dilihat {{ number_format($post['views'], 0, ',', '.') }} kali</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
@endif
