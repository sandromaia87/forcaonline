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

<div class="px-4 sm:px-6 lg:px-8 py-6 max-w-7xl mx-auto space-y-6 select-none" x-data="{}">

    <!-- Mensagens de Notificação -->
    @if($mensagemSucesso)
        <div class="p-4 rounded-xl text-sm font-semibold flex items-center justify-between shadow-xl bg-emerald-950/80 text-emerald-300 border border-emerald-700/80 animate-fade-in">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ $mensagemSucesso }}</span>
            </div>
            <button wire:click="$set('mensagemSucesso', null)" class="text-xs underline hover:text-white cursor-pointer">Fechar</button>
        </div>
    @endif

    @if($mensagemErro)
        <div class="p-4 rounded-xl text-sm font-semibold flex items-center justify-between shadow-xl bg-red-950/80 text-red-300 border border-red-700/80 animate-fade-in">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>{{ $mensagemErro }}</span>
            </div>
            <button wire:click="$set('mensagemErro', null)" class="text-xs underline hover:text-white cursor-pointer">Fechar</button>
        </div>
    @endif


    <!-- ========================================================================= -->
    <!-- 2. TABELA ARGON DASHBOARD PRO - CRUD COMPLETO DE USUÁRIOS/MILITARES       -->
    <!-- ========================================================================= -->
    <div class="p-6 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-2xl space-y-5">
        
        <!-- Header da Tabela com Barra de Ferramentas -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-800/80">
            <div>
                <h2 class="text-lg font-bold text-white uppercase tracking-wide" style="font-family: 'Oswald', sans-serif;">
                    Gestão Completa de Militares & Permissões
                </h2>
                <p class="text-xs text-gray-400 mt-0.5">
                    Controle de credenciais, NIPs de acesso e atribuição tática de módulos
                </p>
            </div>

            <div class="flex items-center gap-3">
                <!-- Campo de Busca em Tempo Real -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input 
                        wire:model.live.debounce.300ms="busca"
                        type="text" 
                        placeholder="Buscar por NIP, nome..." 
                        class="pl-9 pr-3 py-2 rounded-xl text-xs bg-gray-900/80 border border-gray-800 text-white placeholder-gray-500 focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 w-52 sm:w-64"
                    />
                </div>

                <!-- Botão Cadastrar Novo Militar -->
                <button 
                    wire:click="abrirModalCriar"
                    type="button" 
                    class="py-2 px-3.5 rounded-xl text-xs font-bold uppercase tracking-wider bg-gradient-to-r from-yellow-500 to-amber-600 hover:from-yellow-400 hover:to-amber-500 text-black shadow-lg shadow-yellow-500/20 transition-all flex items-center gap-2 cursor-pointer shrink-0"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Cadastrar Militar</span>
                </button>
            </div>
        </div>

        <!-- Tabela Estilo Argon -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-gray-400 uppercase tracking-wider font-mono border-b border-gray-800/80 text-[10px]">
                        <th class="pb-3 font-semibold">Militar / Nome</th>
                        <th class="pb-3 font-semibold">NIP</th>
                        <th class="pb-3 font-semibold">E-mail Cadastrado</th>
                        <th class="pb-3 font-semibold">Módulo Liberado</th>
                        <th class="pb-3 font-semibold text-right">Ações Operacionais</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60">
                    @forelse($usuarios as $u)
                        <tr class="hover:bg-gray-800/30 transition-colors {{ $u['nip'] === '09097074' ? 'bg-indigo-950/20' : '' }}">
                            <!-- Militar / Nome -->
                            <td class="py-3.5 font-bold text-white flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold font-mono shadow-md {{ $u['nip'] === '09097074' ? 'bg-indigo-600 text-white' : 'bg-gray-800 text-yellow-400 border border-gray-700' }}">
                                    {{ strtoupper(substr($u['name'], 0, 2)) }}
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-white flex items-center gap-1.5">
                                        <span>{{ $u['name'] }}</span>
                                        @if($u['nip'] === '09097074')
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-mono bg-indigo-900/60 text-indigo-300 border border-indigo-700/60">Admin</span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] text-gray-400 font-mono">ID #{{ $u['id'] }}</div>
                                </div>
                            </td>

                            <!-- NIP -->
                            <td class="py-3.5 font-mono text-sm font-bold text-yellow-400">
                                {{ $u['nip'] }}
                            </td>

                            <!-- E-mail -->
                            <td class="py-3.5 font-mono text-gray-300">
                                {{ $u['email'] }}
                            </td>

                            <!-- Módulo de Acesso -->
                            <td class="py-3.5">
                                @if(($u['modulo_acesso'] ?? '') === 'licoes_aprendidas')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-blue-950/60 text-blue-300 border border-blue-800/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                        <span>Lições Aprendidas</span>
                                    </span>
                                @elseif(($u['modulo_acesso'] ?? '') === 'sipa_sub')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-950/60 text-amber-300 border border-amber-800/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                        <span>SIPA-SUB</span>
                                    </span>
                                @elseif(($u['modulo_acesso'] ?? '') === 'ambos')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-950/60 text-emerald-300 border border-emerald-800/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        <span>Ambos os Módulos</span>
                                    </span>
                                @else
                                    <span class="text-gray-500 font-mono text-[10px]">Não definido</span>
                                @endif
                            </td>

                            <!-- Ações -->
                            <td class="py-3.5 text-right space-x-2">
                                <button 
                                    wire:click="abrirModalEditar({{ $u['id'] }})"
                                    type="button" 
                                    class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-gray-800 hover:bg-gray-700 text-gray-200 border border-gray-700 transition-colors cursor-pointer"
                                    title="Editar dados e redefinir senha"
                                >
                                    Editar
                                </button>

                                @if($u['nip'] !== '09097074')
                                    <button 
                                        wire:click="confirmarExclusao({{ $u['id'] }})"
                                        type="button" 
                                        class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-red-950/40 hover:bg-red-900/60 text-red-300 border border-red-800/60 transition-colors cursor-pointer"
                                        title="Revogar credencial e excluir militar"
                                    >
                                        Excluir
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400 font-mono text-xs">
                                Nenhum militar localizado com os critérios informados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-3 border-t border-gray-800/60 flex items-center justify-between text-xs text-gray-400 font-mono">
            <span>Total de registros: {{ count($usuarios) }} militar(es)</span>
            <span>ComForS - Controle Tático</span>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. MODAL DE CADASTRO / EDIÇÃO DE MILITAR                                  -->
    <!-- ========================================================================= -->
    @if($modalAberto)
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-[#060f1d] border border-gray-800 rounded-2xl w-full max-w-lg p-6 shadow-2xl space-y-5 animate-fade-in">
                
                <div class="flex items-center justify-between pb-3 border-b border-gray-800">
                    <div>
                        <h3 class="text-base font-bold text-white uppercase tracking-wide" style="font-family: 'Oswald', sans-serif;">
                            {{ $isEdicao ? 'Editar Credencial do Militar' : 'Cadastrar Novo Militar na ForçaOnline' }}
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $isEdicao ? 'Atualize as permissões ou defina uma nova senha' : 'O NIP de 8 dígitos será a chave de acesso exclusivo do militar' }}
                        </p>
                    </div>
                    <button wire:click="fecharModal" class="text-gray-400 hover:text-white p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="salvarUsuario" class="space-y-4">
                    
                    <!-- Nome Completo / Posto / Graduação -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1 font-mono">Nome / Posto / Graduação</label>
                        <input 
                            wire:model="name"
                            type="text" 
                            required
                            placeholder="Ex: 1º Tenente Silva"
                            class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-gray-900 border border-gray-700 text-white focus:outline-none focus:border-yellow-500"
                        />
                        @error('name') <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- NIP de 8 dígitos -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1 font-mono">NIP (8 Dígitos Numéricos)</label>
                        <input 
                            wire:model="nip"
                            type="text" 
                            maxlength="8"
                            inputmode="numeric"
                            required
                            placeholder="Ex: 12345678"
                            class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-gray-900 border border-gray-700 text-white font-mono text-yellow-400 font-bold focus:outline-none focus:border-yellow-500"
                            x-on:input="$event.target.value = $event.target.value.replace(/\D/g, '').slice(0, 8)"
                        />
                        @error('nip') <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- E-mail -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1 font-mono">E-mail Operacional</label>
                        <input 
                            wire:model="email"
                            type="email" 
                            required
                            placeholder="militar@marinha.mil.br"
                            class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-gray-900 border border-gray-700 text-white focus:outline-none focus:border-yellow-500"
                        />
                        @error('email') <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Módulo de Acesso -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1 font-mono">Permissão de Módulo</label>
                        <select 
                            wire:model="modulo_acesso"
                            class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-gray-900 border border-gray-700 text-white focus:outline-none focus:border-yellow-500"
                        >
                            <option value="licoes_aprendidas">Lições Aprendidas (Apenas Módulo 01)</option>
                            <option value="sipa_sub">SIPA-SUB (Apenas Módulo 02)</option>
                            <option value="ambos">Ambos os Módulos (Requer relogin para alternar)</option>
                        </select>
                        @error('modulo_acesso') <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Senha -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1 font-mono">
                                {{ $isEdicao ? 'Nova Senha (opcional)' : 'Senha de Acesso' }}
                            </label>
                            <input 
                                wire:model="password"
                                type="password" 
                                {{ $isEdicao ? '' : 'required' }}
                                placeholder="Mínimo 6 caracteres"
                                class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-gray-900 border border-gray-700 text-white focus:outline-none focus:border-yellow-500"
                            />
                            @error('password') <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1 font-mono">Confirmar Senha</label>
                            <input 
                                wire:model="password_confirmation"
                                type="password" 
                                {{ $isEdicao ? '' : 'required' }}
                                placeholder="Repita a senha"
                                class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-gray-900 border border-gray-700 text-white focus:outline-none focus:border-yellow-500"
                            />
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-800">
                        <button 
                            type="button" 
                            wire:click="fecharModal"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-300 hover:text-white bg-gray-800 hover:bg-gray-700 transition-colors"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="submit" 
                            class="px-5 py-2 rounded-xl text-xs font-bold uppercase tracking-wider bg-gradient-to-r from-yellow-500 to-amber-600 hover:from-yellow-400 hover:to-amber-500 text-black shadow-lg shadow-yellow-500/20 transition-all cursor-pointer"
                        >
                            {{ $isEdicao ? 'Salvar Alterações' : 'Confirmar Cadastro' }}
                        </button>
                    </div>

                </form>

            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 4. MODAL DE CONFIRMAÇÃO DE EXCLUSÃO                                       -->
    <!-- ========================================================================= -->
    @if($modalExclusaoAberto)
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-[#060f1d] border border-red-800/80 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4 animate-fade-in">
                <div class="w-12 h-12 rounded-2xl bg-red-950/80 border border-red-800/60 flex items-center justify-center text-red-400 mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>

                <div class="text-center">
                    <h3 class="text-base font-bold text-white uppercase tracking-wide" style="font-family: 'Oswald', sans-serif;">
                        Revogar Credencial Operacional?
                    </h3>
                    <p class="text-xs text-gray-300 mt-2">
                        Você está prestes a excluir permanentemente o militar <strong class="text-white">{{ $usuarioParaExcluirNome }}</strong>.
                    </p>
                    <p class="text-[11px] text-red-400 mt-1 font-mono">
                        Esta ação revoga imediatamente o acesso ao sistema ForçaOnline.
                    </p>
                </div>

                <div class="flex items-center justify-center gap-3 pt-3 border-t border-gray-800">
                    <button 
                        type="button" 
                        wire:click="$set('modalExclusaoAberto', false)"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-300 hover:text-white bg-gray-800 hover:bg-gray-700 transition-colors"
                    >
                        Cancelar
                    </button>
                    <button 
                        type="button" 
                        wire:click="excluirUsuario"
                        class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider bg-red-600 hover:bg-red-500 text-white shadow-lg shadow-red-600/30 transition-all cursor-pointer"
                    >
                        Confirmar Exclusão
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
