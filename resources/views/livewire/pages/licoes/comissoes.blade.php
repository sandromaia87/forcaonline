<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public array $comissoes = [];
    public string $busca = '';
    public string $filtro_tipo = '';

    // Estado do Modal de Cadastro / Edição
    public bool $modalAberto = false;
    public bool $modalGuiaAberto = false;
    public ?string $editandoId = null;

    public string $codigo = '';
    public string $nome = '';
    public string $tipo = 'Operação de Adestramento';
    public string $meio_naval = 'S34 - Submarino Tikuna';
    public string $status = 'Planejada';
    public string $data_inicio = '';
    public string $data_fim = '';

    public function mount(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->temAcessoAoModulo('licoes_aprendidas')) {
            abort(403, 'Acesso não autorizado.');
        }

        session(['modulo_ativo' => 'licoes_aprendidas']);

        // A tabela inicia vazia conforme especificado; comissões são criadas sob demanda pelos operadores
        $this->comissoes = session('crud_comissoes', []);
        $this->data_inicio = date('Y-m-d');
        $this->data_fim = date('Y-m-d', strtotime('+15 days'));
    }

    public function abrirGuia(): void
    {
        $this->modalGuiaAberto = true;
    }

    public function fecharGuia(): void
    {
        $this->modalGuiaAberto = false;
    }

    public function abrirModalNovo(): void
    {
        $this->reset(['editandoId', 'codigo', 'nome']);
        $this->tipo = 'Operação de Adestramento';
        $this->meio_naval = 'S34 - Submarino Tikuna';
        $this->status = 'Planejada';
        $this->data_inicio = date('Y-m-d');
        $this->data_fim = date('Y-m-d', strtotime('+15 days'));
        $this->modalAberto = true;
    }

    public function editar(string $id): void
    {
        foreach ($this->comissoes as $item) {
            if ($item['id'] === $id) {
                $this->editandoId = $id;
                $this->codigo = $item['codigo'];
                $this->nome = $item['nome'];
                $this->tipo = $item['tipo'];
                $this->meio_naval = $item['meio_naval'];
                $this->status = $item['status'];
                $this->data_inicio = $item['data_inicio'] ?? date('Y-m-d');
                $this->data_fim = $item['data_fim'] ?? date('Y-m-d');
                $this->modalAberto = true;
                break;
            }
        }
    }

    public function salvar(): void
    {
        $this->validate([
            'codigo' => 'required|min:3|max:30',
            'nome' => 'required|min:4|max:120',
            'tipo' => 'required',
            'meio_naval' => 'required',
            'status' => 'required',
        ], [
            'codigo.required' => 'O código identificador da comissão é obrigatório (ex: COM-TROPICALEX-2026).',
            'nome.required' => 'Informe o nome operacional da comissão.',
            'meio_naval.required' => 'Selecione a unidade naval participante.',
        ]);

        $itemAtualizado = [
            'id' => $this->editandoId ?? (string) (count($this->comissoes) + 1),
            'codigo' => strtoupper(trim($this->codigo)),
            'nome' => trim($this->nome),
            'tipo' => $this->tipo,
            'meio_naval' => $this->meio_naval,
            'status' => $this->status,
            'data_inicio' => $this->data_inicio,
            'data_fim' => $this->data_fim,
        ];

        if ($this->editandoId) {
            $this->comissoes = array_map(function ($c) use ($itemAtualizado) {
                return $c['id'] === $this->editandoId ? $itemAtualizado : $c;
            }, $this->comissoes);
        } else {
            $this->comissoes[] = $itemAtualizado;
        }

        session(['crud_comissoes' => $this->comissoes]);
        $this->modalAberto = false;
    }

    public function excluir(string $id): void
    {
        $this->comissoes = array_values(array_filter($this->comissoes, fn ($c) => $c['id'] !== $id));
        session(['crud_comissoes' => $this->comissoes]);
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
                        Tabela de Apoio 3 de 3
                    </span>
                </div>
                <p class="text-[11px] text-gray-400 font-mono mt-0.5 leading-snug">
                    Estrutura preliminar para testes operacionais. Em breve integração com o repositório geral de fainas e comissões da Marinha.
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
            <!-- Busca por código ou nome -->
            <div class="relative flex-1 max-w-md">
                <input 
                    type="text" 
                    wire:model.live.debounce.250ms="busca"
                    placeholder="Buscar comissão por sigla ou nome..." 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl pl-9 pr-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 font-mono transition-colors"
                />
                <svg class="w-4 h-4 text-gray-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Filtro de Tipo -->
            <select 
                wire:model.live="filtro_tipo"
                class="bg-[#02050d] border border-gray-800 rounded-xl px-3 py-2 text-xs text-gray-300 focus:outline-none focus:border-blue-500 font-mono"
            >
                <option value="">Todos os Tipos</option>
                <option value="Operação de Adestramento">Adestramento</option>
                <option value="Operação Tática">Operação Tática</option>
                <option value="Patrulha Naval Submersa">Patrulha Submersa</option>
                <option value="Provas de Mar / PMG">Provas de Mar</option>
            </select>
        </div>

        <div class="flex items-center gap-2.5">
            <!-- Botão Guia de Preenchimento / Apoio -->
            <button 
                type="button" 
                wire:click="abrirGuia" 
                class="flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-xl text-xs font-mono font-semibold text-blue-300 bg-gray-900/90 hover:bg-gray-800 border border-blue-500/30 hover:border-blue-400/60 shadow-lg shadow-black/40 transition-all duration-200 cursor-pointer"
            >
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Guia de Preenchimento</span>
            </button>

            <!-- Botão Nova Comissão -->
            <button 
                type="button" 
                wire:click="abrirModalNovo" 
                class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-mono font-bold text-white bg-gradient-to-r from-blue-700 to-blue-600 hover:from-blue-600 hover:to-blue-500 shadow-lg shadow-blue-900/40 border border-blue-500/40 transition-all duration-200 cursor-pointer shrink-0"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Nova Comissão</span>
            </button>
        </div>
    </div>

    <!-- Tabela Argon Naval de Registros -->
    <div class="rounded-2xl border border-gray-800/80 bg-[#040a16] shadow-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-mono">
                <thead>
                    <tr class="border-b border-gray-800/80 bg-[#020610]/70 text-gray-400 uppercase text-[10px] tracking-wider">
                        <th class="py-3 px-4">Sigla / Código</th>
                        <th class="py-3 px-4">Nome da Comissão / Operação</th>
                        <th class="py-3 px-4">Unidade Participante</th>
                        <th class="py-3 px-4">Tipo de Missão</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-gray-300">
                    @php
                        $filtrados = array_filter($comissoes, function($c) {
                            $matchBusca = empty($this->busca) || 
                                (stripos($c['codigo'], $this->busca) !== false) || 
                                (stripos($c['nome'], $this->busca) !== false);
                            $matchTipo = empty($this->filtro_tipo) || ($c['tipo'] === $this->filtro_tipo);
                            return $matchBusca && $matchTipo;
                        });
                    @endphp

                    @forelse ($filtrados as $c)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="py-3 px-4 font-bold text-blue-400">{{ $c['codigo'] }}</td>
                            <td class="py-3 px-4 font-semibold text-white">{{ $c['nome'] }}</td>
                            <td class="py-3 px-4 text-gray-300">{{ $c['meio_naval'] }}</td>
                            <td class="py-3 px-4 text-gray-400">{{ $c['tipo'] }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $c['status'] === 'Homologada' ? 'bg-emerald-950/70 text-emerald-400 border-emerald-700/50' : ($c['status'] === 'Em Execução no Mar' ? 'bg-blue-950/70 text-blue-400 border-blue-700/50' : 'bg-amber-950/70 text-amber-400 border-amber-700/50') }}">
                                    {{ $c['status'] }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right space-x-1">
                                <button 
                                    wire:click="editar('{{ $c['id'] }}')" 
                                    type="button" 
                                    class="p-1.5 rounded-lg text-gray-400 hover:text-blue-400 hover:bg-blue-950/30 transition-colors"
                                    title="Editar Comissão"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button 
                                    wire:click="excluir('{{ $c['id'] }}')" 
                                    wire:confirm="Confirma a exclusão desta comissão operacional?"
                                    type="button" 
                                    class="p-1.5 rounded-lg text-gray-400 hover:text-red-400 hover:bg-red-950/30 transition-colors"
                                    title="Excluir Comissão"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="max-w-md mx-auto space-y-3">
                                    <div class="w-12 h-12 mx-auto rounded-xl bg-blue-950/40 border border-blue-800/40 flex items-center justify-center text-blue-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-200">Tabela de Comissões Operacionais Inicia Vazia</p>
                                    <p class="text-xs text-gray-400 font-mono leading-relaxed">
                                        Conforme diretriz, as comissões e fainas operacionais são adicionadas sob demanda pelos operadores ao catalogar lições ou viagens. Cadastre a primeira pelo botão "+ Nova Comissão" ou consulte o guia.
                                    </p>
                                    <div class="pt-2 flex items-center justify-center gap-3">
                                        <button type="button" wire:click="abrirModalNovo" class="inline-flex items-center gap-2 px-3.5 py-1.5 text-xs font-mono font-medium rounded-lg bg-blue-600 hover:bg-blue-500 text-white transition-colors cursor-pointer">
                                            + Cadastrar Primeira Comissão
                                        </button>
                                        <button type="button" wire:click="abrirGuia" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-mono text-gray-400 hover:text-white transition-colors cursor-pointer">
                                            📘 Ver Instruções Doutrinárias
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

    <!-- Modal de Cadastro / Edição de Comissão -->
    @if ($modalAberto)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-in fade-in duration-150">
            <div class="w-full max-w-lg rounded-2xl bg-[#061021] border border-blue-900/60 shadow-2xl p-6 sm:p-7 space-y-5">
                <div class="flex items-center justify-between border-b border-gray-800/80 pb-3">
                    <h3 class="text-sm font-bold text-white uppercase font-mono">
                        {{ $editandoId ? 'Editar Comissão Operacional' : 'Cadastrar Nova Comissão Operacional' }}
                    </h3>
                    <button wire:click="$set('modalAberto', false)" type="button" class="text-gray-400 hover:text-white p-1 rounded-lg cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="salvar" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-1">
                                Sigla / Código <span class="text-red-400">*</span>
                            </label>
                            <input 
                                type="text" 
                                wire:model="codigo" 
                                placeholder="Ex: COM-TROPICALEX-2026"
                                class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3 py-2 text-xs text-white uppercase font-mono focus:outline-none focus:border-blue-500"
                            />
                            @error('codigo') <span class="text-[10px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-1">
                                Unidade Naval <span class="text-red-400">*</span>
                            </label>
                            <select 
                                wire:model="meio_naval"
                                class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-blue-500"
                            >
                                <option value="S30 - Submarino Tupi">S30 - Submarino Tupi</option>
                                <option value="S34 - Submarino Tikuna">S34 - Submarino Tikuna</option>
                                <option value="S40 - Submarino Riachuelo">S40 - Submarino Riachuelo</option>
                                <option value="S41 - Submarino Humaitá">S41 - Submarino Humaitá</option>
                                <option value="S42 - Submarino Tonelero">S42 - Submarino Tonelero</option>
                                <option value="ComForS - Comando da Força">ComForS - Comando da Força</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-1">
                            Nome da Comissão / Operação <span class="text-red-400">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="nome" 
                            placeholder="Ex: Operação Tropicalex 2026, Adestramento Básico de Imersão"
                            class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-blue-500"
                        />
                        @error('nome') <span class="text-[10px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-1">
                                Tipo de Missão
                            </label>
                            <select 
                                wire:model="tipo"
                                class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-blue-500"
                            >
                                <option value="Operação de Adestramento">Operação de Adestramento</option>
                                <option value="Operação Tática">Operação Tática</option>
                                <option value="Patrulha Naval Submersa">Patrulha Naval Submersa</option>
                                <option value="Provas de Mar / PMG">Provas de Mar / PMG</option>
                                <option value="Exercício Multinacional">Exercício Multinacional</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-1">
                                Status da Comissão
                            </label>
                            <select 
                                wire:model="status"
                                class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-blue-500"
                            >
                                <option value="Planejada">Planejada</option>
                                <option value="Em Execução no Mar">Em Execução no Mar</option>
                                <option value="Encerrada / Aguardando RFC">Encerrada / Aguardando RFC</option>
                                <option value="Homologada">Homologada</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gray-800/80 flex items-center justify-end gap-2.5">
                        <button 
                            type="button" 
                            wire:click="$set('modalAberto', false)"
                            class="px-4 py-2 rounded-xl text-xs font-mono font-bold text-gray-400 hover:text-white bg-gray-900 border border-gray-800 transition-colors cursor-pointer"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="submit" 
                            class="px-5 py-2 rounded-xl text-xs font-mono font-bold text-white bg-blue-600 hover:bg-blue-500 shadow-md transition-colors cursor-pointer"
                        >
                            Salvar Registro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal de Guia de Apoio: Comissões Operacionais -->
    @if ($modalGuiaAberto)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 backdrop-blur-md p-4 animate-in fade-in duration-200">
            <div class="bg-[#0b1329] border border-blue-500/40 rounded-2xl w-full max-w-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-950/80 to-[#0a1226] border-b border-gray-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/20 border border-blue-400/30 flex items-center justify-center text-blue-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider font-mono">Guia de Apoio: Comissões Operacionais</h3>
                            <p class="text-xs text-blue-400/80 font-mono">Padronização de Missões & Vínculo com RFC - ComForS</p>
                        </div>
                    </div>
                    <button wire:click="fecharGuia" class="text-gray-400 hover:text-white p-1 rounded-lg hover:bg-gray-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto space-y-4 text-xs font-mono text-gray-300">
                    <div class="p-3.5 rounded-xl bg-blue-950/20 border border-blue-800/30">
                        <span class="text-blue-300 font-bold block mb-1">CONCEITO DOUTRINÁRIO DE COMISSÃO</span>
                        Uma comissão é a designação formal de uma missão, faina, exercício tático ou viagem de instrução/provas atribuída a um submarino ou grupo-tarefa do ComForS.
                    </div>

                    <div class="space-y-3">
                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">1. Sigla / Código Identificador</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">Utilize o código padronizado pelo Estado-Maior. Exemplos: <span class="text-blue-300 font-bold">COM-TROPICALEX-2026</span>, <span class="text-blue-300 font-bold">COM-ADEST-S34</span>, <span class="text-blue-300 font-bold">COM-PATSUB-01</span>.</p>
                        </div>

                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">2. Vínculo com o RFC (Relatório Final de Comissão)</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">Ao término de cada comissão, o Comandante do Submarino emite o <span class="text-yellow-400 font-bold">RFC</span>. Esse relatório será gerenciado pelo módulo <span class="text-amber-400 font-bold">SIPA-SUB</span> e poderá ser anexado diretamente à lição aprendida.</p>
                        </div>

                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">3. Tipos de Comissão</p>
                            <p class="text-gray-400 text-[11px] mt-0.5"><span class="text-blue-300 font-semibold">Operação Tática:</span> Emprego em missão real de defesa. <span class="text-blue-300 font-semibold">Adestramento:</span> Exercícios sob o mar. <span class="text-blue-300 font-semibold">Provas de Mar:</span> Testes de aceitação pós-manutenção.</p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-3.5 bg-gray-950/80 border-t border-gray-800 flex items-center justify-end">
                    <button wire:click="fecharGuia" class="px-4 py-2 rounded-xl text-xs font-mono font-semibold bg-blue-600 hover:bg-blue-500 text-white transition-colors">
                        Entendido / Fechar Guia
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
