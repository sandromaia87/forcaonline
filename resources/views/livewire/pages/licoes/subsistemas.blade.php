<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public array $subsistemas = [];
    public string $busca = '';
    public string $filtro_area = '';

    // Estado do Modal
    public bool $modalAberto = false;
    public ?string $editandoId = null;
    public string $nome = '';
    public string $area = 'Propulsão & Geração';
    public string $criticidade = 'Crítica de Segurança';
    public string $status = 'Homologado';

    public function mount(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->temAcessoAoModulo('licoes_aprendidas')) {
            abort(403, 'Acesso não autorizado.');
        }

        session(['modulo_ativo' => 'licoes_aprendidas']);

        $padroes = [
            ['id' => '1', 'nome' => 'Motores de Combustão Principal (MCP)', 'area' => 'Propulsão & Geração', 'criticidade' => 'Crítica de Segurança', 'status' => 'Homologado'],
            ['id' => '2', 'nome' => 'Motores Elétricos de Propulsão (MEP)', 'area' => 'Propulsão & Geração', 'criticidade' => 'Crítica de Segurança', 'status' => 'Homologado'],
            ['id' => '3', 'nome' => 'Sistema de Imersão e Tanques de Lastro', 'area' => 'Governo & Imersão', 'criticidade' => 'Crítica de Segurança', 'status' => 'Homologado'],
            ['id' => '4', 'nome' => 'Lemes Verticais e Horizontais de Mergulho', 'area' => 'Governo & Imersão', 'criticidade' => 'Crítica de Segurança', 'status' => 'Homologado'],
            ['id' => '5', 'nome' => 'Baterias de Propulsão (Chumbo-Ácido / Lítio)', 'area' => 'Eletricidade Naval', 'criticidade' => 'Crítica de Segurança', 'status' => 'Homologado'],
            ['id' => '6', 'nome' => 'Sonar Passivo e Flanco (Cilindro / Flank Array)', 'area' => 'Sensores & Sonar', 'criticidade' => 'Operacional Essencial', 'status' => 'Homologado'],
            ['id' => '7', 'nome' => 'Periscópio de Ataque e Mastro Optrônico', 'area' => 'Sensores & Sonar', 'criticidade' => 'Operacional Essencial', 'status' => 'Homologado'],
            ['id' => '8', 'nome' => 'Tubos de Torpedo e Sistema de Descarga F21', 'area' => 'Armamento Submarino', 'criticidade' => 'Operacional Essencial', 'status' => 'Homologado'],
            ['id' => '9', 'nome' => 'Atmosfera Confinada e Purificação de CO2', 'area' => 'Atmosfera & CAV', 'criticidade' => 'Crítica de Segurança', 'status' => 'Homologado'],
            ['id' => '10', 'nome' => 'Comunicações VLF/HF e Satélite Submerso', 'area' => 'Comunicações', 'criticidade' => 'Tática', 'status' => 'Homologado'],
        ];

        $this->subsistemas = session('crud_subsistemas', $padroes);
    }

    public function abrirModalNovo(): void
    {
        $this->reset(['editandoId', 'nome']);
        $this->area = 'Propulsão & Geração';
        $this->criticidade = 'Crítica de Segurança';
        $this->status = 'Homologado';
        $this->modalAberto = true;
    }

    public function editar(string $id): void
    {
        foreach ($this->subsistemas as $item) {
            if ($item['id'] === $id) {
                $this->editandoId = $id;
                $this->nome = $item['nome'];
                $this->area = $item['area'];
                $this->criticidade = $item['criticidade'];
                $this->status = $item['status'];
                $this->modalAberto = true;
                return;
            }
        }
    }

    public function salvar(): void
    {
        $this->validate([
            'nome' => 'required|min:3|max:120',
            'area' => 'required',
            'criticidade' => 'required',
            'status' => 'required',
        ], [
            'nome.required' => 'Informe a designação do sistema ou subsistema.',
            'nome.min' => 'A designação deve conter no mínimo 3 caracteres.',
        ]);

        if ($this->editandoId) {
            foreach ($this->subsistemas as &$item) {
                if ($item['id'] === $this->editandoId) {
                    $item['nome'] = trim($this->nome);
                    $item['area'] = $this->area;
                    $item['criticidade'] = $this->criticidade;
                    $item['status'] = $this->status;
                    break;
                }
            }
        } else {
            $this->subsistemas[] = [
                'id' => (string) (time() . rand(10, 99)),
                'nome' => trim($this->nome),
                'area' => $this->area,
                'criticidade' => $this->criticidade,
                'status' => $this->status,
            ];
        }

        session(['crud_subsistemas' => $this->subsistemas]);
        $this->modalAberto = false;
    }

    public function excluir(string $id): void
    {
        $this->subsistemas = array_values(array_filter($this->subsistemas, fn ($item) => $item['id'] !== $id));
        session(['crud_subsistemas' => $this->subsistemas]);
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
                    placeholder="Buscar subsistema operacional..." 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl pl-9 pr-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 font-mono transition-colors"
                />
                <svg class="w-4 h-4 text-gray-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Filtro por Macro-área -->
            <select 
                wire:model.live="filtro_area" 
                class="bg-[#02050d] border border-gray-800 rounded-xl px-3 py-2 text-xs text-gray-300 focus:outline-none focus:border-blue-500 font-mono"
            >
                <option value="">Todas Áreas Temáticas</option>
                <option value="Propulsão & Geração">Propulsão & Geração</option>
                <option value="Governo & Imersão">Governo & Imersão</option>
                <option value="Eletricidade Naval">Eletricidade Naval</option>
                <option value="Sensores & Sonar">Sensores & Sonar</option>
                <option value="Armamento Submarino">Armamento Submarino</option>
                <option value="Atmosfera & CAV">Atmosfera & CAV</option>
                <option value="Comunicações">Comunicações</option>
            </select>
        </div>

        <!-- Botão Guia de Preenchimento / Apoio -->
        <button 
            type="button" 
            wire:click="abrirGuia" 
            class="flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-xl text-xs font-mono font-semibold text-blue-300 bg-gray-900/90 hover:bg-gray-800 border border-blue-500/30 hover:border-blue-400/60 shadow-lg shadow-black/40 transition-all duration-200"
        >
            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Guia de Preenchimento</span>
        </button>

        <!-- Botão Novo Subsistema -->
        <button 
            type="button" 
            wire:click="abrirModalNovo" 
            class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-mono font-bold text-white bg-gradient-to-r from-blue-700 to-blue-600 hover:from-blue-600 hover:to-blue-500 shadow-lg shadow-blue-900/40 border border-blue-500/40 transition-all cursor-pointer shrink-0"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Novo Subsistema</span>
        </button>
    </div>

    <!-- Tabela Argon Naval de Registros -->
    <div class="rounded-2xl border border-gray-800/80 bg-[#040a16] shadow-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-mono">
                <thead>
                    <tr class="border-b border-gray-800/80 bg-[#020610]/70 text-gray-400 uppercase text-[10px] tracking-wider">
                        <th class="py-3 px-4">Designação do Subsistema</th>
                        <th class="py-3 px-4">Grande Área / Domínio</th>
                        <th class="py-3 px-4">Nível de Criticidade</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-gray-300">
                    @php
                        $filtrados = array_filter($subsistemas, function($item) {
                            $matchBusca = empty($busca) || 
                                          str_contains(strtolower($item['nome']), strtolower($busca)) || 
                                          str_contains(strtolower($item['area']), strtolower($busca));
                            $matchArea = empty($filtro_area) || $item['area'] === $filtro_area;
                            return $matchBusca && $matchArea;
                        });
                    @endphp

                    @forelse($filtrados as $s)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="py-3 px-4 font-sans font-medium text-white flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-500 shrink-0"></span>
                                <span>{{ $s['nome'] }}</span>
                            </td>
                            <td class="py-3 px-4 text-blue-300/90 font-mono">
                                {{ $s['area'] }}
                            </td>
                            <td class="py-3 px-4">
                                @if($s['criticidade'] === 'Crítica de Segurança')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] bg-red-950/80 text-red-400 border border-red-800/60 font-mono">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400 animate-pulse"></span>
                                        {{ $s['criticidade'] }}
                                    </span>
                                @elseif($s['criticidade'] === 'Operacional Essencial')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] bg-amber-950/80 text-amber-400 border border-amber-800/60 font-mono">
                                        {{ $s['criticidade'] }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] bg-blue-950/80 text-blue-400 border border-blue-800/60 font-mono">
                                        {{ $s['criticidade'] }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] bg-emerald-950/80 text-emerald-400 border border-emerald-800/60">
                                    {{ $s['status'] }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <button 
                                        wire:click="editar('{{ $s['id'] }}')" 
                                        type="button" 
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-yellow-400 hover:bg-gray-900 border border-transparent hover:border-gray-800 transition-colors cursor-pointer"
                                        title="Editar Subsistema"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button 
                                        wire:click="excluir('{{ $s['id'] }}')" 
                                        wire:confirm="Confirmar exclusão deste subsistema?"
                                        type="button" 
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-red-400 hover:bg-red-950/30 border border-transparent hover:border-red-900/50 transition-colors cursor-pointer"
                                        title="Excluir Subsistema"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500 font-mono">
                                Nenhum subsistema operacional localizado com os filtros selecionados.
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
                        {{ $editandoId ? 'Editar Subsistema Operacional' : 'Cadastrar Novo Subsistema Operacional' }}
                    </h3>
                    <button wire:click="fecharModal" type="button" class="text-gray-400 hover:text-white p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="salvar" class="space-y-4">
                    <div>
                        <label class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-1">
                            Designação do Subsistema <span class="text-red-400">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="nome" 
                            placeholder="Ex: Motores de Combustão Principal, Sonar de Flanco" 
                            class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-sans"
                        />
                        @error('nome') <span class="text-[10px] text-red-400 font-mono block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-1">
                                Grande Área Temática <span class="text-red-400">*</span>
                            </label>
                            <select 
                                wire:model="area" 
                                class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono"
                            >
                                <option value="Propulsão & Geração">Propulsão & Geração</option>
                                <option value="Governo & Imersão">Governo & Imersão</option>
                                <option value="Eletricidade Naval">Eletricidade Naval</option>
                                <option value="Sensores & Sonar">Sensores & Sonar</option>
                                <option value="Armamento Submarino">Armamento Submarino</option>
                                <option value="Atmosfera & CAV">Atmosfera & CAV</option>
                                <option value="Comunicações">Comunicações</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-1">
                                Nível de Criticidade <span class="text-red-400">*</span>
                            </label>
                            <select 
                                wire:model="criticidade" 
                                class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono"
                            >
                                <option value="Crítica de Segurança">Crítica de Segurança</option>
                                <option value="Operacional Essencial">Operacional Essencial</option>
                                <option value="Tática">Tática</option>
                                <option value="Apoio Geral">Apoio Geral</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-1">
                            Status <span class="text-red-400">*</span>
                        </label>
                        <select 
                            wire:model="status" 
                            class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono"
                        >
                            <option value="Homologado">Homologado</option>
                            <option value="Em Revisão Técnica">Em Revisão Técnica</option>
                            <option value="Obsoleto">Obsoleto</option>
                        </select>
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


    <!-- Modal de Guia de Preenchimento: Sistemas & Subsistemas -->
    @if($modalGuiaAberto)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 backdrop-blur-md p-4">
            <div class="bg-[#0b1329] border border-blue-500/40 rounded-2xl w-full max-w-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-gradient-to-r from-blue-950/80 to-[#0a1226] border-b border-gray-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/20 border border-blue-400/30 flex items-center justify-center text-blue-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider font-mono">Guia de Apoio: Sistemas & Subsistemas</h3>
                            <p class="text-xs text-blue-400/80 font-mono">Padronização Técnica & Taxonomia Naval - ComForS</p>
                        </div>
                    </div>
                    <button wire:click="fecharGuia" class="text-gray-400 hover:text-white p-1 rounded-lg hover:bg-gray-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto space-y-4 text-xs font-mono text-gray-300">
                    <div class="p-3.5 rounded-xl bg-blue-950/20 border border-blue-800/30">
                        <span class="text-blue-300 font-bold block mb-1">TAXONOMIA OPERACIONAL</span>
                        Esta tabela cataloga os equipamentos e subsistemas técnicos dos submarinos para permitir filtros apurados de falhas, desgastes de ciclo de vida e soluções de engenharia naval.
                    </div>

                    <div class="space-y-3">
                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">1. Nome do Subsistema / Equipamento</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">Nome descritivo padronizado do conjunto funcional ou equipamento. Exemplos: <span class="text-blue-300">Motores de Combustão Principal (MCP)</span>, <span class="text-blue-300">Baterias de Propulsão</span>, <span class="text-blue-300">Sonar de Flanco (FAS)</span>, <span class="text-blue-300">Sistema de Esgoto de Emergência</span>.</p>
                        </div>

                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">2. Área Técnica / Sistema Principal</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">Macro-área de engenharia à qual o subsistema está subordinado: <span class="text-gray-200">Propulsão & Geração, Governo & Imersão, Eletricidade Naval, Sensores & Sonar, Armamento Submarino, Atmosfera & CAV, Comunicações</span>.</p>
                        </div>

                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">3. Criticidade Operacional</p>
                            <p class="text-gray-400 text-[11px] mt-0.5"><span class="text-red-400 font-semibold">Crítica de Segurança:</span> Falhas afetam a segurança de imersão, sobrevivência ou controle do submarino. <span class="text-amber-400 font-semibold">Operacional Essencial:</span> Afeta capacidade de combate ou cumprimento de missão primária. <span class="text-blue-400 font-semibold">Tática / Suporte:</span> Afeta redundâncias ou tarefas secundárias.</p>
                        </div>

                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">4. Status de Homologação</p>
                            <p class="text-gray-400 text-[11px] mt-0.5"><span class="text-green-400">Homologado:</span> Padrão em operação. <span class="text-amber-400">Em Avaliação Técnica:</span> Novo equipamento em testes de mar ou modernização.</p>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-3.5 bg-gray-950/80 border-t border-gray-800 flex items-center justify-end">
                    <button wire:click="fecharGuia" class="px-4 py-2 rounded-xl text-xs font-mono font-semibold bg-blue-600 hover:bg-blue-500 text-white transition-colors">
                        Entendido / Fechar Guia
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>