<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    // Listas dinâmicas
    public array $tipos_documento = [];
    public array $meios_navais = [];
    public array $comissoes = [];

    // Campos do formulário
    public string $tipo_sigla = '';
    public string $numero_gerado = '';
    public string $submarino = '';
    public string $comissao = '';
    public string $grau_sigilo = 'RESERVADO';
    public string $data_documento = '';
    public string $titulo = '';
    public string $ementa = '';
    public bool $salvo = false;

    // Adição inline de novo tipo de documento
    public bool $adicionandoTipo = false;
    public string $novaSigla = '';
    public string $novoNome = '';

    // Modal de Guia
    public bool $modalGuiaAberto = false;

    public function mount(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->temAcessoAoModulo('licoes_aprendidas')) {
            abort(403, 'Acesso não autorizado ao módulo de Lições Aprendidas.');
        }
        session(['modulo_ativo' => 'licoes_aprendidas']);

        $this->data_documento = date('Y-m-d');

        // Carrega das tabelas de apoio
        $this->carregarTipos();

        $crudMeios = array_map(fn($m) => is_array($m) ? ($m['indicativo'] . ' - ' . $m['nome']) : $m, session('crud_meios_navais', []));
        $sessionMeios = session('licoes_meios_navais', []);
        $this->meios_navais = array_values(array_unique(array_filter(array_merge($crudMeios, $sessionMeios))));

        $crudComissoes = array_map(fn($c) => is_array($c) ? ($c['codigo'] . ' - ' . $c['nome']) : $c, session('crud_comissoes', []));
        $sessionComissoes = session('licoes_comissoes', []);
        $this->comissoes = array_values(array_unique(array_filter(array_merge($crudComissoes, $sessionComissoes))));
    }

    public function carregarTipos(): void
    {
        $this->tipos_documento = session('crud_tipos_documento', []);
    }

    public function updatedTipoSigla($value): void
    {
        $this->gerarNumeroUnico($value);
    }

    public function gerarNumeroUnico(string $sigla): void
    {
        if (empty($sigla)) {
            $this->numero_gerado = '';
            return;
        }

        $docs = session('documentos_cadastrados', []);
        // Conta quantos documentos do mesmo tipo já existem
        $doMesmoTipo = array_filter($docs, fn($d) => ($d['tipo_sigla'] ?? '') === $sigla);
        $seq = count($doMesmoTipo) + 1;
        $ano = date('Y');

        $this->numero_gerado = strtoupper(trim($sigla)) . '-' . $ano . '-' . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
    }

    public function salvarNovoTipo(): void
    {
        $this->validate([
            'novaSigla' => ['required', 'string', 'min:2', 'max:8'],
            'novoNome' => ['required', 'string', 'min:3', 'max:150'],
        ], [
            'novaSigla.required' => 'Informe a sigla (ex: RFC, ARFC).',
            'novoNome.required' => 'Informe o nome por extenso.',
        ]);

        $siglaFormatada = strtoupper(trim($this->novaSigla));
        $tipos = session('crud_tipos_documento', []);
        $novo = [
            'id' => (string) Str::uuid(),
            'sigla' => $siglaFormatada,
            'nome' => trim($this->novoNome),
            'descricao' => 'Criado sob demanda em Novo Documento',
            'status' => 'Ativo',
        ];
        array_unshift($tipos, $novo);
        session(['crud_tipos_documento' => $tipos]);

        $this->carregarTipos();
        $this->tipo_sigla = $siglaFormatada;
        $this->gerarNumeroUnico($siglaFormatada);

        $this->reset(['novaSigla', 'novoNome', 'adicionandoTipo']);
    }

    public function salvar(): void
    {
        $this->validate([
            'tipo_sigla' => 'required',
            'titulo' => ['required', 'string', 'min:5', 'max:255'],
            'ementa' => ['required', 'string', 'min:10'],
        ], [
            'tipo_sigla.required' => 'Selecione o tipo de documento para gerar o número único.',
            'titulo.required' => 'Informe o título ou assunto principal do documento.',
            'ementa.required' => 'Descreva a síntese ou ementa do documento (mínimo 10 caracteres).',
        ]);

        // Garante número único
        if (empty($this->numero_gerado)) {
            $this->gerarNumeroUnico($this->tipo_sigla);
        }

        $docs = session('documentos_cadastrados', []);
        $novo = [
            'id' => (string) Str::uuid(),
            'numero' => $this->numero_gerado,
            'tipo_sigla' => $this->tipo_sigla,
            'submarino' => $this->submarino ?: 'Não especificado',
            'comissao' => $this->comissao ?: 'Não vinculada',
            'grau_sigilo' => $this->grau_sigilo,
            'data_documento' => $this->data_documento,
            'titulo' => trim($this->titulo),
            'ementa' => trim($this->ementa),
            'autor' => (auth()->user()->graduacao ?? 'MILITAR') . ' ' . auth()->user()->name,
            'nip' => auth()->user()->nip,
        ];
        array_unshift($docs, $novo);
        session(['documentos_cadastrados' => $docs]);

        $this->salvo = true;
    }

    public function novoDocumento(): void
    {
        $this->reset(['tipo_sigla', 'numero_gerado', 'submarino', 'comissao', 'titulo', 'ementa', 'salvo']);
        $this->grau_sigilo = 'RESERVADO';
        $this->data_documento = date('Y-m-d');
    }
}; ?>

