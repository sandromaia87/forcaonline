<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    // Listas dinâmicas
    public array  = [];
    public array  = [];
    public array  = [];
    
    // Dados do formulário da lição
    public string  = '';
    public string  = 'RESERVADO';
    public string  = '';
    public string  = '';
    public string  = '';
    public string  = '';
    public string  = '';
    public string  = '';

    public bool  = false;
    public bool  = false;

    // Left Sidebar Modal (Drawer Lateral de Apoio)
    public bool  = false;
    public string  = ''; // 'meio_naval' | 'subsistema' | 'comissao'

    // Campos do Drawer: Meio Naval
    public string  = '';
    public string  = '';
    public string  = 'Submarino';
    public string  = 'Classe Riachuelo (S-BR)';
    public string  = 'Operacional';

    // Campos do Drawer: Subsistema
    public string  = '';
    public string  = 'Propulsão & Geração';
    public string  = 'Crítica de Segurança';
    public string  = 'Homologado';

    // Campos do Drawer: Comissão
    public string  = '';
    public string  = '';
    public string  = 'Adestramento de Imersão';
    public string  = '';
    public string  = 'Concluída';

    public function mount(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->temAcessoAoModulo('licoes_aprendidas')) {
            abort(403, 'Acesso não autorizado ao módulo de Lições Aprendidas.');
        }

        session(['modulo_ativo' => 'licoes_aprendidas']);
        $this->data_ocorrencia = date('Y-m-d');

        // Carrega das tabelas de apoio do CRUD e das inserções dinâmicas
        $crudMeios = array_map(fn($m) => is_array($m) ? ($m['indicativo'] . ' - ' . $m['nome']) : $m, session('crud_meios_navais', []));
        $sessionMeios = session('licoes_meios_navais', []);
        $this->meios_navais = array_values(array_unique(array_filter(array_merge($crudMeios, $sessionMeios))));

        $crudSistemas = array_map(fn($s) => is_array($s) ? $s['nome'] : $s, session('crud_subsistemas', []));
        $sessionSistemas = session('licoes_sistemas', []);
        $this->sistemas = array_values(array_unique(array_filter(array_merge($crudSistemas, $sessionSistemas))));

        $crudComissoes = array_map(fn($c) => is_array($c) ? ($c['codigo'] . ' - ' . $c['nome']) : $c, session('crud_comissoes', []));
        $sessionComissoes = session('licoes_comissoes', []);
        $this->comissoes = array_values(array_unique(array_filter(array_merge($crudComissoes, $sessionComissoes))));
    }

    public function abrirGuia(): void
    {
        $this->modalGuiaAberto = true;
    }

    public function fecharGuia(): void
    {
        $this->modalGuiaAberto = false;
    }

    // Gerenciamento do Left Sidebar Modal
    public function abrirDrawer(string $tipo): void
    {
        $this->drawerTipo = $tipo;
        $this->drawerAberto = true;
        $this->resetValidation();

        if ($tipo === 'meio_naval') {
            $this->drawerMeioIndicativo = '';
            $this->drawerMeioNome = '';
            $this->drawerMeioTipo = 'Submarino';
            $this->drawerMeioClasse = 'Classe Riachuelo (S-BR)';
            $this->drawerMeioStatus = 'Operacional';
        } elseif ($tipo === 'subsistema') {
            $this->drawerSisNome = '';
            $this->drawerSisArea = 'Propulsão & Geração';
            $this->drawerSisCriticidade = 'Crítica de Segurança';
            $this->drawerSisStatus = 'Homologado';
        } elseif ($tipo === 'comissao') {
            $this->drawerComCodigo = '';
            $this->drawerComNome = '';
            $this->drawerComTipo = 'Adestramento de Imersão';
            $this->drawerComAno = date('Y');
            $this->drawerComStatus = 'Concluída';
        }
    }

    public function fecharDrawer(): void
    {
        $this->drawerAberto = false;
        $this->drawerTipo = '';
        $this->resetValidation();
    }

    public function salvarDrawerMeio(): void
    {
        $this->validate([
            'drawerMeioIndicativo' => 'required|min:2|max:20',
            'drawerMeioNome' => 'required|min:3|max:100',
        ], [
            'drawerMeioIndicativo.required' => 'O indicativo naval é obrigatório (ex: S34, BACS).',
            'drawerMeioNome.required' => 'O nome do meio naval ou OM é obrigatório.',
        ]);

        $ind = strtoupper(trim($this->drawerMeioIndicativo));
        $nom = trim($this->drawerMeioNome);
        $label = "{$ind} - {$nom}";

        if (! in_array($label, $this->meios_navais)) {
            $this->meios_navais[] = $label;
            session(['licoes_meios_navais' => $this->meios_navais]);
        }

        $crud = session('crud_meios_navais', []);
        $crud[] = [
            'id' => (string) (count($crud) + 1),
            'indicativo' => $ind,
            'nome' => $nom,
            'tipo' => $this->drawerMeioTipo,
            'classe' => $this->drawerMeioClasse,
            'status' => $this->drawerMeioStatus,
        ];
        session(['crud_meios_navais' => $crud]);

        $this->submarino = $label;
        $this->fecharDrawer();
    }

    public function salvarDrawerSistema(): void
    {
        $this->validate([
            'drawerSisNome' => 'required|min:3|max:100',
        ], [
            'drawerSisNome.required' => 'O nome do subsistema operacional é obrigatório.',
        ]);

        $nom = trim($this->drawerSisNome);
        if (! in_array($nom, $this->sistemas)) {
            $this->sistemas[] = $nom;
            session(['licoes_sistemas' => $this->sistemas]);
        }

        $crud = session('crud_subsistemas', []);
        $crud[] = [
            'id' => (string) (count($crud) + 1),
            'nome' => $nom,
            'area' => $this->drawerSisArea,
            'criticidade' => $this->drawerSisCriticidade,
            'status' => $this->drawerSisStatus,
        ];
        session(['crud_subsistemas' => $crud]);

        $this->sistema = $nom;
        $this->fecharDrawer();
    }

    public function salvarDrawerComissao(): void
    {
        $this->validate([
            'drawerComCodigo' => 'required|min:2|max:30',
            'drawerComNome' => 'required|min:3|max:100',
        ], [
            'drawerComCodigo.required' => 'O código da comissão é obrigatório (ex: TROPICALEX-2026).',
            'drawerComNome.required' => 'O nome da comissão ou faina é obrigatório.',
        ]);

        $cod = strtoupper(trim($this->drawerComCodigo));
        $nom = trim($this->drawerComNome);
        $label = "{$cod} - {$nom}";

        if (! in_array($label, $this->comissoes)) {
            $this->comissoes[] = $label;
            session(['licoes_comissoes' => $this->comissoes]);
        }

        $crud = session('crud_comissoes', []);
        $crud[] = [
            'id' => (string) (count($crud) + 1),
            'codigo' => $cod,
            'nome' => $nom,
            'tipo' => $this->drawerComTipo,
            'ano' => $this->drawerComAno ?: date('Y'),
            'status' => $this->drawerComStatus,
        ];
        session(['crud_comissoes' => $crud]);

        $this->comissao = $label;
        $this->fecharDrawer();
    }

    public function salvar(): void
    {
        $this->validate([
            'submarino' => 'required',
            'grau_sigilo' => 'required|in:OSTENSIVO,RESERVADO,CONFIDENCIAL,SECRETO',
            'sistema' => 'required',
            'titulo' => 'required|min:5|max:255',
            'relato' => 'required|min:10',
            'recomendacao' => 'required|min:10',
        ], [
            'submarino.required' => 'Selecione ou cadastre o meio naval / OM.',
            'sistema.required' => 'Selecione ou cadastre o subsistema afetado.',
            'titulo.required' => 'Informe o título da ocorrência (mínimo 5 caracteres).',
            'relato.required' => 'Descreva detalhadamente o relato circunstanciado.',
            'recomendacao.required' => 'Indique a lição técnica homologada recomendada.',
        ]);

        $ano = $this->data_ocorrencia ? date('Y', strtotime($this->data_ocorrencia)) : date('Y');
        $totalExistentes = count(session('licoes_aprendidas_registros', []));
        $seq = str_pad((string)($totalExistentes + 1), 4, '0', STR_PAD_LEFT);
        $protocolo = "LA-{\Cardano_ano}-{$seq}";
        $protocolo = "LA-{\Cardano_ano}-{$seq}";
        $protocolo = "LA-{\Cardano_ano}-{$seq}";
        $protocolo = "LA-{$ano}-{$seq}";

        $novaLicao = [
            'id' => (string) time(),
            'protocolo' => $protocolo,
            'submarino' => $this->submarino,
            'classe' => 'Meio Operacional ComForS',
            'sistema' => $this->sistema,
            'comissao' => $this->comissao,
            'titulo' => trim($this->titulo),
            'data' => $this->data_ocorrencia ? date('d/m/Y', strtotime($this->data_ocorrencia)) : date('d/m/Y'),
            'data_ocorrencia' => $this->data_ocorrencia ?: date('Y-m-d'),
            'autor' => auth()->user()->graduacao . ' ' . auth()->user()->name,
            'nip' => auth()->user()->nip,
            'classificacao' => $this->grau_sigilo,
            'status' => 'Homologada',
            'relato' => trim($this->relato),
            'recomendacao' => trim($this->recomendacao),
            'anexos_count' => 0,
        ];

        $existentes = session('licoes_aprendidas_registros', []);
        array_unshift($existentes, $novaLicao);
        session(['licoes_aprendidas_registros' => $existentes]);

        $this->salvo = true;
    }

    public function resetForm(): void
    {
        $this->reset(['submarino', 'sistema', 'comissao', 'titulo', 'relato', 'recomendacao']);
        $this->grau_sigilo = 'RESERVADO';
        $this->data_ocorrencia = date('Y-m-d');
        $this->salvo = false;
        $this->resetValidation();
    }
}; ?>

