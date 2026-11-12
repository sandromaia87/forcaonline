# ADR 0022: Vinculação Dinâmica de Filtros, Comissão, Ano e Repositório Limpo

## Status
Aprovado / Implementado

## Data
11 de Novembro de 2026

## Contexto
Correção dos filtros da tela de pesquisa para vinculação direta aos CRUDs das tabelas de apoio, adição de filtros por Comissão e por Ano da Lição, e limpeza total dos registros mockados iniciais.

## Decisão de Arquitetura
1. Filtros populados a partir de `crud_meios_navais`, `crud_subsistemas` e `crud_comissoes`.
2. Extração dinâmica de anos únicos a partir dos registros cadastrados.
3. Listagem inicial `[]` vazia com cartão informativo e botão "+ Cadastrar Primeira Lição".

## Consequências
Pesquisa 100% conectada aos dados reais inseridos pelos operadores.
