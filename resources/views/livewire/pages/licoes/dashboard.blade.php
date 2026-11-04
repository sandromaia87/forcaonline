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
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-black via-black/90 to-[#060f1d] border border-blue-900/60 p-8 sm:p-12 shadow-2xl">
            <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start gap-8">
                <img src="{{ asset('img/brasao_0.png') }}" alt="Brasão ComForS" class="h-28 sm:h-32 w-auto drop-shadow-[0_10px_20px_rgba(0,0,0,0.8)] object-contain">

                <div class="flex-1 text-center md:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-950/80 border border-blue-800 text-blue-300 text-xs font-mono mb-3">
                        <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                        <span>Módulo Operacional Ativo</span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl font-bold tracking-tight uppercase" style="font-family: 'Oswald', sans-serif;">
                        Bem-vindo ao Módulo <span class="text-yellow-500">Lições Aprendidas</span>
                    </h1>

                    <p class="text-sm sm:text-base text-gray-300 mt-4 leading-relaxed max-w-3xl">
                        Este é o repositório institucional e doutrinário do <strong>Comando da Força de Submarinos (ComForS)</strong>, destinado à preservação do conhecimento tático, registro de fainas de imersão, ocorrências técnicas e manutenções preventivas das tripulações dos submarinos da Esquadra Brasileira.
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
            <div class="absolute -right-12 -bottom-12 w-80 h-80 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
        </div>

        <!-- Rodapé do Módulo com Assinatura Permanente -->
        <div class="mt-8 text-center text-xs text-gray-400 font-mono">
            Desenvolvido por: <strong class="text-white font-bold tracking-wider underline decoration-yellow-500/80 underline-offset-2">2º SG PD SANDRO</strong> • ComForS
        </div>

    </div>
</div>
