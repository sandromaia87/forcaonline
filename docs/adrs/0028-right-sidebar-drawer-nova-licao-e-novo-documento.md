# 28. Padronização do Drawer Modal Lateral à Direita em Nova Lição e Novo Documento

Data: 2026-03-31

## Status
Aprovado

## Contexto
Após a introdução do painel lateral para inclusão rápida de entidades sem perder o contexto do preenchimento:
1. **Posicionamento à Direita (Right Sidebar Modal)**: Solicitado pelo usuário que o drawer modal deslizante não ocupe o lado esquerdo (onde já se situa o menu fixo de navegação lateral da aplicação), posicionando-se à **DIREITA** (`fixed inset-y-0 right-0`), garantindo harmonia visual, sem sobreposição ao menu principal do ComForS.
2. **Extensão ao Módulo de Documentos**: A tela de cadastro de "Novo Documento" (`pages.licoes.documento` e `NovoDocumentoView.tsx`) passa a utilizar exatamente o mesmo padrão de Right Sidebar Modal nos botões de "Adicionar Novo" para:
   - **Tipo de Documento**: Sigla, Nome, Descrição e Status (persistindo na 4ª tabela de apoio e atualizando em tempo real o número único gerado);
   - **Meio Naval / OM**: Indicativo Naval, Nome do Meio/OM, Tipo, Classe/Projeto e Status;
   - **Comissão Operacional**: Código da Comissão, Nome da Missão, Tipo de Operação, Ano e Status.

## Decisão
1. **Ajuste de Posicionamento em Nova Lição**:
   - Atualizado o container do drawer para `fixed inset-y-0 right-0 max-w-md sm:max-w-lg w-full bg-[#040915] border-l border-blue-500/40 shadow-2xl flex flex-col z-50 animate-in slide-in-from-right duration-300`.
2. **Implementação do Right Sidebar Drawer em Novo Documento**:
   - Removidos os formulários inline secundários da tela de Novo Documento.
   - Adicionada a abertura do painel deslizante à direita para cadastros rápidos de novos Tipos de Documento, Meios Navais e Comissões Operacionais.
   - Ao salvar um novo Tipo de Documento, o sistema calcula e atualiza imediatamente a máscara `{SIGLA}-{ANO}-{SEQUENCIAL}` no cabeçalho do documento em preenchimento.
3. **Persistência Sincronizada**:
   - Os dados cadastrados via drawer são gravados nas sessões e coleções de apoio (`crud_tipos_documento`, `crud_meios_navais`, `crud_comissoes`), ficando imediatamente acessíveis nas telas principais e em seus respectivos CRUDs.

## Consequências
- A experiência de usuário fica consistente e sem conflito visual com o menu lateral esquerdo fixo.
- O fluxo de cadastro de documentos e lições torna-se ágil, permitindo enriquecer tabelas de apoio sob demanda diretamente do painel lateral.
