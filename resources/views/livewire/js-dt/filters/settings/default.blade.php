@php
    declare(strict_types=1);

    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /** @var BaseDataTable $this */
    /** @var string $collectionName */

    $tagIdPrefix = 'dt-header-setting-';
    $counter = (string) app('system_base')->addUniqueCounter($tagIdPrefix);
    $tagId = $tagIdPrefix . $counter;
@endphp

<div id="{{ $tagId }}" class="ml-1 my-auto flex items-center justify-center w-9 h-9 rounded-lg bg-secondary-50 text-secondary-600 hover:bg-secondary-100 transition-colors focus:outline-none">

    <x-website-base::dropdown trigger-class="" hover="false">
        <x-slot:trigger>
            <x-lucide-ellipsis-vertical class="w-5 h-5"/>
        </x-slot:trigger>

        <x-slot:content>
            <!-- New Entry -->
            <x-website-base::dropdown.partials.item
                    wire:click="$dispatchTo('{{ $this->relatedLivewireForm }}', 'open-form', { id: null })">
                {{ __('New Entry') }}
            </x-website-base::dropdown.partials.item>

            <!-- Toggle Visible Items -->
            <x-website-base::dropdown.partials.item
                    wire:click="setCommand('toggle')">
                {{ __('Toggle Visible Items') }}
            </x-website-base::dropdown.partials.item>

            <!-- Export -->
            <x-website-base::dropdown.partials.item
                    wire:click="setCommand('export')">
                {{ __('Export ...') }}
            </x-website-base::dropdown.partials.item>

            <!-- Print -->
            <x-website-base::dropdown.partials.item
                    wire:click="setCommand('print')">
                {{ __('Print ...') }}
            </x-website-base::dropdown.partials.item>

            <div class="my-1 border-t border-surface-200"></div>

            <!-- Reset Filters -->
            <x-website-base::dropdown.partials.item
                    wire:click="$dispatchTo('{{ $this->getName() }}', 'reset-filters')"
                    class="button-reset-all-filters">
                {{ __('Reset all Filters') }}
            </x-website-base::dropdown.partials.item>

            <div class="my-1 border-t border-surface-200"></div>

            <!-- Example with Icon -->
            <a href="#"
               class="flex items-center gap-3 w-full text-left px-4 py-2.5 text-sm transition-colors duration-150 ease-in-out text-surface-700 hover:bg-surface-100 hover:text-secondary-500">
                <x-heroicon-s-heart class="w-4 h-4 text-red-500 shrink-0"/>
                <span>{{ __('Ab zum Test ...') }}</span>
            </a>
        </x-slot:content>
    </x-website-base::dropdown>
</div>