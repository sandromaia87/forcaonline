<?php

use App\Models\User;
use Livewire\Volt\Volt;

test('bloqueia acesso ao CRUD de Meios Navais para operador exclusivo SIPA-SUB', function () {
    $sipaUser = User::factory()->create([
        'nip' => '22222222',
        'modulo_acesso' => 'sipa_sub',
        'password' => bcrypt('senha123'),
    ]);

    $this->actingAs($sipaUser)
        ->get(route('licoes.meios-navais'))
        ->assertForbidden();
});

test('CRUD Meios Navais inicia com 9 unidades oficiais e valida campos obrigatórios', function () {
    $user = User::factory()->create([
        'nip' => '11111111',
        'modulo_acesso' => 'licoes_aprendidas',
        'password' => bcrypt('senha123'),
    ]);

    $this->actingAs($user);

    Volt::test('pages.licoes.meios-navais')
        ->assertSet('meios', fn ($meios) => count($meios) === 9)
        ->call('abrirModalNovo')
        ->set('indicativo', '')
        ->set('nome', '')
        ->call('salvar')
        ->assertHasErrors(['indicativo', 'nome']);
});

test('CRUD Meios Navais permite cadastrar, editar e excluir meio naval', function () {
    $user = User::factory()->create([
        'nip' => '11111111',
        'modulo_acesso' => 'licoes_aprendidas',
        'password' => bcrypt('senha123'),
    ]);

    $this->actingAs($user);

    $component = Volt::test('pages.licoes.meios-navais')
        ->call('abrirModalNovo')
        ->set('indicativo', 'nss-k11')
        ->set('nome', 'Navio de Socorro Submarino Guillobel')
        ->set('tipo', 'Navio de Apoio')
        ->set('classe', 'Apoio Submarino')
        ->set('status', 'Operacional')
        ->call('salvar')
        ->assertHasNoErrors()
        ->assertSet('modalAberto', false);

    $meios = $component->get('meios');
    expect($meios)->toHaveCount(10);
    $novo = collect($meios)->firstWhere('indicativo', 'NSS-K11');
    expect($novo)->not->toBeNull();

    // Editar
    $component
        ->call('editar', $novo['id'])
        ->set('nome', 'NSS Guillobel Homologado')
        ->call('salvar')
        ->assertHasNoErrors();

    expect(collect($component->get('meios'))->firstWhere('id', $novo['id'])['nome'])
        ->toBe('NSS Guillobel Homologado');

    // Excluir
    $component->call('excluir', $novo['id']);
    expect($component->get('meios'))->toHaveCount(9);
});
