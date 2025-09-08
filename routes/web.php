<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ConfiguracaoController;
use App\Http\Controllers\EntidadeController;
use App\Http\Controllers\EnderecoController;
use App\Http\Controllers\ProdutoController;
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
    Route::get('/clientes/{entidade}/editar', [EntidadeController::class, 'edit'])->name('clientes.edit')->middleware(['role:Admin']);
    Route::patch('/clientes/{entidade}', [EntidadeController::class, 'update'])->name('clientes.update')->middleware(['role:Admin']);
    Route::delete('/clientes/{entidade}', [EntidadeController::class, 'destroy'])->name('clientes.destroy')->middleware(['role:Admin']);

    // ROTAS DE FORNECEDORES
    Route::get('/fornecedores', [EntidadeController::class, 'index'])->name('fornecedores.index')->middleware(['role:Admin']);
    Route::get('/fornecedores/criar', [EntidadeController::class, 'create'])->name('fornecedores.create')->middleware(['role:Admin']);
    Route::post('/fornecedores', [EntidadeController::class, 'store'])->name('fornecedores.store')->middleware(['role:Admin']);
    Route::get('/fornecedores/{entidade}/editar', [EntidadeController::class, 'edit'])->name('fornecedores.edit')->middleware(['role:Admin']);
    Route::patch('/fornecedores/{entidade}', [EntidadeController::class, 'update'])->name('fornecedores.update')->middleware(['role:Admin']);
    Route::delete('/fornecedores/{entidade}', [EntidadeController::class, 'destroy'])->name('fornecedores.destroy')->middleware(['role:Admin']);

    Route::post('/entidades/verificar-duplicidade', [EntidadeController::class, 'verificarDuplicidade'])->name('entidades.verificarDuplicidade')->middleware(['role:Admin']);

    // ROTAS DE PRODUTOS
    Route::resource('produtos', ProdutoController::class)->middleware(['role:Admin']);
    Route::get('/produtos-primarios', [ProdutoController::class, 'getProdutosPrimarios'])->name('produtos.primarios')->middleware(['role:Admin']);
    //Route::get('/produtos/buscar-primarios', [ProdutoController::class, 'buscarProdutosPrimarios'])->name('produtos.buscarPrimarios')->middleware(['role:Admin']);
    Route::get('/produtos/buscar/primarios', [ProdutoController::class, 'buscarProdutosPrimarios'])->name('produtos.buscar.primarios')->middleware(['role:Admin']);


    // ROTAS PARA GERENCIAMENTO DE ENDEREÇOS (serão chamadas via AJAX)
    Route::prefix('entidades/{entidade}')->name('enderecos.')->middleware(['role:Admin'])->group(function () {
        Route::get('/enderecos', [EnderecoController::class, 'index'])->name('index');
        Route::post('/enderecos', [EnderecoController::class, 'store'])->name('store');
        Route::get('/enderecos/{endereco}', [EnderecoController::class, 'show'])->name('show');
        Route::patch('/enderecos/{endereco}', [EnderecoController::class, 'update'])->name('update');
        Route::delete('/enderecos/{endereco}', [EnderecoController::class, 'destroy'])->name('destroy');
    });

});

require __DIR__ . '/auth.php';
