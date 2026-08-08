@if(filled($widget->content))
    <div class="rounded-sm border border-slate-300 bg-white shadow-sm">
        @include('themes.default.partials.card-title', ['title' => $widget->title])
        <div class="prose prose-sm prose-slate max-w-none p-4">
            {!! $widget->content !!}
        </div>
    </div>
@endif
