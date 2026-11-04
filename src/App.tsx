import React, { useState } from 'react';
import { UserRecord } from './types';
import { LoginScreen } from './components/LoginScreen';
import { MOCK_USERS } from './data/mockData';

export const App: React.FC = () => {
  const [currentUser, setCurrentUser] = useState<UserRecord | null>(null);
  const [moduloAtivo, setModuloAtivo] = useState<string>('');
  const [usersList, setUsersList] = useState<UserRecord[]>(MOCK_USERS);
  const [busca, setBusca] = useState('');
  const [mensagemSucesso, setMensagemSucesso] = useState<string | null>(null);
  const [mensagemErro, setMensagemErro] = useState<string | null>(null);
  const [sidebarOpen, setSidebarOpen] = useState(false);

  // Modais do CRUD
  const [modalAberto, setModalAberto] = useState(false);
  const [isEdicao, setIsEdicao] = useState(false);
  const [usuarioEditandoId, setUsuarioEditandoId] = useState<number | null>(null);
  const [formNome, setFormNome] = useState('');
  const [formNip, setFormNip] = useState('');
  const [formEmail, setFormEmail] = useState('');
  const [formModulo, setFormModulo] = useState<'licoes_aprendidas' | 'sipa_sub' | 'ambos'>('licoes_aprendidas');
  const [formSenha, setFormSenha] = useState('');

  // Modal Exclusão
  const [modalExclusaoAberto, setModalExclusaoAberto] = useState(false);
  const [usuarioParaExcluir, setUsuarioParaExcluir] = useState<UserRecord | null>(null);

  const handleAuthenticated = (user: UserRecord, modulo: string) => {
    setCurrentUser(user);
    setModuloAtivo(modulo);
  };

  const handleLogout = () => {
    setCurrentUser(null);
    setModuloAtivo('');
    setMensagemSucesso(null);
    setMensagemErro(null);
    setSidebarOpen(false);
  };

  const abrirModalCriar = () => {
    setIsEdicao(false);
    setUsuarioEditandoId(null);
    setFormNome('');
    setFormNip('');
    setFormEmail('');
    setFormModulo('licoes_aprendidas');
    setFormSenha('');
    setModalAberto(true);
  };

  const abrirModalEditar = (u: UserRecord) => {
    setIsEdicao(true);
    setUsuarioEditandoId(u.id);
    setFormNome(u.name);
    setFormNip(u.nip);
    setFormEmail(u.email);
    setFormModulo(u.modulo_acesso);
    setFormSenha('');
    setModalAberto(true);
  };

  const salvarUsuario = (e: React.FormEvent) => {
    e.preventDefault();
    const cleanNip = formNip.replace(/\D/g, '').slice(0, 8);

    if (cleanNip.length !== 8) {
      setMensagemErro('O NIP deve conter exatamente 8 dígitos numéricos.');
      return;
    }

    if (isEdicao && usuarioEditandoId) {
      setUsersList((prev) =>
        prev.map((u) => {
          if (u.id === usuarioEditandoId) {
            return {
              ...u,
              name: formNome,
              nip: cleanNip,
              email: formEmail,
              modulo_acesso: formModulo,
              password: formSenha.trim() ? formSenha : u.password,
            };
          }
          return u;
        })
      );
      setMensagemSucesso(`Militar ${formNome} atualizado com sucesso.`);
    } else {
      const existeNip = usersList.some((u) => u.nip === cleanNip);
      if (existeNip) {
        setMensagemErro('Este NIP já está cadastrado para outro militar.');
        return;
      }

      const novo: UserRecord = {
        id: Date.now(),
        name: formNome,
        nip: cleanNip,
        email: formEmail,
        modulo_acesso: formModulo,
        password: formSenha || 'senha123',
      };
      setUsersList((prev) => [...prev, novo]);
      setMensagemSucesso(`Novo militar ${formNome} cadastrado com sucesso.`);
    }

    setModalAberto(false);
    setMensagemErro(null);
    setTimeout(() => setMensagemSucesso(null), 4000);
  };

  const confirmarExclusao = (u: UserRecord) => {
    if (u.nip === '09097074') {
      setMensagemErro('A credencial de comando central é permanente e não pode ser excluída.');
      return;
    }
    setUsuarioParaExcluir(u);
    setModalExclusaoAberto(true);
  };

  const excluirUsuario = () => {
    if (usuarioParaExcluir) {
      const nome = usuarioParaExcluir.name;
      setUsersList((prev) => prev.filter((u) => u.id !== usuarioParaExcluir.id));
      setModalExclusaoAberto(false);
      setUsuarioParaExcluir(null);
      setMensagemSucesso(`Credencial do militar ${nome} revogada com sucesso.`);
      setTimeout(() => setMensagemSucesso(null), 4000);
    }
  };

  if (!currentUser) {
    return <LoginScreen onAuthenticated={handleAuthenticated} />;
  }

  const isAdmin = currentUser.nip === '09097074';
  const isLicoes = moduloAtivo === 'licoes_aprendidas';
  const isSipa = moduloAtivo === 'sipa_sub';

  const usuariosFiltrados = usersList.filter((u) => {
    if (!busca.trim()) return true;
    const t = busca.toLowerCase();
    return u.name.toLowerCase().includes(t) || u.nip.includes(t) || u.email.toLowerCase().includes(t);
  });

  return (
    <div className="min-h-screen text-white flex flex-col justify-between font-sans select-none bg-[#030712] relative overflow-x-hidden">
      
      {/* ===================================================================== */}
      {/* 0. MARCA D'ÁGUA SUTIL COM IMAGEM DO SUBMARINO                         */}
      {/* ===================================================================== */}
      <div className="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <img
          src="/img/submarino.webp"
          alt=""
          className="w-full h-full object-cover opacity-12 filter grayscale contrast-125 brightness-50"
        />
        <div className="absolute inset-0 bg-gradient-to-br from-[#030712]/95 via-[#060f1d]/90 to-[#02050c]/98" />
      </div>

      {/* Backdrop Mobile */}
      {sidebarOpen && (
        <div
          onClick={() => setSidebarOpen(false)}
          className="fixed inset-0 bg-black/80 backdrop-blur-sm z-40 lg:hidden"
        />
      )}

      {/* ===================================================================== */}
      {/* 1. SIDENAV LATERAL ARGON (DESKTOP FIXA / MOBILE DRAWER)               */}
      {/* ===================================================================== */}
      <aside
        className={`fixed inset-y-0 left-0 z-50 w-64 xl:w-72 bg-[#060f1d]/95 backdrop-blur-2xl border-r border-gray-800/80 flex flex-col justify-between transition-transform duration-300 ease-in-out shadow-2xl ${
          sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
        }`}
      >
        <div>
          {/* Header Brand */}
          <div className="px-6 py-5 border-b border-gray-800/80 flex items-center justify-between">
            <div className="flex items-center gap-3">
              <img
                src="/img/brasao_0.png"
                alt="Brasão ComForS"
                className="h-10 w-auto object-contain drop-shadow-[0_2px_8px_rgba(0,0,0,0.8)]"
              />
              <div>
                <div
                  className="text-xl font-bold uppercase tracking-tight leading-none"
                  style={{ fontFamily: "'Oswald', sans-serif" }}
                >
                  <span style={{ color: '#eab308' }}>Força</span>
                  <span className="text-white">Online</span>
                </div>
                <div className="text-[10px] font-mono uppercase tracking-widest text-gray-400 mt-0.5">
                  ComForS • Marinha
                </div>
              </div>
            </div>

            <button
              onClick={() => setSidebarOpen(false)}
              className="lg:hidden text-gray-400 hover:text-white p-1 rounded-lg"
            >
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          {/* Tag de Módulo Ativo */}
          <div className="px-5 py-4">
            {isAdmin && moduloAtivo === 'sys_ops_k7' ? (
              <div className="p-3 rounded-xl bg-indigo-950/40 border border-indigo-800/60 shadow-inner flex items-center gap-3">
                <div className="w-8 h-8 rounded-lg bg-indigo-600/30 border border-indigo-500/40 flex items-center justify-center text-indigo-300">
                  <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                </div>
                <div>
                  <div className="text-[10px] uppercase font-mono tracking-wider text-indigo-400 font-semibold">Console Central</div>
                  <div className="text-xs font-bold text-white tracking-wide">Comando Supremo</div>
                </div>
              </div>
            ) : isLicoes ? (
              <div className="p-3 rounded-xl bg-blue-950/40 border border-blue-800/60 shadow-inner flex items-center gap-3">
                <div className="w-8 h-8 rounded-lg bg-blue-600/30 border border-blue-500/40 flex items-center justify-center text-blue-300">
                  <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                  </svg>
                </div>
                <div>
                  <div className="text-[10px] uppercase font-mono tracking-wider text-blue-400 font-semibold">Módulo Operacional</div>
                  <div className="text-xs font-bold text-white tracking-wide">Lições Aprendidas</div>
                </div>
              </div>
            ) : (
              <div className="p-3 rounded-xl bg-amber-950/40 border border-amber-800/60 shadow-inner flex items-center gap-3">
                <div className="w-8 h-8 rounded-lg bg-amber-600/30 border border-amber-500/40 flex items-center justify-center text-amber-300">
                  <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                  </svg>
                </div>
                <div>
                  <div className="text-[10px] uppercase font-mono tracking-wider text-amber-400 font-semibold">Módulo de Segurança</div>
                  <div className="text-xs font-bold text-white tracking-wide">SIPA-SUB</div>
                </div>
              </div>
            )}
          </div>

          {/* Links do Menu Argon */}
          <nav className="px-4 space-y-1.5 mt-2">
            <div className="px-3 pb-2 text-[10px] font-mono uppercase tracking-widest text-gray-400 font-bold">
              Menu Principal
            </div>

            {isLicoes && (
              <>
                <button
                  type="button"
                  className="w-full flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 bg-gradient-to-r from-blue-900/60 to-blue-800/40 text-white border border-blue-600/40 shadow-lg shadow-blue-950/50 text-left"
                >
                  <div className="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-500/30">
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                  </div>
                  <span>Visão Geral</span>
                </button>

                <div className="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800/50 cursor-pointer transition-all">
                  <div className="w-8 h-8 rounded-lg bg-gray-800 text-gray-300 flex items-center justify-center border border-gray-700/60">
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                  </div>
                  <span>Registro de Fainas</span>
                </div>

                <div className="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800/50 cursor-pointer transition-all">
                  <div className="w-8 h-8 rounded-lg bg-gray-800 text-gray-300 flex items-center justify-center border border-gray-700/60">
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                  </div>
                  <span>Doutrina & Tática</span>
                </div>

                <div className="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800/50 cursor-pointer transition-all">
                  <div className="w-8 h-8 rounded-lg bg-gray-800 text-gray-300 flex items-center justify-center border border-gray-700/60">
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                  </div>
                  <span>Submarinos Esquadra</span>
                </div>
              </>
            )}

            {isSipa && (
              <>
                <button
                  type="button"
                  className="w-full flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 bg-gradient-to-r from-amber-900/60 to-amber-800/40 text-white border border-amber-600/40 shadow-lg shadow-amber-950/50 text-left"
                >
                  <div className="w-8 h-8 rounded-lg bg-amber-600 text-white flex items-center justify-center shadow-md shadow-amber-500/30">
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                  </div>
                  <span>Painel SIPA</span>
                </button>

                <div className="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800/50 cursor-pointer transition-all">
                  <div className="w-8 h-8 rounded-lg bg-gray-800 text-gray-300 flex items-center justify-center border border-gray-700/60">
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                  </div>
                  <span>RELPREVs (Prevenção)</span>
                </div>

                <div className="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800/50 cursor-pointer transition-all">
                  <div className="w-8 h-8 rounded-lg bg-gray-800 text-gray-300 flex items-center justify-center border border-gray-700/60">
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                  </div>
                  <span>Vistorias de Escape</span>
                </div>
              </>
            )}

            {isAdmin && (
              <>
                <button
                  type="button"
                  className="w-full flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 bg-gradient-to-r from-indigo-900/60 to-indigo-800/40 text-white border border-indigo-600/40 shadow-lg shadow-indigo-950/50 text-left"
                >
                  <div className="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center shadow-md shadow-indigo-500/30">
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                  </div>
                  <span>Gestão de Militares</span>
                </button>

                <div className="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800/50 cursor-pointer transition-all">
                  <div className="w-8 h-8 rounded-lg bg-gray-800 text-gray-300 flex items-center justify-center border border-gray-700/60">
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                  </div>
                  <span>Auditoria & Segurança</span>
                </div>
              </>
            )}
          </nav>
        </div>

        {/* Card Inferior Argon */}
        <div className="p-4 m-4 rounded-2xl bg-gradient-to-br from-[#0c182c] to-[#040813] border border-yellow-500/20 shadow-xl relative overflow-hidden">
          <div className="flex items-center gap-3 relative z-10">
            <div className="w-10 h-10 rounded-xl bg-yellow-500/10 border border-yellow-500/30 flex items-center justify-center shrink-0">
              <img src="/img/brasao_0.png" alt="" className="h-6 w-auto object-contain" />
            </div>
            <div>
              <div className="text-xs font-bold text-white uppercase tracking-wider font-mono">ComForS • CLTI</div>
              <div className="text-[10px] text-yellow-500/90 font-medium">Força de Submarinos</div>
            </div>
          </div>
          <p className="text-[11px] text-gray-400 mt-2.5 italic relative z-10 leading-snug">
            “USQUE AD SUB ACQUAM NAUTA SUM”
          </p>
        </div>
      </aside>

      {/* ===================================================================== */}
      {/* 2. ÁREA DE CONTEÚDO PRINCIPAL (COM TOPBAR ARGON STICKY)               */}
      {/* ===================================================================== */}
      <div className="lg:pl-64 xl:pl-72 flex-1 flex flex-col justify-between min-w-0">
        
        <div>
          {/* Top Navbar Argon */}
          <header className="sticky top-0 z-30 bg-[#030712]/80 backdrop-blur-md border-b border-gray-800/80 px-4 sm:px-6 lg:px-8 py-3.5 transition-all">
            <div className="flex items-center justify-between gap-4">
              
              <div className="flex items-center gap-3">
                <button
                  onClick={() => setSidebarOpen(true)}
                  className="lg:hidden p-2 rounded-xl text-gray-400 hover:text-white bg-gray-900 border border-gray-800"
                >
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                  </svg>
                </button>

                <div>
                  <div className="flex items-center gap-1.5 text-xs text-gray-400 font-mono">
                    <span>Páginas</span>
                    <span>/</span>
                    {isLicoes ? (
                      <span className="text-blue-400 font-medium">Lições Aprendidas</span>
                    ) : isSipa ? (
                      <span className="text-amber-400 font-medium">SIPA-SUB</span>
                    ) : (
                      <span className="text-indigo-400 font-medium">Console Central</span>
                    )}
                  </div>
                  <h1
                    className="text-sm sm:text-base font-bold text-white tracking-wide"
                    style={{ fontFamily: "'Oswald', sans-serif" }}
                  >
                    {isLicoes
                      ? 'PAINEL OPERACIONAL DE DOUTRINA'
                      : isSipa
                      ? 'PAINEL DE SEGURANÇA E PREVENÇÃO'
                      : 'TERMINAL GERAL DE COMANDO'}
                  </h1>
                </div>
              </div>

              <div className="flex items-center gap-3 sm:gap-4">
                <div className="hidden md:flex items-center relative">
                  <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                  </div>
                  <input
                    type="text"
                    placeholder="Pesquisar..."
                    className="w-44 lg:w-56 pl-9 pr-3 py-1.5 rounded-xl text-xs bg-[#060f1d] border border-gray-800 text-white placeholder-gray-500 focus:outline-none focus:border-yellow-500 transition-all"
                  />
                </div>

                <div className="flex items-center gap-2.5 pl-2 sm:border-l border-gray-800">
                  <div className="w-8 h-8 rounded-full bg-gradient-to-br from-yellow-500 to-amber-600 flex items-center justify-center text-black font-bold text-xs shadow-md">
                    {currentUser.name.charAt(0).toUpperCase()}
                  </div>
                  <div className="hidden sm:block text-left">
                    <div className="text-xs font-bold text-white leading-tight truncate max-w-[140px]">
                      {currentUser.name}
                    </div>
                    <div className="text-[10px] font-mono text-gray-400 leading-tight">
                      NIP: <span className="text-yellow-400 font-bold">{currentUser.nip}</span>
                    </div>
                  </div>
                </div>

                <button
                  onClick={handleLogout}
                  type="button"
                  title="Para trocar de módulo, é necessário sair e autenticar novamente"
                  className="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all duration-200 cursor-pointer flex items-center gap-1.5 shadow-md bg-red-950/40 hover:bg-red-900/60 text-red-300 border border-red-800/80 hover:border-red-600"
                >
                  <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                  </svg>
                  <span className="hidden sm:inline">Sair</span>
                </button>
              </div>

            </div>
          </header>

          {/* Mensagens de Feedback */}
          <div className="px-4 sm:px-6 lg:px-8 pt-4">
            {mensagemSucesso && (
              <div className="p-4 rounded-xl text-sm font-semibold flex items-center justify-between shadow-xl bg-emerald-950/80 text-emerald-300 border border-emerald-700/80">
                <div className="flex items-center gap-2">
                  <svg className="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  <span>{mensagemSucesso}</span>
                </div>
                <button onClick={() => setMensagemSucesso(null)} className="text-xs underline hover:text-white cursor-pointer">
                  Fechar
                </button>
              </div>
            )}

            {mensagemErro && (
              <div className="p-4 rounded-xl text-sm font-semibold flex items-center justify-between shadow-xl bg-red-950/80 text-red-300 border border-red-700/80">
                <div className="flex items-center gap-2">
                  <svg className="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                  <span>{mensagemErro}</span>
                </div>
                <button onClick={() => setMensagemErro(null)} className="text-xs underline hover:text-white cursor-pointer">
                  Fechar
                </button>
              </div>
            )}
          </div>

          {/* ================================================================= */}
          {/* 3. VISÃO DO MÓDULO LIÇÕES APRENDIDAS                             */}
          {/* ================================================================= */}
          {isLicoes && (
            <div className="px-4 sm:px-6 lg:px-8 py-6 space-y-6">
              {/* 4 Cards de Métricas */}
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div className="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl flex items-center justify-between hover:border-blue-500/50 transition-all">
                  <div>
                    <p className="text-[11px] font-mono uppercase font-bold tracking-wider text-gray-400">Fainas Registradas</p>
                    <h3 className="text-2xl font-bold text-white mt-1">148</h3>
                    <p className="text-xs text-emerald-400 font-semibold mt-1">↑ +14% neste ciclo</p>
                  </div>
                  <div className="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-700 to-cyan-500 flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                  </div>
                </div>

                <div className="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl flex items-center justify-between hover:border-yellow-500/50 transition-all">
                  <div>
                    <p className="text-[11px] font-mono uppercase font-bold tracking-wider text-gray-400">Doutrinas Ativas</p>
                    <h3 className="text-2xl font-bold text-white mt-1">42</h3>
                    <p className="text-xs text-emerald-400 font-semibold mt-1">✓ Vigentes 2026</p>
                  </div>
                  <div className="w-12 h-12 rounded-2xl bg-gradient-to-tr from-yellow-600 to-amber-500 flex items-center justify-center text-white shadow-lg shadow-yellow-500/30">
                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                  </div>
                </div>

                <div className="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl flex items-center justify-between hover:border-emerald-500/50 transition-all">
                  <div>
                    <p className="text-[11px] font-mono uppercase font-bold tracking-wider text-gray-400">Submarinos</p>
                    <h3 className="text-2xl font-bold text-white mt-1">05</h3>
                    <p className="text-xs text-blue-400 font-semibold mt-1">S-BR & IKL-209</p>
                  </div>
                  <div className="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30">
                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                  </div>
                </div>

                <div className="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl flex items-center justify-between hover:border-indigo-500/50 transition-all">
                  <div>
                    <p className="text-[11px] font-mono uppercase font-bold tracking-wider text-gray-400">Prontidão Tática</p>
                    <h3 className="text-2xl font-bold text-white mt-1">98.6%</h3>
                    <p className="text-xs text-emerald-400 font-semibold mt-1">↑ Nível ótimo</p>
                  </div>
                  <div className="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-500 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30">
                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                  </div>
                </div>
              </div>

              {/* 2 Colunas */}
              <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div className="lg:col-span-8 p-6 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl">
                  <div className="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-4 border-b border-gray-800/80 gap-3">
                    <div>
                      <h2 className="text-base font-bold text-white uppercase tracking-wide" style={{ fontFamily: "'Oswald', sans-serif" }}>
                        Fainas e Ocorrências Recentes de Imersão
                      </h2>
                      <p className="text-xs text-gray-400 mt-0.5">
                        Histórico cronológico de manobras, mergulhos estáticos e testes
                      </p>
                    </div>
                    <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono bg-blue-950/60 text-blue-400 border border-blue-800/60 w-fit">
                      <span className="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse" />
                      <span>Tempo Real</span>
                    </span>
                  </div>

                  <div className="overflow-x-auto">
                    <table className="w-full text-left text-xs">
                      <thead>
                        <tr className="text-gray-400 uppercase tracking-wider font-mono border-b border-gray-800/60 text-[10px]">
                          <th className="pb-3 font-semibold">Submarino</th>
                          <th className="pb-3 font-semibold">Faina / Manobra</th>
                          <th className="pb-3 font-semibold">Setor</th>
                          <th className="pb-3 font-semibold text-right">Status</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-gray-800/50">
                        <tr className="hover:bg-gray-800/30 transition-colors">
                          <td className="py-3.5 font-bold text-white flex items-center gap-2">
                            <span className="w-6 h-6 rounded bg-blue-900/60 flex items-center justify-center text-[10px] font-mono text-blue-300">S40</span>
                            <span>S-40 Riachuelo</span>
                          </td>
                          <td className="py-3.5 text-gray-300">Teste de Sopragem de Tanques</td>
                          <td className="py-3.5 font-mono text-gray-400">Área Charlie 4</td>
                          <td className="py-3.5 text-right font-mono text-emerald-400 font-semibold">Homologado</td>
                        </tr>
                        <tr className="hover:bg-gray-800/30 transition-colors">
                          <td className="py-3.5 font-bold text-white flex items-center gap-2">
                            <span className="w-6 h-6 rounded bg-blue-900/60 flex items-center justify-center text-[10px] font-mono text-blue-300">S41</span>
                            <span>S-41 Humaitá</span>
                          </td>
                          <td className="py-3.5 text-gray-300">Calibração de Sonar Passivo</td>
                          <td className="py-3.5 font-mono text-gray-400">Baía Sepetiba</td>
                          <td className="py-3.5 text-right font-mono text-emerald-400 font-semibold">Homologado</td>
                        </tr>
                        <tr className="hover:bg-gray-800/30 transition-colors">
                          <td className="py-3.5 font-bold text-white flex items-center gap-2">
                            <span className="w-6 h-6 rounded bg-blue-900/60 flex items-center justify-center text-[10px] font-mono text-blue-300">S42</span>
                            <span>S-42 Tonelero</span>
                          </td>
                          <td className="py-3.5 text-gray-300">Verificação de Válvulas de Casco</td>
                          <td className="py-3.5 font-mono text-gray-400">Cais BSN</td>
                          <td className="py-3.5 text-right font-mono text-amber-400 font-semibold">Em Revisão</td>
                        </tr>
                        <tr className="hover:bg-gray-800/30 transition-colors">
                          <td className="py-3.5 font-bold text-white flex items-center gap-2">
                            <span className="w-6 h-6 rounded bg-blue-900/60 flex items-center justify-center text-[10px] font-mono text-blue-300">S34</span>
                            <span>S-34 Tikuna</span>
                          </td>
                          <td className="py-3.5 text-gray-300">Treinamento de Controle de Avarias</td>
                          <td className="py-3.5 font-mono text-gray-400">Área Bravo 2</td>
                          <td className="py-3.5 text-right font-mono text-emerald-400 font-semibold">Homologado</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div className="lg:col-span-4 p-6 rounded-2xl bg-gradient-to-br from-[#0c1e3d] via-[#061122] to-[#030814] border border-blue-500/30 shadow-2xl flex flex-col justify-between">
                  <div>
                    <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider bg-yellow-500/10 text-yellow-400 border border-yellow-500/30 mb-4">
                      ★ Doutrina Tática
                    </div>
                    <h3 className="text-xl font-bold text-white uppercase leading-tight" style={{ fontFamily: "'Oswald', sans-serif" }}>
                      Repositório Doutrinário ComForS
                    </h3>
                    <p className="text-xs text-gray-300 mt-3 leading-relaxed">
                      A preservação das lições aprendidas assegura a superioridade tática e a sobrevivência de nossas tripulações nas profundezas do Atlântico Sul.
                    </p>
                  </div>
                  <div className="pt-6 mt-4 border-t border-blue-500/20 space-y-2">
                    <button type="button" className="w-full py-2.5 px-4 rounded-xl text-xs font-bold uppercase tracking-wider bg-gradient-to-r from-yellow-500 to-amber-600 text-black shadow-lg shadow-yellow-500/20">
                      Registrar Nova Faina
                    </button>
                    <button type="button" className="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-gray-300 bg-gray-900/60 border border-gray-700/60">
                      Consultar Biblioteca Tática
                    </button>
                  </div>
                </div>
              </div>

              {/* Grid 3 colunas inferior */}
              <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div className="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl">
                  <h3 className="text-xs font-bold uppercase tracking-wider text-gray-300 font-mono pb-3 border-b border-gray-800/80 flex items-center justify-between">
                    <span>Tripulação de Quarto</span>
                    <span className="text-[10px] text-emerald-400">● BSN Operacional</span>
                  </h3>
                  <div className="mt-4 space-y-3">
                    <div className="flex items-center justify-between text-xs">
                      <span className="font-bold text-white">CT Ribeiro</span>
                      <span className="font-mono text-gray-400 text-[10px]">Oficial de Manobra</span>
                    </div>
                    <div className="flex items-center justify-between text-xs">
                      <span className="font-bold text-white">1T Castro</span>
                      <span className="font-mono text-gray-400 text-[10px]">Oficial de Armamento</span>
                    </div>
                    <div className="flex items-center justify-between text-xs">
                      <span className="font-bold text-yellow-400">2º SG PD SANDRO</span>
                      <span className="font-mono text-emerald-400 text-[10px]">CLTI / Conectado</span>
                    </div>
                  </div>
                </div>

                <div className="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl">
                  <h3 className="text-xs font-bold uppercase tracking-wider text-gray-300 font-mono pb-3 border-b border-gray-800/80 flex items-center justify-between">
                    <span>Checklists Táticos</span>
                    <span className="text-[10px] text-blue-400">4 Pendentes</span>
                  </h3>
                  <div className="mt-4 space-y-2.5 text-xs text-gray-300">
                    <div className="line-through text-gray-400">✓ Sensores de atmosfera (O2/CO2)</div>
                    <div className="line-through text-gray-400">✓ Redes de ar comprimido 250 bar</div>
                    <div>○ Teste de estanqueidade escotilhas</div>
                    <div>○ Relatório pós-faina de patrulha</div>
                  </div>
                </div>

                <div className="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl">
                  <h3 className="text-xs font-bold uppercase tracking-wider text-gray-300 font-mono pb-3 border-b border-gray-800/80 flex items-center justify-between">
                    <span>Prontidão dos Submarinos</span>
                    <span className="text-[10px] text-yellow-400">Esquadra</span>
                  </h3>
                  <div className="mt-4 space-y-3 text-xs">
                    <div>
                      <div className="flex justify-between mb-1">
                        <span className="font-bold text-white">S-40 Riachuelo</span>
                        <span className="text-emerald-400 font-mono font-bold">100%</span>
                      </div>
                      <div className="w-full bg-gray-800 rounded-full h-1.5">
                        <div className="bg-gradient-to-r from-blue-500 to-emerald-400 h-1.5 rounded-full w-full" />
                      </div>
                    </div>
                    <div>
                      <div className="flex justify-between mb-1">
                        <span className="font-bold text-white">S-41 Humaitá</span>
                        <span className="text-emerald-400 font-mono font-bold">100%</span>
                      </div>
                      <div className="w-full bg-gray-800 rounded-full h-1.5">
                        <div className="bg-gradient-to-r from-blue-500 to-emerald-400 h-1.5 rounded-full w-full" />
                      </div>
                    </div>
                    <div>
                      <div className="flex justify-between mb-1">
                        <span className="font-bold text-white">S-42 Tonelero</span>
                        <span className="text-blue-400 font-mono font-bold">95%</span>
                      </div>
                      <div className="w-full bg-gray-800 rounded-full h-1.5">
                        <div className="bg-gradient-to-r from-blue-500 to-cyan-400 h-1.5 rounded-full w-[95%]" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          )}

          {/* ================================================================= */}
          {/* 4. VISÃO DO MÓDULO SIPA-SUB                                      */}
          {/* ================================================================= */}
          {isSipa && (
            <div className="px-4 sm:px-6 lg:px-8 py-6 space-y-6">
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div className="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl flex items-center justify-between hover:border-amber-500/50 transition-all">
                  <div>
                    <p className="text-[11px] font-mono uppercase font-bold tracking-wider text-gray-400">RELPREVs Ativos</p>
                    <h3 className="text-2xl font-bold text-white mt-1">18</h3>
                    <p className="text-xs text-amber-400 font-semibold mt-1">! 03 Prioritários</p>
                  </div>
                  <div className="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-600 to-yellow-500 flex items-center justify-center text-white shadow-lg shadow-amber-500/30">
                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                  </div>
                </div>

                <div className="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl flex items-center justify-between hover:border-emerald-500/50 transition-all">
                  <div>
                    <p className="text-[11px] font-mono uppercase font-bold tracking-wider text-gray-400">Vistorias Escape</p>
                    <h3 className="text-2xl font-bold text-white mt-1">32</h3>
                    <p className="text-xs text-emerald-400 font-semibold mt-1">✓ 100% Conforme</p>
                  </div>
                  <div className="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30">
                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                  </div>
                </div>

                <div className="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl flex items-center justify-between hover:border-blue-500/50 transition-all">
                  <div>
                    <p className="text-[11px] font-mono uppercase font-bold tracking-wider text-gray-400">Auditorias Imersão</p>
                    <h3 className="text-2xl font-bold text-white mt-1">14</h3>
                    <p className="text-xs text-blue-400 font-semibold mt-1">Ciclo 2026/1</p>
                  </div>
                  <div className="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-700 to-cyan-500 flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    </svg>
                  </div>
                </div>

                <div className="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl flex items-center justify-between hover:border-purple-500/50 transition-all">
                  <div>
                    <p className="text-[11px] font-mono uppercase font-bold tracking-wider text-gray-400">Segurança SIPA</p>
                    <h3 className="text-2xl font-bold text-white mt-1">99.9%</h3>
                    <p className="text-xs text-emerald-400 font-semibold mt-1">↑ Zero Acidentes</p>
                  </div>
                  <div className="w-12 h-12 rounded-2xl bg-gradient-to-tr from-purple-700 to-indigo-500 flex items-center justify-center text-white shadow-lg shadow-purple-500/30">
                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                </div>
              </div>

              <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div className="lg:col-span-8 p-6 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl">
                  <div className="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-4 border-b border-gray-800/80 gap-3">
                    <div>
                      <h2 className="text-base font-bold text-white uppercase tracking-wide" style={{ fontFamily: "'Oswald', sans-serif" }}>
                        Relatórios de Prevenção (RELPREV)
                      </h2>
                      <p className="text-xs text-gray-400 mt-0.5">Gestão de prevenção e integridade estrutural</p>
                    </div>
                    <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono bg-amber-950/60 text-amber-400 border border-amber-800/60 w-fit">
                      <span className="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse" />
                      <span>SIPA Ativo</span>
                    </span>
                  </div>

                  <div className="overflow-x-auto">
                    <table className="w-full text-left text-xs">
                      <thead>
                        <tr className="text-gray-400 uppercase tracking-wider font-mono border-b border-gray-800/60 text-[10px]">
                          <th className="pb-3 font-semibold">Código</th>
                          <th className="pb-3 font-semibold">Submarino</th>
                          <th className="pb-3 font-semibold">Assunto</th>
                          <th className="pb-3 font-semibold text-right">Ação</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-gray-800/50">
                        <tr className="hover:bg-gray-800/30 transition-colors">
                          <td className="py-3.5 font-mono text-yellow-400 font-bold">RP-2026-081</td>
                          <td className="py-3.5 font-bold text-white">S-40 Riachuelo</td>
                          <td className="py-3.5 text-gray-300">Válvula de Exaustão de Gases de Bateria</td>
                          <td className="py-3.5 text-right font-mono text-emerald-400 font-semibold">Concluído</td>
                        </tr>
                        <tr className="hover:bg-gray-800/30 transition-colors">
                          <td className="py-3.5 font-mono text-yellow-400 font-bold">RP-2026-082</td>
                          <td className="py-3.5 font-bold text-white">S-41 Humaitá</td>
                          <td className="py-3.5 text-gray-300">Roupas Submarinas de Escape</td>
                          <td className="py-3.5 text-right font-mono text-emerald-400 font-semibold">Concluído</td>
                        </tr>
                        <tr className="hover:bg-gray-800/30 transition-colors">
                          <td className="py-3.5 font-mono text-yellow-400 font-bold">RP-2026-083</td>
                          <td className="py-3.5 font-bold text-white">S-42 Tonelero</td>
                          <td className="py-3.5 text-gray-300">Pressostato de Reserva do Tanque de Lastro</td>
                          <td className="py-3.5 text-right font-mono text-amber-400 font-semibold">Em Análise</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div className="lg:col-span-4 p-6 rounded-2xl bg-gradient-to-br from-[#1c1305] via-[#0f0a02] to-[#050301] border border-amber-500/30 shadow-2xl flex flex-col justify-between">
                  <div>
                    <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider bg-amber-500/10 text-amber-400 border border-amber-500/30 mb-4">
                      🛡 Segurança Submarina
                    </div>
                    <h3 className="text-xl font-bold text-white uppercase leading-tight" style={{ fontFamily: "'Oswald', sans-serif" }}>
                      Prevenção & Salvamento Submarino
                    </h3>
                    <p className="text-xs text-gray-300 mt-3 leading-relaxed">
                      A integridade de cada vaso de pressão e a segurança de cada submarinista dependem da rigidez nas vistorias de escape.
                    </p>
                  </div>
                  <div className="pt-6 mt-4 border-t border-amber-500/20 space-y-2">
                    <button type="button" className="w-full py-2.5 px-4 rounded-xl text-xs font-bold uppercase tracking-wider bg-gradient-to-r from-amber-500 to-yellow-500 text-black shadow-lg shadow-amber-500/20">
                      Emitir Novo RELPREV
                    </button>
                    <button type="button" className="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-gray-300 bg-gray-900/60 border border-gray-700/60">
                      Normas de Escape
                    </button>
                  </div>
                </div>
              </div>
            </div>
          )}

          {/* ================================================================= */}
          {/* 5. VISÃO DA CONSOLE CENTRAL (CRUD DE USUÁRIOS)                    */}
          {/* ================================================================= */}
          {isAdmin && moduloAtivo === 'sys_ops_k7' && (
            <div className="px-4 sm:px-6 lg:px-8 py-6 space-y-6">
              
              {/* 4 Cards de Métricas do Admin */}
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div className="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl flex items-center justify-between hover:border-blue-500/50 transition-all">
                  <div>
                    <p className="text-[11px] font-mono uppercase font-bold tracking-wider text-gray-400">Tripulação / NIPs</p>
                    <h3 className="text-2xl font-bold text-white mt-1">{usersList.length}</h3>
                    <p className="text-xs text-emerald-400 font-semibold mt-1">✓ Sincronizados</p>
                  </div>
                  <div className="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-700 to-cyan-500 flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                  </div>
                </div>

                <div className="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl flex items-center justify-between hover:border-yellow-500/50 transition-all">
                  <div>
                    <p className="text-[11px] font-mono uppercase font-bold tracking-wider text-gray-400">Módulos Ativos</p>
                    <h3 className="text-2xl font-bold text-white mt-1">02</h3>
                    <p className="text-xs text-yellow-400 font-semibold mt-1">Lições & SIPA-SUB</p>
                  </div>
                  <div className="w-12 h-12 rounded-2xl bg-gradient-to-tr from-yellow-600 to-amber-500 flex items-center justify-center text-white shadow-lg shadow-yellow-500/30">
                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                  </div>
                </div>

                <div className="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl flex items-center justify-between hover:border-purple-500/50 transition-all">
                  <div>
                    <p className="text-[11px] font-mono uppercase font-bold tracking-wider text-gray-400">Sessões Ativas</p>
                    <h3 className="text-2xl font-bold text-white mt-1">01</h3>
                    <p className="text-xs text-purple-400 font-semibold mt-1">Criptografia Ativa</p>
                  </div>
                  <div className="w-12 h-12 rounded-2xl bg-gradient-to-tr from-purple-700 to-indigo-500 flex items-center justify-center text-white shadow-lg shadow-purple-500/30">
                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                  </div>
                </div>

                <div className="p-5 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-xl flex items-center justify-between hover:border-emerald-500/50 transition-all">
                  <div>
                    <p className="text-[11px] font-mono uppercase font-bold tracking-wider text-gray-400">Integridade</p>
                    <h3 className="text-2xl font-bold text-white mt-1">100%</h3>
                    <p className="text-xs text-emerald-400 font-semibold mt-1">Sem falhas</p>
                  </div>
                  <div className="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30">
                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                  </div>
                </div>
              </div>

              {/* Tabela de Gestão de Militares */}
              <div className="p-6 rounded-2xl bg-[#060f1d]/90 backdrop-blur-md border border-gray-800/80 shadow-2xl space-y-5">
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-800/80">
                  <div>
                    <h2 className="text-lg font-bold text-white uppercase tracking-wide" style={{ fontFamily: "'Oswald', sans-serif" }}>
                      Gestão de Militares & Permissões
                    </h2>
                    <p className="text-xs text-gray-400 mt-0.5">
                      Controle de credenciais, NIPs e atribuição tática de módulos
                    </p>
                  </div>

                  <div className="flex items-center gap-3">
                    <div className="relative">
                      <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                      </div>
                      <input
                        type="text"
                        value={busca}
                        onChange={(e) => setBusca(e.target.value)}
                        placeholder="Buscar por NIP, nome..."
                        className="pl-9 pr-3 py-2 rounded-xl text-xs bg-gray-900/80 border border-gray-800 text-white placeholder-gray-500 focus:outline-none focus:border-yellow-500 w-52 sm:w-64"
                      />
                    </div>

                    <button
                      onClick={abrirModalCriar}
                      type="button"
                      className="py-2 px-3.5 rounded-xl text-xs font-bold uppercase tracking-wider bg-gradient-to-r from-yellow-500 to-amber-600 hover:from-yellow-400 hover:to-amber-500 text-black shadow-lg shadow-yellow-500/20 transition-all flex items-center gap-2 cursor-pointer shrink-0"
                    >
                      <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                      </svg>
                      <span>Cadastrar Militar</span>
                    </button>
                  </div>
                </div>

                <div className="overflow-x-auto">
                  <table className="w-full text-left text-xs">
                    <thead>
                      <tr className="text-gray-400 uppercase tracking-wider font-mono border-b border-gray-800/80 text-[10px]">
                        <th className="pb-3 font-semibold">Militar / Nome</th>
                        <th className="pb-3 font-semibold">NIP</th>
                        <th className="pb-3 font-semibold">E-mail</th>
                        <th className="pb-3 font-semibold">Módulo</th>
                        <th className="pb-3 font-semibold text-right">Ações</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-800/60">
                      {usuariosFiltrados.map((u) => (
                        <tr key={u.id} className={`hover:bg-gray-800/30 transition-colors ${u.nip === '09097074' ? 'bg-indigo-950/20' : ''}`}>
                          <td className="py-3.5 font-bold text-white flex items-center gap-3">
                            <div className={`w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold font-mono shadow-md ${u.nip === '09097074' ? 'bg-indigo-600 text-white' : 'bg-gray-800 text-yellow-400 border border-gray-700'}`}>
                              {u.name.slice(0, 2).toUpperCase()}
                            </div>
                            <div>
                              <div className="text-xs font-bold text-white flex items-center gap-1.5">
                                <span>{u.name}</span>
                                {u.nip === '09097074' && (
                                  <span className="px-1.5 py-0.5 rounded text-[9px] font-mono bg-indigo-900/60 text-indigo-300 border border-indigo-700/60">Admin</span>
                                )}
                              </div>
                              <div className="text-[10px] text-gray-400 font-mono">ID #{u.id}</div>
                            </div>
                          </td>

                          <td className="py-3.5 font-mono text-sm font-bold text-yellow-400">
                            {u.nip}
                          </td>

                          <td className="py-3.5 font-mono text-gray-300">
                            {u.email}
                          </td>

                          <td className="py-3.5">
                            {u.modulo_acesso === 'licoes_aprendidas' && (
                              <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-blue-950/60 text-blue-300 border border-blue-800/60">
                                <span className="w-1.5 h-1.5 rounded-full bg-blue-400" />
                                <span>Lições Aprendidas</span>
                              </span>
                            )}
                            {u.modulo_acesso === 'sipa_sub' && (
                              <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-950/60 text-amber-300 border border-amber-800/60">
                                <span className="w-1.5 h-1.5 rounded-full bg-amber-400" />
                                <span>SIPA-SUB</span>
                              </span>
                            )}
                            {u.modulo_acesso === 'ambos' && (
                              <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-950/60 text-emerald-300 border border-emerald-800/60">
                                <span className="w-1.5 h-1.5 rounded-full bg-emerald-400" />
                                <span>Ambos os Módulos</span>
                              </span>
                            )}
                          </td>

                          <td className="py-3.5 text-right space-x-2">
                            <button
                              onClick={() => abrirModalEditar(u)}
                              type="button"
                              className="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-gray-800 hover:bg-gray-700 text-gray-200 border border-gray-700 transition-colors cursor-pointer"
                            >
                              Editar
                            </button>
                            {u.nip !== '09097074' && (
                              <button
                                onClick={() => confirmarExclusao(u)}
                                type="button"
                                className="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-red-950/40 hover:bg-red-900/60 text-red-300 border border-red-800/60 transition-colors cursor-pointer"
                              >
                                Excluir
                              </button>
                            )}
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>

            </div>
          )}

        </div>

        {/* Rodapé Padrão ComForS */}
        <footer className="border-t border-gray-800/80 py-4 px-6 text-xs text-gray-400 font-mono bg-[#030712]/90 backdrop-blur-md">
          <div className="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
            <span className="text-gray-400">
              ComForS - CLTI • “USQUE AD SUB ACQUAM NAUTA SUM”
            </span>
            <span className="text-gray-300">
              Desenvolvido por: <strong className="text-white underline decoration-yellow-500/80 underline-offset-2">2º SG PD SANDRO</strong> • ComForS
            </span>
          </div>
        </footer>

      </div>

      {/* ===================================================================== */}
      {/* 6. MODAL CRIAR / EDITAR MILITAR                                       */}
      {/* ===================================================================== */}
      {modalAberto && (
        <div className="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
          <div className="bg-[#060f1d] border border-gray-800 rounded-2xl w-full max-w-lg p-6 shadow-2xl space-y-5">
            <div className="flex items-center justify-between pb-3 border-b border-gray-800">
              <div>
                <h3 className="text-base font-bold text-white uppercase tracking-wide" style={{ fontFamily: "'Oswald', sans-serif" }}>
                  {isEdicao ? 'Editar Credencial do Militar' : 'Cadastrar Novo Militar na ForçaOnline'}
                </h3>
                <p className="text-xs text-gray-400 mt-0.5">
                  {isEdicao ? 'Atualize as permissões ou defina uma nova senha' : 'O NIP de 8 dígitos será a chave de acesso exclusivo'}
                </p>
              </div>
              <button onClick={() => setModalAberto(false)} className="text-gray-400 hover:text-white p-1 rounded-lg">
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <form onSubmit={salvarUsuario} className="space-y-4">
              <div>
                <label className="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1 font-mono">Nome / Posto / Graduação</label>
                <input
                  type="text"
                  required
                  value={formNome}
                  onChange={(e) => setFormNome(e.target.value)}
                  placeholder="Ex: 1º Tenente Silva"
                  className="w-full px-3.5 py-2.5 rounded-xl text-xs bg-gray-900 border border-gray-700 text-white focus:outline-none focus:border-yellow-500"
                />
              </div>

              <div>
                <label className="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1 font-mono">NIP (8 Dígitos Numéricos)</label>
                <input
                  type="text"
                  maxLength={8}
                  inputMode="numeric"
                  required
                  value={formNip}
                  onChange={(e) => setFormNip(e.target.value.replace(/\D/g, '').slice(0, 8))}
                  placeholder="Ex: 12345678"
                  className="w-full px-3.5 py-2.5 rounded-xl text-xs bg-gray-900 border border-gray-700 text-white font-mono text-yellow-400 font-bold focus:outline-none focus:border-yellow-500"
                />
              </div>

              <div>
                <label className="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1 font-mono">E-mail Operacional</label>
                <input
                  type="email"
                  required
                  value={formEmail}
                  onChange={(e) => setFormEmail(e.target.value)}
                  placeholder="militar@marinha.mil.br"
                  className="w-full px-3.5 py-2.5 rounded-xl text-xs bg-gray-900 border border-gray-700 text-white focus:outline-none focus:border-yellow-500"
                />
              </div>

              <div>
                <label className="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1 font-mono">Permissão de Módulo</label>
                <select
                  value={formModulo}
                  onChange={(e) => setFormModulo(e.target.value as any)}
                  className="w-full px-3.5 py-2.5 rounded-xl text-xs bg-gray-900 border border-gray-700 text-white focus:outline-none focus:border-yellow-500"
                >
                  <option value="licoes_aprendidas">Lições Aprendidas (Apenas Módulo 01)</option>
                  <option value="sipa_sub">SIPA-SUB (Apenas Módulo 02)</option>
                  <option value="ambos">Ambos os Módulos (Requer relogin para alternar)</option>
                </select>
              </div>

              <div>
                <label className="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1 font-mono">
                  {isEdicao ? 'Nova Senha (deixe em branco para manter)' : 'Senha de Acesso'}
                </label>
                <input
                  type="password"
                  value={formSenha}
                  onChange={(e) => setFormSenha(e.target.value)}
                  placeholder="Mínimo 6 caracteres"
                  required={!isEdicao}
                  className="w-full px-3.5 py-2.5 rounded-xl text-xs bg-gray-900 border border-gray-700 text-white focus:outline-none focus:border-yellow-500"
                />
              </div>

              <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-800">
                <button
                  type="button"
                  onClick={() => setModalAberto(false)}
                  className="px-4 py-2 rounded-xl text-xs font-semibold text-gray-300 hover:text-white bg-gray-800 hover:bg-gray-700 transition-colors"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="px-5 py-2 rounded-xl text-xs font-bold uppercase tracking-wider bg-gradient-to-r from-yellow-500 to-amber-600 hover:from-yellow-400 hover:to-amber-500 text-black shadow-lg shadow-yellow-500/20 transition-all cursor-pointer"
                >
                  {isEdicao ? 'Salvar Alterações' : 'Confirmar Cadastro'}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* ===================================================================== */}
      {/* 7. MODAL DE CONFIRMAÇÃO DE EXCLUSÃO                                   */}
      {/* ===================================================================== */}
      {modalExclusaoAberto && usuarioParaExcluir && (
        <div className="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
          <div className="bg-[#060f1d] border border-red-800/80 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4">
            <div className="w-12 h-12 rounded-2xl bg-red-950/80 border border-red-800/60 flex items-center justify-center text-red-400 mx-auto">
              <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>

            <div className="text-center">
              <h3 className="text-base font-bold text-white uppercase tracking-wide" style={{ fontFamily: "'Oswald', sans-serif" }}>
                Revogar Credencial Operacional?
              </h3>
              <p className="text-xs text-gray-300 mt-2">
                Você está prestes a excluir o militar <strong className="text-white">{usuarioParaExcluir.name}</strong>.
              </p>
              <p className="text-[11px] text-red-400 mt-1 font-mono">
                Esta ação revoga imediatamente o acesso ao sistema ForçaOnline.
              </p>
            </div>

            <div className="flex items-center justify-center gap-3 pt-3 border-t border-gray-800">
              <button
                type="button"
                onClick={() => setModalExclusaoAberto(false)}
                className="px-4 py-2 rounded-xl text-xs font-semibold text-gray-300 hover:text-white bg-gray-800 hover:bg-gray-700 transition-colors"
              >
                Cancelar
              </button>
              <button
                type="button"
                onClick={excluirUsuario}
                className="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider bg-red-600 hover:bg-red-500 text-white shadow-lg shadow-red-600/30 transition-all cursor-pointer"
              >
                Confirmar Exclusão
              </button>
            </div>
          </div>
        </div>
      )}

    </div>
  );
};
