<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('layouts.auth')] class extends Component
{
    public int $step = 1;

    #[Validate('required|string')]
    public string $nip = '';

    public string $password = '';
    public bool $remember = false;

    public string $sistemaSelecionado = '';
    public array $sistemasDisponiveis = [];

    // Passo 1: Verifica o NIP e define os acessos
    public function verificarNip()
    {
        $this->validate(['nip' => 'required|string']);

        $user = \App\Models\User::where('nip', $this->nip)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'nip' => 'NIP não encontrado ou sem permissão de acesso militar.',
            ]);
        }

        if ($user->modulo_acesso === 'ambos') {
            $this->sistemasDisponiveis = [
                'licoes_aprendidas' => 'Lições Aprendidas',
                'sipa_sub' => 'SIPA-SUB',
            ];
            $this->step = 2; // Vai para a tela de escolha
        } else {
            $this->sistemaSelecionado = $user->modulo_acesso;
            $this->step = 3; // Pula direto para a senha
        }
    }

    // Passo 2: Define o sistema escolhido pelo usuário
    public function escolherSistema($sistema)
    {
        $this->sistemaSelecionado = $sistema;
        $this->step = 3; // Vai para a tela de senha
    }

    // Passo 3: Autentica e redireciona
    public function login()
    {
        $this->validate(['password' => 'required|string']);
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['nip' => $this->nip, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'password' => 'A senha informada está incorreta.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        session(['modulo_ativo' => $this->sistemaSelecionado]);

        if ($this->sistemaSelecionado === 'licoes_aprendidas') {
            return redirect()->route('licoes.dashboard');
        }

        return redirect()->route('sipasub.dashboard');
    }

    public function voltar($paraPasso)
    {
        $this->step = $paraPasso;
        $this->password = '';
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());
        throw ValidationException::withMessages([
            'nip' => "Muitas tentativas. Tente novamente em {$seconds} segundos.",
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->nip).'|'.request()->ip());
    }
}; ?>

