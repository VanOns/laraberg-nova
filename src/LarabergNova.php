<?php

namespace VanOns\LarabergNova;

use Illuminate\Support\Str;
use Laravel\Nova\Contracts\Deletable as DeletableContract;
use Laravel\Nova\Contracts\Storable as StorableContract;
use Laravel\Nova\Fields\Deletable;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\Storable;
use Laravel\Nova\Http\Requests\NovaRequest;
use VanOns\LarabergNova\Attachment\DeleteAttachments;
use VanOns\LarabergNova\Attachment\DetachAttachment;
use VanOns\LarabergNova\Attachment\DiscardPendingAttachments;
use VanOns\LarabergNova\Attachment\PendingAttachment;
use VanOns\LarabergNova\Attachment\StorePendingAttachment;

class LarabergNova extends Field implements StorableContract, DeletableContract
{
    use Storable;
    use Deletable;
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'laraberg-nova';

    public $showOnIndex = false;

    public $withFiles = false;

    /**
     * The callback that should be executed to store file attachments.
     *
     * @var callable
     */
    public $attachCallback;

    /**
     * The callback that should be executed to delete persisted file attachments.
     *
     * @var callable
     */
    public $detachCallback;

    /**
     * The callback that should be executed to discard file attachments.
     *
     * @var callable
     */
    public $discardCallback;


    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->loadDefaults();

        $this->displayCallback = function ($value, $model) use ($attribute) {
            if (method_exists($model, 'render')) {
                return $model->render($attribute);
            }

            return $value;
        };
    }

    protected function loadDefaults()
    {
        $this->settings(
            $this->keysToCamelRecursive(
                config('laraberg-nova.defaults', [])
            )
        );
    }

    protected function keysToCamelRecursive(array $arr)
    {
        $result = [];
        foreach ($arr as $key => $value) {
            $camel = str_starts_with($key, '__')
                ? '__' . Str::camel($key)
                : Str::camel($key);

            $result[$camel] = is_array($value)
                ? $this->keysToCamelRecursive($value)
                : $value;
        }

        return $result;
    }

    /**
     * Specify the callback that should be used to store file attachments.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function attach(callable $callback)
    {
        $this->withFiles = true;

        $this->attachCallback = $callback;

        return $this;
    }

    /**
     * Specify the callback that should be used to delete a single, persisted file attachment.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function detach(callable $callback)
    {
        $this->withFiles = true;

        $this->detachCallback = $callback;

        return $this;
    }

    /**
     * Specify the callback that should be used to discard pending file attachments.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function discard(callable $callback)
    {
        $this->withFiles = true;

        $this->discardCallback = $callback;

        return $this;
    }

    /**
     * Specify the callback that should be used to delete the field.
     *
     * @param  callable  $deleteCallback
     * @return $this
     */
    public function delete(callable $deleteCallback)
    {
        $this->withFiles = true;

        $this->deleteCallback = $deleteCallback;

        return $this;
    }

    public function withFiles($disk, $path = '/')
    {
        $this->withFiles = true;

        $this->disk($disk)->path($path);

        $this->attach(new StorePendingAttachment($this))
            ->detach(new DetachAttachment($this))
            ->delete(new DeleteAttachments($this))
            ->discard(new DiscardPendingAttachments($this))
            ->prunable();

        return $this;
    }

    public function height(int $height): LarabergNova
    {
        return $this->withSettings(['height' => $height]);
    }

    public function disabledCoreBlocks(array $disabledCoreBlocks): LarabergNova
    {
        return $this->withSettings(compact('disabledCoreBlocks'));
    }

    public function alignWide(bool $alignWide): LarabergNova
    {
        return $this->withSettings(compact('alignWide'));
    }

    public function supportsLayout(bool $supportsLayout): LarabergNova
    {
        return $this->withSettings(compact('supportsLayout'));
    }

    public function maxWidth(int $maxWidth): LarabergNova
    {
        return $this->withSettings(compact('maxWidth'));
    }

    public function imageEditing(bool $imageEditing): LarabergNova
    {
        return $this->withSettings(compact('imageEditing'));
    }

    public function colors(array $colors): LarabergNova
    {
        return $this->withSettings(compact('colors'));
    }

    public function gradients(array $gradients): LarabergNova
    {
        return $this->withSettings(compact('gradients'));
    }

    public function fontSizes(array $fontSizes): LarabergNova
    {
        return $this->withSettings(compact('fontSizes'));
    }

    public function withSettings(array $settings): LarabergNova
    {
        $settings = array_merge(
            $this->meta['settings'] ?? [],
            $settings
        );

        return $this->withMeta(compact('settings'));
    }

    public function settings(array $settings): LarabergNova
    {
        return $this->withSettings($settings);
    }


    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $requestAttribute
     * @param  object  $model
     * @param  string  $attribute
     * @return void|\Closure
     */
    protected function fillAttribute(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        $callbacks = [];

        $maybeCallback = parent::fillAttribute($request, $requestAttribute, $model, $attribute);
        if (is_callable($maybeCallback)) {
            $callbacks[] = $maybeCallback;
        }

        if ($request->{$this->attribute.'DraftId'} && $this->withFiles) {
            $callbacks[] = function () use ($request, $model) {
                PendingAttachment::persistDraft(
                    $request->{$this->attribute.'DraftId'},
                    $this,
                    $model
                );
            };
        }

        if (count($callbacks)) {
            return function () use ($callbacks) {
                collect($callbacks)->each->__invoke();
            };
        }
    }

    /**
     * Get the full path that the field is stored at on disk.
     *
     * @return string|null
     */
    public function getStoragePath()
    {
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            [ 'withFiles' => $this->withFiles ]
        );
    }
}
