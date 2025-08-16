<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::get('/', [ContactController::class, 'index'])->name('contact.index');
Route::post('/guardar-mensaje', [ContactController::class, 'store'])->name('contact.store');
Route::get('/registros', [ContactController::class, 'registros'])->name('contact.registros');

Route::get('/', function () {
    return view('welcome');
});
