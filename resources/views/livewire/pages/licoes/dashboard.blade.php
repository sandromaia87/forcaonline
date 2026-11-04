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

<div class="px-4 sm:px-6 lg:px-8 py-6 max-w-7xl mx-auto space-y-6 select-none">
    
    <!-- ========================================================================= -->
    <!-- 1. LINHA DE CARDS DE MÉTRICAS OPERACIONAIS (PADRÃO ARGON DASHBOARD PRO)  -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Card 1: Registros de Fainas -->
        <div class="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl flex items-center justify-between hover:border-blue-500/50 transition-all group">
            <div>
                <p class="text-[11px] font-mono uppercase font-bold tracking-wider text-gray-400">Fainas Registradas</p>
                <h3 class="text-2xl font-bold text-white mt-1 group-hover:text-blue-400 transition-colors">148</h3>
                <p class="text-xs text-emerald-400 font-semibold mt-1 flex items-center gap-1">
                    <span>↑ +14%</span>
                    <span class="text-gray-400 font-normal">neste ciclo</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-700 to-cyan-500 flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
        </div>

        <!-- Card 2: Doutrinas Ativas -->
        <div class="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl flex items-center justify-between hover:border-yellow-500/50 transition-all group">
            <div>
                <p class="text-[11px] font-mono uppercase font-bold tracking-wider text-gray-400">Doutrinas Ativas</p>
                <h3 class="text-2xl font-bold text-white mt-1 group-hover:text-yellow-400 transition-colors">42</h3>
                <p class="text-xs text-emerald-400 font-semibold mt-1 flex items-center gap-1">
                    <span>✓ Vigentes</span>
                    <span class="text-gray-400 font-normal">revisão 2026</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-yellow-600 to-amber-500 flex items-center justify-center text-white shadow-lg shadow-yellow-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
        </div>

        <!-- Card 3: Submarinos Esquadra -->
        <div class="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl flex items-center justify-between hover:border-emerald-500/50 transition-all group">
            <div>
                <p class="text-[11px] font-mono uppercase font-bold tracking-wider text-gray-400">Submarinos</p>
                <h3 class="text-2xl font-bold text-white mt-1 group-hover:text-emerald-400 transition-colors">05</h3>
                <p class="text-xs text-blue-400 font-semibold mt-1 flex items-center gap-1">
                    <span>S-BR & IKL-209</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
        </div>

        <!-- Card 4: Prontidão Tática -->
        <div class="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl flex items-center justify-between hover:border-indigo-500/50 transition-all group">
            <div>
                <p class="text-[11px] font-mono uppercase font-bold tracking-wider text-gray-400">Prontidão Tática</p>
                <h3 class="text-2xl font-bold text-white mt-1 group-hover:text-indigo-400 transition-colors">98.6%</h3>
                <p class="text-xs text-emerald-400 font-semibold mt-1 flex items-center gap-1">
                    <span>↑ Operacional</span>
                    <span class="text-gray-400 font-normal">nível ótimo</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-500 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 2. SEÇÃO PRINCIPAL (2 COLUNAS ESTILO ARGON: 8 COLS + 4 COLS)              -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Coluna Esquerda (8 cols): Tabela de Registros Recentes & Gráfico Tático -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Card Principal de Visão Geral -->
            <div class="p-6 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-4 border-b border-gray-800/80 gap-3">
                    <div>
                        <h2 class="text-base font-bold text-white uppercase tracking-wide" style="font-family: 'Oswald', sans-serif;">
                            Fainas e Ocorrências Recentes de Imersão
                        </h2>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Histórico cronológico de manobras, mergulhos estáticos e testes de estanqueidade
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono bg-blue-950/60 text-blue-400 border border-blue-800/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                            <span>Tempo Real</span>
                        </span>
                    </div>
                </div>

                <!-- Tabela Argon de Fainas -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-gray-400 uppercase tracking-wider font-mono border-b border-gray-800/60 text-[10px]">
                                <th class="pb-3 font-semibold">Submarino</th>
                                <th class="pb-3 font-semibold">Faina / Manobra</th>
                                <th class="pb-3 font-semibold">Setor / Área</th>
                                <th class="pb-3 font-semibold">Data</th>
                                <th class="pb-3 font-semibold text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/50">
                            <tr class="hover:bg-gray-800/30 transition-colors">
                                <td class="py-3.5 font-bold text-white flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-blue-900/40 border border-blue-700/50 flex items-center justify-center text-blue-300 font-mono text-xs">
                                        S40
                                    </div>
                                    <span>S-40 Riachuelo</span>
                                </td>
                                <td class="py-3.5 text-gray-300">Teste de Sopragem de Tanques</td>
                                <td class="py-3.5 font-mono text-gray-400">Área Charlie 4</td>
                                <td class="py-3.5 font-mono text-gray-400">Hoje, 08:30</td>
                                <td class="py-3.5 text-right">
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-950/60 text-emerald-400 border border-emerald-800/60">
                                        Homologado
                                    </span>
                                </td>
                            </tr>

                            <tr class="hover:bg-gray-800/30 transition-colors">
                                <td class="py-3.5 font-bold text-white flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-blue-900/40 border border-blue-700/50 flex items-center justify-center text-blue-300 font-mono text-xs">
                                        S41
                                    </div>
                                    <span>S-41 Humaitá</span>
                                </td>
                                <td class="py-3.5 text-gray-300">Calibração de Sonar Passivo</td>
                                <td class="py-3.5 font-mono text-gray-400">Baía de Sepetiba</td>
                                <td class="py-3.5 font-mono text-gray-400">Ontem, 16:45</td>
                                <td class="py-3.5 text-right">
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-950/60 text-emerald-400 border border-emerald-800/60">
                                        Homologado
                                    </span>
                                </td>
                            </tr>

                            <tr class="hover:bg-gray-800/30 transition-colors">
                                <td class="py-3.5 font-bold text-white flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-blue-900/40 border border-blue-700/50 flex items-center justify-center text-blue-300 font-mono text-xs">
                                        S42
                                    </div>
                                    <span>S-42 Tonelero</span>
                                </td>
                                <td class="py-3.5 text-gray-300">Verificação de Válvulas de Casco</td>
                                <td class="py-3.5 font-mono text-gray-400">Cais da BSN</td>
                                <td class="py-3.5 font-mono text-gray-400">02/11/2026</td>
                                <td class="py-3.5 text-right">
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-950/60 text-amber-400 border border-amber-800/60">
                                        Em Revisão
                                    </span>
                                </td>
                            </tr>

                            <tr class="hover:bg-gray-800/30 transition-colors">
                                <td class="py-3.5 font-bold text-white flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-blue-900/40 border border-blue-700/50 flex items-center justify-center text-blue-300 font-mono text-xs">
                                        S34
                                    </div>
                                    <span>S-34 Tikuna</span>
                                </td>
                                <td class="py-3.5 text-gray-300">Treinamento de Controle de Avarias</td>
                                <td class="py-3.5 font-mono text-gray-400">Área Bravo 2</td>
                                <td class="py-3.5 font-mono text-gray-400">01/11/2026</td>
                                <td class="py-3.5 text-right">
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-950/60 text-emerald-400 border border-emerald-800/60">
                                        Homologado
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-800/60 flex items-center justify-between text-xs text-gray-400 font-mono">
                    <span>Mostrando 4 de 148 registros operacionais</span>
                    <button type="button" class="text-blue-400 hover:text-blue-300 font-semibold cursor-pointer">Ver todos os registros →</button>
                </div>
            </div>

        </div>

        <!-- Coluna Direita (4 cols): Card Destaque Institucional Argon + Ações Rápidas -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Card de Destaque com Fundo Submarino e Gradiente Argon -->
            <div class="p-6 rounded-2xl bg-gradient-to-br from-[#0c1e3d] via-[#061122] to-[#030814] border border-blue-500/30 shadow-2xl relative overflow-hidden flex flex-col justify-between min-h-[340px]">
                
                <!-- Silhueta de Fundo -->
                <div class="absolute -right-8 -bottom-8 w-44 h-44 opacity-20 pointer-events-none">
                    <img src="{{ asset('img/brasao_0.png') }}" alt="" class="w-full h-full object-contain">
                </div>

                <div class="relative z-10">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider bg-yellow-500/10 text-yellow-400 border border-yellow-500/30 mb-4">
                        ★ Doutrina Tática
                    </div>

                    <h3 class="text-xl font-bold text-white uppercase leading-tight" style="font-family: 'Oswald', sans-serif;">
                        Repositório Doutrinário ComForS
                    </h3>

                    <p class="text-xs text-gray-300 mt-3 leading-relaxed">
                        A preservação das lições aprendidas assegura a superioridade tática e a sobrevivência de nossas tripulações nas profundezas do Atlântico Sul.
                    </p>
                </div>

                <div class="relative z-10 pt-6 mt-4 border-t border-blue-500/20 space-y-2">
                    <button type="button" class="w-full py-2.5 px-4 rounded-xl text-xs font-bold uppercase tracking-wider bg-gradient-to-r from-yellow-500 to-amber-600 hover:from-yellow-400 hover:to-amber-500 text-black shadow-lg shadow-yellow-500/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Registrar Nova Faina</span>
                    </button>
                    <button type="button" class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-gray-300 hover:text-white bg-gray-900/60 hover:bg-gray-800/80 border border-gray-700/60 transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span>Consultar Biblioteca Tática</span>
                    </button>
                </div>
            </div>

        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 3. GRID INFERIOR EM 3 COLUNAS (EQUIPE DE QUARTO, TAREFAS E PRONTIDÃO)     -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Widget 1: Oficiais de Quarto & Serviço -->
        <div class="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-300 font-mono pb-3 border-b border-gray-800/80 flex items-center justify-between">
                <span>Tripulação de Quarto</span>
                <span class="text-[10px] text-emerald-400">● BSN Operacional</span>
            </h3>

            <div class="mt-4 space-y-3.5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-900/60 border border-blue-500/50 flex items-center justify-center text-xs font-bold text-white">
                            CT
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white">Capitão-Tenente Ribeiro</div>
                            <div class="text-[10px] font-mono text-gray-400">Oficial de Manobra</div>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-blue-950/60 text-blue-300 border border-blue-800/60">A Bordo</span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber-900/60 border border-amber-500/50 flex items-center justify-center text-xs font-bold text-white">
                            1T
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white">Primeiro-Tenente Castro</div>
                            <div class="text-[10px] font-mono text-gray-400">Oficial de Armamento</div>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-blue-950/60 text-blue-300 border border-blue-800/60">A Bordo</span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-yellow-900/60 border border-yellow-500/50 flex items-center justify-center text-xs font-bold text-white">
                            2S
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white">2º SG PD SANDRO</div>
                            <div class="text-[10px] font-mono text-gray-400">Operador / CLTI</div>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-emerald-950/60 text-emerald-300 border border-emerald-800/60">Conectado</span>
                </div>
            </div>
        </div>

        <!-- Widget 2: Procedimentos & Checklists Táticos -->
        <div class="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-300 font-mono pb-3 border-b border-gray-800/80 flex items-center justify-between">
                <span>Checklists Táticos</span>
                <span class="text-[10px] text-blue-400">4 Pendentes</span>
            </h3>

            <div class="mt-4 space-y-3">
                <label class="flex items-start gap-3 text-xs cursor-pointer group">
                    <input type="checkbox" checked class="mt-0.5 rounded bg-gray-900 border-gray-700 text-yellow-500 focus:ring-0 cursor-pointer">
                    <span class="text-gray-300 line-through group-hover:text-white transition-colors">Verificação de sensores de atmosfera (O2/CO2)</span>
                </label>
                <label class="flex items-start gap-3 text-xs cursor-pointer group">
                    <input type="checkbox" checked class="mt-0.5 rounded bg-gray-900 border-gray-700 text-yellow-500 focus:ring-0 cursor-pointer">
                    <span class="text-gray-300 line-through group-hover:text-white transition-colors">Inspeção das redes de ar comprimido 250 bar</span>
                </label>
                <label class="flex items-start gap-3 text-xs cursor-pointer group">
                    <input type="checkbox" class="mt-0.5 rounded bg-gray-900 border-gray-700 text-yellow-500 focus:ring-0 cursor-pointer">
                    <span class="text-white group-hover:text-yellow-400 transition-colors">Teste de estanqueidade das escotilhas de escape</span>
                </label>
                <label class="flex items-start gap-3 text-xs cursor-pointer group">
                    <input type="checkbox" class="mt-0.5 rounded bg-gray-900 border-gray-700 text-yellow-500 focus:ring-0 cursor-pointer">
                    <span class="text-white group-hover:text-yellow-400 transition-colors">Relatório pós-faina de patrulha submarina</span>
                </label>
            </div>
        </div>

        <!-- Widget 3: Prontidão da Esquadra -->
        <div class="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-300 font-mono pb-3 border-b border-gray-800/80 flex items-center justify-between">
                <span>Prontidão dos Submarinos</span>
                <span class="text-[10px] text-yellow-400">Esquadra</span>
            </h3>

            <div class="mt-4 space-y-3.5">
                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-bold text-white">S-40 Riachuelo</span>
                        <span class="font-mono text-emerald-400 font-bold">100%</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-emerald-400 h-1.5 rounded-full" style="width: 100%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-bold text-white">S-41 Humaitá</span>
                        <span class="font-mono text-emerald-400 font-bold">100%</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-emerald-400 h-1.5 rounded-full" style="width: 100%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-bold text-white">S-42 Tonelero</span>
                        <span class="font-mono text-blue-400 font-bold">95%</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-cyan-400 h-1.5 rounded-full" style="width: 95%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-bold text-white">S-34 Tikuna</span>
                        <span class="font-mono text-yellow-400 font-bold">98%</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-amber-500 to-yellow-400 h-1.5 rounded-full" style="width: 98%"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
