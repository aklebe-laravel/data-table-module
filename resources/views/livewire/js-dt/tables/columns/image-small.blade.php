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

    $imageBoxCss = $imageBoxCss ?? '';
    $imageBoxCss.= ' small';
@endphp
@include('data-table::livewire.js-dt.tables.columns.image')