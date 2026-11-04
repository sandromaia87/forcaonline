<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Rota raiz aponta para o login
Volt::route('/', 'pages.auth.login')->name('login')->middleware('guest');

// Rotas isoladas dos módulos
Route::middleware(['auth', 'verified'])->group(function () {

    // Redirecionador inteligente para o módulo ativo ou autorizado
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $modulo = session('modulo_ativo', $user->modulo_acesso ?? 'licoes_aprendidas');

        if ($modulo === 'sipa_sub') {
            return redirect()->route('sipasub.dashboard');
        }

        return redirect()->route('licoes.dashboard');
    })->name('dashboard');

    // Grupo Lições Aprendidas
    Route::prefix('licoes-aprendidas')->name('licoes.')->group(function () {
        Volt::route('/dashboard', 'pages.licoes.dashboard')->name('dashboard');
        // Adicione outras rotas do módulo Lições aqui
    });

    // Grupo SIPA-SUB
    Route::prefix('sipa-sub')->name('sipasub.')->group(function () {
        Volt::route('/dashboard', 'pages.sipa.dashboard')->name('dashboard');
        // Adicione outras rotas do módulo SIPA-SUB aqui
    });

});

require __DIR__.'/auth.php';
