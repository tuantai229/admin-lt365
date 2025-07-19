<?php

namespace App\Filament\Widgets;

use App\Models\Document;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopDocumentsWidget extends BaseWidget
{
    protected static ?string $heading = 'Top 5 tài liệu được tải nhiều nhất';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Document::withCount('downloads')->orderBy('downloads_count', 'desc')->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Tên tài liệu'),
                Tables\Columns\TextColumn::make('downloads_count')->label('Lượt tải'),
            ])
            ->paginated(false);
    }
}
