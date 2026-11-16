<?php

use App\Models\User;
use Livewire\Volt\Volt;

test('bloqueia acesso ao CRUD de Subsistemas para operador exclusivo SIPA-SUB', function () {
    $sipaUser = User::factory()->create([
        'nip' => '22222222',
        'modulo_acesso' => 'sipa_sub',
        'password' => bcrypt('senha123'),
    ]);

    $this->actingAs($sipaUser)
        ->get(route('licoes.subsistemas'))
        ->assertForbidden();
});

test('CRUD Subsistemas inicia com 10 sistemas técnicos e valida designação obrigatória', function () {
    $user = User::factory()->create([
        'nip' => '11111111',
        'modulo_acesso' => 'licoes_aprendidas',
        'password' => bcrypt('senha123'),
    ]);

    $this->actingAs($user);

    Volt::test('pages.licoes.subsistemas')
        ->assertSet('subsistemas', fn ($items) => count($items) === 10)
        ->call('abrirModalNovo')
        ->set('nome', 'AB')
        ->call('salvar')
        ->assertHasErrors(['nome']);
});

test('CRUD Subsistemas permite cadastrar, editar e excluir subsistema', function () {
    $user = User::factory()->create([
        'nip' => '11111111',
        'modulo_acesso' => 'licoes_aprendidas',
        'password' => bcrypt('senha123'),
    ]);

    $this->actingAs($user);

    $component = Volt::test('pages.licoes.subsistemas')
        ->call('abrirModalNovo')
        ->set('nome', 'Sistema de Ar Comprimido HP')
        ->set('area', 'Atmosfera & CAV')
        ->set('criticidade', 'Crítica de Segurança')
        ->set('status', 'Homologado')
        ->call('salvar')
        ->assertHasNoErrors()
        ->assertSet('modalAberto', false);

    $lista = $component->get('subsistemas');
    expect($lista)->toHaveCount(11);
    $novo = collect($lista)->firstWhere('nome', 'Sistema de Ar Comprimido HP');
    expect($novo)->not->toBeNull();

    // Editar
    $component
        ->call('editar', $novo['id'])
        ->set('status', 'Em Revisão')
        ->call('salvar')
        ->assertHasNoErrors();

    expect(collect($component->get('subsistemas'))->firstWhere('id', $novo['id'])['status'])
        ->toBe('Em Revisão');

    // Excluir
    $component->call('excluir', $novo['id']);
    expect($component->get('subsistemas'))->toHaveCount(10);
});
