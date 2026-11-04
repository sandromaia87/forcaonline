# ADR 0008: Adoção do Padrão Arquitetural Argon Dashboard Pro para Interfaces Pós-Autenticação

## Status
Aprovado / Implementado

## Data
04 de Novembro de 2026

## Contexto
Após a implementação inicial dos módulos operacionais da ForçaOnline (*Lições Aprendidas* e *SIPA-SUB*), identificou-se a necessidade de alinhar a interface pós-login aos padrões consolidados de mercado para dashboards corporativos e operacionais (especificamente referenciando a consagrada arquitetura do **Argon Dashboard Pro Laravel**). 

Anteriormente, a apresentação limitava-se a um cartão centralizado de boas-vindas. Era imperativo evoluir para uma visão executiva completa, mantendo a identidade visual naval do Comando da Força de Submarinos (ComForS), a paleta de cores escura (`#030712`, `#060f1d`, ouro naval `#eab308` e azul profundo `#3b82f6`), o isolamento estrito entre módulos e a imagem institucional do submarino integrada de forma sutil e sofisticada como marca d'água no plano de fundo.

## Decisão de Arquitetura

Adotou-se a estrutura em dois painéis do padrão Argon Dashboard Pro, composta por:

1. **Sidenav Lateral (Sidebar Esquerda)**:
   - Fixa em resoluções desktop (`w-64` / `w-72`) e gaveta retrátil (*drawer*) em dispositivos móveis.
   - Cabeçalho institucional com o Brasão oficial do ComForS e tipografia `Oswald` (*ForçaOnline*).
   - Indicador contextual do módulo ativo em sessão, garantindo isolamento tático sem disponibilizar cruzamento de rotas.
   - Menu de navegação estilizado com contêineres de ícones coloridos com gradientes sutis e estados ativos iluminados.
   - Cartão inferior institucional de apoio operacional com o lema *“USQUE AD SUB ACQUAM NAUTA SUM”* e identificação do CLTI.

2. **Topbar Superior Glassmorphic (Navbar Superior Fixa)**:
   - Alinhada à área principal com *backdrop-blur* e transparência naval.
   - Navegação em migalhas de pão (*Breadcrumbs*: `Páginas / [Módulo Atual]`) e título da tela em destaque.
   - Campo de busca rápida contextual.
   - Crachá do militar autenticado (Posto/Graduação, Nome e NIP).
   - Botão de saída rápida (*Logout*), assegurando o encerramento seguro da sessão operacional para alternância de funções.

3. **Fundo Tático com Marca d'Água do Submarino**:
   - Integração da imagem `public/img/submarino.webp` como plano de fundo fixo com opacidade controlada (~12%), filtro de escala de cinza e gradiente naval escuro sobreposto (`from-[#030712]/95 via-[#060f1d]/90 to-[#02050c]/98`), conferindo imersão militar sem comprometer a leitura ou o contraste WCAG AA.

4. **Linha de 4 Cards de Métricas Operacionais (Top Stat Cards)**:
   - Quatro cartões de destaque no topo com tipografia técnica, valor métrico de grande porte, indicador percentual/temporal de tendência e contêineres com ícones em gradiente nas laterais direitas.

5. **Grid de Conteúdo Operacional Multidimensional**:
   - Linha intermediária em proporção 8:4 (tabela de registros operacionais recentes + cartão tático institucional com atalhos de ação).
   - Linha inferior em 3 colunas (equipe de quarto/serviço, procedimentos e checklists de prontidão dos meios da Esquadra).

## Consequências
- Elevação substancial do padrão visual da aplicação para o nível de plataformas SaaS profissionais de grande porte.
- Máxima usabilidade, organização clara de informações e visibilidade instantânea do estado operacional dos submarinos.
- Conformidade total com os requisitos de isolamento estrito de módulos e preservação da autoria técnica permanente.
