{{-- CSS Filament v4 tidak memuat utility Tailwind, jadi widget ini memakai
     style scoped sendiri dengan variabel tema panel (--primary-*, --sk-*). --}}
<x-filament-widgets::widget>
    <x-filament::section heading="Aksi Cepat" description="Pintasan tugas yang paling sering dilakukan.">
        <style>
            .qa-grid { display: grid; gap: 0.625rem; grid-template-columns: 1fr; }
            @media (min-width: 640px) { .qa-grid { grid-template-columns: repeat(2, 1fr); } }
            @media (min-width: 1024px) { .qa-grid { grid-template-columns: repeat(3, 1fr); } }
            @media (min-width: 1280px) { .qa-grid { grid-template-columns: repeat(4, 1fr); } }
            .qa-item { display: flex; align-items: center; gap: 0.625rem; padding: 0.625rem; border-radius: 0.375rem; box-shadow: inset 0 0 0 1px var(--sk-line, #dbe4f0); transition: background-color 0.15s, box-shadow 0.15s; min-width: 0; }
            .qa-item:hover { background-color: #f4f8fd; box-shadow: inset 0 0 0 1px var(--primary-300); }
            .qa-icon { display: flex; align-items: center; justify-content: center; flex-shrink: 0; width: 2.25rem; height: 2.25rem; border-radius: 0.375rem; background-color: var(--sk-blue-soft, #eaf3ff); color: var(--sk-blue, #0f6fc7); }
            .qa-icon svg { width: 1.125rem; height: 1.125rem; }
            .qa-label { display: block; font-size: 0.8125rem; font-weight: 600; color: #0f172a; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .qa-item:hover .qa-label { color: var(--sk-blue, #0f6fc7); }
            .qa-desc { display: block; font-size: 0.6875rem; color: #64748b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .qa-text { min-width: 0; }
        </style>

        <div class="qa-grid">
            @foreach (\App\Filament\Widgets\QuickActions::actions() as $action)
                <a href="{{ $action['url'] }}" class="qa-item">
                    <span class="qa-icon">
                        <x-filament::icon :icon="$action['icon']" />
                    </span>
                    <span class="qa-text">
                        <span class="qa-label">{{ $action['label'] }}</span>
                        <span class="qa-desc">{{ $action['description'] }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
