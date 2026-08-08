<?php

namespace App\Filament\Resources\Applicants\Schemas;

use App\Models\Applicant;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class ApplicantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pendaftaran')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('registration_number')
                            ->label('Nomor Pendaftaran')
                            ->weight('bold')
                            ->copyable(),
                        TextEntry::make('path.name')
                            ->label('Jalur')
                            ->formatStateUsing(fn (Applicant $record): string => $record->path->name.' — '.$record->path->academicYear->name),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => Applicant::STATUS_LABELS[$state] ?? $state)
                            ->color(fn (string $state): string => match ($state) {
                                Applicant::STATUS_PENDING => 'gray',
                                Applicant::STATUS_VERIFIED => 'info',
                                Applicant::STATUS_ACCEPTED => 'success',
                                Applicant::STATUS_REJECTED => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('created_at')
                            ->label('Tanggal Daftar')
                            ->dateTime('d M Y H:i'),
                        TextEntry::make('verified_at')
                            ->label('Diverifikasi')
                            ->dateTime('d M Y H:i')
                            ->placeholder('Belum diverifikasi'),
                        TextEntry::make('decided_at')
                            ->label('Diputuskan')
                            ->dateTime('d M Y H:i')
                            ->placeholder('Belum ada keputusan'),
                        TextEntry::make('verification_note')
                            ->label('Catatan Verifikasi')
                            ->placeholder('Tidak ada catatan')
                            ->columnSpanFull(),
                    ]),
                Section::make('Data Diri')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama Lengkap'),
                        TextEntry::make('nisn')
                            ->label('NISN'),
                        TextEntry::make('gender')
                            ->label('Jenis Kelamin')
                            ->formatStateUsing(fn (string $state): string => $state === 'L' ? 'Laki-laki' : 'Perempuan'),
                        TextEntry::make('birth_place')
                            ->label('Tempat Lahir'),
                        TextEntry::make('birth_date')
                            ->label('Tanggal Lahir')
                            ->date('d M Y'),
                        TextEntry::make('religion')
                            ->label('Agama'),
                        TextEntry::make('address')
                            ->label('Alamat')
                            ->columnSpanFull(),
                        TextEntry::make('phone')
                            ->label('No. HP'),
                        TextEntry::make('email')
                            ->label('Email')
                            ->placeholder('-'),
                    ]),
                Section::make('Orang Tua & Asal Sekolah')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('parent_name')
                            ->label('Nama Orang Tua/Wali'),
                        TextEntry::make('parent_phone')
                            ->label('No. HP Orang Tua/Wali'),
                        TextEntry::make('school_origin')
                            ->label('Asal Sekolah'),
                        TextEntry::make('avg_score')
                            ->label('Rata-rata Nilai Rapor')
                            ->placeholder('-'),
                    ]),
                Section::make('Berkas')
                    ->columns(2)
                    ->schema([
                        SpatieMediaLibraryImageEntry::make('photo')
                            ->label('Pas Foto')
                            ->collection('photo')
                            ->conversion('thumb')
                            ->placeholder('Tidak ada foto'),
                        TextEntry::make('documents_list')
                            ->label('Dokumen')
                            ->state(function (Applicant $record): ?HtmlString {
                                $media = $record->getMedia('documents');

                                if ($media->isEmpty()) {
                                    return null;
                                }

                                return new HtmlString(
                                    $media->map(fn ($item): string => '<a href="'.e($item->getUrl()).'" target="_blank" rel="noopener" class="fi-link text-primary-600 underline">'
                                        .e($item->name ?: $item->file_name).'</a> <span class="text-gray-500">('.$item->humanReadableSize.')</span>')
                                        ->implode('<br>'),
                                );
                            })
                            ->placeholder('Tidak ada dokumen'),
                    ]),
            ]);
    }
}
