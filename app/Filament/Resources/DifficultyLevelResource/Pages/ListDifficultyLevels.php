<?php

namespace App\Filament\Resources\DifficultyLevelResource\Pages;

use App\Filament\Resources\DifficultyLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDifficultyLevels extends ListRecords
{
    protected static string $resource = DifficultyLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
