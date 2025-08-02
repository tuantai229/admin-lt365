<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Helpers\FormatHelper;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Tương tác';
    protected static ?string $navigationLabel = 'Liên hệ';
    protected static ?string $modelLabel = 'Liên hệ';
    protected static ?string $pluralModelLabel = 'Liên hệ';
    protected static ?int $navigationSort = 2;

    // Kiểm tra permission
    public static function shouldRegisterNavigation(): bool
    {
        return auth('admin')->user()->can('view_any_contacts');
    }

    public static function canViewAny(): bool
    {
        return auth('admin')->user()->can('view_any_contacts');
    }

    // Không cho phép tạo liên hệ từ admin
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return auth('admin')->user()->can('update_contacts');
    }

    public static function canDelete($record): bool
    {
        return auth('admin')->user()->can('delete_contacts');
    }

    public static function canView($record): bool
    {
        return auth('admin')->user()->can('view_contacts');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('phone')
                ->maxLength(255),
            Forms\Components\TextInput::make('subject')
                ->maxLength(255),
            Forms\Components\Textarea::make('content')
                ->rows(5)
                ->maxLength(65535),
            Forms\Components\Select::make('status')
                ->options([
                    0 => 'Chưa đọc',
                    1 => 'Đã đọc',
                ])
                ->default(0)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('subject')->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn (int $state): string => $state === 0 ? 'Chưa đọc' : 'Đã đọc')
                    ->badge()
                    ->color(fn (int $state): string => match ($state) {
                        0 => 'warning',
                        1 => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->formatStateUsing(fn ($state) => FormatHelper::datetime($state))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        0 => 'Chưa đọc',
                        1 => 'Đã đọc',
                    ]),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContacts::route('/'),
            'view' => Pages\ViewContact::route('/{record}'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
