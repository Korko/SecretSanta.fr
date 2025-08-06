<?php

/*
|--------------------------------------------------------------------------
| Routes Webhook
|--------------------------------------------------------------------------
*/

Route::prefix('webhooks')->group(function () {
    // Webhook pour les jobs échoués
    Route::post('/failed-jobs', function () {
        // Logique pour gérer les jobs échoués
    });
});
