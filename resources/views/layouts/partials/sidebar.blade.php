{{-- resources/views/layouts/partials/sidebar.blade.php --}}
<aside class="w-64 bg-white border-r border-slate-200 flex flex-col shrink-0 min-h-screen sticky top-0">
    <div class="flex items-center gap-3 px-5 py-5 border-b border-slate-100">
        <div class="h-10 w-10 rounded-xl bg-teal-700 flex items-center justify-center text-sm font-semibold text-white shadow-sm">
            F
        </div>
        <div>
            <p class="text-sm font-semibold text-slate-900">Fila & Agenda</p>
            <p class="text-xs text-slate-500">Gestão de filas e horários</p>
        </div>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1">
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
               class="w-full flex items-center px-3 py-2.5 rounded-lg text-sm transition-colors
                      {{ request()->routeIs($item['route']) ? 'bg-teal-50 text-teal-700 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

        <div class="px-3 py-4 border-t border-slate-100">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
                <button type="submit" class="w-full flex items-center justify-center rounded-xl bg-slate-900 px-3 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                Sair
            </button>
        </form>
    </div>
</aside>
