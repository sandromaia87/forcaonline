# ADR 0009: Remoção de Artefatos SPA (/src) e Restauração do Build Nativo Laravel Vite

## Status
Aprovado / Implementado

## Data
04 de Novembro de 2026

## Contexto
Durante a implementação e teste da padronização visual Argon Dashboard Pro, arquivos transitórios da pasta `/src` foram inadvertidamente incluídos no versionamento do repositório, juntamente com dependências pontuais do ecossistema React.

Ao realizar o `git pull` no ambiente oficial de desenvolvimento e produção do **ForçaOnline** (projeto puramente estruturado sobre **Laravel 13 + Livewire + Volt**), a presença da pasta `/src` e a configuração divergente no `vite.config.js` causaram falhas durante a execução do comando `npm run build`, impedindo a compilação correta dos ativos em `public/build` requeridos pela diretiva Blade `@vite(['resources/css/app.css', 'resources/js/app.js'])`.

## Decisão de Arquitetura

1. **Remoção Completa da Pasta `/src` do Repositório**:
   - Exclusão rastreada de todos os arquivos do diretório `/src` do controle de versão Git (`git rm -r src/`), garantindo que o repositório permaneça 100% limpo e estritamente orientado ao ecossistema Laravel/Blade.

2. **Restauração da Configuração Nativa do Vite (`vite.config.js`)**:
   - Restabelecimento do plugin oficial `laravel-vite-plugin` apontando para os pontos de entrada nativos do projeto:
     - `resources/css/app.css` (Tailwind CSS com presets ComForS)
     - `resources/js/app.js` (Scripts globais e extensões)
   - Manutenção da flag de vinculação do servidor de desenvolvimento na porta 3000 (`host: '0.0.0.0'`, `port: 3000`).

3. **Saneamento do `package.json`**:
   - Remoção de dependências e tipagens React excedentes.
   - Preservação exclusiva dos pacotes requeridos pelo ecossistema Laravel (`laravel-vite-plugin`, `tailwindcss`, `@tailwindcss/forms`, `postcss`, `autoprefixer`, etc.).
   - Padronização do script `"build": "vite build"`, que gera com sucesso o arquivo `public/build/manifest.json` consumido pelo Blade.

## Consequências
- Restauração imediata da capacidade de compilação via `npm run build` no servidor de desenvolvimento e homologação do militar.
- Geração correta e veloz do manifesto e dos pacotes de estilo `app-[hash].css` utilizados pelas páginas Volt.
- Repositório mantido estritamente enxuto e livre de arquivos espúrios.
