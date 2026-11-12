# ADR 0025: Inclusão do Link de Comissões no Sidebar e Sincronização Geral

## Status
Aprovado / Implementado

## Data
12 de Novembro de 2026

## Contexto
Identificou-se que o link de acesso direto à 3ª tabela de apoio ("Comissões Operacionais") estava ausente dentro do menu accordion de `navigation.blade.php`. Além disso, foi realizada a consolidação completa de todos os ADRs e orientações de execução pós-git-pull.

## Decisão de Arquitetura
1. Inclusão da opção "Comissões Operacionais" no accordion da barra lateral.
2. Atualização e consolidação documental de todos os ADRs de 0011 a 0025.
3. Orientações claras quanto à execução de `php artisan optimize:clear` e compilação do Vite após o git pull.

## Consequências
Sincronização 100% idêntica entre o repositório Laravel e o ambiente de desenvolvimento.
