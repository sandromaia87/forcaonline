# ADR 0018: Remoção de Card Redundante na Tela de Nova Lição

## Status
Aprovado / Implementado

## Data
11 de Novembro de 2026

## Contexto
O usuário solicitou remover a div de título na tela de Nova Lição pois o cabeçalho superior (navbar) já contempla a identificação da página.

## Decisão de Arquitetura
1. Eliminação do cabeçalho repetitivo no corpo da view `nova.blade.php`.

## Consequências
Eliminação de redundância visual e aproveitamento otimizado da tela.
