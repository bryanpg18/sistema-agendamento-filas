@extends('layouts.app')

@section('conteudo')
<div>
    <h1 class="text-2xl font-semibold text-slate-900 mb-2">Atendimentos</h1>

    {{-- Abas de status --}}
    <div class="flex space-x-6 border-b mb-4">
        <a href="{{ route('atendimentos.index', ['status' => 'em_espera']) }}"
           class="pb-2 {{ $statusAtivo === 'em_espera' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-slate-500' }}">
            Em espera ({{ $contagens['em_espera'] }})
        </a>
        <a href="{{ route('atendimentos.index', ['status' => 'em_atendimento']) }}"
           class="pb-2 {{ $statusAtivo === 'em_atendimento' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-slate-500' }}">
            Em atendimento ({{ $contagens['em_atendimento'] }})
        </a>
        <a href="{{ route('atendimentos.index', ['status' => 'concluido']) }}"
           class="pb-2 {{ $statusAtivo === 'concluido' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-slate-500' }}">
            Concluídos ({{ $contagens['concluido'] }})
        </a>
        <a href="{{ route('atendimentos.index', ['status' => 'cancelado']) }}"
           class="pb-2 {{ $statusAtivo === 'cancelado' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-slate-500' }}">
            Cancelados ({{ $contagens['cancelado'] }})
        </a>
    </div>

    {{-- Tabela de agendamentos --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-medium text-slate-700">Cliente</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-slate-700">Serviço</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-slate-700">Data</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-slate-700">Horário</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-slate-700">Status</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-slate-700">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                @forelse($agendamentos as $agendamento)
                    <tr>
                        <td class="px-4 py-2 text-sm text-slate-900">{{ $agendamento->cliente->nome_completo }}</td>
                        <td class="px-4 py-2 text-sm text-slate-900">{{ $agendamento->servico->nome }}</td>
                        <td class="px-4 py-2 text-sm text-slate-900">{{ $agendamento->data->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 text-sm text-slate-900">{{ $agendamento->horario }}</td>
                        <td class="px-4 py-2 text-sm text-slate-900">{{ ucfirst($agendamento->status) }}</td>
                        <td class="px-4 py-2 text-sm">
                            @if($statusAtivo === 'em_espera')
                                <form method="POST" action="{{ route('atendimentos.iniciar', $agendamento) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="px-3 py-1 bg-indigo-600 text-white rounded">Iniciar</button>
                                </form>
                            @elseif($statusAtivo === 'em_atendimento')
                                <form method="POST" action="{{ route('atendimentos.finalizar', $agendamento) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="px-3 py-1 bg-green-600 text-white rounded">Finalizar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-2 text-center text-sm text-slate-500">
                            Nenhum atendimento encontrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
