<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderItemResource\Pages;
use App\Filament\Resources\OrderItemResource\RelationManagers;
use App\Models\OrderItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// class OrderItemResource extends Resource
// {
//     protected static ?string $model = OrderItem::class;

//     protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

//     public static function form(Form $form): Form
//     {
//         return $form
//             ->schema([
//                 Forms\Components\TextInput::make('order_id')
//                     ->required()
//                     ->numeric(),
//                 Forms\Components\TextInput::make('product_id')
//                     ->required()
//                     ->numeric(),
//                 Forms\Components\TextInput::make('jumlah')
//                     ->required()
//                     ->numeric(),
//                 Forms\Components\TextInput::make('harga_satuan')
//                     ->required()
//                     ->numeric(),
//                 Forms\Components\TextInput::make('subtotal')
//                     ->required()
//                     ->numeric(),
//                 Forms\Components\Textarea::make('catatan_item')
//                     ->columnSpanFull(),
//             ]);
//     }

//     public static function table(Table $table): Table
//     {
//         return $table
//             ->columns([
//                 Tables\Columns\TextColumn::make('order_id')
//                     ->numeric()
//                     ->sortable(),
//                 Tables\Columns\TextColumn::make('product_id')
//                     ->numeric()
//                     ->sortable(),
//                 Tables\Columns\TextColumn::make('jumlah')
//                     ->numeric()
//                     ->sortable(),
//                 Tables\Columns\TextColumn::make('harga_satuan')
//                     ->numeric()
//                     ->sortable(),
//                 Tables\Columns\TextColumn::make('subtotal')
//                     ->numeric()
//                     ->sortable(),
//                 Tables\Columns\TextColumn::make('created_at')
//                     ->dateTime()
//                     ->sortable()
//                     ->toggleable(isToggledHiddenByDefault: true),
//                 Tables\Columns\TextColumn::make('updated_at')
//                     ->dateTime()
//                     ->sortable()
//                     ->toggleable(isToggledHiddenByDefault: true),
//             ])
//             ->filters([
//                 //
//             ])
//             ->actions([
//                 Tables\Actions\EditAction::make(),
//             ])
//             ->bulkActions([
//                 Tables\Actions\BulkActionGroup::make([
//                     Tables\Actions\DeleteBulkAction::make(),
//                 ]),
//             ]);
//     }

//     public static function getRelations(): array
//     {
//         return [
//             //
//         ];
//     }

//     public static function getPages(): array
//     {
//         return [
//             'index' => Pages\ListOrderItems::route('/'),
//             'create' => Pages\CreateOrderItem::route('/create'),
//             'edit' => Pages\EditOrderItem::route('/{record}/edit'),
//         ];
//     }
// }
