<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

new #[Layout('layouts.app')] class extends Component
{
    public array $usuarios = [];
    public string $busca = '';
    public ?string $mensagemSucesso = null;
    public ?string $mensagemErro = null;

    // Estado do Modal de Criar / Editar
    public bool $modalAberto = false;
    public bool $isEdicao = false;
    public ?int $usuarioId = null;

    // Campos do Formulário
    public string $name = '';
    public string $nip = '';
    public string $email = '';
    public string $modulo_acesso = 'licoes_aprendidas';
    public string $password = '';
    public string $password_confirmation = '';

    // Estado do Modal de Confirmação de Exclusão
    public bool $modalExclusaoAberto = false;
    public ?int $usuarioParaExcluirId = null;
    public string $usuarioParaExcluirNome = '';

    private function autorizar(): void
    {
        if (! auth()->check() || auth()->user()->nip !== '09097074') {
            abort(404);
        }
    }

    public function mount(): void
    {
        $this->autorizar();
        session(['modulo_ativo' => 'sys_ops_k7']);
        $this->carregarUsuarios();
    }

    public function updatedBusca(): void
    {
        $this->carregarUsuarios();
    }

    public function carregarUsuarios(): void
    {
        $this->autorizar();

        $query = User::query()->orderBy('id', 'asc');

        if (! empty($this->busca)) {
            $termo = trim($this->busca);
            $query->where(function ($q) use ($termo) {
                $q->where('name', 'like', "%{$termo}%")
                  ->orWhere('nip', 'like', "%{$termo}%")
                  ->orWhere('email', 'like', "%{$termo}%");
            });
        }

        $this->usuarios = $query->get()->toArray();
    }

    public function abrirModalCriar(): void
    {
        $this->autorizar();
        $this->resetForm();
        $this->isEdicao = false;
        $this->modalAberto = true;
    }

    public function abrirModalEditar(int $id): void
    {
        $this->autorizar();
        $user = User::findOrFail($id);

        $this->usuarioId = $user->id;
        $this->name = $user->name;
        $this->nip = $user->nip;
        $this->email = $user->email;
        $this->modulo_acesso = $user->modulo_acesso ?? 'licoes_aprendidas';
        $this->password = '';
        $this->password_confirmation = '';
        $this->isEdicao = true;
        $this->modalAberto = true;
    }

    public function fecharModal(): void
    {
        $this->modalAberto = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->resetErrorBag();
        $this->usuarioId = null;
        $this->name = '';
        $this->nip = '';
        $this->email = '';
        $this->modulo_acesso = 'licoes_aprendidas';
        $this->password = '';
        $this->password_confirmation = '';
    }

    public function salvarUsuario(): void
    {
        $this->autorizar();

        // Sanitiza NIP para 8 dígitos numéricos
        $this->nip = substr(preg_replace('/[^0-9]/', '', $this->nip), 0, 8);

        $rules = [
            'name' => 'required|string|min:3|max:255',
            'nip' => [
                'required',
                'digits:8',
                $this->isEdicao 
                    ? Rule::unique('users', 'nip')->ignore($this->usuarioId)
                    : 'unique:users,nip',
            ],
            'email' => [
                'required',
                'email',
                $this->isEdicao 
                    ? Rule::unique('users', 'email')->ignore($this->usuarioId)
                    : 'unique:users,email',
            ],
            'modulo_acesso' => 'required|in:licoes_aprendidas,sipa_sub,ambos',
        ];

        if (! $this->isEdicao) {
            $rules['password'] = 'required|string|min:6|confirmed';
        } else {
            if (! empty($this->password)) {
                $rules['password'] = 'string|min:6|confirmed';
            }
        }

        $this->validate($rules, [
            'nip.digits' => 'O NIP deve conter exatamente 8 dígitos numéricos.',
            'nip.unique' => 'Este NIP já está cadastrado para outro militar.',
            'email.unique' => 'Este e-mail já está cadastrado no sistema.',
            'password.confirmed' => 'A confirmação de senha não confere.',
            'password.min' => 'A senha deve conter pelo menos 6 caracteres.',
        ]);

        if ($this->isEdicao) {
            $user = User::findOrFail($this->usuarioId);
            $dados = [
                'name' => $this->name,
                'nip' => $this->nip,
                'email' => $this->email,
                'modulo_acesso' => $this->modulo_acesso,
            ];

            if (! empty($this->password)) {
                $dados['password'] = Hash::make($this->password);
            }

            $user->update($dados);
            $this->mensagemSucesso = "Militar {$user->name} atualizado com sucesso.";
        } else {
            $user = User::create([
                'name' => $this->name,
                'nip' => $this->nip,
                'email' => $this->email,
                'modulo_acesso' => $this->modulo_acesso,
                'password' => Hash::make($this->password),
            ]);
            $this->mensagemSucesso = "Novo militar {$user->name} cadastrado com sucesso no sistema.";
        }

        $this->fecharModal();
        $this->carregarUsuarios();
    }

    public function confirmarExclusao(int $id): void
    {
        $this->autorizar();
        $user = User::findOrFail($id);

        if ($user->nip === '09097074') {
            $this->mensagemErro = 'A credencial de comando central é permanente e não pode ser excluída.';
            return;
        }

        $this->usuarioParaExcluirId = $user->id;
        $this->usuarioParaExcluirNome = $user->name;
        $this->modalExclusaoAberto = true;
    }

    public function excluirUsuario(): void
    {
        $this->autorizar();

        if ($this->usuarioParaExcluirId) {
            $user = User::findOrFail($this->usuarioParaExcluirId);

            if ($user->nip === '09097074') {
                $this->mensagemErro = 'Operação não permitida sobre o usuário de comando.';
                $this->modalExclusaoAberto = false;
                return;
            }

            $nome = $user->name;
            $user->delete();
            $this->mensagemSucesso = "Credencial do militar {$nome} foi revogada e excluída.";
            $this->modalExclusaoAberto = false;
            $this->usuarioParaExcluirId = null;
            $this->carregarUsuarios();
        }
    }

    public function alterarModuloUsuario(int $userId, string $novoModulo): void
    {
        $this->autorizar();
        $user = User::find($userId);
        if ($user) {
            $user->modulo_acesso = $novoModulo;
            $user->save();
            $this->mensagemSucesso = "Permissão do militar {$user->name} alterada para [{$novoModulo}].";
            $this->carregarUsuarios();
        }
    }
}; ?>

