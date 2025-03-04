<p align="center"><img height="300px" src="./logo-text.svg" alt="logo"></p>

# Laraberg Nova

A Nova field for Laraberg.

## Quick start

### Requirements

| Dependency   | Minimum version |
|--------------|-----------------|
| PHP          | 8.1             |
| Laravel Nova | 4.35            |

### Installation

Install via Composer:

```bash
composer require van-ons/laraberg-nova
```

Publish Laraberg files:

```bash
php artisan vendor:publish --provider="VanOns\Laraberg\LarabergServiceProvider"
```

Laraberg provides a CSS file that should be present on the page you want to
render content on:

```html
<link rel="stylesheet" href="{{ asset('vendor/laraberg/css/laraberg.css') }}">
```

### Usage

Simply register the field in your Resource:

```php
LarabergNova::make(__('Content'), 'content')
```

Add the `RendersContent` trait to your model. And optionally define the
`$contentColumn` property to point to the column that holds your Laraberg
content, this defaults to `content`.

```php
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use VanOns\Laraberg\Traits\RendersContent;

class Post extends Model
{
    use HasFactory, RendersContent;

    protected $contentColumn = 'content';

    ...
}
```

Call the render method on your model in a template:

```php
{!! $model->render() !!}
```

#### Options

The field has a few options you can configure.

##### Height

You can customize the height of the editor:

```php
LarabergNova::make(__('Content'), 'content')->height(600)
```

##### Attachments

You can enable uploading attachments:

```php
LarabergNova::make(__('Content'), 'content')->withFiles('public')
```

You will need to add the following migration to make this work:

```php
Schema::create('laraberg_nova_pending_attachments', function (Blueprint $table) {
    $table->increments('id');
    $table->string('draft_id')->index();
    $table->string('attachment');
    $table->string('disk');
    $table->timestamps();
});

Schema::create('laraberg_nova_attachments', function (Blueprint $table) {
    $table->increments('id');
    $table->string('attachable_type');
    $table->unsignedInteger('attachable_id');
    $table->string('attachment');
    $table->string('disk');
    $table->string('url')->index();
    $table->timestamps();
    $table->index(['attachable_type', 'attachable_id']);
});
```

## Contributing

Please see [contributing] for more information about how you can contribute.

## Changelog

Please see [changelog] for more information about what has changed recently.

## Upgrading

Please see [upgrading] for more information about how to upgrade.

## Security

Please see [security] for more information about how we deal with security.

## Credits

We would like to thank the following contributors for their contributions to
this project:

- [All Contributors][all-contributors]

## License

The scripts and documentation in this project are released under the [MIT License][license].

---

<p align="center">
    <a href="https://van-ons.nl/" target="_blank">
        <img src="https://opensource.van-ons.nl/files/cow.png" width="50" alt="Logo of Van Ons">
    </a>
</p>

[contributing]: CONTRIBUTING.md
[changelog]: CHANGELOG.md
[upgrading]: UPGRADING.md
[security]: SECURITY.md
[all-contributors]: ../../contributors
[license]: LICENSE.md
