<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public string $busca = '';
    public string $filtro_meio = '';
    public string $filtro_sigilo = '';
    public string $filtro_sistema = '';

    public array $licoes = [];

    public function mount(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->temAcessoAoModulo('licoes_aprendidas')) {
            abort(403, 'Acesso não autorizado.');
        }

        session(['modulo_ativo' => 'licoes_aprendidas']);

        // Carrega lições registradas na sessão ou referências operacionais preliminares
        $padroes = [
            [
                'id' => '1',
                'protocolo' => 'LA-2026-0041',
                'submarino' => 'S34 - Submarino Tikuna',
                'grau_sigilo' => 'RESERVADO',
                'sistema' => 'Propulsão & Geração',
                'comissao' => 'COM-ADEST-S34',
                'rfc' => 'RFC-2026/014',
                'data_ocorrencia' => '2026-03-14',
                'titulo' => 'Estanqueidade de válvula de exaustão durante faina de snorquel',
                'relato' => 'Durante manobra de recarga de baterias em cota periscópica, foi observada microinfiltração na vedação do sistema de exaustão devido a acúmulo de salinidade.',
                'recomendacao' => 'Inspeção preventiva mandatória a cada 50 horas de snorquel e substituição preventiva das gaxetas de vedação de elastômero.',
                'autor' => '2º SG PD SANDRO',
            ],
            [
                'id' => '2',
                'protocolo' => 'LA-2026-0038',
                'submarino' => 'S40 - Submarino Riachuelo',
                'grau_sigilo' => 'CONFIDENCIAL',
                'sistema' => 'Sensores & Sonar',
                'comissao' => 'COM-TROPICALEX-2026',
                'rfc' => 'RFC-2026/029',
                'data_ocorrencia' => '2026-04-02',
                'titulo' => 'Calibração de sensibilidade do sonar passivo em águas com gradiente térmico severo',
                'relato' => 'Variação rápida de termoclina gerou zona de sombra acústica no setor de popa, dificultando detecção tática de contato de superfície.',
                'recomendacao' => 'Ajuste do algoritmo de filtragem acústica para gradientes térmicos do Atlântico Sul homologado pelo CIAMA.',
                'autor' => '1º TEN (EN) GUIMARÃES',
            ],
        ];

        $salvas = session('licoes_cadastradas', []);
        $this->licoes = array_merge($padroes, $salvas);
    }
}; ?>

