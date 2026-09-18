@extends('layouts.app')

@section('titulo', 'Novo Cliente')

@section('conteudo')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <p class="text-sm text-slate-400 dark:text-slate-500">
                <a href="{{ route('clientes.index') }}" class="hover:underline">Clientes</a> &gt; Novo Cliente
            </p>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">Cadastro de Clientes</h1>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-sm transition-colors">
            <h2 class="font-semibold text-slate-700 dark:text-slate-200 mb-4">Dados do cliente</h2>

            <form method="POST" action="{{ route('clientes.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm text-slate-600 dark:text-slate-300 mb-1">Nome completo</label>
                    <input type="text" name="nome_completo" value="{{ old('nome_completo') }}"
                           class="w-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-600"
                           placeholder="Digite o nome completo">
                    @error('nome_completo') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-slate-600 dark:text-slate-300 mb-1">CPF</label>
                        <input type="text" name="cpf" value="{{ old('cpf') }}"
                               x-data x-mask="999.999.999-99"
                               class="w-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-600"
                               placeholder="000.000.000-00">
                        @error('cpf') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-slate-600 dark:text-slate-300 mb-1">Telefone</label>
                        <input type="text" name="telefone" value="{{ old('telefone') }}"
                               x-data x-mask:dynamic="$input.replace(/\D/g, '').length > 10 ? '(99) 99999-9999' : '(99) 9999-9999'"
                               class="w-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-600"
                               placeholder="(00) 00000-0000">
                        @error('telefone') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-slate-600 dark:text-slate-300 mb-1">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-600"
                           placeholder="Digite o e-mail">
                    @error('email') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm text-slate-600 dark:text-slate-300 mb-1">Data de nascimento</label>
                    <input type="date" name="data_nascimento" value="{{ old('data_nascimento') }}"
                           class="w-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-600">
                    @error('data_nascimento') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm text-slate-600 dark:text-slate-300 mb-1">Observações</label>
                    <textarea name="observacoes" rows="3"
                              class="w-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-600"
                              placeholder="Informações adicionais (opcional)">{{ old('observacoes') }}</textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('clientes.index') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="bg-teal-700 hover:bg-teal-800 dark:bg-teal-600 dark:hover:bg-teal-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
