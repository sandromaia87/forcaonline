# ADR 0002: Correção da Rota Dashboard e Implementação de Máscara Numérica no NIP

- **Status:** Aprovado
- **Data:** 2026-11-04
- **Autor / Responsável:** 2º SG PD SANDRO
- **Contexto:** Comando da Força de Submarinos (ComForS / CLTI) — Projeto ForçaOnline

---

## 1. Contexto e Problema

1. **Exceção `RouteNotFoundException: Route [dashboard] not defined`**:
   - Ao autenticar com o usuário `11111111` (ou qualquer outro militar), a aplicação redirecionava para o módulo autorizado (`licoes.dashboard`), cujo layout `resources/views/layouts/app.blade.php` renderizava o componente de navegação `resources/views/livewire/layout/navigation.blade.php`.
   - O componente de navegação padrão continha chamadas fixas a `route('dashboard')`, rota que não existia em `routes/web.php` (as rotas estavam particionadas como `licoes.dashboard` e `sipasub.dashboard`).
   - Isso gerava erro fatal 500 (`Internal Server Error`).

2. **Campo NIP sem Máscara Numérica e Sem Limitação Estrita de 8 Dígitos**:
   - O campo de entrada de NIP estava aceitando letras, caracteres especiais e permitindo inserções com mais de 8 dígitos sem máscara prévia no front-end.

---

## 2. Decisão de Arquitetura

1. **Definição da Rota Inteligente `dashboard` em `routes/web.php`**:
   - Adicionada a rota nomeada `dashboard` dentro do grupo autenticado.
   - A rota inspeciona a sessão ativa (`session('modulo_ativo')`) e as permissões do militar (`auth()->user()->modulo_acesso`), redirecionando-o transparentemente para o módulo correspondente (`sipasub.dashboard` ou `licoes.dashboard`).

2. **Refatoração da Navegação (`resources/views/livewire/layout/navigation.blade.php`)**:
   - O componente agora exibe links contextuais com base na função do militar (`auth()->user()->temAcessoAoModulo(...)`).
   - Estilização naval militar escura (`bg-black/90`, brasão ComForS, indicador visual de módulo ativo e logout limpo).

3. **Adição do Método Auxiliar no Model `app/Models/User.php`**:
   - Criado o método `temAcessoAoModulo(string $modulo): bool` para testar se o militar tem acesso específico ou permissão `ambos`.

4. **Máscara e Validação Estrita do NIP**:
   - No front-end (Alpine.js / Blade): `x-on:input` remove imediatamente caracteres não numéricos e limita a 8 dígitos (`maxlength="8"`, `inputmode="numeric"`, `pattern="[0-9]{8}"`).
   - No componente Livewire: hook `updatedNip` sanitiza o valor em tempo real com `preg_replace('/[^0-9]/', '', $value)` e validação obrigatória `required|digits:8`.

---

## 3. Consequências

- **Resolução Definitiva do Erro 500:** Qualquer link ou redirecionamento para `dashboard` funciona de forma resiliente e contextual.
- **Integridade de Entrada:** É impossível enviar NIPs com formatação incorreta, letras ou tamanho diferente de 8 dígitos.
- **Experiência do Usuário:** O teclado numérico nativo é ativado em dispositivos móveis e a interface bloqueia digitações fora do padrão militar.
