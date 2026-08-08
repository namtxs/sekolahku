@if(! empty($data))
    <div class="rounded-sm border border-slate-300 bg-white shadow-sm">
        @include('themes.default.partials.card-title', ['title' => $widget->title])
        <div class="divide-y divide-slate-100 p-4">
            @foreach($data as $post)
                <article class="group flex gap-3 py-3 first:pt-0 last:pb-0">
                    <a href="{{ $post['url'] }}" class="block h-12 w-16 shrink-0 overflow-hidden rounded bg-slate-100">
                        @if($post['thumb'])
                            <img src="{{ $post['thumb'] }}" alt="{{ $post['title'] }}" loading="lazy" class="size-full object-cover transition duration-300 group-hover:scale-105">
                        @else
                            <span class="flex size-full items-center justify-center">
                                <img src="{{ asset('images/logo.svg') }}" alt="" class="size-6 opacity-40">
                            </span>
                        @endif
                    </a>
                    <div class="min-w-0">
                        <a href="{{ $post['url'] }}"
                            class="line-clamp-2 text-sm font-semibold leading-snug text-slate-800 hover:text-primary-800">
                            {{ $post['title'] }}
                        </a>
                        <p class="mt-1 text-xs text-slate-400">{{ $post['date'] }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
@endif
