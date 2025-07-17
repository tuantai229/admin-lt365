<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Document;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderItems';

    protected static ?string $recordTitleAttribute = 'document_id';

    protected static ?string $label = 'Chi tiết đơn hàng';

    protected static ?string $title = 'Chi tiết đơn hàng';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('document_id')
                    ->label('Sản phẩm')
                    ->relationship('document', 'title')
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $document = Document::find($state);
                        if ($document) {
                            $set('price', $document->price ?? 0);
                        }
                    })
                    ->columnSpan(2),
                TextInput::make('price')
                    ->label('Đơn giá')
                    ->numeric()
                    ->required()
                    ->dehydrated(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('document.title')
                    ->label('Sản phẩm'),
                TextColumn::make('price')
                    ->label('Đơn giá')
                    ->money('VND'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
