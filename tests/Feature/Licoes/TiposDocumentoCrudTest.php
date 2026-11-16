<?php

use App\Models\User;
use Livewire\Volt\Volt;

test('bloqueia acesso ao CRUD de Tipos de Documento para operador exclusivo SIPA-SUB', function () {
    $sipaUser = User::factory()->create([
        'nip' => '22222222',
        'modulo_acesso' => 'sipa_sub',
        'password' => bcrypt('senha123'),
    ]);

    $this->actingAs($sipaUser)
        ->get(route('licoes.tipos-documento'))
        ->assertForbidden();
});

test('CRUD Tipos de Documento inicia com RFC e ARFC e valida sigla e nome', function () {
    $user = User::factory()->create([
        'nip' => '11111111',
        'modulo_acesso' => 'licoes_aprendidas',
        'password' => bcrypt('senha123'),
    ]);

    $this->actingAs($user);

    Volt::test('pages.licoes.tipos-documento')
        ->assertSet('tipos', fn ($tipos) => count($tipos) === 2)
        ->call('abrirModalNovo')
        ->set('sigla', 'R')
        ->set('nome', 'AB')
        ->call('salvar')
        ->assertHasErrors(['sigla', 'nome']);
});

test('CRUD Tipos de Documento permite cadastrar, editar e excluir tipo documental', function () {
    $user = User::factory()->create([
        'nip' => '11111111',
        'modulo_acesso' => 'licoes_aprendidas',
        'password' => bcrypt('senha123'),
    ]);

    $this->actingAs($user);

    $component = Volt::test('pages.licoes.tipos-documento')
        ->call('abrirModalNovo')
        ->set('sigla', 'rte')
        ->set('nome', 'Relatório Técnico de Engenharia')
        ->set('descricao', 'Parecer técnico de engenharia submarina.')
        ->set('status', 'Ativo')
        ->call('salvar')
        ->assertHasNoErrors()
        ->assertSet('modalAberto', false);

    $tipos = $component->get('tipos');
    expect($tipos)->toHaveCount(3);
    $novo = collect($tipos)->firstWhere('sigla', 'RTE');
    expect($novo)->not->toBeNull();

    // Editar
    $component
        ->call('abrirModalEditar', $novo['id'])
        ->set('status', 'Inativo')
        ->call('salvar')
        ->assertHasNoErrors();

    expect(collect($component->get('tipos'))->firstWhere('id', $novo['id'])['status'])
        ->toBe('Inativo');

    // Excluir
    $component->call('excluir', $novo['id']);
    expect($component->get('tipos'))->toHaveCount(2);
});
