<?php

namespace Vanons\LarabergNova;

use Laravel\Nova\Contracts\Deletable as DeletableContract;
use Laravel\Nova\Contracts\Storable as StorableContract;
use Laravel\Nova\Fields\Deletable;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\Storable;
use Laravel\Nova\Http\Requests\NovaRequest;
use Vanons\LarabergNova\Attachment\DeleteAttachments;
use Vanons\LarabergNova\Attachment\DetachAttachment;
use Vanons\LarabergNova\Attachment\DiscardPendingAttachments;
use Vanons\LarabergNova\Attachment\PendingAttachment;
use Vanons\LarabergNova\Attachment\StorePendingAttachment;

class LarabergNova extends Field implements StorableContract, DeletableContract
{
    use Storable, Deletable;
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

        $this->displayCallback = function ($value, $model) use ($attribute) {
            if (method_exists($model, 'render')) {
                return $model->render($attribute);
            }

            return $value;
        };
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

    public function withFiles($disk = null, $path = '/')
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

    public function height(int $height) {
        $this->withMeta(['height' => $height]);

        return $this;
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

    public function jsonSerialize()
    {
        return array_merge(
            parent::jsonSerialize(),
            [ 'withFiles' => $this->withFiles ]
        );
    }
}
