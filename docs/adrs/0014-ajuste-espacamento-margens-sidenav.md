# ADR 0014: Ajuste de Espaçamento e Margens Evitando Colar na Sidenav

## Status
Aprovado / Implementado

## Data
11 de Novembro de 2026

## Contexto
O usuário notou que o conteúdo principal estava visualmente encostado na linha divisória da barra lateral.

## Decisão de Arquitetura
1. Ajuste do padding da tag `<main>` para `px-6 sm:px-8 lg:px-10 py-8 pb-16`.
2. Alinhamento com a largura fixa da sidebar (`lg:ml-64 xl:ml-72`).

## Consequências
Separação visual nítida e harmônica com a estética Argon Naval.
