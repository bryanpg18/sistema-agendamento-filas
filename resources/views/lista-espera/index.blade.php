@extends('layouts.app')

@section('titulo', 'Lista de espera')

@section('conteudo')
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Lista de espera</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Cadastre clientes para avisá-los quando uma vaga compatível for liberada.</p>
    </div>

    <form method="POST" action="{{ route('lista-espera.store') }}" class="mb-6 grid grid-cols-1 gap-4 rounded-xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 md:grid-cols-2 xl:grid-cols-5">
        @csrf
        <div>
            <label for="cliente_id" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Cliente</label>
            <select id="cliente_id" name="cliente_id" required class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                <option value="">Selecione</option>
                @foreach ($clientes as $cliente)
                    <option value="{{ $cliente->id }}" @selected((string) old('cliente_id') === (string) $cliente->id)>{{ $cliente->nome_completo }}</option>
                @endforeach
            </select>
            @error('cliente_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="servico_id" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Serviço</label>
            <select id="servico_id" name="servico_id" required class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                <option value="">Selecione</option>
                @foreach ($servicos as $servico)
                    <option value="{{ $servico->id }}" @selected((string) old('servico_id') === (string) $servico->id)>{{ $servico->nome }}</option>
                @endforeach
            </select>
            @error('servico_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="data_inicio" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">A partir de</label>
            <input id="data_inicio" type="date" name="data_inicio" min="{{ now()->toDateString() }}" value="{{ old('data_inicio') }}" required class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
            @error('data_inicio')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="data_fim" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Até (opcional)</label>
            <input id="data_fim" type="date" name="data_fim" value="{{ old('data_fim') }}" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
            @error('data_fim')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="horario" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Horário (opcional)</label>
            <div class="flex gap-2">
                <input id="horario" type="time" name="horario" value="{{ old('horario') }}" class="min-w-0 flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                <button type="submit" class="rounded-lg bg-teal-700 px-4 py-2 text-sm font-medium text-white transition hover:bg-teal-800 dark:bg-teal-600 dark:hover:bg-teal-700">Adicionar</button>
            </div>
            @error('horario')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400 md:col-span-2 xl:col-span-5">O cliente precisa ter autorizado notificações por e-mail ou WhatsApp no cadastro. Sem horário, qualquer vaga do serviço no período poderá corresponder.</p>
    </form>

    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50 text-left text-slate-500 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-400">
                        <th class="px-5 py-3 font-medium">Cliente</th>
                        <th class="px-5 py-3 font-medium">Serviço</th>
                        <th class="px-5 py-3 font-medium">Período desejado</th>
                        <th class="px-5 py-3 font-medium">Horário</th>
                        <th class="px-5 py-3 font-medium">Situação</th>
                        <th class="px-5 py-3 font-medium">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($listas as $lista)
                        <tr>
                            <td class="px-5 py-3 font-medium text-slate-800 dark:text-slate-200">{{ $lista->cliente->nome_completo }}</td>
                            <td class="px-5 py-3 text-slate-600 dark:text-slate-300">{{ $lista->servico->nome }}</td>
                            <td class="px-5 py-3 text-slate-600 dark:text-slate-300">{{ $lista->data_inicio->format('d/m/Y') }}{{ $lista->data_fim ? ' até '.$lista->data_fim->format('d/m/Y') : ' em diante' }}</td>
                            <td class="px-5 py-3 text-slate-600 dark:text-slate-300">{{ $lista->horario ? substr($lista->horario, 0, 5) : 'Qualquer horário' }}</td>
                            <td class="px-5 py-3 text-slate-600 dark:text-slate-300">
                                @switch($lista->status)
                                    @case('aguardando') Aguardando @break
                                    @case('avisado') Avisado{{ $lista->avisado_em ? ' em '.$lista->avisado_em->format('d/m/Y H:i') : '' }} @break
                                    @case('atendido') Atendido @break
                                    @default Cancelado
                                @endswitch
                            </td>
                            <td class="whitespace-nowrap px-5 py-3">
                                @if (in_array($lista->status, ['aguardando', 'avisado'], true))
                                    <form method="POST" action="{{ route('lista-espera.update', $lista) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="atendido">
                                        <button class="mr-3 text-teal-700 hover:underline dark:text-teal-400">Concluir</button>
                                    </form>
                                    <form method="POST" action="{{ route('lista-espera.destroy', $lista) }}" class="inline" onsubmit="return confirm('Remover este registro da lista?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-rose-600 hover:underline">Remover</button>
                                    </form>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">A lista de espera está vazia.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $listas->links() }}</div>
</div>
@endsection
