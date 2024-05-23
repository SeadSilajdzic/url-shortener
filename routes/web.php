<?php

use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/generate');

Route::post('/shorten', [UrlController::class, 'shorten']);
Route::post('{subdir}/shorten', [UrlController::class, 'shorten'])->where('subdir', '(.*)');

Route::get('/{hash}', [UrlController::class, 'redirect'])
    ->where('hash', '^(?!.*(?:shorten|generate)$).*')
    ->name('redirect');

Route::get('/generate', function () {
    return view('welcome');
});

Route::get('/{subdir}/generate', function () {
    return view('welcome');
})->where('subdir', '(.*)');

Route::fallback(function() {
    abort(404);
});
