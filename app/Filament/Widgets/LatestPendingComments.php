<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestPendingComments extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->can('comments.view_any') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Komentar Menunggu Moderasi')
            ->query(
                Comment::query()
                    ->where('status', 'pending')
                    ->with('post')
                    ->latest()
                    ->limit(5),
            )
            ->paginated(false)
            ->emptyStateIcon(Heroicon::OutlinedCheckCircle)
            ->emptyStateHeading('Tidak ada komentar menunggu')
            ->emptyStateDescription('Semua komentar sudah dimoderasi. Kerja bagus!')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama'),
                TextColumn::make('body')
                    ->label('Komentar')
                    ->limit(60)
                    ->wrap(),
                TextColumn::make('post.title')
                    ->label('Tulisan')
                    ->limit(40),
                TextColumn::make('created_at')
                    ->label('Dikirim')
                    ->since(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Comment $record) => $record->update(['status' => 'approved'])),
                Action::make('spam')
                    ->label('Spam')
                    ->icon(Heroicon::OutlinedNoSymbol)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Comment $record) => $record->update(['status' => 'spam'])),
            ]);
    }
}
