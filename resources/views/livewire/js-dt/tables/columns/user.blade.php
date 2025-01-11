@php
    use App\Models\User;
    use Illuminate\Database\Eloquent\Model;
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /**
     * Used in several datatables using user id or user model itself
     *
     * @var BaseDataTable $this
     * @var Model $item
     * @var string $name
     * @var mixed $value
     **/

    // $value can be the user id
    if (is_scalar($value)) {
        $value = $item;
    }

    // try use shared id for urls if possible
    if ($value instanceof User) {
        $user = $value;
    } else {
        if ($item instanceof User) {
            $user = $item;
        } else {
            if ($value) {
                $user = $value->user;
            }
        }
    }
@endphp
<div class="">
    @isset($user)
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
    @endisset
</div>
