@extends('layouts.app')

@section('conteudo')
<div>
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Relatórios</h1>
            <p class="text-sm text-slate-500">Resumo de atendimentos por período</p>
        </div>
    </div>

        {{-- Filtro de período --}}
        <form method="GET" class="flex items-end gap-3 mb-6 bg-white rounded-xl border border-slate-100 p-4">
            <div>
                <label class="block text-xs text-slate-500 mb-1">De</label>
                <input type="date" name="de" value="{{ request('de') }}"
                       class="border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Até</label>
                <input type="date" name="ate" value="{{ request('ate') }}"
                       class="border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>
            <button type="submit" class="bg-teal-700 hover:bg-teal-800 text-white text-sm font-medium px-4 py-2 rounded-lg">
                Filtrar
            </button>
        </form>

        {{-- Cards resumo --}}
        <div class="grid grid-cols-4 gap-4 mb-6">
            @php
                $cards = [
                    ['label' => 'Total de atendimentos', 'value' => $totalAtendimentos ?? 0],
                    ['label' => 'Concluídos', 'value' => $totalConcluidos ?? 0],
                    ['label' => 'Cancelados', 'value' => $totalCancelados ?? 0],
                    ['label' => 'Novos clientes', 'value' => $novosClientes ?? 0],
                ];
            @endphp
            @foreach ($cards as $card)
                <div class="bg-white rounded-xl border border-slate-100 p-4">
                    <p class="text-sm text-slate-500 mb-2">{{ $card['label'] }}</p>
                    <p class="text-3xl font-semibold text-slate-900">{{ $card['value'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Tabela detalhada --}}
        <div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-left text-slate-500 border-b border-slate-100">
                        <th class="px-5 py-3 font-medium">Serviço</th>
                        <th class="px-5 py-3 font-medium">Atendimentos</th>
                        <th class="px-5 py-3 font-medium">Receita</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($porServico ?? [] as $linha)
                        <tr class="border-b border-slate-50 last:border-0">
                            <td class="px-5 py-3 text-slate-700">{{ $linha->servico }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $linha->total }}</td>
                            <td class="px-5 py-3 text-slate-500">R$ {{ number_format($linha->receita, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-10 text-center text-slate-400">
                                Sem dados para o período selecionado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
</div>
@endsection
