<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 max-w-5xl mx-auto space-y-6 select-none">
        
        <!-- Cabeçalho Oficial no Padrão Argon Dashboard Pro -->
        <div class="p-6 rounded-2xl bg-gradient-to-br from-[#061122] via-[#040b17] to-[#02060e] border border-blue-900/40 shadow-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-yellow-500/10 border border-yellow-500/30 flex items-center justify-center shrink-0">
                    <img src="{{ asset('img/brasao_0.png') }}" alt="" class="h-8 w-auto object-contain">
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold uppercase tracking-tight text-white" style="font-family: 'Oswald', sans-serif;">
                        Painel do Militar • <span class="text-yellow-500">Minha Conta</span>
                    </h1>
                    <p class="text-xs text-gray-400 font-mono mt-0.5">
                        Gerenciamento de credenciais e identificação militar no sistema ForçaOnline
                    </p>
                </div>
            </div>

            <div class="font-mono text-xs bg-black/50 px-3.5 py-2 rounded-xl border border-gray-800">
                <span class="text-gray-400 block text-[10px] uppercase">NIP Militar:</span>
                <span class="text-yellow-400 font-bold text-sm tracking-widest">{{ auth()->user()->nip }}</span>
            </div>
        </div>

        <!-- Seções de Formulários no Padrão Militar Argon -->
        <div class="grid grid-cols-1 gap-6">
            <!-- Atualizar Informações do Militar -->
            <div class="p-6 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-2xl">
                <div class="max-w-2xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <!-- Atualizar Senha de Acesso Militar -->
            <div class="p-6 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-2xl">
                <div class="max-w-2xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
