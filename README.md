# Laravel-Hashids
> Easily integrate Laravel with Hashids with full model support. 

There are lots of packages for integrating Hashids with Laravel, but all of them just provides you with facade and add some syntax sugar.

But what about model binding and automatic id resolving? This package gives you all. All you have to do is just to install this package!

## Installation

[PHP](https://php.net) 5.4+ or [HHVM](http://hhvm.com) 3.3+, [Composer](https://getcomposer.org) and [Laravel](http://laravel.com) 5.0+ are required.

To get the latest version of Laravel Laravel-Hashids, simply install it via composer.

```bash
$ composer require lanin/laravel-hashids
```

Once Laravel-Hashids is installed, you need to register the service provider. Open up `config/app.php` and add the following to the providers key.

```php
Lanin\Laravel\Hashids\HashidsServiceProvider::class,
```

The last thing is to add `\Lanin\Laravel\Hashids\UseHashidsRouter` trait to your `App\Http\Kernel`. 
This will force Laravel to use package router to dispatch the route.

```php
namespace App\Http;

class Kernel extends HttpKernel
{
    use \Lanin\Laravel\Hashids\UseHashidsRouter;
    
    ...
}
```

Also you can register `HashidsFacade` for easier access to the Hashids methods.

```php
'Hashids' => Lanin\Laravel\Hashids\HashidsFacade::class,
```

## Usage

You don't have to modify anything in your routes or html to make it run. 
If you are using model bindings and route resolving in html, everything will convert automatically.

### Binding

After the installation, Router's method `model` that binds your placeholders ids to the models will be updated to automatically support hashids and convert them to the internal integer ids. 

### Routing

If you will pass hash id to the url it will be easily resolved into an associated model. But how replace ids in your html output? There are two ways. Everything depends on how you form your urls.

If you prefer form them by hands, package gives you the blade helper method `@hashids($id)` that will convert your id to the hash string.

```html
<a href="/posts/@hashids($post->id)">{{$post->title}}</a>
```

But this method is tedious. And I prefer using awesome Laravel feature that automatically extracts ids from models and inserts them into your urls. Example:

```php
route('page.show', $page);
url('page', ['id' => $page]);
```

This methods were updated to handle Hashids too. They will automatically replace integer ids from your models to Hashids.

> If for some reason you don't want to convert them, just implement `\Lanin\Laravel\Hashids\DoNotUseHashids` interface in your model.

## Configuration

By default package will use your `APP_KEY` as a salt and 4 symbols length for the hash. If you want to overwrite it, publish hashids configs and edit `config/hahshids.php`

```bash
$ php artisan vendor:publish
```

## Contributing

Please feel free to fork this package and contribute by submitting a pull request to enhance the functionalities.