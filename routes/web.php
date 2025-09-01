<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ConfiguracaoController;
use App\Http\Controllers\EntidadeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ROTAS DE USUÁRIOS
    Route::resource('users', UserController::class)->middleware(['role:Admin']);

    // ROTAS DE CONFIGURAÇÕES
    Route::get('configuracoes', [ConfiguracaoController::class, 'edit'])->name('configuracoes.edit')->middleware(['role:Admin']);
    Route::patch('configuracoes', [ConfiguracaoController::class, 'update'])->name('configuracoes.update')->middleware(['role:Admin']);

    // ROTAS DE CLIENTES
    Route::get('/clientes', [EntidadeController::class, 'index'])->name('clientes.index')->middleware(['role:Admin']);
    Route::get('/clientes/criar', [EntidadeController::class, 'create'])->name('clientes.create')->middleware(['role:Admin']);
    Route::post('/clientes', [EntidadeController::class, 'store'])->name('clientes.store')->middleware(['role:Admin']);

    // ROTAS DE FORNECEDORES
    Route::get('/fornecedores', [EntidadeController::class, 'index'])->name('fornecedores.index')->middleware(['role:Admin']);
    Route::get('/fornecedores/criar', [EntidadeController::class, 'create'])->name('fornecedores.create')->middleware(['role:Admin']);
    Route::post('/fornecedores', [EntidadeController::class, 'store'])->name('fornecedores.store')->middleware(['role:Admin']);

});

require __DIR__ . '/auth.php';
