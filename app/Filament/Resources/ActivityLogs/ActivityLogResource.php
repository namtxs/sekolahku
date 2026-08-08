<?php

namespace App\Filament\Resources\ActivityLogs;

use App\Filament\Resources\ActivityLogs\Pages\ListActivityLogs;
use App\Filament\Resources\ActivityLogs\Tables\ActivityLogsTable;
use App\Models\AcademicYear;
use App\Models\AdmissionPath;
use App\Models\Album;
use App\Models\Applicant;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Comment;
use App\Models\DownloadFile;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Models\Widget;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;
use UnitEnum;

/**
 * Log aktivitas (spatie/laravel-activitylog) — baca-saja: tanpa create,
 * edit, maupun delete. Akses diatur ActivityPolicy (permission
 * `activity.view_any`, hanya Admin/Super Admin).
 */
class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $slug = 'activity-logs';

    protected static ?string $modelLabel = 'Log Aktivitas';

    protected static ?string $pluralModelLabel = 'Log Aktivitas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 2;

    /**
     * Label Indonesia per tipe subjek (dipakai kolom & filter).
     *
     * @var array<class-string, string>
     */
    public const SUBJECT_LABELS = [
        Post::class => 'Berita/Halaman',
        Category::class => 'Kategori',
        Tag::class => 'Tag',
        Comment::class => 'Komentar',
        Banner::class => 'Banner',
        Album::class => 'Album',
        DownloadFile::class => 'Berkas Unduhan',
        Menu::class => 'Menu',
        MenuItem::class => 'Item Menu',
        Widget::class => 'Widget',
        User::class => 'Pengguna',
        AcademicYear::class => 'Tahun Pelajaran',
        AdmissionPath::class => 'Jalur PPDB',
        Applicant::class => 'Pendaftar PPDB',
    ];

    public static function subjectTypeLabel(?string $type): string
    {
        return self::SUBJECT_LABELS[$type] ?? class_basename((string) $type);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return ActivityLogsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivityLogs::route('/'),
        ];
    }
}
