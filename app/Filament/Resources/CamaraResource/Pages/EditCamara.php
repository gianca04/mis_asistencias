<?php

namespace App\Filament\Resources\CamaraResource\Pages;

use App\Filament\Resources\CamaraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCamara extends EditRecord
{
    protected static string $resource = CamaraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
