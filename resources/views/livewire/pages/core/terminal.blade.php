<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\User;

new #[Layout('layouts.app')] class extends Component
{
    public array $usuarios = [];
    public ?string $mensagemSucesso = null;

    public function mount(): void
    {
        // Acesso estritamente exclusivo do administrador geral (retorna 404 para ocultar existência)
        if (! auth()->check() || auth()->user()->nip !== '09097074') {
            abort(404);
        }

        session(['modulo_ativo' => 'sys_ops_k7']);
        $this->carregarUsuarios();
    }

    public function carregarUsuarios(): void
    {
        $this->usuarios = User::orderBy('id', 'asc')->get()->toArray();
    }

    public function alterarModuloUsuario(int $userId, string $novoModulo): void
    {
        $user = User::find($userId);
        if ($user) {
            $user->modulo_acesso = $novoModulo;
            $user->save();
            $this->mensagemSucesso = "Permissão do militar {$user->name} alterada para [{$novoModulo}].";
            $this->carregarUsuarios();
        }
    }
}; ?>

<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto select-none space-y-8">

    <!-- Mensagem de Sucesso -->
    @if($mensagemSucesso)
        <div class="p-4 rounded-xl text-sm font-semibold flex items-center justify-between" style="background-color: #064e3b; color: #a7f3d0; border: 1px solid #059669;">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ $mensagemSucesso }}</span>
            </div>
            <button wire:click="$set('mensagemSucesso', null)" class="text-xs underline cursor-pointer">Fechar</button>
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
                        Gerenciamento irrestrito de usuários, permissões táticas e auditoria da ForçaOnline.
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

    <!-- Atalhos de Acesso Rápido aos Módulos -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card Lições Aprendidas -->
        <div class="rounded-2xl p-6 shadow-xl relative overflow-hidden" style="background: linear-gradient(135deg, #061122 0%, #030814 100%); border: 1px solid #1e3a5f;">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-mono px-2.5 py-1 rounded-full" style="background-color: #0c1e3d; color: #60a5fa; border: 1px solid #1d4ed8;">Módulo 01</span>
                    <h3 class="text-xl font-bold mt-3 text-white uppercase" style="font-family: 'Oswald', sans-serif;">Lições Aprendidas</h3>
                    <p class="text-xs text-gray-300 mt-1.5 leading-relaxed">
                        Repositório doutrinário, fainas de imersão e manutenções preventivas dos submarinos.
                    </p>
                </div>
                <div class="p-2.5 rounded-xl" style="background-color: rgba(30, 58, 95, 0.4);">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-800 flex items-center justify-between">
                <span class="text-xs font-mono text-gray-400">Permissão: Acesso Total</span>
                <a href="{{ route('licoes.dashboard') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-white transition-all shadow-lg" style="background-color: #1d4ed8;">
                    Acessar Lições
                </a>
            </div>
        </div>

        <!-- Card SIPA-SUB -->
        <div class="rounded-2xl p-6 shadow-xl relative overflow-hidden" style="background: linear-gradient(135deg, #181005 0%, #080501 100%); border: 1px solid #78350f;">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-mono px-2.5 py-1 rounded-full" style="background-color: #271704; color: #fbbf24; border: 1px solid #b45309;">Módulo 02</span>
                    <h3 class="text-xl font-bold mt-3 text-white uppercase" style="font-family: 'Oswald', sans-serif;">SIPA-SUB</h3>
                    <p class="text-xs text-gray-300 mt-1.5 leading-relaxed">
                        Investigação, relatórios de prevenção (RELPREVs) e segurança de mergulho.
                    </p>
                </div>
                <div class="p-2.5 rounded-xl" style="background-color: rgba(120, 53, 15, 0.4);">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-800 flex items-center justify-between">
                <span class="text-xs font-mono text-gray-400">Permissão: Acesso Total</span>
                <a href="{{ route('sipasub.dashboard') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-white transition-all shadow-lg" style="background-color: #b45309;">
                    Acessar SIPA-SUB
                </a>
            </div>
        </div>
    </div>

    <!-- Tabela de Gerenciamento de Militares e NIPs -->
    <div class="rounded-2xl p-6 sm:p-8 shadow-xl" style="background-color: #060f1d; border: 1px solid #1f2937;">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold uppercase tracking-tight text-white" style="font-family: 'Oswald', sans-serif;">
                    Quadro Geral de Militares & Permissões
                </h2>
                <p class="text-xs text-gray-400 mt-1">
                    Defina individualmente os módulos aos quais cada NIP tem autorização de acesso.
                </p>
            </div>

            <span class="text-xs font-mono px-3 py-1.5 rounded-lg text-gray-300" style="background-color: #111827; border: 1px solid #374151;">
                Total: {{ count($usuarios) }} Registrados
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-800 text-xs font-mono uppercase text-gray-400">
                        <th class="py-3 px-4">NIP</th>
                        <th class="py-3 px-4">Nome do Militar</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Módulo Atual</th>
                        <th class="py-3 px-4 text-right">Alterar Acesso</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 font-mono text-xs">
                    @foreach($usuarios as $usr)
                        <tr class="hover:bg-gray-900/60 transition-colors {{ $usr['nip'] === '09097074' ? 'bg-purple-950/20' : '' }}">
                            <td class="py-4 px-4 font-bold text-yellow-400">
                                {{ $usr['nip'] }}
                                @if($usr['nip'] === '09097074')
                                    <span class="ml-1.5 px-2 py-0.5 rounded text-[10px] font-bold" style="background-color: #4338ca; color: #ffffff;">ADMIN</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-white font-sans font-semibold">
                                {{ $usr['name'] }}
                            </td>
                            <td class="py-4 px-4 text-gray-400">
                                {{ $usr['email'] }}
                            </td>
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
                            <td class="py-4 px-4 text-right">
                                @if($usr['nip'] !== '09097074')
                                    <div class="inline-flex items-center gap-1.5">
                                        <button wire:click="alterarModuloUsuario({{ $usr['id'] }}, 'licoes_aprendidas')" title="Permitir apenas Lições" class="px-2.5 py-1 rounded text-[11px] font-bold transition-all cursor-pointer {{ $usr['modulo_acesso'] === 'licoes_aprendidas' ? 'opacity-40 pointer-events-none' : 'hover:bg-blue-900 text-blue-300' }}" style="background-color: #0c1e3d; border: 1px solid #1d4ed8;">
                                            Lições
                                        </button>
                                        <button wire:click="alterarModuloUsuario({{ $usr['id'] }}, 'sipa_sub')" title="Permitir apenas SIPA-SUB" class="px-2.5 py-1 rounded text-[11px] font-bold transition-all cursor-pointer {{ $usr['modulo_acesso'] === 'sipa_sub' ? 'opacity-40 pointer-events-none' : 'hover:bg-amber-900 text-amber-300' }}" style="background-color: #271704; border: 1px solid #b45309;">
                                            SIPA
                                        </button>
                                        <button wire:click="alterarModuloUsuario({{ $usr['id'] }}, 'ambos')" title="Permitir Ambos" class="px-2.5 py-1 rounded text-[11px] font-bold transition-all cursor-pointer {{ $usr['modulo_acesso'] === 'ambos' ? 'opacity-40 pointer-events-none' : 'hover:bg-indigo-900 text-indigo-300' }}" style="background-color: #1e1b4b; border: 1px solid #4338ca;">
                                            Ambos
                                        </button>
                                    </div>
                                @else
                                    <span class="text-[11px] text-gray-500 font-mono italic">Imutável</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
