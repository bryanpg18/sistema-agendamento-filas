<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fila & Agenda - @yield('titulo', 'Sistema')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800">
    <div class="flex min-h-screen">
        <aside class="w-64 min-h-screen shrink-0 border-r border-slate-200 bg-white sticky top-0 flex flex-col">
            <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-5">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-700 text-sm font-semibold text-white shadow-sm">F</div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">Fila & Agenda</p>
                    <p class="text-xs text-slate-500">Gestão de filas e horários</p>
                </div>
            </div>

            <nav class="flex-1 space-y-1 px-3 py-4">
                @php
                    $navItems = [
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

                @foreach ($navItems as $item)
                    <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                       class="flex w-full items-center rounded-lg px-3 py-2.5 text-sm transition-colors {{ request()->routeIs($item['route']) ? 'bg-teal-50 font-medium text-teal-700' : 'text-slate-600 hover:bg-slate-50' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-slate-100 px-3 py-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center justify-center rounded-xl bg-slate-900 px-3 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                        Sair
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 p-6 md:p-8 overflow-x-hidden">
            @if (session('sucesso'))
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('sucesso') }}
                </div>
            @endif

            @yield('conteudo')
        </main>
    </div>
</body>
</html>