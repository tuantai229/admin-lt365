<?php

namespace App\Filament\Resources\DifficultyLevelResource\Pages;

use App\Filament\Resources\DifficultyLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDifficultyLevel extends EditRecord
{
    protected static string $resource = DifficultyLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
