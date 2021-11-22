<?php

use Illuminate\Support\Facades\Route;

Route::post('/{resource}/laraberg-nova-attachment/{field}', 'LarabergNovaAttachmentController@store');
Route::delete('/{resource}/laraberg-nova-attachment/{field}', 'LarabergNovaAttachmentController@destroyAttachment');
Route::delete('/{resource}/laraberg-nova-attachment/{field}/{draftId}', 'LarabergNovaAttachmentController@destroyPending');
