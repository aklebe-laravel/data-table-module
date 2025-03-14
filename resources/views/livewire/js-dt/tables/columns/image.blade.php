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
    $value = $value ?: themes('images/no_image_available.jpg');
@endphp
<div class="text-center image-content">
    <div class="image-box {{ $imageBoxCss }}">
        @if($link)
            <a href="{{ $link }}">
                <img src="{{ $value }}" alt="{{ $title ?? '' }}" title="{{ $title ?? '' }}"/>
            </a>
        @else
            <img src="{{ $value }}" alt="{{ $title ?? '' }}" title="{{ $title ?? '' }}"/>
        @endif
    </div>
</div>

