@extends('layouts.app')

@section('titulo', 'Serviços')

@section('conteudo')
    <div>
        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Serviços</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Cadastre os serviços oferecidos pelo estabelecimento.</p>
            </div>
            <a href="{{ route('servicos.create') }}"
               class="rounded-lg bg-teal-700 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-teal-800 dark:bg-teal-600 dark:hover:bg-teal-700">
                + Novo serviço
            </a>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm transition-colors dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-left text-slate-500 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-400">
                            <th class="px-5 py-3 font-medium">Serviço</th>
                            <th class="px-5 py-3 font-medium">Duração</th>
                            <th class="px-5 py-3 font-medium">Preço</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 font-medium">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($servicos as $servico)
                            <tr class="transition hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                                <td class="px-5 py-3 font-medium text-slate-800 dark:text-slate-200">{{ $servico->nome }}</td>
                                <td class="px-5 py-3 text-slate-500 dark:text-slate-400">{{ $servico->duracao_minutos }} min</td>
                                <td class="px-5 py-3 text-slate-500 dark:text-slate-400">{{ $servico->preco === null ? '-' : 'R$ '.number_format((float) $servico->preco, 2, ',', '.') }}</td>
                                <td class="px-5 py-3">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $servico->ativo ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/70 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">
                                        {{ $servico->ativo ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3"><a href="{{ route('servicos.edit', $servico) }}" class="text-teal-700 hover:underline dark:text-teal-400">Editar</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-10 text-center text-slate-400 dark:text-slate-500">Nenhum serviço cadastrado ainda.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $servicos->links() }}</div>
    </div>
@endsection