<div class="min-h-screen flex w-full bg-black text-white relative overflow-hidden select-none">
    <!-- Background Submarino Cobrindo Toda a Tela com Fusão Suave -->
    <div class="absolute inset-0 z-0">
        <div class="w-full h-full bg-cover bg-right lg:bg-center" style="background-image: url('{{ asset('img/submarino.webp') }}');"></div>
        <!-- Degradê Contínuo e Fluido para o Preto Puro (Sem divisórias secas) -->
        <div class="absolute inset-0 bg-gradient-to-r from-black via-black/90 sm:via-black/85 md:via-black/80 lg:via-black/70 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-black/60"></div>
        <div class="absolute inset-0 bg-black/40"></div>
    </div>

    <!-- Lado Esquerdo: Formulário de Autenticação -->
    <div class="w-full lg:w-5/12 xl:w-4/12 flex flex-col justify-between px-6 sm:px-12 lg:px-14 xl:px-16 py-8 sm:py-10 relative z-10 bg-black/70 lg:bg-transparent backdrop-blur-[2px] lg:backdrop-blur-none min-h-screen">
        <div class="w-full max-w-md mx-auto my-auto">
            <!-- Cabeçalho com Logo e Nome -->
            <div class="mb-8 sm:mb-10 flex items-center gap-5">
                <img src="{{ asset('img/brasao_0.png') }}" alt="Brasão ComForS" class="h-20 sm:h-24 w-auto drop-shadow-[0_10px_20px_rgba(0,0,0,0.8)]">
                <div>
                    <h1 class="text-4xl sm:text-5xl font-bold tracking-tight uppercase leading-none" style="font-family: 'Oswald', sans-serif;">
                        <span class="text-yellow-500 drop-shadow">Força</span><span class="text-white drop-shadow">Online</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-300/80 mt-2">Acesso restrito a militares e pessoal autorizado.</p>
                </div>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- PASSO 1: INFORMAR O NIP -->
            @if($step === 1)
                <form wire:submit="verificarNip" class="space-y-5 sm:space-y-6 animate-fade-in">
                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-1.5" for="nip">Informe seu NIP</label>
                        <input wire:model="nip" id="nip" type="text" required autofocus placeholder="09097074" class="block w-full px-4 py-3.5 bg-black/80 border border-gray-700/80 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-mono text-base shadow-inner">
                        <x-input-error :messages="$errors->get('nip')" class="mt-2 text-xs text-red-400" />
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-3.5 px-4 rounded-xl shadow-xl text-sm font-bold text-white bg-gradient-to-r from-blue-700 to-blue-500 hover:from-blue-600 hover:to-blue-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-black focus:ring-blue-500 transition-all transform hover:-translate-y-0.5">
                            <span wire:loading.remove wire:target="verificarNip">Continuar</span>
                            <span wire:loading wire:target="verificarNip">Verificando...</span>
                        </button>
                    </div>
                </form>
            @endif

            <!-- PASSO 2: ESCOLHER O SISTEMA (Apenas se tiver acesso a 'ambos') -->
            @if($step === 2)
                <div class="space-y-4 animate-fade-in">
                    <p class="text-sm text-gray-300 mb-3">Selecione o módulo que deseja acessar:</p>

                    @foreach($sistemasDisponiveis as $chave => $nome)
                        <button wire:click="escolherSistema('{{ $chave }}')" type="button" class="w-full flex items-center justify-between px-5 py-4 bg-black/80 border border-gray-700/80 rounded-xl hover:border-blue-500 hover:bg-blue-950/40 transition-all group shadow-lg">
                            <span class="font-semibold text-gray-200 group-hover:text-white">{{ $nome }}</span>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    @endforeach

                    <button wire:click="voltar(1)" type="button" class="mt-5 text-sm text-gray-400 hover:text-white transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Voltar para NIP
                    </button>
                </div>
            @endif

            <!-- PASSO 3: INFORMAR A SENHA -->
            @if($step === 3)
                <form wire:submit="login" class="space-y-5 sm:space-y-6 animate-fade-in">
                    <div class="flex items-center justify-between bg-black/80 px-4 py-3 rounded-xl border border-gray-700/80">
                        <div>
                            <span class="block text-xs text-gray-400">Módulo Selecionado:</span>
                            <span class="text-sm font-bold text-blue-400">
                                {{ $sistemaSelecionado === 'licoes_aprendidas' ? 'Lições Aprendidas' : 'SIPA-SUB' }}
                            </span>
                        </div>
                        <button type="button" wire:click="voltar(1)" class="text-xs text-gray-400 hover:text-white underline">Alterar</button>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-1.5" for="password">Senha de Acesso</label>
                        <input wire:model="password" id="password" type="password" required autofocus autocomplete="current-password" placeholder="senha123" class="block w-full px-4 py-3.5 bg-black/80 border border-gray-700/80 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-mono text-base shadow-inner">
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-400" />
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-3.5 px-4 rounded-xl shadow-xl text-sm font-bold text-white bg-gradient-to-r from-blue-700 to-blue-500 hover:from-blue-600 hover:to-blue-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-black focus:ring-blue-500 transition-all transform hover:-translate-y-0.5">
                            <span wire:loading.remove wire:target="login">Entrar no Sistema</span>
                            <span wire:loading wire:target="login">Autenticando...</span>
                        </button>
                    </div>
                </form>
            @endif

            <!-- Rodapé Institucional com Assinatura Permanente do Desenvolvedor -->
            <div class="mt-12 sm:mt-14 pt-6 border-t border-gray-800/80 text-center lg:text-left">
                <span class="text-xl font-bold tracking-wider text-yellow-500" style="font-family: 'Oswald', sans-serif;">ComForS - CLTI</span>
                <p class="text-xs text-gray-400 mt-1 font-mono">“USQUE AD SUB ACQUAM NAUTA SUM”</p>

                <!-- Assinatura Permanente e Irremovível do Desenvolvedor -->
                <div class="mt-4 pt-3 border-t border-gray-900 flex items-center justify-between text-[11px] text-gray-400 font-mono">
                    <span class="tracking-wide">
                        Desenvolvido por: <strong class="text-white font-bold tracking-wider underline decoration-yellow-500/80 underline-offset-2">2º SG PD SANDRO</strong>
                    </span>
                    <span class="text-[10px] text-yellow-500/80 uppercase font-semibold">ComForS</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Lado Direito: Card Flutuante Sobre a Classe do Submarino -->
    <div class="hidden lg:flex flex-1 relative z-10 items-end justify-end p-12 xl:p-16 pointer-events-none">
        <div class="max-w-md p-6 rounded-2xl bg-black/75 backdrop-blur-md border border-white/10 shadow-2xl text-left pointer-events-auto">
            <div class="flex items-center gap-2 text-yellow-500 font-mono text-xs font-semibold uppercase tracking-wider mb-2">
                <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span>Comando da Força de Submarinos</span>
            </div>
            <h3 class="text-xl font-bold text-white tracking-wide uppercase" style="font-family: 'Oswald', sans-serif;">
                Submarinos Classe Riachuelo (S-BR) & Classe Tupi
            </h3>
            <p class="text-xs text-gray-300 leading-relaxed mt-1.5">
                Plataforma digital integrada de conhecimento tático, doutrina operacional e segurança de imersão da Esquadra Brasileira na Base de Submarinos da Ilha da Madeira (BSIM).
            </p>
            <div class="mt-4 pt-3 border-t border-gray-800 flex items-center justify-between text-[11px] text-gray-400 font-mono">
                <span>S-40 • S-41 • S-42 • S-43</span>
                <span>Itaguaí - RJ</span>
            </div>
        </div>
    </div>
</div>
