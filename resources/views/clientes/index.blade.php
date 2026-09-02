@extends('layouts.app')

@section('titulo', 'Clientes')

@section('conteudo')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Clientes</h1>
            <p class="text-sm text-gray-500">Gerencie os clientes cadastrados</p>
        </div>
        <a href="{{ route('clientes.create') }}"
           class="bg-teal-800 hover:bg-teal-900 text-white px-4 py-2 rounded-lg text-sm font-medium">
            + Novo Cliente
        </a>
    </div>

    <form method="GET" class="mb-4">
        <input type="text" name="busca" value="{{ request('busca') }}"
               placeholder="Buscar por nome ou CPF..."
               class="w-full max-w-sm border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-700">
    </form>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium">Nome</th>
                    <th class="px-4 py-3 font-medium">CPF</th>
                    <th class="px-4 py-3 font-medium">Telefone</th>
                    <th class="px-4 py-3 font-medium">E-mail</th>
                    <th class="px-4 py-3 font-medium">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($clientes as $cliente)
                    <tr>
                        <td class="px-4 py-3 text-gray-800">{{ $cliente->nome_completo }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $cliente->cpf }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $cliente->telefone }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $cliente->email ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('clientes.edit', $cliente) }}" class="text-teal-700 hover:underline mr-3">Editar</a>
                            <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Remover este cliente?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400">Nenhum cliente cadastrado ainda.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $clientes->links() }}
    </div>
@endsection