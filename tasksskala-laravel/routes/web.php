<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\ContaBancariaController;
use App\Http\Controllers\Admin\ContaPagarController;
use App\Http\Controllers\Admin\ContaReceberController;
use App\Http\Controllers\Admin\FluxoCaixaController;
use App\Http\Controllers\Admin\CategoriaFinanceiraController;
use App\Http\Controllers\Admin\DashboardFinanceiraController;
use App\Http\Controllers\Admin\ImportacaoOfxController;
use App\Http\Controllers\Admin\TipoCustoController;
use App\Http\Controllers\Admin\FornecedorController;
use App\Http\Controllers\Admin\PlanoContasController;
use App\Http\Controllers\Admin\ClienteController;

// Rota principal - redireciona para admin login
Route::get('/', function () {
    return redirect('/admin/login');
})->name('home');

// Rotas de autenticação do admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});

// Rotas do Admin (protegidas)
Route::prefix('admin')->name('admin.')->middleware(['web', App\Http\Middleware\AdminAuth::class])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    
    // Rotas do Sistema Financeiro
    Route::get('dashboard-financeira', [DashboardFinanceiraController::class, 'index'])->name('dashboard-financeira.index');
    Route::get('fluxo-caixa', [FluxoCaixaController::class, 'index'])->name('fluxo-caixa.index');
    
    Route::resource('plano-contas', PlanoContasController::class)->parameters([
        'plano-contas' => 'planoConta'
    ]);
    
    Route::resource('tipos-custo', TipoCustoController::class)->parameters([
        'tipos-custo' => 'tipoCusto'
    ]);
    
    Route::resource('categorias-financeiras', CategoriaFinanceiraController::class)->parameters([
        'categorias-financeiras' => 'categoria_financeira'
    ]);
    
    Route::resource('contas-bancarias', ContaBancariaController::class)->parameters([
        'contas-bancarias' => 'conta_bancaria'
    ]);
    
    Route::resource('clientes', ClienteController::class)->parameters([
        'clientes' => 'cliente'
    ]);
    
    Route::resource('fornecedores', FornecedorController::class)->parameters([
        'fornecedores' => 'fornecedor'
    ]);
    
    Route::resource('contas-pagar', ContaPagarController::class)->parameters([
        'contas-pagar' => 'conta_pagar'
    ]);
    Route::post('contas-pagar/{conta_pagar}/pagar', [ContaPagarController::class, 'pagar'])->name('contas-pagar.pagar');
    
    Route::resource('contas-receber', ContaReceberController::class)->parameters([
        'contas-receber' => 'conta_receber'
    ]);
    Route::post('contas-receber/{conta_receber}/receber', [ContaReceberController::class, 'receber'])->name('contas-receber.receber');
    
    // Rotas de Importação OFX
    Route::get('importacao-ofx', [ImportacaoOfxController::class, 'index'])->name('importacao-ofx.index');
    Route::get('importacao-ofx/importar', [ImportacaoOfxController::class, 'create'])->name('importacao-ofx.create');
    Route::post('importacao-ofx/importar', [ImportacaoOfxController::class, 'store'])->name('importacao-ofx.store');
    Route::get('importacao-ofx/conciliar', [ImportacaoOfxController::class, 'conciliar'])->name('importacao-ofx.conciliar');
    Route::post('importacao-ofx/conciliar/{transacao}', [ImportacaoOfxController::class, 'conciliarTransacao'])->name('importacao-ofx.conciliar-transacao');
    Route::get('importacao-ofx/buscar-contas/{transacao}', [ImportacaoOfxController::class, 'buscarContasSugeridas'])->name('importacao-ofx.buscar-contas');
});
