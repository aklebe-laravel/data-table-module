# DataTable Module

A module for the [Mercy Scaffold](https://github.com/aklebe-laravel/mercy-scaffold).

Frontend data tables (Livewire) for custom objects or Eloquent models and their relations:

- one class per data table, easy to configure for any model
- lists models and their relations
- default and custom sorting and filters
- works best together with the [Form module](https://github.com/aklebe-laravel/form-module)

## Installation

Part of the Mercy base: every project gets it automatically in a released version (`base_requires` of the
[Mercy defaults](https://github.com/aklebe-laravel/mercy-php-deployment/blob/master/resources/defaults/setup.yaml)).
List it in the project's `requires` only to use another version or branch, see
[Require modules and themes](https://github.com/aklebe-laravel/deploy-env-module#require-modules-and-themes).

## Rows or cards

A data table renders its collections as table rows by default. `$tableView` switches the template, for example
to cards in a grid (images, products, ...):

```php
use Livewire\Attributes\Locked;

class MyItemCards extends MyItem
{
    #[Locked]
    public string $tableView = self::TABLE_VIEW_CARDS;

    // optional: own card per item (gets $item and $collectionName like a table row)
    #[Locked]
    public string $cardView = 'my-module::data-tables.cards.my-item';

    #[Locked]
    public string $cardsGridCss = 'grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4';

    // optional: frame and padding (empty = none)
    #[Locked]
    public string $cardsWrapperCss = '';

    #[Locked]
    public string $cardsBodyCss = '';
}
```

Or per usage as parameter: `<livewire:my-module::data-table.my-item :table-view="\Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable::TABLE_VIEW_CARDS"/>`.

- The default card (`tables/cards/card.blade.php`) shows all visible columns with their column views (label above
  the value; hide it with `'options' => ['card_label' => false]`).
- Header (filters, search), pagination and footer stay the same. Instead of the column headers, the visible
  sortable columns are offered as sort buttons (one column at a time, see `setSingleSort()`).
- Redeclared properties need `#[Locked]` again: PHP does not inherit attributes.

Examples: `website-base::data-table.media-item-picker` (image picker of the WebsiteBase module) and
`central-ui::data-table.vm-desktop-items` (VM desktop of the CentralUi module: cards for the compact and icons
layouts, the standard rows with full header for the list layout, rows from `getFixCollection()`; it extends
the admin table of the same model and inherits columns, form link and actions).

## Header and toolbar

The header shows the filter elements (rows per page, search, own filters with `addFilterElement()`) ordered by
`position`. Elements that are no filter (buttons, a status chip, a nested Livewire component) are added the same
way with `addHeaderElement()`, they just have no `builder`:

```php
class MyItems extends DefaultDataTable
{
    // all elements in one row instead of the two column grid (HEADER_VIEW_DEFAULT)
    public string $headerView = self::HEADER_VIEW_TOOLBAR;

    protected function initFilters(): void
    {
        parent::initFilters();

        // search without the settings menu, growing
        $this->setDefaultFilter('search.view', 'data-table::livewire.js-dt.filters.default-elements.text');
        $this->setDefaultFilter('search.css_group', 'flex-1 min-w-48 max-w-md');

        // own element on the right: the view gets $_config and $collectionName
        $this->addHeaderElement('my_button', 'my-module::data-tables.my-button', 9000, 'ml-auto');
    }
}
```

- `css_group` is the css of the element's wrapper, `hidden` hides it (for example `rows_per_page` without pagination).
- `$headerPagination = false` hides the pagination line below the elements (all entries on one page).
- The element list is rebuilt on every request (`initBooted()`), so it can depend on the user or a layout.

## Console

Create a data table class:

```
php artisan data-table:make {table_name} {module_name?}
```

To create the form and the data table classes of a model at once, use `form:make` of the Form module,
see also [Create forms and datatables](https://github.com/aklebe-laravel/deploy-env-module#create-forms-and-datatables).

## License

MIT, see [LICENSE.md](LICENSE.md).
