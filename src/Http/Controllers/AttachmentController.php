<?php

namespace VanOns\LarabergNova\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Http\Requests\NovaRequest;

class AttachmentController extends Controller
{
    /**
     * Store an attachment for a Trix field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NovaRequest $request)
    {
        $field = $this->findField($request);

        return response()->json(['url' => call_user_func(
            $field->attachCallback, $request
        )]);
    }

    /**
     * Delete a single, persisted attachment for a Trix field by URL.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function destroyAttachment(NovaRequest $request)
    {
        $field = $this->findField($request);

        call_user_func($field->detachCallback, $request);
    }

    /**
     * Purge all pending attachments for a Trix field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function destroyPending(NovaRequest $request)
    {
        $field = $this->findField($request);

        call_user_func($field->discardCallback, $request);
    }

    /**
     * @param NovaRequest $request
     * @return \Laravel\Nova\Fields\Field
     */
    public function findField(NovaRequest $request) {
        $fields = $request->newResource()->availableFields($request);

        return $this->findFieldRecursive($request->field, $fields);
    }

    protected function findFieldRecursive(string $attribute, FieldCollection $fields) {
        if ($field = $fields->findFieldByAttribute($attribute)) {
            return $field;
        }

        return $fields->map(function ($field) use ($attribute) {
            $subFields = $field->fields ?? $field->meta['fields'] ?? null;

            if (!is_array($subFields)) {
                return null;
            }

            return $this->findFieldRecursive($attribute, FieldCollection::make($subFields));
        })->filter()->first();
    }
}
