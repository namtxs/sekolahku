@php($links = collect($widget->settings['links'] ?? [])->filter(fn ($link) => filled($link['label'] ?? null) && filled($link['url'] ?? null)))
@if($links->isNotEmpty())
    <div class="rounded-sm border border-slate-300 bg-white shadow-sm">
        @include('themes.default.partials.card-title', ['title' => $widget->title])
        <ul class="divide-y divide-slate-100 p-4 text-sm">
            @foreach($links as $link)
                <li>
                    <a href="{{ $link['url'] }}" @if(str_starts_with($link['url'], 'http')) target="_blank" rel="noopener" @endif
                        class="group flex items-center gap-2 py-2.5 text-slate-700 first:pt-0 last:pb-0 hover:text-primary-800">
                        <svg class="size-3 shrink-0 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                        {{ $link['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif
