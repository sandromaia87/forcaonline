# ADR 0010: Reversão das Telas Pós-Login para Boas-Vindas e Execução do Preview Nativo com Artisan Serve

## Status
Aprovado / Implementado

## Data
04 de Novembro de 2026

## Contexto
Após a implementação inicial da estrutura Argon Dashboard Pro, constatou-se que foram adicionados componentes informativos e menus adicionais não solicitados (módulos e funcionalidades ainda não deliberados no escopo do projeto). 

Além disso, a visualização prévia da aplicação em desenvolvimento passou a apresentar a página informativa padrão do Vite (*"This is the Vite development server that provides Hot Module Replacement..."*), dado que o servidor do Vite rodava isoladamente sem o despacho das requisições pelo servidor HTTP do Laravel.

## Decisão de Arquitetura

1. **Reversão Estrita para Apresentação de Boas-Vindas**:
   - Os componentes de dashboard pós-login (`licoes/dashboard.blade.php` e `sipa/dashboard.blade.php`) foram revertidos para exibir **exclusivamente a apresentação oficial de boas-vindas** de cada módulo.
   - Eliminados todos os cartões de métricas hipotéticas, tabelas de dados não solicitadas, checklists e listas de tripulação.
   - Preservada a moldura visual Argon: Sidenav lateral elegante com Brasão e identificação institucional, Topbar com dados do militar e botão de saída, e plano de fundo tático com a marca d'água do submarino com opacidade suave.

2. **Saneamento do Menu de Navegação (`navigation.blade.php`)**:
   - Removidos todos os menus/links de funcionalidades não implementadas.
   - A barra de navegação passa a exibir estritamente o item de início do respectivo módulo ativo da sessão.

3. **Execução do Preview Diretamente pelo PHP Artisan Serve**:
   - Com o ambiente PHP 8.5 devidamente configurado, o comando `"dev"` do `package.json` foi padronizado para executar `php artisan serve --host 0.0.0.0 --port 3000`.
   - Dessa forma, o preview em tempo real consome a aplicação real Laravel 13 com Livewire Volt e os estilos compilados em `public/build`, extinguindo a tela branca do Vite.

## Consequências
- Alinhamento total com as diretrizes do projeto: layout Argon aplicado como container de boas-vindas sem criação de recursos ou menus precipitados.
- Visualização do preview perfeitamente funcional exibindo a tela oficial da ForçaOnline.
