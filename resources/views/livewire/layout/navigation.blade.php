<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div x-data="{ sidebarOpen: false }">
    <!-- ========================================================================= -->
    <!-- 1. ARGON DASHBOARD SIDENAV (LATERAL FIXA)                                 -->
    <!-- ========================================================================= -->
    <aside 
        class="fixed inset-y-0 left-0 z-50 w-64 xl:w-72 bg-[#040915] border-r border-gray-800/80 flex flex-col justify-between transition-transform duration-300 ease-in-out"
        :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full lg:translate-x-0': !sidebarOpen }"
    >
        <div>
            <!-- Header da Sidenav com Brasão e Identidade ComForS -->
            <div class="p-6 border-b border-gray-800/80 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-1.5 rounded-xl bg-black/60 border border-yellow-500/30 shrink-0">
                        <img src="{{ asset('img/brasao_0.png') }}" alt="Brasão ComForS" class="h-9 w-auto object-contain">
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-oswald text-lg font-bold tracking-tight text-yellow-500">FORÇA</span>
                            <span class="font-oswald text-lg font-bold tracking-tight text-white">ONLINE</span>
                        </div>
                        <div class="text-[10px] font-mono text-gray-400 uppercase tracking-wider">ComForS • CLTI</div>
                    </div>
                </div>

                <!-- Botão Fechar Mobile -->
                <button @click="sidebarOpen = false" class="lg:hidden p-1.5 text-gray-400 hover:text-white rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Menus Principais de Navegação Estilo Argon -->
            <nav class="p-4 space-y-1.5">
                <div class="px-3 pb-2 text-[10px] font-mono uppercase tracking-wider text-gray-500">
                    Navegação
                </div>

                <!-- Item Início / Dashboard Específico do Módulo Ativo -->
                @if(session('modulo_ativo') === 'licoes_aprendidas' || request()->routeIs('licoes.*'))
                    <a href="{{ route('licoes.dashboard') }}" wire:navigate class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('licoes.*') ? 'bg-gradient-to-r from-blue-900/60 to-blue-800/40 text-white border border-blue-600/40 shadow-lg shadow-blue-950/50' : 'text-gray-400 hover:text-white hover:bg-gray-900/60' }}">
                        <div class="w-8 h-8 rounded-lg {{ request()->routeIs('licoes.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30' : 'bg-gray-800 text-gray-400' }} flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                        <span>Início / Boas-Vindas</span>
                    </a>
                @elseif(session('modulo_ativo') === 'sipa_sub' || request()->routeIs('sipasub.*'))
                    <a href="{{ route('sipasub.dashboard') }}" wire:navigate class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('sipasub.*') ? 'bg-gradient-to-r from-amber-900/60 to-amber-800/40 text-white border border-amber-600/40 shadow-lg shadow-amber-950/50' : 'text-gray-400 hover:text-white hover:bg-gray-900/60' }}">
                        <div class="w-8 h-8 rounded-lg {{ request()->routeIs('sipasub.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-500/30' : 'bg-gray-800 text-gray-400' }} flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                        <span>Início / Boas-Vindas</span>
                    </a>
                @elseif(auth()->user()->nip === '09097074')
                    <a href="{{ route('sys.ops.k7') }}" wire:navigate class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('sys.ops.*') ? 'bg-gradient-to-r from-indigo-900/60 to-indigo-800/40 text-white border border-indigo-600/40 shadow-lg shadow-indigo-950/50' : 'text-gray-400 hover:text-white hover:bg-gray-900/60' }}">
                        <div class="w-8 h-8 rounded-lg {{ request()->routeIs('sys.ops.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/30' : 'bg-gray-800 text-gray-400' }} flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <span>Gestão de Militares</span>
                    </a>
                @endif

                <!-- Link Minha Conta / Painel do Militar -->
                <a href="{{ route('profile') }}" wire:navigate class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('profile') ? 'bg-gradient-to-r from-gray-800 to-gray-700 text-white border border-gray-600 shadow-md' : 'text-gray-400 hover:text-white hover:bg-gray-900/60' }}">
                    <div class="w-8 h-8 rounded-lg {{ request()->routeIs('profile') ? 'bg-yellow-500 text-black shadow-md' : 'bg-gray-800 text-yellow-400' }} flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <span>Minha Conta</span>
                </a>
            </nav>
        </div>

        <!-- Card Inferior Argon: Cartão Institucional ComForS -->
        <div class="p-4 m-4 rounded-2xl bg-gradient-to-br from-[#0c182c] to-[#040813] border border-yellow-500/20 shadow-xl relative overflow-hidden">
            <div class="flex items-center gap-3 relative z-10">
                <div class="w-10 h-10 rounded-xl bg-yellow-500/10 border border-yellow-500/30 flex items-center justify-center shrink-0">
                    <img src="{{ asset('img/brasao_0.png') }}" alt="" class="h-6 w-auto object-contain">
                </div>
                <div>
                    <div class="text-xs font-bold text-white uppercase tracking-wider font-mono">ComForS • CLTI</div>
                    <div class="text-[10px] text-yellow-500/90 font-medium">Força de Submarinos</div>
                </div>
            </div>
            <p class="text-[11px] text-gray-400 mt-2.5 italic relative z-10 leading-snug">
                “USQUE AD SUB ACQUAM NAUTA SUM”
            </p>
        </div>
    </aside>

    <!-- ========================================================================= -->
    <!-- 2. TOP NAVBAR ARGON DASHBOARD (STICKY, COM BREADCRUMB E PERFIL)           -->
    <!-- ========================================================================= -->
    <header class="lg:pl-64 xl:pl-72 sticky top-0 z-30 bg-[#030712]/80 backdrop-blur-md border-b border-gray-800/80 px-4 sm:px-6 lg:px-8 py-3.5 transition-all">
        <div class="flex items-center justify-between gap-4">
            
            <!-- Esquerda: Botão Menu Mobile & Breadcrumbs Estilo Argon -->
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-gray-400 hover:text-white bg-gray-900 border border-gray-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <div>
                    <!-- Breadcrumbs -->
                    <div class="flex items-center gap-1.5 text-xs text-gray-400 font-mono">
                        <span>Páginas</span>
                        <span>/</span>
                        @if(request()->routeIs('profile'))
                            <span class="text-yellow-400 font-medium">Minha Conta</span>
                        @elseif(request()->routeIs('licoes.*'))
                            <span class="text-blue-400 font-medium">Lições Aprendidas</span>
                        @elseif(request()->routeIs('sipasub.*'))
                            <span class="text-amber-400 font-medium">SIPA-SUB</span>
                        @elseif(auth()->user()->nip === '09097074')
                            <span class="text-indigo-400 font-medium">Console Central</span>
                        @endif
                    </div>
                    <!-- Título Atual -->
                    <h1 class="text-sm sm:text-base font-bold text-white tracking-wide" style="font-family: 'Oswald', sans-serif;">
                        @if(request()->routeIs('profile'))
                            PAINEL DO MILITAR / MINHA CONTA
                        @elseif(request()->routeIs('licoes.*'))
                            MÓDULO LIÇÕES APRENDIDAS
                        @elseif(request()->routeIs('sipasub.*'))
                            MÓDULO SIPA-SUB
                        @elseif(auth()->user()->nip === '09097074')
                            TERMINAL GERAL DE COMANDO
                        @else
                            DASHBOARD
                        @endif
                    </h1>
                </div>
            </div>

            <!-- Direita: Identificação do Militar + Botão Sair -->
            <div class="flex items-center gap-3 sm:gap-4">
                
                <!-- Perfil do Militar Conectado (Clicável para ir a Minha Conta) -->
                <a href="{{ route('profile') }}" wire:navigate class="flex items-center gap-2.5 pl-2 hover:opacity-85 transition-opacity cursor-pointer group" title="Acessar Minha Conta">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-yellow-500 to-amber-600 flex items-center justify-center text-black font-bold text-xs shadow-md group-hover:scale-105 transition-transform">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="hidden sm:block text-left">
                        <div class="text-xs font-bold text-white leading-tight truncate max-w-[140px] group-hover:text-yellow-400 transition-colors">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] font-mono text-gray-400 leading-tight">
                            NIP: <span class="text-yellow-400 font-bold">{{ auth()->user()->nip }}</span>
                        </div>
                    </div>
                </a>

                <!-- Botão Sair Obrigatório (Isolamento estrito entre módulos) -->
                <button 
                    wire:click="logout" 
                    type="button" 
                    title="Para trocar de módulo, é necessário sair e autenticar novamente" 
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all duration-200 cursor-pointer flex items-center gap-1.5 shadow-md bg-red-950/40 hover:bg-red-900/60 text-red-300 border border-red-800/80 hover:border-red-600"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span class="hidden sm:inline">Sair</span>
                </button>
            </div>

        </div>
    </header>
</div>
