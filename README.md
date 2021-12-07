# Laraberg Nova

A nova field for Laraberg

## Installation

Install via composer

```bash
composer require van-ons/laraberg-nova
```

Publish Laraberg files

```bash
php artisan vendor:publish --provider="VanOns\Laraberg\LarabergServiceProvider"
```

Laraberg provides a CSS file that should be present on the page you want to render content on:

```html
<link rel="stylesheet" href="{{asset('vendor/laraberg/css/laraberg.css')}}">
```
## Usage

Simply register the field in your Resource

```php
LarabergNova::make(__('Content'), 'content')
```

Add the `RendersContent` trait to your model. And optionally define the `$contentColumn` property to point to the column that holds your Laraberg content, this defaults to `content`.

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



Call the render method on your model in a template.


```php
{!! $model->render() !!}
```

### Options

The field has a few options you can configure.

#### Height

You can customize the height of the editor.

```php
LarabergNova::make(__('Content'), 'content')->height(600)
```
#### Attachments

You can enable uploading attachments.

```php
LarabergNova::make(__('Content'), 'content')->withFiles('public')
```

You will need to add the following migration to make this work.

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

