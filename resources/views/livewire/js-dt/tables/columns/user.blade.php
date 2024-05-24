@php
    /**
     * Used in several datatables using user id or user model itself
     *
     * @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this
     * @var Illuminate\Database\Eloquent\Model $item
     * @var string $name
     * @var mixed $value
     **/

    // $value can be the user id
    if (is_scalar($value)) {
        $value = $item;
    }

    // try use shared id for urls if possible
    if ($value instanceof \App\Models\User) {
        $user = $value;
    } else {
        if ($item instanceof \App\Models\User) {
            $user = $item;
        } else {
            $user = $value->user;
        }
    }
@endphp
<div class="">
    @if($user)
        <a target="_blank" href="{{ route('user-profile', $user->shared_id) }}">
            @include("data-table::livewire.js-dt.tables.columns.image-small", [
                'value' => $user->imageMaker ? $user->imageMaker->final_thumb_small_url : themes('images/generic-avatar.jpg'),
                'title' => $user->name,
            ])
        </a>
    @else
        <span class="text-danger">
            <span class="bi bi-person"></span>
        </span>
    @endif
</div>
