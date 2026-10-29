<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('modulo_acesso')->nullable()->after('email'); // Ex: 'licoes_aprendidas', 'sipa_sub', ou 'admin'
            $table->string('nip')->unique()->nullable()->after('id'); // Adiciona o NIP para o login militar
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
