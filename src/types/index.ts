export interface UserRecord {
  id: number;
  name: string;
  nip: string;
  email: string;
  modulo_acesso: 'ambos' | 'licoes_aprendidas' | 'sipa_sub';
  password: string;
}
