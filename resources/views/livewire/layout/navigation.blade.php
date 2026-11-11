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

                <!-- Módulo Lições Aprendidas: Início, Nova Lição e Accordion de Tabelas de Apoio -->
                @if(session('modulo_ativo') === 'licoes_aprendidas' || request()->routeIs('licoes.*'))
                    <!-- Início -->
                    <a href="{{ route('licoes.dashboard') }}" wire:navigate class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('licoes.dashboard') ? 'bg-gradient-to-r from-blue-900/60 to-blue-800/40 text-white border border-blue-600/40 shadow-lg shadow-blue-950/50' : 'text-gray-400 hover:text-white hover:bg-gray-900/60' }}">
                        <div class="w-8 h-8 rounded-lg {{ request()->routeIs('licoes.dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30' : 'bg-gray-800 text-gray-400' }} flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                        <span>Início</span>
                    </a>

                    <!-- Botão Nova Lição no Sidebar -->
                    <a href="{{ route('licoes.nova') }}" wire:navigate class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('licoes.nova') ? 'bg-gradient-to-r from-blue-900/60 to-blue-800/40 text-white border border-blue-600/40 shadow-lg shadow-blue-950/50' : 'text-gray-400 hover:text-white hover:bg-gray-900/60' }}">
                        <div class="w-8 h-8 rounded-lg {{ request()->routeIs('licoes.nova') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30' : 'bg-gray-800 text-gray-400' }} flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <span>Nova Lição</span>
                    </a>

                    <!-- Accordion Demonstrativo de Tabelas de Apoio / CRUDs -->
                    <div 
                        x-data="{ openCadastros: {{ (request()->routeIs('licoes.meios-navais') || request()->routeIs('licoes.subsistemas')) ? 'true' : 'false' }} }" 
                        class="pt-1"
                    >
                        <!-- Trigger Accordion -->
                        <button 
                            type="button" 
                            @click="openCadastros = !openCadastros" 
                            class="w-full flex items-center justify-between px-3.5 py-2 rounded-xl text-xs font-mono font-bold text-gray-400 hover:text-white hover:bg-gray-900/60 transition-all cursor-pointer group"
                        >
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-gray-800/80 group-hover:bg-blue-950 group-hover:text-blue-400 text-gray-400 flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                                <span class="uppercase tracking-wider">Tabelas de Apoio</span>
                            </div>
                            <svg 
                                class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200" 
                                :class="{ 'rotate-180': openCadastros }" 
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Links Internos do Accordion -->
                        <div x-show="openCadastros" x-transition class="pl-4 pr-1 py-1 space-y-1">
                            <!-- 1. CRUD Meios Navais & OMs -->
                            <a 
                                href="{{ route('licoes.meios-navais') }}" 
                                wire:navigate 
                                class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all duration-150 {{ request()->routeIs('licoes.meios-navais') ? 'bg-blue-900/50 text-blue-300 border border-blue-600/40' : 'text-gray-400 hover:text-white hover:bg-gray-900/40' }}"
                            >
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('licoes.meios-navais') ? 'bg-blue-400' : 'bg-gray-600' }}"></span>
                                <span>Meios Navais & OMs</span>
                            </a>

                            <!-- 2. CRUD Sistemas & Subsistemas -->
                            <a 
                                href="{{ route('licoes.subsistemas') }}" 
                                wire:navigate 
                                class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all duration-150 {{ request()->routeIs('licoes.subsistemas') ? 'bg-blue-900/50 text-blue-300 border border-blue-600/40' : 'text-gray-400 hover:text-white hover:bg-gray-900/40' }}"
                            >
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('licoes.subsistemas') ? 'bg-blue-400' : 'bg-gray-600' }}"></span>
                                <span>Sistemas & Subsistemas</span>
                            </a>
                        </div>
                    </div>
                @elseif(session('modulo_ativo') === 'sipa_sub' || request()->routeIs('sipasub.*'))
                    <a href="{{ route('sipasub.dashboard') }}" wire:navigate class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('sipasub.*') ? 'bg-gradient-to-r from-amber-900/60 to-amber-800/40 text-white border border-amber-600/40 shadow-lg shadow-amber-950/50' : 'text-gray-400 hover:text-white hover:bg-gray-900/60' }}">
                        <div class="w-8 h-8 rounded-lg {{ request()->routeIs('sipasub.*') ? 'bg-amber-600 text-white shadow-md shadow-blue-500/30' : 'bg-gray-800 text-gray-400' }} flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                        <span>Início</span>
                    </a>
                @elseif(auth()->user()->nip === '09097074')
                    <a href="{{ route('sys.ops.k7') }}" wire:navigate class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('sys.ops.*') ? 'bg-gradient-to-r from-indigo-900/60 to-indigo-800/40 text-white border border-indigo-600/40 shadow-lg shadow-indigo-950/50' : 'text-gray-400 hover:text-white hover:bg-gray-900/60' }}">
                        <div class="w-8 h-8 rounded-lg {{ request()->routeIs('sys.ops.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/30' : 'bg-gray-800 text-gray-400' }} flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <span>Gestão de Militares</span>
                    </a>
                @endif
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

    <!-- Backdrop escuro para mobile -->
    <div 
        x-show="sidebarOpen" 
        @click="sidebarOpen = false" 
        class="fixed inset-0 z-40 bg-black/80 lg:hidden backdrop-blur-sm"
        style="display: none;"
    ></div>

    <!-- ========================================================================= -->
    <!-- 2. TOP NAVBAR ARGON DASHBOARD (STICKY, COM BREADCRUMB E PERFIL)           -->
    <!-- ========================================================================= -->
    <header class="lg:ml-64 xl:ml-72 sticky top-0 z-30 bg-[#030712]/80 backdrop-blur-md border-b border-gray-800/80 px-6 sm:px-8 lg:px-10 py-4 transition-all">
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
                        @elseif(request()->routeIs('licoes.nova'))
                            <a href="{{ route('licoes.dashboard') }}" wire:navigate class="hover:underline">Lições Aprendidas</a>
                            <span>/</span>
                            <span class="text-blue-400 font-medium">Nova Lição</span>
                        @elseif(request()->routeIs('licoes.meios-navais'))
                            <a href="{{ route('licoes.dashboard') }}" wire:navigate class="hover:underline">Lições Aprendidas</a>
                            <span>/</span>
                            <span class="text-blue-400 font-medium">Meios Navais & OMs</span>
                        @elseif(request()->routeIs('licoes.subsistemas'))
                            <a href="{{ route('licoes.dashboard') }}" wire:navigate class="hover:underline">Lições Aprendidas</a>
                            <span>/</span>
                            <span class="text-blue-400 font-medium">Sistemas & Subsistemas</span>
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
                        @elseif(request()->routeIs('licoes.nova'))
                            CADASTRAR NOVA LIÇÃO APRENDIDA
                        @elseif(request()->routeIs('licoes.meios-navais'))
                            GESTÃO DE MEIOS NAVAIS E ORGANIZAÇÕES MILITARES
                        @elseif(request()->routeIs('licoes.subsistemas'))
                            GESTÃO DE SISTEMAS E SUBSISTEMAS OPERACIONAIS
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

            <!-- Direita: Perfil do Militar com Dropdown Interativo (Minha Conta & Sair) -->
            <div class="relative" x-data="{ userDropdownOpen: false }" @click.outside="userDropdownOpen = false">
                <!-- Botão Trigger do Dropdown -->
                <button 
                    @click="userDropdownOpen = !userDropdownOpen" 
                    type="button" 
                    class="flex items-center gap-2.5 pl-2 py-1.5 pr-3 rounded-xl bg-gray-950/60 border border-gray-800/80 hover:border-yellow-500/40 hover:bg-gray-900 transition-all cursor-pointer group text-left"
                    title="Menu da Conta Militar"
                >
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-yellow-500 to-amber-600 flex items-center justify-center text-black font-bold text-xs shadow-md group-hover:scale-105 transition-transform">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="hidden sm:block text-left">
                        <div class="text-xs font-bold text-white leading-tight truncate max-w-[140px] group-hover:text-yellow-400 transition-colors">
                            {{ auth()->user()->name }}
                        </div>
                        <div class="text-[10px] font-mono text-gray-400 leading-tight">
                            NIP: <span class="text-yellow-400 font-bold">{{ auth()->user()->nip }}</span>
                        </div>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-yellow-400 transition-transform duration-200" :class="{ 'rotate-180': userDropdownOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Menu Dropdown Suspenso Estilo Argon -->
                <div 
                    x-show="userDropdownOpen" 
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                    class="absolute right-0 mt-2 w-56 rounded-2xl bg-[#060f1d] border border-gray-800 shadow-2xl py-2 z-50 text-xs font-mono"
                    style="display: none;"
                >
                    <!-- Identificação do Militar no Topo do Dropdown -->
                    <div class="px-4 py-2.5 border-b border-gray-800/80">
                        <span class="text-[10px] uppercase text-gray-400 block font-semibold">Identificação Militar</span>
                        <div class="text-white font-bold truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[11px] text-yellow-400 font-bold mt-0.5">NIP: {{ auth()->user()->nip }}</div>
                    </div>

                    <!-- Opção Minha Conta -->
                    <div class="py-1">
                        <a 
                            href="{{ route('profile') }}" 
                            wire:navigate 
                            @click="userDropdownOpen = false"
                            class="flex items-center gap-2.5 px-4 py-2.5 text-gray-300 hover:text-white hover:bg-white/5 transition-colors cursor-pointer group"
                        >
                            <svg class="w-4 h-4 text-yellow-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>Minha Conta</span>
                        </a>
                    </div>

                    <!-- Divisor -->
                    <div class="border-t border-gray-800/80 my-1"></div>

                    <!-- Opção Sair com Isolamento Estrito -->
                    <div class="py-1">
                        <button 
                            wire:click="logout" 
                            type="button" 
                            class="w-full flex items-center gap-2.5 px-4 py-2 text-red-400 hover:text-red-300 hover:bg-red-950/30 transition-colors text-left cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span>Sair do Sistema</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </header>

</div>
