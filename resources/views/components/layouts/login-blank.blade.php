@props(['livewire' => null])
{{--
    Bare-bones layout for the NexaSpace login pages.
    Delegates HTML head / Filament & Livewire assets to layout.base,
    but skips fi-simple-main-ctn entirely so the slot content can fill
    the full viewport without any max-width or centering constraints.
--}}
<x-filament-panels::layout.base :livewire="$livewire">
    {{ $slot }}
</x-filament-panels::layout.base>
