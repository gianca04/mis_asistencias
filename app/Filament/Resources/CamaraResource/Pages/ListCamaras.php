<?php

namespace App\Filament\Resources\CamaraResource\Pages;

use App\Filament\Resources\CamaraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCamaras extends ListRecords
{
    protected static string $resource = CamaraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
