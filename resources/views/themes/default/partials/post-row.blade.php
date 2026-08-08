{{-- Baris berita horizontal (thumbnail kiri, teks kanan) untuk daftar. Variabel: $post --}}
@php($thumb = $post->getFirstMediaUrl('featured', 'thumb'))

<article class="group -mx-3 flex gap-4 rounded border-b border-slate-200 px-3 py-5 transition first:pt-3 last:border-0 hover:bg-slate-50 sm:gap-5">
    <a href="{{ route('posts.show', $post->slug) }}"
        class="relative block h-24 w-32 shrink-0 overflow-hidden rounded bg-slate-100 sm:h-32 sm:w-48">
        @if($thumb)
            <img src="{{ $thumb }}" alt="{{ $post->title }}" loading="lazy"
                class="size-full object-cover transition duration-300 group-hover:scale-[1.04]">
        @else
            <div class="flex size-full items-center justify-center bg-slate-100">
                <img src="{{ asset('images/logo.svg') }}" alt="" class="size-10 opacity-40">
            </div>
        @endif
    </a>
    <div class="min-w-0 flex-1">
        @if($post->category)
            <a href="{{ route('categories.show', $post->category->slug) }}"
                class="text-xs font-semibold uppercase tracking-wide text-primary-700 hover:text-primary-900">
                {{ $post->category->name }}
            </a>
        @endif
        <h3 class="mt-1 font-heading text-base font-bold leading-snug text-slate-900 sm:text-lg">
            <a href="{{ route('posts.show', $post->slug) }}" class="transition hover:text-primary-800">{{ $post->title }}</a>
        </h3>
        <p class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-400">
            <time datetime="{{ $post->published_at?->toDateString() }}" class="inline-flex items-center gap-1">
                <svg class="size-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z" clip-rule="evenodd"/></svg>
                {{ tanggal_id($post->published_at) }}
            </time>
            <span class="inline-flex items-center gap-1">
                <svg class="size-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/><path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd"/></svg>
                Dilihat {{ number_format($post->views, 0, ',', '.') }} kali
            </span>
        </p>
        @if(filled($post->excerpt))
            <p class="mt-2 hidden text-sm leading-relaxed text-slate-500 sm:line-clamp-2">{{ $post->excerpt }}</p>
        @endif
    </div>
</article>
