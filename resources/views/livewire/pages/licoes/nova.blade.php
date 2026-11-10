<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    // Listas dinâmicas que iniciam vazias conforme especificado
    public array $meios_navais = [];
    public array $sistemas = [];

    // Dados do formulário
    public string $submarino = '';
    public string $grau_sigilo = 'RESERVADO';
    public string $sistema = '';
    public string $data_ocorrencia = '';
    public string $titulo = '';
    public string $relato = '';
    public string $recomendacao = '';
    public bool $salvo = false;

    // Controle inline de adição de novo meio naval
    public bool $adicionando_meio = false;
    public string $novo_meio = '';

    // Controle inline de adição de novo subsistema
    public bool $adicionando_sistema = false;
    public string $novo_sistema = '';

    public function mount(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->temAcessoAoModulo('licoes_aprendidas')) {
            abort(403, 'Acesso não autorizado ao módulo de Lições Aprendidas.');
        }

        session(['modulo_ativo' => 'licoes_aprendidas']);
        $this->data_ocorrencia = date('Y-m-d');

        // Carrega da sessão se houver inclusões prévias na navegação, senão inicia estritamente vazio
        $this->meios_navais = session('licoes_meios_navais', []);
        $this->sistemas = session('licoes_sistemas', []);
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
        $this->reset(['submarino', 'sistema', 'titulo', 'relato', 'recomendacao', 'salvo']);
        $this->grau_sigilo = 'RESERVADO';
        $this->data_ocorrencia = date('Y-m-d');
    }
}; ?>

