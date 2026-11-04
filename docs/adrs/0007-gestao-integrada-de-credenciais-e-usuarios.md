# ADR 0007: Gestão Integrada de Credenciais Militares e Ciclo de Vida de Usuários (CRUD)

- **Status:** Aprovado
- **Data:** 2026-11-04
- **Autor / Responsável:** 2º SG PD SANDRO
- **Contexto:** Comando da Força de Submarinos (ComForS / CLTI) — Projeto ForçaOnline

---

## 1. Contexto e Problema

O ecossistema ForçaOnline necessita de governança centralizada sobre o cadastro de militares e suas respectivas prerrogativas de acesso aos módulos de missão (*Lições Aprendidas* e *SIPA-SUB*):
- O ciclo de vida dos usuários (criação, consulta, edição e revogação de credenciais) deve ser operacionalizado com estrito controle de segurança.
- As operações de escrita e exclusão devem ser restritas exclusivamente a autoridades centrais autorizadas.
- O campo NIP deve manter validação rigorosa de formato numérico de 8 dígitos e unicidade no banco de dados.

---

## 2. Decisão de Arquitetura

1. **Camada de Autorização Estrita**:
   - Todas as mutações e visualizações da gestão de credenciais exigem verificação de autoridade suprema no hook `mount()` e em cada método executivo do componente Livewire Volt.
   - Requisições não autorizadas disparam imediatamente `abort(404)` para preservação do sigilo do recurso.

2. **Operações do Ciclo de Vida (CRUD)**:
   - **Create (Cadastrar):** Cadastro com validação de Nome militar, NIP único de 8 dígitos numéricos, e-mail institucional, perfil de módulo autorizado (`licoes_aprendidas`, `sipa_sub` ou `ambos`) e hash seguro de senha via Bcrypt.
   - **Read (Consulta e Filtro):** Listagem em tempo real com busca debounced por termo (Nome, NIP ou E-mail).
   - **Update (Edição):** Atualização de dados cadastrais e permissões táticas, permitindo redefinição opcional de credencial de senha.
   - **Delete (Revogação):** Exclusão com confirmação modal de segurança e proteção de imutabilidade sobre a credencial de comando central.

3. **Atualização do Model `User` (`app/Models/User.php`)**:
   - Inclusão do campo `email` no atributo `#[Fillable]` para suporte às operações de escrita em lote.

---

## 3. Consequências

- **Autonomia Administrativa:** O comando central pode registrar novos militares, reatribuir módulos e revogar credenciais diretamente pela interface sem intervenção manual no banco de dados.
- **Integridade dos Dados:** Validações de unicidade e formatos numéricos eliminam inconsistências cadastrais.
- **Segurança Reforçada:** O isolamento estrito impede qualquer vazamento de dados de outros militares ou escalonamento indevido de privilégios.
