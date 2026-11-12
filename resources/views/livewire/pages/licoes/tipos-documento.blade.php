<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    // A tabela de tipos de documento inicia vazia conforme diretriz; novos tipos são cadastrados sob demanda
    public array $tipos = [];
    public string $busca = '';
    public string $filtro_status = '';

    // Modal de Cadastro/Edição
    public bool $modalAberto = false;
    public bool $modalGuiaAberto = false;
    public ?string $editandoId = null;

    // Campos do formulário
    public string $sigla = '';
    public string $nome = '';
    public string $descricao = '';
    public string $status = 'Ativo';

    public function mount(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->temAcessoAoModulo('licoes_aprendidas')) {
            abort(403, 'Acesso não autorizado ao módulo de Lições Aprendidas.');
        }
        session(['modulo_ativo' => 'licoes_aprendidas']);

        // Carrega da sessão, iniciando vazia
        $this->tipos = session('crud_tipos_documento', []);
    }

    public function abrirModalNovo(): void
    {
        $this->reset(['editandoId', 'sigla', 'nome', 'descricao']);
        $this->status = 'Ativo';
        $this->resetValidation();
        $this->modalAberto = true;
    }

    public function abrirModalEditar(string $id): void
    {
        $encontrado = collect($this->tipos)->firstWhere('id', $id);
        if ($encontrado) {
            $this->editandoId = $id;
            $this->sigla = $encontrado['sigla'];
            $this->nome = $encontrado['nome'];
            $this->descricao = $encontrado['descricao'] ?? '';
            $this->status = $encontrado['status'] ?? 'Ativo';
            $this->resetValidation();
            $this->modalAberto = true;
        }
    }

    public function salvar(): void
    {
        $this->validate([
            'sigla' => ['required', 'string', 'min:2', 'max:8'],
            'nome' => ['required', 'string', 'min:3', 'max:150'],
        ], [
            'sigla.required' => 'Informe a sigla identificadora (ex: RFC, ARFC, RTE).',
            'sigla.min' => 'A sigla deve conter pelo menos 2 caracteres.',
            'nome.required' => 'Informe a designação completa do tipo de documento.',
        ]);

        $siglaFormatada = strtoupper(trim($this->sigla));

        if ($this->editandoId) {
            $this->tipos = array_map(function ($t) use ($siglaFormatada) {
                if ($t['id'] === $this->editandoId) {
                    return [
                        'id' => $this->editandoId,
                        'sigla' => $siglaFormatada,
                        'nome' => trim($this->nome),
                        'descricao' => trim($this->descricao),
                        'status' => $this->status,
                    ];
                }
                return $t;
            }, $this->tipos);
        } else {
            $novo = [
                'id' => (string) Str::uuid(),
                'sigla' => $siglaFormatada,
                'nome' => trim($this->nome),
                'descricao' => trim($this->descricao),
                'status' => $this->status,
            ];
            array_unshift($this->tipos, $novo);
        }

        session(['crud_tipos_documento' => $this->tipos]);
        $this->modalAberto = false;
    }

    public function excluir(string $id): void
    {
        $this->tipos = array_values(array_filter($this->tipos, fn($t) => $t['id'] !== $id));
        session(['crud_tipos_documento' => $this->tipos]);
    }

    public function fecharModal(): void
    {
        $this->modalAberto = false;
    }
}; ?>

