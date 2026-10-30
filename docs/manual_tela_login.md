# Manual Operacional e Especificação Técnica: Tela de Login Militar — ForçaOnline

**Comando da Força de Submarinos (ComForS) — Centro de Liderança e Tecnologia da Informação (CLTI)**  
*“USQUE AD SUB ACQUAM NAUTA SUM”*  
**Autor e Desenvolvedor:** 2º SG PD SANDRO  
**Módulos Integrados:** Lições Aprendidas & SIPA-SUB  
**Versão da Documentação:** 1.0.0  

---

## 1. Visão Geral e Finalidade

O **ForçaOnline** é a plataforma digital unificada do Comando da Força de Submarinos (ComForS) da Marinha do Brasil, projetada para controlar o acesso seguro e fornecer subsídios operacionais às tripulações e oficiais da Força. 

A **Tela de Login** atua como a primeira barreira de segurança perimetral militar, implementando um fluxo de autenticação em **três etapas progressivas baseadas no NIP (Número de Inscrição do Pessoal)**, garantindo a correta segregação de acesso conforme o perfil de missão e as credenciais militares cadastradas.

---

## 2. Identidade Visual e Arquitetura de Design

A interface foi concebida para transmitir sobriedade militar, autoridade naval e foco operacional no ambiente das forças submarinas:

| Elemento | Especificação Visual | Função no Sistema |
| :--- | :--- | :--- |
| **Paleta de Cores** | Preto Puro (`#000000`) com degradê contínuo | Evita divisões secas; une a área de formulário e a fotografia naval submarina em uma atmosfera única de imersão |
| **Fotografia Naval** | Submarino em trânsito no Atlântico Sul (`submarino.webp` / `submarino.jpg`) | Contextualização do ambiente operativo das forças de submarinos brasileiras |
| **Brasão ComForS** | Brasão heráldico oficial do Comando da Força (`brasao_0.png`) | Identificação institucional imediata da Organização Militar |
| **Logotipo ForçaOnline** | Tipografia *Oswald* em caixa alta: **FORÇA** (Dourado/Amarelo Naval) e **ONLINE** (Branco) | Marca visual da plataforma |
| **Lema Oficial** | *“USQUE AD SUB ACQUAM NAUTA SUM”* | Lema histórico em latim: "Mesmo sob as águas, sou marinheiro" |
| **Assinatura do Autor** | **Desenvolvido por: 2º SG PD SANDRO** | Crédito oficial e permanente de autoria no rodapé institucional |

### 2.1. Card Informativo das Classes de Submarino (Lado Direito)
Sobreposto à fotografia do submarino no canto inferior direito, o card flutuante em acrílico escuro translúcido com *backdrop-blur* informa os parâmetros da Força:
- **Título**: Submarinos Classe Riachuelo (S-BR) & Classe Tupi
- **Descrição**: Plataforma digital integrada de conhecimento tático, doutrina operacional e segurança de imersão da Esquadra Brasileira na Base de Submarinos da Ilha da Madeira (BSIM).
- **Unidades Navais Representadas**: S-40 Riachuelo, S-41 Humaitá, S-42 Tonelero e S-43 Angostura.
- **Localização de Apoio**: Complexo Naval de Itaguaí - RJ.

---

## 3. Fluxo Funcional de Autenticação em 3 Passos (Livewire Volt)

O processo de autenticação é governado pelo componente reativo `login.blade.php`, estruturado em três fases interdependentes:

```
[Início] 
   │
   ▼
┌─────────────────────────────────┐
│ Passo 1: Informar o NIP Militar │ ──(NIP Inválido)──► [Exibe Erro de Validação]
└─────────────────────────────────┘
   │ (NIP Válido e Encontrado)
   ├───────────────────────────────┐
   │ (modulo_acesso == 'ambos')    │ (modulo_acesso != 'ambos')
   ▼                               ▼
┌───────────────────────────────┐  │
│ Passo 2: Seleção de Módulo:   │  │
│  - Lições Aprendidas          │  │
│  - SIPA-SUB                   │  │
└───────────────────────────────┘  │
   │ (Módulo Selecionado)          │
   └───────────────┬───────────────┘
                   ▼
┌─────────────────────────────────┐
│ Passo 3: Informar Senha         │ ──(Senha Incorreta)──► [Exibe Erro / Rate Limit]
└─────────────────────────────────┘
   │ (Autenticação Aprovada)
   ▼
[Redireciona para o Dashboard do Módulo Selecionado]
```

---

### Passo 1: Validação do NIP (Número de Inscrição do Pessoal)
- **Campo de Entrada**: `nip` (Texto numérico, 8 dígitos).
- **Comportamento**:
  - O militar digita seu NIP e aciona o botão **"Continuar"** (ou tecla `Enter`).
  - O sistema aciona o método `verificarNip()`.
  - É realizada uma busca no banco de dados na tabela `users` pela coluna `nip`.
