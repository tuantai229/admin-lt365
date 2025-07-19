<?php

namespace App\Filament\Widgets;

use App\Models\Document;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class NewDocumentsWidget extends BaseWidget
{
    protected static ?string $heading = 'Tài liệu mới';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Document::latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Tên tài liệu'),
                Tables\Columns\TextColumn::make('created_at')->label('Thời gian tạo')->dateTime(),
            ])
            ->paginated(false);
    }
}
