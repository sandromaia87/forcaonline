<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Rota raiz aponta para o login
Volt::route('/', 'pages.auth.login')->name('login')->middleware('guest');

// Rotas isoladas dos módulos (sem ligação direta entre módulos)
Route::middleware(['auth', 'verified'])->group(function () {

    // Redirecionador inteligente para o módulo ativo da sessão
    Route::get('/dashboard', function () {
        $modulo = session('modulo_ativo');

        if ($modulo === 'sipa_sub') {
            return redirect()->route('sipasub.dashboard');
        }

        if ($modulo === 'licoes_aprendidas') {
            return redirect()->route('licoes.dashboard');
        }

        $user = auth()->user();
        if ($user->modulo_acesso === 'sipa_sub') {
            session(['modulo_ativo' => 'sipa_sub']);
            return redirect()->route('sipasub.dashboard');
        }

        session(['modulo_ativo' => 'licoes_aprendidas']);
        return redirect()->route('licoes.dashboard');
    })->name('dashboard');

    // Grupo Lições Aprendidas (Isolado: exige sessão ativa de Lições)
    Route::prefix('licoes-aprendidas')->name('licoes.')->middleware(function ($request, $next) {
        $user = auth()->user();

        if (! $user->temAcessoAoModulo('licoes_aprendidas')) {
            abort(403, 'Acesso não autorizado ao módulo de Lições Aprendidas.');
        }

        // Requisito: Não pode trocar de módulo sem deslogar
        if (session('modulo_ativo') && session('modulo_ativo') !== 'licoes_aprendidas') {
            return redirect()->route('sipasub.dashboard');
        }

        session(['modulo_ativo' => 'licoes_aprendidas']);
        return $next($request);
    })->group(function () {
        Volt::route('/dashboard', 'pages.licoes.dashboard')->name('dashboard');
    });

    // Grupo SIPA-SUB (Isolado: exige sessão ativa de SIPA-SUB)
    Route::prefix('sipa-sub')->name('sipasub.')->middleware(function ($request, $next) {
        $user = auth()->user();

        if (! $user->temAcessoAoModulo('sipa_sub')) {
            abort(403, 'Acesso não autorizado ao módulo SIPA-SUB.');
        }

        // Requisito: Não pode trocar de módulo sem deslogar
        if (session('modulo_ativo') && session('modulo_ativo') !== 'sipa_sub') {
            return redirect()->route('licoes.dashboard');
        }

        session(['modulo_ativo' => 'sipa_sub']);
        return $next($request);
    })->group(function () {
        Volt::route('/dashboard', 'pages.sipa.dashboard')->name('dashboard');
    });

});

require __DIR__.'/auth.php';
