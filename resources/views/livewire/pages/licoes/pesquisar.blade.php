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
                'submarino' => 'S34 - Submarino Tikuna',
                'grau_sigilo' => 'RESERVADO',
                'sistema' => 'Propulsão & Geração',
                'data_ocorrencia' => '2026-03-14',
                'titulo' => 'Estanqueidade de válvula de exaustão durante faina de snorquel',
                'relato' => 'Durante manobra de recarga de baterias em cota periscópica, foi observada microinfiltração na vedação do sistema de exaustão devido a acúmulo de salinidade.',
                'recomendacao' => 'Inspeção preventiva mandatória a cada 50 horas de snorquel e substituição preventiva das gaxetas de vedação de elastômero.',
                'autor' => '2º SG PD SANDRO',
            ],
            [
                'id' => '2',
                'submarino' => 'S40 - Submarino Riachuelo',
                'grau_sigilo' => 'CONFIDENCIAL',
                'sistema' => 'Sensores & Sonar',
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

<div class="max-w-6xl mx-auto space-y-6 select-none">

    <!-- Barra de Filtros e Busca Rápida -->
    <div class="p-5 rounded-2xl bg-[#040a16] border border-gray-800/80 shadow-xl space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-white uppercase font-mono tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Filtros de Pesquisa Doutrinária</span>
                </h2>
                <p class="text-[11px] text-gray-400 font-mono mt-0.5">
                    Consulte ocorrências técnicas, fainas e recomendações homologadas pelo ComForS
                </p>
            </div>

            <!-- Botão Nova Lição Rápida -->
            <a 
                href="{{ route('licoes.nova') }}" 
                wire:navigate 
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-mono font-bold text-white bg-blue-600 hover:bg-blue-500 shadow-md transition-all cursor-pointer self-start sm:self-auto"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Cadastrar Nova</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Palavra-chave -->
            <div class="lg:col-span-2 relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.250ms="busca" 
                    placeholder="Buscar por título, resumo ou recomendação..." 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl pl-9 pr-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 font-mono transition-colors"
                />
                <svg class="w-4 h-4 text-gray-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
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

            <!-- Meio Naval -->
            <div>
                <input 
                    type="text" 
                    wire:model.live.debounce.250ms="filtro_meio" 
                    placeholder="Filtrar por meio (ex: S34)..." 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 font-mono"
                />
            </div>
        </div>
    </div>

    <!-- Lista de Resultados -->
    <div class="space-y-4">
        @php
            $filtradas = array_filter($licoes, function($item) {
                $matchBusca = empty($busca) || 
                              str_contains(strtolower($item['titulo']), strtolower($busca)) || 
                              str_contains(strtolower($item['relato']), strtolower($busca)) || 
                              str_contains(strtolower($item['recomendacao']), strtolower($busca));
                $matchSigilo = empty($filtro_sigilo) || $item['grau_sigilo'] === $filtro_sigilo;
                $matchMeio = empty($filtro_meio) || str_contains(strtolower($item['submarino']), strtolower($filtro_meio));
                return $matchBusca && $matchSigilo && $matchMeio;
            });
        @endphp

        @forelse($filtradas as $item)
            <div class="p-6 rounded-2xl bg-[#040a16] border border-gray-800/80 shadow-xl hover:border-blue-900/60 transition-all space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-b border-gray-800/80 pb-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-mono font-bold bg-blue-950/80 text-yellow-400 border border-blue-800/60">
                            {{ $item['submarino'] }}
                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-gray-900 text-blue-300 border border-gray-800">
                            {{ $item['sistema'] }}
                        </span>
                        <span class="text-[11px] font-mono text-gray-500">
                            Data: {{ $item['data_ocorrencia'] }}
                        </span>
                    </div>

                    <div>
                        @if($item['grau_sigilo'] === 'SECRETO')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono bg-red-950/80 text-red-400 border border-red-800/60 font-bold">
                                SECRETO
                            </span>
                        @elseif($item['grau_sigilo'] === 'CONFIDENCIAL')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono bg-amber-950/80 text-amber-400 border border-amber-800/60 font-bold">
                                CONFIDENCIAL
                            </span>
                        @elseif($item['grau_sigilo'] === 'RESERVADO')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono bg-blue-950/80 text-blue-400 border border-blue-800/60 font-bold">
                                RESERVADO
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono bg-gray-900 text-gray-300 border border-gray-700">
                                OSTENSIVO
                            </span>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="text-base font-bold text-white font-oswald uppercase tracking-wide">
                        {{ $item['titulo'] }}
                    </h3>
                    <p class="text-xs text-gray-300 font-sans leading-relaxed mt-2">
                        <strong class="text-gray-400 font-mono text-[11px] uppercase block mb-0.5">Relato Operacional:</strong>
                        {{ $item['relato'] }}
                    </p>
                </div>

                <div class="p-3.5 rounded-xl bg-blue-950/30 border border-blue-800/50">
                    <div class="text-[11px] font-mono uppercase text-yellow-400 font-bold flex items-center gap-1.5 mb-1">
                        <svg class="w-3.5 h-3.5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Recomendação Doutrinária Homologada:</span>
                    </div>
                    <p class="text-xs text-gray-200 leading-relaxed font-sans">
                        {{ $item['recomendacao'] }}
                    </p>
                </div>

                <div class="flex items-center justify-between text-[11px] font-mono text-gray-500 pt-1">
                    <span>Relatado por: <strong class="text-gray-300">{{ $item['autor'] ?? 'Militar ComForS' }}</strong></span>
                    <span class="text-yellow-500/80">ComForS • CLTI</span>
                </div>
            </div>
        @empty
            <div class="p-12 text-center rounded-2xl bg-[#040a16] border border-gray-800/80 shadow-xl space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-gray-900 border border-gray-800 flex items-center justify-center mx-auto text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-white font-mono uppercase">Nenhuma lição encontrada</h4>
                <p class="text-xs text-gray-400 font-mono max-w-md mx-auto">
                    Não foram localizados registros com os critérios informados. Ajuste os filtros de busca ou utilize o botão abaixo.
                </p>
                <div class="pt-2">
                    <a 
                        href="{{ route('licoes.nova') }}" 
                        wire:navigate 
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-mono font-bold text-white bg-blue-600 hover:bg-blue-500 shadow-md transition-colors cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Cadastrar Nova Lição</span>
                    </a>
                </div>
            </div>
        @endforelse
    </div>

</div>
