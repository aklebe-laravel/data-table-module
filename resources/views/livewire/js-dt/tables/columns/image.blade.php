@php
    use Illuminate\Database\Eloquent\Model;
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /**
     * @var BaseDataTable $this
     * @var Model $item
     * @var string $name
     * @var mixed $value The image path
     * @var string $link make clickable link if exists
     * @var string $imageBoxCss image-box css
     **/

    $link = $link ?? '';
    $imageBoxCss = $imageBoxCss ?? '';
    $value = $value ?? '';
    $value = $value ?: theme_asset('images/no_image_available.webp');
@endphp
<div class="text-center image-content w-full">
    <div class="image-box {{ $imageBoxCss }}">
        @if($link)
            <a href="{{ $link }}">
                <img src="{{ $value }}" alt="{{ $title ?? '' }}" title="{{ $title ?? '' }}" class="rounded"/>
            </a>
        @else
            <img src="{{ $value }}" alt="{{ $title ?? '' }}" title="{{ $title ?? '' }}" class="rounded"/>
        @endif
    </div>
</div>

