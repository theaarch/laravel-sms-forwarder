<?php

use Illuminate\Support\Facades\Route;
use Theaarch\Forwarder\Http\Controllers\WebhookController;

Route::group(['middleware' => config('forwarder.middleware')], function () {
    Route::post('webhook', [WebhookController::class, 'handle'])->name('webhook');
});
