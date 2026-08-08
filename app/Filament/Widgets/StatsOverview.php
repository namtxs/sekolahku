<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Comments\CommentResource;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\Posts\PostResource;
use App\Models\Comment;
use App\Models\ContactMessage;
use App\Models\Post;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Ringkasan Konten';

    protected ?string $description = 'Statistik konten dan interaksi pengunjung situs.';

    public static function canView(): bool
    {
        return auth()->user()?->can('posts.view_any') ?? false;
    }

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        $user = auth()->user();

        // Kelas sk-stat-* memberi blok ikon berwarna + aksen tepi (theme.css).
        return [
            Stat::make('Berita', Post::query()->posts()->count())
                ->description('Total berita di situs')
                ->icon(Heroicon::OutlinedNewspaper)
                ->descriptionIcon(Heroicon::OutlinedNewspaper)
                ->chart($this->monthlyPostCounts())
                ->url(PostResource::getUrl())
                ->color('primary')
                ->extraAttributes(['class' => 'sk-stat sk-stat-blue']),
            Stat::make('Halaman', Post::query()->pages()->count())
                ->description('Total halaman statis')
                ->icon(Heroicon::OutlinedDocumentText)
                ->descriptionIcon(Heroicon::OutlinedDocumentText)
                ->color('info')
                ->extraAttributes(['class' => 'sk-stat sk-stat-indigo']),
            Stat::make('Total Dilihat', number_format((int) Post::query()->sum('views'), 0, ',', '.'))
                ->description('Akumulasi tayangan konten')
                ->icon(Heroicon::OutlinedEye)
                ->descriptionIcon(Heroicon::OutlinedEye)
                ->color('success')
                ->extraAttributes(['class' => 'sk-stat sk-stat-green']),
            Stat::make('Komentar Menunggu', Comment::query()->where('status', 'pending')->count())
                ->description('Perlu moderasi — klik untuk meninjau')
                ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                ->descriptionIcon(Heroicon::OutlinedChatBubbleLeftRight)
                ->url($user?->can('comments.view_any') ? CommentResource::getUrl() : null)
                ->color('warning')
                ->extraAttributes(['class' => 'sk-stat sk-stat-amber']),
            Stat::make('Pesan Belum Dibaca', ContactMessage::query()->where('is_read', false)->count())
                ->description('Pesan masuk baru — klik untuk membaca')
                ->icon(Heroicon::OutlinedEnvelope)
                ->descriptionIcon(Heroicon::OutlinedEnvelope)
                ->url($user?->can('settings.view_any') ? ContactMessageResource::getUrl() : null)
                ->color('danger')
                ->extraAttributes(['class' => 'sk-stat sk-stat-red']),
            Stat::make('Komentar Disetujui', Comment::query()->where('status', 'approved')->count())
                ->description('Tampil di situs publik')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->descriptionIcon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->extraAttributes(['class' => 'sk-stat sk-stat-green']),
        ];
    }

    /**
     * Jumlah berita per bulan (6 bulan terakhir) untuk chart mini.
     *
     * @return array<int, int>
     */
    protected function monthlyPostCounts(): array
    {
        return collect(range(5, 0))
            ->map(function (int $monthsAgo): int {
                $month = Carbon::now()->subMonthsNoOverflow($monthsAgo);

                return Post::query()
                    ->posts()
                    ->whereBetween('created_at', [
                        $month->copy()->startOfMonth(),
                        $month->copy()->endOfMonth(),
                    ])
                    ->count();
            })
            ->all();
    }
}
