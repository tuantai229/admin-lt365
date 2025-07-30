<?php
namespace App\Traits;

use App\Models\MetaSeo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasMetaSeo
{
    public function metaSeo(): MorphOne
    {
        return $this->morphOne(MetaSeo::class, 'seoable', 'type', 'type_id');
    }

    public function getOrCreateMetaSeo(): MetaSeo
    {
        return $this->metaSeo()->firstOrCreate([
            'type' => $this->getMorphClass(),
            'type_id' => $this->id
        ]);
    }

    public function getMorphClass(): string
    {
        return strtolower(class_basename($this));
    }
}
