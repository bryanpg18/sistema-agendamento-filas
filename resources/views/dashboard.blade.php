@extends('layouts.app')

@section('conteudo')
<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Dashboard</h1>
        <div class="flex items-center gap-2">
            <span class="text-sm text-slate-500">Olá, {{ auth()->user()->name ?? 'Administrador' }}</span>
            <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center">
                <i data-lucide="user" class="w-4 h-4 text-slate-500"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2 xl:grid-cols-4">
        @php
            $statCards = [
                ['label' => 'Clientes', 'sub' => 'Cadastrados', 'value' => $clientesCount ?? 0, 'icon' => 'user', 'bg' => 'bg-blue-50', 'iconBg' => 'bg-blue-500'],
                ['label' => 'Agendamentos', 'sub' => 'Hoje', 'value' => $agendamentosHoje ?? 0, 'icon' => 'calendar', 'bg' => 'bg-blue-50', 'iconBg' => 'bg-blue-500'],
                ['label' => 'Atendimentos', 'sub' => 'Em andamento', 'value' => $atendimentosAndamento ?? 0, 'icon' => 'clipboard-list', 'bg' => 'bg-amber-50', 'iconBg' => 'bg-amber-500'],
                ['label' => 'Concluídos', 'sub' => 'Hoje', 'value' => $concluido ?? 0, 'icon' => 'check-circle-2', 'bg' => 'bg-emerald-50', 'iconBg' => 'bg-emerald-500'],
            ];
        @endphp

        @foreach ($statCards as $card)
            <div class="rounded-2xl p-4 shadow-sm ring-1 ring-slate-100 {{ $card['bg'] }}">
                <p class="text-sm text-slate-500">{{ $card['label'] }}</p>
                <p class="text-xs text-slate-400 mb-3">{{ $card['sub'] }}</p>
                <div class="flex items-center justify-between">
                    <span class="text-3xl font-semibold text-slate-900">{{ $card['value'] }}</span>
                    <div class="w-9 h-9 rounded-full {{ $card['iconBg'] }} flex items-center justify-center text-white">
                        <i data-lucide="{{ $card['icon'] }}" class="w-[18px] h-[18px]"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <h2 class="mb-4 font-medium text-slate-900">Próximos agendamentos</h2>
            <div class="space-y-3">
                @forelse ($proximosAgendamentos ?? [] as $agendamento)
                    <div class="flex items-center gap-3">
                        <span class="w-12 text-sm text-slate-400">{{ $agendamento->hora }}</span>
                        <span class="text-sm text-slate-700">{{ $agendamento->cliente }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Nenhum agendamento para hoje.</p>
                @endforelse
            </div>
            <a href="{{ Route::has('agendamentos.index') ? route('agendamentos.index') : '#' }}"
               class="mt-4 inline-block rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-500 hover:bg-slate-50">
                Ver todos
            </a>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <h2 class="mb-4 font-medium text-slate-900">Atendimentos por status</h2>
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
            <span class="text-slate-500">${s.label}</span>
            <span class="font-medium text-slate-900">${s.value}</span>
        </div>
    `).join('');
</script>
@endsection
