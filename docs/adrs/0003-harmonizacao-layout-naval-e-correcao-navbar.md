# ADR 0003: Harmonização Visual do Layout Naval e Correção da Barra Superior

- **Status:** Aprovado
- **Data:** 2026-11-04
- **Autor / Responsável:** 2º SG PD SANDRO
- **Contexto:** Comando da Força de Submarinos (ComForS / CLTI) — Projeto ForçaOnline

---

## 1. Contexto e Problema

Ao carregar os módulos operacionais pós-login (`/licoes-aprendidas/dashboard` e `/sipa-sub/dashboard`), a interface apresentava divergências visuais críticas:
1. **Barra Superior Branca Desalinhada**: A barra de navegação renderizava com fundo branco em virtude do layout base `resources/views/layouts/app.blade.php` do Laravel Breeze padrão (`bg-gray-100 dark:bg-gray-900`) sem a classe `dark` no elemento `<html>`, fazendo com que textos em branco ficassem ilegíveis sobre fundo claro.
2. **Desproporção do Brasão e Textos**: O brasão do ComForS ficava centralizado e com escala excessiva, ocupando a tela inteira em um bloco vertical rígido.
3. **Colisão de Rodapé**: As faixas de dados do militar conectado e a assinatura do desenvolvedor colidiam nas margens inferiores do componente.
4. **Ausência de Tipografia Naval**: A fonte militar *Oswald* não estava carregada em `layouts/app.blade.php`, gerando fallback para fontes genéricas do sistema.

---

## 2. Decisão de Arquitetura

1. **Unificação do Layout Base (`resources/views/layouts/app.blade.php`)**:
   - Inclusão das Google Fonts oficiais (*Oswald*, *Bebas Neue*, *Montserrat*, *Figtree*).
   - Definição explícita de `class="dark"` no elemento `<html>`.
   - Aplicação de estilos diretos de fundo naval escuro (`background-color: #030b17; color: #ffffff;`) para garantir que o layout nunca renderize com fundo claro, independentemente de cache de CSS.
   - Rodapé institucional global fixado na base do layout com a autoria permanente do **2º SG PD SANDRO**.

2. **Refatoração da Barra de Navegação (`resources/views/livewire/layout/navigation.blade.php`)**:
   - Fundo preto sólido (`#000000`) com borda sutil `#1f2937`.
   - Brasão oficial ComForS na marca do sistema `ForçaOnline`.
   - Links contextuais de módulo com destaque ativo azul para Lições Aprendidas e âmbar para SIPA-SUB.
   - Área do militar com nome em branco de alto contraste, NIP em amarelo naval e botão de logout em tom carmesim militar (`#450a0a`).

3. **Novo Design Horizontal dos Dashboards Volt (`licoes/dashboard.blade.php` e `sipa/dashboard.blade.php`)**:
   - Card militar moderno com gradiente sutil.
   - Brasão proporcional em caixa destacada (`h-24 sm:h-28`, object-contain).
   - Título em caixa alta com tipografia *Oswald*.
   - Faixa de status integrada com espaçamento e sem sobreposição de textos.

---

## 3. Consequências

- **Consistência Visual Total**: A identidade visual entre a tela de login e as telas internas é 100% contínua e imersiva.
- **Resiliência a Cache**: Estilos garantidos mesmo antes do carregamento completo do bundle de estilos.
- **Ergonomia e Legibilidade**: Alto contraste em todas as resoluções de tela.
