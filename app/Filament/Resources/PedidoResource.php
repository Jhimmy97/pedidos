<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PedidoResource\Pages;
use App\Filament\Resources\PedidoResource\RelationManagers;
use App\Models\Pedido;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Select::make('tipo')
                ->label('Tipo de pedido')
                ->required()
                ->options([
                    'local' => 'Consumo en local',
                    'para_llevar' => 'Para llevar',
                ])
                ->reactive(),

            Forms\Components\Select::make('mesa_id')
                ->label('Mesa')
                ->relationship('mesa', 'numero')
                ->searchable()
                ->hidden(fn (Get $get) => $get('tipo') !== 'local')
                ->required(fn (Get $get) => $get('tipo') === 'local'),

            Forms\Components\Select::make('cliente_id')
                ->label('Cliente')
                ->relationship('cliente', 'nombre')
                ->searchable()
                ->createOptionForm([
                    Forms\Components\TextInput::make('nombre')->required(),
                ])
                ->hidden(fn (Get $get) => $get('tipo') !== 'para_llevar')
                ->required(fn (Get $get) => $get('tipo') === 'para_llevar'),

            Forms\Components\Select::make('estado')
                ->label('Estado del pedido')
                ->required()
                ->options([
                    'pendiente' => 'Pendiente',
                    'en_preparacion' => 'Preparando',
                    'listo' => 'Listo',
                    'entregado' => 'Entregado',
                    'cancelado' => 'Cancelado',
                ]),
            Forms\Components\Repeater::make('detalles')
                ->label('Productos del pedido')
                ->relationship()
                ->schema([
                    Forms\Components\Select::make('producto_id')
                        ->label('Producto')
                        ->relationship('producto', 'nombre')
                        ->required()
                        ->searchable()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $producto = \App\Models\Producto::find($state);
                            $precio = $producto?->precio ?? 0;
                            $cantidad = $get('cantidad') ?? 1;

                            $set('precio_unitario', $precio);
                            $set('subtotal', $precio * $cantidad);
                        }),

                    Forms\Components\TextInput::make('cantidad')
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function (callable $set, callable $get) {
                            $set('subtotal', ($get('cantidad') ?? 1) * ($get('precio_unitario') ?? 0));
                        }),

                    Forms\Components\TextInput::make('precio_unitario')
                        ->label('Precio Unitario')
                        ->disabled()
                        ->dehydrated()
                        ->required()
                        ->numeric(),

                    Forms\Components\TextInput::make('subtotal')
                        ->label('Subtotal')
                        ->disabled()
                        ->numeric()
                        ->dehydrated(),
                ])
                ->addActionLabel('Añadir producto')
                ->columnSpanFull()
                ->reactive()
                ->afterStateUpdated(function (callable $set, $get) {
                    // Cuando cambie algo dentro del repeater, recalcula el total
                    $detalles = $get('detalles') ?? [];
                    $total = collect($detalles)->sum(fn ($detalle) =>
                        ($detalle['cantidad'] ?? 1) * ($detalle['precio_unitario'] ?? 0)
                    );
                    $set('total', $total);
                }),

            Forms\Components\TextInput::make('total')
                ->label('Total S/.')
                ->required()
                ->numeric()
                ->disabled()
                ->dehydrated()
                ->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'local' => 'info',
                        'para_llevar' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'local' => 'Consumo en local',
                        'para_llevar' => 'Para llevar',
                        default => ucfirst($state),
                    }),
                Tables\Columns\TextColumn::make('mesa.numero')
                ->label('Mesa'),
                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->colors([
                        'gray' => 'pendiente',
                        'info' => 'en_preparacion',
                        'success' => 'listo',
                        'primary' => 'entregado',
                        'danger' => 'cancelado',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                        ->label('Total S/.')
                        ->money('PEN', true)
                        ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Fecha')
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                ->label('Estado')
                ->options([
                    'pendiente' => 'Pendiente',
                    'en_preparacion' => 'Preparando',
                    'listo' => 'Listo',
                    'entregado' => 'Entregado',
                    'cancelado' => 'Cancelado',
                ]),

            ])
            ->actions([
                Tables\Actions\Action::make('preparar')
                    ->label('Preparar')
                    ->icon('heroicon-o-fire')
                    ->color('info')
                    ->visible(fn ($record) => $record->estado === 'pendiente')
                    ->action(fn ($record) => $record->update(['estado' => 'en_preparacion'])),

                Tables\Actions\Action::make('marcar_listo')
                    ->label('Listo')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->estado === 'en_preparacion')
                    ->action(fn ($record) => $record->update(['estado' => 'listo'])),   

                Tables\Actions\Action::make('entregar')
                    ->label('Entregar')
                    ->icon('heroicon-o-truck')
                    ->color('primary')
                    ->visible(fn ($record) => $record->estado === 'listo')
                    ->action(fn ($record) => $record->update(['estado' => 'entregado'])),

                Tables\Actions\Action::make('cancelar')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => in_array($record->estado, ['pendiente', 'en_preparacion']))
                    ->action(fn ($record) => $record->update(['estado' => 'cancelado'])),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListPedidos::route('/'),
            'create' => Pages\CreatePedido::route('/create'),
            'edit' => Pages\EditPedido::route('/{record}/edit'),
        ];
    }

    
}
