<?php

namespace App\Filament\Resources\PedidoResource\Pages;

use App\Filament\Resources\PedidoResource;
use App\Models\Pedido;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View as ViewContract;

class Cocina extends ListRecords 
{
    protected static string $resource = PedidoResource::class;

    protected static string $view = 'filament.resources.pedido-resource.pages.cocina';
public function table(Table $table): Table
{
    return $table
        ->query(
            Pedido::query()
                ->whereIn('estado', ['pendiente', 'en_preparacion'])
                ->latest()
        )
        ->columns([
            Tables\Columns\TextColumn::make('id')
                ->label('Pedido #')
                ->sortable(),

            Tables\Columns\TextColumn::make('tipo')
                ->label('Tipo')
                ->formatStateUsing(fn ($state) => $state === 'local' ? 'Consumo en local' : 'Para llevar'),

            Tables\Columns\TextColumn::make('mesa.numero')
                ->label('Mesa')
                ->getStateUsing(fn ($record) => $record?->tipo === 'local' ? ($record->mesa->numero ?? '-') : '-'),

            Tables\Columns\TextColumn::make('cliente.nombre')
                ->label('Cliente')
                ->getStateUsing(fn ($record) => $record?->tipo === 'para_llevar' ? ($record->cliente->nombre ?? '-') : '-'),

            Tables\Columns\TextColumn::make('estado')
                ->label('Estado')
                ->badge(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Hora')
                ->since(),
        ])
        ->actions([
            TableAction::make('ver')
                ->label('Ver detalles')
                ->icon('heroicon-o-eye')
                ->modalHeading('Detalles del pedido')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Cerrar')
                ->infolist(function ($record) {
                    return [
                        Section::make('Información general')->schema([
                            TextEntry::make('id')->label('Pedido #'),
                            TextEntry::make('tipo')->label('Tipo')->formatStateUsing(fn ($state) => $state === 'local' ? 'Consumo en local' : 'Para llevar'),
                            TextEntry::make('mesa.numero')->label('Mesa')->visible(fn ($record) => $record->tipo === 'local'),
                            TextEntry::make('cliente.nombre')->label('Cliente')->visible(fn ($record) => $record->tipo === 'para_llevar'),
                            TextEntry::make('estado')->label('Estado'),
                            TextEntry::make('total')->label('Total S/.'),
                            TextEntry::make('created_at')->label('Fecha')->dateTime(),
                        ])->columns(2),

                        Section::make('Productos')->schema([
                            RepeatableEntry::make('detalles')->schema([
                                TextEntry::make('producto.nombre')->label('Producto'),
                                TextEntry::make('cantidad'),
                            ])->columns(2)
                        ]),
                    ];
                }),
            TableAction::make('preparar')
                ->label('Marcar como preparando')
                ->icon('heroicon-o-fire')
                ->color('info')
                ->visible(fn ($record) => $record->estado === 'pendiente')
                ->action(fn ($record) => $record->update(['estado' => 'en_preparacion'])),

            TableAction::make('listo')
                ->label('Marcar como listo')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn ($record) => $record->estado === 'en_preparacion')
                ->action(fn ($record) => $record->update(['estado' => 'listo'])),
        ])
        ->poll(3000);;
    }

    public function getTitle(): string
    {
        return 'Pedidos en cocina';
    }

    public function getHeaderActions(): array
    {
        return [];
    }
}
