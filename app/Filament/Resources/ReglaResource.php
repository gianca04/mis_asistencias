<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReglaResource\Pages;
use App\Models\Regla;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReglaResource extends Resource
{
    //use Translatable;

    protected static ?string $modelLabel = 'Regla';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'hora_entrada', 'hora_tardanza'];  // Cambia 'nombre' por 'name' si es el nombre correcto en la base de datos
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Nombre' => $record->name,
            'Reglas' => $record->hora_entrada . ' - ' . $record->hora_tardanza,

        ];
    }
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        // Optimiza la consulta, asegurando que solo cargue lo necesario
        return parent::getGlobalSearchEloquentQuery(); // Elimina la relación inexistente 'user'
    }


    protected static ?string $model = Regla::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Sección para la información básica de la regla
                Forms\Components\Section::make('Información de la Regla')
                    ->description('Detalles sobre la regla que se está creando.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre de la Regla')
                            ->required()
                            ->prefixIcon('heroicon-o-document')
                            ->maxLength(255),
                    ]),

                // Sección para horarios
                Forms\Components\Section::make('Horarios')
                    ->description('Definición de los horarios para la regla.')
                    ->schema([
                        Forms\Components\TimePicker::make('hora_entrada')
                            ->required()
                            ->native()
                            ->prefixIcon('heroicon-o-clock')
                            ->label('Hora de Entrada')
                            ->helperText('La hora en la que los estudiantes deben ingresar a clase.'),

                        Forms\Components\TimePicker::make('hora_tardanza')
                            ->required()
                            ->prefixIcon('heroicon-o-clock')
                            ->label('Hora de Tardanza')
                            ->after('hora_entrada') // Asegura que la hora de tardanza sea posterior a la hora de entrada
                            ->helperText('La hora máxima para que el estudiante llegue tarde sin justificación.'),
                    ])
                    ->columns(2), // Colocar estos dos campos en 2 columnas para mejor presentación

                // Sección para comentarios adicionales
                Forms\Components\Section::make('Comentarios Adicionales')
                    ->description('Información adicional relevante sobre la regla.')
                    ->schema([
                        Forms\Components\Textarea::make('comentarios')
                            ->columnSpanFull()
                            ->label('Comentarios')
                            ->maxLength(500)
                            ->helperText('Comentarios adicionales sobre la regla.'),
                    ]),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->label('Nombre de la Regla')
                    ->tooltip('Nombre de la regla asignada'),

                TextColumn::make('hora_entrada')
                    ->badge()
                    ->searchable()
                    ->color('success')
                    ->tooltip('Hora de entrada del estudiante.'),

                TextColumn::make('hora_tardanza')
                    ->badge()
                    ->searchable()
                    ->color('danger')
                    ->tooltip('Hora límite para llegar tarde sin justificación.'),

                TextColumn::make('comentarios')
                    ->limit(50) // Limita la longitud para la vista en tabla
                    ->tooltip('Comentarios adicionales sobre la regla.'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Fecha de Creación')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Última Actualización')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([

                // Puedes agregar filtros si es necesario
            ])
            ->actions([

                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('info'),
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReglas::route('/'),
            'create' => Pages\CreateRegla::route('/create'),
            'edit' => Pages\EditRegla::route('/{record}/edit'),
        ];
    }
}
