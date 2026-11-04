<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ForçaOnline') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Google Fonts Militares (Oswald, Bebas Neue, Montserrat) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@700;900&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts e Estilos -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-white min-h-screen flex flex-col justify-between" style="background-color: #030b17; color: #ffffff;">
    <div class="min-h-screen flex flex-col justify-between" style="background-color: #030b17;">
        <div>
            <!-- Barra Superior Naval ComForS -->
            <livewire:layout.navigation />

            <!-- Page Heading (se houver) -->
            @if (isset($header))
                <header class="border-b border-gray-800" style="background-color: #060f1d;">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Conteúdo Principal do Módulo -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Rodapé Global Institucional -->
        <footer class="border-t border-gray-900 py-4 px-6 text-xs text-gray-400 font-mono" style="background-color: #000000;">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
                <span>ComForS - CLTI • “USQUE AD SUB ACQUAM NAUTA SUM”</span>
                <span>
                    Desenvolvido por: <strong class="text-white underline decoration-yellow-500/80 underline-offset-2">2º SG PD SANDRO</strong> • ComForS
                </span>
            </div>
        </footer>
    </div>
</body>
</html>
