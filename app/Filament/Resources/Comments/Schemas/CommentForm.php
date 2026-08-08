<?php

namespace App\Filament\Resources\Comments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'spam' => 'Spam',
                    ])
                    ->required(),
                Textarea::make('body')
                    ->label('Isi Komentar')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),
            ]);
    }
}
