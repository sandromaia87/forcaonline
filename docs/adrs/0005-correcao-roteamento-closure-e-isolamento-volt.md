# ADR 0005: Correção do Roteamento de Middleware e Isolamento Nativo via Volt

- **Status:** Aprovado
- **Data:** 2026-11-04
- **Autor / Responsável:** 2º SG PD SANDRO
- **Contexto:** Comando da Força de Submarinos (ComForS / CLTI) — Projeto ForçaOnline

---

## 1. Contexto e Problema

Ao carregar as rotas do módulo SIPA-SUB (`/sipa-sub/dashboard`) ou Lições Aprendidas, o Laravel lançou a seguinte exceção fatal de inicialização:
```
Error - Internal Server Error
Object of class Closure could not be converted to string
at vendor/laravel/framework/src/Illuminate/Routing/RouteRegistrar.php:130
at routes/web.php:35
```

### Causa Técnica:
No Laravel, o método fluente `Route::middleware(...)` aplicado a um grupo de rotas (`RouteRegistrar`) espera estritamente nomes de middleware registrados (como strings ou class-strings de middleware). Ao receber uma `Closure` anônima diretamente no encadeamento `Route::prefix(...)->middleware(function () {})`, o Laravel tenta tratá-la como uma string de identificador de middleware, resultando no erro fatal `Object of class Closure could not be converted to string`.

---

## 2. Decisão de Arquitetura

1. **Roteamento Limpo em `routes/web.php`**:
   - Removidas todas as Closures passadas incorretamente para o `Route::middleware()`.
   - As rotas dos módulos utilizam apenas os middlewares padrão registrados no grupo principal (`['auth', 'verified']`).

2. **Isolamento de Sessão Nativo nos Componentes Volt**:
   - O isolamento entre módulos foi movido para o ciclo de vida nativo do Livewire Volt através do método `mount()` em:
     - `resources/views/livewire/pages/licoes/dashboard.blade.php`
     - `resources/views/livewire/pages/sipa/dashboard.blade.php`
   - O método `mount()` executa no servidor antes de qualquer renderização:
     1. Valida se o militar possui permissão para o módulo acessado (`temAcessoAoModulo(...)`), abortando com 403 caso negativo.
     2. Valida se a sessão ativa pertence a outro módulo. Caso afirmativo, redireciona o militar de volta para o seu módulo ativo, impedindo a alternância sem logout prévio.
     3. Registra o módulo ativo na sessão (`session(['modulo_ativo' => '...'])`).

---

## 3. Consequências

- **Eliminação Total do Erro 500**: O registro de rotas do Laravel volta a ser 100% canônico e compatível com PHP 8.3 e Laravel 11/12/13.
- **Isolamento Estrito Garantido**: A regra de negócio de não permitir transição entre módulos sem deslogar permanece rigorosamente ativa e protegida pelo ciclo de vida do Livewire Volt.
- **Rastreabilidade e Segurança**: Abortos 403 e redirecionamentos ocorrem de forma transparente antes do envio de HTML para o cliente.
