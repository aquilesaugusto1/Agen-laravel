<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaParceiraController;
use App\Http\Controllers\ConsultorController;
use App\Http\Controllers\TechLeadController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ApontamentoController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\ProjetoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\ImportController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('/apontamentos', [ApontamentoController::class, 'index'])->name('apontamentos.index');
    Route::post('/apontamentos', [ApontamentoController::class, 'storeOrUpdate'])->name('apontamentos.storeOrUpdate');
    Route::get('/api/agendas', [ApontamentoController::class, 'getAgendasAsEvents'])->name('api.agendas');

    Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
    Route::post('/relatorios', [RelatorioController::class, 'gerar'])->name('relatorios.gerar');
    
    Route::get('agendas/alocacao', [AgendaController::class, 'alocacao'])->name('agendas.alocacao');
    Route::resource('agendas', AgendaController::class);

    Route::middleware('role:admin,techlead')->group(function () {
        Route::resource('consultores', ConsultorController::class)
             ->parameters(['consultores' => 'consultor'])
             ->except(['destroy']);
        
        Route::get('/enviar-agendas', [EmailController::class, 'create'])->name('email.agendas.create');
        Route::post('/enviar-agendas', [EmailController::class, 'send'])->name('email.agendas.send');
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('empresas', EmpresaParceiraController::class);
        Route::resource('techleads', TechLeadController::class);
        Route::resource('projetos', ProjetoController::class);
        Route::delete('consultores/{consultor}', [ConsultorController::class, 'destroy'])->name('consultores.destroy');
        
        Route::get('/importar', [ImportController::class, 'create'])->name('imports.create');
        Route::post('/importar/upload', [ImportController::class, 'upload'])->name('imports.upload');
        Route::get('/importar/mapeamento', [ImportController::class, 'mapping'])->name('imports.mapping');
        Route::post('/importar/processar', [ImportController::class, 'process'])->name('imports.process');
    });
});

require __DIR__.'/auth.php';
