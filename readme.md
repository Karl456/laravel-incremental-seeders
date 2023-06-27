A Laravel package for incremental seeders

## Installation

```
composer require karl456/laravel-incremental-seeders
```

You can publish the config file with:

```php
php artisan vendor:publish --provider="Karl456\IncrementalSeeders\IncrementalSeederServiceProvider"
```

## Usage

There are two commands to get you going

`php artisan make:incremental-seeder {SEEDER NAME}`

This will create a seeder file for you to update just like you would with a normal seeder.

`php artisan db:incremental-seed`

This will run any seeders which have not previously been run and update the database table accordingly.
