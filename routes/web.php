<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Rota raiz aponta para o login
Volt::route('/', 'pages.auth.login')->name('login')->middleware('guest');

// Rotas isoladas dos módulos
Route::middleware(['auth', 'verified'])->group(function () {

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
