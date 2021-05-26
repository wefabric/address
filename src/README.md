# Wefabric Address package

This package provides helper classes to handle addresses. 


## Installation
For installation run

```composer require wefabric/adresss```

After the package is installed run the following command to publish the migration files

```php artisan vendor:publish --provider="Wefabric\Address\Providers\ServiceProvider" --tag="migrations" ```
```php artisan migrate```
