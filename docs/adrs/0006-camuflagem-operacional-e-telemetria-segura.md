# ADR 0006: Camuflagem Operacional e Estrutura de Telemetria de Segurança

- **Status:** Aprovado
- **Data:** 2026-11-04
- **Autor / Responsável:** 2º SG PD SANDRO
- **Contexto:** Comando da Força de Submarinos (ComForS / CLTI) — Projeto ForçaOnline

---

## 1. Contexto e Problema

Para elevar o nível de segurança cibernética e proteção de dados críticos dos submarinos da Força:
- Há a necessidade de uma camada de telemetria operacional com rotas ofuscadas e nomenclatura técnica camuflada.
- O endpoint de auditoria e monitoramento não deve ser dedutível através de dicionários comuns de ataque (como `/admin`, `/painel` ou `/dashboard`).
- Qualquer acesso não autenticado ou desautorizado ao endpoint camuflado deve retornar explicitamente `404 Not Found` (em vez de `403 Forbidden`), omitindo por completo a própria existência da rota para potenciais varreduras de rede.

---

## 2. Decisão de Arquitetura

1. **Roteamento Camuflado (`routes/web.php`)**:
   - Criado o prefixo operacional camuflado `/sys-ops/k7-overview` com identificador `sys.ops.k7`.
   - A rota é associada ao componente de controle interno em `pages.core.terminal`.

2. **Guarda Silenciosa de Requisição (`mount()`)**:
   - O componente implementa verificação estrita de credenciais militares autorizadas.
   - Qualquer tentativa de requisição sem a devida autorização dispara imediatamente `abort(404)`.
   - Elimina qualquer vazamento de cabeçalhos ou dicas sobre a presença do recurso no servidor.

3. **Fluxo Otimizado de Autenticação (`login.blade.php`)**:
   - A validação de credencial militar realiza o direcionamento contextual correspondente, dispensando etapas intermediárias desnecessárias.

---

## 3. Consequências

- **Superfície de Ataque Reduzida:** A camada de telemetria permanece invisível a scanners e tentativas de varredura.
- **Conformidade Operacional:** Alinhamento rigoroso com as diretrizes de segurança da informação militar e requisitos estratégicos do ComForS.
