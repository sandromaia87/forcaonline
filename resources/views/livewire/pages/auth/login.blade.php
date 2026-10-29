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

        // Busca o usuário no banco
        $user = \App\Models\User::where('nip', $this->nip)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'nip' => 'NIP não encontrado ou sem permissão de acesso.',
            ]);
        }

        // Lógica de roteamento baseada na permissão
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

        // Guarda o módulo escolhido na sessão (útil para o menu lateral depois)
        session(['modulo_ativo' => $this->sistemaSelecionado]);

        // Redireciona para o painel isolado correto
        if ($this->sistemaSelecionado === 'licoes_aprendidas') {
            return redirect()->route('licoes.dashboard');
        }

        return redirect()->route('sipasub.dashboard');
    }

    public function voltar($paraPasso)
    {
        $this->step = $paraPasso;
        $this->password = ''; // Limpa a senha por segurança
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new \Illuminate\Auth\Events\Lockout(request()));
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

<div class="min-h-screen flex w-full bg-[#030b17]">

    <!-- Lado Esquerdo (Formulário) -->
    <div class="w-full lg:w-1/3 flex flex-col justify-center px-8 sm:px-16 lg:px-24 xl:px-32 relative z-10 bg-[#060f1d]">
        <div class="w-full max-w-md mx-auto">

            <!-- Cabeçalho com Logo e Nome (Testando com a fonte Oswald no CDN) -->
            <div class="mb-10 flex items-center gap-6">
                <img src="{{ asset('img/brasao_0.png') }}" alt="Brasão ComForS" class="h-24 w-auto drop-shadow-lg">
                <div>
                    <h1 class="text-4xl lg:text-5xl font-bold tracking-tight uppercase" style="font-family: 'Oswald', sans-serif;">
                        <span class="text-yellow-500">Força</span><span class="text-white">Online</span>
                    </h1>
                    <p class="text-xs lg:text-sm text-gray-400 mt-2">Acesso restrito a militares e pessoal autorizado.</p>
                </div>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- PASSO 1: INFORMAR O NIP -->
            @if($step === 1)
                <form wire:submit="verificarNip" class="space-y-6 animate-fade-in">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1" for="nip">Informe seu NIP</label>
                        <input wire:model="nip" id="nip" type="text" required autofocus class="block w-full px-4 py-3 bg-[#030b17] border border-gray-800 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-shadow">
                        <x-input-error :messages="$errors->get('nip')" class="mt-2 text-xs text-red-400" />
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-lg shadow-lg text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-[#060f1d] focus:ring-blue-500 transition-all transform hover:-translate-y-0.5">
                            <span wire:loading.remove wire:target="verificarNip">Continuar</span>
                            <span wire:loading wire:target="verificarNip">Verificando...</span>
                        </button>
                    </div>
                </form>
            @endif

            <!-- PASSO 2: ESCOLHER O SISTEMA (Apenas se tiver acesso a 'ambos') -->
            @if($step === 2)
                <div class="space-y-4 animate-fade-in">
                    <p class="text-sm text-gray-400 mb-4">Selecione o módulo que deseja acessar:</p>

                    @foreach($sistemasDisponiveis as $chave => $nome)
                        <button wire:click="escolherSistema('{{ $chave }}')" type="button" class="w-full flex items-center justify-between px-6 py-4 bg-[#030b17] border border-gray-800 rounded-lg hover:border-blue-500 hover:bg-[#0a192f] transition-all group">
                            <span class="font-semibold text-gray-300 group-hover:text-white">{{ $nome }}</span>
                            <svg class="w-5 h-5 text-gray-600 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    @endforeach

                    <button wire:click="voltar(1)" type="button" class="mt-6 text-sm text-gray-500 hover:text-gray-300 transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Voltar para NIP
                    </button>
                </div>
            @endif

            <!-- PASSO 3: INFORMAR A SENHA -->
            @if($step === 3)
                <form wire:submit="login" class="space-y-6 animate-fade-in">

                    <!-- Badge indicando o sistema selecionado -->
                    <div class="flex items-center justify-between bg-[#030b17] px-4 py-3 rounded-lg border border-gray-800">
                        <div>
                            <span class="block text-xs text-gray-500">Módulo Selecionado:</span>
                            <span class="text-sm font-bold text-blue-400">
                                {{ $sistemaSelecionado === 'licoes_aprendidas' ? 'Lições Aprendidas' : 'SIPA-SUB' }}
                            </span>
                        </div>
                        <button type="button" wire:click="voltar(1)" class="text-xs text-gray-400 hover:text-white underline">Alterar</button>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1" for="password">Senha de Acesso</label>
                        <input wire:model="password" id="password" type="password" required autofocus autocomplete="current-password" class="block w-full px-4 py-3 bg-[#030b17] border border-gray-800 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-shadow">
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-400" />
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-lg shadow-lg text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-[#060f1d] focus:ring-blue-500 transition-all transform hover:-translate-y-0.5">
                            <span wire:loading.remove wire:target="login">Entrar no Sistema</span>
                            <span wire:loading wire:target="login">Autenticando...</span>
                        </button>
                    </div>
                </form>
            @endif

            <div class="mt-16 pt-8 border-t border-gray-800 text-center lg:text-left">
                <span class="text-xl font-bold tracking-wider text-yellow-500">ComForS - CLTI</span>
                <p class="text-xs text-gray-500 mt-1">“USQUE AD SUB ACQUAM NAUTA SUM”</p>
            </div>
        </div>
    </div>

    <!-- Lado Direito: Imagem com Gradiente de Fusão -->
    <div class="hidden lg:block w-2/3 relative overflow-hidden bg-[#030b17]">
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('img/submarino.webp') }}');"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#060f1d] via-[#060f1d]/60 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-[#030b17]/40 via-transparent to-[#030b17]/80"></div>
    </div>
</div>
