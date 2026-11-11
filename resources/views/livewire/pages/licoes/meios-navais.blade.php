<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public array $meios = [];
    public string $busca = '';
    public string $filtro_tipo = '';

    // Estado do Modal de Cadastro / Edição
    public bool $modalAberto = false;
    public ?string $editandoId = null;
    public string $indicativo = '';
    public string $nome = '';
    public string $tipo = 'Submarino';
    public string $classe = 'Classe Riachuelo (S-BR)';
    public string $status = 'Operacional';

    public function mount(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->temAcessoAoModulo('licoes_aprendidas')) {
            abort(403, 'Acesso não autorizado.');
        }

        session(['modulo_ativo' => 'licoes_aprendidas']);

        $padroes = [
            ['id' => '1', 'indicativo' => 'S30', 'nome' => 'Submarino Tupi', 'tipo' => 'Submarino', 'classe' => 'Classe Tupi (IKL 209)', 'status' => 'Operacional'],
            ['id' => '2', 'indicativo' => 'S34', 'nome' => 'Submarino Tikuna', 'tipo' => 'Submarino', 'classe' => 'Classe Tikuna (IKL Mod)', 'status' => 'Operacional'],
            ['id' => '3', 'indicativo' => 'S40', 'nome' => 'Submarino Riachuelo', 'tipo' => 'Submarino', 'classe' => 'Classe Riachuelo (S-BR)', 'status' => 'Operacional'],
            ['id' => '4', 'indicativo' => 'S41', 'nome' => 'Submarino Humaitá', 'tipo' => 'Submarino', 'classe' => 'Classe Riachuelo (S-BR)', 'status' => 'Operacional'],
            ['id' => '5', 'indicativo' => 'S42', 'nome' => 'Submarino Tonelero', 'tipo' => 'Submarino', 'classe' => 'Classe Riachuelo (S-BR)', 'status' => 'Operacional'],
            ['id' => '6', 'indicativo' => 'S43', 'nome' => 'Submarino Angostura', 'tipo' => 'Submarino', 'classe' => 'Classe Riachuelo (S-BR)', 'status' => 'Em Construção'],
            ['id' => '7', 'indicativo' => 'S44', 'nome' => 'Submarino Álvaro Alberto', 'tipo' => 'Submarino', 'classe' => 'Propulsão Nuclear (SN-BR)', 'status' => 'Em Projeto'],
            ['id' => '8', 'indicativo' => 'BACS', 'nome' => 'Base Almirante Castro e Silva', 'tipo' => 'Organização Militar', 'classe' => 'Base Operacional', 'status' => 'Ativa'],
            ['id' => '9', 'indicativo' => 'CIAMA', 'nome' => 'Centro de Instrução e Adestramento Almirante Átilla Monteiro Aché', 'tipo' => 'Organização Militar', 'classe' => 'Centro de Instrução', 'status' => 'Ativa'],
        ];

        $this->meios = session('crud_meios_navais', $padroes);
    }

    public function abrirModalNovo(): void
    {
        $this->reset(['editandoId', 'indicativo', 'nome']);
        $this->tipo = 'Submarino';
        $this->classe = 'Classe Riachuelo (S-BR)';
        $this->status = 'Operacional';
        $this->modalAberto = true;
    }

    public function editar(string $id): void
    {
        foreach ($this->meios as $item) {
            if ($item['id'] === $id) {
                $this->editandoId = $id;
                $this->indicativo = $item['indicativo'];
                $this->nome = $item['nome'];
                $this->tipo = $item['tipo'];
                $this->classe = $item['classe'];
                $this->status = $item['status'];
                $this->modalAberto = true;
                return;
            }
        }
    }

    public function salvar(): void
    {
        $this->validate([
            'indicativo' => 'required|min:2|max:15',
            'nome' => 'required|min:3|max:100',
            'tipo' => 'required',
            'classe' => 'required',
            'status' => 'required',
        ], [
            'indicativo.required' => 'Informe o indicativo naval (ex: S34, BACS).',
            'nome.required' => 'Informe o nome completo do meio ou OM.',
        ]);

        if ($this->editandoId) {
            foreach ($this->meios as &$item) {
                if ($item['id'] === $this->editandoId) {
                    $item['indicativo'] = strtoupper(trim($this->indicativo));
                    $item['nome'] = trim($this->nome);
                    $item['tipo'] = $this->tipo;
                    $item['classe'] = $this->classe;
                    $item['status'] = $this->status;
                    break;
                }
            }
        } else {
            $this->meios[] = [
                'id' => (string) (time() . rand(10, 99)),
                'indicativo' => strtoupper(trim($this->indicativo)),
                'nome' => trim($this->nome),
                'tipo' => $this->tipo,
                'classe' => $this->classe,
                'status' => $this->status,
            ];
        }

        session(['crud_meios_navais' => $this->meios]);
        $this->modalAberto = false;
    }

    public function excluir(string $id): void
    {
        $this->meios = array_values(array_filter($this->meios, fn ($item) => $item['id'] !== $id));
        session(['crud_meios_navais' => $this->meios]);
    }

    public function fecharModal(): void
    {
        $this->modalAberto = false;
    }
}; ?>