- **Regras de Decisão**:
  1. Se o NIP não for localizado: Retorna a mensagem de erro: *"NIP não encontrado ou sem permissão de acesso militar."*
  2. Se o usuário possuir `modulo_acesso = 'ambos'`: O sistema avança para o **Passo 2** para que o militar escolha qual área deseja acessar nesta sessão.
  3. Se o usuário possuir acesso restrito a um único módulo (`modulo_acesso = 'licoes_aprendidas'` ou `'sipa_sub'`): O sistema define automaticamente o módulo selecionado e salta diretamente para o **Passo 3** (Senha).

---

### Passo 2: Seleção de Módulo Operacional (Multi-Acesso)
- **Público-alvo**: Militares com autorização ampla (`modulo_acesso = 'ambos'`).
- **Opções Apresentadas**:
  1. **Lições Aprendidas**: Direciona para o banco doutrinário de experiências operacionais e engenharia submarina.
  2. **SIPA-SUB**: Direciona para o Sistema de Investigação e Prevenção de Acidentes de Submarinos (RELPREVs e relatórios de segurança).
- **Ações Disponíveis**:
  - Clique no módulo desejado: Armazena a escolha e avança para o **Passo 3**.
  - Link **"Voltar para NIP"**: Retorna ao Passo 1 caso o militar deseje corrigir o número digitado.

---

### Passo 3: Informar Senha e Conclusão do Login
- **Exibição Informativa**:
  - Badge em destaque com o nome do módulo selecionado (*Lições Aprendidas* ou *SIPA-SUB*).
  - Botão **"Alterar"**: Permite retornar ao Passo 2 (ou Passo 1) sem perder a sessão.
- **Campo de Entrada**: `password` (Senha mascarada, autocomplete `current-password`).
- **Ação**:
  - Aciona o método `login()`.
  - Valida a senha contra o hash criptográfico do usuário (`Auth::attempt()`).
  - Em caso de falha: Registra a tentativa no *RateLimiter* e exibe *"A senha informada está incorreta."*
  - Em caso de sucesso: 
    - Limpa o contador de tentativas (`RateLimiter::clear()`).
    - Regenera o ID de sessão (`Session::regenerate()`).
    - Grava o módulo ativo na sessão (`session(['modulo_ativo' => $sistemaSelecionado])`).
    - Redireciona o militar para a rota de destino (`licoes.dashboard` ou `sipasub.dashboard`).

---

## 4. Mapeamento de Dados e Estrutura de Banco

### 4.1. Migration da Tabela `users`
Arquivo: `database/migrations/2026_10_29_172634_add_modulo_acesso_to_users_table.php`

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('modulo_acesso')->nullable()->after('email'); // 'licoes_aprendidas', 'sipa_sub', ou 'ambos'
    $table->string('nip')->unique()->nullable()->after('id');     // NIP militar único
});
```

### 4.2. Usuário de Referência (Seeder de Homologação)
Arquivo: `database/seeders/DatabaseSeeder.php`

| Campo | Valor Padrão no Seeder | Finalidade |
| :--- | :--- | :--- |
| **name** | Sandro Maia | Nome de guerra e militar |
| **nip** | `09097074` | Credencial militar de acesso |
| **modulo_acesso** | `ambos` | Habilita os Passos 1, 2 e 3 para testes completos |
| **password** | `bcrypt('senha123')` | Senha de homologação |

---

## 5. Medidas de Segurança Implementadas

1. **Proteção contra Força Bruta (Rate Limiting)**:
   - Limite configurado de **5 tentativas consecutivas**.
   - Chave de bloqueio calculada por combinação de NIP e IP de origem (`$this->throttleKey()`).
   - Bloqueio temporário com aviso de contagem regressiva em segundos após exceder o limite.
2. **Proteção de Sessão**:
   - `Session::regenerate()` executado obrigatoriamente no momento da autenticação para proteção contra *Session Fixation*.
3. **Imutabilidade e Rastreabilidade**:
   - Rodapé com menção explícita à autoria militar do **2º SG PD SANDRO** e à Organização Militar ComForS / CLTI.

---

## 6. Checklist para Operadores e Suporte Técnico

| Sintoma Observado | Causa Provável | Procedimento de Resolução |
| :--- | :--- | :--- |
| *"NIP não encontrado ou sem permissão"* | NIP não consta na base ou militar não possui `modulo_acesso` configurado | Verificar na tabela `users` se o NIP possui 8 dígitos corretos e se a coluna `modulo_acesso` está preenchida |
| *"A senha informada está incorreta"* | Senha digitada difere do hash gravado no banco | Solicitar reset de senha via administrador do CLTI ou via comando de seeder |
| *"Muitas tentativas. Tente novamente em X segundos"* | Acionamento da proteção do RateLimiter | Aguardar o tempo estipulado ou limpar o cache de rate-limit via `php artisan cache:clear` |
