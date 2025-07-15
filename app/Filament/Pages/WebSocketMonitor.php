<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class WebSocketMonitor extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-signal';

    protected static string $view = 'filament.pages.websocket-monitor';

    protected static ?string $navigationLabel = 'Monitor WebSocket';

    protected static ?string $title = 'Monitor de Reconocimiento Facial';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Sistema';

    public function getTitle(): string
    {
        return 'Monitor de Reconocimiento Facial';
    }

    public function getSubtitle(): string
    {
        return 'Monitoreo en tiempo real del sistema de asistencias';
    }

    protected function getViewData(): array
    {
        return [
            'websocket_url' => config('services.face_service.url', 'http://localhost:5000'),
        ];
    }
}
