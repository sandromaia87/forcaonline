export interface UserRecord {
  id: number;
  name: string;
  nip: string;
  email: string;
  modulo_acesso: 'ambos' | 'licoes_aprendidas' | 'sipa_sub';
  password: string;
}

// Usuários configurados conforme DatabaseSeeder.php
export const MOCK_USERS: UserRecord[] = [
  // NIP Administrador Geral (OCULTO - Não demonstrado no front)
  {
    id: 1,
    name: 'Administrador Geral',
    nip: '09097074',
    email: 'admin.geral@marinha.mil.br',
    modulo_acesso: 'ambos',
    password: 'senha123',
  },
  // NIP Função 1: Lições Aprendidas
  {
    id: 2,
    name: 'Operador Lições Aprendidas',
    nip: '11111111',
    email: 'licoes@marinha.mil.br',
    modulo_acesso: 'licoes_aprendidas',
    password: 'senha123',
  },
  // NIP Função 2: SIPA-SUB
  {
    id: 3,
    name: 'Operador SIPA-SUB',
    nip: '22222222',
    email: 'sipa@marinha.mil.br',
    modulo_acesso: 'sipa_sub',
    password: 'senha123',
  },
  // NIP com ambas as funções
  {
    id: 4,
    name: 'Operador Multi-Módulos',
    nip: '33333333',
    email: 'ambos@marinha.mil.br',
    modulo_acesso: 'ambos',
    password: 'senha123',
  },
];
