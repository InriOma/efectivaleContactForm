<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::get('/', [ContactController::class, 'index'])->name('contact.index');
Route::post('/guardar-mensaje', [ContactController::class, 'store'])->name('contact.store');
Route::get('/registros', [ContactController::class, 'registros'])->name('contact.registros');
Route::delete('/mensaje/{id}', [ContactController::class, 'destroy'])->name('contact.destroy');
Route::delete('/mensajes/limpiar', [ContactController::class, 'clear'])->name('contact.clear');
Route::get('/mensaje/{id}/editar', [ContactController::class, 'edit'])->name('contact.edit');
Route::put('/mensaje/{id}', [ContactController::class, 'update'])->name('contact.update');

/* Route::get('/', function () {
    return view('welcome');
}); */
