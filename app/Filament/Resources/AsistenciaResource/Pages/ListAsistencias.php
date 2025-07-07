<?php

namespace App\Filament\Resources\AsistenciaResource\Pages;

use App\Filament\Resources\AsistenciaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAsistencias extends ListRecords
{
    protected static string $resource = AsistenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('registroMasivo')
                ->label('Registro Masivo')
                ->icon('heroicon-o-user-group')
                ->color('info')
                ->url(fn (): string => static::$resource::getUrl('registro-masivo')),
        ];
    }
}
