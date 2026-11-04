# ADR 0004: Isolamento Estrito de Sessão entre Módulos e Obrigatoriedade de Logout

- **Status:** Aprovado
- **Data:** 2026-11-04
- **Autor / Responsável:** 2º SG PD SANDRO
- **Contexto:** Comando da Força de Submarinos (ComForS / CLTI) — Projeto ForçaOnline

---

## 1. Contexto e Problema

Por diretriz e requisito estratégico de segurança operacional da Força de Submarinos:
- **Não deve haver ligação ou ponte de navegação direta entre os módulos** (*Lições Aprendidas* e *SIPA-SUB*) durante uma sessão ativa.
- Mesmo que o militar possua permissão de acesso a ambos os módulos (`modulo_acesso = 'ambos'`) ou seja o Administrador Geral, ele **não pode alternar diretamente de um módulo para o outro** a partir da interface interna.
- Para mudar de módulo, o militar é **obrigatoriamente compelido a sair da aplicação (Logout)** e realizar um novo procedimento de login, escolhendo o outro módulo na etapa 2 de seleção.

---

## 2. Decisão de Arquitetura

1. **Remoção de Links Cruzados na Barra de Navegação (`resources/views/livewire/layout/navigation.blade.php`)**:
   - Eliminados todos os botões/links de navegação que permitiam chavear entre *Lições Aprendidas* e *SIPA-SUB*.
   - A barra passa a exibir apenas:
     - Logotipo e Brasão Oficial ComForS;
     - Badge informativo fixo do módulo ativo (`Módulo Ativo: Lições Aprendidas` ou `Módulo Ativo: SIPA-SUB`);
     - Nome e NIP do militar conectado;
     - Botão destacado **Sair (Logout)**, explicitando a necessidade de logout para alternância de perfil de missão.

2. **Guarda de Rotas no Back-End (`routes/web.php`)**:
   - As rotas de cada módulo (`/licoes-aprendidas/*` e `/sipa-sub/*`) receberam middleware de verificação de sessão ativa (`session('modulo_ativo')`).
   - Se um militar autenticado em uma sessão de *Lições Aprendidas* tentar acessar manualmente uma URL do *SIPA-SUB* (ou vice-versa), a requisição é interceptada e o militar é redirecionado de volta para o dashboard do seu módulo atualmente ativo, impedindo a mistura de contextos operacionais.

---

## 3. Consequências

- **Segurança Compartimentada:** Doutrina de sigilo e foco operacional mantida: registros de acidentes e ocorrências do SIPA-SUB ficam estritamente isolados da doutrina de Lições Aprendidas.
- **Rastreabilidade Limpa:** Cada sessão de auditoria e navegação pertence a um único módulo sem transições ambíguas.
- **Conformidade com o Requisito do Empreendedor:** A única forma de alternar entre os sistemas é através do fluxo explícito: `Logout -> NIP -> Escolha de Sistema -> Senha`.
