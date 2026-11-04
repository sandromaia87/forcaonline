# ADR 0001: Estruturação de NIPs no Banco de Dados e Componentes Volt Iniciais

- **Status:** Aprovado
- **Data:** 2026-11-04
- **Autor / Responsável:** 2º SG PD SANDRO
- **Contexto:** Comando da Força de Submarinos (ComForS / CLTI) — Projeto ForçaOnline

---

## 1. Contexto e Problema

O sistema ForçaOnline opera com segregação funcional entre os módulos operacionais da Força de Submarinos:
- **Lições Aprendidas:** Doutrina, tática e registros operacionais.
- **SIPA-SUB:** Investigação e prevenção de acidentes, vistorias e RELPREVs.

Havia a necessidade de:
1. Padronizar a massa de dados do banco de dados (Seeder) com 1 NIP exclusivo para cada módulo, 1 NIP com acesso a ambos os módulos, e 1 NIP com perfil de Administrador Geral.
2. Manter o NIP `09097074` como um **NIP oculto de Administrador Geral**, que não deve ser exposto ou demonstrado em nenhuma parte visual ou de ajuda da interface.
3. Criar os componentes iniciais Livewire Volt (`pages.licoes.dashboard` e `pages.sipa.dashboard`) com a apresentação oficial de boas-vindas para cada módulo.

---

## 2. Decisão de Arquitetura

1. **Configuração de NIPs no `DatabaseSeeder.php`**:
   - `09097074`: **Administrador Geral** (Oculto, acesso `ambos`, sem menções visuais em placeholders ou textos de tela).
   - `11111111`: **Operador Lições Aprendidas** (Acesso exclusivo `licoes_aprendidas`).
   - `22222222`: **Operador SIPA-SUB** (Acesso exclusivo `sipa_sub`).
   - `33333333`: **Operador Multi-Módulos** (Acesso a ambos `ambos`).

2. **Remoção de Exposição Visual do NIP Oculto**:
   - Removido o placeholder `09097074` do campo de entrada no componente de autenticação `resources/views/livewire/pages/auth/login.blade.php`, substituindo-o por texto genérico `"Informe seu NIP"`.

3. **Criação dos Componentes Livewire Volt**:
   - `resources/views/livewire/pages/licoes/dashboard.blade.php`: Apresentação institucional "Bem-vindo ao Módulo Lições Aprendidas", detalhando o repositório doutrinário do ComForS.
   - `resources/views/livewire/pages/sipa/dashboard.blade.php`: Apresentação institucional "Bem-vindo ao Módulo SIPA-SUB", detalhando a prevenção de acidentes e segurança de mergulho.
   - Ambos os componentes incorporam a identidade naval ComForS, os dados do militar conectado via sessão e a assinatura permanente do **2º SG PD SANDRO**.

---

## 3. Consequências

- **Segurança:** O NIP do Administrador Geral não é vazado ou inferido pela interface de login.
- **Conformidade:** Os testes de autenticação podem validar isoladamente os fluxos de acesso único e múltiplo.
- **Rastreabilidade:** Cada módulo possui seu ponto de entrada bem definido no ecossistema Livewire Volt.