<div class="space-y-6 select-none">
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
                        Tabela de Apoio 4 de 4
                    </span>
                </div>
                <p class="text-[11px] text-gray-400 font-mono mt-0.5 leading-snug">
                    Estrutura para catalogação de tipos de documento. A sigla informada (ex: RFC, ARFC) é utilizada na geração automática do número único.
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-mono text-amber-300/80 bg-amber-950/60 border border-amber-700/40 px-2.5 py-1 rounded-lg">
                Status: Em Desenvolvimento
            </span>
        </div>
    </div>

    <!-- Barra de Controle e Ações -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-2xl bg-[#040a16] border border-gray-800/80 shadow-xl">
        <div class="flex flex-1 items-center gap-3">
            <div class="relative flex-1 max-w-md">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="busca"
                    placeholder="Buscar tipo de documento por sigla ou nome..."
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl pl-9 pr-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 font-mono transition-colors"
                />
                <svg class="w-4 h-4 text-gray-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <select
                wire:model.live="filtro_status"
                class="bg-[#02050d] border border-gray-800 rounded-xl px-3 py-2 text-xs text-gray-300 focus:outline-none focus:border-blue-500 font-mono"
            >
                <option value="">Todos os Status</option>
                <option value="Ativo">Ativo</option>
                <option value="Em Revisão">Em Revisão</option>
            </select>
        </div>

        <div class="flex items-center gap-2.5">
            <button
                type="button"
                wire:click="$set('modalGuiaAberto', true)"
                class="flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-xl text-xs font-mono font-semibold text-blue-300 bg-gray-900/90 hover:bg-gray-800 border border-blue-500/30 hover:border-blue-400/60 shadow-lg shadow-black/40 transition-all cursor-pointer"
            >
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Guia de Preenchimento</span>
            </button>

            <button
                type="button"
                wire:click="abrirModalNovo"
                class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-mono font-bold text-white bg-gradient-to-r from-blue-700 to-blue-600 hover:from-blue-600 hover:to-blue-500 shadow-lg shadow-blue-900/40 border border-blue-500/40 transition-all cursor-pointer shrink-0"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Novo Tipo de Documento</span>
            </button>
        </div>
    </div>

    <!-- Tabela de Registros -->
    @php
        $filtrados = array_filter($this->tipos, function($item) {
            $matchBusca = empty($this->busca) ||
                (stripos($item['sigla'] ?? '', $this->busca) !== false) ||
                (stripos($item['nome'] ?? '', $this->busca) !== false) ||
                (stripos($item['descricao'] ?? '', $this->busca) !== false);
            $matchStatus = empty($this->filtro_status) || (($item['status'] ?? 'Ativo') === $this->filtro_status);
            return $matchBusca && $matchStatus;
        });
    @endphp

    <div class="rounded-2xl border border-gray-800/80 bg-[#040a16] shadow-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-mono">
                <thead>
                    <tr class="border-b border-gray-800/80 bg-[#020610]/70 text-gray-400 uppercase text-[10px] tracking-wider">
                        <th class="py-3 px-4">Sigla</th>
                        <th class="py-3 px-4">Nome do Tipo de Documento</th>
                        <th class="py-3 px-4">Padrão de Numeração Gerada</th>
                        <th class="py-3 px-4">Finalidade Doutrinária</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-gray-300">
                    @forelse ($filtrados as $t)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="py-3 px-4 font-bold text-yellow-400">
                                <span class="px-2.5 py-1 rounded-md bg-yellow-950/60 border border-yellow-700/50">
                                    {{ $t['sigla'] }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-semibold text-white">{{ $t['nome'] }}</td>
                            <td class="py-3 px-4 text-blue-400 font-bold">
                                {{ $t['sigla'] }}-{{ date('Y') }}-XXXX
                            </td>
                            <td class="py-3 px-4 text-gray-400 max-w-xs truncate">{{ $t['descricao'] ?: '-' }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ ($t['status'] ?? 'Ativo') === 'Ativo' ? 'bg-emerald-950/70 text-emerald-400 border-emerald-700/50' : 'bg-amber-950/70 text-amber-400 border-amber-700/50' }}">
                                    {{ $t['status'] ?? 'Ativo' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right space-x-1">
                                <button
                                    wire:click="abrirModalEditar('{{ $t['id'] }}')"
                                    type="button"
                                    class="p-1.5 rounded-lg text-gray-400 hover:text-blue-400 hover:bg-blue-950/30 transition-colors cursor-pointer"
                                    title="Editar Tipo"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button
                                    wire:click="excluir('{{ $t['id'] }}')"
                                    wire:confirm="Confirma a exclusão deste tipo de documento?"
                                    type="button"
                                    class="p-1.5 rounded-lg text-gray-400 hover:text-red-400 hover:bg-red-950/30 transition-colors cursor-pointer"
                                    title="Excluir Tipo"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colSpan="6" class="py-12 text-center">
                                <div class="max-w-md mx-auto space-y-3">
                                    <div class="w-12 h-12 mx-auto rounded-xl bg-blue-950/40 border border-blue-800/40 flex items-center justify-center text-blue-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-200">Tabela de Tipos de Documento Inicia Vazia</p>
                                    <p class="text-xs text-gray-400 font-mono leading-relaxed">
                                        Cadastre aqui as categorias doutrinárias e suas siglas (ex: <strong class="text-yellow-400">RFC</strong> para Relatório Final de Comissão ou <strong class="text-yellow-400">ARFC</strong> para Análise de RFC). O sistema utilizará a sigla para gerar automaticamente o número único do documento.
                                    </p>
                                    <div class="pt-2 flex items-center justify-center gap-3">
                                        <button
                                            type="button"
                                            wire:click="abrirModalNovo"
                                            class="inline-flex items-center gap-2 px-3.5 py-1.5 text-xs font-mono font-medium rounded-lg bg-blue-600 hover:bg-blue-500 text-white transition-colors cursor-pointer"
                                        >
                                            + Cadastrar Primeiro Tipo
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="$set('modalGuiaAberto', true)"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-mono text-gray-400 hover:text-white transition-colors cursor-pointer"
                                        >
                                            📘 Ver Orientações Doutrinárias
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de Cadastro / Edição -->
    @if ($modalAberto)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-in fade-in duration-150">
            <div class="w-full max-w-lg rounded-2xl bg-[#061021] border border-blue-900/60 shadow-2xl p-6 sm:p-7 space-y-5">
                <div class="flex items-center justify-between border-b border-gray-800/80 pb-3">
                    <h3 class="text-sm font-bold text-white uppercase font-mono">
                        {{ $editandoId ? 'Editar Tipo de Documento' : 'Cadastrar Novo Tipo de Documento' }}
                    </h3>
                    <button
                        wire:click="fecharModal"
                        type="button"
                        class="text-gray-400 hover:text-white p-1 rounded-lg cursor-pointer"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="salvar" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-1">
                                Sigla Identificadora <span class="text-red-400">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model.live="sigla"
                                placeholder="Ex: RFC, ARFC, RTE"
                                class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3 py-2 text-xs text-white uppercase font-mono focus:outline-none focus:border-blue-500"
                            />
                            @error('sigla') <span class="text-[10px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-1">
                                Status do Tipo <span class="text-red-400">*</span>
                            </label>
                            <select
                                wire:model="status"
                                class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-blue-500 cursor-pointer"
                            >
                                <option value="Ativo">Ativo</option>
                                <option value="Em Revisão">Em Revisão</option>
                            </select>
                        </div>
                    </div>

                    <!-- Visualização da Numeração Gerada -->
                    @if (!empty($sigla))
                        <div class="p-3 rounded-xl bg-blue-950/30 border border-blue-800/40 text-xs font-mono">
                            <span class="text-gray-400 block text-[10px] uppercase">Padrão de Número Único a Ser Gerado:</span>
                            <span class="text-yellow-400 font-bold text-sm">
                                {{ strtoupper(trim($sigla)) }}-{{ date('Y') }}-0001
                            </span>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-1">
                            Nome Completo por Extenso <span class="text-red-400">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model="nome"
                            placeholder="Ex: Relatório Final de Comissão, Análise de Relatório Final de Comissão"
                            class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-blue-500"
                        />
                        @error('nome') <span class="text-[10px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-1">
                            Finalidade / Descrição Doutrinária
                        </label>
                        <textarea
                            wire:model="descricao"
                            rows="2"
                            placeholder="Descreva a finalidade técnica ou regulamentar deste tipo de documento..."
                            class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3 py-2 text-xs text-white font-sans focus:outline-none focus:border-blue-500"
                        ></textarea>
                    </div>

                    <div class="pt-3 border-t border-gray-800/80 flex items-center justify-end gap-2.5">
                        <button
                            type="button"
                            wire:click="fecharModal"
                            class="px-4 py-2 rounded-xl text-xs font-mono font-bold text-gray-400 hover:text-white bg-gray-900 border border-gray-800 transition-colors cursor-pointer"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2 rounded-xl text-xs font-mono font-bold text-white bg-blue-600 hover:bg-blue-500 shadow-md transition-colors cursor-pointer"
                        >
                            Salvar Tipo de Documento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal de Guia de Apoio: Tipos de Documento -->
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
                                Guia de Apoio: Tipos de Documento
                            </h3>
                            <p class="text-xs text-blue-400/80 font-mono">
                                Geração de Números Únicos & Padronização Doutrinária - ComForS
                            </p>
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
                        <span class="text-blue-300 font-bold block mb-1">FINALIDADE DA TABELA DE TIPOS</span>
                        Esta tabela de apoio centraliza os tipos de documentos que servem de referência para as Lições Aprendidas. Cada tipo registrado possui uma <strong class="text-yellow-400">sigla</strong> que alimenta a regra de geração do número único.
                    </div>

                    <div class="space-y-3">
                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">1. Exemplos de Siglas Doutrinárias</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">
                                <strong class="text-yellow-400">RFC</strong>: Relatório Final de Comissão.<br />
                                <strong class="text-yellow-400">ARFC</strong>: Análise de Relatório Final de Comissão.<br />
                                <strong class="text-yellow-400">RTE</strong>: Relatório Técnico de Engenharia.<br />
                                <strong class="text-yellow-400">RCI</strong>: Relatório Circunstanciado de Incidente Operacional.
                            </p>
                        </div>

                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">2. Regra de Numeração Única</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">
                                Ao cadastrar um novo documento na tela <strong class="text-blue-400">Novo Documento</strong>, ao escolher o tipo, o sistema combina a <span class="text-yellow-400">SIGLA</span> + <span class="text-white">ANO</span> + <span class="text-emerald-400">SEQUENCIAL DE 4 DÍGITOS</span>. Exemplo: <code class="text-white bg-black/40 px-1 py-0.5 rounded">RFC-2026-0001</code> ou <code class="text-white bg-black/40 px-1 py-0.5 rounded">ARFC-2026-0001</code>.
                            </p>
                        </div>

                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">3. Vínculo Futuro com a Lição Aprendida</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">
                                Os documentos cadastrados com seu respectivo número único ficarão disponíveis para citação e associação como evidência/referência oficial de lições aprendidas.
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
