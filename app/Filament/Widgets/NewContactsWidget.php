<?php

namespace App\Filament\Widgets;

use App\Models\Contact;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class NewContactsWidget extends BaseWidget
{
    protected static ?string $heading = 'Liên hệ mới';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Contact::latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Tên'),
                Tables\Columns\TextColumn::make('email')->label('Email'),
                Tables\Columns\TextColumn::make('created_at')->label('Thời gian tạo')->dateTime(),
            ])
            ->paginated(false);
    }
}
