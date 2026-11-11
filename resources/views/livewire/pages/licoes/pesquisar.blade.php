<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    // Filtros de busca
    public string $busca = '';
    public string $filtro_meio = '';
    public string $filtro_sistema = '';
    public string $filtro_comissao = '';
    public string $filtro_sigilo = '';
    public string $filtro_ano = '';

    // Coleções dinâmicas
    public array $licoes = [];
    public array $opcoes_meios = [];
    public array $opcoes_sistemas = [];
    public array $opcoes_comissoes = [];
    public array $opcoes_anos = [];

    public function mount(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->temAcessoAoModulo('licoes_aprendidas')) {
            abort(403, 'Acesso não autorizado.');
        }

        session(['modulo_ativo' => 'licoes_aprendidas']);

        // A pesquisa inicia limpa / vazia se nenhum registro foi realizado ainda
        $this->licoes = session('licoes_cadastradas', []);

        // 1. Vinculação dinâmica com o CRUD de Meios Navais & OMs
        $crudMeios = session('crud_meios_navais', []);
        $sessionMeios = session('licoes_meios_navais', []);
        $meiosFormatados = [];
        foreach ($crudMeios as $m) {
            $meiosFormatados[] = is_array($m) ? ($m['indicativo'] . ' - ' . $m['nome']) : $m;
        }
        $this->opcoes_meios = array_values(array_unique(array_filter(array_merge($meiosFormatados, $sessionMeios))));

        // 2. Vinculação dinâmica com o CRUD de Sistemas & Subsistemas
        $crudSubs = session('crud_subsistemas', []);
        $sessionSubs = session('licoes_sistemas', []);
        $sistemasFormatados = [];
        foreach ($crudSubs as $s) {
            $sistemasFormatados[] = is_array($s) ? $s['nome'] : $s;
        }
        $this->opcoes_sistemas = array_values(array_unique(array_filter(array_merge($sistemasFormatados, $sessionSubs))));

        // 3. Vinculação dinâmica com o CRUD de Comissões Operacionais
        $crudComs = session('crud_comissoes', []);
        $sessionComs = session('licoes_comissoes', []);
        $comissoesFormatadas = [];
        foreach ($crudComs as $c) {
            $comissoesFormatadas[] = is_array($c) ? ($c['codigo'] . ' - ' . $c['nome']) : $c;
        }
        $this->opcoes_comissoes = array_values(array_unique(array_filter(array_merge($comissoesFormatadas, $sessionComs))));

        // 4. Anos distintos das lições cadastradas
        $anos = [];
        foreach ($this->licoes as $l) {
            if (! empty($l['data_ocorrencia'])) {
                $ano = substr($l['data_ocorrencia'], 0, 4);
                if (is_numeric($ano)) {
                    $anos[] = $ano;
                }
            }
        }
        $anos = array_values(array_unique($anos));
        rsort($anos);
        $this->opcoes_anos = $anos;
    }

    public function limparFiltros(): void
    {
        $this->reset(['busca', 'filtro_meio', 'filtro_sistema', 'filtro_comissao', 'filtro_sigilo', 'filtro_ano']);
    }
}; ?>

