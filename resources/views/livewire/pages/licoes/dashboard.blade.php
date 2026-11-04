<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    //
}; ?>

<div class="py-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto select-none">
    
    <!-- Painel Principal de Boas-Vindas e Apresentação -->
    <div class="rounded-2xl p-6 sm:p-10 shadow-2xl relative overflow-hidden" style="background: linear-gradient(135deg, #061122 0%, #030814 100%); border: 1px solid #1e3a5f;">
        
        <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start gap-6 sm:gap-8">
            <!-- Brasão Oficial Proporcional com Sombra Naval -->
            <div class="shrink-0 p-3 rounded-2xl" style="background-color: rgba(0, 0, 0, 0.4); border: 1px solid rgba(30, 58, 95, 0.5);">
                <img src="{{ asset('img/brasao_0.png') }}" alt="Brasão ComForS" class="h-24 sm:h-28 w-auto object-contain drop-shadow-[0_8px_16px_rgba(0,0,0,0.8)]">
            </div>

            <!-- Textos de Apresentação -->
            <div class="flex-1 text-center md:text-left">
                <!-- Tag de Status -->
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-mono mb-3" style="background-color: #0c1e3d; color: #93c5fd; border: 1px solid #1d4ed8;">
                    <span class="w-2 h-2 rounded-full animate-pulse" style="background-color: #3b82f6;"></span>
                    <span>Módulo Operacional Ativo</span>
                </div>

                <!-- Título Principal -->
                <h1 class="text-2xl sm:text-4xl font-bold tracking-tight uppercase leading-tight" style="font-family: 'Oswald', sans-serif; color: #ffffff;">
                    Bem-vindo ao Módulo <span style="color: #eab308;">Lições Aprendidas</span>
                </h1>

                <!-- Resumo Operacional -->
                <p class="text-sm sm:text-base text-gray-300 mt-3 leading-relaxed max-w-3xl" style="color: #d1d5db;">
                    Este é o repositório institucional e doutrinário do <strong>Comando da Força de Submarinos (ComForS)</strong>, destinado à preservação do conhecimento tático, registro de fainas de imersão, ocorrências técnicas e manutenções preventivas das tripulações dos submarinos da Esquadra Brasileira.
                </p>

                <!-- Faixa Integrada de Dados do Militar -->
                <div class="mt-6 pt-5 flex flex-wrap items-center justify-between gap-3 text-xs font-mono" style="border-top: 1px solid rgba(30, 58, 95, 0.6); color: #9ca3af;">
                    <div class="flex items-center gap-2">
                        <span>Militar:</span>
                        <strong class="text-white font-semibold" style="color: #ffffff;">{{ auth()->user()->name }}</strong>
                        <span class="px-2 py-0.5 rounded text-[11px]" style="background-color: #0c1e3d; color: #facc15; border: 1px solid #1d4ed8;">NIP: {{ auth()->user()->nip }}</span>
                    </div>

                    <div class="font-semibold text-yellow-500/90" style="color: #eab308;">
                        ComForS - CLTI • “USQUE AD SUB ACQUAM NAUTA SUM”
                    </div>
                </div>
            </div>
        </div>

        <!-- Efeito sutil de iluminação de fundo -->
        <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full blur-3xl pointer-events-none" style="background: radial-gradient(circle, rgba(37,99,235,0.15) 0%, transparent 70%);"></div>
    </div>

</div>
