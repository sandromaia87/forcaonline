# ADR 0011: Ocultação Rígida do Painel de Admin e Reversão para Foco no Usuário

## Status
Aprovado / Implementado

## Data
10 de Novembro de 2026

## Contexto
O cliente solicitou expressamente que o painel de administrador e o NIP 09097074 sejam estritamente ocultos e que o foco seja direcionado inicialmente ao painel do usuário militar.

## Decisão de Arquitetura
1. Ocultação completa de qualquer link, indicador ou menção ao NIP 09097074 na interface de login e menus comuns.
2. Acesso ao terminal operacional restrito a validação direta na autenticação sem pistas visuais no frontend.
3. Foco da navegação concentrado nas atribuições do militar logado.

## Consequências
Ambiente seguro conforme diretrizes do Comando da Força de Submarinos.
