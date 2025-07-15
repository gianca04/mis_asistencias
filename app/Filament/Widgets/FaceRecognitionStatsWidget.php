<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FaceRecognitionStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';

    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        return [
            Stat::make('Estado del Servicio', $this->getServiceStatus())
                ->description('Estado del microservicio de reconocimiento facial')
                ->descriptionIcon($this->getServiceStatusIcon())
                ->color($this->getServiceStatusColor()),

            Stat::make('Salones Monitoreando', $this->getSalonesMonitoreando())
                ->description('Salones actualmente siendo monitoreados')
                ->descriptionIcon('heroicon-m-eye')
                ->color('info'),

            Stat::make('Detecciones Hoy', $this->getDeteccionesHoy())
                ->description('Detecciones de rostros en el día')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
        ];
    }

    private function getServiceStatus(): string
    {
        // Aquí puedes implementar una verificación real del estado del servicio
        // Por ahora retornamos un estado simulado
        return 'En línea';
    }

    private function getServiceStatusIcon(): string
    {
        return 'heroicon-m-signal';
    }

    private function getServiceStatusColor(): string
    {
        return 'success';
    }

    private function getSalonesMonitoreando(): string
    {
        // Aquí puedes obtener datos reales de tu base de datos
        // Por ejemplo, contar matriculas activas
        return '0';
    }

    private function getDeteccionesHoy(): string
    {
        // Aquí puedes obtener datos reales de asistencias del día
        // Por ejemplo, contar asistencias de hoy
        return '0';
    }
}
