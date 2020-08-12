# A Laravel wrapper for  [Paybox Payment Gateway](https://paybox.money/)

> Only receiving payments are currently supported.

## Installation 

 - Install the package via composer:
 `composer require arniro/laravel-paybox`
 - Publish configuration file:
 `php artisan vendor:publish --tag paybox-config`
 - Set `merchant_id` and `secret_key` in the config file

## Usage

All you need to do is to redirect a user to the generated url of a Paybox website:

```php
use Arniro\Paybox\Facades\Paybox;

class OrdersController extends Controller
{
    public function store()
    {
        ...

        return Paybox::generateUrl([
            'price' => 500,
            'description' => 'Products description',
            'order_id' => 123456,
            'email' => 'john@company.com',
            'phone' => '123456789',
            'name' => 'John Doe',
            'address' => 'Dummy address'
        ])->redirect();
    }
}
```

You can also override any configuration values except `merchant_id` and `secret_key` while generating an url:

```php
return Paybox::generateUrl([
    ...,
    'currency' => 'KZT' 
])->redirect();
```

## Testing 

```
composer test
```

## License 

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
