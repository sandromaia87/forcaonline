<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ], [
                'current_password.required' => 'Informe a sua senha atual.',
                'current_password.current_password' => 'A senha atual informada está incorreta.',
                'password.required' => 'Informe a nova senha.',
                'password.confirmed' => 'A confirmação da nova senha não confere.',
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');
            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <header class="pb-4 border-b border-gray-800/80 mb-6">
        <h2 class="text-base font-bold text-white uppercase tracking-wide" style="font-family: 'Oswald', sans-serif;">
            Segurança & Senha Militar
        </h2>
        <p class="mt-1 text-xs text-gray-400">
            Mantenha sua senha segura para garantir a integridade do seu acesso operacional ao ComForS.
        </p>
    </header>

    <form wire:submit="updatePassword" class="space-y-4">
        <div>
            <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1 font-mono">
                Senha Atual
            </label>
            <input 
                wire:model="current_password" 
                id="update_password_current_password" 
                type="password" 
                class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-gray-900 border border-gray-700 text-white focus:outline-none focus:border-yellow-500 font-mono" 
                autocomplete="current-password" 
            />
            @error('current_password') <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1 font-mono">
                    Nova Senha
                </label>
                <input 
                    wire:model="password" 
                    id="update_password_password" 
                    type="password" 
                    class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-gray-900 border border-gray-700 text-white focus:outline-none focus:border-yellow-500 font-mono" 
                    autocomplete="new-password" 
                />
                @error('password') <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1 font-mono">
                    Confirmar Nova Senha
                </label>
                <input 
                    wire:model="password_confirmation" 
                    id="update_password_password_confirmation" 
                    type="password" 
                    class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-gray-900 border border-gray-700 text-white focus:outline-none focus:border-yellow-500 font-mono" 
                    autocomplete="new-password" 
                />
                @error('password_confirmation') <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button 
                type="submit" 
                class="px-5 py-2 rounded-xl text-xs font-bold uppercase tracking-wider bg-gradient-to-r from-yellow-500 to-amber-600 hover:from-yellow-400 hover:to-amber-500 text-black shadow-lg shadow-yellow-500/20 transition-all cursor-pointer"
            >
                Atualizar Senha
            </button>

            <span wire:loading wire:target="updatePassword" class="text-xs text-gray-400 font-mono">
                Processando...
            </span>

            <x-action-message class="text-xs text-emerald-400 font-mono font-semibold" on="password-updated">
                ✓ Senha militar atualizada com sucesso.
            </x-action-message>
        </div>
    </form>
</section>