<div class="max-w-5xl mx-auto space-y-6 select-none">

    <!-- Card de Cabeçalho Oficial no Padrão Argon Naval -->
    <div class="rounded-2xl p-6 sm:p-8 bg-gradient-to-r from-[#061122] via-[#050e1b] to-[#030813] border border-blue-900/40 shadow-xl relative overflow-hidden">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-950/70 border border-blue-600/40 flex items-center justify-center shrink-0 shadow-lg">
                    <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-mono uppercase tracking-wider bg-blue-950/80 text-blue-300 border border-blue-800/60 mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                        <span>ComForS • Registro Tático & Operacional</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold text-white tracking-wide uppercase" style="font-family: 'Oswald', sans-serif;">
                        Cadastrar Nova Lição Aprendida
                    </h1>
                    <p class="text-xs text-gray-400 font-mono mt-0.5">
                        Preservação do conhecimento doutrinário para as tripulações da Força de Submarinos
                    </p>
                </div>
            </div>

            <!-- Botão Retornar ao Dashboard -->
            <a 
                href="{{ route('licoes.dashboard') }}" 
                wire:navigate 
                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-xs font-mono font-bold text-gray-300 hover:text-white bg-gray-900/80 hover:bg-gray-800 border border-gray-700/80 transition-all shrink-0 cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Voltar ao Dashboard</span>
            </a>
        </div>
    </div>

    <!-- Mensagem de Sucesso (Se Salvo) -->
    @if ($salvo)
        <div class="p-6 rounded-2xl bg-gradient-to-r from-emerald-950/80 to-[#041a12] border border-emerald-500/40 shadow-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-emerald-300 uppercase font-mono">Lição Aprendida Registrada com Sucesso!</h3>
                    <p class="text-xs text-gray-300 mt-0.5">
                        Ocorrência homologada preliminarmente sob supervisão do ComForS / CLTI.
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
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
                    Classificação de Sigilo <span class="text-red-400">*</span>
                </label>
                <select 
                    id="grau_sigilo" 
                    wire:model="grau_sigilo" 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono transition-colors"
                >
                    <option value="OSTENSIVO">OSTENSIVO (Público Naval)</option>
                    <option value="RESERVADO">RESERVADO (Uso Interno)</option>
                    <option value="CONFIDENCIAL">CONFIDENCIAL (Doutrinário ComForS)</option>
                    <option value="SECRETO">SECRETO (Operações Sensíveis)</option>
                </select>
                @error('grau_sigilo') <span class="text-[11px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- 3. Data da Ocorrência / Faina -->
            <div>
                <label for="data_ocorrencia" class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-2">
                    Data da Faina / Evento <span class="text-red-400">*</span>
                </label>
                <input 
                    type="date" 
                    id="data_ocorrencia" 
                    wire:model="data_ocorrencia" 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono transition-colors"
                />
            </div>
        </div>

        <!-- 4. Sistema ou Subsistema Naval (Dinâmico com Adicionar Novo) -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <label for="sistema" class="text-xs font-mono uppercase text-gray-300 font-semibold">
                    Sistema ou Subsistema Operacional Afetado <span class="text-red-400">*</span>
                </label>
                @if (! $adicionando_sistema)
                    <button 
                        type="button" 
                        wire:click="abrirNovoSistema" 
                        class="inline-flex items-center gap-1 text-[11px] font-mono text-yellow-400 hover:text-yellow-300 hover:underline cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>+ Adicionar Novo</span>
                    </button>
                @endif
            </div>

            @if ($adicionando_sistema)
                <div class="p-3 rounded-xl bg-blue-950/40 border border-blue-800/80 space-y-2.5">
                    <div class="text-[11px] font-mono text-blue-300 font-semibold flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                        <span>Novo Subsistema Operacional</span>
                    </div>
                    <input 
                        type="text" 
                        wire:model="novo_sistema" 
                        placeholder="Ex: Propulsão e Motores, Sistema de Governo e Lemes, Sonar Passivo..." 
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
                        <option value="">Nenhum subsistema cadastrado. Clique em "+ Adicionar Novo"</option>
                    @else
                        <option value="">Selecione o Subsistema Operacional</option>
                        @foreach ($sistemas as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    @endif
                </select>
            @endif
            @error('sistema') <span class="text-[11px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- 5. Título da Lição Aprendida -->
        <div>
            <label for="titulo" class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-2">
                Título Síntese da Lição Aprendida <span class="text-red-400">*</span>
            </label>
            <input 
                type="text" 
                id="titulo" 
                wire:model="titulo" 
                placeholder="Ex.: Falha de vedação na válvula de exaustão durante manobra de snorquel em cota periscópica" 
                class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-sans transition-colors"
            />
            @error('titulo') <span class="text-[11px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- 6. Relato Detalhado da Ocorrência -->
        <div>
            <label for="relato" class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-2">
                Relato Detalhado da Ocorrência / Faina <span class="text-red-400">*</span>
            </label>
            <textarea 
                id="relato" 
                wire:model="relato" 
                rows="4" 
                placeholder="Descreva a sequência de fatos, sintomas observados pelos operadores, condições de mar, velocidade, cota de imersão e ação de pronta resposta adotada..."
                class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-sans leading-relaxed transition-colors"
            ></textarea>
            @error('relato') <span class="text-[11px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- 7. Recomendação Doutrinária Homologada -->
        <div>
            <label for="recomendacao" class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-2">
                Recomendação Doutrinária Homologada / Lição para Tripulações <span class="text-red-400">*</span>
            </label>
            <textarea 
                id="recomendacao" 
                wire:model="recomendacao" 
                rows="3" 
                placeholder="Indique a recomendação corretiva, modificação de check-list operacional ou medida de prevenção a ser difundida para todas as tripulações..."
                class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-sans leading-relaxed transition-colors"
            ></textarea>
            @error('recomendacao') <span class="text-[11px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Barra Inferior de Ações -->
        <div class="pt-4 border-t border-gray-800/80 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-xs font-mono text-gray-500 flex items-center gap-2">
                <span>Militar Relator:</span>
                <span class="text-yellow-400 font-bold">{{ auth()->user()->name }}</span>
                <span>(NIP: {{ auth()->user()->nip }})</span>
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
                    <span>Salvar Lição Aprendida</span>
                </button>
            </div>
        </div>

    </form>

</div>
