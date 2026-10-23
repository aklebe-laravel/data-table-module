## DataTable Module

A module for the [Mercy Scaffold](https://github.com/aklebe-laravel/mercy-scaffold).

Frontend data tables (Livewire) for custom objects or Eloquent models and their relations:

- one class per data table, easy to configure for any model
- lists models and their relations
- default and custom sorting and filters
- works best together with the [Form module](https://github.com/aklebe-laravel/form-module)

## Installation

Add the module to `requires` in the project's `setup.yaml`, see
[Require modules and themes](https://github.com/aklebe-laravel/deploy-env-module#require-modules-and-themes).

## Console

Create a data table class:

```
php artisan data-table:make {table_name} {module_name?}
```

To create the form and the data table classes of a model at once, use `form:make` of the Form module,
see also [Create forms and datatables](https://github.com/aklebe-laravel/deploy-env-module#create-forms-and-datatables).

## License

MIT, see [LICENSE.md](LICENSE.md).
