<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. NIP Administrador Geral (OCULTO - Não demonstrado em lugar algum no front)
        User::updateOrCreate(
            ['nip' => '09097074'],
            [
                'name' => 'Administrador Geral',
                'email' => 'admin.geral@marinha.mil.br',
                'modulo_acesso' => 'ambos',
                'password' => bcrypt('senha123'),
            ]
        );

        // 2. NIP exclusivo para Lições Aprendidas (Função 1)
        User::updateOrCreate(
            ['nip' => '11111111'],
            [
                'name' => 'Operador Lições Aprendidas',
                'email' => 'licoes@marinha.mil.br',
                'modulo_acesso' => 'licoes_aprendidas',
                'password' => bcrypt('senha123'),
            ]
        );

        // 3. NIP exclusivo para SIPA-SUB (Função 2)
        User::updateOrCreate(
            ['nip' => '22222222'],
            [
                'name' => 'Operador SIPA-SUB',
                'email' => 'sipa@marinha.mil.br',
                'modulo_acesso' => 'sipa_sub',
                'password' => bcrypt('senha123'),
            ]
        );

        // 4. NIP com acesso às duas funções (Ambas)
        User::updateOrCreate(
            ['nip' => '33333333'],
            [
                'name' => 'Operador Multi-Módulos',
                'email' => 'ambos@marinha.mil.br',
                'modulo_acesso' => 'ambos',
                'password' => bcrypt('senha123'),
            ]
        );
    }
}
