<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    // Listas dinâmicas que iniciam vazias conforme especificado
    public array $meios_navais = [];
    public array $sistemas = [];
    public array $comissoes = [];
    public array $rfcs_disponiveis = [];

    // Dados do formulário
    public string $submarino = '';
    public string $grau_sigilo = 'RESERVADO';
    public string $sistema = '';
    public string $comissao = '';
    public string $rfc_numero = '';
    public string $data_ocorrencia = '';
    public string $titulo = '';
    public string $relato = '';
    public string $recomendacao = '';
    public bool $salvo = false;

    // Modal de Guia de Apoio
    public bool $modalGuiaAberto = false;

    // Controle inline de adição de novo meio naval
    public bool $adicionando_meio = false;
    public string $novo_meio = '';

    // Controle inline de adição de novo subsistema
    public bool $adicionando_sistema = false;
    public string $novo_sistema = '';

    // Controle inline de adição de nova comissão
    public bool $adicionando_comissao = false;
    public string $nova_comissao = '';

    public function mount(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->temAcessoAoModulo('licoes_aprendidas')) {
            abort(403, 'Acesso não autorizado ao módulo de Lições Aprendidas.');
        }

        session(['modulo_ativo' => 'licoes_aprendidas']);
        $this->data_ocorrencia = date('Y-m-d');

        // Carrega das sessões de apoio (iniciam vazias se ainda não cadastradas)
        $this->meios_navais = session('licoes_meios_navais', []);
        $this->sistemas = session('licoes_sistemas', []);
        $this->comissoes = session('licoes_comissoes', []);

        // Exemplos preliminares de RFCs vinculados (futuro CRUD no SIPA-SUB)
        $this->rfcs_disponiveis = [
            ['id' => '1', 'numero' => 'RFC-2026/014', 'descricao' => 'RFC-2026/014 - Faina de Imersão e Teste de Snorquel S34'],
            ['id' => '2', 'numero' => 'RFC-2026/029', 'descricao' => 'RFC-2026/029 - Operação Tropicalex 2026 S40'],
            ['id' => '3', 'numero' => 'RFC-2026/033', 'descricao' => 'RFC-2026/033 - Patrulha Submersa Atlântico Sul S41'],
        ];
    }

    public function abrirGuia(): void
    {
        $this->modalGuiaAberto = true;
    }

    public function fecharGuia(): void
    {
        $this->modalGuiaAberto = false;
    }

    public function abrirNovoMeio(): void
    {
        $this->adicionando_meio = true;
        $this->novo_meio = '';
    }

    public function cancelarNovoMeio(): void
    {
        $this->adicionando_meio = false;
        $this->novo_meio = '';
    }

    public function adicionarMeio(): void
    {
        $this->validate([
            'novo_meio' => 'required|min:2|max:100',
        ], [
            'novo_meio.required' => 'Digite a identificação do novo meio naval ou OM.',
            'novo_meio.min' => 'A identificação deve ter pelo menos 2 caracteres.',
        ]);

        $item = trim($this->novo_meio);
        if (! in_array($item, $this->meios_navais)) {
            $this->meios_navais[] = $item;
            session(['licoes_meios_navais' => $this->meios_navais]);
        }

        $this->submarino = $item;
        $this->adicionando_meio = false;
        $this->novo_meio = '';
    }

    public function abrirNovoSistema(): void
    {
        $this->adicionando_sistema = true;
        $this->novo_sistema = '';
    }

    public function cancelarNovoSistema(): void
    {
        $this->adicionando_sistema = false;
        $this->novo_sistema = '';
    }

    public function adicionarSistema(): void
    {
        $this->validate([
            'novo_sistema' => 'required|min:2|max:150',
        ], [
            'novo_sistema.required' => 'Digite a descrição do novo subsistema.',
            'novo_sistema.min' => 'O subsistema deve ter pelo menos 2 caracteres.',
        ]);

        $item = trim($this->novo_sistema);
        if (! in_array($item, $this->sistemas)) {
            $this->sistemas[] = $item;
            session(['licoes_sistemas' => $this->sistemas]);
        }

        $this->sistema = $item;
        $this->adicionando_sistema = false;
        $this->novo_sistema = '';
    }

    public function abrirNovaComissao(): void
    {
        $this->adicionando_comissao = true;
        $this->nova_comissao = '';
    }

    public function cancelarNovaComissao(): void
    {
        $this->adicionando_comissao = false;
        $this->nova_comissao = '';
    }

    public function adicionarComissao(): void
    {
        $this->validate([
            'nova_comissao' => 'required|min:3|max:120',
        ], [
            'nova_comissao.required' => 'Digite o nome ou sigla da nova comissão.',
            'nova_comissao.min' => 'A comissão deve ter pelo menos 3 caracteres.',
        ]);

        $item = trim($this->nova_comissao);
        if (! in_array($item, $this->comissoes)) {
            $this->comissoes[] = $item;
            session(['licoes_comissoes' => $this->comissoes]);
        }

        $this->comissao = $item;
        $this->adicionando_comissao = false;
        $this->nova_comissao = '';
    }

    public function salvar(): void
    {
        $this->validate([
            'submarino' => 'required',
            'grau_sigilo' => 'required',
            'sistema' => 'required',
            'titulo' => 'required|min:5|max:255',
            'relato' => 'required|min:10',
            'recomendacao' => 'required|min:10',
        ], [
            'submarino.required' => 'Selecione ou adicione o meio naval correspondente.',
            'grau_sigilo.required' => 'Indique o grau de sigilo doutrinário.',
            'sistema.required' => 'Selecione ou adicione o subsistema operacional afetado.',
            'titulo.required' => 'Informe o título da lição aprendida.',
            'relato.required' => 'Descreva detalhadamente a ocorrência operacional ou técnica.',
            'recomendacao.required' => 'Apresente a lição doutrinária homologada para as tripulações.',
        ]);

        $this->salvo = true;
    }

    public function novaLicao(): void
    {
        $this->reset(['submarino', 'sistema', 'comissao', 'rfc_numero', 'titulo', 'relato', 'recomendacao', 'salvo']);
        $this->grau_sigilo = 'RESERVADO';
        $this->data_ocorrencia = date('Y-m-d');
    }
}; ?>

