<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public function mount(): void
    {
        $user = auth()->user();

        // 1. Verifica permissão de acesso ao módulo
        if (! $user || ! $user->temAcessoAoModulo('sipa_sub')) {
            abort(403, 'Acesso não autorizado ao módulo SIPA-SUB.');
        }

        // 2. Isolamento de sessão: não pode trocar de módulo sem deslogar
        if (session('modulo_ativo') && session('modulo_ativo') !== 'sipa_sub') {
            $this->redirectRoute('licoes.dashboard');
            return;
        }

        session(['modulo_ativo' => 'sipa_sub']);
    }
}; ?>

<div class="px-4 sm:px-6 lg:px-8 py-8 max-w-6xl mx-auto select-none">
    
    <!-- Painel Oficial de Boas-Vindas no Padrão Argon Dashboard -->
    <div class="rounded-2xl p-6 sm:p-10 shadow-2xl relative overflow-hidden bg-gradient-to-br from-[#1a1104] via-[#0f0901] to-[#050300] border border-amber-900/40">
        
        <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start gap-6 sm:gap-8">
            <!-- Brasão Oficial Proporcional com Sombra Naval -->
            <div class="shrink-0 p-3 rounded-2xl bg-black/40 border border-amber-900/40 shadow-xl">
                <img src="{{ asset('img/brasao_0.png') }}" alt="Brasão ComForS" class="h-24 sm:h-28 w-auto object-contain drop-shadow-[0_8px_16px_rgba(0,0,0,0.8)]">
            </div>

            <!-- Textos de Apresentação -->
            <div class="flex-1 text-center md:text-left">
                <!-- Tag de Status do Módulo -->
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-mono mb-3 bg-amber-950/60 text-amber-300 border border-amber-800/60">
                    <span class="w-2 h-2 rounded-full animate-pulse bg-amber-500"></span>
                    <span>Módulo de Segurança e Prevenção Ativo</span>
                </div>

                <!-- Título Principal -->
                <h1 class="text-2xl sm:text-4xl font-bold tracking-tight uppercase leading-tight text-white" style="font-family: 'Oswald', sans-serif;">
                    Bem-vindo ao Módulo <span class="text-amber-500" style="color: #f59e0b;">SIPA-SUB</span>
                </h1>

                <!-- Resumo Institucional -->
                <p class="text-sm sm:text-base text-gray-300 mt-3 leading-relaxed max-w-3xl">
                    Este é o <strong>Sistema de Investigação e Prevenção de Acidentes de Submarinos (SIPA-SUB)</strong> do Comando da Força de Submarinos, responsável pelo gerenciamento de notificações preventivas, integridade estrutural e segurança operacional das tripulações dos submarinos da Marinha do Brasil.
                </p>

                <!-- Faixa de Dados do Militar Autenticado -->
                <div class="mt-6 pt-5 flex flex-wrap items-center justify-between gap-3 text-xs font-mono border-t border-amber-900/40 text-gray-400">
                    <div class="flex items-center gap-2">
                        <span>Militar Conectado:</span>
                        <strong class="text-white font-semibold">{{ auth()->user()->name }}</strong>
                        <span class="px-2 py-0.5 rounded text-[11px] bg-amber-950/60 text-yellow-400 border border-amber-800/60 font-bold">NIP: {{ auth()->user()->nip }}</span>
                    </div>

                    <div class="font-semibold text-yellow-500/90">
                        ComForS - CLTI • “USQUE AD SUB ACQUAM NAUTA SUM”
                    </div>
                </div>
            </div>
        </div>

        <!-- Efeito sutil de iluminação de fundo -->
        <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full blur-3xl pointer-events-none bg-amber-600/10"></div>
    </div>

</div>
