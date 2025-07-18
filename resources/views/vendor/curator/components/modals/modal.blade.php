<x-filament::modal 
    id="curator-panel" 
    width="7xl" 
    class="curator-panel" 
    displayClasses="block"
    :close-button="true"
    :close-by-clicking-away="true"
>
    <x-slot name="heading">
        {{ trans('curator::views.panel.heading') }}
    </x-slot>
    <livewire:curator-panel/>
</x-filament::modal>
