<?php

namespace App\Filament\Widgets;

use App\Models\Newsletter;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class NewNewslettersWidget extends BaseWidget
{
    protected static ?string $heading = 'Đăng ký newsletter mới';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Newsletter::latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('email')->label('Email'),
                Tables\Columns\TextColumn::make('created_at')->label('Thời gian tạo')->dateTime(),
            ])
            ->paginated(false);
    }
}
