<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Fila & Agenda' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 antialiased">
    <div class="flex min-h-screen">
        <aside class="w-64 shrink-0 bg-white border-r border-gray-200 flex flex-col">
            <div class="h-16 flex items-center gap-2 px-6 border-b border-gray-200">
                <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center text-white text-sm font-semibold">F</div>
                <span class="font-semibold text-gray-800">Fila & Agenda</span>
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
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ $active ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-50' }}">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>
            <div class="px-3 py-4 border-t border-gray-200">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 w-full text-left">Sair</button>
                </form>
            </div>
        </aside>
        <div class="flex-1 flex flex-col">
            <main class="flex-1 p-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>