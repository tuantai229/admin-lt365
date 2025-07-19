<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class NewCommentsWidget extends BaseWidget
{
    protected static ?string $heading = 'Bình luận mới';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Comment::latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('content')->label('Nội dung'),
                Tables\Columns\TextColumn::make('created_at')->label('Thời gian tạo')->dateTime(),
            ])
            ->paginated(false);
    }
}
