<?php

namespace VanOns\LarabergNova\Attachment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use VanOns\LarabergNova\LarabergNova;

class PendingAttachment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'laraberg_nova_pending_attachments';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Persist the given draft's pending attachments.
     *
     * @param string $draftId
     * @param LarabergNova $field
     * @param mixed $model
     * @return void
     */
    public static function persistDraft($draftId, LarabergNova $field, $model)
    {
        static::where('draft_id', $draftId)->get()->each->persist($field, $model);
    }

    /**
     * Persist the pending attachment.
     *
     * @param LarabergNova $field
     * @param mixed $model
     * @return void
     * @throws \Exception
     */
    public function persist(LarabergNova $field, $model)
    {
        $disk = $field->getStorageDisk();

        Attachment::create([
            'attachable_type' => $model->getMorphClass(),
            'attachable_id' => $model->getKey(),
            'attachment' => $this->attachment,
            'disk' => $disk,
            'url' => Storage::disk($disk)->url($this->attachment),
        ]);

        $this->delete();
    }

    /**
     * Purge the attachment.
     *
     * @return void
     */
    public function purge()
    {
        Storage::disk($this->disk)->delete($this->attachment);

        $this->delete();
    }
}
