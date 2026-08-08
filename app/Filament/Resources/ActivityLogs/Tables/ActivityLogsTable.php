<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use App\Filament\Resources\ActivityLogs\ActivityLogResource;
use App\Models\User;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity;

class ActivityLogsTable
{
    /** @var array<string, string> */
    protected const EVENT_LABELS = [
        'created' => 'Dibuat',
        'updated' => 'Diubah',
        'deleted' => 'Dihapus',
        'restored' => 'Dipulihkan',
    ];

    /** @var array<string, string> */
    protected const EVENT_COLORS = [
        'created' => 'success',
        'updated' => 'info',
        'deleted' => 'danger',
        'restored' => 'gray',
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['causer', 'subject']))
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),
                TextColumn::make('subject_type')
                    ->label('Subjek')
                    ->formatStateUsing(fn (?string $state): string => ActivityLogResource::subjectTypeLabel($state))
                    ->description(fn (Activity $record): string => static::subjectTitle($record)),
                TextColumn::make('event')
                    ->label('Aksi')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => self::EVENT_LABELS[$state] ?? (string) $state)
                    ->color(fn (?string $state): string => self::EVENT_COLORS[$state] ?? 'gray'),
                TextColumn::make('causer.name')
                    ->label('Pelaku')
                    ->placeholder('Sistem / Publik'),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateIcon(Heroicon::OutlinedClipboardDocumentList)
            ->emptyStateHeading('Belum ada aktivitas tercatat')
            ->emptyStateDescription('Perubahan data (buat, ubah, hapus) oleh pengguna panel akan tercatat di sini.')
            ->filters([
                SelectFilter::make('subject_type')
                    ->label('Tipe Subjek')
                    ->options(ActivityLogResource::SUBJECT_LABELS),
                SelectFilter::make('causer_id')
                    ->label('Pelaku')
                    ->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->all()),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Detail')
                    ->modalHeading('Detail Aktivitas')
                    ->schema([
                        TextEntry::make('subject_type')
                            ->label('Subjek')
                            ->formatStateUsing(fn (?string $state): string => ActivityLogResource::subjectTypeLabel($state)),
                        TextEntry::make('subject_title')
                            ->label('Judul/Nama')
                            ->state(fn (Activity $record): string => static::subjectTitle($record)),
                        TextEntry::make('event')
                            ->label('Aksi')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => self::EVENT_LABELS[$state] ?? (string) $state)
                            ->color(fn (?string $state): string => self::EVENT_COLORS[$state] ?? 'gray'),
                        TextEntry::make('causer.name')
                            ->label('Pelaku')
                            ->placeholder('Sistem / Publik'),
                        TextEntry::make('created_at')
                            ->label('Waktu')
                            ->dateTime('d M Y H:i:s'),
                        TextEntry::make('changes_summary')
                            ->label('Properti Berubah (lama → baru)')
                            ->state(fn (Activity $record): array => static::changeLines($record))
                            ->listWithLineBreaks()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * Judul subjek: dari record terkait bila masih ada, atau dari snapshot
     * properti log (subjek bisa sudah dihapus permanen).
     */
    protected static function subjectTitle(Activity $record): string
    {
        $subject = $record->subject;

        $title = $subject?->title
            ?? $subject?->name
            ?? $record->getExtraProperty('attributes.title')
            ?? $record->getExtraProperty('attributes.name')
            ?? $record->getExtraProperty('old.title')
            ?? $record->getExtraProperty('old.name');

        return $title ?? '#'.$record->subject_id;
    }

    /**
     * Baris ringkasan perubahan "kolom: lama → baru" untuk modal detail.
     *
     * @return array<int, string>
     */
    protected static function changeLines(Activity $record): array
    {
        $attributes = (array) ($record->properties['attributes'] ?? []);
        $old = (array) ($record->properties['old'] ?? []);

        $lines = [];

        foreach ($attributes as $key => $new) {
            $lines[] = array_key_exists($key, $old)
                ? sprintf('%s: %s → %s', $key, static::formatValue($old[$key]), static::formatValue($new))
                : sprintf('%s: %s', $key, static::formatValue($new));
        }

        // Kolom yang hilang pada nilai baru (jarang, mis. event deleted).
        foreach (array_diff_key($old, $attributes) as $key => $value) {
            $lines[] = sprintf('%s: %s → —', $key, static::formatValue($value));
        }

        return $lines !== [] ? $lines : ['(tidak ada rincian properti)'];
    }

    protected static function formatValue(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        if (is_bool($value)) {
            return $value ? 'ya' : 'tidak';
        }

        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE) ?: '—';
        }

        return str((string) $value)->limit(120)->toString();
    }
}
