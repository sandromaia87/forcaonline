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

<div x-data="{ sidebarOpen: false }" class="select-none">
    
    <!-- Backdrop para Mobile -->
    <div 
        x-show="sidebarOpen" 
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="sidebarOpen = false" 
        class="fixed inset-0 bg-black/80 backdrop-blur-sm z-40 lg:hidden"
        style="display: none;"
    ></div>

    <!-- ========================================================================= -->
    <!-- 1. SIDENAV LATERAL ARGON (FIXA EM DESKTOP / DRAWER EM MOBILE)             -->
    <!-- ========================================================================= -->
    <aside 
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed inset-y-0 left-0 z-50 w-64 xl:w-72 bg-[#060f1d]/95 backdrop-blur-2xl border-r border-gray-800/80 flex flex-col justify-between transition-transform duration-300 ease-in-out shadow-2xl"
    >
        <!-- Topo da Sidenav: Marca & Identidade ComForS -->
        <div>
            <!-- Header Brand -->
            <div class="px-6 py-5 border-b border-gray-800/80 flex items-center justify-between">
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 group">
                    <img src="{{ asset('img/brasao_0.png') }}" alt="Brasão ComForS" class="h-10 w-auto object-contain drop-shadow-[0_2px_8px_rgba(0,0,0,0.8)] group-hover:scale-105 transition-transform">
                    <div>
                        <div class="text-xl font-bold uppercase tracking-tight leading-none" style="font-family: 'Oswald', sans-serif;">
                            <span class="text-yellow-500" style="color: #eab308;">Força</span><span class="text-white">Online</span>
                        </div>
                        <div class="text-[10px] font-mono uppercase tracking-widest text-gray-400 mt-0.5">ComForS • Marinha</div>
                    </div>
                </a>

                <!-- Fechar em mobile -->
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white p-1 rounded-lg hover:bg-gray-800/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Indicador do Módulo Operacional Atual (Isolamento estrito entre módulos) -->
            <div class="px-5 py-4">
                @if(auth()->user()->nip === '09097074')
                    <div class="p-3 rounded-xl bg-indigo-950/40 border border-indigo-800/60 shadow-inner flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-600/30 border border-indigo-500/40 flex items-center justify-center text-indigo-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase font-mono tracking-wider text-indigo-400 font-semibold">Console Central</div>
                            <div class="text-xs font-bold text-white tracking-wide">Comando Supremo</div>
                        </div>
                    </div>
                @elseif(request()->routeIs('licoes.*'))
                    <div class="p-3 rounded-xl bg-blue-950/40 border border-blue-800/60 shadow-inner flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-600/30 border border-blue-500/40 flex items-center justify-center text-blue-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase font-mono tracking-wider text-blue-400 font-semibold">Módulo Operacional</div>
                            <div class="text-xs font-bold text-white tracking-wide">Lições Aprendidas</div>
                        </div>
                    </div>
                @elseif(request()->routeIs('sipasub.*'))
                    <div class="p-3 rounded-xl bg-amber-950/40 border border-amber-800/60 shadow-inner flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-600/30 border border-amber-500/40 flex items-center justify-center text-amber-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase font-mono tracking-wider text-amber-400 font-semibold">Módulo de Segurança</div>
                            <div class="text-xs font-bold text-white tracking-wide">SIPA-SUB</div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Navegação Oficial: Apenas o item ativo da tela de boas-vindas do módulo -->
            <nav class="px-4 space-y-1.5 mt-2">
                <div class="px-3 pb-2 text-[10px] font-mono uppercase tracking-widest text-gray-400 font-bold">
                    Navegação
                </div>

                @if(request()->routeIs('licoes.*'))
                    <a href="{{ route('licoes.dashboard') }}" wire:navigate class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 bg-gradient-to-r from-blue-900/60 to-blue-800/40 text-white border border-blue-600/40 shadow-lg shadow-blue-950/50">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-500/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                        <span>Início / Boas-Vindas</span>
                    </a>
                @elseif(request()->routeIs('sipasub.*'))
                    <a href="{{ route('sipasub.dashboard') }}" wire:navigate class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 bg-gradient-to-r from-amber-900/60 to-amber-800/40 text-white border border-amber-600/40 shadow-lg shadow-amber-950/50">
                        <div class="w-8 h-8 rounded-lg bg-amber-600 text-white flex items-center justify-center shadow-md shadow-amber-500/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                        <span>Início / Boas-Vindas</span>
                    </a>
                @elseif(auth()->user()->nip === '09097074')
                    <a href="{{ route('sys.ops.k7') }}" wire:navigate class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 bg-gradient-to-r from-indigo-900/60 to-indigo-800/40 text-white border border-indigo-600/40 shadow-lg shadow-indigo-950/50">
                        <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center shadow-md shadow-indigo-500/30">
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
                        @if(request()->routeIs('licoes.*'))
                            <span class="text-blue-400 font-medium">Lições Aprendidas</span>
                        @elseif(request()->routeIs('sipasub.*'))
                            <span class="text-amber-400 font-medium">SIPA-SUB</span>
                        @elseif(auth()->user()->nip === '09097074')
                            <span class="text-indigo-400 font-medium">Console Central</span>
                        @endif
                    </div>
                    <!-- Título Atual -->
                    <h1 class="text-sm sm:text-base font-bold text-white tracking-wide" style="font-family: 'Oswald', sans-serif;">
                        @if(request()->routeIs('licoes.*'))
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
                
                <!-- Perfil do Militar Conectado -->
                <div class="flex items-center gap-2.5 pl-2">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-yellow-500 to-amber-600 flex items-center justify-center text-black font-bold text-xs shadow-md">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="hidden sm:block text-left">
                        <div class="text-xs font-bold text-white leading-tight truncate max-w-[140px]">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] font-mono text-gray-400 leading-tight">
                            NIP: <span class="text-yellow-400 font-bold">{{ auth()->user()->nip }}</span>
                        </div>
                    </div>
                </div>

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