<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto select-none space-y-8" x-data="{}" style="color: #ffffff;">

    <!-- Alertas de Feedback -->
    @if($mensagemSucesso)
        <div class="p-4 rounded-xl text-sm font-semibold flex items-center justify-between shadow-lg" style="background-color: #064e3b; color: #a7f3d0; border: 1px solid #059669;">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ $mensagemSucesso }}</span>
            </div>
            <button wire:click="$set('mensagemSucesso', null)" class="text-xs underline hover:text-white cursor-pointer">Fechar</button>
        </div>
    @endif

    @if($mensagemErro)
        <div class="p-4 rounded-xl text-sm font-semibold flex items-center justify-between shadow-lg" style="background-color: #450a0a; color: #fca5a5; border: 1px solid #991b1b;">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>{{ $mensagemErro }}</span>
            </div>
            <button wire:click="$set('mensagemErro', null)" class="text-xs underline hover:text-white cursor-pointer">Fechar</button>
        </div>
    @endif

    <!-- Banner Superior do Comando Geral -->
    <div class="rounded-2xl p-6 sm:p-8 shadow-2xl relative overflow-hidden" style="background: linear-gradient(135deg, #111827 0%, #030712 100%); border: 1px solid #374151;">
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="p-3 rounded-2xl" style="background-color: rgba(0, 0, 0, 0.6); border: 1px solid rgba(255, 255, 255, 0.1);">
                    <img src="{{ asset('img/brasao_0.png') }}" alt="Brasão ComForS" class="h-16 sm:h-20 w-auto object-contain drop-shadow">
                </div>
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-mono mb-2" style="background-color: #1e1b4b; color: #c7d2fe; border: 1px solid #4338ca;">
                        <span class="w-2 h-2 rounded-full animate-pulse" style="background-color: #818cf8;"></span>
                        <span>Terminal Administrativo Central • Nível Supremo</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight uppercase" style="font-family: 'Oswald', sans-serif; color: #ffffff;">
                        Console Geral de Comando & Administração
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-400 mt-1">
                        Gerenciamento completo (CRUD) de militares, permissões táticas e auditoria da ForçaOnline.
                    </p>
                </div>
            </div>

            <!-- Dados do Administrador -->
            <div class="text-right font-mono text-xs text-gray-400 shrink-0">
                <div>Administrador: <strong class="text-white">{{ auth()->user()->name }}</strong></div>
                <div class="text-yellow-400 font-bold mt-0.5">NIP: {{ auth()->user()->nip }}</div>
                <div class="text-gray-500 text-[11px] mt-1">ComForS - CLTI</div>
            </div>
        </div>
    </div>

    <!-- Seção de Ações Rápidas & Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 1: Lições Aprendidas -->
        <div class="rounded-2xl p-6 shadow-xl relative overflow-hidden flex flex-col justify-between" style="background: linear-gradient(135deg, #061122 0%, #030814 100%); border: 1px solid #1e3a5f;">
            <div>
                <span class="text-xs font-mono px-2.5 py-1 rounded-full" style="background-color: #0c1e3d; color: #60a5fa; border: 1px solid #1d4ed8;">Módulo 01</span>
                <h3 class="text-xl font-bold mt-3 text-white uppercase" style="font-family: 'Oswald', sans-serif;">Lições Aprendidas</h3>
                <p class="text-xs text-gray-300 mt-1.5 leading-relaxed">
                    Repositório de fainas e doutrina tática de submarinos.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-gray-800 flex items-center justify-between">
                <span class="text-xs font-mono text-gray-400">Acesso Total</span>
                <a href="{{ route('licoes.dashboard') }}" class="px-3.5 py-1.5 rounded-lg text-xs font-bold text-white transition-all shadow-lg" style="background-color: #1d4ed8;">
                    Acessar
                </a>
            </div>
        </div>

        <!-- Card 2: SIPA-SUB -->
        <div class="rounded-2xl p-6 shadow-xl relative overflow-hidden flex flex-col justify-between" style="background: linear-gradient(135deg, #181005 0%, #080501 100%); border: 1px solid #78350f;">
            <div>
                <span class="text-xs font-mono px-2.5 py-1 rounded-full" style="background-color: #271704; color: #fbbf24; border: 1px solid #b45309;">Módulo 02</span>
                <h3 class="text-xl font-bold mt-3 text-white uppercase" style="font-family: 'Oswald', sans-serif;">SIPA-SUB</h3>
                <p class="text-xs text-gray-300 mt-1.5 leading-relaxed">
                    Investigação, relatórios RELPREV e prevenção.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-gray-800 flex items-center justify-between">
                <span class="text-xs font-mono text-gray-400">Acesso Total</span>
                <a href="{{ route('sipasub.dashboard') }}" class="px-3.5 py-1.5 rounded-lg text-xs font-bold text-white transition-all shadow-lg" style="background-color: #b45309;">
                    Acessar
                </a>
            </div>
        </div>

        <!-- Card 3: Botão de Novo Militar -->
        <div class="rounded-2xl p-6 shadow-xl relative overflow-hidden flex flex-col justify-between" style="background: linear-gradient(135deg, #052e16 0%, #02120a 100%); border: 1px solid #166534;">
            <div>
                <span class="text-xs font-mono px-2.5 py-1 rounded-full" style="background-color: #064e3b; color: #a7f3d0; border: 1px solid #059669;">Gestão de Tripulação</span>
                <h3 class="text-xl font-bold mt-3 text-white uppercase" style="font-family: 'Oswald', sans-serif;">Cadastrar Militar</h3>
                <p class="text-xs text-emerald-200/80 mt-1.5 leading-relaxed">
                    Adicionar novo NIP com perfil de acesso tático e senha inicial.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-emerald-900/60 flex items-center justify-between">
                <span class="text-xs font-mono text-emerald-300">Novo Registro</span>
                <button wire:click="abrirModalCriar" type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-white transition-all shadow-lg hover:bg-emerald-600 flex items-center gap-1.5 cursor-pointer" style="background-color: #059669;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Novo Militar</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Tabela Geral de Militares e NIPs com CRUD Completo -->
    <div class="rounded-2xl p-6 sm:p-8 shadow-xl" style="background-color: #060f1d; border: 1px solid #1f2937;">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold uppercase tracking-tight text-white" style="font-family: 'Oswald', sans-serif;">
                    Gestão Completa de Militares & Credenciais (CRUD)
                </h2>
                <p class="text-xs text-gray-400 mt-1">
                    Visualize, cadastre, edite ou revogue credenciais militares autorizadas no ForçaOnline.
                </p>
            </div>

            <!-- Campo de Busca e Botão Novo -->
            <div class="flex items-center gap-3">
                <div class="relative">
                    <input wire:model.live.debounce.250ms="busca" type="text" placeholder="Buscar por Nome, NIP ou Email..." class="w-64 sm:w-80 px-3.5 py-2 pl-9 bg-black/80 border border-gray-700 rounded-xl text-xs text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 font-mono shadow-inner">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <button wire:click="abrirModalCriar" type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-white flex items-center gap-1.5 shadow-lg cursor-pointer shrink-0" style="background-color: #059669;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span class="hidden sm:inline">Adicionar</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-800">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-xs font-mono uppercase text-gray-400 border-b border-gray-800" style="background-color: #0b1526;">
                        <th class="py-3.5 px-4">NIP</th>
                        <th class="py-3.5 px-4">Nome do Militar</th>
                        <th class="py-3.5 px-4">Email de Notificação</th>
                        <th class="py-3.5 px-4">Módulo Autorizado</th>
                        <th class="py-3.5 px-4 text-right">Ações do Comando</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 font-mono text-xs">
                    @forelse($usuarios as $usr)
                        <tr class="hover:bg-gray-900/60 transition-colors {{ $usr['nip'] === '09097074' ? 'bg-purple-950/20' : '' }}">
                            <!-- NIP -->
                            <td class="py-4 px-4 font-bold text-yellow-400">
                                {{ $usr['nip'] }}
                                @if($usr['nip'] === '09097074')
                                    <span class="ml-1.5 px-2 py-0.5 rounded text-[10px] font-bold" style="background-color: #4338ca; color: #ffffff;">COMANDO</span>
                                @endif
                            </td>

                            <!-- Nome -->
                            <td class="py-4 px-4 text-white font-sans font-semibold">
                                {{ $usr['name'] }}
                            </td>

                            <!-- Email -->
                            <td class="py-4 px-4 text-gray-400">
                                {{ $usr['email'] }}
                            </td>

                            <!-- Módulo Atual -->
                            <td class="py-4 px-4">
                                @if($usr['modulo_acesso'] === 'licoes_aprendidas')
                                    <span class="px-2.5 py-1 rounded text-[11px] font-semibold" style="background-color: #0c1e3d; color: #60a5fa; border: 1px solid #1d4ed8;">
                                        Lições Aprendidas
                                    </span>
                                @elseif($usr['modulo_acesso'] === 'sipa_sub')
                                    <span class="px-2.5 py-1 rounded text-[11px] font-semibold" style="background-color: #271704; color: #fbbf24; border: 1px solid #b45309;">
                                        SIPA-SUB
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded text-[11px] font-semibold" style="background-color: #1e1b4b; color: #a5b4fc; border: 1px solid #4338ca;">
                                        Ambos os Módulos
                                    </span>
                                @endif
                            </td>

                            <!-- Ações CRUD (Editar, Excluir, Chavear) -->
                            <td class="py-4 px-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <!-- Botão Editar -->
                                    <button wire:click="abrirModalEditar({{ $usr['id'] }})" title="Editar Militar" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-blue-300 hover:text-white hover:bg-blue-900/60 border border-blue-800/80 transition-all flex items-center gap-1 cursor-pointer" style="background-color: #0c1e3d;">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Editar</span>
                                    </button>

                                    <!-- Botão Excluir (exceto o próprio usuário de comando) -->
                                    @if($usr['nip'] !== '09097074')
                                        <button wire:click="confirmarExclusao({{ $usr['id'] }})" title="Excluir Militar" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-red-300 hover:text-white hover:bg-red-900/60 border border-red-800/80 transition-all flex items-center gap-1 cursor-pointer" style="background-color: #450a0a;">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span>Excluir</span>
                                        </button>
                                    @else
                                        <span class="text-[11px] text-gray-500 italic px-2">Protegido</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500 font-mono text-xs">
                                Nenhum militar encontrado para os critérios informados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL DE CADASTRO / EDIÇÃO DE MILITAR -->
    @if($modalAberto)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-sm animate-fade-in">
            <div class="w-full max-w-lg rounded-2xl p-6 sm:p-8 shadow-2xl relative border border-gray-700" style="background-color: #0b1526;">
                <div class="flex items-center justify-between pb-4 border-b border-gray-800 mb-6">
                    <div>
                        <h3 class="text-xl font-bold uppercase tracking-tight text-white" style="font-family: 'Oswald', sans-serif;">
                            {{ $isEdicao ? 'Editar Militar Cadastrado' : 'Cadastrar Novo Militar' }}
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Preencha as informações militares e o módulo de acesso autorizado.
                        </p>
                    </div>
                    <button wire:click="fecharModal" type="button" class="text-gray-400 hover:text-white cursor-pointer p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="salvarUsuario" class="space-y-4 font-sans">
                    <!-- Nome Completo -->
                    <div>
                        <label class="block text-xs font-mono text-gray-300 mb-1" for="name">Nome Completo do Militar</label>
                        <input wire:model="name" id="name" type="text" required placeholder="Ex: 1º TEN SILVA" class="w-full px-3.5 py-2.5 bg-black/80 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500">
                        <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs text-red-400" />
                    </div>

                    <!-- NIP e Email em 2 colunas -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-mono text-gray-300 mb-1" for="nip">NIP (8 dígitos numéricos)</label>
                            <input wire:model="nip" id="nip" type="text" maxlength="8" inputmode="numeric" required placeholder="00000000" class="w-full px-3.5 py-2.5 bg-black/80 border border-gray-700 rounded-xl text-white text-sm font-mono tracking-widest focus:outline-none focus:border-blue-500">
                            <x-input-error :messages="$errors->get('nip')" class="mt-1 text-xs text-red-400" />
                        </div>

                        <div>
                            <label class="block text-xs font-mono text-gray-300 mb-1" for="email">E-mail de Notificação</label>
                            <input wire:model="email" id="email" type="email" required placeholder="militar@marinha.mil.br" class="w-full px-3.5 py-2.5 bg-black/80 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500">
                            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-400" />
                        </div>
                    </div>

                    <!-- Módulo Autorizado -->
                    <div>
                        <label class="block text-xs font-mono text-gray-300 mb-1" for="modulo_acesso">Módulo de Acesso Autorizado</label>
                        <select wire:model="modulo_acesso" id="modulo_acesso" class="w-full px-3.5 py-2.5 bg-black/80 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500">
                            <option value="licoes_aprendidas">Lições Aprendidas (Apenas Função 1)</option>
                            <option value="sipa_sub">SIPA-SUB (Apenas Função 2)</option>
                            <option value="ambos">Ambos os Módulos (Escolha no Login)</option>
                        </select>
                        <x-input-error :messages="$errors->get('modulo_acesso')" class="mt-1 text-xs text-red-400" />
                    </div>

                    <!-- Senha e Confirmação de Senha -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-mono text-gray-300 mb-1" for="password">
                                Senha de Acesso {{ $isEdicao ? '(Em branco para manter)' : '' }}
                            </label>
                            <input wire:model="password" id="password" type="password" {{ $isEdicao ? '' : 'required' }} placeholder="Mínimo 6 caracteres" class="w-full px-3.5 py-2.5 bg-black/80 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500 font-mono">
                            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-400" />
                        </div>

                        <div>
                            <label class="block text-xs font-mono text-gray-300 mb-1" for="password_confirmation">Confirmar Senha</label>
                            <input wire:model="password_confirmation" id="password_confirmation" type="password" {{ $isEdicao ? '' : 'required' }} placeholder="Repita a senha" class="w-full px-3.5 py-2.5 bg-black/80 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500 font-mono">
                        </div>
                    </div>

                    <!-- Botões de Ação do Modal -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-800">
                        <button wire:click="fecharModal" type="button" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-gray-400 hover:text-white border border-gray-700 transition-colors cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white shadow-lg transition-all transform hover:-translate-y-0.5 cursor-pointer" style="background-color: #059669;">
                            {{ $isEdicao ? 'Salvar Alterações' : 'Cadastrar Militar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL DE CONFIRMAÇÃO DE EXCLUSÃO -->
    @if($modalExclusaoAberto)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-sm animate-fade-in">
            <div class="w-full max-w-md rounded-2xl p-6 sm:p-8 shadow-2xl relative border border-red-900" style="background-color: #1a0808;">
                <div class="text-center">
                    <div class="w-12 h-12 rounded-full mx-auto flex items-center justify-center mb-4" style="background-color: #450a0a; border: 1px solid #991b1b;">
                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>

                    <h3 class="text-lg font-bold text-white uppercase tracking-tight" style="font-family: 'Oswald', sans-serif;">
                        Revogar Credencial Militar?
                    </h3>

                    <p class="text-xs text-gray-300 mt-2 leading-relaxed">
                        Tem certeza que deseja revogar o acesso e excluir os registros do militar <strong class="text-white">{{ $usuarioParaExcluirNome }}</strong>? Esta ação é irreversível.
                    </p>

                    <div class="flex items-center justify-center gap-3 mt-6">
                        <button wire:click="$set('modalExclusaoAberto', false)" type="button" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-400 hover:text-white border border-gray-700 cursor-pointer">
                            Cancelar
                        </button>
                        <button wire:click="excluirUsuario" type="button" class="px-5 py-2 rounded-xl text-xs font-bold text-white shadow-lg cursor-pointer" style="background-color: #dc2626;">
                            Confirmar Exclusão
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
