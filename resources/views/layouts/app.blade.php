<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Fila & Agenda - @yield('titulo', 'Sistema')</title>

    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-100 antialiased transition-colors duration-150">
    <div class="flex min-h-screen">
        <aside class="w-64 min-h-screen shrink-0 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 sticky top-0 flex flex-col transition-colors duration-150">
            <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 px-5 py-5">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-700 dark:bg-teal-600 text-sm font-semibold text-white shadow-sm">F</div>
                <div>
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Fila & Agenda</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Gestão de filas e horários</p>
                </div>
            </div>

            <!-- Alternador de Tema (Light / Dark) -->
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

            <nav class="flex-1 space-y-1 px-3 py-4">
                @php
                    $navItems = [
                        ['label' => 'Dashboard', 'route' => 'dashboard'],
                        ['label' => 'Clientes', 'route' => 'clientes.index'],
                        ['label' => 'Serviços', 'route' => 'servicos.index'],
                        ['label' => 'Agendamentos', 'route' => 'agendamentos.index'],
                        ['label' => 'Horários', 'route' => 'horarios.index'],
                        ['label' => 'Atendimentos', 'route' => 'atendimentos.index'],
                        ['label' => 'Histórico', 'route' => 'historico.index'],
                        ['label' => 'Relatórios', 'route' => 'relatorios.index'],
                        ['label' => 'Configurações', 'route' => 'configuracoes.index'],
                    ];
                @endphp

                @foreach ($navItems as $item)
                    <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                       class="flex w-full items-center rounded-lg px-3 py-2.5 text-sm transition-colors {{ request()->routeIs($item['route']) ? 'bg-teal-50 dark:bg-teal-950/60 font-medium text-teal-700 dark:text-teal-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-slate-100 dark:border-slate-800 px-3 py-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center justify-center rounded-xl bg-slate-900 dark:bg-slate-800 px-3 py-2.5 text-sm font-medium text-white dark:text-slate-200 transition hover:bg-slate-800 dark:hover:bg-slate-700">
                        Sair
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 p-6 md:p-8 overflow-x-hidden">
            @if (session('sucesso'))
                <div class="mb-4 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/50 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-300">
                    {{ session('sucesso') }}
                </div>
            @endif
            @if (session('erro'))
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-800 dark:bg-rose-950/50 dark:text-rose-300">
                    {{ session('erro') }}
                </div>
            @endif

            {{ $slot ?? '' }}
            @yield('conteudo')
        </main>
    </div>
</body>
</html>