<div class="space-y-6 select-none w-full">
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
            class="flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl text-xs font-mono font-semibold text-blue-300 bg-gray-900/90 hover:bg-gray-800 border border-blue-500/30 hover:border-blue-400/60 shadow-lg shadow-black/40 transition-all cursor-pointer self-start sm:self-auto shrink-0"
        >
            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Guia de Preenchimento</span>
        </button>
    </div>

    <!-- Alerta de Sucesso -->
    @if ()
        <div class="p-6 rounded-2xl bg-gradient-to-r from-emerald-950/80 to-[#041a12] border border-emerald-500/40 shadow-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-emerald-300 uppercase font-mono">
                        Lição Aprendida Registrada com Sucesso!
                    </h3>
                    <p class="text-xs text-gray-300 mt-0.5">
                        Ocorrência homologada preliminarmente sob supervisão do ComForS / CLTI.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button 
                    wire:click="resetForm" 
                    type="button" 
                    class="px-3.5 py-1.5 rounded-xl text-xs font-mono font-bold text-white bg-emerald-600 hover:bg-emerald-500 transition-colors cursor-pointer"
                >
                    Cadastrar Outra
                </button>
                <a 
                    href="{{ route('dashboard') }}" 
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
            <!-- 1. Meio Naval / Submarino (Com Left Sidebar Drawer) -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="submarino" class="text-xs font-mono uppercase text-gray-300 font-semibold">
                        Meio Naval / OM <span class="text-red-400">*</span>
                    </label>
                    <button 
                        type="button" 
                        wire:click="abrirDrawer('meio_naval')" 
                        class="inline-flex items-center gap-1 text-[11px] font-mono text-yellow-400 hover:text-yellow-300 hover:underline cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>+ Adicionar Novo</span>
                    </button>
                </div>

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

            <!-- 3. Subsistema Afetado (Com Left Sidebar Drawer) -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="sistema" class="text-xs font-mono uppercase text-gray-300 font-semibold">
                        Subsistema Afetado <span class="text-red-400">*</span>
                    </label>
                    <button 
                        type="button" 
                        wire:click="abrirDrawer('subsistema')" 
                        class="inline-flex items-center gap-1 text-[11px] font-mono text-yellow-400 hover:text-yellow-300 hover:underline cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>+ Adicionar Novo</span>
                    </button>
                </div>

                <select 
                    id="sistema" 
                    wire:model="sistema" 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono transition-colors"
                >
                    @if (empty($sistemas))
                        <option value="">Nenhum cadastrado. Clique em "+ Adicionar Novo"</option>
                    @else
                        <option value="">Selecione o Subsistema Afetado</option>
                        @foreach ($sistemas as $sis)
                            <option value="{{ $sis }}">{{ $sis }}</option>
                        @endforeach
                    @endif
                </select>
                @error('sistema') <span class="text-[11px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- 2ª Linha: Comissão Operacional e Data da Ocorrência -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- 4. Comissão Operacional (Com Left Sidebar Drawer) -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="comissao" class="text-xs font-mono uppercase text-gray-300 font-semibold">
                        Comissão Operacional
                    </label>
                    <button 
                        type="button" 
                        wire:click="abrirDrawer('comissao')" 
                        class="inline-flex items-center gap-1 text-[11px] font-mono text-yellow-400 hover:text-yellow-300 hover:underline cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>+ Adicionar Nova</span>
                    </button>
                </div>

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
                <span class="text-[10px] text-gray-500 font-mono mt-1 block">Faina ou missão naval onde o fato ocorreu</span>
                @error('comissao') <span class="text-[11px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- 5. Data da Ocorrência -->
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
                @error('data_ocorrencia') <span class="text-[11px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- 3ª Linha: Título da Lição -->
        <div>
            <label for="titulo" class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-2">
                Título da Lição Aprendida <span class="text-red-400">*</span>
            </label>
            <input 
                type="text" 
                id="titulo" 
                wire:model="titulo" 
                placeholder="Ex: Procedimento de estanqueidade de válvulas durante faina de snorquel em mar grosso" 
                class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono transition-colors"
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
                class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-sans leading-relaxed transition-colors"
            ></textarea>
            @error('relato') <span class="text-[11px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- 5ª Linha: Recomendação Doutrinária -->
        <div>
            <label for="recomendacao" class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-2">
                Lição Doutrinária / Recomendação Técnica <span class="text-red-400">*</span>
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
            <div class="text-xs font-mono text-gray-400 flex items-center gap-2">
                <span class="text-yellow-400 font-bold">* Campos obrigatórios</span>
                <span>• Notificante: <strong class="text-white">{{ auth()->user()->graduacao }} {{ auth()->user()->name }}</strong> (NIP: {{ auth()->user()->nip }})</span>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a 
                    href="{{ route('dashboard') }}" 
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
                    <span>Homologar Registro Preliminar</span>
                </button>
            </div>
        </div>
    </form>

    <!-- ========================================================================= -->
    <!-- LEFT SIDEBAR MODAL (DRAWER LATERAL) ESTILO ARGON PRO                       -->
    <!-- ========================================================================= -->
    @if ($drawerAberto)
        <div class="fixed inset-0 z-50 overflow-hidden">
            <!-- Backdrop escuro com blur -->
            <div 
                class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity animate-in fade-in duration-200" 
                wire:click="fecharDrawer"
            ></div>

            <!-- Drawer Deslizante da Esquerda (Left Sidebar Modal) -->
            <aside class="fixed inset-y-0 left-0 max-w-md sm:max-w-lg w-full bg-[#040915] border-r border-blue-500/40 shadow-2xl flex flex-col z-50 animate-in slide-in-from-left duration-300">
                <!-- Header do Drawer -->
                <div class="p-6 bg-gradient-to-r from-blue-950/90 to-[#040a16] border-b border-gray-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/20 border border-blue-400/40 flex items-center justify-center text-blue-400 shrink-0">
                            @if ($drawerTipo === 'meio_naval')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            @elseif ($drawerTipo === 'subsistema')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            @endif
                        </div>
                        <div>
                            <div class="text-[10px] font-mono uppercase tracking-wider text-blue-400 font-semibold">
                                Painel de Apoio Rápido • Tabelas de Apoio
                            </div>
                            <h2 class="text-base font-bold text-white uppercase font-oswald tracking-wide">
                                @if ($drawerTipo === 'meio_naval')
                                    Cadastrar Meio Naval / OM
                                @elseif ($drawerTipo === 'subsistema')
                                    Cadastrar Subsistema Operacional
                                @else
                                    Cadastrar Comissão Operacional
                                @endif
                            </h2>
                        </div>
                    </div>

                    <button 
                        type="button" 
                        wire:click="fecharDrawer" 
                        class="p-2 text-gray-400 hover:text-white rounded-xl hover:bg-gray-800/80 transition-colors cursor-pointer"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Conteúdo do Formulário no Drawer Lateral -->
                <div class="p-6 overflow-y-auto flex-1 space-y-4 text-xs font-mono text-gray-300">
                    <div class="p-3 rounded-xl bg-blue-950/30 border border-blue-800/40 text-[11px] leading-relaxed text-blue-300">
                        Ao salvar, o registro será adicionado automaticamente à respectiva tabela de apoio e selecionado diretamente no formulário da sua lição.
                    </div>

                    <!-- CASO 1: MEIO NAVAL -->
                    @if ($drawerTipo === 'meio_naval')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-300 uppercase font-semibold mb-1">
                                    Indicativo Naval <span class="text-red-400">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="drawerMeioIndicativo" 
                                    placeholder="Ex: S34, S40, BACS, CIAMA..." 
                                    class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3.5 py-2 text-white font-mono uppercase focus:outline-none focus:border-blue-500"
                                />
                                @error('drawerMeioIndicativo') <span class="text-[10px] text-red-400 block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-gray-300 uppercase font-semibold mb-1">
                                    Nome do Meio / OM <span class="text-red-400">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="drawerMeioNome" 
                                    placeholder="Ex: Submarino Tikuna, Base Almirante Castro e Silva..." 
                                    class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3.5 py-2 text-white font-mono focus:outline-none focus:border-blue-500"
                                />
                                @error('drawerMeioNome') <span class="text-[10px] text-red-400 block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-gray-300 uppercase font-semibold mb-1">Tipo do Meio</label>
                                <select 
                                    wire:model="drawerMeioTipo" 
                                    class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3.5 py-2 text-white font-mono focus:outline-none focus:border-blue-500"
                                >
                                    <option value="Submarino">Submarino</option>
                                    <option value="Organização Militar">Organização Militar</option>
                                    <option value="Embarcação de Apoio">Embarcação de Apoio</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-300 uppercase font-semibold mb-1">Classe / Projeto</label>
                                <select 
                                    wire:model="drawerMeioClasse" 
                                    class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3.5 py-2 text-white font-mono focus:outline-none focus:border-blue-500"
                                >
                                    <option value="Classe Riachuelo (S-BR)">Classe Riachuelo (S-BR)</option>
                                    <option value="Classe Tikuna (IKL Mod)">Classe Tikuna (IKL Mod)</option>
                                    <option value="Classe Tupi (IKL 209)">Classe Tupi (IKL 209)</option>
                                    <option value="Propulsão Nuclear (SN-BR)">Propulsão Nuclear (SN-BR)</option>
                                    <option value="Base Operacional">Base Operacional</option>
                                    <option value="Centro de Instrução">Centro de Instrução</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-300 uppercase font-semibold mb-1">Status Operativo</label>
                                <select 
                                    wire:model="drawerMeioStatus" 
                                    class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3.5 py-2 text-white font-mono focus:outline-none focus:border-blue-500"
                                >
                                    <option value="Operacional">Operacional</option>
                                    <option value="Em Manutenção">Em Manutenção</option>
                                    <option value="Em Construção">Em Construção</option>
                                    <option value="Ativa">Ativa</option>
                                </select>
                            </div>
                        </div>

                    <!-- CASO 2: SUBSISTEMA -->
                    @elseif ($drawerTipo === 'subsistema')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-300 uppercase font-semibold mb-1">
                                    Nome do Subsistema / Equipamento <span class="text-red-400">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="drawerSisNome" 
                                    placeholder="Ex: Motores de Combustão Principal (MCP), Sonar Flank Array..." 
                                    class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3.5 py-2 text-white font-mono focus:outline-none focus:border-blue-500"
                                />
                                @error('drawerSisNome') <span class="text-[10px] text-red-400 block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-gray-300 uppercase font-semibold mb-1">Área Técnica / Categoria</label>
                                <select 
                                    wire:model="drawerSisArea" 
                                    class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3.5 py-2 text-white font-mono focus:outline-none focus:border-blue-500"
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
                                <label class="block text-gray-300 uppercase font-semibold mb-1">Criticidade Operacional</label>
                                <select 
                                    wire:model="drawerSisCriticidade" 
                                    class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3.5 py-2 text-white font-mono focus:outline-none focus:border-blue-500"
                                >
                                    <option value="Crítica de Segurança">Crítica de Segurança</option>
                                    <option value="Operacional Essencial">Operacional Essencial</option>
                                    <option value="Tática">Tática</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-300 uppercase font-semibold mb-1">Status</label>
                                <select 
                                    wire:model="drawerSisStatus" 
                                    class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3.5 py-2 text-white font-mono focus:outline-none focus:border-blue-500"
                                >
                                    <option value="Homologado">Homologado</option>
                                    <option value="Em Análise">Em Análise</option>
                                </select>
                            </div>
                        </div>

                    <!-- CASO 3: COMISSÃO OPERACIONAL -->
                    @elseif ($drawerTipo === 'comissao')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-300 uppercase font-semibold mb-1">
                                    Código da Comissão <span class="text-red-400">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="drawerComCodigo" 
                                    placeholder="Ex: TROPICALEX-2026, ADESTRA-S34..." 
                                    class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3.5 py-2 text-white font-mono uppercase focus:outline-none focus:border-blue-500"
                                />
                                @error('drawerComCodigo') <span class="text-[10px] text-red-400 block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-gray-300 uppercase font-semibold mb-1">
                                    Nome da Comissão / Missão <span class="text-red-400">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="drawerComNome" 
                                    placeholder="Ex: Operação Tropicalex, Adestramento de Equipe..." 
                                    class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3.5 py-2 text-white font-mono focus:outline-none focus:border-blue-500"
                                />
                                @error('drawerComNome') <span class="text-[10px] text-red-400 block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-gray-300 uppercase font-semibold mb-1">Tipo de Operação</label>
                                <select 
                                    wire:model="drawerComTipo" 
                                    class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3.5 py-2 text-white font-mono focus:outline-none focus:border-blue-500"
                                >
                                    <option value="Adestramento de Imersão">Adestramento de Imersão</option>
                                    <option value="Operação Multinacional">Operação Multinacional</option>
                                    <option value="Patrulha de Vigilância">Patrulha de Vigilância</option>
                                    <option value="Teste de Aceitação de Mar">Teste de Aceitação de Mar</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-gray-300 uppercase font-semibold mb-1">Ano</label>
                                    <input 
                                        type="number" 
                                        wire:model="drawerComAno" 
                                        placeholder="2026" 
                                        class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3.5 py-2 text-white font-mono focus:outline-none focus:border-blue-500"
                                    />
                                </div>
                                <div>
                                    <label class="block text-gray-300 uppercase font-semibold mb-1">Status</label>
                                    <select 
                                        wire:model="drawerComStatus" 
                                        class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3.5 py-2 text-white font-mono focus:outline-none focus:border-blue-500"
                                    >
                                        <option value="Concluída">Concluída</option>
                                        <option value="Em Andamento">Em Andamento</option>
                                        <option value="Planejada">Planejada</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Footer do Drawer com Ações -->
                <div class="p-4 sm:p-6 bg-gray-950/90 border-t border-gray-800 flex items-center justify-between gap-3">
                    <button 
                        type="button" 
                        wire:click="fecharDrawer" 
                        class="px-4 py-2 rounded-xl text-xs font-mono font-bold text-gray-400 hover:text-white bg-gray-900 border border-gray-700 transition-colors cursor-pointer"
                    >
                        Cancelar
                    </button>

                    @if ($drawerTipo === 'meio_naval')
                        <button 
                            type="button" 
                            wire:click="salvarDrawerMeio" 
                            class="px-5 py-2 rounded-xl text-xs font-mono font-bold text-white bg-blue-600 hover:bg-blue-500 shadow-lg shadow-blue-900/50 border border-blue-500/40 transition-all cursor-pointer"
                        >
                            + Salvar & Selecionar Meio
                        </button>
                    @elseif ($drawerTipo === 'subsistema')
                        <button 
                            type="button" 
                            wire:click="salvarDrawerSistema" 
                            class="px-5 py-2 rounded-xl text-xs font-mono font-bold text-white bg-blue-600 hover:bg-blue-500 shadow-lg shadow-blue-900/50 border border-blue-500/40 transition-all cursor-pointer"
                        >
                            + Salvar & Selecionar Subsistema
                        </button>
                    @elseif ($drawerTipo === 'comissao')
                        <button 
                            type="button" 
                            wire:click="salvarDrawerComissao" 
                            class="px-5 py-2 rounded-xl text-xs font-mono font-bold text-white bg-blue-600 hover:bg-blue-500 shadow-lg shadow-blue-900/50 border border-blue-500/40 transition-all cursor-pointer"
                        >
                            + Salvar & Selecionar Comissão
                        </button>
                    @endif
                </div>
            </aside>
        </div>
    @endif

    <!-- Modal de Guia de Preenchimento da Lição Aprendida -->
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
                                Guia de Preenchimento: Lições Aprendidas
                            </h3>
                            <p class="text-xs text-blue-400/80 font-mono">Diretrizes do Estado-Maior do ComForS</p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        wire:click="fecharGuia" 
                        class="text-gray-400 hover:text-white p-1 rounded-lg hover:bg-gray-800 transition-colors cursor-pointer"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
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
                            <p class="text-gray-400 text-[11px] mt-0.5">
                                Submarino ou OM onde a ocorrência foi registrada. Clique em <span class="text-yellow-400 font-bold">+ Adicionar Novo</span> para abrir o painel lateral com todos os campos cadastrais da tabela de apoio.
                            </p>
                        </div>

                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">2. Comissão Operacional & Data</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">
                                Identifica a missão naval na qual o evento ocorreu e a data cronológica exata do fato observado.
                            </p>
                        </div>

                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">3. Subsistema Afetado</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">
                                Equipamento ou área de engenharia envolvida (MCP, MEP, Lastro, Sonar, CAV). Também pode ser adicionado pelo painel lateral com sua área técnica e criticidade.
                            </p>
                        </div>

                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">4. Grau de Sigilo</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">
                                Classificação doutrinária da informação segundo a legislação militar: <span class="text-emerald-400">Ostensivo</span>, <span class="text-blue-400">Reservado</span>, <span class="text-yellow-400">Confidencial</span> ou <span class="text-red-400">Secreto</span>.
                            </p>
                        </div>

                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">5. Relato Circunstanciado & Recomendação Técnica</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">
                                O relato deve conter a cronologia precisa dos fatos e ações no mar. A recomendação deve orientar correções em procedimentos operacionais padrão (POPs), planos de manutenção preventiva ou instrução no CIAMA.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-3.5 bg-gray-950/80 border-t border-gray-800 flex items-center justify-end">
                    <button 
                        type="button" 
                        wire:click="fecharGuia" 
                        class="px-4 py-2 rounded-xl text-xs font-mono font-semibold bg-blue-600 hover:bg-blue-500 text-white transition-colors cursor-pointer"
                    >
                        Entendido / Fechar Guia
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
