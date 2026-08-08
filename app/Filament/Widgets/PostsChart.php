<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class PostsChart extends ChartWidget
{
    // Chart tampil setelah hal-hal yang butuh tindakan (komentar pending).
    protected static ?int $sort = 4;

    protected ?string $heading = 'Berita per Bulan';

    protected ?string $description = '6 bulan terakhir';

    public static function canView(): bool
    {
        return auth()->user()?->can('posts.view_any') ?? false;
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        foreach (range(5, 0) as $monthsAgo) {
            $month = Carbon::now()->subMonthsNoOverflow($monthsAgo);

            $labels[] = $month->translatedFormat('M Y');
            $data[] = Post::query()
                ->posts()
                ->whereBetween('created_at', [
                    $month->copy()->startOfMonth(),
                    $month->copy()->endOfMonth(),
                ])
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Berita',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
