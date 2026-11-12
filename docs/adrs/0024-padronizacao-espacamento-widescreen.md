# ADR 0024: Padronização de Espaçamento Widescreen sem Restrição de Largura

## Status
Aprovado / Implementado

## Data
11 de Novembro de 2026

## Contexto
Em monitores WideScreen, as telas com `max-w-6xl mx-auto` ou `max-w-5xl mx-auto` ficavam centralizadas com grandes margens ociosas nas laterais, enquanto Pesquisar e Comissões aproveitavam melhor a largura.

## Decisão de Arquitetura
1. Remoção das classes `max-w-* mx-auto` dos containers raiz de `dashboard.blade.php`, `nova.blade.php`, `meios-navais.blade.php`, `subsistemas.blade.php` e `pesquisar.blade.php`.
2. Padronização com `space-y-6 select-none` utilizando 100% da largura útil da viewport.

## Consequências
Experiência consistente em resoluções Full HD, Quad HD e UltraWide.
