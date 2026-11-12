<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

new #[Layout('layouts.app')] class extends Component
{
    use WithFileUploads;

    // Upload de Documento PDF (Limite 10 MB)
    public $arquivo_pdf = null;
    public ?string $nome_arquivo_pdf = null;
    public ?int $tamanho_arquivo_pdf = null;
    // Listas dinâmicas
    public array  = [];
    public array  = [];
    public array  = [];

    // Campos do formulário
    public string  = '';
    public string  = '';
    public string  = '';
    public string  = '';
    public string  = 'RESERVADO';
    public string  = '';
    public string  = '';
    public string  = '';

    public bool  = false;
    public bool  = false;

    // Right Sidebar Modal (Drawer Lateral à Direita)
    public bool  = false;
    public string  = ''; // 'tipo_documento' | 'meio_naval' | 'comissao'

    // Campos do Drawer: Tipo de Documento
    public string  = '';
    public string  = '';
    public string  = '';
    public string  = 'Ativo';

    // Campos do Drawer: Meio Naval
    public string  = '';
    public string  = '';
    public string  = 'Submarino';
    public string  = 'Classe Riachuelo (S-BR)';
    public string  = 'Operacional';

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
        $padroes = [
            [
                'id' => '1',
                'sigla' => 'RFC',
                'nome' => 'Relatório Final de Comissão',
                'descricao' => 'Relatório circunstanciado elaborado pelo Comandante da Unidade e tripulação ao encerramento de missões, exercícios no mar e fainas de imersão.',
                'status' => 'Ativo',
            ],
            [
                'id' => '2',
                'sigla' => 'ARFC',
                'nome' => 'Análise de Relatório Final de Comissão',
                'descricao' => 'Parecer técnico e lições aprendidas homologadas pelo Estado-Maior do ComForS sobre os fatos relatados no RFC.',
                'status' => 'Ativo',
            ],
        ];
        $this->tipos_documento = session('crud_tipos_documento', $padroes);
    }

    public function updatedTipoSigla($value): void
    {
        $this->gerarNumeroUnico($value);
    }

    public function updatedDataDocumento(): void
    {
        if ($this->tipo_sigla) {
            $this->gerarNumeroUnico($this->tipo_sigla);
        }
    }

    public function gerarNumeroUnico(string $sigla): void
    {
        if (empty($sigla)) {
            $this->numero_gerado = '';
            return;
        }

        $docs = session('documentos_cadastrados', []);
        $doMesmoTipo = array_filter($docs, fn($d) => ($d['tipo_sigla'] ?? '') === $sigla);
        $seq = count($doMesmoTipo) + 1;
        $ano = $this->data_documento ? date('Y', strtotime($this->data_documento)) : date('Y');
        $this->numero_gerado = strtoupper(trim($sigla)) . '-' . $ano . '-' . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
    }

    // Right Sidebar Drawer Methods
    public function abrirDrawer(string $tipo): void
    {
        $this->drawerTipo = $tipo;
        $this->drawerAberto = true;
        $this->resetValidation();

        if ($tipo === 'tipo_documento') {
            $this->drawerTipoSigla = '';
            $this->drawerTipoNome = '';
            $this->drawerTipoDescricao = '';
            $this->drawerTipoStatus = 'Ativo';
        } elseif ($tipo === 'meio_naval') {
            $this->drawerMeioIndicativo = '';
            $this->drawerMeioNome = '';
            $this->drawerMeioTipo = 'Submarino';
            $this->drawerMeioClasse = 'Classe Riachuelo (S-BR)';
            $this->drawerMeioStatus = 'Operacional';
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

    public function salvarDrawerTipo(): void
    {
        $this->validate([
            'drawerTipoSigla' => ['required', 'string', 'min:2', 'max:10'],
            'drawerTipoNome' => ['required', 'string', 'min:3', 'max:150'],
        ], [
            'drawerTipoSigla.required' => 'Informe a sigla (ex: RFC, ARFC).',
            'drawerTipoNome.required' => 'Informe o nome do tipo documental.',
        ]);

        $siglaFormatada = strtoupper(trim($this->drawerTipoSigla));
        $tipos = session('crud_tipos_documento', $this->tipos_documento);

        $novo = [
            'id' => (string) Str::uuid(),
            'sigla' => $siglaFormatada,
            'nome' => trim($this->drawerTipoNome),
            'descricao' => trim($this->drawerTipoDescricao) ?: 'Cadastrado via painel rápido de Novo Documento',
            'status' => $this->drawerTipoStatus,
        ];

        array_unshift($tipos, $novo);
        session(['crud_tipos_documento' => $tipos]);

        $this->carregarTipos();
        $this->tipo_sigla = $siglaFormatada;
        $this->gerarNumeroUnico($siglaFormatada);
        $this->fecharDrawer();
    }

    public function salvarDrawerMeio(): void
    {
        $this->validate([
            'drawerMeioIndicativo' => 'required|min:2|max:20',
            'drawerMeioNome' => 'required|min:3|max:100',
        ], [
            'drawerMeioIndicativo.required' => 'O indicativo naval é obrigatório.',
            'drawerMeioNome.required' => 'O nome do meio naval é obrigatório.',
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

    public function salvarDrawerComissao(): void
    {
        $this->validate([
            'drawerComCodigo' => 'required|min:2|max:30',
            'drawerComNome' => 'required|min:3|max:100',
        ], [
            'drawerComCodigo.required' => 'O código da comissão é obrigatório.',
            'drawerComNome.required' => 'O nome da comissão é obrigatório.',
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
            'tipo_sigla' => 'required',
            'titulo' => 'required|min:5|max:255',
            'ementa' => 'required|min:10',
            'grau_sigilo' => 'required|in:OSTENSIVO,RESERVADO,CONFIDENCIAL,SECRETO',
            'arquivo_pdf' => 'nullable|file|mimes:pdf|max:10240',
        ], [
            'tipo_sigla.required' => 'Selecione o tipo de documento ou adicione um novo.',
            'titulo.required' => 'Informe o título do documento (mínimo 5 caracteres).',
            'ementa.required' => 'Descreva suscintamente a ementa ou resumo do documento.',
            'arquivo_pdf.file' => 'O anexo enviado é inválido.',
            'arquivo_pdf.mimes' => 'O arquivo enviado deve ser estritamente no formato PDF.',
            'arquivo_pdf.max' => 'O arquivo PDF não pode ultrapassar o limite máximo de 10 MB.',
        ]);

        $nomeOriginal = null;
        $tamanhoBytes = null;
        if ($this->arquivo_pdf) {
            $nomeOriginal = $this->arquivo_pdf->getClientOriginalName();
            $tamanhoBytes = $this->arquivo_pdf->getSize();
        }

        $tipoEncontrado = collect($this->tipos_documento)->firstWhere('sigla', $this->tipo_sigla);
        $nomeTipo = $tipoEncontrado['nome'] ?? $this->tipo_sigla;

        $novoDoc = [
            'id' => (string) Str::uuid(),
            'numero' => $this->numero_gerado,
            'tipo_sigla' => $this->tipo_sigla,
            'tipo_nome' => $nomeTipo,
            'submarino' => $this->submarino,
            'comissao' => $this->comissao,
            'grau_sigilo' => $this->grau_sigilo,
            'data_documento' => $this->data_documento ?: date('Y-m-d'),
            'titulo' => trim($this->titulo),
            'ementa' => trim($this->ementa),
            'arquivo_pdf_nome' => $nomeOriginal,
            'arquivo_pdf_tamanho' => $tamanhoBytes,
            'autor' => auth()->user()->graduacao . ' ' . auth()->user()->name,
            'nip' => auth()->user()->nip,
            'status' => 'Cadastrado',
        ];

        $documentos = session('documentos_cadastrados', []);
        array_unshift($documentos, $novoDoc);
        session(['documentos_cadastrados' => $documentos]);

        $this->salvo = true;
    }

    public function resetForm(): void
    {
        $this->reset(['tipo_sigla', 'numero_gerado', 'submarino', 'comissao', 'titulo', 'ementa', 'arquivo_pdf', 'nome_arquivo_pdf', 'tamanho_arquivo_pdf']);
        $this->grau_sigilo = 'RESERVADO';
        $this->data_documento = date('Y-m-d');
        $this->salvo = false;
        $this->resetValidation();
    }
}; ?>

<div class="space-y-6 select-none w-full">
    <!-- Banner de Homologação & Construção -->
    <div class="p-4 rounded-2xl bg-gradient-to-r from-amber-950/40 via-[#0a1424] to-[#040a16] border border-amber-500/30 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center shrink-0">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-amber-400 font-mono uppercase tracking-wider">
                        Módulo em Construção • Cadastro de Documentos
                    </span>
                    <span class="text-[10px] font-mono text-gray-500 bg-gray-900 px-2 py-0.5 rounded border border-gray-800">
                        Geração de Número Único
                    </span>
                </div>
                <p class="text-[11px] text-gray-400 font-mono mt-0.5 leading-snug">
                    Cadastre documentos de referência que servirão de fundamentação técnica e histórica para as Lições Aprendidas.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
            <button 
                type="button" 
                wire:click="$set('modalGuiaAberto', true)" 
                class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-mono font-semibold text-blue-300 bg-gray-900/90 hover:bg-gray-800 border border-blue-500/30 hover:border-blue-400/60 shadow-lg transition-all cursor-pointer"
            >
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Guia de Preenchimento</span>
            </button>
        </div>
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
                    <h3 class="text-sm font-bold text-emerald-300 uppercase font-mono">
                        Documento Registrado com Sucesso: <span class="text-yellow-400">{{ $numero_gerado }}</span>
                    </h3>
                    <p class="text-xs text-gray-300 mt-0.5">
                        Número único gerado e armazenado na base de referências doutrinárias do ComForS.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button 
                    wire:click="resetForm" 
                    type="button" 
                    class="px-3.5 py-1.5 rounded-xl text-xs font-mono font-bold text-white bg-emerald-600 hover:bg-emerald-500 transition-colors cursor-pointer"
                >
                    Cadastrar Outro Documento
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

    <!-- Formulário Principal -->
    <form wire:submit="salvar" class="rounded-2xl p-6 sm:p-8 bg-[#040a16] border border-gray-800/80 shadow-2xl space-y-6">
        <!-- Linha 1: Tipo de Documento e Número Único Automático -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Seleção do Tipo de Documento com Right Sidebar Drawer -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="tipo_sigla" class="text-xs font-mono uppercase text-gray-300 font-semibold">
                        Tipo de Documento <span class="text-red-400">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <button 
                            type="button" 
                            wire:click="abrirDrawer('tipo_documento')" 
                            class="inline-flex items-center gap-1 text-[11px] font-mono text-yellow-400 hover:text-yellow-300 hover:underline cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>+ Novo Tipo</span>
                        </button>
                        <a 
                            href="{{ route('tipos-documento') }}" 
                            wire:navigate 
                            class="text-[10px] font-mono text-blue-400 hover:underline cursor-pointer"
                        >
                            (Tabela de Apoio)
                        </a>
                    </div>
                </div>

                <select 
                    id="tipo_sigla" 
                    wire:model.live="tipo_sigla" 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono transition-colors"
                >
                    <option value="">Selecione o Tipo de Documento</option>
                    @foreach ($tipos_documento as $td)
                        <option value="{{ $td['sigla'] }}">
                            [{{ $td['sigla'] }}] - {{ $td['nome'] }}
                        </option>
                    @endforeach
                </select>
                @error('tipo_sigla') <span class="text-[11px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
                <span class="text-[10px] text-gray-500 font-mono mt-1 block">
                    A sigla define o padrão identificador único do documento militar
                </span>
            </div>

            <!-- Número Único Gerado Automaticamente -->
            <div>
                <label class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-2">
                    Número Único do Documento <span class="text-yellow-400">(Automático)</span>
                </label>
                <div class="flex items-center gap-3">
                    <div class="flex-1 bg-[#02050d] border border-yellow-500/40 rounded-xl px-4 py-2.5 text-sm font-mono font-bold text-yellow-400 shadow-inner flex items-center justify-between">
                        <span>{{ $numero_gerado ?: 'SELECIONE O TIPO AO LADO' }}</span>
                        @if ($numero_gerado)
                            <span class="text-[10px] bg-yellow-950 text-yellow-300 px-2 py-0.5 rounded border border-yellow-700/50 uppercase">
                                Sequencial Oficial
                            </span>
                        @endif
                    </div>
                </div>
                <span class="text-[10px] text-gray-500 font-mono mt-1 block">
                    Regra padrão ComForS: {SIGLA}-{ANO}-{SEQUENCIAL} (Ex: RFC-2026-0001, ARFC-2026-0001)
                </span>
            </div>
        </div>

        <!-- Linha 2: Meio Naval, Comissão, Grau de Sigilo, Data de Emissão -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
            <!-- Meio Naval / OM com Right Sidebar Drawer -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="submarino" class="text-xs font-mono uppercase text-gray-300 font-semibold">
                        Meio Naval / OM
                    </label>
                    <button 
                        type="button" 
                        wire:click="abrirDrawer('meio_naval')" 
                        class="text-[11px] font-mono text-yellow-400 hover:underline cursor-pointer"
                    >
                        + Novo
                    </button>
                </div>
                <select 
                    id="submarino" 
                    wire:model="submarino" 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 font-mono"
                >
                    <option value="">Não informado / Geral</option>
                    @foreach ($meios_navais as $m)
                        <option value="{{ $m }}">{{ $m }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Comissão Operacional com Right Sidebar Drawer -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="comissao" class="text-xs font-mono uppercase text-gray-300 font-semibold">
                        Comissão
                    </label>
                    <button 
                        type="button" 
                        wire:click="abrirDrawer('comissao')" 
                        class="text-[11px] font-mono text-yellow-400 hover:underline cursor-pointer"
                    >
                        + Nova
                    </button>
                </div>
                <select 
                    id="comissao" 
                    wire:model="comissao" 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 font-mono"
                >
                    <option value="">Não vinculada</option>
                    @foreach ($comissoes as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Grau de Sigilo -->
            <div>
                <label for="grau_sigilo" class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-2">
                    Grau de Sigilo <span class="text-red-400">*</span>
                </label>
                <select 
                    id="grau_sigilo" 
                    wire:model="grau_sigilo" 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 font-mono"
                >
                    <option value="OSTENSIVO">OSTENSIVO</option>
                    <option value="RESERVADO">RESERVADO</option>
                    <option value="CONFIDENCIAL">CONFIDENCIAL</option>
                    <option value="SECRETO">SECRETO</option>
                </select>
            </div>

            <!-- Data de Emissão -->
            <div>
                <label for="data_documento" class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-2">
                    Data de Emissão <span class="text-red-400">*</span>
                </label>
                <input 
                    type="date" 
                    id="data_documento" 
                    wire:model.live="data_documento" 
                    class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 font-mono"
                />
            </div>
        </div>

        <!-- Linha 3: Título do Documento -->
        <div>
            <label for="titulo" class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-2">
                Título do Documento Militar <span class="text-red-400">*</span>
            </label>
            <input 
                type="text" 
                id="titulo" 
                wire:model="titulo" 
                placeholder="Ex: Relatório Final da Comissão de Adestramento de Equipes de Manobra e Snorquel do S34" 
                class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-gray-600 focus:outline-none focus:border-blue-500 font-mono"
            />
            @error('titulo') <span class="text-[11px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Linha 4: Ementa / Resumo do Documento -->
        <div>
            <label for="ementa" class="block text-xs font-mono uppercase text-gray-300 font-semibold mb-2">
                Ementa / Resumo Executivo do Documento <span class="text-red-400">*</span>
            </label>
            <textarea 
                id="ementa" 
                wire:model="ementa" 
                rows="4" 
                placeholder="Descreva suscintamente o teor do documento, principais ocorrências operacionais relatadas, conclusões e referências normativas que servirão de embasamento para Lições Aprendidas..." 
                class="w-full bg-[#02050d] border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-gray-600 focus:outline-none focus:border-blue-500 font-sans leading-relaxed"
            ></textarea>
            @error('ementa') <span class="text-[11px] text-red-400 font-mono mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Linha 5: Upload de Arquivo PDF Oficial (Limite Máximo de 10 MB) -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <label for="arquivo_pdf" class="text-xs font-mono uppercase text-gray-300 font-semibold flex items-center gap-2">
                    <span>Anexo do Documento (PDF)</span>
                    <span class="text-[10px] text-blue-400 bg-blue-950/80 border border-blue-800/60 px-2 py-0.5 rounded font-normal">
                        Limite: 10 MB
                    </span>
                </label>
                @if ($arquivo_pdf)
                    <button 
                        type="button" 
                        wire:click="$set('arquivo_pdf', null)" 
                        class="text-[11px] font-mono text-red-400 hover:text-red-300 hover:underline cursor-pointer"
                    >
                        ✕ Remover PDF
                    </button>
                @endif
            </div>

            @if ($arquivo_pdf)
                <!-- Card de Arquivo PDF Selecionado -->
                <div class="p-4 rounded-xl bg-blue-950/30 border border-blue-500/40 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-red-500/20 border border-red-500/40 flex items-center justify-center text-red-400 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-white font-mono truncate">
                                {{ $arquivo_pdf->getClientOriginalName() }}
                            </div>
                            <div class="text-[11px] text-gray-400 font-mono mt-0.5 flex items-center gap-2">
                                <span class="text-emerald-400 font-bold">{{ number_format($arquivo_pdf->getSize() / 1024 / 1024, 2) }} MB</span>
                                <span>•</span>
                                <span class="text-blue-300">Formato PDF Validado</span>
                            </div>
                        </div>
                    </div>

                    <button 
                        type="button" 
                        wire:click="$set('arquivo_pdf', null)" 
                        class="p-2 rounded-lg text-gray-400 hover:text-red-400 hover:bg-red-950/30 transition-colors shrink-0 cursor-pointer"
                        title="Remover este anexo"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            @else
                <!-- Dropzone de Seleção de PDF -->
                <div class="relative border-2 border-dashed border-gray-700 hover:border-blue-500/60 rounded-2xl p-6 text-center transition-all bg-[#02050d] group cursor-pointer">
                    <input 
                        type="file" 
                        id="arquivo_pdf" 
                        wire:model="arquivo_pdf" 
                        accept="application/pdf" 
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                    />
                    <div class="flex flex-col items-center justify-center gap-2">
                        <div class="w-12 h-12 rounded-xl bg-blue-500/10 group-hover:bg-blue-500/20 border border-blue-500/30 flex items-center justify-center text-blue-400 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <div class="text-xs font-mono text-gray-300">
                            <span class="text-blue-400 font-bold group-hover:underline">Clique para selecionar o PDF</span> ou arraste o arquivo aqui
                        </div>
                        <p class="text-[10px] font-mono text-gray-500">
                            Formato aceito: <strong class="text-yellow-400">.PDF</strong> • Tamanho máximo permitido: <strong class="text-yellow-400">10 MB</strong>
                        </p>
                    </div>
                </div>
            @endif

            <!-- Loading de upload -->
            <div wire:loading wire:target="arquivo_pdf" class="text-xs text-blue-400 font-mono mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                <span>Processando e validando arquivo PDF...</span>
            </div>

            @error('arquivo_pdf') 
                <span class="text-[11px] text-red-400 font-mono mt-1.5 block">{{ $message }}</span> 
            @enderror
        </div>

        <!-- Barra Inferior de Ações -->
        <div class="pt-4 border-t border-gray-800/80 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-xs font-mono text-gray-400 flex items-center gap-2">
                <span class="text-yellow-400 font-bold">* Campos obrigatórios</span>
                <span>• Cadastrado por: <strong class="text-white">{{ auth()->user()->graduacao }} {{ auth()->user()->name }}</strong> (NIP: {{ auth()->user()->nip }})</span>
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
                    <span>Registrar Documento Militar</span>
                </button>
            </div>
        </div>
    </form>

    <!-- ========================================================================= -->
    <!-- RIGHT SIDEBAR MODAL (DRAWER LATERAL À DIREITA) ESTILO ARGON PRO           -->
    <!-- ========================================================================= -->
    @if ($drawerAberto)
        <div class="fixed inset-0 z-50 overflow-hidden">
            <!-- Backdrop escuro com blur -->
            <div 
                class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity animate-in fade-in duration-200" 
                wire:click="fecharDrawer"
            ></div>

            <!-- Drawer Deslizante da Direita (Right Sidebar Modal) -->
            <aside class="fixed inset-y-0 right-0 max-w-md sm:max-w-lg w-full bg-[#040915] border-l border-blue-500/40 shadow-2xl flex flex-col z-50 animate-in slide-in-from-right duration-300">
                <!-- Header do Drawer -->
                <div class="p-6 bg-gradient-to-r from-blue-950/90 to-[#040a16] border-b border-gray-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/20 border border-blue-400/40 flex items-center justify-center text-blue-400 shrink-0">
                            @if ($drawerTipo === 'tipo_documento')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            @elseif ($drawerTipo === 'meio_naval')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            @endif
                        </div>
                        <div>
                            <div class="text-[10px] font-mono uppercase tracking-wider text-blue-400 font-semibold">
                                Painel de Apoio Rápido • Documentos
                            </div>
                            <h2 class="text-base font-bold text-white uppercase font-oswald tracking-wide">
                                @if ($drawerTipo === 'tipo_documento')
                                    Cadastrar Tipo de Documento
                                @elseif ($drawerTipo === 'meio_naval')
                                    Cadastrar Meio Naval / OM
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
                        Ao salvar, o registro será adicionado automaticamente à respectiva tabela de apoio e selecionado diretamente no formulário do documento militar.
                    </div>

                    <!-- CASO 1: TIPO DE DOCUMENTO -->
                    @if ($drawerTipo === 'tipo_documento')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-300 uppercase font-semibold mb-1">
                                    Sigla do Documento <span class="text-red-400">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="drawerTipoSigla" 
                                    placeholder="Ex: RFC, ARFC, RTE, RCI..." 
                                    maxlength="10"
                                    class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3.5 py-2 text-white font-mono uppercase focus:outline-none focus:border-blue-500"
                                />
                                @error('drawerTipoSigla') <span class="text-[10px] text-red-400 block mt-1">{{ $message }}</span> @enderror
                                <span class="text-[10px] text-gray-500 mt-1 block">
                                    Prefixo da numeração única: <strong class="text-yellow-400">{{ strtoupper($drawerTipoSigla ?: 'SIGLA') }}-{{ date('Y') }}-0001</strong>
                                </span>
                            </div>

                            <div>
                                <label class="block text-gray-300 uppercase font-semibold mb-1">
                                    Nome do Tipo de Documento <span class="text-red-400">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="drawerTipoNome" 
                                    placeholder="Ex: Relatório Final de Comissão, Relatório de Inspeção..." 
                                    class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3.5 py-2 text-white font-mono focus:outline-none focus:border-blue-500"
                                />
                                @error('drawerTipoNome') <span class="text-[10px] text-red-400 block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-gray-300 uppercase font-semibold mb-1">Descrição / Aplicação Doutrinária</label>
                                <textarea 
                                    wire:model="drawerTipoDescricao" 
                                    rows="3" 
                                    placeholder="Descreva a finalidade doutrinária e momentos de emissão deste documento..." 
                                    class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3.5 py-2 text-white font-mono focus:outline-none focus:border-blue-500"
                                ></textarea>
                            </div>

                            <div>
                                <label class="block text-gray-300 uppercase font-semibold mb-1">Status</label>
                                <select 
                                    wire:model="drawerTipoStatus" 
                                    class="w-full bg-[#02050d] border border-gray-700 rounded-xl px-3.5 py-2 text-white font-mono focus:outline-none focus:border-blue-500"
                                >
                                    <option value="Ativo">Ativo (Disponível para emissão)</option>
                                    <option value="Inativo">Inativo</option>
                                </select>
                            </div>
                        </div>

                    <!-- CASO 2: MEIO NAVAL -->
                    @elseif ($drawerTipo === 'meio_naval')
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

                    @if ($drawerTipo === 'tipo_documento')
                        <button 
                            type="button" 
                            wire:click="salvarDrawerTipo" 
                            class="px-5 py-2 rounded-xl text-xs font-mono font-bold text-white bg-blue-600 hover:bg-blue-500 shadow-lg shadow-blue-900/50 border border-blue-500/40 transition-all cursor-pointer"
                        >
                            + Salvar & Selecionar Tipo
                        </button>
                    @elseif ($drawerTipo === 'meio_naval')
                        <button 
                            type="button" 
                            wire:click="salvarDrawerMeio" 
                            class="px-5 py-2 rounded-xl text-xs font-mono font-bold text-white bg-blue-600 hover:bg-blue-500 shadow-lg shadow-blue-900/50 border border-blue-500/40 transition-all cursor-pointer"
                        >
                            + Salvar & Selecionar Meio
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

    <!-- Modal de Guia de Preenchimento -->
    @if ($modalGuiaAberto)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 backdrop-blur-md p-4 animate-in fade-in duration-200">
            <div class="bg-[#0b1329] border border-blue-500/40 rounded-2xl w-full max-w-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-950/80 to-[#0a1226] border-b border-gray-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/20 border border-blue-400/30 flex items-center justify-center text-blue-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider font-mono">
                                Guia de Cadastro de Documentos Militares
                            </h3>
                            <p class="text-xs text-blue-400/80 font-mono">Diretrizes de Rastreabilidade do ComForS</p>
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
                        <span class="text-blue-300 font-bold block mb-1">FINALIDADE DA BASE DOCUMENTAL</span>
                        Esta tela centraliza o cadastro prévio de documentos que servirão de embasamento para o SIPA-SUB e Lições Aprendidas, assegurando numeração oficial única baseada na sigla militar.
                    </div>

                    <div class="space-y-3">
                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">1. Tipo de Documento & Sigla</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">
                                A sigla (ex: <strong class="text-yellow-400">RFC</strong> para Relatório Final de Comissão ou <strong class="text-yellow-400">ARFC</strong> para Análise) determina o prefixo do número de registro único. Caso o tipo ainda não exista, utilize o painel lateral deslizante à direita para cadastrá-lo com todos os seus dados.
                            </p>
                        </div>

                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">2. Geração do Número Único</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">
                                O número é gerado automaticamente no padrão <strong class="text-yellow-400">{SIGLA}-{ANO}-{SEQUENCIAL}</strong>, garantindo unicidade cronológica.
                            </p>
                        </div>

                        <div class="border-l-2 border-blue-500 pl-3">
                            <p class="text-white font-bold">3. Vínculo com Lições Aprendidas</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">
                                Posteriormente, este documento poderá ser anexado ou referenciado diretamente ao redigir uma Lição Aprendida ou faina no SIPA-SUB.
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
