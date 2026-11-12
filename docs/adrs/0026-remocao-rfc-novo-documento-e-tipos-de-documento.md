# 26. Remoção do Vínculo de RFC, Criação de "Novo Documento" e Tabela de Apoio "Tipos de Documento"

Data: 2026-03-31

## Status
Aprovado

## Contexto
O processo de homologação das Lições Aprendidas no âmbito do Comando da Força de Submarinos (ComForS) necessitou de uma evolução estrutural em duas frentes:
1. **Desacoplamento Temporário do RFC**: Na tela de cadastro de Nova Lição (`pages.licoes.nova`), o vínculo direto com "Relatório Final de Comissão (RFC)" foi removido momentaneamente, pois os relatórios e demais atos documentais serão formalmente geridos em módulo documental dedicado do SIPA-SUB / Lições Aprendidas.
2. **Criação de Documentos de Referência**: Necessidade de registrar e catalogar documentos que servem de fundamentação/referência para lições aprendidas, demandando um fluxo próprio de entrada ("Novo Documento").
3. **Padronização de Numeração Única por Sigla**: Como os tipos de documentos variam (RFC - Relatório Final de Comissão, ARFC - Análise de Relatório Final de Comissão, RTE - Relatório Técnico de Engenharia, RCI - Relatório de Comissão de Inquérito, etc.), tornou-se imperativo criar uma tabela de apoio específica para "Tipos de Documento", onde a sigla cadastrada alimenta a regra de geração do identificador único no padrão `{SIGLA}-{ANO}-{SEQUENCIAL}` (ex.: `RFC-2026-0001`, `ARFC-2026-0001`).

## Decisão
1. **Remoção de RFC em Nova Lição**:
   - Exclusão do select de RFC e de seu modal de visualização em `resources/views/livewire/pages/licoes/nova.blade.php`.
   - Ajuste do grid da Linha 2 para 2 colunas proporcionais (Comissão Operacional e Data da Ocorrência).
2. **Novo Link e Rota de "Novo Documento"**:
   - Rota `/documento` registrada em `routes/web.php` apontando para o componente Volt `pages.licoes.documento`.
   - Inserção do link "Novo Documento" no menu lateral (`navigation.blade.php`) posicionado imediatamente abaixo de "Nova Lição", com ícone de documento normativo.
3. **Criação da 4ª Tabela de Apoio "Tipos de Documento"**:
   - Rota `/tipos-documento` registrada em `routes/web.php` apontando para `pages.licoes.tipos-documento`.
   - Inserção do link na seção de Tabelas de Apoio do menu accordion.
   - Atributos cadastrais: Sigla do Documento (uppercase, máx 10 caracteres), Nome do Tipo Documental, Descrição/Aplicação Doutrinária, Status (Ativo/Inativo) e preview em tempo real da máscara gerada (`{SIGLA}-AAAA-XXXX`).
   - Modal explicativo doutrinário com instruções de parametrização de siglas navais.
4. **Tela de Cadastro "Novo Documento"**:
   - Seleção do Tipo de Documento com atalho modal inline para cadastrar novo tipo caso ainda não exista.
   - Lógica reativa no Livewire/Volt (`updatedTipoDocId`): ao selecionar a sigla, calcula o ano corrente e gera automaticamente o número único no padrão formal da Força.
   - Campos de cabeçalho: Meio Naval / OM Emissora, Comissão Operacional associada, Grau de Sigilo (Reservado, Confidencial, Secreto, Ostensivo), Data de Emissão, Título do Documento e Ementa/Resumo Executivo.
   - Banner militar de homologação operacional e modal de apoio instrucional.
5. **Padronização Visual**:
   - Utilização do padrão Widescreen container sem margens laterais excessivas (`space-y-6 select-none` com `w-full`).

## Consequências
- A rastreabilidade documental passa a ser unificada sob tipos e siglas bem definidos.
- Cada tipo documental garante sua própria sequência cronológica e sigla oficial.
- Nova Lição permanece limpa e focada no relato do fato observado, pronta para ser vinculada aos documentos gerados nesta nova base.
