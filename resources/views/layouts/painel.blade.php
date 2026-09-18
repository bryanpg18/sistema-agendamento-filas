<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Fila & Agenda')</title>

    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 transition-colors duration-150">
    <div class="flex min-h-screen">
        <aside class="w-64 shrink-0 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col transition-colors duration-150">
            <div class="h-16 flex items-center gap-2 px-6 border-b border-slate-200 dark:border-slate-800">
                <div class="w-8 h-8 rounded-lg bg-teal-800 dark:bg-teal-600 flex items-center justify-center text-white text-sm font-semibold">F</div>
                <span class="font-semibold text-slate-800 dark:text-white">Fila & Agenda</span>
            </div>

            <!-- Alternador de Tema -->
            <div class="px-3 pt-3 pb-1">
                <button type="button"
                        x-data="{
                            dark: document.documentElement.classList.contains('dark'),
                            toggleTheme() {
                                this.dark = !this.dark;
                                if (this.dark) {
                                    document.documentElement.classList.add('dark');
                                    localStorage.setItem('theme', 'dark');
                                } else {
                                    document.documentElement.classList.remove('dark');
                                    localStorage.setItem('theme', 'light');
                                }
                            }
                        }"
                        @click="toggleTheme()"
                        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-xs font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800/80 hover:bg-slate-200 dark:hover:bg-slate-800 transition">
                    <span class="flex items-center gap-2">
                        <span x-show="!dark" class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"/></svg>
                            Modo Claro
                        </span>
                        <span x-show="dark" style="display: none;" class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-indigo-400" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/></svg>
                            Modo Escuro
                        </span>
                    </span>
                    <span class="text-[10px] uppercase font-semibold px-1.5 py-0.5 rounded bg-white dark:bg-slate-700 text-slate-500 dark:text-slate-300 shadow-xs" x-text="dark ? 'Escuro' : 'Claro'"></span>
                </button>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1">
                @php
                    $links = [
                        ['label' => 'Dashboard', 'route' => 'dashboard'],
                        ['label' => 'Clientes', 'route' => 'clientes.index'],
                        ['label' => 'Agendamentos', 'route' => 'agendamentos.index'],
                        ['label' => 'Horários', 'route' => 'horarios.index'],
                        ['label' => 'Atendimentos', 'route' => 'atendimentos.index'],
                        ['label' => 'Histórico', 'route' => 'historico.index'],
                        ['label' => 'Relatórios', 'route' => 'relatorios.index'],
                        ['label' => 'Configurações', 'route' => 'configuracoes.index'],
                    ];
                @endphp
                @foreach ($links as $link)
                    @php $active = Route::has($link['route']) && request()->routeIs($link['route'].'*'); @endphp
                    <a href="{{ Route::has($link['route']) ? route($link['route']) : '#' }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $active ? 'bg-teal-50 dark:bg-teal-950/60 text-teal-800 dark:text-teal-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>
            <div class="px-3 py-4 border-t border-slate-200 dark:border-slate-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 w-full text-left">Sair</button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col">
            @hasSection('titulo_pagina')
                <header class="h-16 shrink-0 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-8 transition-colors duration-150">
                    <h1 class="text-lg font-semibold text-slate-800 dark:text-white">@yield('titulo_pagina')</h1>
                    <div>@yield('cabecalho_acoes')</div>
                </header>
            @endif

            <main class="flex-1 p-8">
                @yield('conteudo')
            </main>
        </div>
    </div>
</body>
</html>