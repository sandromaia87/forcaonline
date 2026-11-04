<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    //
}; ?>

<div class="py-12 bg-[#030b17] min-h-screen text-white select-none">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Painel de Apresentação e Boas-Vindas -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-black via-black/90 to-[#120e06] border border-amber-900/60 p-8 sm:p-12 shadow-2xl">
            <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start gap-8">
                <img src="{{ asset('img/brasao_0.png') }}" alt="Brasão ComForS" class="h-28 sm:h-32 w-auto drop-shadow-[0_10px_20px_rgba(0,0,0,0.8)] object-contain">

                <div class="flex-1 text-center md:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-950/80 border border-amber-800 text-amber-300 text-xs font-mono mb-3">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                        <span>Módulo de Segurança e Prevenção Ativo</span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl font-bold tracking-tight uppercase" style="font-family: 'Oswald', sans-serif;">
                        Bem-vindo ao Módulo <span class="text-amber-500">SIPA-SUB</span>
                    </h1>

                    <p class="text-sm sm:text-base text-gray-300 mt-4 leading-relaxed max-w-3xl">
                        Este é o <strong>Sistema de Investigação e Prevenção de Acidentes de Submarinos (SIPA-SUB)</strong> do Comando da Força de Submarinos, responsável pelo gerenciamento de Relatórios de Prevenção (RELPREVs), monitoramento de integridade estrutural, vistorias de prontidão de escape e auditorias de segurança de mergulho.
                    </p>

                    <div class="mt-6 pt-6 border-t border-gray-800/80 flex flex-wrap items-center justify-between gap-4 text-xs font-mono text-gray-400">
                        <div>
                            <span>Militar Conectado: </span>
                            <strong class="text-white">{{ auth()->user()->name ?? 'Militar Autenticado' }}</strong>
                            <span class="text-gray-500"> (NIP: {{ auth()->user()->nip ?? '00000000' }})</span>
                        </div>
                        <div class="text-yellow-500/90 font-semibold">
                            ComForS - CLTI • “USQUE AD SUB ACQUAM NAUTA SUM”
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fundo Decorativo -->
            <div class="absolute -right-12 -bottom-12 w-80 h-80 bg-amber-600/10 rounded-full blur-3xl pointer-events-none"></div>
        </div>

        <!-- Rodapé do Módulo com Assinatura Permanente -->
        <div class="mt-8 text-center text-xs text-gray-400 font-mono">
            Desenvolvido por: <strong class="text-white font-bold tracking-wider underline decoration-yellow-500/80 underline-offset-2">2º SG PD SANDRO</strong> • ComForS
        </div>

    </div>
</div>
