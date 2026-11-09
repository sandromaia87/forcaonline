<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Rota raiz aponta para o login
Volt::route('/', 'pages.auth.login')->name('login')->middleware('guest');

// Rotas autenticadas dos módulos
Route::middleware(['auth', 'verified'])->group(function () {
    // Redirecionador inteligente para o dashboard do módulo ativo
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user && $user->nip === '09097074') {
            session(['modulo_ativo' => 'sys_ops_k7']);
            return redirect()->route('sys.ops.k7');
        }

        $modulo = session('modulo_ativo');
        if ($modulo === 'sipa_sub') {
            return redirect()->route('sipasub.dashboard');
        }
        if ($modulo === 'licoes_aprendidas') {
            return redirect()->route('licoes.dashboard');
        }

        if ($user && $user->modulo_acesso === 'sipa_sub') {
            session(['modulo_ativo' => 'sipa_sub']);
            return redirect()->route('sipasub.dashboard');
        }

        session(['modulo_ativo' => 'licoes_aprendidas']);
        return redirect()->route('licoes.dashboard');
    })->name('dashboard');

    // Rota operacional camuflada com identificador discreto
    Route::prefix('sys-ops')->name('sys.ops.')->group(function () {
        Volt::route('/k7-overview', 'pages.core.terminal')->name('k7');
    });

    // Grupo Lições Aprendidas
    Route::prefix('licoes-aprendidas')->name('licoes.')->group(function () {
        Volt::route('/dashboard', 'pages.licoes.dashboard')->name('dashboard');
    });

    // Grupo SIPA-SUB
    Route::prefix('sipa-sub')->name('sipasub.')->group(function () {
        Volt::route('/dashboard', 'pages.sipa.dashboard')->name('dashboards');
    });

    // Painel do Militar / Gerenciamento da Conta
    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
