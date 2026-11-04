<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ForçaOnline - ComForS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Google Fonts Militares (Oswald, Bebas Neue, Montserrat) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@500;600;700;900&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts e Estilos -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-white min-h-screen bg-[#030712] selection:bg-yellow-500 selection:text-black">
    
    <!-- Fundo Naval Sutil com Imagem do Submarino (Marca d'água discreta, sem tirar o destaque do conteúdo) -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <img 
            src="{{ asset('img/submarino.webp') }}" 
            alt="" 
            class="w-full h-full object-cover opacity-12 filter grayscale contrast-125 brightness-50"
        />
        <!-- Gradiente escuro naval suave sobreposto para garantir legibilidade absoluta do padrão Argon -->
        <div class="absolute inset-0 bg-gradient-to-br from-[#030712]/95 via-[#060f1d]/90 to-[#02050c]/98"></div>
    </div>

    <!-- Estrutura Principal Argon: Sidenav + Área de Conteúdo -->
    <div class="relative z-10 min-h-screen flex flex-col justify-between">
        
        <div>
            <!-- Navegação Argon (Sidenav Lateral Fixa + Topbar com Breadcrumb e Perfil) -->
            <livewire:layout.navigation />

            <!-- Page Heading Opcional -->
            @if (isset($header))
                <header class="lg:pl-64 xl:pl-72 border-b border-gray-800/60 bg-[#060f1d]/60 backdrop-blur-md">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Área de Conteúdo Principal (Alinhada com a Sidenav Lateral) -->
            <main class="lg:pl-64 xl:pl-72 flex-1 pb-12">
                {{ $slot }}
            </main>
        </div>

        <!-- Rodapé Institucional Permanente Padrão ComForS -->
        <footer class="lg:pl-64 xl:pl-72 border-t border-gray-800/80 py-4 px-6 text-xs text-gray-400 font-mono bg-[#030712]/90 backdrop-blur-md">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
                <span class="text-gray-400">
                    ComForS - CLTI • “USQUE AD SUB ACQUAM NAUTA SUM”
                </span>
                <span class="text-gray-300">
                    Desenvolvido por: <strong class="text-white underline decoration-yellow-500/80 underline-offset-2">2º SG PD SANDRO</strong> • ComForS
                </span>
            </div>
        </footer>

    </div>
</body>
</html>