<div class="max-w-5xl mx-auto space-y-6 select-none">
    <!-- Banner de Construção & Homologação Doutrinária com Botão Guia -->
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
                        Cadastro de Lição
                    </span>
                </div>
                <p class="text-[11px] text-gray-400 font-mono mt-0.5 leading-snug">
                    Ambiente em validação operacional. Em breve persistência definitiva no banco corporativo centralizado do ComForS.
                </p>
            </div>
        </div>

        <button 
            type="button" 
            wire:click="abrirGuia"
            class="flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl text-xs font-mono font-semibold text-blue-300 bg-gray-900/90 hover:bg-gray-800 border border-blue-500/30 hover:border-blue-400/60 shadow-lg shadow-black/40 transition-all duration-200 cursor-pointer self-start sm:self-auto shrink-0"
        >
            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Guia de Preenchimento</span>
        </button>
    </div>

    <!-- Mensagem de Sucesso (Se Salvo) -->
    @if ($salvo)
        <div class="p-6 rounded-2xl bg-gradient-to-r from-emerald-950/80 to-[#041a12] border border-emerald-500/40 shadow-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-emerald-300 uppercase font-mono">Lição Aprendida Registrada com Sucesso!</h3>
                    <p class="text-xs text-gray-300 mt-0.5">
                        Ocorrência homologada preliminarmente sob supervisão do ComForS / CLTI. Protocolo gerado com sucesso.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button 
                    wire:click="novaLicao" 
                    type="button" 
                    class="px-3.5 py-1.5 rounded-xl text-xs font-mono font-bold text-white bg-emerald-600 hover:bg-emerald-500 transition-colors cursor-pointer"
                >
                    Cadastrar Outra
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
        <!-- 1ª Linha: Meio Naval, Grau de Sigilo, Subsistema -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- 1. Meio Naval / Submarino (Dinâmico com Adicionar Novo) -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="submarino" class="text-xs font-mono uppercase text-gray-300 font-semibold">
                        Meio Naval / OM <span class="text-red-400">*</span>
                    </label>
                    @if (! $adicionando_meio)
                        <button 
                            type="button" 
                            wire:click="abrirNovoMeio" 
                            class="inline-flex items-center gap-1 text-[11px] font-mono text-yellow-400 hover:text-yellow-300 hover:underline cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>+ Adicionar Novo</span>
                        </button>
                    @endif
                </div>

                @if ($adicionando_meio)
                    <div class="p-3 rounded-xl bg-blue-950/40 border border-blue-800/80 space-y-2.5">
                        <div class="text-[11px] font-mono text-blue-300 font-semibold flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                            <span>Novo Meio Naval</span>
                        </div>
                        <input 
                            type="text" 
                            wire:model="novo_meio" 
                            placeholder="Ex: S34 - Tikuna, BACS, S40..." 
                            class="w-full bg-[#02050d] border border-gray-700 rounded-lg px-3 py-1.5 text-xs text-white focus:outline-none focus:border-blue-500 font-mono"
                        />
                        @error('novo_meio') <span class="text-[10px] text-red-400 font-mono block">{{ $message }}</span> @enderror
                        <div class="flex items-center gap-2">
                            <button 
                                type="button" 
                                wire:click="adicionarMeio" 
                                class="px-2.5 py-1 rounded-lg text-[11px] font-mono font-bold bg-blue-600 hover:bg-blue-500 text-white cursor-pointer"
                            >
                                Adicionar
                            </button>
                            <button 
                                type="button" 
                                wire:click="cancelarNovoMeio" 
                                class="px-2.5 py-1 rounded-lg text-[11px] font-mono text-gray-400 hover:text-white bg-gray-900 border border-gray-700 cursor-pointer"
                            >
                                Cancelar
                            </button>
                        </div>
                    </div>
                @else
                    <select 
                        id="submarino" 
                        wire:model="submarino" 
                        class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono transition-colors"
                    >
                        @if (empty($meios_navais))
                            <option value="">Nenhum cadastrado. Clique em "+ Adicionar Novo"</option>
                        @else
                            <option value="">Selecione o Meio Naval / OM</option>
                            @foreach ($meios_navais as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        @endif
                    </select>
                @endif
                @error('submarino') <span class="text-[11px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- 2. Grau de Sigilo -->
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
                @error('grau_sigilo') <span class="text-[11px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- 3. Subsistema Afetado (Dinâmico com Adicionar Novo) -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="sistema" class="text-xs font-mono uppercase text-gray-300 font-semibold">
                        Subsistema Afetado <span class="text-red-400">*</span>
                    </label>
                    @if (! $adicionando_sistema)
                        <button 
                            type="button" 
                            wire:click="abrirNovoSistema" 
                            class="inline-flex items-center gap-1 text-[11px] font-mono text-yellow-400 hover:text-yellow-300 hover:underline cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>+ Adicionar Novo</span>
                        </button>
                    @endif
                </div>

                @if ($adicionando_sistema)
                    <div class="p-3 rounded-xl bg-blue-950/40 border border-blue-800/80 space-y-2.5">
                        <div class="text-[11px] font-mono text-blue-300 font-semibold flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                            <span>Novo Subsistema</span>
                        </div>
                        <input 
                            type="text" 
                            wire:model="novo_sistema" 
                            placeholder="Ex: MCP, Baterias, Lemes, Sonar..." 
                            class="w-full bg-[#02050d] border border-gray-700 rounded-lg px-3 py-1.5 text-xs text-white focus:outline-none focus:border-blue-500 font-mono"
                        />
                        @error('novo_sistema') <span class="text-[10px] text-red-400 font-mono block">{{ $message }}</span> @enderror
                        <div class="flex items-center gap-2">
                            <button 
                                type="button" 
                                wire:click="adicionarSistema" 
                                class="px-2.5 py-1 rounded-lg text-[11px] font-mono font-bold bg-blue-600 hover:bg-blue-500 text-white cursor-pointer"
                            >
                                Adicionar
                            </button>
                            <button 
                                type="button" 
                                wire:click="cancelarNovoSistema" 
                                class="px-2.5 py-1 rounded-lg text-[11px] font-mono text-gray-400 hover:text-white bg-gray-900 border border-gray-700 cursor-pointer"
                            >
                                Cancelar
                            </button>
                        </div>
                    </div>
                @else
                    <select 
                        id="sistema" 
                        wire:model="sistema" 
                        class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono transition-colors"
                    >
                        @if (empty($sistemas))
                            <option value="">Nenhum cadastrado. Clique em "+ Adicionar Novo"</option>
                        @else
                            <option value="">Selecione o Subsistema</option>
                            @foreach ($sistemas as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        @endif
                    </select>
                @endif
                @error('sistema') <span class="text-[11px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- 2ª Linha: Comissão (Dinâmica), Relatório Final de Comissão (RFC - SIPA-SUB), Data -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- 4. Comissão Operacional (Dinâmico com Adicionar Novo) -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="comissao" class="text-xs font-mono uppercase text-gray-300 font-semibold">
                        Comissão Operacional
                    </label>
                    @if (! $adicionando_comissao)
                        <button 
                            type="button" 
                            wire:click="abrirNovaComissao" 
                            class="inline-flex items-center gap-1 text-[11px] font-mono text-yellow-400 hover:text-yellow-300 hover:underline cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>+ Adicionar Nova</span>
                        </button>
                    @endif
                </div>

                @if ($adicionando_comissao)
                    <div class="p-3 rounded-xl bg-blue-950/40 border border-blue-800/80 space-y-2.5">
                        <div class="text-[11px] font-mono text-blue-300 font-semibold flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                            <span>Nova Comissão</span>
                        </div>
                        <input 
                            type="text" 
                            wire:model="nova_comissao" 
                            placeholder="Ex: Operação Tropicalex, Adestramento S34..." 
                            class="w-full bg-[#02050d] border border-gray-700 rounded-lg px-3 py-1.5 text-xs text-white focus:outline-none focus:border-blue-500 font-mono"
                        />
                        @error('nova_comissao') <span class="text-[10px] text-red-400 font-mono block">{{ $message }}</span> @enderror
                        <div class="flex items-center gap-2">
                            <button 
                                type="button" 
                                wire:click="adicionarComissao" 
                                class="px-2.5 py-1 rounded-lg text-[11px] font-mono font-bold bg-blue-600 hover:bg-blue-500 text-white cursor-pointer"
                            >
                                Adicionar
                            </button>
                            <button 
                                type="button" 
                                wire:click="cancelarNovaComissao" 
                                class="px-2.5 py-1 rounded-lg text-[11px] font-mono text-gray-400 hover:text-white bg-gray-900 border border-gray-700 cursor-pointer"
                            >
                                Cancelar
                            </button>
                        </div>
                    </div>
                @else
                    <select 
                        id="comissao" 
                        wire:model="comissao" 
                        class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono transition-colors"
                    >
                        <option value="">Não vinculada / Selecione uma Comissão</option>
                        @foreach ($comissoes as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                @endif
                <span class="text-[10px] text-gray-500 font-mono mt-1 block">Faina ou missão naval onde o fato ocorreu</span>
            </div>

            <!-- 5. Relatório Final de Comissão (RFC) - Futuro SIPA-SUB -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="rfc_numero" class="text-xs font-mono uppercase text-gray-300 font-semibold flex items-center gap-1.5">
                        <span>Vincular RFC</span>
                        <span class="text-[10px] text-amber-400 bg-amber-950/60 border border-amber-700/40 px-1.5 py-0.2 rounded font-normal">SIPA-SUB</span>
                    </label>
                </div>
                <select 
                    id="rfc_numero" 
                    wire:model="rfc_numero" 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono transition-colors"
                >
                    <option value="">Nenhum RFC vinculado</option>
                    @foreach ($rfcs_disponiveis as $r)
                        <option value="{{ $r['numero'] }}">{{ $r['descricao'] }}</option>
                    @endforeach
                </select>
                <span class="text-[10px] text-amber-400/80 font-mono mt-1 block">
                    * Será integrado ao CRUD de Relatórios Finais no SIPA-SUB
                </span>
            </div>

            <!-- 6. Data da Ocorrência -->
            <div>
                <label for="data_ocorrencia" class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-2">
                    Data da Ocorrência
                </label>
                <input 
                    type="date" 
                    id="data_ocorrencia" 
                    wire:model="data_ocorrencia" 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono transition-colors"
                />
            </div>
        </div>

        <!-- 3ª Linha: Título -->
        <div>
            <label for="titulo" class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-2">
                Título da Lição Aprendida <span class="text-red-400">*</span>
            </label>
            <input 
                type="text" 
                id="titulo" 
                wire:model="titulo" 
                placeholder="Ex: Procedimento de estanqueidade de válvulas durante faina de snorquel em mar grosso"
                class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono transition-colors"
            />
            @error('titulo') <span class="text-[11px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- 4ª Linha: Relato Circunstanciado -->
        <div>
            <label for="relato" class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-2">
                Relato Circunstanciado do Evento <span class="text-red-400">*</span>
            </label>
            <textarea 
                id="relato" 
                wire:model="relato" 
                rows="4" 
                placeholder="Descreva de forma clara e objetiva o que aconteceu, quais sistemas foram afetados, a cota de imersão, estado de prontidão e ação imediata adotada pela tripulação..."
                class="w-full bg-[#02050d] border border-gray-800 rounded-xl p-3.5 text-xs text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono transition-colors leading-relaxed"
            ></textarea>
            @error('relato') <span class="text-[11px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- 5ª Linha: Recomendação / Lição Homologada -->
        <div>
            <label for="recomendacao" class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-2">
                Lição Doutrinária / Recomendação Técnica <span class="text-red-400">*</span>
            </label>
            <textarea 
                id="recomendacao" 
                wire:model="recomendacao" 
                rows="3" 
                placeholder="Indique a recomendação corretiva, modificação de procedimento operacional padrão (POP), diretriz de segurança ou necessidade de revisão pelo CIAMA/CASNAV..."
                class="w-full bg-[#02050d] border border-gray-800 rounded-xl p-3.5 text-xs text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono transition-colors leading-relaxed"
            ></textarea>
            @error('recomendacao') <span class="text-[11px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Botões de Ação do Formulário -->
        <div class="pt-4 border-t border-gray-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="text-[11px] font-mono text-gray-400">
                <span class="text-yellow-400 font-bold">* Campos obrigatórios</span> • Submetido sob autenticação do NIP: <span class="text-white font-bold">{{ auth()->user()->nip }}</span>
            </div>

            <div class="flex items-center gap-3">
                <a 
                    href="{{ route('licoes.dashboard') }}" 
                    wire:navigate 
                    class="px-4 py-2.5 rounded-xl text-xs font-mono font-bold text-gray-400 hover:text-white bg-gray-900 border border-gray-800 hover:border-gray-700 transition-colors cursor-pointer"
                >
                    Cancelar
                </a>
                <button 
                    type="submit" 
                    class="px-6 py-2.5 rounded-xl text-xs font-mono font-bold text-white bg-gradient-to-r from-blue-700 to-blue-600 hover:from-blue-600 hover:to-blue-500 shadow-lg shadow-blue-900/40 border border-blue-500/40 transition-all cursor-pointer"
                >
                    Homologar Registro Preliminar
                </button>
            </div>
        </div>
    </form>

    <!-- Modal de Guia de Preenchimento da Lição Aprendida -->
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
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider font-mono">Guia de Preenchimento: Lições Aprendidas</h3>
                            <p class="text-xs text-blue-400/80 font-mono">Diretrizes do Estado-Maior do ComForS</p>
                        </div>
                    </div>
                    <button wire:click="fecharGuia" class="text-gray-400 hover:text-white p-1 rounded-lg hover:bg-gray-800 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto space-y-4 text-xs font-mono text-gray-300">
                    <div class="p-3.5 rounded-xl bg-blue-950/20 border border-blue-800/30">
                        <span class="text-blue-300 font-bold block mb-1">OBJETIVO DOUTRINÁRIO</span>
                        O registro de Lições Aprendidas visa prevenir a recorrência de incidentes operacionais, aprimorar a segurança de imersão e aperfeiçoar a doutrina tática dos submarinos da Marinha do Brasil.
                    </div>

                    <div class="space-y-3">
                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">1. Meio Naval / OM</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">Submarino ou Organização Militar em que a lição foi verificada. Se o meio não estiver na lista, utilize <span class="text-yellow-400 font-bold">+ Adicionar Novo</span> para cadastrá-lo imediatamente.</p>
                        </div>

                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">2. Comissão Operacional & RFC (SIPA-SUB)</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">Indica a faina ou viagem onde ocorreu a observação. O campo de <span class="text-amber-400 font-bold">RFC</span> vincula a ocorrência ao Relatório Final de Comissão que será gerenciado pelo módulo SIPA-SUB.</p>
                        </div>

                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">3. Subsistema Afetado</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">Componente técnico envolvido (Propulsão MCP/MEP, Lastro, Sonar, Baterias). Caso necessário, adicione novos componentes sob demanda.</p>
                        </div>

                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">4. Grau de Sigilo</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">Classificação doutrinária de acesso da informação: <span class="text-emerald-400">Ostensivo</span>, <span class="text-blue-400">Reservado</span>, <span class="text-yellow-400">Confidencial</span> ou <span class="text-red-400">Secreto</span>.</p>
                        </div>

                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">5. Relato Circunstanciado & Recomendação Técnica</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">O relato deve conter a cronologia dos fatos com precisão técnica. A recomendação deve orientar correções em procedimentos operacionais, manuais do fabricante ou inspeções periódicas.</p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-3.5 bg-gray-950/80 border-t border-gray-800 flex items-center justify-end">
                    <button wire:click="fecharGuia" class="px-4 py-2 rounded-xl text-xs font-mono font-semibold bg-blue-600 hover:bg-blue-500 text-white transition-colors cursor-pointer">
                        Entendido / Fechar Guia
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
