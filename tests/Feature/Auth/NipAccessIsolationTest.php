<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Volt\Volt;

test('tela inicial de login por NIP é renderizada para visitantes', function () {
    $this->get('/')
        ->assertOk()
        ->assertSeeVolt('pages.auth.login');
});

test('Passo 1 valida que o NIP possui exatamente 8 dígitos numéricos e existe no banco', function () {
    Volt::test('pages.auth.login')
        ->set('nip', '')
        ->call('verificarNip')
        ->assertHasErrors(['nip']);

    Volt::test('pages.auth.login')
        ->set('nip', '1234')
        ->call('verificarNip')
        ->assertHasErrors(['nip']);

    Volt::test('pages.auth.login')
        ->set('nip', '99999999')
        ->call('verificarNip')
        ->assertHasErrors(['nip']);
});

test('operador exclusivo de Lições Aprendidas pula o Passo 2 e autentica direto no módulo correto', function () {
    $user = User::factory()->create([
        'nip' => '11111111',
        'name' => 'Operador Lições Aprendidas',
        'email' => 'licoes@marinha.mil.br',
        'modulo_acesso' => 'licoes_aprendidas',
        'password' => bcrypt('senha123'),
    ]);

    expect($user->temAcessoAoModulo('licoes_aprendidas'))->toBeTrue();
    expect($user->temAcessoAoModulo('sipa_sub'))->toBeFalse();

    $component = Volt::test('pages.auth.login')
        ->set('nip', '11111111')
        ->call('verificarNip')
        ->assertHasNoErrors()
        ->assertSet('step', 3)
        ->assertSet('sistemaSelecionado', 'licoes_aprendidas');

    $component
        ->set('password', 'senha123')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('licoes.dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
    expect(session('modulo_ativo'))->toBe('licoes_aprendidas');
});

test('operador exclusivo do SIPA-SUB pula o Passo 2 e autentica direto no SIPA-SUB', function () {
    $user = User::factory()->create([
        'nip' => '22222222',
        'name' => 'Operador SIPA-SUB',
        'email' => 'sipa@marinha.mil.br',
        'modulo_acesso' => 'sipa_sub',
        'password' => bcrypt('senha123'),
    ]);

    expect($user->temAcessoAoModulo('sipa_sub'))->toBeTrue();
    expect($user->temAcessoAoModulo('licoes_aprendidas'))->toBeFalse();

    $component = Volt::test('pages.auth.login')
        ->set('nip', '22222222')
        ->call('verificarNip')
        ->assertHasNoErrors()
        ->assertSet('step', 3)
        ->assertSet('sistemaSelecionado', 'sipa_sub');

    $component
        ->set('password', 'senha123')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('sipasub.dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
    expect(session('modulo_ativo'))->toBe('sipa_sub');
});

test('operador multi-módulos visualiza o Passo 2 e escolhe qual módulo iniciar', function () {
    $user = User::factory()->create([
        'nip' => '33333333',
        'name' => 'Operador Multi-Módulos',
        'email' => 'ambos@marinha.mil.br',
        'modulo_acesso' => 'ambos',
        'password' => bcrypt('senha123'),
    ]);

    expect($user->temAcessoAoModulo('licoes_aprendidas'))->toBeTrue();
    expect($user->temAcessoAoModulo('sipa_sub'))->toBeTrue();

    $component = Volt::test('pages.auth.login')
        ->set('nip', '33333333')
        ->call('verificarNip')
        ->assertHasNoErrors()
        ->assertSet('step', 2)
        ->call('escolherSistema', 'sipa_sub')
        ->assertSet('step', 3)
        ->assertSet('sistemaSelecionado', 'sipa_sub');

    $component
        ->set('password', 'senha123')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('sipasub.dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
    expect(session('modulo_ativo'))->toBe('sipa_sub');
});

test('bloqueia acesso após 5 tentativas de senha inválidas via RateLimiter', function () {
    User::factory()->create([
        'nip' => '11111111',
        'modulo_acesso' => 'licoes_aprendidas',
        'password' => bcrypt('senha123'),
    ]);

    RateLimiter::clear('11111111|127.0.0.1');

    for ($i = 0; $i < 5; $i++) {
        Volt::test('pages.auth.login')
            ->set('nip', '11111111')
            ->call('verificarNip')
            ->set('password', 'senha-errada')
            ->call('login')
            ->assertHasErrors(['password']);
    }

    Volt::test('pages.auth.login')
        ->set('nip', '11111111')
        ->call('verificarNip')
        ->set('password', 'senha123')
        ->call('login')
        ->assertHasErrors(['nip']);

    $this->assertGuest();
});

test('isolamento de acesso impede usuário sem permissão ou em módulo diferente de cruzar módulos', function () {
    $userLicoes = User::factory()->create([
        'nip' => '11111111',
        'modulo_acesso' => 'licoes_aprendidas',
        'password' => bcrypt('senha123'),
    ]);

    $this->actingAs($userLicoes)
        ->withSession(['modulo_ativo' => 'licoes_aprendidas'])
        ->get(route('licoes.dashboard'))
        ->assertOk();

    // Operador exclusivo de Lições Aprendidas não pode acessar SIPA-SUB (403)
    $this->actingAs($userLicoes)
        ->withSession(['modulo_ativo' => 'licoes_aprendidas'])
        ->get(route('sipasub.dashboard'))
        ->assertForbidden();

    // Operador multi-módulo com sessão travada em sipa_sub é redirecionado ao tentar abrir rotas de licoes
    $userAmbos = User::factory()->create([
        'nip' => '33333333',
        'modulo_acesso' => 'ambos',
        'password' => bcrypt('senha123'),
    ]);

    $this->actingAs($userAmbos)
        ->withSession(['modulo_ativo' => 'sipa_sub'])
        ->get(route('sipasub.dashboard'))
        ->assertOk();

    $this->actingAs($userAmbos)
        ->withSession(['modulo_ativo' => 'licoes_aprendidas'])
        ->get(route('sipasub.dashboard'))
        ->assertRedirect(route('licoes.dashboard'));
});

test('visitantes não autenticados são redirecionados ao tentar acessar páginas protegidas', function () {
    $this->get(route('licoes.dashboard'))->assertRedirect('/');
    $this->get(route('licoes.nova'))->assertRedirect('/');
    $this->get(route('licoes.documento'))->assertRedirect('/');
    $this->get(route('licoes.pesquisar'))->assertRedirect('/');
    $this->get(route('licoes.meios-navais'))->assertRedirect('/');
    $this->get(route('licoes.subsistemas'))->assertRedirect('/');
    $this->get(route('licoes.comissoes'))->assertRedirect('/');
    $this->get(route('licoes.tipos-documento'))->assertRedirect('/');
    $this->get(route('sipasub.dashboard'))->assertRedirect('/');
    $this->get(route('profile'))->assertRedirect('/');
});
