## DataTable

A module for [Mercy Scaffold Application](https://github.com/aklebe-laravel/mercy-scaffold.git)
(or any based on it like [Jumble Sale](https://github.com/aklebe-laravel/jumble-sale.git)).

This module will provide frontend data tables with the following features

1) easy configuration of datatables for any model
3) can list any model and their relations
4) default and customize sort and filter
4) most benefit if used together with [Form-Module](https://github.com/aklebe-laravel/form-module.git)

### Console

Create the datatable class file.

```
php artisan data-table:make {table_name} {module_name?}
```

See readme ```DeployEnv``` to create datatable and form classes at once.
