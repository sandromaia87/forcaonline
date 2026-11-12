# 27. Correção de Variável no Modal Guia, Pré-Cadastro de RFC/ARFC e Left Sidebar Modal em Lições Aprendidas

Data: 2026-03-31

## Status
Aprovado

## Contexto
Durante a validação operacional dos módulos de apoio e de Lições Aprendidas no âmbito do ComForS:
1. **Erro de Renderização Blade**: Os componentes de apoio `meios-navais.blade.php` e `subsistemas.blade.php` lançavam a exceção PHP `Undefined variable $modalGuiaAberto` ao tentar renderizar a diretiva `@if($modalGuiaAberto)` sem que a propriedade e seus métodos de abertura/fechamento estivessem declarados na classe Volt.
2. **Pré-cadastros de Tipos de Documento**: Identificou-se a necessidade doutrinária de já disponibilizar pré-cadastrados os tipos `RFC` (Relatório Final de Comissão) e `ARFC` (Análise de Relatório Final de Comissão) na 4ª tabela de apoio e na tela de Novo Documento, garantindo que o sistema inicie com a base mínima oficial enquanto permite novos registros pelos militares.
3. **Left Sidebar Modal (Drawer Lateral) para Inclusão Rápida**: Na tela de cadastro de Lição Aprendida (`nova.blade.php`), o acionamento de "+ Adicionar Novo" para Meio Naval, Subsistema Afetado ou Comissão Operacional deve proporcionar uma experiência fluida sem perder o contexto do preenchimento da lição. A interface deve abrir um modal deslizante pela lateral esquerda (estilo Argon Drawer / Left Sidebar Modal), contendo exatamente os mesmos campos completos da respectiva tabela de apoio.

## Decisão
1. **Correção de Variáveis em Volt**:
   - Declaração de `public bool $modalGuiaAberto = false;` e métodos `abrirGuia()` / `fecharGuia()` em `resources/views/livewire/pages/licoes/meios-navais.blade.php` e `resources/views/livewire/pages/licoes/subsistemas.blade.php`.
2. **Inicialização Oficial de RFC e ARFC**:
   - Definição dos registros padrão de `RFC` e `ARFC` em `pages.licoes.tipos-documento` e `pages.licoes.documento`, sincronizados via sessão `crud_tipos_documento`.
   - Replicação imediata na visualização React com `localStorage` (`forcaonline_tipos_documento`).
3. **Implementação do Left Sidebar Modal (Drawer Lateral)**:
   - Substituição dos blocos inline em `nova.blade.php` e `NovaLicaoView.tsx` por um componente lateral deslizante (`fixed inset-y-0 left-0 max-w-lg w-full bg-[#040915] border-r border-blue-500/40 shadow-2xl z-50 animate-in slide-in-from-left duration-300`) com backdrop escuro e blur.
   - **Campos de Meio Naval**: Indicativo Naval, Nome do Meio/OM, Tipo (Submarino/OM/Apoio), Classe/Projeto e Status Operativo.
   - **Campos de Subsistema**: Nome do Subsistema, Área Técnica/Categoria (Propulsão, Governo, Eletricidade, Sensores, etc.), Criticidade Operacional e Status.
   - **Campos de Comissão**: Código da Comissão (ex: TROPICALEX-2026), Nome da Missão, Tipo de Operação, Ano e Status.
   - Ao salvar no modal lateral, o novo item é persistido na lista da sessão/tabela de apoio e automaticamente selecionado no campo correspondente da lição.

## Consequências
- Eliminação do erro fatal de variável indefinida no Blade.
- A tela de documentos inicia pronta para emitir números oficiais nos padrões `RFC-2026-XXXX` e `ARFC-2026-XXXX`.
- O militar tem a conveniência de cadastrar novos meios, subsistemas e comissões com dados ricos e oficiais sem sair da tela de redação da lição.
