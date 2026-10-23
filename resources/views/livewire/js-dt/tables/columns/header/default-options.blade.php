@php
    declare(strict_types=1);
    $icon = 'lucide-' . ($column['icon'] ?? 'circle-question-mark');
    $sortable = $column['sortable'] ?? false;
@endphp

<x-website-base::dropdown trigger-class="" :hover="true" placement="bottom-start">
    <x-slot:trigger>
        {{-- Alle Farbzustände liegen zentral in x-bind:class, um Spezifitäts-Konflikte zu verhindern --}}
        <span
                class="inline-flex transition-colors duration-150"
                x-bind:class="{
                'text-secondary-600 font-semibold': open,
                '{{ $sortable ? 'text-secondary-500' : 'text-secondary-300' }} hover:text-secondary-600': !open
            }"
        >
            <x-dynamic-component
                    :component="$icon"
                    class="h-4 w-4 cursor-pointer"
            />
        </span>
    </x-slot:trigger>

    <x-slot:content>
        <!-- Titel des Spalten-Headers -->
        <div class="px-4 py-2 font-semibold text-surface-500 border-b border-surface-200 text-start">
            {{ $column['label'] }}
        </div>

        <!-- 1) UNTERMENÜ: Sotiereinstellungen -->
        @if ($sortable)
            <x-website-base::dropdown.sub :label="__('Toggle Sort')">
                <button
                        type="button"
                        wire:click="$dispatchSelf('set-sort', {'column':'{{ $column['name'] }}', 'direction':'asc', 'collectionName':'{{ $collectionName }}'})"
                        class="w-full text-left px-4 py-2 text-sm text-surface-700 hover:bg-surface-100 hover:text-secondary-500 transition-colors block"
                >
                    {{ __('Sort Down') }}
                </button>

                <button
                        type="button"
                        wire:click="$dispatchSelf('set-sort', {'column':'{{ $column['name'] }}', 'direction':'desc', 'collectionName':'{{ $collectionName }}'})"
                        class="w-full text-left px-4 py-2 text-sm text-surface-700 hover:bg-surface-100 hover:text-secondary-500 transition-colors block"
                >
                    {{ __('Sort Up') }}
                </button>
            </x-website-base::dropdown.sub>

            <!-- Trennlinie zu den restlichen Filtern -->
            <div class="my-1 border-t border-surface-200"></div>
        @endif

        <!-- 2) DIREKTE AKTION: Filter zurücksetzen -->
        <button
                type="button"
                wire:click="$dispatchTo('{{ $this->getName() }}', 'reset-filters')"
                class="w-full text-left px-4 py-2.5 text-sm text-surface-700 hover:bg-surface-100 hover:text-secondary-500 transition-colors button-reset-all-filters"
        >
            {{ __('Reset all Filters') }}
        </button>
    </x-slot:content>
</x-website-base::dropdown>
