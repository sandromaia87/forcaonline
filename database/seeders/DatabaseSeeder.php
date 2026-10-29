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
        \App\Models\User::factory()->create([
            'name' => 'Sandro Maia',
            'nip' => '09097074',
            'modulo_acesso' => 'ambos', // Mude para testar as rotas depois
            'password' => bcrypt('senha123'), // Senha de teste
        ]);
    }
}
