@extends('layouts.app')

@section('titulo', 'Bloqueios de agenda')

@section('conteudo')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Bloqueios de agenda</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Feche um dia inteiro ou reserve um intervalo do expediente.</p>
    </div>

    <section class="rounded-xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <h2 class="mb-4 text-sm font-semibold text-slate-700 dark:text-slate-200">Adicionar bloqueio</h2>

        <form method="POST" action="{{ route('bloqueios-agenda.store') }}" class="space-y-4"
              x-data="{ diaInteiro: {{ old('dia_inteiro') ? 'true' : 'false' }}, dataInicio: '{{ old('data') }}' }">
            @csrf

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="data" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Data inicial</label>
                    <input id="data" name="data" type="date" min="{{ now()->toDateString() }}" value="{{ old('data') }}" x-model="dataInicio" required
                           class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-600 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                    @error('data') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="data_fim" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Data final <span class="font-normal text-slate-400">(opcional)</span></label>
                    <input id="data_fim" name="data_fim" type="date" :min="dataInicio" value="{{ old('data_fim') }}"
                           class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-600 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                    @error('data_fim') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="motivo" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Motivo</label>
                    <input id="motivo" name="motivo" type="text" maxlength="255" value="{{ old('motivo') }}" required placeholder="Almoço, feriado, férias..."
                           class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-600 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                    @error('motivo') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <input type="hidden" name="dia_inteiro" value="0">
                <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                    <input type="checkbox" name="dia_inteiro" value="1" x-model="diaInteiro"
                           class="rounded border-slate-300 text-teal-700 focus:ring-teal-600 dark:border-slate-600 dark:bg-slate-800">
                    Bloquear o dia inteiro
                </label>
                @error('dia_inteiro') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="horario_inicio" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Início</label>
                    <input id="horario_inicio" name="horario_inicio" type="time" value="{{ old('horario_inicio') }}" :disabled="diaInteiro" :required="! diaInteiro"
                           class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-600 disabled:opacity-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                    @error('horario_inicio') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="horario_fim" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Fim</label>
                    <input id="horario_fim" name="horario_fim" type="time" value="{{ old('horario_fim') }}" :disabled="diaInteiro" :required="! diaInteiro"
                           class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-600 disabled:opacity-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                    @error('horario_fim') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="rounded-lg bg-teal-700 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-teal-800 dark:bg-teal-600 dark:hover:bg-teal-700">
                    Salvar bloqueio
                </button>
            </div>
        </form>
    </section>

    <section class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="border-b border-slate-100 px-6 py-4 dark:border-slate-800">
            <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Bloqueios cadastrados</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                    <tr>
                        <th class="px-5 py-3 text-left text-sm font-medium text-slate-500 dark:text-slate-400">Data</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-slate-500 dark:text-slate-400">Período</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-slate-500 dark:text-slate-400">Motivo</th>
                        <th class="px-5 py-3 text-right text-sm font-medium text-slate-500 dark:text-slate-400">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($bloqueios as $bloqueio)
                        <tr>
                            <td class="whitespace-nowrap px-5 py-3 text-sm text-slate-700 dark:text-slate-300">{{ $bloqueio->data->format('d/m/Y') }}</td>
                            <td class="whitespace-nowrap px-5 py-3 text-sm text-slate-700 dark:text-slate-300">
                                @if ($bloqueio->horario_inicio && $bloqueio->horario_fim)
                                    {{ substr($bloqueio->horario_inicio, 0, 5) }}–{{ substr($bloqueio->horario_fim, 0, 5) }}
                                @else
                                    Dia inteiro
                                @endif
                            </td>
                            <td class="px-5 py-3 text-sm text-slate-700 dark:text-slate-300">{{ $bloqueio->motivo }}</td>
                            <td class="px-5 py-3 text-right">
                                <form method="POST" action="{{ route('bloqueios-agenda.destroy', $bloqueio) }}" onsubmit="return confirm('Remover este bloqueio?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-rose-600 hover:text-rose-700 dark:text-rose-400">Remover</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-sm text-slate-400 dark:text-slate-500">Nenhum bloqueio cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">{{ $bloqueios->links() }}</div>
    </section>
</div>
@endsection
