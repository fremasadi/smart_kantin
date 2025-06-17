<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiResource\Pages;
use App\Filament\Resources\TransaksiResource\RelationManagers;
use App\Models\Transaksi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// class TransaksiResource extends Resource
// {
//     protected static ?string $model = Transaksi::class;

//     protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

//     public static function form(Form $form): Form
//     {
//         return $form
//             ->schema([
//                 Forms\Components\TextInput::make('murid.name') // nama dari model Murid
//     ->label('Nama Murid')
//     ->disabled()
//     ->dehydrated(false),

// Forms\Components\TextInput::make('orangtua.name') // nama dari model User (orangtua)
//     ->label('Nama Orangtua')
//     ->disabled()
//     ->dehydrated(false),

    
//                 Forms\Components\TextInput::make('nominal')
//                     ->label('Nominal')
//                     ->disabled()
//                     ->dehydrated(false)
//                     ->numeric(),
    
//                 Forms\Components\TextInput::make('bukti_transfer')
//                     ->label('Bukti Transfer')
//                     ->disabled()
//                     ->dehydrated(false)
//                     ->maxLength(255),
    
//                 Forms\Components\Select::make('status')
//                     ->options([
//                         'pending' => 'Pending',
//                         'diterima' => 'Diterima',
//                         'ditolak' => 'Ditolak',
//                     ])
//                     ->required(),
//             ]);
//     }

//     public static function table(Table $table): Table
//     {
//         return $table
//             ->columns([
//                 Tables\Columns\TextColumn::make('murid.name')
//     ->label('Nama Murid')
//     ->sortable()
//     ->searchable(),

//     Tables\Columns\TextColumn::make('orangtua.name')
//     ->label('Nama Orangtua')
//     ->sortable()
//     ->searchable(),

//                 Tables\Columns\TextColumn::make('nominal')
//                     ->numeric()
//                     ->sortable(),
//                 // Tables\Columns\TextColumn::make('bukti_transfer')
//                 //     ->searchable(),
//                 Tables\Columns\TextColumn::make('status'),
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
//                 Tables\Actions\Action::make('Lihat Bukti')
//                     ->label('Lihat Bukti Transfer')
//                     ->icon('heroicon-o-eye')
//                     ->modalHeading('Bukti Transfer')
//                     ->modalContent(function ($record) {
//                         return view('filament.custom.bukti-transfer', [
//                             'image' => $record->bukti_transfer,
//                         ]);
//                     })
//                     ->modalSubmitAction(false)
//                     ->modalCancelActionLabel('Tutup'),
//                     Tables\Actions\Action::make('Konfirmasi')
//                     ->label('Konfirmasi')
//                     ->icon('heroicon-o-check')
//                     ->requiresConfirmation()
//                     ->visible(fn ($record) => $record->status === 'pending') // hanya tampil jika pending
//                     ->action(function ($record) {
//                         // Tambah saldo murid
//                         $murid = $record->murid;
//                         $murid->saldo += $record->nominal;
//                         $murid->save();

//                         // Ubah status transaksi
//                         $record->status = 'Diterima';
//                         $record->save();
//                     })
//                     ->color('success'),

//             ])
            
//             ->bulkActions([
//                 Tables\Actions\BulkActionGroup::make([
//                     // Tables\Actions\DeleteBulkAction::make(),
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
//             'index' => Pages\ListTransaksis::route('/'),
//             // 'create' => Pages\CreateTransaksi::route('/create'),
//             // 'edit' => Pages\EditTransaksi::route('/{record}/edit'),
//         ];
//     }
// }
