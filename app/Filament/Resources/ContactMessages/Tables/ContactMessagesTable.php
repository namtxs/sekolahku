<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use App\Models\ContactMessage;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_read')
                    ->label('Dibaca')
                    ->boolean(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('subject')
                    ->label('Subjek')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('created_at')
                    ->label('Diterima')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateIcon(Heroicon::OutlinedEnvelope)
            ->emptyStateHeading('Belum ada pesan masuk')
            ->emptyStateDescription('Pesan dari formulir kontak di situs publik akan muncul di sini.')
            ->filters([
                TernaryFilter::make('is_read')
                    ->label('Status Baca'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalHeading(fn (ContactMessage $record): string => 'Pesan: '.$record->subject)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama'),
                        TextEntry::make('email')
                            ->label('Email'),
                        TextEntry::make('phone')
                            ->label('Telepon')
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->label('Diterima')
                            ->dateTime('d M Y H:i'),
                        TextEntry::make('subject')
                            ->label('Subjek')
                            ->columnSpanFull(),
                        TextEntry::make('message')
                            ->label('Pesan')
                            ->columnSpanFull(),
                    ])
                    ->mutateRecordDataUsing(function (array $data, ContactMessage $record): array {
                        // Membuka modal sekaligus menandai pesan sebagai dibaca.
                        if (! $record->is_read) {
                            $record->update(['is_read' => true]);
                        }

                        return $data;
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markRead')
                        ->label('Tandai Dibaca')
                        ->icon(Heroicon::OutlinedEnvelopeOpen)
                        ->color('success')
                        ->deselectRecordsAfterCompletion()
                        ->action(fn (Collection $records) => $records->each->update(['is_read' => true])),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