<div class="max-w-6xl mx-auto space-y-5 select-none">
    <!-- Banner de Construção & Homologação Doutrinária -->
    <div class="p-4 rounded-2xl bg-gradient-to-r from-amber-950/40 via-[#0a1424] to-[#040a16] border border-amber-500/30 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center shrink-0">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-amber-400 font-mono uppercase tracking-wider">
                        Módulo em Construção • Homologação Doutrinária
                    </span>
                    <span class="text-[10px] font-mono text-gray-500 bg-gray-900 px-2 py-0.5 rounded border border-gray-800">
                        Pesquisa & Relatórios
                    </span>
                </div>
                <p class="text-[11px] text-gray-400 font-mono mt-0.5 leading-snug">
                    Ambiente em fase de validação operacional. Em breve pesquisa indexada na base de dados centralizada do ComForS.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Botão Gerar Relatório PDF -->
            <button 
                type="button" 
                onclick="window.print()"
                class="flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl text-xs font-mono font-semibold text-yellow-400 bg-gray-900/90 hover:bg-gray-800 border border-yellow-500/40 hover:border-yellow-400 shadow-lg shadow-black/40 transition-all cursor-pointer shrink-0"
            >
                <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Gerar Relatório PDF</span>
            </button>

            <!-- Botão Cadastrar Nova Lição -->
            <a 
                href="{{ route('licoes.nova') }}" 
                wire:navigate
                class="flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-xs font-mono font-bold text-white bg-blue-600 hover:bg-blue-500 border border-blue-500/40 shadow-md shadow-blue-950/60 transition-all cursor-pointer shrink-0"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>+ Cadastrar</span>
            </a>
        </div>
    </div>

    <!-- Barra de Filtros e Busca Simplificada (Sem cabeçalho redundante) -->
    <div class="p-4 sm:p-5 rounded-2xl bg-[#040a16] border border-gray-800/80 shadow-xl space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Palavra-chave -->
            <div class="lg:col-span-2 relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.250ms="busca" 
                    placeholder="Buscar por termo, título, relato, comissão ou recomendação..." 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl pl-9 pr-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 font-mono transition-colors"
                />
                <svg class="w-4 h-4 text-gray-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Meio Naval -->
            <div>
                <select 
                    wire:model.live="filtro_meio" 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3 py-2 text-xs text-gray-300 focus:outline-none focus:border-blue-500 font-mono"
                >
                    <option value="">Todos os Meios Navais</option>
                    <option value="S30">S30 - Submarino Tupi</option>
                    <option value="S34">S34 - Submarino Tikuna</option>
                    <option value="S40">S40 - Submarino Riachuelo</option>
                    <option value="S41">S41 - Submarino Humaitá</option>
                    <option value="S42">S42 - Submarino Tonelero</option>
                    <option value="BACS">BACS</option>
                    <option value="CIAMA">CIAMA</option>
                </select>
            </div>

            <!-- Grau de Sigilo -->
            <div>
                <select 
                    wire:model.live="filtro_sigilo" 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3 py-2 text-xs text-gray-300 focus:outline-none focus:border-blue-500 font-mono"
                >
                    <option value="">Todos Sigilos</option>
                    <option value="OSTENSIVO">OSTENSIVO</option>
                    <option value="RESERVADO">RESERVADO</option>
                    <option value="CONFIDENCIAL">CONFIDENCIAL</option>
                    <option value="SECRETO">SECRETO</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Listagem de Lições Aprendidas -->
    <div class="space-y-3.5">
        @php
            $filtradas = array_filter($licoes, function($item) {
                $matchBusca = empty($this->busca) || 
                    (stripos($item['titulo'] ?? '', $this->busca) !== false) || 
                    (stripos($item['relato'] ?? '', $this->busca) !== false) ||
                    (stripos($item['recomendacao'] ?? '', $this->busca) !== false);

                $matchMeio = empty($this->filtro_meio) || (stripos($item['submarino'] ?? '', $this->filtro_meio) !== false);
                $matchSigilo = empty($this->filtro_sigilo) || (($item['grau_sigilo'] ?? '') === $this->filtro_sigilo);

                return $matchBusca && $matchMeio && $matchSigilo;
            });
        @endphp

        @forelse ($filtradas as $licao)
            <div class="p-5 rounded-2xl bg-[#040a16] border border-gray-800/80 hover:border-blue-500/40 transition-all duration-200 shadow-xl space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-800/60 pb-3">
                    <div class="flex flex-wrap items-center gap-2">
                        @if(isset($licao['protocolo']))
                            <span class="text-[11px] font-mono font-bold text-blue-400 bg-blue-950/60 border border-blue-800/50 px-2 py-0.5 rounded">
                                {{ $licao['protocolo'] }}
                            </span>
                        @endif
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold border {{ $licao['grau_sigilo'] === 'SECRETO' ? 'bg-red-950/70 text-red-400 border-red-700/50' : ($licao['grau_sigilo'] === 'CONFIDENCIAL' ? 'bg-yellow-950/70 text-yellow-400 border-yellow-700/50' : 'bg-blue-950/70 text-blue-400 border-blue-700/50') }}">
                            {{ $licao['grau_sigilo'] }}
                        </span>
                        <span class="text-xs font-mono text-gray-300 font-semibold">
                            {{ $licao['submarino'] }}
                        </span>
                        @if(isset($licao['comissao']))
                            <span class="text-[10px] font-mono text-gray-400 bg-gray-900 px-2 py-0.5 rounded border border-gray-800">
                                ⚓ {{ $licao['comissao'] }}
                            </span>
                        @endif
                        @if(isset($licao['rfc']))
                            <span class="text-[10px] font-mono text-amber-400 bg-amber-950/40 px-2 py-0.5 rounded border border-amber-800/40">
                                📋 {{ $licao['rfc'] }}
                            </span>
                        @endif
                    </div>
                    <span class="text-[11px] font-mono text-gray-500">
                        Ocorrência: {{ $licao['data_ocorrencia'] ?? date('Y-m-d') }}
                    </span>
                </div>

                <div>
                    <h3 class="text-sm font-bold text-white font-mono">
                        {{ $licao['titulo'] }}
                    </h3>
                    <p class="text-xs text-gray-300 font-mono mt-1 leading-relaxed">
                        {{ $licao['relato'] }}
                    </p>
                </div>

                <div class="p-3 rounded-xl bg-blue-950/20 border border-blue-800/30 text-xs font-mono text-blue-200">
                    <span class="text-blue-400 font-bold block mb-0.5">Recomendação Doutrinária:</span>
                    {{ $licao['recomendacao'] }}
                </div>

                <div class="flex items-center justify-between text-[11px] font-mono text-gray-400 pt-1">
                    <span>Notificante: <strong class="text-yellow-400">{{ $licao['autor'] ?? auth()->user()->name }}</strong></span>
                    <span class="text-emerald-400 font-semibold">● Homologado Preliminarmente</span>
                </div>
            </div>
        @empty
            <div class="p-10 text-center rounded-2xl bg-[#040a16] border border-gray-800/80">
                <p class="text-xs text-gray-400 font-mono">Nenhuma lição aprendida localizada para os filtros selecionados.</p>
            </div>
        @endforelse
    </div>
</div>
