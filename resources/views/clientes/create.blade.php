@extends('layouts.painel')

@section('titulo', 'Novo Cliente')

@section('conteudo')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <p class="text-sm text-gray-400">
                <a href="{{ route('clientes.index') }}" class="hover:underline">Clientes</a> &gt; Novo Cliente
            </p>
            <h1 class="text-2xl font-bold text-gray-800 mt-1">Cadastro de Clientes</h1>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-700 mb-4">Dados do cliente</h2>

            <form method="POST" action="{{ route('clientes.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nome completo</label>
                    <input type="text" name="nome_completo" value="{{ old('nome_completo') }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-700"
                           placeholder="Digite o nome completo">
                    @error('nome_completo') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">CPF</label>
                        <input type="text" name="cpf" value="{{ old('cpf') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-700"
                               placeholder="000.000.000-00">
                        @error('cpf') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Telefone</label>
                        <input type="text" name="telefone" value="{{ old('telefone') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-700"
                               placeholder="(00) 0000-0000">
                        @error('telefone') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-700"
                           placeholder="Digite o e-mail">
                    @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Data de nascimento</label>
                    <input type="date" name="data_nascimento" value="{{ old('data_nascimento') }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-700">
                    @error('data_nascimento') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Observações</label>
                    <textarea name="observacoes" rows="3"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-700"
                              placeholder="Informações adicionais (opcional)">{{ old('observacoes') }}</textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('clientes.index') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="bg-teal-800 hover:bg-teal-900 text-white px-5 py-2 rounded-lg text-sm font-medium">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
