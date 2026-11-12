# ADR 0013: Indicador de Passos no Login, Submarinos da Classe Tikuna e Dropdown do Militar

## Status
Aprovado / Implementado

## Data
10 de Novembro de 2026

## Contexto
Aprimoramento da tela de login com indicação dos passos de autenticação (NIP, Módulo, Senha), inclusão da Classe Tikuna e dos cascos S30 a S44 no card lateral, além de migrar o link "Minha Conta" do sidebar para um dropdown no topo.

## Decisão de Arquitetura
1. Indicador visual com steps 1. NIP / 2. Módulo / 3. Senha.
2. Atualização do card informativo com Classe Tikuna e indicativos S30, S34, S40, S41, S42, S43, S44.
3. Menu dropdown na barra superior ao clicar na identificação do militar, contendo "Minha Conta" e "Sair".
4. Remoção do link "Minha Conta" da barra lateral fixa.

## Consequências
Interface mais limpa no sidebar e experiência de login mais fluida e instrutiva.
