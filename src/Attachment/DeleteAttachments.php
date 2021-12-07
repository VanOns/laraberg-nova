<?php

namespace VanOns\LarabergNova\Attachment;

use Illuminate\Http\Request;
use VanOns\LarabergNova\LarabergNova;

class DeleteAttachments
{
    /**
     * The field instance.
     *
     * @var \Laraberg\Nova\Fields\Attachment
     */
    public $field;

    /**
     * Create a new class instance.
     *
     * @param LarabergNova $field
     * @return void
     */
    public function __construct(LarabergNova $field)
    {
        $this->field = $field;
    }

    /**
     * Delete the attachments associated with the field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $model
     * @return array
     */
    public function __invoke(Request $request, $model)
    {
        Attachment::where('attachable_type', $model->getMorphClass())
                ->where('attachable_id', $model->getKey())
                ->get()
                ->each
                ->purge();

        return [$this->field->attribute => ''];
    }
}
