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

<nav x-data="{ open: false }" class="border-b text-white select-none relative z-50" style="background-color: #000000; border-color: #1f2937;">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center gap-6">
                <!-- Logo & Brand -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3">
                        <img src="{{ asset('img/brasao_0.png') }}" alt="Brasão ComForS" class="h-10 w-auto drop-shadow object-contain">
                        <span class="text-xl font-bold uppercase tracking-tight" style="font-family: 'Oswald', sans-serif;">
                            <span class="text-yellow-500" style="color: #eab308;">Força</span><span class="text-white" style="color: #ffffff;">Online</span>
                        </span>
                    </a>
                </div>

                <!-- Navigation Links baseados no perfil do militar -->
                <div class="hidden sm:flex sm:items-center sm:gap-2">
                    @if(auth()->user()->temAcessoAoModulo('licoes_aprendidas'))
                        <a href="{{ route('licoes.dashboard') }}" wire:navigate class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('licoes.*') ? 'bg-blue-950 text-blue-400 border border-blue-700 font-bold' : 'text-gray-400 hover:text-white hover:bg-gray-900' }}" style="{{ request()->routeIs('licoes.*') ? 'background-color: #0c1e3d; color: #60a5fa; border: 1px solid #1d4ed8;' : 'color: #9ca3af;' }}">
                            Lições Aprendidas
                        </a>
                    @endif

                    @if(auth()->user()->temAcessoAoModulo('sipa_sub'))
                        <a href="{{ route('sipasub.dashboard') }}" wire:navigate class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('sipasub.*') ? 'bg-amber-950 text-amber-400 border border-amber-700 font-bold' : 'text-gray-400 hover:text-white hover:bg-gray-900' }}" style="{{ request()->routeIs('sipasub.*') ? 'background-color: #271704; color: #fbbf24; border: 1px solid #b45309;' : 'color: #9ca3af;' }}">
                            SIPA-SUB
                        </a>
                    @endif
                </div>
            </div>

            <!-- Dados do Militar e Botão Sair -->
            <div class="hidden sm:flex sm:items-center sm:gap-4">
                <div class="text-right">
                    <div class="text-xs font-semibold text-white" style="color: #ffffff;">{{ auth()->user()->name }}</div>
                    <div class="text-[11px] font-mono text-gray-400" style="color: #9ca3af;">NIP: <span class="text-yellow-400 font-bold" style="color: #facc15;">{{ auth()->user()->nip }}</span></div>
                </div>

                <button wire:click="logout" type="button" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors cursor-pointer flex items-center gap-1.5 shadow" style="background-color: #450a0a; color: #fca5a5; border: 1px solid #991b1b;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Sair</span>
                </button>
            </div>

            <!-- Hamburger Mobile -->
            <div class="flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-800 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu Mobile -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-800 px-4 pt-3 pb-4" style="background-color: #060f1d;">
        <div class="space-y-2">
            @if(auth()->user()->temAcessoAoModulo('licoes_aprendidas'))
                <a href="{{ route('licoes.dashboard') }}" wire:navigate class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('licoes.*') ? 'text-blue-400 font-bold' : 'text-gray-300' }}">
                    Lições Aprendidas
                </a>
            @endif

            @if(auth()->user()->temAcessoAoModulo('sipa_sub'))
                <a href="{{ route('sipasub.dashboard') }}" wire:navigate class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('sipasub.*') ? 'text-amber-400 font-bold' : 'text-gray-300' }}">
                    SIPA-SUB
                </a>
            @endif
        </div>

        <div class="pt-4 pb-2 border-t border-gray-800 mt-3 flex items-center justify-between">
            <div>
                <div class="text-sm font-medium text-white" style="color: #ffffff;">{{ auth()->user()->name }}</div>
                <div class="text-xs font-mono text-gray-400" style="color: #9ca3af;">NIP: <span class="text-yellow-400 font-bold" style="color: #facc15;">{{ auth()->user()->nip }}</span></div>
            </div>

            <button wire:click="logout" type="button" class="px-3 py-1.5 rounded-lg text-xs font-semibold" style="background-color: #450a0a; color: #fca5a5; border: 1px solid #991b1b;">
                Sair
            </button>
        </div>
    </div>
</nav>
