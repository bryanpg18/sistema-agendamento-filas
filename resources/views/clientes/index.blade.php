@extends('layouts.app')

@section('conteudo')
<div x-data="{ busca: '' }">
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Clientes</h1>
            <p class="text-sm text-slate-500">Gerencie os clientes cadastrados</p>
        </div>
        <a href="{{ Route::has('clientes.create') ? route('clientes.create') : '#' }}"
class="rounded-lg bg-teal-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-teal-800">
            + Novo Cliente
        </a>
    </div>

    <input
type="text"
x-model="busca"
placeholder="Buscar por nome ou CPF..."
class="mb-4 w-72 rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-200"
    >

    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50 text-left text-slate-500">
                    <th class="px-5 py-3 font-medium">Nome</th>
                    <th class="px-5 py-3 font-medium">CPF</th>
                    <th class="px-5 py-3 font-medium">Telefone</th>
                    <th class="px-5 py-3 font-medium">E-mail</th>
                    <th class="px-5 py-3 font-medium">Ações</th>
                </tr>
            </thead>
            <tbody>
@forelse ($clientes ?? [] as $cliente)
                    <tr x-show="busca === '' || '{{ strtolower($cliente->nome_completo) }}'.includes(busca.toLowerCase()) || '{{ $cliente->cpf }}'.includes(busca)"
class="border-b border-slate-50 last:border-0">
                        <td class="px-5 py-3 text-slate-700">{{ $cliente->nome_completo }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ $cliente->cpf_formatado }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ $cliente->telefone_formatado }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ $cliente->email }}</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('clientes.edit', $cliente) }}" class="mr-3 text-teal-700 hover:underline">Editar</a>
                            <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="inline" onsubmit="return confirm('Remover este cliente?')">
@csrf
@method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">Remover</button>
                            </form>
                        </td>
                    </tr>
@empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-slate-400">
                            Nenhum cliente cadastrado ainda.
                        </td>
                    </tr>
@endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection