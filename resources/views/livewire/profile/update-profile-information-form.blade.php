<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ], [
            'name.required' => 'Informe o nome completo ou de guerra.',
            'email.required' => 'Informe o e-mail militar.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso por outro militar.',
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }
}; ?>

<section>
    <header class="pb-4 border-b border-gray-800/80 mb-6">
        <h2 class="text-base font-bold text-white uppercase tracking-wide" style="font-family: 'Oswald', sans-serif;">
            Dados Pessoais e Operacionais
        </h2>
        <p class="mt-1 text-xs text-gray-400">
            Atualize seu nome de guerra e endereço de e-mail cadastrado na Força.
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="space-y-4">
        <!-- NIP Militar (Fixo) -->
        <div>
            <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1 font-mono">
                NIP Militar (Número de Inscrição do Pessoal)
            </label>
            <input 
                type="text" 
                disabled 
                value="{{ auth()->user()->nip }}" 
                class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-gray-900/60 border border-gray-800 text-yellow-400 font-mono font-bold tracking-widest cursor-not-allowed"
            />
            <span class="text-[10px] text-gray-500 font-mono mt-1 block">O NIP é a chave primária de identificação militar e não pode ser alterado.</span>
        </div>

        <!-- Nome / Posto / Graduação -->
        <div>
            <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1 font-mono">
                Nome de Guerra / Posto / Graduação
            </label>
            <input 
                wire:model="name" 
                id="name" 
                type="text" 
                class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-gray-900 border border-gray-700 text-white focus:outline-none focus:border-yellow-500 font-mono" 
                required 
                autofocus 
                autocomplete="name" 
            />
            @error('name') <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- E-mail Operacional -->
        <div>
            <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1 font-mono">
                E-mail Operacional
            </label>
            <input 
                wire:model="email" 
                id="email" 
                type="email" 
                class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-gray-900 border border-gray-700 text-white focus:outline-none focus:border-yellow-500 font-mono" 
                required 
                autocomplete="username" 
            />
            @error('email') <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button 
                type="submit" 
                class="px-5 py-2 rounded-xl text-xs font-bold uppercase tracking-wider bg-gradient-to-r from-blue-700 to-blue-500 hover:from-blue-600 hover:to-blue-400 text-white shadow-lg shadow-blue-900/30 transition-all cursor-pointer"
            >
                Salvar Alterações
            </button>

            <span wire:loading wire:target="updateProfileInformation" class="text-xs text-gray-400 font-mono">
                Salvando dados...
            </span>

            <x-action-message class="text-xs text-emerald-400 font-mono font-semibold" on="profile-updated">
                ✓ Dados atualizados com sucesso.
            </x-action-message>
        </div>
    </form>
</section>
