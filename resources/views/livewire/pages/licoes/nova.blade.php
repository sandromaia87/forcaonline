<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public string $submarino = '';
    public string $grau_sigilo = 'RESERVADO';
    public string $sistema = '';
    public string $data_ocorrencia = '';
    public string $titulo = '';
    public string $relato = '';
    public string $recomendacao = '';
    public bool $salvo = false;

    public function mount(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->temAcessoAoModulo('licoes_aprendidas')) {
            abort(403, 'Acesso não autorizado ao módulo de Lições Aprendidas.');
        }

        session(['modulo_ativo' => 'licoes_aprendidas']);
        $this->data_ocorrencia = date('Y-m-d');
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
            'submarino.required' => 'Selecione o submarino ou OM correspondente.',
            'grau_sigilo.required' => 'Indique o grau de sigilo doutrinário.',
            'sistema.required' => 'Selecione o subsistema operacional afetado.',
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
            <!-- 1. Submarino / Meio Naval -->
            <div>
                <label for="submarino" class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-2">
                    Submarino / Meio Naval <span class="text-red-400">*</span>
                </label>
                <select 
                    id="submarino" 
                    wire:model="submarino" 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono transition-colors"
                >
                    <option value="">Selecione o Submarino / OM</option>
                    <optgroup label="Classe Tupi (IKL 209/1400)">
                        <option value="S30">S30 - Submarino Tupi</option>
                    </optgroup>
                    <optgroup label="Classe Tikuna (IKL Mod)">
                        <option value="S34">S34 - Submarino Tikuna</option>
                    </optgroup>
                    <optgroup label="Classe Riachuelo (Scorpène / PROSUB)">
                        <option value="S40">S40 - Submarino Riachuelo</option>
                        <option value="S41">S41 - Submarino Humaitá</option>
                        <option value="S42">S42 - Submarino Tonelero</option>
                        <option value="S43">S43 - Submarino Angostura</option>
                    </optgroup>
                    <optgroup label="Propulsão Nuclear">
                        <option value="S44">S44 - Submarino Álvaro Alberto (SN-BR)</option>
                    </optgroup>
                    <optgroup label="Organizações Militares de Apoio">
                        <option value="BACS">Base Almirante Castro e Silva (BACS)</option>
                        <option value="CIAMA">Centro de Instrução Almirante Átilla Monteiro Aché (CIAMA)</option>
                        <option value="CLTI">Centro de Liderança e TI (CLTI)</option>
                    </optgroup>
                </select>
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

        <!-- 4. Sistema ou Subsistema Naval -->
        <div>
            <label for="sistema" class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-2">
                Sistema ou Subsistema Operacional Afetado <span class="text-red-400">*</span>
            </label>
            <select 
                id="sistema" 
                wire:model="sistema" 
                class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono transition-colors"
            >
                <option value="">Selecione o Subsistema</option>
                <option value="propulsao">Propulsão, MCP e Motores Elétricos de Propulsão (MEP)</option>
                <option value="imersao">Sistema de Imersão, Tanques de Lastro e Reguladores de Governo</option>
                <option value="eletrico">Geração e Armazenamento Elétrico (Baterias de Propulsão / Quadros de Distribuição)</option>
                <option value="sensores">Sensores Táticos, Sonar Passivo/Ativo, MAE e Periscópios</option>
                <option value="armamento">Sistema de Armamento e Tubos de Lançamento de Torpedos (F21 / Mk 48)</option>
                <option value="atmosfera">Atmosfera Confinada, Oxi-geradores, Purificação e Controle de Avarias (CAV)</option>
                <option value="comunicacoes">Telecomunicações Submarinas (VLF/ELF, Satélite, Rádio HF)</option>
                <option value="salvamento">Faina de Socorro, Escape e Salvamento Submarino</option>
                <option value="procedimentos">Procedimentos Operacionais e Doutrina de Quarto de Manobra</option>
            </select>
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
