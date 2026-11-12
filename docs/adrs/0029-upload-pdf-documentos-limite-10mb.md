# 29. Upload de Arquivo PDF Oficial Limitado a 10 MB em Novo Documento

Data: 2026-03-31

## Status
Aprovado

## Contexto
Ao registrar documentos de referência para Lições Aprendidas e SIPA-SUB (como Relatórios Finais de Comissão - RFC, Análises de RFC - ARFC, Relatórios Técnicos de Engenharia - RTE, etc.), torna-se imperativo que o militar notificante possa anexar a via digitalizada ou original do documento oficial em formato PDF.
Para assegurar a integridade do armazenamento, performance da aplicação e conformidade com os padrões de segurança da informação da Marinha do Brasil, estabeleceu-se o limite máximo de **10 MB** por arquivo.

## Decisão
1. **Suporte a Upload em Livewire Volt (`pages.licoes.documento`)**:
   - Inclusão do trait `Livewire\WithFileUploads`.
   - Propriedade reativa `$arquivo_pdf` para recepção do binário do arquivo.
   - Regras de validação estritas no método `salvar()`:
     - `'arquivo_pdf' => 'nullable|file|mimes:pdf|max:10240'` (10.240 KB = 10 MB).
     - Mensagens customizadas em português: proibição de formatos não-PDF e bloqueio de arquivos excedentes a 10 MB.
   - Extração e persistência dos metadados (`arquivo_pdf_nome` e `arquivo_pdf_tamanho`).
2. **Interface Visual e Experiência do Usuário (Argon Pro UX)**:
   - Área de dropzone estilizada com drag-and-drop e botão para seleção manual.
   - Card de feedback imediato ao anexar o PDF: exibição do ícone naval de documento, nome original do arquivo, tamanho formatado em MB com precisão de duas casas decimais e botão para remoção/substituição antes da homologação.
   - Alerta visual e bloqueio imediato se o militar tentar selecionar arquivos com formatos distintos de `.pdf` ou superiores a 10 MB.
3. **Paridade no Componente React (`NovoDocumentoView.tsx`)**:
   - Validação no `onChange` para tamanho máximo de `10 * 1024 * 1024` bytes e mime-type `application/pdf`.
   - Persistência dos metadados no repositório de documentos do `localStorage`.

## Consequências
- A base de documentos de referência do ComForS agora armazena não apenas metadados e ementas, mas também o arquivo PDF probatório.
- O limite de 10 MB preserva a estabilidade operacional e atende plenamente aos relatórios de comissão da Força.