<div class="max-w-6xl mx-auto space-y-6 select-none">

    <!-- Barra de Controle e Ações -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-[#040a16] border border-gray-800/80 shadow-xl">
        <div class="flex flex-wrap items-center gap-3 flex-1">
            <!-- Campo de Busca -->
            <div class="relative flex-1 min-w-[200px] max-w-sm">
                <input 
                    type="text" 
                    wire:model.live.debounce.250ms="busca" 
                    placeholder="Buscar meio ou OM..." 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl pl-9 pr-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 font-mono transition-colors"
                />
                <svg class="w-4 h-4 text-gray-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Filtro de Categoria -->
            <select 
                wire:model.live="filtro_tipo" 
                class="bg-[#02050d] border border-gray-800 rounded-xl px-3 py-2 text-xs text-gray-300 focus:outline-none focus:border-blue-500 font-mono"
            >
                <option value="">Todas Categorias</option>
                <option value="Submarino">Submarinos</option>
                <option value="Organização Militar">Organizações Militares</option>
            </select>
        </div>

        <!-- Botão Novo Meio Naval -->
        <button 
            type="button" 
            wire:click="abrirModalNovo" 
            class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-mono font-bold text-white bg-gradient-to-r from-blue-700 to-blue-600 hover:from-blue-600 hover:to-blue-500 shadow-lg shadow-blue-900/40 border border-blue-500/40 transition-all cursor-pointer shrink-0"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Novo Meio Naval / OM</span>
        </button>
    </div>

    <!-- Tabela Argon Naval de Registros -->
    <div class="rounded-2xl border border-gray-800/80 bg-[#040a16] shadow-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-mono">
                <thead>
                    <tr class="border-b border-gray-800/80 bg-[#020610]/70 text-gray-400 uppercase text-[10px] tracking-wider">
                        <th class="py-3 px-4">Indicativo</th>
                        <th class="py-3 px-4">Nome do Meio / Organização Militar</th>
                        <th class="py-3 px-4">Classe / Padrão</th>
                        <th class="py-3 px-4">Tipo</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-gray-300">
                    @php
                        $filtrados = array_filter($meios, function($item) {
                            $matchBusca = empty($busca) || 
                                          str_contains(strtolower($item['indicativo']), strtolower($busca)) || 
                                          str_contains(strtolower($item['nome']), strtolower($busca));
                            $matchTipo = empty($filtro_tipo) || $item['tipo'] === $filtro_tipo;
                            return $matchBusca && $matchTipo;
                        });
                    @endphp

                    @forelse($filtrados as $m)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="py-3 px-4 font-bold text-white flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[11px] bg-blue-950/80 text-yellow-400 border border-blue-800/60">
                                    {{ $m['indicativo'] }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-sans font-medium text-white">
                                {{ $m['nome'] }}
                            </td>
                            <td class="py-3 px-4 text-gray-400">
                                {{ $m['classe'] }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-xs {{ $m['tipo'] === 'Submarino' ? 'text-blue-400 font-semibold' : 'text-amber-400 font-semibold' }}">
                                    {{ $m['tipo'] }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if($m['status'] === 'Operacional' || $m['status'] === 'Ativa')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] bg-emerald-950/80 text-emerald-400 border border-emerald-800/60 font-mono">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        {{ $m['status'] }}
                                    </span>
                                @elseif($m['status'] === 'Em Manutenção')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] bg-amber-950/80 text-amber-400 border border-amber-800/60 font-mono">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                        {{ $m['status'] }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] bg-gray-900 text-gray-400 border border-gray-700 font-mono">
                                        {{ $m['status'] }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <button 
                                        wire:click="editar('{{ $m['id'] }}')" 
                                        type="button" 
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-yellow-400 hover:bg-gray-900 border border-transparent hover:border-gray-800 transition-colors cursor-pointer"
                                        title="Editar Meio Naval"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button 
                                        wire:click="excluir('{{ $m['id'] }}')" 
                                        wire:confirm="Confirmar exclusão deste registro?"
                                        type="button" 
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-red-400 hover:bg-red-950/30 border border-transparent hover:border-red-900/50 transition-colors cursor-pointer"
                                        title="Excluir Meio Naval"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 font-mono">
                                Nenhum meio naval ou OM localizado com os filtros selecionados.
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
                        {{ $editandoId ? 'Editar Meio Naval / OM' : 'Cadastrar Novo Meio Naval / OM' }}
                    </h3>
                    <button wire:click="fecharModal" type="button" class="text-gray-400 hover:text-white p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="salvar" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-1">
                                Indicativo / Sigla <span class="text-red-400">*</span>
                            </label>
                            <input 
                                type="text" 
                                wire:model="indicativo" 
                                placeholder="Ex: S34, S40, BACS" 
                                class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono"
                            />
                            @error('indicativo') <span class="text-[10px] text-red-400 font-mono block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-1">
                                Tipo de Meio <span class="text-red-400">*</span>
                            </label>
                            <select 
                                wire:model="tipo" 
                                class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono"
                            >
                                <option value="Submarino">Submarino</option>
                                <option value="Organização Militar">Organização Militar</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-1">
                            Nome Completo Oficial <span class="text-red-400">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="nome" 
                            placeholder="Ex: Submarino Tikuna, Base Almirante Castro e Silva" 
                            class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-sans"
                        />
                        @error('nome') <span class="text-[10px] text-red-400 font-mono block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-1">
                                Classe / Enquadramento <span class="text-red-400">*</span>
                            </label>
                            <input 
                                type="text" 
                                wire:model="classe" 
                                placeholder="Ex: Classe Tikuna, S-BR" 
                                class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-1">
                                Status Operacional <span class="text-red-400">*</span>
                            </label>
                            <select 
                                wire:model="status" 
                                class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono"
                            >
                                <option value="Operacional">Operacional</option>
                                <option value="Ativa">Ativa</option>
                                <option value="Em Manutenção">Em Manutenção</option>
                                <option value="Em Construção">Em Construção</option>
                                <option value="Em Projeto">Em Projeto</option>
                            </select>
                        </div>
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
                            Salvar Registro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
