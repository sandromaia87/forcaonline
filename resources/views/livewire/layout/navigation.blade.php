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

<nav x-data="{ open: false }" class="bg-black/90 border-b border-gray-800 text-white select-none">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo & Brand -->
                <div class="shrink-0 flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2.5">
                        <img src="{{ asset('img/brasao_0.png') }}" alt="Brasão ComForS" class="h-9 w-auto drop-shadow">
                        <span class="text-xl font-bold uppercase tracking-tight" style="font-family: 'Oswald', sans-serif;">
                            <span class="text-yellow-500">Força</span><span class="text-white">Online</span>
                        </span>
                    </a>
                </div>

                <!-- Navigation Links baseados no perfil do militar -->
                <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex">
                    @if(auth()->user()->temAcessoAoModulo('licoes_aprendidas'))
                        <a href="{{ route('licoes.dashboard') }}" wire:navigate class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('licoes.*') ? 'border-blue-500 text-blue-400 font-bold' : 'border-transparent text-gray-400 hover:text-gray-200 hover:border-gray-600' }}">
                            Lições Aprendidas
                        </a>
                    @endif

                    @if(auth()->user()->temAcessoAoModulo('sipa_sub'))
                        <a href="{{ route('sipasub.dashboard') }}" wire:navigate class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('sipasub.*') ? 'border-amber-500 text-amber-400 font-bold' : 'border-transparent text-gray-400 hover:text-gray-200 hover:border-gray-600' }}">
                            SIPA-SUB
                        </a>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <div class="text-xs font-semibold text-white">{{ auth()->user()->name }}</div>
                        <div class="text-[11px] font-mono text-gray-400">NIP: {{ auth()->user()->nip }}</div>
                    </div>

                    <button wire:click="logout" type="button" class="px-3 py-1.5 rounded-lg bg-red-950/70 hover:bg-red-900 border border-red-800 text-xs font-semibold text-red-300 transition-colors cursor-pointer flex items-center gap-1.5 shadow">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Sair</span>
                    </button>
                </div>
            </div>

            <!-- Hamburger Mobile -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-800 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu Mobile -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-800 bg-[#060f1d] px-4 pt-3 pb-4">
        <div class="space-y-2">
            @if(auth()->user()->temAcessoAoModulo('licoes_aprendidas'))
                <a href="{{ route('licoes.dashboard') }}" wire:navigate class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('licoes.*') ? 'bg-blue-950/80 text-blue-400 font-bold' : 'text-gray-300 hover:bg-gray-800' }}">
                    Lições Aprendidas
                </a>
            @endif

            @if(auth()->user()->temAcessoAoModulo('sipa_sub'))
                <a href="{{ route('sipasub.dashboard') }}" wire:navigate class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('sipasub.*') ? 'bg-amber-950/80 text-amber-400 font-bold' : 'text-gray-300 hover:bg-gray-800' }}">
                    SIPA-SUB
                </a>
            @endif
        </div>

        <div class="pt-4 pb-2 border-t border-gray-800 mt-3 flex items-center justify-between">
            <div>
                <div class="text-sm font-medium text-white">{{ auth()->user()->name }}</div>
                <div class="text-xs text-gray-400 font-mono">NIP: {{ auth()->user()->nip }}</div>
            </div>

            <button wire:click="logout" type="button" class="px-3 py-1.5 rounded-lg bg-red-950/70 border border-red-800 text-xs font-semibold text-red-300">
                Sair
            </button>
        </div>
    </div>
</nav>
