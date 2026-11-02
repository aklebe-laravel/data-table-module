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

## Console

Create a data table class:

```
php artisan data-table:make {table_name} {module_name?}
```

To create the form and the data table classes of a model at once, use `form:make` of the Form module,
see also [Create forms and datatables](https://github.com/aklebe-laravel/deploy-env-module#create-forms-and-datatables).

## License

MIT, see [LICENSE.md](LICENSE.md).
