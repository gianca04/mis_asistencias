<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CamaraResource\Pages;
use App\Filament\Resources\CamaraResource\RelationManagers;
use App\Models\Camara;
use App\Models\Matricula;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CamaraResource extends Resource
{
    protected static ?string $model = Camara::class;

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';

    protected static ?string $navigationLabel = 'Cámaras';

    protected static ?string $modelLabel = 'Cámara';

    protected static ?string $pluralModelLabel = 'Cámaras';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['matricula.grado', 'matricula.seccion']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Cámara')
                    ->description('Configure los detalles de la cámara de vigilancia')
                    ->schema([
                        Forms\Components\TextInput::make('url_stream')
                            ->label('URL del Stream')
                            ->required()
                            ->url()
                            ->placeholder('http://192.168.1.100:8080/stream')
                            ->helperText('Ingrese la URL completa del stream de video')
                            ->maxLength(255),
                        Forms\Components\Select::make('matricula_id')
                            ->label('Matrícula')
                            ->relationship(
                                'matricula',
                                'id',
                                fn (Builder $query) => $query->with(['grado', 'seccion'])
                                    ->orderBy('anio_escolar', 'desc')
                                    ->orderBy('grado_id')
                                    ->orderBy('seccion_id')
                            )
                            ->getOptionLabelFromRecordUsing(fn (Matricula $record): string => $record->display_name)
                            ->searchable(['codigo_matricula', 'anio_escolar'])
                            ->preload()
                            ->required()
                            ->placeholder('Seleccione una matrícula')
                            ->helperText('Seleccione la matrícula a la que pertenece esta cámara'),
                        Forms\Components\Toggle::make('activo')
                            ->label('Estado Activo')
                            ->default(true)
                            ->helperText('Indica si la cámara está activa y funcionando')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('url_stream')
                    ->label('URL del Stream')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) > 50) {
                            return $state;
                        }
                        return null;
                    }),
                Tables\Columns\TextColumn::make('matricula.grado.nombre')
                    ->label('Grado')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('matricula.seccion.nombre')
                    ->label('Sección')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('matricula.anio_escolar')
                    ->label('Año Escolar')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('matricula.codigo_matricula')
                    ->label('Código Matrícula')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('activo')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Estado')
                    ->boolean()
                    ->trueLabel('Activas')
                    ->falseLabel('Inactivas')
                    ->native(false),
                Tables\Filters\SelectFilter::make('matricula.grado_id')
                    ->label('Grado')
                    ->relationship('matricula.grado', 'nombre')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('matricula.anio_escolar')
                    ->label('Año Escolar')
                    ->options(function () {
                        return Matricula::distinct()
                            ->pluck('anio_escolar', 'anio_escolar')
                            ->toArray();
                    })
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListCamaras::route('/'),
            'create' => Pages\CreateCamara::route('/create'),
            'edit' => Pages\EditCamara::route('/{record}/edit'),
        ];
    }
}
