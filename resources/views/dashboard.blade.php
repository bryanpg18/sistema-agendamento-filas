@extends('layouts.app')

@section('conteudo')
<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Dashboard</h1>
        <div class="flex items-center gap-2">
            <span class="text-sm text-slate-500 dark:text-slate-400">Olá, {{ auth()->user()->name ?? 'Administrador' }}</span>
            <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center">
                <i data-lucide="user" class="w-4 h-4 text-slate-500 dark:text-slate-300"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2 xl:grid-cols-4">
        @php
            $statCards = [
                ['label' => 'Clientes', 'sub' => 'Cadastrados', 'value' => $clientesCount ?? 0, 'icon' => 'user', 'bg' => 'bg-blue-50/80 dark:bg-blue-950/40', 'ring' => 'ring-blue-100 dark:ring-blue-900/50', 'iconBg' => 'bg-blue-500 dark:bg-blue-600'],
                ['label' => 'Agendamentos', 'sub' => 'Hoje', 'value' => $agendamentosHoje ?? 0, 'icon' => 'calendar', 'bg' => 'bg-teal-50/80 dark:bg-teal-950/40', 'ring' => 'ring-teal-100 dark:ring-teal-900/50', 'iconBg' => 'bg-teal-600 dark:bg-teal-500'],
                ['label' => 'Atendimentos', 'sub' => 'Em andamento', 'value' => $atendimentosAndamento ?? 0, 'icon' => 'clipboard-list', 'bg' => 'bg-amber-50/80 dark:bg-amber-950/40', 'ring' => 'ring-amber-100 dark:ring-amber-900/50', 'iconBg' => 'bg-amber-500 dark:bg-amber-600'],
                ['label' => 'Concluídos', 'sub' => 'Hoje', 'value' => $concluido ?? 0, 'icon' => 'check-circle-2', 'bg' => 'bg-emerald-50/80 dark:bg-emerald-950/40', 'ring' => 'ring-emerald-100 dark:ring-emerald-900/50', 'iconBg' => 'bg-emerald-500 dark:bg-emerald-600'],
            ];
        @endphp

        @foreach ($statCards as $card)
            <div class="rounded-2xl p-4 shadow-sm ring-1 {{ $card['ring'] }} {{ $card['bg'] }} transition-colors">
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">{{ $card['label'] }}</p>
                <p class="text-xs text-slate-400 dark:text-slate-400 mb-3">{{ $card['sub'] }}</p>
                <div class="flex items-center justify-between">
                    <span class="text-3xl font-semibold text-slate-900 dark:text-white">{{ $card['value'] }}</span>
                    <div class="w-9 h-9 rounded-full {{ $card['iconBg'] }} flex items-center justify-center text-white shadow-xs">
                        <i data-lucide="{{ $card['icon'] }}" class="w-[18px] h-[18px]"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        <div class="rounded-2xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm transition-colors">
            <h2 class="mb-4 font-medium text-slate-900 dark:text-white">Próximos agendamentos</h2>
            <div class="space-y-3">
                @forelse ($proximosAgendamentos ?? [] as $agendamento)
                    <div class="flex items-center gap-3">
                        <span class="w-12 text-sm text-slate-400 dark:text-slate-500">{{ substr($agendamento->horario, 0, 5) }}</span>
                        <span class="text-sm text-slate-700 dark:text-slate-200">{{ $agendamento->cliente->nome_completo }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400 dark:text-slate-500">Nenhum agendamento para hoje.</p>
                @endforelse
            </div>
            <a href="{{ Route::has('agendamentos.index') ? route('agendamentos.index') : '#' }}"
               class="mt-4 inline-block rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                Ver todos
            </a>
        </div>

        <div class="rounded-2xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm transition-colors">
            <h2 class="mb-4 font-medium text-slate-900 dark:text-white">Atendimentos por status</h2>
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
                <div class="w-36 h-36">
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="space-y-2" id="statusLegend"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
    lucide.createIcons();

    const statusData = [
        { label: 'Em espera', value: {{ $emEspera ?? 5 }}, color: '#f59e0b' },
        { label: 'Em atendimento', value: {{ $emAtendimento ?? 5 }}, color: '#3b82f6' },
        { label: 'Concluído', value: {{ $concluido ?? 8 }}, color: '#10b981' },
        { label: 'Cancelado', value: {{ $cancelado ?? 2 }}, color: '#ef4444' },
    ];

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: statusData.map(s => s.label),
            datasets: [{
                data: statusData.map(s => s.value),
                backgroundColor: statusData.map(s => s.color),
                borderWidth: 0,
            }],
        },
        options: {
            cutout: '65%',
            plugins: { legend: { display: false } },
        },
    });

    const legend = document.getElementById('statusLegend');
    legend.innerHTML = statusData.map(s => `
        <div class="flex items-center gap-2 text-sm">
            <span class="w-2.5 h-2.5 rounded-full" style="background-color:${s.color}"></span>
            <span class="text-slate-500 dark:text-slate-400">${s.label}</span>
            <span class="font-medium text-slate-900 dark:text-white">${s.value}</span>
        </div>
    `).join('');
</script>
@endsection
