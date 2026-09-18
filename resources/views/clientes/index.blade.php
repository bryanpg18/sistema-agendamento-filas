@extends('layouts.app')

@section('titulo', 'Clientes')

@section('conteudo')
<div x-data="{ busca: '{{ request('busca') }}' }">
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Clientes</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Gerencie os clientes cadastrados</p>
        </div>
        <a href="{{ route('clientes.create') }}"
           class="rounded-lg bg-teal-700 hover:bg-teal-800 dark:bg-teal-600 dark:hover:bg-teal-700 px-4 py-2.5 text-sm font-medium text-white transition">
            + Novo Cliente
        </a>
    </div>

    <form method="GET" class="mb-4">
        <input
            type="text"
            name="busca"
            value="{{ request('busca') }}"
            placeholder="Buscar por nome ou CPF..."
            class="w-72 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-200 dark:focus:ring-teal-800"
        >
    </form>

    <div class="overflow-hidden rounded-xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60 text-left text-slate-500 dark:text-slate-400">
                        <th class="px-5 py-3 font-medium">Nome</th>
                        <th class="px-5 py-3 font-medium">CPF</th>
                        <th class="px-5 py-3 font-medium">Telefone</th>
                        <th class="px-5 py-3 font-medium">E-mail</th>
                        <th class="px-5 py-3 font-medium">Data de nascimento</th>
                        <th class="px-5 py-3 font-medium">Observações</th>
                        <th class="px-5 py-3 font-medium">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($clientes as $cliente)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                            <td class="px-5 py-3 text-slate-800 dark:text-slate-200 font-medium">{{ $cliente->nome_completo }}</td>
                            <td class="px-5 py-3 text-slate-500 dark:text-slate-400">{{ $cliente->cpf_formatado }}</td>
                            <td class="px-5 py-3 text-slate-500 dark:text-slate-400">{{ $cliente->telefone_formatado }}</td>
                            <td class="px-5 py-3 text-slate-500 dark:text-slate-400">{{ $cliente->email ?? '-' }}</td>
                            <td class="px-5 py-3 text-slate-500 dark:text-slate-400">
                                {{ $cliente->data_nascimento?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="px-5 py-3 text-slate-500 dark:text-slate-400 max-w-xs truncate" title="{{ $cliente->observacoes }}">
                                {{ $cliente->observacoes ?? '-' }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <a href="{{ route('clientes.edit', $cliente) }}" class="mr-3 text-teal-700 dark:text-teal-400 hover:underline">Editar</a>
                                <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Remover este cliente?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-500 hover:underline">Remover</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-slate-400 dark:text-slate-500">
                                Nenhum cliente cadastrado ainda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $clientes->links() }}
    </div>
</div>
@endsection