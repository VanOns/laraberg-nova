<?php

use Illuminate\Support\Facades\Route;
use VanOns\LarabergNova\Http\Controllers\AttachmentController;

Route::post('/{resource}/attachment/{field}', [AttachmentController::class, 'store']);
Route::delete('/{resource}/attachment/{field}', [AttachmentController::class, 'destroyAttachment']);
Route::delete('/{resource}/attachment/{field}/{draftId}', [AttachmentController::class, 'destroyPending']);
