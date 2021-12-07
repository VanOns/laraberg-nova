<?php

namespace VanOns\LarabergNova\Attachment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use VanOns\LarabergNova\LarabergNova;

class StorePendingAttachment
{
    /**
     * The field instance.
     *
     * @var Attachment
     */
    public $field;

    /**
     * Create a new invokable instance.
     *
     * @param LarabergNova $field
     * @return void
     */
    public function __construct(LarabergNova $field)
    {
        $this->field = $field;
    }

    /**
     * Attach a pending attachment to the field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function __invoke(Request $request)
    {
        $disk = $this->field->getStorageDisk();

        return Storage::disk($disk)->url(PendingAttachment::create([
            'draft_id' => $request->draftId,
            'attachment' => $request->file('attachment')->store($this->field->getStorageDir(), $disk),
            'disk' => $disk,
        ])->attachment);
    }
}
