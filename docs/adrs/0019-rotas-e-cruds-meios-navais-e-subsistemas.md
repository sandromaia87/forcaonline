# ADR 0019: Rotas e CRUDs em Accordion para Meios Navais e Subsistemas

## Status
Aprovado / Implementado

## Data
11 de Novembro de 2026

## Contexto
Criação de menus recolhidos em accordion no sidebar para gestão de tabelas de apoio (Meios Navais e Sistemas/Subsistemas Afetados).

## Decisão de Arquitetura
1. Implementação do grupo expansível em Alpine.js no sidebar.
2. Criação das rotas `licoes.meios-navais` e `licoes.subsistemas` com componentes dedicados.

## Consequências
Organização modular das tabelas de apoio sem poluir a navegação primária.
