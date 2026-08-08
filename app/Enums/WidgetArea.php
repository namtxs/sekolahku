<?php

namespace App\Enums;

enum WidgetArea: string
{
    case Sidebar = 'sidebar';
    case PageSidebar = 'page_sidebar';

    public function label(): string
    {
        return match ($this) {
            self::Sidebar => 'Sidebar Berita',
            self::PageSidebar => 'Sidebar Halaman Statis',
        };
    }

    /**
     * Opsi untuk Select Filament: [value => label].
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $area): array => [$area->value => $area->label()])
            ->all();
    }
}