<div class="space-y-6 select-none">
    <!-- Banner de Construção & Homologação Doutrinária com Botão Guia -->
    <div class="p-4 rounded-2xl bg-gradient-to-r from-amber-950/40 via-[#0a1424] to-[#040a16] border border-amber-500/30 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center shrink-0">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-amber-400 font-mono uppercase tracking-wider">
                        Módulo em Construção • Cadastro de Documento Referência
                    </span>
                    <span class="text-[10px] font-mono text-gray-500 bg-gray-900 px-2 py-0.5 rounded border border-gray-800">
                        Documentos Técnicos
                    </span>
                </div>
                <p class="text-[11px] text-gray-400 font-mono mt-0.5 leading-snug">
                    Cadastre relatórios e análises doutrinárias que servirão como referência para as Lições Aprendidas. O número único é gerado automaticamente com base na sigla.
                </p>
            </div>
        </div>

        <button
            type="button"
            wire:click="$set('modalGuiaAberto', true)"
            class="flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl text-xs font-mono font-semibold text-blue-300 bg-gray-900/90 hover:bg-gray-800 border border-blue-500/30 hover:border-blue-400/60 shadow-lg shadow-black/40 transition-all cursor-pointer self-start sm:self-auto shrink-0"
        >
            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Guia de Preenchimento</span>
        </button>
    </div>

    <!-- Alerta de Sucesso -->
    @if ($salvo)
        <div class="p-6 rounded-2xl bg-gradient-to-r from-emerald-950/80 to-[#041a12] border border-emerald-500/40 shadow-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-emerald-300 uppercase font-mono">
                            Documento Referência Registrado com Sucesso!
                        </h3>
                        <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-emerald-900/60 text-yellow-400 border border-emerald-600/50">
                            {{ $numero_gerado }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-300 mt-0.5">
                        O documento foi catalogado e seu identificador único está ativo no repositório doutrinário do ComForS.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button
                    wire:click="novoDocumento"
                    type="button"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-mono font-bold text-white bg-emerald-600 hover:bg-emerald-500 transition-colors cursor-pointer"
                >
                    Cadastrar Outro Documento
                </button>
                <a
                    href="{{ route('licoes.dashboard') }}"
                    wire:navigate
                    class="px-3.5 py-1.5 rounded-xl text-xs font-mono font-bold text-gray-300 hover:text-white bg-gray-900 border border-gray-700 transition-colors cursor-pointer"
                >
                    Ir ao Início
                </a>
            </div>
        </div>
    @endif

    <!-- Formulário Principal Argon -->
    <form wire:submit="salvar" class="rounded-2xl p-6 sm:p-8 bg-[#040a16] border border-gray-800/80 shadow-2xl space-y-6">
        <!-- Linha 1: Tipo de Documento, Número Único Gerado, Grau de Sigilo -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- 1. Tipo de Documento -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="tipo_sigla" class="text-xs font-mono uppercase text-gray-300 font-semibold">
                        Tipo de Documento <span class="text-red-400">*</span>
                    </label>
                    @if (!$adicionandoTipo)
                        <button
                            type="button"
                            wire:click="$set('adicionandoTipo', true)"
                            class="inline-flex items-center gap-1 text-[11px] font-mono text-yellow-400 hover:text-yellow-300 hover:underline cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>+ Novo Tipo</span>
                        </button>
                    @endif
                </div>

                @if ($adicionandoTipo)
                    <div class="p-3 rounded-xl bg-blue-950/40 border border-blue-800/80 space-y-2.5">
                        <div class="text-[11px] font-mono text-blue-300 font-semibold flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                            <span>Cadastrar Novo Tipo</span>
                        </div>
                        <input
                            type="text"
                            wire:model="novaSigla"
                            placeholder="Sigla (ex: RFC, ARFC)"
                            class="w-full bg-[#02050d] border border-gray-700 rounded-lg px-3 py-1.5 text-xs text-white uppercase font-mono"
                        />
                        <input
                            type="text"
                            wire:model="novoNome"
                            placeholder="Nome por extenso"
                            class="w-full bg-[#02050d] border border-gray-700 rounded-lg px-3 py-1.5 text-xs text-white font-mono"
                        />
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                wire:click="salvarNovoTipo"
                                class="px-2.5 py-1 rounded-lg text-[11px] font-mono font-bold bg-blue-600 hover:bg-blue-500 text-white cursor-pointer"
                            >
                                Adicionar
                            </button>
                            <button
                                type="button"
                                wire:click="$set('adicionandoTipo', false)"
                                class="px-2.5 py-1 rounded-lg text-[11px] font-mono text-gray-400 hover:text-white bg-gray-900 border border-gray-700 cursor-pointer"
                            >
                                Cancelar
                            </button>
                        </div>
                    </div>
                @else
                    <select
                        id="tipo_sigla"
                        wire:model.live="tipo_sigla"
                        class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono transition-colors"
                    >
                        <option value="">Selecione o Tipo de Documento</option>
                        @foreach ($tipos_documento as $t)
                            <option value="{{ $t['sigla'] }}">
                                {{ $t['sigla'] }} - {{ $t['nome'] }}
                            </option>
                        @endforeach
                    </select>
                @endif
                @error('tipo_sigla') <span class="text-[11px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- 2. Número Único Gerado Automaticamente -->
            <div>
                <label class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-2">
                    Número Único do Documento
                </label>
                <div class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs font-mono flex items-center justify-between">
                    @if (!empty($numero_gerado))
                        <span class="text-yellow-400 font-bold text-sm tracking-wide">
                            {{ $numero_gerado }}
                        </span>
                        <span class="text-[10px] text-emerald-400 bg-emerald-950/60 border border-emerald-700/40 px-2 py-0.5 rounded font-mono">
                            ● Gerado
                        </span>
                    @else
                        <span class="text-gray-500 italic">
                            Selecione o tipo acima para gerar
                        </span>
                    @endif
                </div>
                <span class="text-[10px] text-gray-500 font-mono mt-1 block">Regra: SIGLA-ANO-SEQUENCIAL</span>
            </div>

            <!-- 3. Grau de Sigilo -->
            <div>
                <label for="grau_sigilo" class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-2">
                    Grau de Sigilo <span class="text-red-400">*</span>
                </label>
                <select
                    id="grau_sigilo"
                    wire:model="grau_sigilo"
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono transition-colors"
                >
                    <option value="OSTENSIVO">OSTENSIVO</option>
                    <option value="RESERVADO">RESERVADO</option>
                    <option value="CONFIDENCIAL">CONFIDENCIAL</option>
                    <option value="SECRETO">SECRETO</option>
                </select>
            </div>
        </div>

        <!-- Linha 2: Meio Naval / OM, Comissão Operacional, Data -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- 4. Meio Naval / OM -->
            <div>
                <label for="submarino" class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-2">
                    Meio Naval / OM de Origem
                </label>
                <select
                    id="submarino"
                    wire:model="submarino"
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono transition-colors"
                >
                    <option value="">Não vinculado a meio específico</option>
                    @foreach ($meios_navais as $m)
                        <option value="{{ $m }}">{{ $m }}</option>
                    @endforeach
                </select>
                <span class="text-[10px] text-gray-500 font-mono mt-1 block">Submarino ou OM que originou o documento</span>
            </div>

            <!-- 5. Comissão Operacional -->
            <div>
                <label for="comissao" class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-2">
                    Comissão Operacional de Referência
                </label>
                <select
                    id="comissao"
                    wire:model="comissao"
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono transition-colors"
                >
                    <option value="">Não vinculado a comissão</option>
                    @foreach ($comissoes as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
                <span class="text-[10px] text-gray-500 font-mono mt-1 block">Missão ou faina referente ao documento</span>
            </div>

            <!-- 6. Data do Documento -->
            <div>
                <label for="data_documento" class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-2">
                    Data de Emissão do Documento <span class="text-red-400">*</span>
                </label>
                <input
                    type="date"
                    id="data_documento"
                    wire:model="data_documento"
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono transition-colors"
                />
            </div>
        </div>

        <!-- Linha 3: Título / Objeto do Documento -->
        <div>
            <label for="titulo" class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-2">
                Título / Objeto do Documento <span class="text-red-400">*</span>
            </label>
            <input
                type="text"
                id="titulo"
                wire:model="titulo"
                placeholder="Ex: Relatório Final de Comissão da Operação Tropicalex 2026 - Submarino Tikuna"
                class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono transition-colors"
            />
            @error('titulo') <span class="text-[11px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Linha 4: Ementa / Resumo do Conteúdo -->
        <div>
            <label for="ementa" class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-2">
                Ementa / Resumo Descritivo do Conteúdo <span class="text-red-400">*</span>
            </label>
            <textarea
                id="ementa"
                wire:model="ementa"
                rows="4"
                placeholder="Apresente a síntese dos principais fatos, resultados operacionais, manobras executadas ou pareceres constantes no documento..."
                class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-sans leading-relaxed transition-colors"
            ></textarea>
            @error('ementa') <span class="text-[11px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Barra Inferior de Ações -->
        <div class="pt-4 border-t border-gray-800/80 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-xs font-mono text-gray-400 flex items-center gap-2">
                <span class="text-yellow-400 font-bold">* Campos obrigatórios</span>
                <span>• Responsável pelo Registro: <strong class="text-white">{{ auth()->user()->name }}</strong> (NIP: {{ auth()->user()->nip }})</span>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a
                    href="{{ route('licoes.dashboard') }}"
                    wire:navigate
                    class="w-full sm:w-auto text-center px-4 py-2.5 rounded-xl text-xs font-mono font-bold text-gray-400 hover:text-white bg-gray-900/60 hover:bg-gray-900 border border-gray-800 transition-colors cursor-pointer"
                >
                    Cancelar
                </a>
                <button
                    type="submit"
                    class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-xs font-mono font-bold text-white bg-gradient-to-r from-blue-700 to-blue-600 hover:from-blue-600 hover:to-blue-500 shadow-lg shadow-blue-900/50 border border-blue-500/40 transition-all cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    <span>Salvar e Homologar Documento</span>
                </button>
            </div>
        </div>
    </form>

    <!-- Modal de Guia de Preenchimento -->
    @if ($modalGuiaAberto)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 backdrop-blur-md p-4 animate-in fade-in duration-200">
            <div class="bg-[#0b1329] border border-blue-500/40 rounded-2xl w-full max-w-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-950/80 to-[#0a1226] border-b border-gray-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/20 border border-blue-400/30 flex items-center justify-center text-blue-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider font-mono">
                                Guia de Apoio: Cadastro de Documento Referência
                            </h3>
                            <p class="text-xs text-blue-400/80 font-mono">Diretrizes do Estado-Maior do ComForS</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        wire:click="$set('modalGuiaAberto', false)"
                        class="text-gray-400 hover:text-white p-1 rounded-lg hover:bg-gray-800 transition-colors cursor-pointer"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto space-y-4 text-xs font-mono text-gray-300">
                    <div class="p-3.5 rounded-xl bg-blue-950/20 border border-blue-800/30">
                        <span class="text-blue-300 font-bold block mb-1">FINALIDADE DOUTRINÁRIA</span>
                        Esta tela permite registrar e catalogar documentos operativos e administrativos (como RFCs, ARFCs e relatórios técnicos) que respaldam as Lições Aprendidas identificadas pelas tripulações.
                    </div>

                    <div class="space-y-3">
                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">1. Tipo de Documento & Sigla</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">
                                Selecione o tipo pré-cadastrado na tabela de apoio (ex: RFC para Relatório Final de Comissão, ARFC para Análise de RFC). Caso o tipo desejado não conste na lista, clique em <span class="text-yellow-400 font-bold">+ Novo Tipo</span> para cadastrá-lo dinamicamente.
                            </p>
                        </div>

                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">2. Regra de Numeração Automática</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">
                                O número único é construído dinamicamente na fórmula: <code class="text-white bg-black/40 px-1 py-0.5 rounded">SIGLA-ANO-SEQUENCIAL</code> (ex: <code class="text-yellow-400 bg-black/40 px-1 py-0.5 rounded">RFC-2026-0001</code>). Isso garante rastreabilidade total de cada documento.
                            </p>
                        </div>

                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">3. Vínculo Operacional</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">
                                Você pode correlacionar o documento ao <span class="text-blue-300 font-semibold">Meio Naval / OM</span> e à <span class="text-blue-300 font-semibold">Comissão Operacional</span> em que o evento ocorreu.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-3.5 bg-gray-950/80 border-t border-gray-800 flex items-center justify-end">
                    <button
                        type="button"
                        wire:click="$set('modalGuiaAberto', false)"
                        class="px-4 py-2 rounded-xl text-xs font-mono font-semibold bg-blue-600 hover:bg-blue-500 text-white transition-colors cursor-pointer"
                    >
                        Entendido / Fechar Guia
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