<div class="max-w-6xl mx-auto space-y-5 select-none">
    <!-- Banner de Construção & Homologação Doutrinária com Ações -->
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

    <!-- Barra de Filtros e Busca Vinculada aos CRUDs das Tabelas de Apoio -->
    <div class="p-4 sm:p-5 rounded-2xl bg-[#040a16] border border-gray-800/80 shadow-xl space-y-3.5">
        <!-- 1ª Linha de Filtros: Termo de Busca + Sigilo + Ano -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Palavra-chave -->
            <div class="lg:col-span-2 relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.250ms="busca" 
                    placeholder="Buscar por termo, título, relato ou recomendação..." 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl pl-9 pr-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 font-mono transition-colors"
                />
                <svg class="w-4 h-4 text-gray-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Grau de Sigilo -->
            <div>
                <select 
                    wire:model.live="filtro_sigilo" 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3 py-2 text-xs text-gray-300 focus:outline-none focus:border-blue-500 font-mono"
                >
                    <option value="">Todos os Sigilos</option>
                    <option value="OSTENSIVO">OSTENSIVO</option>
                    <option value="RESERVADO">RESERVADO</option>
                    <option value="CONFIDENCIAL">CONFIDENCIAL</option>
                    <option value="SECRETO">SECRETO</option>
                </select>
            </div>

            <!-- Ano da Lição (Dinâmico das lições cadastradas) -->
            <div>
                <select 
                    wire:model.live="filtro_ano" 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3 py-2 text-xs text-gray-300 focus:outline-none focus:border-blue-500 font-mono"
                >
                    <option value="">Todos os Anos</option>
                    @if (empty($opcoes_anos))
                        <option value="{{ date('Y') }}">{{ date('Y') }} (Ano Corrente)</option>
                    @else
                        @foreach ($opcoes_anos as $ano)
                            <option value="{{ $ano }}">{{ $ano }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>

        <!-- 2ª Linha de Filtros: Meios Navais, Áreas Técnicas e Comissões (100% vinculados aos CRUDs) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1 border-t border-gray-800/60">
            <!-- Meio Naval / OM (Vinculado dinamicamente ao CRUD Meios Navais) -->
            <div>
                <label class="block text-[10px] font-mono uppercase text-gray-400 mb-1">
                    Meio Naval / OM (CRUD)
                </label>
                <select 
                    wire:model.live="filtro_meio" 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3 py-2 text-xs text-gray-300 focus:outline-none focus:border-blue-500 font-mono"
                >
                    <option value="">Todos os Meios Navais</option>
                    @if(empty($opcoes_meios))
                        <option value="" disabled>Nenhum meio naval cadastrado no CRUD</option>
                    @else
                        @foreach ($opcoes_meios as $m)
                            <option value="{{ $m }}">{{ $m }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Área Técnica / Subsistema (Vinculado dinamicamente ao CRUD Subsistemas) -->
            <div>
                <label class="block text-[10px] font-mono uppercase text-gray-400 mb-1">
                    Subsistema Afetado (CRUD)
                </label>
                <select 
                    wire:model.live="filtro_sistema" 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3 py-2 text-xs text-gray-300 focus:outline-none focus:border-blue-500 font-mono"
                >
                    <option value="">Todos os Subsistemas</option>
                    @if(empty($opcoes_sistemas))
                        <option value="" disabled>Nenhum subsistema cadastrado no CRUD</option>
                    @else
                        @foreach ($opcoes_sistemas as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Comissão Operacional (Vinculada dinamicamente ao CRUD Comissões) -->
            <div>
                <label class="block text-[10px] font-mono uppercase text-gray-400 mb-1">
                    Comissão Operacional (CRUD)
                </label>
                <select 
                    wire:model.live="filtro_comissao" 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3 py-2 text-xs text-gray-300 focus:outline-none focus:border-blue-500 font-mono"
                >
                    <option value="">Todas as Comissões</option>
                    @if(empty($opcoes_comissoes))
                        <option value="" disabled>Nenhuma comissão cadastrada no CRUD</option>
                    @else
                        @foreach ($opcoes_comissoes as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>

        @if ($busca || $filtro_meio || $filtro_sistema || $filtro_comissao || $filtro_sigilo || $filtro_ano)
            <div class="flex items-center justify-between pt-1 border-t border-gray-800/60 text-xs font-mono">
                <span class="text-blue-400">Filtros ativos aplicados</span>
                <button 
                    type="button" 
                    wire:click="limparFiltros" 
                    class="text-yellow-400 hover:underline cursor-pointer"
                >
                    Limpar Todos os Filtros
                </button>
            </div>
        @endif
    </div>

    <!-- Listagem de Lições Aprendidas (Inicia 100% limpa) -->
    <div class="space-y-3.5">
        @php
            $filtradas = array_filter($licoes, function($item) {
                $matchBusca = empty($this->busca) || 
                    (stripos($item['titulo'] ?? '', $this->busca) !== false) || 
                    (stripos($item['relato'] ?? '', $this->busca) !== false) ||
                    (stripos($item['recomendacao'] ?? '', $this->busca) !== false);

                $matchMeio = empty($this->filtro_meio) || (stripos($item['submarino'] ?? '', $this->filtro_meio) !== false);
                $matchSistema = empty($this->filtro_sistema) || (stripos($item['sistema'] ?? '', $this->filtro_sistema) !== false);
                $matchComissao = empty($this->filtro_comissao) || (stripos($item['comissao'] ?? '', $this->filtro_comissao) !== false);
                $matchSigilo = empty($this->filtro_sigilo) || (($item['grau_sigilo'] ?? '') === $this->filtro_sigilo);
                $matchAno = empty($this->filtro_ano) || (str_starts_with($item['data_ocorrencia'] ?? '', $this->filtro_ano));

                return $matchBusca && $matchMeio && $matchSistema && $matchComissao && $matchSigilo && $matchAno;
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
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold border {{ ($licao['grau_sigilo'] ?? '') === 'SECRETO' ? 'bg-red-950/70 text-red-400 border-red-700/50' : (($licao['grau_sigilo'] ?? '') === 'CONFIDENCIAL' ? 'bg-yellow-950/70 text-yellow-400 border-yellow-700/50' : 'bg-blue-950/70 text-blue-400 border-blue-700/50') }}">
                            {{ $licao['grau_sigilo'] ?? 'RESERVADO' }}
                        </span>
                        <span class="text-xs font-mono text-gray-300 font-semibold">
                            {{ $licao['submarino'] ?? 'Meio Naval Não Informado' }}
                        </span>
                        @if(!empty($licao['sistema']))
                            <span class="text-[10px] font-mono text-gray-400 bg-gray-900 px-2 py-0.5 rounded border border-gray-800">
                                ⚙️ {{ $licao['sistema'] }}
                            </span>
                        @endif
                        @if(!empty($licao['comissao']))
                            <span class="text-[10px] font-mono text-gray-400 bg-gray-900 px-2 py-0.5 rounded border border-gray-800">
                                ⚓ {{ $licao['comissao'] }}
                            </span>
                        @endif
                        @if(!empty($licao['rfc']))
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
                        {{ $licao['titulo'] ?? 'Ocorrência sem título' }}
                    </h3>
                    <p class="text-xs text-gray-300 font-mono mt-1 leading-relaxed">
                        {{ $licao['relato'] ?? '' }}
                    </p>
                </div>

                <div class="p-3 rounded-xl bg-blue-950/20 border border-blue-800/30 text-xs font-mono text-blue-200">
                    <span class="text-blue-400 font-bold block mb-0.5">Recomendação Doutrinária:</span>
                    {{ $licao['recomendacao'] ?? '' }}
                </div>

                <div class="flex items-center justify-between text-[11px] font-mono text-gray-400 pt-1">
                    <span>Notificante: <strong class="text-yellow-400">{{ $licao['autor'] ?? auth()->user()->name }}</strong> (NIP: {{ $licao['nip'] ?? auth()->user()->nip }})</span>
                    <span class="text-emerald-400 font-semibold">● Homologado Preliminarmente</span>
                </div>
            </div>
        @empty
            <div class="p-12 text-center rounded-2xl bg-[#040a16] border border-gray-800/80">
                <div class="max-w-md mx-auto space-y-3">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-blue-950/40 border border-blue-800/40 flex items-center justify-center text-blue-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-200">Repositório de Lições Aprendidas Inicia Limpo</p>
                    <p class="text-xs text-gray-400 font-mono leading-relaxed">
                        Não há registros de lições cadastradas no sistema até o momento. À medida que as comissões e tripulações cadastrarem novas ocorrências na tela de Nova Lição, os relatórios aparecerão aqui com filtros por Meio Naval, Subsistema, Comissão e Ano.
                    </p>
                    <div class="pt-2">
                        <a 
                            href="{{ route('licoes.nova') }}" 
                            wire:navigate
                            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-mono font-bold rounded-xl bg-blue-600 hover:bg-blue-500 text-white shadow-md shadow-blue-950/60 transition-colors cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>+ Cadastrar Primeira Lição</span>
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
