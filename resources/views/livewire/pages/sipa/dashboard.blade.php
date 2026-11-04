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

<div class="px-4 sm:px-6 lg:px-8 py-6 max-w-7xl mx-auto space-y-6 select-none">
    
    <!-- ========================================================================= -->
    <!-- 1. LINHA DE CARDS DE MÉTRICAS OPERACIONAIS SIPA-SUB (PADRÃO ARGON PRO)   -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Card 1: RELPREVs Ativos -->
        <div class="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl flex items-center justify-between hover:border-amber-500/50 transition-all group">
            <div>
                <p class="text-[11px] font-mono uppercase font-bold tracking-wider text-gray-400">RELPREVs Ativos</p>
                <h3 class="text-2xl font-bold text-white mt-1 group-hover:text-amber-400 transition-colors">18</h3>
                <p class="text-xs text-amber-400 font-semibold mt-1 flex items-center gap-1">
                    <span>! 03 Prioritários</span>
                    <span class="text-gray-400 font-normal">em análise</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-600 to-yellow-500 flex items-center justify-center text-white shadow-lg shadow-amber-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
        </div>

        <!-- Card 2: Vistorias de Escape -->
        <div class="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl flex items-center justify-between hover:border-emerald-500/50 transition-all group">
            <div>
                <p class="text-[11px] font-mono uppercase font-bold tracking-wider text-gray-400">Vistorias de Escape</p>
                <h3 class="text-2xl font-bold text-white mt-1 group-hover:text-emerald-400 transition-colors">32</h3>
                <p class="text-xs text-emerald-400 font-semibold mt-1 flex items-center gap-1">
                    <span>✓ 100% Conforme</span>
                    <span class="text-gray-400 font-normal">sistemas OK</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
        </div>

        <!-- Card 3: Auditorias de Mergulho -->
        <div class="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl flex items-center justify-between hover:border-blue-500/50 transition-all group">
            <div>
                <p class="text-[11px] font-mono uppercase font-bold tracking-wider text-gray-400">Auditorias Imersão</p>
                <h3 class="text-2xl font-bold text-white mt-1 group-hover:text-blue-400 transition-colors">14</h3>
                <p class="text-xs text-blue-400 font-semibold mt-1 flex items-center gap-1">
                    <span>Ciclo 2026/1</span>
                    <span class="text-gray-400 font-normal">homologadas</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-700 to-cyan-500 flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>

        <!-- Card 4: Índice de Segurança SIPA -->
        <div class="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl flex items-center justify-between hover:border-purple-500/50 transition-all group">
            <div>
                <p class="text-[11px] font-mono uppercase font-bold tracking-wider text-gray-400">Segurança Operacional</p>
                <h3 class="text-2xl font-bold text-white mt-1 group-hover:text-purple-400 transition-colors">99.9%</h3>
                <p class="text-xs text-emerald-400 font-semibold mt-1 flex items-center gap-1">
                    <span>↑ Zero Acidentes</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-purple-700 to-indigo-500 flex items-center justify-center text-white shadow-lg shadow-purple-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 2. SEÇÃO PRINCIPAL (2 COLUNAS: 8 COLS + 4 COLS)                            -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Coluna Esquerda (8 cols): Tabela de RELPREVs em Andamento -->
        <div class="lg:col-span-8 space-y-6">
            
            <div class="p-6 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-4 border-b border-gray-800/80 gap-3">
                    <div>
                        <h2 class="text-base font-bold text-white uppercase tracking-wide" style="font-family: 'Oswald', sans-serif;">
                            Relatórios de Prevenção (RELPREV) & Vistorias Recentes
                        </h2>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Gestão de prevenção, integridade e conformidade de equipamentos de salvamento
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono bg-amber-950/60 text-amber-400 border border-amber-800/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                            <span>SIPA Ativo</span>
                        </span>
                    </div>
                </div>

                <!-- Tabela de RELPREVs -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-gray-400 uppercase tracking-wider font-mono border-b border-gray-800/60 text-[10px]">
                                <th class="pb-3 font-semibold">Código</th>
                                <th class="pb-3 font-semibold">Submarino / OM</th>
                                <th class="pb-3 font-semibold">Assunto / Subsistema</th>
                                <th class="pb-3 font-semibold">Classificação</th>
                                <th class="pb-3 font-semibold text-right">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/50">
                            <tr class="hover:bg-gray-800/30 transition-colors">
                                <td class="py-3.5 font-mono text-yellow-400 font-bold">RP-2026-081</td>
                                <td class="py-3.5 font-bold text-white">S-40 Riachuelo</td>
                                <td class="py-3.5 text-gray-300">Válvula de Exaustão de Gases de Bateria</td>
                                <td class="py-3.5">
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-semibold bg-blue-950/60 text-blue-400 border border-blue-800/60">
                                        Preventivo
                                    </span>
                                </td>
                                <td class="py-3.5 text-right font-mono text-emerald-400 font-semibold">Concluído</td>
                            </tr>

                            <tr class="hover:bg-gray-800/30 transition-colors">
                                <td class="py-3.5 font-mono text-yellow-400 font-bold">RP-2026-082</td>
                                <td class="py-3.5 font-bold text-white">S-41 Humaitá</td>
                                <td class="py-3.5 text-gray-300">Guarnição das Roupas Submarinas de Escape</td>
                                <td class="py-3.5">
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-950/60 text-emerald-400 border border-emerald-800/60">
                                        Rotina
                                    </span>
                                </td>
                                <td class="py-3.5 text-right font-mono text-emerald-400 font-semibold">Concluído</td>
                            </tr>

                            <tr class="hover:bg-gray-800/30 transition-colors">
                                <td class="py-3.5 font-mono text-yellow-400 font-bold">RP-2026-083</td>
                                <td class="py-3.5 font-bold text-white">S-42 Tonelero</td>
                                <td class="py-3.5 text-gray-300">Pressostato de Reserva do Tanque de Lastro</td>
                                <td class="py-3.5">
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-950/60 text-amber-400 border border-amber-800/60">
                                        Prioritário
                                    </span>
                                </td>
                                <td class="py-3.5 text-right font-mono text-amber-400 font-semibold">Em Análise</td>
                            </tr>

                            <tr class="hover:bg-gray-800/30 transition-colors">
                                <td class="py-3.5 font-mono text-yellow-400 font-bold">RP-2026-084</td>
                                <td class="py-3.5 font-bold text-white">Base Submarinos</td>
                                <td class="py-3.5 text-gray-300">Recarga do Banco de Ar Respirável de Emergência</td>
                                <td class="py-3.5">
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-semibold bg-blue-950/60 text-blue-400 border border-blue-800/60">
                                        Auditoria
                                    </span>
                                </td>
                                <td class="py-3.5 text-right font-mono text-emerald-400 font-semibold">Concluído</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-800/60 flex items-center justify-between text-xs text-gray-400 font-mono">
                    <span>Mostrando 4 de 18 relatórios registrados</span>
                    <button type="button" class="text-amber-400 hover:text-amber-300 font-semibold cursor-pointer">Ver todos os RELPREVs →</button>
                </div>
            </div>

        </div>

        <!-- Coluna Direita (4 cols): Card Destaque SIPA-SUB + Ações de Emergência -->
        <div class="lg:col-span-4 space-y-6">
            
            <div class="p-6 rounded-2xl bg-gradient-to-br from-[#1c1305] via-[#0f0a02] to-[#050301] border border-amber-500/30 shadow-2xl relative overflow-hidden flex flex-col justify-between min-h-[340px]">
                
                <div class="absolute -right-8 -bottom-8 w-44 h-44 opacity-20 pointer-events-none">
                    <img src="{{ asset('img/brasao_0.png') }}" alt="" class="w-full h-full object-contain">
                </div>

                <div class="relative z-10">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider bg-amber-500/10 text-amber-400 border border-amber-500/30 mb-4">
                        🛡 Segurança Submarina
                    </div>

                    <h3 class="text-xl font-bold text-white uppercase leading-tight" style="font-family: 'Oswald', sans-serif;">
                        Prevenção & Salvamento Submarino
                    </h3>

                    <p class="text-xs text-gray-300 mt-3 leading-relaxed">
                        A integridade de cada vaso de pressão e a segurança de cada submarinista dependem da rigidez nas vistorias de escape e da notificação proativa via RELPREV.
                    </p>
                </div>

                <div class="relative z-10 pt-6 mt-4 border-t border-amber-500/20 space-y-2">
                    <button type="button" class="w-full py-2.5 px-4 rounded-xl text-xs font-bold uppercase tracking-wider bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-400 hover:to-yellow-400 text-black shadow-lg shadow-amber-500/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Emitir Novo RELPREV</span>
                    </button>
                    <button type="button" class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-gray-300 hover:text-white bg-gray-900/60 hover:bg-gray-800/80 border border-gray-700/60 transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Consultar Normas de Escape</span>
                    </button>
                </div>
            </div>

        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 3. GRID INFERIOR EM 3 COLUNAS                                             -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Widget 1: Comissão SIPA-SUB -->
        <div class="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-300 font-mono pb-3 border-b border-gray-800/80 flex items-center justify-between">
                <span>Comissão de Prevenção</span>
                <span class="text-[10px] text-amber-400">Plantão SIPA</span>
            </h3>

            <div class="mt-4 space-y-3.5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber-900/60 border border-amber-500/50 flex items-center justify-center text-xs font-bold text-white">
                            CF
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white">Capitão de Fragata Barros</div>
                            <div class="text-[10px] font-mono text-gray-400">Presidente SIPA-SUB</div>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-amber-950/60 text-amber-300 border border-amber-800/60">Ativo</span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-900/60 border border-blue-500/50 flex items-center justify-center text-xs font-bold text-white">
                            CC
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white">Capitão de Corveta Diniz</div>
                            <div class="text-[10px] font-mono text-gray-400">Perito em Vaso de Pressão</div>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-blue-950/60 text-blue-300 border border-blue-800/60">Ativo</span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-yellow-900/60 border border-yellow-500/50 flex items-center justify-center text-xs font-bold text-white">
                            2S
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white">2º SG PD SANDRO</div>
                            <div class="text-[10px] font-mono text-gray-400">CLTI / ComForS</div>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-emerald-950/60 text-emerald-300 border border-emerald-800/60">Conectado</span>
                </div>
            </div>
        </div>

        <!-- Widget 2: Vistorias Programadas -->
        <div class="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-300 font-mono pb-3 border-b border-gray-800/80 flex items-center justify-between">
                <span>Vistorias Programadas</span>
                <span class="text-[10px] text-emerald-400">Trimestre</span>
            </h3>

            <div class="mt-4 space-y-3">
                <div class="p-2.5 rounded-xl bg-gray-900/50 border border-gray-800 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-white">Câmara de Escape do S-40</div>
                        <div class="text-[10px] text-gray-400 font-mono">Simulação hiperbárica</div>
                    </div>
                    <span class="text-[10px] font-mono text-yellow-400 font-bold">12/Nov</span>
                </div>

                <div class="p-2.5 rounded-xl bg-gray-900/50 border border-gray-800 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-white">Sistemas de Ar de Emergência</div>
                        <div class="text-[10px] text-gray-400 font-mono">Bateria de cilindros BSN</div>
                    </div>
                    <span class="text-[10px] font-mono text-emerald-400 font-bold">18/Nov</span>
                </div>

                <div class="p-2.5 rounded-xl bg-gray-900/50 border border-gray-800 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-white">Boias de Resgate Submarino</div>
                        <div class="text-[10px] text-gray-400 font-mono">Teste de acionamento acústico</div>
                    </div>
                    <span class="text-[10px] font-mono text-blue-400 font-bold">25/Nov</span>
                </div>
            </div>
        </div>

        <!-- Widget 3: Conformidade de Segurança dos Meios -->
        <div class="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-300 font-mono pb-3 border-b border-gray-800/80 flex items-center justify-between">
                <span>Conformidade SIPA-SUB</span>
                <span class="text-[10px] text-yellow-400">Auditoria</span>
            </h3>

            <div class="mt-4 space-y-3.5">
                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-bold text-white">Sistemas de Escape Hiperbárico</span>
                        <span class="font-mono text-emerald-400 font-bold">100%</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-amber-500 to-emerald-400 h-1.5 rounded-full" style="width: 100%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-bold text-white">Sensores Atmosféricos de Bordo</span>
                        <span class="font-mono text-emerald-400 font-bold">99.5%</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-amber-500 to-emerald-400 h-1.5 rounded-full" style="width: 99.5%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-bold text-white">Válvulas e Redes de Inundação</span>
                        <span class="font-mono text-emerald-400 font-bold">100%</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-amber-500 to-emerald-400 h-1.5 rounded-full" style="width: 100%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-bold text-white">Procedimentos Homologados ComForS</span>
                        <span class="font-mono text-blue-400 font-bold">98.0%</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-cyan-400 h-1.5 rounded-full" style="width: 98%"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
