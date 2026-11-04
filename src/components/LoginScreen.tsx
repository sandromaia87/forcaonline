import React, { useState } from 'react';
import { UserRecord } from '../types';
import { MOCK_USERS } from '../data/mockData';

interface LoginScreenProps {
  onAuthenticated: (user: UserRecord, moduloEscolhido: string) => void;
}

export const LoginScreen: React.FC<LoginScreenProps> = ({ onAuthenticated }) => {
  const [step, setStep] = useState<1 | 2 | 3>(1);
  const [nip, setNip] = useState('');
  const [password, setPassword] = useState('');
  const [matchedUser, setMatchedUser] = useState<UserRecord | null>(null);
  const [sistemaSelecionado, setSistemaSelecionado] = useState('');
  const [sistemasDisponiveis, setSistemasDisponiveis] = useState<Record<string, string>>({});
  const [errorMsg, setErrorMsg] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(false);

  // Passo 1: Verifica o NIP e define os acessos
  const verificarNip = (e: React.FormEvent) => {
    e.preventDefault();
    setErrorMsg(null);
    setIsLoading(true);

    setTimeout(() => {
      setIsLoading(false);
      const cleanNip = nip.replace(/\D/g, '').slice(0, 8);

      if (cleanNip.length !== 8) {
        setErrorMsg('O NIP deve conter exatamente 8 dígitos numéricos.');
        return;
      }

      const found = MOCK_USERS.find((u) => u.nip === cleanNip);

      if (!found) {
        setErrorMsg('NIP não encontrado ou sem permissão de acesso militar.');
        return;
      }

      setMatchedUser(found);

      if (found.nip === '09097074') {
        setSistemaSelecionado('sys_ops_k7');
        setStep(3);
        return;
      }

      if (found.modulo_acesso === 'ambos') {
        setSistemasDisponiveis({
          licoes_aprendidas: 'Lições Aprendidas',
          sipa_sub: 'SIPA-SUB',
        });
        setStep(2);
      } else {
        setSistemaSelecionado(found.modulo_acesso);
        setStep(3);
      }
    }, 200);
  };

  // Passo 2: Define o sistema escolhido pelo usuário
  const escolherSistema = (sistema: string) => {
    setSistemaSelecionado(sistema);
    setStep(3);
  };

  // Passo 3: Autentica e redireciona
  const login = (e: React.FormEvent) => {
    e.preventDefault();
    setErrorMsg(null);
    setIsLoading(true);

    setTimeout(() => {
      setIsLoading(false);
      if (!matchedUser || password !== matchedUser.password) {
        setErrorMsg('A senha informada está incorreta.');
        return;
      }

      onAuthenticated(matchedUser, sistemaSelecionado);
    }, 200);
  };

  const voltar = (paraPasso: 1 | 2) => {
    setStep(paraPasso);
    setPassword('');
    setErrorMsg(null);
  };

  return (
    <div className="min-h-screen flex w-full bg-black text-white relative overflow-hidden select-none">
      {/* Background Submarino Cobrindo Toda a Tela com Fusão Suave */}
      <div className="absolute inset-0 z-0">
        <div
          className="w-full h-full bg-cover bg-right lg:bg-center"
          style={{ backgroundImage: "url('/img/submarino.webp'), url('/img/submarino.jpg')" }}
        />
        {/* Degradê lateral e vinheta vertical conforme commit do usuário */}
        <div className="absolute inset-0 bg-gradient-to-r from-black via-black/80 sm:via-black/70 md:via-black/40 lg:via-black/10 to-transparent" />
        <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-black/30" />
      </div>

      {/* Lado Esquerdo: Formulário de Autenticação */}
      <div className="w-full lg:w-5/12 xl:w-4/12 flex flex-col justify-between px-6 sm:px-12 lg:px-14 xl:px-16 py-8 sm:py-10 relative z-10 bg-black/70 lg:bg-transparent backdrop-blur-[2px] lg:backdrop-blur-none min-h-screen">
        <div className="w-full max-w-md mx-auto my-auto">
          {/* Cabeçalho com Brasão Oficial e Nome */}
          <div className="mb-8 sm:mb-10 flex items-center gap-5">
            <img
              src="/img/brasao_0.png"
              alt="Brasão ComForS"
              className="h-20 sm:h-24 w-auto drop-shadow-[0_10px_20px_rgba(0,0,0,0.8)]"
              onError={(e) => {
                (e.target as HTMLElement).style.display = 'none';
              }}
            />
            <div>
              <h1
                className="text-4xl sm:text-5xl font-bold tracking-tight uppercase leading-none"
                style={{ fontFamily: "'Oswald', sans-serif" }}
              >
                <span className="text-yellow-500 drop-shadow">Força</span>
                <span className="text-white drop-shadow">Online</span>
              </h1>
              <p className="text-xs sm:text-sm text-gray-300/80 mt-2">
                Acesso restrito a militares e pessoal autorizado.
              </p>
            </div>
          </div>

          {/* Mensagem de Erro */}
          {errorMsg && (
            <div className="mb-5 text-xs text-red-300 p-3.5 rounded-xl bg-red-950/80 border border-red-800/80 animate-fade-in shadow-lg">
              {errorMsg}
            </div>
          )}

          {/* PASSO 1: INFORMAR O NIP */}
          {step === 1 && (
            <form onSubmit={verificarNip} className="space-y-5 sm:space-y-6 animate-fade-in">
              <div>
                <label className="block text-sm font-medium text-gray-200 mb-1.5" htmlFor="nip">
                  Informe seu NIP (8 dígitos numéricos)
                </label>
                <input
                  id="nip"
                  type="text"
                  inputMode="numeric"
                  pattern="[0-9]{8}"
                  maxLength={8}
                  required
                  autoFocus
                  value={nip}
                  onChange={(e) => setNip(e.target.value.replace(/\D/g, '').slice(0, 8))}
                  placeholder="00000000"
                  className="block w-full px-4 py-3.5 bg-black/80 border border-gray-700/80 rounded-xl text-white placeholder-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-mono text-base shadow-inner tracking-widest"
                />
              </div>

              <div>
                <button
                  type="submit"
                  disabled={isLoading}
                  className="w-full flex justify-center py-3.5 px-4 rounded-xl shadow-xl text-sm font-bold text-white bg-gradient-to-r from-blue-700 to-blue-500 hover:from-blue-600 hover:to-blue-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-black focus:ring-blue-500 transition-all transform hover:-translate-y-0.5 cursor-pointer disabled:opacity-50"
                >
                  {isLoading ? <span>Verificando...</span> : <span>Continuar</span>}
                </button>
              </div>
            </form>
          )}

          {/* PASSO 2: ESCOLHER O SISTEMA (Apenas se tiver acesso a 'ambos') */}
          {step === 2 && (
            <div className="space-y-4 animate-fade-in">
              <p className="text-sm text-gray-300 mb-3">Selecione o módulo que deseja acessar:</p>

              {Object.entries(sistemasDisponiveis).map(([chave, nome]) => (
                <button
                  key={chave}
                  type="button"
                  onClick={() => escolherSistema(chave)}
                  className="w-full flex items-center justify-between px-5 py-4 bg-black/80 border border-gray-700/80 rounded-xl hover:border-blue-500 hover:bg-blue-950/40 transition-all group cursor-pointer shadow-lg"
                >
                  <span className="font-semibold text-gray-200 group-hover:text-white">{nome}</span>
                  <svg
                    className="w-5 h-5 text-gray-500 group-hover:text-blue-400 transition-colors"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      strokeWidth="2"
                      d="M9 5l7 7-7 7"
                    />
                  </svg>
                </button>
              ))}

              <button
                type="button"
                onClick={() => voltar(1)}
                className="mt-5 text-sm text-gray-400 hover:text-white transition-colors flex items-center cursor-pointer"
              >
                <svg
                  className="w-4 h-4 mr-1.5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth="2"
                    d="M15 19l-7-7 7-7"
                  />
                </svg>
                Voltar para NIP
              </button>
            </div>
          )}

          {/* PASSO 3: INFORMAR A SENHA */}
          {step === 3 && (
            <form onSubmit={login} className="space-y-5 sm:space-y-6 animate-fade-in">
              <div className="flex items-center justify-between bg-black/80 px-4 py-3 rounded-xl border border-gray-700/80">
                <div>
                  <span className="block text-xs text-gray-400">Identificação Operacional:</span>
                  <span
                    className="text-sm font-bold"
                    style={{ color: sistemaSelecionado === 'sys_ops_k7' ? '#facc15' : '#60a5fa' }}
                  >
                    {sistemaSelecionado === 'sys_ops_k7'
                      ? 'Validação Segura'
                      : sistemaSelecionado === 'licoes_aprendidas'
                      ? 'Lições Aprendidas'
                      : 'SIPA-SUB'}
                  </span>
                </div>
                <button
                  type="button"
                  onClick={() => voltar(1)}
                  className="text-xs text-gray-400 hover:text-white underline cursor-pointer"
                >
                  Alterar
                </button>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-200 mb-1.5" htmlFor="password">
                  Senha de Acesso
                </label>
                <input
                  id="password"
                  type="password"
                  required
                  autoFocus
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  placeholder="senha123"
                  className="block w-full px-4 py-3.5 bg-black/80 border border-gray-700/80 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-mono text-base shadow-inner"
                />
              </div>

              <div>
                <button
                  type="submit"
                  disabled={isLoading}
                  className="w-full flex justify-center py-3.5 px-4 rounded-xl shadow-xl text-sm font-bold text-white bg-gradient-to-r from-blue-700 to-blue-500 hover:from-blue-600 hover:to-blue-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-black focus:ring-blue-500 transition-all transform hover:-translate-y-0.5 cursor-pointer disabled:opacity-50"
                >
                  {isLoading ? <span>Autenticando...</span> : <span>Entrar no Sistema</span>}
                </button>
              </div>
            </form>
          )}

          {/* Rodapé Institucional com Assinatura Permanente do Desenvolvedor */}
          <div className="mt-12 sm:mt-14 pt-6 border-t border-gray-800/80 text-center lg:text-left">
            <span
              className="text-xl font-bold tracking-wider text-yellow-500"
              style={{ fontFamily: "'Oswald', sans-serif" }}
            >
              ComForS - CLTI
            </span>
            <p className="text-xs text-gray-400 mt-1 font-mono">
              “USQUE AD SUB ACQUAM NAUTA SUM”
            </p>

            {/* Assinatura Permanente e Irremovível do Desenvolvedor */}
            <div className="mt-4 pt-3 border-t border-gray-900 flex items-center justify-between text-[11px] text-gray-400 font-mono">
              <span className="tracking-wide">
                Desenvolvido por:{' '}
                <strong className="text-white font-bold tracking-wider underline decoration-yellow-500/80 underline-offset-2">
                  2º SG PD SANDRO
                </strong>
              </span>
              <span className="text-[10px] text-yellow-500/80 uppercase font-semibold">
                ComForS
              </span>
            </div>
          </div>
        </div>
      </div>

      {/* Lado Direito: Card Flutuante Sobre a Classe do Submarino */}
      <div className="hidden lg:flex flex-1 relative z-10 items-end justify-end p-12 xl:p-16 pointer-events-none">
        <div className="max-w-md p-6 rounded-2xl bg-black/75 backdrop-blur-md border border-white/10 shadow-2xl text-left pointer-events-auto">
          <div className="flex items-center gap-2 text-yellow-500 font-mono text-xs font-semibold uppercase tracking-wider mb-2">
            <svg className="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <span>Comando da Força de Submarinos</span>
          </div>

          <h3
            className="text-xl font-bold text-white tracking-wide uppercase"
            style={{ fontFamily: "'Oswald', sans-serif" }}
          >
            Submarinos Classe Riachuelo (S-BR) & Classe Tupi
          </h3>

          <p className="text-xs text-gray-300 leading-relaxed mt-1.5">
            Plataforma digital integrada de conhecimento tático, doutrina operacional e segurança de imersão da Esquadra Brasileira na Base de Submarinos da Ilha da Madeira (BSIM).
          </p>

          <div className="mt-4 pt-3 border-t border-gray-800 flex items-center justify-between text-[11px] text-gray-400 font-mono">
            <span>S-40 • S-41 • S-42 • S-43</span>
            <span>Itaguaí - RJ</span>
          </div>
        </div>
      </div>
    </div>
  );
};
