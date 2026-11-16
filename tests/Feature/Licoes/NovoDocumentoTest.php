<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Livewire\Volt\Volt;

test('bloqueia acesso a Novo Documento para operador exclusivo SIPA-SUB', function () {
    $sipaUser = User::factory()->create([
        'nip' => '22222222',
        'modulo_acesso' => 'sipa_sub',
        'password' => bcrypt('senha123'),
    ]);

    $this->actingAs($sipaUser)
        ->get(route('licoes.documento'))
        ->assertForbidden();
});

test('Novo Documento gera número único automático por sigla e ano e valida campos obrigatórios', function () {
    $user = User::factory()->create([
        'nip' => '11111111',
        'modulo_acesso' => 'licoes_aprendidas',
        'password' => bcrypt('senha123'),
    ]);

    $this->actingAs($user);

    Volt::test('pages.licoes.documento')
        ->set('data_documento', '2026-05-15')
        ->set('tipo_sigla', 'RFC')
        ->assertSet('numero_gerado', 'RFC-2026-0001')
        ->set('tipo_sigla', '')
        ->set('titulo', 'Doc')
        ->set('ementa', 'Curto')
        ->call('salvar')
        ->assertHasErrors(['tipo_sigla', 'titulo', 'ementa']);
});

test('Novo Documento aceita anexo PDF válido até 10 MB e rejeita arquivos não-PDF ou acima de 10 MB', function () {
    $user = User::factory()->create([
        'nip' => '11111111',
        'name' => 'Operador Lições',
        'modulo_acesso' => 'licoes_aprendidas',
        'password' => bcrypt('senha123'),
    ]);

    $this->actingAs($user);

    // Arquivo não PDF deve ser rejeitado
    $invalidType = UploadedFile::fake()->create('planilha.xlsx', 500, 'application/vnd.ms-excel');
    Volt::test('pages.licoes.documento')
        ->set('tipo_sigla', 'RFC')
        ->set('titulo', 'Relatório Final de Comissão S40')
        ->set('ementa', 'Resumo executivo completo da comissão realizada pelo S40.')
        ->set('arquivo_pdf', $invalidType)
        ->call('salvar')
        ->assertHasErrors(['arquivo_pdf']);

    // Arquivo PDF acima de 10 MB (10240 KB) deve ser rejeitado
    $oversizedPdf = UploadedFile::fake()->create('grande.pdf', 11000, 'application/pdf');
    Volt::test('pages.licoes.documento')
        ->set('tipo_sigla', 'RFC')
        ->set('titulo', 'Relatório Final de Comissão S40')
        ->set('ementa', 'Resumo executivo completo da comissão realizada pelo S40.')
        ->set('arquivo_pdf', $oversizedPdf)
        ->call('salvar')
        ->assertHasErrors(['arquivo_pdf']);

    // PDF válido de 2 MB deve ser salvo com número único
    $validPdf = UploadedFile::fake()->create('rfc-oficial.pdf', 2048, 'application/pdf');
    Volt::test('pages.licoes.documento')
        ->set('data_documento', '2026-05-15')
        ->set('tipo_sigla', 'RFC')
        ->set('titulo', 'Relatório Final de Comissão S40')
        ->set('ementa', 'Resumo executivo completo da comissão realizada pelo S40.')
        ->set('arquivo_pdf', $validPdf)
        ->call('salvar')
        ->assertHasNoErrors()
        ->assertSet('salvo', true);

    $docs = session('documentos_cadastrados', []);
    expect($docs)->toHaveCount(1);
    expect($docs[0]['numero'])->toBe('RFC-2026-0001');
    expect($docs[0]['arquivo_pdf_nome'])->toBe('rfc-oficial.pdf');
});
