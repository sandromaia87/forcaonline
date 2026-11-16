<?php

use App\Models\User;
use Livewire\Volt\Volt;

test('bloqueia acesso ao CRUD de Comissões para operador exclusivo SIPA-SUB', function () {
    $sipaUser = User::factory()->create([
        'nip' => '22222222',
        'modulo_acesso' => 'sipa_sub',
        'password' => bcrypt('senha123'),
    ]);

    $this->actingAs($sipaUser)
        ->get(route('licoes.comissoes'))
        ->assertForbidden();
});

test('CRUD Comissões inicia vazio e valida código e nome obrigatórios', function () {
    $user = User::factory()->create([
        'nip' => '11111111',
        'modulo_acesso' => 'licoes_aprendidas',
        'password' => bcrypt('senha123'),
    ]);

    $this->actingAs($user);

    Volt::test('pages.licoes.comissoes')
        ->assertSet('comissoes', [])
        ->call('abrirModalNovo')
        ->set('codigo', '')
        ->set('nome', '')
        ->call('salvar')
        ->assertHasErrors(['codigo', 'nome']);
});

test('CRUD Comissões permite cadastrar, editar e excluir comissão operacional', function () {
    $user = User::factory()->create([
        'nip' => '11111111',
        'modulo_acesso' => 'licoes_aprendidas',
        'password' => bcrypt('senha123'),
    ]);

    $this->actingAs($user);

    $component = Volt::test('pages.licoes.comissoes')
        ->call('abrirModalNovo')
        ->set('codigo', 'com-tropicalex-2026')
        ->set('nome', 'Operação Tropicalex 2026')
        ->set('tipo', 'Operação Tática')
        ->set('meio_naval', 'S40 - Submarino Riachuelo')
        ->set('status', 'Planejada')
        ->call('salvar')
        ->assertHasNoErrors()
        ->assertSet('modalAberto', false);

    $lista = $component->get('comissoes');
    expect($lista)->toHaveCount(1);
    expect($lista[0]['codigo'])->toBe('COM-TROPICALEX-2026');

    // Editar
    $component
        ->call('editar', $lista[0]['id'])
        ->set('status', 'Concluída')
        ->call('salvar')
        ->assertHasNoErrors();

    expect($component->get('comissoes')[0]['status'])->toBe('Concluída');

    // Excluir
    $component->call('excluir', $lista[0]['id']);
    expect($component->get('comissoes'))->toHaveCount(0);
});
