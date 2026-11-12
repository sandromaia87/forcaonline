# ADR 0021: Comissões Operacionais, Vínculo de RFC e Guia na Nova Lição

## Status
Aprovado / Implementado

## Data
11 de Novembro de 2026

## Contexto
Adição da 3ª tabela de apoio (Comissões Operacionais), campo de seleção de RFC (Relatório Final de Comissão - SIPA-SUB), campo Comissão com recurso de adicionar novo na tela de Nova Lição e indicador de construção em todas as 3 tabelas de apoio e na pesquisa.

## Decisão de Arquitetura
1. Rota e view `licoes.comissoes`.
2. Campos de comissão dinâmica e seleção de RFC em `nova.blade.php`.
3. Banners visuais de homologação/construção nas 3 tabelas de apoio e pesquisa.

## Consequências
Integração futura facilitada com o módulo SIPA-SUB e conformidade doutrinária.
