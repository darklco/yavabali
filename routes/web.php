<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/api/docs', function () {
    return view('product');
});

Route::get('/docs', function () {
    $jsonPath = storage_path('api-docs/api-docs.json');
    if (!File::exists($jsonPath)) {
        abort(404, 'File not found');
    }

    $json = File::get($jsonPath);
    return Response::make($json, 200, [
        'Content-Type' => 'application/json',
    ]);
})->name('l5-swagger.default.docs');