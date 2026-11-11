<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public function mount(): void
    {
        $user = auth()->user();

        // 1. Verifica permissão de acesso ao módulo
        if (! $user || ! $user->temAcessoAoModulo('licoes_aprendidas')) {
            abort(403, 'Acesso não autorizado ao módulo de Lições Aprendidas.');
        }

        // 2. Isolamento de sessão: não pode trocar de módulo sem deslogar
        if (session('modulo_ativo') && session('modulo_ativo') !== 'licoes_aprendidas') {
            $this->redirectRoute('sipasub.dashboard');
            return;
        }

        session(['modulo_ativo' => 'licoes_aprendidas']);
    }
}; ?>

<div class="space-y-6 select-none">
    
    <!-- Banner de Boas-Vindas Compacto e Discreto (Padrão Argon Naval) -->
    <div class="rounded-xl px-5 py-3.5 bg-gradient-to-r from-[#061122] via-[#040b17] to-[#02060e] border border-blue-900/40 shadow-lg flex flex-col sm:flex-row items-center justify-between gap-4">
        
        <div class="flex items-center gap-3.5">
            <div class="shrink-0 p-1.5 rounded-lg bg-black/40 border border-blue-900/40">
                <img src="{{ asset('img/brasao_0.png') }}" alt="Brasão ComForS" class="h-10 w-auto object-contain drop-shadow">
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-sm sm:text-base font-bold uppercase text-white tracking-wide" style="font-family: 'Oswald', sans-serif;">
                        Módulo <span class="text-yellow-500" style="color: #eab308;">Lições Aprendidas</span>
                    </h1>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-mono bg-blue-950/80 text-blue-300 border border-blue-800/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                        Ativo
                    </span>
                </div>
                <p class="text-[9px] text-gray-400 font-mono mt-0.5">
                    Comando da Força de Submarinos • ComForS
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 text-xs font-mono text-gray-400 bg-black/30 px-3 py-1.5 rounded-lg border border-gray-800">
            <span>Militar Conectado:</span>
            <strong class="text-white font-semibold">{{ auth()->user()->name }}</strong>
            <span class="text-yellow-400 font-bold">NIP {{ auth()->user()->nip }}</span>
        </div>

    </div>

</div>
