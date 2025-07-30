<?php

namespace App\Filament\Resources\CenterResource\Pages;

use App\Filament\Resources\CenterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCenter extends EditRecord
{
    protected static string $resource = CenterResource::class;

    public function mount(string|int $record): void
    {
        parent::mount($record);
        $this->record->getOrCreateMetaSeo();
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $this->record->load('metaSeo');
        $data = $this->record->attributesToArray();

        if ($this->record->metaSeo) {
            $data = array_merge($data, $this->record->metaSeo->attributesToArray());
        }

        $this->form->fill($data);
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $seoData = [
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'meta_keywords' => $data['meta_keywords'] ?? null,
            'meta_robots' => $data['meta_robots'] ?? 'index,follow',
        ];

        $record->metaSeo()->updateOrCreate([], $seoData);

        unset($data['meta_title'], $data['meta_description'], $data['meta_keywords'], $data['meta_robots']);
        
        $record->update($data);

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
