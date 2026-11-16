<?php

use App\Models\User;
use Livewire\Volt\Volt;

test('bloqueia acesso a Nova Lição e Pesquisa para operador exclusivo SIPA-SUB', function () {
    $sipaUser = User::factory()->create([
        'nip' => '22222222',
        'modulo_acesso' => 'sipa_sub',
        'password' => bcrypt('senha123'),
    ]);

    $this->actingAs($sipaUser)->get(route('licoes.nova'))->assertForbidden();
    $this->actingAs($sipaUser)->get(route('licoes.pesquisar'))->assertForbidden();
});

test('Nova Lição valida campos obrigatórios antes de registrar', function () {
    $user = User::factory()->create([
        'nip' => '11111111',
        'modulo_acesso' => 'licoes_aprendidas',
        'password' => bcrypt('senha123'),
    ]);

    $this->actingAs($user);

    Volt::test('pages.licoes.nova')
        ->set('submarino', '')
        ->set('sistema', '')
        ->set('titulo', 'Ab')
        ->set('relato', 'Curto')
        ->set('recomendacao', 'Curto')
        ->call('salvar')
        ->assertHasErrors(['submarino', 'sistema', 'titulo', 'relato', 'recomendacao']);
});

test('Nova Lição permite cadastro rápido via Drawer e gera protocolo LA-ANO-SEQ', function () {
    $user = User::factory()->create([
        'nip' => '11111111',
        'name' => 'Operador Lições',
        'modulo_acesso' => 'licoes_aprendidas',
        'password' => bcrypt('senha123'),
    ]);

    $this->actingAs($user);

    $component = Volt::test('pages.licoes.nova')
        ->call('abrirDrawer', 'meio_naval')
        ->set('drawerMeioIndicativo', 's45')
        ->set('drawerMeioNome', 'Submarino Teste')
        ->call('salvarDrawerMeio')
        ->assertHasNoErrors()
        ->assertSet('submarino', 'S45 - Submarino Teste')
        ->call('abrirDrawer', 'subsistema')
        ->set('drawerSisNome', 'Válvula de Lastro Principal')
        ->call('salvarDrawerSistema')
        ->assertHasNoErrors()
        ->assertSet('sistema', 'Válvula de Lastro Principal')
        ->set('grau_sigilo', 'CONFIDENCIAL')
        ->set('data_ocorrencia', '2026-07-10')
        ->set('titulo', 'Estanqueidade durante manobra de imersão')
        ->set('relato', 'Descrição detalhada do evento operacional observado em cota de imersão.')
        ->set('recomendacao', 'Revisar procedimento operacional padrão antes do exercício de mergulho.')
        ->call('salvar')
        ->assertHasNoErrors()
        ->assertSet('salvo', true);

    $registros = session('licoes_aprendidas_registros', []);
    expect($registros)->toHaveCount(1);
    expect($registros[0]['protocolo'])->toBe('LA-2026-0001');
    expect($registros[0]['nip'])->toBe('11111111');
});

test('Pesquisar Lições inicia limpa e permite limpar filtros', function () {
    $user = User::factory()->create([
        'nip' => '11111111',
        'modulo_acesso' => 'licoes_aprendidas',
        'password' => bcrypt('senha123'),
    ]);

    $this->actingAs($user);

    Volt::test('pages.licoes.pesquisar')
        ->assertSet('licoes', [])
        ->set('busca', 'snorquel')
        ->set('filtro_sigilo', 'RESERVADO')
        ->call('limparFiltros')
        ->assertSet('busca', '')
        ->assertSet('filtro_sigilo', '');
});
